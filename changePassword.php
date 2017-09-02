<?php
require_once "Includes/db.php";
require_once "config/config.php";

/** Start session */
session_start();
if( !array_key_exists( "user", $_SESSION ) ) {
    header( 'Location: index.php' );
    exit;
}

/** Retrieve the ID of the rower who is trying to change his password. */
$rower_id = RowerDB::getInstance()->get_rower_id_by_email( $_SESSION['user'] );

/** Checks that the Request method is POST, which means that the data
 * was submitted from the form for entering the log data on the editLog.php
 * page itself. */
if( $_SERVER['REQUEST_METHOD'] == "POST" ) {
    /** Checks whether the $_POST array contains an element with the "back" key. */
    if( array_key_exists( "back", $_POST ) ) {
        /** The Back to the List key was pressed.
         * Code redirects the user to the editLogList.php */
        header( 'Location: editLogList.php' );
        exit;
    }
    /** The "log" key in the $_POST array is NOT empty, so a description is entered.
     * Adds the log description and the date rowed to the database via RowerDB.insert_log.
     */
    else if( $_POST['cur_password'] != "" && $_POST['new_password'] != "" ) {
        RowerDB::getInstance()->change_password( $_POST['rower_id'], $_POST['cur_password'], $_POST['new_password'] );
        header('Location: editLogList.php');
        exit;
    }
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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
            <span class="w3-bar-item w3-left"><?php echo $conf['settings']['app.title'] ?> - Change Password</span>
            <a class="w3-bar-item w3-button w3-right" href="editLogList.php"><i class="fa fa-tachometer" aria-hidden="true"></i></a>
        </div>

        <!-- !PAGE CONTENT! -->
        <div class="w3-main" style="margin-top:43px;">
            <body>
                <form name="changePassword" action="changePassword.php" method="POST">
                    <input type="hidden" name="rower_id" value="<?php echo $rower_id; ?>" />
                    <table>
                        <tr>
                            <td>
                                <label>Current Password:</label>
                            </td>
                            <td>
                                <input type="password" name="cur_password"/><br/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>New Password:</label>
                            </td>
                            <td>
                                <input type="password" name="new_password"/>
                            </td>
                        </tr>
                    </table>

                    <br/>
                    <br/>
                    <input type="submit" name="changePassword" value="Change Password"/>
                </form>
            </body>
        </div>
    </body>
</html>