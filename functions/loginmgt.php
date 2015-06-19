<?php

session_start();
require_once("../config/constants.php");
require_once(ABSPATH . "config/constantsSpecific.php");

require_once(ABSPATH . "functions/tools.php");
require_once(ABSPATH . "functions/permissions.php");

$action = getFromPost("action", "");
define("NL", "\n");

$data = file_get_contents(ABSPATH . 'config/json_conf/itemtypes.json');
$json = json_decode($data, true);

$userlogin = getFromPost("login", "");
$password = getFromPost("password", "");

try {

    include ABSPATH . 'vendor/autoload.php';
    $cypher = new EndyJasmi\Cypher(NEO4J_URL_ROOT);

    $password = md5($password);
    $result = $cypher->statement(
                    "MATCH (a:User) WHERE a.login='" . $userlogin . "' AND a.password='" . $password . "' RETURN a as USER, id(a) as UID "
            )->execute();
    if (count($result[0]) > 0) {
        echo "<result login_result='success' id='" . $result[0]['id'] . "'>";
        
        $_SESSION['username'] = $result[0][0]['USER']["first_name"]." ".$result[0][0]['USER']["last_name"];
        $_SESSION['login'] = $result[0][0]['USER']["login"];
        $_SESSION['uid'] = $result[0][0]['UID'];
        $_SESSION['userimgurl'] = $result[0][0]['USER']['photo'];
        $_SESSION['bootstrapTheme']= $result[0][0]['USER']['preferedTheme'];
        header("location:../");
    } else {
       header("location:../login.php?fail=wrongpass");
    }
    echo "</result>";
} catch (Exception $e) {
    header("location:../errorHandling/errorDB.html");
    echo "<dbError><message>Internal error, please try again later. <message><detail>$e</detail></dbError>";
}
?>