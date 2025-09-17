<?php
// api/fetch_books.php
header('Content-Type: application/json; charset=utf-8');

// Turn off HTML error output so we don't break JSON
ini_set('display_errors', 0);

require_once __DIR__ . '/../db.php';  // must define either $conn (mysqli) or $pdo (PDO)

$rows = [];

try {
    if (isset($conn) && $conn instanceof mysqli) {
        // MYSQLI
        $sql = "SELECT id, title, price FROM books ORDER BY created_at DESC";
        if (!$res = $conn->query($sql)) {
            http_response_code(500);
            echo json_encode(["error" => $conn->error]);
            exit;
        }
        while ($r = $res->fetch_assoc()) {
            $r['id'] = (int)$r['id'];
            $r['price'] = (float)$r['price'];
            $rows[] = $r;
        }
        echo json_encode($rows);
        exit;
    }

    if (isset($pdo) && $pdo instanceof PDO) {
        // PDO
        $stmt = $pdo->query("SELECT id, title, price FROM books ORDER BY created_at DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($data as &$r) {
            $r['id'] = (int)$r['id'];
            $r['price'] = (float)$r['price'];
        }
        echo json_encode($data);
        exit;
    }

    // No DB handle found
    http_response_code(500);
    echo json_encode(["error" => "DB connection not found in db.php"]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
