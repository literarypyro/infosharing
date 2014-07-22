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
	$db=new mysqli("localhost","root","","transport");

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
		
		$sql="select * from timetable_hour where timetable_id='".$timetable_id."' order by time_from";
		
		$rs=$db2->query($sql);
		$nm=$rs->num_rows;
		
		$rowCount=14;	
		if($nm>0){
			
			for($i=0;$i<$nm;$i++){
				$row=$rs->fetch_assoc();		
				
				$sql3="select * from timetable_provided where timetable_id='".$timetable_id."' and hour_id='".$row['id']."' and timetable_date like '".$timetable."%%'";
				$rs3=$db2->query($sql3);
				$nm3=$rs3->num_rows;

				$cars_provided=0;
				$reserve_provided=0;			
				
				if($nm3>0){
					$row3=$rs3->fetch_assoc();
					$cars_provided=$row3['cars_provided'];
					$reserve_provided=$row3['reserve_provided'];
				}
				
				$sql4="select * from timetable_remarks where timetable_id='".$timetable_id."' and hour_id='".$row['id']."' and timetable_date like '".$timetable."%%'";
				$rs4=$db2->query($sql4);
				$nm4=$rs4->num_rows;

				$timetable_remarks="";
				
				if($nm4>0){
					$row4=$rs4->fetch_assoc();
					$timetable_remarks=$row4['timetable_remarks'];
				}
				
				$timestamp="between '".date("Y-m-d H:i:s",strtotime($timetable." ".$from.":00"))."' and '".date("Y-m-d H:i:s",strtotime($timetable." ".$to.":00"))."'";		
				
				$car_sql3="select sum(cancel) as cancel from incident_report where incident_date ".$timestamp." and level in ('3','4')";
				$car_rs3=$db->query($car_sql3);
				$car_nm3=$car_rs3->num_rows;
				if($car_nm3>0){
					$car_row3=$car_rs3->fetch_assoc();
					$loop_cancelled=$car_row3['cancel']*1;
				}

				$car_sql2="select * from train_availability where date ".$timestamp." and type='revenue' and status='cancelled'";
				$car_rs2=$db->query($car_sql2);
				$car_nm2=$car_rs2->num_rows;
				
				if(is_decimal($loop_cancelled)){
					$loop_cancelled=floor($loop_cancelled);
					if($loop_cancelled==0){ $loop_cancelled="1/2"; }
					else { $loop_cancelled.=" 1/2"; }
				}		
				$cars_cancelled=0;	
			
				$car_sql3="select sum(cancel) as cancel from incident_report where incident_date ".$timestamp." and incident_type in ('gradual','c_loops')";
				
				$car_rs3=$db->query($car_sql3);
				$car_nm3=$car_rs3->num_rows;
				if($car_nm3>0){
					$car_row3=$car_rs3->fetch_assoc();
					$cars_cancelled+=$car_row3['cancel']*1;
				}
					
			
				$car_sql3="select sum(cancel) as cancel from incident_report inner join incident_description on incident_report.id=incident_description.incident_id where incident_date ".$timestamp." and level in ('3','4') and cancel>=1 and incident_type in ('rolling')";

				$car_rs3=$db->query($car_sql3);
				$car_nm3=$car_rs3->num_rows;
				if($car_nm3>0){
					$car_row3=$car_rs3->fetch_assoc();
					$cars_cancelled+=$car_row3['cancel']*1;
				}
				
						

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
						
						
						

				addContent(setRange("D".$rowCount,"D".$rowCount),$excel,$cars_provided,"true",$ExWs);
				addContent(setRange("E".$rowCount,"E".$rowCount),$excel,$reserve_provided,"true",$ExWs);
					
				addContent(setRange("F".$rowCount,"F".$rowCount),$excel,$cars_cancelled,"true",$ExWs);
				addContent(setRange("G".$rowCount,"G".$rowCount),$excel,$loop_cancelled,"true",$ExWs);


				addContent(setRange("H".$rowCount,"L".$rowCount),$excel,$incident." ".$timetable_remarks,"true",$ExWs);



				
				$rowCount++;
					
				
				
			}
		}
		
		
		
		
		save($ExWb,$excel,$newFilename); 	
		
		
		
		
		echo "Train Hourly Report has been generated!  Press right click and Save As: <a href='".$newFilename."'>Here</a>";		
	


	}
	
}

?>
