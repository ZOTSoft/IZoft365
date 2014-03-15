<?
  
  

  function partnerchecksessionpassword(){
      
      if (isset($_SESSION['partner'])){         
          //if (isset($_SESSION['wplace']))
          $query=mysql_query("SELECT password FROM `dbisoftik`.`s_partner` WHERE id=".$_SESSION['puserid'].' LIMIT 1'); 
          //echo "SELECT password FROM ".$from." WHERE id=".$_SESSION['userid'].' LIMIT 1'; die;
          echo mysql_error();
          //die;
          
          if (mysql_numrows($query)){
            $row=mysql_result($query,0);
            if (md5($row)!=$_SESSION['ppassword']){
                header("Location: /partner/login.php?err=1");
                die;
            }else{
                
                mysql_query("UPDATE `dbisoftik`.`s_partner` SET last_action=".time().", isonline=1 WHERE id=".$_SESSION['puserid']);
                
            }
          }else{
          header("Location: /partner/login.php?err=2");
          die;
          }
      }else{
        header("Location: /partner/login.php?nosession");
        die;
      }
  }
  
?>