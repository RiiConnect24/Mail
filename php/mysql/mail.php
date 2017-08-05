<?php
//MySQL code for $db
// However, since this is also a mail-only PHP lib, I deemed it appropiate to put the time here
$interval = 5; // If you change this, you can change from 5 min. Min is 1min.

global $db;

function connectMySQL(){
global $db;

if(!$db) $db = new mysqli('127.0.0.1', 'USERNAME', 'PASS', 'USERNAME');
return $db;
}
?>
