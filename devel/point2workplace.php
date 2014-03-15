<?
die();
include('../company/config.php');
$link = mysql_pconnect('localhost', LOGIN, PASS) or die("Нет соединения с базой данных: " . mysql_error());
mysql_query('SET NAMES utf8');

$q=mysql_query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME LIKE 'db\_%'");

while($row=mysql_fetch_array($q)){
    $qqw=mysql_query("SELECT * FROM `".$row['SCHEMA_NAME']."`.`t_workplace`");
    if (mysql_numrows($qqw)==0){
        $qw=mysql_query("SELECT * FROM `".$row['SCHEMA_NAME']."`.`s_automated_point`"); 
        while($row2=mysql_fetch_array($qw)){ 
            mysql_query("INSERT INTO `".$row['SCHEMA_NAME']."`.`t_workplace` SET 
            `idout`='".$row2['idout']."',
            `idlink`='".$row2['idlink']."',
            `parentid`='".$row2['parentid']."',
            `isgroup`='".$row2['isgroup']."',
            `name`='".$row2['login']."',
            `login`='".$row2['login']."',
            `password`='".$row2['password']."',
            `isonline`='0',
            `last_action`='".$row2['last_action']."',
            `cookie_key`='".$row2['cookie_key']."',
            `apid`='".$row2['id']."'"); 
            
            $lastid=mysql_insert_id();
            
            $qrights=mysql_query("SELECT * FROM `".$row['SCHEMA_NAME']."`.`t_employee_automated_point` WHERE `automated_pointid`='".$row2['id']."'");
            while($row_right=mysql_fetch_array($qrights)){
                mysql_query("INSERT INTO `".$row['SCHEMA_NAME']."`.`t_employee_workplace` SET 
                    `employeeid`='".$row_right['employeeid']."',
                    `wpid`='".$lastid."'
                ");
            } 
            mysql_query("UPDATE `".$row['SCHEMA_NAME']."`.`s_automated_point` SET `login`='".$row2['login']."--', `cookie_key`='' WHERE id='".$row2['id']."'");
        }
        
    }
}

?>