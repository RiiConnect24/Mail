<?php
$noredir = true; // Stop redirection
require "/var/www/riiconnect24.net/core/core.php";
/*
Copyright (c) 2015-2017 RiiConnect24, and its (Lead) Developers
Specific Authors: Joshua "thejsa" Kelly (name subject to change), Billy "PokeAcer" Humphreys, Joshua "shutterbug2000" Stokes.

This Program is licensed under the RiiConnect24 Development License, which is legally set out hereto.
This License grants Current Developers of RiiConnect24 access to this code and its previous iterations, IF
- They still develop actively for RiiConnect24.
- They are not under any sanction.
- They have their own Git access
Access to RiiConnect24's Private Git Organization, on the git subdomain of the rc24.xyz domain, is strictly limited to RiiConnect24 Developers.
Any code accessed by RiiConnect24 Developers via their Developer status is to be kept private at all times. Furthermore, if a non-Developer is authorised
by the lead developers (Billy Humphreys, Larsen Vallecillo, John Pansera - all 3 must agree to authorisation), they must conform to the legally binding license.
Statistical data (such as the times ran, points of interest, etcetera) is allowed to be shared if anonymised - if only 1 or 2 of the statistic is shown, please state "a small number" or round to 0.

By adding this to the RiiConnect24 Progrmas, all Developers agree to this license. Breaking this license by sharing code when not authorised is a copyright violation and shall be dealt with via a DMCA takedown notice.
*/

//Include the Mail lib; $interval is set here for easy access
include 'mysql/mail.php';
$checkInterval = interval($_REQUEST['mlchkid']);

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
echo("msg=" . "Success" . "\n");
echo("res=" . generateRandomString(40, 2) . "\n");
echo("mail.flag=" . generateRandomString(33, 1) . "\n");
echo("interval=".$checkInterval."\n");
?>

