<?php
if(isset($_GET['cancel'])){
	$db=new mysqli("localhost","root","","transport");
	$sql="update train_availability set status='cancelled' where id='".$_GET['cancel']."'";
	$rs=$db->query($sql);
	echo "<script language='javascript'>";
	echo "window.opener.location.reload();";

	echo "self.close();";
	echo "</script>";
}
?>

