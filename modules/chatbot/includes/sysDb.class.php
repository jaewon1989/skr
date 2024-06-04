<?php

// 시스템 DB 연결 및 쿼리 진행 
class sysDB {
    private $conn;
    private $host;
    private $user;
    private $password;
    private $baseName;
    private $port;
    private $Debug;
    private $status_fatal;
    
    function __construct() {
        $this->conn = false;
        $this->host = '110.10.189.18'; //hostname
        $this->user = 'cloud'; //username
        $this->password = 'cloud5279!!'; //password
        $this->baseName = 'cloud'; //name of your database
        $this->port = '3306';
        $this->debug = true;
        //$this->connect();
    }
 
    function __destruct() {
        $this->disconnect();
    }
    
    function connect() {
        if (!$this->conn) {
            $this->conn = mysql_connect($this->host, $this->user, $this->password); 
            mysql_select_db($this->baseName, $this->conn); 
            mysql_set_charset('utf8',$this->conn);
            
            if (!$this->conn) {
                $this->status_fatal = true;
                echo 'Connection BDD failed';
                die();
            } 
            else {
                $this->status_fatal = false;
            }
        }
 
        return $this->conn;
    }
 
    function disconnect() {
        if ($this->conn) {
            @pg_close($this->conn);
        }
    }
     
    // system table 컬럼 얻기  
    function getCols($tbl){

        $con= $this->connect();
        $cols=array();
        $result = mysql_query("SHOW COLUMNS FROM ".$tbl,$con); 
        while ($r=mysql_fetch_array($result))
        {
           if($r["Field"]!='uid') $cols[]= $r["Field"];     
        }  
        return $cols;   

    }

    function getDbSelect($table,$where,$data){
        $sql = 'select '.$data.' from '.$table.($where?' where '.$this->getSqlFilter($where):'');
        $result = $this->db_query($sql);
    
        return $result;
    }

    function getDbData($table,$where,$data){
        $row = mysql_fetch_array($this->getDbSelect($table,getSqlFilter($where),$data));
       
        return $row;
    }

    function db_query($sql){
        $con = $this->connect();
        mysql_query('set names utf8',$con);
        mysql_query('set sql_mode=\'\'',$con);

        return mysql_query($sql,$con);
    }

    //SQL필터링
    function getSqlFilter($sql){

        return preg_replace("( union| update| insert| delete| drop|\/\*|\*\/|\\\|\;)",'',$sql);
    }


}