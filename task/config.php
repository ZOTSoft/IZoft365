<?php
	//объявление сессии
	if (!isset($_SESSION)) { 
		session_start();
	}
		
	/*include('../../company/config.php');
	define ('DB_HOST', HOST);
	define ('DB_USER', $_SESSION['base_user']);
	define ('DB_PASSWORD', $_SESSION['base_password']);
	define ('DBASE', $_SESSION['base']);
	
	
	//подсключение к серверу
	@mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("MySQL Error: " . mysql_error());
	@mysql_query("set names utf8") or die ("<br>Invalid query: " . mysql_error());
	if (isset($_SESSION['timezone'])){ 
		date_default_timezone_set($_SESSION['timezone']); 
		mysql_query("SET `time_zone` = '".date('P')."'"); 
	}     
	@mysql_select_db(DBASE) or die("<br>Invalid query: " . mysql_error());
		*/
	//полный путь
	//define(PATH,"http://{$_SERVER['HTTP_HOST']}");
	
	include('../../company/config.php');
	$DB_HOST = HOST;
	$DB_USER = $_SESSION['base_user'];
	$DB_PASSWORD = $_SESSION['base_password'];
	$DBASE = $_SESSION['base'];
	
	
	//подсключение к серверу
	@mysql_connect($DB_HOST, $DB_USER, $DB_PASSWORD) or die ("MySQL Error: " . mysql_error());
	@mysql_query("set names utf8") or die ("<br>Invalid query: " . mysql_error());
	if (isset($_SESSION['timezone'])){ 
		date_default_timezone_set($_SESSION['timezone']); 
		mysql_query("SET `time_zone` = '".date('P')."'"); 
	}     
	@mysql_select_db($DBASE) or die("<br>Invalid query: " . mysql_error());
?>
