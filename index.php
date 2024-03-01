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
    // echo "Connecting to database successfully\n";
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Failed to connect to database: " . $e->getMessage() . "\n";
    exit;
}

// Select the database and collection
$collection = $client->$dbName->$collectionName;

// Check if the user clicked the logout button
if(isset($_GET['logout'])) {
    // Unset all of the session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the login page or any other desired page
    header("Location: signin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Table</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-4">
            <h2>User Table</h2>
            <div>
                <a href="add_admin.php" class="btn btn-outline-primary me-2">Add Admin</a>
                <a href="user.php" class="btn btn-outline-primary me-2">Add User</a>
                <a href="edit_user_profile.php" class="btn btn-outline-success me-2">Edit Profile</a>
                <a href="?logout" class="btn btn-outline-danger">Logout</a> <!-- Logout link -->
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Birthdate</th>
                        <th>Location</th>
                        <th>Bio</th>
                        <th>Action</th> <!-- New column for delete button -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Retrieve data from MongoDB collection
                    $cursor = $collection->find();

                    // Iterate over data and display in table rows
                    foreach ($cursor as $document) {
                        echo "<tr>";
                        echo "<td>".($document['username'] ?? "")."</td>";
                        echo "<td>".($document['fullName'] ?? "")."</td>";
                        echo "<td>".($document['email'] ?? "")."</td>";
                        echo "<td>".($document['birthdate'] ?? "")."</td>";
                        echo "<td>".($document['location'] ?? "")."</td>";
                        echo "<td>".($document['bio'] ?? "")."</td>";
                        echo "<td><a href='delete.php?userId=" . $document['_id'] . "' class='btn btn-outline-danger'>Delete</a></td>"; // Delete button with user ID as parameter
                        echo "</tr>";
                        
                    }
                    
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS (optional, if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
