<?php
header("Content-Type: application/json");
require_once "../db.php";

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';

if (!$email) {
    echo json_encode(["status"=>"error","message"=>"Email required"]);
    exit;
}

$token  = bin2hex(random_bytes(32));
$expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

$stmt = $conn->prepare(
    "UPDATE users SET reset_token=?, reset_token_expiry=? WHERE email=?"
);
$stmt->bind_param("sss", $token, $expiry, $email);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode([
        "status"=>"success",
        "message"=>"Reset token generated (demo)",
        "token"=>$token
    ]);
} else {
    echo json_encode([
        "status"=>"error",
        "message"=>"Email not registered"
    ]);
}
