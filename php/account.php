<?php
header('Content-Type: text/plain;charset=utf-8');

function genPassword($mode, $length) {
    if ($mode == 1) {
        $characters = '0123456789abcdef';
    }
    if ($mode == 2) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }
    $string = '';
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }
    return $string;
}

$passwd = genPassword(2, 16);
$mlchkid = genPassword(1, 16);
$debug = true;

if ($debug) {
    try {
        $logfile = fopen("logs/account.txt", "a+");
        fwrite($logfile, "--START ACCOUNT--\nmlid=" . $_REQUEST['mlid'] . "\npasswd=" . $passwd . "\nmlchkid=" . $mlchkid . "\n--END ACCOUNT--\n\n");
        fclose($logfile);
    } catch (Exception $e) {

    }
}

include "mysql.php"; // Time for MySQL.
$db = connectMySQL();

        $stmt = $db->prepare('INSERT IGNORE INTO `accounts` (`mlid`,
		`mlchkid`,
		`passwd`
		) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $_REQUEST['mlid'], $mlchkid, $passwd);
        if($stmt->execute()){
                $success = 1;
        }else{
                error_log('DATABASE ERROR ON cgi-bin/account.cgi - '.$stmt->error);
				exit;
			}

echo("\n");
echo("cd=100\n");
echo("msg=success\n");
echo("mlid=" . $_REQUEST['mlid'] . "\n");
echo("passwd=" . $passwd . "\n");
echo("mlchkid=" . $mlchkid . "\n");
