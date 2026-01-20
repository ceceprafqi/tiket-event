<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../database/database.php";

$sql = "SELECT id, title, category, location, event_date, price, quota 
        FROM events 
        ORDER BY event_date ASC";

$result = $conn->query($sql);

$events = [];

while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $events
]);
