<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../database/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

$event_id = (int)($_POST["event_id"] ?? 0);
$name     = trim($_POST["name"] ?? "");
$email    = trim($_POST["email"] ?? "");
$qty      = (int)($_POST["qty"] ?? 0);

if ($event_id <= 0 || $name === "" || $email === "" || $qty <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}

$conn->begin_transaction();

try {
    // 1. Ambil data event
    $stmt = $conn->prepare("SELECT price, quota FROM events WHERE id = ? FOR UPDATE");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Event not found");
    }

    $event = $result->fetch_assoc();

    if ($event["quota"] < $qty) {
        throw new Exception("Quota not enough");
    }

    $total_price = $event["price"] * $qty;

    // 2. Simpan booking
    $stmt = $conn->prepare(
        "INSERT INTO bookings (event_id, name, email, qty, total_price)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("issii", $event_id, $name, $email, $qty, $total_price);
    $stmt->execute();

    // 3. Kurangi kuota
    $stmt = $conn->prepare(
        "UPDATE events SET quota = quota - ? WHERE id = ?"
    );
    $stmt->bind_param("ii", $qty, $event_id);
    $stmt->execute();

    $conn->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Booking successful",
        "total_price" => $total_price
    ]);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
