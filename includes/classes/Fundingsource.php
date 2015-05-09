<?php
/**
 * includes/classes/Fundingsource.php
 *
 * Class for adding, editing, and getting various funding source information 
 *
 * @version    0.1 2015-04-15
 * @package    BEP Pipeline
 * @copyright  Copyright (c) 2015 Lisa Hilton
 * @license    GNU General Public License
 * @since      Since Release 1.0
 */
class Fundingsource{
	public function __construct(){	}
	
	//Get ids on funding sources
	public function getFundingID(){
		//clear results
		$items="";
		$connection=Database::getConnection();
		$query='SELECT idFundingSource FROM fundingsource';
		$result_obj="";
		$result_obj=$connection->query($query);
		try{
			while($result = $result_obj->fetch_array(MYSQLI_ASSOC)){
				$items[]=$result['idFundingSource'];
			}
			return($items);
		}
		catch(Exception $e){
			return false;
		}
	}
	
	//Get the types of funding for the summary header
	public function getFiscalYears(){
		$connection=Database::getConnection();
		$query="SELECT fiscalYear, typeOfFunding FROM fundingsource";
		$result_obj="";
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
	
	//Get a certain piece of the funding information
	public function getFundingInformation($toget){
		$connection=Database::getConnection();
		switch($toget){
			case 'fundingamount':
				$query="SELECT fundingamount FROM fundingsource";
				break;
			case 'spent':
				$query="SELECT spent FROM fundingsource";
				break;
			case 'obligation':
				$query="SELECT obligation FROM fundingsource";
				break;
			case 'impactfee':
				$query="SELECT impactfee FROM fundingsource";
				break;
			case 'remaining':
				$query="SELECT (fundingamount - spent - obligation - IFNULL(impactfee,0)) as remaining FROM fundingsource";
				break;
		}
		$result_obj="";
		$result_obj=$connection->query($query);
		try{
			while($result = $result_obj->fetch_array(MYSQLI_ASSOC)){
				$items[]=$result[$toget];
			}
			return($items);
		}
		catch(Exception $e){
			return false;
		}
		
	}
	
	//Get basic info on all possible funding sources for a project.
	public function getPossibleFundingSources(){
		$items='';
		$connection=Database::getConnection();
		$query="SELECT idFundingSource, fiscalyear, typeOfFunding from fundingsource";
		$results='';
		$results=$connection->query($query);
		try{
			while($result = $results->fetch_array(MYSQLI_ASSOC)){
				$items[]=$result;
			}
			return($items);
		}
		catch(Exception $e){
			return false;
		}
	}
	
	//Get all the information on the funding sources
	public function getFullFundingSources(){
		$connection=Database::getConnection();
		$query="SELECT * FROM fundingsource";
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
	

	//Get the idFundingSource for a given fiscal year and type of funding
	public function getFundingSource($fy, $typefunding){
		$connection=Database::getConnection();
		$query = "SELECT idFundingSource FROM fundingsource WHERE fiscalYear = '".$fy;
		$query.= "' AND typeOfFunding = '".$typefunding."'";
		$result_obj='';
		$result_obj=$connection->query($query);
		try{
			$item = $result_obj->fetch_array(MYSQL_NUM);
			return $item[0];
		}
		catch(Exception $e){
			return false;
		}
	}
	
	//Add a funding source to the database
	public function addFundingSource($fundinginfo){
		extract($fundinginfo);
		$connection=Database::getConnection();
		$query="INSERT INTO fundingsource VALUES (";
		$query.="DEFAULT, ";
		$query.="'".$fiscalyear."', ";
		$query.="'".$typeoffunding."', ";
		$query.="'".$fundingamount."', ";
		$query.="'".$spent."', ";
		$query.="'".$obligation."', ";
		$query.="'".$impactfee."')";
		$result_obj='';
		if (!$connection->query($query)){
			echo "Error :" .$query . "<br>" . $connection->error;
		}else{
			echo "Funding source added.<br>\n";
		}
	}
	
	//update the funding totals and information
	public function updatefunding($idFundingSource,$value){
		$connection=Database::getConnection();
		$query="UPDATE fundingsource SET ";
		$query.="fundingamount='".$value['funding']."', ";
		$query.="spent='".$value['spent']."', ";
		$query.="obligation='".$value['obligation']."', ";
		if ($value['impactfee']==""){
			$query.="impactfee=NULL ";
		}else{
			$query.="impactfee='".$value['impactfee']."' ";
		}
		$query .= "WHERE idFundingSource = '".$idFundingSource."'";
		$result_obj='';
		$result_obj=$connection->query($query);
		if (!$connection->query($query)){
			echo "Error :" .$query . "<br>" . $connection->error;
		}else{
			echo "Funding updated successfully.<br>\n";
		}
	}
	
	//get the number of possible funding sources
	public function getNumberSources(){
		$connection = Database::getConnection();
		$item="";
		$query = "SELECT COUNT(*) FROM fundingsource";
		$result_obj=$connection->query($query);
		try{
			$item = $result_obj->fetch_array(MYSQL_NUM);
			return $item[0];
		}
		catch(Exception $e){
			return false;
		}
	}
	
	//Form to add a funding source
	public function addFundingSourceForm(){
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
	
	//Form to edit the funding source totals
	public function edittotalsform(){
		$fundingsources=$this->getFullFundingSources();
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
	public function edittotals($newtotals){
		foreach($newtotals as $key=>$value){
			$this->updatefunding($key,$value);
		}
	}
}