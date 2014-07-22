<?php
$db=new mysqli("localhost","root","","transport");

$car_id=$_GET['car_id'];
?>
<style type='text/css'>
.rowClass {
	background-color:#eaf2d3;
}
.rowHeading {
	background-color:#a7c942;

	color:rgb(0,51,153);
	color:white;	
	
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


#add_form td{
background-color: rgb(185, 201, 254);  
border:1px solid #4ad;

}

select { border: 1px solid rgb(185, 201, 254); color: rgb(0,51,153); background-color: rgb(185, 201, 254);  }
</style>
<?php
/*

#add_form td:last-child{
background-color:white;

}

#add_form td:nth-child(odd) {
background-color: #33aa55; 
color:white;
font-weight:bold;
padding:5px;

}

#add_form td:nth-child(even) {
background-color: rgb(185, 201, 254);  
border:1px solid #4ad;

}
*/
?>
<body>
Car #<?php echo $car_id; ?>
<br>
<br>
Incident History
<br>
<table width=80% id='add_form' name='add_form'>
	<tr>
	<th>Index No</th>
	<th>Incident Date</th>
	<th>Incident Number</th>
	<th>Description</th>
	</tr>
<?php

$sql="select * from incident_union where car_no='".$car_id."' order by incident_date desc";
$rs=$db->query($sql);
$nm=$rs->num_rows;

for($i=0;$i<$nm;$i++){
	$row=$rs->fetch_assoc();
?>	
	<tr>
		<td><?php echo $row['index_no']; ?></td>
		<td><?php echo date("F d, Y",strtotime($row['incident_date'])); ?></td>
		<td><a href='#' onclick='window.open("edit_ccdr.php?ir=<?php echo $row['id']; ?>")'><?php echo $row['incident_no']; ?></a></td>
		<td><?php echo $row['description']; ?></td>
	
	</tr>
<?php
}
?>	
</table>
</body>