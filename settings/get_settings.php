<?php
header("Content-Type: application/json");
require_once "../db.php";

$user_id = $_GET['user_id'] ?? '';

if (!$user_id) {
    echo json_encode(["status"=>"error","message"=>"User ID required"]);
    exit;
}

$stmt = $conn->prepare(
    "SELECT dark_mode, language FROM user_settings WHERE user_id=?"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "status"=>"success",
        "settings"=>[
            "dark_mode"=>0,
            "language"=>"English"
        ]
    ]);
    exit;
}

echo json_encode([
    "status"=>"success",
    "settings"=>$result->fetch_assoc()
]);
