<?php
include 'config.php';

mb_internal_encoding('utf-8');

function generate_UUID() {
        return str_replace(
                array('+','/','='),
                array('-','_',''),
                base64_encode(file_get_contents('/dev/urandom', 0, null, 0, 8))
        );
    }


    header('Content-Type: text/plain;charset=utf-8');

    $debug = false;

    /*if (!(substr($_SERVER['HTTP_USER_AGENT'], 0, 12) === "WiiConnect24")) {
        //exit();
    }*/

    if ($debug) {
        try {
            $logfile = fopen("logs/send.txt", "a+");
            fwrite($logfile, "--START MESSAGE--\n");
            foreach (getallheaders() as $name => $value) {
                fwrite($logfile, "$name: $value\n");
            }
            foreach ($_POST as $key => $value) {
                fwrite($logfile, $key . ": \n" . $value . "\n");
            }
            fwrite($logfile, "--END MESSAGE--\n\n");
            fclose($logfile);
        } catch (Exception $e) {
            error_log('Error logging to logs/send.txt');
        }
    }

    //WiiID =
    //echo

    // MySQL stuff
    include "mysql.php"; // Time for MySQL!
    $db = connectMySQL();

    /*
     * Bulk mail handler
     *
     * Note: $mails was previously known as $mxes but it felt like a change to
     * a more descriptive name as apparently it's sentient and likes to please
     * pedantic programmers like Lauren
     */
    $mails = array();
    foreach($_POST as $key => $value){
        if(substr($key, 0, 1) == 'm'){
            $mails[$key] = $value;
        }
    }
    foreach($mails as $mail){
        /* Get all our awesome data */
        $from = NULL;
        $to = []; /* Array of wiiIDs to send to */
        $pcTo = []; /* email recipients */
        $message_id = NULL; /* Message-Id as provided by the Wii */

        $lines_to_remove = 0; /* Lines to remove before the actual message data */

        $line = strtok($mail, "\r\n");
        while ($line !== false) {
            $lines_to_remove++;
            $matches = [];

            if(preg_match("/^RCPT TO:\s(.*)@(.*)$/", $line, $matches)) { // (allusers|w[0-9]*) matches allusers and wXXXX
                /* I'm matching allusers in the regex in case you want to handle that specially in the future */
                //error_log($line);
                if($matches[2] != 'YOURDOMAIN'){
                    //error_log('pc email detected');
                    //error_log(json_encode($matches));
                    $pcTo[] = $matches[1].'@'.$matches[2];
                }else if($matches[1] != 'allusers') {
                    $to[] = substr($matches[1], 1);
                    //error_log('wiimail detected');
                    //error_log(json_encode($matches));
                }
            }else if(preg_match("/^MAIL FROM:\s(w[0-9]*)@(?:.*)$/", $line, $matches)) {
                /* MAIL FROM line */
                $from = $matches[1];
                if($from == 'w9999999999990000') { /* sanity check; that's "Nintendo's special ID" according to PokeAcer */
                    error_log('w9999999999990000 tried to send mail, IP address: '.$_SERVER['REMOTE_ADDR']);
                    echo("cd=450\n");
                    echo("msg=w9999999999990000 tried to send mail.\n");
                    die('Byeeeeeeee script kiddie');
                }
            }else if(preg_match("/^Message-Id:\s<([0-9a-fA-F]*)@(?:.*)>$/", $line, $matches)) {
                $message_id = $matches[1];
            }else if(preg_match("/^DATA$/", $line, $matches)){
                /* DATA line, end the party (sorry, Pinkie Pie) as we don't need to cut out any more lines */
                break;
            }

            $line = strtok("\r\n");
        }

        $message = implode("\n", array_slice(explode("\n", $mail), $lines_to_remove));

        $message_id = $message_id ?: generate_UUID(); /* Just in case we don't get one from the Wii */

        foreach($to as $receipient_id) {
            $mail_id = generate_UUID(); /* for the individual mail, because you're worth it */


                $stmt = $db->prepare('INSERT INTO `mails` (`sender_wiiID`,
                `mail`,
                `recipient_id`,
                `mail_id`,
                `message_id`) VALUES (?, ?, ?, ?, ?)'); /* mail_id is the unique ID for each copy of a message (different for each recipient) should be VARCHAR(255) UNIQUE PRIMARY KEY, message_id is the unique ID for each message (provided by the Wii; will be the same for each recipient) should be VARCHAR(255) KEY */
                $stmt->bind_param('sssss', $from, $message, $receipient_id, $mail_id, $message_id);
            if($stmt->execute()){
                $success = 1;
            }else{
                error_log('DATABASE ERROR ON cgi-bin/send.cgi - '.$stmt->error);
                echo("cd=250\n");
                echo("msg=Database error.\n");
                exit;
            }
        }
        // handle external email (through SendGrid)
            //error_log('about to check if pc emails should be sent');
        if(!empty($pcTo)) {
            //error_log('pc emails will be sent');
            $smtpServer = 'smtp.sendgrid.net';
            $username = 'apikey'; // YXBpa2V5
            $password = 'SENDGRIDAPIKEY';
            $newLine = "\r\n";
            $port = 25;
            $timeout = 45;

            //connect to the host and port
            $smtpConnect = fsockopen($smtpServer, $port, $errno, $errstr, $timeout);
            $smtpResponse = fgets($smtpConnect, 4096);//error_log($smtpResponse);
            if(empty($smtpConnect)) {
                error_log('Failed to connect to SendGrid for sending: '.$smtpResponse);
                echo("cd=260\n");
                echo("msg=Failed to connect to SendGrid.\n");
                exit;
            }
            else {
                $logArray['connection'] = "<p>Connected to: $smtpResponse";
            }

            fputs($smtpConnect, "HELO\r\n");

            // say HELO
            fputs($smtpConnect, "HELO YOURSERVER". "\r\n");
            $smtpResponse = fgets($smtpConnect, 4096);//error_log($smtpResponse);
            $logArray['heloresponse'] = "$smtpResponse";

            // EHLO replaces HELO, but we don't support ESMTP

            //fputs($smtpConnect, "EHLO rc24.xyz". "\r\n");
            //$smtpResponse = fgets($smtpConnect, 4096);error_log($smtpResponse);
            //$logArray['ehloresponse2'] = "$smtpResponse";

            //request for auth login
            //error_log("Attempting auth");
            fputs($smtpConnect,"AUTH LOGIN" . "\r\n");
            $smtpResponse = fgets($smtpConnect, 4096);//error_log($smtpResponse);
            $logArray['authrequest'] = "$smtpResponse";

            //error_log("Sending username");

            //send the username
            fputs($smtpConnect, base64_encode($username) . "\r\n");
            $smtpResponse = fgets($smtpConnect, 4096);//error_log($smtpResponse);
            $logArray['authusername'] = "$smtpResponse";

            //send the password
            fputs($smtpConnect, base64_encode($password) . "\r\n");
            $smtpResponse = fgets($smtpConnect, 4096);//error_log($smtpResponse);
            $logArray['authpassword'] = "$smtpResponse";

            //error_log("==== WII INPUT DATA START ====");
            //error_log($mail);
            //error_log("==== WII INPUT DATA END ====");

            //SMTP data from the Wii
            fputs($smtpConnect, $mail . "\r\n.\r\n");
            $smtpResponse = fgets($smtpConnect, 4096);//error_log($smtpResponse);
            $logArray['wiidataresponse'] = "$smtpResponse";

            // say goodbye
            fputs($smtpConnect,"QUIT" . "\r\n");
            $smtpResponse = fgets($smtpConnect, 4096);//error_log($smtpResponse);
            $logArray['quitresponse'] = "$smtpResponse";
            $logArray['quitcode'] = substr($smtpResponse,0,3);
            fclose($smtpConnect);
            //a return value of 221 in $retVal["quitcode"] is a success
            //error_log('Output from SendGrid handler code:');
            //error_log(json_encode($logArray));
        }
    }

    echo("cd=100\n");
    echo("msg=success\n");
    echo("mlnum=".(count($mails)-1)."\n");
    for($i = 1; $i <= count($mails)-1; $i++){
        echo("cd".$i."=100\n");
        echo("msg".$i."=success\n");
    }
?>
