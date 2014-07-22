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
function getTrainDriver($id,$dbase){

//$db=new mysqli("localhost","root","","transport");
	$sql="select * from train_driver where id='".$id."'";
	$rs=$dbase->query($sql);
	$row=$rs->fetch_assoc();
	
	$name=$row['position']." ".substr($row['firstName'],0,1).". ".$row['lastName'];
	return $name;


}

function getPHTrainDriver($id,$dbase){

//$db=new mysqli("localhost","root","","transport");
	$sql="select firstName,lastName from ph_trams where id='".$id."' limit 1";
	
	
	$rs=$dbase->query($sql);
	$nm=$rs->num_rows;
	if($nm>0){
		$row=$rs->fetch_assoc();
	
		$name=substr($row['firstName'],0,1).". ".$row['lastName'];
	}
	else {
	
		$name=$id;
	}
	return $name;


}




function getLevel($id,$dbase){
//$db=new mysqli("localhost","root","","transport");
	$sql="select * from level where incident_id='".$id."'";
	$rs=$dbase->query($sql);
	$row=$rs->fetch_assoc();
	$level=$row['order'];
	return $level;

}

function getOrdinal($number){
$ends = array('th','st','nd','rd','th','th','th','th','th','th');
if (($number %100) >= 11 && ($number%100) <= 13)
   $abbreviation = $number. 'th';
else
   $abbreviation = $number. $ends[$number % 10];

   
 return $abbreviation;  

}
if(isset($_GET['tar'])){
	$tar_date=$_GET['tar'];

	$filename="TAR.xls";

	$oldfilename="forms/".$filename;
	$dateSlip=date("Y-m-d His");
	$newFilename="printout/TAR_".$dateSlip.".xls";
	copy($oldfilename,$newFilename);

	$workSheetName="TAR";	
	$workbookname=$newFilename;
	$excel=loadExistingWorkbook($workbookname);

  	$ExWs=createWorksheet($excel,$workSheetName,"openActive");


	
	
	$db=new mysqli("localhost","root","","transport");

	addContent(setRange("O9","Q9"),$excel,date("F d, Y",strtotime($tar_date)),"true",$ExWs);

	addContent(setRange("O8","Q8"),$excel,date("l",strtotime($tar_date)),"true",$ExWs);

	$db=new mysqli("localhost","root","","transport");
	$timeTableSQL="select *,timetable_day.id as timeId from timetable_day inner join timetable_code on timetable_day.timetable_code=timetable_code.id where train_date like '".$tar_date."%%'";

	$timeTableRS=$db->query($timeTableSQL);
	$timeTableNM=$timeTableRS->num_rows;
	if($timeTableNM>0){
		$timeTableRow=$timeTableRS->fetch_assoc();
		addContent(setRange("O10","Q10"),$excel,$timeTableRow['code'],"true",$ExWs);
		
		
	}	
	
	
	$sql="select * from train_availability where date like '".$tar_date."%%' order by date";
	$rs=$db->query($sql);
	$nm=$rs->num_rows;

	$rowCount=0;
	$page_counter=1;
	
	$rowCount+=14;
	
	for($i=0;$i<$nm;$i++){
		$row=$rs->fetch_assoc();
		$train_index=$row['index_no'];	

		$start=$rowCount;	
		$end=$start*1+3;
			
		addContent(setRange("A".$start,"A".$end),$excel,$train_index,"true",$ExWs);


		

		$sql3="select * from train_switch where train_ava_id='".$row['id']."' order by date_change";
		$rs3=$db->query($sql3);
		$nm3=$rs3->num_rows;

		if($nm3>4){
			$nm3=4;
		}
		
		$col=66;
		for($n=0;$n<$nm3;$n++){
			$row3=$rs3->fetch_assoc();
			

			addContent(setRange(chr($col).$start,chr($col).($start+1)),$excel,date("H:i",strtotime($row3['date_change'])),"true",$ExWs);
			addContent(setRange(chr($col).($end-1),chr($col).$end),$excel,$row3['new_index'],"true",$ExWs);

			$col++;
			
		}	
			
		addContent(setRange("F".$start,"F".$start),$excel,$row['car_a'],"true",$ExWs);
		addContent(setRange("F".($start+1),"F".($start+2)),$excel,$row['car_b'],"true",$ExWs);
		addContent(setRange("F".($end),"F".$end),$excel,$row['car_c'],"true",$ExWs);
			
		$sql2="select * from train_ava_time where train_ava_id='".$row['id']."'";
		$rs2=$db->query($sql2);
		$row2=$rs2->fetch_assoc();

		if($row2['boundary_time']==""){
			$boundary_time="";
		}
		else {
			$boundary_time=date("H:i",strtotime($row2['boundary_time']));
		}	
		
		if($row2['insert_time']==""){
			$insert_time="";
			$insert_driver="";
		}
		else {

			if($row2['insert_time']=="0000-00-00 00:00:00"){
				$insert_date="";
				$insert_time="";
			}
			else {		
				$insert_time=date("H:i",strtotime($row2['insert_time']));
				$insert_date=date("Y-m-d",strtotime($row2['insert_time']));

				$insert_time=date("H:i",strtotime($row2['insert_time']));
				$insert_date=date("Y-m-d",strtotime($row2['insert_time']));
				if(strtotime($availability_date)>strtotime($insert_date)){

					$insert_time=$insert_date."\n".$insert_time;
				}
			}
			
			
			
			$inserted_to=$row2['inserted_to'];
			
			if($row['type']=="unimog"){
				$insert_driver=getPHTrainDriver($row2['insert_driver'],$db)."\nMAINTENANCE PROVIDER";
			}

			else if($row['type']=="test"){
				$insert_driver=getPHTrainDriver($row2['insert_driver'],$db)."\nMAINTENANCE PROVIDER";
			}
			else if($row['type']=="reserve"){
				$insert_driver=$row2['insert_driver'];
			}

			else {
				$insert_driver=getTrainDriver($row2['insert_driver'],$db);
			
			
			}
			if($inserted_to=="quezon"){ $inserted_to="Quezon Ave.\n"; }			
			else { $inserted_to=""; }			
			
			
		}		

		if($row2['remove_time']==""){
			$remove_time="";
			$remove_driver="";
			$remove_remarks="";

		}
		else {
			if($row2['remove_time']=="0000-00-00 00:00:00"){
				$remove_time="";
				$remove_date="";
			}			
			else {		
			
				$remove_date=date("Y-m-d",strtotime($row2['remove_time']));

				$remove_time=date("H:i",strtotime($row2['remove_time']));
				if(strtotime($availability_date)>strtotime($remove_date)){
					$remove_time=$remove_date."\n".$remove_time;
				}

			
			}
			if($row['type']=="unimog"){
				$remove_driver=getPHTrainDriver($row2['remove_driver'],$db)."\nMAINTENANCE PROVIDER";
			}

			else if($row['type']=="test"){
				$remove_driver=getPHTrainDriver($row2['remove_driver'],$db)."\nMAINTENANCE PROVIDER";
			}
			else if($row['type']=="reserve"){
				$remove_driver=$row2['remove_driver'];
			}

			else {
				$remove_driver=getTrainDriver($row2['remove_driver'],$db);
			}
			if($removed_from=="quezon"){ $removed_from="Quezon Ave.<br/>"; }			
			else { $removed_from=""; }
			$remove_remarks=$row2['removal_remarks'];

			
			
			
			
			
			
		}




		if($boundary_time==""){
		}
		else {
		addContent(setRange("G".$start,"G".$end),$excel,$boundary_time,"true",$ExWs);
		}

		$cancelSQL="select * from train_incident_view where train_ava_id='".$row['id']."'";
		
		
		$cancelRS=$db->query($cancelSQL);
		$incidentClause="";	

		$level2Clause="";	
		$level3Clause="";
			
		$l2Count=0;
		$l3Count=0;
		
		$cancelNM=$cancelRS->num_rows;
		if($cancelNM>0){
			for($m=0;$m<$cancelNM;$m++){
			$cancelRow=$cancelRS->fetch_assoc();		
			$level=$cancelRow['level'];			
			$order=getLevel($cancelRow['incident_id'],$db);
				if($level==1){
				}
				else {
					
				
				}
				
				if($m==0){
					$incidentClause.="SEE IN ".$cancelRow['incident_no'];
				}
				else {
					$incidentClause.=",\n";
					$incidentClause.="IN ".$cancelRow['incident_no'];
				}
				
				
				if($level==2){
					if($l2Count==0){
						$level2Clause.=getOrdinal($order);
					}
					else {
						$level2Clause.=",\n";
						$level2Clause.=getOrdinal($order);
						
					}
					$l2Count++;

				}
				else if($level==3){
					if($l3Count==0){
						$level3Clause.=getOrdinal($order);
					}
					else {
						$level3Clause.=",\n";
						$level3Clause.=getOrdinal($order);
						
					}
					$l3Count++;

				}

			}
			
		}
		
		
		
		
		$excel->getActiveSheet()->getStyle("L".$start.":O".$end)->getAlignment()->setWrapText(true);
		$excel->getActiveSheet()->getStyle("I".$start.":I".$end)->getAlignment()->setWrapText(true);
		$excel->getActiveSheet()->getStyle("K".$start.":K".$end)->getAlignment()->setWrapText(true);

		
		if($row['status']=="active"){
			addContent(setRange("H".$start,"H".$end),$excel,$inserted_to.$insert_time,"true",$ExWs);
			addContent(setRange("I".$start,"I".$end),$excel,$insert_driver,"true",$ExWs);
			addContent(setRange("J".$start,"J".$end),$excel,$removed_from.$remove_time,"true",$ExWs);
			addContent(setRange("K".$start,"K".$end),$excel,$remove_driver,"true",$ExWs);
			
			
			
			
			
		
		}
		else {
			if($boundary_time==""){
			addContent(setRange("G".$start,"K".$end),$excel,"CANCELLED","true",$ExWs);

			}
			else {
			addContent(setRange("H".$start,"K".$end),$excel,"CANCELLED","true",$ExWs);
			}


		}

		addContent(setRange("L".$start,"O".$end),$excel,$remove_remarks."\n".$incidentClause,"true",$ExWs);

		addContent(setRange("P".$start,"P".$end),$excel,$level2Clause,"true",$ExWs);
		addContent(setRange("Q".$start,"Q".$end),$excel,$level3Clause,"true",$ExWs);

		
//		addContent(setRange("I".$start,"I".$end),$excel,"First Line\nSecond Line\nFirst Line\nSecond Line","true",$ExWs);

//		$excel->getActiveSheet()->getStyle("I".$start.":I".$end)->getAlignment()->setWrapText(true);
			
	
		if($page_counter==7){	
			$page_counter=1;	
			
			$rowCount+=2;
			$rowCount+=17;
			
		
		}
		else {
			$page_counter++;
			$rowCount+=4;	
		}
	
	
	}

	save($ExWb,$excel,$newFilename); 	
	echo "Train Availability Report has been generated!  Press right click and Save As: <a href='".$newFilename."'>Here</a>";




}
?>
