<?php

if (!defined('ABSPATH')) {
    die("ABSPATH undefined. Should not happen");
}

require_once(ABSPATH . "functions/tools.php");

class InternalException extends Exception
{
   public function __construct($errorMessage)
   {
      parent::__construct("An internal error has occurred");
      logInternalError("Internal error : " . $errorMessage);
   }
}

class MysqlException extends Exception
{
   public function __construct($errorMessage, $errorCode, $query = "")
   {
      parent::__construct($errorMessage, $errorCode);
      $error = "MySQL error '$errorCode' :  $errorMessage";
      if ("" != $query) {
          $error .= " - Query : $query";
      }
      logInternalError($error);
   }
}

class BasexException extends Exception
{
   public function __construct($errorMessage, $query = "")
   {
      parent::__construct($errorMessage);
      $error = "BaseX error :  $errorMessage";
      if ("" != $query) {
          $error.="\nQuery: $query";
      }
      logInternalError($error);
   }
}
?>
