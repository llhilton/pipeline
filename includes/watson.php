<h2>Watson Upload</h2>
<?php
//Adapted from http://stackoverflow.com/questions/11448307/importing-csv-data-using-php-mysql

//Upload File
if (isset($_POST['submit'])) {

	if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
		echo "<p>" . "File ". $_FILES['filename']['name'] ." uploaded
 successfully." . "</p>";
	}

	//Clear old watson data
	$subawards=new Subawards();
	$subawards->clearwatson();
	
	//Get an array of all the countries
	$country=new Country();
	$allcountries=$country->getIdCountries();
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
					if ($country<>""){
						$countryconnection=Database::getConnection();
						$idcountry="";
						//first need to get the idcountry for the country
						$idcountry = array_search($country,$allcountries);
						$countryquery="INSERT INTO watson_country VALUES (DEFAULT, ".$idwatson.", ".$idcountry.")";
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