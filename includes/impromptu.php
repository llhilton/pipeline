<h1>Impromptu Upload</h1>
<?php
//Adapted from http://stackoverflow.com/questions/11448307/importing-csv-data-using-php-mysql

require_once('classes/connecti.php');

function impromptutoold(){
	$connection = Database::getConnection();
	//Drop the old impromptu info
	$query = "DROP TABLE impromptu_old";
	if (!$connection->query($query)){
		echo "Error :" .$query . "<br>" . $connection->error;
	}
	//Re-create the imprompt_old table
	$query= "CREATE TABLE impromptu_old LIKE impromptu";
	if (!$connection->query($query)){
		echo "Error :" .$query . "<br>" . $connection->error;
	}
	//Move data to the new table.
	$query="INSERT impromptu_old SELECT * FROM impromptu";
	if (!$connection->query($query)){
		echo "Error :" .$query . "<br>" . $connection->error;
	}
	//Empty impromptu table
	$query="TRUNCATE TABLE impromptu";
	if (!$connection->query($query)){
		echo "Error :" .$query . "<br>" . $connection->error;
	}
}

//Upload File
if (isset($_POST['submit'])) {

	if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
		echo "<p>" . "File ". $_FILES['filename']['name'] ." uploaded
 successfully." . "</p>";
		//echo "<h2>Displaying contents:</h2>";
		//readfile($_FILES['filename']['tmp_name']);
	}

	//Move current Impromptu table to Impromptu_old
	impromptutoold();
	$connection=Database::getConnection();
	//Import uploaded file to Database
	$handle = fopen($_FILES['filename']['tmp_name'], "r");
	$i=0; //Used to filter out the heading row.
	while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
		//$import="INSERT into importing(text,number)values('$data[0]','$data[1]')";
		if ($i<>0){	
			$projectstringarray=explode('.',$data[0]);
			$grantcode=$projectstringarray[0];
			$fundingtype=$projectstringarray[1];
			$fiscalyear=$projectstringarray[2];
			$program=$projectstringarray[3];
			$shortawardnumber=ltrim($projectstringarray[6],0);
			if ($shortawardnumber>0 && $shortawardnumber<99999 &&($program==35 or $program==67)){
				$query="INSERT INTO impromptu ";
				$query .= "VALUES (DEFAULT, '".$grantcode."', ";//grantcode
				$query.= "'".$fundingtype."', ";//fundingtype
				$query.= "'".$fiscalyear."', ";//fiscalyear
				$query.="'".$program."', ";//program
				$query.="'".$shortawardnumber."', "; //short award number
				$query.="'".$data[0]."', ";
				$query.="'".$data[1]."', ";
				$query.="'".$data[2]."', ";
				$query.="'".$data[3]."', ";
				$query.="'".$data[4]."', ";
				$query.="'".$data[5]."', ";
				$query.="'".$data[6]."', ";
				$query.="'".$data[7]."', ";
				$query.="'".$data[8]."'";
				$query.=")";
				//echo $query.'<br>';
				if (!$connection->query($query)){
					echo "Error :" .$query . "<br>" . $connection->error;
				}
			}
			
		}
		$i++;
	}

	fclose($handle);

	print "Import done";

	//view upload form
} else {
	print "Upload new csv of Impromptu by browsing to file and clicking on Upload<br />\n";
	print "<form enctype='multipart/form-data' action='impromptu.php' method='post'>";
	print "File name to import:<br />\n";
	print "<input size='50' type='file' name='filename'><br />\n";
	print "<input type='submit' name='submit' value='Upload'></form>";

}