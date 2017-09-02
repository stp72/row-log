<?php
require_once "Includes/db.php";
require_once "config/config.php";

// verify user's credentials
if( $_SERVER['REQUEST_METHOD'] == "POST" ) {
    RowerDB::getInstance()->create_rower( $_POST['fname'], $_POST['lname'], $_POST['user'], null, $_POST['userpassword'] );
}
?>

<!DOCTYPE html>
<html>
    <title><?php echo $conf['settings']['app.title'] ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/rowing-log.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>html,body,h1,h2,h3,h4,h5 {font-family: "Raleway", sans-serif}</style>
    <body class="w3-light-grey">
        <!-- Top container -->
        <div class="w3-bar w3-top w3-black w3-large" style="z-index:4">
            <span class="w3-bar-item w3-left"><?php echo $conf['settings']['app.title'] ?></span>
            <a class="w3-bar-item w3-button w3-dark-grey w3-right" href="index.php">Back to Standings</a>
        </div>

        <!-- !PAGE CONTENT! -->
        <div class="w3-main" style="margin-top:43px;">
            <form name="logon" action="addUser.php" method="POST" >
                <table>
                    <tr>
                        <td>Email:</td>
                        <td><input type="text" name="user"/></td>
                    </tr>
                    <tr>
                        <td>First Name:</td>
                        <td><input type="text" name="fname"/></td>
                    </tr>
                    <tr>
                        <td>Last Name:</td>
                        <td><input type="text" name="lname"/></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input type="password" name="userpassword"/></td>
                    </tr>
                </table>
                <input type="submit" value="Add User"/>
            </form>
        </div>
    </body>
</html>