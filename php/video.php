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
            font-weight: bold;
            font-family: 'Arial', sans-serif;
        }

        #video-container {
            position: relative;
        }

        video {
            width: 640px;
            height: 360px;
            display: block;
            margin: 0 auto;
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
        echo '<div class="text-container">Video of previous 24 hour captures.</div>';
        echo '<video controls>
                <source src="' . $latestVideo . '" type="video/mp4">
                Your browser does not support the video tag.
              </video>';
    } else {
        echo 'No videos found.';
    }
    ?>
</div>

</body>
</html>
