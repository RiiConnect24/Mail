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

/*require 'config/core.php';
$anarray = array (
    'v'	=>	1,
    'aip'	=>	1,
    'uip'	=>	get_ip(),
    't'	=>	'event',
    'tid'	=>	$tid, //Set in Config
    'ds'	=>	'script',
    'uid'	=>	hash('sha256', $wii_id),
    'ec'	=>	'script',
    'ea'	=>	'del',
    'ev'	=>	count($_POST['delnum']),
);

file_get_contents("https://www.google-analytics.com/collect?").http_build_query($anarray);*/

/* Explanation
* If we use delete properly, it lets us off with receive.php sending *every single mail in the server* to the Wii
* The Wii can just say "delete x mail" instead of deleting all.
*/
?>
