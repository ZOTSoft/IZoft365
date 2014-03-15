<?PHP 
header("Content-Type: text/html; charset=utf-8");
session_start();

include('../company/mysql_connect.php');
include('partner_check.php');
include('../company/core.php');

partnerchecksessionpassword();

include('../company/functions.php');
include('templates.php');
    if (!(isset($_SESSION['partner']))){
        header("Location: /partner/login.php");
        die;
    }


?><!DOCTYPE html>
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <title>Режим Партнёра</title>
    <link rel="stylesheet" type="text/css" href="/company/css/print.css" media="print" />
    <link rel="stylesheet" type="text/css" href="/company/css/my.css">
    <link rel="stylesheet" type="text/css" href="/company/mtree/css_tree/css_tree.css">
    <link rel="stylesheet" type="text/css" href="/company/mtree/css_tree/tablecss.css">
    <script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/company/mtree/mytreeview.js"></script>
    <script type="text/javascript" src="/company/js/jquery.form.min.js"></script>
    <script type="text/javascript" src="/company/js/init.js?12321"></script>
    <link rel="stylesheet" type="text/css" href="/company/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/company/bootstrap/css/bootstrap-datetimepicker.min.css">
    <script type="text/javascript" src="/company/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="/company/bootstrap/js/bootstrap-inputmask.min.js"></script>
    <script type="text/javascript" src="/company/js/bootbox.min.js"></script>
    <script type="text/javascript" src="/company/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    <script>
        const version=<?=VERSION?>;
    </script>
</head>
<body>
<div class="bb-alert alert alert-info" style="display:none;">
        <span></span>
    </div>
<div class="bb-alert alert alert-danger" style="display:none;">
        <span></span>
    </div>
    
<div class="container-loader" id="loading">
  <div class="round">
  </div>
      <div class='l'>
      Загрузка...
      </div>
</div>
<table class="tab">
    <tr>
        <td class="tab_header" colspan="2" style="background: #47A447;">
            
            <div class="tab_logout">
            <? 
                echo '<a href="/partner/login.php?do=logout" class="btn btn-danger btn-lg"><i class="glyphicon glyphicon-off"></i> Выход</a><span>Вы вошли как <b>'.$_SESSION['pfio'].'('.$_SESSION['puser'].')</b></span>';
            ?> </div>
            <ul class="menu">
                <li onclick="clickto('my_company')" class="tab_desctop"><a href="#">Мои компании</a></li>
               <li onclick="show_balance()"><a href="javascript:void(0)">Баланс</a></li>
                <li onclick="show_feedback()"><a href="javascript:void(0)">Обратная связь</a></li>                  
            </ul>
        </td>
    </tr>
    <tr>
        <td class="lefttd">
            <div class="td_logo"><img src="/company/i/logo.png" alt=""></div>
            <div class="td_links">

                <div class="s_links" id="tab_clients" style="display: none;">
                   
                   <a href="#" onclick="show_tree('s_employee')">Сотрудники</a>
                </div>
            </div>
        </td>
        <td class="righttd">
        <ul class="nav nav-tabs nav-tabs-success" id="zottabs">
          <li class="active"><a href="#my_company" data-toggle="tab">Мои компании</a></li>
        </ul>
        <div class="tab-content" id="zotcontent" >
            <div class="tab-pane fade in active" id="my_company">
                <div class="toolbar" style="padding: 9px 0 9px 11px;">
                    <h4>Мои компании</h4>
                </div>     
                   <? $query_client=mysql_query("SELECT s_accounts.id,`db`,`clientid`, s_accounts.username, s_accounts.username, s_accounts.email, s_accounts.phone FROM `dbisoftik`.t_partner
                                            LEFT join `dbisoftik`.s_accounts on `dbisoftik`.t_partner.clientid=`dbisoftik`.s_accounts.id 
                                         WHERE t_partner.partnerid=".$_SESSION['puserid']);
                                         
                                         
                  $query_p=mysql_query("SELECT * FROM `dbisoftik`.`prices`");
            $prices=array();
            while($rowp=mysql_fetch_assoc($query_p)){
                $prices[$rowp['type']]=$rowp['price'];
            }
                  echo '<table width="100%" class="table">
                            <tr>
                                <th  width="50">#</th>
                                <th width="200">Компания</th>
                                <th width="200">Телефон</th>
                                <th>Емейл</th>
                            </tr>
                  ';
                  $i=1;
                   while($client=mysql_fetch_assoc($query_client)){
                        echo '<tr class="active"><td>'.$i.'</td>
                                <td><a href="#" onclick="showclient(this)" style="border-bottom:1px dashed #428bca">'.$client['username'].'</a></td>
                                <td>'.$client['phone'].'</td>
                                <td>'.$client['email'].'</td>
                              </tr>
                              <tr style="display:none" class="showclient success"><td colspan="4">
                              ';
                        $i++;
                        
                     
$base=$client['db'];
$acid=$client['clientid'];



echo '          
          <p class="tranz">';
                            
                            echo '<table class="melkiynubas">
                                <tr>
                                    <td>Объект</td>
                                    <td>Статус</td>
                                    <td>Окончание</td>
                                    <td>Цена/мес.</td>
                                    <td>Действие</td>
                                </tr>';
                            $itogo=0;
                            $query=mysql_query("SELECT * FROM `".$base."`.`s_automated_point`");
                            while($row=mysql_fetch_assoc($query)){
                                $query2=mysql_query("SELECT * FROM `".$base."`.`t_workplace` WHERE apid='".$row['id']."'");
                                $price=$prices['automated_point'];
                                $itogo+=$price;
                                if ($row['expiration_date']!=''){
                                    $exp_date=date('d.m.Y',strtotime('+1 month',strtotime($row['expiration_date'])));
                                }else{
                                    $exp_date=date('d.m.Y',strtotime('+1 month',time()));
                                }
                                
                                $remain = strtotime($row['expiration_date']) - time();
                                $days = floor($remain/86400);
                                $str='';
                                if($days > 0) $str=' (осталось '.$days.' '.morph($days,'день','дня','дней').')';
                                
                                //$exp_date=date('d.m.Y',strtotime('+1 month',strtotime($row['expirate_date'])));
                                echo '<tr id="pay'.$row['id'].'_'.$client['id'].'"><td><i class="glyphicon glyphicon-home" style="color:#FF740A"></i> '.$row['name']. '</td><td>'.($row['status']==1?'<i class="glyphicon glyphicon-ok-circle" style="color:green"></i>':'<i class="glyphicon glyphicon-remove-circle" style="color:red"></i>').'</td><td><span class="expdate">'.($row['expiration_date']!=''?date('d.m.Y',strtotime($row['expiration_date'])):'-').$str.'</span></td><td>'.$price.'</a><td>
                                
                                        
                                        <a href="#" onclick=\'partner_pay("Торговая точка","'.$acid.'","'.$row['name'].'","'.$row['id'].'","'.$price.'","'.$exp_date.'","pay'.$row['id'].'_'.$client['id'].'"); return false;\'><i class="glyphicon glyphicon-usd"></i></a> </td></tr>';
                                if (mysql_num_rows($query2)){
                                    while($row2=mysql_fetch_assoc($query2)){ 
                                        
                                        $price=$prices['workplace'];
                                        $itogo+=$prices['workplace'];
                                        if ($row2['expiration_date']!=''){
                                            $exp_date=date('d.m.Y',strtotime('+1 month',strtotime($row2['expiration_date'])));
                                        }else{
                                            $exp_date=date('d.m.Y',strtotime('+1 month',time()));
                                        }
                                        
                                        $remain = strtotime($row2['expiration_date']) - time();
                                        $days = floor($remain/86400);
                                        $str='';
                                        if($days > 0) $str=' (осталось '.$days.' '.morph($days,'день','дня','дней').')';
                                        
                                        $exp_date=$row['expiration_date'];
                                        echo '<tr id="zpay'.$row2['id'].'_'.$client['id'].'"><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="glyphicon glyphicon-user" style="color:#617C9C"></i> '.$row2['name']. '</td><td>'.($row2['status']==1?'<i class="glyphicon glyphicon-ok-circle" style="color:green"></i>':'<i class="glyphicon glyphicon-remove-circle" style="color:red"></i>').'</td><td><span class="expdate">'.($row2['expiration_date']!=''?date('d.m.Y',strtotime($row2['expiration_date'])):'-').$str.'</span></td><td>'.$price.'</a><td>
                                        
                                        
                                        <a href="#" onclick=\'partner_pay("Рабочее место","'.$acid.'","'.$row2['name'].'","'.$row2['id'].'","'.$price.'","'.$exp_date.'","zpay'.$row2['id'].'_'.$client['id'].'"); return false;\'><i class="glyphicon glyphicon-usd"></i></a> 
                                    </td></tr>';                              
                                    }                           
                                }                    
                            }
                            //<a href="#"><i class="glyphicon glyphicon-off" style="color:green"></i></a> <a href="#" onclick="payall()">Оплатить всё</a>
                            echo '<tr><td colspan="3"><b>Итого к оплате:</b></td><td><b>'.$itogo.'</b></td><td></td></tr>';
                            
                            
                            echo '</table>

          
        </td></tr>';
                   }
        echo '</table>';
?>
              <br /><br /><br /><br /><br /><br /><br /><br />    
            </div>
        </td>
    </tr>
</table>
<!--Див со всеми окнами -->
<div id="windows"></div>
<!--Див со всеми диалогами -->
<div id="dialogs"></div>
<!--Окно удаления -->

<div class="contactinfo2">Наши телефоны:<br>Сотовый: <span>8-701-111-97-23</span><br>Городской: <span>8-727-3-27-01-74</span></div>


</body>
</html>