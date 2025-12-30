<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../db.php";

/* =========================
   Get scan ID
========================= */
$scan_id = $_GET['scan_id'] ?? '';

if (!$scan_id) {
    echo json_encode([
        "status" => "error",
        "message" => "scan_id is required"
    ]);
    exit;
}

/* =========================
   Fetch scan + disease
========================= */
$stmt = $conn->prepare(
    "SELECT dh.id, dh.confidence, dh.image_path, dh.created_at,
            pd.id AS disease_id, pd.name, pd.type, pd.severity
     FROM disease_history dh
     JOIN paddy_diseases pd ON dh.disease_id = pd.id
     WHERE dh.id = ?"
);
$stmt->bind_param("i", $scan_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Scan result not found"
    ]);
    exit;
}

$scan = $result->fetch_assoc();

/* =========================
   Solutions
========================= */
$chemical = [];
$organic  = [];

$stmt = $conn->prepare(
    "SELECT solution_type, title, description, dosage
     FROM disease_solutions
     WHERE disease_id = ?"
);
$stmt->bind_param("i", $scan['disease_id']);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    if ($row['solution_type'] === 'chemical') {
        $chemical[] = $row;
    } else {
        $organic[] = $row;
    }
}

/* =========================
   Prevention tips
========================= */
$prevention = [];

$stmt = $conn->prepare(
    "SELECT tip FROM disease_management WHERE disease_id = ?"
);
$stmt->bind_param("i", $scan['disease_id']);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $prevention[] = $row['tip'];
}

/* =========================
   Final Response
========================= */
echo json_encode([
    "status" => "success",
    "scan" => [
        "scan_id" => (int)$scan['id'],
        "disease" => $scan['name'],
        "type" => $scan['type'],
        "severity" => $scan['severity'],
        "confidence" => $scan['confidence'],
        "image" => $scan['image_path'],
        "scanned_at" => $scan['created_at']
    ],
    "chemical_solutions" => $chemical,
    "organic_solutions" => $organic,
    "prevention" => $prevention
], JSON_UNESCAPED_SLASHES);
