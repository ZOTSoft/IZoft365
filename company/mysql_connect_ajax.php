<?
include($_SERVER['DOCUMENT_ROOT'].'/company/config.php');
$db_sconn = mysql_connect(HOST,LOGIN,PASS) or die("Database error");
mysql_query("set names 'utf8'",$db_sconn);
?>