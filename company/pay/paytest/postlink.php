<?php 
require_once("../paysys/kkb.utils.php");
	// $data['BANK_SIGN_CHARDATA'] = ";skljfasldimn,samdbfyJHGkmbsa;fliHJ:OIUHkjbn"
	// $data['BANK_SIGN_TYPE'] = "SHA/RSA"
	// $data['CUSTOMER_NAME'] = "123"
	// $data['CUSTOMER_SIGN_TYPE'] = "RSA"
	// $data['DEPARTMENT_AMOUNT'] = "10"
	// $data['MERCHANT_NAME'] = "test merch"
	// $data['MERCHANT_SIGN_TYPE'] = "RSA"
	// $data['ORDER_AMOUNT'] = "10"
	// $data['ORDER_CURRENCY'] = "398"
	// $data['ORDER_ORDER_ID'] = "000001"
	// $data['PAYMENT_AMOUNT'] = "10"
	// $data['PAYMENT_RESPONSE_CODE'] = "00"
	// $data['RESULTS_TIMESTAMP'] = "2001-01-01 00:00:00"
	// $data['TAG_BANK'] = "BANK"
	// $data['TAG_BANK_SIGN'] = "BANK_SIGN"
	// $data['TAG_CUSTOMER'] = "CUSTOMER"
	// $data['TAG_CUSTOMER_SIGN'] = "CUSTOMER_SIGN"
	// $data['TAG_DEPARTMENT'] = "DEPARTMENT"
	// $data['TAG_DOCUMENT'] = "DOCUMENT"
	// $data['TAG_MERCHANT'] = "MERCHANT"
	// $data['TAG_MERCHANT_SIGN'] = "MERCHANT_SIGN"
	// $data['TAG_ORDER'] = "ORDER"
	// $data['TAG_PAYMENT'] = "PAYMENT"
	// $data['TAG_RESULTS'] = "RESULTS"
	// $data['CHECKRESULT'] = "[SIGN_GOOD]"
$self = $_SERVER['PHP_SELF'];
$path1 = '../paysys/config.txt';
$result = 0;

 include('../../mysql_connect.php');
    $q=mysql_query("SELECT * FROM dbisoftik.payconfirm");
    $row=mysql_fetch_assoc($q);
    
$_POST['response']=$row['data'];


if(isset($_POST["response"])){$response = $_POST["response"];};
$result = process_response(stripslashes($response),$path1);
//foreach ($result as $key => $value) {echo $key." = ".$value."<br>";};
if (is_array($result)){
	if (in_array("ERROR",$result)){
		if ($result["ERROR_TYPE"]=="ERROR"){
			echo "System error:".$result["ERROR"];
		} elseif ($result["ERROR_TYPE"]=="system"){
			echo "Bank system error > Code: '".$result["ERROR_CODE"]."' Text: '".$result["ERROR_CHARDATA"]."' Time: '".$result["ERROR_TIME"]."' Order_ID: '".$result["RESPONSE_ORDER_ID"]."'";
		}elseif ($result["ERROR_TYPE"]=="auth"){
			echo "Bank system user autentication error > Code: '".$result["ERROR_CODE"]."' Text: '".$result["ERROR_CHARDATA"]."' Time: '".$result["ERROR_TIME"]."' Order_ID: '".$result["RESPONSE_ORDER_ID"]."'";
		};
	};
	if (in_array("DOCUMENT",$result)){
		echo "Result DATA: <BR>";
		foreach ($result as $key => $value) {echo "Postlink Result: ".$key." = ".$value."<br>";};
	};
} else { echo "System error".$result; };
?>
