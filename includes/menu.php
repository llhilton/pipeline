<?php
	function displayMenu(){
		?>
<ul>
	<li><div class="navhead">Summaries</div></li>
	<li><a href="index.php">CTR Version</a></li>
	<li><a href="index.php?action=fullsummary">Full Version</a></li>
	<li><a href="index.php?action=edittotalsform">Edit Totals</a></li>
	<li><a href="index.php?action=addfundingsourceform">Add funding source</a></li>
	<li><div class="navhead">Projects</div></li>
	<li><a href="index.php?category=MENA">MENA</a></li>
	<li><a href="index.php?category=MENA-Iraq">MENA-Iraq</a></li>
	<li><a href="index.php?category=Southeast%20Asia">Southeast Asia</a></li>
	<li><a href="index.php?category=Sub-Saharan%20Africa">Sub-Saharan Africa</a></li>
	<li><a href="index.php?category=South%20Asia">South Asia</a></li>
	<li><a href="index.php?category=Ukraine">Ukraine</a></li>
	<li><a href="index.php?category=Global">Global</a></li>
	<li><a href="index.php?action=addprojectform">Add a project</a></li>
	<li><a href="index.php?action=newcountryform">Add a country</a></li>
	<li><div class="navhead">Imports</div></li>
	<li><a href="impromptu.php">Import new Impromptu file</a></li>
	<li><a href="watson.php">Import new Watson file</a></li>
</ul>
<?php }