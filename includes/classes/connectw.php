<?php
//connecting to the Database
class Database{
	private static $_DB_HOST = 'localhost:3306';
	private static $_DB_NAME = 'casestudies';
	private static $_DB_USER = 'root';
	private static $_DB_PASSWORD = '';
	protected static $_connection = NULL;
	
	public static function getConnection() {
		if(!self::$_connection){
			self::$_connection = new mysqli(self::$_DB_HOST,self::$_DB_USER,self::$_DB_PASSWORD, self::$_DB_NAME);
			if (self::$_connection->connect_error){
				die('Connect Error: '. self::$_connection->connect_error);
			}
		}
		return self::$_connection;
	}
	
	private function __construct(){	}
}