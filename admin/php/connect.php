<?php
/**
* CLASS DATABASE
*/

	class Database extends Config {

	    private static $db;
	    private $connection;

	    public function __construct() {
	        $this->connection = new MySQLi($this->SERVERNAME,$this->DATABASE_USERNAME,$this->DATABASE_PASSWORD,$this->DATABASE_NAME);
	        $this->connection->query("SET NAMES 'utf8'");
	    }	

	    function __destruct() {
	        $this->connection->close();
	    }

	    public static function getConnection() {
	        if (static::$db == null) {
	            static::$db = new Database();
	        }
	        return static::$db->connection;
	    }
	}
?>