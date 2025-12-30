<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../db.php';

/* Fetch all fertilizer guide data */
$sql = "SELECT id, fertilizer_name, application_method, dosage
        FROM fertilizer_guide";

$result = $conn->query($sql);

$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

if (count($data) > 0) {
    echo json_encode([
        "status" => "success",
        "fertilizers" => $data
    ], JSON_UNESCAPED_SLASHES);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No fertilizer data found"
    ]);
}
