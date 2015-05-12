<?php
/**
 * includes/classes/Country.php
*
* Class for working with the country table (not the project_country table).
*
* @version    0.1 2015-04-15
* @package    BEP Pipeline
* @copyright  Copyright (c) 2015 Lisa Hilton
* @license    GNU General Public License
* @since      Since Release 1.0
*/
class Country{
	public function __construct(){	}
	
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
	public function addnewcountry($country, $region){
		$connection = Database::getConnection();
		$query="INSERT INTO country VALUES (DEFAULT, '".$country."', '".$region."')";
		if (!$connection->query($query)){
			echo "Error :" .$query . "<br>" . $connection->error;
		}else{
			echo "Country added successfully.<br>\n";
		}
	}
	
	// Get all the countries
	public function getPossibleCountries(){
		$connection=Database::getConnection();
		$query = "SELECT * FROM country ORDER BY country ASC";
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
	
	//Get an array of all the countries with their IDs as the key.
	function getIdCountries(){
		$connection = Database::getConnection();
		$query = "SELECT idcountry, country FROM country";
		$result_obj="";
		$items=array();
		$result_obj=$connection->query($query);
		try{
			while($result = $result_obj->fetch_array(MYSQLI_ASSOC)){
				$items[$result['idcountry']]=$result['country'];
			}
			return($items);
		}
		catch(Exception $e){
			return false;
		}
	}
}