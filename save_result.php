<?php
header("Content-Type: application/json");
require_once "db.php";

$user_id = $_POST['user_id'];
$disease = $_POST['disease'];
$confidence = $_POST['confidence'];
$image_path = $_POST['image_path'];

$stmt = $conn->prepare(
    "INSERT INTO disease_history (user_id, disease, confidence, image_path)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("isds",
    $user_id, $disease, $confidence, $image_path
);

$stmt->execute();
echo json_encode(["status"=>"success"]);
