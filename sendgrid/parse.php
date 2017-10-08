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
  'from' => $envelope['from'],
  'subject' => $_POST['subject']
);
$body = array();
$body[] = [
  'type' => TYPEMULTIPART,
  'subtype' => 'mixed'
]; // mark as multipart message (even without an image it should be fine, but is required to have images)

$body[] = [
  'charset' => 'utf-8',
  'type' => TYPETEXT,
  'subtype' => 'plain',
  'description' => 'wiimail',
  'contents.data' => $_POST['text']
]; // text portion

error_log('attachment info: '.json_encode($_POST['attachment-info']));

// handle images, if they exist
$attachmentInfo = json_decode($_POST['attachment-info'], true);
error_log('attachment info encoded to JSON: '.json_encode($attachmentInfo));
foreach($attachmentInfo as $key => $info) {
  if($info['type'] != 'image/jpeg') continue; // if not a jpeg, go to next attachment
  if(!$_FILES[$key]['name'] || $_FILES[$key]['error']) continue; // if an error / file doesn't exist, go to next attachment
  $body[] = [
    'type' => TYPEIMAGE,
    'encoding' => ENCBASE64,
    'subtype' => 'jpeg',
    'description' => $info['name'],
    'disposition' => 'ATTACHMENT', // probably doesn't need to be in caps but oh well
    'contents.data' => file_get_contents($_FILES[$key]['tmp_name'])
  ];
  break; // remove to continue processing further images, but according to Billy the Wii only supports one
}

// compose
$mail = imap_mail_compose($envelope, $body);

include "../php/config/config.php"; // MySQL, remember!

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
