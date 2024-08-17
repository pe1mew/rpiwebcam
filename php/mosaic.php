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
        
        include 'footer.php';  // footer.

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
