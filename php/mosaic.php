<!--!

    @file mosaic.php
    @brief A dynamic image gallery with full-size image previews and tooltips.
    
    This HTML and PHP file dynamically generates a thumbnail gallery of images from a specified 
    directory. Users can click on a thumbnail to view the full-size image in an overlay. Tooltips 
    with file names appear when hovering over the thumbnails.
    
    @details
    - The PHP script scans a specified directory for `.jpg` images and sorts them by file name in descending order.
    - Thumbnails are displayed in a flexible grid layout, adjusting to the available screen space.
    - When a user clicks on a thumbnail, a full-size preview of the image is shown in an overlay.
    - CSS handles the styling of the gallery, and JavaScript manages the interaction for opening and closing the full-size image preview.
    - The layout adapts to different screen sizes, making it responsive.

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
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .thumbnail {
            margin: 5px; /* Reduced margin to 5px */
            cursor: pointer;
            display: flex;
            flex-grow: 1;
            max-width: calc(15% - 10px); /* Adjusted max-width calculation */
            position: relative;
        }

        .thumbnail img {
            width: 100%; /* Set width to 100% to fill the container */
            height: auto; /* Maintain aspect ratio */
            max-width: 150%; /* Limit the maximum width to 150% of the original size */
            object-fit: contain;
        }

        .tooltip {
            visibility: hidden;
            width: auto;
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 5px;
            border-radius: 5px;
            position: fixed;
            z-index: 1;
            transform: translate(-50%, 0);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .thumbnail:hover .tooltip {
            visibility: visible;
            opacity: 1;
        }

        .text-container {
            text-align: center;
            margin-top: 15px;
            margin-bottom: 15px; /* Adjust as needed for space between text and image */
            font-size: 20px; /* Adjust as needed */
            font-family: 'Arial', sans-serif;
            line-height: 2.5; /* Increase the line spacing (1.8 is an example value) */
        }

        #fullSize {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            justify-content: center;
            align-items: center;
        }

        #fullSize img {
            max-width: 90%;
            max-height: 90%;
            margin: auto;
            cursor: pointer;
        }
        
        #mainButton {
            padding: 10px;
            background-color: #f2b543;
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

        // Sort the files by file name in descending order
        arsort($files);

        $imageCount = count($files);

        // Calculate the number of columns that fit on the screen
        $columnsToFit = floor((100 - 10) / (15 + 10)); /* Adjusted margin and max-width */

        // Calculate the number of rows and columns
        $rowCount = ceil($imageCount / $columnsToFit);
        $colCount = min($imageCount, $columnsToFit);

        $images = array_values($files); // Reset array keys

        // Display images in a matrix layout
        for ($row = 0; $row < $rowCount; $row++) {
            for ($col = 0; $col < $colCount; $col++) {
                $index = $row * $colCount + $col;
                if ($index < count($images)) {
                    echo '<div class="thumbnail" onclick="openFullSize(' . $index . ')">';
                    echo '<img src="' . $images[$index] . '" alt="Thumbnail">';
                    echo '<div class="tooltip">' . basename($images[$index]) . '</div>';
                    echo '</div>';
                }
            }
        }
        
        // Add instructions
        echo '<div id="menu" class="text-container">Click <a href="index.php" id="mainButton">Home</a> for homepage or <a href="video.php" id="videoButton">Video</a> of last 24 hour captures.</div>';

        
    ?>

    <div id="fullSize" onclick="closeFullSize()">
        <img id="fullSizeImage" src="" alt="Full Size Image">
    </div>

    <script>
        var images = <?php echo json_encode($images); ?>;
        var currentImageIndex = 0;

        function openFullSize(index) {
            currentImageIndex = index;
            document.getElementById('fullSizeImage').src = images[index];
            document.getElementById('fullSize').style.display = 'flex';
        }

        function closeFullSize() {
            document.getElementById('fullSize').style.display = 'none';
        }
    </script>
</body>
</html>
