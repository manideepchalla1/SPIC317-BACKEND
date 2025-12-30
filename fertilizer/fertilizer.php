<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../db.php';

/*
  Accept disease_name from:
  - GET (URL / Params)
  - POST form-data
*/
$disease_name = '';

if (isset($_GET['disease_name']) && trim($_GET['disease_name']) !== '') {
    $disease_name = trim($_GET['disease_name']);
} elseif (isset($_POST['disease_name']) && trim($_POST['disease_name']) !== '') {
    $disease_name = trim($_POST['disease_name']);
}

if ($disease_name === '') {
    echo json_encode([
        "status" => "error",
        "message" => "disease_name is required"
    ]);
    exit;
}

/* Fetch fertilizer info */
$sql = "SELECT fertilizer_name, application_method, dosage
        FROM fertilizer_guide
        WHERE disease_name = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $disease_name);
$stmt->execute();

$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$stmt->close();

if (count($data) > 0) {
    echo json_encode([
        "status" => "success",
        "fertilizers" => $data
    ], JSON_UNESCAPED_SLASHES);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No fertilizer info found"
    ]);
}
