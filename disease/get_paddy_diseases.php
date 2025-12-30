<?php
header("Content-Type: application/json");
require_once "../db.php";

$sql = "SELECT id, name, type, severity, image FROM paddy_diseases";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error"
    ]);
    exit;
}

$diseases = [];

while ($row = $result->fetch_assoc()) {
    $diseases[] = [
        "id" => (int)$row['id'],
        "name" => $row['name'],
        "type" => $row['type'],
        "severity" => $row['severity'],
        "image" => "uploads/" . $row['image']
    ];
}

echo json_encode([
    "status" => "success",
    "diseases" => $diseases
], JSON_UNESCAPED_SLASHES);
