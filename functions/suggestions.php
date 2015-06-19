<?php

if (!defined('ABSPATH')) {
    die("ABSPATH undefined. Should not happen");
}

require_once(ABSPATH . "functions/tools.php");
require_once(ABSPATH . "functions/emails.php");
require_once(ABSPATH . "functions/users.php");

function handleSuggestion($userId, $title, $content, $url) {
    try {
        $db = dbConnect();

        $createdOn = date("Y-m-d H:i:s");

        $sql = "INSERT INTO suggestions(userId, title, content, url, createdOn) VALUES (?,?,?,?,?)";
        sendQuery($db, $sql, 'issss', $userId, $title, $content, $url, $createdOn);

        $userName = $userId;
        $email    = "";
        if (0 != $userId) {
            $ret = userGetInfoFromId($db, $userId, $user);
            if ("" == $ret) {
                $userName = $user["displayName"];
                $email = $user["email"];
            }
        }

        $ret = emailSuggestionLink($userName, $email, $title, $content, $url);
        if ("" != $ret) {
            throw new InternalException("Error sending suggestion email ($ret)");
        }
    } catch(Exception $e) {
        return false;
    }

    return true;
}
?>