<?php
if(isset($_POST['position'])){
	
		$db=new mysqli("localhost","root","","transport");
		$check="select * from train_driver where firstName='".strtoupper($_POST['firstName'])."' and lastName='".strtoupper($_POST['lastName'])."' and midName='".strtoupper($_POST['midName'])."'";
		$checkRS=$db->query($check);
		$checkNM=$checkRS->num_rows;
		if($checkNM>0){
			$message="<font color=red>Username already exists.</font>";
		}
		else {
			$sql="insert into train_driver(firstName,lastName,midName,position,empNum) values (\"".strtoupper($_POST['firstName'])."\",\"".strtoupper($_POST['lastName'])."\",\"".strtoupper($_POST['midName'])."\",\"".$_POST['position']."\",'".$_POST['empNum']."')";
			$rs=$db->query($sql);
			$login_id=$db->insert_id;
				
	
			$message="<font color=red>Data has been added.</font>";
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
</style>
<?php
require("monitoring menu.php");
?>
<br>
<br>
<br>
<?php
echo $message;
?>
<form action='createRecord.php' method='post'>
<table id=admin_form cellpadding=2>
<tr colspan=2 >
	<th colspan=2><h2>Create New Record</h2></th>
</tr>
<tr>
	<td>First Name</td>
	<td><input type=text name='firstName' size=40 ></td>
</tr>
<tr>
	<td>Last Name</td>
	<td><input type=text name='lastName' size=40 ></td>
</tr>
<tr>
	<td>Middle Name</td>
	<td><input type=text name='midName' size=40 ></td>
</tr>
<tr>
	<td>Employee Number (leave blank if unsure)</td>
	<td><input type=text name='empNum' size=40 ></td>
</tr>

<tr>
	<td>Position</td>
	<td>
	<select name='position' id='position'>

		<option value="TD">Train Driver</option>
		<option value="CCRE">CCRE</option>
		<option value="STDO">Senior TDO</option>
		<option value="SVTDO">Supervising TDO</option>
		<option value="CLERK III">CLERK III</option>
		<option value="CLERK IV">CLERK IV</option>
		<option value="SUP">From Support</option>
		<option value="CHIEF TDO">CHIEF TDO</option>

	</select>
	</td>
</tr>
<tr>
	<td colspan=2 align=center><input type=submit value='Submit' /></td>
</tr>
</table>
</form>