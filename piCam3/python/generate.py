##
# @file generate.py
# @brief Generat a mp4-video of a series of images and uploads it to a API on the web.
# @author Remko Welling (pe1mew@pe1mew.nl)
# @date see version table
#
# @section Versions
#
#  version|date      |Comment
#  -------|----------|-------------------
#   0.0.2 |  3-3-2024| Change file name to YYYMMDD.
#   0.0.3 |  3-3-2024| Refactoring code, added function for removal of used files and uplad for api.
#   0.1.0 |  4-3-2024| First version for release successfully executed 24 hours of testing. 
#   0.1.2 |  1-7-2024| Modification to reduce image size for video and set nice to lower priority to allow normal operation
#   0.1.3 | 10-7-2024| Added compression of images to reduce the file size of the video. 
#
# @ToDo
# 
# @todo make compression of image configurable through config file.
# @section Execution
#
# Example: `python generate.py --config_file config.json`
#
# @section Notes
#
# ### Disclaimer
# This Python code is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
#
# ### License
# This Python code is free software:
# you can redistribute it and/or modify it under the terms of a
# Creative Commons Attribution-NonCommercial 4.0 International License
# (http://creativecommons.org/licenses/by-nc/4.0/) by PE1MEW (http://pe1mew.nl)
# E-mail: pe1mew@pe1mew.nl
#
# <a rel="license" href="http://creativecommons.org/licenses/by-nc/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nc/4.0/88x31.png" /></a><br />This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc/4.0/">Creative Commons Attribution-NonCommercial 4.0 International License</a>.
#

import requests
import argparse
import cv2
import os
import json
import time

def load_configuration(config_file):
    """! load parameters from configuration file.
    @param string   Path and filename.
    @return json    Object with configuration parameters.
    """
    try:
        with open(config_file, 'r') as file:
            config = json.load(file)
        return config
    except FileNotFoundError:
        print(f"Error: Configuration file '{config_file}' not found.")
        return {}
    except json.JSONDecodeError:
        print(f"Error: Unable to parse JSON in configuration file '{config_file}'.")
        return {}

def compress_image(input_path, output_path, quality=90):
    """Compress an image to reduce file size."""
    image = cv2.imread(input_path)
    if image is None:
        print(f"Error: Unable to read image '{input_path}'.")
        return False
    # Compress the image and save it
    cv2.imwrite(output_path, image, [int(cv2.IMWRITE_JPEG_QUALITY), quality])
    return True

def create_video(config):
    """! Generate video.
    @param json     json object with configuration items.
    @return string  Filename of generated video
    """
    output_path = config.get('output_path')
    resize_width = config.get('resize_width', 640)  # Default width
    resize_height = config.get('resize_height', 480)  # Default height
    compressed_path = os.path.join(output_path, 'compressed')

    # Create a directory for compressed images if it doesn't exist
    if not os.path.exists(compressed_path):
        os.makedirs(compressed_path)

    images = [img for img in os.listdir(output_path) if img.endswith(".jpg")]
    images.sort()

    if not images:
        print("No JPG images found in the specified directory.")
        return

    # Generate the filename based on current date and time
    current_datetime = time.strftime("%Y%m%d")
    filename = f"{current_datetime}.mp4"

    # Read the first image to get its original dimensions
    frame = cv2.imread(os.path.join(output_path, images[0]))
    height, width, layers = frame.shape

    # Set up the video writer with the resized dimensions
    video = cv2.VideoWriter(os.path.join(output_path, filename), cv2.VideoWriter_fourcc(*'avc1'), 2, (resize_width, resize_height))

    image_quality = config.get('video_image_quality', 90)  # Default image quality

    for image in images:
        img_path = os.path.join(output_path, image)
        compressed_img_path = os.path.join(compressed_path, image)

        if compress_image(img_path, compressed_img_path, image_quality):
            frame = cv2.imread(compressed_img_path)
            if frame is not None:
                # Resize the frame
                resized_frame = cv2.resize(frame, (resize_width, resize_height))
                video.write(resized_frame)
 
    cv2.destroyAllWindows()
    video.release()

    print(f"Created video: {filename}")
    return filename


def remove_images(config):
    """! Remove all .JPG images.
    @param json     json object with configuration items.
    """
    output_path = config.get('output_path')

    try:
        # Get a list of all JPG files in the output path
        jpg_files = [f for f in os.listdir(output_path) if f.lower().endswith('.jpg')]
        jpg_files = sorted(jpg_files, key=lambda x: os.path.getmtime(os.path.join(output_path, x)))

        # Calculate the number of files to remove
        num_to_remove = len(jpg_files)

        # Remove the oldest JPG files
        for i in range(num_to_remove):
            file_to_remove = os.path.join(output_path, jpg_files[i])
            os.remove(file_to_remove)
            print(f"Removed image: {file_to_remove}")

    except Exception as e:
        print(f"Error removing images: {e}")

def remove_movies(config):
    """! Remove all .mp4 images.
    @param json     json object with configuration items.
    """
    output_path = config.get('output_path')

    try:
        # Get a list of all JPG files in the output path
        mp4_files = [f for f in os.listdir(output_path) if f.lower().endswith('.mp4')]
        mp4_files = sorted(mp4_files, key=lambda x: os.path.getmtime(os.path.join(output_path, x)))

        # Calculate the number of files to remove
        num_to_remove = len(mp4_files)

        # Remove the oldest JPG files
        for i in range(num_to_remove):
            file_to_remove = os.path.join(output_path, mp4_files[i])
            os.remove(file_to_remove)
            print(f"Removed image: {file_to_remove}")

    except Exception as e:
        print(f"Error removing images: {e}")

def upload_video(config, filename):
    """! capture save and upload image.
    @param json     Object with configuration parameters.
    @param string   To be uploaded filename.
    """
    try:
        # Check if upload_url and source_identifier are provided
        if config.get('upload_url') and config.get('source_identifier'):
            # Prepare headers with sourceIdentifier
            headers = {'sourceIdentifier': config['source_identifier']}

            # Combine output_path and file name for uploading
            output_path_with_filename = os.path.join(config.get('output_path', 'images'), filename)

            # Prepare files for the POST request
            files = {'image': open(output_path_with_filename, 'rb')}

            # Perform HTTP POST request
            response = requests.post(config['upload_url'], files=files, headers=headers)

            # Print the response from the server
            print("Upload response:", response.text)

    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    # Set lower priority for the process
    os.nice(10)  # Increase the value to lower priority

    # Set up command-line argument parser
    parser = argparse.ArgumentParser(description="Capture an image on a Raspberry Pi, save it to disk, and optionally upload it.")
    parser.add_argument("--config_file", help="Path to the JSON configuration file")
    args = parser.parse_args()

    # Load configuration from the JSON file
    if args.config_file:
        config = load_configuration(args.config_file)
        
        # Call the functions to generate vidio from files, remove used files and upload.
        videofile = create_video(config)
        remove_images(config)
        upload_video(config, videofile)
    else:
        print("Error: Please provide a configuration file using the --config_file argument.")
