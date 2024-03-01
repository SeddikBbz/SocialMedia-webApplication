<?php
session_start(); // Start the session

// Include MongoDB PHP library
require 'vendor/autoload.php';

// Create a MongoDB client and select your database
$client = new MongoDB\Client('mongodb://localhost:27017');
$database = $client->selectDatabase('socialMedia');

// Select the collection where admins are stored
$collection = $database->users_admin;

// Check if any admin exists in the database
$adminExists = $collection->findOne(['role' => 'admin']);

// If an admin exists, redirect to the sign-in page
if ($adminExists) {
    header("Location: signin.php");
    exit();
}

// Initialize variables
$error = '';

// Process signup form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $defaultRole = 'admin'; // Default role set to 'admin'

    // Generate a new ObjectId for the new admin
    $adminId = new MongoDB\BSON\ObjectId();

    // Check if the email already exists
    $existingUser = $collection->findOne(['email' => $email]);
    if ($existingUser) {
        $error = "Email already exists. Please choose a different email.";
    } else {
        // Prepare new user document with the generated ID
        $newAdmin = [
            "_id" => $adminId, // Use the new ObjectId
            "username" => $username,
            "fullName" => $fullName,
            "email" => $email,
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "role" => $defaultRole
        ];

        // Insert new user document into the collection
        $insertResult = $collection->insertOne($newAdmin);

        // Redirect to sign-in page after successful registration
        if ($insertResult->getInsertedCount() > 0) {
            header("Location: signin.php");
            exit();
        } else {
            $error = "Failed to register user. Please try again.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-no-repeat bg-cover bg-center">
    <div class="bg-gray-100 min-h-screen flex flex-col">
        <div class="container max-w-sm mx-auto flex-1 flex flex-col items-center justify-center px-2">
            <div class="bg-white px-6 py-8 rounded shadow-md text-black w-full">
                <h1 class="mb-8 text-3xl text-center">Sign Up</h1>
                <?php if (!empty($error)) : ?>
                    <div class="mb-4">
                        <div class="font-medium text-red-600">
                            <?php echo $error; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">

                    <input 
                        type="text"
                        class="block border border-gray-300 w-full p-3 rounded mb-4"
                        name="username"
                        placeholder="Username" />

                    <input 
                        type="text"
                        class="block border border-gray-300 w-full p-3 rounded mb-4"
                        name="fullName"
                        placeholder="Full Name" />

                    <input 
                        type="email"
                        class="block border border-gray-300 w-full p-3 rounded mb-4"
                        name="email"
                        placeholder="Email" />

                    <input 
                        type="password"
                        class="block border border-gray-300 w-full p-3 rounded mb-4"
                        name="password"
                        placeholder="Password" />

                    <button
                        type="submit"
                        class="w-full text-center py-3 rounded bg-green-500 text-white hover:bg-green-600 focus:outline-none my-1"
                    >Sign Up</button>
                </form>
            </div>

        </div>
    </div>
</body>

</html>
