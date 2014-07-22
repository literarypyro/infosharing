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
if((isset($_GET['ccip_id']))&&($_GET['ccip_id']!=="5")&&($_GET['ccip_id']!=="6")){
	$star_id=$_GET['ccip_id'];
	$timetable=$_GET['ccip_date'];
	
	$filename="";
	if($star_id=="1"){
		$filename="CCIP-WD29.xls";
		
	}
	else if($star_id=="2"){
		$filename="CCIP-SAT12.xls";
		
		
	}
	else if($star_id=="3"){
		$filename="CCIP-SUN13.xls";
		
		
	}
	else if($star_id=="4"){
		$filename="CCIP-HOL07.xls";
	}
	
//	else if($star_id=="5"){
//		$filename="STAR-HOL08.xls";
//	}

//	else if($star_id=="6"){
//		$filename="STAR-HOL09.xls";
//	}
	else if($star_id=="7"){
		$filename="CCIP-WD30.xls";
	}
	$oldfilename="forms/".$filename;
	$dateSlip=date("Y-m-d His");	
	
	$newFilename="printout/CCIP_".$dateSlip.".xls";
	copy($oldfilename,$newFilename);

	$workSheetName="Control Center Insertion Program";	
	$workbookname=$newFilename;
	$excel=loadExistingWorkbook($workbookname);

  	$ExWs=createWorksheet($excel,$workSheetName,"openActive");
	
	
	addContent(setRange("H9","I9"),$excel,date("F d, Y",strtotime($timetable)),"true",$ExWs);
	addContent(setRange("H8","I8"),$excel,date("l",strtotime($timetable)),"true",$ExWs);


	save($ExWb,$excel,$newFilename); 	
	echo "<br>";
	echo "Insertion Form has been generated!  Press right click and Save As: <a href='".$newFilename."'>Here</a>";		
	
}

?>
