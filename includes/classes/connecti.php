<?php
//connecting to the Database
class Database{
	private static $_DB_HOST = 'nova.umuc.edu';
	private static $_DB_NAME = 'ct463b14';
	private static $_DB_USER = 'ct463b14';
	private static $_DB_PASSWORD = 'e4y4p5h9';
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