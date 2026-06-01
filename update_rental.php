<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $location = filter_var($_POST['location'], FILTER_SANITIZE_STRING);
    $image = filter_var($_POST['image'], FILTER_SANITIZE_URL);

    if ($id && $title && $description && $price && $location && $image) {
        $sql = "UPDATE rentals SET title = ?, description = ?, price = ?, location = ?, image = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdssi", $title, $description, $price, $location, $image, $id);
        if ($stmt->execute()) {
            $_SESSION['update_message'] = ['status' => 'success', 'message' => 'Rental updated successfully!'];
        } else {
            $_SESSION['update_message'] = ['status' => 'error', 'message' => 'Failed to update rental.'];
        }
        $stmt->close();
    } else {
        $_SESSION['update_message'] = ['status' => 'error', 'message' => 'All fields are required.'];
    }
}

header("Location: admin_panel.php");
exit();
?>