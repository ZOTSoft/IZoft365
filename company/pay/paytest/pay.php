<?php
require_once("../paysys/kkb.utils.php");
$self = $_SERVER['PHP_SELF'];
$path1 = '../paysys/config.txt';	// Путь к файлу настроек config.dat


$order_id = 3;				// Порядковый номер заказа - преобразуется в формат "000001", 
							// номер заказа должен быть уникальным и состоит только из цифр
							// пожалуйста поменяйте значение  $order_id на другуюб потому что по этому номеру уже тестировали!
$currency_id = "398"; 			// Шифр валюты  - 840-USD, 398-Tenge
$amount = 10;				// Сумма платежа
$content = process_request($order_id,$currency_id,$amount,$path1); // Возвращает подписанный и base64 кодированный XML документ для отправки в банк
	//в поле email укажите реальный электронный адрес	
	//если вы тестируете то поменяйте значение action на https://3dsecure.kkb.kz/jsp/process/logon.jsp
?>
<html>
<head>
<title>Pay</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
</head>
<body>
<form name="SendOrder" method="post" action="https://epay.kkb.kz/jsp/process/logon.jsp">
   <input type="hidden" name="Signed_Order_B64" value="<?php echo $content;?>">
   <input type="hidden"  name="email" size=50 maxlength=50  value="zottig@bk.ru">

   <input type="hidden" name="Language" value="rus">
   
   <input type="hidden" name="BackLink" value="http://<?=$_SERVER['SERVER_NAME']?>/company/?do=paysuccess&no=<?=$order_id?>">
   <input type="hidden" name="PostLink" value="http://<?=$_SERVER['SERVER_NAME']?>/payconfirm.php">
   <input type="hidden" name="FailureBackLink" value="http://<?=$_SERVER['SERVER_NAME']?>/company/?do=payfail&no=<?=$order_id?>">
   
   
   Со счетом согласен (-а)<br>
   <input type="submit" name="GotoPay"  value="Да, перейти к оплате" >&nbsp;
</form>
</body>
</html>
