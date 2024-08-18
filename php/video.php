<!--!

    @file latest_video.php
    @brief Displays the latest video from the "images/" directory.

    This file is responsible for scanning the "images/" directory for .mp4 files, 
    sorting them by modification time, and displaying the latest video in an HTML video player.
    If no videos are found, it displays a "No videos found" message.

    @details
     - The PHP script uses `glob()` to fetch all .mp4 files from the specified directory.
     - The files are sorted by modification time in descending order using `usort()`.
     - The latest video is selected and displayed using the HTML5 `<video>` tag.
     - If no videos are found, a message is displayed to inform the user.

    The layout is styled with CSS to center the video player on the page. 
    Authentication is required via the `auth.php` file.

    @version 0.24
    @date 18-8-2024
    @author Remko Welling (PE1MEW) pe1mew@gmail.com

-->
 
 <?php
    $videosDirectory = 'images/';

    // Get all files in the directory
    $files = glob($videosDirectory . '*.mp4');

    // Sort files by modification time, latest first
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    // Get the latest video
    $latestVideo = isset($files[0]) ? $files[0] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latest Video</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .text-container {
            text-align: center;
            margin-top: 15px;
            margin-bottom: 15px; /* Adjust as needed for space between text and image */
            font-size: 20px; /* Adjust as needed */
            font-family: 'Arial', sans-serif;
            line-height: 2.5; /* Increase the line spacing (1.8 is an example value) */
        }

        #video-container {
            position: relative;
        }

        video {
            width: 80vw;  /* Set the video width to 80% of the viewport width */
            height: auto;  /* Maintain the aspect ratio */
            max-height: 80vh; /* Set a max-height of 80% of the viewport height */
            display: block;
            margin: 0 auto;
        }
        
        #mainButton {
            padding: 10px;
            background-color: #f2b543;
            color: #fff;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        
        #mosaicButton {
            padding: 10px;
            background-color: #4285f4;
            color: #fff;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        
    </style>
</head>
<body>
    
    <div id="video-container">
        <?php
            // Include the authentication file
            require_once 'auth.php';

            // Display the latest video if available
            if ($latestVideo) {
                echo '<video controls>
                        <source src="' . $latestVideo . '" type="video/mp4">
                        Your browser does not support the video tag.
                      </video>';
                
                // present menu
                echo '<div id="menu" class="text-container">Click <a href="index.php" id="mainButton">Home</a> for homepage or <a href="mosaic.php" id="mosaicButton">Mosaic</a> for 24 hour captures.</div>';
                
            } else {
                echo '<div id="infoMessage" class="text-container">No videos found.</div>';
            }
        ?>
    </div>

</body>
</html>
