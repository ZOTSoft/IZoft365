<?PHP  
header("Content-Type: text/html; charset=utf-8");
session_start();

include('../company/mysql_connect.php');
include('partner_check.php');
include('../company/core.php');

partnerchecksessionpassword();

include('../company/functions.php');
include('../company/templates.php');
if (!(isset($_SESSION['partner']) )){
    header("Location: /partner/login.php");
    die;
}

    switch($_GET['do']){
        case 'get_window_feedback':
            echo $template['get_window_feedback'];
        break;
         case 'save_partner_settings':
            $pass='';
            if ($_POST['password']!=''){
                $pass=',`password`="'.md5(FISH.md5($_POST['password'])).'" ';
            }
            mysql_query("UPDATE  `dbisoftik`.`s_partner` SET `fio`='".addslashes($_POST['fio'])."',`phone`='".addslashes($_POST['phone'])."',`email`='".addslashes($_POST['email'])."'".$pass." WHERE id='".$_SESSION['puserid']."'");
        break;
        case 'get_window_balance':
            $query=mysql_query("SELECT `fio`,`phone`,`email`,`balance`,`username` FROM `dbisoftik`.`s_partner` WHERE id='".$_SESSION['puserid']."' LIMIT 1");
            $row=mysql_fetch_array($query); 

            
            echo '<div class="account_settings"><form action="ajax.php?do=save_partner_settings" method="post" id="show_account_settings">
            <table>
                <tr><td class="settingstext">ФИО</td><td><input value="'.$row['fio'].'" class="settingsinput" name="fio"></td></tr>
                <tr><td class="settingstext">Телефон</td><td><input value="'.$row['phone'].'" class="settingsinput" name="phone"></td></tr>
                <tr><td class="settingstext">Емейл</td><td><input value="'.$row['email'].'" class="settingsinput" name="email"> </td></tr>
                <tr><td class="settingstext">Пароль</td><td><input value="" type="password" name="password" class="settingsinput"></td></tr>
            </table>
            <br />
            <a href="#" class="btn btn-primary"  onclick="save_account_settings()">Сохранить</a>
            </form>';
            
            
            echo '
            <br /><br />Аккаунт <b>'.$row['username'].'</b> (ID '.$_SESSION['puserid'].')<br />';
                            echo 'Ваш баланс: '.$row['balance'].'.  <a href="#" onclick="topup();return false;">Пополнить баланс</a>';
             echo '
            
                            
                            
<ul class="nav nav-tabs" id="sett_tabs">
  <li class="active"><a href="#settings_hystory" data-toggle="tab">История транзакций</a></li>
  <li><a href="#settings_invoice" data-toggle="tab">Счета</a></li>
</ul>

<div class="tab-content" id="sett_content">
          <div class="tab-pane fade in active" id="settings_hystory" style="background:#f4f4f4;padding:20px">
          ';
                    
                    $query=mysql_query("SELECT tr.date,tr.name,tr.amount,ac.username FROM `dbisoftik`.`s_transaction` as tr
                                LEFT JOIN `dbisoftik`.`s_accounts` AS ac ON ac.id=tr.clientid
                    WHERE tr.pid='".$_SESSION['puserid']."' ORDER by tr.id DESC");
                    if (mysql_numrows($query)){
                        echo '<ul type="square">';
                        while($row=mysql_fetch_assoc($query)){
                            echo '<li><b>'.$row['date'].'</b> <b>'.$row['username'].'</b> -'.$row['name'].' <b>'.$row['amount'].'</b></li>';
                        }
                        echo '</ul>';
                    }else{
                        echo 'Пусто.';
                    }
                    echo '
          </div>
          <div class="tab-pane" id="settings_invoice" style="background:#f4f4f4;padding:20px">';
          $query=mysql_query("SELECT * FROM `dbisoftik`.`z_invoice` 
          WHERE acid='".$_SESSION['puserid']."' AND usertype=1 ORDER by id DESC");
          if (mysql_numrows($query)){
              echo '<table class="melkiynubas invoices_here">
                            <tr>
                                <td>#</td>
                                <td>Дата</td>
                                <td>Сумма</td>
                                <td>Описание</td>
                                <td>Статус</td>
                            </tr>
                        ';
                        
                        
                        while($row=mysql_fetch_assoc($query)){
                            echo '<tr>
                            <td><a href="#" onclick="show_invoice(\''.$row['id'].'\'); return false;"><b>P'.str_pad($row['id'], 10, '0', STR_PAD_LEFT).'</b></a></td>
                                <td>'.date('d.m.Y H:i:s',strtotime($row['date'])).'</td> 
                                <td>'.str_replace(' ',"&nbsp;",number_format($row['amount'],2,'.',' ')).'</td> 
                                <td>'.$row['description'].'</td> 
                                <td>'.($row['status']?'<i class="glyphicon glyphicon-ok-circle" style="color:green"></i>':'<i class="glyphicon glyphicon-remove-circle" style="color:red"></i>').'</td> 
                            </tr>';
                        }
                        echo '</table>';
          }else{
              echo 'Пусто.';
          }
          echo '
          </div>
        </div>
        </div>'; 
            
        break; 
        case 'create_invoice':
            $tg=intval($_GET['tg']);
            $query=mysql_query("INSERT into `dbisoftik`.`z_invoice` SET `amount`='".$tg."',`description`='Предоплата за услуги автоматизации от Партнера',usertype=1,`acid`='".($_SESSION['puserid'])."' ");
            $_GET['id']=mysql_insert_id();
            $_GET['do']='show_invoice';
            $_GET['zrow']='<tr>
                            <td><a href="#" onclick="show_invoice(\''.$_GET['id'].'\'); return false;"><b>P'.str_pad($_GET['id'], 10, '0', STR_PAD_LEFT).'</b></a></td>
                            <td>'.date('d.m.Y H:i:s').'</td> 
                            <td>'.number_format($tg,2,'.',' ').'</td> 
                            <td>Предоплата за услуги автоматизации от Партнера</td> 
                            <td><i class="glyphicon glyphicon-remove-circle" style="color:red"></i></td> 
                        </tr>';

        case 'show_invoice':
        if (isset($_GET['id'])){
            
            $month=array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
            
            
            $id=intval($_GET['id']);
        
            
            $query=mysql_query("SELECT * FROM `dbisoftik`.`z_invoice` WHERE id='".$id."' LIMIT 1");
            $row=mysql_fetch_assoc($query);
            
            $amountnf=$row['amount'];
            $amount=number_format($amountnf,2,'.',' ');
            $amount_text=num2str($amountnf);
            $invoice_num='P'.str_pad($row['id'], 10, '0', STR_PAD_LEFT);
            
            $m=$month[(date('n',strtotime($row['date']))-1)];
            $date=date('j '.$m.' Y',strtotime($row['date']));
            
            $message= <<<HTML
            <body class="thiswindow">
            <style>
            .thiswindow *{font:12px Arial;vertical-align:top;}
            .thiswindow b{font-weight:bold;}
            .coolblacktable{border-spacing:0;border-collapse:collapse;}
            .coolblacktable td{border:1px solid #000;}
            .coolestblacktr td{font-weight:bold; text-align:center;}
            .worldwidewhitepride td{border:none;font-weight:bold;}
            .coolblacktable :nth-child(3),.coolblacktable :nth-child(5),.coolblacktable :nth-child(6){text-align:right;}
            </style>
            <table width="900" style="font:12px Arial;">
                <tr>
                    <td style="text-align: center;padding: 7px 0 0 212px;font: 12px Arial;"> Внимание! Оплата данного счета означает согласие с условиями поставки товара. Уведомление об оплате<br />
     обязательно, в противном случае не гарантируется наличие товара на складе. Товар отпускается по факту<br />  прихода денег на р/с Поставщика, самовывозом, при наличии доверенности и документов удостоверяющих<br /> личность.</td>
                </tr>
                <tr>
                    <td style="padding:30px 0 0 0">
                        <span style="font:bold 15px Arial;">Образец платежного поручения</span>
                        <table style="width:100%; border:1px solid #000;border-collapse: collapse;border-spacing:0">
                            <tr>
                                <td style="border-right:1px solid #000;"><b>Бенефициар:</b></td>
                                <td style="border-right:1px solid #000; text-align:center;"><b>ИИК</b></td>
                                <td style="text-align:center;" ><b>Кбе</b></td>
                            </tr>
                            <tr>
                                <td style="border-right:1px solid #000;"><b>Товарищество с ограниченной ответственностью  "Paloma service"</b></td>
                                <td style="border-right:1px solid #000; text-align:center;vertical-align:middle;border-bottom:1px solid #000;" rowspan="2"><b>KZ598560000003951196</b></td>
                                <td style="text-align:center; vertical-align:middle;border-bottom:1px solid #000;" rowspan="2"><b>17</b></td>
                            </tr>
                            <tr>
                                <td style="border-right:1px solid #000;border-bottom:1px solid #000;">БИН: 100740000739</td>
                                
                            </tr>
                            <tr>
                                <td style="border-right:1px solid #000;">Банк бенефициара:</td>
                                <td style="border-right:1px solid #000; text-align:center;"><b>БИК</b></td>
                                <td style="text-align:center;"><b>Код назначения платежа</b></td>
                            </tr>
                            <tr>
                                <td style="border-right:1px solid #000;">АО "Банк ЦентрКредит"</td>
                                <td style="border-right:1px solid #000; text-align:center;"><b>KCJBKZKX</b></td>
                                <td> </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="border-bottom:1px solid #000; font:bold 18px Arial; padding:20px 0 10px 0 ">Счет на оплату № {$invoice_num} от {$date} г.</td>
                </tr>
                 <tr>
                    <td>
                        <table width="100%">
                            <tr>
                                <td>Поставщик:</td>
                                <td style="padding-bottom:10px; font-weight:bold;">БИН / ИИН 100740000739,Товарищество с ограниченной ответственностью  "Paloma

service",Республика Казахстан, Алматы, Чайковского, дом № 22, к.202, тел.: +7-702-111-9723, 327-

01-74</td>
                            </tr>
                            <tr>
                                <td>Покупатель:</td>
                                <td style="padding-bottom:10px; font-weight:bold;">Частное лицо</td>
                            </tr><tr>
                                <td>Договор:</td>
                                <td style="padding-bottom:10px; font-weight:bold;">Без договора</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="100%" class="coolblacktable">
                            <tr class="coolestblacktr">
                                <td>#</td>
                                <td>Наименование</td>
                                <td>Кол-во</td>
                                <td>Ед.</td>
                                <td>Цена</td>
                                <td>Сумма</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Предоплата за услуги автоматизации от Партнера</td>
                                <td>1.000</td>
                                <td>шт</td>
                                <td>{$amount}</td>
                                <td>{$amount}</td>
                            </tr>
                            <tr class="worldwidewhitepride">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Итого:</td>
                                <td>{$amount}</td>
                            </tr> 
                            <tr class="worldwidewhitepride">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Без налога (НДС)</td>
                                <td>-</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>Всего наименований 1, на сумму {$amount} KZT</td>
                </tr>
                <tr>
                    <td style="font-weight:bold; border-bottom:1px solid #000;">Всего к оплате: {$amount_text} </td>
                </tr>
                <tr>
                    <td style="padding:17px 0 0 0">
                        <table width="100%">
                            <td style="font-weight:bold; width:100px">Исполнитель</td>
                            <td style="border-bottom:1px solid #000; width:300px" ></td>
                            <td>/Бухгалтер   /</td>
                        </table>
                    </td>
                </tr>
                
            </table>
            
            </body>
            
HTML;

    if (isset($_GET['type'])&&($_GET['type']=='print')){
        
    echo '<html>
<head>
    <meta charset="UTF-8">
    <title>Печать</title>
    <script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
<script>$(document).ready(function() {
window.print();
setTimeout(function() {

        window.close();                      
   
}, 200); 
    });</script>
</head>
<body>';
     echo $message;
     echo '</body></html>';
    }else{
        $res=array();
        $res['message']=$message;
        $res['id']=$row['id'];
        if (isset($_GET['zrow']))
            $res['zrows']=$_GET['zrow'];
            
        echo json_encode($res);
        }
    }  
        break;
        case 'pay':
            if (isset($_SESSION['puserid'])){
           
                if (isset($_GET['type'])&&isset($_GET['amount'])&&isset($_GET['id'])){
                    
                    if ($_GET['type']=='Торговая точка'){
                        $type='s_automated_point';
                    }else{
                        $type='t_workplace';
                    }
                    $query=mysql_query("SELECT `balance` FROM `dbisoftik`.`s_partner` WHERE id='".$_SESSION['puserid']."' LIMIT 1");
                    
                    $s_account=mysql_query("SELECT `db` FROM `dbisoftik`.`s_accounts` WHERE id='".addslashes($_GET['clientid'])."' LIMIT 1");
                    $r_account=mysql_fetch_assoc($s_account);
                    $base=$r_account['db'];
           
                    $b=mysql_fetch_assoc($query);
                    
                    $balance=intval($b['balance']);
                    $amount=intval($_GET['amount']);                                        
                                        
                                        
                    if ($balance>=$amount){
                        
                        $qw=mysql_query("SELECT `id`,`name`,`status`,`expiration_date` FROM `".$base."`.`".$type."` WHERE id='".addslashes($_GET['id'])."'");
                        $row=mysql_fetch_assoc($qw);
                        
                        mysql_query("UPDATE `".$base."`.`".$type."` SET status=1, expiration_date=(IF (expiration_date is null, DATE_ADD(NOW(), INTERVAL 1 MONTH), DATE_ADD(expiration_date, INTERVAL 1 MONTH))) WHERE id='".addslashes($_GET['id'])."'");
                        
                        set_partner_balance($_SESSION['puserid'],(abs($amount)*-1),'Продление '.$_GET['type'].' '.$row['name'].'('.$row['id'].')',addslashes($_GET['clientid']));    
                        
                        $qw=mysql_query("SELECT `expiration_date` FROM `".$base."`.`".$type."` WHERE id='".addslashes($_GET['id'])."'",$db);
                        $row=mysql_fetch_assoc($qw);      
                        
                        $remain = strtotime($row['expiration_date']) - time();
                        $days = floor($remain/86400);
                        if($days > 0) $str=' (осталось '.$days.' '.morph($days,'день','дня','дней').')';
                                
                                
                        echo json_encode(array('result'=>'ok','message'=>'Оплата успешно произведена','date'=>date('d.m.Y',strtotime($row['expiration_date'])).$str));
                    }else{
                        echo json_encode(array('result'=>'error','message'=>'Недостаточно средств'));        
                    }
                }
                
            }
        break;
    }
?>
