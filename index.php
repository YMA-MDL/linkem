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
        <link rel="icon"       type="image/png"       href="./favicon.ico">
        <title>Linkem</title>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
        <!-- Optional theme -->
        <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap-theme.min.css">
        <!-- Optional theme -->
        <link rel="stylesheet" href="styles/index.css">
        <!-- datatables style -->
        <link href="vendor/datatable/css/dataTables.bootstrap.default.css" rel="stylesheet">
        <link href="vendor/datatable/css/dataTables.bootstrap.css" rel="stylesheet">
        <link href="vendor/datatable/css/dataTables.colVis.css" rel="stylesheet">

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
        <?php
        include 'modules/modals.php'
        ?>
    </body>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="vendor/jquery/js/jquery-1.10.2.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
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
    <!-- linkem_nodeCollection.js -->
    <script src="scripts/linkem_templates.js"></script>
    <!-- linkem_graph.js -->
    <script src="scripts/linkem_graph.js"></script>
    <!-- UI events.js -->
    <script src="scripts/UIevents.js"></script>
    <!-- FilePicker Integration -->
    <script type="text/javascript" src="//api.filepicker.io/v2/filepicker.js"></script>
    <!-- datatables -->        
    <script src="vendor/datatable/js/jquery.dataTables.min.js"></script> 
    <script src="vendor/datatable/js/dataTables.bootstrap.js"></script>
    <script src="vendor/datatable/js/dataTables.fixedHeader.js"></script> 
    <script src="vendor/datatable/js/dataTables.colVis.js"></script> 

</html>
