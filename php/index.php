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
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        img {
            max-width: 100%;
            max-height: 80vh; /* Adjust as needed */
            display: block;
            margin: 0 auto;
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
    
        // Include the authentication file
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
            echo '<div class="text-header">Refresh page for latest image:</div>';
            echo '<img src="data:image/jpeg;base64,' . base64_encode(file_get_contents($latestFile)) . '" alt="Latest Image">';
            
            // Add the "Mosaic" button
            echo '<div class="text-container">Click <a href="mosaic.php" id="mosaicButton">Mosaic</a> for 24 hour captures or <a href="video.php" id="videoButton">Video</a> of last 24 hour captures.</div>';
        } else {
            echo 'No JPG images found in the directory.';
        }

        include 'footer.php';  // footer.
    ?>
</body>
</html>


