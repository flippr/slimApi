<?php

class Db extends PDO
{
    public function __construct()
    {
        $ini_array = parse_ini_file("../config/settings.ini", true);
        $this->conn_var = $ini_array['environment']['conn_var'];
        $dbHost = $ini_array[$this->conn_var]['host'];
        $dbUser = $ini_array[$this->conn_var]['user'];
        $dbPass = $ini_array[$this->conn_var]['pass'];
        $dbName = $ini_array[$this->conn_var]['db'];

        parent::__construct("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
    }
} 