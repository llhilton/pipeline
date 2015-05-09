<?php
/**
 * includes/classes/Subawards.php
*
* Class for workings with a project's sub-awards
*
* @version    0.1 2015-04-15
* @package    BEP Pipeline
* @copyright  Copyright (c) 2015 Lisa Hilton
* @license    GNU General Public License
* @since      Since Release 1.0
*/
class Subawards{
	public function __construct(){	}
	
	//if subaward is in the array, return "checked" for the checkboxes
	public function checksubaward($shortnumber,$shortarray){
		if (in_array($shortnumber, $shortarray)){
			return "checked";
		}
	}
	
	//A form to add or edit subawards to a project
	public function addsubawardsform($idproject){
		$project=new Project();
		$projectinfo=$project->getProject($idproject);
		if (is_array($projectinfo['shortawardnumbers'])){
			$shortawards=$projectinfo['shortawardnumbers'];
			$shorts="";
			foreach ($shortawards as $shortnumber){
				$shorts[]=$shortnumber['shortawardnumber'];
			}
		}else{
			$shorts=array();
		}
		echo "<h2>".$projectinfo['title']."</h2>";
		echo "<p>Select subaward(s)";
		$subawards=$this->getSubawards();
		echo '<form action="index.php?action=addsubawards&idproject='.$idproject.'" method="post">';
		foreach ($subawards as $subaward){
			//echo checksubaward($subaward['shortAwardNumber'], $shorts);
			echo '<input id="'.$subaward['shortAwardNumber'].'" name="subawards[]" value="'.$subaward['shortAwardNumber'].'" type="checkbox" '.$this->checksubaward($subaward['shortAwardNumber'], $shorts).' >'.$subaward['shortAwardNumber'].': '.$subaward['title'].'<br>';
	
		}
		echo '<input type="submit" name="addsubawards" value="Submit"> <input type="reset" value="Reset">';
		echo '</form>';
	}
	
	//Add subawards to a project
	public function addsubawards($idproject, $awards){
		$this->deleteProjectSubawards($idproject);
		foreach ($awards as $award){
			//print_r($award);
			$this->addSubaward($idproject, $award);
		}
		$project=new Project();
		$projectinfo=$project->displayProject($idproject);
	}
	
	//Get the short award number(s) for a project
	public function getProjectShortAwardNumbers($idproject){
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
	
	//Get the watson information based on a short award number
	public function getWatsonShort($shortaward){
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
	
	//Get the impromptu information based on a short award number
	public function getImpromptuShort($shortaward,$version){
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
	

	//Get all possible subawards
	public function getSubawards(){
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
	public function deleteProjectSubawards($idproject){
		$connection=Database::getConnection();
		$query="DELETE FROM project_shortawardnumber WHERE project_idproject = '".$idproject."'";
		if (!$connection->query($query)){
			echo "Error :" .$query . "<br>" . $connection->error;
		}
	}
	
	//Add subaward to project
	public function addSubaward($idproject, $award){
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
	
	//Empty the watson and watson_country tables.
	function clearwatson(){
		$connection = Database::getConnection();
		//Empty watson table
		$query="TRUNCATE TABLE watson";
		if (!$connection->query($query)){
			echo "Error :" .$query . "<br>" . $connection->error;
		}
		//Empty watson_country table
		$query="TRUNCATE TABLE watson_country";
		if (!$connection->query($query)){
			echo "Error :" .$query . "<br>" . $connection->error;
		}
	}
	
	
}