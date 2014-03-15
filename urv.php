<?
$error='';
session_start();
include('company/mysql_connect.php');
include('company/core.php');
include('company/functions.php');



if (isset($_POST['login'])&&isset($_POST['password'])){
    $dot=strpos($_POST['login'],'.');
    $db=addslashes(substr($_POST['login'],$dot+1));
    $login=addslashes(substr($_POST['login'],0,$dot));
    $pass=md5(FISH.md5($_POST['password'])); 

    $account=get_account_by_username($db);
    
    if ($urv=get_urv_by_pass($account['db'],$login,$pass)){ 

                        
                        $_SESSION=array('base'=>$account['db'],'base_user'=>$account['db_user'],'base_password'=>$account['db_password'],'userid'=>$urv['id'],'password'=>md5($urv['password']),'user'=>$login,'urv'=>1,'timezone'=>$account['timezone'],'urvid'=>$urv['id']);
                        set_urv_cookie($urv['id'],$account['db'],$account['id'],$_POST['chk']);
                        unset($_SESSION['main']);
                        unset($_SESSION['admin']);
                        unset($_SESSION['wplace']);
                        zlog($login,1101);
                        header("Location: /task/urv/terminal.php"); die;
                    
                }

}
    

?><!DOCTYPE html PUBLIC>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Paloma365 Авторизация</title>
<link href="/index/CSS/login.css" rel="stylesheet" type="text/css">
<link href="/index/CSS/colors.css" rel="stylesheet" type="text/css">

</head>
<body style=" no-repeat; background-size: 100%;">
<div style="padding: 30px 0 0 30px;"><a href="http://paloma365.kz"><img src="/index/images/logo.png" alt=""></a></div>
<div id="regform">
            
          <div id="centralregform" style="opacity: 0">
              <div id="acceslable" class="biglabel">Восстановление доступа</div>
              <div class="smalllable">Мы пришлем информацию для входа владельцу :</div>
              <input type="text" value id="accesinput" placeholder="Почта">
              <button class="bg lightblue active hover btnpos1" onclick="recovery()">Выслать</button>
          </div>              
          <div id="leftregform">
            <div id="loginlabel" class="biglabel">Вход в личный кабинет</div>
            <form method="post" action="" id="frmauth">
                <input type="text" name="login" id="logininput" placeholder="Логин.Компания">
                <input type="password" name="password"  id="pwdinput" placeholder="Пароль" >
                <input type="hidden" name="redirect" value="" id="redff">
                <input type="checkbox" name="chk" id="chk" value="1" checked="checked"><label for="chk" id="chkl">Запомнить</label>
                <input type="submit"  value="Войти" class="bg lightblue active hover btnpos1" >
            </form>

          </div>          
               
        </div> 

</body>
</html>
