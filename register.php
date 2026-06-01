<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = 'user';

    if (empty($username) || empty($password)) {
        $error = "Please fill in both username and password.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Username already taken.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $role);
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['role'] = $role;
                header("Location: user_dashboard.php");
                exit();
            } else {
                $error = "Registration failed. Check database connection.";
            }
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Home Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex items-center justify-center h-screen" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('bluffview_dallas_homes.webp') no-repeat center/cover;">
    <div class="bg-white bg-opacity-20 p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-white">User Registration</h2>
        <?php if (isset($error)) { ?>
            <p class="text-red-500 mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>
        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-black">Username</label>
                <input type="text" name="username" class="w-full p-2 border rounded bg-white bg-opacity-20" required>
            </div>
            <div class="mb-4">
                <label class="block text-black">Password</label>
                <input type="password" name="password" class="w-full p-2 border rounded bg-white bg-opacity-20" required>
            </div>
            <button type="submit" class="bg-green-800 text-white px-4 py-2 rounded w-full hover:bg-green-700">Register</button>
        </form>
        <p class="mt-4 text-center text-black">Already have an account? <a href="login.php?role=user" class="text-black hover:underline">Login here</a></p>
    </div>
</body>
</html>