<?
$error='';
session_start();
include('company/mysql_connect.php');
include('company/core.php');
include('company/functions.php');

$message='Ваша почта подтвержена';

mysql_query("UPDATE dbisoftik.s_accounts SET status=1 WHERE regkey='".addslashes($_GET['key'])."' ");
?><!DOCTYPE html PUBLIC>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Paloma365 Подтверждение почты</title>
<link href="/index/CSS/login.css" rel="stylesheet" type="text/css">
<link href="/index/CSS/colors.css" rel="stylesheet" type="text/css">
<script src="/index/JS/jquery.js"></script>
<script src="/index/JS/login.js"></script>
</head>
<body style=" no-repeat; background-size: 100%;">
<div style="padding: 30px 0 0 30px;"><a href="http://paloma365.kz"><img src="/index/images/logo.png" alt=""></a></div>
<div id="regform" style="height: 127px;">
           <div style="text-align: center;font: 21px Arial;padding: 20px;"><?=$message;?> </div>
           <div style="text-align:center;">Для продолжения работы нажмите <a href="/login.php">сюда</a>.</div>
        </div> 

</body>
</html>
