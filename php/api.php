<?php

$expectedSourceIdentifier = 'xXj4gkS6yB0LIwfifkAz';

// Step 1: Check if the "sourceidentifier" header is present and has the correct value
if (isset($_SERVER['HTTP_SOURCEIDENTIFIER']) &&
    $_SERVER['HTTP_SOURCEIDENTIFIER'] === $expectedSourceIdentifier) {

    // Step 2: Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Step 3: Check if the request contains a file
        if (isset($_FILES['image'])) {

            // Set the target directory
            $targetDirectory = 'images/';

            // Get the file details
            $fileName = $_FILES['image']['name'];
            $fileTempName = $_FILES['image']['tmp_name'];
            $fileSize = $_FILES['image']['size'];

            // Set the destination path
            $destinationPath = $targetDirectory . $fileName;

            // Move the uploaded file to the destination path
            if (move_uploaded_file($fileTempName, $destinationPath)) {
                // File upload successful
                
                // Remove images older than 24 hours
                cleanupOldImages($targetDirectory);

                $response = array('status' => 'success', 'message' => 'Image uploaded successfully.');
            } else {
                // File upload failed
                $response = array('status' => 'error', 'message' => 'Failed to upload image.');
            }

        } else {
            // No file in the request
            $response = array('status' => 'error', 'message' => 'No image file found in the request.');
        }

    } else {
        // Invalid request method
        $response = array('status' => 'error', 'message' => 'Invalid request method.');
    }

} else {
    // Invalid or missing sourceidentifier header
    $response = array('status' => 'error', 'message' => 'Authentication failure.');
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);

// Function to remove images older than 24 hours
function cleanupOldImages($directory) {
    $files = glob($directory . '*.*');
    $currentTimestamp = time();

    foreach ($files as $file) {
        if (is_file($file)) {
            $fileTimestamp = filemtime($file);
            // Check if the file is older than 24 hours
            if (($currentTimestamp - $fileTimestamp) > (24 * 3600)) {
//            if (($currentTimestamp - $fileTimestamp) > (1 * 3600)) {  // for testing 1 hour
                unlink($file); // Remove the file
            }
        }
    }
}
