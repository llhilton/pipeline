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
	
	//Get all information on funding sources 
	static function getFunding(){
		//clear results
		$items="";
		$connection=Database::getConnection();
		$query='SELECT * FROM fundingsource';
		$result_obj=$connection->query($query);
		try{
			while($result = $result_obj->fetch_array(MYSQLI_ASSOC)){
				$items[]=$result;
			}
			return($items);
		}
		catch(Exception $e){
			return false;
		}
	}
	
	//Get all basic information on all projects
	static function getProjects(){
		$items="";
		$connection=Database::getConnection();
		$query='SElECT * FROM project';
		$result_obj=$connection->query($query);
		try{
			while($result = $result_obj->fetch_array(MYSQLI_ASSOC)){
				$items[]=$result;
			}
			return($items);
		}
		catch(Exception $e){
			return false;
		}
	}
	
	//Get all basic information on a project
	static function getProject($idproject){
		$item="";
		$connection=Database::getConnection();
		$query='SELECT * FROM project WHERE idproject = ' . $idproject;
		$result_obj='';
		$result_obj=$connection->query($query);
		try{
			$item = $result_obj->fetch_array(MYSQL_ASSOC);
			return $item;
		} 
		catch(Exception $e){
			return false;
		}
	}
	
	//Get all possible funding sources for a project.
	static function getPossibleFundingSources(){
		$items='';
		$connection=Database::getConnection();
		$query="SELECT idFundingSource, fiscalyear, typeOfFunding from fundingsource";
		$results='';
		$results=$connection->query($query);
		try{
			while($result = $results->fetch_array(MYSQLI_ASSOC)){
				$items[]=$result;
			}
			return($items);
		}
		catch(Exception $e){
			return false;
		}
	}
	
	//Add funding to a project.
	static function addFundingToProject($idproject,$toadd){
		$connection=Database::getConnection();
		foreach ($toadd as $value){
			$query = "INSERT INTO project_funded VALUES ";
			$query .= "(DEFAULT,".$value[1].", 0, ".$idproject.",".$value[0].")";
			if ($connection->query($query)){
				echo "The funding has been added successfully.";
			}else{
				echo "Error :" .$query . "<br>" . $connection->error;
			}
		} 
	}
	
	//Get the idFundingSource for a given fiscal year and type of funding
	static function getFundingSource($fy, $typefunding){
		$connection=Database::getConnection();
		$query = "SELECT idFundingSource FROM fundingsource WHERE fiscalYear = '".$fy;
		$query.= "' AND typeOfFunding = '".$typefunding."'";
		$result_obj='';
		$result_obj=$connection->query($query);
		try{
			$item = $result_obj->fetch_array(MYSQL_NUM);
			return $item[0];
		}
		catch(Exception $e){
			return false;
		}
	}
	
	//Get the amount of funding for a project in a fiscal year
	static function getProjectFiscalYear($idproject, $fiscalyear, $typeoffunding){
		$idfunding=Database::getFundingSource($fiscalyear, $typeoffunding);
		$connection=Database::getConnection();
		$query = "SELECT amount FROM project_funded WHERE project_idproject ='".$idproject."'";
		$query.= " AND FundingSource_idFundingSource='".$idfunding."'";
		$result_obj='';
		$result_obj=$connection->query($query);
		try{
			if ($result_obj->num_rows>0){
				$item = $result_obj->fetch_array(MYSQL_NUM);
				return $item[0];
			}else{
				return "";
			}
		}
		catch(Exception $e){
			return false;
		}
	}
	
	static function getProjectsFunding(){
		$connection = Database::getConnection();
		$query="SELECT * FROM project_funded AS a LEFT JOIN fundingsource ON a.FundingSource_idFundingSource=idFundingSource";
		$items='';
		$result_obj=$connection->query($query);
		try{
			while($result = $result_obj->fetch_array(MYSQLI_ASSOC)){
				$items[]=$result;
			}
			return($items);
		}
		catch(Exception $e){
			return false;
		}	
	}
}