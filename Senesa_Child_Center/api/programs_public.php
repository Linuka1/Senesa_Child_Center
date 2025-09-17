<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . "/../db.php"; // adjust path if your db.php is elsewhere

$sql = "SELECT id, name, summary, age_group AS age, image 
        FROM programs 
        WHERE status='Active'
        ORDER BY id ASC";

$res = $conn->query($sql);
if (!$res) {
    echo json_encode(['ok' => false, 'error' => $conn->error]);
    exit;
}

$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
}

echo json_encode(['ok' => true, 'data' => $rows]);
