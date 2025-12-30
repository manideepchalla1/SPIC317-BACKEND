<?php
header("Content-Type: application/json; charset=UTF-8");

$response = [
    "status" => "success",
    "faq" => [
        "How to scan?" => "Take a clear photo of paddy leaf and scan.",
        "Is internet required?" => "Yes, for disease detection.",
        "Is app free?" => "Yes, basic features are free."
    ]
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
