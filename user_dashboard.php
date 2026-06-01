<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: index.html");
    exit();
}

$sql = "SELECT * FROM rentals";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Home Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-green-100 font-sans">

    <nav class="bg-green-900 shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="#home" class="text-3xl font-bold text-white">HomeRental</a>
            <div class="space-x-10">
                <a href="#home" class="text-white text-xl font-bold hover:text-green-600 transition">Home</a>
                <a href="#properties" class="text-white text-xl font-bold hover:text-green-600 transition">Properties</a>
                <a href="#contact" class="text-white text-xl font-bold hover:text-green-600 transition">Chat</a>
                <a href="logout.php" class="bg-green-800 text-xl font-bold text-white px-4 py-1 rounded hover:bg-green-600 transition">Logout</a>
            </div>
        </div>
    </nav>

    
    <section id="home" class="relative bg-cover bg-center h-screen flex items-center justify-center" style="background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative text-center text-white">
            <h1 class="text-7xl font-bold mb-10">Find Your Dream Home</h1>
            <p class="text-xl mb-12">Explore a wide range of rental properties tailored to your needs.</p>
            <a href="#properties" class="bg-green-800 bg-opacity-70 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">Browse Properties</a>
        </div>
    </section>

  
    <section id="properties" class="py-16 bg-green-100">
        <div class="container mx-auto px-4">
            <h2 class="text-green-800 text-3xl font-bold text-center mb-12">Available Properties</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <div class="rental-card bg-green-200 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition duration-300">
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($row['title']); ?></h3>
                            <p class="text-gray-800 mt-2"><?php echo htmlspecialchars($row['description']); ?></p>
                            <p class="text-green-800 font-bold mt-4">Rs<?php echo number_format($row['price'], 2); ?>/month</p>
                            <p class="text-gray-700 mt-1"><?php echo htmlspecialchars($row['location']); ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    
    <section id="contact" class="py-16 bg-green-100">
        <div class="container mx-auto px-4">
            <h2 class="text-green-800 text-3xl font-bold text-center mb-12">Chat with Us</h2>
            <div class="max-w-lg mx-auto bg-green-200 p-8 rounded-xl shadow-lg relative">
                
                <button id="chat-toggle" class="bg-green-800 text-white p-3 rounded-full absolute bottom-4 right-4 hover:bg-green-700 transition transform hover:scale-105">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                </button>
                
                <div id="chat-window" class="hidden bg-white border border-gray-200 rounded-lg shadow-lg">
                    <div class="flex justify-between items-center bg-green-800 text-white p-4 rounded-t-lg">
                        <h3 class="text-lg font-semibold">HomeRental AI Assistant</h3>
                        <button id="chat-close" class="text-white hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="chat-container" class="h-80 bg-white p-4 overflow-y-auto">
                        <div id="chat-messages"></div>
                    </div>
                    <div class="p-4 border-t flex items-center">
                        <input type="text" id="chat-input" class="flex-1 p-2 border rounded-l-lg focus:outline-none focus:ring-2 focus:ring-green-600" placeholder="Ask about rentals...">
                        <button id="chat-send" class="bg-green-800 text-white px-4 py-2 rounded-r-lg hover:bg-green-700 transition transform hover:scale-105">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <footer class="bg-green-800 text-white py-6">
        <div class="container mx-auto px-4 text-center">
            <p>© 2025 HomeRental. All rights reserved.</p>
        </div>
    </footer>

    
    <script>
        const chatToggle = document.getElementById('chat-toggle');
        const chatWindow = document.getElementById('chat-window');
        const chatClose = document.getElementById('chat-close');
        const chatMessages = document.getElementById('chat-messages');
        const chatInput = document.getElementById('chat-input');
        const chatSend = document.getElementById('chat-send');

        
        chatToggle.addEventListener('click', () => {
            chatWindow.classList.toggle('hidden');
        });
        chatClose.addEventListener('click', () => {
            chatWindow.classList.add('hidden');
        });

        // Chatbot
        const responses = {
            'hi|hello|hey': 'Hi! Welcome to HomeRental. How can I assist you today?',
            'rentals|properties|houses': 'Browse the Properties section above to see available rentals.',
            'price|cost|budget': 'Rental prices are listed in the Properties section.',
            'contact|support|help': 'You can contact our head branch in Nugegoda at 076 4536720 for assistance.',
            'location|area|city': 'Check the Properties section for locations.',
            'book|booking|reserve': 'Please contact our head office to help you book a home that perfectly matches you.',
            'available|vacant|empty': 'You can find currently available properties in the Properties section.',
            'thank you|thanks|thx|thankyou': 'You’re welcome! Let me know if there’s anything else I can help you with.',
            'default': 'Oops, Try asking about rentals, prices, or contact!'
        };

        function addMessage(message, isUser = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = isUser ? 'text-right mb-3' : 'text-left mb-3';
            messageDiv.innerHTML = `<p class="inline-block p-3 rounded-lg ${isUser ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800'} max-w-xs">${message}</p>`;
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function getResponse(userMessage) {
            const lowerMessage = userMessage.toLowerCase();
            for (const [key, value] of Object.entries(responses)) {
                if (key !== 'default') {
                    const keywords = key.split('|');
                    if (keywords.some(keyword => lowerMessage.includes(keyword))) {
                        return value;
                    }
                }
            }
            return responses['default'];
        }

        chatSend.addEventListener('click', () => {
            const message = chatInput.value.trim();
            if (message) {
                addMessage(message, true);
                const response = getResponse(message);
                setTimeout(() => addMessage(response), 500);
                chatInput.value = '';
            }
        });

        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                chatSend.click();
            }
        });

        // greet
        addMessage('Hello! I’m the HomeRental AI assistant. How can I help you find your perfect home?');
    </script>
</body>
</html>