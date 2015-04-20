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
	<th>Task number</th>
	<th>Unique ID</th>
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

function addfunding($idproject, $fundstoadd){
	$i=0;
	foreach ($fundstoadd as $key=>$value){
		$toadd[$i]=array($key,$value);
	}
	$results=Database::addFundingToProject($idproject,$toadd);
	echo '<a href="projects.php?idproject='.$idproject.'">Return to project.</a>';
}

//Get where projects have FY10 program funds not yet obligated.
function getFiscalYearProject($idproject, $fiscalyear,$typefunding){
	$projectfunding=Database::getProjectFiscalYear($idproject, $fiscalyear,$typefunding);
	if (is_numeric($projectfunding)){
		$projectfunding="$".number_format($projectfunding,2);
	}	
	return $projectfunding;
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
		$projectarray=array('idproject'=>$idproject);
		foreach ($fundingsources as $source){
			$idfundingsource=$source['idFundingSource'];
			$sourcearray="";
			if ($idprojectsource==$idfundingsource){
				$sourcearray=array($idfundingsource=>$value['amount']);
			}else{
				$sourcearray= array($projectarray, $idfundingsource=>"");
			}
			array_push($projectarray, $sourcearray);
			print_r($projectarray);
			echo "<br>";
		}
		$newarray[]=$projectarray;
	}
	return $newarray;
}

//Combine the projects and funding-for-projects arrays
function combineProjectsFunding($projects,$funding){
	foreach($projects as $project){
		
	
		
	}
}

//Display multiple projects.
function displayProjects(){
	$projects_array=Database::getProjects();
	$projectfunding = getProjectsFunding();
	$mergedarray = array_merge($projects_array, $projectfunding);
	print_r($mergedarray);
	//$combinedprojectsarray = combineProjectsFunding($projects_array,$projectfunding);
	echo "<table border=1>";
	displayProjectsTableHeading();
	foreach($projects_array as $value){
		echo "<tr>";
		echo "<td><a href=\"?idproject=" . $value['idproject']. "\">". $value['title'] ."</a></td>";
		
		/*echo "<td>".getFiscalYearProject($value['idproject'], '10','Program')."</td>";
		echo "<td>".getFiscalYearProject($value['idproject'], '10','Impact')."</td>";
		
		echo "<td>".getFiscalYearProject($value['idproject'], '11','Program')."</td>";
		echo "<td>".getFiscalYearProject($value['idproject'], '11','Impact')."</td>";
		
		echo "<td>".getFiscalYearProject($value['idproject'], '12','Program')."</td>";
		echo "<td>".getFiscalYearProject($value['idproject'], '12','Impact')."</td>";
		
		echo "<td>".getFiscalYearProject($value['idproject'], '12-Iraq','Program')."</td>";
		echo "<td>".getFiscalYearProject($value['idproject'], '12-Iraq','Impact')."</td>";
		
		echo "<td>".getFiscalYearProject($value['idproject'], '13','Program')."</td>";
		echo "<td>".getFiscalYearProject($value['idproject'], '13','Impact')."</td>";
		
		echo "<td>".getFiscalYearProject($value['idproject'], '14','Program')."</td>";
		echo "<td>".getFiscalYearProject($value['idproject'], '14','Impact')."</td>";*/
		
		echo "<td>" . $value['task_number'] ."</td>";
		echo "<td>" . $value['unique_id'] ."</td>";
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
