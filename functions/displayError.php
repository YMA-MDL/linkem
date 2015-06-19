<?php

function displayError($error, $isIframe = false) {
    if (true == $isIframe) {
        $link = '<a href="#" onclick="parent.$.colorbox.close(); return false;">close this window now.</a>';
    } else {
        $link = '<a href="' . URL_ROOT . '">Go back to mysimplegrid</a>';
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script src="scripts/IE/testIE.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="styles/internalError.css" media="screen" />

        <script src="scripts/jquery/jquery.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="scripts/jquery_plugins/jquery.center.min.js"></script>

        <title>mysimplegrid - Internal error</title>

        <LINK REL="SHORTCUT ICON" HREF="images/favicon.ico" type="image/x-icon">
    </head>
    <body>
        <div class="error shadow">
            <div class="FormTitle shadow">
                <div id="FormLogo">
                    <img src="images/mysimplegrid_text_logo_450_80.png" ></img>
                </div>
            </div>
            <div class="errormessage">
                <p><?php echo $error; ?></p>
                <p><?php echo $link; ?></p>
            </div>
        </div>
    </body>
    <script>
        $(document).ready(function() {
            if (parent.$.colorbox) {
                parent.$.colorbox.resize({width: 500, height: 350});
            }
            jQuery('.error').center();
        });
  </script>
</html>
<?php
}
?>