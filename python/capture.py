##
# @file capture.py
# @brief Captures a image from a raspi camera and uploads it to a API on the web.
# @author Remko Welling (pe1mew@pe1mew.nl)
# @date see version table
#
# @section Versions
#
#  version|date      |Comment
#  -------|----------|-------------------
#   0.0.4 | 29-2-2024| Added limitation to upload only during daytime
#   0.0.5 |  2-3-2024| Corrected working of daytime detection to one that uses sun elevation above horizon
#   0.0.6 |  2-3-2024| Added Doxygen compatibel documentation.
#   0.0.7 |  3-3-2024| Cleanup, remove deletion of files, .
#   0.1.0 |  4-3-2024| First version for release successfully executed 24 hours of testing. 
#   0.0.1 | 28-5-2024| Added ability so set brightness of camera before capturing. 
#
# @section Execution
#
# Example: `python camera3_004.py --config_file config.json`
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
# 


import argparse
import json
import time
import os
from PIL import Image, ImageDraw, ImageFont
import picamera
import requests
import ephem  # Add the ephem library for sunrise and sunset calculations
from datetime import datetime

def sunup(lat, long, time, degrees):
    """! Calculates if sun is up at observer coordinates.
    @param number   Latitude of observer.
    @param number   Longitude of the observer.
    @param time     Actual time.
    @param string   Angle in degrees above horizon when sun is "up".
    @return         True when sun is up, false when sun is under.
    """
    o = ephem.Observer()
    o.long = long
    o.lat = lat
    o.date = time
    s = ephem.Sun()
    s.compute(o)
    return s.alt > ephem.degrees(degrees)

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

def capture_and_save_image(config):
    """! capture save and upload image.
    @param json     Object with configuration parameters.
    """
    try:
        latitude = config.get('latitude', 0.0)
        longitude = config.get('longitude', 0.0)

        # Check if it is daytime before capturing an image
        if not sunup(str(latitude), str(longitude), datetime.now(), '-3'):
            print("It is not daytime. Skipping image capture.")
            return

        # Create a PiCamera object
        with picamera.PiCamera() as camera:
            # Wait for the camera to warm up
            time.sleep(2)

            # Generate the filename based on current date and time
            current_datetime = time.strftime("%Y%m%d_%H%M%S")
            filename = f"{current_datetime}.jpg"

            # Combine the output path and filename
            output_path_with_filename = os.path.join(config.get('output_path', 'images'), filename)

            # When required the brightness can be set manualy before capture is executed.
            #camera.brightness = 55

            # Capture an image and save it to the specified output path
            camera.capture(output_path_with_filename)

            # Generate date time overlay for image
            overlay_datetime = time.strftime("%d-%m-%Y %H:%M")

            # Add date and time to the image
            add_text_to_image(output_path_with_filename, overlay_datetime, config)

            print(f"Image captured and saved to {output_path_with_filename}")

            # Check if upload_url and source_identifier are provided
            if config.get('upload_url') and config.get('source_identifier'):
                # Prepare headers with sourceIdentifier
                headers = {'sourceIdentifier': config['source_identifier']}

                # Prepare files for the POST request
                files = {'image': open(output_path_with_filename, 'rb')}

                # Perform HTTP POST request
                response = requests.post(config['upload_url'], files=files, headers=headers)

                # Print the response from the server
                print("Upload response:", response.text)

    except Exception as e:
        print(f"Error: {e}")

def add_text_to_image(image_path, text, config):
    """! Add text to image.
    @param string   image and path.
    @param string   Text to add to image.
    @param json     object with configuration parameters.
    """
    try:
        # Open the image using Pillow
        image = Image.open(image_path)

        # Create a drawing object
        draw = ImageDraw.Draw(image)

        # Set font size, position, and text color from the config file
        font_size = config.get('font_size', 12)
        font_path = config.get('font_path', "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf")
        font = ImageFont.truetype(font_path, font_size)

        # Set position and text color from the config file
        position = tuple(config.get('text_position', (10, 10)))  # Convert to tuple
        text_color = tuple(config.get('text_color', (255, 255, 255)))  # Convert to tuple

        # Add text to the image
        draw.text(position, text, font=font, fill=text_color)

        # Save the modified image
        image.save(image_path)

    except Exception as e:
        print(f"Error adding text to image: {e}")

if __name__ == "__main__":
    # Set up command-line argument parser
    parser = argparse.ArgumentParser(description="Capture an image on a Raspberry Pi, save it to disk, and optionally upload it.")
    parser.add_argument("--config_file", help="Path to the JSON configuration file")
    args = parser.parse_args()

    # Load configuration from the JSON file
    if args.config_file:
        config = load_configuration(args.config_file)
        # Call the function to capture and save the image
        capture_and_save_image(config)
    else:
        print("Error: Please provide a configuration file using the --config_file argument.")
