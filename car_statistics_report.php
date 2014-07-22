<?php
$db=new mysqli("localhost","root","","transport");




?>

<table border=1 style='border-collapse:collapse;' width=100%>
<tr><th colspan=13>Car Statistics Report</th></tr>
<tr>	
	<th>Car #</th>
	<th>January</th>
	<th>February</th>
	<th>March</th>
	<th>April</th>
	<th>May</th>
	<th>June</th>
	<th>July</th>
	<th>August</th>
	<th>September</th>
	<th>October</th>
	<th>November</th>
	<th>December</th>
</tr>
<?php
for($i=1;$i<=73;$i++){
	for($k=1;$k<=12;$k++){
		$stats["Car_".$i]["Month_".$k]=0;
	
	}
}

$sql="SELECT car_no,month(incident_date) as mo,sum(1) as count FROM incident_cars inner join incident_report on incident_cars.incident_id=incident_report.id where incident_date like '".date("Y")."-%%' group by car_no*1,month(incident_date)";
$rs=$db->query($sql);

$nm=$rs->num_rows;

if($nm>0){
	for($i=0;$i<$nm;$i++){
		$row=$rs->fetch_assoc();
		$car_id=$row['car_no']*1;
		$month=$row['mo']*1;
		
		$stats["Car_".$car_id]["Month_".$month]=$row['count'];
		
	}
}

for($i=1;$i<=73;$i++){
?>
<tr>
<th><?php echo $i; ?></th>
<?php
for($k=1;$k<=12;$k++){
?>
	<td align=center><?php echo $stats["Car_".$i]["Month_".$k]; ?></td>
<?php
}

?>
</tr>

<?php
}





?>
</table>