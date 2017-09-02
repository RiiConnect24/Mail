<?php
// This will change the interval any Wii connecting to your server will check. Min is 1min.
$interval = 5;

// API key for Sendgrid
$password = "changeme";
// Verification domain for Sendgrid
$domain = "my.domain";


global $db;

// MySQL code for connecting to the database
function connectMySQL()
{
    global $db;

    if (!$db) {
        $db = new mysqli('127.0.0.1', 'USERNAME', 'PASS', 'DATABASE');
    }
    return $db;
}
