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
$db=new mysqli("localhost","root","","transport");

if(isset($_GET['year'])){
	$year=$_GET['year'];
	$level=$_GET['level'];

	$filename="Statistics Report.xls";

	$oldfilename="forms/".$filename;
	$dateSlip=date("Y-m-d His");
	$newFilename="printout/Stats Report_".$dateSlip.".xls";
	copy($oldfilename,$newFilename);

	$workSheetName="Statistics Report";	
	$workbookname=$newFilename;
	$excel=loadExistingWorkbook($workbookname);

  	$ExWs=createWorksheet($excel,$workSheetName,"openActive");


	
	for($i=1;$i<=12;$i++){
		$equipt_count["Month_".$i]["Equipt_114"]=0;
		$equipt_count["Month_".$i]["Equipt_102"]=0;
		$equipt_count["Month_".$i]["Equipt_110"]=0;
		$equipt_count["Month_".$i]["Equipt_11"]=0;
		$equipt_count["Month_".$i]["Equipt_113"]=0;
		$equipt_count["Month_".$i]["Equipt_104"]=0;
		$equipt_count["Month_".$i]["Equipt_108"]=0;
		$equipt_count["Month_".$i]["Equipt_109"]=0;
		$equipt_count["Month_".$i]["Equipt_103"]=0;
		$equipt_count["Month_".$i]["Equipt_124"]=0;
		$equipt_count["Month_".$i]["Equipt_67"]=0;
		$equipt_count["Month_".$i]["Equipt_111"]=0;
		$equipt_count["Month_".$i]["Equipt_112"]=0;
		$equipt_count["Month_".$i]["Equipt_Others"]=0;
		

		$equipt_count["Month_".$i]["Equipt_105"]=0;
		$equipt_count["Month_".$i]["Equipt_81"]=0;
		$equipt_count["Month_".$i]["Equipt_118"]=0;
		$equipt_count["Month_".$i]["Equipt_119"]=0;
		$equipt_count["Month_".$i]["Equipt_64"]=0;
		$equipt_count["Month_".$i]["Equipt_115"]=0;
		$equipt_count["Month_".$i]["Equipt_89"]=0;
		$equipt_count["Month_".$i]["Equipt_120"]=0;
		$equipt_count["Month_".$i]["Equipt_123"]=0;
		$equipt_count["Month_".$i]["Equipt_121"]=0;
		$equipt_count["Month_".$i]["Equipt_116"]=0;
		$equipt_count["Month_".$i]["Equipt_2"]=0;
		$equipt_count["Month_".$i]["Equipt_122"]=0;
		$equipt_count["Month_".$i]["Equipt_117"]=0;		
	
	}

	$rowCount=4;
	for($i=1;$i<=12;$i++){	
		$month_heading=date("F",strtotime($year."-".$i."-01"));
		$date_limit=date("t",strtotime($year."-".$i."-01"));
		
		$start_date=date("Y-m-d",strtotime($year."-".$i."-01"));
		$end_date=date("Y-m-d",strtotime($year."-".$i."-".$date_limit));
		
		$sql="select *,count(1) as equipt_count from incident_report where level='".$level."' and incident_date between '".$start_date." 00:00:00' and '".$end_date." 23:59:59' and equipt in ('114','102','110','11','113','104','108','109','103','124','67','111','112') group by equipt";
		$rs=$db->query($sql);
		$nm=$rs->num_rows;
		
		for($k=0;$k<$nm;$k++){
			$row=$rs->fetch_assoc();
			$equipt_count["Month_".$i]["Equipt_".$row['equipt']]=$row['equipt_count'];
			
		}

		$sql="select *,count(1) as equipt_count from incident_report inner join external.incident_defects on incident_report.id=external.incident_defects.incident_id where level='".$level."' and incident_date between '".$start_date." 00:00:00' and '".$end_date." 23:59:59' and external.incident_defects.equipt_id in ('114','102','110','11','113','104','108','109','103','124','67','111','112') group by equipt"; 
		$rs=$db->query($sql);
		$nm=$rs->num_rows;
		
		for($k=0;$k<$nm;$k++){
			$row=$rs->fetch_assoc();
			$equipt_count["Month_".$i]["Equipt_".$row['equipt_id']]+=$row['equipt_count'];
		}		
		
		
		$sql="select *,count(1) as equipt_count from incident_report where level='".$level."' and incident_date between '".$start_date." 00:00:00' and '".$end_date." 23:59:59' and equipt in ('105','81','118','119','64','115','89','120','123','121','116','2','122','117') group by equipt";
		$rs=$db->query($sql);
		$nm=$rs->num_rows;
		
		for($k=0;$k<$nm;$k++){
			$row=$rs->fetch_assoc();
			$equipt_count["Month_".$i]["Equipt_".$row['equipt']]=$row['equipt_count'];
			$equipt_count["Month_".$i]["Equipt_Others"]+=$row['equipt_count']*1;
			
			
			
		}

		$sql="select *,count(1) as equipt_count from incident_report inner join external.incident_defects on incident_report.id=external.incident_defects.incident_id where level='".$level."' and incident_date between '".$start_date." 00:00:00' and '".$end_date." 23:59:59' and external.incident_defects.equipt_id in ('105','81','118','119','64','115','89','120','123','121','116','2','122','117') group by equipt"; 
		$rs=$db->query($sql);
		$nm=$rs->num_rows;
		
		for($k=0;$k<$nm;$k++){
			$row=$rs->fetch_assoc();
			$equipt_count["Month_".$i]["Equipt_".$row['equipt_id']]+=$row['equipt_count'];
			$equipt_count["Month_".$i]["Equipt_Others"]+=$row['equipt_count']*1;

			
		}			
		addContent(setRange("B".$rowCount,"B".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_114"],"true",$ExWs);
		addContent(setRange("C".$rowCount,"C".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_102"],"true",$ExWs);
		addContent(setRange("D".$rowCount,"D".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_110"],"true",$ExWs);
		addContent(setRange("E".$rowCount,"E".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_11"],"true",$ExWs);
		addContent(setRange("F".$rowCount,"F".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_113"],"true",$ExWs);
		addContent(setRange("G".$rowCount,"G".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_104"],"true",$ExWs);
		addContent(setRange("H".$rowCount,"H".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_124"],"true",$ExWs);
		addContent(setRange("I".$rowCount,"I".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_109"],"true",$ExWs);
		addContent(setRange("J".$rowCount,"J".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_103"],"true",$ExWs);
		addContent(setRange("K".$rowCount,"K".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_108"],"true",$ExWs);
		addContent(setRange("L".$rowCount,"L".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_67"],"true",$ExWs);
		addContent(setRange("M".$rowCount,"M".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_111"],"true",$ExWs);
		addContent(setRange("N".$rowCount,"N".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_112"],"true",$ExWs);
		addContent(setRange("O".$rowCount,"O".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_Others"],"true",$ExWs);

		$rowCount++;
	}		
		
	$ExWs=setActiveWorksheet($excel,"",1);
		
	
	$rowCount=4;	
	for($i=1;$i<=12;$i++){	
		
		addContent(setRange("B".$rowCount,"B".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_105"],"true",$ExWs);
		addContent(setRange("C".$rowCount,"C".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_81"],"true",$ExWs);
		addContent(setRange("D".$rowCount,"D".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_118"],"true",$ExWs);
		addContent(setRange("E".$rowCount,"E".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_119"],"true",$ExWs);
		addContent(setRange("F".$rowCount,"F".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_64"],"true",$ExWs);
		addContent(setRange("G".$rowCount,"G".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_115"],"true",$ExWs);
		addContent(setRange("H".$rowCount,"H".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_89"],"true",$ExWs);
		addContent(setRange("I".$rowCount,"I".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_120"],"true",$ExWs);
		addContent(setRange("J".$rowCount,"J".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_123"],"true",$ExWs);
		addContent(setRange("K".$rowCount,"K".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_121"],"true",$ExWs);
		addContent(setRange("L".$rowCount,"L".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_116"],"true",$ExWs);
		addContent(setRange("M".$rowCount,"M".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_2"],"true",$ExWs);
		addContent(setRange("N".$rowCount,"N".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_122"],"true",$ExWs);
		addContent(setRange("O".$rowCount,"O".$rowCount),$excel,$equipt_count["Month_".$i]["Equipt_117"],"true",$ExWs);


	
		
		$rowCount++;
		
		
	}
	

	save($ExWb,$excel,$newFilename); 	
	echo "Statistics Report has been generated!  Press right click and Save As: <a href='".$newFilename."'>Here</a>";




}
?>
