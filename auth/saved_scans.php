<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../db.php';

/* Get user_id (from login/session later) */
$user_id = $_GET['user_id'] ?? '';

if (!$user_id) {
    echo json_encode([
        "status" => "error",
        "message" => "user_id is required"
    ]);
    exit;
}

/* Fetch scan history */
$sql = "SELECT id, image_path, result_name, risk_level, scan_date
        FROM scan_history
        WHERE user_id = ?
        ORDER BY scan_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$stmt->close();

echo json_encode([
    "status" => "success",
    "scans" => $data
], JSON_UNESCAPED_SLASHES);
