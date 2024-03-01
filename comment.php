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

// Collection names
$userCollectionName = "users";
$postCollectionName = "posts";
$commentCollectionName = "comments"; // New collection for comments

// Connect to MongoDB
try {
    $client = new Client($uri);
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Failed to connect to database: " . $e->getMessage() . "\n";
    exit;
}

// Select the database and collections
$database = $client->selectDatabase($dbName);
$userCollection = $database->selectCollection($userCollectionName);
$postCollection = $database->selectCollection($postCollectionName);
$commentCollection = $database->selectCollection($commentCollectionName); // New collection

// Get user ID from URL parameter
$userId = $_GET['userId'] ?? null;

if ($userId !== null) {
    // Find user by ID
    $user = $userCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Comment</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Add Comment</h2>
        
        <?php
        // Check if the session variable $_SESSION['userId'] is set
        if (isset($_SESSION['userId'])) {
            $userId = $_SESSION['userId']; // Adjust this based on your authentication method

            // Fetch user's posts from the database
            $userPosts = [];
            if ($userId !== null) {
                $userPostsCursor = $postCollection->find(['userId' => new MongoDB\BSON\ObjectId($userId)]);
                foreach ($userPostsCursor as $post) {
                    $userPosts[] = $post;
                }
            }

            // Display posts or message accordingly
            if (!empty($userPosts)) {
                foreach ($userPosts as $post) {
                    echo '<div class="mb-3">';
                    echo '<p>' . $post['content'] . '</p>';
                    echo '<hr>';
                    echo '<form method="post">';
                    echo '<input type="hidden" name="postId" value="' . $post['_id'] . '">';
                    echo '<div class="mb-3">';
                    echo '<label for="content" class="form-label">Your Comment</label>';
                    echo '<textarea class="form-control" id="content" name="content" rows="3" required></textarea>';
                    echo '</div>';
                    echo '<button type="submit" class="btn btn-primary">Add Comment</button>';
                    echo '</form>';
                    echo '</div>';
                }
            } else {
                echo '<div class="alert alert-warning" role="alert">';
                echo 'You have no posts. Please add a post first.';
                echo '</div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">';
            echo 'You are not logged in. Please log in to view and add comments.';
            echo '</div>';
        }
        ?>

    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['userId'])) {
        // Retrieve user ID from session or any other authentication method you use
        $userId = $_SESSION['userId']; // Adjust this based on your authentication method
        $postId = $_POST['postId'];
        $content = $_POST['content'];

        // Prepare new comment data
        $newComment = [
            "userId" => new MongoDB\BSON\ObjectId($userId),
            "postId" => new MongoDB\BSON\ObjectId($postId),
            "content" => $content,
            "createdAt" => new MongoDB\BSON\UTCDateTime(time() * 1000) // Current time in milliseconds
        ];

        // Add comment to the post document in the posts collection
        $updateResult = $postCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($postId)],
            ['$push' => ['comments' => $newComment]]
        );

        // Insert comment into the comments collection
        $insertResult = $commentCollection->insertOne($newComment);

        // Check if insertion was successful
        if ($updateResult->getModifiedCount() > 0 && $insertResult->getInsertedCount() > 0) {
            // Redirect to the same page to clear the form
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "<div class='container mt-3 alert alert-danger' role='alert'>Failed to add comment. Please try again.</div>";
        }
    }
    ?>

    <!-- Bootstrap JS (optional, if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Return Home Button -->
    <div class="container mt-3 d-flex justify-content-end">
        <a href="user_profile.php" class="btn btn-secondary">Return Home</a>
    </div>
</body>
</html>
