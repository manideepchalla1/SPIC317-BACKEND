<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../db.php";

/* READ JSON INPUT */
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid JSON input"
    ]);
    exit;
}

$name     = trim($data['name'] ?? '');
$email    = trim($data['email'] ?? '');
$phone    = trim($data['phone'] ?? '');
$password = $data['password'] ?? '';
$confirm  = $data['confirm_password'] ?? '';

/* VALIDATION */

// Name
if (strlen($name) < 3) {
    echo json_encode([
        "status" => "error",
        "message" => "Name must be at least 3 characters"
    ]);
    exit;
}

// Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid email address"
    ]);
    exit;
}

// Phone (10 digits only)
if (!preg_match('/^[0-9]{10}$/', $phone)) {
    echo json_encode([
        "status" => "error",
        "message" => "Phone number must be exactly 10 digits"
    ]);
    exit;
}

// Password length
if (strlen($password) < 6) {
    echo json_encode([
        "status" => "error",
        "message" => "Password must be at least 6 characters"
    ]);
    exit;
}

// Password match
if ($password !== $confirm) {
    echo json_encode([
        "status" => "error",
        "message" => "Passwords do not match"
    ]);
    exit;
}

/* CHECK EMAIL */
$checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
$checkEmail->bind_param("s", $email);
$checkEmail->execute();
$checkEmail->store_result();

if ($checkEmail->num_rows > 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Email already registered"
    ]);
    exit;
}

/* CHECK PHONE */
$checkPhone = $conn->prepare("SELECT id FROM users WHERE phone = ?");
$checkPhone->bind_param("s", $phone);
$checkPhone->execute();
$checkPhone->store_result();

if ($checkPhone->num_rows > 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Phone number already registered"
    ]);
    exit;
}

/* INSERT USER */
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO users (name, email, phone, password, is_verified)
     VALUES (?, ?, ?, ?, 0)"
);
$stmt->bind_param("ssss", $name, $email, $phone, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Signup successful. Please verify your email.",
        "user_id" => $stmt->insert_id
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Signup failed. Try again later."
    ]);
}
