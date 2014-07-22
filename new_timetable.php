<?php
$db2=new mysqli("localhost","root","","timetable");
?>
<?php
if(isset($_POST['timetable_id'])){
	$from_hour=$_POST['from_hour'];
	$from_minute=$_POST['from_minute'];
	if($from_minute<10){ $from_minute="0".$from_minute; }
	
	$from_amorpm=$_POST['from_amorpm'];
	$from_time=date("Y-m-d H:i",strtotime(date("F d, Y")." ".$from_hour.":".$from_minute." ".$from_amorpm));	
	
	$to_hour=$_POST['to_hour'];
	$to_minute=$_POST['to_minute'];
	if($to_minute<10){ $to_minute="0".$to_minute; }

	$to_amorpm=$_POST['to_amorpm'];
	$to_time=date("Y-m-d H:i",strtotime(date("F d, Y")." ".$to_hour.":".$to_minute." ".$to_amorpm));	
	
	$update="insert into timetable_hour(timetable_id,time_label,time_from,time_to,headway) values ('".$_POST['timetable_id']."','".$_POST['time_label']."','".$from_time."','".$to_time."','".$_POST['headway']."')";
	
	$updateRS=$db2->query($update);
	
	$hour_id=$db2->insert_id;
	
	$cars_required=$_POST['cars_required'];
	$reserve_required=$_POST['reserve_required'];
	
	$insert="insert into timetable_required(timetable_id,hour_id,cars_required,reserve_required) values ('".$_POST['timetable_id']."','".$hour_id."','".$cars_required."','".$reserve_required."')";
	$insertRS=$db2->query($insert);

	$timetable_id=$_POST['timetable_id'];

}

if(isset($_POST['edit_hour'])){
	
	if(($_POST['formElement']=="time_from")||($_POST['formElement']=="time_to")){
		if($_POST['formElement']=="time_from") { $prefix="from"; }
		else if($_POST['formElement']=="time_to") { $prefix="to"; }
		
		$hour=$_POST[$prefix."_hour"];
		$minute=$_POST[$prefix."_minute"];
		$amorpm=$_POST[$prefix."_amorpm"];


		if($minute<10){ $minute="0".$minute; }
	
		$element_time=date("Y-m-d H:i",strtotime(date("F d, Y")." ".$hour.":".$minute." ".$amorpm));	

		$update="update timetable_hour set ".$_POST['formElement']."='".$element_time."' where id='".$_POST['edit_hour']."'";
		
		$updateRS=$db2->query($update);	
	



	}
	else if(($_POST['formElement']=="cars_required")||($_POST['formElement']=="reserve_required")){
		$timetable_id=$_GET['timetable_id'];
		
		$searchSQL="select * from timetable_required where hour_id='".$_POST['edit_hour']."' and timetable_id='".$timetable_id."'";
		$searchRS=$db2->query($searchSQL);
		$searchNM=$searchRS->num_rows;
		
		if($searchNM>0){
			$update="update timetable_required set ".$_POST['formElement']."='".$_POST['formValue']."' where hour_id='".$_POST['edit_hour']."' and timetable_id='".$timetable_id."'";
			$updateRS=$db2->query($update);	
		}
		else {
			$update="insert into timetable_required(timetable_id,hour_id,".$_POST['formElement'].") values ('".$timetable_id."','".$_POST['edit_hour']."','".$_POST['formValue']."')";
			$updateRS=$db2->query($update);	
			
		}
	}
	else {
		$update="update timetable_hour set ".$_POST['formElement']."='".$_POST['formValue']."' where id='".$_POST['edit_hour']."'";
		$updateRS=$db2->query($update);	
	
	}
}

if(isset($_GET['timetable_id'])){
	$timetable_id=$_GET['timetable_id'];
}

?>
<?php
$sql="select * from timetable where id='".$timetable_id."'";
$rs=$db2->query($sql);

$nm=$rs->num_rows;

if($nm>0){
	$row=$rs->fetch_assoc();
	$timetable_code=$row['code'];
	$report_file=$row['report_file'];
	
	echo "<h2>".$timetable_code."</h2>";
	

}

?>
<script language='javascript' src='ajax.js'></script>
<script language="javascript">
function deleteRow(index,timetable_id){
	var check=confirm("Remove Record?");
	if(check){
		makeajax("processing.php?removeTimetableHour="+index+"&timetable_id="+timetable_id,"reloadPage");	
	}
}

function reloadPage(ajaxHTML){
	var timetable_id=ajaxHTML;
	self.location="new_timetable.php?timetable_id="+timetable_id;


}

function editElement(type,hour_id){
	var editHTML="";
	editHTML+="<table style='border:1px solid gray'>";
	if(type=="headway"){
		editHTML+="<tr>";
		editHTML+="<td>Edit Headway</td>";
		editHTML+="<td><input type=text name='formValue' /></td>";
	
		editHTML+="</tr>";
		
		
		
	}
	else if(type=="cars_required"){
		editHTML+="<tr>";
		editHTML+="<td>Edit Cars Required</td>";
		editHTML+="<td><input type=text name='formValue' /></td>";
	
	
		editHTML+="</tr>";
		
		
	}
	else if(type=="reserve_required"){
		editHTML+="<tr>";
		editHTML+="<td>Edit Reserve Required</td>";
		editHTML+="<td><input type=text name='formValue' /></td>";
	
	
		editHTML+="</tr>";
		
		
	}
	else if(type=="time_from"){
		var prefix="from";
		
		
		
		
		
		editHTML+="<tr>";
		editHTML+="<td>Edit From Time</td>";
	
	
		editHTML+="<td>";

		editHTML+="<select name='"+prefix+"_hour'>";
		editHTML+="<option></option>";
		
		
		for(var i=1;i<=12;i++){
			editHTML+="<option value='"+i+"'";

			editHTML+=">"+i+"</option>";
		}
		editHTML+="</select>";

		
		editHTML+="<select name='"+prefix+"_minute'>";
		editHTML+="<option></option>";		
		var label="";
		for(var i=0;i<=59;i++){
			
			if(i<10){
				label="0"+i;			
			}
			else {
				label=i;
			}
			
			editHTML+="<option value='"+i+"' ";
			editHTML+=">"+label+"</option>";

		}
		editHTML+="</select>";

		
		editHTML+="<select name='"+prefix+"_amorpm'>";	
		editHTML+="<option></option>";
		editHTML+="<option value='am' ";
		editHTML+=">AM</option>";
		editHTML+="<option value='pm' ";
		editHTML+=">PM</option>";			
		
		editHTML+="</select>";
		
		editHTML+="</td>";
	
		editHTML+="</tr>";
		
		
	}
	else if(type=="time_to"){
		var prefix="to";
		editHTML+="<tr>";
		editHTML+="<td>Edit To Time</td>";
			
		editHTML+="<td>";
		editHTML+="<select name='"+prefix+"_hour'>";
		editHTML+="<option></option>";
		
		
		for(var i=1;i<=12;i++){
			editHTML+="<option value='"+i+"'";

			editHTML+=">"+i+"</option>";
		}
		editHTML+="</select>";

		
		editHTML+="<select name='"+prefix+"_minute'>";
		editHTML+="<option></option>";		
		var label="";
		for(var i=0;i<=59;i++){
			
			if(i<10){
				label="0"+i;			
			}
			else {
				label=i;
			}
			
			editHTML+="<option value='"+i+"' ";
			editHTML+=">"+label+"</option>";

		}
		editHTML+="</select>";

		
		editHTML+="<select name='"+prefix+"_amorpm'>";	
		editHTML+="<option></option>";
		editHTML+="<option value='am' ";
		editHTML+=">AM</option>";
		editHTML+="<option value='pm' ";
		editHTML+=">PM</option>";			
		
		editHTML+="</select>";

		editHTML+="</td>";
		
		
		editHTML+="</tr>";
		
		
	}
	else if(type=="time_label"){
		editHTML+="<tr>";
		editHTML+="<td>Edit Time Label</td>";
		editHTML+="<td><input type=text name='formValue' /></td>";
	
	
		editHTML+="</tr>";
			
		
	}
	
	editHTML+="<tr>";
	editHTML+="<td colspan=2 align=center><input type=hidden name='edit_hour' value='"+hour_id+"' /><input type=hidden name='formElement' value='"+type+"' /><input type=submit value='Edit' /></td>";
	editHTML+="</tr>";
	editHTML+="</table>";
	document.getElementById('editForm').innerHTML=editHTML;
}
</script>
<style type="text/css">
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
</style>
<table class='train_ava' width=100% >
<tr class='rowHeading'>
<th>Time Label</th>
<th>From</th>
<th>To</th>
<th>Headway</th>
<th>Cars Required</th>
<th>Reserve Required</th>
<th>&nbsp;</th>
</tr>
<?php
$sql="select * from timetable_hour where timetable_id='".$timetable_id."' order by time_from";
$rs=$db2->query($sql);
$nm=$rs->num_rows;
for($i=0;$i<$nm;$i++){
?>
	<tr <?php if($i%2>0){ echo "class='rowClass'"; } ?>>
<?php
	$row=$rs->fetch_assoc();
	
	$label=$row['time_label'];
	$from=date("H:i",strtotime($row['time_from']));
	$to=date("H:i",strtotime($row['time_to']));
	
	$headway=$row['headway'];
	
?>		
	<td><?php echo $label; ?> <a href='#' onclick="editElement('time_label','<?php echo $row['id']; ?>')">Edit</a></td>
	<td><?php echo $from; ?> <a href='#' onclick="editElement('time_from','<?php echo $row['id']; ?>')">Edit</a></td>
	<td><?php echo $to; ?> <a href='#' onclick="editElement('time_to','<?php echo $row['id']; ?>')">Edit</a></td>
	<td><?php echo $headway; ?> <a href='#' onclick="editElement('headway','<?php echo $row['id']; ?>')">Edit</a></td>

<?php	
	$cars_required=0;
	$reserve_required=0;
			
	$sql2="select * from timetable_required where timetable_id='".$timetable_id."' and hour_id='".$row['id']."'";
	$rs2=$db2->query($sql2);
	$nm2=$rs2->num_rows;
	
	if($nm2>0){
		$row2=$rs2->fetch_assoc();	
		$cars_required=$row2['cars_required'];
		$reserve_required=$row2['reserve_required'];	
	}
?>
	<td><?php echo $cars_required; ?> <a href='#' onclick="editElement('cars_required','<?php echo $row['id']; ?>')">Edit</a></td>
	<td><?php echo $reserve_required; ?> <a href='#' onclick="editElement('reserve_required','<?php echo $row['id']; ?>')">Edit</a></td>
	<td><a href='#' onclick="deleteRow('<?php echo $row['id']; ?>','<?php echo $timetable_id; ?>')" >X</a></td>
	</tr>
<?php		
}
?>
</table>
<?php
if($report_file==""){
}
else {

?>
<div align=right><a href='#' onclick="window.open('print_template.php?timetable_id=<?php echo $timetable_id; ?>','_blank')">Print Template</a></div>
<?php

}
?>

<br>
<table>
<tr>
<td>
<form action="new_timetable.php" method="post">
<table id='add_form' >
<tr>
	<th>
	Time Label (e.g. 1500-1600)
	</th>
	<td>
	<input type=text name='time_label' />
	</td>
</tr>
<tr>
	<th>
	From
	</th>
	<td>
		<select name='from_hour'>
		<?php
			for($i=1;$i<13;$i++){
		?>	
			<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
		<?php
			}
		?>
		</select>

		<select name='from_minute'>
		<?php
			for($i=0;$i<60;$i++){
		?>	
				<option value="<?php echo $i; ?>"><?php if($i<10){ echo "0".$i; } else { echo $i; } ?></option>
		<?php
			}
		?>
		</select>

		<select name='from_amorpm'>
			<option value='am'>AM</option>
			<option value='pm'>PM</option>
		</select>
	</td>
</tr>
<tr>
	<th>
	To
	</th>
	<td>
		<select name='to_hour'>
		<?php
			for($i=1;$i<13;$i++){
		?>	
			<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
		<?php
			}
		?>
		</select>

		<select name='to_minute'>
		<?php
			for($i=0;$i<60;$i++){
		?>		
			<option value="<?php echo $i; ?>"><?php if($i<10){ echo "0".$i; } else { echo $i; } ?></option>
		<?php
			}
		?>
		</select>

		<select name='to_amorpm'>
			<option value='am'>AM</option>
			<option value='pm'>PM</option>
		</select>
	</td>
</tr>

<tr>
	<th>
	Headway
	</th>
	<td><input type=text name='headway' />
	</td>
</tr>
<tr>
	<th>
	Cars Required
	</th>
	<td><input type=text name='cars_required' />
	</td>
</tr>
<tr>
	<th>
	Reserve Required
	</th>
	<td><input type=text name='reserve_required' />
	</td>
</tr>
<?php 
if($timetable_id==""){
}
else {
?>
<tr>
	<td align=center colspan=2><input type=submit value='Submit' /></td>
</tr>
<?php
}
?>
</table>
		
<input type=hidden name='timetable_id' value="<?php echo $timetable_id; ?>" />
</form>
</td>
<td valign=top>
<form action="new_timetable.php?timetable_id=<?php echo $timetable_id; ?>" method="post" id='editForm' name='editForm'>
	
	
	
	
	
	
	
	
	
	
</form>
</td>
</tr>
</table>
<?php
?>