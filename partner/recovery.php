<?php

$error='';
session_start();
include('../company/mysql_connect.php');
include('../company/functions.php');


if (isset($_POST['mail'])){
    if (recovery_account($_POST['mail'])){ 
        echo json_encode(array('result'=>'ok')); die;
    }else{
        echo json_encode(array('result'=>'noexist')); die;
    }
}
if (isset($_GET['key'])){
    if (mail_new_password_partner($_GET['pkey'])){ 
        //echo json_encode(array('result'=>'ok')); die;
        header("Location: /?recovery=1"); die;
    }else{
        //echo json_encode(array('result'=>'noexist')); die;
        header("Location: /?recovery=2"); die;
    }
}


?>
