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

require_once('classes/connecti.php');
//require_once('classes/html_table.php');
$connection = Database::getConnection();
$fundingsources=Database::getFunding();

function getTotals($funding){
	//set up the arrays to store the information
	$headings=array();
	$totalbudget=array();
	$totalspend=array();
	$totalobligation=array();
	$totalprogrambudget=array();
	$totalprogramspend=array();
	$totalprogramobligation=array();
	$totalprogramavailable=array();
	$totalimpactbudget=array();
	$totalimpactspend=array();
	$totalimpactobligation=array();
	$totalimpactfee=array();
	$totalimpactavailable=array();
	$totalavailable=array();
	
	foreach ($funding as $key=>$value){
		$fiscalyear="FY".$value['fiscalYear'];
		$fundingtype=$value['typeOfFunding'];
		$totalfunding=$value['fundingamount'];
		$spent=$value['spent'];
		$obligation=$value['obligation'];
		$impactfee=$value['impactfee'];
		//Set the total amount for the fiscal year
		if (array_key_exists($fiscalyear,$totalbudget)){
			$totalbudget[$fiscalyear] += $totalfunding;
		}else{
			$totalbudget[$fiscalyear]=$totalfunding;
		}
		//Set total spent for fiscal year
		if (array_key_exists($fiscalyear, $totalspend)){
			$totalspend[$fiscalyear] += $spent;
		}else{
			$totalspend[$fiscalyear] =$spent;
		}
		//set total obligation for fiscal year
		if (array_key_exists($fiscalyear, $totalobligation)){
			$totalobligation[$fiscalyear] += $obligation;
		}else{
			$totalobligation[$fiscalyear] = $obligation;
		}
		if ($fundingtype=='Program'){
			$totalprogrambudget[$fiscalyear] = $totalfunding;
			$totalprogramspend[$fiscalyear] = $spent;
			$totalprogramobligation[$fiscalyear] = $obligation;
			$totalprogramavailable[$fiscalyear] = $totalfunding - $spent - $obligation ;
		}
		if ($fundingtype=='Impact'){
			$totalimpactbudget[$fiscalyear]=$totalfunding;
			$totalimpactspend[$fiscalyear]=$spent;
			$totalimpactobligation[$fiscalyear]=$obligation;
			$totalimpactfee[$fiscalyear]=$impactfee;
			$totalimpactavailable[$fiscalyear]=$totalfunding - $spent - $obligation - $impactfee;
		}
	}
	//total up all the fiscal years
	$totalbudget['Total']=array_sum($totalbudget);
	$totalspend['Total']=array_sum($totalspend);
	$totalobligation['Total']=array_sum($totalobligation);
	$totalprogrambudget ['Total']=array_sum($totalprogrambudget);
	$totalprogramspend['Total']=array_sum($totalprogramspend);
	$totalprogramobligation['Total']=array_sum($totalprogramobligation);
	$totalprogramavailable['Total']=array_sum($totalprogramavailable);
	$totalimpactbudget['Total']=array_sum($totalimpactbudget);
	$totalimpactspend['Total']=array_sum($totalimpactspend);
	$totalimpactobligation['Total']=array_sum($totalimpactobligation);
	$totalimpactfee['Total']=array_sum($totalimpactfee);
	$totalimpactavailable['Total']=array_sum($totalimpactavailable);
	
	//Get the totals for the old funds
	$totalbudget['FY10-12'] = $totalbudget['FY10'] + $totalbudget['FY11'] + $totalbudget['FY12'];
	$totalspend['FY10-12']=$totalspend['FY10'] + $totalspend['FY11'] + $totalspend['FY12'];
	$totalobligation['FY10-12']=$totalobligation['FY10'] + $totalobligation['FY11'] + $totalobligation['FY12'];
	$totalprogrambudget ['FY10-12']=$totalprogrambudget['FY10'] + $totalprogrambudget['FY11'] + $totalprogrambudget['FY12'];
	$totalprogramspend['FY10-12']=$totalprogramspend['FY10'] + $totalprogramspend['FY11'] + $totalprogramspend['FY12'];
	$totalprogramobligation['FY10-12']=$totalprogramobligation['FY10'] + $totalprogramobligation['FY11'] + $totalprogramobligation['FY12'];
	$totalprogramavailable['FY10-12']=$totalprogramavailable['FY10'] + $totalprogramavailable['FY11'] + $totalprogramavailable['FY12'];
	$totalimpactbudget['FY10-12']=$totalimpactbudget['FY10'] + $totalimpactbudget['FY11'] + $totalimpactbudget['FY12'];
	$totalimpactspend['FY10-12']=$totalimpactspend['FY10'] + $totalimpactspend['FY11'] + $totalimpactspend['FY12'];
	$totalimpactobligation['FY10-12']=$totalimpactobligation['FY10'] + $totalimpactobligation['FY11'] + $totalimpactobligation['FY12'];
	$totalimpactfee['FY10-12']=$totalimpactfee['FY10'] + $totalimpactfee['FY11'] + $totalimpactfee['FY12'];
	$totalimpactavailable['FY10-12']=$totalimpactavailable['FY10'] + $totalimpactavailable['FY11'] + $totalimpactavailable['FY12'];
	
	return array($totalbudget, $totalspend, $totalobligation, $totalprogrambudget, $totalprogramspend, $totalprogramobligation, $totalprogramavailable,$totalimpactbudget, $totalimpactspend, $totalimpactobligation, $totalimpactfee, $totalimpactavailable);
}

/*echo "<table>"; 
$totalbudget = getTotals($fundingsources);
echo "<tr><td>Fiscal year</td>";
foreach ($totalbudget as $key=>$value){
	echo "<td>".$key."</td>";
}
echo "</tr>";
echo "<tr><td>Total budget</td>";
setlocale(LC_MONETARY, 'en_US');
foreach ($totalbudget as $key=>$value){
	echo "<td>$".number_format($value)."</td>";
}
echo "</tr>";
echo "</table>";
*/
$totalsarray=getTotals($fundingsources);?>
<table>
<tr>
<th>BEP Pipeline</th>
<th>FY10</th>
<th>FY11</th>
<th>FY12</th>
<th>FY10-12 Total</th>
<th>FY12 Iraq</th>
<th>FY13</th>
<th>FY14</th>
<th>Total</th>
</tr>
<tr>
<?php 

foreach($totalsarray as $key=>$value){
	echo "<tr>";
	echo "<td> </td>";
	echo "<td>".$value['FY10']."</td>";
	echo "<td>".$value['FY11']."</td>";
	echo "<td>".$value['FY12']."</td>";
	echo "<td>".$value['FY10-12']."</td>";
	echo "<td>".$value['FY12-Iraq']."</td>";
	echo "<td>".$value['FY13']."</td>";
	echo "<td>".$value['FY14']."</td>";
	echo "<td>".$value['Total']."</td>";
	echo"</tr>";
}
?>
</table>