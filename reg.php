<?php
session_start();
include('company/mysql_connect.php');
include('company/functions.php');
include('company/core.php');

function copy_base_to_base($newbase,$dumpbase){
    if (!mysql_query ("CREATE DATABASE IF NOT EXISTS ".$newbase)){
        printf ("creating database error: %s\n", mysql_error());     
    }
    mysql_select_db('information_schema');
    
    $retval = mysql_query("SELECT TABLE_NAME  FROM TABLES WHERE TABLE_SCHEMA='".$dumpbase."'");
    if(!$retval){
        
      die('Could not get data: ' . mysql_error());
    }
    
    while($row = mysql_fetch_array($retval, MYSQL_ASSOC)){
        $tblname=$row['TABLE_NAME'];
        if (!mysql_query ("create table if not exists ".$newbase.".".$tblname." like ".$dumpbase.".".$tblname)){
            echo json_encode(array('type'=>'error','message'=>"create table in database error: %s\n", mysql_error())); die;
        } 
             
        if (!mysql_query ("insert ".$newbase.".".$tblname." select * from ".$dumpbase.".".$tblname)){
            echo json_encode(array('type'=>'error','message'=>"create table in database error: %s\n", mysql_error())); die;
        } 
    }
    
}

if(isset($_GET['step'])){
   $q=mysql_query("SELECT id FROM dbisoftik.s_accounts WHERE regkey='".addslashes($_GET['key'])."'");
   if (mysql_numrows($q)){
        mysql_query("UPDATE dbisoftik.s_accounts SET status=1 WHERE regkey='".addslashes($_GET['key'])."' ");
        header('Location: /company');
        die;
   }else{
       echo 'Ошибка'; 
       die;
   }
    
}
 
if (isset($_POST['login'])&&isset($_POST['password'])&&isset($_POST['fio'])&&isset($_POST['phone'])&&isset($_POST['email'])){
    //if ((!preg_match( "/[\||\'|\<|\>|\[|\]|\"|\!|\?|\$|\@|\#|\%|\^|\/|\\\|\&|\~|\*|\{|\}|\+|\_|\:|\.|\,|\;|\`|\=|\(|\)|\§|\°]/", $_POST['login']))){
    if(preg_match("/^[a-zA-Z0-9]+$/",$_POST['login'])){ 
        $res=mysql_query("SELECT username FROM dbisoftik.s_accounts WHERE username='".addslashes($_POST['login'])."'");
        if (mysql_num_rows($res)>0){
            echo json_encode(array('type'=>'error','message'=>'Данный логин занят!')); die;  
        }
        
        if (strlen($_POST['login'])>14){
            echo json_encode(array('type'=>'error','message'=>'Логин слишком длинный. Максимально 14 символов.')); die; 
        }
        if (strlen($_POST['login'])<2){
            echo json_encode(array('type'=>'error','message'=>'Логин слишком короткий')); die; 
        }
        if (strlen($_POST['password'])<6){
            echo json_encode(array('type'=>'error','message'=>'Пароль меньше 6 символов!')); die; 
        }
        if (strlen($_POST['fio'])<1){
            echo json_encode(array('type'=>'error','message'=>'Не заполнено поле ФИО!')); die; 
        }
        if (strlen($_POST['phone'])<1){
            echo json_encode(array('type'=>'error','message'=>'Не заполнено поле Телефон!')); die; 
        }
        if (!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2}/is', $_POST['email'])){
            echo json_encode(array('type'=>'error','message'=>'Не заполнено поле или неверный email!')); die; 
        }
        $res=mysql_query("SELECT email FROM dbisoftik.s_accounts WHERE email='".addslashes($_POST['email'])."'");
        if (mysql_num_rows($res)>0){
            echo json_encode(array('type'=>'error','message'=>'Данный email занят!')); die;  
        }
        
        
        
        $dbpwd=substr(md5(FISH.md5($_POST['login'].time())),1,16);

            $regkey=md5('THIS IS REGAAAAA!!!'.time());
        if($result = mysql_query("INSERT INTO dbisoftik.s_accounts (username,db_user,db_password,fio,phone,email,db,password,timezone,regkey) VALUES ('".addslashes($_POST['login'])."','u_".addslashes($_POST['login'])."','".addslashes($dbpwd)."','".addslashes($_POST['fio'])."','".addslashes($_POST['phone'])."','".addslashes($_POST['email'])."','db_".addslashes($_POST['login'])."','".md5(FISH.md5($_POST['password']))."','Asia/Almaty','".$regkey."')")){
            copy_base_to_base("db_".addslashes($_POST['login']), 'base1');
            
        }else{ 
            echo json_encode(array('type'=>'error','message'=>"Ошибка в запросе на добавление нового клиента: ", mysql_error())); die;
        }
        
        if($result = mysql_query("GRANT ALL PRIVILEGES ON db_".addslashes($_POST['login']).".* TO u_".addslashes($_POST['login'])."@localhost  IDENTIFIED BY '".addslashes($dbpwd)."' WITH GRANT OPTION")){
            //echo json_encode(array('type'=>'ok','message'=>'Всё глатко!')); die; 
            $account_user=get_account_user_by_pass($_POST['login'],md5(FISH.md5($_POST['password'])));
            $_SESSION=array('base'=>$account_user['db'],'base_user'=>$account_user['db_user'],'base_password'=>$account_user['db_password'],'user'=>$account_user['username'],'fio'=>$account_user['fio'],'password'=>md5($account_user['password']),'userid'=>$account_user['id'],'admin'=>1,'timezone'=>$account_user['timezone']);
            set_account_user_cookie($account_user['id'],1);
            zlog($account_user['username'].'Зарегистрировался и Авторизовался в системе.',1306);
            
            $message='
Вас приветствует облачный сервис <a href="http://paloma365.kz">Paloma365</a>. Благодарим за регистрацию.<br />
Paloma365 – это быстрое и удобное решение для Вашего  бизнеса, наши облачные услуги позволят 
вам автоматизировать работу предприятия из любой точки земного шара.<br />
<br />
Для ознакомления с принципом работы облаков  ознакомьтесь с <a href="http://paloma365.kz/support">инструкцией</a> или просмотрите <a href="http://paloma365.kz/support">обучающее видео</a>.
<br />
Ваш аккаунт: '.addslashes($_POST['login']).'
<br>
Пароль: указанный Вами при регистрации
<br />
Для подтверждение регистрации перейдите по следующей <a href="http://paloma365.kz/reg.php?step=2&key='.$regkey.'">ссылке</a><br />
<br />Желаем удачной работы.';
            sendmail('Paloma365 – Регистрация аккаунта.',$message,$_POST['email']); 
            echo json_encode(array('type'=>'ok','link'=>"/company/")); die;
            //mail         
        }else{
            echo json_encode(array('type'=>'error','message'=>'Чото не проскочило при создание юзера')); die; 
        }
    }else{
        echo json_encode(array('type'=>'error','message'=>'Неверные символы в логине')); die; 
    }
}else{
    echo json_encode(array('type'=>'error','message'=>'Нет данных')); die; 
}
?>