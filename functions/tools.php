<?php

if (!defined('ABSPATH')) {
    die("ABSPATH undefined. Should not happen");
}

require_once(ABSPATH . "vendor/phpass/PasswordHash.php");
require_once(ABSPATH . "functions/exceptions.php");
require_once(ABSPATH . "functions/displayError.php");

function getFromPost($name, $defaultValue = "") {
    if (isset($_POST) && isset($_POST[$name])) {
        return $_POST[$name];
    }

    return $defaultValue;
}

function getFromGet($name, $defaultValue = "") {
    if (isset($_GET) && isset($_GET[$name])) {
        return $_GET[$name];
    }

    return $defaultValue;
}

function getFromSession($name, $defaultValue = "") {
    if (isset($_SESSION) && isset($_SESSION[$name])) {
        return $_SESSION[$name];
    }

    return $defaultValue;
}

function pwdHash($password, &$hash)
{
     if (MAXLEN_PASSWORD < strlen($password)) {
            /* TODO : handle error properly */
            return false;
    }

    // Base-2 logarithm of the iteration count used for password stretching
    $hash_cost_log2 = 8;
    // Do we require the hashes to be portable to older systems (less secure)?
    $hash_portable  = FALSE;

    /* Create Hasher instance and hash current password */
    $hasher         = new PasswordHash($hash_cost_log2, $hash_portable);
    $hashPwd        = $hasher->HashPassword($password);
    if (strlen($hashPwd) < 20) {
        return false;
    }

    $hash = $hashPwd;

    return true;
}


function pwdHashCheck($password, $hashPwd)
{
     if (MAXLEN_PASSWORD < strlen($password)) {
            /* TODO : handle error properly */
            return false;
    }
    
    // Base-2 logarithm of the iteration count used for password stretching
    $hash_cost_log2 = 8;
    // Do we require the hashes to be portable to older systems (less secure)?
    $hash_portable  = FALSE;

    /* Create Hasher instance and hash current password */
    $hasher = new PasswordHash($hash_cost_log2, $hash_portable);

    $verify = $hasher->CheckPassword($password, $hashPwd);

    return $verify;
}


function utcTimeToLocal($timezone, $datetime) {
    $date = DateTime::createFromFormat("Y-m-d H:i:s", $datetime, new DateTimeZone('UTC'));
    $date->setTimeZone(new DateTimeZone($timezone));

    return $date->format('Y-m-d H:i:s');
}

function dateUtcToUserTime($datetime) {
    /* Retrieve timezone from session and return the input date if none found */
    $timezone = getFromSession("timezone");
    if ("" == $timezone) {
        return $datetime;
    }

    return utcTimeToLocal($timezone, $datetime);
}

/* Redirect to the login page if no session for the current user is detected */
function redirectIfNoSession() {
    $userId = getFromSession('uid', "");
    if ("" == $userId) {
        header("Location: " . URL_ROOT . "login.php");
        exit(0);
    }
}

function initMixpanel() {
    ?>
<script type='text/javascript'>
    (function(c,a){window.mixpanel=a;var b,d,h,e;b=c.createElement('script');
    b.type='text/javascript';b.async=!0;b.src=('https:'===c.location.protocol?'https:':'http:')+
    '//cdn.mxpnl.com/libs/mixpanel-2.2.min.js';d=c.getElementsByTagName('script')[0];
    d.parentNode.insertBefore(b,d);a._i=[];a.init=function(b,c,f){function d(a,b){
    var c=b.split(".");2==c.length&&(a=a[c[0]],b=c[1]);a[b]=function(){a.push([b].concat(
    Array.prototype.slice.call(arguments,0)))}}var g=a;'undefined'!==typeof f?g=a[f]=[]:
    f='mixpanel';g.people=g.people||[];h=['disable','track','track_pageview','track_links',
    'track_forms','register','register_once','unregister','identify','alias','name_tag',
    'set_config','people.set','people.increment'];for(e=0;e<h.length;e++)d(g,h[e]);
    a._i.push([b,c,f])};a.__SV=1.2;})(document,window.mixpanel||[]);

    mixpanel.init("<?php echo METRICS_MIXPANEL_TOKEN; ?>");
</script>
<?php
}

function logInternalError($message) {
    $backtrace = debug_backtrace();
    $backtraceMsg = " Callstack :";
    foreach($backtrace as $curFunc) {
        $backtraceMsg .= "\n    * " . basename($curFunc['file']) . " - " . $curFunc['function'] . " - " . $curFunc['line'] . "";
    }
    $errorMessage = date('Y-m-d H:i:s') . "\n$backtraceMsg\n Message: $message\n\n";
    error_log($errorMessage, 3, ERROR_LOGFILE);
}

?>