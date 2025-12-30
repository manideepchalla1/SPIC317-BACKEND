<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../db.php';

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$user_id  = $data['user_id'] ?? '';
$name     = $data['name'] ?? '';
$village  = $data['village'] ?? '';
$district = $data['district'] ?? '';
$phone    = $data['phone'] ?? '';

if ($user_id === '' || $name === '' || $village === '' || $district === '' || $phone === '') {
    echo json_encode([
        "status" => "error",
        "message" => "All fields are required"
    ]);
    exit;
}

$sql = "UPDATE users
        SET name = ?, village = ?, district = ?, phone = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $name, $village, $district, $phone, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Profile updated successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Profile update failed"
    ]);
}

$stmt->close();
