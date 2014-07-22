<?php
session_start();
?>
<?php
if(isset($_POST['modifyUser'])){
	$db=new mysqli("localhost","root","","transport");
	if($_POST['action']=="edit"){
		if($_POST['modifyField']=="position"){
			$update="update train_driver set position='".$_POST['modifyRole']."' where id='".$_POST['modifyUser']."'";
			$rs=$db->query($update);
		}
		else {
			$update="update train_driver set ".$_POST['modifyField']."='".$_POST['modifyValue']."' where id='".$_POST['modifyUser']."'";
			$rs=$db->query($update);
		}
	}
	else if($_POST['action']=="delete"){
		$update="delete from train_driver where id='".$_POST['modifyUser']."'";
//		$update="update train_driver set position='INACTIVE' where id='".$_POST['modifyUser']."'";

		$rs=$db->query($update);
		//$update="update login set status='inactive' where username='".$_POST['modifyUser']."'";
	}
}
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
#admin_form th{
background-color: #4ad;  
}

#admin_form td:nth-child(odd) {
background-color: #33aa55; 
color:white;
font-weight:bold;
padding:5px;

}
#admin_form td:last-child{
background-color:white;

}


#admin_form td:nth-child(even) {
background-color: rgb(185, 201, 254);  
border:1px solid #4ad;

}


select { border: 1px solid rgb(185, 201, 254); color: rgb(0,51,153); background-color: rgb(185, 201, 254);  }
</style>
<?php
require("monitoring menu.php");
?>
<br>
<br>
<script language='javascript'>
function enableField(fieldType){
	if(fieldType=="position"){
		document.getElementById('modifyRole').disabled=false;
		document.getElementById('modifyValue').disabled=true;		
	}
	else {
		document.getElementById('modifyRole').disabled=true;
		document.getElementById('modifyValue').disabled=false;
	}
} 
function submitForm(){
	var action=document.getElementById('action').value;
	if(action=='delete'){
		var check=confirm("Delete the Account?");
		if(check){
			document.forms["admin_form"].submit();
		
		}
	
	}
	else {
		document.forms["admin_form"].submit();
	
	
	}

}
</script>
<br>
<br>

<form action='admin_page.php' id='admin_form' name='admin_form' method='post'>
<table>
<tr>
<th colspan=2>Edit Train Driver/STDO/CCRE</th>
</tr>
<tr>
<td>Edit User</td>
<td>
<select name='modifyUser'>
<?php 
$db=new mysqli("localhost","root","","transport");
$userSQL="select * from train_driver order by lastName ";
$userRS=$db->query($userSQL);
$userNM=$userRS->num_rows;
for($i=0;$i<$userNM;$i++){
	$userRow=$userRS->fetch_assoc();
	?>
	<option value='<?php echo $userRow['id']; ?>'><?php echo strtoupper($userRow['lastName']).", ".$userRow['firstName']; ?></option>	
	<?php

}
?>
</select>
</td>
</tr>
<tr>
<td>Enter Action</td>
<td>
	<select name='action' id='action'>
		<option value='edit'>Edit</option>
		<option value='delete'>Delete</option>
	</select>

</tr>
</tr>
<tr>
<td>Modify this Data:</td>
<td>
<select name='modifyField' id='modifyField' onchange='enableField(this.value)'>
<option value='firstName'>First Name</option>
<option value='lastName'>Last Name</option>
<option value='midName'>Middle Name</option>
<option value='empNum'>Employee Number</option>
<option value='position'>Position</option>

</select>

</td>
</tr>
<tr>
<td>Change To:</td>
<td><input type=text name='modifyValue' id='modifyValue' /></td>
</tr>
<tr>
<td>Change To:</td>
<td>
<select name='modifyRole' id='modifyRole'  disabled=true>
<?php 
?>

	<option value="TD">Train Driver</option>
	<option value="CCRE">CCRE</option>
	<option value="STDO">Senior TDO</option>
	<option value="SVTDO">Supervising TDO</option>
	<option value="CLERK III">CLERK III</option>
	<option value="CLERK IV">CLERK IV</option>
	<option value="SUP">From Support</option>
	<option value="CHIEF TDO">CHIEF TDO</option>

<?php
?>
</select>
</td>
</tr>
<tr>
<td colspan=2 align=center><input type=button value='Submit' onclick='submitForm()' /></td>
</tr>
</table>
</form>
<br>

<table width=100% border=1 class='train_ava' >
<tr>
<td>Name</td>
<td>Middle Name</td>
<td>Employee Number</td>
<td>Position</td>

</tr>
<?php
$db=new mysqli("localhost","root","","transport");
$sql="select * from train_driver order by lastName";
$rs=$db->query($sql);
$nm=$rs->num_rows;
for($i=0;$i<$nm;$i++){
	$row=$rs->fetch_assoc();
?>
	<tr>
		<td><?php echo strtoupper($row['lastName']).", ".$row['firstName']; ?></td>
		<td><?php echo $row['midName']; ?></td>
		<td><?php echo $row['empNum']; ?></td>
		<td><?php echo $row['position']; ?></td>
	</tr>
<?php
}
?>
</table>