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

spl_autoload_register( 'autoload' );
  
  /**
   * autoload
   *
   * @author Joe Sexton <joe.sexton@bigideas.com>
   * @param  string $class
   * @param  string $dir
   * @return bool
   */
  function autoload( $class, $dir = null ) {
 
    if ( is_null( $dir ) )
      $dir = '../';
 
    foreach ( scandir( $dir ) as $file ) {
 
      // directory?
      if ( is_dir( $dir.$file ) && substr( $file, 0, 1 ) !== '.' )
        autoload( $class, $dir.$file.'/' );
 
      // php file?
      if ( substr( $file, 0, 2 ) !== '._' && preg_match( "/.php$/i" , $file ) ) {
 
        // filename matches class?
        if ( str_replace( '.php', '', $file ) == $class || str_replace( '.class.php', '', $file ) == $class ) {
 
            include $dir . $file;
        }
      }
    }
  }
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
		case 'displayproject': //Display the information on a project
			$project=new Project();
			$project->displayProject($idproject);
			break;
		case 'fullsummary': //Display the full summary of where funds are
			$summary=new Summary();
			$summary->displaySummary('full');
			break;
		case 'edittotalsform': //Form to edit the funding sources
			$fundingsource=new Fundingsource();
			$fundingsource->edittotalsform();
			break;
		case 'edittotals': //Edit the funding sources
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
			$fundingsource=new Fundingsource();
			$fundingsource->edittotals($newtotals);
			break;
		case 'addfundingsourceform': //Form to add a new funding source
			$fundingsource=new Fundingsource();
			$fundingsource->addFundingSourceForm();
			break;
		case 'addfundingsource': //add a new funding source
			$fundinginfo="";
			$fundinginfo['fiscalyear']=filter_var($_POST['fiscalyear'], FILTER_SANITIZE_STRING);
			$fundinginfo['typeoffunding']=filter_var($_POST['typeoffunding'], FILTER_SANITIZE_STRING);
			$fundinginfo['fundingamount']=filter_var($_POST['fundingamount'],FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$fundinginfo['spent']=filter_var($_POST['spent'],FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$fundinginfo['obligation']=filter_var($_POST['obligation'],FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$fundinginfo['impactfee']=filter_var($_POST['impactfee'],FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$fundingsource=new Fundingsource();
			$fundingsource->addFundingSource($fundinginfo);
			break;
		case 'addprojectform': //Form to add a new project
			$project=new Project();
			$project->addprojectform();
			break;
		case 'addproject': //Add a new project
			//Add the basic information and get the project ID
			$projectinfo['basics']['title']=filter_var($_POST['title'], FILTER_SANITIZE_STRING);
			$projectinfo['basics']['unique_id']=filter_var($_POST['unique_id'], FILTER_SANITIZE_STRING);
			$projectinfo['basics']['task_number']=filter_var($_POST['task_number'], FILTER_SANITIZE_STRING);
			$projectinfo['basics']['notes']=filter_var($_POST['notes'], FILTER_SANITIZE_STRING);
			$project=new Project();
			$idproject=$project->addproject($projectinfo);
			
			//Add the funding to the new project
			$fundingsource=new Fundingsource();
			$numbersources=$fundingsource->getNumberSources();
			for ($i=1;$i<=$numbersources;$i++){
				if ($_POST['funding'][$i]<>""){
					$funding[$i]=filter_var($_POST['funding'][$i], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
				}
			}
			$projectfunding=new ProjectFunding();
			$projectfunding->addfunding($idproject, $funding);
			
			$i=0;
			foreach ($_POST['country'] as $key=>$value){
				$country[$i]=filter_var($key, FILTER_SANITIZE_NUMBER_INT);
				$i++;
			}
			//print_r($country);
			$projectcountry=new ProjectCountry();
			$projectcountry->addCountryToProject($idproject, $country);
			
			$i=0;
			foreach ($_POST['subawards'] as $value){
				$subawards[$i]=filter_var($value, FILTER_SANITIZE_NUMBER_INT);
				$i++;
			}
			if (is_array($subawards)){
				$subawardsobj=new Subawards();
				$subawardsobj->addSubaward($idproject, $subawards);
			}
			
			$project->displayProject($idproject);
			
			break;
		case 'newcountryform': //Form to add a new country
			$country=new Country();
			$country->newcountryform();
			break;
		case 'newcountry': //Add a new country
			$country = $_POST['country'];
			$region = $_POST['region'];
			$countryobject=new Country();
			$countryobject->addnewcountry($country, $region);
			break;
		case 'addfundingform': //A form to add funding to a project.
			$projectfunding=new ProjectFunding();
			$projectfunding->addfundingform($idproject);
			break;
		case 'addfunding': //Add the submitted funding to a project.
			$fundstoadd='';
			//get where the funding is being added
			foreach ($_POST as $key=>$value){
				if ($value<>"" && $key<>'addfunding'){
					$fundstoadd[$key]=filter_var($value,FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
				}
			}
			$projectfunding=new ProjectFunding();
			$projectfunding->addfunding($idproject, $fundstoadd);
			$project=new Project();
			$project->displayProject($idproject);
			break;
		case 'editfundingform': //A form to edit a project's funding.
			$projectfunding=new ProjectFunding();
			$projectfunding->editfundingform($idproject);
			break;
		case 'editfunding': //Change the project's funding source(s).
			foreach ($_POST as $key=>$value){
				$fundstoedit[$key]=filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			}
			$projectfunding=new ProjectFunding();
			$projectfunding->editfunding($idproject, $fundstoedit);
			$project= new Project();
			$project->displayProject($idproject);
			break;
		case 'addcountryform': //Form to add a country/countries to a project.
			$projectcountry=new ProjectCountry();
			$projectcountry->addcountryform($idproject);
			break;
		case 'editcountryform': //Form to edit the country/ies on a project
			$projectcountry = new ProjectCountry();
			$projectcountry->editcountryform($idproject);
			break;
		case 'addcountry': //Add country/countries to a project.
			foreach ($_POST['country'] as $key=>$value){
				$countriestoadd[$key]=filter_var($value, FILTER_SANITIZE_NUMBER_INT);
			}
			$projectcountry = new ProjectCountry();
			$projectcountry->addcountry($idproject, $countriestoadd);
			break;
		case 'editcountry': //Edit a project's countries
			if (isset($_POST['country'])){
				foreach ($_POST['country'] as $key=>$value){
					$countriestoedit[$key]=filter_var($value, FILTER_SANITIZE_NUMBER_INT);
				}
			}else{
				$countriestoedit=FALSE;
			}
			$projectcountry=new ProjectCountry();
			$projectcountry->editcountry($idproject,$countriestoedit);
			break;
		case 'addsubawardsform': //Form to add subwards to a project
			$subawards=new Subawards();
			$subawards->addsubawardsform($idproject);
			break;
		case 'addsubawards': //Add subawards to a project
			if (isset($_POST['subawards'])){
				$subawards=array();
				foreach ($_POST['subawards'] as $value){
					$subawards[]=filter_var($value, FILTER_SANITIZE_NUMBER_INT);
				}
				$subawardsobj=new Subawards();
				if (count($subawards)>0){
					$subawardsobj->addsubawards($idproject,$subawards);
				}else{
					$subawardsobj->deleteProjectSubawards($idproject);
				}
			}else{
				$subawardsobj=new Subawards();
				$subawardsobj->deleteProjectSubawards($idproject);
			}
			$project=new Project();
			$project->displayProject($idproject);
			break;
		case 'editprojectbasicsform': //Form to edit the basics of a project
			$project=new Project();
			$project->editprojectbasicsform($idproject);
			break;
		case 'editprojectbasics': //Edit a project's basics
			$projectbasics['title']=filter_var($_POST['title'], FILTER_SANITIZE_STRING);
			$projectbasics['unique_id']=filter_var($_POST['unique_id'], FILTER_SANITIZE_STRING);
			$projectbasics['task_number']=filter_var($_POST['task_number'], FILTER_SANITIZE_STRING);
			$projectbasics['notes']=filter_var($_POST['notes'], FILTER_SANITIZE_STRING);
			$project=new Project();
			$project->editprojectbasics($idproject, $projectbasics);
			break;
	}
}elseif (isset($_GET['category'])){//If no action but the category, show those projects
	$category = filter_var($_GET['category'], FILTER_SANITIZE_STRING);
	$projects=new Projects();
	$projects->displayProjectsbyRegion($category);
}else{//If no action, the default should be to show the CTR summary
	$summary=new Summary();
	$summary->displaySummary('CTR');
}

?>
</div>
<footer>
(c) 2015 by Lisa Hilton
</footer>
</body>
</html>