<?php
/**
 * includes/classes/ProjectCountry.php
 *
 * Class for working with the connection of projects and countries 
 *
 * @version    0.1 2015-04-15
 * @package    BEP Pipeline
 * @copyright  Copyright (c) 2015 Lisa Hilton
 * @license    GNU General Public License
 * @since      Since Release 1.0
 */
class ProjectCountry{
	public function __construct(){	}
	
	//form to add a country to a project
	function addcountryform($idproject){
		$project=new Project();
		$projectinfo=$project->getProject($idproject);
		echo "<h2>".$projectinfo['title']."</h2>";
		$countries=$this->getPossibleCountries();
		echo '<form action="index.php?action=addcountry&idproject='.$idproject.'" method="post">';
		foreach ($countries as $value){
			echo '<input id="'.$value['idcountry'].'" name="country['.$value['idcountry'].']" type="checkbox">'.$value['country'].'<br>';
		}
		echo '<input type="submit" name="addcountry" value="Submit"> <input type="reset" value="Reset">';
		echo '</form>';
	}
	
	//check a multi-dimensional array to see if the value is in it. Couresty of http://stackoverflow.com/questions/4128323/in-array-and-multidimensional-array
	function in_array_r($needle, $haystack, $strict = false) {
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
				return true;
			}
		}
		return false;
	}
	
	//check to see if a country is part of a project and return "checked" if so
	function check_checkbox($countrytocheck, $countriesinproject){
		if ($this->in_array_r($countrytocheck['country'], $countriesinproject)){
			return "checked";
		}
	}
	
	//form to add a country to a project
	function editcountryform($idproject){
		$project=new Project();
		$projectinfo=$project->getProject($idproject);
		echo "<h2>".$projectinfo['title']."</h2>";
		$country=new Country();
		$countries=$country->getPossibleCountries();
		echo '<form action="index.php?action=editcountry&idproject='.$idproject.'" method="post">';
		foreach ($countries as $value){
			echo '<input id="'.$value['idcountry'].'" name="country['.$value['idcountry'].']" type="checkbox" '.$this->check_checkbox($value, $projectinfo['countries']).'>'.$value['country'].'<br>';
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
		$results=$this->addCountryToProject($idproject,$toadd);
		$project=new Project();
		$project->displayProject($idproject);
	}
	
	//Edit the country/countries for a project.
	//It will be easier to drop all previous countries and just add the new ones.
	function editcountry($idproject, $countriestoedit){
		$projectcountries = $this->removeCountriesForProject($idproject);
		if ($countriestoedit){
			$this->addcountry($idproject, $countriestoedit);
		}
	}
	
	//Get a project's country/ies.
	public function getProjectCountry($idproject){
		$connection = Database::getConnection();
		$query="SELECT c.country, c.region, c.idcountry FROM project_country AS p ";
		$query .= "INNER JOIN country AS c ON p.country_idcountry = c.idcountry WHERE p.project_idproject = ". $idproject;
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
	
	//Add countries to a project.
	public function addCountryToProject($idproject,$toadd){
		$connection=Database::getConnection();
		foreach ($toadd as $value){
			if ($value<>'addcountry'){
				$query = "INSERT INTO project_country VALUES ";
				$query .= "(DEFAULT, ".$idproject.",".$value.")";
				if (!$connection->query($query)){
					echo "Error :" .$query . "<br>" . $connection->error;
					die;//This will stop the query. This way, the "Country added" message won't be a lie.
				}
			}
		}
		echo "Country/Countries added successfully<br>\n";
	}
	

	//Delete all countries for a project
	public function removeCountriesForProject($idproject){
		$connection=Database::getConnection();
		$query="DELETE FROM project_country WHERE project_idproject = '".$idproject."'";
		if (!$connection->query($query)){
			echo "Error :" .$query . "<br>" . $connection->error;
		}
	}
	
	//Show the information on the project's countries
	public function displayProjectCountry($projectcountries){
		foreach ($projectcountries as $value){
			echo $value['country'].", <a href=\"index.php?category=".$value['region']."\">".$value['region']."</a><br>";
		}
	}
	
}