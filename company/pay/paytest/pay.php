<?php
require_once("../paysys/kkb.utils.php");
$self = $_SERVER['PHP_SELF'];
$path1 = '../paysys/config.txt';	// ���� � ����� �������� config.dat


$order_id = 3;				// ���������� ����� ������ - ������������� � ������ "000001", 
							// ����� ������ ������ ���� ���������� � ������� ������ �� ����
							// ���������� ��������� ��������  $order_id �� ������� ������ ��� �� ����� ������ ��� �����������!
$currency_id = "398"; 			// ���� ������  - 840-USD, 398-Tenge
$amount = 10;				// ����� �������
$content = process_request($order_id,$currency_id,$amount,$path1); // ���������� ����������� � base64 ������������ XML �������� ��� �������� � ����
	//� ���� email ������� �������� ����������� �����	
	//���� �� ���������� �� ��������� �������� action �� https://3dsecure.kkb.kz/jsp/process/logon.jsp
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
   
   
   �� ������ �������� (-�)<br>
   <input type="submit" name="GotoPay"  value="��, ������� � ������" >&nbsp;
</form>
</body>
</html>
