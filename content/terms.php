<?php
header("Content-Type: application/json");
require_once "../db.php";

$res = $conn->query(
    "SELECT content FROM app_content WHERE type='terms' LIMIT 1"
);

echo json_encode([
    "status"=>"success",
    "content"=>$res->fetch_assoc()['content']
]);
