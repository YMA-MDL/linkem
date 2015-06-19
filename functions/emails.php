<?php

if (!defined('ABSPATH')) {
    die("ABSPATH undefined. Should not happen");
}

require_once(ABSPATH . "PHPClass/Encryption.php");
require_once(ABSPATH . "vendor/phpmailer/class.phpmailer.php");

function sendEmail($emailDst, $subject, $body) {
    // Passing 'true' enables exceptions.  This is optional and defaults to false.
    $mailer     = new PHPMailer(true);

    // Set up to, from, and the message body.  The body doesn't have to be HTML; check the PHPMailer documentation for details.
    $mailer->Sender     = EMAIL_FROM;
    $mailer->AddReplyTo(EMAIL_FROM, EMAIL_REALNAME);
    $mailer->SetFrom(EMAIL_FROM, EMAIL_REALNAME);
    $mailer->AddAddress($emailDst);
    $mailer->Subject    = $subject;
    //$mailer->MsgHTML($body);
    $mailer->IsHTML(false);
    $mailer->Body       = $body;

    // Set up our connection information.
    $mailer->IsSMTP();
    $mailer->SMTPAuth   = true;
    $mailer->SMTPSecure = 'ssl';
    $mailer->Port       = 465;
    $mailer->Host       = "smtp.mandrillapp.com";
    $mailer->Username   = MANDRILL_USERNAME;
    $mailer->Password   = MANDRILL_SMTPKEY;

    // TODO : enable only in dev : $mailer->SMTPDebug = 1;

    // TODO : catch mailer error ?
    // All done!
    $mailer->Send();
            echo "<log>3</log>";

    return "";
}



function sendEmailTemplateInvitation($emailDst, $url) {
    $target = 'https://mandrillapp.com/api/1.0/messages/send-template.json';
    $args = array("key" => MANDRILL_SMTPKEY,
                  "template_name" => "Beta Invite",
                  "template_content" => array(),
                  "message" => array(
                        "subject" => "mysimplegrid's private beta",
                        "from_email" => EMAIL_FROM,
                        "from_name" => EMAIL_REALNAME,
                        "to" => array(array("email" => $emailDst, "name" => $emailDst)),
                        "global_merge_vars" => array(array("name" => "PRIVATEINVITATIONURL", "content" => $url))
                  ));

    $payload = json_encode($args);

    $ch = curl_init($target);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload))
    );

    $result = curl_exec($ch);
    $resultDecode = json_decode($result);

    if ("sent" != $resultDecode[0]->status) {
        $resultPrint = print_r($resultDecode, true);
        throw new InternalException("Error sending mail through mandrill - Return value : " . $resultPrint);
    }

    return "";
}

function emailConfirmationLink($emailDst, $uniqueId) {
    $decoder    = new Encryption();
    $encrypted  = $decoder->encode($emailDst . "<>" . $uniqueId);

    $appPath    = URL_ROOT . "RegConfirmation.php?keyconf=";
    $url        = $appPath . $encrypted;
    $subject    = "mysimplegrid Registration Confirmation";

    $body       = "Welcome to mysimplegrid,\n\nThank you for registering to our service.\nPlease follow this link to confirm your registration :\n";
    $body      .= $url;
    $body      .= "\n\nIf you want to come back to the website after registering you can use the following link:\n".URL_ROOT."\n\n";
    $body      .= "Sincerely,\n\nmysimplegrid's Team";

    $ret = sendEmail($emailDst, $subject, $body);

    return $ret;
}


function emailInvitationLink($emailDst, $userName, $uniqueId) {
    $url = URL_ROOT . "Register.php?inviteref=$uniqueId";

    $ret = sendEmailTemplateInvitation($emailDst, $url);

    return $ret;
}


function emailSuggestionLink($userName, $email, $title, $content, $url) {
    $subject    = "New Suggestion from a user";
    $body       = "The user $userName ($email) sent a new suggestion.\n\n";
    $body      .= "Origin url : $url\n\n";
    $body      .= "Title : $title\n\n";
    $body      .= "Content : $content";

    $ret = sendEmail(EMAIL_SUGGESTIONS, $subject, $body);

    return $ret;
}


function emailUserInviteNotification($meetingName, $userName, $email, $title, $content, $url) {
    $AppName = "meetingnotes.io";
    $subject    = "You received an invitation to a meeting on $AppName";
    $body       = "You havee been invited to join a meeting on $AppName .\n\n";
    $body      .= "Use the following url to join : $url\n\n";

    $ret = sendEmail($email, $subject, $body);

    return $ret;
}
function emailUserCreationInviteNotification($meetingName, $userName, $email, $title, $content, $url,$password) {
    $AppName = "meetingnotes.io";
    $subject    = $title;
    $body       = "Welcome to ".$AppName." you have been invited .\n\n";
    $body      .= "Here is your password : ".$password." \n";
    $body      .= "Use this email address as your login. \n";
    $body      .= "You'll be able to change this from your account page. \n";
    $body      .= "Use the following url to access the meeting : $url\n\n";

    $ret = sendEmail($email, $subject, $body);

    return $ret;
}

function emailResetPasswordLink($emailDst,$userName, $resetCode) {
    $url        = URL_ROOT . "resetPassword.php?email=$emailDst&code=$resetCode";
    $subject    = "Forgot your meetingnotes password";
    $body       = "Hi $userName,\n\n";
    $body      .= "here is your password : $resetCode \n\n";
//    $body      .= "To reset your password click on the link below (or copy and paste the URL into your browser):\n";
//    $body      .= "$url\n\n";
    $body      .= "Sincerely,\n\nmeetingnotes' Team";

    $ret = sendEmail($emailDst, $subject, $body);

    return $ret;
}

?>