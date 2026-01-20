<?php
header("Content-Type: application/json");
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

$title = $_POST["title"] ?? "";
$category = $_POST["category"] ?? "";
$location = $_POST["location"] ?? "";
$event_date = $_POST["event_date"] ?? "";
$price = $_POST["price"] ?? 0;
$quota = $_POST["quota"] ?? 0;

if ($title === "" || $category === "" || $price <= 0 || $quota <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO events (title, category, location, event_date, price, quota)
     VALUES (?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "ssssii",
    $title,
    $category,
    $location,
    $event_date,
    $price,
    $quota
);

$stmt->execute();

echo json_encode(["status" => "success", "message" => "Event created"]);
