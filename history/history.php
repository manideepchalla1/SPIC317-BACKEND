<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../db.php';

// Step 1: Get user_id from POST or GET and cast
$user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : (isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0);

// Step 2: Validate
if ($user_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "user_id is required and must be a positive integer"
    ]);
    exit;
}

// Step 3: Prepare SQL
$sql = "SELECT id, disease, confidence, image_path, created_at
        FROM disease_history
        WHERE user_id = ?
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode([
        "status" => "error",
        "message" => "Prepare failed",
        "error" => $conn->error
    ]);
    exit;
}

// Step 4: Bind & execute
if (!$stmt->bind_param("i", $user_id)) {
    echo json_encode([
        "status" => "error",
        "message" => "Bind failed",
        "error" => $stmt->error
    ]);
    $stmt->close();
    exit;
}

if (!$stmt->execute()) {
    echo json_encode([
        "status" => "error",
        "message" => "Execute failed",
        "error" => $stmt->error
    ]);
    $stmt->close();
    exit;
}

// Step 5: Fetch results (support environments without mysqlnd)
$data = [];
if (method_exists($stmt, 'get_result')) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    $stmt->bind_result($id, $disease, $confidence, $image_path, $created_at);
    while ($stmt->fetch()) {
        $data[] = [
            'id' => $id,
            'disease' => $disease,
            'confidence' => (float) $confidence,
            'image_path' => $image_path,
            'created_at' => $created_at
        ];
    }
}

$stmt->close();

// Step 6: Send response
echo json_encode([
    "status" => "success",
    "history" => $data
]);
