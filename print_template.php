<?php
session_start();
?>
<?php
require_once("phpexcel/Classes/PHPExcel.php");
require_once("phpexcel/Classes/PHPExcel/IOFactory.php");
require("excel functions.php");

?>
<?php
$db2=new mysqli("localhost","root","","timetable");

if(isset($_GET['timetable_id'])){
	$timetable_id=$_GET['timetable_id'];
	$filename="Hourly Template.xls";

	$oldfilename="forms/".$filename;
	$dateSlip=date("Y-m-d His");

	$sql="select * from timetable where id='".$timetable_id."'";	
	$rs=$db2->query($sql);
	
	$row=$rs->fetch_assoc();	
	
	$report_file=$row['report_file'];
	$timetable_code=$row['code'];
	
	if($report_file==""){
	}
	else {
		$newFilename="forms/STAR_".$report_file.".xls";
		copy($oldfilename,$newFilename);
		
		$workSheetName="STAR";	

		$workbookname=$newFilename;
		$excel=loadExistingWorkbook($workbookname);
		
		$ExWs=createWorksheet($excel,$workSheetName,"openActive");

		addContent(setRange("J10","K10"),$excel,$timetable_code,"true",$ExWs);

		
		$rowCount=14;
		
		$sql2="select * from timetable_hour where timetable_id='".$timetable_id."' order by time_from";
		$rs2=$db2->query($sql2);
		$nm2=$rs2->num_rows;
			
		for($i=0;$i<$nm2;$i++){
			$row2=$rs2->fetch_assoc();
			$timelabel=$row2['time_label'];
			$headway=$row2['headway'];
			
			addContent(setRange("A".$rowCount,"A".$rowCount),$excel,$timelabel,"false",$ExWs);
			addContent(setRange("B".$rowCount,"B".$rowCount),$excel,$headway,"false",$ExWs);
			
			$cars_required=0;
			$reserve_required=0;
					
			$sql3="select * from timetable_required where timetable_id='".$timetable_id."' and hour_id='".$row2['id']."'";
			$rs3=$db2->query($sql3);
			$nm3=$rs3->num_rows;
			
			if($nm3>0){
				$row3=$rs3->fetch_assoc();
				$cars_required=$row3['cars_required'];				
				
				addContent(setRange("C".$rowCount,"C".$rowCount),$excel,$cars_required,"false",$ExWs);
				
			}
				
					
					
			$rowCount++;
		}	

		save($ExWb,$excel,$newFilename); 	
		echo "Template has been generated!  Press right click and Save As: <a href='".$newFilename."'>Here</a>";

		
		
	}
	
	
	

		
		
		
		
		
		
		
		
		
		
		
		
}
?>