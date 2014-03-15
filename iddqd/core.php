<?PHP  header("Content-Type: text/html; charset=utf-8");
session_start();
error_reporting(E_ALL ^ E_NOTICE);


 
 if ($_SERVER['PHP_AUTH_USER'] == 'admin' && $_SERVER['PHP_AUTH_PW'] == 'H82tahda0wdw' ) {                                      
    include('../company/mysql_connect.php');
    date_default_timezone_set('Asia/Almaty'); 
    mysql_query("SET `time_zone` = '".date('P')."'");
    
    $tab='';
    
    switch($_GET['do']){
        case 'setoffline':
            mysql_query("UPDATE ".addslashes($_GET['table'])." SET last_action=0,cookie_key='' WHERE id=".intval($_GET['id']));
            //echo "UPDATE ".addslashes($_GET['table'])." SET last_action=0 WHERE id=".intval($_GET['id']);
            $tab='';
            header('location: index.php'.$tab);
        break; 
        case 'cleardemoacc':
            $q=mysql_query("SELECT * FROM  `dbdemo`.`s_accounts`");
            while($row=mysql_fetch_assoc($q)){
                mysql_query("DROP DATABASE IF EXISTS `".$row['db']."`");
            }
            mysql_query("DELETE FROM `dbdemo`.`s_accounts`");
            //$tab='#demo';
            header('location: index.php'.$tab);    
        break;
        case 'addpartnerclient':
             mysql_query("INSERT into `dbisoftik`.`t_partner` SET `partnerid`='".intval($_POST['id'])."',`clientid`='".intval($_POST['client'])."'");
             $tab='#partner_settings';
             header('location: index.php'.$tab);    
        break;
        case 'delpartnerclient':
             mysql_query("DELETE FROM `dbisoftik`.`t_partner` WHERE `partnerid`='".intval($_GET['id'])."' AND `clientid`='".intval($_GET['client'])."'");
             $tab='#partner_settings';
             header('location: index.php'.$tab);    
        break;
        case 'addcompany':
            echo '<form method="post" action="core.php?do=addpartnerclient" id="addclient">
            Выберите клиента: 
            <input type="hidden" name="id" value="'.$_GET['id'].'">
            <select name="client">
            ';
            $q=mysql_query("SELECT sa.username,sa.id FROM `dbisoftik`.`s_accounts`  AS sa
            LEFT JOIN `dbisoftik`.`t_partner` AS tp  ON tp.clientid=sa.id
             WHERE  tp.id is NULL");
             while($row=mysql_fetch_assoc($q)){
                 echo '<option value="'.$row['id'].'"> '.$row['username'].' ('.$row['id'].')</option>';
             }
            echo '</select>
            <div style="display:none">
            <input type="submit" id="lalala"></div>
            </form>';
        break;   
        case 'addpay':
        
            if (isset($_POST['id'])){
                $check=mysql_query("SELECT id,usertype FROM `dbisoftik`.`z_invoice` WHERE id='".intval($_POST['id'])."' AND status=0");
                if (mysql_numrows($check)){
                    $r=mysql_fetch_assoc($check);
                    
                    mysql_query("UPDATE `dbisoftik`.`z_invoice` SET status=1,paydescription='".addslashes($_POST['description'])."',paydate=NOW(),paytype='".addslashes($_POST['type'])."' WHERE id='".intval($_POST['id'])."'");
                    if ($r['usertype']){
                        
                        $tab='#invoice_partner';
                        $q=mysql_query("SELECT sa.percent,sa.id,zi.amount,zi.acid,sa.db FROM `dbisoftik`.`z_invoice` as zi 
                        LEFT JOIN `dbisoftik`.`s_partner` AS sa ON sa.id=zi.acid 
                        WHERE zi.id='".intval($_POST['id'])."'");
                        $a=mysql_fetch_assoc($q);
                        
                        $amount=($a['amount']/(100-$a['percent'])*100);
                        
                        mysql_query("UPDATE `dbisoftik`.`s_partner` SET `balance`=`balance`+".intval($amount)." WHERE id='".intval($a['acid'])."'");
                        mysql_query("INSERT into `dbisoftik`.`s_transaction` SET `name`='Оплата по счету №ZOT".intval($_POST['id']).' в размере '.$a['amount']." была принята( Процент партнера ".intval($a['percent'])."). Начислено ',`amount`='".intval($amount)."',pid='".$a['id']."'");
                    }else{
                        $tab='#invoice';
                        $q=mysql_query("SELECT zi.amount,zi.acid,sa.db FROM `dbisoftik`.`z_invoice` as zi 
                            LEFT JOIN `dbisoftik`.`s_accounts` AS sa ON sa.id=zi.acid 
                            WHERE zi.id='".intval($_POST['id'])."'");
                        $a=mysql_fetch_assoc($q);
                            
                        mysql_query("UPDATE `dbisoftik`.`s_accounts` SET `balance`=`balance`+".intval($a['amount'])." WHERE id='".intval($a['acid'])."'");
                            
                        mysql_query("INSERT into `".$a['db']."`.`s_transaction` SET `name`='Оплата по счету №ZOT".intval($_POST['id'])." была принята',`amount`='".intval($a['amount'])."'");
                    }
                    
                }
                
            }
            header('location: index.php'.$tab);
        break;
    }
    
        
 }
    
?>