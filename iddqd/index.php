<?php
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    die('Access denied');
} else {
    if ($_SERVER['PHP_AUTH_USER'] == 'admin' && $_SERVER['PHP_AUTH_PW'] == 'H82tahda0wdw' ) { 
        include('../company/mysql_connect.php');
               
        header("Content-Type: text/html; charset=utf-8");
        session_start();
        
function morph($n, $f1, $f2, $f5) {
    $n = abs(intval($n)) % 100;
    if ($n>10 && $n<20) return $f5;
    $n = $n % 10;
    if ($n>1 && $n<5) return $f2;
    if ($n==1) return $f1;
    return $f5;
}

date_default_timezone_set('Asia/Almaty'); 
    mysql_query("SET `time_zone` = '".date('P')."'"); 
?><!DOCTYPE html>
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <title>Режим Бога</title>
    <link rel="stylesheet" type="text/css" href="/company/css/print.css" media="print" />
    <link rel="stylesheet" type="text/css" href="/company/css/my.css">
    <link rel="stylesheet" type="text/css" href="/company/mtree/css_tree/css_tree.css">
    <link rel="stylesheet" type="text/css" href="/company/mtree/css_tree/tablecss.css">
    <script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/company/mtree/mytreeview.js"></script>
    <script type="text/javascript" src="/company/js/jquery.form.min.js"></script>
    <script type="text/javascript" src="/iddqd/iddqd.js?12321"></script>
    <link rel="stylesheet" type="text/css" href="/company/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/company/bootstrap/css/bootstrap-datetimepicker.min.css">
    <script type="text/javascript" src="/company/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="/company/bootstrap/js/bootstrap-inputmask.min.js"></script>
    <script type="text/javascript" src="/company/js/bootbox.min.js"></script>
    <script type="text/javascript" src="/company/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
        function acception(id,desc){
            data='<form method="post" action="core.php?do=addpay" id="frm1">'+desc;
            
            data+='<input type="hidden" name="id" value="'+id+'"><h4>Описание</h4><textarea name="description" class="form-control"></textarea>';
            data+='<br /><select name="type" class="form-control"><?
                 $q2=mysql_query("SELECT * FROM `dbisoftik`.`s_paytype`");   
                 while($row=mysql_fetch_assoc($q2)){
                     echo '<option value="'.$row['id'].'">'.$row['type'].'</option>';
                 }
            ?></select></form>';
            bootbox.dialog({
              message: data,
              title: 'Подтверждение оплаты',
              buttons: {
                pay: {
                  label: "Подтвердить",
                  className: "btn-success",
                  callback: function() {
                    $("#frm1").submit();
                  }
                }
              }
            });
        }
    
    </script>
</head>
<body>
<div class="bb-alert alert alert-info" style="display:none;">
        <span></span>
    </div>
<div class="bb-alert alert alert-danger" style="display:none;">
        <span></span>
    </div>
    
<div class="container-loader" id="loading">
  <div class="round">
  </div>
      <div class='l'>
      Загрузка...
      </div>
</div>
<table class="tab">
    <tr>
        <td class="tab_header" colspan="2" style="background: #BA0305;">
           <!-- <ul class="menu">
                <li onclick="idd_showtab('activity')"><a href="#">Активность</a></li>
                <li onclick="idd_showtab('invoice')" class="tab_desctop"><a href="#">Счета клиентов</a></li>
                <li onclick="idd_showtab('invoice_partner')"><a href="javascript:void(0)">Счета от партнеров</a></li>
                <li onclick="idd_showtab('feedback')"><a href="javascript:void(0)">Обратная связь</a></li>                  
            </ul>-->
        </td>
    </tr>
    <tr>
        <td class="lefttd" style="display: none;">
            <div class="td_logo"><img src="/company/i/logo.png" alt=""></div>
            <div class="td_links">

                <div class="s_links" id="tab_clients" style="display: none;">
                   
                   <a href="#" onclick="show_tree('s_employee')">Сотрудники</a>
                </div>
            </div>
        </td>
        <td class="righttd">
        <ul class="nav nav-tabs nav-tabs-success" id="zottabs">
          <li class="active"><a href="#activity" data-toggle="tab">Активность</a></li>
          <li><a href="#automated_point" data-toggle="tab">Клиенты</a></li>
          <li><a href="#invoice" data-toggle="tab">Счета клиентов</a></li>
          <li><a href="#partner_settings" data-toggle="tab">Управление партнерами</a></li>
          <li><a href="#invoice_partner" data-toggle="tab">Счета от партнеров</a></li>
          <li><a href="#feedback" data-toggle="tab">Обратная связь</a></li>
          <li><a href="#demo" data-toggle="tab">Демо аккаунты</a></li>
        </ul>
        <div class="tab-content righttd-content" id="zotcontent"  style="height: 92%;">
            
            <div class="tab-pane fade in active" id="activity">
                <div class="toolbar" style="padding: 9px 0 9px 11px;">
                    <h4>Активность</h4>
                </div>     
<?
      $time=time()-(10*60);
    $q=mysql_query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME LIKE 'db\_%'");
echo '<table class="table table-striped table-hover table-bordered" style="width:900px"><tr>
    <th>#</th>
    <th>База</th>
    <th>Логин</th>
    <th>Последнее действие</th> 
    <th>Последний счет</th> 
    <th></th>
    </tr>';
$i=1;
    while($row=mysql_fetch_array($q)){
        $bases[]=$row['SCHEMA_NAME'];//array('Пользователь'=>'`'.$row['SCHEMA_NAME'].'`.`s_employee`','Торговая точка'=>'`'.$row['SCHEMA_NAME'].'`.`s_automated_point`','Рабочее место'=>'`'.$row['SCHEMA_NAME'].'`.`t_workplace`');
    }
     echo '<tr><td colspan="6" style="background:#7FADDF"><i class="glyphicon glyphicon-user"></i> Пользователи</td></tr>';
     $i=1;
  foreach($bases as $k=>$base){
     $qw=mysql_query("SELECT * FROM ".$base.".s_employee WHERE last_action>".$time.' ORDER by last_action DESC'); 
     
    
     
     while($row2=mysql_fetch_array($qw)){     
        echo '<tr><td>'.$i.'</td>
            <td>'.$base.'</td>
            <td>'.$row2['name'].'</td>
            <td>'.date('H:i:s',$row2['last_action']).'('.round((time()-$row2['last_action'])/60).'мин. назад)</td>
            <td>'.(isset($row2['creationdt'])?date('d.m.Y H:i:s',strtotime($row2['creationdt'])).'('.round((time()-strtotime($row2['creationdt']))/60).'мин. назад)':'').'</td>
            <td><a href="core.php?do=setoffline&table='.$base.'.s_employee'.'&id='.$row2['id'].'">Выйти</a></td>
            </tr>';
        $i++;
     }
     
  }
  echo '<tr><td colspan="6" style="background:#F19253"><i class="glyphicon glyphicon-home"></i> Рабочие места</td></tr>';
  $i=1;  
  foreach($bases as $base){
         $qw=mysql_query("SELECT *,creationdt FROM ".$base.".t_workplace as tw LEFT JOIN (SELECT wpid,max(creationdt) as creationdt FROM `".$base."`.d_order GROUP by wpid)  AS do ON do.wpid=tw.id WHERE last_action>".$time.' ORDER by last_action DESC');
      
        while($row2=mysql_fetch_array($qw)){     
        echo '<tr><td>'.$i.'</td>
            <td>'.$base.'</td>
            <td>'.$row2['name'].'</td>
            <td>'.date('H:i:s',$row2['last_action']).'('.round((time()-$row2['last_action'])/60).'мин. назад)</td>
            <td>'.(isset($row2['creationdt'])?date('d.m.Y H:i:s',strtotime($row2['creationdt'])).'('.round((time()-strtotime($row2['creationdt']))/60).'мин. назад)':'').'</td>
            <td><a href="core.php?do=setoffline&table='.$base.'t_workplace'.'&id='.$row2['id'].'">Выйти</a></td>
            </tr>';
        $i++;
     }
  }

    echo '<tr><td colspan="6" style="background:#7DCF80"><i class="glyphicon glyphicon-tower"></i> Аккаунты</td></tr>';
     $qw2=mysql_query("SELECT * FROM `dbisoftik`.`s_accounts` WHERE last_action>".$time.' ORDER by last_action DESC'); 
     while($row3=mysql_fetch_array($qw2)){     
        echo '<tr>
        <td>'.$i.'</td>
        <td>'.$row3['db'].'</td>
        <td>Аккаунт</td>
        <td>'.date('H:i:s',$row3['last_action']).'('.round((time()-$row3['last_action'])/60).'мин. назад)</td>
        <td></td>
        <td><a href="core.php?do=setoffline&table=`dbisoftik`.`s_accounts`&id='.$row3['id'].'">Выйти</a></td>
        </tr>';
        $i++;
     }

echo '</table>';
?>
            </div>
            <div class="tab-pane" id="invoice">
                <div class="toolbar" style="padding: 9px 0 9px 11px;">
                    <h4>Счета клиентов</h4>
                </div>
                
                <?
$q=mysql_query("SELECT zi.id,zi.date,sa.username,zi.amount,zi.description  FROM `dbisoftik`.`z_invoice` AS zi
                LEFT JOIN `dbisoftik`.`s_accounts` AS sa ON sa.id=zi.acid
 WHERE zi.status=0 AND usertype=0 ORDER by zi.id DESC");
 
$q2=mysql_query("SELECT zi.id,zi.date,sa.username,zi.amount,zi.description,zi.paydate,zi.paydescription,pt.type  FROM `dbisoftik`.`z_invoice` AS zi
                LEFT JOIN `dbisoftik`.`s_accounts` AS sa ON sa.id=zi.acid
                LEFT JOIN `dbisoftik`.`s_paytype` AS pt ON pt.id=zi.paytype
 WHERE zi.status=1 AND usertype=0 ORDER by zi.paydate DESC");

echo '<table width="100%">
    <tr>
        <td width="50%">
<h3>Неоплаченные счета <a href="#" onclick="location.reload(); return false;"><i class="glyphicon glyphicon-repeat" style="color:green; font-size:20px"></i></a>   </h3>     
        
<table class="table table-striped">
    <tr>
        <td># Счета</td>
        <td>Дата</td>
        <td>Аккаунт</td>
        <td>Сумма</td>
        <td>Описание</td>
        <td>Действие</td>
    </tr>
';
while($row=mysql_fetch_assoc($q)){
    echo '<tr>
        <td>P'.str_pad($row['id'], 10, '0', STR_PAD_LEFT).'</td>
        <td>'.date('d.m.Y H:i:s',strtotime($row['date'])).'</td>
        <td>'.$row['username'].'</td>
        <td>'.$row['amount'].'</td>
        <td>'.$row['description'].'</td>
        <td><a href="#" onclick="acception(\''.$row['id'].'\',\'P'.str_pad($row['id'], 10, '0', STR_PAD_LEFT).'<br />'.date('d.m.Y H:i:s',strtotime($row['date'])).'<br />'.$row['username'].'<br />'.$row['amount'].'<br />'.$row['description'].'\')"><i class="glyphicon glyphicon-usd"></i> Подтвердить</a> </td>
    </tr>';     
}
echo '</table></td>
   <td>
   <h3>Оплаченные счета</h3>
   <table class="table table-striped">
    <tr>
        <td># Счета</td>
        <td>Дата</td>
        <td>Аккаунт</td>
        <td>Сумма</td>
        <td>Описание</td>
        <td>Тип оплаты</td>
        <td>Дата оплаты</td>
        <td>Описание оплаты</td>
    </tr>
';
while($row=mysql_fetch_assoc($q2)){
    echo '<tr>
        <td>P'.str_pad($row['id'], 10, '0', STR_PAD_LEFT).'</td>
        <td>'.date('d.m.Y H:i:s',strtotime($row['date'])).'</td>
        <td>'.$row['username'].'</td>
        <td>'.$row['amount'].'</td>
        <td>'.$row['description'].'</td>
        <td>'.$row['type'].'</td>
        <td>'.date('d.m.Y H:i:s',strtotime($row['paydate'])).'</td>
        <td>'.$row['paydescription'].'</td>
    </tr>';     
}

echo'</table>
   </td>
    </tr>
</table>';    
?>
            </div>
            <div class="tab-pane" id="automated_point">
                      <? $query_client=mysql_query("SELECT  sa.username,
                                                            sa.phone, 
                                                            sa.email, 
                                                            sa.db,
                                                            sa.id,
                                                            sp.username as partner,
                                                            sp.id as partnerid
                                                            FROM `dbisoftik`.s_accounts as sa 
                                                            LEFT JOIN `dbisoftik`.t_partner AS tp ON tp.clientid=sa.id
                                                            LEFT JOIN `dbisoftik`.s_partner as sp ON sp.id=tp.partnerid");
                                         
                                         
                  $query_p=mysql_query("SELECT * FROM `dbisoftik`.`prices`");
            $prices=array();
            while($rowp=mysql_fetch_assoc($query_p)){
                $prices[$rowp['type']]=$rowp['price'];
            }
                  echo '<table width="100%" class="table">
                            <tr>
                                <th  width="50">#</th>
                                <th width="200">Компания</th>
                                <th width="200">Телефон</th>
                                <th width="200">Емейл</th>
                                <th>Партнер</th>
                            </tr>
                  ';
                  $i=1;
                   while($client=mysql_fetch_assoc($query_client)){
                        echo '<tr class="active"><td>'.$i.'</td>
                                <td><a href="#" onclick="showclient(this);return false" style="border-bottom:1px dashed #428bca">'.$client['username'].'</a></td>
                                <td>'.$client['phone'].'</td>
                                <td>'.$client['email'].'</td>
                                <td>'.(isset($client['partner'])?$client['partner'].' (ID '.$client['partnerid'].')':'').'</td>
                              </tr>
                              <tr style="display:none" class="showclient success"><td colspan="5">
                              ';
                        $i++;
                        
                     
$base=$client['db'];
$acid=$client['id'];



echo '          
          <p class="tranz">';
                            
                            echo '<table class="melkiynubas">
                                <tr>
                                    <td>Объект</td>
                                    <td>Статус</td>
                                    <td>Окончание</td>
                                    <td>Цена/мес.</td>
                                </tr>';
                            $itogo=0;
                            $query=mysql_query("SELECT * FROM `".$base."`.`s_automated_point`");
                            while($row=mysql_fetch_assoc($query)){
                                $query2=mysql_query("SELECT * FROM `".$base."`.`t_workplace` WHERE apid='".$row['id']."'");
                                $price=$prices['automated_point'];
                                $itogo+=$price;
                                if ($row['expiration_date']!=''){
                                    $exp_date=date('d.m.Y',strtotime('+1 month',strtotime($row['expiration_date'])));
                                }else{
                                    $exp_date=date('d.m.Y',strtotime('+1 month',time()));
                                }
                                
                                $remain = strtotime($row['expiration_date']) - time();
                                $days = floor($remain/86400);
                                $str='';
                                if($days > 0) $str=' (осталось '.$days.' '.morph($days,'день','дня','дней').')';
                                
                                //$exp_date=date('d.m.Y',strtotime('+1 month',strtotime($row['expirate_date'])));
                                echo '<tr id="pay'.$row['id'].'_'.$client['id'].'"><td><i class="glyphicon glyphicon-home" style="color:#FF740A"></i> '.$row['name']. '</td><td>'.($row['status']==1?'<i class="glyphicon glyphicon-ok-circle" style="color:green"></i>':'<i class="glyphicon glyphicon-remove-circle" style="color:red"></i>').'</td><td><span class="expdate">'.($row['expiration_date']!=''?date('d.m.Y',strtotime($row['expiration_date'])):'-').$str.'</span></td><td>'.$price.'</a>
                                
                                        
                                       </td></tr>';
                                if (mysql_num_rows($query2)){
                                    while($row2=mysql_fetch_assoc($query2)){ 
                                        
                                        $price=$prices['workplace'];
                                        $itogo+=$prices['workplace'];
                                        if ($row2['expiration_date']!=''){
                                            $exp_date=date('d.m.Y',strtotime('+1 month',strtotime($row2['expiration_date'])));
                                        }else{
                                            $exp_date=date('d.m.Y',strtotime('+1 month',time()));
                                        }
                                        
                                        $remain = strtotime($row2['expiration_date']) - time();
                                        $days = floor($remain/86400);
                                        $str='';
                                        if($days > 0) $str=' (осталось '.$days.' '.morph($days,'день','дня','дней').')';
                                        
                                        $exp_date=$row['expiration_date'];
                                        echo '<tr id="zpay'.$row2['id'].'_'.$client['id'].'"><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="glyphicon glyphicon-user" style="color:#617C9C"></i> '.$row2['name']. '</td><td>'.($row2['status']==1?'<i class="glyphicon glyphicon-ok-circle" style="color:green"></i>':'<i class="glyphicon glyphicon-remove-circle" style="color:red"></i>').'</td><td><span class="expdate">'.($row2['expiration_date']!=''?date('d.m.Y',strtotime($row2['expiration_date'])):'-').$str.'</span></td><td>'.$price.'</a></td>
                                        
                                        
                                       </tr>';                              
                                    }                           
                                }                    
                            }
                            //<a href="#"><i class="glyphicon glyphicon-off" style="color:green"></i></a> <a href="#" onclick="payall()">Оплатить всё</a>
                            echo '<tr><td colspan="3"><b>Итого к оплате:</b></td><td><b>'.$itogo.'</b></td><td></td></tr>';
                            
                            
                            echo '</table>

          
        </td></tr>';
                   }
        echo '</table>';
?>
            </div>
            <div class="tab-pane" id="partner_settings">
              <? $query_client=mysql_query("SELECT  * FROM `dbisoftik`.s_partner ");

                  echo '<table width="100%" class="table">
                            <tr>
                                <th  width="50">#</th>
                                <th width="200">Логин</th>
                                <th width="200">ФИО</th>
                                <th width="200">Телефон</th>
                                <th width="200">Емейл</th>
                                <th width="200">Баланс</th>
                                <th width="200">Процент</th>
                            </tr>';
                  $i=1;
                   while($partner=mysql_fetch_assoc($query_client)){
                        echo '<tr class="active"><td>'.$i.'</td>
                                <td><a href="#" onclick="showclient(this);return false" style="border-bottom:1px dashed #428bca">'.$partner['username'].'</a></td>
                                <td>'.$partner['fio'].'</td>
                                <td>'.$partner['phone'].'</td>
                                <td>'.$partner['email'].'</td>
                                <td>'.$partner['balance'].'</td>
                                <td>'.$partner['percent'].'</td>
                              </tr>
                              <tr style="display:none" class="showclient success"><td colspan="7">';
                              
                        $query=mysql_query("SELECT * FROM `dbisoftik`.`t_partner` AS tp
                                            LEFT JOIN `dbisoftik`.`s_accounts` AS sa ON sa.id=tp.clientid 
                                            
                                            WHERE tp.partnerid='".$partner['id']."'");
                        echo '<table class="melkiynubas" style="width: 430px;">
                            <tr><td>#</td><td>Наименование</td><td><a href="#" onclick="addcompany(\''.$partner['id'].'\')"><i class="glyphicon glyphicon-plus"></i></a></td></tr>
                        ';
                        $i=1;
                        while($row=mysql_fetch_assoc($query)){
                            echo '<tr><td>'.$i.'</td><td>'.$row['username'].'</td><td>
                                <a href="core.php?do=delpartnerclient&id='.$partner['id'].'&client='.$row['clientid'].'" onclick="return confirm(\'Вы действительно хотите удалить?\')"><i class="glyphicon glyphicon-minus"></i></a>
                            </td></tr>';
                            $i++;
                        }
                        echo '</table>';
                        echo '</td></tr>';
                        $i++;
                   }
              ?>
              </table>
            </div>
            <div class="tab-pane" id="demo">
                <div class="toolbar" style="padding: 9px 0 9px 11px;">
                    <h4>Демо аккаунты</h4>
                </div>
                <?
                    $q=mysql_query("SELECT * FROM dbdemo.s_accounts ORDER by last_action");
                    if ($c=mysql_numrows($q)){
                        $row=mysql_fetch_array($q);
                        $last=date('d.m.Y H:i:s',$row['last_action']);
                    }else{
                        $c=0;
                    }
                    
                
                ?>
                <b>Всего демо аккаунтов: </b> <?=$c?><br />
                <? if ($c>0){ ?><b>Последее дейтвие: </b><?=$last?> <? }?><br />
                <a href="core.php?do=cleardemoacc" class="btn btn-primary">Удалить демо аккаунты</a>
            </div>
            <div class="tab-pane" id="invoice_partner">
                <div class="toolbar" style="padding: 9px 0 9px 11px;">
                    <h4>Счета от партнеров</h4>
                </div>
                
                <?
$q=mysql_query("SELECT zi.id,zi.date,sa.username,zi.amount,zi.description  FROM `dbisoftik`.`z_invoice` AS zi
                LEFT JOIN `dbisoftik`.`s_partner` AS sa ON sa.id=zi.acid
 WHERE zi.status=0 AND usertype=1 ORDER by zi.id DESC");
 
$q2=mysql_query("SELECT zi.id,zi.date,sa.username,zi.amount,zi.description,zi.paydate,zi.paydescription,pt.type  FROM `dbisoftik`.`z_invoice` AS zi
                LEFT JOIN `dbisoftik`.`s_partner` AS sa ON sa.id=zi.acid
                LEFT JOIN `dbisoftik`.`s_paytype` AS pt ON pt.id=zi.paytype
 WHERE zi.status=1 AND usertype=1 ORDER by zi.paydate DESC");

echo '<table width="100%">
    <tr>
        <td width="50%">
<h3>Неоплаченные счета <a href="#" onclick="location.reload(); return false;"><i class="glyphicon glyphicon-repeat" style="color:green; font-size:20px"></i></a>   </h3>     
        
<table class="table table-striped">
    <tr>
        <td># Счета</td>
        <td>Дата</td>
        <td>Аккаунт</td>
        <td>Сумма</td>
        <td>Описание</td>
        <td>Действие</td>
    </tr>
';
while($row=mysql_fetch_assoc($q)){
    echo '<tr>
        <td>P'.str_pad($row['id'], 10, '0', STR_PAD_LEFT).'</td>
        <td>'.date('d.m.Y H:i:s',strtotime($row['date'])).'</td>
        <td>'.$row['username'].'</td>
        <td>'.$row['amount'].'</td>
        <td>'.$row['description'].'</td>
        <td><a href="#" onclick="acception(\''.$row['id'].'\',\'P'.str_pad($row['id'], 10, '0', STR_PAD_LEFT).'<br />'.date('d.m.Y H:i:s',strtotime($row['date'])).'<br />'.$row['username'].'<br />'.$row['amount'].'<br />'.$row['description'].'\')"><i class="glyphicon glyphicon-usd"></i> Подтвердить</a> </td>
    </tr>';     
}
echo '</table></td>
   <td>
   <h3>Оплаченные счета</h3>
   <table class="table table-striped">
    <tr>
        <td># Счета</td>
        <td>Дата</td>
        <td>Аккаунт</td>
        <td>Сумма</td>
        <td>Описание</td>
        <td>Тип оплаты</td>
        <td>Дата оплаты</td>
        <td>Описание оплаты</td>
    </tr>
';
while($row=mysql_fetch_assoc($q2)){
    echo '<tr>
        <td>P'.str_pad($row['id'], 10, '0', STR_PAD_LEFT).'</td>
        <td>'.date('d.m.Y H:i:s',strtotime($row['date'])).'</td>
        <td>'.$row['username'].'</td>
        <td>'.$row['amount'].'</td>
        <td>'.$row['description'].'</td>
        <td>'.$row['type'].'</td>
        <td>'.date('d.m.Y H:i:s',strtotime($row['paydate'])).'</td>
        <td>'.$row['paydescription'].'</td>
    </tr>';     
}

echo'</table>
   </td>
    </tr>
</table>';     
?>
            </div> 
            <div class="tab-pane" id="feedback">
                <div class="toolbar" style="padding: 9px 0 9px 11px;">
                    <h4>Обратная связь</h4>
                </div>
 <?               
                $q=mysql_query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME LIKE 'db\_%'");
echo '<table class="table table-striped table-hover table-bordered"><tr>
    <td>id</td>
    <td>Аккаунт</td>
    <td>Юзер</td>
    <td>Дата</td>
    <td>Сообщение</td>
    </tr>';
$i=1;
$union=array();
while($row=mysql_fetch_array($q)){
    $union[]="(SELECT *,'".$row['SCHEMA_NAME']."' AS base FROM ".$row['SCHEMA_NAME'].".z_feedback)";
}

     $qw=mysql_query("SELECT * FROM (".join(' UNION ALL ',$union).") AS t ORDER by date DESC"); 
     while($row2=mysql_fetch_array($qw)){     
        echo '<tr>
        <td>'.$row2['base'].'</td>
        <td>'.$row2['userid'].'</td>
        <td>'.$row2['user'].'</td>
        <td>'.date('d.m.Y H:i:s',$row2['date']).'</td>
        <td>'.$row2['message'].'</td>
        </tr>';
     }

echo'</table>';
?>
            </div>
        </div>
        </td>
    </tr>
</table>

</body>
</html>
<?
    }else{
      header('WWW-Authenticate: Basic realm="realm"'); 
      header('HTTP/1.0 401 Unauthorized'); 
      exit; 
    }
}
?>