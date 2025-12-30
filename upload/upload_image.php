<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../db.php';

if (!isset($_FILES['image'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Image file is required"
    ]);
    exit;
}

$user_id = $_POST['user_id'] ?? 0;

$image = $_FILES['image'];

$allowed = ['image/jpeg','image/png','image/jpg'];
if (!in_array($image['type'], $allowed)) {
    echo json_encode([
        "status" => "error",
        "message" => "Only JPG and PNG allowed"
    ]);
    exit;
}

$ext = pathinfo($image['name'], PATHINFO_EXTENSION);
$newName = "paddy_" . time() . "." . $ext;
$destination = "../uploads/" . $newName;

if (move_uploaded_file($image['tmp_name'], $destination)) {

    /* Save to DB */
    $stmt = $conn->prepare(
        "INSERT INTO uploaded_images (user_id, image_path) VALUES (?, ?)"
    );
    $path = "uploads/" . $newName;
    $stmt->bind_param("is", $user_id, $path);
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        "status" => "success",
        "image_path" => $path
    ]);

} else {
    echo json_encode([
        "status" => "error",
        "message" => "Upload failed"
    ]);
}
