<?
$error='';
session_start();
include('../company/mysql_connect.php');
include('../company/core.php');
include('../company/functions.php');



if(isset($_GET['do'])&&($_GET['do']=='logout')){

        partner_destroy();
        header('Location: /');
    }
if (isset($_COOKIE['pkey'])&&($_COOKIE['pkey']!='')&&($_COOKIE['pkey']!='0')&&($_COOKIE['pkey']!=' ')){
        if (isset($_COOKIE['pzid'])&&($_COOKIE['pzid']!='')){
                if ($partner=get_partner_by_cookie($account['db'],$_COOKIE['pkey'])){
                    set_partner_cookie($partner['id'],$account['db'],$account['id'],true);
                    $_SESSION=array('partnerid'=>$partner['id'],'ppassword'=>md5($partner['password']),'pfio'=>$partner['fio'],'puser'=>$partner['name'],'partner'=>1);

                    header("Location: /partner"); die;

                }
            
        }
}
    
    
  if (isset($_SESSION['partner'])){
      header("Location: /partner"); die;
  }
?><!DOCTYPE html PUBLIC>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Paloma365 Авторизация Партнёра</title>
<link href="/index/CSS/login.css" rel="stylesheet" type="text/css">
<link href="/index/CSS/colors.css" rel="stylesheet" type="text/css">
<script src="/index/JS/jquery.js"></script>
<script src="/index/JS/partner_login.js"></script>
</head>
<body style=" no-repeat; background-size: 100%;">
<div style="padding: 30px 0 0 30px;"><a href="/"><img src="/index/images/logo.png" alt=""></a></div>
<div id="regform">
            <div id="regblock" style="display: none; opacity: 0">
              <div class="biglabel">Форма Регистрации:</div>
              <input type="text" value id="reglogin" placeholder="Логин">
              <input type="password" value id="regpass" placeholder="Пароль">
              <input type="text" value id="regfio" placeholder="ФИО">
              <input type="text" value id="regphone" placeholder="Телефон">
              <input type="text" value id="regmail" placeholder="Е-mail:"> 
              <button id="submitbtn" class="bg lightblue active hover" style="position: absolute; bottom: 10px; left: 10px;width: 130px;" onclick="registration()">Получить</button>
              <button id="cancelbtn" class="bg lightblue active hover" style="position: absolute; bottom: 10px; right: 15px;width: 130px;">Отмена</button>
          </div>
          <div id="centralregform" style="opacity: 0">
              <div id="acceslable" class="biglabel">Восстановление доступа</div>
              <div class="smalllable">Мы пришлем информацию для входа владельцу :</div>
              <input type="text" value id="accesinput" placeholder="Почта">
              <button class="bg forestGreen active hover btnpos1" onclick="recovery()">Выслать</button>
          </div>              
          <div id="leftregform">
            <div id="loginlabel" class="biglabel">Вход в режим партнёра</div>
            <form method="post" action="/auth.php" id="frmauth" onsubmit="return auth()">
                <input type="text" name="login" id="logininput" placeholder="Логин">
                <input type="password" name="password"  id="pwdinput" placeholder="Пароль" >
                <input type="hidden" name="redirect" value="" id="redff">
                <input type="checkbox" name="chk" id="chk" value="1" checked="checked"><label for="chk" id="chkl">Запомнить</label>
                <button class="bg forestGreen active hover btnpos1" >Войти</button>
            </form>

            
            <a onclick="changeDiv()" href="#">Забыли пароль?</a>
          </div>          
          <div id="rightregform">
            <div id="reglable" class="biglabel">Регистрация</div>
            <div class="smalllable">Чтобы стать партнёром Paloma365 нужно зарегистрироваться. Это займет всего пару секунд.</div>
            <button id="regbtn" class="bg forestGreen active hover btnpos1">Зарегистрироваться</button>
          </div>            
        </div> 

</body>
</html>
