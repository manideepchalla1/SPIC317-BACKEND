<?php
// Database connection details
$host = "localhost";
$user = "root";
$pass = "";
$db   = "myapp";   // change if your DB name is different

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// Test query
$result = $conn->query("SHOW TABLES");

if ($result) {
    echo "✅ Database connected successfully!<br><br>";
    echo "📋 Tables in database:<br>";

    while ($row = $result->fetch_array()) {
        echo "- " . $row[0] . "<br>";
    }
} else {
    echo "❌ Query failed";
}
?>
