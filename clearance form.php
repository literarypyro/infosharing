<?php
session_start();
?>
<style type='text/css'>
.rowClass {
	background-color:#eaf2d3;
}
.rowHeading {
	background-color:#a7c942;
	color:rgb(0,51,153);
}
.train_ava td{
	border: 1px solid #a7c942;
	color: rgb(0,51,153);
	cellpadding: 5px;

}
 .train_ava th {
	border: 1px solid #a7c942;
	cellpadding: 5px;
	
}
body {
	margin-left:30px;
	margin-right:30px;

}
input[type="text"]{ 
	height:25px; 
	font-weight:bold; 
	font-size:15px; 
	font-family:courier; 
	border: 1px solid #C6C6C6; 
	background-color: rgb(185, 201, 254);  
	color: rgb(0,51,153);
	border-radius: 3px;
}
#cellHeading {
	background-image: -o-linear-gradient(bottom, rgb(185, 201, 254) 38%, #4ad 62%);
	background-image: -moz-linear-gradient(bottom, rgb(185, 201, 254) 38%,#4ad 62%);
	background-image: -moz-linear-gradient(bottom, rgb(185, 201, 254) 38%, #4ad 62%);
	background-image: -webkit-gradient(linear, left bottom, left top, color-stop(0.38, rgb(185, 201, 254)), color-stop(0.62, #4ad));
	background-image: -webkit-linear-gradient(bottom, rgb(185, 201, 254) 38%,#4ad 62%);
	background-image: -ms-linear-gradient(bottom, rgb(185, 201, 254) 38%, #4ad 62%);
	background-image: linear-gradient(bottom, rgb(185, 201, 254) 38%, #4ad 62%);

	background-color: rgb(185, 201, 254);  

	color: rgb(0,51,153);
	padding:5px;
	-moz-border-radius: 5px;
	border-radius: 5px;

}
input[type="text"]:focus {
	background-color:rgb(158,27,32);
	color:white;

}
textarea:focus {
	background-color:rgb(158,27,32);
	color:white;
	font-weight:bold;

}
.date {
	text-style:bold;
	font-size:20px;

}
textarea{ 
	border: 1px solid rgb(185, 201, 254);
	background-color: rgb(185, 201, 254);  
	color: rgb(0,51,153);
	border-radius: 3px;
}
#add_form th{
background-color: #4ad;  
}

#add_form td:nth-child(odd) {
background-color: #33aa55; 
color:white;
font-weight:bold;
padding:5px;

}
#add_form td:last-child{
background-color:white;

}


#add_form td:nth-child(even) {
background-color: rgb(185, 201, 254);  
border:1px solid #4ad;

}


select { border: 1px solid rgb(185, 201, 254); color: rgb(0,51,153); background-color: rgb(185, 201, 254);  }
</style>
<?php
function setTime($hour,$minute,$amorpm){


	if($amorpm=="pm"){
		if($hour<12){
			$hour+=12;
			
		}
		else {
		}
	
	}
	else {
		if($hour=="12"){
			$hour=0;
			
		}
	
	}
	$timestring=$hour.":".$minute;
	
	return $timestring;
}

?>
<?php
if(isset($_POST['clearanceId'])){
	$db=new mysqli("localhost","root","","transport");
	
	$day=$_SESSION['day'];
	$month=$_SESSION['month'];
	$year=$_SESSION['year'];	

	$clearance_date=$year."-".$month."-".$day;

	$update="update clearance ";

	if(($_POST['formElement']=="logout")||($_POST['formElement']=="login")){

		$hour=$_POST[$_POST['formElement']."_hour"];
		$minute=$_POST[$_POST['formElement']."_minute"];
		$amorpm=$_POST[$_POST['formElement']."_amorpm"];
		
		$clearance_timestamp=$clearance_date." ".setTime($hour,$minute,$amorpm);	

		$update.=" set ".$_POST['formElement']."='".$clearance_timestamp."' ";
		
		
		
		
		
	}
	else if($_POST['formElement']=="position") {
		$position=$_POST['position'];
		$company=$_POST['company'];

		$update.=" set ".$_POST['formElement']."='".$_POST[$_POST['formElement']]."', company='".$_POST['company']."' ";
	
	
	}
	else {
		$update.=" set ".$_POST['formElement']."='".$_POST[$_POST['formElement']]."' ";
	
	
	
	}
	$update.=" where clearance_no='".$_POST['clearanceId']."' and date='".$clearance_date."'";
	$updateRS=$db->query($update);
	

}
?>
<script language='javascript' src='ajax.js'></script>
<script language='javascript'>
function deleteRow(index,index_date){
	var check=confirm("Remove Record?");
	if(check){
	makeajax("processing.php?removeClearance="+index+"&removeDate="+index_date,"reloadPage");	
	}
}
function reloadPage(ajaxHTML){
	self.location="clearance form.php";


}
function fillEdit(element,clearance_id){
	var elementHTML="";

	elementHTML+="<table name='add_form' id='add_form' >";
	
	
	if((element=="login")||(element=="logout")){
		
		elementHTML+="<tr>";
		elementHTML+="<td>Enter "+element.toUpperCase()+"</td>";
		
		
		var prefix=element;
		
		var d=new Date();
		
		var year=d.getFullYear();
		var mmonth=d.getMonth()*1+1;
		var day=d.getDate();
		
		var tentativehour=d.getHours();
		var minute=d.getMinutes();
		var hour=0;

		var amorpm="AM";
	
		if(tentativehour==0){
			hour=12;
			
			amorpm="AM";
		
		}
		else {
			if(tentativehour>12){
				hour=tentativehour-12;
				amorpm="PM";
			}
			else {
				hour=tentativehour;
				amorpm="AM";
			}
		
		}	
		
		
		
		
		elementHTML+="<td>";		
		elementHTML+="<select name='"+prefix+"_hour'>";
		elementHTML+="<option></option>";
		
		
		for(var i=1;i<=12;i++){
			elementHTML+="<option value='"+i+"' ";
			if(hour==i){
				elementHTML+="selected";
			}
			elementHTML+=">"+i+"</option>";
		}
		elementHTML+="</select>";

		
		elementHTML+="<select name='"+prefix+"_minute'>";
		elementHTML+="<option></option>";		
		var label="";
		for(var i=0;i<=59;i++){
			
			if(i<10){
				label="0"+i;			
			}
			else {
				label=i;
			}
			
			elementHTML+="<option value='"+i+"' ";
			if(minute==i){
			elementHTML+="selected";
			}
			elementHTML+=">"+label+"</option>";

		}
		elementHTML+="</select>";

		
		elementHTML+="<select name='"+prefix+"_amorpm'>";	
		elementHTML+="<option></option>";
		elementHTML+="<option value='am' ";
		if(amorpm=="AM"){
			elementHTML+="selected";
		}
		elementHTML+=">AM</option>";

		elementHTML+="<option value='pm' ";
		if(amorpm=="PM"){
			elementHTML+="selected";
		}
		elementHTML+=">PM</option>";			
		
		elementHTML+="</select>";
		
		elementHTML+="</td>";
		elementHTML+="</tr>";	
		
	}
	else if((element=="activity")||(element=="location")){
		elementHTML+="<tr>";
		elementHTML+="<td>Enter "+element.toUpperCase()+"</td>";

		elementHTML+="<td><textarea rows=5 cols=50 name='"+element+"'></textarea></td>";

		elementHTML+="</tr>";	

	}
	else if(element=="position"){
		elementHTML+="<tr>";
		elementHTML+="<td>Enter POSITION</td>";
		elementHTML+="<td><input type=text name='position' /></td>";
		elementHTML+="</tr>";
	
		elementHTML+="<tr>";
		elementHTML+="<td>Enter COMPANY</td>";
		elementHTML+="<td><input type=text name='company' /></td>";
		elementHTML+="</tr>";
	
	}
	else if(element=="received_by"){
	
		elementHTML+="<tr>";
		elementHTML+="<td>Enter RECEIVED BY</td>";
		elementHTML+="<td><select name='received_by' id='received_by'>";
		elementHTML+="</select>";
		elementHTML+="</td>";
		elementHTML+="</tr>";
	
	
	
	}
	
	else {
		elementHTML+="<tr>";
		elementHTML+="<td>Enter "+element.toUpperCase()+"</td>";
		elementHTML+="<td><input type=text name='"+element+"' /></td>";
		elementHTML+="</tr>";
	
	}
	
	elementHTML+="<tr>";
	
	elementHTML+="<td colspan=2 align=center>";
	elementHTML+="<input type=hidden name='clearanceId' id='clearanceId' value='"+clearance_id+"' />";
	elementHTML+="<input type=hidden name='formElement' id='formElement' value='"+element+"' />";

	elementHTML+="<input type=submit value='Edit' />";	
	elementHTML+="</td>";
	elementHTML+="</tr>";
	elementHTML+="</table>";
	
	document.getElementById('clearance_edit').innerHTML=elementHTML;	

	if(element=="received_by"){
	makeajax("processing.php?received_by=Y","fillReceived");			
	
	}
	
}
function fillReceived(ajaxHTML){
	if(ajaxHTML=="None available"){
	}
	else {

		var driverHTML="";
		var driverTerms=ajaxHTML.split("==>");
		var count=(driverTerms.length)*1-1;
		
		for(var n=0;n<count;n++){
			var parts=driverTerms[n].split(";");
			driverHTML+="<option value='"+parts[0]+"'>";
			driverHTML+=parts[1].replace("_ENYE_","Ñ");
			driverHTML+="</option>";
		
		}
		
	}
	document.getElementById('received_by').innerHTML=driverHTML;

}

</script>
<body>
<?php
require("monitoring menu.php");
?>
<br>
<br>
<form action='clearance form.php' method='post'>
<?php
$mm=date("m");
$yy=date("Y");
$dd=date("d");

$hh=date("h");

$min=date("i");
$aa=date("a");


if(isset($_POST['day'])){
	$yy=$_POST['year'];
	$mm=$_POST['month'];
	$dd=$_POST['day'];
	
	$_SESSION['day']=$_POST['day'];
	$_SESSION['month']=$_POST['month'];
	$_SESSION['year']=$_POST['year'];
	
}	



?>

<select name='month'>
<?php
for($i=1;$i<13;$i++){
?>
	<option value='<?php echo $i; ?>' 
	<?php
	if($i==$mm){
		echo "selected";
	}
	?>
	>
	<?php
	echo date("F",strtotime(date("Y")."-".$i."-01"));
	?>
	</option>
<?php
}
?>
</select>
<select name='day'>
<?php
for($i=1;$i<=31;$i++){
?>
	<option value='<?php echo $i; ?>' 
	<?php
	if($i==$dd){
		echo "selected";
	}
	?>		
	>
	<?php
	
	echo $i;
	?>
	</option>
<?php
}
?>
</select>
<select name='year'>
<?php
$dateRecent=date("Y")*1+16;
for($i=1999;$i<=$dateRecent;$i++){
?>
	<option value='<?php echo $i; ?>' 
	<?php
	if($i==$yy){
		echo "selected";
	}
	?>		
	>
	<?php
	echo $i;
	?>
	</option>
<?php
}
?>
</select>
<input type=submit value='Retrieve Date' />
</form>

<br>
<table class='train_ava' width=100%>
<tr class='rowHeading'>
	<th>Clearance No.</th>
	<th>Location</th>
	<th>Activity</th>
	<th>Person</th>
	<th>Position/Company</th>
	<th>Received By</th>
	<th>Login</th>
	<th>Logout</th>
	<th>Work Permit/Control No.</th>
</tr>
<?php
if((isset($_POST['day']))||(isset($_SESSION['day']))){
	if(isset($_POST['day'])){
		$year=$_POST['year'];
		$month=$_POST['month'];
		$day=$_POST['day'];
	}
	
	if(isset($_SESSION['day'])){
		$year=$_SESSION['year'];
		$month=$_SESSION['month'];
		$day=$_SESSION['day'];
	
	
	}
	
	$clearance_date=date("Y-m-d",strtotime($year."-".$month."-".$day));

	$db=new mysqli("localhost","root","","transport");	
	
	$sql="select * from clearance where date like '".$clearance_date."%%' order by clearance_no";
	$rs=$db->query($sql);
	$nm=$rs->num_rows;	
	
	if($nm>0){
		for($i=0;$i<$nm;$i++){
			$row=$rs->fetch_assoc();
			$clearance_no=$row['clearance_no'];
			$location=$row['location'];
			$activity=$row['activity'];
			$person=$row['person'];
			$position=$row['position'];
			$company=$row['company'];
			$received_by=$row['received_by'];
			$login=$row['login'];
			$logout=$row['logout'];
		
			$sql2="select * from train_driver where id='".$received_by."'";
			$rs2=$db->query($sql2);
			$nm2=$rs2->num_rows;
			
			if($nm2>0){
				$row2=$rs2->fetch_assoc();
				$received_by=$row2['position']." ".substr($row2['firstName'],0,1).". ".$row2['lastName'];
			
			}

		
			if($login=="0000-00-00 00:00:00"){
				$login="";
			}
			else {
				$login=date("H:i",strtotime($row['login']));
			}
			
			if($logout=="0000-00-00 00:00:00"){
				$logout="";
			}
			else {
				$logout=date("H:i",strtotime($row['logout']));
			}
			$control_no=$row['control_no'];
			
?>			
			<tr <?php if($i%2>0){ echo "class='rowClass'"; } ?>>
				<td align=center><?php echo $clearance_no; ?></td>	
				<td><?php echo $location; ?> <a href='#' onclick="fillEdit('location','<?php echo $clearance_no; ?>')">Edit</a></td>

				<td><?php echo $activity; ?> <a href='#' onclick="fillEdit('activity','<?php echo $clearance_no; ?>')">Edit</a></td>
				<td><?php echo $person; ?> <a href='#' onclick="fillEdit('person','<?php echo $clearance_no; ?>')">Edit</a></td>
				<td><?php echo $position." / ".$company; ?> <a href='#' onclick="fillEdit('position','<?php echo $clearance_no; ?>')">Edit</a></td>
				<td><?php echo $received_by; ?> <a href='#' onclick="fillEdit('received_by','<?php echo $clearance_no; ?>')">Edit</a></td>
				<td><?php echo $login; ?> <a href='#' onclick="fillEdit('login','<?php echo $clearance_no; ?>')">Edit</a></td>
				<td><?php echo $logout; ?>  <a href='#' onclick="fillEdit('logout','<?php echo $clearance_no; ?>')">Edit</a></td>
				<td><?php echo $control_no; ?>  <a href='#' onclick="fillEdit('control_no','<?php echo $clearance_no; ?>')">Edit</a></td>
				<td><a href='#' onclick="deleteRow('<?php echo $clearance_no; ?>','<?php echo $clearance_date; ?>')">X</a></td>
			</tr>	
			
<?php		
		}	
	}
	

}


?>
</table>
<a href='#' onclick='window.open("clearance entry.php");'>Add New Entry</a>
<br>
<a href='#' onclick='window.open("generate_clearance_form.php?clearance_date=<?php echo $clearance_date; ?>");'>Generate Printout</a>


<br>
<br>

<form action='clearance form.php' method='post'>
<div id='clearance_edit' name='clearance_edit'>




</div>
</form>
</body>