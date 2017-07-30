<?php
// To make stuff log
$debug = true;

header('Content-Type: text/plain;charset=utf-8');
$headers = apache_request_headers();

require 'mysql.php'; //Load MySQL
$db = connectMySQL();

$wii_id = substr($_POST['mlid'], 1);


$stmt = $db->prepare('DELETE FROM `mails` WHERE `sent` = 1 AND `recipient_id` = ? ORDER BY `timestamp` ASC LIMIT ?');
$stmt->bind_param('si', $wii_id, $_POST['delnum']);

if($stmt->execute()){
echo("cd=100\n");
echo("msg=Success.\n");
echo("deletenum=" . $_POST['delnum'] . "\n");
echo("interval=10");
}

/* Explanation
* If we use delete properly, it lets us off with receive.php sending *every single mail in the server* to the Wii
* The Wii can just say "delete x mail" instead of deleting all.
*/
