<?php

mb_internal_encoding('utf-8');
function generate_UUID() { /* jsa was here */
        return str_replace(
                array('+','/','='),
                array('-','_',''),
                base64_encode(file_get_contents('/dev/urandom', 0, null, 0, 8))
        );
    }

$envelope = json_decode($_POST['envelope'], true);
$id = substr($envelope["to"][0], 1, -9);
$uuid = generate_UUID();

$envelope = json_decode($_POST['envelope'], true);
$envelope = array(
    'to' => $envelope['to'][0],
    'from' => $envelope['from']
);
$body = array([
    'charset'	=>	'utf-8',
    'subject'	=> $_POST['subject'],
    'type' => TYPETEXT,
    'subtype' => 'plain',
    'description' => 'wiimail',
    'contents.data' => $_POST['text']
]);
$mail = imap_mail_compose($envelope, $body);

include "php/mysql/mail.php"; // MySQL, remember!

//DEBUG
$db = connectMySQL();
$stmt = $db->prepare("INSERT INTO `mails` (`sender_wiiID`, `recipient_id`, `mail_id`, `message_id`, `mail`) VALUES (?, ?, ?, ?, ?)");
if(!$stmt) error_log($db->error);
$stmt->bind_param('sssss', $envelope['from'], $id, $uuid, $uuid, $mail);
if(!$stmt) error_log($db->error);
            if($stmt->execute()){
                $success = 1;
            }else{
                error_log('DATABASE ERROR ON sendgrid/parse.php - '.$stmt->error);
                http_response_code(250);
                exit;
           }

?>
