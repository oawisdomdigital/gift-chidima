<?php
// Minimal header for admin pages with theme + menu-mode toggles
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="bg-white border-b">
  <div class="container mx-auto px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-4">
      <a href="dashboard.php" class="text-lg font-bold text-gray-900">Admin Dashboard</a>
    </div>

    <!-- right side: primary nav + theme + menu-mode + user (links aligned to right) -->
    <div class="text-sm flex items-center gap-4">

  <!-- primary nav (desktop) moved to right -->
      <nav class="hidden md:flex items-center gap-3 text-sm text-gray-600" aria-label="Primary admin navigation">
        <div class="relative group" role="navigation" aria-label="Admin navigation">
          <span class="hover:text-gray-900 cursor-pointer">Homepage</span>
          <div class="absolute left-0 w-48 bg-white rounded-md shadow-lg p-2 hidden group-hover:block z-50">
            <a href="hero_form.php" class="block px-4 py-2 hover:bg-gray-100 rounded">Hero Section</a>
            <a href="biography_form.php" class="block px-4 py-2 hover:bg-gray-100 rounded">Biography</a>
            <a href="key_highlights_form.php" class="block px-4 py-2 hover:bg-gray-100 rounded">Key Highlights</a>
            <a href="awards_form.php" class="block px-4 py-2 hover:bg-gray-100 rounded">Awards</a>
            <a href="ventures_form.php" class="block px-4 py-2 hover:bg-gray-100 rounded">Ventures</a>
            <a href="testimonials_form.php" class="block px-4 py-2 hover:bg-gray-100 rounded">Testimonials</a>
            <a href="final_cta_form.php" class="block px-4 py-2 hover:bg-gray-100 rounded">Final CTA</a>
            <a href="footer_form.php" class="block px-4 py-2 hover:bg-gray-100 rounded">Footer</a>
          </div>
        </div>

        <div class="relative group">
          <span class="hover:text-gray-900 cursor-pointer">Books</span>
          <div class="absolute left-0 mt-1 w-48 bg-white rounded-md shadow-lg p-2 hidden group-hover:block z-50">
            <a href="books_list.php" class="block px-4 py-2 hover:bg-gray-100 rounded">List Books</a>
            <a href="books_add.php" class="block px-4 py-2 hover:bg-gray-100 rounded">Add Book</a>
            <a href="store_banner_form.php" class="block px-4 py-2 hover:bg-gray-100 rounded">Store Banner</a>
          </div>
        </div>

        <div class="relative group">
          <span class="hover:text-gray-900 cursor-pointer">Book Me</span>
          <div class="absolute left-0 mt-1 w-48 bg-white rounded-md shadow-lg p-2 hidden group-hover:block z-50">
            <a href="bookme_sections.php" class="block px-4 py-2 hover:bg-gray-100 rounded">Page Sections</a>
            <a href="bookme_topics.php" class="block px-4 py-2 hover:bg-gray-100 rounded">Speaking Topics</a>
            <a href="bookme_requests.php" class="block px-4 py-2 hover:bg-gray-100 rounded">Booking Requests</a>
          </div>
        </div>

        <div class="relative group">
          <span class="hover:text-gray-900 cursor-pointer">Media</span>
          <div class="absolute left-0 mt-1 w-48 bg-white rounded-md shadow-lg p-2 hidden group-hover:block z-50">
            <a href="gallery_form.php" class="block px-4 py-2 hover:bg-gray-100 rounded">Gallery</a>
            <a href="ads_list.php" class="block px-4 py-2 hover:bg-gray-100 rounded">Advertisements</a>
            <a href="blog_post_form.php" class="block px-4 py-2 hover:bg-gray-100 rounded">Blog Posts</a>
          </div>
        </div>
      </nav>
      <!-- Theme toggle + Menu Mode toggle (persisted via localStorage) -->
      <div class="flex items-center gap-2">
        <button id="themeToggle" class="btn px-2 py-1" aria-pressed="false" title="Toggle light / dark theme">🌓</button>
      </div>

      <!-- user / auth links -->
      <?php if (!empty($_SESSION['admin_id'])): ?>
        <div class="relative" id="headerUser">
          <button id="headerUserBtn" class="text-sm text-gray-700 hover:text-gray-900 focus:outline-none" aria-haspopup="true" aria-expanded="false">
            Hello, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
          </button>
          <div id="headerUserMenu" class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg p-2 hidden z-50" role="menu" aria-hidden="true">
            <a href="profile.php" class="block px-3 py-2 text-sm hover:bg-gray-100 rounded" role="menuitem">Profile</a>
            <a href="inbox.php" class="block px-3 py-2 text-sm hover:bg-gray-100 rounded" role="menuitem">Inbox</a>
            <a href="bookme_requests.php" class="block px-3 py-2 text-sm hover:bg-gray-100 rounded" role="menuitem">Requests</a>
            <div class="border-t my-2"></div>
            <a href="logout.php" class="block px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded" role="menuitem">Logout</a>
          </div>
        </div>
      <?php else: ?>
        <a href="login.php" class="text-blue-600 hover:text-blue-800">Login</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- small inline styles to help header adapt in "dark-mode" if global dark class is present -->
  <style>
    /* If your global stylesheet already handles dark-mode, these may be redundant.
       These rules provide a minimal dark look for this header when body has .dark-mode */
    body.dark-mode header.bg-white{
      background: rgba(8,10,20,0.92) !important;
      border-bottom-color: rgba(255,255,255,0.04) !important;
    }
    body.dark-mode .text-gray-900 { color: #e6eefc !important; }
    body.dark-mode .text-gray-700 { color: #94a3b8 !important; }
    body.dark-mode .bg-white { background-color: transparent !important; }
    /* simple utility for showing/hiding user menu */
    #headerUserMenu.show { display: block !important; }
  </style>
  

  <!-- Theme + Menu Mode script -->
  <script>
    (function(){
      const THEME_KEY = 'dg_theme_v1';
      const MENU_KEY = 'dg_menu_mode_v1'; // 'hover' or 'click'
      const themeToggle = document.getElementById('themeToggle');
      const menuModeBtn = document.getElementById('menuModeBtn');

      // user menu elements
      const userBtn = document.getElementById('headerUserBtn');
      const userMenu = document.getElementById('headerUserMenu');

      // read persisted state
      let theme = localStorage.getItem(THEME_KEY) || 'light';
      let menuMode = localStorage.getItem(MENU_KEY) || 'hover';

      // apply theme to body
      function applyTheme(t) {
        if (t === 'dark') {
          document.body.classList.add('dark-mode');
          if (themeToggle) themeToggle.textContent = '🌙';
          if (themeToggle) themeToggle.setAttribute('aria-pressed','true');
        } else {
          document.body.classList.remove('dark-mode');
          if (themeToggle) themeToggle.textContent = '🌓';
          if (themeToggle) themeToggle.setAttribute('aria-pressed','false');
        }
      }

      // apply menu mode UI
      function applyMenuModeUI() {
        if (menuModeBtn) menuModeBtn.textContent = 'Mode: ' + (menuMode === 'hover' ? 'Hover' : 'Click');
      }

      // initialize
      applyTheme(theme);
      applyMenuModeUI();

      // toggles
      if (themeToggle) {
        themeToggle.addEventListener('click', function(e){
          e.preventDefault();
          theme = (theme === 'dark') ? 'light' : 'dark';
          localStorage.setItem(THEME_KEY, theme);
          applyTheme(theme);
          // also trigger a simple event so other scripts (dashboard) can sync if needed
          window.dispatchEvent(new CustomEvent('dg:theme-changed', { detail: { theme } }));
        });
      }

      if (menuModeBtn) {
        menuModeBtn.addEventListener('click', function(e){
          e.preventDefault();
          menuMode = (menuMode === 'hover') ? 'click' : 'hover';
          localStorage.setItem(MENU_KEY, menuMode);
          applyMenuModeUI();
          window.dispatchEvent(new CustomEvent('dg:menu-mode-changed', { detail: { menuMode } }));
        });
      }

      // small user menu toggle
      if (userBtn && userMenu) {
        userBtn.addEventListener('click', function(e){
          e.preventDefault();
          const open = userMenu.classList.toggle('show');
          userBtn.setAttribute('aria-expanded', String(open));
          userMenu.setAttribute('aria-hidden', String(!open));
        });
        // close when clicking outside
        document.addEventListener('click', function(e){
          if (!userMenu.contains(e.target) && e.target !== userBtn) {
            userMenu.classList.remove('show');
            if (userBtn) userBtn.setAttribute('aria-expanded','false');
            if (userMenu) userMenu.setAttribute('aria-hidden','true');
          }
        });
        // close on ESC
        document.addEventListener('keydown', function(e){
          if (e.key === 'Escape') {
            userMenu.classList.remove('show');
            if (userBtn) userBtn.setAttribute('aria-expanded','false');
            if (userMenu) userMenu.setAttribute('aria-hidden','true');
          }
        });
      }

      // Expose small API for other scripts to query the current theme/menuMode
      window.dgAdmin = window.dgAdmin || {};
      window.dgAdmin.getTheme = () => theme;
      window.dgAdmin.getMenuMode = () => menuMode;

      // Broadcast initial state for pages that already loaded (so they can sync)
      window.dispatchEvent(new CustomEvent('dg:theme-changed', { detail: { theme } }));
      window.dispatchEvent(new CustomEvent('dg:menu-mode-changed', { detail: { menuMode } }));

    })();
  </script>
</header>
