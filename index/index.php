<!DOCTYPE html PUBLIC>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="keywords" content="Облачная, автоматизация, кафе, рестораны, фасфуд, магазины, бутики, общепит, ISOFT, ISOFT365, Paloma365, Paloma, Paloma 365,paloma365.kz, салон красоты.">
<meta name="description" content="Облачная автоматизация кафе, ресторанов, фастфуд, магазинов, бутиков. Автоматизация за 5000 тенге!">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Paloma365</title> 
<link href="index/CSS/mainstyle_1.css" rel="stylesheet" type="text/css">
<link href="index/CSS/colors.css" rel="stylesheet" type="text/css">
<link href="index/CSS/animate.css" rel="stylesheet" type="text/css"> 
<link href="index/CSS/anythingslider.css" rel="stylesheet" type="text/css">    
<link href="index/CSS/lightbox.css" rel="stylesheet" type="text/css">   
<link rel="shortcut icon" href="/favicon.png" type="image/x-icon" />    
<script src="index/JS/jquery.js"></script>
<script src="index/JS/jquery.easing.1.2.js"></script>
<script src="index/JS/jquery.anythingslider.js"></script>
<script src="index/JS/jquery-ui.js"></script>
<script src="index/JS/lightbox-2.6.min.js"></script>
<script src="index/JS/mainjs.js"></script>
</head>
<body  style="">
<div class="login-glass" id="auth-glass" style="display: none; opacity:0"> </div>
    <!--<div class="main-page">-->
     <div id="glass" class="glass" style="display: none;opacity: 0"></div>
        <div id="regform" style="display: none;opacity: 0">
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
            <div id="loginlabel" class="biglabel">Вход в личный кабинет</div>
                <input type="text" name="login" id="logininput" placeholder="Логин.Компания">
                <input type="password" name="password"  id="pwdinput" placeholder="Пароль" onkeydown="if(event.keyCode==13){auth();}">
                <input type="checkbox" name="chk" id="chk" value="1" checked="checked"><label for="chk" id="chkl">Запомнить</label>
                <button class="bg lightblue active hover btnpos1" onclick="auth()">Войти</button>
            
            
            <div style="display: none">
                <form method="post" action="/auth.php" id="frmauth">
                    <input type="text" name="login" id="zzlogin"/>
                    <input type="hidden" name="redirect" value="" id="redff">
                    <input type="password" name="password" id="zzpassword"/>
                    <input type="submit" value="Login" id="zzsubmit"/>
                </form>
            </div>
            
            <a onclick="changeDiv()" href="#">Забыли пароль?</a>
          </div>          
          <div id="rightregform">
            <div id="reglable" class="biglabel">Регистрация</div>
            <div class="smalllable">Чтобы оценить все возможности Paloma365 нужно зарегистрироваться. Это займет всего пару секунд.</div>
            <button id="regbtn" class="bg lightblue active hover btnpos1">Зарегистрироваться</button>
          </div>            
        </div>     
        <div class="main-header">
            <a href="/" target="_self"><img src="index/images/logo.png"></a>
            <button id="signinbtn" class="bg lightblue hover active">Вход</button>
            <div id="main-menu-div" class="clearfix">
                <ul id="main-menu">
                    <li><a id="menu-item-1" href="/solutions">Решения</a></li>
                    <li><a id="menu-item-2" href="/aboutus">О нас</a></li>
                    <li><a id="menu-item-3" href="/support">Поддержка</a></li>
                    <li><a id="menu-item-4" href="/partners">Партнеры</a></li>
                    <li><a id="menu-item-5" href="/contacts">Контакты</a></li>
                </ul>
            </div>                
        </div>
            <?php if (!isset($_GET['do'])){ ?>
                <div id="sl">
                       <ul id="myslider"> 
                        <li> 
                         <div class="caption-top"> 
                             <div style="margin:1em 0 0 1em; font: italic bold arial, sans-serif;">Вы сможете использовать облачные решения с любого устройства...</div>
                             <button onclick="location.href='/solutions'" style="border-color:white!important;position: absolute;bottom: 20px;right: 20px;width:126px" class="bg red active hover">Подробнее</button>
                         </div> 
                         <img src="index/images/1slide.jpg" alt=""> 
                        </li> 
                        <li>
                         <img src="index/images/2slide.jpg" alt=""> 
                         <div class="caption-top"> 
                             <div style="margin:1em 0 0 1em; font: italic bold arial, sans-serif;">Наша облачная автоматизация подойдет для совершенно разных типов обьектов...</div>
                             <button onclick="location.href='/solutions'" style="border-color:white!important;position: absolute;bottom: 20px;right: 20px;width:126px" class="bg red active hover">Подробнее</button>
                         </div> 
                        </li> 
                        <li> 
                         <img src="index/images/3slide.jpg" alt=""> 
                         <div class="caption-top"> 
                             <div style="margin:1em 0 0 1em; font: italic bold arial, sans-serif;">Управляйте своим бизнесом из любой точки мира...</div>
                             <button onclick="location.href='/solutions'" style="border-color:white!important;position: absolute;bottom: 20px;right: 20px;width:126px" class="bg red active hover">Подробнее</button>
                         </div> 
                        </li>  
                        <!--<li> 
                         <img src="index/images/sss.jpg" alt=""> 
                         <div class="caption-top"> 
                             <div style="margin:1em 0 0 1em; font: italic bold arial, sans-serif;">Ваш бизнес в безопастности...</div>
                             <button onclick="location.href='/solutions'" style="border-color:white!important;position: absolute;bottom: 20px;right: 20px;width:126px" class="bg red active hover">Подробнее</button>
                         </div> 
                        </li>  -->                           
                       </ul>
                </div>
            <?php }?>
     <div id="main-content" class="main-content">
                        <?php include('index/PHP/indexcontent.php'); ?>      
     </div>
        <footer>
            <div class="footerdiv">
                <img id="logo_img" src="index/images/paloma_service.png">
                <span id="logo_text">&COPY; ТОО "Paloma service"</span>
            </div>
        </footer>
    <!--</div>-->
<?
if (isset($_GET['recovery'])){
    echo '<script>$(function(){   ';
    if ($_GET['recovery']==1) echo 'alert("Новый пароль выслан на почту!")'; else echo 'alert("Чтото пошло не так!")';
    echo'});</script>';
}
?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-47543523-1', 'paloma365.kz');
  ga('send', 'pageview');

</script>
</body>
</html>
