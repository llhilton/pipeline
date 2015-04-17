<?php
// http://coyotelab.org/php/upload-csv-and-insert-into-database-using-phpmysql.html
/*	$db = mysql_connect("Database", "username", "password") or die("Could not connect.");
	if(!$db) 
	    die("no db");
	if(!mysql_select_db("Databasename",$db))
	    die("No database selected.");
	    */
	    ?>
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Upload page</title>
	<style type="text/css">
	body {
	    background: #E3F4FC;
	    font: normal 14px/30px Helvetica, Arial, sans-serif;
	    color: #2b2b2b;
	}
	a {
	    color:#898989;
	    font-size:14px;
	    font-weight:bold;
	    text-decoration:none;
	}
	a:hover {
	    color:#CC0033;
	}
	h1 {
	    font: bold 14px Helvetica, Arial, sans-serif;
	    color: #CC0033;
	}
	h2 {
	    font: bold 14px Helvetica, Arial, sans-serif;
	    color: #898989;
	}
	#container {
	    background: #CCC;
	    margin: 100px auto;
	    width: 945px;
	}
	#form           {padding: 20px 150px;}
	#form input     {margin-bottom: 20px;}
	</style>
	</head>
	<body>
	<div id="container">
	<div id="form">
	<?php
	include "connection.php"; //Connect to Database
	$deleterecords = "TRUNCATE TABLE tablename"; //empty the table of its current records
	mysql_query($deleterecords);
	//Upload File
	if (isset($_POST['submit'])) {
 	    if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
 	        echo "<h1>" . "File ". $_FILES['filename']['name'] ." uploaded successfully." . "</h1>";
 	        echo "<h2>Displaying contents:</h2>";
			//is the file .csv?
			$temp = explode(".", $_FILES["filename"]["name"]);
			if ($temp[1] == 'csv'){
				echo "<h2>Yes it's a csv file.</h2>";
			}else{
				die("Wrong file type");
			}
			if (strtolower($temp[0]) == "impromptu" || strtolower($temp[0]) == "watson" || strtolower($temp[0]) == "history" ){ //Remove the history one after done testing.
				echo "<h2>Right file name.</h2>";
			} else{
				die("Wrong file name");
			}
	        //readfile($_FILES['filename']['tmp_name']);
 	    }
 
 	    //Import uploaded file to Database
 	    $handle = fopen($_FILES['filename']['tmp_name'], "r");
		fgetcsv($handle, 0, ","); // skip the first row of data, which will be headers
	    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
			//think we should explode the string on input, to make it easier to have the short award number and other info easily accessible.
			$stringexplode=explode(".",$data[0]);
			$agreement = $stringexplode[0];
			$typeofaward = $stringexplode[1];
			$fy = $stringexplode[2];
			$program = $stringexplode[3];
			$shortawardnumber = $stringexplode[6];
			echo $shortawardnumber;
			
			//Need to convert the date format for watson projects
			$data[1] = date('Y-m-d',strtotime($data[1]));
			
			$data[2] = filter_var($data[2],FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); // Sanitize values for the impromptu amount
			print_r($data);
	        //$import="INSERT into tablename(item1,item2,item3,item4,item5) values('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]')";
			//echo $import;
	  //      mysql_query($import) or die(mysql_error());
 	    fclose($handle);
 	    print "Import done";
 	    //view upload form
	    }
 	}else {
 	    print "Upload new csv by browsing to file and clicking on Upload<br />\n";
	    print "<form enctype='multipart/form-data' action='upload.php' method='post'>";
 	    print "File name to import:<br />\n";
 	    print "<input size='50' type='file' name='filename'><br />\n";
	    print "<input type='submit' name='submit' value='Upload'></form>";
	}
	?>
	</div>
	</div>
	</body>
	</html>
