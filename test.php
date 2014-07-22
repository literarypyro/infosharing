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
	window.open("test2.php","_blank");
	
}

function testScript(responseHTML){
	alert(responseHTML);

}
function testFunction(animal){
	alert(animal[1]);
	alert(animal[2]);

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
<input type=text name='test' id='test' />
<!--
<form action='test.php' method='post'>
<input type='checkbox' name='coordinated_with' />Coordinated With
-->
<input type=button onclick='testAJAX()' />
<!--
<input type=submit value='Submit' />
-->

</form>
</body>

