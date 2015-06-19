<!DOCTYPE html>

<!-- saved from url=(0077)chrome-extension://gihkgljdimgfffabkemicpaeljmoobil/docs/examples/signin.html -->
<html lang="en"><head profile="http://www.w3.org/2005/10/profile">
        <link rel="icon"       type="image/png"       href="images/G.png">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <?php
        session_start();
        session_destroy();
        require_once("./config/constants.php");
        require_once(ABSPATH . "functions/tools.php");
        ?> 
        <meta charset="utf-8">
        <title>Linkem</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
        <!-- Optional theme -->
        <link rel="stylesheet" href="styles/index.css">
        <style type="text/css">
            html { 

            }
            body {
                padding-top: 40px;
                padding-bottom: 40px;
                background-color: #ddd;
            }

            .form-signin {
                max-width: 400px;
                padding: 15px;
                margin: 0 auto;
                border: solid 1px black;
                border-radius: 5px;
                -webkit-box-shadow:  0px 0px 10px 3px rgba(0,0,0,0.75);
                -moz-box-shadow:  0px 0px 10px 3px rgba(0,0,0,0.75);
                box-shadow:  0px 0px 10px 3px rgba(0,0,0,0.75);
                background-color: #eee;
            }


            .form-signin input,a {
                margin-top: 15px;
            }
            .container h1{
                text-align: center;
                font-size: 60px;
                font-family: 'calibri';
                font-weight: bold;
                color:#5bc0de;
            }
            .maindiv{
                width: 100%;
            }

        </style>

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="../assets/js/html5shiv.js"></script>
        <![endif]-->


        <script src="scripts/ie/testIE.js" type="text/javascript"></script>
    </head>

    <body>
        <div class="container maindiv" role="form">
            <form action="functions/loginmgt.php" method="post">
               
                <div class="col-md-6 form-signin">
                    <h1>linkem</h1>
                    <h2>login</h2>
                    <input id="login" autofocus="autofocus"  name="login" type="login" pattern="[^ @]*@[^ @]*" class="form-control input-lg" placeholder="Login">
                    <input name="password" type="password" class="form-control input-lg" placeholder="Password">
                    <?php
                    if (isset($_GET["ReqNode"])) {
                        $_SESSION["ReqNode"] = $_GET["ReqNode"];
                    } else {
                        $_SESSION["ReqNode"] = null;
                    }

                    if (isset($_GET["fail"])) {
                        if ($_GET["fail"] == "wrongpass") {
                            echo '<input class="btn btn-lg btn-block btn-warning btn-block" type="submit" value ="Wrong Email/Pass - Try Again">';
                        }
                    } else {
                        echo '<input class="btn btn-lg btn-block btn-primary center-block " type="submit" value ="Sign in">';
                    }
                    ?>
                </div>
            </form>
        </div> <!-- /container -->

        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    </body></html>