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
function getStation($station){
	$db=new mysqli("localhost","root","","transport");
	
	$sql="select * from station where id='".$station."'";
	$rs=$db->query($sql);
	$row=$rs->fetch_assoc();
	$name=$row['station_name'];
	return $name;

}
?>
<?php
if(isset($_GET['ser_int'])){
	$service_interruption=$_GET['ser_int'];
	$serint_date=$_GET['ser_intdate'];
	
	$filename="ServiceIntReport.xls";

	$oldfilename="forms/".$filename;
	$dateSlip=date("Y-m-d His");
	$newFilename="printout/Service_Interruption_".$dateSlip.".xls";
	copy($oldfilename,$newFilename);
	$workSheetName="Service Interruption";	
	$workbookname=$newFilename;
	$excel=loadExistingWorkbook($workbookname);

  	$ExWs=createWorksheet($excel,$workSheetName,"openActive");
	
	$rowCount=0;	
	
	$db=new mysqli("localhost","root","","transport");



	addContent(setRange("H8","J8"),$excel,"Day:  ".date("l",strtotime($serint_date)),"true",$ExWs);	
	addContent(setRange("H9","J9"),$excel,"Date:  ".date("F d, Y",strtotime($serint_date)),"true",$ExWs);
	
	$timeTableSQL="select *,timetable_day.id as timeId from timetable_day inner join timetable_code on timetable_day.timetable_code=timetable_code.id where train_date like '".$serint_date."%%'";

	$timeTableRS=$db->query($timeTableSQL);
	$timeTableNM=$timeTableRS->num_rows;
	if($timeTableNM>0){
		$timeTableRow=$timeTableRS->fetch_assoc();
		addContent(setRange("H10","J10"),$excel,$timeTableRow['code'],"true",$ExWs);
		
		
	}		
	
	$sql="select * from incident_report inner join incident_description on incident_report.id=incident_description.incident_id where incident_report.id='".$service_interruption."'";

	$rs=$db->query($sql);	
	
	$row=$rs->fetch_assoc();
	
	$incident_no=$row['incident_no'];

	$direction=$row['direction'];
	$index_no=$row['index_no'];
	$car_no=$row['car_no'];
	$details=$row['description'];	
	$location=$row['location'];
	$stationCount=substr_count($location,"-");
	if($stationCount>0){
		$station=explode("-",$location);		

		$originStation=trim($station[0]);
		$destinationStation=trim($station[1]);
		
		$stationName=getStation($originStation)." - ".getStation($destinationStation);
		
	
	}
	else {
		$stationName=getStation(trim($location));
	
	}

	$service_location="";
	
	if($direction=="S"){	
		$service_location=$stationName;
		
	}
	else if(($direction=="NB")||($direction=="SB")){
		$service_location=$stationName." ".$direction;
	}
	else {
		$service_location=$direction;
	}
	
	addContent(setRange("C15","I15"),$excel,$incident_no,"true",$ExWs);		
	
	addContent(setRange("B17","J19"),$excel,"Index #".$index_no.", Car ".$car_no.", ".$details."","true",$ExWs);		

	addContent(setRange("B21","J21"),$excel,$service_location,"true",$ExWs);		

	$rowCount+=24;

	$sql="select * from service_interruption where incident_id='".$service_interruption."'";
	$rs=$db->query($sql);

	$nm=$rs->num_rows;
	
	for($i=0;$i<$nm;$i++){
		
		$row=$rs->fetch_assoc();
		
		$text=$row['description'];
		
		$time=date("Hi",strtotime($row['time']))."H";
		
		
		
		$lines=strlen(trim($text));

		$lineCount=ceil($lines/94);
		
		$rowAdd=($lineCount-1);
		
		$start=$rowCount;
		
		$end=$rowCount+$rowAdd;
		
		$rowCount=$end;
		
		if($start>$end){ $start=$end; }
		
		addContent(setRange("A".$start,"A".$end),$excel,$time,"true",$ExWs);		
		addContent(setRange("B".$start,"J".$end),$excel,$text,"true",$ExWs);		
		$excel->getActiveSheet()->getStyle("B".$start,"J".$end)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);		
		$excel->getActiveSheet()->getStyle("B".$start,"J".$end)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);		


		$styleArray = array(
			'borders' => array(
				'outline' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			),
		);
		$excel->getActiveSheet()->getStyle("A".$start.":A".$end)->applyFromArray($styleArray);
		$excel->getActiveSheet()->getStyle("B".$start.":J".$end)->applyFromArray($styleArray);


		$rowCount++;
	}

		
	
	save($ExWb,$excel,$newFilename); 	
	echo "Service Interruption has been generated!  Press right click and Save As: <a href='".$newFilename."'>Here</a>";


}
?>