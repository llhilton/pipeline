<?php
//connecting to the Database

define('DB_HOST', 'nova.umuc.edu');
define('DB_NAME', 'ct463b14');
define('DB_USER', 'ct463b14');
define('DB_PASSWORD', 'e4y4p5h9');

if ($connection = @new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_PASSWORD)){
	echo "Successful connection to MySQL";
}