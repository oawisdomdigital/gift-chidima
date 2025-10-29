<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}
require_once __DIR__ . '/api.php';
// Content-Type and CORS are handled by api.php / cors.php

$type = $_GET['type'] ?? '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

function table_exists($mysqli, $table) {
    $t = $mysqli->real_escape_string($table);
    $res = $mysqli->query("SHOW TABLES LIKE '".$t."'");
    return ($res && $res->num_rows>0);
}

$out = ['success'=>true,'data'=>[]];

if ($type === 'booking') {
    if (!table_exists($mysqli,'booking_requests')) { echo json_encode(['success'=>false,'message'=>'No bookings table']); exit(); }
    // status distribution
    $res = $mysqli->query("SELECT status, COUNT(*) AS cnt FROM booking_requests GROUP BY status");
    $dist = [];
    if ($res) while ($r = $res->fetch_assoc()) $dist[$r['status']] = intval($r['cnt']);
    $out['data']['status_distribution'] = $dist;

    // recent by day last 30
    $days = 30;
    $series = [];
    $today = new DateTime();
    for ($i=0;$i<$days;$i++) { $d = clone $today; $d->modify("-{$i} days"); $series[$d->format('Y-m-d')] = 0; }
    $res = $mysqli->query("SELECT DATE(created_at) as d, COUNT(*) as cnt FROM booking_requests WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL $days DAY) GROUP BY DATE(created_at)");
    if ($res) while ($r = $res->fetch_assoc()) { $k = $r['d']; if (isset($series[$k])) $series[$k] = intval($r['cnt']); }
    $out['data']['recent_by_day'] = array_reverse($series, true);

    // if id provided, return record summary
    if ($id>0) {
        $stmt = $mysqli->prepare("SELECT id, name, email, organization, event_type, event_date, location, audience_size, topics, budget, status, created_at FROM booking_requests WHERE id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i',$id); $stmt->execute(); $res = $stmt->get_result(); $row = $res->fetch_assoc();
            $out['data']['record'] = $row ?: null;
        }
    }

    echo json_encode($out); exit();
}

if ($type === 'post') {
    if (!table_exists($mysqli,'posts')) { echo json_encode(['success'=>false,'message'=>'No posts table']); exit(); }
    // average length, recent posts, top authors
    $res = $mysqli->query("SELECT AVG(CHAR_LENGTH(body)) AS avg_chars, AVG( (LENGTH(body) - LENGTH(REPLACE(body, ' ', '')))+1 ) AS avg_words FROM posts");
    if ($res) $out['data']['averages'] = $res->fetch_assoc();
    $res = $mysqli->query("SELECT author, COUNT(*) AS cnt FROM posts GROUP BY author ORDER BY cnt DESC LIMIT 5");
    $authors = [];
    if ($res) while ($r = $res->fetch_assoc()) $authors[] = $r;
    $out['data']['top_authors'] = $authors;
    if ($id>0) {
        $stmt = $mysqli->prepare("SELECT id, title, body, author, created_at FROM posts WHERE id = ? LIMIT 1");
        if ($stmt) { $stmt->bind_param('i',$id); $stmt->execute(); $out['data']['record'] = $stmt->get_result()->fetch_assoc(); }
    }
    echo json_encode($out); exit();
}

if ($type === 'ad') {
    if (!table_exists($mysqli,'advertisements')) { echo json_encode(['success'=>false,'message'=>'No advertisements table']); exit(); }
    // if impressions column exists, return sum and recent trend
    $hasImpr = false; $cols = $mysqli->query("SHOW COLUMNS FROM advertisements");
    if ($cols) { while ($c = $cols->fetch_assoc()) { if (in_array(strtolower($c['Field']), ['impressions','views','impression'])) $hasImpr = true; } }
    if ($hasImpr) {
        $r = $mysqli->query("SELECT SUM(impressions) AS impressions FROM advertisements"); if ($r) $out['data']['impressions_total'] = intval($r->fetch_assoc()['impressions'] ?: 0);
        // last 30 days impressions per day if impressions are recorded per-row by date (best effort)
        $days = 30; $series = []; $today=new DateTime(); for ($i=0;$i<$days;$i++){ $d=clone $today; $d->modify("-{$i} days"); $series[$d->format('Y-m-d')]=0; }
        // best-effort: if there is an impressions_history table skip, else just counts
        $res = $mysqli->query("SELECT DATE(created_at) as d, SUM(impressions) as s FROM advertisements WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL $days DAY) GROUP BY DATE(created_at)");
        if ($res) while ($r = $res->fetch_assoc()) { $k=$r['d']; if (isset($series[$k])) $series[$k]=intval($r['s']); }
        $out['data']['recent_impressions'] = array_reverse($series,true);
    } else {
        // fallback: counts
        $r = $mysqli->query("SELECT COUNT(*) AS cnt FROM advertisements"); if ($r) $out['data']['count'] = intval($r->fetch_assoc()['cnt'] ?: 0);
    }
    if ($id>0) { $stmt = $mysqli->prepare("SELECT id, name, type, status, body, created_at, updated_at FROM advertisements WHERE id = ? LIMIT 1"); if ($stmt){ $stmt->bind_param('i',$id); $stmt->execute(); $out['data']['record'] = $stmt->get_result()->fetch_assoc(); } }
    echo json_encode($out); exit();
}

// fallback: return totals
echo json_encode(['success'=>true,'data'=>['message'=>'Unsupported type','supported'=>['booking','post','ad']]]);
exit();
