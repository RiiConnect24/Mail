<?php

if (!file_exists('config/config.php'))
{
	echo ("cd=630\n");
	echo ("msg=Configuration file not found.\n");
	exit();
}

include "config/config.php";

 // Load MySQL.

$db = connectMySQL();
$headers = getallheaders(); // Only works on apache2
$mlid = substr($_POST['mlid'], 1); //mlid = Mail ID.
$stmt = $db->prepare('SELECT * FROM `mails` WHERE `recipient_id` = ? AND `sent` = 0 ORDER BY `timestamp` ASC');

if (!$stmt):
	error_log($db->error);
	die($db->error);
endif;
$stmt->bind_param('s', $mlid);

if (!$stmt->execute())
{
	error_log('failed to execute $stmt');
	echo ("\ncd=230");
	echo ("\nmsg=Failed to execute $stmt.");
	die('failed to execute $stmt');
}

$mails = $stmt->get_result(); // array of results... with a lonely mail :(
$mails2 = $mails->fetch_all(MYSQLI_ASSOC);
$mailnum = 0;
$mailsize = 0;
$mailoutput = "";

$wc24mimebounary = "BoundaryForDL" . date("YmdHi") . "/" . rand(1000000, 9999999);

for ($i = 0; $i < count($mails2); $i++)
{
  if ($mailsize > (int) $_POST['maxsize'])
  {
    break;
  }

  $output = $mails2[$i]["mail"];
  $mailnum += 1;
  $mailsize += strlen($output);

  $mailoutput .= "\r\n--" . $wc24mimebounary . "\r\n";
	$mailoutput .= "Content-Type: text/plain\r\n\r\n";
	$mailoutput .= $output;

  error_log($mailoutput);

	/* Update the mail's row to set it as sent
	* The reason we don't just delete it is because we have delete.php
	*/
	$stmt = $db->prepare('UPDATE `mails` SET `sent` = 1 WHERE `mail_id` = ?');
	$stmt->bind_param('s', $mails2[$i]['mail_id']);
	if (!$stmt->execute()) error_log('Warning: Failed to mark mail as sent');
}

header("Content-Type: multipart/mixed; boundary=" . $wc24mimebounary);
echo "--" . $wc24mimebounary . "\r\n";
echo "Content-Type: text/plain\r\n\r\n";
echo "This part is ignored.\r\n\r\n\r\n\n";
echo "cd=100\n";
echo "msg=Success.\n";
echo "mailnum=" . $mailnum . "\n";
echo "mailsize=" . $mailsize . "\n";
echo "allnum=" . $mailnum . "\n";

echo $mailoutput;

echo "\r\n--" . $wc24mimebounary . "--\r\n";
?>
