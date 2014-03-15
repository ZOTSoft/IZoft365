<?php 
if(isset($_POST["response"])){
    require_once("pay/paysys/kkb.utils.php");

$self = $_SERVER['PHP_SELF'];
$path1 = 'pay/paysys/config.txt';
$result = 0;

 include('mysql_connect.php');
    $response = $_POST["response"];
mysql_query("INSERT into dbisoftik.payconfirm SET data='".addslashes($response)."'");


$result = process_response(stripslashes($response),$path1);

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
        
        if($result['CHECKRESULT']=='[SIGN_GOOD]'){
            
            $description='Оплата через Epay.kkb.';
            $paytype=2;
            $invoice_id=intval($result['ORDER_ORDER_ID']);
            
            $check=mysql_query("SELECT id,usertype FROM `dbisoftik`.`z_invoice` WHERE id='".$invoice_id."' AND status=0");
            if (mysql_numrows($check)){
                    $r=mysql_fetch_assoc($check);
                    
             mysql_query("UPDATE `dbisoftik`.`z_invoice` SET status=1,paydescription='".$description."',paydate=NOW(),paytype='".$paytype."' WHERE id='".$invoice_id."'");
                    if ($r['usertype']){
                        
                        
                        $q=mysql_query("SELECT sa.percent,sa.id,zi.amount,zi.acid,sa.db FROM `dbisoftik`.`z_invoice` as zi 
                        LEFT JOIN `dbisoftik`.`s_partner` AS sa ON sa.id=zi.acid 
                        WHERE zi.id='".$invoice_id."'");
                        $a=mysql_fetch_assoc($q);
                        
                        $amount=($a['amount']/(100-$a['percent'])*100);
                        
                        mysql_query("UPDATE `dbisoftik`.`s_partner` SET `balance`=`balance`+".intval($amount)." WHERE id='".intval($a['acid'])."'");
                        mysql_query("INSERT into `dbisoftik`.`s_transaction` SET `name`='Оплата по счету №P".str_pad($invoice_id, 10, '0', STR_PAD_LEFT).' в размере '.$a['amount']." была принята( Процент партнера ".intval($a['percent'])."). Начислено ',`amount`='".intval($amount)."',pid='".$a['id']."'");
                    }else{
                        $q=mysql_query("SELECT zi.amount,zi.acid,sa.db FROM `dbisoftik`.`z_invoice` as zi 
                            LEFT JOIN `dbisoftik`.`s_accounts` AS sa ON sa.id=zi.acid 
                            WHERE zi.id='".$invoice_id."'");
                        $a=mysql_fetch_assoc($q);
                            
                        mysql_query("UPDATE `dbisoftik`.`s_accounts` SET `balance`=`balance`+".intval($a['amount'])." WHERE id='".intval($a['acid'])."'");
                            
                        mysql_query("INSERT into `".$a['db']."`.`s_transaction` SET `name`='Оплата по счету №P".str_pad($invoice_id, 10, '0', STR_PAD_LEFT)." была принята',`amount`='".intval($a['amount'])."'");
                    }
                    
            }
                    
                    
        }

	};
} else { echo "System error".$result; };
}
?>0
