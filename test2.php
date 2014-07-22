<?php

if(isset($_POST['coordinated_with'])){
	echo "Yes";

}
else {
	echo "No";

}

?>
<script language='javascript' src='ajax.js'></script>
<script language='javascript'>

function testAJAX(){
	//makeajax("processing.php?trainDriver=Y","testScript");
//	var text = 
//	alert(text);
	window.opener.document.getElementById('test').value="kaya mo ba to";
}

function testScript(responseHTML){
//	alert(responseHTML);

}
function testFunction(animal){
	//alert(animal[1]);
//	alert(animal[2]);

}
</script>
<body>
<?php
if(isset($_GET['word'])){
echo $_GET['word'];
}


$animal['1']="tiger";
$animal['2']="lion";

echo "<a href='#' onclick='testFunction(".$animal.")'>Try</a>";



?>
<!--
<input type='checkbox' name='coordinated_with' />Coordinated With
-->
<?php
echo $_POST['testValue']; 
?>
<input type=button onclick='testAJAX()' />
<form action='test2.php' method='post'>
<input type='text' name='testValue' id='testValue'>
<input type=submit value='Submit' />
</form>
</body>

