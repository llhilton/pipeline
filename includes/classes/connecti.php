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
				$items[$result['idproject']]=$result;
			}
			return($items);
		}
		catch(Exception $e){
			return false;
		}
	}
	
	static function getProjectsRegions(){
		$connection=Database::getConnection();
		$items="";
		$query = "SELECT project_country.project_idproject, country.region FROM project_country INNER JOIN country on project_country.country_idcountry = country.idcountry";
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

	//Add a new country to the database
	static function addnewcountry($country, $region){
		$connection = Database::getConnection();
		$query="INSERT INTO country VALUES (DEFAULT, '".$country."', '".$region."')";
		if (!$connection->query($query)){
			echo "Error :" .$query . "<br>" . $connection->error;
		}else{
			echo "Country added successfully.<br>";
		}
	}

	//Get a project's funding source(s).
	static function getProjectFunding($idproject){
		$connection = Database::getConnection();
		$query="SELECT p.amount, f.idFundingSource, f.fiscalYear, f.typeOfFunding FROM project_funded AS p INNER JOIN fundingsource AS f ON p.FundingSource_idFundingSource = f.idFundingSource WHERE p.project_idproject = ". $idproject;
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
	
	//Get a project's funding source(s).
	static function getProjectCountry($idproject){
		$connection = Database::getConnection();
		$query="SELECT c.country, c.region, c.idcountry FROM project_country AS p ";
		$query .= "INNER JOIN country AS c ON p.country_idcountry = c.idcountry WHERE p.project_idproject = ". $idproject;
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
	
	
	//Get all basic information on a project
	static function getProject($idproject){
		$item="";
		$connection=Database::getConnection();
		$query='SELECT * FROM project WHERE idproject = ' . $idproject;
		$result_obj='';
		$result_obj=$connection->query($query);
		try{
			$item = $result_obj->fetch_array(MYSQL_ASSOC);
			//Add the project's funding source(s)
			$item['funding']=Database::getProjectFunding($idproject);
			$item['countries']=Database::getProjectCountry($idproject);
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
			if (!$connection->query($query)){
				echo "Error :" .$query . "<br>" . $connection->error;
				die;//This will stop the query. This way, the "Funds added" message won't be a lie.
			}
		} 
		echo "Funds added successfully";
	}
	
	//Add countries to a project.
	static function addCountryToProject($idproject,$toadd){
		$connection=Database::getConnection();
		foreach ($toadd as $value){
			if ($value[0]<>'addcountry'){
				$query = "INSERT INTO project_country VALUES ";
				$query .= "(DEFAULT, ".$idproject.",".$value[0].")";
				if (!$connection->query($query)){
					echo "Error :" .$query . "<br>" . $connection->error;
					die;//This will stop the query. This way, the "Country added" message won't be a lie.
				}
			}
		}
		echo "Country/Countries added successfully";
	}

	//Edit the funding for a project
	static function editFundingForProject($idproject,$fundstoedit){
		$connection=Database::getConnection();
		//print_r($fundstoedit);
		foreach ($fundstoedit as $key=>$value){
			$keyarray=explode('.',$key);
			//print_r($keyarray);
			if ($value==0){
				$query="DELETE FROM project_funded WHERE FundingSource_idFundingSource = '".$keyarray[0]."' AND project_idproject= '".$idproject."'";
				if (!$connection->query($query)){
				 echo "Error :" .$query . "<br>" . $connection->error;
				 die;//This will stop the query. This way, the "Funds edited" message won't be a lie.
				 }
			}else{
				if ($keyarray[1]=='u'){
					$query = "UPDATE project_funded ";
					$query .= "SET amount = '".$value."' WHERE FundingSource_idFundingSource = '".$keyarray[0]."' AND project_idproject= '".$idproject."'";
					if (!$connection->query($query)){
				 		echo "Error :" .$query . "<br>" . $connection->error;
				 		die;//This will stop the query. This way, the "Funds edited" message won't be a lie.
				 	}
				} else {
					$query="INSERT INTO project_funded ";
					$query .= "VALUES (DEFAULT, '".$value."', '0', '".$idproject."', '".$keyarray[0]."')";
					if (!$connection->query($query)){
						echo "Error :" .$query . "<br>" . $connection->error;
						die;//This will stop the query. This way, the "Funds edited" message won't be a lie.
					} 
				}
			}
			
		}
		echo "Funds edited successfully";
	}
	
	//Delete all countries for a project
	static function removeCountriesForProject($idproject){
		$connection=Database::getConnection();
		$query="DELETE FROM project_country WHERE project_idproject = '".$idproject."'";
		if (!$connection->query($query)){
			echo "Error :" .$query . "<br>" . $connection->error;
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
	
	//Get projects and their funding sources.
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

	//get the number of possible funding sources
	static function getNumberSources(){
		$connection = Database::getConnection();
		$item="";
		$query = "SELECT COUNT(*) FROM fundingsource";
		$result_obj=$connection->query($query);
		try{
			$item = $result_obj->fetch_array(MYSQL_NUM);
			return $item[0];
		}
		catch(Exception $e){
			return false;
		}
	}
	
	// Get all the countries
	static function getPossibleCountries(){
		$connection=Database::getConnection();
		$query = "SELECT * FROM country ORDER BY country ASC";
		$items="";
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
	
	//Get all possible subawards 
	static function getSubawards(){
		$connection = Database::getConnection();
		$query = "SELECT * FROM watson WHERE status!='Award -- Terminated' and shortAwardNumber!='' ORDER BY shortAwardNumber";
		$items="";
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