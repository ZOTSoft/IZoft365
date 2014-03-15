<?php
  function partner_destroy(){
        if (isset($_SESSION['partner'])){
            mysql_query("UPDATE `dbisoftik`.`s_partner` SET last_action=0,cookie_key='' WHERE id=".$_SESSION['puserid']);
        }
        unset($_SESSION['partner']);
        session_destroy();
        setcookie('pkey','',time()-3600*48); //2 суток
        setcookie('pzid','',time()-3600*48); //2 суток
        unset($_COOKIE['pkey']);
   }
   
   
   
   function get_partner_by_pass($login,$pass){
      $query=mysql_query("SELECT * FROM `dbisoftik`.`s_partner` WHERE UPPER(`username`)=UPPER('".addslashes($login)."') AND password='".addslashes($pass)."'");   
      if (mysql_numrows($query)){
          return mysql_fetch_array($query);
      }else{
          return false;
      }
  }
  


  
  function get_partner_by_cookie($cookie){
      $query=mysql_query("SELECT * FROM `dbisoftik`.`s_partner` WHERE cookie_key='".addslashes($cookie)."'");  
      if (mysql_numrows($query)){
          return mysql_fetch_array($query);
      }else{
          return false;
      }
  }
  function set_partner_cookie($userid,$set){
        $cookie_key=md5('BILL GATES WAS HERE'.time());
        mysql_query("UPDATE `dbisoftik`.`s_partner` SET last_action=UNIX_TIMESTAMP(), `cookie_key`='".$cookie_key."' WHERE id=".$userid);
        //zlog($cookie_key,1300);
        if ($set){
            
            setcookie('pkey',$cookie_key,time()+3600*48); //2 суток
            //zlog($cookie_key,1301);
        }
   }
   
   function mail_new_password_partner($key){
       if (($key!='')&&($key!==0)&&($key!=' ')){
          $q=mysql_query("SELECT email,username FROM `dbisoftik`.`s_partner`  WHERE `recovery_key`='".addslashes($key)."' LIMIT 1");

          if (mysql_num_rows($q)){
              $r=mysql_fetch_array($q);
              $pass=substr(md5('Плюшевая Борода одобряет '.time()),1,8);
              mysql_query("UPDATE `dbisoftik`.`s_partner` SET `recovery_key`='',password='".md5(FISH.md5($pass))."' WHERE `recovery_key`='".addslashes($key)."' LIMIT 1");
              $message='Ваш логин: '.$r['username'].'<br>Ваш новый пароль: <b>'.$pass.'</b>';
              sendmail('Новый пароль',$message,$r['email']);
              return true;
          }else{
              return false;
          }
       }else 
       return false;
   }
   
   
   function set_partner_balance($partner_id,$amount,$desc,$clientid){
       $q=mysql_query("SELECT `balance` FROM `dbisoftik`.`s_partner` WHERE id='".intval($partner_id)."' LIMIT 1");
       if ($row=mysql_fetch_assoc($q)){
            $newbalance=$row['balance']+$amount;
            mysql_query("UPDATE `dbisoftik`.`s_partner` SET `balance`='".intval($newbalance)."',chkey='".chkey($partner_id,$newbalance)."' WHERE id='".intval($partner_id)."'");
            mysql_query("INSERT into `dbisoftik`.`s_transaction` SET `name`='".addslashes($desc)."',`amount`='".intval($amount)."', pid='".$partner_id."', clientid='".$clientid."'");
       }
       
       
   }
   
?>
