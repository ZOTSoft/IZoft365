<?
$time=time()-5*60;
$error='';
session_start();
include('../company/mysql_connect.php');
include('../company/core.php');
include('../company/functions.php');

if (!isset($_POST['chk'])) $_POST['chk']=0;
if(isset($_GET['do'])&&($_GET['do']=='logout')){
        //    \m/ \m/ 
        //zlog($_SESSION['user'],1100);
        parter_destroy();
        header('Location: /partner/');
    }
    
    
if (isset($_GET['checkauth'])){
     if (isset($_SESSION['parner'])){
      echo json_encode(array('type'=>'login','link'=>"partner")); die;
  }else
    if (isset($_COOKIE['pkey'])&&($_COOKIE['pkey']!='')&&($_COOKIE['pkey']!='0')&&($_COOKIE['pkey']!=' ')){
        if (isset($_COOKIE['pzid'])&&($_COOKIE['pzid']!='')){
                if ($partner=get_partner_by_cookie($_COOKIE['pkey'])){
                    set_partner_cookie($partner['id'],isset($_POST['chk']));
                    $_SESSION=array('puser'=>$partner['username'],'pfio'=>$partner['fio'],'ppassword'=>md5($partner['password']),'puserid'=>$partner['id'],'partner'=>1);
                    echo json_encode(array('type'=>'login','link'=>"partner")); die;
                }
            
        }
    }else{   
        echo json_encode(array('type'=>'nologin',)); die;
    }
}


if (isset($_POST['login'])&&isset($_POST['password'])){
    $login=addslashes($_POST['login']);
    $pass=md5(FISH.md5($_POST['password'])); 
            if ($partner=get_partner_by_pass($login,$pass)){
                if($partner['last_action']<$time){
                    
                        $_SESSION=array('puserid'=>$partner['id'],'ppassword'=>md5($partner['password']),'pfio'=>$partner['fio'],'puser'=>$login,'partner'=>1);
                        set_partner_cookie($partner['id'],(int)$_POST['chk']);

                        echo json_encode(array('type'=>'login','link'=>"partner")); die;

                }else{ 
                    $error='Имеется активная сессия'; 
                    //zlog($login,1303,$account['db']);
                }
            
        }else{
            $error='Такого аккаунта не сущестует';
        }
        
}else{
     
    
    if(isset($_GET['err'])){
      switch ($_GET['err']){
          case 1:$error='Неверный пароль'; break;
          case 2:$error='Ваще неверный пароль'; break;
          //case 3:echo 'Сессии нет'; break;
      }
    }
    
}

echo json_encode(array('type'=>'error','message'=>$error)); die;
//print_r($error);
?>