<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Авторизация</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="Refresh" content="10">
</head>
<body>
<?
include('../company/mysql_connect.php');


$q=mysql_query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME LIKE 'db\_%'");
echo '<table border=1 cellspacing=2 cellpadding=6><tr>
    <td>#</td>
    <td>База</td>
    <td>Логин</td>
    <td>Последнее действие</td> 
    <td></td>
    </tr>';
$i=1;
    while($row=mysql_fetch_array($q)){
  $bases=array('Пользователь'=>'`'.$row['SCHEMA_NAME'].'`.`s_employee`','Торговая точка'=>'`'.$row['SCHEMA_NAME'].'`.`s_automated_point`');
  foreach($bases as $k=>$base){
     $qw=mysql_query("SELECT * FROM ".$base." WHERE isonline=1"); 
     
     while($row2=mysql_fetch_array($qw)){     
        echo '<tr><td>'.$i.'</td>
            <td>'.$row['SCHEMA_NAME'].' '.$k.'</td>
            <td>'.$row2['name'].'</td>
            <td>'.date('H:i:s',$row2['last_action']).'('.round((time()-$row2['last_action'])/60).'мин. назад)</td>
            <td><a href="#">Выйти</a></td>
            </tr>';
        $i++;
     }
     
  }
}
     $qw2=mysql_query("SELECT * FROM `dbisoftik`.`s_accounts` WHERE isonline=1"); 
     while($row3=mysql_fetch_array($qw2)){     
        echo '<tr>
        <td>'.$i.'</td>
        <td>Аккаунты</td>
        <td>'.$row3['username'].'</td>
        <td>'.date('H:i:s',$row3['last_action']).'('.round((time()-$row3['last_action'])/60).'мин. назад)</td>
        <td><a href="#">Выйти</a></td>
        </tr>';
        $i++;
     }

echo '</table>';
$q=mysql_query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME LIKE 'db\_%'");
echo '<table border=1 cellspacing=2 cellpadding=6><tr>
    <td>id</td>
    <td>Юзер</td>
    <td>Дата</td>
    <td>Сообщение</td>
    </tr>';
$i=1;
while($row=mysql_fetch_array($q)){
     $qw=mysql_query("SELECT * FROM ".$row['SCHEMA_NAME'].".z_feedback"); 
     while($row2=mysql_fetch_array($qw)){     
        echo '<tr>
        <td>'.$row2['userid'].'</td>
        <td>'.$row2['user'].'</td>
        <td>'.date('d.m.Y H:i:s',$row2['date']).'</td>
        <td>'.$row2['message'].'</td>
        </tr>';
     }
}
echo'</table>';





















$array=array();
$i=1;
$q=mysql_query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME LIKE 'db\_%'");
    while($row=mysql_fetch_array($q)){
  $bases=array('Пользователь'=>'`'.$row['SCHEMA_NAME'].'`.`s_employee`','Торговая точка'=>'`'.$row['SCHEMA_NAME'].'`.`s_automated_point`');
  foreach($bases as $k=>$base){
     $qw=mysql_query("SELECT * FROM ".$base."  WHERE last_action>1"); 
     
     while($row2=mysql_fetch_array($qw)){     
         $row2['SCHEMA_NAME']=$row['SCHEMA_NAME'];
         $array[$row2['last_action'].str_pad($i, 4, '0', STR_PAD_LEFT)]=$row2;
         $i++;
     }
     
  }
}
     $qw2=mysql_query("SELECT * FROM `dbisoftik`.`s_accounts` WHERE last_action>1"); 
     while($row3=mysql_fetch_array($qw2)){     
        $row3['SCHEMA_NAME']='Аккаунты';
        $row3['name']=$row['username'];
        $i++;
        $array[$row3['last_action'].str_pad($i, 4, '0', STR_PAD_LEFT)]=$row3;
     }
echo '<table border=1 cellspacing=2 cellpadding=6><tr>
    <td>#</td>
    <td>База</td>
    <td>Логин</td>
    <td>Последнее действие</td>
    </tr>';
$i=1;
krsort($array);
foreach($array as $row){
echo '<tr><td>'.$i.'</td>
            <td>'.$row['SCHEMA_NAME'].' '.$k.'</td>
            <td>'.$row['name'].'</td>
            <td>'.date('d.m.Y H:i:s',$row['last_action']).'('.round((time()-$row['last_action'])/60).'мин. назад)</td>
            </tr>';
        $i++;
}
echo '</table>';


?>
<br />
ALTER TABLE `s_employee`
ADD COLUMN `automated_pointid`  int(11) NULL DEFAULT 0 AFTER `position`;
<br />

</body>
</html>