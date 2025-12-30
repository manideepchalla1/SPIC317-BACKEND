<?php
header("Content-Type: application/json");
require_once "../db.php";

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';

if (!$email) {
    echo json_encode(["status"=>"error","message"=>"Email required"]);
    exit;
}

// check user
$stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status"=>"error","message"=>"Email not registered"]);
    exit;
}

$user = $result->fetch_assoc();
$user_id = $user['id'];

// generate OTP
$otp = rand(100000, 999999);
$expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// save OTP
$stmt = $conn->prepare(
    "INSERT INTO email_otps (user_id, otp, expires_at)
     VALUES (?, ?, ?)"
);
$stmt->bind_param("iss", $user_id, $otp, $expiry);
$stmt->execute();

echo json_encode([
    "status" => "success",
    "message" => "OTP sent (demo)",
    "otp" => $otp   // demo only
]);
