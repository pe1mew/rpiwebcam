<!--! 
    @file  index.php
    @brief Main page of the webcam project.

    This php file is responsible for displaying the homepage of the website. It dynamically 
    loads the latest image from the server and provides options for users to view a mosaic 
    of the last 24-hour captures or a video of those captures.

    @details
    - The PHP script in this file scans a directory for images and loads the latest image.
    - It also includes buttons that link to additional features like a mosaic and video view.
    - The layout is responsive and adjusts to different screen sizes.

    @version 0.24
    @date 18-8-2024
    @author Remko Welling (PE1MEW) pe1mew@gmail.com
    
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        img {
            max-width: 100%;
            max-height: 90vh; /* Adjust as needed */
            display: block;
            margin-top: 0; /* Align image to the top */
        }

        .text-header {
            text-align: center;
            margin-top: 15px;
            margin-bottom: 15px; /* Adjust as needed for space between text and image */
            font-size: 20px; /* Adjust as needed */
            font-weight: bold;
            font-family: 'Arial', sans-serif;
        }

        .text-container {
            text-align: center;
            margin-top: 15px;
            margin-bottom: 15px; /* Adjust as needed for space between text and image */
            font-size: 20px; /* Adjust as needed */
            font-family: 'Arial', sans-serif;
            line-height: 2.5; /* Increase the line spacing (1.8 is an example value) */
        }

        .text-footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px; /* Adjust as needed */
            font-family: 'Arial', sans-serif;
            font-style: italic; 
            line-height: 1; /* Increase the line spacing (1.8 is an example value) */
        }

        .text-message {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 20px; /* Adjust as needed */
            font-family: 'Arial', sans-serif;
            line-height: 2.5; /* Increase the line spacing */
            height: 100vh; /* Ensure the div takes full height of the viewport */
        }

        #mosaicButton {
            padding: 10px;
            background-color: #4285f4;
            color: #fff;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        
        #videoButton {
            padding: 10px;
            background-color: #f44285;
            color: #fff;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php
    
        // Include authentication. Configure 'auth.php' to enable authentication.
        require_once 'auth.php';
        
        $directory = 'images'; // Replace with the path to your image directory

        // Scan the directory for JPG files
        $files = glob($directory . '/*.jpg');

        // Sort the files by modification time, newest first
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        // Get the latest file
        $latestFile = reset($files);

        // Check if there are any JPG files in the directory
        if ($latestFile) {
            // Output image content
            echo '<img src="data:image/jpeg;base64,' . base64_encode(file_get_contents($latestFile)) . '" alt="Latest Image">';
            
            // Add instructions
            echo '<div class="text-container">Refresh page for latest image, click <a href="mosaic.php" id="mosaicButton">Mosaic</a> for 24 hour captures or <a href="video.php" id="videoButton">Video</a> of last 24 hour captures.</div>';
        } else {
            // Present message when no images are found to be displayed.
            echo '<div class="text-message">No images found to display.</div>';
        }

        // Present footer with relevant information about the source and documentation of the project.
        echo '<div id="footer" class="text-footer"> Documentation: <a href="https://github.com/pe1mew/rpiwebcam" target="_blank">https://github.com/pe1mew/rpiwebcam</a> | Version 0.24.</div>';
    ?>
</body>
</html>


