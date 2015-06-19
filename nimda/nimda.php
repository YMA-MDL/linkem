<?PHP
session_start();


if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
    header("Location: ./../login.php");
}
require_once("./../config/constants.php");
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
        <!-- Optional theme -->
        <link rel="stylesheet" href="styles/index.css">

    </head>
    <body>
        <?php
        include 'modules/navbar.php'
        ?>
       
    </body>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <!-- notify.js -->
    <script src="./../vendor/notify/notify.min.js"></script>     
    <!-- utils.js -->
    <script src="./../scripts/utils.js"></script>

</html>

<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// manage nodeTypes templates
/*
 *  --> nodetypes
 *  --> nodetypes properties
 *  --> nodetypes picture
 */

// manage relationship templates
/*
 * 
 */

// manage users
/*
 *  --> create Users
 * 
 */