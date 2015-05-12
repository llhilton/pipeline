<?php
/**
 * includes/classes/Project.php
*
* Class for workings with a single project
*
* @version    0.1 2015-04-15
* @package    BEP Pipeline
* @copyright  Copyright (c) 2015 Lisa Hilton
* @license    GNU General Public License
* @since      Since Release 1.0
*/
class Project{
	public function __construct(){	}
	
	//Form to edit the basic information on a project.
	public function editprojectbasicsform($idproject){
		$projectbasics=$this->getProject($idproject);
		echo '<form action="index.php?action=editprojectbasics&idproject='.$idproject.'" method="post">';
		echo '<fieldset><legend>Project Information</legend>';
		echo '<label for="title">Title: </label>';
		echo '<input id="title" name="title" type="text" value="'.$projectbasics['title'].'"><br>';
		echo '<label for="unique_id">Unique Identifier: </label>';
		echo '<input id="unique_id" name="unique_id" type="text" value="'.$projectbasics['unique_id'].'"><br>';
		echo '<label for="task_number">Task number: </label>';
		echo '<input id="task_number" name="task_number" type="text" value="'.$projectbasics['task_number'].'"><br>';
		echo '<label for="notes">Notes: </label>';
		echo '<textarea cols="40" rows="3" name="notes">'.$projectbasics['notes'].'</textarea><br>';
		echo '</fieldset>';
		echo '<input type="submit" name="editprojectbasics" value="Submit"> <input type="reset" value="Reset">';
		echo '</form>';
	}
	
	//Update the basic information on a project
	function editprojectbasics($idproject, $projectbasics){
		$this->editProjectBasicsQuery($idproject, $projectbasics);
		$this->displayProject($idproject);
	}
	
	//A form to add a new country to the database
	public function addprojectform(){
		echo '<form action="index.php?action=addproject" method="post">';
		echo '<fieldset><legend>Project Information</legend>';
		echo '<label for="title">Title: </label>';
		echo '<input id="title" name="title" type="text"><br>';
		echo '<label for="unique_id">Unique Identifier: </label>';
		echo '<input id="unique_id" name="unique_id" type="text"><br>';
		echo '<label for="task_number">Task number: </label>';
		echo '<input id="task_number" name="task_number" type="text"><br>';
		echo '<label for="notes">Notes: </label>';
		echo '<textarea cols="40" rows="3" name="notes"></textarea><br>';
		echo '</fieldset><fieldset><legend>Funding</legend>';
		$fundingsource=new Fundingsource();
		$possiblesources=$fundingsource->getPossibleFundingSources();
		$i=0;
		foreach ($possiblesources as $value){
			echo '<label for="'.$value['fiscalyear'].'_'.$value['typeOfFunding'].'">FY'.$value['fiscalyear'].' '.$value['typeOfFunding'].'</label> $';
			echo '<input id="'.$value['idFundingSource'].'" name="funding['.$value['idFundingSource'].']" type="text"><br>';
			$i++;
			if ($i % 2 == 0){
				echo "<hr>";
			}
		}
		echo '</fieldset><fieldset><legend>Countries</legend>';
		$country=new Country();
		$countries=$country->getPossibleCountries();
		foreach ($countries as $value){
			echo '<input id="'.$value['idcountry'].'" name="country['.$value['idcountry'].']" type="checkbox">'.$value['country'].'<br>';
		}
	
		echo '</fieldset><fieldset><legend>Subawards</legend>';
		$subawardobj=new Subawards();
		$subawards=$subawardobj->getSubawards();
		foreach ($subawards as $subaward){
			echo '<input id="'.$subaward['shortAwardNumber'].'" name="subawards[]" value="'.$subaward['shortAwardNumber'].'" type="checkbox">'.$subaward['shortAwardNumber'].': '.$subaward['title'].'<br>';
		}
		echo '</fieldset>';
		echo '<input type="submit" name="addproject" value="Submit"> <input type="reset" value="Reset">';
		echo '</form>';
	}
	
	//Add a project and it's associated information
	public function addproject($projectinfo){
		//print_r($projectinfo);
		$newidproject=$this->addProjectQuery($projectinfo['basics']);
		return $newidproject;
		//echo $newidproject;
		/*$projectfunding=new ProjectFunding();
		$projectcountry=new ProjectCountry();
		$projectfunding->addFundingToProject($newidproject, $projectinfo['funding']);
		$projectcountry->addCountryToProject($newidproject, $projectinfo['country']);
		$subawards=new Subawards();
		foreach($projectinfo['subawards'] as $value){
			$subawards->addSubaward($newidproject, $value);
		}
		echo '<a href="index.php?idproject='.$newidproject.'">View the project</a>';*/
	}
	

	
	//Display information on a single project.
	public function displayProject($idproject){
		$project_array=$this->getProject($idproject);
		echo "<h2><a href=\"index.php?action=displayproject&idproject=".$project_array['idproject']."\">". $project_array['title'] ."</a></h2>";
		echo "Task Number: " . $project_array['task_number'] ."<br>";
		echo "Unique ID :" . $project_array['unique_id'] ."<br>";
		echo "Notes: " . $project_array['notes'] ."<br>";
		echo '<a href="index.php?action=editprojectbasicsform&idproject='.$project_array['idproject'].'">Edit project information</a>';
		echo "<h3>Funding/Deobligations</h3>";
		if ($project_array['funding']){
			$projectfunding=new ProjectFunding();
			$projectfunding->displayProjectFunding($project_array['funding']);
			echo "<a href=\"index.php?action=editfundingform&idproject=".$project_array['idproject']."\">Edit funding/deobligations</a><br>";
		}else{
			echo "<a href=\"index.php?action=addfundingform&idproject=".$project_array['idproject']."\">Add funding/deobligations</a><br>";
		}
		echo "<h3>Location</h3>";
		if ($project_array['countries']){
			$projectcountry=new ProjectCountry();
			$projectcountry->displayProjectCountry($project_array['countries']);
			echo "<a href=\"index.php?action=editcountryform&idproject=".$project_array['idproject']."\">Edit countries</a><br>";
		}else {
			echo "<a href=\"index.php?action=addcountryform&idproject=".$project_array['idproject']."\">Add country</a><br>";
		}
		echo "<h3>Watson/Impromptu Information</h3>";
		
		$subawards=new Subawards();
		if ($project_array['shortawardnumbers']){
			foreach($project_array['shortawardnumbers'] as $shortaward){
				$watson=$subawards->getWatsonShort($shortaward['shortawardnumber']);
				echo "<b>".$shortaward['shortawardnumber']."</b><br>";
				foreach ($watson as $watsonvalue){
					echo "Watson Title: ".$watsonvalue['title']."<br>\n";
					echo "Watson Status: ".$watsonvalue['status']."<br>\n";
					echo "Watson Start Date: ".date("m/d/y",strtotime($watsonvalue['startdate']))."<br>\n";
					echo "Watson End Date: ".date("m/d/y",strtotime($watsonvalue['enddate']))."<br>\n";
					echo "Watson Budget: $".number_format($watsonvalue['budget'],2)."<br><br>\n";
				}
				$impromptu="";
				$impromptu=$subawards->getImpromptuShort($shortaward['shortawardnumber'], 'new');
				if ($impromptu<>""){
					echo '<div class="impromptuCurrent"><b>Impromptu Information</b><br>';
					foreach ($impromptu as $impromptuvalue){
						echo "<u>Project String: ".$impromptuvalue['project_string']."</u><br>\n";
						echo "Impromptu Budget: $".number_format($impromptuvalue['total_obligation'],2)."<br>\n";
						echo "Impromptu Expended: $".number_format($impromptuvalue['expended'],2)."<br>\n";
						echo "Impromtu Remaining (unloaded): $".number_format($impromptuvalue['award_reserve'],2)."<br>\n";
					}
					echo "</div>";
				}
				$oldimpromptu="";
				$oldimpromptu=$subawards->getImpromptuShort($shortaward['shortawardnumber'],'old');
				if ($oldimpromptu<>""){
					echo '<div class="impromptuOld"><b>Previous Impromptu Information</b><br>';
					foreach ($oldimpromptu as $impromptuvalue){
						echo "<u>Project String: ".$impromptuvalue['project_string']."</u><br>\n";
						echo "Impromptu Budget: $".number_format($impromptuvalue['total_obligation'],2)."<br>\n";
						echo "Impromptu Expended: $".number_format($impromptuvalue['expended'],2)."<br>\n";
						echo "Impromtu Remaining (unloaded): $".number_format($impromptuvalue['award_reserve'],2)."<br>\n";
					}
					echo "</div>";
				}
			}
		}
		echo '<div class="clearfloat"><a href="index.php?action=addsubawardsform&idproject='.$project_array['idproject'].'">Add/Edit subaward(s)</a></div>';
	}
	
	//Get all basic information on a project
	public function getProject($idproject){
		$item="";
		$connection=Database::getConnection();
		$query='SELECT * FROM project WHERE idproject = ' . $idproject;
		$result_obj='';
		$result_obj=$connection->query($query);
		try{
			$item = $result_obj->fetch_array(MYSQL_ASSOC);
			//Add the project's funding source(s)
			$projectfunding=new ProjectFunding();
			$item['funding']=$projectfunding->getProjectFunding($idproject);
			$projectcountry=new ProjectCountry();
			$item['countries']=$projectcountry->getProjectCountry($idproject);
			$subawards=new Subawards();
			$item['shortawardnumbers']=$subawards->getProjectShortAwardNumbers($idproject);
			//$item['watson_country']=Database::getWatsonAwardCountries($idproject, $item['shortawardnumbers']);
			//$item['watson']=Database::getWatson($item['shortawardnumbers']);
			return $item;
		}
		catch(Exception $e){
			return false;
		}
	}
	
	//add a project
	public function addProjectQuery($projectbasics){
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
	public function editProjectBasicsQuery($idproject, $projectbasics){
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
	
	
	
}