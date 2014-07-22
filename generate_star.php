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
	$star_id=$_GET['star_id'];
	$timetable=$_GET['timetable'];
	
	$filename="";
	if($star_id=="1"){
		$filename="STAR-WD29.xls";
		
	}
	else if($star_id=="2"){
		$filename="STAR-SAT12.xls";
		
		
	}
	else if($star_id=="3"){
		$filename="STAR-SUN13.xls";
		
		
	}
	else if($star_id=="4"){
		$filename="STAR-HOL07.xls";
	}
	
	else if($star_id=="5"){
		$filename="STAR-HOL08.xls";
	}

	else if($star_id=="6"){
		$filename="STAR-HOL09.xls";
	}
	else if($star_id=="7"){
		$filename="STAR-WD30.xls";
	}
	$oldfilename="forms/".$filename;
	$dateSlip=date("Y-m-d His");	
	
	$newFilename="printout/STAR_".$dateSlip.".xls";
	copy($oldfilename,$newFilename);

	$workSheetName="STAR";	
	$workbookname=$newFilename;
	$excel=loadExistingWorkbook($workbookname);

  	$ExWs=createWorksheet($excel,$workSheetName,"openActive");
	
	$db=new mysqli("localhost","root","","transport");
	
	$rowCount=0;	
	
	$rowCount+=14;
	$reserve="select * from reserve_1 where date='".$timetable."'";
	$rRS=$db->query($reserve);
	
	$rRow=$rRS->fetch_assoc();


	$reserve="select * from reserve_2 where date='".$timetable."'";
	$rRS=$db->query($reserve);
	
	$rRow2=$rRS->fetch_assoc();
	


	$provided="select * from cars_provided_1 where date='".$timetable."'";
	$pRS=$db->query($provided);
	
	$pRow=$pRS->fetch_assoc();


	$provided="select * from cars_provided_2 where date='".$timetable."'";
	$pRS=$db->query($provided);
	
	$pRow2=$pRS->fetch_assoc();



	$sql="select * from train_availability_required inner join timetable_hour on train_availability_required.time=timetable_hour.time where timetable_code='".$star_id."'";
	$rs=$db->query($sql);
	
	$nm=$rs->num_rows;

	addContent(setRange("J9","L9"),$excel,date("F d, Y",strtotime($timetable)),"true",$ExWs);

	addContent(setRange("J8","L8"),$excel,date("l",strtotime($timetable)),"true",$ExWs);

	
	for($i=0;$i<$nm;$i++){
		$row=$rs->fetch_assoc();
		$hour=$row['time'];
		$nexthour=$hour+1;
		$loop_cancelled=0;		


		
		if($hour=="201"){
			$timestamp="between '".date("Y-m-d H:i:s",strtotime($timetable." 20:01:00"))."' and '".date("Y-m-d H:i:s",strtotime($timetable." 20:30:00"))."'";

		}
		else if($hour=="203"){
			$timestamp="between '".date("Y-m-d H:i:s",strtotime($timetable." 20:31:00"))."' and '".date("Y-m-d H:i:s",strtotime($timetable." 21:00:00"))."'";

		}
		else if($hour=="501"){
			$timestamp="between '".date("Y-m-d H:i:s",strtotime($timetable." 05:01:00"))."' and '".date("Y-m-d H:i:s",strtotime($timetable." 05:30:00"))."'";
		
		}		
		else {
		//	$hour=str_replace("30",":30",$hour);
			
			if($star_id=="7"){ $nexthour+=100; }
			$nexthour=str_replace("31",":31",$nexthour);

			
			
			
			$timestamp="between '".date("Y-m-d H:i:s",strtotime($timetable." ".str_replace("30",":31",$hour).":00"))."' and '".date("Y-m-d H:i:s",strtotime($timetable." ".$nexthour.":00"))."'";
		}
		

		$car_sql="select * from train_availability inner join train_ava_time on train_availability.id=train_ava_time.train_ava_id where date ".$timestamp."  and status='active' and type='revenue' and insert_time is not null";

		$car_rs=$db->query($car_sql);
		$car_nm=$car_rs->num_rows;
		
		$cars_provided=$car_nm*3;
		
		
		for($n=0;$n<$car_nm;$n++){
			$car_row=$car_rs->fetch_assoc();
			if($car_row['cancel_loop']=="SB"){
//				$loop_cancelled+=.5;
			}
			else if($car_row['cancel_loop']=="NB"){
//				$loop_cancelled++;
			
			}
			
		}
		
//  		$car_sql3="select sum(cancel) as cancel from train_incident_view where train_ava_id in (select id from train_availability where date like '".$timetable."%%') and incident_date ".$timestamp."";

  		$car_sql3="select sum(cancel) as cancel from incident_report where incident_date ".$timestamp."  and level in ('3','4')";

		
//		$car_sql3="select sum(cancel) as cancel from train_incident_view where train_ava_id in (select id from train_availability where date like '".$timetable."%%' and status='active') and incident_date ".$timestamp."";
		
		$car_rs3=$db->query($car_sql3);
		$car_nm3=$car_rs3->num_rows;
		if($car_nm3>0){
			$car_row3=$car_rs3->fetch_assoc();
			$loop_cancelled=$car_row3['cancel']*1;
		
		}
		
		

		$car_sql2="select * from train_availability where date ".$timestamp." and type='revenue' and status='cancelled'";

		$car_rs2=$db->query($car_sql2);
		$car_nm2=$car_rs2->num_rows;
		
		//$cars_cancelled=$car_nm2;
		$cars_cancelled=0;	
  		$car_sql3="select sum(cancel) as cancel from incident_report where incident_date ".$timestamp." and incident_type in ('gradual','c_loops')";

//		echo $car_sql3;
		$car_rs3=$db->query($car_sql3);
		$car_nm3=$car_rs3->num_rows;
		if($car_nm3>0){
			$car_row3=$car_rs3->fetch_assoc();
			$cars_cancelled+=$car_row3['cancel']*1;
		
		}
		
  		//$car_sql3="select sum(cancel) as cancel from incident_report inner join incident_description on incident_report.id=incident_id where incident_date ".$timestamp." and level_condition='1' and level='3' and cancel>=1 and incident_type in ('rolling')";
 		$car_sql3="select sum(cancel) as cancel from incident_report inner join incident_description on incident_report.id=incident_description.incident_id where incident_date ".$timestamp." and level in ('3','4') and cancel>=1 and incident_type in ('rolling')";
		
		//echo $car_sql3;
		$car_rs3=$db->query($car_sql3);
		$car_nm3=$car_rs3->num_rows;
		if($car_nm3>0){
			$car_row3=$car_rs3->fetch_assoc();
			$cars_cancelled+=$car_row3['cancel']*1;
		
		}
				
		//$loop_cancelled+=$cars_cancelled;
		
		if(is_decimal($loop_cancelled)){
			$loop_cancelled=round($loop_cancelled)." 1/2";
		
		}

		if($hour==201){
			$hourLabel="20";
		
		}
		else if($hour=="203"){
			$hourLabel="21";
		}
		else {
			$hourLabel=str_replace(":","",str_replace("30","",$hour));
		
		}
		
		if($hourLabel<=12){
			$reserve=$rRow['h_'.str_replace("30","",$hourLabel)]; 
		}
		else {
			$reserve=$rRow2['h_'.str_replace("30","",$hourLabel)]; 
		}		
		

		if($hourLabel<=12){
			$cars_provided=$pRow['h_'.str_replace("30","",$hourLabel)]; 
		}
		else {
			$cars_provided=$pRow2['h_'.str_replace("30","",$hourLabel)]; 
		}		
		addContent(setRange("D".$rowCount,"D".$rowCount),$excel,$cars_provided,"true",$ExWs);
		
		if($hour==201){
			$hourLabel="20";
		
		}
		else if($hour=="203"){
			$hourLabel="21";
		}
		else {
			$hourLabel=str_replace(":","",str_replace("30","",$hour));
		}
		
		if($hourLabel<=12){
			$reserve=$rRow['h_'.str_replace("30","",$hourLabel)]; 
		}
		else {
			$reserve=$rRow2['h_'.str_replace("30","",$hourLabel)]; 
		}
	
		addContent(setRange("E".$rowCount,"E".$rowCount),$excel,$reserve,"true",$ExWs);
		addContent(setRange("F".$rowCount,"F".$rowCount),$excel,$cars_cancelled,"true",$ExWs);
		addContent(setRange("G".$rowCount,"G".$rowCount),$excel,$loop_cancelled,"true",$ExWs);
	
		$incident="";	
//		$incident_sql="select * from train_incident_view inner join incident_description on train_incident_view.incident_id=incident_description.incident_id where train_ava_id in (select id from train_availability where date ".$timestamp." and type='revenue')";
//		$incident_sql="select * from train_incident_view inner join incident_description on train_incident_view.incident_id=incident_description.incident_id where incident_date ".$timestamp." and level='3'";
	
		$incident_sql="select * from incident_report inner join incident_description on incident_report.id=incident_description.incident_id where incident_date ".$timestamp." and ((incident_type in ('rolling') and level in ('3','4')) or (incident_type in ('gradual','c_loops','r_trains','unload','nload')))";

		$incident_rs=$db->query($incident_sql);
		
		$incident_nm=$incident_rs->num_rows;
		if($incident_nm>0){
			for($m=0;$m<$incident_nm;$m++){
				$incident_row=$incident_rs->fetch_assoc();
				if($m==0){
					$incident.="SEE IN ".$incident_row['incident_no'];
					
					if($incident_row['incident_type']=="rolling"){
						$incident.="(".$incident_row['index_no'].")";
					}
				}
				else {
					$incident.=", IN ".$incident_row['incident_no'];
					if($incident_row['incident_type']=="rolling"){
						$incident.="(".$incident_row['index_no'].")";
					}

				}
			}
		}
		else {
			echo "&nbsp;";			
		}
		
		$train_remarks="";

			$remarks_sql="select * from train_hourly_remarks where hourly_date='".$timetable."' and hour='".$hour."' limit 1";
			$remarks_rs=$db->query($remarks_sql);
			$remarks_nm=$remarks_rs->num_rows;
			
			if($remarks_nm>0){
				$remarks_row=$remarks_rs->fetch_assoc();
				$train_remarks=$remarks_row['remarks'];
			}
			else {
			
			}		
		
		
		
		addContent(setRange("H".$rowCount,"L".$rowCount),$excel,$incident." ".$train_remarks,"true",$ExWs);
		
	
		$rowCount++;
		
		
	}		
	

	save($ExWb,$excel,$newFilename); 	
	echo "<br>";
	echo "Train Hourly Report has been generated!  Press right click and Save As: <a href='".$newFilename."'>Here</a>";		
	
}

?>
