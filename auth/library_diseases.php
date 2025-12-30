<?php
header("Content-Type: application/json");
require_once "db.php";

$sql = "SELECT id, name, type, severity, image FROM paddy_diseases";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "status" => "error",
        "message" => $conn->error
    ]);
    exit;
}

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        "id" => (int)$row['id'],
        "name" => $row['name'],
        "type" => $row['type'],
        "severity" => $row['severity'],
        "image" => "uploads/" . $row['image']
    ];
}

echo json_encode([
    "status" => "success",
    "diseases" => $data
], JSON_UNESCAPED_SLASHES);
