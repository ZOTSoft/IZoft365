<?php
session_start();
include('../company/mysql_connect.php');
include('../company/functions.php');
include('../company/core.php');


if (isset($_POST['login'])&&isset($_POST['password'])&&isset($_POST['fio'])&&isset($_POST['phone'])&&isset($_POST['email'])){
    //if ((!preg_match( "/[\||\'|\<|\>|\[|\]|\"|\!|\?|\$|\@|\#|\%|\^|\/|\\\|\&|\~|\*|\{|\}|\+|\_|\:|\.|\,|\;|\`|\=|\(|\)|\§|\°]/", $_POST['login']))){
    if(preg_match("/^[a-zA-Z0-9]+$/",$_POST['login'])){ 
        $res=mysql_query("SELECT username FROM dbisoftik.s_partner WHERE username='".addslashes($_POST['login'])."'");
        if (mysql_num_rows($res)>0){
            echo json_encode(array('type'=>'error','message'=>'Данный логин занят!')); die;  
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
        $res=mysql_query("SELECT email FROM dbisoftik.s_partner WHERE email='".addslashes($_POST['email'])."'");
        if (mysql_num_rows($res)>0){
            echo json_encode(array('type'=>'error','message'=>'Данный email занят!')); die;  
        }
        
        
        
        $dbpwd=substr(md5(FISH.md5($_POST['login'].time())),1,16);

            $regkey=md5('THIS IS REGAAAAA!!!'.time());
        if($result = mysql_query("INSERT INTO dbisoftik.s_partner (username,fio,phone,email,password,regkey) VALUES ('".addslashes($_POST['login'])."','".addslashes($_POST['fio'])."','".addslashes($_POST['phone'])."','".addslashes($_POST['email'])."','".md5(FISH.md5($_POST['password']))."','".$regkey."')")){
            
            
        }else{ 
            echo json_encode(array('type'=>'error','message'=>"Ошибка в запросе на добавление нового клиента: ", mysql_error())); die;
        }
            //echo json_encode(array('type'=>'ok','message'=>'Всё глатко!')); die; 
            $partner=get_partner_by_pass($_POST['login'],md5(FISH.md5($_POST['password'])));
            $_SESSION=array('puser'=>$partner['username'],'pfio'=>$partner['fio'],'ppassword'=>md5($partner['password']),'puserid'=>$partner['id'],'partner'=>1);
            set_partner_cookie($partner['id'],1);
            //zlog($partner['username'].'Зарегистрировался и Авторизовался в системе.',1306);
            
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
            //sendmail('Paloma365 – Регистрация аккаунта.',$message,$_POST['email']); 
            echo json_encode(array('type'=>'ok','link'=>"/partner/")); die;
            //mail         

    }else{
        echo json_encode(array('type'=>'error','message'=>'Неверные символы в логине')); die; 
    }
}else{
    echo json_encode(array('type'=>'error','message'=>'Нет данных')); die; 
}
?>