<?php
header("Content-Type: application/json");

/*
|--------------------------------------------------------------------------
| Database Connection
|--------------------------------------------------------------------------
| auth/login.php → go one folder back → db.php
*/
require_once __DIR__ . "/../db.php";

/*
|--------------------------------------------------------------------------
| Read Input (x-www-form-urlencoded or form-data)
|--------------------------------------------------------------------------
*/
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
*/
if ($email === '' || $password === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Email and password are required"
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Fetch User
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo json_encode([
        "status" => "error",
        "message" => "User not found"
    ]);
    exit;
}

$user = $result->fetch_assoc();

/*
|--------------------------------------------------------------------------
| Verify Password
|--------------------------------------------------------------------------
*/
if (!password_verify($password, $user['password'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid password"
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Success Response
|--------------------------------------------------------------------------
*/
echo json_encode([
    "status" => "success",
    "user" => [
        "id"       => (int)$user['id'],
        "name"     => $user['name'],
        "email"    => $user['email'],
        "village"  => $user['village'],
        "district" => $user['district'],
        "language" => $user['language']
    ]
]);
