<?php

/**
 * ***********************************************************************
 * * @file         constants.php
 * * @brief        Config file containing constants used everywhere.
 * *
 * * This configuration file contains major constants used everywhere. It is also responsible
 *  for including the installation's specific constants.
 * *
 * * @author       Remi JANNEL
 * *
 * * @date         23/07/2012
 * *
 * ***********************************************************************
 * */
define('ABSPATH', dirname(__FILE__) . "/../");
define('APPNAME', "Ganister");
define("TABTYPEFOLDER", "./partials/tabTypeDetail/");
define("FILEVAULT", "./VAULT/files/");

/* Include also all the installation specific constants */
require_once(ABSPATH . "/config/constantsSpecific.php");

/* Ensure current constants are up-to-date */
//if (!defined('CONSTANTS_SPECIFICS_VERSION') || CONSTANTS_SPECIFICS_VERSION != "1.1") {
//    die("Your constants are not up-to-date");
//}

define("MAXLEN_PASSWORD", 72); // phpass does not support longer passwords

define("USER_DOCUMENTS_NBMAX", 20); // Maximum number of documents allowed per user
define("USER_TEMPLATES_NBMAX", 20); // Maximum number of templates allowed per user

/* Each date should be stored/computed by PHP in UTC by default */
date_default_timezone_set('UTC');



/* List of shared state available for a document */
define("DOC_SHARE_PRIVATE", "private");
define("DOC_SHARE_SHARED", "shared");
define("DOC_SHARE_UNLISTED", "unlisted");
define("DOC_SHARE_PUBLIC", "public");

/* Type of version available for a document */
define("VERSION_TYPE_DRAFT", "draft");
define("VERSION_TYPE_RELEASED", "release");


/* Internal file where errors are logged */
define("ERROR_LOGFILE", ABSPATH . "logs/errors.txt");

/* urlKey for document in demo mode */
define("DOC_URLKEY_DEMO", "demo");



/* Different plans available for a user */
define("USER_PLAN_ADMIN", "admin");
define("USER_PLAN_FREE", "free");
define("USER_PLAN_PREMIUM", "premium");
?>