<?php
// This will change the interval any Wii connecting to your server will check. Min is 1min
// (but don't set it to 1min unless you wanna fry your Wii on standby, the fan doesn't run in that mode)
$interval = 5;

// API key for SendGrid
$password = "changeme";
// Verification domain for SendGrid
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
