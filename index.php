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

require_once('includes/classes/connecti.php');
require_once('includes/classes/watson.php');

$connection = Database::getConnection();
if ($result = $connection->query("SHOW TABLES")) {
	$count = $result->num_rows;
	echo "Tables: ($count)<br />";
	while ($row = $result->fetch_array()) {
		echo $row[0]. '<br />';
	}
}

$watson = new Watson();
$watson->getForm();

