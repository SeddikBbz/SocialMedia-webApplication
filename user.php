<?php
require 'vendor/autoload.php'; // Include Composer's autoloader

// Use MongoDB classes
use MongoDB\Client;
use MongoDB\BSON\ObjectId; // Import ObjectId class

session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addUser'])) {
        // Check if all required fields are filled out
        if (empty($_POST['username']) || empty($_POST['fullName']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['birthdate']) || empty($_POST['location']) || empty($_POST['bio'])) {
            echo "<div class='container mt-3'>";
            echo "<div class='alert alert-danger' role='alert'>All fields are required.</div>";
            echo "</div>";
            exit(); // Stop further execution
        }

        // Connection URI
        $uri = "mongodb://localhost:27017";

        // Connect to MongoDB
        $client = new Client($uri);

        // Select the database and collection
        $collection = $client->socialMedia->users;

        // Check if the user already exists
        $existingUser = $collection->findOne(['email' => $_POST['email']]);

        if ($existingUser) {
            // Show error message
            echo "<div class='container mt-3'>";
            echo "<div class='alert alert-danger' role='alert'>User already exists. Please choose a different username or email.</div>";
            echo "</div>";
            exit(); // Stop further execution
        }
        

        // Prepare new user data
        $newUser = [
            "_id"      => new ObjectId(), // Generate a unique user ID
            "username" => $_POST['username'],
            "fullName" => $_POST['fullName'],
            "email"    => $_POST['email'],
            "password" => password_hash($_POST['password'], PASSWORD_DEFAULT), // Hash the password
            "birthdate"=> $_POST['birthdate'],
            "location" => $_POST['location'] ?? "",
            "bio"      => $_POST['bio'] ?? "",
            "friends"  => [],
            "posts"    => [],
            "createdAt"=> date("Y-m-d\TH:i:s\Z"),
            "role"     => 'user' // Set default role
        ];

        // Insert new user into the collection
        $insertResult = $collection->insertOne($newUser); // Use insertOne() for single document insertion

        // Check if insertion was successful
        if ($insertResult->getInsertedCount() > 0) {
            // Show success message
            echo "<div class='container mt-3'>";
            echo "<div class='alert alert-success' role='alert'>User added successfully!</div>";
            echo "</div>";
            
            // Redirect to the index page with page reload after 3 seconds
            echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 1000);</script>";
            
        } else {
            // Show error message
            echo "<div class='container mt-3'>";
            echo "<div class='alert alert-danger' role='alert'>Failed to add user. Please try again.</div>";
            echo "</div>";
        }
    } elseif (isset($_POST['uploadCsv'])) {
        // Handle form submission for uploading CSV file
        $successCount = 0; // Initialize counter for successful inserts

        if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] === UPLOAD_ERR_OK) {
            // Get the temporary file name
            $tmpFileName = $_FILES['csvFile']['tmp_name'];
            
            // Read the CSV file into an array
            $csvData = array_map('str_getcsv', file($tmpFileName));
            
            // Remove the header row
            $header = array_shift($csvData);
            
            // Connection URI
            $uri = "mongodb://localhost:27017";

            // Connect to MongoDB
            $client = new Client($uri);

            // Select the database and collection
            $collection = $client->socialMedia->users;

            // Iterate over each row in the CSV data
            foreach ($csvData as $row) {
                // Check if the user already exists
                $existingUser = $collection->findOne(['email' => $row[2]]);

                if ($existingUser) {
                    continue; // Skip insertion if user already exists
                }

                // Prepare new user data
                $newUser = [
                    "_id"      => new ObjectId(), // Generate a unique user ID
                    "username" => $row[0], // Assuming the first column is username
                    "fullName" => $row[1], // Assuming the second column is full name
                    "email"    => $row[2], // Assuming the third column is email
                    "password" => password_hash($row[3], PASSWORD_DEFAULT), // Assuming the fourth column is password
                    "birthdate"=> $row[4], // Assuming the fifth column is birthdate
                    "location" => $row[5] ?? "", // Assuming the sixth column is location
                    "bio"      => $row[6] ?? "", // Assuming the seventh column is bio
                    "friends"  => [],
                    "posts"    => [],
                    "createdAt"=> date("Y-m-d\TH:i:s\Z"),
                    "role"     => 'user' // Set default role
                ];

                // Insert new user into the collection
                $insertResult = $collection->insertOne($newUser); // Use insertOne() for single document insertion

                // Check if insertion was successful
                if ($insertResult->getInsertedCount() > 0) {
                    // Increment successful insert counter
                    $successCount++;
                }
            }

            // Check if any users were successfully inserted
            if ($successCount > 0) {
                // Show success message
                echo "<div class='container mt-3'>";
                echo "<div class='alert alert-success' role='alert'>$successCount users added successfully!</div>";
                echo "</div>";

                
            // Redirect to the index page with page reload after 3 seconds
            echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 1000);</script>";
            } else {
                // Show error message
                echo "<div class='container mt-3'>";
                echo "<div class='alert alert-danger' role='alert'>Failed to add users. Please try again.</div>";
                echo "</div>";
            }
        } else {
            // Show error message if no file was uploaded
            echo "<div class='container mt-3'>";
            echo "<div class='alert alert-danger' role='alert'>Please select a CSV file to upload.</div>";
            echo "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-5">Add User</h2>
        <div class="row">
            <!-- User input section -->
            <div class="col-md-5">
                <h5 class="mb-5">Manual user addition</h5>
                <form method="post">
                    <!-- Input fields for manual user addition -->
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
                    <div class="mb-3">
                        <label for="birthdate" class="form-label">Birthdate</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location">
                    </div>
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" name="addUser">Add User</button>
                </form>
            </div>

            <!-- Vertical line -->
            <div class="col-md-1 vertical-line"></div>
            
            <!-- File upload section -->
            <div class="col-md-6">
                <h5 class="mb-4">Auto user addition</h5>
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="csvFile" class="form-label">Upload CSV File</label>
                        <input type="file" class="form-control" id="csvFile" name="csvFile">
                    </div>
                    <button type="submit" class="btn btn-primary" name="uploadCsv">Upload CSV</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional, if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
