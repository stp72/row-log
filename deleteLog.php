<?php
require_once "Includes/db.php";
  
RowerDB::getInstance()->delete_log( $_POST['rower_log_id'] );
header('Location: editLogList.php' );
?>
