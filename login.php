<?php
session_start();
include 'connect.php';

$role = isset($_GET['role']) ? $_GET['role'] : '';
if ($role !== 'user' && $role !== 'admin') {
    header("Location: index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ? AND role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            if ($role == 'admin') {
                header("Location: admin_panel.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid username.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($role); ?> Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex items-center justify-center h-screen" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('bluffview_dallas_homes.webp') no-repeat center/cover;">
    <div class="bg-white bg-opacity-20 p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-white"><?php echo ucfirst($role); ?> Login</h2>
        <?php if (isset($error)) { ?>
            <p class="text-red-500 mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>
        <form id="login-form" method="POST" action="">
            <div class="mb-4">
                <label class="block text-black">Username</label>
                <input type="text" id="username" name="username" class="w-full p-2 border rounded bg-white bg-opacity-20" required>
            </div>
            <div class="mb-4">
                <label class="block text-black">Password</label>
                <input type="password" id="password" name="password" class="w-full p-2 border rounded bg-white bg-opacity-20" required>
            </div>
            <button type="submit" class="bg-green-800 text-white px-4 py-2 rounded w-full hover:bg-green-700">Login</button>
        </form>
        <?php if ($role === 'user') { ?>
            <p class="mt-4 text-center text-black">New user? <a href="register.php" class="text-black hover:underline">Register here</a></p>
        <?php } ?>
        <p class="mt-2 text-center text-black">Switch to <a href="login.php?role=<?php echo $role === 'user' ? 'admin' : 'user'; ?>" class="text-black hover:underline"><?php echo $role === 'user' ? 'Admin' : 'User'; ?> Login</a></p>
    </div>
</body>
</html>
