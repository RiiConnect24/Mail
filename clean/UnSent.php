<?php
include "../php/config/config.php"; //Load MySQL
$db = connectMySQL();

$stmt = $db->query('UPDATE `mails` SET `sent` = 0 WHERE `sent` != 0');
if(!$stmt) {
    error_log('Failed to make mails all not sent');
}
?>
