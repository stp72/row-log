<?php
require 'lib/password.php';

class RowerDB extends mysqli {
    // single instance of self shared among all instances
    private static $instance = null;
    // db connection config vars
    private $user = "";
    private $pass = "";
    private $dbName = "";
    private $dbHost = "localhost";

    //This method must be static, and must return an instance of the object if the object
    //does not already exist.
    public static function getInstance() {
        if( !self::$instance instanceof self ) {
            self::$instance = new self;
        }
        
        return self::$instance;
    }

    // The clone and wakeup methods prevents external instantiation of copies of the Singleton class,
    // thus eliminating the possibility of duplicate objects.
    public function __clone() {
        trigger_error( 'Clone is not allowed.', E_USER_ERROR );
    }

    public function __wakeup() {
        trigger_error( 'Deserializing is not allowed.', E_USER_ERROR );
    }

    // private constructor
    private function __construct() {
        parent::__construct( $this->dbHost, $this->user, $this->pass, $this->dbName );
        if( mysqli_connect_error() ) {
            exit( 'Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() );
        }
        
        parent::set_charset('utf-8');
    }

    public function verify_rower_credentials( $email, $password ) {
        $db_email = $this->real_escape_string( $email );
        $stmt = $this->prepare( 'SELECT passwd FROM rower WHERE email = ?' );
       
        if( !$stmt->bind_param( 's', $db_email ) ) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            return false;
        }
        
        if( !$stmt->execute() ) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            return false;
        }

        $passwd_hash = '';
        $stmt->bind_result( $passwd_hash );
        $stmt->fetch();
        $stmt->close();
        
        $escaped_password = $this->real_escape_string( $password );
        if( password_verify( $escaped_password, $passwd_hash ) ) {
            return true;
        }
        
        return false;
    }
    
    public function change_password( $rower_id, $cur_password, $new_password ) {
        $stmt = $this->prepare( 'SELECT passwd FROM rower WHERE rower_id = ?' );
       
        if( !$stmt->bind_param( 'i', $rower_id ) ) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            return false;
        }
        
        if( !$stmt->execute() ) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            return false;
        }

        $passwd_hash = '';
        $stmt->bind_result( $passwd_hash );
        $stmt->fetch();
        $stmt->close();
        
        $escaped_password = $this->real_escape_string( $cur_password );
        error_log( "change_password - passwd = " . $escaped_password . ", hash = " . $passwd_hash . ", id = " . $rower_id );
        if( password_verify( $escaped_password, $passwd_hash ) ) {
            return $this->update_password( $rower_id, $new_password );
        }
        
        echo "Your current password doesn't match.  No changes were done!";
        return false;
    }

    private function update_password( $rower_id, $password ) {
        $escaped_passwd = $this->real_escape_string( $password );
        $db_passwd = password_hash( $escaped_passwd, PASSWORD_BCRYPT );
        $stmt = $this->prepare( "UPDATE rower SET passwd = ? WHERE rower_id = ?" );
        if( !$stmt->bind_param( 'si', $db_passwd, $rower_id ) ) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            return false;
        }
        
        if( !$stmt->execute() ) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            return false;
        }
        
        return true;
    }
    
    public function get_rower_id_by_email( $email ) {
        $db_email = $this->real_escape_string( $email );
        $stmt = $this->prepare( "SELECT rower_id FROM rower WHERE email = ?" );
        if( !$stmt->bind_param( 's', $db_email ) ) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            return null;
        }
        
        if( !$stmt->execute() ) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            return null;
        }

        $rower = null;
        $stmt->bind_result( $rower );
        $stmt->fetch();
        $stmt->close();
        
        return $rower;
    }

    public function get_rankings() {
        return $this->query( "SELECT CONCAT(r.first_name, ' ', r.last_name) AS rower_name, SUM(IFNULL(l.distance,0)) AS total_distance FROM rower r LEFT JOIN rower_log l ON r.rower_id = l.rower_id GROUP BY r.rower_id ORDER BY SUM(l.distance) DESC, r.last_name ASC, r.first_name ASC" );
    }

    public function get_rower_count() {
        $rower_count = $this->query( "SELECT COUNT(rower_id) as rower_count FROM rower" );
        if( $rower_count->num_rows > 0 ) {
            $row = $rower_count->fetch_row();
            return $row[0];
        }
        else {
            return 0;
        }
    }

    public function get_season_meters() {
        $season_meters = $this->query( "SELECT COALESCE( SUM( distance ), 0) AS season_meters FROM rower_log" );
        if( $season_meters->num_rows > 0) {
            $row = $season_meters->fetch_row();
            return $row[0];
        }
        else {
            return 0;
        }
    }
    
    public function get_avg_meters() {
        $total_meters = $this->get_season_meters();
        if( $total_meters == 0 ) {
            return 0;
        }
        
        $rowers = $this->get_rower_count();
        return round( $total_meters / $rowers, 2 );
    }

    public function create_rower( $first_name, $last_name, $email, $weight_class, $password ) {
        $db_first_name = $this->real_escape_string( $first_name );
        $db_last_name = $this->real_escape_string( $last_name );
        $db_email = $this->real_escape_string( $email );
        $db_weight_class = $this->real_escape_string( $weight_class );

        $escaped_passwd = $this->real_escape_string( $password );
        $db_passwd = password_hash( $escaped_passwd, PASSWORD_BCRYPT );
        $stmt = $this->prepare( "INSERT INTO rower( first_name, last_name, email, weight_class, passwd ) VALUES( ?, ?, ?, ?, ? )" );
        if( !$stmt->bind_param( "sssis", $db_first_name, $db_last_name, $db_email, $db_weight_class, $db_passwd ) ) {
            echo "ERROR - Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        if( !$stmt->execute() ) {
            echo "ERROR - Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
    }

    public function get_rower_meters( $rower_id ) {
        $stmt = $this->prepare( "SELECT COALESCE( SUM( distance ), 0) FROM rower_log WHERE rower_id = ?" );
        if( !$stmt->bind_param( 'i', $rower_id ) ) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            return 0;
        }
        
        if( !$stmt->execute() ) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            return 0;
        }

        $rower_meters = 0;
        $stmt->bind_result( $rower_meters );
        $stmt->fetch();
        $stmt->close();
        
        return $rower_meters;
    }
    
    function insert_log( $rower_id, $date_rowed, $distance, $notes ) {
        $db_notes = $this->real_escape_string( $notes );
        
        $stmt = $this->prepare( "INSERT INTO rower_log( rower_id, date_rowed, distance, notes ) VALUES( ?, ?, ?, ? )" );
        if( !$stmt->bind_param( "isis", $rower_id, $date_rowed, $distance, $db_notes ) ) {
            echo "ERROR - Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        if( !$stmt->execute() ) {
            echo "ERROR - Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
    }

    function format_date_for_sql( $date ) {
        if( $date == "" ) {
            return null;
        }
        else {
            $dateParts = date_parse( $date );
            return $dateParts['year'] * 10000 + $dateParts['month'] * 100 + $dateParts['day'];
        }
    }

    public function update_log( $rower_log_id, $date_rowed, $distance, $notes ) {
        $db_notes = $this->real_escape_string($notes);
        $this->query( "UPDATE rower_log SET notes = '" . $db_notes .
                      "', date_rowed = " . $this->format_date_for_sql( $date_rowed ) .
                      ", distance = " . $distance .
                      " WHERE rower_log_id = " . $rower_log_id );
    }

    public function get_logs_by_rower_id( $rower_id ) {
        $array = [];
        $stmt = $this->prepare( "SELECT rower_log_id, rower_id, date_rowed, distance, notes FROM rower_log WHERE rower_id = ? order by date_rowed DESC" );
        if( !$stmt->bind_param( 'i', $rower_id ) ) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            return false;
        }
        
        if( !$stmt->execute() ) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            return false;
        }
        
        $stmt->store_result();
        
        $variables = [];
        $data = [];
        $meta = $stmt->result_metadata();
        
        while( $field = $meta->fetch_field() ) {
            $variables[] = &$data[$field->name]; // pass by reference
        }
        
        call_user_func_array( array( $stmt, 'bind_result' ), $variables );
        
        $i=0;
        while( $stmt->fetch() )
        {
            $array[$i] = array();
            foreach( $data as $k=>$v ) {
                $array[$i][$k] = $v;
            }
            $i++;
        }

        return $array;
    }

    public function get_log_by_rower_log_id( $rower_log_id ) {
        return $this->query( "SELECT rower_log_id, rower_id, date_rowed, distance, notes FROM rower_log WHERE rower_log_id = " . $rower_log_id );
    }

    public function delete_log( $rower_log_id ) {
        $this->query( "DELETE FROM rower_log WHERE rower_log_id = " . $rower_log_id );
    }
}
?>