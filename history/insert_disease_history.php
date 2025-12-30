<?php
header("Content-Type: application/json");
require_once "../db.php";

/* 1️⃣ Read inputs */
$user_id    = $_POST['user_id'] ?? '';
$disease_id = $_POST['disease_id'] ?? '';
$confidence = $_POST['confidence'] ?? '';
$image_path = $_POST['image_path'] ?? '';

/* 2️⃣ Validation */
if (!$user_id || !$disease_id || !$confidence) {
    echo json_encode([
        "status" => "error",
        "message" => "user_id, disease_id and confidence are required"
    ]);
    exit;
}

/* 3️⃣ Insert into DB */
$stmt = $conn->prepare(
    "INSERT INTO disease_history (user_id, disease_id, confidence, image_path)
     VALUES (?, ?, ?, ?)"
);

$stmt->bind_param(
    "iids",
    $user_id,
    $disease_id,
    $confidence,
    $image_path
);

/* 4️⃣ Execute */
if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "scan_id" => $stmt->insert_id
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Insert failed"
    ]);
}
