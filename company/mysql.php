<?
include($_SERVER['DOCUMENT_ROOT'].'/company/config.php');
$db = mysql_connect(HOST,$_SESSION['base_user'],$_SESSION['base_password']) or die("Database error");
mysql_select_db($_SESSION['base'], $db);
mysql_query("set names 'utf8'");
?>