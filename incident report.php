<?php
ini_set("date.timezone","Asia/Kuala_Lumpur");
?>
<?php
if(isset($_POST['equipment'])){
	
	$incident_id=$_POST['incident_no'];
	$description=$_POST['description'];
	$dotc_taken="";

	if(isset($_POST['dotc'])){
		$dotc_taken=$_POST['dotc'];

	}
	else if(isset($_POST['dotc_coordinated'])){
		$dotc_taken=$_POST['dotc_coordinated']." ".$_POST['coordinated_to'];
	}
	
	
	$maintenance_taken=$_POST['maintenance'];
	$level=$_POST['level'];
	$duration=$_POST['duration'];
	
	$year=$_POST['year'];
	$month=$_POST['month'];
	$day=$_POST['day'];
	
	$hour=$_POST['hour'];
	$minute=$_POST['minute'];
	$amorpm=$_POST['amorpm'];

	$equipment=$_POST['equipment'];
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
	
	$reported_by=$_POST['reported_by'];
	$received_by=$_POST['received_by'];

		
	$level_condition=$_POST['condition'];
	
	$incident_date=date("Y-m-d H:i",strtotime($year."-".$month."-".$day." ".$hour.":".$minute));
	$incidentYear=$year;
	
	
	$type=$_POST['type'];
	
	$cancel=0;
	if(isset($_POST['cancel'])){
		
		if($_POST['cancel']=="more"){
			$cancel=$_POST['cancel_more'];

		
		}
		else if($_POST['cancel']=="half"){
			$cancel=.5;
		
		}
		else if($_POST['cancel']=="whole"){
			$cancel=1;
		
		}
		else if($_POST['cancel']=="none"){
			$cancel=0;
		
		}
	}	
	$unit_no="";
	if(isset($_POST['unit_no'])){
		$unit_no=$_POST['unit_no'];
	}
	
	$db=new mysqli("localhost","root","","transport");
	$sql="insert into incident_report ";
	$sql.="(incident_type,incident_no,level,incident_date,";
	$sql.="description,action_dotc,action_maintenance,duration,equipt,cancel,unit_no,level_condition)";
	$sql.=" values ";
	$sql.="(\"".$type."\",\"".$incident_id."\",'".$level."','".$incident_date."',";
	$sql.="\"".$description."\",\"".$dotc_taken."\",\"".$maintenance_taken."\",\"".$duration."\",'".$equipment."','".$cancel."','".$unit_no."','".$level_condition."')";

	$rs=$db->query($sql);
	$incident_code=$db->insert_id;

	
	if($level==2){
		$update="update incident_report set l2='".$_POST['order']."' where id='".$incident_code."'";
		$rs=$db->query($update);
	
	
		$update="insert into level(date,level,incident_id) values ('".date("Y-m-d",strtotime($incident_date))."','2','".$incident_code."')";
		$rs=$db->query($update);
	
	
	}
	else if($level==3){
		$update="update incident_report set l3='".$_POST['order']."' where id='".$incident_code."'";
		$rs=$db->query($update);

		$update="insert into level(date,level,incident_id) values ('".date("Y-m-d",strtotime($incident_date))."','3','".$incident_code."')";
		$rs=$db->query($update);

		
	}
	else if($level==1){
		$update="update incident_report set l3='".$_POST['order']."' where id='".$incident_code."'";
		$rs=$db->query($update);

		$update="insert into level(date,level,incident_id) values ('".date("Y-m-d",strtotime($incident_date))."','1','".$incident_code."')";
		$rs=$db->query($update);

		
	}

	else if($level==0){
		$update="update incident_report set l3='".$_POST['order']."' where id='".$incident_code."'";
		$rs=$db->query($update);

		$update="insert into level(date,level,incident_id) values ('".date("Y-m-d",strtotime($incident_date))."','0','".$incident_code."')";
		$rs=$db->query($update);

		
	}	
	
	else if($level==4){
		$update="update incident_report set l4='".$_POST['order']."' where id='".$incident_code."'";
		$rs=$db->query($update);

		$update="insert into level(date,level,incident_id) values ('".date("Y-m-d",strtotime($incident_date))."','4','".$incident_code."')";
		$rs=$db->query($update);

		
	}	

	$incidentSQL="select * from incident_no where incident_id='".$incident_code."'";
	$incidentRS=$db->query($incidentSQL);
	$incidentNM=$incidentRS->num_rows;
	
	if($incidentNM>0){
		
	
	}
	else {
		$insert="insert into incident_no(year,incident_id) values ('".$incidentYear."','".$incident_code."')";
		$insertRS=$db->query($insert);
		
	}
	
	$noRS=$db->query($incidentSQL);
	$noRow=$noRS->fetch_assoc();
	$incident_term=$noRow['incident_no'];
	
	//Calculate the Suffix for the Incident No.
	
	$suffixSQL="select * from equipment_type where equipment_code='".$type."'";
	$suffixRS=$db->query($suffixSQL);
	$suffixRow=$suffixRS->fetch_assoc();
	
	$incident_suffix=$suffixRow['incident_code'];
	
	$incident_number=$incident_term." ".$incident_suffix;
	
	$update="update incident_report set incident_no='".$incident_number."' where id='".$incident_code."'";
	$updateRS=$db->query($update);

	
	if($_POST['incident_link']==""){
	}
	else {
		$update="update incident_report set linked_to='".$_POST['incident_link']."' where id='".$incident_code."'";
		$updateRS=$db->query($update);

	}
	
	$location=$_POST['location'];
	$direction=$_POST['direction'];
	
	$subitem=$_POST['subitem'];
	
	$index_no=$_POST['index_id'];
	$car_no=$_POST['car_id'];
	
	$sql="insert into incident_description ";
	$sql.="(incident_id,location,direction,equipt,subitem,index_no,car_no,reported_by,received_by)";	
	$sql.=" values ";
	$sql.=" ('".$incident_code."','".$location."','".$direction."','".$equipment."','".$subitem."','".$index_no."','".$car_no."','".$reported_by."','".$received_by."')";
	$rs=$db->query($sql);
	
	if($_POST['car_id']==""){
	
	}
	else {
	
	$sql="insert into incident_cars ";
	$sql.="(incident_id,car_no) values ";
	$sql.="('".$incident_code."','".$car_no."')";
	
	$rs=$db->query($sql);
	
	}
	if($_POST['car_id_2']==""){
	
	}
	else {
		$sql="insert into incident_cars ";
		$sql.="(incident_id,car_no) values ";
		$sql.="('".$incident_code."','".$_POST['car_id_2']."')";
		$rs=$db->query($sql);
	
	
	}
	
	if($_POST['car_id_3']==""){
	
	}
	else {
		$sql="insert into incident_cars ";
		$sql.="(incident_id,car_no) values ";
		$sql.="('".$incident_code."','".$_POST['car_id_3']."')";
		$rs=$db->query($sql);
	
	
	}
	
	
	if(isset($_GET['cancel'])){
		$db=new mysqli("localhost","root","","transport");
		$sql="update train_availability set status='cancelled' where id='".$_GET['cancel']."' and status='active'";
		$rs=$db->query($sql);
		
		
		$sql="update train_ava_time set cancel_loop='1' where train_ava_id='".$_GET['cancel']."'";
		$rs=$db->query($sql);


		$sql="insert into train_incident_report(train_ava_id,incident_id) values ";
		$sql.="('".$_GET['cancel']."','".$incident_code."')";
		$rs=$db->query($sql);
		
		
		echo "<script language='javascript'>";
		echo "window.opener.location='train_availability.php';";
		//echo "window.opener.location.reload();";
		echo "</script>";
	}
	if(isset($_GET['add_incident'])){

		$sql="insert into train_incident_report(train_ava_id,incident_id) values ";
		$sql.="('".$_GET['add_incident']."','".$incident_code."')";
		$rs=$db->query($sql);

		if(isset($_POST['cancel'])){
			$sql="update train_ava_time set cancel_loop='".$cancel."' where train_ava_id='".$_GET['cancel']."'";
			$rs=$db->query($sql);
		}
		
		echo "<script language='javascript'>";
		echo "window.opener.location='train_availability.php';";
		//echo "window.opener.location.reload();";
		echo "</script>";
	
	}
	
	if($level_condition=='3'){
		echo "<script language='javascript'>";
		echo "window.open('service interruption.php?incident=".$incident_code."');";
		echo "</script>";
//		header("Location: service interruption.php?incident=".$incident_code);
	}

	
	$db2=new mysqli("localhost","root","","external");
	$update="insert into incident_defects(incident_id,equipt_id,sub_item_id) (select '".$incident_code."',equipt_id,sub_item_id from temp_multiple)";
	
	$updateRS=$db2->query($update);
	
	$update="delete from temp_multiple";
	$updateRS=$db2->query($update);
}

?>
<script language='javascript' src='ajax.js'></script>
<script language='javascript'>
function openLink(){
	window.open("link_incident.php","_blank");


}


function scrollCat(){
	var problemType=document.getElementById('type').value;
	var category=document.getElementById('category').value;
	if(problemType=="rolling"){

		makeajax("processing.php?scrollRolling="+problemType+"&category="+category,"scrollRolling");	
	
	
	}
	else if(problemType=="power"){
		makeajax("processing.php?scrollRolling="+problemType+"&category="+category,"scrollRolling");	
	
	}
	else {
		makeajax("processing.php?scrollRolling="+problemType,"scrollRolling");	
	
	}

}
function scrollType(){
	var problemType=document.getElementById('type').value;
	var rollingHTML="";
	
	if((problemType=="rolling")||(problemType=="unload")){
		document.getElementById('index_id').disabled=false;
		document.getElementById('car_id').disabled=false;
		
		/*
		rollingHTML+="<select name='category' id='category' onchange='scrollCat()' >";
		rollingHTML+="<option value='EXT'>Exterior</option>";
		rollingHTML+="<option value='UFE'>Underfloor Equipment</option>";
		rollingHTML+="<option value='OB'>Onboard Equipment and Accessories</option>";
		rollingHTML+="<option value='OTH'>Others</option>";


		rollingHTML+="</select>";	
		
		document.getElementById('rolling_category').innerHTML=rollingHTML;
		*/
	}
	else {
		if(problemType=="power"){
			rollingHTML+="<select id='category' name='category' onchange='scrollCat()'>";
			rollingHTML+="<option value='OCS'>Overhead Catenary System</option>";
			rollingHTML+="<option value='SS'>Station Substation</option>";
			rollingHTML+="<option value='TPSS'>Traction Power Substation Equipment</option>";


			rollingHTML+="</select>";	
			
			document.getElementById('rolling_category').innerHTML=rollingHTML;
		
		
		}
		else {
			document.getElementById('rolling_category').innerHTML=rollingHTML;

		}
		
		document.getElementById('index_id').disabled=true;
		document.getElementById('car_id').disabled=true;
	}
	
	
	var category="";
	var equiptHTML="";
	
	if(problemType=="rolling"){
//		category=document.getElementById('category').value;

//		makeajax("processing.php?scrollRolling="+problemType+"&category="+category,"scrollRolling");	
		makeajax("processing.php?scrollRolling="+problemType,"scrollRolling");	

		
	}
	else if(problemType=="power"){
		category=document.getElementById('category').value;

		makeajax("processing.php?scrollRolling="+problemType+"&category="+category,"scrollRolling");	
	
	}
	else if(problemType=="others"){
		makeajax("processing.php?scrollOthers="+problemType,"scrollRolling");	
		
	
	}
	else {
		makeajax("processing.php?scrollRolling="+problemType,"scrollRolling");	
	
	}

	if(problemType=="cc_equipt"){
		equiptHTML="<input type='text' name='cc_equipt' id='cc_equipt' style='border:1px solid gray;' />"; 		

		document.getElementById('equipment_space').innerHTML=equiptHTML;
		
	}
	else if(problemType=="station_equipt"){
		equiptHTML="<input type='text' name='station_equipt' id='station_equipt' style='border:1px solid gray;' />"; 		

		document.getElementById('equipment_space').innerHTML=equiptHTML;
	
	
	}
	else if(problemType=="depot_equipt"){
		equiptHTML="<input type='text' name='depot' id='depot' style='border:1px solid gray;' />"; 		

		document.getElementById('equipment_space').innerHTML=equiptHTML;
	
	
	}
	else {
		document.getElementById('equipment_space').innerHTML="";
	}	



	if(problemType=="afc"){
		equiptHTML="<input type='text' name='unit_no' id='unit_no' style='border:1px solid gray;' size=5 />"; 		
		
		document.getElementById('unit_space').innerHTML=equiptHTML;
	
	}

/*
scrollRolling();
*/
}

function subItemScroll(){
	var problemType=document.getElementById('equipment').value;

	makeajax("processing.php?scrollSubItem="+problemType,"subItem");	

}

function subItem(ajaxHTML){
	var subHTML="";
	
	if(ajaxHTML=="No data available"){
	
	
	}
	else {
		var subItemTerms=ajaxHTML.split("==>");
		var count=(subItemTerms.length)*1-1;
		subHTML="<select id='subitem' name='subitem'>";
		subHTML+="<option></option>";
		for(var n=0;n<count;n++){
			var parts=subItemTerms[n].split(";");
			subHTML+="<option value='"+parts[0]+"'>";
			subHTML+=parts[1];
			subHTML+="</option>";
		
		}
		subHTML+="</select>";
	
	}
	document.getElementById('sub_item_space').innerHTML=subHTML;

}

function scrollRolling(ajaxHTML){

	var rollingHTML="<option></option>";

	if(ajaxHTML=="No data available"){
	
		
	}
	else {
		var equipmentTerms=ajaxHTML.split("==>");
		var count=(equipmentTerms.length)*1-1;
		
		for(var n=0;n<count;n++){
			var parts=equipmentTerms[n].split(";");
			rollingHTML+="<option value='"+parts[0]+"'>";
			rollingHTML+=parts[1];
			rollingHTML+="</option>";
		
		}

	
	}




	
	document.getElementById('equipment').innerHTML=rollingHTML;
	
	document.getElementById('sub_item_space').innerHTML="";	
/*
	var rollingType=document.getElementById('rolling_type').value;
	if(document.getElementById('rolling_type').disabled==true){
		document.getElementById('equipment').disabled=true;

	}
	else {
		if(rollingType=="equipt"){
			document.getElementById('equipment').disabled=false;
		}
		else {
			document.getElementById('equipment').disabled=true;
		}
	}
*/
}
function getMore(cancel){
	if(cancel=="more"){
		document.getElementById('cancel_more').disabled=false;
	}
	else {
		document.getElementById('cancel_more').disabled=true;
	}

}


function getLevel(element){
	var level=element.value;
	var conditionHTML="";
	/*	if((level=="1")){
		document.getElementById('remove').className='removalnone';
		document.getElementById('order').disabled=true;
	}	
	else {
		document.getElementById('remove').className='removal';
		document.getElementById('order').disabled=false;
	}
*/
	if(level==3){
		conditionHTML+="<select name='condition'>";
		conditionHTML+="<option></option>";
		conditionHTML+="<option value='1'>Train is removed without replacement</option>";
		conditionHTML+="<option value='2'>Cancellation of loops and insertion</option>";
		conditionHTML+="</select>";
	}
	else if(level==4){
		conditionHTML+="<select name='condition'>";
		conditionHTML+="<option></option>";
		conditionHTML+="<option value='3'>Service interruption</option>";
		conditionHTML+="<option value='4'>Cancellation of loops. Ticket refunds.</option>";
		conditionHTML+="</select>";
	
	}
	document.getElementById('condition').innerHTML=conditionHTML;
}

function changeDirection(element){
	var direction=element.value;

}
function setPreset(check){
	var remarksHTML="";
	
	if(check.checked){
		remarksHTML="<select name='dotc_coordinated' id='dotc_coordinated'>";
		remarksHTML+="<option>Coordinated with</option>";
		remarksHTML+="<option>Coordinated to</option>";
		remarksHTML+="</select>";
		remarksHTML+="<input style='border: 1px solid gray' type=text name='coordinated_to' id='coordinated_to' />";
	}
	else {
		remarksHTML="<textarea rows=5 cols=50 name='dotc'></textarea>";
	}

	document.getElementById('remarks_space').innerHTML=remarksHTML;
}

function addCoordinate(){
	var coordinate=document.getElementById('dotc_coordinated').value;
	var remarksValue=document.getElementById('dotc').value;
	var additional="";
	
	if(coordinate=="c_with"){
		additional="Coordinated with "+document.getElementById('coordinated_to').value+".";
		
		
	}
	else if(coordinate=="c_to"){
		
		additional="Coordinated to "+document.getElementById('coordinated_to').value+".";
		
		
	}

	else if(coordinate=="reinitialize"){
		
		additional="Re-initialized, ok.";
		
		
	}
	else if(coordinate=="recorded"){
		
		additional="Recorded.";
		
		
	}
	
	document.getElementById('dotc').value=remarksValue+" "+additional;	

}
function activateMultiple(){
	var multipleSignal=document.getElementById('multipleFlag');
	if(multipleSignal.checked){
		//alert("A");
		//document.getElementById('equipment').innerHTML="";
		var multipleTable="<table name='multi_list' id='multi_list' width=80%>";
		
		
		
		
		multipleTable+="</table>";
		multipleTable+="<a href='#' onclick=\"window.open('multiple_defects.php?problemType=RS')\">Update</a>";	
		
		document.getElementById('multiple_space').innerHTML=multipleTable;
			
		
		
	}
	else {
		//scrollType();
		document.getElementById('multiple_space').innerHTML="";
	
	}

}

function retrieveDefects(){
	makeajax("processing.php?retrieveAdditional=Y","getAdditional");	

}
function getAdditional(ajaxHTML){
	var subHTML="";
	
	if(ajaxHTML=="No data available"){
	
	
	}
	else {
		var subItemTerms=ajaxHTML.split(";");
		var count=(subItemTerms.length)*1-1;
		subHTML="<tr><th>Equipment</th><th>Sub-item</th></tr>";
		for(var n=0;n<count;n++){
			var parts=subItemTerms[n].split(",");
			subHTML+="<tr>";
			subHTML+="<td>"+parts[0]+"</td><td>";
			subHTML+=parts[1];
			subHTML+="</td>";
			subHTML+="</tr>";
		}
		//subHTML+="</select>";
	
	}
	document.getElementById('multi_list').innerHTML=subHTML;



}
</script>
<style type='text/css'>
body {
	background-color: #gray;
	color: #fff;
	margin-left:30px;
	margin-right:30px;	
	
	
<!--	margin: 0;
	padding: 0;
-->	
}
.content {
	width: 80%;
	margin: 20px auto 40px auto;
	background-color: #ffa;
	color: #333;
	border: 2px solid #1a3c2d;
	padding: .75em;
	spacing: .5px;
}

.ir table {
	//margin: .75em auto auto auto;
	color: #000;
	border: 1px solid rgb(185, 201, 254);
}

.ir th {
	background-color: #33aa55;
	color: #fff;
	border: 1px solid rgb(185, 201, 254);
	


}

.ir tr td:first-child {
	background-color: rgb(185, 201, 254);
	color: rgb(0,51,153);

}
.ir tr td:last-child {
	background-color: #dfe7f2;
	color: #fff;

}

.ir td {
	border: 1px solid rgb(185, 201, 254);

}

input[type="text"]{ 
	height:25px; 
	font-weight:bold; 
	font-size:15px; 
	font-family:courier; 
	border: 1px solid #dfe7f2;
	background-color: #dfe7f2;
	color: rgb(0,51,153);
	border-radius: 3px;
}
textarea{ 
	border: 1px solid #dfe7f2;
	background-color: #dfe7f2;
	color: rgb(0,51,153);
	border-radius: 3px;
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
ul.nav li {
	list-style-type:none;
	display: inline;
	padding-left: 0;
	margin-left: 0;

	
	padding: 5px;
	spacing: 1.75px;
	color: black;
	
	
	min-width: 8em;
	margin-right: 0.5em;
	
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	-webkit-box-shadow: 3px 3px 3px 3px rgba(43, 43, 77, 0.5);
	-moz-box-shadow: 3px 3px 3px 3px rgba(43, 43, 77, 0.5);
	box-shadow: 3px 3px 3px 3px rgba(43, 43, 77, 0.5);
}
select { border: 1px solid #dfe7f2; color: rgb(0,51,153); background-color:  #dfe7f2;  }

ul.nav li a{
	text-decoration: none;

}


.removal {

	color: rgb(0,51,153);
}

.removalnone {
	color:rgb(223,231,242);

}

.ir #multi_list tr th {

	background-color: #33aa55;
	color: #fff;
	border: 1px solid rgb(185, 201, 254);

}

.ir #multi_list tr:nth-child(2) td {
	background-color: rgb(185, 201, 254);
	color: rgb(0,51,153);


}
.ir #multi_list tr:nth-child(n+2) td{

	background-color: #dfe7f2;
	color: rgb(0,51,153);

}


</style>

<body>
<?php
require("monitoring menu.php");
?>
<br>
<br>
<div>
<br>
<br>
<!--<a href='monitoring menu.php'>Go Back to Monitoring Menu</a>-->
<form action='incident report.php<?php if(isset($_GET['cancel'])){ echo "?cancel=".$_GET['cancel']; } else if(isset($_GET['add_incident'])){ echo "?add_incident=".$_GET['add_incident']; } ?>' method='post'>
<table class='ir'>
<tr>
<th colspan=2>Incident Report</th>
</tr>
<tr>
<td>Type of Problem</td>
<td>
<select name='type' id='type' onchange='scrollType()'>
<option value='rolling' <?php if((isset($_GET['cancel']))||(isset($_GET['add_incident']))){ echo "selected"; } ?>>Rolling Stock</option>
<option value='cc_equipt'>CC Equipment</option>
<option value='communication'>Communication</option>
<option value='depot_equipt'>Depot Equipment</option>
<option value='power'>Power</option>
<option value='signaling'>Signaling</option>
<option value='tracks'>Tracks</option>
<option value='afc'>AFC Equipment</option>
<option value='station_equipt'>Station Equipment</option>
<option value='gradual'>Gradual Removal</option>
<option value='c_loops'>Cancelled Loops; Acc. Delay/Failure</option>
<option value='r_trains'>Running Trains</option>
<option value='unload'>Unloading of Passengers</option>
<option value='nload'>Not Loading</option>

<!--
<option value='ser_int'>Service Interruption</option>
-->
<option value='others'>Others</option>
</select> 
<!--
<select name='rolling_type' id='rolling_type' onchange='scrollRolling()'>
<option value='equipt'>Equipment Problem</option>
<option value='train_failure'>Train Failure</option>
</select> 
<select name='equipment' id='equipment'>
<option>On-board Radio</option>
<option>Digital Diagnostic System</option>
<option>Public Address System</option>
<option>Driver's Cab ACU</option>
<option>Passengers' Section ACU</option>
<option>Wiper</option>
<option>Intercom</option>
</select>
-->

<span id='rolling_category' name='rolling_category'>
<!--
	<select name='category' id='category' onchange='scrollCat()'>
		<option value='EXT'>Exterior</option>
		<option value='UFE'>Underfloor Equipment</option>
		<option value='OB'>Onboard Equipment and Accessories</option>
		<option value='OTH'>Others</option>
		
	</select>	
-->
</span>

</td>

</tr>
<tr>
<td>Accessories/Equipment/Train Unavailability</td>
<td>
<select name='equipment' id='equipment' onchange='subItemScroll()'>

<option></option>
<?php 
$db=new mysqli("localhost","root","","transport");
//$sql="select * from equipment where type='RS' and category='EXT' order by equipment_name";
$sql="select * from equipment where type='RS' order by equipment_name";
$rs=$db->query($sql);
$nm=$rs->num_rows;
for($i=0;$i<$nm;$i++){
$row=$rs->fetch_assoc();
?>
<option value='<?php echo $row['id']; ?>'><?php echo $row['equipment_name']; ?></option>
<?php
}
?>
<option value='others'>OTHERS</option>
</select>
<span name='equipment_space' id='equipment_space'>
</span>
<span id='sub_item_space' name='sub_item_space'>
</span>
<span id='unit_space' name='unit_space'>
</span>

</td>
</tr>
<tr>
<td><input type=checkbox name='multipleFlag' id='multipleFlag' onclick='activateMultiple()' /><font color=blue>
Additional Defects
</td>
<td>
<span id='multiple_space' name='multiple_space'>
</span>

</td>
<tr>
<td>Link Incident Report</td>
<td>
<input type='text' name='incident_no_link' id='incident_no_link' />
<input type='hidden' name='incident_link' id='incident_link' />
<input type=button value='Link Incident' onclick='openLink()' />

</td>
</tr>


<tr><td>Index No./Car No.</td>
<td>
<?php
$retrieve_id="";
if(isset($_GET['cancel'])){
	$retrieve_id=$_GET['cancel'];

}

if(isset($_GET['add_incident'])){
	$retrieve_id=$_GET['add_incident'];

}

?>
<?php
if($retrieve_id==""){
}
else {
	$sql="select * from train_availability where id='".$retrieve_id."'";
	$rs=$db->query($sql);
	$row=$rs->fetch_assoc();
	$index_id=$row['index_no'];
		

	$switchSQL="select * from train_switch where train_ava_id='".$retrieve_id."' order by date_change desc";
	$switchRS=$db->query($switchSQL);
	$switchNM=$switchRS->num_rows;
	
	if($switchNM>0){
		$switchRow=$switchRS->fetch_assoc();
		
		$index_id=$switchRow['new_index'];
			
	}
	
	
	

}

?>


<input  name='index_id' id='index_id' style='border: 1px solid gray' type=text size=5 value='<?php echo $index_id; ?>' /> <span style='color: rgb(0,51,153);' >/</span> 
<select name='car_id' id='car_id'>
	<option></option>
<?php
if($retrieve_id==""){
}

else {
	$sql="select * from train_availability where id='".$retrieve_id."'";
	$rs=$db->query($sql);
	$row=$rs->fetch_assoc();
?>	
	<option selected value='<?php echo $row['car_a']; ?>'><?php echo $row['car_a']; ?></option>	
	<option value='<?php echo $row['car_b']; ?>'><?php echo $row['car_b']; ?></option>	
	<option value='<?php echo $row['car_c']; ?>'><?php echo $row['car_c']; ?></option>	

<?php	
}
?>



</select><font style='color: rgb(0,51,153);'>,</font> 

<select name='car_id_2' id='car_id_2'>
	<option></option>
<?php
if($retrieve_id==""){
}

else {
	$sql="select * from train_availability where id='".$retrieve_id."'";
	$rs=$db->query($sql);
	$row=$rs->fetch_assoc();
?>	
	<option value='<?php echo $row['car_a']; ?>'><?php echo $row['car_a']; ?></option>	
	<option value='<?php echo $row['car_b']; ?>'><?php echo $row['car_b']; ?></option>	
	<option value='<?php echo $row['car_c']; ?>'><?php echo $row['car_c']; ?></option>	

<?php	
}
?>



</select><font style='color: rgb(0,51,153);'>,</font> 

<select name='car_id_3' id='car_id_3'>
	<option></option>
<?php
if($retrieve_id==""){
}

else {
	$sql="select * from train_availability where id='".$retrieve_id."'";
	$rs=$db->query($sql);
	$row=$rs->fetch_assoc();
?>	
	<option value='<?php echo $row['car_a']; ?>'><?php echo $row['car_a']; ?></option>	
	<option value='<?php echo $row['car_b']; ?>'><?php echo $row['car_b']; ?></option>	
	<option value='<?php echo $row['car_c']; ?>'><?php echo $row['car_c']; ?></option>	

<?php	
}
?>



</select>


</td>
</tr>
<tr>
<td>Cancelled Loop</td>
<td>
<select name='cancel' id='cancel' onchange='getMore(this.value)'>
<option value='none'>0</option>
<option value='whole'>1</option>
<option value='half'>1/2</option>
<option value='more'> more than 1</option>
</select>
<input type=text name='cancel_more' id='cancel_more' size=5 style='border:1px solid gray' disabled />
</td>
</tr>
<tr>
<td>Incident No.</td><td> <!--<input type=text name='incident_no' />--> <font color='blue'>**System Generated**</font>
</td>
</tr>
<tr>
<td>Location/Direction</td>
<td>
<select name='direction'>
<option></option>
<option value='S'>Station</option>
<option value='D'>Depot</option>
<option value='ML'>Mainline</option>
<option value='CC'>Control Center</option>
<option value='NB'>Northbound</option>
<option value='SB'>Southbound</option>
<option value='NTB'>North Turnback</option>
<option value='IR'>Insertion/Removal Area</option>
<option value='SPT'>Shaw Pocket Track</option>
<option value='TPT'>Taft Pocket Track</option>
</select>
<input style='border:1px solid gray;' type=text name='location' id='location' size=5 />
</td>
</tr>

<tr>
<td>Level</td><td> <select name='level' id='level' onchange='getLevel(this)'>
<option value='0'>0</option>
<option value='1'>1</option>
<option value='2'>2</option>
<option value='3'>3</option>
<option value='4'>4</option>
</select>
<!--
<input type='text' style='border: 1px solid gray' name='order' id='order' size=5 disabled />
<font id='remove' name='remove' class='removalnone' >Order of Removal</font>
-->
<span id='condition' name='condition'>
</span>
</td>
</tr>
<tr>
<td>Date:</td><td>
<select name='month'>
<?php
$mm=date("m");
$yy=date("Y");
$dd=date("d");

$hh=date("h");

$min=date("i");
$aa=date("a");

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
</td></tr>
<tr><td>Time: </td><td>
<select name='hour'>
<?php
for($i=1;$i<=12;$i++){
?>
	<option value='<?php echo $i; ?>' 
	<?php
	if($i*1==$hh*1){
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
<select name='minute'>
<?php
for($i=0;$i<=59;$i++){
?>
	<option value='<?php echo $i; ?>' 
	<?php
	if($i*1==$min*1){
		echo "selected";
	}
	?>		
	>
	<?php
	if($i<10){
	echo "0".$i;
	}
	else {
	echo $i;
	}
	?>	
	</option>
<?php
}
?>
</select>
<select name='amorpm'>
<option value='am' <?php if($aa=="am"){ echo "selected"; } ?>>AM</option>
<option value='pm' <?php if($aa=="pm"){ echo "selected"; } ?>>PM</option>
</select>
</td>
</tr>
<tr>
<td>Incident Duration</td>
<td><input type=text name='duration' /></td>
</tr>


<tr>
<td valign=top>Details:</td><td> <textarea rows=5 cols=50 name='description'></textarea></td></tr>
<tr>
<th colspan=2>Reporting</th></tr>
<tr>
<td>Reported By</td>
<td><input type=text name='reported_by' id='reported_by' /></td>
</tr>
<tr>
<td>Received By</td>
<td>
<select name='received_by' id='received_by'>
<?php
$db=new mysqli("localhost","root","","transport");
$sql="select * from train_driver where position in ('STDO','CCRE') order by lastName";
$rs=$db->query($sql);
$nm=$rs->num_rows;
for($i=0;$i<$nm;$i++){
	$row=$rs->fetch_assoc();
?>	
	<option value='<?php echo $row['id']; ?>'><?php echo $row['lastName'].", ".$row['firstName']; ?></option>


<?php
}





?>
</select>
</td>
</tr>


<tr>
<th colspan=2>Action Taken</th></tr>
<tr>
<td valign=top>DOTC:</td><td><span name='remarks_space' id='remarks_space'> <textarea rows=5 cols=50 name='dotc' id='dotc'></textarea></span>
<br>
<select name='dotc_coordinated' id='dotc_coordinated'>
<option value='c_with'>Coordinated with</option>
<option value='c_to'>Coordinated to</option>
<option value='reinitialize'>Re-initialized</option>
<option value='recorded'>Recorded</option>

</select>
<input style='border: 1px solid gray' type=text name='coordinated_to' id='coordinated_to' /><input type=button value='Add' onclick='addCoordinate()' />

<!--
<input type=checkbox name='remarks_check' id='remarks_check' onclick='setPreset(this)' /><font color=blue>Preset Values</font>
-->

</td></tr>
<tr>
<td valign=top>Maintenance Provider:</td><td> <textarea rows=5 cols=50 name='maintenance'></textarea></td></tr>
<tr>
<th colspan=2><input type=submit value='Submit' /></th>
</tr>
</table>
</form>
</div>
<!--index number and car no.-->
</body>