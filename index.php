<?php
/**
 * index.php
 *
 * Entry page for BEP pipeline view
 *
 * @version    0.1 2015-04-15
 * @package    BEP Pipeline
 * @copyright  Copyright (c) 2015 Lisa Hilton
 * @license    GNU General Public License
 * @since      Since Release 1.0
 */
require_once('includes/menu.php');
require_once('includes/projects.php');
require_once('includes/summary.php');
//require_once('includes/classes/connecti.php');
//require_once('includes/classes/watson.php');
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php //echo $title;?>BEP Pipeline</title>
<link rel="stylesheet" type="text/css" href="http://nova.umuc.edu/~ct463b14/pipeline.css">
</head>
<body>
<header>
<h1>BEP Pipeline</h1>
</header>
<nav>
<?php displayMenu();?>
</nav>
<div id="main">
<?php

if (isset($_GET['action'])){//If there's an action, take that. 
	if(isset($_GET['idproject'])){// If there is an idproject, get that
		$idproject = filter_var($_GET['idproject'], FILTER_SANITIZE_NUMBER_INT);
	}
	$action = filter_var($_GET['action'], FILTER_SANITIZE_STRING);
	switch ($action){
		case 'displayproject':
			displayProject($idproject);
			break;
		case 'fullsummary':
			displayFullSummary();
			break;
		case 'edittotalsform':
			edittotalsform();
			break;
		case 'edittotals':
			$newtotals=array();
			foreach($_POST['funding'] as $key=>$value){
				$key=filter_var($key, FILTER_SANITIZE_NUMBER_INT);
				$newtotals[$key]['funding']=filter_var($value,FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			}
			foreach($_POST['spent'] as $key=>$value){
				$key=filter_var($key, FILTER_SANITIZE_NUMBER_INT);
				$newtotals[$key]['spent']=filter_var($value,FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			}
			foreach($_POST['obligation'] as $key=>$value){
				$key=filter_var($key, FILTER_SANITIZE_NUMBER_INT);
				$newtotals[$key]['obligation']=filter_var($value,FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			}
			foreach($_POST['impactfee'] as $key=>$value){
				$key=filter_var($key, FILTER_SANITIZE_NUMBER_INT);
				$newtotals[$key]['impactfee']=filter_var($value,FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			}
			edittotals($newtotals);
			break;
		case 'addfundingsourceform':
			addFundingSourceForm();
			break;
		case 'addfundingsource':
			$fundinginfo="";
			$fundinginfo['fiscalyear']=filter_var($_POST['fiscalyear'], FILTER_SANITIZE_STRING);
			$fundinginfo['typeoffunding']=filter_var($_POST['typeoffunding'], FILTER_SANITIZE_STRING);
			$fundinginfo['fundingamount']=filter_var($_POST['fundingamount'],FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$fundinginfo['spent']=filter_var($_POST['spent'],FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$fundinginfo['obligation']=filter_var($_POST['obligation'],FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$fundinginfo['impactfee']=filter_var($_POST['impactfee'],FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			addFundingSource($fundinginfo);
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
		case 'newcountryform':
			newcountryform();
			break;
		case 'newcountry':
			$country = $_POST['country'];
			$region = $_POST['region'];
			newcountry($country, $region);
			break;
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
				addsubawards($idproject,$subawards);
			}else{
				displayProject($idproject);
			}
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
}elseif (isset($_GET['category'])){//If no action but the category, show those projects
	$category = filter_var($_GET['category'], FILTER_SANITIZE_STRING);
	displayProjectsbyRegion($category);
}else{//If no action, the default should be to show the CTR summary
	displayCTRSummary();
}

?>
</div>
<footer>
(c) 2015 by Lisa Hilton
</footer>
</body>
</html>