<?php

if (!defined('ABSPATH')) {
    die("ABSPATH undefined. Should not happen");
}

/* Check if the action can be performed by current user or not based on its user identifier,
   the document identifier if any (some actions just need the user to be logged) and the permissions
   linked to the current document.
*/
function userCanPerformAction($db, $action, $userId, $basexKey) {
    $actionHandled  = false;
    $error          = "";

    /* First check if user tries to perform an action linked to the datastore which only require user to be logged. */
    switch($action) {
        case 'GetCommunityTemplates':
        case 'GetMyTemplates':
        case 'SaveATemplate':
        case 'createDoc':
        case 'Vote':
        case 'deleteDocuments':
        case 'deleteTemplates':
        case 'GetDocumentList':
            if ("" == $userId) {
                return "You should be logged to perform this action ($action)";
            }

            $actionHandled = true;
            break;
    }

    /* If global action requiring only to be logged we leave */
    if (true == $actionHandled) {
        return "";
    }
    
    /* All the other operations need a document key so we check its validity */
    $docInfo  = null;
    if("" == $basexKey) {
        return "This action requires a document key ($action)";
    }

    /* Retrieve permissions linked to current document.
       For demo mode remaining operations are always authorized */
    if (DOC_URLKEY_DEMO == $basexKey) {
        $permissions = DOC_PERMISSIONS_FULL;
    } else {
        documentGetInfoFromKey($db, 'basex', $basexKey, $docInfo);
        if (null == $docInfo) {
            return "The document '$basexKey' does not exist. This should not happen.";
        }

        /* If current user is the owner of the document he can do anything so we leave */
        if ($userId == $docInfo['owner']) {
            return "";
        }

        $permissions = getUserPermissionOnDoc($db, $userId, $docInfo);
    }

    /* Decide which permissions current user has on the document */
    switch($action) {
        /* These operations require to have full access to the document */
        case 'add_an_itemtype':
        case 'add_an_itemtype_root':
        case 'add_a_property':
        case 'UpdateItemTypeName':
        case 'UpdatePropertyName':
        case 'UpdateFormat':
        case 'PropertyListElemAdd':
        case 'PropertyListElemRename':
        case 'PropertyListElemDelete':
        case 'deletePropertyDefinition':
        case 'deleteItemType':
        case 'MoveProperty':
        case 'UpdateDocTitle':
            if (DOC_PERMISSIONS_FULL != $permissions) {
                $error = "You don't have the rights to perform this action ($action)";
            }

            $actionHandled = true;
            break;

        /* These operation require to have write access to the grid */
        case 'UndoLastModification':
        case 'UpdatePropertyValue':
        case 'UpdateWidth':
        case 'UpdateHeight':
        case 'DeleteItemInstance':
        case 'NewItemInstance':
        case 'MoveItemInstance':
            if (DOC_PERMISSIONS_FULL != $permissions && DOC_PERMISSIONS_WRITEGRID != $permissions) {
                $error = "You don't have the rights to perform this action ($action)";
            }

            $actionHandled = true;
            break;

        /* These operations require the user to have read access to the document */
        case 'GetDocumentDatamodel':
        case 'GetDBContent':
        case 'VersionGetList':
            if (DOC_PERMISSIONS_NONE == $permissions) {
                $error = "You don't have the rights to perform this action ($action)";
            }

            $actionHandled = true;
            break;

        /* These operations can be done by anyone as it is linked to the demo */
        case 'createDocDemo':
            $actionHandled  = true;
            break;

        /* These operations require the user to be the owner so they can not happen
           especially in demo mode */
        case 'ChangeShareState':
        case 'DocChangeFav':
        case "VersionSave":
        case "VersionDelete":
        case "VersionRestore":
        case "VersionCreateFrom":
            $error          = "You don't have the rights to perform this action ($action)";
            $actionHandled  = true;
            break;
    }

    if (false == $actionHandled) {
        $error = "The action '$action' is not supported.";
    }

    return $error;
}

/* Retrieve the write permissions of a user (who is not the owner) on a specific document
   This is, for the moment, based on the column "permissions" in documents table.
   When we implement the ability to share a document to a specific user with specific permissions
   we will also have to rely on it
*/
function getUserPermissionOnDoc($db, $userId, $docInfo) {
    $permissions = DOC_PERMISSIONS_NONE;

    /* No access to the document if it is private as user is not the owner */
    if (DOC_SHARE_PRIVATE == $docInfo['shareState']) {
       $permissions =  DOC_PERMISSIONS_NONE;
    } else {
        /* For an unlisted or public document, everyone has the same default rights as set in database */
        if(DOC_SHARE_UNLISTED == $docInfo['shareState'] || DOC_SHARE_PUBLIC == $docInfo['shareState']) {
            $permissions = $docInfo['permissions'];
        }

        /* If user is logged we need to check if he has specific rights */
        if ("" == $userId) {
            /* TODO : when we add the ability to share the document with specific people, we will need to check
               if current user has specific permissions to the document */
        }
    }

    return $permissions;
}
?>