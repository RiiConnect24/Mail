<?php
ini_set('max_execution_time', 120);
$noredir = true; // Stop redirection

if (!isset($_POST['mlchkid'], $_POST['chlng']))
{
	echo ("cd=320\n");
	echo ("msg=Receive data is incorrect format.\n");
	exit();
}

if(!require('config/config.php')) {
    echo ("cd=620\n");
    echo ("msg=Configuration file not found.\n");
    exit();
}
require_once 'vendor/autoload.php';
$client = (new Raven_Client($sentryurl))->install();

header('Content-Type: text/plain;charset=utf-8');
header("X-Wii-Mail-Download-Span: " . $interval);
header("X-Wii-Mail-Check-Span: " . $interval);

function generateRandomString($length = 33, $mode)
{
	if ($mode == 1)
	{
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}
	else
	if ($mode == 2)
	{
		$characters = '0123456789abcdef';
	}

	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++)
	{
		$randomString.= $characters[rand(0, $charactersLength - 1) ];
	}

	return $randomString;
}

require 'config/core.php';
$anarray = array (
    'v'	=>	1,
    'aip'	=>	1,
    'uip'	=>	get_ip(),
    'tid'	=>	$tid, //Set in Config
    'ds'	=>	'script',
    'uid'	=>	hash('sha256', $_POST['mlchkid']),
    'ec'	=>	'script',
    'ea'	=>	'chck',
);

file_get_contents("https://www.google-analytics.com/collect?").http_build_query($anarray);

$hmac_key = "ce4cf29a3d6be1c2619172b5cb298c8972d450ad";

echo ("cd=" . "100" . "\n");
echo ("msg=" . "Success." . "\n");
echo ("res=" . hash_hmac('sha1', hex2bin($_POST['chlng']), hex2bin($hmac_key)) . "\n"); // We haven't figured out how to hash this correctly, but this resembles the SHA1 HMAC thing that Nintendo used. If we figure out how to hash it properly, we won't get 102032 on games.
echo ("mail.flag=" . generateRandomString(33, 1) . "\n");
echo ("interval=" . $interval . "\n");
?>
