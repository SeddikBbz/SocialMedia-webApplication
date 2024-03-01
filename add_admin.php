<?php
require 'vendor/autoload.php'; // Include Composer's autoloader

use MongoDB\Client;
use MongoDB\BSON\ObjectId; // Import ObjectId class

session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connection URI
    $uri = "mongodb://localhost:27017";

    // Connect to MongoDB
    $client = new Client($uri);

    // Select the database and collection
    $dbName = "socialMedia";
    $collectionName = "users_admin";
    $collection = $client->$dbName->$collectionName;

    // Check if the provided email or username already exists
    $emailExists = $collection->findOne(['email' => $_POST['email']]);
    $usernameExists = $collection->findOne(['username' => $_POST['username']]);

    // If email or username already exists, display error message
    if ($emailExists || $usernameExists) {
        echo "<div class='container mt-3 alert alert-danger' role='alert'>Admin user with the provided email or username already exists.</div>";
    } else {
        // Generate a unique admin ID
        $adminId = new ObjectId();

        // Prepare new admin user data
        $newAdmin = [
            "_id" => $adminId, // Assign the generated admin ID
            "username" => $_POST['username'],
            "fullName" => $_POST['fullName'],
            "email" => $_POST['email'],
            "password" => password_hash($_POST['password'], PASSWORD_DEFAULT), // Hash the password
            "role" => "admin"
        ];

        // Insert new admin into the collection
        $insertResult = $collection->insertOne($newAdmin);

        // Check if insertion was successful
        if ($insertResult->getInsertedCount() > 0) {
            // Show success message
            echo "<div class='container mt-3 alert alert-success' role='alert'>Admin user added successfully.</div>";

            // Redirect to the index page with page reload after 3 seconds
            echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 1000);</script>";
        } else {
            // Show error message
            echo "<div class='container mt-3 alert alert-danger' role='alert'>Failed to add admin user. Please try again.</div>";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Add Admin</h2>
        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="fullName" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="fullName" name="fullName" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Admin</button>
        </form>
    </div>

    <!-- Bootstrap JS (optional, if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
