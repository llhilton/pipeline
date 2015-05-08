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
	
	//Get ids on funding sources 
	static function getFundingID(){
		//clear results
		$items="";
		$connection=Database::getConnection();
		$query='SELECT idFundingSource FROM fundingsource';
		$result_obj="";
		$result_obj=$connection->query($query);
		try{
			while($result = $result_obj->fetch_array(MYSQLI_ASSOC)){
				$items[]=$result['idFundingSource'];
			}
			return($items);
		}
		catch(Exception $e){
			return false;
		}
	}
	
	//Get the types of funding for the summary header
	static function getFiscalYears(){
		$connection=Database::getConnection();
		$query="SELECT fiscalYear, typeOfFunding FROM fundingsource";
		$result_obj="";
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
	
	//Get the amount budgeted for each funding source
	static function getBudgeted(){
		$connection=Database::getConnection();
		$query="SELECT fundingamount FROM fundingsource";
		$result_obj="";
		$result_obj=$connection->query($query);
		try{
			while($result = $result_obj->fetch_array(MYSQLI_ASSOC)){
				$items[]=$result['fundingamount'];
			}
			return($items);
		}
		catch(Exception $e){
			return false;
		}
	}
	
	//Get the amount spent so far for each funding source
	static function getSpent(){
		$connection=Database::getConnection();
		$query="SELECT spent FROM fundingsource";
		$result_obj="";
		$result_obj=$connection->query($query);
		try{
			while($result = $result_obj->fetch_array(MYSQLI_ASSOC)){
				$items[]=$result['spent'];
			}
			return($items);
		}
		catch(Exception $e){
			return false;
		}
	}
	
	//Get the amount obligated so far for each funding source
	static function getObligated(){
		$connection=Database::getConnection();
		$query="SELECT obligation FROM fundingsource";
		$result_obj="";
		$result_obj=$connection->query($query);
		try{
			while($result = $result_obj->fetch_array(MYSQLI_ASSOC)){
				$items[]=$result['obligation'];
			}
			return($items);
		}
		catch(Exception $e){
			return false;
		}
	}
	
	//Get the impact fee for each funding source
	static function getImpactFee(){
		$connection=Database::getConnection();
		$query="SELECT impactfee FROM fundingsource";
		$result_obj="";
		$result_obj=$connection->query($query);
		try{
			while($result = $result_obj->fetch_array(MYSQLI_ASSOC)){
				$items[]=$result['impactfee'];
			}
			return($items);
		}
		catch(Exception $e){
			return false;
		}
	}
	
	//Get the amount remaining in each funding source after spend, obligation, and fees
	static function getRemaining(){
		$connection=Database::getConnection();
		$query="SELECT (fundingamount - spent - obligation - IFNULL(impactfee,0)) as remaining FROM fundingsource";
		$result_obj="";
		$result_obj=$connection->query($query);
		try{
			while($result = $result_obj->fetch_array(MYSQLI_ASSOC)){
				$items[]=$result['remaining'];
			}
			return($items);
		}
		catch(Exception $e){
			return false;
		}
	}
	
	//Get the regional pipelines
	static function getRegionalPipeline(){
		//$items=array('MENA'=>0,'MENA-Iraq'=>0,'South Asia'=>0,'Southeast Asia'=>0,'Sub-Saharan Africa'=>0,'Ukraine'=>0,'Global'=>0);
		$items="";
		$fundingids=Database::getFundingID();
		//print_r($fundingids);
		foreach ($fundingids as $fundingid){
			//echo $fundingid;
			$query = "SELECT sum(amount) FROM project_funded WHERE FundingSource_idFundingSource='".$fundingid."'";
			echo $query;
			/*foreach ($projectregions as $value){
				$query="SELECT pf.amount, fs.fiscalYear, fs.typeOfFunding  FROM project_funded as pf ";
				$query.="INNER JOIN fundingsource AS fs on pf.FundingSource_idFundingSource = ";
				$query.= "fs.idFundingSource ";
				$query.= "WHERE pf.project_idproject = '".$value['project_idproject']."' ";
				$query.= "AND fs.idFundingSource = '".$fundingid."'";
				//echo $query;
				$connection = Database::getConnection();
				$result_obj='';
				$result_obj=$connection->query($query);
				try{
					while($result = $result_obj->fetch_array(MYSQLI_ASSOC)){
						$items[]=array('amount'=>$result['amount'], 'fiscalYear'=>$result['fiscalYear'], 'typeOfFunding'=>$result['typeOfFunding']);
					}
					//return($items);
				}
				catch(Exception $e){
					return false;
				}
				//echo $query;
				//print_r($value);
			}*/

		}
		print_r($items);
		
	}
	
	//Get the pipeline funding
	static function getPipeline(){
		//$items=array('MENA'=>0,'MENA-Iraq'=>0,'South Asia'=>0,'Southeast Asia'=>0,'Sub-Saharan Africa'=>0,'Ukraine'=>0,'Global'=>0);
		$items="";
		$fundingids=Database::getFundingID();
		//print_r($fundingids);
		$i=0;
		foreach ($fundingids as $fundingid){
			//echo $fundingid;
			$query = "SELECT sum(amount) AS total FROM project_funded WHERE FundingSource_idFundingSource='".$fundingid."'";
			$connection = Database::getConnection();
			$result_obj='';
			$result_obj=$connection->query($query);
			try{
				$result = $result_obj->fetch_array(MYSQLI_ASSOC);
				$items[$i]=$result['total'];
			 }
			 //return($items);
			 //}
			 catch(Exception $e){
				 return false;
			 }
			 $i++;
			 //echo $query;
			 //print_r($value);	
		}
		//print_r($items);
		return($items);
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
			echo "Country added successfully.<br>\n";
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
	
	//Get the subawards and that information for a project
	static function getProjectShortAwardNumbers($idproject){
		$connection=Database::getConnection();
		$query="SELECT shortawardnumber FROM project_shortawardnumber WHERE project_idproject = '".$idproject."'";
		$result_obj="";
		$result_obj=$connection->query($query);
		$items="";
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
	
	//Get the subawards and that information for a project
	static function getWatsonShort($shortaward){
		$connection=Database::getConnection();
		$query="SELECT * FROM watson WHERE shortAwardNumber = '".$shortaward."'";
		$result_obj="";
		$result_obj=$connection->query($query);
		$items="";
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
	
	//Get the subawards and that information for a project
	static function getImpromptuShort($shortaward,$version){
		$connection=Database::getConnection();
		if ($version=='new'){
			$tablename="impromptu";
		}else{
			$tablename="impromptu_old";
		}
		$query="SELECT * FROM ".$tablename." WHERE shortAwardNumber = '".$shortaward."'";
		$result_obj="";
		$result_obj=$connection->query($query);
		$items="";
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
			$item['shortawardnumbers']=Database::getProjectShortAwardNumbers($idproject);
			//$item['watson_country']=Database::getWatsonAwardCountries($idproject, $item['shortawardnumbers']);
			//$item['watson']=Database::getWatson($item['shortawardnumbers']);
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
		echo "Funds/deobligations added successfully<br>\n";
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
		echo "Country/Countries added successfully<br>\n";
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
		echo "Funds/deobligations edited successfully";
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
	
	//Remove subawards connected to a project (preparation for adding or editing)
	static function deleteProjectSubawards($idproject){
		$connection=Database::getConnection();
		$query="DELETE FROM project_shortawardnumber WHERE project_idproject = '".$idproject."'";
		if (!$connection->query($query)){
			echo "Error :" .$query . "<br>" . $connection->error;
		}
	}
	
	//Add subaward to project
	static function addSubaward($idproject, $award){
		$connection=Database::getConnection();
		$query = "INSERT INTO project_shortawardnumber VALUES (";
		$query.= "DEFAULT, ";
		$query.= "'".$idproject."', ";
		$query.="'".$award."')";
		if (!$connection->query($query)){
			echo "Error :" .$query . "<br>" . $connection->error;
		}else{
			echo $award." added successfully.<br>\n";
		}
	}
	
	//add a project
	static function addProject($projectbasics){
		extract($projectbasics);
		$connection=Database::getConnection();
		$query="INSERT INTO project VALUES (";
		$query.="DEFAULT, ";
		$query.="'".$task_number."', ";
		$query.="'".$unique_id."', ";
		$query.="'".$title."', ";
		$query.="'".$notes."')";
		$result_obj='';
		$result_obj=$connection->query($query);
		try{
			return($connection->insert_id);
		}
		catch(Exception $e){
			return false;
		}
	}
	
	//edit the basic information on a project
	static function editProjectBasics($idproject, $projectbasics){
		extract($projectbasics);
		$connection=Database::getConnection();
		$query = "UPDATE project SET ";
		$query.="task_number='".$task_number."', ";
		$query.="unique_id='".$unique_id."', ";
		$query.="title='".$title."', ";
		$query.="notes='".$notes."' ";
		$query .= "WHERE idproject = '".$idproject."'";
		$result_obj='';
		$result_obj=$connection->query($query);
		if (!$connection->query($query)){
			echo "Error :" .$query . "<br>" . $connection->error;
		}else{
			echo "Project updated successfully.<br>\n";
		}
	}
	
	//Get all the information on the funding sources
	static function getFullFundingSources(){
		$connection=Database::getConnection();
		$query="SELECT * FROM fundingsource";
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
	
	//update the funding information
	static function updatefunding($idFundingSource,$value){
		$connection=Database::getConnection();
		$query="UPDATE fundingsource SET ";
		$query.="fundingamount='".$value['funding']."', ";
		$query.="spent='".$value['spent']."', ";
		$query.="obligation='".$value['obligation']."', ";
		if ($value['impactfee']==""){
			$query.="impactfee=NULL ";
		}else{
			$query.="impactfee='".$value['impactfee']."' ";
		}
		$query .= "WHERE idFundingSource = '".$idFundingSource."'";
		$result_obj='';
		$result_obj=$connection->query($query);
		if (!$connection->query($query)){
			echo "Error :" .$query . "<br>" . $connection->error;
		}else{
			echo "Funding updated successfully.<br>\n";
		}
	}
	
	//Add a funding source to the database
	static function addFundingSource($fundinginfo){
		extract($fundinginfo);
		$connection=Database::getConnection();
		$query="INSERT INTO fundingsource VALUES (";
		$query.="DEFAULT, ";
		$query.="'".$fiscalyear."', ";
		$query.="'".$typeoffunding."', ";
		$query.="'".$fundingamount."', ";
		$query.="'".$spent."', ";
		$query.="'".$obligation."', ";
		$query.="'".$impactfee."')";
		$result_obj='';
		if (!$connection->query($query)){
			echo "Error :" .$query . "<br>" . $connection->error;
		}else{
			echo "Funding source added.<br>\n";
		}
	}
}