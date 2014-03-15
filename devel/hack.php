<?php  
session_start();
$g_link=mysql_connect('localhost',$_SESSION['base_user'],$_SESSION['base_password']);
$base=$_SESSION['base'];

$idchange=$_GET['idchange'];   
mysql_select_db($base, $g_link);
mysql_query("set names 'utf8'");  
$sql1='select id from d_order where changeid='.$idchange.' order by id desc limit 1'; 
$result1=mysql_query($sql1);
$row=mysql_fetch_assoc($result1);

echo json_encode($row);

?>
