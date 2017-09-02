<?php
require_once "Includes/db.php";
require_once "config/config.php";

session_start();
if( array_key_exists( "user", $_SESSION ) ) {
    $rower_id = RowerDB::getInstance()->get_rower_id_by_email( $_SESSION['user'] );
}
else {
    header( 'Location: index.php' );
    exit;
}
?>

<!DOCTYPE html>
<html>
    <title><?php echo $conf['settings']['app.title'] ?> - My Dashboard</title>
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
            <a class="w3-bar-item w3-button w3-right" href="index.php"><i class="fa fa-sign-out"></i></a>
            <a class="w3-bar-item w3-button w3-right" href="changePassword.php"><i class="fa fa-cog"></i></a>
        </div>

        <!-- !PAGE CONTENT! -->
        <div class="w3-main" style="margin-top:43px;">

        <!-- Header -->
        <header class="w3-container" style="padding-top:22px">
            <h5><b><i class="fa fa-dashboard"></i> My Dashboard</b></h5>
        </header>

        <div class="w3-row-padding w3-margin-bottom">
            <div class="w3-quarter">
                <div class="w3-container w3-red w3-padding-16">
                    <div class="w3-left"><i class="fa fa-thumbs-o-up w3-xxxlarge"></i></div>
                    <div class="w3-right">
                        <h3>
                            <?php
                            $rower_meters = RowerDB::getInstance()->get_rower_meters( $rower_id );
                            echo htmlentities( $rower_meters );
                            ?>
                        </h3>
                    </div>
                    <div class="w3-clear"></div>
                    <h4>My Total Meters</h4>
                </div>
            </div>
            <div class="w3-quarter">
                <div class="w3-container w3-blue w3-padding-16">
                    <div class="w3-left"><i class="fa fa-trophy w3-xxxlarge"></i></div>
                    <div class="w3-right">
                        <h3>
                            <?php
                            $season_meters = RowerDB::getInstance()->get_season_meters();
                            echo htmlentities( $season_meters );
                            ?>
                        </h3>
                    </div>
                    <div class="w3-clear"></div>
                    <h4>ONEC Total Meters</h4>
                </div>
            </div>
            <div class="w3-quarter">
                <div class="w3-container w3-teal w3-padding-16">
                    <div class="w3-left"><i class="fa fa-bar-chart w3-xxxlarge"></i></div>
                    <div class="w3-right">
                        <h3>
                            <?php
                            $avg_meters = RowerDB::getInstance()->get_avg_meters();
                            echo htmlentities( $avg_meters );
                            ?>
                        </h3>
                    </div>
                    <div class="w3-clear"></div>
                    <h4>Average Meters/Rower</h4>
                </div>
            </div>
            <div class="w3-quarter">
                <div class="w3-container w3-orange w3-text-white w3-padding-16">
                    <div class="w3-left"><i class="fa fa-users w3-xxxlarge"></i></div>
                    <div class="w3-right">
                        <h3>
                            <?php
                            $rower_count = RowerDB::getInstance()->get_rower_count();
                            echo htmlentities( $rower_count );
                            ?>
                        </h3>
                    </div>
                    <div class="w3-clear"></div>
                    <h4>Total Rowers</h4>
                </div>
            </div>
        </div>

        <div class="w3-container">
        <form name="addLog" action="editLog.php" method="POST">
            <input type="hidden" name="rower_id" value="<?php echo $rower_id; ?>"/>
            <input type="submit" name="addLog" value="Add New Entry"/>
        </form>
        </div>
        
        <div class="w3-container">
        <table class="w3-table w3-striped w3-bordered w3-border w3-hoverable w3-white">
            <thead>
                <tr>
                    <th>Date Rowed</th>
                    <th>Distance</th>
                    <th>Brief Description of Route</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rower_logs = RowerDB::getInstance()->get_logs_by_rower_id( $rower_id );
                foreach( $rower_logs as $key => $value ):
                    echo "<tr><td>" . htmlentities( $value['date_rowed'] ) . "</td>";
                    echo "<td>" . htmlentities( $value['distance'] ) . "</td>";
                    echo "<td>" . htmlentities( $value['notes'] ) . "</td>";
                    $rower_log_id = $value['rower_log_id'];
                    //echo "<td>rower_log_id=" . $rower_log_id . "</td>";
                    //echo "<td>rower_id=" . $rower_id . "</td>";
                    //The loop is left open
                ?>
                <td>
                    <form name="editLog" action="editLog.php" method="GET">
                        <input type="hidden" name="rower_log_id" value="<?php echo $rower_log_id; ?>"/>
                        <input type="submit" name="editLog" value="Edit"/>
                    </form>
                </td>
                <td>
                    <form name="deleteLog" action="deleteLog.php" method="POST">
                        <input type="hidden" name="rower_log_id" value="<?php echo $rower_log_id; ?>"/>
                        <input type="submit" name="deleteLog" value="Delete"/>
                    </form>
                </td>
                <?php
                echo "</tr>\n";
                endforeach;
                ?>
            </tbody>
        </table>
        </div>
    </body>
</html>