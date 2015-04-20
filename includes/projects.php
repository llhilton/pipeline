<?php
require_once('classes/connecti.php');
//require_once('classes/html_table.php');
//$connection = Database::getConnection();

//Display the headings for the table that lists multiple projects. 
function displayProjectsTableHeading(){?>
	<tr>
	<th>Title</th><?php 
	//Print out each possible funding source.
	$fundingsources = Database::getPossibleFundingSources();
	foreach ($fundingsources as $source){
		echo "<th> FY";
		echo $source['fiscalyear']. " ";
		echo $source['typeOfFunding'];
		echo "</th>";
	}
	?>
	<!-- <th>Task number</th>
	<th>Unique ID</th> -->
	<th>Notes</th>
	</tr>
<?php 
}

//A form to add funding to a project.
function addfundingform($idproject){
	$projectinfo=Database::getProject($idproject);
	echo "<h1>".$projectinfo['title']."</h1>";
	$possiblesources=Database::getPossibleFundingSources();
	echo '<form action="projects.php?action=addfunding&idproject='.$idproject.'" method="post">';
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

//add funding to a project
function addfunding($idproject, $fundstoadd){
	$i=0;
	foreach ($fundstoadd as $key=>$value){
		$toadd[$i]=array($key,$value);
	}
	$results=Database::addFundingToProject($idproject,$toadd);
	echo '<a href="projects.php?idproject='.$idproject.'">Return to project.</a>';
}

//Build an array for each funded project that includes a result for each fiscal year
//and type of funding.
function getProjectsFunding(){
	$fundingarray=Database::getProjectsFunding();
	$fundingsources=Database::getPossibleFundingSources();
	$newarray="";
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
		$newarray[$idproject]= $projectarray;
	}
	return $newarray;
}

//Combine the projects and funding-for-projects arrays
function combineProjectsFunding($projects,$funding){
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
	return($newarray);
}

//Display multiple projects.
function displayProjects(){
	$projects_array=Database::getProjects();
	$projectfunding = getProjectsFunding();
	$mergedarray = combineProjectsFunding($projects_array, $projectfunding);
	$numbersources = Database::getNumberSources(); //Get the number of possible funding sources.
//	print_r($mergedarray);
	echo "<table border=1>";
	displayProjectsTableHeading();
	foreach($mergedarray as $key=>$value){
		echo "<tr>";
		echo "<td><a href=\"?idproject=" . $value['idproject']. "\">". $value['title'] ."</a><br>";
		if ($value['task_number']<>""){
			echo "Task Number: " . $value['task_number'] ."<br>";
		}
		if ($value['unique_id']<>""){
			echo "Unique ID: " . $value['unique_id'] ;
		}
		echo "</td>";
		//print_r($value);
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

//Display information on a single project.
function displayProject($project_array){
	echo "Title: ". $project_array['title'] ."<br>";
	echo "Task Number: " . $project_array['task_number'] ."<br>";
	echo "Unique ID :" . $project_array['unique_id'] ."<br>";
	echo "Notes: " . $project_array['notes'] ."<br>";
	echo "<a href=\"projects.php?action=addfundingform&idproject=".$project_array['idproject']."\">Add funding</a>";
}

?>
<ul>
	<li><a href="projects.php">Show all projects</a>
	<li><a href="projects.php?action=MENA">Show all MENA projects</a>
</ul>

<?php 

//If there is an idproject but no action, show the project information
if (isset($_GET['idproject']) && (!isset($_GET['action']))){
	$idproject=filter_var($_GET['idproject'], FILTER_SANITIZE_NUMBER_INT);
	$projectinfo=Database::getProject($idproject);
	displayProject($projectinfo);
	//Need to add links for editing the projects and adding new details.
}elseif(isset($_GET['action']) && isset($_GET['idproject'])){
	// If there is an action and an idproject, do that action for that project.
	$action = filter_var($_GET['action'], FILTER_SANITIZE_STRING);
	$idproject = filter_var($_GET['idproject'], FILTER_SANITIZE_NUMBER_INT);
	switch ($action){
		case 'addfundingform': //A form to add funding to a project.
			addfundingform($idproject);
			break;
		case 'addfunding': //Add the submitted funding to a project.
			$fundstoadd='';
			//get where the funding is being added
			foreach ($_POST as $key=>$value){
				if ($value<>"" && $key<>'addfunding'){
					$fundstoadd[$key]=filter_var($value,FILTER_SANITIZE_NUMBER_FLOAT);
				}
			}
			addfunding($idproject, $fundstoadd);
			break;
	}
}else{ 
	//Display all projects
	displayProjects();
}
