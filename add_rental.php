<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $location = $_POST['location'];
    $image = $_POST['image'];

    $sql = "INSERT INTO rentals (title, description, price, location, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdss", $title, $description, $price, $location, $image);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_panel.php");
    exit();
}
?>