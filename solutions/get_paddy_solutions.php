<?php
header("Content-Type: application/json");
require_once "../db.php";

$disease_id = $_GET['disease_id'] ?? '';

if (!$disease_id) {
    echo json_encode([
        "status" => "error",
        "message" => "Disease ID required"
    ]);
    exit;
}

/* =========================
   CHEMICAL SOLUTIONS
========================= */
$chemical = [];
$res = $conn->prepare(
    "SELECT title, description, dosage 
     FROM disease_solutions 
     WHERE disease_id=? AND solution_type='chemical'"
);
$res->bind_param("i", $disease_id);
$res->execute();
$result = $res->get_result();

while ($row = $result->fetch_assoc()) {
    $chemical[] = $row;
}

/* =========================
   ORGANIC SOLUTIONS
========================= */
$organic = [];
$res = $conn->prepare(
    "SELECT title, description, dosage 
     FROM disease_solutions 
     WHERE disease_id=? AND solution_type='organic'"
);
$res->bind_param("i", $disease_id);
$res->execute();
$result = $res->get_result();

while ($row = $result->fetch_assoc()) {
    $organic[] = $row;
}

/* =========================
   FIELD MANAGEMENT
========================= */
$management = [];
$res = $conn->prepare(
    "SELECT tip FROM disease_management WHERE disease_id=?"
);
$res->bind_param("i", $disease_id);
$res->execute();
$result = $res->get_result();

while ($row = $result->fetch_assoc()) {
    $management[] = $row['tip'];
}

/* =========================
   FINAL RESPONSE
========================= */
echo json_encode([
    "status" => "success",
    "chemical_solutions" => $chemical,
    "organic_solutions" => $organic,
    "field_management" => $management
], JSON_UNESCAPED_SLASHES);
