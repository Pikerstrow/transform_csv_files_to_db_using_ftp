<?php
include_once "config.php";

class Database {

    public $connection;

    public function __construct() {
        $this->open_connection();
    }

    public function open_connection(){
        $this->connection = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        $this->connection->set_charset("utf8");
    }

    public function query($query) {
        $result = $this->connection->query($query);
        $this ->check_the_query($result);
        return $result;
    }

    private function check_the_query($result) {
        if(!$result) {
            die("Query failed");
        }
    }

    public static function check_data_exist($table_name_in_db){
        $conn = new self;
        $query = "SELECT * FROM `$table_name_in_db`";

        $result = $conn->query($query);

        if(($result->num_rows) > 0){
            return true;
        }
        return false;
    }

}

/**
 * Class DatabaseRemote - для під'єднання до віддіаленої БД для подальшого пересилання даних із локальної БД.
 */
class DatabaseRemote extends Database {

    public $db_ip_address;
    public $db_user;
    public $db_pass;
    public $db_name;
    public $db_port;
    public $db_tab_pref;

    public function __construct($ip_address,$db_user,$db_pass,$db_name, $db_port, $db_tab_pref) {

        $this->db_ip_address = $ip_address;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        $this->db_name = $db_name;
        $this->db_port = $db_port;
        $this->db_tab_pref = $db_tab_pref;

        $this->open_connection();
    }

    public function open_connection(){
        $this->connection = new mysqli($this->db_ip_address, $this->db_user, $this->db_pass, $this->db_name, $this->db_port, $this->db_tab_pref);
        $this->connection->set_charset("utf8");
    }



    public function test_connection_after_creating(){

        if ($this->connection->connect_errno) {
            echo "<span style='color:red;'>Wrong settings!</span>";
        } else {
            $_SESSION['db_user']       = $this->db_user;
            $_SESSION['db_password']   = $this->db_pass;
            $_SESSION['db_name']       = $this->db_name;
            $_SESSION['db_port']       = $this->db_port;
            $_SESSION['db_ip_address'] = $this->db_ip_address;
            $_SESSION['db_table_prefix'] = $this->db_tab_pref;

            echo "<span style='color:green;'>Connection is ok</span>";
        }

    }

}


$database = new Database();
