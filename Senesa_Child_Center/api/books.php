<?php
header('Content-Type: application/json');
require_once '../db.php'; // adjust path

$action = $_GET['action'] ?? '';

/* ===== LIST BOOKS ===== */
if ($action === 'list') {
    $rows = [];
    $res = $conn->query("SELECT * FROM books ORDER BY id DESC");
    while ($r = $res->fetch_assoc()) {
        $rows[] = [
            'id' => (int)$r['id'],
            'title' => $r['title'],
            'author' => $r['author'],
            'price' => (float)$r['price'],
            'image' => $r['image'],
            'stock' => isset($r['stock']) ? (int)$r['stock'] : 0,
            'status' => $r['status'] ?: 'In Stock',
            'created_at' => $r['created_at']
        ];
    }
    echo json_encode(['ok' => true, 'data' => $rows]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

/* ===== ADD BOOK ===== */
if ($action === 'add') {
    $title = $conn->real_escape_string($input['title'] ?? '');
    $author = $conn->real_escape_string($input['author'] ?? '');
    $price = $input['price'] ?? 0;
    $image = $conn->real_escape_string($input['image'] ?? '');
    $stock = (int)($input['stock'] ?? 0);
    $status = $conn->real_escape_string($input['status'] ?? 'In Stock');

    $sql = "INSERT INTO books (title,author,price,image,stock,status,created_at)
            VALUES ('$title','$author',$price,'$image',$stock,'$status',NOW())";
    if ($conn->query($sql)) {
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'error' => $conn->error]);
    }
    exit;
}

/* ===== UPDATE BOOK ===== */
if ($action === 'update') {
    $id = (int)($input['id'] ?? 0);
    if (!$id) { echo json_encode(['ok'=>false,'error'=>'Missing ID']); exit; }

    // Build dynamic SQL SET
    $fields = [];
    if (isset($input['title'])) {
        $fields[] = "title='" . $conn->real_escape_string($input['title']) . "'";
    }
    if (isset($input['author'])) {
        $fields[] = "author='" . $conn->real_escape_string($input['author']) . "'";
    }
    if (isset($input['price'])) {
        $fields[] = "price=" . floatval($input['price']);
    }
    if (isset($input['image'])) {
        $fields[] = "image='" . $conn->real_escape_string($input['image']) . "'";
    }
    if (isset($input['stock'])) {
        $fields[] = "stock=" . intval($input['stock']);
    }
    if (isset($input['status'])) {
        $fields[] = "status='" . $conn->real_escape_string($input['status']) . "'";
    }

    if (empty($fields)) {
        echo json_encode(['ok' => false, 'error' => 'No fields to update']);
        exit;
    }

    $sql = "UPDATE books SET " . implode(',', $fields) . " WHERE id=$id";
    if ($conn->query($sql)) {
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'error' => $conn->error]);
    }
    exit;
}

/* ===== DELETE BOOK ===== */
if ($action === 'delete') {
    $id = (int)($input['id'] ?? 0);
    if (!$id) { echo json_encode(['ok'=>false,'error'=>'Missing ID']); exit; }
    if ($conn->query("DELETE FROM books WHERE id=$id")) {
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'error' => $conn->error]);
    }
    exit;
}

echo json_encode(['ok' => false, 'error' => 'Unknown action']);




