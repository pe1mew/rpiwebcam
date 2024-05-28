# Website for "Image capture and post" script
This directory holds 3 files that are required for the Python "Image capture and post" scipts. 

 - `api.php` presents a REST API for posting images.
 - `index.php` "home" page for watching the images. 
 - `mosaic.php` Shows a mosaic of the images of the last 24 hours. 
 - `video.php` Presents a motion capture of last 24 hour images taken. 
 - `auth.php` Deliverst password protection for the site. 
 
## Directory structure
The PHP-files can be located enywehere on a webserver that supports php. 
At the same level as the php files a directory named `images` shall be located with read and write access for the php files. 
 
## API
The API can be called to post a .jpg image. To be able to do so, the http header shall contain a entry names `sourceidentifier` that contains a secret. [Random keys](https://acte.ltd/utils/randomkeygen) can be generated following the link. 
The secret to be tested for can be configured with `$expectedSourceIdentifier = 'xXj4gkS6yB0LIwfifkAz';` in `api.php`.

When the `sourceidentifier` is present and the secret is accepted, the request method is verified to be `POST`. Then the existance of a `image` file is verified. 

When all mandatory requirements are met the uploaded file is accepted and stored in the images directory. 

After storing a new image, images older than 24 hours are removed. 

## Website 
