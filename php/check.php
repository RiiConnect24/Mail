<?php
$noredir = true; // Stop redirection

//Include the Mail lib.
include 'mysql.php';

header('Content-Type: text/plain;charset=utf-8');
header("X-Wii-Mail-Download-Span: ".$interval);
header("X-Wii-Mail-Check-Span: ".$interval);

function generateRandomString($length = 33, $mode) {
    if ($mode == 1)
    {
	     $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }
    else if ($mode == 2)
    {
	     $characters = '0123456789abcdef';
    }
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

echo("cd=" . "100" . "\n");
echo("msg=" . "Success." . "\n");
echo("res=" . generateRandomString(40, 2) . "\n");
echo("mail.flag=" . generateRandomString(33, 1) . "\n");
echo("interval="."5"."\n");
?>
