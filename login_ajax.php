<?
$time=time()-5*60;
$error='';
session_start();
include('company/mysql_connect.php');
include('company/core.php');
include('company/functions.php');



if (!isset($_POST['chk'])) $_POST['chk']=0;
if(isset($_GET['do'])&&($_GET['do']=='logout')){
        //    \m/ \m/ 
        zlog($_SESSION['user'],1100);
        seek_and_destroy();
        header('Location: /');
    }
    
    
if (isset($_GET['checkauth'])){
     if (isset($_SESSION['main'])){
      echo json_encode(array('type'=>'login','link'=>"company")); die;
  }elseif (isset($_SESSION['admin'])){
      echo json_encode(array('type'=>'login','link'=>"company")); die;
  }elseif (isset($_SESSION['point'])){
      echo json_encode(array('type'=>'login','link'=>"front")); die;
  }elseif (isset($_SESSION['urv'])){
      echo json_encode(array('type'=>'login','link'=>"task/urv")); die;
  }else
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
                    //print_r("Location: /company"); die;
                    echo json_encode(array('type'=>'login','link'=>"company")); die;
                }else{
                    if ($urv=get_urv_by_cookie($account['db'],$_COOKIE['key'])){
                        set_urv_cookie($urv['id'],$account['db'],$account['id'],isset($_POST['chk']));
                        $_SESSION=array('base'=>$account['db'],'base_user'=>$account['db_user'],'base_password'=>$account['db_password'],'userid'=>$urv['id'],'password'=>md5($urv['password']),'user'=>$urv['name'],'urv'=>1,'timezone'=>$account_user['timezone'],'urvid'=>$urv['id']);
                        unset($_SESSION['main']);
                        unset($_SESSION['wplace']);
                        unset($_SESSION['admin']);
                        //print_r("Location: /front");  die;
                        echo json_encode(array('type'=>'login','link'=>"task/urv")); die;
                    }else
                    if ($automatedpoint=get_automated_point_by_cookie($account['db'],$_COOKIE['key'])){
                        set_automated_point_cookie($automatedpoint['id'],$account['db'],$account['id'],isset($_POST['chk']));
                        $_SESSION=array('base'=>$account['db'],'base_user'=>$account['db_user'],'base_password'=>$account['db_password'],'idap'=>$automatedpoint['id'],'userid'=>$automatedpoint['id'],'password'=>md5($automatedpoint['password']),'user'=>$automatedpoint['name'],'point'=>1,'timezone'=>$automatedpoint['timezone']);
                        unset($_SESSION['main']);
                        unset($_SESSION['wplace']);
                        unset($_SESSION['admin']);
                        unset($_SESSION['urv']); 
                        //print_r("Location: /front");  die;
                        echo json_encode(array('type'=>'login','link'=>"front")); die;
                    }else{
                        if ($workplace=get_workplace_by_cookie($account['db'],$_COOKIE['key'])){
                            set_workplace_cookie($workplace['wid'],$account['db'],$account['id'],isset($_POST['chk']));
                            $_SESSION=array('base'=>$account['db'],'base_user'=>$account['db_user'],'base_password'=>$account['db_password'],'idap'=>$workplace['id'],'wid'=>$workplace['wid'],'userid'=>$workplace['wid'],'password'=>md5($workplace['password']),'user'=>$workplace['name'],'wplace'=>1,'point'=>1,'timezone'=>$workplace['timezone']);
                            unset($_SESSION['main']);
                            unset($_SESSION['admin']);
                            unset($_SESSION['urv']); 
                            //print_r("Location: /front");  die;
                            echo json_encode(array('type'=>'login','link'=>"front")); die;
                        }else{
                            if ($account_user=get_account_user_by_cookie($_COOKIE['key'])){
                                set_account_user_cookie($account_user['id'],isset($_POST['chk']));
                                $_SESSION=array('base'=>$account_user['db'],'base_user'=>$account_user['db_user'],'base_password'=>$account_user['db_password'],'user'=>$account_user['username'],'fio'=>$account_user['fio'],'password'=>md5($account_user['password']),'userid'=>$account_user['id'],'admin'=>1,'timezone'=>$account_user['timezone']);
                                unset($_SESSION['main']);
                                unset($_SESSION['point']);
                                unset($_SESSION['wplace']);
                                //print_r("Location: /company/admin.php"); die;    
                                echo json_encode(array('type'=>'login','link'=>"company")); die;
                            } 
                        }
                    }
                }
            }
        }
    }else{   
        echo json_encode(array('type'=>'nologin',)); die;
    }
}


if (isset($_POST['login'])&&isset($_POST['password'])){
    $dot=strpos($_POST['login'],'.');
    $db=addslashes(substr($_POST['login'],$dot+1));
    $login=addslashes(substr($_POST['login'],0,$dot));
    $pass=md5(FISH.md5($_POST['password'])); 
    if ($dot){
        if ($account=get_account_by_username($db)){
            if ($employee=get_employee_by_pass($account['db'],$login,$pass)){
                
                if($employee['last_action']<$time){
                    if (check_employee_interface($employee['id'],$account['db'])){
                        $_SESSION=array('base'=>$account['db'],'base_user'=>$account['db_user'],'base_password'=>$account['db_password'],'userid'=>$employee['id'],'password'=>md5($employee['password']),'fio'=>$employee['fio'],'user'=>$login,'main'=>1,'timezone'=>$account['timezone']);
                        set_employee_cookie($employee['id'],$account['db'],$account['id'],(int)$_POST['chk']);
                        unset($_SESSION['admin']);
                        unset($_SESSION['point']);
                        unset($_SESSION['wplace']);
                        unset($_SESSION['urv']); 
                        zlog($login,1101);
                        echo json_encode(array('type'=>'login','link'=>"company")); die;
                    }else{
                        $error='Нет доступных интерфейсов'; 
                        zlog($login,1304,$account['db']);    
                    }
                }else{ 
                    $error='Имеется активная сессия'; 
                    zlog($login,1303,$account['db']);
                }
            }else{
                 if ($urv=get_urv_by_pass($account['db'],$login,$pass)){ 
                    if($urv['last_action']<$time){
                        
                        $_SESSION=array('base'=>$account['db'],'base_user'=>$account['db_user'],'base_password'=>$account['db_password'],'userid'=>$urv['id'],'password'=>md5($urv['password']),'user'=>$login,'urv'=>1,'timezone'=>$account['timezone'],'urvid'=>$urv['id']);
                        set_urv_cookie($urv['id'],$account['db'],$account['id'],$_POST['chk']);
                        unset($_SESSION['main']);
                        unset($_SESSION['admin']);
                        unset($_SESSION['wplace']);
                        zlog($login,1101);
                        echo json_encode(array('type'=>'login','link'=>"task/urv")); die;
                    }else{
                        $error='Имеется активная сессия';
                        zlog($login,1303,$account['db']);
                    }
                }else
                if ($automatedpoint=get_automated_point_by_pass($account['db'],$login,$pass)){ 
                    if($automatedpoint['last_action']<$time){
                        
                        $_SESSION=array('base'=>$account['db'],'base_user'=>$account['db_user'],'base_password'=>$account['db_password'],'idap'=>$automatedpoint['id'],'userid'=>$automatedpoint['id'],'password'=>md5($automatedpoint['password']),'user'=>$login,'point'=>1,'timezone'=>$automatedpoint['timezone']);
                        set_automated_point_cookie($automatedpoint['id'],$account['db'],$account['id'],$_POST['chk']);
                        unset($_SESSION['main']);
                        unset($_SESSION['admin']);
                        unset($_SESSION['wplace']);
                        unset($_SESSION['urv']); 
                        zlog($login,1101);
                        echo json_encode(array('type'=>'login','link'=>"front")); die;
                    }else{
                        $error='Имеется активная сессия';
                        zlog($login,1303,$account['db']);
                    }
                }else{
                    if ($workplace=get_workplace_by_pass($account['db'],$login,$pass)){ 
                        if($workplace['last_action']<$time){
                            /*if (check_pay_workplace($workplace['id'])){
                                
                            }*/
                            $_SESSION=array('base'=>$account['db'],'base_user'=>$account['db_user'],'base_password'=>$account['db_password'],'idap'=>$workplace['id'],'wid'=>$workplace['wid'],'userid'=>$workplace['wid'],'password'=>md5($workplace['password']),'user'=>$login,'wplace'=>1,'point'=>1,'timezone'=>$workplace['timezone']);
                            set_workplace_cookie($workplace['wid'],$account['db'],$account['id'],$_POST['chk']);
                            unset($_SESSION['main']);
                            unset($_SESSION['admin']);
                            unset($_SESSION['urv']); 
                            zlog($login,1101);
                            echo json_encode(array('type'=>'login','link'=>"front")); die;
                        }else{
                            zlog($login,1303,$account['db']);
                            $error='Имеется активная сессия';
                        }
                    }else{
                        $error='Неверный пароль';
                        zlog($login,1302,$account['db']);
                    }
                }
            }
        }else{
            $error='Такого аккаунта не сущестует';
            
        }
    }else{
        if ($account_user=get_account_user_by_pass($_POST['login'],$pass)){ 
                if($account_user['last_action']<$time){
                    
                    $_SESSION=array('base'=>$account_user['db'],'base_user'=>$account_user['db_user'],'base_password'=>$account_user['db_password'],'user'=>$login,'fio'=>$account_user['fio'],'password'=>md5($account_user['password']),'userid'=>$account_user['id'],'admin'=>1,'timezone'=>$account_user['timezone']);
                    set_account_user_cookie($account_user['id'],$_POST['chk']);
                    
                    unset($_SESSION['main']);
                    unset($_SESSION['wplace']);
                    unset($_SESSION['point']);
                    unset($_SESSION['urv']); 
                    zlog($login,1101);
                    //print_r("Location: /company/admin.php"); die;
                    echo json_encode(array('type'=>'login','link'=>"company")); die;
                }else{
                    $error='Имеется активная сессия';
                    zlog($login,1303,$account_user['db']);
                }
            }else{
            $error='Неверный логин или пароль';
            }
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