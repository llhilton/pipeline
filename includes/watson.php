<?php
//Adapted from http://stackoverflow.com/questions/11448307/importing-csv-data-using-php-mysql

//require_once('classes/connecti.php');


//Get an array of all the countries.
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

//Empty the watson and watson_country tables. 
function clearwatson(){
	$connection = Database::getConnection();
	//Empty watson table
	$query="TRUNCATE TABLE watson";
	if (!$connection->query($query)){
		echo "Error :" .$query . "<br>" . $connection->error;
	}
	//Empty watson_country table
	$query="TRUNCATE TABLE watson_country";
	if (!$connection->query($query)){
		echo "Error :" .$query . "<br>" . $connection->error;
	}
}

//Upload File
if (isset($_POST['submit'])) {

	if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
		echo "<p>" . "File ". $_FILES['filename']['name'] ." uploaded
 successfully." . "</p>";
	}

	//Clear old watson data
	clearwatson();
	
	//Get an array of all the countries
	$allcountries=getIdCountries();
	//print_r($allcountries);
	
	$connection=Database::getConnection();
	//Import uploaded file to Database
	
	$handle = fopen($_FILES['filename']['tmp_name'], "r");
	$i=0; //Used to filter out the heading rows
	while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
		list(,$shortawardnumber,$title,$budget,$status,$startdate,$enddate,$countries)=$data;
		$budget=filter_var($budget, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		if ($i>1){	
			$countriesarray=explode(',',$countries);
			//print_r($countriesarray);
			//echo "<br>";
			$title=$connection->real_escape_string($title);
			if ($shortawardnumber>1000 && $shortawardnumber<99999){
				$query="INSERT INTO watson ";
				$query .= "VALUES (DEFAULT, ";
				$query.="'".$shortawardnumber."', ";
				$query.="'".$title."', ";
				$query.="'".$budget."', ";
				$query.="'".$status."', ";
				$query.="'".date("Y-m-d", strtotime($startdate))."', ";
				$query.="'".date("Y-m-d", strtotime($enddate))."'";
				$query.=")";
				
				$result_obj='';
				$result_obj=$connection->query($query);
				try{
					$idwatson=$connection->insert_id;
				}
				catch(Exception $e){
					return false;
				}
				
				//Add the countries to watson
				foreach ($countriesarray as $country){
					$country=trim($country);
					//echo $country;
					if ($country<>""){
						$countryconnection=Database::getConnection();
						$idcountry="";
						//first need to get the idcountry for the country
						$idcountry = array_search($country,$allcountries);
						//echo $idcountry."<br>";
						
						//echo "hi".$idcountry."<br>";
						//add it to the watson_country table.
						$countryquery="INSERT INTO watson_country VALUES (DEFAULT, ".$idwatson.", ".$idcountry.")";
						//echo $countryquery."<br>";
						
						if (!$countryconnection->query($countryquery)){
							echo "Error :" .$countryquery . "<br>" . $countryconnection->error."<br>".$country;
						}
					}
				}
			}
			
		}
		$i++;
	}

	fclose($handle);

	echo "Import done";

	//view upload form
} else {
	echo "Upload new csv of watson by browsing to file and clicking on Upload<br />\n";
	echo "<form enctype='multipart/form-data' action='watson.php' method='post'>";
	echo "File name to import:<br />\n";
	echo "<input size='50' type='file' name='filename'><br />\n";
	echo "<input type='submit' name='submit' value='Upload'></form><br> \n";
	echo "Please be patient after submitting, as it may take a while to import the data.";

}