<?php
/**
 * summary.php
 *
 * Summary of funding file
 *
 * @version    0.1 2015-04-15
 * @package    BEP Pipeline
 * @copyright  Copyright (c) 2015 Lisa Hilton
 * @license    GNU General Public License
 * @since      Since Release 1.0
 */

require_once('classes/connecti.php');

/*echo '<a href="summary.php?action=fullsummary">Full summary</a><br>';
echo '<a href="summary.php">CTR Summary</a><br>';
echo '<a href="projects.php">Projects list</a><br>';
*/
//Form to add a funding source
function addFundingSourceForm(){
	echo '<form action="index.php?action=addfundingsource" method="post">';
		echo '<fieldset><legend>Add Funding Source</legend>';
		echo '<label for="fiscalyear">Fiscal Year: FY</label>';
		echo '<input id="fiscalyear" name="fiscalyear" type"text"><br>';
		echo '<label for="typeoffunding">Type of Funding</label>';
		echo '<input type="radio" name="typeoffunding" value="Program" checked> Program';
		echo '<input type="radio" name="typeoffunding" value="Impact"> Impact <br>';
		echo '<label for="fundingamount">Funding amount $</label>';
		echo '<input id="fundingamount" name="fundingamount" type"text"><br>';
		echo '<label for="spent">Spent $</label>';
		echo '<input id="spent" name="spent" type"text"><br>';
		echo '<label for="obligation">Obligation $</label>';
		echo '<input id="obligation" name="obligation" type"text""><br>';
		echo '<label for="impactfee">Impact fee $</label>';
		echo '<input id="impactfee" name="impactfee" type"text"><br>';
		echo '</fieldset>';
	echo '<input type="submit" name="addfundingsource" value="Submit"> <input type="reset" value="Reset">';
	echo '</form>';
}

//Add a funding source
function addFundingSource($fundinginfo){
	Database::addFundingSource($fundinginfo);
	displayFullSummary();
}

//Form to edit the funding source totals
function edittotalsform(){
	$fundingsources=Database::getFullFundingSources();
	//print_r($fundingsources);
	$i=0;
	echo '<form action="index.php?action=edittotals" method="post">';
	foreach ($fundingsources as $value){
		echo '<fieldset><legend>FY'.$value['fiscalYear'].' '.$value['typeOfFunding'].'</legend>';
		echo '<label for="fundingamount">Funding amount $</label>';
		echo '<input id="'.$value['fundingamount'].'" name="funding['.$value['idFundingSource'].']" type"text" value="'.$value['fundingamount'].'"><br>';
		echo '<label for="spent">Spent $</label>';
		echo '<input id="'.$value['spent'].'" name="spent['.$value['idFundingSource'].']" type"text" value="'.$value['spent'].'"><br>';
		echo '<label for="obligation">Obligation $</label>';
		echo '<input id="'.$value['obligation'].'" name="obligation['.$value['idFundingSource'].']" type"text" value="'.$value['obligation'].'"><br>';
		echo '<label for="impactfee">Impact fee $</label>';
		echo '<input id="'.$value['impactfee'].'" name="impactfee['.$value['idFundingSource'].']" type"text" value="'.$value['impactfee'].'"><br>';
		echo '</fieldset>';
	}
	echo '<input type="submit" name="edittotals" value="Submit"> <input type="reset" value="Reset">';
	echo '</form>';
}

//Edit the funding totals
function edittotals($newtotals){
	foreach($newtotals as $key=>$value){
		Database::updatefunding($key,$value);
	}
	displayFullSummary();
}

//Calculate the free and clear amount
function calculateFreeAndClear($remaining, $pipeline){
	$numberofcells=count($remaining);
	$freeandclear="";
	for ($i=0;$i<$numberofcells;$i++){
		$freeandclear[$i]=$remaining[$i]-$pipeline[$i];
	}
	return $freeandclear;
}

//show the heading of the table for the summary
function showSummaryHeading(){
	echo "<table><tr>";
	echo "<th>BEP Pipeline</th>";
	$fiscalyears = Database::getFiscalYears();
	foreach ($fiscalyears as $value){
		echo "<th>FY".$value['fiscalYear']." ".$value['typeOfFunding']."</th>";
	}
	echo "<th>Total</th>";
	echo "</tr>";
}

//Show the full budgeted amounts
function showFullBudgeted(){
	echo "<tr>";
	echo "<td>Budgeted amount</td>";
	$budgeted = Database::getBudgeted();
	foreach ($budgeted as $value){
		echo "<td>$".number_format($value,2)."</td>";
	}
	echo "<td>$".number_format(array_sum($budgeted),2)."</td>";
	echo "</tr>";
}

//Show the full spent amounts
function showFullSpent(){
	echo "<tr>";
	echo "<td>Spent to date</td>";
	$spent = Database::getSpent();
	foreach ($spent as $value){
		echo "<td>$".number_format($value,2)."</td>";
	}
	echo "<td>$".number_format(array_sum($spent),2)."</td>";
	echo "</tr>";
}

//Show the full obligated amounts
function showFullObligated(){
	echo "<tr>";
	echo "<td>Obligated to date</td>";
	$obligated = Database::getObligated();
	foreach ($obligated as $value){
		echo "<td>$".number_format($value,2)."</td>";
	}
	echo "<td>$".number_format(array_sum($obligated),2)."</td>";
	echo "</tr>";
}

//Show the full impact fee amounts
function showFullImpactFee(){
	echo "<tr>";
	echo "<td>Impact fee</td>";
	$impactfee= Database::getImpactFee();
	foreach ($impactfee as $value){
		if ($value>0){
			echo "<td>$".number_format($value,2)."</td>";
		}else{
			echo "<td></td>";
		}
	}
	echo "<td>$".number_format(array_sum($impactfee),2)."</td>";
	echo "</tr>";
}

//Show the remaining amounts
function showFullRemaining($remaining){
	echo "<tr>";
	echo "<td>Remaining</td>";
	foreach ($remaining as $value){
		echo "<td>$".number_format($value,2)."</td>";
	}
	echo "<td>$".number_format(array_sum($remaining),2)."</td>";
	echo "</tr>";
}

//Show the full pipeline funding
function showFullPipeline($pipelinefunding){
	echo "<tr>";
	echo "<td>Pipeline</td>";
	foreach ($pipelinefunding as $value){
		echo "<td>$".number_format($value,2)."</td>";
	}
	echo "<td>$".number_format(array_sum($pipelinefunding),2)."</td>";
	echo "</tr>";
}


//show the free and clear amount
function showFullFreeAndClear($freeandclear){
	echo "<tr>";
	echo "<td>Free and Clear</td>";
	foreach ($freeandclear as $value){
		echo "<td>$".number_format($value,2)."</td>";
	}
	echo "<td>$".number_format(array_sum($freeandclear),2)."</td>";
	echo "</tr>";
	echo "</table>";
}

//show the heading of the table for the CTR summary
function showCTRHeading(){
	echo "<table><tr>";
	echo "<th>BEP Pipeline</th>";
	$fiscalyears = Database::getFiscalYears();
	$i=0;
	foreach ($fiscalyears as $value){
		if ($i%2==0){
			echo "<th>FY".$value['fiscalYear']."</th>";
		}
		$i++;
	}
	echo "<th>Total</th>";
	echo "</tr>";
}

//Show the CTR budgeted amounts
function showCTRBudgeted(){
	echo "<tr>";
	echo "<td>Budgeted amount</td>";
	$budgeted = Database::getBudgeted();
	$sum=0;
	for ($i=0;$i<Database::getNumberSources();$i++){
		if ($i%2==0){
			$sum=$budgeted[$i];
		}else{
			$sum+=$budgeted[$i];
			echo "<td>$".number_format($sum,2)."</td>";
		}
	}
	echo "<td>$".number_format(array_sum($budgeted),2)."</td>";
	echo "</tr>";
}

//Show the full CTR amounts
function showCTRSpent(){
	echo "<tr>";
	echo "<td>Spent to date</td>";
	$spent = Database::getSpent();
	$sum=0;
	for ($i=0;$i<Database::getNumberSources();$i++){
		if ($i%2==0){
			$sum=$spent[$i];
		}else{
			$sum+=$spent[$i];
			echo "<td>$".number_format($sum,2)."</td>";
		}
	}
	echo "<td>$".number_format(array_sum($spent),2)."</td>";
	echo "</tr>";
}

//Calculate the amount obligated (including impact fee) for CTR version
function calculateObligated($obligated, $impactfee){
	$numberofcells=count($obligated);
	$total="";
	for ($i=0;$i<$numberofcells;$i++){
		$total[$i]=$obligated[$i]+$impactfee[$i];
	}
	return $total;
}

//Show the full obligated amounts
function showCTRObligated(){
	echo "<tr>";
	echo "<td>Obligated to date</td>";
	$obligated = Database::getObligated();
	$impactfee=Database::getImpactFee();
	$totalobligated=calculateObligated($obligated,$impactfee);
	$sum=0;
	for ($i=0;$i<Database::getNumberSources();$i++){
		if ($i%2==0){
			$sum=$totalobligated[$i];
		}else{
			$sum+=$totalobligated[$i];
			echo "<td>$".number_format($sum,2)."</td>";
		}
	}
	echo "<td>$".number_format(array_sum($totalobligated),2)."</td>";
	echo "</tr>";
}

//Show the remaining amounts for CTR
function showCTRRemaining($remaining){
	echo "<tr>";
	echo "<td>Remaining</td>";
	$sum=0;
	for ($i=0;$i<Database::getNumberSources();$i++){
		if ($i%2==0){
			$sum=$remaining[$i];
		}else{
			$sum+=$remaining[$i];
			echo "<td>$".number_format($sum,2)."</td>";
		}
	}
	echo "<td>$".number_format(array_sum($remaining),2)."</td>";
	echo "</tr>";
}

//Show the CTR pipeline funding
function showCTRPipeline($pipelinefunding){
	echo "<tr>";
	echo "<td>Pipeline</td>";
	$sum=0;
	for ($i=0;$i<Database::getNumberSources();$i++){
		if ($i%2==0){
			$sum=$pipelinefunding[$i];
		}else{
			$sum+=$pipelinefunding[$i];
			echo "<td>$".number_format($sum,2)."</td>";
		}
	}echo "<td>$".number_format(array_sum($pipelinefunding),2)."</td>";
	echo "</tr>";
}

//show the CTR free and clear amount
function showCTRFreeAndClear($freeandclear){
	echo "<tr>";
	echo "<td>Free and Clear</td>";
$sum=0;
	for ($i=0;$i<Database::getNumberSources();$i++){
		if ($i%2==0){
			$sum=$freeandclear[$i];
		}else{
			$sum+=$freeandclear[$i];
			echo "<td>$".number_format($sum,2)."</td>";
		}
	}
	echo "<td>$".number_format(array_sum($freeandclear),2)."</td>";
	echo "</tr>";
	echo "</table>";
}

//Display the pipeline summary for CTR
function displayCTRSummary(){
	showCTRHeading();
	showCTRBudgeted();
	showCTRSpent();
	showCTRObligated();
	$remaining=Database::getRemaining();
	showCTRRemaining($remaining);
	$pipelinefunding=Database::getPipeline();
	showCTRPipeline($pipelinefunding);
	$freeandclear=calculateFreeAndClear($remaining, $pipelinefunding);
	showCTRFreeAndClear($freeandclear);
}


//Display the full summary 
function displayFullSummary(){
	showSummaryHeading();
	showFullBudgeted();
	showFullSpent();
	showFullObligated();
	showFullImpactFee();
	$remaining=Database::getRemaining();
	showFullRemaining($remaining);
	$pipelinefunding=Database::getPipeline();
	showFullPipeline($pipelinefunding);	
	$freeandclear=calculateFreeAndClear($remaining, $pipelinefunding);	
	showFullFreeAndClear($freeandclear);	
}

/*
if (isset($_GET['action'])){
	$action=$_GET['action'];
	switch ($action){
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
	}
}else{
	displayCTRSummary();
}


*/		