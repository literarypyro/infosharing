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
function getStats($problem_type,$availability_date,$dbase){
//	$db=new mysqli("localhost","root","","transport");
//	$incident_sql="select count(*) as level_count,level from train_incident_view where incident_type='".$problem_type."' and train_ava_id in (select id from train_availability where date like '".$availability_date."%%' and status='active') group by level";


	if($problem_type=="rolling"){
		$incident_sql="select count(*) as level_count,level from incident_report where incident_type in ('rolling','unload','nload') and incident_date like '".$availability_date."%%' group by level";
	}
	else {	
		$incident_sql="select count(*) as level_count,level from incident_report where incident_type='".$problem_type."' and incident_date like '".$availability_date."%%' group by level";
	}
	
	
	$incident_rs=$dbase->query($incident_sql);
	$incident_nm=$incident_rs->num_rows;
	for($n=0;$n<$incident_nm;$n++){
		$incident_row=$incident_rs->fetch_assoc();
		$incident[$incident_row['level']]=$incident_row['level_count'];
	
	}
	return $incident;

}
function getCondition($problem_type,$availability_date,$dbase){
//	$db=new mysqli("localhost","root","","transport");
//	$incident_sql="select count(*) as level_count,level,level_condition from train_incident_view where incident_type='".$problem_type."' and train_ava_id in (select id from train_availability where date like '".$availability_date."%%' and status='active') group by level,level_condition";


	if($problem_type=="rolling"){
		$incident_sql="select count(*) as level_count,level,level_condition from incident_report where incident_type in ('rolling','unload','nload') and incident_date like '".$availability_date."%%' group by level,level_condition";
	}
	else {	
	
		$incident_sql="select count(*) as level_count,level,level_condition from incident_report where incident_type='".$problem_type."' and incident_date like '".$availability_date."%%' group by level,level_condition";
	}

	$incident_rs=$dbase->query($incident_sql);
	$incident_nm=$incident_rs->num_rows;
	
	
	
	for($n=0;$n<$incident_nm;$n++){
		$incident_row=$incident_rs->fetch_assoc();
		if($incident_row['level_condition']==""){
		
		}
		else {
			$condition[$incident_row['level']]["condition_".$incident_row['level_condition']]=$incident_row['level_count'];

		}
	
	}
	return $condition;

}
?>



<?php
if(isset($_GET['sccdr'])){
	$sccdr_date=$_GET['sccdr'];

	$filename="SCCDR.xls";

	$oldfilename="forms/".$filename;
	$dateSlip=date("Y-m-d His");
	$newFilename="printout/SCCDR_".$dateSlip.".xls";
	copy($oldfilename,$newFilename);
	$workSheetName="SCCDR";	
	$workbookname=$newFilename;
	$excel=loadExistingWorkbook($workbookname);

  	$ExWs=createWorksheet($excel,$workSheetName,"openActive");
	
//	$category=array('rolling','station_equipt','signaling','power','afc','depot_equipt','communication','tracks','cc_equipt','gradual','c_loops','r_trains');
	$category=array('rolling','station_equipt','signaling','power','afc','depot_equipt','communication','tracks','cc_equipt');


		
	$rowCount=17;	
	

	
	
	
	
	
	addContent(setRange("R9","T9"),$excel,date("F d, Y",strtotime($sccdr_date)),"true",$ExWs);

	addContent(setRange("R8","T8"),$excel,date("l",strtotime($sccdr_date)),"true",$ExWs);	
	
	$db=new mysqli("localhost","root","","transport");




	$timeTableSQL="select *,timetable_day.id as timeId from timetable_day inner join timetable_code on timetable_day.timetable_code=timetable_code.id where train_date like '".$sccdr_date."%%'";

	$timeTableRS=$db->query($timeTableSQL);
	$timeTableNM=$timeTableRS->num_rows;
	if($timeTableNM>0){
		$timeTableRow=$timeTableRS->fetch_assoc();
		addContent(setRange("R10","T10"),$excel,$timeTableRow['code'],"true",$ExWs);
		
		
	}		
	
	
	
	
	
	
	
	for($i=0;$i<count($category);$i++){

		$stats=getStats($category[$i],$sccdr_date,$db);
		$condition=getCondition($category[$i],$sccdr_date,$db);
		
		
		if($condition['3']['condition_1']==""){
			$condition_1="'0/";
		
		}
		else {
			$condition_1="'".($condition['3']['condition_1']*1)."/";
		
		}
		

		if($condition['4']['condition_3']==""){
			$condition_3="";
		
		}
		else {
			$condition_3="'".($condition['4']['condition_3']*1)."/";
		
		}
		if($category[$i]=="rolling"){
			$am_sql="select sum(cancel) as pm_count from incident_report where incident_type in ('rolling','unload','nload') and incident_date like '".$sccdr_date."%%'  and level in ('3')";


		}
		else {
			$am_sql="select sum(cancel) as pm_count from incident_report where incident_type='".$category[$i]."' and incident_date like '".$sccdr_date."%%'  and level in ('3')";
		}
		$am_rs=$db->query($am_sql);
		$am_nm=$am_rs->num_rows;

		if($am_nm>0){
			$am_row=$am_rs->fetch_assoc();
			if($category[$i]=="rolling"){
			$stats['3']=$am_row['pm_count']*1;
			}
			else {
			$stats['3']=0;
			}

		}

		if($problem_type=="rolling"){
			$am_sql="select sum(cancel) as pm_count from incident_report where incident_type in ('rolling','unload','nload') and incident_date like '".$sccdr_date."%%'  and level in ('4')";
		}
		else {
			$am_sql="select sum(cancel) as pm_count from incident_report where incident_type='".$category[$i]."' and incident_date like '".$sccdr_date."%%'  and level in ('4')";
		}

		$am_rs=$db->query($am_sql);
		$am_nm=$am_rs->num_rows;

		if($am_nm>0){
			$am_row=$am_rs->fetch_assoc();
			if($category[$i]=="rolling"){
			$stats['4']=$am_row['pm_count']*1;
			}
		}

		
		
		addContent(setRange("B".$rowCount,"B".$rowCount),$excel,$stats['1'],"true",$ExWs);
		addContent(setRange("C".$rowCount,"C".$rowCount),$excel,$stats['2'],"true",$ExWs);
		addContent(setRange("D".$rowCount,"E".$rowCount),$excel,$condition_1.$stats['3'],"true",$ExWs);
		addContent(setRange("F".$rowCount,"G".$rowCount),$excel,$condition_3.$stats['4'],"true",$ExWs);
		
		
	
	
		$rowCount++;
	}	
	
	$incident_sql="select * from train_incident_view where level_condition='3' and train_ava_id in (select id from train_availability where date like '".$sccdr_date."%%')";

	$incident_rs=$db->query($incident_sql);
	$incident_nm=$incident_rs->num_rows;
	
	if($incident_nm>0){
	
		addContent(setRange("A12","U12"),$excel,"With service interruption.","true",$ExWs);
	
	}
	else {
	
		addContent(setRange("A12","U12"),$excel,"No service interruption.","true",$ExWs);
	
	}

	
	
	$availability_date=$sccdr_date;
	
	$db=new mysqli("localhost","root","","transport");
	
	//$am_sql="select count(*) as am_count from train_availability where date like '".$availability_date."%%' and status='cancelled' and date between '".$availability_date." 00:00:01' and '".$availability_date." 12:00:00'";

	$am_sql="select sum(cancel) as am_count from incident_report inner join incident_description on incident_report.id=incident_description.incident_id where incident_date between '".$availability_date." 00:00:00' and '".$availability_date." 12:00:00' and level='3' and cancel>=1 and incident_type in ('rolling')";




	$am_rs=$db->query($am_sql);
	$am_nm=$am_rs->num_rows;

	$am=0;
	if($am_nm>0){
		$am_row=$am_rs->fetch_assoc();
		$am=$am_row['am_count'];
	}
	
	$car_sql3="select sum(cancel) as cancel from incident_report where incident_date between '".$availability_date." 00:00:01' and '".$availability_date." 12:00:00' and incident_type in ('gradual','c_loops')";

	//		echo $car_sql3;
	$car_rs3=$db->query($car_sql3);
	$car_nm3=$car_rs3->num_rows;
	if($car_nm3>0){
		$car_row3=$car_rs3->fetch_assoc();
		$am+=$car_row3['cancel']*1;

	}

	

	//$am_sql="select count(*) as pm_count from train_availability where date like '".$availability_date."%%' and status='cancelled' and date between '".$availability_date." 12:00:01' and '".$availability_date." 23:59:59'";

	$am_sql="select sum(cancel) as pm_count from incident_report inner join incident_description on incident_report.id=incident_description.incident_id where incident_date between '".$availability_date." 12:00:01' and '".$availability_date." 23:59:59' and level='3' and cancel>=1 and incident_type in ('rolling')";

	
	$am_rs=$db->query($am_sql);
	$am_nm=$am_rs->num_rows;

	$pm=0;
	if($am_nm>0){
		$am_row=$am_rs->fetch_assoc();
		$pm=$am_row['pm_count'];
	}

	$car_sql3="select sum(cancel) as cancel from incident_report where incident_date between '".$availability_date." 12:00:01' and '".$availability_date." 23:59:59' and incident_type in ('gradual','c_loops')";

	//		echo $car_sql3;
	$car_rs3=$db->query($car_sql3);
	$car_nm3=$car_rs3->num_rows;
	if($car_nm3>0){
		$car_row3=$car_rs3->fetch_assoc();
		$pm+=$car_row3['cancel']*1;

	}
	
	

//	$am_sql="select sum(cancel) as am_count from train_incident_view where train_ava_id in (select id from train_availability where date like '".$availability_date."%%' and status='active') and incident_date between '".$availability_date." 00:00:01' and '".$availability_date." 12:00:00'";
//	$am_sql="select sum(cancel) as am_count from train_incident_view where train_ava_id in (select id from train_availability where date like '".$availability_date."%%') and incident_date between '".$availability_date." 00:00:01' and '".$availability_date." 12:00:00'";
	$am_sql="select sum(cancel) as am_count from incident_report where incident_date between '".$availability_date." 00:00:01' and '".$availability_date." 12:00:00'  and level in ('3','4')";


	$am_rs=$db->query($am_sql);
	$am_nm=$am_rs->num_rows;

	$am_cancel=0;
	if($am_nm>0){
		$am_row=$am_rs->fetch_assoc();
		$am_cancel=$am_row['am_count']*1;
	}

//	$pm_sql="select sum(cancel) as pm_count from train_incident_view where train_ava_id in (select id from train_availability where date like '".$availability_date."%%' and status='active') and incident_date between '".$availability_date." 12:00:01' and '".$availability_date." 00:00:00'";
	//$pm_sql="select sum(cancel) as pm_count from train_incident_view where train_ava_id in (select id from train_availability where date like '".$availability_date."%%') and incident_date between '".$availability_date." 12:00:01' and '".$availability_date." 00:00:00'";

	$pm_sql="select sum(cancel) as pm_count from incident_report where incident_date between '".$availability_date." 12:00:01' and '".$availability_date." 23:59:59'  and level in ('3','4')";
	
	$pm_rs=$db->query($pm_sql);
	$pm_nm=$pm_rs->num_rows;

	$pm_cancel=0;
	if($pm_nm>0){
		$pm_row=$pm_rs->fetch_assoc();
		$pm_cancel=$pm_row['pm_count']*1;
	}

	
	$planned=0;
	$actual=0;
	$percentage=0;

	$planned_sql="select * from timetable_day inner join timetable_code on timetable_code=timetable_code.id where train_date='".$availability_date."'";
	$planned_rs=$db->query($planned_sql);
	$planned_nm=$planned_rs->num_rows;
	if($planned_nm>0){

		$planned_row=$planned_rs->fetch_assoc();
		//$am_sql="select sum(cancel) as cancel from train_incident_view where train_ava_id in (select id from train_availability where date like '".$availability_date."%%' and status='active')";
		$am_sql="select sum(cancel) as cancel from incident_report where incident_date like '".$availability_date."%%' and incident_type in ('rolling','gradual','c_loops','r_trains','unload','nload')";

		$am_rs=$db->query($am_sql);
		$am_nm=$am_rs->num_rows;
		$am_row=$am_rs->fetch_assoc();
		$planned=$planned_row['planned_loops'];
		$actual=$planned_row['planned_loops']*1-$am_row['cancel']*1;

		
		$percentage=$actual/$planned;
		
		
	}	
	
	$train_sql="select * from train_availability inner join train_compo on train_availability.id=tar_id where train_availability.date like '".$availability_date."%%' and status='active' group by car_no";

//	$train_sql="select * from train_availability where date like '".$availability_date."%%' and status='active'";
	$train_rs=$db->query($train_sql);
	$train_nm=$train_rs->num_rows;

	addContent(setRange("Q19","Q19"),$excel,$am,"true",$ExWs);
	addContent(setRange("R19","R19"),$excel,$am_cancel,"true",$ExWs);

	addContent(setRange("S19","S19"),$excel,$pm,"true",$ExWs);
	addContent(setRange("T19","T19"),$excel,$pm_cancel,"true",$ExWs);	
	
	addContent(setRange("Q21","R21"),$excel,$planned,"true",$ExWs);
	addContent(setRange("S21","T21"),$excel,$actual,"true",$ExWs);	
	
	addContent(setRange("Q23","R23"),$excel,$percentage,"true",$ExWs);
	addContent(setRange("S23","T23"),$excel,$train_nm,"true",$ExWs);	

	save($ExWb,$excel,$newFilename); 	
	echo "CCDR Summary has been generated!  Press right click and Save As: <a href='".$newFilename."'>Here</a>";
	
}
?>