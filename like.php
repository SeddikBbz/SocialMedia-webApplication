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

// Collection names
$userCollectionName = "users";
$postCollectionName = "posts";
$likeCollectionName = "likes";

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
$likeCollection = $database->selectCollection($likeCollectionName);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Like Posts</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Like Posts</h2>
        <div class="row">
            <?php
            // Check if user is logged in
            if (isset($_SESSION['userId'])) {
                $userId = $_SESSION['userId']; // Adjust this based on your authentication method

                // Fetch user's posts from the database
                $userPostsCursor = $postCollection->find(['userId' => new ObjectId($userId)]);

                // Iterate over the cursor and store posts in an array
                $userPosts = [];
                foreach ($userPostsCursor as $post) {
                    $post['_id'] = (string) $post['_id']; // Convert ObjectId to string
                    $userPosts[] = $post;
                }

                // Check if user has any posts
                if (!empty($userPosts)) {
                    // User has posts
                    foreach ($userPosts as $post) {
                        echo "<div class='col-md-4 mb-4'>";
                        echo "<div class='card'>";
                        echo "<div class='card-body'>";
                        // echo "<h5 class='card-title'>Post ID: " . $post['_id'] . "</h5>";
                        echo "<p class='card-text'>" . $post['content'] . "</p>";
                        echo "<form method='post'>";
                        echo "<input type='hidden' name='postId' value='" . $post['_id'] . "'>";
                        // Like button
                        echo "<button type='submit' class='btn btn-primary'>Like</button>";
                        echo "</form>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    // No posts message
                    echo '<div class="alert alert-warning" role="alert">';
                    echo 'You have no posts. Please add a post first.';
                    echo '</div>';
                }
            } else {
                // User not logged in message
                echo '<div class="alert alert-warning" role="alert">';
                echo 'Please log in to view posts.';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['userId'])) {
        $userId = $_SESSION['userId']; // Adjust this based on your authentication method
        $postId = $_POST['postId'];

        // Prepare new like data
        $newLike = [
            "postId" => new ObjectId($postId),
            "userId" => new ObjectId($userId),
            "createdAt" => new MongoDB\BSON\UTCDateTime(time() * 1000) // Current time in milliseconds
        ];

        // Insert new like into the MongoDB collection
        $insertResult = $likeCollection->insertOne($newLike);

        // Check if insertion was successful
        if ($insertResult->getInsertedCount() > 0) {
            // Update the post with the new like
            $updateResult = $postCollection->updateOne(
                ['_id' => new ObjectId($postId)],
                ['$push' => ['likes' => new ObjectId($userId)]]
            );

            // Check if update was successful
            if ($updateResult->getModifiedCount() > 0) {
                // Redirect to the same page to reflect the change
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "<div class='container mt-3 alert alert-danger' role='alert'>Failed to like post. Please try again.</div>";
            }
        } else {
            echo "<div class='container mt-3 alert alert-danger' role='alert'>Failed to like post. Please try again.</div>";
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
