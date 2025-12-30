<?php
header("Content-Type: application/json");
require_once "db.php";

/* Step 1: Get input */
$disease_name = $_POST['disease_name'] ?? '';

/* Step 2: Validate input */
if ($disease_name == "") {
    echo json_encode([
        "status" => "error",
        "message" => "disease_name is required"
    ]);
    exit;
}

/* Step 3: Prepare SQL */
$stmt = $conn->prepare(
    "SELECT symptoms, cause, solution 
     FROM disease_info 
     WHERE disease_name = ?"
);

/* Step 4: Bind input */
$stmt->bind_param("s", $disease_name);

/* Step 5: Execute query */
$stmt->execute();

/* Step 6: Get result */
$result = $stmt->get_result();
$data = $result->fetch_assoc();

/* Step 7: Send response */
if ($data) {
    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Disease not found"
    ]);
}
