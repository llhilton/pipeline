<h1>Watson Upload</h1>
<?php
//Adapted from http://stackoverflow.com/questions/11448307/importing-csv-data-using-php-mysql

require_once('classes/connecti.php');

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
		echo "<h2>Displaying contents:</h2>";
		readfile($_FILES['filename']['tmp_name']);
	}

	//Clear old watson data
	clearwatson();
	
	$connection=Database::getConnection();
	//Import uploaded file to Database
	
	$handle = fopen($_FILES['filename']['tmp_name'], "r");
	$i=0; //Used to filter out the heading row.
	while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
		//$import="INSERT into importing(text,number)values('$data[0]','$data[1]')";
		if ($i<>0){	
			$countriesarray=explode('.',$data[7]);
			
			if ($data[1]>0 && $data[1]<99999){
				$query="INSERT INTO watson ";
				$query .= "VALUES (DEFAULT, ";
				$query.="'".$data[1]."', ";
				$query.="'".$data[2]."', ";
				$query.="'".$data[3]."', ";
				$query.="'".$data[4]."', ";
				$query.="'".$data[5]."', ";
				$query.="'".$data[6]."'";
				$query.=")";
				
				echo $query.'<br>';
				/*if (!$connection->query($query)){
					echo "Error :" .$query . "<br>" . $connection->error;
				}*/
				foreach ($countriesarray as $country){
					//first need to get the idcountry for the country, then
					//add it to the watson_country table.
					$query="INSERT INTO watson_country VALUES (DEFAULT, ";
					//$query.="'".$country
				}
			}
			
		}
		$i++;
	}

	fclose($handle);

	print "Import done";

	//view upload form
} else {
	print "Upload new csv of watson by browsing to file and clicking on Upload<br />\n";
	print "<form enctype='multipart/form-data' action='watson.php' method='post'>";
	print "File name to import:<br />\n";
	print "<input size='50' type='file' name='filename'><br />\n";
	print "<input type='submit' name='submit' value='Upload'></form>";

}