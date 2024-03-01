<?php
require 'vendor/autoload.php'; // Include Composer's autoloader

// Use MongoDB classes
use MongoDB\Client;
use MongoDB\Driver\Exception\Exception as MongoDBException;

// Connection URI
$uri = "mongodb://localhost:27017";

// Database name
$dbName = "socialMedia";

// Collection name
$collectionName = "test_collection";

try {
    // Create a new MongoDB client
    $client = new Client($uri);
    
    // Select the database and collection
    $collection = $client->$dbName->$collectionName;
    
    // Data to insert
    $data = [
        ['name' => 'John', 'age' => 30],
        ['name' => 'Alice', 'age' => 25],
        ['name' => 'Bob', 'age' => 35],
    ];

    // Insert data into the collection
    $result = $collection->insertMany($data);

    // Output success message
    echo "Collection created and data inserted successfully\n";
} catch (MongoDBException $e) {
    // Output error message
    echo "Failed to create collection or insert data: " . $e->getMessage() . "\n";
}
?>
