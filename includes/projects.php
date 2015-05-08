<?php
require_once('classes/connecti.php');
//require_once('classes/html_table.php');
//echo '<a href="summary.php?action=fullsummary">Full summary</a><br>';
//echo '<a href="summary.php">CTR Summary</a><br>';

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

//Form to edit the basic information on a project.
function editprojectbasicsform($idproject){
	$projectbasics=Database::getProject($idproject);
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
	Database::editProjectBasics($idproject, $projectbasics);
	displayProject($idproject);
}

//if subaward is in the array, return "checked" for the checkboxes
function checksubaward($shortnumber,$shortarray){
	if (in_array($shortnumber, $shortarray)){
		return "checked";
	}
}

//A form to add or edit subawards to a project
function addsubawardsform($idproject){
	$projectinfo=Database::getProject($idproject);
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
	$subawards=Database::getSubawards();
	echo '<form action="index.php?action=addsubawards&idproject='.$idproject.'" method="post">';
	foreach ($subawards as $subaward){
		//echo checksubaward($subaward['shortAwardNumber'], $shorts);
		echo '<input id="'.$subaward['shortAwardNumber'].'" name="subawards[]" value="'.$subaward['shortAwardNumber'].'" type="checkbox" '.checksubaward($subaward['shortAwardNumber'], $shorts).' >'.$subaward['shortAwardNumber'].': '.$subaward['title'].'<br>';
		
	}
	echo '<input type="submit" name="addsubawards" value="Submit"> <input type="reset" value="Reset">';
	echo '</form>';
}

//A form to add a new country to the database
function newcountryform(){
	$regions=array('MENA','MENA-Iraq','South Asia','Southeast Asia','Sub-Saharan Africa','Ukraine','Global');
	echo '<form action="index.php?action=newcountry" method="post">';
	echo '<label for="country">New country</label>';
	echo '<input id="country" name="country" type="text"><br>';
	echo '<select name="region">';
	foreach ($regions as $region){
		echo '<option value="'.region.'">'.$region.'</option>'."\n";
	}
	echo '</select><br>';
	echo '<input type="submit" name="newcountry" value="Submit"> <input type="reset" value="Reset">';
	echo '</form>';
}

//Add a new country to the database
function newcountry($country, $region){
	Database::addnewcountry($country, $region);
}

//A form to add a new country to the database
function addprojectform(){
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
	$possiblesources=Database::getPossibleFundingSources();
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
	$countries=Database::getPossibleCountries();
	foreach ($countries as $value){
		echo '<input id="'.$value['idcountry'].'" name="country['.$value['idcountry'].']" type="checkbox">'.$value['country'].'<br>';
	}
	
	echo '</fieldset><fieldset><legend>Subawards</legend>';
	$subawards=Database::getSubawards();
	foreach ($subawards as $subaward){
		//echo checksubaward($subaward['shortAwardNumber'], $shorts);
		echo '<input id="'.$subaward['shortAwardNumber'].'" name="subawards[]" value="'.$subaward['shortAwardNumber'].'" type="checkbox">'.$subaward['shortAwardNumber'].': '.$subaward['title'].'<br>';
	}
	echo '</fieldset>';
	echo '<input type="submit" name="addproject" value="Submit"> <input type="reset" value="Reset">';
	echo '</form>';
	
}

//Add a project and it's associated information
function addproject($projectinfo){
	$newidproject=Database::addProject($projectinfo['basics']);
	echo $newidproject;
	Database::addFundingToProject($newidproject, $projectinfo['funding']);
	Database::addCountryToProject($newidproject, $projectinfo['country']);
	foreach($projectinfo['subawards'] as $value){
		Database::addSubaward($newidproject, $value);
	}
	echo '<a href="index.php?idproject='.$newidproject.'">View the project</a>';
}

//A form to add funding/deobligation to a project.
function addfundingform($idproject){
	$projectinfo=Database::getProject($idproject);
	echo "<h2>".$projectinfo['title']."</h2>";
	$possiblesources=Database::getPossibleFundingSources();
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
function editfundingform($idproject){
	$projectinfo=Database::getProject($idproject);
	echo "<h2>".$projectinfo['title']."</h2>";
	$possiblesources=Database::getPossibleFundingSources();
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

//form to add a country to a project
function addcountryform($idproject){
	$projectinfo=Database::getProject($idproject);
	echo "<h2>".$projectinfo['title']."</h2>";
	$countries=Database::getPossibleCountries();
	echo '<form action="index.php?action=addcountry&idproject='.$idproject.'" method="post">';
	foreach ($countries as $value){
		echo '<input id="'.$value['idcountry'].'" name="country['.$value['idcountry'].']" type="checkbox">'.$value['country'].'<br>';
	}
	echo '<input type="submit" name="addcountry" value="Submit"> <input type="reset" value="Reset">';
	echo '</form>';
}

//Add subawards to a project
function addsubawards($idproject, $awards){
	Database::deleteProjectSubawards($idproject);
	foreach ($awards as $award){
		//print_r($award);
		Database::addSubaward($idproject, $award);
	} 
	displayProject($idproject);
}

//check a multi-dimensional array to see if the value is in it. Couresty of http://stackoverflow.com/questions/4128323/in-array-and-multidimensional-array
function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}
//check to see if a country is part of a project and return "checked" if so
function check_checkbox($countrytocheck, $countriesinproject){
	if (in_array_r($countrytocheck['country'], $countriesinproject)){
		return "checked";
	}
}

//form to add a country to a project
function editcountryform($idproject){
	$projectinfo=Database::getProject($idproject);
	echo "<h2>".$projectinfo['title']."</h2>";
	$countries=Database::getPossibleCountries();
	echo '<form action="index.php?action=editcountry&idproject='.$idproject.'" method="post">';
	foreach ($countries as $value){
		echo '<input id="'.$value['idcountry'].'" name="country['.$value['idcountry'].']" type="checkbox" '.check_checkbox($value, $projectinfo['countries']).'>'.$value['country'].'<br>';
	}
	echo '<input type="submit" name="editcountry" value="Submit"> <input type="reset" value="Reset">';
	echo '</form>';
}

//add country to a project
function addcountry($idproject, $countriestoadd){
	$i=0;
	foreach ($countriestoadd as $key=>$value){
		$toadd[$i]=array($key,$value);
		$i++;
	}
	$results=Database::addCountryToProject($idproject,$toadd);
	displayProject($idproject);
}

//Edit the country/countries for a project.
//It will be easier to drop all previous countries and just add the new ones.
function editcountry($idproject, $countriestoedit){
	$projectcountries = Database::removeCountriesForProject($idproject);
	if ($countriestoedit){
		addcountry($idproject, $countriestoedit);
	}
	displayProject($idproject);
}

//add funding/deobligation to a project
function addfunding($idproject, $fundstoadd){
	$i=0;
	foreach ($fundstoadd as $key=>$value){
		$toadd[$i]=array($key,$value);
		$i++;
	}
	$results=Database::addFundingToProject($idproject,$toadd);
	displayProject($idproject);
}

//edit funding/deobligation to a project
function editfunding($idproject, $fundstoedit){
	$projectfunds = Database::getProjectFunding($idproject);
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
	$results=Database::editFundingForProject($idproject,$changedfunds);
	displayProject($idproject);
}

//Build an array for each funded project that includes a result for each fiscal year
//and type of funding.
function getProjectsFunding(){
	$fundingarray=Database::getProjectsFunding();
	$fundingsources=Database::getPossibleFundingSources();
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
				for($i=1;$i<=Database::getNumberSources();$i++){
					$newarray[$idproject][$i]+=$projectarray[$i];
				}
			}else{
				$newarray[$idproject]= $projectarray;
			}
		}
	}
	return $newarray;
}

//Combine the projects and funding-for-projects arrays
function combineProjectsFunding($projects,$funding){
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

//Get regions for the projects.
function combineProjectsRegions($projects,$regions){
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

//Get all the regions for projects, using Global as a default
function getProjectsRegions($projects){
	$regions=Database::getProjectsRegions();
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

function cmpregion($a, $b)
{
    return strcmp($a["region"], $b["region"]);
}

function cmptitle($a, $b)
{
	return strcmp($a["title"], $b["title"]);
}

/*//Display multiple projects.
function displayProjects(){
	$projects_array=Database::getProjects();
	$projectfunding = getProjectsFunding();
	$mergedarray = combineProjectsFunding($projects_array, $projectfunding);
	$projectregions=getProjectsRegions($mergedarray);
	$mergedarray=combineProjectsRegions($mergedarray, $projectregions);
	usort($mergedarray,"cmpregion");
	$numbersources = Database::getNumberSources(); //Get the number of possible funding sources.
	echo "<table>";
	displayProjectsTableHeading();
	foreach($mergedarray as $key=>$value){
		echo "<tr>";
		echo "<td><a href=\"?action=displayproject&idproject=" . $value['idproject']. "\">". $value['title'] ."</a><br>";
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
}*/

//Display information on a single project.
function displayProject($idproject){
	$project_array=Database::getProject($idproject);
	echo "<h2>". $project_array['title'] ."</h2>";
	echo "Task Number: " . $project_array['task_number'] ."<br>";
	echo "Unique ID :" . $project_array['unique_id'] ."<br>";
	echo "Notes: " . $project_array['notes'] ."<br>";
	echo '<a href="index.php?action=editprojectbasicsform&idproject='.$project_array['idproject'].'">Edit project information</a>';
	echo "<h3>Funding/Deobligations</h3>";
	if ($project_array['funding']){ 
		foreach ($project_array['funding'] as $value){
			echo "FY".$value['fiscalYear']." ".$value['typeOfFunding'].": $".number_format($value['amount'],2)."<br>";
		}
		echo "<a href=\"index.php?action=editfundingform&idproject=".$project_array['idproject']."\">Edit funding/deobligations</a><br>";
	}else{
		echo "<a href=\"index.php?action=addfundingform&idproject=".$project_array['idproject']."\">Add funding/deobligations</a><br>";
	}
	echo "<h3>Location</h3>";
	if ($project_array['countries']){
		foreach ($project_array['countries'] as $value){
			echo $value['country'].", ".$value['region']."<br>";	
		}
		echo "<a href=\"index.php?action=editcountryform&idproject=".$project_array['idproject']."\">Edit countries</a><br>";
	}else {	
		echo "<a href=\"index.php?action=addcountryform&idproject=".$project_array['idproject']."\">Add country</a><br>";
	}
	echo "<h3>Watson/Impromptu Information</h3>";
	if ($project_array['shortawardnumbers']){
		foreach($project_array['shortawardnumbers'] as $shortaward){
			$watson=Database::getWatsonShort($shortaward['shortawardnumber']);
			echo "<b>".$shortaward['shortawardnumber']."</b><br>";
			foreach ($watson as $watsonvalue){
				echo "Watson Title: ".$watsonvalue['title']."<br>\n";
				echo "Watson Status: ".$watsonvalue['status']."<br>\n";
				echo "Watson Start Date: ".date("m/d/y",strtotime($watsonvalue['startdate']))."<br>\n";
				echo "Watson End Date: ".date("m/d/y",strtotime($watsonvalue['enddate']))."<br>\n";
				echo "Watson Budget: $".number_format($watsonvalue['budget'],2)."<br><br>\n";
			}
			$impromptu="";
			$impromptu=Database::getImpromptuShort($shortaward['shortawardnumber'], 'new');
			if ($impromptu<>""){
				echo "<b>Impromptu Information</b><br>";
				foreach ($impromptu as $impromptuvalue){
					echo "<u>Project String: ".$impromptuvalue['project_string']."</u><br>\n";
					echo "Impromptu Budget: $".number_format($impromptuvalue['total_obligation'],2)."<br>\n";
					echo "Impromptu Expended: $".number_format($impromptuvalue['expended'],2)."<br>\n";
					echo "Impromtu Remaining (unloaded): $".number_format($impromptuvalue['award_reserve'],2)."<br>\n";
				}
			}
			$oldimpromptu="";
			$oldimpromptu=Database::getImpromptuShort($shortaward['shortawardnumber'],'old');
			if ($oldimpromptu<>""){
				echo "<br><b>Old Impromptu Information</b><br>";
				foreach ($oldimpromptu as $impromptuvalue){
					echo "<u>Project String: ".$impromptuvalue['project_string']."</u><br>\n";
					echo "Impromptu Budget: $".number_format($impromptuvalue['total_obligation'],2)."<br>\n";
					echo "Impromptu Expended: $".number_format($impromptuvalue['expended'],2)."<br>\n";
					echo "Impromtu Remaining (unloaded): $".number_format($impromptuvalue['award_reserve'],2)."<br>\n";
				}
			}
		}
	}
	echo '<a href="index.php?action=addsubawardsform&idproject='.$project_array['idproject'].'">Add/Edit subaward(s)</a><br>';
}

//Get only those projects from a given region
function filter_by_value ($array, $index, $category){
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

//Show projects from a given region
function displayProjectsbyRegion($category){
	$projects_array=Database::getProjects();
	$projectfunding = getProjectsFunding();
	$mergedarray = combineProjectsFunding($projects_array, $projectfunding);
	$projectregions=getProjectsRegions($mergedarray);
	$mergedarray=combineProjectsRegions($mergedarray, $projectregions);
	$mergedarray=filter_by_value($mergedarray, 'region', $category);
	$numbersources = Database::getNumberSources(); //Get the number of possible funding sources.
	echo "<table>";
	displayProjectsTableHeading();
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


?>
<!-- <ul>
	<li><a href="index.php">Show all projects</a></li>
	<li><a href="index.php?category=MENA">Show all MENA projects</a></li>
	<li><a href="index.php?category=MENA-Iraq">Show all MENA-Iraq projects</a></li>
	<li><a href="index.php?category=Southeast%20Asia">Show all Southeast Asia projects</a></li>
	<li><a href="index.php?category=Sub-Saharan%20Africa">Show all Sub-Saharan Africa projects</a></li>
	<li><a href="index.php?category=South%20Asia">Show all South Asia projects</a></li>
	<li><a href="index.php?category=Ukraine">Show all Ukraine projects</a></li>
	<li><a href="index.php?category=Global">Show all Global projects</a></li>
	<li><a href="impromptu.php">Import new Impromptu file</a></li>
	<li><a href="watson.php">Import new Watson file</a></li>
	<li><a href="index.php?action=addprojectform">Add a project</a></li>
</ul>
-->
<?php 
/*
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
					$fundstoadd[$key]=filter_var($value,FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
				}
			}
			addfunding($idproject, $fundstoadd);
			break;
		case 'editfundingform': //A form to edit a project's funding.
			editfundingform($idproject);
			break;
		case 'editfunding': //Change the project's funding source(s).
			foreach ($_POST as $key=>$value){
				$fundstoedit[$key]=filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			}
			editfunding($idproject, $fundstoedit);
			break;
		case 'addcountryform': //Form to add a country/countries to a project.
			addcountryform($idproject);
			break;
		case 'editcountryform':
			editcountryform($idproject);
			break;
		case 'addcountry': //Add country/countries to a project.
			foreach ($_POST['country'] as $key=>$value){
				$countriestoadd[$key]=filter_var($value, FILTER_SANITIZE_NUMBER_INT);
			}
			addcountry($idproject, $countriestoadd);
			break;
		case 'editcountry':
			if (isset($_POST['country'])){
				foreach ($_POST['country'] as $key=>$value){
					$countriestoedit[$key]=filter_var($value, FILTER_SANITIZE_NUMBER_INT);
				}
			}else{
				$countriestoedit=FALSE;
			}
			editcountry($idproject,$countriestoedit);
			break;
		case 'addsubawardsform':
			addsubawardsform($idproject);
			break;
		case 'addsubawards':
			if (isset($_POST['subawards'])){
				$subawards="";
				foreach ($_POST['subawards'] as $value){
					$subawards[]=filter_var($value, FILTER_SANITIZE_NUMBER_INT);
				}
			}
			addsubawards($idproject,$subawards);
			break;
		case 'editprojectbasicsform':
			editprojectbasicsform($idproject);
			break;
		case 'editprojectbasics':
			$projectbasics['title']=filter_var($_POST['title'], FILTER_SANITIZE_STRING);
			$projectbasics['unique_id']=filter_var($_POST['unique_id'], FILTER_SANITIZE_STRING);
			$projectbasics['task_number']=filter_var($_POST['task_number'], FILTER_SANITIZE_STRING);
			$projectbasics['notes']=filter_var($_POST['notes'], FILTER_SANITIZE_STRING);
			editprojectbasics($idproject, $projectbasics);
			break;
	}
}elseif(isset($_GET['action']) && !isset($_GET['idproject'])){
	// If there is an action and an idproject, do that action for that project.
	$action = filter_var($_GET['action'], FILTER_SANITIZE_STRING);
	switch ($action){
		case 'newcountryform':
			newcountryform();
			break;
		case 'newcountry':
			$country = $_POST['country'];
			$region = $_POST['region'];
			newcountry($country, $region);
			break;	
		case 'addprojectform':
			addprojectform();
			break;
		case 'addproject':
			$projectinfo['basics']['title']=filter_var($_POST['title'], FILTER_SANITIZE_STRING);
			$projectinfo['basics']['unique_id']=filter_var($_POST['unique_id'], FILTER_SANITIZE_STRING);
			$projectinfo['basics']['task_number']=filter_var($_POST['task_number'], FILTER_SANITIZE_STRING);
			$projectinfo['basics']['notes']=filter_var($_POST['notes'], FILTER_SANITIZE_STRING);
			$numbersources=Database::getNumberSources();
			$j=0;
			for ($i=1;$i<=$numbersources;$i++){
				if ($_POST['funding'][$i]<>""){
					$projectinfo['funding'][$j]=array($i,filter_var($_POST['funding'][$i], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
					$j++;
				}
			}
			$i=0;
			foreach ($_POST['country'] as $key=>$value){
				$projectinfo['country'][$i]=filter_var($key, FILTER_SANITIZE_NUMBER_INT);
				$i++;
			}
			$i=0;
			foreach ($_POST['subawards'] as $value){
				$projectinfo['subawards'][$i]=filter_var($value, FILTER_SANITIZE_NUMBER_INT);
				$i++;
			}
			//print_r($projectinfo);
			addproject($projectinfo);
			break;
				
	}
}else {
	if (isset($_GET['category'])){
		$category = filter_var($_GET['category'], FILTER_SANITIZE_STRING);
		displayProjectsbyRegion($category);
	} else {
	//Display all projects
	displayProjects();
	}
}*/