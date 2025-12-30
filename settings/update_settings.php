<?php
header("Content-Type: application/json");
require_once "../db.php";

$data = json_decode(file_get_contents("php://input"), true);

$user_id   = $data['user_id'] ?? '';
$dark_mode = $data['dark_mode'] ?? 0;
$language  = $data['language'] ?? 'English';

if (!$user_id) {
    echo json_encode(["status"=>"error","message"=>"User ID required"]);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO user_settings (user_id, dark_mode, language)
     VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE
     dark_mode=VALUES(dark_mode),
     language=VALUES(language)"
);
$stmt->bind_param("iis", $user_id, $dark_mode, $language);
$stmt->execute();

echo json_encode([
    "status"=>"success",
    "message"=>"Settings updated"
]);
