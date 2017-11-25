<?php
include "../php/config/config.php"; //Load MySQL
$db = connectMySQL();

if(!$db->query('DELETE FROM `mails` WHERE `sent` != 1 AND `timestamp` < NOW() - INTERVAL 28 DAY')) {
    echo("Failed to purge orphan mails\n");
    error_log("Failed to purge orphan mails\n");
    exit;
}

echo "Successfully purged orphan mails\n";

// Optimise database
if(!$db->query('OPTIMIZE TABLE `mails`')) {
    echo "Failed optimize mails table\n";
    error_log("Failed optimize mails table\n");
    exit;
}

echo "Optimized mails table successfully.\n";
