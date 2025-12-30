<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../db.php';

$scan_id = $_GET['scan_id'] ?? '';

if (!$scan_id) {
    echo json_encode([
        "status" => "error",
        "message" => "scan_id is required"
    ]);
    exit;
}

$sql = "SELECT 
            id,
            disease_id,
            image_path,
            result_name,
            risk_level,
            accuracy,
            treatment_applied,
            prevention_notes,
            scan_date
        FROM scan_history
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $scan_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {

    echo json_encode([
        "status" => "success",
        "scan" => [
            "scan_id" => (int)$row['id'],
            "disease_id" => (int)$row['disease_id'],
            "image" => $row['image_path'],
            "disease_name" => $row['result_name'],
            "risk_level" => $row['risk_level'],
            "accuracy" => $row['accuracy'],
            "scan_date" => $row['scan_date'],
            "treatment_applied" => $row['treatment_applied'],
            "prevention_notes" => explode(',', $row['prevention_notes'])
        ]
    ], JSON_UNESCAPED_SLASHES);

} else {
    echo json_encode([
        "status" => "error",
        "message" => "Scan not found"
    ]);
}

$stmt->close();
