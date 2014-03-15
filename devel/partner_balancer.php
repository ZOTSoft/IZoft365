<?php
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    die('Access denied');
} else {
    if ($_SERVER['PHP_AUTH_USER'] == 'admin' && $_SERVER['PHP_AUTH_PW'] == 'H82tahda0wdw' ) { 
        include('../company/mysql_connect.php');
        
        if (isset($_POST['id'])){
            $check=mysql_query("SELECT id FROM `dbisoftik`.`z_invoice` WHERE id='".intval($_POST['id'])."' AND status=0");
            if (mysql_numrows($check)){
                mysql_query("UPDATE `dbisoftik`.`z_invoice` SET status=1,paydescription='".addslashes($_POST['description'])."',paydate=NOW(),paytype='".addslashes($_POST['type'])."' WHERE id='".intval($_POST['id'])."'");
                $q=mysql_query("SELECT sa.percent,sa.id,zi.amount,zi.acid,sa.db FROM `dbisoftik`.`z_invoice` as zi 
                LEFT JOIN `dbisoftik`.`s_partner` AS sa ON sa.id=zi.acid 
                WHERE zi.id='".intval($_POST['id'])."'");
                $a=mysql_fetch_assoc($q);
                
                $amount=($a['amount']/(100-$a['percent'])*100);
                
                mysql_query("UPDATE `dbisoftik`.`s_partner` SET `balance`=`balance`+".intval($amount)." WHERE id='".intval($a['acid'])."'");
                
                mysql_query("INSERT into `dbisoftik`.`s_transaction` SET `name`='Оплата по счету №ZOT".intval($_POST['id']).' в размере '.$a['amount']." была принята( Процент партнера ".intval($a['percent'])."). Начислено ',`amount`='".intval($amount)."',pid='".$a['id']."'");
            }
            
        }
        ?><!DOCTYPE html>
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <title>Управлятор</title>
    <link rel="stylesheet" type="text/css" href="/company/mtree/css_tree/css_tree.css">
    <link rel="stylesheet" type="text/css" href="/company/mtree/css_tree/tablecss.css">
    <script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/company/mtree/mytreeview.js"></script>
    <script type="text/javascript" src="/company/js/jquery.form.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/company/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/company/bootstrap/css/bootstrap-datetimepicker.min.css">
    <script type="text/javascript" src="/company/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="/company/bootstrap/js/bootstrap-inputmask.min.js"></script>
    <script type="text/javascript" src="/company/js/bootbox.min.js"></script>
    <script type="text/javascript" src="/company/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    <style type="text/css">
        td{vertical-align: top;font-size:12px}
    </style>
    <script type="text/javascript">
        function acception(id,desc){
            data='<form method="post" id="frm1">'+desc;
            
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
        <td>ZOT'.$row['id'].'</td>
        <td>'.date('d.m.Y H:i:s',strtotime($row['date'])).'</td>
        <td>'.$row['username'].'</td>
        <td>'.$row['amount'].'</td>
        <td>'.$row['description'].'</td>
        <td><a href="#" onclick="acception(\''.$row['id'].'\',\'ZOT'.$row['id'].'<br />'.date('d.m.Y H:i:s',strtotime($row['date'])).'<br />'.$row['username'].'<br />'.$row['amount'].'<br />'.$row['description'].'\')"><i class="glyphicon glyphicon-usd"></i> Подтвердить</a> </td>
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
        <td>ZOT'.$row['id'].'</td>
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
    }else{
      header('WWW-Authenticate: Basic realm="realm"'); 
      header('HTTP/1.0 401 Unauthorized'); 
      exit; 
    }
}
?>