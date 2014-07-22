<?php
session_start();
?>
<?php
require_once("phpexcel/Classes/PHPExcel.php");
require_once("phpexcel/Classes/PHPExcel/IOFactory.php");
require("excel functions.php");

?>
<?php
ini_set("date.timezone","Asia/Kuala_Lumpur");
?>
<?php
function hour($id){
	$db=new mysqli("localhost","root","","transport");


}

?>
<?php
function is_decimal( $val )
{
    return is_numeric( $val ) && floor( $val ) != $val;
}
?>
<?php
if(isset($_GET['star_id'])){
	$db2=new mysqli("localhost","root","","timetable");


	$star_id=$_GET['star_id'];
	$timetable=$_GET['timetable'];
	
	$timetable_id=$_GET['timetable_id'];
	
	$sql="select * from timetable where id='".$timetable_id."'";	
	
	$rs=$db2->query($sql);
	
	$row=$rs->fetch_assoc();	
	
	$report_file=$row['report_file'];
	
	if($report_file==""){
	
	}
	else {
		$oldfilename="forms/STAR_".$report_file.".xls";
		$dateSlip=date("Y-m-d His");	
	
		$newFilename="printout/STAR_".$dateSlip.".xls";
		copy($oldfilename,$newFilename);

		$workSheetName="STAR";	
		$workbookname=$newFilename;
		
		$excel=loadExistingWorkbook($workbookname);

		$ExWs=createWorksheet($excel,$workSheetName,"openActive");
		
			
		save($ExWb,$excel,$newFilename); 			
		
		
		echo "<br>";
		echo "Train Hourly Report has been generated!  Press right click and Save As: <a href='".$newFilename."'>Here</a>";		
	


	}
	
}

?>
