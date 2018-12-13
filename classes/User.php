<?php

class User extends DbObject {

    protected static $db_table = "users";
    protected static $db_table_fields = array('login', 'password', 'email');

    public $login;
    public $password;
    public $email;

}