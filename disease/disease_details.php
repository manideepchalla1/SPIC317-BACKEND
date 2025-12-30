<?php
header("Content-Type: application/json");

/*
|--------------------------------------------------------------------------
| Database connection
|--------------------------------------------------------------------------
| disease_details.php is inside: myapp/disease/
| db.php is inside: myapp/
| So we must go ONE level up
*/
require_once __DIR__ . "/../db.php";

/*
|--------------------------------------------------------------------------
| Get disease ID
|--------------------------------------------------------------------------
*/
$id = $_GET['id'] ?? '';

if (!$id) {
    echo json_encode([
        "status" => "error",
        "message" => "Disease ID is required"
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Fetch main disease details
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare(
    "SELECT id, name, type, severity 
     FROM paddy_diseases 
     WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Disease not found"
    ]);
    exit;
}

$disease = $result->fetch_assoc();

/*
|--------------------------------------------------------------------------
| Fetch chemical & organic solutions
|--------------------------------------------------------------------------
*/
$chemical = [];
$organic  = [];

$stmt = $conn->prepare(
    "SELECT solution_type, title, description, dosage
     FROM disease_solutions
     WHERE disease_id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    if ($row['solution_type'] === 'chemical') {
        $chemical[] = [
            "title" => $row['title'],
            "description" => $row['description'],
            "dosage" => $row['dosage']
        ];
    } else {
        $organic[] = [
            "title" => $row['title'],
            "description" => $row['description'],
            "dosage" => $row['dosage']
        ];
    }
}

/*
|--------------------------------------------------------------------------
| Fetch prevention / field management tips
|--------------------------------------------------------------------------
*/
$prevention = [];

$stmt = $conn->prepare(
    "SELECT tip FROM disease_management WHERE disease_id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $prevention[] = $row['tip'];
}

/*
|--------------------------------------------------------------------------
| Final JSON response
|--------------------------------------------------------------------------
*/
echo json_encode([
    "status" => "success",
    "disease" => [
        "id" => (int)$disease['id'],
        "name" => $disease['name'],
        "type" => $disease['type'],
        "severity" => $disease['severity']
    ],
    "chemical_solutions" => $chemical,
    "organic_solutions" => $organic,
    "prevention" => $prevention
], JSON_UNESCAPED_SLASHES);
