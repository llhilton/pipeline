<?php
/**
 * includes/classes/Projects.php
*
* Class for workings with multiple projects
*
* @version    0.1 2015-04-15
* @package    BEP Pipeline
* @copyright  Copyright (c) 2015 Lisa Hilton
* @license    GNU General Public License
* @since      Since Release 1.0
*/
class Projects{
	public function __construct(){	}
	
	//Get the pipeline funding
	public function getPipeline(){
		$items="";
		$fundingsource=new Fundingsource();
		$fundingids=$fundingsource->getFundingID();
		$i=0;
		foreach ($fundingids as $fundingid){
			$query = "SELECT sum(amount) AS total FROM project_funded WHERE FundingSource_idFundingSource='".$fundingid."'";
			$connection = Database::getConnection();
			$result_obj='';
			$result_obj=$connection->query($query);
			try{
				$result = $result_obj->fetch_array(MYSQLI_ASSOC);
				$items[$i]=$result['total'];
			}
			catch(Exception $e){
				return false;
			}
			$i++;
		}
		return($items);
	}
	
	//Get all basic information on all projects
	public function getProjects(){
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
	
	//Get projects and their regions
	public function getProjectsRegions(){
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
	
	//Filter to get only those projects from a given region
	public function filter_by_value ($array, $index, $category){
		echo "<h2>".$category." Projects</h2>";
		if(is_array($array) && count($array)>0)
		{
			$temparray="";
			foreach($array as $value){
				if ($value['region']==$category){
					$temparray[]=$value;
				}
			}
		}
		return $temparray;
	}
	
	//Get all the regions for projects, using Global as a default
	function getProjectsRegionsArray($projects){
		$regions=$this->getProjectsRegions();
		$projectregions="";
		foreach	($projects as $project){
			$temparray="";
			$idproject=$project['idproject'];
			$temparray[$idproject]=0;
			$tempregion='Global';
			foreach ($regions as $region){
				if ($region['project_idproject']==$idproject){
					if ($tempregion<>$region['region']){
						$temparray[$idproject]+=1;
						$tempregion=$region['region'];
					}
				}
			}
			if ($temparray[$idproject]<>1){
				$projectregions[$idproject]="Global";
			}else{
				$projectregions[$idproject]=$tempregion;
			}
		}
		return $projectregions;
	}
	
	//Get regions for the projects.
	public function combineProjectsRegions($projects,$regions){
		$newarray="";
		foreach ($projects as $project){
			$idproject = $project['idproject'];
			$smallarray="";
			$smallarray=$project;
			$smallarray['region']=$regions[$idproject];
			$newarray[]=$smallarray;
		}
		return $newarray;
	}
	
	//Show projects from a given region
	public function displayProjectsbyRegion($category){
		$projects_array=$this->getProjects();
		$projectfunding = $this->getProjectsFunding();
		$mergedarray = $this->combineProjectsFunding($projects_array, $projectfunding);
		$projectregions=$this->getProjectsRegionsArray($mergedarray);
		$mergedarray=$this->combineProjectsRegions($mergedarray, $projectregions);
		$mergedarray=$this->filter_by_value($mergedarray, 'region', $category);
		$fundingsource = new Fundingsource();
		$numbersources = $fundingsource->getNumberSources(); //Get the number of possible funding sources.
		echo "<table>";
		$this->displayProjectsTableHeading();
		foreach($mergedarray as $key=>$value){
			echo "<tr>";
			echo "<td><a href=\"index.php?action=displayproject&idproject=" . $value['idproject']. "\">". $value['title'] ."</a><br>";
			if ($value['task_number']<>""){
				echo "Task Number: " . $value['task_number'] ."<br>";
			}
			if ($value['unique_id']<>""){
				echo "Unique ID: " . $value['unique_id'] ;
			}
			echo "</td>";
			if (!is_null($value['funding'])){
				foreach ($value['funding'] as $fkey=>$fvalue){
					if ($fvalue<>0 && $fkey<>"idproject"){
						echo "<td>$".number_format($fvalue,2)."</td>";
					}elseif ($fkey=="idproject"){
						echo "";
					}else{
						echo "<td></td>";
					}
				}
			}else{
				for($i=0;$i<$numbersources;$i++){
					echo "<td></td>";
				}
			}
	
			echo "<td>" . $value['notes'] ."</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	
	//Display the headings for the table that lists multiple projects.
	public function displayProjectsTableHeading(){?>
		<tr>
		<th>Title</th><?php 
		//Print out each possible funding source.
		$fundingsource=new Fundingsource();
		$fundingsources=$fundingsource->getPossibleFundingSources();
		foreach ($fundingsources as $source){
			echo "<th> FY";
			echo $source['fiscalyear']. " ";
			echo $source['typeOfFunding'];
			echo "</th>";
		}
		?>
		<th>Notes</th>
		</tr>
	<?php 
	}
	
	
	//Build an array for each funded project that includes a result for each fiscal year
	//and type of funding.
	public function getProjectsFunding(){
		$fundingarray=$this->getProjectsFundingQuery();
		$fundingsource=new Fundingsource();
		$fundingsources=$fundingsource->getPossibleFundingSources();
		$newarray=array();
		if (is_array($fundingarray)){
			foreach ($fundingarray as $value){
				$idproject=$value['project_idproject'];
				$idprojectsource=$value['FundingSource_idFundingSource'];
				$projectarray="";
				foreach ($fundingsources as $source){
					$idfundingsource=$source['idFundingSource'];
					if ($idprojectsource==$idfundingsource){
						$projectarray[$idfundingsource]=$value['amount'];
					}else{
						$projectarray[$idfundingsource]= "";
					}
				}
				$projectarray['idproject']= $idproject;
				//If the project already has a value (for projects with multiple fiscal years)
				if (array_key_exists($idproject,$newarray)){
					$fundingsource = new Fundingsource();
					$numbersources = $fundingsource->getNumberSources(); //Get the number of possible funding sources.
					for($i=1;$i<=$numbersources;$i++){
						$newarray[$idproject][$i]+=$projectarray[$i];
					}
				}else{
					$newarray[$idproject]= $projectarray;
				}
			}
		}
		return $newarray;
	}
	
	
	 //Get projects and their funding sources.
	 public function getProjectsFundingQuery(){
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
	

	//Combine the projects and funding-for-projects arrays
	public function combineProjectsFunding($projects,$funding){
		if (is_array($funding)){
			$newarray="";
			foreach($projects as $idproject=>$project){
				$smallarray="";
				$smallarray=$project;
				if (array_key_exists($idproject, $funding)){
					$smallarray['funding']=$funding[$idproject];
				}else{
					$smallarray['funding']=NULL;
				}
				$newarray[]=$smallarray;
			}
		}
		return($newarray);
	}
	 
}