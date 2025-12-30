<?php
header("Content-Type: application/json");

echo json_encode([
    "status" => "success",
    "message" => "Logged out successfully"
]);
