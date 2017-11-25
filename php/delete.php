<?php
// To make stuff log

$debug = true;
header('Content-Type: text/plain;charset=utf-8');
$headers = apache_request_headers();

if(!require('config/config.php')) {
    echo ("cd=640\n");
    echo ("msg=Configuration file not found.\n");
    exit();
}
require_once 'vendor/autoload.php';
$client = (new Raven_Client($sentryurl))->install();

// Load MySQL

/* Explanation
* If we use delete properly, it lets us off with receive.php sending *every single mail in the server* to the Wii
* The Wii can just say "delete x mail" instead of deleting all.
*/

$db = connectMySQL();
$wii_id = substr($_POST['mlid'], 1);
$stmt = $db->prepare('DELETE FROM `mails` WHERE `sent` = 1 AND `recipient_id` = ? ORDER BY `timestamp` ASC LIMIT ?');
$stmt->bind_param('si', $wii_id, $_POST['delnum']);

if ($stmt->execute())
{
	echo ("cd=100\n");
	echo ("msg=Success.\n");
	echo ("deletenum=" . $_POST['delnum'] . "\n");
}
?>
