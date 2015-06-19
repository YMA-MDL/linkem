<?php

require_once(ABSPATH . "functions/emails.php");
require_once(ABSPATH . "functions/users.php");

function inviteCreate($db, $userId, $userName, $emailDst) {
    $invitedOn  = date("Y-m-d H:i:s");
    $code       = substr(md5(uniqid(time(), false)), 16, 32); /* generate 13 characters unique code */
    $used       = 0; /* Code not used yet */

    $sql = "INSERT INTO invites(userId, email, code, used, invitedOn) VALUES (?,?,?,?,?)";
    sendQuery($db, $sql,'issis', $userId, $emailDst, $code, $used, $invitedOn);

    /* TODO : handle the case where code already in db => regenerate  error code 1062 */

    /* User has created an invite, we must update it */
    $ret = usersUseInvite($db, $userId);
    if ("" != $ret) {
        return $ret;
    }

    $ret = emailInvitationLink($emailDst, $userName, $code);
    if ("" != $ret) {
        throw new InternalException("Error sending invitation email ($ret)");
    }

    return "";
}

function inviteCheckCodeValidity($db, $code, &$valid) {
    /* First create a new invitation in the database */
    $sql = "SELECT id FROM invites WHERE code=? AND used=0";
    $stmt = sendQuery($db, $sql, 's', $code);

    $result = $stmt->get_result()->fetch_assoc();
    if (!$result) {
        $valid = false;
    } else {
        $valid = true;
    }

    return "";
}


function inviteUseCode($db, $code, $userId) {
    $sql = "UPDATE invites SET used=? WHERE code=?";
    sendQuery($db, $sql, 'is', $userId, $code);

    return "";
}
?>