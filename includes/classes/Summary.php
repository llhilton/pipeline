<?php
/**
 * includes/classes/Summary.php
 *
 * Class for showing the summary of the budgeted information
 *
 * @version    0.1 2015-04-15
 * @package    BEP Pipeline
 * @copyright  Copyright (c) 2015 Lisa Hilton
 * @license    GNU General Public License
 * @since      Since Release 1.0
 */

class Summary{
	
	public function __construct(){	}
	
	//show the heading of the table for the summary
	public function showSummaryHeading($summaryType){
		echo "<table><tr>";
		echo "<th>BEP Pipeline</th>";
		$fundingsource=new Fundingsource();
		$fiscalyears = $fundingsource->getFiscalYears();
		if ($summaryType=='CTR'){
			$i=0;
			foreach ($fiscalyears as $value){
				if ($i%2==0){
					echo "<th>FY".$value['fiscalYear']."</th>";
				}
				$i++;
			}
		}elseif ($summaryType=='full'){
			foreach ($fiscalyears as $value){
				echo "<th>FY".$value['fiscalYear']." ".$value['typeOfFunding']."</th>";
			}
		}
		echo "<th>Total</th>";
		echo "</tr>";
	}
	
	//Show the funding amounts in a row
	public function showRow($summaryType, $rowlead, $rowinfo, $numbersources){
		echo "<tr>";
		echo "<td>".$rowlead."</td>";
		$sum=0;
		if ($summaryType=='CTR'){
			for ($i=0;$i<$numbersources;$i++){
				if ($i%2==0){
					$sum=$rowinfo[$i];
				}else{
					$sum+=$rowinfo[$i];
					echo "<td>$".number_format($sum,2)."</td>";
				}
			}
		}elseif ($summaryType=='full'){
			foreach ($rowinfo as $value){
				echo "<td>$".number_format($value,2)."</td>";
			}
		}
		echo "<td>$".number_format(array_sum($rowinfo),2)."</td>";
		echo "</tr>";
	}
	
	//Calculate the free and clear amount
	public function calculateFreeAndClear($remaining, $pipeline){
		$numberofcells=count($remaining);
		$freeandclear="";
		for ($i=0;$i<$numberofcells;$i++){
			$freeandclear[$i]=$remaining[$i]-$pipeline[$i];
		}
		return $freeandclear;
	}
	
	//Close the table
	public function closeTable(){
		echo "</table>";
	}
	
	//Display the summary
	public function displaySummary($summaryType){
		$this->showSummaryHeading($summaryType);
		$fundingsource = new Fundingsource();
		$numbersources = $fundingsource->getNumberSources();
		
		$budgeted = $fundingsource->getFundingInformation('fundingamount');
		$spent=$fundingsource->getFundingInformation('spent');
		$obligation=$fundingsource->getFundingInformation('obligation');
		$impactfee=$fundingsource->getFundingInformation('impactfee');
		$remaining=$fundingsource->getFundingInformation('remaining');

		$this->showRow($summaryType, 'Budgeted', $budgeted, $numbersources);
		$this->showRow($summaryType, 'Spent', $spent, $numbersources);
		$this->showRow($summaryType, 'Obligation', $obligation, $numbersources);
		$this->showRow($summaryType, 'Impact Fee', $impactfee, $numbersources);
		$this->showRow($summaryType, 'Remaining', $remaining, $numbersources);
		
		$projects = new Projects();
		$pipeline = $projects->getPipeline();
		$this->showRow($summaryType, 'Pipeline',$pipeline,$numbersources);
		$freeandclear = $this->calculateFreeAndClear($remaining, $pipeline);
		$this->showRow($summaryType, 'Free and Clear', $freeandclear, $numbersources);
		
		$this->closeTable();
	}
}