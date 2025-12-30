<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../db.php';

$fertilizer_id = $_GET['fertilizer_id'] ?? '';

if (!$fertilizer_id) {
    echo json_encode([
        "status" => "error",
        "message" => "fertilizer_id is required"
    ]);
    exit;
}

/* MAIN */
$f = $conn->query(
    "SELECT name, about FROM fertilizers WHERE id = $fertilizer_id"
)->fetch_assoc();

/* DOSAGE */
$d = $conn->query(
    "SELECT dosage FROM fertilizer_dosage WHERE fertilizer_id = $fertilizer_id"
)->fetch_assoc();

/* TIMING */
$timing = [];
$res = $conn->query(
    "SELECT stage, timing FROM fertilizer_application_timing 
     WHERE fertilizer_id = $fertilizer_id"
);
while ($row = $res->fetch_assoc()) {
    $timing[] = $row;
}

/* HOW TO APPLY */
$app = $conn->query(
    "SELECT method FROM fertilizer_how_to_apply 
     WHERE fertilizer_id = $fertilizer_id"
)->fetch_assoc();

/* BENEFITS */
$benefits = [];
$res = $conn->query(
    "SELECT benefit FROM fertilizer_benefits 
     WHERE fertilizer_id = $fertilizer_id"
);
while ($row = $res->fetch_assoc()) {
    $benefits[] = $row['benefit'];
}

/* SAFETY */
$safety = [];
$res = $conn->query(
    "SELECT precaution FROM fertilizer_safety_precautions 
     WHERE fertilizer_id = $fertilizer_id"
);
while ($row = $res->fetch_assoc()) {
    $safety[] = $row['precaution'];
}

/* RESPONSE */
echo json_encode([
    "status" => "success",
    "fertilizer" => [
        "name" => $f['name'],
        "about" => $f['about'],
        "dosage" => $d['dosage'],
        "application_timing" => $timing,
        "how_to_apply" => $app['method'],
        "benefits" => $benefits,
        "safety_precautions" => $safety
    ]
], JSON_UNESCAPED_SLASHES);
