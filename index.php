<?PHP
session_start();


if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
    header("Location: login.php");
}
require_once("./config/constants.php");
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
        <div class=" col-sm-8 col-md-8 col-lg-9 centralpanel">
            <?php
            include 'modules/central.php'
            ?>
        </div>
        <div class="col-sm-4 col-md-4 col-lg-3 rightpanel">
            <?php
            include 'modules/rightpanel.php'
            ?>
        </div>


    </body>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <!-- notify.js -->
    <script src="vendor/notify/notify.min.js"></script>     
    <!-- cytoscape -->
    <script src="vendor/cytoscape/cytoscape.min.js"></script>
    <!-- utils.js -->
    <script src="scripts/utils.js"></script>

    <!-- linkem_nodes.js -->
    <script src="scripts/linkem_nodes.js"></script>
    <!-- linkem_edgess.js -->
    <script src="scripts/linkem_edges.js"></script>
    <!-- linkem_nodeCollection.js -->
    <script src="scripts/linkem_nodeCollection.js"></script>
    <!-- linkem_graph.js -->
    <script src="scripts/linkem_graph.js"></script>
    <!-- UI events.js -->
    <script src="scripts/UIevents.js"></script>

</html>
