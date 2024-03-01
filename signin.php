<?php
session_start(); // Start the session

// Include Composer's autoloader
require 'vendor/autoload.php';

// Use MongoDB classes
use MongoDB\Client;

// Connection URI
$uri = "mongodb://localhost:27017";

// Database name
$dbName = "socialMedia";

// Connect to MongoDB
try {
    $client = new Client($uri);
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Failed to connect to database: " . $e->getMessage() . "\n";
    exit;
}

// Select the database
$database = $client->selectDatabase($dbName);

// Select the collections
$userCollection = $database->selectCollection("users");
$adminCollection = $database->selectCollection("users_admin");

// Check if any admin exists
$adminExists = $adminCollection->countDocuments();

// If an admin exists, redirect to signup page
if ($adminExists === 0) {
    header("Location: signup.php");
    exit();
}

// Process sign-in form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $selectedRole = $_POST['role'];

    // Validate form data (e.g., check if fields are not empty)

    // Find the user in the appropriate collection based on role
    $collection = ($selectedRole === 'admin') ? $adminCollection : $userCollection;
    $user = $collection->findOne(['email' => $email]);

    // If user is found, verify password and role
    if (!empty($user)) {
        // Verify password hash
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables for user
            $_SESSION['userId'] = (string) $user['_id']; // Convert ObjectID to string
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            // Redirect to appropriate dashboard or home page based on role
            if ($user['role'] === 'admin') {
                header("Location: index.php");
            } else {
                header("Location: user_profile.php");
            }
            exit();
        } else {
            // Password is incorrect
            $error = "Invalid email or password.";
        }
    } else {
        // User not found
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-no-repeat bg-cover bg-center">
    <div class="bg-gray-100 min-h-screen flex flex-col">
        <div class="container max-w-sm mx-auto flex-1 flex flex-col items-center justify-center px-2">
            <div class="bg-white px-6 py-8 rounded shadow-md text-black w-full">
                <h1 class="mb-8 text-3xl text-center">Sign In</h1>
                <?php if (isset($error)) : ?>
                    <div class="mb-4">
                        <div class="font-medium text-red-600">
                            <?php echo $error; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <form method="POST" action=""> <!-- Keep action empty or point it to the same page -->
                    <select 
                        class="block border border-gray-300 w-full p-3 rounded mb-4"
                        name="role"
                        required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                    <input 
                        type="email"
                        class="block border border-gray-300 w-full p-3 rounded mb-4"
                        name="email"
                        placeholder="Email"
                        required />
                    <input 
                        type="password"
                        class="block border border-gray-300 w-full p-3 rounded mb-4"
                        name="password"
                        placeholder="Password"
                        required />
                    <button
                        type="submit"
                        class="w-full text-center py-3 rounded bg-green-500 text-white hover:bg-green-600 focus:outline-none my-1"
                    >Sign In</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
