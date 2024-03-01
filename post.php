<?php
// Start the session
session_start();

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

// Connect to MongoDB
try {
    $client = new Client($uri);
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Failed to connect to database: " . $e->getMessage() . "\n";
    exit;
}

// Select the database and collection
$database = $client->selectDatabase($dbName);
$userCollection = $database->selectCollection($userCollectionName);
$postCollection = $database->selectCollection($postCollectionName);

// Get user ID from URL parameter
$userId = $_GET['userId'] ?? null;

// Initialize user variable
$user = null;

if ($userId !== null) {
    // Find user by ID
    $user = $userCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);

    if (!$user) {
        echo "<div class='alert alert-danger' role='alert'>User not found!</div>";
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Post</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .user-id {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Add Post</h2>
               
        <?php if ($user !== null): ?>
            <form method="post">
                <input type="hidden" name="userId" value="<?php echo $userId; ?>">
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Add Post</button>
            </form>
        <?php endif; ?>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve user ID and post content from the form
        $userId = $_POST['userId'];
        $content = $_POST['content'];

        // Prepare new post data
        $newPost = [
            "userId" => new MongoDB\BSON\ObjectId($userId),
            "content" => $content,
            "likes" => [],
            "comments" => [],
            "createdAt" => new MongoDB\BSON\UTCDateTime(time() * 1000) // Current time in milliseconds
        ];
        
          // Update the user document to include the new post in the posts array
        $updateResult = $userCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($userId)],
            ['$push' => ['posts' => $newPost]]
        );

        // Insert new post into the MongoDB collection
        $result = $postCollection->insertOne($newPost);

        // Check if insertion was successful
        if ($result->getInsertedCount() > 0) {
            // Redirect to the same page to clear the form
            header("Location: ".$_SERVER['PHP_SELF']."?userId=".$userId);
            exit();
        } else {
            echo "<div class='container mt-3 alert alert-danger' role='alert'>Failed to add post. Please try again.</div>";
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
