<?php
require_once "Includes/db.php";
require_once "config/config.php";

/** Start session */
session_start();
if( !array_key_exists( "user", $_SESSION ) ) {
    header( 'Location: index.php' );
    exit;
}
  
/** Retrieve the ID of the rower who is trying to add a log. */
$rower_id = RowerDB::getInstance()->get_rower_id_by_email( $_SESSION['user'] );
/** Initialize $logDescriptionIsEmpty. */
$logDistanceIsEmpty = false;

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
    /** Checks whether the element with the "distance" key in the $_POST array is empty,
     * which means that no distance was entered.
     */
    else if( $_POST['distance'] == "" ) {
        $logDistanceIsEmpty = true;
    }
    /** The "log" key in the $_POST array is NOT empty, so a description is entered.
     * Adds the log description and the date rowed to the database via RowerDB.insert_log.
     */
    else if( $_POST['rower_log_id'] == "" ) {
        RowerDB::getInstance()->insert_log( $_POST['rower_id'], $_POST['date_rowed'], $_POST['distance'], $_POST['notes'] );
        header( 'Location: editLogList.php' );
        exit;
    }
    else if( $_POST['rower_log_id'] != "" ) {
        RowerDB::getInstance()->update_log( $_POST['rower_log_id'], $_POST['date_rowed'], $_POST['distance'], $_POST['notes'] );
        header('Location: editLogList.php');
        exit;
    }
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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
            <span class="w3-bar-item w3-left"><?php echo $conf['settings']['app.title'] ?> - Add/Edit Log</span>
            <a class="w3-bar-item w3-button w3-dark-grey w3-right" href="editLogList.php"><i class="fa fa-tachometer" aria-hidden="true"></i> My Dashboard</a>
        </div>

        <!-- !PAGE CONTENT! -->
        <div class="w3-main" style="margin-top:43px;">
            <body>
                <?php
                  if( $_SERVER['REQUEST_METHOD'] == "POST" ) {
                      $log = array("id" => $_POST['rower_log_id'], "rower_id" => $_POST['rower_id'], "distance" => $_POST['distance'], "rowed_date" => $_POST['rowed_date'], "notes" => $_POST['notes'] );
                  }
                  else if( array_key_exists( "rower_log_id", $_GET ) ) {
                      $log = mysqli_fetch_array( RowerDB::getInstance()->get_log_by_rower_log_id( $_GET['rower_log_id'] ) );
                  }
                  else {
                      $log = array( "rower_log_id" => "", "rower_id"=>"", "distance" => "", "rowed_date" => "", "notes" => "" );
                  }
                ?>
        
                <form name="editLog" action="editLog.php" method="POST">
                    <input type="hidden" name="rower_log_id" value="<?php echo $log['rower_log_id']; ?>" />
                    <input type="hidden" name="rower_id" value="<?php echo $log['rower_id']; ?>" />
                    <table>
                        <tr>
                            <td>
                                <label>Date Rowed (yyyy-mm-dd):</label>
                            </td>
                            <td>
                                <input type="date" name="date_rowed"  value="<?php if( $log['date_rowed'] == "" ) { echo date( "Y-m-d" ); } else { echo $log['date_rowed']; } ?>" /><br/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>Distance (meters):</label>
                            </td>
                            <td>
                                <input type="number" name="distance" value="<?php echo $log['distance']; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>Brief Description of Route:</label>
                            </td>
                            <td>
                                <textarea name="notes" rows="10" cols="30"><?php echo $log['notes']; ?></textarea>
                            </td>
                        </tr>
                    </table>

                    <br/>
                    <br/>
                    <input type="submit" name="saveLog" value="Save Changes"/>
                </form>
            </body>
        </div>
    </body>
</html>