<?php
header("Content-Type: application/json");
require_once "../config/database.php";

$result = $conn->query("SELECT * FROM events");

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $events
]);
