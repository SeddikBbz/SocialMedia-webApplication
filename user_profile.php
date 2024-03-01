<?php
// Start the session
session_start();

// Include Composer's autoloader
require 'vendor/autoload.php';

// Use MongoDB classes
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

// Connection URI
$uri = "mongodb://localhost:27017";

// Database name
$dbName = "socialMedia";

// Collection name
$collectionName = "users";

// Connect to MongoDB
try {
    $client = new Client($uri);
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Failed to connect to database: " . $e->getMessage() . "\n";
    exit;
}

// Select the database and collection
$database = $client->selectDatabase($dbName);
$collection = $database->selectCollection($collectionName);

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    // Redirect to the sign-in page if the user is not logged in
    header("Location: signin.php");
    exit;
}

// Check if the logout parameter is present in the URL
if (isset($_GET['logout'])) {
    // Unset all session variables
    $_SESSION = [];

    // Destroy the session
    session_destroy();

    // Redirect to the sign-in page
    header("Location: signin.php");
    exit;
}

// Get the current user ID from the session
$currentUserId = $_SESSION['userId'];

// Retrieve the current user's data from MongoDB
$currentUser = $collection->findOne(['_id' => new ObjectId($currentUserId)]);

// Check if user data is found
if (!$currentUser) {
    echo "User not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4"> <!-- Reduce top margin -->
        <div class="d-flex justify-content-end mb-3">
            <a href="edit_user_profile.php" class="btn btn-outline-success me-2">Edit Profile</a>
            <a href="?logout" class="btn btn-outline-danger">Logout</a>
        </div>

        <h2 class="text-center mb-4">User Profile</h2>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="text-center mb-4">
                    <a href="post.php?userId=<?php echo $currentUser['_id']; ?>" class="btn btn-outline-success">Add Post</a>
                    <a href="comment.php?userId=<?php echo $currentUser['_id']; ?>" class="btn btn-outline-info">Add Comment</a>
                    <a href="like.php?userId=<?php echo $currentUser['_id']; ?>" class="btn btn-outline-warning">Add Like</a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">User Information</h5>
                        <?php if ($currentUser): ?>
                            <table class="table table-bordered mt-2"> <!-- Reduced top margin -->
                                <tr>
                                    <td><strong>Username:</strong></td>
                                    <td><?php echo $currentUser['username']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Full Name:</strong></td>
                                    <td><?php echo $currentUser['fullName']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><?php echo $currentUser['email']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Birthdate:</strong></td>
                                    <td><?php echo $currentUser['birthdate']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Location:</strong></td>
                                    <td><?php echo $currentUser['location']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Bio:</strong></td>
                                    <td><?php echo $currentUser['bio']; ?></td>
                                </tr>
                            </table>
                        <?php else: ?>
                            <p class="text-center">User data not found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>


</html>
