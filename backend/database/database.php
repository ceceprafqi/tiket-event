<?php

$conn = new mysqli("localhost", "root", "", "tiket_event");

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
