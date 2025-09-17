<?php
// api/books_public.php — fetch books for public view
error_reporting(E_ALL);
ini_set('display_errors',1);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../db.php'; // includes $conn (MySQLi)

if(!$conn || $conn->connect_error){
    echo json_encode(['ok'=>false,'error'=>'DB connection failed: '.$conn->connect_error]);
    exit;
}

$rows=[];
$result=$conn->query("SELECT * FROM books WHERE status <> 'Hidden' ORDER BY id DESC");
while($row=$result->fetch_assoc()){
    $rows[]=$row;
}
echo json_encode(['ok'=>true,'data'=>$rows]);

