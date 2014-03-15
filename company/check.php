<?
  include($_SERVER['DOCUMENT_ROOT'].'/company/mysql_connect.php');
  

  function checksessionpassword(){
      
      if (isset($_SESSION['userid'])){
          $checkid=$_SESSION['userid'];
          if (isset($_SESSION['main'])){
              $from="`".$_SESSION['base']."`.`s_employee`";
          }elseif(isset($_SESSION['wplace'])){
              $checkid=$_SESSION['wid'];
              $from="`".$_SESSION['base']."`.`t_workplace`";
          }elseif(isset($_SESSION['urv'])){
              $from="`".$_SESSION['base']."`.`s_pointurv`";
          }elseif(isset($_SESSION['point'])){
              $from="`".$_SESSION['base']."`.`s_automated_point`";
          }elseif(isset($_SESSION['admin'])){
              $from='`dbisoftik`.`s_accounts`';
          }
          
          //if (isset($_SESSION['wplace']))
          
          
          $query=mysql_query("SELECT password FROM ".$from." WHERE id=".$checkid.' LIMIT 1'); 
          //echo "SELECT password FROM ".$from." WHERE id=".$_SESSION['userid'].' LIMIT 1'; die;
          echo mysql_error();
          //die;
          
          if (mysql_numrows($query)){
            $row=mysql_result($query,0);
            //echo md5($row).' '.$_SESSION['password']; die;
            if (md5($row)!=$_SESSION['password']){
                
                header("Location: /login.php?err=1");
                die;
            }else{
                
                mysql_query("UPDATE ".$from." SET last_action=".time().", isonline=1 WHERE id=".$_SESSION['userid']);
                
            }
          }else{
          header("Location: /login.php?err=2");
          die;
          }
      }else{
        header("Location: /login.php?nosession");
        die;
      }
  }
  
  define('FISH','13f31e2bc4948db0'); 
?>