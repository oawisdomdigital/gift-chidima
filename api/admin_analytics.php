<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/api.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}
// Content-Type and CORS handled by api.php/cors.php

$type = $_GET['type'] ?? 'all';
$start = $_GET['start_date'] ?? null; // YYYY-MM-DD
$end = $_GET['end_date'] ?? null;     // YYYY-MM-DD

// helper: safe exists
function table_exists($mysqli, $table) {
    $t = $mysqli->real_escape_string($table);
    $res = $mysqli->query("SHOW TABLES LIKE '".$t."'");
    return ($res && $res->num_rows>0);
}

$out = ['success'=>true,'data'=>[]];

// totals
$totals = [];
if (table_exists($mysqli,'advertisements')) {
    $r = $mysqli->query("SELECT COUNT(*) AS cnt FROM advertisements");
    $totals['ads'] = $r ? intval($r->fetch_assoc()['cnt']) : 0;
} else { $totals['ads'] = 0; }

if (table_exists($mysqli,'posts')) {
    $r = $mysqli->query("SELECT COUNT(*) AS cnt FROM posts");
    $totals['posts'] = $r ? intval($r->fetch_assoc()['cnt']) : 0;
} else { $totals['posts'] = 0; }

if (table_exists($mysqli,'newsletter_subscribers')) {
    $r = $mysqli->query("SELECT COUNT(*) AS cnt FROM newsletter_subscribers");
    $totals['subscribers'] = $r ? intval($r->fetch_assoc()['cnt']) : 0;
} else { $totals['subscribers'] = 0; }

if (table_exists($mysqli,'booking_requests')) {
    $r = $mysqli->query("SELECT COUNT(*) AS cnt FROM booking_requests");
    $totals['bookings'] = $r ? intval($r->fetch_assoc()['cnt']) : 0;
} else { $totals['bookings'] = 0; }

if (table_exists($mysqli,'gallery')) {
    $r = $mysqli->query("SELECT COUNT(*) AS cnt FROM gallery");
    $totals['gallery'] = $r ? intval($r->fetch_assoc()['cnt']) : 0;
} else { $totals['gallery'] = 0; }

$out['data']['totals'] = $totals;

// booking status distribution
if (table_exists($mysqli,'booking_requests')) {
    $res = $mysqli->query("SELECT status, COUNT(*) AS cnt FROM booking_requests GROUP BY status");
    $dist = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) { $dist[$row['status']] = intval($row['cnt']); }
    }
    $out['data']['booking_status_distribution'] = $dist;
}

// posts: average length (chars) and avg words
if (table_exists($mysqli,'posts')) {
    $res = $mysqli->query("SELECT AVG(CHAR_LENGTH(body)) AS avg_chars, AVG( (LENGTH(body) - LENGTH(REPLACE(body, ' ', '')))+1 ) AS avg_words FROM posts");
    if ($res) {
        $row = $res->fetch_assoc();
        $out['data']['posts'] = ['avg_chars' => intval($row['avg_chars'] ?: 0), 'avg_words' => intval($row['avg_words'] ?: 0)];
    }
}

// ads: try to return impressions if column exists
if (table_exists($mysqli,'advertisements')) {
    $hasImpr = false;
    $cols = $mysqli->query("SHOW COLUMNS FROM advertisements");
    if ($cols) {
        while ($c = $cols->fetch_assoc()) { if (in_array(strtolower($c['Field']), ['impressions','views','impression'])) $hasImpr = true; }
    }
    if ($hasImpr) {
        $res = $mysqli->query("SELECT SUM(impressions) AS impressions FROM advertisements");
        if ($res) { $row = $res->fetch_assoc(); $out['data']['ads_impressions'] = intval($row['impressions'] ?: 0); }
    }
}

// time series: combine created_at from available tables for last N days (default 7)
$days = 7;
if (isset($_GET['days'])) $days = max(1, intval($_GET['days']));
$series = [];
$today = new DateTime();
for ($i=0;$i<$days;$i++) { $d = clone $today; $d->modify("-{$i} days"); $series[$d->format('Y-m-d')] = 0; }

$tables = ['advertisements'=>'created_at','posts'=>'created_at','newsletter_subscribers'=>'created_at','booking_requests'=>'created_at','gallery'=>'created_at'];
foreach ($tables as $tbl=>$col) {
    if (!table_exists($mysqli,$tbl)) continue;
    $res = $mysqli->query("SELECT DATE($col) as d, COUNT(*) as cnt FROM $tbl WHERE $col >= DATE_SUB(CURDATE(), INTERVAL $days DAY) GROUP BY DATE($col)");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $d = $r['d'];
            if (isset($series[$d])) $series[$d] += intval($r['cnt']);
        }
    }
}

$out['data']['time_series'] = array_reverse($series, true);

echo json_encode($out);
exit;
