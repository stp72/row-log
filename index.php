<?php
    require_once "Includes/db.php";
    require_once "config/config.php";
    $loggedIn = false;
    if( array_key_exists( "user", $_SESSION ) ) {
        $loggedIn = true;
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
            <a class="w3-bar-item w3-button w3-right" href="signIn.php"><i class="fa fa-sign-in" aria-hidden="true"></i></a>
        </div>
        
        <!-- !PAGE CONTENT! -->
        <div class="w3-main" style="margin-top:43px;">
            <div class="w3-row-padding w3-margin-bottom">
                <div class="w3-third">
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
                <div class="w3-third">
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
                <div class="w3-third">
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
            <table class="w3-table w3-striped w3-bordered w3-border w3-hoverable w3-white">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Name</th>
                        <th>Total Distance (meters)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $i = 1;
                        $logs = RowerDB::getInstance()->get_rankings();
                        while( $row = mysqli_fetch_array( $logs ) ) {
                            echo "<tr><td>" . htmlentities($i) . "</td>";
                            echo "<td>" . htmlentities($row['rower_name']) . "</td>";
                            echo "<td>" . htmlentities($row['total_distance']) . "</td></tr>\n";
                            $i++;
                        }
                    ?>
                </tbody>
            </table><br>
        </div>
        <hr>
    </body>
</html>