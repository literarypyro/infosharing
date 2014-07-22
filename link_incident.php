<?php
$db=new mysqli("localhost","root","","transport");
?>
<?php
function getOrdinal($number){
$ends = array('th','st','nd','rd','th','th','th','th','th','th');
if (($number %100) >= 11 && ($number%100) <= 13)
   $abbreviation = $number. 'th';
else
   $abbreviation = $number. $ends[$number % 10];

   
 return $abbreviation;  

}
?>
<?php
if(isset($_POST['fieldType'])){
	$sql="update incident_report ";
	$incident_report=$_POST['incident_report'];
	switch($_POST['fieldType']){
		case "onboard_equipt":
			$sql.="set equipt='".$_POST['equipment']."' ";
			break;
		case "dotc":
			if(isset($_POST['dotc'])){
				$dotc_taken=$_POST['dotc'];

			}
			else if(isset($_POST['dotc_coordinated'])){
				$dotc_taken=$_POST['dotc_coordinated']." ".$_POST['coordinated_to'];
			}		
		
			$sql.="set action_dotc='".$dotc_taken."' ";
			break;
		case "maintenance":
			$sql.="set action_maintenance='".$_POST['maintenance_provider']."' ";
			break;
		case "level":
			$sql.="set level='".$_POST['level']."' ";
			break;
		case "description":
			$sql.="set description='".$_POST['description']."' ";
			break;

		case "duration":
			$sql.="set duration='".$_POST['duration']."' ";
			break;
		case "incident_no":
			
			$incidentSQL="select * from incident_report where id='".$incident_report."'";
			$incidentRS=$db->query($incidentSQL);
			$incidentRow=$incidentRS->fetch_assoc();
			
			$suffixSQL="select * from equipment_type where equipment_code='".$incidentRow['incident_type']."'";
			$suffixRS=$db->query($suffixSQL);
			
			$suffixRow=$suffixRS->fetch_assoc();
			$suffix=$suffixRow['incident_code'];
		
		
		
			$sql.="set incident_no='".$_POST['incident_number']." ".$suffix."' ";
			//$_POST['incident_report']=$_POST['incident_number'];
			break;
		case "problem":
			$sql.="set incident_type='".$_POST['type']."',equipt='',";

			$incidentSQL="select * from incident_report where id='".$incident_report."'";
			$incidentRS=$db->query($incidentSQL);
			$incidentRow=$incidentRS->fetch_assoc();
			
			$suffixSQL="select * from equipment_type where equipment_code='".$_POST['type']."'";
			$suffixRS=$db->query($suffixSQL);
			$suffixRow=$suffixRS->fetch_assoc();
			$suffix=$suffixRow['incident_code'];
		
		
		
			$sql.="incident_no='".$incidentRow['id']." ".$suffix."' ";


			
			if($_POST['type']=="ser_int"){
				echo "<script language='javascript'>";
				echo "window.open('service interruption.php?incident=".$incident_report."');";
				echo "</script>";
			}
			
			
			
			
			break;
		case "cancelled":
		
			$cancelTerm=$_POST['cancel'];
			if($cancelTerm=="whole"){
				$cancel=1;
			}
			else if($cancelTerm=="half"){
				$cancel=.5;
			}
			else if($cancelTerm=="more"){
				$cancel=$_POST['cancel_more'];
			}
			$sql.="set cancel='".$cancel."' ";
			break;	
		case "date":
			$year=$_POST['year'];
			$month=$_POST['month'];
			$day=$_POST['day'];
			
			$hour=$_POST['hour'];
//			echo $hour;
			$minute=$_POST['minute'];
//			echo $minute;
			$amorpm=$_POST['amorpm'];
//			echo $amorpm;
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
			
			$incident_date=$year."-".$month."-".$day." ".$hour.":".$minute;
			//date("Y-m-d H:i",strtotime($year."-".$month."-".$day." ".$hour.":".$minute));
	//		echo $incident_date;
			$sql.="set incident_date='".$incident_date."' ";
			break;
		}
	$sql.=" where id='".$incident_report."'";

	$rs=$db->query($sql);
	
	if($_POST['fieldType']=='onboard_equipt'){
		$update="update incident_description set equipt='".$_POST['equipment']."', subitem='".$_POST['subitem']."' where incident_id='".$incident_report."'";

		$rs=$db->query($update);
	}
	if($_POST['fieldType']=='problem'){
		$update="update incident_description set equipt='', subitem='' where incident_id='".$incident_report."'";
		$rs=$db->query($update);
		
		
		
		
		
		
	}


	else if($_POST['fieldType']=="level"){
		$levelSQL="select * from level where incident_id='".$incident_report."'";
		$levelRS=$db->query($levelSQL);
		
		$levelNM=$levelRS->num_rows;

		if($_POST['level']=="2"){
			$update="update incident_report set l2='".$_POST['order']."',l3='',l4='' where id='".$incident_report."'";
			$rs=$db->query($update);
			
			
			if($levelNM>0){
//				$update="update level set level='2',order='".$_POST['order']."' where id='".$incident_report."'";
//				$rs=$db->query($update);
			}
			else {
//				$update="insert into level(level,order,incident_id
			
			}

		}
		else if($_POST['level']=="3"){
			$update="update incident_report set l3='".$_POST['order']."',l2='',l4='' where id='".$incident_report."'";
			$rs=$db->query($update);
		
			if($levelNM>0){
			
			
			}
			else {
			
			
			}

		}
		else if($_POST['level']=="4"){
			$update="update incident_report set l4='".$_POST['order']."',l2='',l3='' where id='".$incident_report."'";
			$rs=$db->query($update);
			
			if($levelNM>0){
			
			
			}
			else {
			
			}

		}

	}
	else if($_POST['fieldType']=="index"){
		$update="update incident_description set index_no='".$_POST['index_id']."', car_no='".$_POST['car']."' where incident_id='".$incident_report."'";
		$rs=$db->query($update);
		
		$update="delete from incident_cars where incident_id='".$incident_report."'";
		$rs=$db->query($update);

		if($_POST['car']==""){
		}
		else {
			$update="insert into incident_cars(incident_id,car_no) values ('".$incident_report."','".$_POST['car']."')";
			$rs=$db->query($update);

		}
		
		if($_POST['car_2']==""){
		}
		else {
			$update="insert into incident_cars(incident_id,car_no) values ('".$incident_report."','".$_POST['car_2']."')";
			$rs=$db->query($update);
		
		}
		
		if($_POST['car_3']==""){
		}
		else {
			$update="insert into incident_cars(incident_id,car_no) values ('".$incident_report."','".$_POST['car_3']."')";
			$rs=$db->query($update);
		
		}
		
		
		
	}
	
	else if($_POST['fieldType']=="location"){
		$update="update incident_description set location='".$_POST['location']."',direction='".$_POST['direction']."' where incident_id='".$incident_report."'";

		$rs=$db->query($update);

	}

	else if($_POST['fieldType']=="reported_by"){
		$update="update incident_description set reported_by='".$_POST['reported_by']."' where incident_id='".$incident_report."'";

		$rs=$db->query($update);

	}

	else if($_POST['fieldType']=="received_by"){
		$update="update incident_description set received_by='".$_POST['received_by']."' where incident_id='".$incident_report."'";

		$rs=$db->query($update);

	}

	
	echo "Data updated.";

}

?>
<style type='text/css'>
.ccdr tr:nth-child(odd)
{
background-color: #dfe7f2;
color: #000000;
}
.ccdr tr th:first-child {
	color: rgb(0,51,153);

}

.ccdr td, .ccdr th {
border: 1px solid rgb(185, 201, 254);
padding: 0.3em;
}
.ccdr {
border: 1px solid rgb(185, 201, 254);

}
.ccdr #ccdr_heading {
background-color:rgb(185, 201, 254);
color: rgb(0,51,153);
}
body {
	margin-left:30px;
	margin-right:30px;

}


textarea{ 
	border: 1px solid rgb(185, 201, 254);
	background-color: #dfe7f2;
	color: rgb(0,51,153);
	border-radius: 3px;
}

#edit_form th {
	background-color: rgb(185, 201, 254);
	color: rgb(0,51,153);

}
#edit_form input[type="text"] {
	height:25px; 
	font-weight:bold; 
	font-size:15px; 
	font-family:courier; 
	border: 1px solid rgb(185, 201, 254);
	background-color: #dfe7f2;
	color: rgb(0,51,153);
	border-radius: 3px;

}

.label {
	background-color: rgb(185, 201, 254);
	color: rgb(0,51,153);
	spacing: 2px;
	padding: 5px;
}

.text_input {
	height:25px; 
	font-weight:bold; 
	font-size:15px; 
	font-family:courier; 
	border: 1px solid rgb(185, 201, 254);
	background-color: #dfe7f2;
	color: rgb(0,51,153);
	border-radius: 3px;

}


.rowHeading {
	color: rgb(0,51,153);
	font-weight:bold;
}

select { border: 1px solid rgb(185, 201, 254); color: rgb(0,51,153); background-color: #dfe7f2;  }
</style>
<script language='javascript' src='ajax.js'></script>
<script language='javascript'>
function linkIncident(){
	
	var check=confirm("Link to Incident?");
	if(check){
		var incident_id=document.getElementById('incident_report').value;
		var incident_no=document.getElementById('incident_report1').value;

		window.opener.document.getElementById('incident_link').value=incident_id;
		window.opener.document.getElementById('incident_no_link').value=incident_no;
		
		
		self.close();
	}
}
function fillEdit(elementName){
	var elementContents="";
	
	if(elementName=='dotc'){
		elementContents="<tr class='rowHeading'><td>&nbsp;</td><td>Edit Action Taken</td></tr>";
		elementContents+="<tr><th width=20%>DOTC</th><td><span name='remarks_space' id='remarks_space'><textarea rows=5 cols=50 name='dotc'></textarea></span>";
		elementContents+="<input type=checkbox name='remarks_check' id='remarks_check' onclick='setPreset(this)' /><font color=blue>Preset Values</font>";
		
		
		elementContents+="</td></tr>";
			
		
		document.getElementById('fieldType').value='dotc';
	}
	else if(elementName=="maintenance"){
		elementContents="<tr class='rowHeading'><td>&nbsp;</td><td>Edit Action Taken</td></tr>";

		elementContents+="<tr><th width=20%>Maintenance Provider</th><td><textarea rows=5 cols=50 name='maintenance_provider'></textarea></td></tr>";
		document.getElementById('fieldType').value='maintenance';
	
	}
	else if(elementName=="level"){
		elementContents="<tr class='rowHeading'><td>&nbsp;</td><td>Edit CCDR Details</td></tr>";
		elementContents+="<tr><th width=20%>Level</th>";
		elementContents+="<td>";
		elementContents+="<select name='level' onchange='enterOrder(this.value)'>";
		elementContents+="<option value='1'>1</option>";
		elementContents+="<option value='2'>2</option>";
		elementContents+="<option value='3'>3</option>";
		elementContents+="<option value='4'>4</option>";
		elementContents+="</select><span name='order_space' id='order_space'></span>";
		elementContents+="</td></tr>";
		document.getElementById('fieldType').value='level';

	}
	else if(elementName=="description"){
		elementContents="<tr class='rowHeading'><td>&nbsp;</td><td>Edit CCDR Details</td></tr>";
		elementContents+="<tr><th width=20%>Description</th><td><textarea rows=5 cols=50 name='description'></textarea></td></tr>";
		document.getElementById('fieldType').value='description';

	}
	else if(elementName=="onboard_equipt"){
	
/*		elementContents="<tr class='rowHeading'><td>&nbsp;</td><td>Edit CCDR Details</td></tr>";
		var equipmentHTML=document.getElementById('equipment_copy').innerHTML;
		elementContents+="<tr><th width=20%>On-Board Equipment/Accessories</th><td><select name='onboard_equipt'>"+equipmentHTML+"</select></td></tr>";
		document.getElementById('fieldType').value='onboard_equipt';
*/		
	}
	else if(elementName=="duration"){
		elementContents="<tr class='rowHeading'><td>&nbsp;</td><td>Edit CCDR Details</td></tr>";
		elementContents+="<tr><th width=20%>Incident Duration</th><td><input type=text name='duration' /></td></tr>";
		document.getElementById('fieldType').value='duration';


	}
	else if(elementName=="incident_no"){
		elementContents="<tr class='rowHeading'><td>&nbsp;</td><td>Edit CCDR Details</td></tr>";
		elementContents+="<tr><th width=20%>Incident Number</th><td><input type=text name='incident_number' /></td></tr>";
		document.getElementById('fieldType').value='incident_no';


	}

	else if(elementName=="date"){
		elementContents="<tr class='rowHeading'><td>&nbsp;</td><td>Edit CCDR Details</td></tr>";
	
		var dateHTML=setDate();
//		var dateHTML=document.getElementById('dateStamp').innerHTML;
		elementContents+="<tr><th width=20%>Date/Time</th><td>"+dateHTML+"</td></tr>";
		document.getElementById('fieldType').value='date';
		
	}
	else if(elementName=="problem"){
		elementContents="<tr class='rowHeading'><td>&nbsp;</td><td>Edit CCDR Details</td></tr>";
		elementContents+="<tr><th width=20%>Type of Problem</th>";
		
		elementContents+="<td>";
		elementContents+="<select name='type' id='type' onchange='getCategory(this.value)'>";
		elementContents+="<option value='cc_equipt'>CC Equipment</option>";
		elementContents+="<option value='communication'>Communication</option>";
		elementContents+="<option value='depot_equipt'>Depot Equipment</option>";
		elementContents+="<option value='power'>Power</option>";
		elementContents+="<option value='rolling'>Rolling Stock</option>";
		elementContents+="<option value='signaling'>Signaling</option>";
		elementContents+="<option value='tracks'>Tracks</option>";
		elementContents+="<option value='gradual'>Gradual Removal</option>";
		elementContents+="<option value='c_loops'>Cancelled Loops; Acc. Delay/Failure</option>";
		elementContents+="<option value='r_trains'>Running Trains</option>";

		//		elementContents+="<option value='ser_int'>Service Interruption</option>";
		elementContents+="<option value='others'>Others</option>";

		elementContents+="</select>";

//		elementContents+="<span id='rolling_category' name='rolling_category'>";
		
//		elementContents+="</span>";
		
		elementContents+="</td></tr>";
		
		document.getElementById('fieldType').value='problem';
	}
	else if(elementName=="index"){
		elementContents="<tr class='rowHeading'><td>&nbsp;</td><td>Edit CCDR Details</td></tr>";
		elementContents+="<tr><th width=20%>Index/Car No.</th>";

		elementContents+="<td>";
		elementContents+="<input type='text' name='index_id' id='index_id' size=5 /> / ";

		elementContents+="<span id='car_space' name='car_space'></span>";	

		
		elementContents+="</td></tr>";

		document.getElementById('fieldType').value='index';

		
	}
	else if(elementName=="location"){
		
		elementContents="<tr class='rowHeading'><td>&nbsp;</td><td>Edit CCDR Details</td></tr>";
		elementContents+="<tr><th width=20%>Location/Direction</th>";

		elementContents+="<td>";

		elementContents+="<select name='direction' id='direction' onchange='setDirection(this.value)'>";
		elementContents+="<option></option>";
		elementContents+="<option value='S'>Station</option>";
		elementContents+="<option value='D'>Depot</option>";
		elementContents+="<option value='CC'>Control Center</option>";

		elementContents+="<option value='NB'>Northbound</option>";
		elementContents+="<option value='SB'>Southbound</option>";
		elementContents+="<option value='NTB'>North Turnback</option>";
		elementContents+="<option value='IR'>Insertion/Removal Area</option>";
		elementContents+="<option value='SPT'>Shaw Pocket Track</option>";
		elementContents+="<option value='TPT'>Taft Pocket Track</option>";


		elementContents+="</select>";
		
		elementContents+=" ";
		elementContents+="<input type='text' size=5 name='location' id='location' />";
		
		elementContents+="</td></tr>";
		
		document.getElementById('fieldType').value='location';
		
		
	}
	else if(elementName=="reported_by"){

		elementContents="<tr class='rowHeading'><td>&nbsp;</td><td>Edit Reporting Details</td></tr>";
		elementContents+="<tr><th width=20%>Reported By</th>";
		elementContents+="<td>";
		elementContents+="<input type=text name='reported_by' id='reported_by' />";

		elementContents+="</td></tr>";

		document.getElementById('fieldType').value='reported_by';
	
	}
	else if(elementName=="received_by"){
		elementContents="<tr class='rowHeading'><td>&nbsp;</td><td>Edit Reporting Details</td></tr>";
		elementContents+="<tr><th width=20%>Reported By</th>";
		elementContents+="<td>";
		elementContents+="<span name='receive_space' id='receive_space'> </span>";

		elementContents+="</td></tr>";

		document.getElementById('fieldType').value='received_by';
	
	}
	else if(elementName=="cancel"){
		elementContents="<tr class='rowHeading'><td>&nbsp;</td><td>Edit Incident Details</td></tr>";
		elementContents+="<tr><th width=20%>Cancelled Loops</th>";
		elementContents+="<td>";
		
		
		elementContents+="<select name='cancel' id='cancel' onchange='getMore(this.value)'>";
		elementContents+="<option value='none'>0</option>";
		elementContents+="<option value='whole'>1</option>";
		elementContents+="<option value='half'>1/2</option>";
		elementContents+="<option value='more'> more than 1</option>";
		elementContents+="</select>";
		elementContents+="<input type=text name='cancel_more' id='cancel_more' size=5 style='border:1px solid gray' disabled />";		
		
		elementContents+="</td></tr>";

		document.getElementById('fieldType').value='cancelled';
	
	
	
	}
	
	
	
	document.getElementById('edit_table').innerHTML=elementContents;
	
	
	if(elementName=="index"){

		var incident_id=document.getElementById('incident_report').value;

		makeajax("processing.php?getCars="+incident_id,"fillCars");	

		
		
	}
	else if(elementName=="received_by"){
	
		makeajax("processing.php?supervisor=Y","fillSuper");	
	
	}
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

function getMore(cancel){
	if(cancel=="more"){
		document.getElementById('cancel_more').disabled=false;
	}
	else {
		document.getElementById('cancel_more').disabled=true;
	}

}


function fillSuper(ajaxHTML){

	if(ajaxHTML=="None available"){
	}
	else {

		driverHTML="<select name='received_by' id='received_by'>";

		var driverTerms=ajaxHTML.split("==>");
		var count=(driverTerms.length)*1-1;
		
		for(var n=0;n<count;n++){
			var parts=driverTerms[n].split(";");
			driverHTML+="<option value='"+parts[0]+"'>";
			driverHTML+=parts[1];
			driverHTML+="</option>";
		
		}
		driverHTML+="</select>";
		
	}
	document.getElementById('receive_space').innerHTML=driverHTML;
	
}

function fillCars(ajaxHTML){
	var subHTML="";
	
	if(ajaxHTML=="No data available"){
	
	
	}
	else {
		var subItemTerms=ajaxHTML.split(";");
		var count=(subItemTerms.length)*1-1;


		var optionHTML="";
		for(var n=0;n<count;n++){
			optionHTML+="<option value='"+subItemTerms[n]+"'>";
			optionHTML+=subItemTerms[n];
			optionHTML+="</option>";
		
		}


		subHTML="<select id='car' name='car'>";
		subHTML+="<option></option>";
		subHTML+=optionHTML;
		subHTML+="</select>, ";


		subHTML+="<select id='car_2' name='car_2'>";
		subHTML+="<option></option>";
		subHTML+=optionHTML;
		subHTML+="</select>, ";

		subHTML+="<select id='car_3' name='car_3'>";
		subHTML+="<option></option>";
		subHTML+=optionHTML;
		subHTML+="</select>";
		
	}
	document.getElementById('car_space').innerHTML=subHTML;
	
}


function enterOrder(level){
	var orderHTML="";
	if((level==2)||(level==3)){
		orderHTML+="<input type='text' name='order' id='order' size=5 /> Order of Removal";
		
	}
	else {
		orderHTML="";
	
	}

	document.getElementById('order_space').innerHTML=orderHTML;
}



function fillEquipt(problemType,equiptId){
	var elementName=problemType;
	
	if(elementName=="onboard_equipt"){
		
		
		
		elementContents="<tr class='rowHeading'><td>&nbsp;</td><td>Edit CCDR Details</td></tr>";
		elementContents+="<tr><th width=20%>On-Board Equipment/Accessories</th><td><span id='rolling_category' name='rolling_category'></span><span id='equipment_space' name='equipment_space'> <select name='equipment' id='equipment' onchange='fillSubItem(this.value)'></select></span> <span id='sub_item_space' name='sub_item_space' ></span></td></tr>";

		//var equipmentHTML=document.getElementById('equipment_copy').innerHTML;
		//elementContents+="<tr><th width=20%>On-Board Equipment/Accessories</th><td><select name='onboard_equipt'>"+equipmentHTML+"</select></td></tr>";

		document.getElementById('edit_table').innerHTML=elementContents;

		document.getElementById('fieldType').value='onboard_equipt';

		if((equiptId=="rolling")||(equiptId=="power")){
			getCategory(equiptId);
		}
		else if((equiptId=="cc_equipt")||(equiptId=="depot_equipt")){
			
			document.getElementById('equipment_space').innerHTML="<input type='text' name='equipment' id='equipment' />";
		}
		else if((equiptId=="gradual")||(equiptId=="ser_int")){
			document.getElementById('equipment_space').innerHTML="";
		}
		else if(equiptId=="others"){
			makeajax("processing.php?scrollOthers="+problemType,"fillOnboard");		
		}
		else {
			makeajax("processing.php?scrollRolling="+problemType,"fillOnboard");		
		}
	}
}

function fillItem(equiptId,categoryId){
	makeajax("processing.php?scrollRolling="+equiptId+"&category="+categoryId,"fillOnboard");	
	

}

function fillSubItem(equiptId){
	makeajax("processing.php?scrollSubItem="+equiptId,"subItem");	
	

}


function setDate(){
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
	
	
	
	dateHTML="<select name='month' id='month'>";

	for(var i=1;i<=12;i++){
		d=new Date(year+"-"+i+"-1");	
		var month="";

		switch(i){
			case 1: month='January'; break;
			case 2: month='February'; break;
			case 3: month='March'; break;
			case 4: month='April'; break;
			case 5: month='May'; break;
			case 6: month='June'; break;
			case 7: month='July'; break;
			case 8: month='August'; break;
			case 9: month='September'; break;
			case 10: month='October'; break;
			case 11: month='November'; break;
			case 12: month='December'; break;
		
		}
		
		dateHTML+="<option value='"+i+"' "; 
		
		if(mmonth==i){
		dateHTML+="selected";
		}
		dateHTML+=">";
		dateHTML+=month;
		dateHTML+="</option>";
		
	}
	dateHTML+="</select>";

	
	dateHTML+="<select name='day' id='day'>";
	for(var i=1;i<=31;i++){
		dateHTML+="<option value='"+i+"' ";
		if(day==i){
		dateHTML+="selected";
		}
		dateHTML+=">"+i+"</option>";
	}
	
	dateHTML+="</select>";

	yearLimit=year*1+16;
	dateHTML+="<select name='year' id='year'>";
	for(var i=1999;i<=yearLimit;i++){
		dateHTML+="<option value='"+i+"' ";
		if(year==i){
		dateHTML+="selected";
		}
		dateHTML+=">"+i+"</option>";
	}
	
	dateHTML+="</select>";
//	dateHTML+="<br>";
	dateHTML+="<select name='hour'>";
	
	for(var i=1;i<=12;i++){
		dateHTML+="<option value='"+i+"' ";
		if(hour==i){
		dateHTML+="selected";
		}
		dateHTML+=">"+i+"</option>";
	}
	
	
	
	dateHTML+="</select>";

	dateHTML+="<select name='minute'>";
	
	var label="";
	for(var i=0;i<=59;i++){
		
		if(i<10){
			label="0"+i;			
		}
		else {
			label=i;
		}
		
		dateHTML+="<option value='"+i+"' ";
		if(minute==i){
		dateHTML+="selected";
		}
		dateHTML+=">"+label+"</option>";

	}
	
	
	
	dateHTML+="</select>";
	dateHTML+="<select name='amorpm'>";
	dateHTML+="<option value='am' ";
	if(amorpm=="AM"){
		dateHTML+="selected";
	}
	dateHTML+=">AM</option>";

	dateHTML+="<option value='pm' ";
	if(amorpm=="PM"){
		dateHTML+="selected";
	}
	dateHTML+=">PM</option>";

	dateHTML+="</select>";
	
    return dateHTML;
	



	
}




function fillOnboard(ajaxHTML){
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

}


function subItem(ajaxHTML){
	var subHTML="";
	
	if(ajaxHTML=="No data available"){
	
	
	}
	else {
		var subItemTerms=ajaxHTML.split("==>");
		var count=(subItemTerms.length)*1-1;
		subHTML="<select id='subitem' name='subitem'>";
		//subHTML+="<option></option>";
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



function getCategory(problemType){
	var rollingHTML="";
	if(problemType=="rolling"){
		
		rollingHTML+="<select name='category' id='category' onchange='fillItem(\""+problemType+"\",this.value)' >";
		rollingHTML+="<option></option>";
		rollingHTML+="<option value='EXT'>Exterior</option>";
		rollingHTML+="<option value='UFE'>Underfloor Equipment</option>";
		rollingHTML+="<option value='OB'>Onboard Equipment and Accessories</option>";
		rollingHTML+="<option value='OTH'>Others</option>";


		rollingHTML+="</select>";	
		document.getElementById('rolling_category').innerHTML=rollingHTML;
	
	}
	
	else {
		if(problemType=="power"){
			rollingHTML+="<select id='category' name='category' onchange='fillItem(\""+problemType+"\",this.value)'>";
			rollingHTML+="<option></option>";
			rollingHTML+="<option value='OCS'>Overhead Catenary System</option>";
			rollingHTML+="<option value='SS'>Station Substation</option>";
			rollingHTML+="<option value='TPSS'>Traction Power Substation Equipment</option>";


			rollingHTML+="</select>";	
			
			document.getElementById('rolling_category').innerHTML=rollingHTML;
		
		
		}
		else {
			document.getElementById('rolling_category').innerHTML=rollingHTML;

		}
		
	}
}



</script>
<body>
<br>


<form action='link_incident.php' method='post'><span class='label'>Search Incident Number</span><input class='text_input' type=text name='search_incident_number' /><input type=submit value='Search' /></form>

<?php
$db=new mysqli("localhost","root","","transport");
?>
<?php
if(isset($_POST['search_incident_number'])){
	$sql="select * from incident_report where incident_no like '".$_POST['search_incident_number']."%%' order by incident_no";

	$rs=$db->query($sql);
	$nm=$rs->num_rows;
	if($nm>0){
	?>	
<br>
<form action='link_incident.php' method=post>
	<span class='label'>
	Retrieve Incident Report: 
	</span>
	<select  class='text_input' name='incident_report'>
	<?php
		for($i=0;$i<$nm;$i++){
		$row=$rs->fetch_assoc();
	?>	
			<option value="<?php echo $row['id']; ?>"><?php echo $row['incident_no']; ?></option>
	<?php
		}
	?>
	</select>
	<input type=submit value='Retrieve' />
</form>
	<?php	
	}

}
?>
<br>

<?php
?>
<?php
	if((isset($_POST['incident_report']))||(isset($_GET['ir']))){
		
		if(isset($_GET['ir'])){
			$incident_report=$_GET['ir'];
		}
		else {
			$incident_report=$_POST['incident_report'];
		
		}
		
//		$sql="select * from train_incident_view inner join level on train_incident_view.incident_id=level.incident_id where id='".$incident_report."'";
		$sql="select * from incident_report inner join level on incident_report.id=level.incident_id where incident_report.id='".$incident_report."'";
		
		$rs=$db->query($sql);
		$row=$rs->fetch_assoc();
			
		$level_condition=$row['level_condition'];
		
		$conditionSQL="select * from level_condition where id='".$level_condition."'";
		$conditionRS=$db->query($conditionSQL);
		
		$conditionRow=$conditionRS->fetch_assoc();
		
		$condition=$conditionRow['description'];
		
		
			
		$incident_no=$row['incident_no'];
		$problem_type2=$row['incident_type'];
		$equipSQL="select * from equipment_type where equipment_code='".$problem_type2."'";
		$equipRS=$db->query($equipSQL);
		$row2=$equipRS->fetch_assoc();
		$problem_type=$row2['equipment_name'];


		$level=$row['level'];
		
		$levelClause=="";
		if($level==2){
			$levelClause.=" (".getOrdinal($row['order']).")";
		
		}
		else if($level==3){
			$levelClause.=" (".getOrdinal($row['order']).")";
		
		}
		else if($level==4){
			$levelClause.=" (".getOrdinal($row['order']).")";
		
		}		
		$cancel=$row['cancel'];
		
		
		$date=date("Y-m-d",strtotime($row['incident_date']));
		$time=date("H:ia",strtotime($row['incident_date']));
		$duration=$row['duration'];
		$equipt=$row['equipt'];
		
		$onboard_equipt="";
		if($problem_type2=="others"){
			$equipSQL="select * from other_problem where id='".$equipt."'";

			$equipRS=$db->query($equipSQL);
			$row2=$equipRS->fetch_assoc();
			$onboard_equipt=$row2['problem'];		
		}
		else {
			$equipSQL="select * from equipment where id='".$equipt."'";

			$equipRS=$db->query($equipSQL);
			$row2=$equipRS->fetch_assoc();
			$onboard_equipt=$row2['equipment_name'];
		}
		
		$description=$row['description'];
		$dotc_action=$row['action_dotc'];
		$maintenance_action=$row['action_maintenance'];
		
		$category=$row['category'];

		$categoryName="";

		if($category==""){
			$categorySQL="select * from category where category_code='".$category."'";
			$categoryRS=$db->query($categorySQL);
			
			$categoryRow=$categoryRS->fetch_assoc();
			
			$categoryName=$categoryRow['category'];

		
		}
		

		$irSQL="select * from incident_description where incident_id='".$incident_report."'";
		$irRS=$db->query($irSQL);
		
			
		$irRow=$irRS->fetch_assoc();
		
		
		$indexNo=$irRow['index_no'];
		$carNo=$irRow['car_no'];
		
		$car[0]="";
		$car[1]="";
		$car[2]="";

		
		$carSQL="select * from incident_cars where incident_id='".$incident_report."'";
		$carRS=$db->query($carSQL);
		$carNM=$carRS->num_rows;
		
		if($carNM>0){
			for($b=0;$b<$carNM;$b++){
				$carRow=$carRS->fetch_assoc();
				$car[$b]=$carRow['car_no'];
			}			
			
			$carClause=$car[0];
			if($car[1]==""){
			}
			else {
				$carClause.=", ".$car[1];
			}
			
			if($car[2]==""){
			}
			else {
				$carClause.=", ".$car[2];
			}
			
		}
		
		
		
		
		
		
		
		$location=$irRow['location'];
		$direction=$irRow['direction'];
		
		
		
		$reported_by=$irRow['reported_by'];
		$received_by="";
		
		$tdSQL="select * from train_driver where id='".$irRow['received_by']."'";
		$tdRS=$db->query($tdSQL);
		$tdNM=$tdRS->num_rows;
		if($tdNM>0){
			$tdRow=$tdRS->fetch_assoc();
			$received_by=$tdRow['lastName'].", ".$tdRow['firstName'];
			
		}
		
		
		
		
		if($direction=="S"){
			$direction="";
		}
		
		
		$subClause="";
		
		$subItemSQL="select * from sub_item where id='".$irRow['subitem']."'";
		$subItemRS=$db->query($subItemSQL);
		$subItemNM=$subItemRS->num_rows;
		
		if($subItemNM>0){
			$subItemRow=$subItemRS->fetch_assoc();

			$subClause=" / ".$subItemRow['sub_item'];			
		
		}
		
		$db=new mysqli("localhost","root","","transport");
		
		$serviceSQL="select * from service_interruption where incident_id='".$incident_report."'";
		$serviceRS=$db->query($serviceSQL);
		$serviceNM=$serviceRS->fetch_assoc();
		
		
	}



?>

<?php
if(isset($_POST['incident_report'])){
?>
<form id='edit_form' name='edit_form' action='link_incident.php'  method='post'>
	<table id='edit_table' name='edit_table' width=80%>	
	</table>
	<table width=80%>
	<tr><th  style='border:2px solid red;' width=20%>Incident ID</th><td><input type=hidden id='incident_report' name='incident_report' value='<?php echo $incident_report; ?>' /><input style='border:2px solid red;' type='text' id='incident_report1' name='incident_report1' value='<?php echo $incident_no; ?>' /></td></tr>
	</table>
	<br>
	<div align=left><font color=white>| | | | | | | | | | | | | | | | | | | |</font><input  type=hidden name='fieldType' id='fieldType' /><input type=button value='Link Incident' onclick='linkIncident()' /></div>
</form>
<?php
}
?>
<br>
<br>


<table  width=80% class='ccdr'>
<tr id='ccdr_heading'><th colspan=3>Link Incident Number</th></tr>
<tr><th width=20%>Incident Number:</th><td width=70%><?php echo $incident_no; ?></td></tr>
<tr><th>Type of Problem:</th>
<td>
<?php echo $problem_type; ?>
<?php
if($categoryName==""){
}
else {
	echo " / ".$categoryName;

}
?>
<?php
if($level_condition=="3"){
	if($serviceNM>0){
		echo " [<a href='#' onclick='window.open(\"service interruption.php?incident=".$incident_report."\")'>Report</a>]";
	
	}

}

?>
</td>
</tr>

<tr><th>On-board Equipt/Accessories:</th>


<td><?php echo $onboard_equipt; ?>
<?php
echo $subClause; 
?>

<span style="visibility:hidden">
<select name='equipment_copy' id='equipment_copy' hidden>
<option></option>
<?php 
$db=new mysqli("localhost","root","","transport");
$sql="select * from equipment order by equipment_name";
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
</span>
</td>
</tr>

<tr>
<th>Index No./Car No.</th>


<td>
<?php
if($carNo==""){
	echo $indexNo;
}
else {
	echo $indexNo." / ".$carClause;

}

?>


</td>
</tr>

<tr>
<th>Cancelled Loops</th>
<td>
<?php echo $cancel; ?>

</td>
</tr>

</tr>
<tr><th>Level:</th><td><?php echo $level; echo $levelClause; echo ". ".$condition; ?></td>
</tr>



<tr><th>Date:</th><td><?php echo $date; ?></td>
</tr>
<tr><th>Time:</th><td><?php echo $time; ?></td>
</tr>
<tr><th>Incident Duration:</th><td><?php echo $duration; ?></td>
</tr>

<tr><th>Location/Direction:</th><td><?php echo $direction; echo " ".$location; ?></td></tr>




<tr><th>Description:</th><td><?php echo $description; ?></td></tr>
		
		

</table>
<br>
<table  class='ccdr' width=80% border=1>
<tr id='ccdr_heading'><th colspan=3>Reporting</th></tr>
<tr><th width=20%>Reported By</th><td width=70%><?php echo $reported_by; ?></td>

</tr>
<tr><th>Received By:</th><td><?php echo $received_by; ?></td>
</tr>

</table>
<br>
<table  class='ccdr' width=80% border=1>
<tr id='ccdr_heading'><th colspan=3>Action Taken</th></tr>
<tr><th width=20%>DOTC:</th><td width=70%><?php echo $dotc_action; ?></td>
</tr>
<tr><th>Maintenance Provider:</th><td><?php echo $maintenance_action; ?></td>
</tr>


</table>
<br>
<br>
</body>