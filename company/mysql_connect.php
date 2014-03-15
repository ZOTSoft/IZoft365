<?
include($_SERVER['DOCUMENT_ROOT'].'/company/config.php');
$db = mysql_connect(HOST,LOGIN,PASS) or die("Database error");
mysql_query("set names 'utf8'");
?>