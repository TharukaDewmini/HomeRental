<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.html");
    exit();
}

$sql = "SELECT * FROM rentals";
$result = $conn->query($sql);

// Check update message
$update_message = isset($_SESSION['update_message']) ? $_SESSION['update_message'] : null;
unset($_SESSION['update_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-green-100">
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-green-800">Admin Panel - Manage Rentals</h1>
            <a href="logout.php" class="bg-green-800 text-white px-4 py-2 rounded hover:bg-green-600 transition">Logout</a>
        </div>
        <?php if ($update_message) { ?>
            <p class="text-<?php echo $update_message['status'] === 'success' ? 'green' : 'red'; ?>-500 mb-4 p-4 bg-white rounded-lg shadow">
                <?php echo htmlspecialchars($update_message['message']); ?>
            </p>
        <?php } ?>
        <h2 class="text-2xl font-bold mb-4 text-black">Add New Rental</h2>
        <form method="POST" action="add_rental.php" class="bg-white p-6 rounded-lg shadow-lg mb-8">
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold">Title</label>
                <input type="text" name="title" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold">Description</label>
                <textarea name="description" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold">Price (Rs/month)</label>
                <input type="number" name="price" step="0.01" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold">Location</label>
                <input type="text" name="location" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold">Image URL</label>
                <input type="text" name="image" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button type="submit" class="bg-green-800 text-white px-4 py-2 rounded hover:bg-green-600 transition">Add Rental</button>
        </form>
        <h2 class="text-2xl font-bold mb-4 text-black">Existing Rentals</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="rental-card bg-white p-4 rounded-lg shadow-lg">
                    <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="w-full h-48 object-cover rounded">
                    <h3 class="text-xl font-bold mt-2 text-gray-800"><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p class="text-gray-600"><?php echo htmlspecialchars($row['description']); ?></p>
                    <p class="text-gray-800 font-semibold">Rs<?php echo number_format($row['price'], 2); ?>/month</p>
                    <p class="text-gray-600"><?php echo htmlspecialchars($row['location']); ?></p>
                    <div class="flex space-x-2 mt-4">
                        <button onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['title'])); ?>', '<?php echo htmlspecialchars(addslashes($row['description'])); ?>', <?php echo $row['price']; ?>, '<?php echo htmlspecialchars(addslashes($row['location'])); ?>', '<?php echo htmlspecialchars(addslashes($row['image'])); ?>')" class="bg-green-800 text-white px-4 py-2 rounded hover:bg-green-600 transition">Update</button>
                        <a href="delete_rental.php?id=<?php echo $row['id']; ?>" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition" onclick="return confirm('Are you sure you want to delete this rental?')">Delete</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-700">Update Rental</h2>
                <button onclick="closeEditModal()" class="text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="POST" action="update_rental.php">
                <input type="hidden" id="edit_id" name="id">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Title</label>
                    <input type="text" id="edit_title" name="title" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Description</label>
                    <textarea id="edit_description" name="description" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Price (Rs/month)</label>
                    <input type="number" id="edit_price" name="price" step="0.01" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Location</label>
                    <input type="text" id="edit_location" name="location" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Image URL</label>
                    <input type="text" id="edit_image" name="image" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Update Rental</button>
                    <button type="button" onclick="closeEditModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, title, description, price, location, image) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_location').value = location;
            document.getElementById('edit_image').value = image;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>