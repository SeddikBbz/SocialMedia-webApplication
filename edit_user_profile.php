<?php
session_start(); // Start the session

// Include Composer's autoloader
require 'vendor/autoload.php';

// Use MongoDB classes
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

// Connection URI
$uri = "mongodb://localhost:27017";

// Database name
$dbName = "socialMedia";

// Collection name for regular users
$userCollectionName = "users";

// Collection name for admins
$adminCollectionName = "users_admin";

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
$adminCollection = $database->selectCollection($adminCollectionName);

// Check if the user is not logged in
if (!isset($_SESSION['userId'])) {
    // Redirect the user to the login page if not logged in
    header("Location: signin.php");
    exit(); // Stop further execution
}

// Get the current user ID from the session
$currentUserId = $_SESSION['userId'] ?? null;

// Construct the ObjectId from the hexadecimal string
$currentUserId = new ObjectId($currentUserId);

// Retrieve the current user's data from the regular users collection
$currentUser = $userCollection->findOne(['_id' => $currentUserId]);

// Retrieve the current admin's data from the users_admin collection based on the user's ID
$currentAdmin = $adminCollection->findOne(['_id' => $currentUserId]);

// Initialize the success message variable
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if new password matches confirm password
    if ($password !== $confirm_password) {
        echo "<script>alert('Password and Confirm Password do not match. Please try again.');</script>";
        exit();
    }

    // Check if the current user is an admin
    if ($currentAdmin) {
        // Update email if changed
        $currentAdmin['email'] = $email;

        // Update password if changed
        if (!empty($password)) {
            $currentAdmin['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Update the admin's data in the users_admin collection
        $result = $adminCollection->updateOne(
            ['_id' => $currentUserId],
            ['$set' => [
                'email' => $currentAdmin['email'],
                'password' => $currentAdmin['password']
            ]]
        );
    } else {
        // Update email if changed
        $currentUser['email'] = $email;

        // Update password if changed
        if (!empty($password)) {
            $currentUser['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Update the user's data in the users collection
        $result = $userCollection->updateOne(
            ['_id' => $currentUserId],
            ['$set' => [
                'email' => $currentUser['email'],
                'password' => $currentUser['password']
            ]]
        );
    }

    if ($result->getModifiedCount() > 0) {
        // Set the success message
        $successMessage = "<div class='container mt-3'>
                            <div class='alert alert-success' role='alert'>Profile updated successfully!</div>
                        </div>";

        // Redirect to the index page with page reload after 3 seconds
        echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 1000);</script>";
                        
    } else {
        // Handle update failure
        $successMessage = "<div class='container mt-3'>
                            <div class='alert alert-danger' role='alert'>Failed to update profile!</div>
                        </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
    <div class="container mt-5">
        <h2 class="mb-4">Edit Profile</h2>
        <?php echo $successMessage; ?> <!-- Display the success message -->
        <?php if ($currentUser || $currentAdmin): ?> <!-- Check if $currentUser or $currentAdmin exists -->
            <form method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $currentUser ? $currentUser['email'] : $currentAdmin['email']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        <?php else: ?>
            <div class="alert alert-danger" role="alert">User data not found.</div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS (optional, if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
