<?php

session_start();

require_once("../config/constants.php");
require_once("../config/constantsSpecific.php");

define("NL", "\n");
header("Content-type: text/xml");

require_once(ABSPATH . "functions/tools.php");

/* Prepare XML response */
echo "<?xml version='1.0' encoding='UTF-8' ?>" . NL;
echo "<result>";

/* Ensure that a session is started except when adding a new user */
$action = getFromPost("action", "");
//if ("add_a_user" != $action) {
//    $userId = getFromSession("uid");
//    if ("" == $userId) {
//        echo "<error>you should be logged to do this operation</error>";
//        echo "</result>";
//        exit(0);
//    }
//}
include '../vendor/autoload.php';

try {
    $cypher = new EndyJasmi\Cypher(NEO4J_URL_ROOT);
$userId = getFromSession("uid");
    switch ($action) {
        case "switchTheme":
            $_SESSION['bootstrapTheme'] = getFromPost("theme", "native");
            // update user preference
             // launch the query
            $result = $cypher->statement(
                            'START user=node(' . $userId . ') '
                            . 'SET  user.preferedTheme="'.getFromPost("theme", "native").'" '
                    )->execute();
            $data = array();
            echo $_SESSION['bootstrapTheme'];
            break;
        case "sendEmail":
            $userName = "michel";
            $emailType = getFromPost("emailtype");
            $email = getFromPost("useremail", "");
            $meetingName = getFromPost("mID", "");
            switch ($emailType) {
                case "SimpleInvite":
                    $title = "[Meeting] Meeting Invitation";
                    $content = "you are invited";
                    $url = "http://127.0.0.1:8080/meetingnotes/login.php";
                    echo $meetingName . NL;
                    echo $userName . NL;
                    echo $email . NL;
                    echo $title . NL;
                    echo $content . NL;
                    echo $url . NL;
                    emailUserInviteNotification($meetingName, $userName, $email, $title, $content, $url);
                    break;
                case "UserCreationAndInvite":
                    $title = "[New Account] User Creation and Meeting Invitation";
                    $password = getFromPost("userp", "");
                    $content = "you are invited";
                    $url = "http://127.0.0.1:8080/meetingnotes/login.php";
                    echo $meetingName . NL;
                    echo $userName . NL;
                    echo $email . NL;
                    echo $title . NL;
                    echo $content . NL;
                    echo $url . NL;
                    emailUserCreationInviteNotification($meetingName, $userName, $email, $title, $content, $url, $password);
                    break;
                default:
                    echo "<error>This email type is not supported</error>";
                    break;
            }
            break;
        default:
            echo "<error>This action is not supported</error>";
            break;
    }

    /* End XML response properly */
    echo "</result>";
} catch (Exception $e) {
    echo "<dbError>Internal error, please try again later. $e </dbError></result>";
}
?>