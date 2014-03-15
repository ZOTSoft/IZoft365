<?
$error='';
session_start();
include('company/mysql_connect.php');
include('company/core.php');
include('company/functions.php');



if(isset($_GET['do'])&&($_GET['do']=='logout')){
        //    \m/ \m/ 
        zlog($_SESSION['user'],1100);
        seek_and_destroy();
        header('Location: /');
    }
if (isset($_COOKIE['key'])&&($_COOKIE['key']!='')&&($_COOKIE['key']!='0')&&($_COOKIE['key']!=' ')){
    
        if (isset($_COOKIE['zid'])&&($_COOKIE['zid']!='')){
            if ($account=get_account_by_id(addslashes($_COOKIE['zid']))){
                if ($employee=get_employee_by_cookie($account['db'],$_COOKIE['key'])){
                    set_employee_cookie($employee['id'],$account['db'],$account['id'],true);
                    $_SESSION=array('base'=>$account['db'],'base_user'=>$account['db_user'],'base_password'=>$account['db_password'],'userid'=>$employee['id'],'password'=>md5($employee['password']),'fio'=>$employee['fio'],'user'=>$employee['name'],'main'=>1,'timezone'=>$account['timezone']);
                    unset($_SESSION['admin']);
                    unset($_SESSION['wplace']);
                    unset($_SESSION['point']);
                    unset($_SESSION['urv']);
                   
                    header("Location: /company"); die;
                    //echo json_encode(array('type'=>'login','link'=>"company")); die;
                }else{
                    if ($urv=get_urv_by_cookie($account['db'],$_COOKIE['key'])){
                        //print_r($urv);
                        //die;
                        set_urv_cookie($urv['id'],$account['db'],$account['id'],true);
                        $_SESSION=array('base'=>$account['db'],'base_user'=>$account['db_user'],'base_password'=>$account['db_password'],'userid'=>$urv['id'],'password'=>md5($urv['password']),'user'=>$urv['name'],'urv'=>1,'timezone'=>$account['timezone'],'urvid'=>$urv['id']);
                        unset($_SESSION['main']);
                        unset($_SESSION['wplace']);
                        unset($_SESSION['admin']);
                        // die(1);
                        header("Location: /task/urv");  die;
                        //echo json_encode(array('type'=>'login','link'=>"urv")); die;
                    }else
                    if ($automatedpoint=get_automated_point_by_cookie($account['db'],$_COOKIE['key'])){
                        set_automated_point_cookie($automatedpoint['id'],$account['db'],$account['id'],true);
                        $_SESSION=array('base'=>$account['db'],'base_user'=>$account['db_user'],'base_password'=>$account['db_password'],'idap'=>$automatedpoint['id'],'userid'=>$automatedpoint['id'],'password'=>md5($automatedpoint['password']),'user'=>$automatedpoint['name'],'point'=>1,'timezone'=>$automatedpoint['timezone']);
                        unset($_SESSION['main']);
                        unset($_SESSION['wplace']);
                        unset($_SESSION['admin']);
                        unset($_SESSION['urv']); 
                        header("Location: /front");  die;
                        //echo json_encode(array('type'=>'login','link'=>"front")); die;
                    }else{
                        if ($workplace=get_workplace_by_cookie($account['db'],$_COOKIE['key'])){
                            set_workplace_cookie($workplace['wid'],$account['db'],$account['id'],true);
                            $_SESSION=array('base'=>$account['db'],'base_user'=>$account['db_user'],'base_password'=>$account['db_password'],'idap'=>$workplace['id'],'wid'=>$workplace['wid'],'userid'=>$workplace['wid'],'password'=>md5($workplace['password']),'user'=>$workplace['name'],'wplace'=>1,'point'=>1,'timezone'=>$workplace['timezone']);
                            unset($_SESSION['main']);
                            unset($_SESSION['admin']);
                            unset($_SESSION['urv']); 
                            header("Location: /front");  die;
                            //echo json_encode(array('type'=>'login','link'=>"front")); die;
                        }else{
                            if ($account_user=get_account_user_by_cookie($_COOKIE['key'])){
                                set_account_user_cookie($account_user['id'],true);
                                $_SESSION=array('base'=>$account_user['db'],'base_user'=>$account_user['db_user'],'base_password'=>$account_user['db_password'],'user'=>$account_user['username'],'fio'=>$account_user['fio'],'password'=>md5($account_user['password']),'userid'=>$account_user['id'],'admin'=>1,'timezone'=>$account_user['timezone']);
                                unset($_SESSION['main']);
                                unset($_SESSION['point']);
                                unset($_SESSION['wplace']);
                                header("Location: /company/index.php"); die;    
                                //echo json_encode(array('type'=>'login','link'=>"company")); die;
                            } 
                        }
                    }
                }
            }
        }
    }
    
    
  if (isset($_SESSION['main'])){
      header("Location: /company"); die;
  }elseif (isset($_SESSION['admin'])){
      header("Location: /company"); die;
  }elseif (isset($_SESSION['point'])){
      header("Location: /front"); die;
  }elseif (isset($_SESSION['urv'])){
      header("Location: /task/urv"); die;
  }
?><!DOCTYPE html PUBLIC>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Paloma365 Авторизация</title>
<link href="/index/CSS/login.css" rel="stylesheet" type="text/css">
<link href="/index/CSS/colors.css" rel="stylesheet" type="text/css">
<link href="/index/CSS/keyboard.css" rel="stylesheet" type="text/css">
<script src="/index/JS/jquery.js"></script>
<script src="/index/JS/keyboard.js"></script>
<script src="/index/JS/login.js"></script>
</head>
<body style=" no-repeat; background-size: 100%;">
<div style="padding: 30px 0 0 30px;"><a href="http://paloma365.kz"><img src="/index/images/logo.png" alt=""></a></div>
<div id="regform">
            <div id="regblock" style="display: none; opacity: 0">
              <div class="biglabel">Форма Регистрации:</div>
              <input type="text" value id="reglogin" placeholder="Компания">
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
              <button class="bg lightblue active hover btnpos1" onclick="recovery()">Выслать</button>
          </div>              
          <div id="leftregform">
            <div id="loginlabel" class="biglabel">Вход в личный кабинет </div>
            <form method="post" action="/auth.php" id="frmauth" onsubmit="return auth()">
                <div class="ppp1"><input type="text" name="login" id="logininput" placeholder="Логин.Компания" class="keyboardInput"></div>
                <div class="ppp2"><input type="password" name="password"  id="pwdinput" placeholder="Пароль"  class="keyboardInput"></div>
                <input type="hidden" name="redirect" value="" id="redff">
                <input type="checkbox" name="chk" id="chk" value="1" checked="checked"><label for="chk" id="chkl">Запомнить</label>
                <button class="bg lightblue active hover btnpos1" >Войти</button>
            </form>

            
            <a onclick="changeDiv()" href="#">Забыли пароль?</a>
          </div>          
          <div id="rightregform">
            <div id="reglable" class="biglabel">Регистрация</div>
            <div class="smalllable">Чтобы оценить все возможности Paloma365 нужно зарегистрироваться. Это займет всего пару секунд.</div>
            <button id="regbtn" class="bg lightblue active hover btnpos1">Зарегистрироваться</button>
          </div>            
        </div> 

</body>
</html>
