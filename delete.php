<?php
require 'vendor/autoload.php'; // Include Composer's autoloader

// Use MongoDB classes
use MongoDB\Client;

// Connection URI
$uri = "mongodb://localhost:27017";

// Database name
$dbName = "socialMedia";

// Collection name
$collectionName = "users"; // Adjusted collection name

// Start the session
session_start();

// Connect to MongoDB
try {
    $client = new MongoDB\Client($uri);
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Failed to connect to database: " . $e->getMessage() . "\n";
    exit;
}

// Select the database and collection
$collection = $client->$dbName->$collectionName;

// Check if the user ID is present in the URL
if(isset($_GET['userId'])) {
    // Get the user ID from the URL parameter
    $userId = $_GET['userId'];

    // Ensure the user ID is not empty
    if(!empty($userId)) {
        // Find the user with the given ID
        $user = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);

        // If user is found, delete the user
        if ($user) {
            // Delete the user from the collection
            $result = $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);
            if ($result->getDeletedCount() === 1) {
                // Redirect back to index.php after successful deletion
                header("Location: index.php");
                exit();
            } else {
                // Display an error message if deletion fails
                echo "Failed to delete user.";
            }
        } else {
            // Display an error message if the user is not found
            echo "<p>User not found.</p>";
        }
    } else {
        // Display an error message if the user ID is empty
        echo "<p>User ID is empty.</p>";
    }
} else {
    // Display an error message if the user ID parameter is not set
    echo "<p>User ID parameter is missing.</p>";
}
?>
