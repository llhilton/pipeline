<?php
class Watson{
	public function getForm(){
		print "Upload Watson csv by browsing to file and clicking on Upload<br />\n";
		print "<form enctype='multipart/form-data' action='index.php' method='post'>";
		print "File name to import:<br />\n";
		print "<input size='50' type='file' name='filename'><br />\n";
		print "<input type='submit' name='submit' value='Upload'></form>";
	}
}