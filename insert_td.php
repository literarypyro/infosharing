<?php

$db=new mysqli("localhost","root","","transport");

$update="insert into equipment(equipment_name,type,category) values ";
$update.="('Drive Circuit Interlocking (DCI)','RS','OB'),";
$update.="('Communication Error','RS','OB')";
$rs=$db->query($update);


$update="insert into equipment_type(equipment_code,equipment_name,incident_code) values ";
$update.="('unload','Unloading of Passengers','RS'),";
$update.="('nload','Not Loading','RS')";
$rs=$db->query($update);

$update="insert into train_driver(lastName,firstName,midName,position) values ";
$update.="('ALPAPARA','GARY KENNETH','','SUP'),";
$update.="('SAMAN','LUIS','A','SUP')";
$rs=$db->query($update);



?>

