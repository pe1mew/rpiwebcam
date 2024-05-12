<?php
// Define the valid credentials
$valid_username = 'username';
$valid_password = 'password';

// Check if the user has entered a username and password
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    ($_SERVER['PHP_AUTH_USER'] != $valid_username) || ($_SERVER['PHP_AUTH_PW'] != $valid_password)) {
    // If not, request authentication
    header('WWW-Authenticate: Basic realm="Restricted Area"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authorization Required';
    exit;
}
?>
