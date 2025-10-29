<?php
// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!doctype html>
<html lang="en">
<head>
  <?php 
  $page_title = 'Admin Dashboard - Dr. Gift';
  include 'includes/head.php'; 
  ?>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    :root{
      --navy: #071731;
      --gold: #D4AF37;
      --gold-dark: #B8941F;
      --muted: #6b7280;
      --panel-bg: rgba(255,255,255,0.92);
    }

    body {
      font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: linear-gradient(135deg,#f8fafc 0%,#eef1f5 100%);
      margin:0; color:#0f172a;
    }

    /* Dark mode variables & page-wide overrides (applies when header toggles body.dark-mode) */
    body.dark-mode {
      --panel-bg: rgba(8,12,20,0.72);
      --navy: #e6eefc;
      --gold: #D4AF37;
      --gold-dark: #B8941F;
      --muted: #94a3b8;
      background: linear-gradient(135deg,#071731 0%,#091a2b 100%);
      color: var(--navy);
    }

    body.dark-mode header, body.dark-mode .card, body.dark-mode .panel, body.dark-mode #sidebar {
      background: rgba(4,8,16,0.6) !important;
      color: var(--navy) !important;
      border-color: rgba(255,255,255,0.03);
    }

    /* Improve contrast in dark mode for text and controls */
    body.dark-mode .muted { color: #9fb0c8 !important; }
    body.dark-mode .stat-label { color: #9fb0c8 !important; }
    body.dark-mode .page-title { color: var(--navy) !important; }
    body.dark-mode .btn { background: rgba(255,255,255,0.03) !important; color: var(--navy) !important; border-color: rgba(255,255,255,0.04) !important; }
    body.dark-mode .btn.gold { box-shadow: 0 8px 24px rgba(181,138,44,0.12); }
    body.dark-mode .activity-item { color: var(--navy) !important; }
    body.dark-mode .activity-badge, body.dark-mode .sidebar-icon { background: rgba(255,255,255,0.03) !important; color: var(--navy) !important; }
    body.dark-mode a { color: var(--navy) !important; }

    body.dark-mode .stat-value { color: var(--navy) !important; }
    body.dark-mode .brand-icon { background: rgba(255,255,255,0.03); color:var(--navy); }
    body.dark-mode .sidebar-item-label { color: var(--navy); }
    body.dark-mode .skeleton { background: linear-gradient(90deg,#0b1220 0%, #071731 50%, #0b1220 100%); }

    /* ---------- LAYOUT ---------- */
    .app-wrap { display:flex; min-height:100vh; }

    /* Sidebar: collapsed by default on desktop (icons-only), expands on hover or when pinned */
    #sidebar {
      position: fixed;
      top: 0; left: 0; bottom: 0;
      width: 72px; /* collapsed width */
      background: var(--panel-bg);
      -webkit-backdrop-filter: blur(6px);
      backdrop-filter: blur(6px);
      box-shadow: 0 10px 30px rgba(8,15,35,0.06);
      transition: width .28s cubic-bezier(.2,.9,.2,1), transform .22s;
      z-index: 60;
      overflow: hidden;
      display:flex;
      flex-direction:column;
    }

    /* expanded state */
    body.sidebar-expanded #sidebar {
      width: 280px;
    }

    /* off-canvas for mobile - when body has sidebar-open-mobile */
    #sidebar.mobile-hidden { transform: translateX(-110%); }
    body.sidebar-open-mobile #sidebar { transform: translateX(0); left: 0; }

    /* overlay for mobile */
    #overlay {
      position: fixed; inset:0; background: rgba(2,6,23,0.45); z-index:50; display:none;
    }
    body.sidebar-open-mobile #overlay { display:block; }

    /* Sidebar content layout */
    .sidebar-brand { padding: 12px 8px; border-bottom:1px solid #eef2f7; display:flex; align-items:center; gap:10px; }
    .brand-full { display:none; font-weight:700; color:var(--navy); text-decoration:none; }
    .brand-icon { width:44px; height:44px; display:flex; align-items:center; justify-content:center; border-radius:10px; background:#fff; box-shadow:0 6px 18px rgba(8,15,35,0.04); font-weight:700; color:var(--navy); }

    /* show/hide brand parts depending on collapsed/expanded */
    body.sidebar-expanded .brand-full { display:inline-block; }
    body.sidebar-expanded .brand-icon { margin-right:8px; }
    body.sidebar-expanded .sidebar-item-label { display:inline-block; opacity:1; }

    .sidebar-item { display:flex; align-items:center; gap:12px; padding:10px 10px; cursor:pointer; color:#0f172a; transition: background .14s, transform .14s; border-radius:8px; text-decoration:none; }
  .sidebar-item:hover, .sidebar-item:focus { background:#eef6ff; transform: translateX(6px); outline:none; color:var(--navy); }
    .sidebar-icon { width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:#f1f5f9; }

    .sidebar-footer { margin-top:auto; padding:12px; border-top:1px solid #eef2f7; font-size:.95rem; color:var(--muted); }

    /* ensure label hidden by default (CSS-controlled, not inline) */
    .sidebar-item-label { display:none; transition: opacity .18s ease; }

    /* main area */
    main { margin-left:72px; padding:20px; flex:1; transition: margin-left .28s; }
    body.sidebar-expanded main { margin-left:280px; }
    @media (max-width: 1024px) {
      #sidebar { position:fixed; left:0; transform: translateX(-110%); width:280px; }
      body.sidebar-open-mobile #sidebar { transform: translateX(0); }
      main { margin-left:0; padding:12px; }
    }

    /* header */
    .page-header { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:18px; }
    .page-title { font-family: 'Playfair Display', serif; font-size:1.5rem; font-weight:700; color:var(--navy); position:relative; }
    .page-title::after { content:''; position:absolute; left:0; bottom:-8px; width:60px; height:3px; background:var(--gold); border-radius:2px; }

    /* top row: 3 stat cards + quick analytics */
    .top-row { display:grid; grid-template-columns: repeat(3, minmax(0,1fr)) 320px; gap:12px; align-items:stretch; margin-bottom:18px; }
    .card { background:var(--panel-bg); padding:12px; border-radius:12px; box-shadow: 0 8px 24px rgba(8,15,35,0.06); display:flex; justify-content:space-between; align-items:center; min-height:120px; }
    .stat-left { display:flex; flex-direction:column; gap:6px; }
    .stat-label { font-size:.86rem; color:var(--muted); }
    .stat-value { font-size:1.6rem; font-weight:700; color:var(--navy); }
    .gold-blob { width:56px; height:56px; border-radius:12px; display:flex; align-items:center; justify-content:center; background: linear-gradient(90deg,var(--gold),var(--gold-dark)); color:white; font-size:1.2rem; }

    /* main grid */
    .main-grid { display:grid; grid-template-columns: 1fr 380px; gap:16px; align-items:start; min-height: calc(100vh - 320px); }
    .panel { background:var(--panel-bg); padding:14px; border-radius:12px; box-shadow:0 8px 24px rgba(8,15,35,0.06); }

    /* activities list */
    #activitiesList { display:flex; flex-direction:column; gap:10px; max-height: 620px; overflow:auto; padding-right:6px; }
    .activity-item { display:flex; gap:12px; align-items:flex-start; padding:10px; border-radius:8px; transition: background .12s, transform .12s; }
  .activity-item:hover { background:#f6fbff; transform: translateX(4px); }
    .activity-badge { width:44px; height:44px; border-radius:8px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-size:1.05rem; }

    .activity-actions { display:flex; gap:6px; align-items:center; margin-left:8px; }
    .action-btn { border:1px solid rgba(15,23,42,0.06); background:white; padding:6px 8px; border-radius:8px; cursor:pointer; font-size:0.9rem; transition: background .12s; }
  .action-btn:hover { background:#eef6ff; }

  /* Dark-mode hover contrasts */
  body.dark-mode .sidebar-item:hover, body.dark-mode .sidebar-item:focus { background: rgba(255,255,255,0.04) !important; color: var(--navy) !important; }
  body.dark-mode .activity-item:hover { background: rgba(255,255,255,0.03) !important; }
  body.dark-mode .action-btn:hover { background: rgba(255,255,255,0.02) !important; }

    /* compact chart: increase height to avoid truncation */
    #compactChart { height:140px !important; width:100% !important; display:block; }

    /* skeleton */
    .skeleton { background: linear-gradient(90deg,#eef2f7 0%, #f8fafc 50%, #eef2f7 100%); background-size:200% 100%; animation: shimmer 1.1s linear infinite; border-radius:8px; }
    @keyframes shimmer { 100% { background-position: -200% 0; } }

    /* utilities */
    .btn { display:inline-flex; gap:.5rem; align-items:center; padding:.5rem .75rem; border-radius:8px; cursor:pointer; border:1px solid rgba(15,23,42,0.06); background:white; }
    .btn.gold { background:var(--gold); color:white; border:none; box-shadow:0 10px 30px rgba(181,138,44,0.12); }
    .muted { color:var(--muted); font-size:.92rem; }
    .small { font-size:.88rem; }

    /* show full sidebar content when expanded also for smaller widths if toggled */
    @media (max-width: 1024px) {
      body.sidebar-expanded #sidebar { width: 280px; }
    }

    @media (max-width: 900px) {
      .top-row { grid-template-columns: 1fr; }
      .main-grid { grid-template-columns: 1fr; }
      main { padding:12px; }
      /* allow compact chart to be responsive on small screens */
      #compactChart { height:80px !important; }
    }

    /* Desktop: show sidebar labels and use full width so icons+links are visible */
    @media (min-width: 1025px) {
      #sidebar { width: 280px; }
      main { margin-left: 280px; }
      .brand-full { display:inline-block; }
      .sidebar-item-label { display:inline-block; }
    }

    /* Quick analytics card: stack chart on its own line */
    .quick-analytics-card { display:flex; flex-direction:column; gap:8px; align-items:stretch; }
    .quick-analytics-card .chart-wrap { width:100%; height:140px; }
  </style>
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <div id="overlay" aria-hidden="true"></div>

  <!-- SIDEBAR (collapsed by default on desktop; off-canvas on mobile) -->
  <aside id="sidebar" class="mobile-hidden" aria-hidden="true">
    <div class="sidebar-brand">
      <div class="brand-icon">DG</div>
      <a href="dashboard.php" class="brand-full">Admin Dashboard</a>
    </div>

    <nav style="padding:10px;">
      <ul style="list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:6px;">
        <li><a href="#" class="sidebar-item sidebar-link" data-activity-type="all"><span class="sidebar-icon">🏠</span><span class="sidebar-item-label">Dashboard</span></a></li>
        <li><a href="#" class="sidebar-item sidebar-link" data-activity-type="ad"><span class="sidebar-icon">📢</span><span class="sidebar-item-label">Advertisements</span></a></li>
        <li><a href="#" class="sidebar-item sidebar-link" data-activity-type="book"><span class="sidebar-icon">📚</span><span class="sidebar-item-label">Books</span></a></li>
        <li><a href="#" class="sidebar-item sidebar-link" data-activity-type="gallery"><span class="sidebar-icon">🖼️</span><span class="sidebar-item-label">Gallery</span></a></li>
        <li><a href="#" class="sidebar-item sidebar-link" data-activity-type="post"><span class="sidebar-icon">📝</span><span class="sidebar-item-label">Blog Posts</span></a></li>
        <li><a href="#" class="sidebar-item sidebar-link" data-activity-type="booking"><span class="sidebar-icon">📆</span><span class="sidebar-item-label">Book Me</span></a></li>

        <li style="margin-top:10px; font-size:.8rem; color:var(--muted); padding-left:10px;">Quick Links</li>
        <li><a href="#" class="sidebar-item sidebar-link" data-activity-type="highlights"><span class="sidebar-icon">✨</span><span class="sidebar-item-label">Key Highlights</span></a></li>
        <li><a href="#" class="sidebar-item sidebar-link" data-activity-type="cta"><span class="sidebar-icon">🎯</span><span class="sidebar-item-label">Final CTA</span></a></li>
      </ul>
    </nav>

    <div class="sidebar-footer">
      <div class="small muted">Logged in as</div>
      <div style="font-weight:600; color:var(--navy);"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></div>
      <div style="margin-top:8px; display:flex; gap:8px;">
        <button id="pinSidebarBtn" class="btn small" title="Pin sidebar (toggle expanded)">Pin</button>
        <button id="openMobileClose" class="btn small" style="display:none;">Close</button>
      </div>
    </div>
  </aside>

  <main>
    <div style="max-width:1280px; margin:0 auto;">

      <div class="page-header">
        <div style="display:flex; align-items:center; gap:12px;">
          <button id="openMobileSidebar" class="btn" aria-label="Open menu">☰</button>
          <div>
            <div class="page-title">Overview and recent site activity</div>
          </div>
        </div>

        <div>
          <button id="refreshBtn" class="btn gold">Refresh</button>
        </div>
      </div>

      <div class="top-row" role="region" aria-label="Top statistics & quick analytics">
        <div class="card" id="adsCard" role="button" tabindex="0">
          <div class="stat-left">
            <div class="stat-label">Advertisements</div>
            <div id="adsCount" class="stat-value">—</div>
          </div>
          <div class="gold-blob">📢</div>
        </div>

        <div class="card" id="subsCard" role="button" tabindex="0">
          <div class="stat-left">
            <div class="stat-label">Subscribers</div>
            <div id="subsCount" class="stat-value">—</div>
          </div>
          <div style="width:56px;height:56px;border-radius:12px;background:#fff;display:flex;align-items:center;justify-content:center;">👥</div>
        </div>

        <div class="card" id="postsCard" role="button" tabindex="0">
          <div class="stat-left">
            <div class="stat-label">Posts</div>
            <div id="postsCount" class="stat-value">—</div>
          </div>
          <div style="width:56px;height:56px;border-radius:12px;background:#fff;display:flex;align-items:center;justify-content:center;">📝</div>
        </div>

        <div class="card quick-analytics-card" aria-label="Quick analytics">
          <div>
            <div class="stat-label">Quick Analytics</div>
            <div style="display:flex; gap:12px; margin-top:8px;">
              <div style="text-align:center;">
                <div class="small muted">Total</div>
                <div id="analyticTotal" style="font-weight:700; font-size:1.05rem;">—</div>
              </div>
              <div style="text-align:center;">
                <div class="small muted">Bookings</div>
                <div id="analyticBookings" style="font-weight:700; font-size:1.05rem;">—</div>
              </div>
              <div style="text-align:center;">
                <div class="small muted">Posts</div>
                <div id="analyticPosts" style="font-weight:700; font-size:1.05rem;">—</div>
              </div>
              <div style="text-align:center;">
                <div class="small muted">Subscribers</div>
                <div id="analyticSubscribers" style="font-weight:700; font-size:1.05rem;">—</div>
              </div>
            </div>
          </div>
          <div class="chart-wrap">
            <canvas id="compactChart" height="140"></canvas>
          </div>
        </div>
      </div>

      <div class="main-grid" role="main">
        <section class="panel" aria-labelledby="recentHeading">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h2 id="recentHeading" style="margin:0; font-weight:600;">Recent Activities</h2>
            <div style="display:flex; gap:8px; align-items:center;">
              <button id="showAllActivities" class="btn small">Show all</button>
              <button id="loadMoreBtn" class="btn small" style="display:none;">Load more</button>
            </div>
          </div>

          <div id="activitiesList">
            <div class="skeleton" style="height:64px"></div>
            <div class="skeleton" style="height:64px"></div>
            <div class="skeleton" style="height:64px"></div>
          </div>
        </section>

        <aside>
          <div class="panel detail-box" aria-labelledby="detailsHeading">
            <h3 id="detailsHeading" style="margin:0 0 8px 0; font-weight:600;">Details</h3>
            <div id="detailContent" style="color:#334155; min-height:160px;">Select an activity to view details.</div>
          </div>

          <div class="panel" style="margin-top:12px;" aria-labelledby="timelineHeading">
            <h3 id="timelineHeading" style="margin:0 0 8px 0; font-weight:600;">Activity Timeline</h3>
            <div id="timeline" class="muted">No activity selected.</div>
          </div>
        </aside>
      </div>

    </div>
  </main>

  <script>
    /* ------------------------------------------------------------------
       Sidebar behavior (YNEX-like): desktop expanded by default, pin saved
       ------------------------------------------------------------------ */
    (function() {
  const PIN_KEY = 'dg_sidebar_pinned_v1';
  const COLLAPSE_KEY = 'dg_sidebar_collapsed_v1';
  const body = document.body;
      const sidebar = document.getElementById('sidebar');
      const openMobileBtn = document.getElementById('openMobileSidebar');
      const overlay = document.getElementById('overlay');
      const pinBtn = document.getElementById('pinSidebarBtn');
      const openMobileClose = document.getElementById('openMobileClose');

  // persisted user collapse (separate from pin)
  let collapsed = localStorage.getItem(COLLAPSE_KEY) === '1';

      function isDesktop() { return window.innerWidth > 1024; }

      // Read persisted pin state
      let pinned = localStorage.getItem(PIN_KEY) === '1';

      function applyPinState() {
        if (pinned) {
          body.classList.add('sidebar-expanded');
          if (pinBtn) pinBtn.textContent = 'Unpin';
          if (sidebar) sidebar.setAttribute('aria-hidden', 'false');
          // pinned wins over collapsed user preference
          collapsed = false;
          try { localStorage.removeItem(COLLAPSE_KEY); } catch (err) {}
        } else {
          // YNEX-like: desktop shows expanded by default even if not pinned
          if (isDesktop()) {
            body.classList.add('sidebar-expanded');
          } else {
            body.classList.remove('sidebar-expanded');
          }
          if (pinBtn) pinBtn.textContent = 'Pin';
        }
      }

      function applyCollapsedState() {
        // If pinned, ignore collapsed
        if (pinned) {
          body.classList.add('sidebar-expanded');
          return;
        }
        if (collapsed) {
          body.classList.remove('sidebar-expanded');
        } else {
          // desktop default is expanded, mobile default collapsed unless opened
          if (isDesktop()) body.classList.add('sidebar-expanded');
          else body.classList.remove('sidebar-expanded');
        }
        updateSidebarItemTitles();
      }

      function updateSidebarItemTitles() {
        // When collapsed (no .sidebar-expanded), ensure icons have native title for tooltips
        const isExpanded = document.body.classList.contains('sidebar-expanded');
        document.querySelectorAll('#sidebar .sidebar-item').forEach(a => {
          const lbl = a.querySelector('.sidebar-item-label');
          const text = lbl ? lbl.textContent.trim() : a.textContent.trim();
          if (!isExpanded) {
            a.setAttribute('title', text);
            a.setAttribute('aria-label', text);
          } else {
            a.removeAttribute('title');
            a.removeAttribute('aria-label');
          }
        });
      }

      function openMobile() {
        body.classList.add('sidebar-open-mobile');
        body.classList.add('sidebar-expanded'); // show labels while mobile menu open
        document.body.style.overflow = 'hidden';
        if (overlay) overlay.style.display = 'block';
        if (openMobileClose) openMobileClose.style.display = 'inline-flex';
        if (sidebar) sidebar.setAttribute('aria-hidden', 'false');
      }

      function closeMobile() {
        body.classList.remove('sidebar-open-mobile');
        // restore non-mobile expanded state depending on pinned/viewport
        if (!pinned && !isDesktop()) body.classList.remove('sidebar-expanded');
        document.body.style.overflow = '';
        if (overlay) overlay.style.display = 'none';
        if (openMobileClose) openMobileClose.style.display = 'none';
        if (sidebar) sidebar.setAttribute('aria-hidden', String(!isDesktop()));
      }

      // Initialize
      (function init() {
        // Ensure the sidebar class mobile-hidden removed on desktop
        if (sidebar && isDesktop()) sidebar.classList.remove('mobile-hidden');

  applyPinState();
  // apply any explicit collapsed state (user preference)
  applyCollapsedState();
  // set initial aria state for hamburger
  if (openMobileBtn) {
    if (isDesktop()) {
      try { openMobileBtn.setAttribute('aria-expanded', String(!collapsed)); } catch (err){}
    } else {
      try { openMobileBtn.setAttribute('aria-expanded', String(document.body.classList.contains('sidebar-open-mobile'))); } catch (err){}
    }
  }

        // mobile close button visibility
        if (openMobileClose) {
          openMobileClose.style.display = body.classList.contains('sidebar-open-mobile') ? 'inline-flex' : 'none';
          openMobileClose.addEventListener('click', (e) => {
            e.preventDefault();
            closeMobile();
          });
        }

        // Overlay initial aria-hidden
        if (overlay) overlay.setAttribute('aria-hidden', 'true');
      })();

      // Toggle handler for openMobileBtn (hamburger)
      if (openMobileBtn) {
        openMobileBtn.addEventListener('click', (e) => {
          e.preventDefault();
          if (isDesktop()) {
            // On desktop: toggle collapsed state and persist it
            collapsed = !collapsed;
            try { localStorage.setItem(COLLAPSE_KEY, collapsed ? '1' : '0'); } catch (err){}
            applyCollapsedState();
            try { openMobileBtn.setAttribute('aria-expanded', String(!collapsed)); } catch (err){}
          } else {
            // Mobile: open/close off-canvas
            if (document.body.classList.contains('sidebar-open-mobile')) {
              closeMobile();
              try { openMobileBtn.setAttribute('aria-expanded','false'); } catch (err){}
            } else {
              openMobile();
              try { openMobileBtn.setAttribute('aria-expanded','true'); } catch (err){}
            }
          }
        });
      }

      // Overlay click closes mobile menu
      if (overlay) overlay.addEventListener('click', closeMobile);

      // Desktop: expand on hover if not pinned, collapse on leave
      if (sidebar) {
        sidebar.addEventListener('mouseenter', () => {
          if (isDesktop()) body.classList.add('sidebar-expanded');
        });
        sidebar.addEventListener('mouseleave', () => {
          if (isDesktop() && !pinned) body.classList.remove('sidebar-expanded');
        });
      }

      // Pin toggle: update localStorage
      if (pinBtn) {
        pinBtn.addEventListener('click', (e) => {
          e.preventDefault();
          pinned = !pinned;
          localStorage.setItem(PIN_KEY, pinned ? '1' : '0');
          applyPinState();
        });
      }

      // Close mobile on ESC
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMobile(); });

      // Handle resizes: keep behavior sensible when viewport crosses breakpoint
      let resizeTimer = null;
      window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
          // If now desktop, ensure sidebar not hidden
          if (isDesktop()) {
            if (sidebar) sidebar.classList.remove('mobile-hidden');
            // If pinned -> expanded; if not pinned -> expanded by default (YNEX-like)
            if (pinned) body.classList.add('sidebar-expanded');
            else body.classList.add('sidebar-expanded');
            // ensure overlay/close hidden
            if (overlay) overlay.style.display = 'none';
            if (openMobileClose) openMobileClose.style.display = 'none';
            document.body.style.overflow = '';
            body.classList.remove('sidebar-open-mobile');
          } else {
            // On small screens, remove expanded unless pinned
            if (!pinned) body.classList.remove('sidebar-expanded');
            // keep mobile-hidden status
            if (sidebar) sidebar.classList.add('mobile-hidden');
            body.classList.remove('sidebar-open-mobile');
            if (overlay) overlay.style.display = 'none';
            if (openMobileClose) openMobileClose.style.display = 'none';
            document.body.style.overflow = '';
          }
        }, 120);
      });
      // ensure titles/tooltips reflect current collapsed/expanded state after resize
      window.addEventListener('resize', () => { updateSidebarItemTitles(); });
    })();


    // ---------- Data & rendering logic (preserved + paging + quick actions) ----------
    let _compactChart = null;
    window._activitiesCache = [];
    let _renderIndex = 0;
    const PAGE_SIZE = 8;

    function showActivitySkeletons() {
      const container = document.getElementById('activitiesList');
      container.innerHTML = '';
      for (let i=0;i<4;i++){
        const s = document.createElement('div');
        s.className = 'skeleton';
        s.style.height = '64px';
        s.style.borderRadius = '10px';
        container.appendChild(s);
      }
      const loadBtn = document.getElementById('loadMoreBtn');
      if (loadBtn) loadBtn.style.display = 'none';
    }

    async function fetchStats() {
      try {
        ['adsCount','subsCount','postsCount'].forEach(id=>{
          const el = document.getElementById(id);
          if (el) el.textContent = '—';
        });
        const res = await fetch('../api/admin_stats.php');
        const json = await res.json();
        if (json.success) {
          const d = json.data || {};
          const ads = (typeof d.ads !== 'undefined') ? d.ads : (d.adsCount || 0);
          const subs = (typeof d.subscribers !== 'undefined') ? d.subscribers : (d.subsCount || 0);
          // Posts fallback: try several keys, then fallback to counting cached activities
          let postsNum = Number(d.posts ?? d.postsCount ?? d.post_count ?? d.total_posts ?? 0);
          if ((!postsNum || postsNum === 0) && window._activitiesCache && window._activitiesCache.length) {
            postsNum = window._activitiesCache.filter(i => i.type === 'post').length;
          }
          document.getElementById('adsCount').textContent = String(ads);
          document.getElementById('subsCount').textContent = String(subs);
          document.getElementById('postsCount').textContent = String(postsNum);
        }
      } catch (e) { console.error(e); }
      finally { fetchServerAnalytics().catch(e=>console.error(e)); }
    }

    async function fetchActivities(filterType = '') {
      try {
        showActivitySkeletons();
        const res = await fetch('../api/admin_activities.php');
        const json = await res.json();
        if (!json.success) {
          renderActivities([]);
          return [];
        }
        let items = json.data || [];
        if (filterType && filterType !== 'all') items = items.filter(i => i.type === filterType);
        // sort if not already
        items.sort((a,b) => {
          const ta = a.created_at ? new Date(a.created_at).getTime() : 0;
          const tb = b.created_at ? new Date(b.created_at).getTime() : 0;
          return tb - ta;
        });
        window._activitiesCache = items.slice();
        _renderIndex = 0;
        renderActivitiesPage();
        updateAnalyticsFromItems(items);
        // Ensure posts stat reflects the freshest activity data when API report is missing or zero
        try {
          const postsEl = document.getElementById('postsCount');
          if (postsEl) {
            const postsFromItems = items.filter(it => it.type === 'post').length;
            if (postsFromItems > 0) postsEl.textContent = String(postsFromItems);
          }
        } catch (err) { /* ignore */ }
        fetchServerAnalytics(filterType).catch(err => console.error('server analytics error', err));
        return window._activitiesCache;
      } catch (e) { console.error(e); renderActivities([]); return []; }
    }

    function renderActivitiesPage() {
      const items = window._activitiesCache || [];
      const start = _renderIndex;
      const end = Math.min(_renderIndex + PAGE_SIZE, items.length);
      const slice = items.slice(start, end);
      if (start === 0) {
        document.getElementById('activitiesList').innerHTML = '';
      }
      slice.forEach(item => appendActivityItem(item));
      _renderIndex = end;
      const loadMoreBtn = document.getElementById('loadMoreBtn');
      if (_renderIndex < items.length) {
        loadMoreBtn.style.display = 'inline-flex';
      } else {
        loadMoreBtn.style.display = 'none';
      }
    }

    function appendActivityItem(item) {
      const container = document.getElementById('activitiesList');
      const el = document.createElement('div');
      el.className = 'activity-item';
      el.dataset.id = item.id;
      el.dataset.type = item.type;
      const when = item.created_at ? new Date(item.created_at).toLocaleString() : '';
      let icon = '🔔';
      if (item.type === 'ad') icon = '📢';
      else if (item.type === 'subscriber') icon = '📨';
      else if (item.type === 'post') icon = '📝';
      else if (item.type === 'booking') icon = '📅';
      else if (item.type === 'gallery') icon = '🖼️';

      const actions = [];
      if (item.type === 'ad') {
        actions.push(`<button class="action-btn" data-action="edit-ad" data-id="${item.id}" title="Edit ad">✏️</button>`);
      }
      if (item.type === 'subscriber') {
        actions.push(`<button class="action-btn" data-action="email-subscriber" data-email="${escapeAttr(item.title)}" title="Email subscriber">✉️</button>`);
      }
      if (item.type === 'post') {
        actions.push(`<button class="action-btn" data-action="edit-post" data-id="${item.id}" title="Edit post">✏️</button>`);
      }
      if (item.type === 'booking') {
        actions.push(`<button class="action-btn" data-action="view-booking" data-id="${item.id}" title="View booking">🔎</button>`);
      }
      if (item.type === 'gallery') {
        actions.push(`<button class="action-btn" data-action="view-gallery" data-id="${item.id}" title="View media">🖼️</button>`);
      }

      el.innerHTML = `
        <div style="display:flex; gap:12px; align-items:start; width:100%;">
          <div class="activity-badge">${icon}</div>
          <div style="flex:1;">
            <div style="font-weight:600;color:var(--navy);font-size:.95rem;">${escapeHtml(item.title)}</div>
            <div style="font-size:.82rem;color:var(--muted);margin-top:6px;">${escapeHtml(item.subtype || item.status || '')} • ${when}</div>
          </div>
          <div style="display:flex; align-items:center; gap:6px;">
            <div class="activity-actions">${actions.join('')}</div>
          </div>
        </div>
      `;

      // click area: load details and context
      el.addEventListener('click', (e) => {
        if (e.target.closest('.action-btn')) return;
        document.querySelectorAll('#activitiesList .activity-item').forEach(n => n.style.background='');
        el.style.background = '#f8fafc';
        loadDetail(item.id, item.type);
        updateAnalyticsContext(item);
      });

      // delegate action buttons
      el.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', (ev) => {
          ev.stopPropagation();
          const act = btn.dataset.action;
          if (act === 'edit-ad') {
            const id = btn.dataset.id;
            window.location.href = `ads_list.php?edit=${encodeURIComponent(id)}`;
          } else if (act === 'email-subscriber') {
            const mail = btn.dataset.email || '';
            window.location.href = `mailto:${encodeURIComponent(mail)}`;
          } else if (act === 'edit-post') {
            const id = btn.dataset.id;
            window.location.href = `blog_post_form.php?id=${encodeURIComponent(id)}`;
          } else if (act === 'view-booking') {
            const id = btn.dataset.id;
            window.location.href = `bookme_requests.php?view=${encodeURIComponent(id)}`;
          } else if (act === 'view-gallery') {
            const id = btn.dataset.id;
            window.location.href = `gallery_form.php?id=${encodeURIComponent(id)}`;
          }
        });
      });

      container.appendChild(el);
    }

    function renderActivities(items) {
      document.getElementById('activitiesList').innerHTML = '';
      window._activitiesCache = items.slice();
      _renderIndex = 0;
      renderActivitiesPage();
    }

    function renderAllActivities(items) {
      const container = document.getElementById('activitiesList');
      container.innerHTML = '';
      window._activitiesCache = items.slice();
      _renderIndex = items.length;
      items.forEach(item => appendActivityItem(item));
      const loadMoreBtn = document.getElementById('loadMoreBtn');
      if (loadMoreBtn) loadMoreBtn.style.display = 'none';
    }

    function updateAnalyticsFromItems(items) {
      const counts = { booking:0, post:0, subscriber:0, ad:0, gallery:0, other:0 };
      const last7 = {};
      const today = new Date();
      for (let i=0;i<7;i++) { const d = new Date(today); d.setDate(today.getDate()-i); last7[d.toISOString().slice(0,10)] = 0; }
      items.forEach(it => {
        if (counts[it.type] !== undefined) counts[it.type]++; else counts.other++;
        if (it.created_at) {
          const key = new Date(it.created_at).toISOString().slice(0,10);
          if (last7[key] !== undefined) last7[key]++;
        }
      });
      document.getElementById('analyticTotal').textContent = String(items.length);
      document.getElementById('analyticBookings').textContent = String(counts.booking);
      document.getElementById('analyticPosts').textContent = String(counts.post);
      document.getElementById('analyticSubscribers').textContent = String(counts.subscriber);

      const labels = Object.keys(last7).reverse();
      const data = labels.map(l => last7[l]);
      const ctx = document.getElementById('compactChart').getContext('2d');
      if (_compactChart) _compactChart.destroy();
      // Keep chart visually stable on desktop by disabling Chart.js responsive resizing there
      const isDesktop = window.innerWidth > 900;
      _compactChart = new Chart(ctx, {
        type: 'bar',
        data: { labels: labels, datasets: [{ label: 'Activities', data: data, backgroundColor: '#60a5fa' }] },
        options: {
          plugins:{legend:{display:false}},
          scales:{x:{display:false}, y:{display:false}},
          responsive: !isDesktop,
          maintainAspectRatio: false
        }
      });
    }

    function updateAnalyticsContext(item) {
      const ctxEl = document.getElementById('analyticsContext') || document.getElementById('timeline');
      if (!item) { if (ctxEl) ctxEl.textContent = 'Click an item to see context'; return; }
      fetch(`../api/admin_analytics_context.php?type=${encodeURIComponent(item.type)}&id=${encodeURIComponent(item.id)}`)
        .then(r => r.json())
        .then(data => {
          if (!data.success) { if (ctxEl) ctxEl.textContent = 'No context available'; return; }
          const d = data.data || {};
          let html = `<strong style="display:block;margin-bottom:6px;">${escapeHtml(item.type.toUpperCase())}</strong>`;
          if (d.record) {
            if (item.type === 'booking') {
              const b = d.record;
              html += `<div class="small muted">${escapeHtml(b.organization || '')}</div>`;
              html += `<div style="margin-top:6px;color:#334155">Status: ${escapeHtml(b.status || '')}</div>`;
            } else if (item.type === 'post') {
              const p = d.record;
              html += `<div class="small muted">Author: ${escapeHtml(p.author || '')}</div>`;
            } else if (item.type === 'ad') {
              const a = d.record;
              html += `<div class="small muted">Type: ${escapeHtml(a.type || '')}</div>`;
            }
          }
          if (d.status_distribution) {
            html += `<div style="margin-top:8px;color:var(--muted);">Status distribution:</div><ul style="margin-top:6px;color:#334155">`;
            for (const k in d.status_distribution) html += `<li>${escapeHtml(k)}: ${d.status_distribution[k]}</li>`;
            html += '</ul>';
          }
          if (ctxEl) ctxEl.innerHTML = html;
        }).catch(err => { console.error('analytics context error', err); if (ctxEl) ctxEl.textContent = 'Context error'; });
    }

    async function fetchServerAnalytics(filterType) {
      const params = new URLSearchParams();
      params.set('days', 7);
      if (filterType && filterType !== 'all') params.set('type', filterType);
      try {
        const res = await fetch('../api/admin_analytics.php?' + params.toString());
        const json = await res.json();
        if (!json.success) return;
        const d = json.data || {};
        if (d.time_series) {
          const labels = Object.keys(d.time_series);
          const data = labels.map(l => d.time_series[l]);
          const ctx = document.getElementById('compactChart').getContext('2d');
          if (_compactChart) _compactChart.destroy();
          const isDesktop = window.innerWidth > 900;
          _compactChart = new Chart(ctx, {
            type: 'line',
            data: { labels: labels, datasets:[{ label:'Activity', data: data, borderColor:'#60a5fa', backgroundColor:'rgba(96,165,250,0.15)', fill:true }] },
            options: { plugins:{legend:{display:false}}, responsive: !isDesktop, maintainAspectRatio:false }
          });
        }
      } catch(e) { console.error(e); }
    }

    async function loadDetail(id, type) {
      try {
        const res = await fetch(`../api/admin_activity_detail.php?id=${id}&type=${encodeURIComponent(type)}`);
        const json = await res.json();
        const panel = document.getElementById('detailContent');
        if (!json.success) { panel.innerHTML = '<div class="muted">Unable to load details.</div>'; return; }
        const d = json.data;
        if (d.type === 'ad') {
          const ad = d.detail;
          panel.innerHTML = `<h3 style="margin:0 0 8px 0;font-weight:600">${escapeHtml(ad.name)}</h3>
            <div class="muted" style="margin-bottom:8px;">Type: ${escapeHtml(ad.type)} • Status: ${escapeHtml(ad.status)}</div>
            <div style="color:#334155">${escapeHtml(ad.body).replace(/\n/g,'<br>')}</div>`;
        } else if (d.type === 'subscriber') {
          const s = d.detail;
          panel.innerHTML = `<h3 style="margin:0 0 8px 0;font-weight:600">Subscriber — ${escapeHtml(s.email)}</h3>
            <div class="muted">${escapeHtml(s.name || '-')}</div>`;
        } else if (d.type === 'post') {
          const p = d.detail;
          panel.innerHTML = `<h3 style="margin:0 0 8px 0;font-weight:600">${escapeHtml(p.title)}</h3>
            <div class="muted">Author: ${escapeHtml(p.author || '-')}</div>
            <div style="margin-top:8px;color:#334155">${escapeHtml(p.body || '').replace(/\n/g,'<br>')}</div>`;
        } else {
          panel.innerHTML = '<div class="muted">No detail template for this activity type.</div>';
        }
      } catch (e) { console.error(e); }
    }

    function escapeHtml(str){ if(!str && str!==0) return ''; return String(str).replace(/[&<>\"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }
    function escapeAttr(s){ return escapeHtml(s).replace(/'/g, '&#39;'); }

    // UI bindings
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) refreshBtn.addEventListener('click', () => { fetchStats(); fetchActivities(); });
    const subsCard = document.getElementById('subsCard');
    if (subsCard) subsCard.addEventListener('click', () => { fetchActivities('subscriber'); });
    const adsCard = document.getElementById('adsCard'); if (adsCard) adsCard.addEventListener('click', () => { fetchActivities('ad'); });
    const postsCard = document.getElementById('postsCard'); if (postsCard) postsCard.addEventListener('click', () => { fetchActivities('post'); });

    // sidebar-link filtering (desktop & mobile)
    document.querySelectorAll('.sidebar-link').forEach(a => {
      a.addEventListener('click', (e) => {
        e.preventDefault();
        const type = a.dataset.activityType || '';
        if (type && type !== 'all') fetchActivities(type);
        else fetchActivities();
        // close mobile if open
        document.body.classList.remove('sidebar-open-mobile');
        // hide overlay & restore scroll
        document.body.style.overflow = '';
        const ov = document.getElementById('overlay');
        if (ov) ov.style.display = 'none';
      });
      a.addEventListener('keydown', (e) => { if (e.key === 'Enter') a.click(); });
    });

    // Load more and Show All controls
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) loadMoreBtn.addEventListener('click', (e) => {
      e.preventDefault();
      renderActivitiesPage();
    });
    const showAllBtn = document.getElementById('showAllActivities');
    if (showAllBtn) showAllBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      // Always refresh activities from server then render the full list
      try {
        await fetchActivities();
        renderAllActivities(window._activitiesCache || []);
      } catch (err) {
        console.error('Error refreshing activities for Show all', err);
        // fallback to cached render
        renderAllActivities(window._activitiesCache || []);
      }
    });

    // initial load
    showActivitySkeletons();
    fetchStats();
    fetchActivities();
    setInterval(()=>{ fetchStats(); fetchActivities(); }, 300000);
  </script>
</body>
</html>
