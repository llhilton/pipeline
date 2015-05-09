<?php
/**
 * includes/classes/ProjectFunding.php
 *
 * Class for working with the connection of projects and funding 
 *
 * @version    0.1 2015-04-15
 * @package    BEP Pipeline
 * @copyright  Copyright (c) 2015 Lisa Hilton
 * @license    GNU General Public License
 * @since      Since Release 1.0
 */
class ProjectFunding{
	public function __construct(){	}
	
	//A form to add funding/deobligation to a project.
	public function addfundingform($idproject){
		$project=new Project();
		$projectinfo=$project->getProject($idproject);
		echo "<h2>".$projectinfo['title']."</h2>";
		$fundingsource=new Fundingsource();
		$possiblesources=$fundingsource->getPossibleFundingSources();
		echo '<form action="index.php?action=addfunding&idproject='.$idproject.'" method="post">';
		$i=0;
		foreach ($possiblesources as $value){
			echo '<label for="'.$value['fiscalyear'].'_'.$value['typeOfFunding'].'">FY'.$value['fiscalyear'].' '.$value['typeOfFunding'].'</label> $';
			echo '<input id="'.$value['idFundingSource'].'" name="'.$value['idFundingSource'].'" type="text"><br>';
			$i++;
			if ($i % 2 == 0){
				echo "<hr>";
			}
		}
		echo '<input type="submit" name="addfunding" value="Submit"> <input type="reset" value="Reset">';
		echo '</form>';
	}
	
	//A form to edit funding/deobligation to a project.
	public function editfundingform($idproject){
		$project=new Project();
		$projectinfo=$project->getProject($idproject);
		echo "<h2>".$projectinfo['title']."</h2>";
		$fundingsource=new Fundingsource();
		$possiblesources=$fundingsource->getPossibleFundingSources();
		echo '<form action="index.php?action=editfunding&idproject='.$idproject.'" method="post">';
		$i=0;
		$funding="";
		if ($projectinfo['funding']){
			foreach ($projectinfo['funding'] as $value){
				$funding[$value['idFundingSource']] = $value['amount'];
			}
		}else{
			$funding[0]=0;
		}
		foreach ($possiblesources as $value){
			echo '<label for="'.$value['fiscalyear'].'_'.$value['typeOfFunding'].'">FY'.$value['fiscalyear'].' '.$value['typeOfFunding'].'</label> $';
			$sourcetocheck=$value['idFundingSource'];
			$keyforsource=array_key_exists($sourcetocheck, $funding);
			if ($keyforsource){
				echo '<input id="'.$value['idFundingSource'].'" name="'.$value['idFundingSource'].'" value="'.$funding[$sourcetocheck].'" type="text"><br>';
			}else{
				echo '<input id="'.$value['idFundingSource'].'" name="'.$value['idFundingSource'].'" type="text"><br>';
			}
			$i++;
			if ($i % 2 == 0){
				echo "<hr>";
			}
		}
		echo '<input type="submit" name="editfunding" value="Submit"> <input type="reset" value="Reset">';
		echo '</form>';
	}
	

	//Get a project's funding source(s).
	public function getProjectFunding($idproject){
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
	
	//Add funding to a project.
	public function addFundingToProject($idproject,$toadd){
		$connection=Database::getConnection();
		//print_r($toadd);
		foreach ($toadd as $key=>$value){
			$query = "INSERT INTO project_funded VALUES ";
			$query .= "(DEFAULT,".$value.", 0, ".$idproject.",".$key.")";
			//echo $query."<br>";
			if ($connection->query($query)){
				echo "$".number_format($value,2)." added.</br>";
			}else{
				echo "Error :" .$query . "<br>" . $connection->error;
			}
		}
	}
	
	//Edit the funding for a project
	public function editFundingForProject($idproject,$fundstoedit){
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
	

	//Get the amount of funding for a project in a fiscal year
	public function getProjectFiscalYear($idproject, $fiscalyear, $typeoffunding){
		$fundingsource=new Fundingsource();
		$idfunding=$fundingsource->getFundingSource($fiscalyear, $typeoffunding);
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
	
	//add funding/deobligation to a project
	public function addfunding($idproject, $fundstoadd){
		$results=$this->addFundingToProject($idproject,$fundstoadd);
		/*$project=new Project();
		$project->displayProject($idproject);*/
	}
	
	//edit funding/deobligation to a project
	public function editfunding($idproject, $fundstoedit){
		$projectfunds = $this->getProjectFunding($idproject);
		$originalfunds="";
		foreach ($projectfunds as $originalvalue){
			$originalfunds[$originalvalue['idFundingSource']]=$originalvalue['amount'];
		}
		//print_r($originalfunds);
		$changedfunds="";
		foreach ($fundstoedit as $key=>$editvalue){
			//echo $key." ".$editvalue."<br>";
			if ($key<>'editfunding'){
				//echo $key;
				if (array_key_exists($key, $originalfunds)){
					$changedfunds[$key.'.u']=$editvalue;
				}else{
					$changedfunds[$key.'.n']=$editvalue;
				}
			}
		}
		$results=$this->editFundingForProject($idproject,$changedfunds);
		$project=new Project();
		$projectinfo=$project->displayProject($idproject);
	}
	
	public function displayProjectFunding($projectfunding){
		foreach ($projectfunding as $value){
			echo "FY".$value['fiscalYear']." ".$value['typeOfFunding'].": $".number_format($value['amount'],2)."<br>";
		}
	}
	
}