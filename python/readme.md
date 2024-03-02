# Image capture and post
The python script `capture.py` is capturing an image using the raspicam and saving the image to a configureable directory. 
A image captured will have the date and time of the capture at a configureable location, colour and size. 
This is set in the config file. 
The script will hold the latest number of captures as configured in the config file. 
The captured image is named `YYYYMMDD_HHMMSS.jpg` and uplaoded to a REST API that is set in the configuration file. 

The configuration file is selected while calling the python script with the `--config_file` argument:
 `python capture.py --config_file config.json`

## configuration file
The configuration file is a JSON struct that holds all configurable settings: 
```json
{
    "output_path": "captures",
    "upload_url": "https://www.pe1mew.nl/test/api.php",
    "source_identifier": "xXj4gkS6yB0LIwfifkAz",
    "num_to_keep": 2,
    "text_position": [10, 10],
    "text_color": [128, 128, 128],
    "font_size": 17,
    "font_path": "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"
}
```

 - `output_path`: (path to) the directory where the captured images are stored,
 - `upload_url`: URL to the api in PHP to accept the image. Example: "https://www.pe1mew.nl/test/api.php",
 - `source_identifier`: A random string to identify the source of the image. Example: "xXj4gkS6yB0LIwfifkAz",
 - `num_to_keep`: Number of latest file to keep on disk. Examle: 2,
 - `text_position`: Position in pixels as array with x and y where the overlay text is positioned starting top-lef. Example: [10, 10],
 - `text_color`: Colour of the overlay text is positiond in R, G, and B. Example: [128, 128, 128],
 - `font_size`: Font size of the overlay text. Example: 17,
 - `font_path`: Path to the used font: "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf" (do not change) 

## crontab
The python script is executed at a regular interval using the crontab. 

To configure crontab execute `crontab -e` and add the following line for a execution at 5 minute interval: 
```bash
# every 5 minutes take a picture and upload
*/5 * * * * cd ~/python && python capture.py --config_file config.json >> capture.log 2>&1
```

## log and log rotation 
The python script produces status and progress information at execution. With the crontab executing this output is redirected to
a logfile. 

The script `log_rotation.sh` is execute dayly at 12 PM and performs teh following activities:

 1. Checks if the log file (capture.log) exists.
 2. Creates a backup of the current log file, appending the timestamp to the filename.
 3. Removes old backup files exceeding the specified maximum limit (max_backup_files).
 4. Creates a new empty log file.
 5. Prints a message indicating that log rotation was successful.

To configure crontab execute `crontab -e` and add the following line for a execution at 24 hour interval:
```bash
0 12 * * *  cd ~/python && ./log_rotation.sh >> capture.log 2>&1
```





pip install opencv-python
pip install pillow
