<?php


class Ftp {

    private $host;
    private $port;
    private $user;
    private $password;

    public $connection;
    public $log_in;

    public $all_files;
    public $csv_file_list;

    public function __construct($host, $port, $user, $password){
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
    }

    public function connect(){
        if ($this->connection = ftp_connect($this->host, $this->port)){
            return true;
        } else {
            return false;
        }
    }

    public function login_and_get_file_list(){
        if($this->connection){
            if($this->log_in = ftp_login($this->connection, $this->user, $this->password)){
                ftp_pasv($this->connection, true);

                $_SESSION['ftp_host'] = $this->host;
                $_SESSION['ftp_user'] = $this->user;
                $_SESSION['ftp_password'] = $this->password;

                $this->all_files = ftp_nlist($this->connection, '.');

                return true;
            }
        }

        return false;
    }

    public function filter_files(){
        foreach($this->all_files as $value){
            if(preg_match('#\.csv$#i', $value)){
                $this->csv_file_list[] = $value;
            }
        }
        $_SESSION['ftp_list_of_files'] = $this->csv_file_list;
    }

}