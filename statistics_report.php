<form action='statistics_report.php' method='post'>
<table>
<tr><th>Level</th>
<td>
<select name='level'>
<option value='2'>2</option>
<option value='3'>3</option>
</select>
</td>
</tr>
<tr>
<th>Year</th>
<td>
<?php
$startYear=2013;

$endYear=date("Y")*1+16;

?>
<select name='year'>
<?php
for($k=$startYear;$k<=$endYear;$k++){
?>
	<option value="<?php echo $k; ?>"><?php echo $k; ?></option>

<?php
}
?>
</select>
</td>
</tr>
<tr>
<th colspan=2><input type=submit value='Submit' /></th>
</tr>
</table>
</form>

<?php
$db=new mysqli("localhost","root","","transport");

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

	
}

if(isset($_POST['year'])){
	$year=$_POST['year'];
	$level=$_POST['level'];
}
else {
	$year=date("Y");
	$level="2";
}

?>
<h2><?php echo "Year ".$year; ?></h2>
<h2><?php echo "Level ".$level; ?></h2>
<table border=1px style='border-collapse:collapse;' width=100%>
<tr>
<th>&nbsp;</th>
<th>Door Failure</th>
<th>Drive Circuit<br> Interlocking</th>
<th>Filter Undervoltage</th>
<th>Static Converter</th>
<th>Mechanical Brakes</th>
<th>Overcurrent</th>
<th>Unshaded, Weak,<br> Dropping Current</th>
<th>OCS Undervoltage</th>
<th>Communication Error</th>
<th>Start-up<br> Interlocking</th>
<th>Regulator</th>
<th>Main Chopper</th>
<th>Auxiliary Chopper</th>
<th><a href='#' onclick="window.open('statistics_others_report.php?year=<?php echo $year; ?>&level=<?php echo $level; ?>')">Others</a></th>
</tr>
<?php
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



	
	$sql="select *,count(1) as equipt_count from incident_report where level='".$level."' and incident_date between '".$start_date." 00:00:00' and '".$end_date." 23:59:59' and equipt in ('105','81','118','119','64','115','89','120','123','121','116','2','122','117') group by level";
	$rs=$db->query($sql);
	$nm=$rs->num_rows;

	if($nm>0){
		$row=$rs->fetch_assoc();
		$equipt_count["Month_".$i]["Equipt_Others"]=$row['equipt_count'];
	}

	$sql="select *,count(1) as equipt_count from incident_report inner join external.incident_defects on incident_report.id=external.incident_defects.incident_id where level='".$level."' and incident_date between '".$start_date." 00:00:00' and '".$end_date." 23:59:59' and external.incident_defects.equipt_id in ('105','81','118','119','64','115','89','120','123','121','116','2','122','117') group by level"; 
	$rs=$db->query($sql);
	$nm=$rs->num_rows;
	
	for($k=0;$k<$nm;$k++){
		$row=$rs->fetch_assoc();
		$equipt_count["Month_".$i]["Equipt_".$row['equipt_id']]+=$row['equipt_count'];
	}



	
?>	
	<tr>
	<td align=center><?php echo strtoupper($month_heading); ?></td>
	<td align=center><?php echo $equipt_count["Month_".$i]["Equipt_114"]; ?></td>
	<td align=center><?php echo $equipt_count["Month_".$i]["Equipt_102"]; ?></td>
	<td align=center><?php echo $equipt_count["Month_".$i]["Equipt_110"]; ?></td>
	<td align=center><?php echo $equipt_count["Month_".$i]["Equipt_11"]; ?></td>
	<td align=center><?php echo $equipt_count["Month_".$i]["Equipt_113"]; ?></td>
	<td align=center><?php echo $equipt_count["Month_".$i]["Equipt_104"]; ?></td>
	<td align=center><?php echo $equipt_count["Month_".$i]["Equipt_124"]; ?></td>
	<td align=center><?php echo $equipt_count["Month_".$i]["Equipt_109"]; ?></td>
	<td align=center><?php echo $equipt_count["Month_".$i]["Equipt_103"]; ?></td>
	<td align=center><?php echo $equipt_count["Month_".$i]["Equipt_108"]; ?></td>
	<td align=center><?php echo $equipt_count["Month_".$i]["Equipt_67"]; ?></td>
	<td align=center><?php echo $equipt_count["Month_".$i]["Equipt_111"]; ?></td>
	<td align=center><?php echo $equipt_count["Month_".$i]["Equipt_112"]; ?></td>
	<td align=center><?php echo $equipt_count["Month_".$i]["Equipt_Others"]; ?></td>
	</tr>	

<?php	
}


?>
</table>
<br>
<br>
<a href='#' onclick='window.open("generate_statistics_report.php?year=<?php echo $year; ?>&level=<?php echo $level; ?>");'>Generate Printout</a>