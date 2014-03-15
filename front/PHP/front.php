<?php
    session_start();
    include('../../company/check.php');
    include('../../company/functions.php');
    checksessionpassword();    
    if (!isset($_SESSION['point'])){
        header("Location: /login.php");
        die;
}

$logClass=array();
$logClass[1]='Создание';
$logClass[2]='Изменение';
$logClass[3]='Просмотр';
$logClass[4]='Отказ';
$logClass[5]='Удаление позиции в счете';
$logClass[6]='Разблокировка счета';
$logClass[7]='Оплата счета';
$logClass[8]='Печать счета на оплату';
$logClass[9]='Печать подзаказника';
$logClass[10]='Начисленно баллов';
$logClass[11]='Снято баллов';
$logClass[12]='Печать счета об оплате';
$logClass[13]='Открытие смены';
$logClass[14]='Закрытие смены';
$logClass[15]='Вход в интерфейс';
$logClass[16]='Выход из интерфеса';
$logClass[17]='Ввод пароля';
$logClass[18]='Удаление';
$logClass[19]='Возврат';
$logClass[20]='Регистрация чека';
$logClass[21]='Смена сотрудника';




function logmsg($index){
    global $logClass;
    return $logClass[$index];  
}

include('errorsPHP.php');

include('frontFunctions.php');
//include('fitnesPHP.php');
//$g_link=mysql_connect('localhost',$_SESSION['base_user'],$_SESSION['base_password']);

//mysql_select_db( $_SESSION['base'], $g_link);
//mysql_query("set names 'utf8'");
include('../../company/mysql.php');


if (isset($_SESSION['timezone'])){ 
    if (!empty($_SESSION['timezone'])){
        date_default_timezone_set($_SESSION['timezone']); 
        mysql_query("SET `time_zone` = '".date('P')."'"); 
    }
//        mysql_query("SET time_zone = '+06:00'"); 
//        SET time_zone = '+03:00'
    }
//mysql_query("SET time_zone = '+06:00'");
 
if (isset($_GET['ftype'])){
    $actionScript=$_GET['ftype'];
}else{
    $actionScript=(ISSET($_POST['actionScript']))?$_POST['actionScript']:'';
}
switch ($actionScript){
   
    case 'refuse':{
        if (isset($_POST['orderid'])){
            $orderid=$_POST['orderid'];
        }else{
           echo json_encode(array('rescode'=>  errcode(2),'resmsg'=>  errmsg(2)));die;
        }
        if (isset($_POST['action'])){
            $action=$_POST['action'];
        }else{
           echo json_encode(array('rescode'=>  errcode(3),'resmsg'=>  errmsg(3)));die;
        }
        if (isset($_POST['orderRefuse'])){
            $orderRow=$_POST['orderRefuse'];
            if (!isset($orderRow['id'])){
                echo json_encode(array('rescode'=>  errcode(8),'resmsg'=>  errmsg(8)));die;
            }               
            if (!isset($orderRow['printer'])){
                echo json_encode(array('rescode'=>  errcode(9),'resmsg'=>  errmsg(9)));die;
            }
            if (!isset($orderRow['price'])){
                echo json_encode(array('rescode'=>  errcode(10),'resmsg'=>  errmsg(10)));die;
            }
            if (!isset($orderRow['note'])){
                $orderRow['note']='';
            }

        }else{
           echo json_encode(array('rescode'=>  errcode(4),'resmsg'=>  errmsg(4)));die;
        }
        if (isset($_POST['pwdRefuse'])){
             $pwd=md5(FISH.md5($_POST['pwdRefuse']));
        }else{
           echo json_encode(array('rescode'=>  errcode(5),'resmsg'=>  errmsg(5)));die;
        }
        if (isset($_POST['count'])){
            $count=$_POST['count']*-1;
        }else{
           echo json_encode(array('rescode'=>  errcode(6),'resmsg'=>  errmsg(6)));die;
        }
        if (isset($_POST['note'])){
            $order['note']=$_POST['note'];
        }else{
           $order['note']='';
        }  
        
        
         $resTrBegin=false;
         $resTrBegin=mysql_query('START TRANSACTION');
         if ($resTrBegin) {
                refuse($orderid,$orderRow,$pwd,$count,$action/*,$servicesum,$discountsum,$totalsum*/); 
               $resTrEnd=false; 
               $resTrEnd=mysql_query('COMMIT');
               if ($resTrEnd){
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xml'=>SubOrd($action, $orderid)));die;
               }else{
                    echo json_encode(array('rescode'=>  errcode(187),'resmsg'=>  errmsg(187))); die;
               }
         }else{
             echo json_encode(array('rescode'=>  errcode(188),'resmsg'=>  errmsg(188))); die;
         }
        
            
        
        
    break;
    }
    
    case 'loadMenu':{
        if ($_SESSION['doNotUseMenuDesign']==0){
            $sql="SELECT m.id,m.parentid,m.isgroup,i.name,m.menuid,m.itemid,m.printer,m.sortid,ROUND(m.price,0) as price,IF(ISNULL(i.complex),0,i.complex) as complex,i.isservice as isservice FROM s_automated_point as p
            LEFT JOIN t_menu_items as m on m.menuid=p.menuid
            LEFT JOIN s_items as i ON i.id = m.itemid
            LEFT JOIN d_stop_list as sl on sl.menu_item_id=m.id AND sl.apid=".$_SESSION['idap']."
            WHERE p.id=".$_SESSION['idap']." and ISNULL(sl.menu_item_id)
            ORDER BY isgroup desc,m.name";
            
            $result2 = mysql_query('select m.lastupdate from s_automated_point as ap
                                    LEFT JOIN s_menu as m on m.id=ap.menuid WHERE ap.id='.$_SESSION['idap']); 
            if ($result2){ 
                $rowRes=  mysql_fetch_assoc($result2);
                $_SESSION['lastupdate']=$rowRes['lastupdate'];
            }  
        }else if ($_SESSION['doNotUseMenuDesign']==1){
            $sql="SELECT
                       i.id, i.id as itemid,i.parentid as parentid,i.isgroup as isgroup,i. NAME as name,ROUND(i.price,0) as price,IF(ISNULL(i.complex),0,i.complex) as complex,i.i_printer as printer,i.isservice as isservice 
                FROM
                        s_items AS i
                LEFT JOIN d_stop_list as sl on sl.menu_item_id=i.id AND sl.apid=".$_SESSION['idap']."
                WHERE i.i_useInMenu=1
                ORDER BY i.isgroup desc,i.name
                ";
        }
        
        $result = mysql_query($sql);
        if ($result){
            $rows = array();
            while($r = mysql_fetch_assoc($result)) {
                $rows[] = $r;
            }
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'simplemenu'=>$rows)); die;
        }else{
           echo json_encode(array('rescode'=>  errcode(15),'resmsg'=>  errmsg(15))); die; 
        }
        
    break;
    }
    
    case 'loadTypePay':{
        $result = mysql_query("SELECT id,name FROM s_types_of_payment ORDER BY id");
        if ($result){            
             while($r = mysql_fetch_assoc($result)) {
                    $rows[] = $r;
             }
             echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'typePay'=>$rows)); die;
        }else{
          echo json_encode(array('rescode'=>  errcode(16),'resmsg'=>  errmsg(16))); die;  
        }  
    break;
    } 
    
     case 'loadConf':{
        $result = mysql_query("SELECT ap.servicepercent as defaultservicepercent,
                                ap.useservicepercent as serviceinfastfood,
                                ap.printsubordinfastfood as printsuborderintfastfood,
                                ap.zreportonclose as zreportonclose,
                                cl.name as defaultclientname,  
                                cl.id as defaultclientid,
                                ap.name as apname,
                                ap.usechoosetable,
                                ap.askCount as askCount,
                                ap.useChangePrice as useChangePrice,
                                ap.useURV as useURV,
                                ap.typeOfDiscountService as typeOfDiscountService,
                                ap.doNotUseMenuDesign as doNotUseMenuDesign,
                                ap.waiterCanTakePayment as waiterCanTakePayment,
                                ap.materialsSumMoreServiceSum as materialsSumMoreServiceSum,
                                ap.blockZeroSale as blockZeroSale,
                                ap.searchInMenu as searchInMenu,
                                IF(ap.pwdDeleteFromOrder='' OR ISNULL(ap.pwdDeleteFromOrder),0,1) as pwdDeleteFromOrder,
                                ap.rememberAboutDiscount as rememberAboutDiscount,
                                ap.useBuyerDisplay as useBuyerDisplay,
                                ap.switchOffCompAfterClose as switchOffCompAfterClose,
                                ap.cashid as cashid,
                                ap.slipid as slipid,
                                ap.useFR as useFR,
                                ap.alwaysUseNote as alwaysUseNote,
                                ap.warehouseid as warehouseid,
                                ap.noSaldoButton as noSaldoButton,
                                ap.useLocation as useLocation
                                FROM s_automated_point as ap
                                LEFT JOIN s_clients as cl  on cl.id=ap.cashclientid
                                where ap.id=".$_SESSION['idap']);
        
        if (isset($_SESSION['wid'])){
            $result1 =  mysql_query('SELECT wp.name as wpname FROM t_workplace as wp where id='.$_SESSION['wid']);
            if ($result1){
                $rows1 =  mysql_fetch_assoc($result1);
                
            }else{
               $rows1=array('wpname'=>'');
            } 
            
        }else{
           $rows1=array('wpname'=>''); 
        }
        
        if ($result){
             
             $rows =  mysql_fetch_assoc($result);
             $_SESSION['typeOfDiscountService']=$rows['typeOfDiscountService'];
             $_SESSION['doNotUseMenuDesign']=$rows['doNotUseMenuDesign'];
             
             $tmparray=checkChage();
             $idchange=$tmparray['idchange'];
             
             
             echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'config'=>$rows,'workplace'=>$rows1,'idchange'=>$idchange)); die;
        }else{
          echo json_encode(array('rescode'=>  errcode(156),'resmsg'=>  errmsg(156))); die;  
        }
    break;
    }
    
    case 'preLoadTable':{
                $useRoomQuery=  mysql_query('SELECT uselocation FROM s_automated_point WHERE id='.$_SESSION['idap']);
                if ($useRoomQuery){
                    $useRoomRow =  mysql_fetch_assoc($useRoomQuery);
                    if ($useRoomRow['uselocation']==1){                        
                        $result= mysql_query('SELECT loc.id as id,loc.name name FROM s_location as loc
                            WHERE loc.pointid='.$_SESSION['idap']);
                        $rows = array();
                        while($r = mysql_fetch_assoc($result)) {
                            $rows[] = $r;                   
                        };
                        $result = mysql_query("SELECT IF (
                                                servicepercent <>- 1,
                                                CONCAT(
                                                        NAME,
                                                        ' (',
                                                        servicepercent,
                                                        '%)'
                                                ),
                                                NAME
                                        ) AS name,
                                        servicepercent,
                                        id,
                                        locationid
                                        FROM
                                                s_objects
                                        ORDER BY
                                                id,NAME");
                        $rows2 = array();
                        while($r = mysql_fetch_assoc($result)) {
                            $rows2[] = $r;                   
                        }
                        echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'loc'=>$rows,'obj'=>$rows2,'location'=>1)); die; 
                    }else{
                        $result = mysql_query("SELECT
                                        IF (
                                                servicepercent <>- 1,
                                                CONCAT(
                                                        NAME,
                                                        ' (',
                                                        servicepercent,
                                                        '%)'
                                                ),
                                                NAME
                                        ) AS name,
                                        servicepercent,
                                        id,
                                        locationid
                                        FROM
                                                s_objects
                                        ORDER BY
                                                id,NAME");
                        $rows = array();
                        while($r = mysql_fetch_assoc($result)) {
                            $rows[] = $r;                   
                        };
                        echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'obj'=>$rows,'location'=>0)); die;
                    }
                }else{
                   echo json_encode(array('rescode'=>  errcode(122),'resmsg'=>  errmsg(122))); die; 
                }            
                if (!$result){
                  echo json_encode(array('rescode'=>  errcode(29),'resmsg'=>  errmsg(29))); die;  
                }
              
    break;
    }
    
    case 'selectEmployee':{   
                $tmparray=checkChage();
                $closeChange=$tmparray['closeChange'];
                
                if (isset($_POST['shtemp'])){
                    $sh=md5(FISH.md5($_POST['shtemp']));
                }else{
                   echo json_encode(array('rescode'=>  errcode(18),'resmsg'=>  errmsg(18)));die;
                }
                $sql="SELECT i.name as interfaceName,e.id as id,e.name as name ,i.value as interfaces FROM s_employee as e
                                LEFT JOIN t_employee_interface as t on e.id=t.employeeid
                                LEFT JOIN s_interfaces as i on t.rollid=i.id
                                LEFT JOIN t_employee_workplace as w on w.employeeid=e.id
                               where e.password='".$sh."' and e.isuser=1 and i.value>-1";
                if (isset($_SESSION['wid'])/*&&isset($_SESSION['idap'])*/){
                    $sql.=" and wpid=".$_SESSION['wid'];
                };
                $sql.=" ORDER BY interfaces";
                $result = mysql_query($sql);
                if ($result){
                    if (mysql_num_rows($result)>0){                        
                        $tmparray=changeInformation();
                        $infoChange=$tmparray['infoChange'];
                        $rows = array();
                        $i=0;
                        $haveCashier=FALSE;
                        while($r = mysql_fetch_assoc($result)) {
                            $rows[] = $r;
                            if ($rows[$i]['interfaces']==2){
                                $haveCashier=TRUE;
                            }
                            $i++;
                        }
                        if ($closeChange==1&&!$haveCashier){
                            echo json_encode(array('rescode'=>  errcode(13),'resmsg'=>  errmsg(13))); die;
                        }
                        
                        $_SESSION['employeeid']=$rows[0]['id'];  
                        if (mysql_num_rows($result)==1){ 
                            $_SESSION['interfaces']=$rows[0]['interfaces'];
                             
                            $sessionCheckResult=  checkEmployeeSession();
                           
                            if ($sessionCheckResult==0){
                                updateSessionEmp(1);
                                
                            }else if ($sessionCheckResult==1){
                                echo json_encode(array('rescode'=>  errcode(114),'resmsg'=>  errmsg(114))); die;
                            }
                          }
                        
                        echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'emp'=>$rows,'closeChange'=>$closeChange,'infoChange'=>$infoChange)); die;
                    }else{
                       echo json_encode(array('rescode'=>  errcode(91),'resmsg'=>  errmsg(91),'closeChange'=>$closeChange)); die;
                    }
                } else{
                  echo json_encode(array('rescode'=>  errcode(17),'resmsg'=>  errmsg(17))); die;  
                } 
        
    break;
    }
    
    
    case 'showOrders':{
            
            if (isset($_POST['action'])){
               $action=$_POST['action']; 
            }else{
                echo json_encode(array('rescode'=>  errcode(3),'resmsg'=>  errmsg(3))); die;
            };
            
            if (isset($_POST['filter'])){
               $filter=$_POST['filter']; 
            }else{
               $filter='nopay';
            };
            
            $tmparray=checkChage();
            $closeChange=$tmparray['closeChange'];
            $idchange=$tmparray['idchange'];
            
           
            
        switch ($action) {
            case 'cashierOrders':
                switch ($filter) {
                case 'pay':
                    $filter=' and d.closed=1 ';
                    break;
                case 'nopay':
                    $filter=' and d.closed=0 ';
                break;
                case 'all':
                    $filter=' ';
                break;
              }         
              /*if(d.closed=0,"Открыт","Закрыт") as st,*/
              if (isset($_POST['pageCount'])){
                  $lim=$_POST['pageCount'];
              }else{
                   echo json_encode(array('rescode'=> 1 ,'resmsg'=>  'PHP Error!')); die; 
              }
              
              if ($lim<0){
                die;
              }
              
              $pn=round($lim/50)+1;
              
              
              $query = mysql_query('SELECT count(d.idout) as count FROM d_order as d where d.changeid='.$idchange.' '.$filter);
              $r =  mysql_fetch_assoc($query);
              
              
              $sql = 'Select d.printed as printed,d.closed as closed, d.id as id,d.idout as visibleid, if(d.closed=1,"Оплачен",if(d.printed=0,"Открыт","Распечатан")) as pr, if (ISNULL(pt.name),"",pt.name) as payname,if (ISNULL(o.name),"",o.name) as tablename,d.totalsum as dsum, e.name as empname,p.name as partname 
                             From d_order as d 
                             Left Join s_objects as o on d.objectid=o.id 
                             Left Join s_clients as p on p.id=d.clientid 
                             Left Join s_employee as e on e.id=d.employeeid 
                             LEFT JOIN s_types_of_payment as pt on d.paymentid = pt.id 
                             Where changeid=' . $idchange .'  '.$filter.
                            'ORDER BY d.id DESC LIMIT '.$lim.',50';    
                $query = mysql_query($sql);
                if ($query) {
                    if (mysql_num_rows($query)>0){
                        $i = 0;
                        while ($row = mysql_fetch_assoc($query)) {                       
                            $data[$i]['lock'] = $row['pr'];
    //                        $data[$i]['st'] = $row['st'];
                            $data[$i]['visibleid'] = $row['visibleid'];
                            $data[$i]['payname'] = $row['payname'];
                            $data[$i]['tablename'] = $row['tablename'];
                            $data[$i]['dsum'] = (float)$row['dsum'];
                            $data[$i]['partname'] = $row['partname'];
                            $data[$i]['employeename'] = $row['empname'];
                            $data[$i]['id'] = $row['id'];
                            $data[$i]['closed'] = $row['closed'];
                            $data[$i]['printed'] = $row['printed'];
                            $i++;
                        }
                    }else{
                        echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'pageCount'=>1,'pn'=>1)); die; 
                    }
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'rows'=>$data,'pageCount'=>ceil($r['count']/50),'pn'=>$pn)); die;
                }else{
                   echo json_encode(array('rescode'=>  errcode(20),'resmsg'=>  errmsg(20))); die; 
                };

                break;
            case 'officiantOrders':    
                
                if (isset($_POST['employeeid'])){
                    $empid=$_POST['employeeid']; 
                 }else{
                     echo json_encode(array('rescode'=>  errcode(19),'resmsg'=>  errmsg(19))); die;
                 };                
                $sql = 'Select p.name as partname, d.id as id,d.idout as visibleid,if(d.printed=0,"Открыт","Распечатан") as pr,if (ISNULL(o.name),"",o.name) as tablename,d.totalsum as dsum, e.name as empname From d_order as d 
                        Left Join s_objects as o on d.objectid=o.id 
                        Left Join s_clients as p on d.clientid=p.id 
                        Left Join s_employee as e on d.employeeid=e.id 
                        Where changeid=' . $idchange . ' and 
                        d.employeeid=' . $_SESSION['employeeid'] . ' and d.closed=0  
                        ORDER BY d.id ';
                
               // echo $sql;
                $query = mysql_query($sql);
                if ($query) {
                    $i = 0;
                    while ($row = mysql_fetch_assoc($query)) {
                        $data[$i]['num'] = $i+1;
                        $data[$i]['st'] = $row['pr'];                        
                        $data[$i]['visibleid'] = $row['visibleid'];
                        $data[$i]['empname'] = $row['empname'];
                        $data[$i]['tablename'] = $row['tablename'];
                        $data[$i]['dsum'] = (float)$row['dsum'];
                        $data[$i]['partname'] = $row['partname'];
                        $data[$i]['id'] = $row['id'];
                        $i++;
                    }
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'rows'=>$data)); die;
                }else{
                    echo json_encode(array('rescode'=>  errcode(34),'resmsg'=>  errmsg(34))); die;
                };
                break;
        }
        
    break;
    }
    
    
    case 'changeOpenClose':{
         if (isset($_POST['action'])){
               $action=$_POST['action']; 
            }else{
               echo json_encode(array('rescode'=>  errcode(3),'resmsg'=>  errmsg(3))); die;
            };
//            if (isset($_POST['employeeid'])){
//               $employeeid=$_POST['employeeid']; 
//               checkEmployee($employeeid);
//            }else{
//              echo json_encode(array('rescode'=>  errcode(19),'resmsg'=>  errmsg(19))); die;
//            };        
        
        $divChange=divChangeWorkplace();
        
        if ($divChange==0){
            $sqlchange =  mysql_query('select id,closed from d_changes where idautomated_point='.$_SESSION['idap'].' order by id desc limit 1');
        }else{
            $sqlchange =  mysql_query('select id,closed from d_changes where idautomated_point='.$_SESSION['idap'].' and idworkplace='.$_SESSION['wid'].' order by id desc limit 1');
        }
            if (mysql_num_rows($sqlchange)>0){
               $tmparray = mysql_fetch_assoc($sqlchange);
               $idchange=$tmparray['id'];
               $closed=$tmparray['closed'];
            }else{
               $idchange=0;
               $closed=1;
            }            
            switch ($action){
                case 'open':
                     if (($closed==1)){  
                                    if ($divChange==1){
                                        $sql = 'Insert Into d_changes (employeeid,idautomated_point,idworkplace,closed) Values('; 
                                    }else{
                                        $sql = 'Insert Into d_changes (employeeid,idautomated_point,closed) Values('; 
                                    }
                                   
                                    $sql .= '"'.$_SESSION['employeeid'].'",';
                                    $sql .= '"'.$_SESSION['idap'].'",';
                                    if ($divChange==1){
                                        $sql .= '"'.$_SESSION['wid'].'",';    
                                    }
                                    $sql .= '0)';
                                    $result=mysql_query($sql);
                                    $tmparray=changeInformation();
                                    $infoChange=$tmparray['infoChange'];
                                    if ($result){ 
                                        $lastid =  mysql_insert_id();
                                        
                                        if ($divChange==1){
                                            $query=  mysql_query('SELECT MAX(id) as id FROM d_changes WHERE idautomated_point='.$_SESSION['idap'].' and idworkplace='.$_SESSION['wid']);
                                        }else{
                                            $query=  mysql_query('SELECT MAX(id) as id FROM d_changes WHERE idautomated_point='.$_SESSION['idap']); 
                                        }
                                        
                                        $row=  mysql_fetch_assoc($query);
                                        
                                        $logStr="Открытие смены № ".$row['id'];
                                        addlog($logStr,13);
                                        
                                        $tmparray=checkChage();
                                        echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'closeChange'=>0,'infoChange'=>$infoChange,'idchange'=>$tmparray['idchange'])); die; 
                                    }else{
                                       echo json_encode(array('rescode'=>  errcode(23),'resmsg'=>  errmsg(23))); die; 
                                    }
                        }else
                        {
                            echo json_encode(array('rescode'=>  errcode(21),'resmsg'=>  errmsg(21))); die;
                        };
                break;
                case 'close':
                     if (($closed==0)) {

                            $tmp =  mysql_query('select COUNT(id) as count from d_order where closed<>1 and changeid='.$idchange);
                            $haveorders = mysql_fetch_assoc($tmp);

                            if ($haveorders['count']==0){
                                $sql = 'Update d_changes SET ';
                                            $sql .= 'dtclosed=NOW(),';
                                            $sql .= 'closed=1 ';
                                            $sql .= 'WHERE id='.$idchange; 
                                $result=mysql_query($sql);
                                if ($result){ 
                                        /////////////vlad
                                        $logStr="Закрытие смены №".$idchange;
                                        addlog($logStr,14);
                                        $tmp =  mysql_query('select zreportonclose from s_automated_point where id='.$_SESSION['idap']);
                                        $haveorders = mysql_fetch_assoc($tmp);
                                        $xmlChange='';
                                        if ($haveorders['zreportonclose']==1)
                                            $xmlChange='<Header><order>Zreport</order></Header>';
                                        /////////////////
                                        $tmparray=changeInformation();
                                        $infoChange=$tmparray['infoChange'];
                                        $tmparray=checkChage();
                                        echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'closeChange'=>1,'zReportOnClose'=>$haveorders['zreportonclose'],'infoChange'=>$infoChange,'idchange'=>$tmparray['idchange'])); die; 
                                }else{
                                       echo json_encode(array('rescode'=>  errcode(23),'resmsg'=>  errmsg(23))); die; 
                                 }
                            }else{        
                                 echo json_encode(array('rescode'=>  errcode(24),'resmsg'=>  errmsg(24))); die;
                            };    
                        }else{
                          echo json_encode(array('rescode'=>  errcode(22),'resmsg'=>  errmsg(22))); die;  
                        }
                break;

                deafault:
                  echo json_encode(array('rescode'=>  errcode(1),'resmsg'=>  errmsg(1))); die;  
                break;
            }
    break;
    }
    
    
    
    case 'chooseTable':{
        
        if (isset($_POST['event'])){
               $ChooseTable=$_POST['event']; 
            }else{
               echo json_encode(array('rescode'=>  errcode(3),'resmsg'=>  errmsg(3))); die;
            };
        $rooms=false;        
        switch($ChooseTable){
            case 'Clients':
                if (isset($_POST['shtrih'])){
                       $shtrih=$_POST['shtrih']; 
                }else{
                    echo json_encode(array('rescode'=>  errcode(111),'resmsg'=>  errmsg(111))); die;
                };
                if (isset($_POST['clid'])){
                       $clid=$_POST['clid']; 
                }else{
                    echo json_encode(array('rescode'=>  errcode(111),'resmsg'=>  errmsg(111))); die;
                };
                $sql="SELECT
                                            IF (ISNULL(ds.id), 0, ds.id) AS discountid,
                                             c.id AS clientid,
                                             CONCAT(IF (SUM(IFNULL(bl.points,0))=0,'','('),
                                                    IF (SUM(IFNULL(bl.points,0))=0,'',SUM(IFNULL(bl.points,0))),
                                                    IF (SUM(IFNULL(bl.points,0))=0,'',')'),
                                                    c. NAME,
                                            IF (
                                                    ISNULL(ds. NAME),
                                                    '',
                                                    CONCAT('(', ds. NAME, ')')
                                            )
                                            ) AS clientname,
                                             ROUND(

                                                    IF (
                                                            ISNULL(ds.percentvalue),
                                                            0,
                                                            ds.percentvalue
                                                    )
                                            ) AS discountpercent,
                                            IF (
                                                    ISNULL(ds.usediscountsincafe) ,- 1,
                                                    ds.usediscountsincafe
                                            ) AS usediscountsincafe,

                                            IF (
                                                    ISNULL(ds.usediscountsinfastfood) ,- 1,
                                                    ds.usediscountsinfastfood
                                            ) AS usediscountsinfastfood,

                                            IF (
                                                    ISNULL(ds.usegiftsincafe) ,- 1,
                                                    ds.usegiftsincafe
                                            ) AS usegiftsincafe,
                                            IF (
                                                    ISNULL(ds.usegiftsinfastfood) ,- 1,
                                                    ds.usegiftsinfastfood
                                            ) AS usegiftsinfastfood,
                                            IF (
                                                    ISNULL(ds.useserviceincafe) ,- 1,
                                                    ds.useserviceincafe
                                            ) AS useserviceincafe,
                                            IF (
                                                    ISNULL(ds.useserviceinfastfood) ,- 1,
                                                    ds.useserviceinfastfood
                                            ) AS useserviceinfastfood,                                           
                                             ROUND(
                                                    IF (
                                                            ISNULL(ds.servicepercent) ,- 1,
                                                            ds.servicepercent
                                                    )
                                            ) AS servicepercent,
                                            SUM(IFNULL(bl.points,0)) as balance
                                            FROM
                                                    s_clients AS c
                                            LEFT JOIN (
								SELECT d.id,d.name,d.idpartner,d.percentvalue,
											d.sumvalue,d.usediscountsincafe,
											d.usediscountsinfastfood,d.usegiftsincafe,
											d.usegiftsinfastfood,d.useserviceincafe,
											d.useserviceinfastfood,d.servicepercent,
											d.usebalanceincafe,d.usebalanceinfastfood,
                                                                                        dc.clientid as clientid
								FROM s_discount as d
								LEFT JOIN t_discount_ap as da on da.discountid=d.id
                                                                LEFT JOIN t_discount_clients as dc on dc.discountid=d.id
								WHERE da.apid=".$_SESSION['idap']."
							) AS ds ON ds.clientid = c.id or c.parentid=ds.clientid 																						
                                            LEFT JOIN d_balance AS bl ON c.id = bl.clientid
                                        WHERE c.isgroup<>1";
//                LEFT JOIN s_discount AS ds ON ds.id = dc.discountid 
                if ($shtrih!=''){
                    $sql.=" and shtrih='".addslashes($shtrih)."'";  
                }else if ($clid==''){
                    $sql.=" and c.parentid=0";
                };
                if ($clid!=''){
                    $sql.=" and c.id='".addslashes($clid)."'";  
                }
                $sql.=" GROUP BY c.id,ds.id 
                        ORDER BY ds.NAME"; 
                $result = mysql_query($sql);
            
                if (!$result){
                  echo json_encode(array('rescode'=>  errcode(28),'resmsg'=>  errmsg(28))); die;  
                } 
                
                
            break;
            case 'Tables':
                
//                $useRoomQuery=  mysql_query('SELECT uselocation FROM s_automated_point WHERE id='.$_SESSION['idap']);
//                $rooms=true;
//                if ($useRoomQuery){
//                    $useRoomRow =  mysql_fetch_assoc($useRoomQuery);
//                    if ($useRoomRow['uselocation']==1){                        
//                        $result= mysql_query('SELECT id,name FROM s_location WHERE pointid='.$_SESSION['idap']);
//                    }else{
//                        $result = mysql_query("SELECT
//                                        IF (
//                                                servicepercent <>- 1,
//                                                CONCAT(
//                                                        NAME,
//                                                        ' (',
//                                                        servicepercent,
//                                                        '%)'
//                                                ),
//                                                NAME
//                                        ) AS name,
//                                        servicepercent,
//                                        id
//                                        FROM
//                                                s_objects
//                                        ORDER BY
//                                                id,NAME");
//                    }
//                }else{
//                   echo json_encode(array('rescode'=>  errcode(122),'resmsg'=>  errmsg(122))); die; 
//                }            
//     
//                if (!$result){
//                  echo json_encode(array('rescode'=>  errcode(29),'resmsg'=>  errmsg(29))); die;  
//                }
            break;
            case 'Discount':
                $result = mysql_query("SELECT * FROM s_discount ORDER BY name");
                if (!$result){
                  echo json_encode(array('rescode'=>  errcode(30),'resmsg'=>  errmsg(30))); die;  
                }
            break;  
            case 'note':
                if (isset($_POST['itemid'])){
                    $itemid=$_POST['itemid']; 
                 }else{
                    echo json_encode(array('rescode'=>  errcode(32),'resmsg'=>  errmsg(32))); die;
                 };
                 
                $result =  mysql_query('select name from s_note where itemid='.addslashes($itemid).' or itemid=0 or itemid=-2 or itemid=(SELECT parentid FROM s_items WHERE id='.addslashes($itemid).') ORDER by itemid DESC');
                if (!$result){
                  echo json_encode(array('rescode'=>  errcode(31),'resmsg'=>  errmsg(31))); die;  
                }
            break;
            default:{
                echo json_encode(array('rescode'=>  errcode(33),'resmsg'=>  errmsg(33))); die; 
            break;
            }
        }
        if (mysql_num_rows($result)>0){
            $rows = array();
            while($r = mysql_fetch_assoc($result)) {
                $rows[] = $r;                   
            }
            if ($ChooseTable=='Tables'){
                if ($rooms){
                    if ($useRoomRow['uselocation']==1){                        
                        echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>$rows,'location'=>1)); die; 
                    }else{
                        echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>$rows,'location'=>0)); die;
                    }
                };
            }else{
                echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>$rows)); die;   
            }
        }else{
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>0)); die;
        };
    break;
    }
   
    case 'saveOrder':{
        
        if (isset($_POST['order'])){
               $order = $_POST['order'];               
                if (!isset($order['orderid'])){
                    echo json_encode(array('rescode'=>  errcode(41),'resmsg'=>  errmsg(41))); die;           
                }else{
                    if ($order['orderid']!=0){
                        checkOrder($order['orderid']);
                    }
                };
                if (!isset($order['printed'])){
                    echo json_encode(array('rescode'=>  errcode(44),'resmsg'=>  errmsg(44))); die;       
                };
                if (!isset($order['closed'])){
                    echo json_encode(array('rescode'=>  errcode(45),'resmsg'=>  errmsg(45))); die;           
                }else{
                };
                if (!isset($order['clientid'])){
                    echo json_encode(array('rescode'=>  errcode(46),'resmsg'=>  errmsg(46))); die;           
                }else{
                    checkClient($order['clientid']);
                };
                if (!isset($order['totalsum'])){
                    echo json_encode(array('rescode'=>  errcode(47),'resmsg'=>  errmsg(47))); die;           
                };
                if (!isset($order['employeeid'])){
                    echo json_encode(array('rescode'=>  errcode(48),'resmsg'=>  errmsg(48))); die;           
                }else{
//                    checkEmployee($order['employeeid']);
                };
                if (!isset($order['discountpercent'])){
                    echo json_encode(array('rescode'=>  errcode(49),'resmsg'=>  errmsg(49))); die;          
                };                            
                if (!isset($order['discountsum'])){
                     echo json_encode(array('rescode'=>  errcode(50),'resmsg'=>  errmsg(50))); die;
                };
                if (!isset($order['servicepercent'])){
                     echo json_encode(array('rescode'=>  errcode(51),'resmsg'=>  errmsg(51))); die;          
                };
                if (!isset($order['servicesum'])){
                    echo json_encode(array('rescode'=>  errcode(52),'resmsg'=>  errmsg(52))); die;           
                };
                if (!isset($order['guestscount'])){
                   $order['guestscount']=0;            
                };
                if (!isset($order['tableid'])){
                   $order['tableid']=0;            
                };
                if (!isset($order['discountid'])){
                   echo json_encode(array('rescode'=>  errcode(107),'resmsg'=>  errmsg(107))); die;             
                };
                if (!isset($order['interfaceid'])){
                   echo json_encode(array('rescode'=>  errcode(108),'resmsg'=>  errmsg(108))); die;           
                };  
            }else{
               echo json_encode(array('rescode'=>  errcode(35),'resmsg'=>  errmsg(35))); die;
            };  
        if (isset($_POST['ordertable'])){
               $ordertable=$_POST['ordertable']; 
               $ifaceParams=getIfaceParams();
               if ($ordertable!='empty'){                   
               foreach ($ordertable as $value) {                   
                    if (!isset($value['id'])){
                        echo json_encode(array('rescode'=>  errcode(53),'resmsg'=>  errmsg(53))); die;          
                    }else{
                        checkItem($value['id']);
                    };   
                   if (!isset($value['name'])){
                        echo json_encode(array('rescode'=>  errcode(54),'resmsg'=>  errmsg(54))); die;          
                   };
                    if (!isset($value['price'])){
                        echo json_encode(array('rescode'=>  errcode(55),'resmsg'=>  errmsg(55))); die;          
                    }else{
                        if ($ifaceParams['blockZeroSale']==1){
                            if ($value['price']==0){
                              echo json_encode(array('rescode'=>  errcode(171),'resmsg'=>  errmsg(171))); die;    
                            }
                        }
                    };
                    if (!isset($value['count'])){
                        echo json_encode(array('rescode'=>  errcode(56),'resmsg'=>  errmsg(56))); die;          
                    };
                    if (!isset($value['printer'])){
                        echo json_encode(array('rescode'=>  errcode(57),'resmsg'=>  errmsg(57))); die;          
                    };
                    if (!isset($value['summa'])){
                       echo json_encode(array('rescode'=>  errcode(58),'resmsg'=>  errmsg(58))); die;          
                    };
                    if (!isset($value['note'])){
                       $value['note']='';          
                    }; 
                }
               }           
            }else{
               echo json_encode(array('rescode'=>  errcode(36),'resmsg'=>  errmsg(36))); die;
            };
                          
        if (isset($_POST['printed'])){
               $printed = $_POST['printed'];
            }else{
               echo json_encode(array('rescode'=>  errcode(37),'resmsg'=>  errmsg(37))); die;
            };
              
        if (isset($_POST['closed'])){
               $closed = $_POST['closed'];
            }else{
               echo json_encode(array('rescode'=>  errcode(38),'resmsg'=>  errmsg(38))); die;
            };
        if (isset($_POST['sumfromclient'])){            
               $sumfromclient = $_POST['sumfromclient'];            
               if ($sumfromclient<$order['totalsum']&&$closed==1){
                  echo json_encode(array('rescode'=>  errcode(79),'resmsg'=>  errmsg(79))); die; 
               }
            }else{
               echo json_encode(array('rescode'=>  errcode(40),'resmsg'=>  errmsg(40))); die;
            };
        if (isset($_POST['interface'])){
//            $interface=$_POST['interface'];
//            checkInterface($interface);
            $interface=$_SESSION['interfaces'];
        }else{
            echo json_encode(array('rescode'=>  errcode(80),'resmsg'=>  errmsg(80))); die;
        }
        if ($closed==1){ 
            if (!isset($order['paymentid'])){
                echo json_encode(array('rescode'=>  errcode(43),'resmsg'=>  errmsg(43))); die;          
            };
        };
        $tmparray=checkChage();

        $idchange=$tmparray['idchange'];

        $closeChange=$tmparray['closeChange'];

        $_SESSION['clientid']=$order['clientid'];

        if ($closeChange==1){
            echo json_encode(array('rescode'=>  errcode(13),'resmsg'=>  errmsg(13))); die;
        };

        checkSumOrder($order, $ordertable);
        $xmlSubOrder=''; 
             
         //транзакция
         $resTrBegin=false;
         $resTrBegin=mysql_query('START TRANSACTION');
         if ($resTrBegin) {
                if (checkPrinted($order['orderid'])==0){   
                    $tmparray=doSaveOrder($order,$ordertable,$idchange,$interface);   
                    $tmparray['gifts']=0;
                    $tmparray['balans']=0;
                    $xmlSubOrder=$tmparray['xmlSub'];     
                }else{
                    $tmparray['orderid']=$order['orderid'];   
                    $tmparray['gifts']=0;
                    $tmparray['balans']=0;
                }

                $xmlOrder='';
                if ($printed==1&&$closed==0){                
                    $tmparray=doPrintOrder($tmparray['orderid']);    
                    $tmparray['gifts']=0;
                    $tmparray['balans']=0;
                    $xmlOrder=$tmparray['xmlOrd'];                
                };


                if ($closed==1){        
                    $tmparray=doPayOrder($tmparray['orderid'],$order['paymentid'],$sumfromclient);       
                    $xmlOrder=$tmparray['xmlOrd'];
                };
               $resTrEnd=false; 
               $resTrEnd=mysql_query('COMMIT');
               if ($resTrEnd){
                echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlSubOrder'=>$xmlSubOrder,'xmlOrder'=>$xmlOrder,'gifts'=>$tmparray['gifts'],'orderid'=>$tmparray['orderid'],
                    'balans'=>$tmparray['balans']));die; 
               }else{
                  echo json_encode(array('rescode'=>  errcode(185),'resmsg'=>  errmsg(185))); die;
               }
         }else{
             echo json_encode(array('rescode'=>  errcode(186),'resmsg'=>  errmsg(186))); die;
         }
    break;
    }
           
    case 'showOrder':{
            
        if (isset($_POST['orderid'])){
               $orderid = $_POST['orderid'];
               checkOrder($orderid);
        }else{
               echo json_encode(array('rescode'=>  errcode(2),'resmsg'=>  errmsg(2))); die;
        }; 
        
       if (isset($_POST['flag'])){
               $flag = $_POST['flag'];
        }else{
               echo json_encode(array('rescode'=>  errcode(184),'resmsg'=>  errmsg(184))); die;
        };
         
         
            $sql = "SELECT
                    p.id AS client,
                    p.name as clientname,
                    d.id AS id,
                    d.idout AS visibleid,
                    d.printed AS printed,
                    d.closed AS closed,
                    d.discountpercent AS discount,
                    d.servicepercent AS servicepercent,
                    d.servicesum AS servicesum,
                    d.guestcount AS guests,
                o.NAME AS tablename,
                o.id AS tableid,
                (d.totalsum) AS totalsum,
                e. NAME AS employeename,
                DATE_FORMAT(
                    d.creationdt,
                    '%d.%m.%y %H:%i:%S'
                ) AS dt,
                tp. NAME AS payment,
                p. NAME AS clientname,
                d.discountpercent AS discountpercent,
                d.discountsum AS discountsum
            FROM
                d_order AS d
            LEFT JOIN s_clients AS p ON d.clientid = p.id
            LEFT JOIN s_objects AS o ON d.objectid = o.id
            LEFT JOIN s_employee AS e ON d.employeeid = e.id
            LEFT JOIN s_types_of_payment AS tp ON d.paymentid = tp.id
            WHERE
                d.id = " . addslashes($orderid);
            
            $result = mysql_query($sql);
            if ($result) {
                $row = mysql_fetch_assoc($result);
                $orderarray[0]['orderid'] = $row['id'];
                $orderarray[0]['visibleid'] = $row['visibleid'];
                $orderarray[0]['tablename'] = $row['tablename'];
                $orderarray[0]['tableid'] = $row['tableid'];
                $orderarray[0]['discount'] = $row['discount'];
                $orderarray[0]['servicepercent'] = $row['servicepercent'];
                $orderarray[0]['servicesum'] = $row['servicesum'];
                $orderarray[0]['guests'] = $row['guests'];
                $orderarray[0]['totalsum'] = (float)$row['totalsum'];
                $orderarray[0]['employeename'] = $row['employeename'];
                $orderarray[0]['dt'] = $row['dt'];
                $orderarray[0]['closed'] = $row['closed'];
                $orderarray[0]['printed'] = $row['printed'];
                $orderarray[0]['client'] = $row['client'];
                $orderarray[0]['clientname'] = $row['clientname'];
                $orderarray[0]['discountpercent'] = $row['discountpercent'];
                $orderarray[0]['discountsum'] = $row['discountsum'];
                
                
                if ($flag=='return'){
                    $sql2=mysql_query("SELECT id FROM d_order WHERE parentid=".addslashes($orderid));
                    $stext="(t.orderid = " . addslashes($orderid);
                    while ($r2=  mysql_fetch_assoc($sql2)){
                        $stext.=" or t.orderid =".$r2['id'];
                    }
                    $stext.=")";
                }else{
                   $stext="(t.orderid = " . addslashes($orderid).")"; 
                }
//                t.orderid = " . addslashes($orderid) . "
                
                $sql="select * from (SELECT            
                                            t.note <> '',
                                            if (NOT ISNULL(t3.name),CONCAT(i.name,' (',t3.name,')'),i.name) AS foodname,
                                            
                                        t.price AS price,
                                        sum(t.quantity) AS quantity,
                                        sum(t.price * t.quantity) AS summa,
                                        t.printerid AS printer,
                                        t.itemid AS itemid,
                                        t.id as innerId,
                                        i.complex as complex,
                                        t.coocked as coocked
                                    FROM
                                        t_order AS t
                                    LEFT JOIN s_items AS i ON t.itemid = i.id
                                    LEFT JOIN (
							SELECT t.parentid as id,GROUP_CONCAT(i.name ORDER BY i.name SEPARATOR ',<br>') as name FROM t_order as t
							LEFT JOIN s_items as i on i.id=t.itemid
							WHERE t.orderid=" . addslashes($orderid) . " and t.parentid<>0
                                                            GROUP BY t.parentid
						) as t3 on t3.id=t.id
                                    WHERE
                                        ".$stext."  and t.parentid=0                                            
                                    GROUP BY
                                        foodname,
                                        t.itemid,
                                        t.price,
                                        t.note,
                                        t.coocked
                                    ORDER BY t.dt
                                ) as restable";
//                                where restable.quantity>0";
                $result = mysql_query($sql);
                if (mysql_num_rows($result)>0){
                    $i = 0;
                    while ($row1 = mysql_fetch_assoc($result)) {
                        $ordertable[$i]['visibleid'] = $i+1;                        
                        $ordertable[$i]['name'] = $row1['foodname'];
                        $ordertable[$i]['price'] = (float)$row1['price'];
                        $ordertable[$i]['count'] = (float)$row1['quantity'];
                        $ordertable[$i]['summa'] = (float)$row1['summa'];
                        $ordertable[$i]['printer'] = $row1['printer'];   
                        $ordertable[$i]['itemid'] = $row1['itemid'];
                        $ordertable[$i]['status'] = 'old';
                        $ordertable[$i]['note'] = '';
                        $ordertable[$i]['innerId'] = $row1['innerId'];
                        $ordertable[$i]['complex'] = $row1['complex'];
                        $ordertable[$i]['coocked'] = $row1['coocked'];
                        $i++;
                    }
                }else{
                   $ordertable='empty' ;
                }
                $logStr="Просмотр cчета №".$row['visibleid'];
                addlog($logStr,3);
               echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'order'=>  $orderarray,'ordertable'=>$ordertable)); die; 
            }else{
                echo json_encode(array('rescode'=>  errcode(66),'resmsg'=>  errmsg(66))); die;
            };
            
    break;
    }
    
    case 'unblockOrder':{
        
        if (isset($_POST['orderid'])){
               $orderid = $_POST['orderid'];
               checkOrder($orderid);
        }else{
               echo json_encode(array('rescode'=>  errcode(2),'resmsg'=>  errmsg(2))); die;
         }; 
       if (isset($_POST['pwd'])){
               $unlockpwd = md5(FISH.md5($_POST['pwd']));
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
         }; 

       $result=mysql_query("select id from s_automated_point where pwdorderunlock='".$unlockpwd."' and id=".$_SESSION['idap']);

       if ($result){           
            if (mysql_num_rows($result)>0){                
                $resultUnlock = mysql_query('update d_order set printed=0 where printed=1 and closed=0 and id='.$orderid);
                if ($resultUnlock){
                   $logStr="Разблокировка счета №".getShiftIdOrder($orderid);
                   addlog($logStr,6);
                   echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0))); die; 
                }
                else{
                   echo json_encode(array('rescode'=>  errcode(87),'resmsg'=>  errmsg(87))); die; 
                }
            }else{
               echo json_encode(array('rescode'=>  errcode(88),'resmsg'=>  errmsg(88))); die; 
            }
       }else{
          echo json_encode(array('rescode'=>  errcode(86),'resmsg'=>  errmsg(86))); die; 
       }
    break;
    }
    case 'dopwdclose':{ 
        if (isset($_POST['pwd'])){
            if ($_POST['pwd']!='')
               $pwd = md5(FISH.md5($_POST['pwd']));
            else{
                $pwd=$_POST['pwd'];
            } 
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
       
        if ($pwd==''){
            $sqlQuery=  mysql_query('SELECT pwdClose FROM s_automated_point WHERE id='.$_SESSION['idap']);
            if ($sqlQuery){
                $sqlRow=  mysql_fetch_assoc($sqlQuery);
                if (($sqlRow['pwdClose']=='')||$sqlRow['pwdClose']==NULL){
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
                }else{
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>1)); die;
                }
            }
        }
        $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdClose="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(93),'resmsg'=>  errmsg(93))); die;
        }

    break;
    }
    case 'dopwdexit':{ 
        if (isset($_POST['pwd'])){
            if ($_POST['pwd']!='')
               $pwd = md5(FISH.md5($_POST['pwd']));
            else{
               $pwd=$_POST['pwd'];
            } 
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
       
        if ($pwd==''){
            $sqlQuery=  mysql_query('SELECT pwdexit FROM s_automated_point WHERE id='.$_SESSION['idap']);
            if ($sqlQuery){
                $sqlRow=  mysql_fetch_assoc($sqlQuery);
                if (($sqlRow['pwdexit']=='')||$sqlRow['pwdexit']==NULL){
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
                }else{
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>1)); die;
                }
            }
        }
        $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdexit="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(93),'resmsg'=>  errmsg(93))); die;
        }
        
        
//        $pwdexit='';
//        if (isset($_POST['pwdexit'])){
//               $pwdexit=md5(FISH.md5($_POST['pwdexit']));              
//        }        
//        $result=mysql_query("select id from s_automated_point where pwdexit='".addslashes($pwdexit)."' and id=".$_SESSION['idap']);
//       if ($result){           
//            if (mysql_num_rows($result)>0){                                
//                echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0))); die;                 
//            }else{
//               echo json_encode(array('rescode'=>  errcode(93),'resmsg'=>  errmsg(93))); die; 
//            }
//       }else{
//          echo json_encode(array('rescode'=>  errcode(92),'resmsg'=>  errmsg(92))); die; 
//       }
    break;
    }
    
    case 'selectByShtrih':{
         if (isset($_POST['shtrih'])){
             $shtrih=$_POST['shtrih'];               
         }else{
             echo json_encode(array('rescode'=>  errcode(94),'resmsg'=>  errmsg(94))); die;  
         }
         
         $sql1="SELECT ap.doNotUseMenuDesign,ap.menuid FROM s_automated_point as ap WHERE ap.id=".$_SESSION['idap'];
         $query1=  mysql_query($sql1);
         $r1=  mysql_fetch_assoc($query1);
         
         if ($r1['doNotUseMenuDesign']==1){
             $sqlText="SELECT
                            i.id AS id,
                            i. name AS name,	
                            ROUND(i.price, 0) AS price,
                            i.weight AS weight
            FROM s_items as i
            LEFT JOIN s_shtrih as sh on sh.itemid=i.id
            WHERE i.mainShtrih='".addslashes($shtrih)."' OR sh.shtrih='".addslashes($shtrih)."' OR i.idlink='".addslashes($shtrih)."'";
         }else if ($r1['doNotUseMenuDesign']==0){
                 $sqlText="SELECT
                                i.id AS id,
                                i. name AS name,	
                                ROUND(tm .price, 0) AS price,
                                i.weight AS weight
                FROM s_items as i
                LEFT JOIN t_menu_items as tm ON tm.itemid=i.id
                LEFT JOIN s_shtrih as sh on sh.itemid=i.id
                WHERE (i.mainShtrih='".addslashes($shtrih)."' OR sh.shtrih='".addslashes($shtrih)."' OR i.idlink='".addslashes($shtrih)."') and tm.menuid='".$r1['menuid']."'";
         }
         
//         $sqlText="SELECT i.id as id,i.name as name,if(ap.doNotUseMenuDesign=1,ROUND(i.price,0),ROUND(t.price,0)) as price,i.weight as weight FROM s_shtrih as s 
//                                right join s_items as i ON i.id=s.itemid
//                                LEFT join t_menu_items as t on t.itemid=i.id
//                                LEFT join s_automated_point as ap on ap.menuid=t.menuid or ap.id=".$_SESSION['idap']." 
//                                WHERE 
//                                (s.shtrih='".addslashes($shtrih)."' 
//                                or i.idlink='".addslashes($shtrih)."' or i.mainShtrih='".addslashes($shtrih)."') 
//                                and (ap.id=".$_SESSION['idap']." or ap.doNotUseMenuDesign=1)";
        $result = mysql_query($sqlText); 
        if ($result){
            if (mysql_num_rows($result)>0){
                $row = array();
                 while($r = mysql_fetch_assoc($result)) {
                    $row[] = $r;                   
                 }
                echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0), 'row'=>$row)); die;
            }else{
               echo json_encode(array('rescode'=>  errcode(96),'resmsg'=>  errmsg(96))); die; 
            }
        }else{
             echo json_encode(array('rescode'=>  errcode(95),'resmsg'=>  errmsg(95))); die;  
         }
    }
    
    case 'giftSave':{    
        if (isset($_POST['balans'])){
            $balans=$_POST['balans'];
        }else{
            echo json_encode(array('rescode'=>  errcode(104),'resmsg'=>  errmsg(104))); die;
        };
        $orderid=$_POST['orderid'];
                if (!isset($orderid)){
                    echo json_encode(array('rescode'=>  errcode(41),'resmsg'=>  errmsg(41))); die;           
                }else{
                    if ($orderid!=0){
                        checkOrder($orderid);
                    }
                };           

              if (isset($_POST['ordertable'])){
               $ordertable=$_POST['ordertable'];
                $totalpoints=0;
                if ($ordertable!='empty'){ 
                    foreach ($ordertable as $value) {                   
                     if (!isset($value['id'])){
                         echo json_encode(array('rescode'=>  errcode(53),'resmsg'=>   errmsg(53))); die;          
                     }else{
                         checkItem($value['id']);
                     };   
                     if (!isset($value['name'])){
                          echo json_encode(array('rescode'=>  errcode(54),'resmsg'=>  errmsg(54))); die;          
                     };
                     if (!isset($value['count'])){
                         echo json_encode(array('rescode'=>  errcode(56),'resmsg'=>  errmsg(56))); die;          
                     };
                     if ($_SESSION['giftType']==0){
                        $totalpoints=$totalpoints+$value['pointscount'];
                     }
                 }                    
                }              
            }else{
               echo json_encode(array('rescode'=>  errcode(36),'resmsg'=>  errmsg(36))); die;
            };
           
            $confQuery =  mysql_query('SELECT giftpaytype FROM s_automated_point where id='.$_SESSION['idap']);
            $confRow =  mysql_fetch_assoc($confQuery);

            $infQuery=  mysql_query('SELECT changeid,employeeid,clientid FROM d_order where id='.addslashes($orderid));
            if ($infQuery){
                if (mysql_num_rows($infQuery)>0){
                    $infOrd=  mysql_fetch_assoc($infQuery);
                    $clientid=$infOrd['clientid'];
                    $employeeid=$infOrd['clientid'];
                }else{
                    $clientid=$_SESSION['clientid'];
                    $employeeid=$_SESSION['employeeid'];
                }
            }else{
               echo json_encode(array('rescode'=>  errcode(103),'resmsg'=>  errmsg(103))); die; 
            }
            
            if ($_SESSION['giftType']==0){ //Подарки бонусные
                    $balansQuery =  mysql_query('SELECT SUM(points) as points FROM d_balance  where clientid='.$clientid.' GROUP BY clientid');
                    if ($balansQuery){
                        $balansRow = mysql_fetch_assoc($balansQuery); 
                        if (($balansRow<$totalpoints)&&($_SESSION['giftType']==0)){
                             echo json_encode(array('rescode'=>  errcode(120),'resmsg'=>  errmsg(120))); die;
                        }
                    }else{
                      echo json_encode(array('rescode'=>  errcode(101),'resmsg'=>  errmsg(101))); die;  
                    }


                    if (($balansRow['points']!=$balans)&&($_SESSION['giftType']==0)){
                        echo json_encode(array('rescode'=>  errcode(105),'resmsg'=>  errmsg(105))); die;
                    }
        //            

                    $levelGiftQuery =  mysql_query('SELECT max(pointscount) as points FROM s_giftlevels where pointscount<='.$balansRow['points']);

                    if ($levelGiftQuery){
                        $levelGiftRow = mysql_fetch_assoc($levelGiftQuery);
                    }else{
                       echo json_encode(array('rescode'=>  errcode(106),'resmsg'=>  errmsg(106))); die; 
                    }
            }            
            $tmparray=checkChage();
          
            $idchange=$tmparray['idchange'];
            
            $closeChange=$tmparray['closeChange'];
            
            if ($closeChange==1){
                echo json_encode(array('rescode'=>  errcode(13),'resmsg'=>  errmsg(13))); die;
            };
            
            $tmp=mysql_query("select if (max(idout+0) IS NULL,1,max(idout+0)+1) as shiftId from d_order where changeid=".$idchange);
            if (!$tmp){
              echo json_encode(array('rescode'=>  errcode(65),'resmsg'=>  errmsg(65))); die;  
            }
            
            $wpid=0;
            if (isset($_SESSION['wid'])) $wpid=$_SESSION['wid'];
            
             
            
            $idout = mysql_fetch_assoc($tmp);
            $sql = 'Insert Into d_order (idout,dtclose,changeid,employeeid,objectid,discountpercent,discountsum,printed,closed,
            clientid,totalsum,guestcount,idautomated_point,servicepercent,servicesum,sumfromclient,paymentid,wpid) Values(';
            $sql .= ''.  addslashes($idout['shiftId']).',';
            $sql .= 'NOW(),';
            $sql .= '' . $idchange. ',';
            $sql .= '' . $employeeid . ',';
            $sql .= '0,';
            $sql .= '0,';
            $sql .= '0,';
            $sql .= '1,';//printed
            $sql .= '1,';//closed
            $sql .= '' . $clientid. ', ';
            $sql .= '0,';
            $sql .= '1,';
            $sql .= '' .$_SESSION['idap']. ',';
            $sql .= '0,';
            $sql .= '0,';
            $sql .= '0,'.$confRow['giftpaytype'].','.$wpid.')';
            $resultInsertHeader = mysql_query($sql);
            if ($resultInsertHeader) {
                $last_id = mysql_insert_id();  
                $sqlInsertTable = 'Insert Into t_order (orderid,itemid,quantity,printerid,sum,price) Values '; 
                foreach ($ordertable as $value)  {
                   $sqlInsertTable .= '(' . $last_id . ','
                            . '' . addslashes($value['id']) . ','
                            . '' . addslashes($value['count']) . ','
                            . '0,'
                            . '0,'
                            . '0),'; 
                }                        
                $sql = substr($sqlInsertTable, 0, strlen($sqlInsertTable) - 1);
                $result = mysql_query($sql);
                
                if ($result) {
                  if ($_SESSION['giftType']==0){  
                    $decPointsQuery=mysql_query('INSERT INTO d_balance SET
                                                   employeeid='.$employeeid.',
                                                   clientid='.$clientid.',
                                                   orderid='.$last_id.',
                                                   points='.$totalpoints*-1);
                   if (!$decPointsQuery){
                       echo json_encode(array('rescode'=>  errcode(100),'resmsg'=> errmsg(100)));die; 
                    }
                   $logStr="(Подарки) Создание счета №".getShiftIdOrder($last_id)." Снято баллов:".$totalpoints*-1;                   
                   addlog($logStr,11);
                  } 
                 if ($_SESSION['giftType']==0){
                    $balansQuery = mysql_query("SELECT SUM(points) as balans FROM d_balance WHERE clientid=".$clientid." GROUP BY clientid");
                     if ($balansQuery){
                         $balansRow = mysql_fetch_assoc($balansQuery);                
                     }else{            
                         echo json_encode(array('rescode'=>  errcode(101),'resmsg'=>  errmsg(101))); die;
                     };                  
                    $infRow='Снято балов: '.$totalpoints.' ('.$balansRow['balans'].')';
                  }
                  else{
                     $infRow='';
                  }                    
                    unset($_SESSION['giftType']);
                    unset($_SESSION['clientid']);  
                    unset($_SESSION['giftType']);
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlOrder'=>  printOrd($last_id,'of',$infRow),'orderid'=>$last_id));                    
                 }else{
                            echo json_encode(array('rescode'=>  errcode(64),'resmsg'=> errmsg(64)));die;
                 }
            }else{
                echo json_encode(array('rescode'=>  errcode(63),'resmsg'=>  errmsg(63)));die;
            }
    break;
    }
    
    case 'employeeSessionDie':{
                 $sessionCheckResult=  checkEmployeeSession();
                            if ($sessionCheckResult==0){
//                                echo json_encode(array('rescode'=>  errcode(115),'resmsg'=>  errmsg(115))); die
                                  echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0))); die;
                            }else if ($sessionCheckResult==1){
                                updateSessionEmp(0);
                            };
                            
                 $logStr="Выход из интерфейса:".getInterfaceName($_SESSION['interfaces']);
                 addlog($logStr,16);
                 unset($_SESSION['employeeid']);
                 unset($_SESSION['interfaces']);
                 echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0))); die;
    break;
    }
    
    case 'employeeSessionCreate':{
                if (isset($_POST['interface'])){
                    $interface=$_POST['interface'];
                }else{
                    echo json_encode(array('rescode'=>  errcode(116),'resmsg'=>  errmsg(116))); die;
                }
                $_SESSION['interfaces']=$interface;
                $sessionCheckResult=  checkEmployeeSession();
                            if ($sessionCheckResult==0){
                                updateSessionEmp(1);
                            }else if ($sessionCheckResult==1){
                                echo json_encode(array('rescode'=>  errcode(114),'resmsg'=>  errmsg(114))); die;
                            }
                 
                 $logStr="Вход в интерфейс:".getInterfaceName($_SESSION['interfaces']);
                 addlog($logStr,15);
                 echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'interface'=>$interface)); die;
    break;
    }
    case 'lastAction':{
           $queryLastAction =  mysql_query('UPDATE s_employee set last_action=UNIX_TIMESTAMP(now()) WHERE id='.$_SESSION['employeeid']);
           if ($queryLastAction){
              echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0))); die; 
           }
           else{
               echo json_encode(array('rescode'=>  errcode(118),'resmsg'=>  errmsg(118))); die;
           }
    break;
    } 
    case 'getGift':{
          if (isset($_POST['clientid'])){
               $clientid=$_POST['clientid'];              
          }else{
              checkClient($clientid);
          } 
          $tmparray=giftList($clientid);
          $_SESSION['clientid']=$clientid;
          
          if ($tmparray==0){
              
             echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'balans'=>0));
          }else{
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'balans'=>$tmparray['balans'],'giftlevel'=>$tmparray['giftlevel']));
          } 
     break;
     }
     case 'getGiftItems':{
          if (isset($_POST['levelid'])){
               $levelid=$_POST['levelid'];              
          }else{
          } 
          $tmparray=giftListItems($levelid);
          if ($tmparray==0){
             echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'balans'=>0));
          }else{
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'giftItems'=>$tmparray['giftItems']));
          } 
     break;
     }
     case 'returnOrder':{
          if (!isset($orderid)){
                    echo json_encode(array('rescode'=>  errcode(41),'resmsg'=>  errmsg(41))); die;           
                }else{
                    if ($orderid!=0){
                        checkOrder($orderid);
                    }
                };
     break;
     }
//      case 'refreshMenu':{
////          echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'value'=>0)); die;
//          if (!isset($_SESSION['lastupdate'])){
//              echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'value'=>0)); die;
////              echo json_encode(array('rescode'=>  errcode(122),'resmsg'=>  errmsg(122))); die;
//          };          
//          $lastUpdateQuery =  mysql_query('SELECT lastupdate FROM s_menu');
//          if ($lastUpdateQuery){
//              $tmprow=  mysql_fetch_assoc($lastUpdateQuery);
//              $t1=new DateTime($tmprow['lastupdate']);
//              $t2=new DateTime($_SESSION['lastupdate']);
//              if ($t1!=$t2){
//                  $_SESSION['lastupdate']=$tmprow['lastupdate'];
//                  echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  $t1.'---'.$t2,'value'=>1)); die;                  
//              }else{
//                  echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'value'=>0)); die;
//              }
//          }else{
//             echo json_encode(array('rescode'=>  errcode(121),'resmsg'=>  errmsg(121))); die; 
//          }            
//     break;
//     }
     case 'getTablesFromRoom':{
          if (!isset($_POST['roomid'])){
              echo json_encode(array('rescode'=>  errcode(123),'resmsg'=>  errmsg(123))); die;
          }else{
              $roomid=$_POST['roomid'];
          };          
          $result = mysql_query("SELECT
                                        IF (
                                                servicepercent <>- 1,
                                                CONCAT(
                                                        NAME,
                                                        ' (',
                                                        servicepercent,
                                                        '%)'
                                                ),
                                                NAME
                                        ) AS name,
                                        servicepercent,
                                        id
                                        FROM
                                                s_objects
                                        where locationid=".$roomid."
                                        ORDER BY
                                                NAME"); 
          
          if (mysql_num_rows($result)>0){
            $rows = array();
            while($r = mysql_fetch_assoc($result)) {
                $rows[] = $r;                   
            }
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>$rows)); die;
          }else{
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>0)); die;
           };
     break;
     }
     case 'showRegCheck':{
          $tmparray=checkChage();
          $closeChange=$tmparray['closeChange'];
          $idchange=$tmparray['idchange'];
            $regCheckQuery=  mysql_query("SELECT
                                                    d.creationdt AS creationdt,
                                                    d.dtclose AS dtclose,
                                                    d.idout AS num,
                                                    round(d.totalsum) AS summa,
                                                    t1.content as content
                                            FROM
                                                    d_order AS d
                                            LEFT JOIN (SELECT
                                             GROUP_CONCAT(CONCAT(i.name,' ',round(t.price),' x ',round(t.quantity),' = ', round(t.sum)) SEPARATOR '<br>') as content,
                                             t.orderid
                                            FROM
                                                    t_order AS t
                                            LEFT JOIN s_items as i on i.id=t.itemid
                                            LEFT JOIN d_order as d1 on d1.id=t.orderid
                                                WHERE d1.changeid=".$idchange." and d1.employeeid=".$_SESSION['employeeid']." 
                                            GROUP BY t.orderid) as t1 on t1.orderid=d.id
                                            WHERE
                                        d.changeid = ".$idchange."
                                AND d.employeeid = ".$_SESSION['employeeid'].' ORDER BY d.dtclose DESC');
          if ($regCheckQuery){
              $rows = array();
                while($r = mysql_fetch_assoc($regCheckQuery)) {
                    $rows[] = $r;                   
                }
                echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>$rows));
          }else{
             echo json_encode(array('rescode'=>  errcode(124),'resmsg'=>  errmsg(124))); die; 
          }
         
     break;
     }
     case 'regCheck':{
         if (!isset($_POST['shtrih'])){
              echo json_encode(array('rescode'=>  errcode(125),'resmsg'=>  errmsg(125))); die;
          }else{
                $shtrihReg=$_POST['shtrih'];
          };
          $tmparray=checkChage();
          
          $idchange=$tmparray['idchange'];
            
          $closeChange=$tmparray['closeChange'];
            
          if ($closeChange==1){
                echo json_encode(array('rescode'=>  errcode(13),'resmsg'=>  errmsg(13))); die;
          };
          $sqlCheck=  mysql_query('SELECT id as id,idout as idout FROM d_order WHERE employeeid=0 and barcode="'.addslashes($shtrihReg).'" and changeid='.$idchange);
              if (mysql_num_rows($sqlCheck)>0){
                  $sqlRow=  mysql_fetch_assoc($sqlCheck);
                  $sqlReg =  mysql_query('UPDATE d_order SET employeeid='.$_SESSION['employeeid'].', dtclose=NOW() WHERE id="'.$sqlRow['id'].'"');
                        if ($sqlReg){
                            $logStr="Регистрация чека №".$sqlRow['idout']."; штрих-код чека: ".addslashes($shtrihReg);
                            addlog($logStr,20);
                            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0)));die;
                        }else{
                            echo json_encode(array('rescode'=>  errcode(126),'resmsg'=>  errmsg(126))); die;
                        }
              }else{
                  echo json_encode(array('rescode'=>  errcode(128),'resmsg'=>  errmsg(128))); die;
              };  
     break;
     }
    
     case 'getClientsFromGroup':{
         if (!isset($_POST['parentid'])){
              echo json_encode(array('rescode'=>  errcode(130),'resmsg'=>  errmsg(130))); die;
          }else{
                $parentid=$_POST['parentid'];
          };
          
          if (!isset($_POST['filter'])){
             $filter='';
          }else{
                $filter=$_POST['filter'];
          }; 
          
          if (!isset($_POST['filtertype'])){
             $filtertype='';
          }else{
                $filtertype=$_POST['filtertype'];
          };
          
          $strwhere="c.parentid=".addslashes($parentid);
          
          switch ($filtertype){
              case '1':{
                  $strwhere=' UPPER(c.name) LIKE UPPER("%'.addslashes($filter).'%") and c.isgroup=0 ';
              break;
              }
              case '2':{
                  $strwhere=' UPPER(c.shtrih) LIKE UPPER("%'.addslashes($filter).'%") and c.isgroup=0 ';
              break;
              }
              case '3':{
                  $strwhere=' UPPER(c.phone) LIKE UPPER("%'.addslashes($filter).'%") and c.isgroup=0 ';
              break;
              }
              case '4':{
                  $strwhere=' UPPER(c.email) LIKE UPPER("%'.addslashes($filter).'%") and c.isgroup=0 ';
              break;
              }              
          } 
        $sqltext='';          
        if ($filtertype==''){
            $sql="SELECT                       
                                                c.parentid as parentid,
                                                c.isgroup as isgroup                                           
                                                FROM
                                                        s_clients AS c                                            
                                            WHERE  c.id=".addslashes($parentid)." GROUP BY c.id
                                            ORDER BY c.NAME LIMIT 20";
            $sqlUpperQuery=  mysql_query($sql);
            if (mysql_num_rows($sqlUpperQuery)>0){
                $upperRow=  mysql_fetch_assoc($sqlUpperQuery);          
                    $sqltext='(SELECT
                                    '.$upperRow['parentid'].' as parentid,
                                    '.$upperRow['isgroup'].' as isgroup,
                                    0 AS discountid,
                                    0 as tel,
                                    '.$upperRow['parentid'].' AS clientid,
                                    "← Назад" AS clientname,
                                    0 AS discountpercent,
                                    0 AS usediscountsincafe,
                                    0 AS usediscountsinfastfood,
                                    0 AS usegiftsincafe,
                                    0 AS usegiftsinfastfood,
                                    0 AS useserviceincafe,
                                    0 AS useserviceinfastfood,   
                                    0 AS servicepercent,
                                    0 as balance)
                        UNION ';
            }
        }
                $sqltext.="(SELECT                       
                                            c.parentid as parentid,
                                            c.isgroup as isgroup,
                                            IF (ISNULL(ds.id), 0, ds.id) AS discountid,
                                            c.phone as tel,
                                            c.id AS clientid,
                                            if(c.isgroup=1,CONCAT(CONCAT(IF (SUM(IFNULL(bl.points,0))=0,'','('),
                                                    IF (SUM(IFNULL(bl.points,0))=0,'',SUM(IFNULL(bl.points,0))),
                                                    IF (SUM(IFNULL(bl.points,0))=0,'',')'),
                                                    c. NAME,
                                            IF (
                                                    ISNULL(ds. NAME),
                                                    '',
                                                    CONCAT('(', ds. NAME, ')')
                                            )
                                            ),' ','↓'),CONCAT(CONCAT(IF (SUM(IFNULL(bl.points,0))=0,'','('),
                                                    IF (SUM(IFNULL(bl.points,0))=0,'',SUM(IFNULL(bl.points,0))),
                                                    IF (SUM(IFNULL(bl.points,0))=0,'',')'),
                                                    c. NAME,
                                            IF (
                                                    ISNULL(ds. NAME),
                                                    '',
                                                    CONCAT('(', ds. NAME, ')')
                                            )
                                            ),' ','+')) as clientname,
                                             ROUND(
                                                    IF (
                                                            ISNULL(ds.percentvalue),
                                                            0,
                                                            ds.percentvalue
                                                    )
                                            ) AS discountpercent,
                                            IF (
                                                    ISNULL(ds.usediscountsincafe) ,- 1,
                                                    ds.usediscountsincafe
                                            ) AS usediscountsincafe,
                                            IF (
                                                    ISNULL(ds.usediscountsinfastfood) ,- 1,
                                                    ds.usediscountsinfastfood
                                            ) AS usediscountsinfastfood,
                                            IF (
                                                    ISNULL(ds.usegiftsincafe) ,- 1,
                                                    ds.usegiftsincafe
                                            ) AS usegiftsincafe,
                                            IF (
                                                    ISNULL(ds.usegiftsinfastfood) ,- 1,
                                                    ds.usegiftsinfastfood
                                            ) AS usegiftsinfastfood,
                                            IF (
                                                    ISNULL(ds.useserviceincafe) ,- 1,
                                                    ds.useserviceincafe
                                            ) AS useserviceincafe,
                                            IF (
                                                    ISNULL(ds.useserviceinfastfood) ,- 1,
                                                    ds.useserviceinfastfood
                                            ) AS useserviceinfastfood,     
                                             ROUND(
                                                    IF (
                                                            ISNULL(ds.servicepercent) ,- 1,
                                                            ds.servicepercent
                                                    )
                                            ) AS servicepercent,
                                            SUM(IFNULL(bl.points,0)) as balance
                                            FROM
                                                    s_clients AS c
                                           
                                            LEFT JOIN (
								SELECT d.id,d.name,d.idpartner,d.percentvalue,
											d.sumvalue,d.usediscountsincafe,
											d.usediscountsinfastfood,d.usegiftsincafe,
											d.usegiftsinfastfood,d.useserviceincafe,
											d.useserviceinfastfood,d.servicepercent,
											d.usebalanceincafe,d.usebalanceinfastfood,
                                                                                        dc.clientid as clientid
								FROM s_discount as d
								LEFT JOIN t_discount_ap as da on da.discountid=d.id
                                                                LEFT JOIN t_discount_clients as dc on dc.discountid=d.id
								WHERE da.apid=".$_SESSION['idap']."
							) AS ds ON ds.clientid = c.id or c.parentid=ds.clientid																					
                                            LEFT JOIN d_balance AS bl ON c.id = bl.clientid  
                                        WHERE  ".$strwhere." GROUP BY c.id,ds.id 
                                        ORDER BY c.isgroup DESC,c.NAME DESC LIMIT 20)";   
        $selectFolderQuery =  mysql_query($sqltext);
//        LEFT JOIN s_discount AS ds ON ds.idpartner = c.id or c.parentid=ds.idpartner
//                                            LEFT JOIN d_balance AS bl ON c.id = bl.clientid
       
        
        if ($selectFolderQuery){
            $rows = array();
                while($r = mysql_fetch_assoc($selectFolderQuery)) {
                    $rows[] = $r;                   
                }
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>$rows));
        }  
        else{
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>0));
        }
     break;
     }
    
     case 'stopListPwdQuery':{
         if (isset($_POST['pwd'])){ 
               $pwd = md5(FISH.md5($_POST['pwd'])); 
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        };
         $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdStopList="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
        }else{
            echo json_encode(array('rescode'=>  errcode(142),'resmsg'=>  errmsg(142))); die;
        }
        echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0)));          
     break;
     }
     case 'getMenuFolders':{
         if (!isset($_POST['parentid'])){
              echo json_encode(array('rescode'=>  errcode(130),'resmsg'=>  errmsg(130))); die;
          }else{
                $parentid=  addslashes($_POST['parentid']);
          };  
        $sqltext='';   
        if ($_SESSION['doNotUseMenuDesign']==0){
            $sqlUpperQuery=  mysql_query('SELECT
                                            t.id as id,
                                            t.NAME as name,
                                            t.isgroup as isgroup,
                                            t.parentid as parentid
                                       FROM 
                                            t_menu_items as t
                                       LEFT JOIN s_menu as m on m.id=t.menuid
                                       LEFT JOIN s_automated_point as ap on ap.menuid=m.id
                                       WHERE t.id = '.$parentid.' and ap.id='.$_SESSION['idap']);
            if (mysql_num_rows($sqlUpperQuery)>0){
                $upperRow=  mysql_fetch_assoc($sqlUpperQuery);          
                    $sqltext='(SELECT '.$upperRow['parentid'].' as id,"← Назад" as name, '.$upperRow['isgroup'].' as isgroup, '.$upperRow['parentid'].' as parentid,0 as menuid)
                        UNION';
            }
            $sqltext.='(SELECT
                           t.id as id,
                           if(t.isgroup=1,CONCAT(t.name," ","↓"),CONCAT(t.name," ","→")) as name,
                           t.isgroup as isgroup,
                           t.parentid as parentid,
                           t.menuid as menuid
                      FROM 
                           t_menu_items as t
                      LEFT JOIN s_menu as m on m.id=t.menuid
                      LEFT JOIN s_automated_point as ap on ap.menuid=m.id
                      WHERE t.parentid = '.$parentid.' and ap.id='.$_SESSION['idap'].')';
        }else if ($_SESSION['doNotUseMenuDesign']==1){
            $sql="SELECT                       
                            i.parentid as parentid,
                            i.isgroup as isgroup
                        FROM
                            s_items AS i
                        WHERE  i.id=".addslashes($parentid)." and i.i_useInMenu=1 GROUP BY i.id 
                        ORDER BY i.NAME DESC";
                $sqlUpperQuery=  mysql_query($sql);
                if (mysql_num_rows($sqlUpperQuery)>0){
                    $upperRow=  mysql_fetch_assoc($sqlUpperQuery);          
                        $sqltext='(SELECT
                                        '.$upperRow['parentid'].' as parentid,
                                        '.$upperRow['isgroup'].' as isgroup,
                                        '.$upperRow['parentid'].' AS itemid,
                                        "← Назад" AS itemname,
                                        0 as id,
                                        0 as printer
                                       )
                            UNION ';
                }   
                    $sqltext.="(SELECT
                                i.parentid AS parentid,
                                i.isgroup AS isgroup,
                                i.id AS itemid,

                        IF (
                                i.isgroup = 1,
                                CONCAT(i. NAME, ' ', '↓'),
                                CONCAT(i. NAME, ' ', '→')
                        ) AS name,
                         i.id AS id,
                         i.i_printer AS printer
                        FROM
                                s_items AS i
                        WHERE
                                i.parentid = ".addslashes($parentid)." and i.i_useInMenu=1
                        GROUP BY
                                i.id
                        ORDER BY
                                i.id)";             
        }
        
        $selectFolderQuery =  mysql_query($sqltext);
        if ($selectFolderQuery){
            $rows = array();
                while($r = mysql_fetch_assoc($selectFolderQuery)) {
                    $rows[] = $r;                   
                }
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>$rows));
        }    
     break;
     } 
     case 'addToStopList':{
            if (!isset($_POST['itemid'])){
                 echo json_encode(array('rescode'=>  errcode(132),'resmsg'=>  errmsg(132))); die;
             }else{
                   $itemid=$_POST['itemid'];
            };
            $checkQuery=  mysql_query('SELECT id FROM d_stop_list where menu_item_id='.$itemid);
            if (mysql_num_rows($checkQuery)>0){
                echo json_encode(array('rescode'=>  errcode(135),'resmsg'=>  errmsg(135))); die;
            };
            
            $updateMenu=  mysql_query('UPDATE s_menu SET lastupdate=NOW()');
            
           $addQuery=  mysql_query('INSERT INTO d_stop_list SET employeeid='.$_SESSION['employeeid'].', apid='.$_SESSION['idap'].', menu_item_id='.$itemid);
           if ($addQuery){
               echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0)));
           }else{
               echo json_encode(array('rescode'=>  errcode(133),'resmsg'=>  errmsg(133))); die; 
           }
     break;
     }
      case 'deleteToStopList':{
            if (!isset($_POST['rowid'])){
                 echo json_encode(array('rescode'=>  errcode(132),'resmsg'=>  errmsg(132))); die;
             }else{
                   $rowid=$_POST['rowid'];
            };          
           $deleteQuery=  mysql_query('DELETE FROM d_stop_list WHERE id='.$rowid);
           if ($deleteQuery){
               echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0)));
           }else{
               echo json_encode(array('rescode'=>  errcode(133),'resmsg'=>  errmsg(133))); die; 
           }
     break;
     }
     case 'getStopList':{  
         if ($_SESSION['doNotUseMenuDesign']==0){
            $getQuery=  mysql_query('SELECT d.id as id,t.name as name FROM d_stop_list as d
                                    LEFT JOIN t_menu_items as t on t.id=d.menu_item_id');
         }else if ($_SESSION['doNotUseMenuDesign']==1){
            $getQuery=  mysql_query('SELECT d.id as id,t.name as name FROM d_stop_list as d
                                    LEFT JOIN s_items as t on t.id=d.menu_item_id');
         }
           if ($getQuery){
                    $rows = array();
                        while($r = mysql_fetch_assoc($getQuery)) {
                            $rows[] = $r;                   
                        }
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>$rows));
           }else{
               echo json_encode(array('rescode'=>  errcode(133),'resmsg'=>  errmsg(133))); die; 
           }
     break;
     }
     case 'saveDivide':{            
                    if (!isset($_POST['orderid'])){
                        echo json_encode(array('rescode'=>  errcode(41),'resmsg'=>  errmsg(41))); die;           
                    }else{
                        $orderid=$_POST['orderid'];
                    };
                    
            if (isset($_POST['ordertable'])){
                   $ordertable=$_POST['ordertable']; 
                   if ($ordertable!='empty'){                   
                   foreach ($ordertable as $value) {                   
                        if (!isset($value['itemid'])){
                            echo json_encode(array('rescode'=>  errcode(53),'resmsg'=>  errmsg(53))); die;          
                        }else{
                            checkItem($value['itemid']);
                        };   
                       if (!isset($value['name'])){
                            echo json_encode(array('rescode'=>  errcode(54),'resmsg'=>  errmsg(54))); die;          
                       };
                        if (!isset($value['price'])){
                            echo json_encode(array('rescode'=>  errcode(55),'resmsg'=>  errmsg(55))); die;          
                        };
                        if (!isset($value['count'])){
                            echo json_encode(array('rescode'=>  errcode(56),'resmsg'=>  errmsg(56))); die;          
                        };
                        if (!isset($value['printer'])){
                            echo json_encode(array('rescode'=>  errcode(57),'resmsg'=>  errmsg(57))); die;          
                        };
                        if (!isset($value['summa'])){
                           echo json_encode(array('rescode'=>  errcode(58),'resmsg'=>  errmsg(58))); die;          
                        };
                        if (!isset($value['note'])){
                           $value['note']='';          
                        }; 
                    }
                   }           
                }else{
                   echo json_encode(array('rescode'=>  errcode(36),'resmsg'=>  errmsg(36))); die;
                };
            
                       
        $divideOrder=showOrderTemp($orderid);

        if ($divideOrder['printed']==1||$divideOrder['closed']==1){
            echo json_encode(array('rescode'=>  errcode(137),'resmsg'=>  errmsg(137))); die;
        }
        
        $summa=0;
        $servicesum=0;
        $discountsum=0;
        $comediscount=0;
        $comeservice=0;
        $comesum=0;
        $nowdiscount=0;
        $nowservice=0;
        $nowsum=0;
            foreach ($ordertable as $value){
                $sum2=0;
                $discount=0;
                $service=0;
                if  ($_SESSION['typeOfDiscountService']==0){
                    $service=(round($value['summa']*($divideOrder['servicepercent']))/100);
                    $discount= (round($value['summa']*($divideOrder['discountpercent']))/100);       

                }else if($_SESSION['typeOfDiscountService']==1){
                      $service=(round($value['summa']*($divideOrder['servicepercent']))/100);
                      $sum2=$value['summa']+$service;
                      $discount= (round(sum2*($divideOrder['discountpercent']))/100);
                  }else if($_SESSION['typeOfDiscountService']==2){
                      $discount= (round($value['summa']*($divideOrder['discountpercent']))/100);
                      $sum2=$value['summa']-$discount;
                      $service=(round($sum2*($divideOrder['servicepercent']))/100);
                  }
                 $comediscount+=$discount;
                 $comeservice+=$service;
                 $comesum+=$value['summa']+$service-$discount;
            }

            $divideOrder['totalsum']=$nowsum+$comesum;
            $divideOrder['discountsum']=$comediscount+$nowdiscount;
            $divideOrder['servicesum']=$nowservice+$comeservice;
             
            $tmparray=checkChage();
            $idchange=$tmparray['idchange'];
            $closeChange=$tmparray['closeChange'];
            if ($closeChange==1){
                echo json_encode(array('rescode'=>  errcode(13),'resmsg'=>  errmsg(13))); die;
            };    
            $tmp=mysql_query("select if (max(idout+0) IS NULL,1,max(idout+0)+1) as shiftId from d_order where changeid=".$idchange);
            if (!$tmp){
              echo json_encode(array('rescode'=>  errcode(65),'resmsg'=>  errmsg(65))); die;  
            }
            $wpid=0;
            if (isset($_SESSION['wid'])) $wpid=$_SESSION['wid'];
            
            
            
            $idout = mysql_fetch_assoc($tmp);
            $sql = 'Insert Into d_order (idout,parentid,changeid,employeeid,objectid,discountpercent,discountsum,printed,closed,
            clientid,totalsum,guestcount,idautomated_point,servicepercent,servicesum,sumfromclient,paymentid,wpid,interfaceid,discountid) Values(';
           
            $sql .= ''.  $idout['shiftId'].',';
            $sql .= ''.  $orderid.',';
            $sql .= '' . $idchange . ',';
            $sql .= '' . $divideOrder['employeeid'] . ',';
            $sql .= '' . $divideOrder['tableid'] . ',';
            $sql .= '' . $divideOrder['discountpercent'] . ',';
            $sql .= '' . $divideOrder['discountsum'] . ',';
            $sql .= '0,';//printed
            $sql .= '0,';//closed
            $sql .= '' . $divideOrder['client']. ', ';
            $sql .= '' . $divideOrder['totalsum']. ',';
            $sql .= '"' .$divideOrder['guests']. '",';
            $sql .= '' .$_SESSION['idap']. ',';
            $sql .= '"' .$divideOrder['servicepercent']. '",';
            $sql .= '"' .$divideOrder['servicesum']. '",';
            $sql .= '0,0,'.$wpid.','.$_SESSION['interfaces'].','.$divideOrder['discountid'].')';
            $resultInsertHeader = mysql_query($sql);
            $logStr2='';
            if ($resultInsertHeader) {
                $last_id = mysql_insert_id();
                $sqlInsertTable = 'Insert Into t_order (orderid,itemid,quantity,printerid,sum,note,price) Values ';
                foreach ($ordertable as $value)  {
                    $value['printer']=$value['printer']+0;
                    $sqlInsertTable .= '(' . $last_id . ','
                            . '' . addslashes($value['itemid']) . ','
                            . '' . addslashes($value['count']) . ','
                            . '' . addslashes($value['printer']) . ','
                            . '' . (addslashes($value['count'])*addslashes($value['price']) ) . ','
                            . '"' .addslashes($value['note']) . '",'
                            . '' . addslashes($value['price']) . '),';
                    $q =  mysql_query('SELECT name FROM s_items WHERE id='.addslashes($value['itemid']));
                    $qr =  mysql_fetch_assoc($q);
                    $itemname=$qr['name'];
                    $logStr2 .= 'Товар:('.$itemname.'), кол-во: '.addslashes($value['count']).', цена:'.addslashes($value['price']).' <br>';
                }                
                $sql = substr($sqlInsertTable, 0, strlen($sqlInsertTable) - 1);
                $result = mysql_query($sql);
                if ($result) {
                    $logStr="Разбиение счета №".$orderid."; Создание счета №".$last_id.";
                        <br>  Процент скидки:".addslashes($divideOrder['discountpercent']).";
                        <br>  Процент обслуживания:".addslashes($divideOrder['servicepercent'])."
                        <br>  Сумма скидки:".addslashes($divideOrder['discountsum'])."
                        <br>  Сумма обслуживания:".addslashes($divideOrder['servicesum'])."
                        <br>  Итоговая сумма".addslashes($divideOrder['totalsum']);
                    addlog($logStr.'<br>Товары<br>'.$logStr2,1);
                }else{
                            echo json_encode(array('rescode'=>  errcode(64),'resmsg'=> errmsg(64)));die;
                }
            }else{
                echo json_encode(array('rescode'=>  errcode(63),'resmsg'=>  errmsg(63)));die;
            } 
        
       
            
        $order=showOrderTemp($orderid);    
       
        $sqlInsertTable = 'Insert Into t_order (orderid,itemid,quantity,printerid,sum,note,price) Values ';
        foreach ($ordertable as $value)  {
            $value['printer']=$value['printer']+0;
            $sqlInsertTable .= '(' . $order['orderid'] . ','
                    . '' . addslashes($value['itemid']) . ','
                    . '' . addslashes($value['count']*-1) . ','
                    . '' . addslashes($value['printer']) . ','
                    . '' . (addslashes($value['count']*-1)*addslashes($value['price']) ) . ','
                    . '"' .addslashes($value['note']) . '",'
                    . '' . addslashes($value['price']) . '),';
        }                
        $sql = substr($sqlInsertTable, 0, strlen($sqlInsertTable) - 1);
        
        $result = mysql_query($sql);
        
        if ($result) {
        }else{
             echo json_encode(array('rescode'=>  errcode(64),'resmsg'=> errmsg(64)));die;
        }
        
        calcorderadnupdate($order['orderid']);
        echo json_encode(array('rescode'=>  errcode(0),'resmsg'=> errmsg(0)));die;
        
     break;
     }
     case 'saveReturn':{
            if (!isset($_POST['orderid'])){
                        echo json_encode(array('rescode'=>  errcode(41),'resmsg'=>  errmsg(41))); die;           
                    }else{
                        $orderid=$_POST['orderid'];
                    };
                    
            if (isset($_POST['ordertable'])){
                   $ordertable=$_POST['ordertable']; 
                   if ($ordertable!='empty'){                   
                   foreach ($ordertable as $value) {                   
                        if (!isset($value['itemid'])){
                            echo json_encode(array('rescode'=>  errcode(53),'resmsg'=>  errmsg(53))); die;          
                        }else{
                            checkItem($value['itemid']);
                        };   
                       if (!isset($value['name'])){
                            echo json_encode(array('rescode'=>  errcode(54),'resmsg'=>  errmsg(54))); die;          
                       };
                        if (!isset($value['price'])){
                            echo json_encode(array('rescode'=>  errcode(55),'resmsg'=>  errmsg(55))); die;          
                        };
                        if (!isset($value['count'])){
                            echo json_encode(array('rescode'=>  errcode(56),'resmsg'=>  errmsg(56))); die;          
                        };
                        if (!isset($value['printer'])){
                            echo json_encode(array('rescode'=>  errcode(57),'resmsg'=>  errmsg(57))); die;          
                        };
                        if (!isset($value['summa'])){
                           echo json_encode(array('rescode'=>  errcode(58),'resmsg'=>  errmsg(58))); die;          
                        };
                        if (!isset($value['note'])){
                           $value['note']='';          
                        }; 
                    }
                   }           
                }else{
                   echo json_encode(array('rescode'=>  errcode(36),'resmsg'=>  errmsg(36))); die;
                };
            
                       
           $returnOrder=showOrderTemp($orderid);
           
            if ($returnOrder['closed']!=1){
                echo json_encode(array('rescode'=>  errcode(137),'resmsg'=>  errmsg(137))); die;
            }
            
            $sum=0;
            foreach ($ordertable as $value){
                 $sum=$sum+$value['summa'];
            }  
            $sum=$sum*-1;
            
          
            
            $sum2=0;
            $discount=0;
            $service=0;
            if  ($_SESSION['typeOfDiscountService']==0){
                $service=(round($sum*($returnOrder['servicepercent']))/100);
                $discount= (round($sum*($returnOrder['discountpercent']))/100);       
            }else if($_SESSION['typeOfDiscountService']==1){
                  $service=(round($sum*($returnOrder['servicepercent']))/100);
                  $sum2=$sum+$service;
                  $discount= (round(sum2*($returnOrder['discountpercent']))/100);
              }else if($_SESSION['typeOfDiscountService']==2){
                  $discount= (round($sum*($returnOrder['discountpercent']))/100);
                  $sum2=$sum-$discount;
                  $service=(round($sum2*($orderdata['servicepercent']))/100);
              }
             $comediscount=$discount;
             $comeservice=$service;
             $comesum=$sum+$comeservice-$comediscount;  
            
             $returnOrder['discountsum']=$comediscount*-1;
             $returnOrder['servicesum']=$comeservice*-1;
             $returnOrder['totalsum']=$comesum*-1;
            
            $tmparray=checkChage();
            $idchange=$tmparray['idchange'];
            $closeChange=$tmparray['closeChange'];
            if ($closeChange==1){
                echo json_encode(array('rescode'=>  errcode(13),'resmsg'=>  errmsg(13))); die;
            };    
            $tmp=mysql_query("select if (max(idout+0) IS NULL,1,max(idout+0)+1) as shiftId from d_order where changeid=".$idchange);
            if (!$tmp){
              echo json_encode(array('rescode'=>  errcode(65),'resmsg'=>  errmsg(65))); die;  
            }
            $wpid=0;
            if (isset($_SESSION['wid'])) $wpid=$_SESSION['wid'];
             
            $idout = mysql_fetch_assoc($tmp);
            $sql = 'Insert Into d_order (idout,parentid,sumfromclient,changeid,employeeid,objectid,discountpercent,discountsum,printed,closed,dtclose,
            clientid,totalsum,guestcount,idautomated_point,servicepercent,servicesum,paymentid,wpid,interfaceid,discountid) Values(';
           
            $sql .= ''.  $idout['shiftId'].',';
            $sql .= ''.  $orderid.',';
            $sql .= ''.  ($returnOrder['totalsum']).',';
            $sql .= '' . $idchange. ',';
            $sql .= '' . $returnOrder['employeeid'] . ','; 
            $sql .= '' . $returnOrder['tableid'] . ',';
            $sql .= '' . $returnOrder['discountpercent'] . ','; 
            $sql .= '' . $returnOrder['discountsum'] . ',';
            $sql .= '0,';//printed
            $sql .= '1,';//closed
            $sql .= 'NOW(), ';
            $sql .= '' . $returnOrder['client']. ', ';
            $sql .= '' . (($returnOrder['totalsum'])). ',';
            $sql .= '"' .$returnOrder['guests']. '",';
            $sql .= '' .$_SESSION['idap']. ',';
            $sql .= '"' .(($returnOrder['servicepercent'])). '",';
            $sql .= '"' .(($returnOrder['servicesum'])). '",';
            $sql .= $returnOrder['paymentid'].','.$wpid.','.$_SESSION['interfaces'].','.$returnOrder['discountid'].')';
            $resultInsertHeader = mysql_query($sql);
            $logStr2='';
            if ($resultInsertHeader) {
                $tmparr=null;
                $last_id = mysql_insert_id();
                $sqlInsertTable = 'Insert Into t_order (orderid,itemid,quantity,printerid,sum,note,discountsum,servicesum,salesum,price) Values ';
                foreach ($ordertable as $value)  {
                    $tmparr=getTableSum($returnOrder, (addslashes($value['count'])*addslashes($value['price'])));
                    $value['printer']=$value['printer']+0;
                    $countC=addslashes($value['count']);
                    $priceC=addslashes($value['price']);
                    $sumC=$countC*$priceC;
                    $sqlInsertTable .= '(' . $last_id . ','
                            . '' . addslashes($value['itemid']) . ','
                            . '' . ($countC) . ','
                            . '' . addslashes($value['printer']) . ','
                            . '' . $sumC . ','
                            . '"' .addslashes($value['note']) . '",'
                            . '' . ($tmparr['discount']) . ','
                            . '' . ($tmparr['service']) . ','
                            . '' . ($tmparr['sum']) . ','
                            . '' . ($priceC) . '),';
                    $logStr2 .= 'Товар:('.addslashes($value['name']).'), кол-во: '.addslashes($value['count']).', цена:'.addslashes($value['price']).' <br>';
                    $sql = substr($sqlInsertTable, 0, strlen($sqlInsertTable) - 1);
                    $result = mysql_query($sql);
                    
                    $last_id_t=mysql_insert_id();
                    
                    $sqlComplex=  mysql_query("SELECT count(t.id) as count FROM t_order as t WHERE t.parentid=".addslashes($value['innerId']));
                    if ($sqlComplex){
                        $sqlComplexRow=  mysql_fetch_assoc($sqlComplex);
                        if ($sqlComplexRow['count']>0){
                            $sqlText='SELECT 
                                            i.name as name,
                                            t.itemid as itemid,
                                            t.quantity as count,
                                            t.printerid as printer,                                            
                                            t.price as price,
                                            t.note as note
                                          FROM t_order as t 
                                          LEFT join s_items as i on i.id=t.itemid 
                                          WHERE t.parentid='.addslashes($value['innerId']);
                            $sqlComplexContent=mysql_query($sqlText);
                            if ($sqlComplexContent){
                                
                                $logStr2.='Содержимое комплекса:('.addslashes($value['name']).')';
                                while($combovalue = mysql_fetch_assoc($sqlComplexContent)){
                                    $tmpArrSum2=getTableSum($returnOrder,($countC*$combovalue['price']));
                                    $sqlInsertTable = 'Insert Into t_order (orderid,parentid,itemid,quantity,printerid,sum,note,discountsum,servicesum,salesum,price) Values ';
                                      $sqlInsertTable .= '(' . $last_id . ','
                                       . '' . $last_id_t . ',' 
                                       . '' . $combovalue['itemid'] . ','
                                       . '' . $countC . ','
                                       . '' . $combovalue['printer'] . ','
                                       . '' . (($countC*$combovalue['price'])) . ','
                                       . '"' .$combovalue['note'] . '",'    
                                       . '' . ($tmpArrSum2['discount']) . ','
                                       . '' . ($tmpArrSum2['service']) . ','
                                       . '' . ($tmpArrSum2['sum']) . ','
                                       . '' . ($combovalue['price']) . '),';
                                    $sql = substr($sqlInsertTable, 0, strlen($sqlInsertTable) - 1);
                                    $result = mysql_query($sql);
                                    $q =  mysql_query('SELECT name FROM s_items WHERE id='.$combovalue['itemid']);
                                    $qr =  mysql_fetch_assoc($q);
                                    $itemname=$qr['name'];
                                    
                                    $logStr2 .= '<br> Товар: '.$itemname.', кол-во: '.$countC.', цена:'.$combovalue['price'].' ';
                                }
                            }
                        }    
                    }
                }                
                 
                if ($result) {
                    $logStr="Возврат счета №".$idout['shiftId'].";
                        <br> Клиент:".getOrderInf($last_id).";
                        <br>  Процент скидки:".addslashes($returnOrder['discountpercent']).";
                        <br>  Процент обслуживания:".addslashes($returnOrder['servicepercent'])."
                        <br>  Сумма скидки:".addslashes($returnOrder['discountsum'])."
                        <br>  Сумма обслуживания:".addslashes($returnOrder['servicesum'])."
                        <br>  Итоговая сумма".addslashes($returnOrder['totalsum']);
                    addlog($logStr.'<br>'.$logStr2,12);
                    $infRow='';
                    
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlOrder'=>printOrd($last_id,'return',$infRow),'orderid'=>$last_id));die;
                }else{
                            echo json_encode(array('rescode'=>  errcode(64),'resmsg'=> errmsg(64)));die;
                }
            }else{
                echo json_encode(array('rescode'=>  errcode(63),'resmsg'=>  errmsg(63)));die;
            } 
     break;
     }
    case 'regCheckReport':{ 
    $tmparray=checkChage();
          $closeChange=$tmparray['closeChange'];
          $idchange=$tmparray['idchange'];
            $regCheckQuery=  mysql_query("SELECT
                                                    d.creationdt AS creationdt,
                                                    d.idout AS num,
                                                    round(d.totalsum) AS summa,
                                                    t1.content as content
                                            FROM
                                                    d_order AS d
                                            LEFT JOIN (SELECT
                                             GROUP_CONCAT(CONCAT(i.name,' ',round(t.price),' x ',round(t.quantity),' = ', round(t.sum)) SEPARATOR '<br>') as content,
                                             t.orderid
                                            FROM
                                                    t_order AS t
                                            LEFT JOIN s_items as i on i.id=t.itemid
                                            LEFT JOIN d_order as d1 on d1.id=t.orderid
                                                WHERE d1.changeid=".$idchange." and d1.employeeid=".$_SESSION['employeeid']." 
                                            GROUP BY t.orderid) as t1 on t1.orderid=d.id
                                            WHERE
                                        d.changeid = ".$idchange."
                                AND d.employeeid = ".$_SESSION['employeeid'].' ORDER BY d.creationdt DESC');
         
          if ($regCheckQuery){
              $reportStr='';
              $rows = array();
              $reportStr='<Header><customprint>';
              $i=1;
              $sum=0;
              $sqlHeader=  mysql_query('SELECT ap.name as apname FROM s_automated_point as ap WHERE ap.id='.$_SESSION['idap']);
              $tmprow=  mysql_fetch_assoc($sqlHeader);
              $reportStr.='        '.$tmprow['apname'];
              $sqlHeader=  mysql_query('SELECT emp.name as empname FROM s_employee as emp WHERE emp.id='.$_SESSION['employeeid']);
              $tmprow=  mysql_fetch_assoc($sqlHeader);
              $reportStr.='  <br>Сотрудник:'.$tmprow['empname'];
              $reportStr.='<br>';
               $reportStr.='<br>';
                while($r = mysql_fetch_assoc($regCheckQuery)) {
                    $reportStr .= $i.':№'.$r['num'].'; '.$r['creationdt'].';<br>Итог:'.$r['summa'].'<br>';  
//                    $reportStr .= 'Услуги <br>';
                    $reportStr .= $r['content'].'<br>';
                    $reportStr .= '------------- <br>';
                    $sum+=$r['summa'];
                    $i++;
                }
              $reportStr .= 'Общий итог: '.$sum.' <br>';
              $sqlPart=  mysql_query("SELECT
                                    e.`name` AS waitername,
                                    tordnum.sumservice AS sumserviceorder,
                                    tordnum.sumitems AS sumitemorder,
                                    tordnum.consumption AS consumption,
                                   (((tordnum.sumservice-tordnum.consumption) * e.e_itemservicepercent) / 100)+((tordnum.sumitems* e.e_itempercent) / 100) AS sumsalary
                            FROM
                                    d_order AS o
                            LEFT JOIN (
                                    SELECT
                                            e.id AS empid,
                                            t_order.orderid,
                                            t_order.quantity,
                                            SUM(
                                                    IF (
                                                            t_order.parentid > 0,
                                                            t_order.sum *- 1,
                                                            t_order.sum
                                                    )
                                            ) AS summa,
                                            sum(
                                                    IF (
                                                            t_order.parentid = 0 AND i.isservice<>0,
                                                            t_order.sum,
                                                            0
                                                    )
                                            ) AS sumservice,
                                            sum(
                                                    IF (
                                                            t_order.parentid > 0 AND i.isservice<>1,
                                                            t_order.sum,
                                                            0
                                                    )
                                            ) AS consumption,
                                            sum(
                                                    IF (
                                                            t_order.parentid = 0 AND i.isservice<>1,
                                                            t_order.sum,
                                                            0
                                                    )
                                            ) AS sumitems
                                    FROM
                                            t_order
                                    LEFT JOIN d_order AS d ON d.id = t_order.orderid and d.changeid = ".$idchange."
                                    LEFT JOIN s_employee AS e ON e.id = d.employeeid
                                    LEFT JOIN s_items as i on i.id=t_order.itemid
                                    GROUP BY
                                            d.employeeid
                            ) AS tordnum ON o.id = tordnum.orderid
                            OR tordnum.empid = o.employeeid
                            LEFT JOIN s_employee AS e ON o.employeeid = e.id
                            WHERE
                                    o.changeid = ".$idchange." and e.id=".$_SESSION['employeeid']."
                            GROUP BY
                                    e.id");
              $rowPart  =  mysql_fetch_assoc($sqlPart);
              $reportStr .= 'Товары: '. floatval($rowPart['sumitemorder']).'<br>';
              $reportStr .= 'Услуги: '. floatval($rowPart['sumserviceorder']).'<br>';
              $reportStr .= 'Затраты: '. floatval($rowPart['consumption']).'<br>';
              $reportStr .= 'Заработано: '. floatval($rowPart['sumsalary']).'<br>';
              $reportStr.='</customprint></Header>';
              $logStr="Печать отчета о зарплате сотрудника <br>".$reportStr;
              addlog($logStr,20);
                echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'report'=>$reportStr));
          }else{
             echo json_encode(array('rescode'=>  errcode(124),'resmsg'=>  errmsg(124))); die; 
          }    
    break;
    }
    case 'pwdClientCheck':{
        
        if (isset($_POST['pwd'])){
            if ($_POST['pwd']!='')
               $pwd = md5(FISH.md5($_POST['pwd']));
            else{
                $pwd=$_POST['pwd'];
            }
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
        $logStr="Запрос на выбор клиента";
        addlog($logStr,17);
        if ($pwd==''){
            $sqlQuery=  mysql_query('SELECT pwdclient FROM s_automated_point WHERE id='.$_SESSION['idap']);
            if ($sqlQuery){
                $sqlRow=  mysql_fetch_assoc($sqlQuery);
                if (($sqlRow['pwdclient']=='')||$sqlRow['pwdclient']==NULL){
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
                }else{
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>1)); die;
                }
            }
        }
        $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdclient="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(138),'resmsg'=>  errmsg(138))); die;
        }
     break;
     }
     case 'selectReturnOrder':{
        if (isset($_POST['orderNum'])){
               $orderNum = $_POST['orderNum']; 
        }else{
               echo json_encode(array('rescode'=>  errcode(139),'resmsg'=>  errmsg(139))); die;
        }; 
        if (isset($_POST['date'])){
               $date = $_POST['date']; 
        }else{
               echo json_encode(array('rescode'=>  errcode(140),'resmsg'=>  errmsg(140))); die;
        }; 
        
        
        $sqlQuery=  mysql_query('SELECT id FROM d_order WHERE idout='.$orderNum.' and DATE_FORMAT(dtclose,"%Y-%m-%d")="'.$date.'" and idautomated_point='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            $sqlrow=  mysql_fetch_assoc($sqlQuery);
            $logStr="Начало возврата счета №".$orderNum;
            addlog($logStr,3);
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'orderid'=>$sqlrow['id'])); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(155),'resmsg'=>  errmsg(155))); die;
        }
     break;
     }
     case 'checkDividePwd':{
        if (isset($_POST['pwd'])){
               $pwd = md5(FISH.md5($_POST['pwd']));
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        };
        $logStr="Запрос на разделение счета";
       addlog($logStr,17);
         $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdDivide="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0))); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(167),'resmsg'=>  errmsg(167))); die;
        }
       
     break;
     }
     case 'checkChangePricePassword':{
        if (isset($_POST['pwd'])){
            if ($_POST['pwd']!='')
               $pwd = md5(FISH.md5($_POST['pwd']));
            else{
                $pwd=$_POST['pwd'];
            }
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
        $logStr="Запрос на смену цены";
        addlog($logStr,17);
        $checkUseQuery=  mysql_query('SELECT useChangePrice FROM s_automated_point WHERE id='.$_SESSION['idap']);
        if ($checkUseQuery){
            $checkRow=  mysql_fetch_assoc($checkUseQuery);
            if ($checkRow['useChangePrice']==0){
                echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0))); die;
            }
        }
        if ($pwd==''){
            $sqlQuery=  mysql_query('SELECT pwdChangePrice FROM s_automated_point WHERE id='.$_SESSION['idap']);
            if ($sqlQuery){
                $sqlRow=  mysql_fetch_assoc($sqlQuery);
                if (($sqlRow['pwdChangePrice']=='')||$sqlRow['pwdChangePrice']==NULL){
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
                }else{
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>1)); die;
                }
            }
        }
        $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdChangePrice="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(138),'resmsg'=>  errmsg(138))); die;
        }       
     break;
     }  
      case 'checkAktReportPassword':{
        if (isset($_POST['pwd'])){
            if ($_POST['pwd']!='')
               $pwd = md5(FISH.md5($_POST['pwd']));
            else{
                $pwd=$_POST['pwd'];
            }
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
        $logStr="Запрос отчета акт реализации";
       addlog($logStr,17);
        if ($pwd==''){
            $sqlQuery=  mysql_query('SELECT pwdReportAktReal FROM s_automated_point WHERE id='.$_SESSION['idap']);
            
            if ($sqlQuery){
                $sqlRow=  mysql_fetch_assoc($sqlQuery);
                if (($sqlRow['pwdReportAktReal']=='')||$sqlRow['pwdReportAktReal']==NULL){
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
                }else{
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>1)); die;
                }
            }
        }
        $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdReportAktReal="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(143),'resmsg'=>  errmsg(143))); die;
        }       
     break;
     }
     case 'checkPoschetamReportPassword':{
        if (isset($_POST['pwd'])){
            if ($_POST['pwd']!='')
               $pwd = md5(FISH.md5($_POST['pwd']));
            else{
                $pwd=$_POST['pwd'];
            }
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
       $logStr="Запрос отчета по счетам";
       addlog($logStr,17);
        if ($pwd==''){
            $sqlQuery=  mysql_query('SELECT pwdReportPoSchetam FROM s_automated_point WHERE id='.$_SESSION['idap']);
            if ($sqlQuery){
                $sqlRow=  mysql_fetch_assoc($sqlQuery);
                if (($sqlRow['pwdReportPoSchetam']=='')||$sqlRow['pwdReportPoSchetam']==NULL){
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
                }else{
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>1)); die;
                }
            }
        }
        $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdReportPoSchetam="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(144),'resmsg'=>  errmsg(144))); die;
        }       
     break;
     }
     case 'checkItogReportPassword':{
        if (isset($_POST['pwd'])){
            if ($_POST['pwd']!='')
               $pwd = md5(FISH.md5($_POST['pwd']));
            else{
                $pwd=$_POST['pwd'];
            }
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
       $logStr="Запрос итогового отчета";
       addlog($logStr,17);
        if ($pwd==''){
            $sqlQuery=  mysql_query('SELECT pwdReportItogoviyReport FROM s_automated_point WHERE id='.$_SESSION['idap']);
            if ($sqlQuery){
                $sqlRow=  mysql_fetch_assoc($sqlQuery);
                if (($sqlRow['pwdReportItogoviyReport']=='')||$sqlRow['pwdReportItogoviyReport']==NULL){
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
                }else{
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>1)); die;
                }
            }
        }
        $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdReportItogoviyReport="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(145),'resmsg'=>  errmsg(145))); die;
        }       
     break;
     }
     case 'checkRefuseReportPassword':{
        if (isset($_POST['pwd'])){
            if ($_POST['pwd']!='')
               $pwd = md5(FISH.md5($_POST['pwd']));
            else{
                $pwd=$_POST['pwd'];
            }
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
       $logStr="Запрос отчета по отказам";
       addlog($logStr,17);
        if ($pwd==''){
            $sqlQuery=  mysql_query('SELECT pwdReportOtkaz FROM s_automated_point WHERE id='.$_SESSION['idap']);
            if ($sqlQuery){
                $sqlRow=  mysql_fetch_assoc($sqlQuery);
                if (($sqlRow['pwdReportOtkaz']=='')||$sqlRow['pwdReportOtkaz']==NULL){
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
                }else{
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>1)); die;
                }
            }
        }
        $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdReportOtkaz="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(147),'resmsg'=>  errmsg(147))); die;
        }       
     break;
     }
     case 'checkRefuse_and_ordersReportPassword':{
        if (isset($_POST['pwd'])){
            if ($_POST['pwd']!='')
               $pwd = md5(FISH.md5($_POST['pwd']));
            else{
                $pwd=$_POST['pwd'];
            }
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
       $logStr="Запрос отчета по отказам и заказам";
       addlog($logStr,17);
        if ($pwd==''){
            $sqlQuery=  mysql_query('SELECT pwdReportZakazOtkaz FROM s_automated_point WHERE id='.$_SESSION['idap']);
            if ($sqlQuery){
                $sqlRow=  mysql_fetch_assoc($sqlQuery);
                if (($sqlRow['pwdReportZakazOtkaz']=='')||$sqlRow['pwdReportZakazOtkaz']==NULL){
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
                }else{
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>1)); die;
                }
            }
        }
        $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdReportZakazOtkaz="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(146),'resmsg'=>  errmsg(146))); die;
        }       
     break;
     }
     case 'checkXreportReportPassword':{
        if (isset($_POST['pwd'])){
            if ($_POST['pwd']!='')
               $pwd = md5(FISH.md5($_POST['pwd']));
            else{
                $pwd=$_POST['pwd'];
            }
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
       $logStr="Запрос Х отчета";
       addlog($logStr,17);
        if ($pwd==''){
            $sqlQuery=  mysql_query('SELECT pwdReportX FROM s_automated_point WHERE id='.$_SESSION['idap']);
            if ($sqlQuery){
                $sqlRow=  mysql_fetch_assoc($sqlQuery);
                if (($sqlRow['pwdReportX']=='')||$sqlRow['pwdReportX']==NULL){
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
                }else{
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>1)); die;
                }
            }
        }
        $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdReportX="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(148),'resmsg'=>  errmsg(148))); die;
        }       
     break;
     }
     case 'pwdAddClient':{
        if (isset($_POST['pwd'])){
            if ($_POST['pwd']!='')
               $pwd = md5(FISH.md5($_POST['pwd']));
            else{
               $pwd=$_POST['pwd'];
            }
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
        
       $logStr="Начало добавления клиента";
       addlog($logStr,17);
//        $sqlGroup=mysql_query('(SELECT 0 as id, "Корень" as name) UNION (SELECT id,name FROM s_clients WHERE isgroup=1)');
            $sqlD =  mysql_query('SELECT IF (ISNULL(defaultFolderDuringAddClient),0,defaultFolderDuringAddClient) as defaultFolderDuringAddClient FROM s_automated_point WHERE id='.$_SESSION['idap']);
           if ($sqlD) {
                $sqlR= mysql_fetch_assoc($sqlD);
           }else{
               
           }
        if ($sqlR['defaultFolderDuringAddClient']==0){
            $sqlGroup=mysql_query('(SELECT 0 as id,"Корень" as name) UNION (SELECT id,name FROM s_clients WHERE parentid=0 and isgroup=1)');
        }else{
            $sqlGroup=mysql_query('SELECT id,name FROM s_clients WHERE id='.$sqlR['defaultFolderDuringAddClient'].' or parentid='.$sqlR['defaultFolderDuringAddClient'].' and isgroup=1');
        }
        if(mysql_num_rows($sqlGroup)>0){
            $rows = array();
                        while($r = mysql_fetch_assoc($sqlGroup)) {
                            $rows[] = $r;                   
                        }
        }else{
            $rows = '';
        }
       
        $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdAddClientFromFront="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0,'groups'=>$rows)); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(149),'resmsg'=>  errmsg(149))); die;
        }       
     break;
     }
     case 'doAddClient':{
         
          if (!isset($_POST['clientInfo'])){
              echo json_encode(array('rescode'=>  errcode(150),'resmsg'=>  errmsg(150))); die;       
          }else{
              $clientData=  $_POST['clientInfo'];
          };
          
          if ($clientData['card']!=''){
            $sqlCheck =  mysql_query('SELECT id FROM s_clients WHERE shtrih='.addslashes($clientData['card']));
            if (mysql_num_rows($sqlCheck)>0){
                echo json_encode(array('rescode'=>  errcode(152),'resmsg'=>  errmsg(152))); die;
            };    
          }
          
          if (addslashes($clientData['fio'])==''){
              echo json_encode(array('rescode'=>  errcode(153),'resmsg'=>  errmsg(153))); die;
          }
          
                                
          $sqlInsert=  mysql_query('INSERT INTO s_clients (parentid,name,shtrih,birthday,email,phone,address,info)
              VALUES('.addslashes($clientData['group']).',
                      "'.addslashes($clientData['fio']).'",
                       "'.addslashes($clientData['card']).'",
                       "'.addslashes($clientData['dateOfBirth']).'",
                       "'.addslashes($clientData['email']).'",
                       "'.addslashes($clientData['tel']).'",
                       "'.addslashes($clientData['adress']).'",
                      "'.addslashes($clientData['supInfo']).'")');
         
          $lastid =  mysql_insert_id();
          
         $logStr="Добавление клиента";
         $logStr.="<br>Сотрудник:".$_SESSION['employeeid'];
         $logStr.="<br>Имя клиента:".addslashes($clientData['fio']);
         $logStr.="<br>Номер карты:".addslashes($clientData['card']);
         $logStr.="<br>День рожденья:".addslashes($clientData['dateOfBirth']);
         $logStr.="<br>Email:".addslashes($clientData['email']);
         $logStr.="<br>Телефон:".addslashes($clientData['tel']);
         $logStr.="<br>Адрес:".addslashes($clientData['supInfo']);
         addlog($logStr,1);
         
         
         if ($sqlInsert){
             echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'lastid'=>$lastid)); die;
         }else{
             echo json_encode(array('rescode'=>  errcode(151),'resmsg'=>  errmsg(151))); die;
         }
     break;
     }
  
     case 'loginInToURV':{
           $query = "SELECT
                        ap.idpointurv as idp
                FROM
                        s_automated_point as ap
                WHERE
                        ap.id = ".$_SESSION['idap']."
                AND ap.useURV=1 and ap.idpointurv>0" ;
//$_SESSION['urvid']=mysql select urvid from s_automatedpoint where id=$_SESSION[''idap''] and useurv=1;
        $res = mysql_query($query);
        if ($res){
            if (mysql_num_rows($res)>0){
                $row = mysql_fetch_assoc($res);
                 $_SESSION['urvid']=$row["idp"];
                 $_SESSION['urv']=1;
//header("Location: /urv"); die;
                 echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'location'=>"/urv")); die;
            }
            else
            {
                echo json_encode(array('rescode'=>  errcode(154),'resmsg'=>  errmsg(154))); die;
            }
        }
        else{
             echo json_encode(array('rescode'=>  errcode(154),'resmsg'=>  errmsg(154))); die;
        }
     break;
     }
     case 'getTime':{
          echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'time'=>date("d.m.y H:i"))); die;
     break;
     }
     case 'pwdService':{
        if (isset($_POST['pwd'])){
            if ($_POST['pwd']!='')
               $pwd = md5(FISH.md5($_POST['pwd']));
            else{
                $pwd=$_POST['pwd'];
            } 
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
        $logStr="Выбор процента обслуживания";
       addlog($logStr,17);
        if ($pwd==''){
            $sqlQuery=  mysql_query('SELECT pwdservicepercent FROM s_automated_point WHERE id='.$_SESSION['idap']);
            if ($sqlQuery){
                $sqlRow=  mysql_fetch_assoc($sqlQuery);
                if (($sqlRow['pwdservicepercent']=='')||$sqlRow['pwdservicepercent']==NULL){
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
                }else{
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>1)); die;
                }
            }
        }
        $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdservicepercent="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(148),'resmsg'=>  errmsg(148))); die;
        }       
     break;
     }
     case 'checkPwdReturn':{
        if (isset($_POST['pwd'])){
            if ($_POST['pwd']!='')
               $pwd = md5(FISH.md5($_POST['pwd']));
            else{
                $pwd=$_POST['pwd'];
            }
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
       
       $logStr="Вход в интерфейс возврата";
       addlog($logStr,17);
        $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdReturn="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0))); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(141),'resmsg'=>  errmsg(141))); die;
        }       
     break;
     }
     case 'getComboGroup':{
        if (isset($_POST['itemid'])){
            $itemid=$_POST['itemid'];
        }else{
               echo json_encode(array('rescode'=>  errcode(157),'resmsg'=>  errmsg(157))); die;
        }; 
       
       $sqlQuery=  mysql_query('SELECT id,name,price, mincount,maxcount,defaultitem,0 as itemcount,itemid FROM s_combo_groups where itemid='.$itemid);
       if ($sqlQuery){
            $rows = array();
            while($r = mysql_fetch_assoc($sqlQuery)) {
                $sqlSubItems =  mysql_query('SELECT c.printer as printer,c.id as checkid,i.id as id,i.name as name,c.price as price,c.idcombogroup as idcombogroup,'.$r['price'].' as groupprice,'.$r['itemid'].' as parentid FROM s_combo_items as c
                                LEFT JOIN s_items as i ON i.id=c.itemid
                                where c.idcombogroup='.$r['id']);
                $rowsSub = array();
                while($rSub = mysql_fetch_assoc($sqlSubItems)) {
                    $rowsSub[] = $rSub;                   
                }
                $r['subItems']=$rowsSub;
                $rows[] = $r;  
            } 
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'rows'=>$rows)); die; 
       }else{
           echo json_encode(array('rescode'=>  errcode(158),'resmsg'=>  errmsg(158))); die;
       }
     break;
     }
     case 'checkComboPwd':{
        if (isset($_POST['pwd'])){
            if ($_POST['pwd']!='')
               $pwd = md5(FISH.md5($_POST['pwd']));
            else{
                $pwd=$_POST['pwd'];
            }
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
        $logStr="Вход в конструктор комбо меню";
       addlog($logStr,17);
        if ($pwd==''){
            $sqlQuery=  mysql_query('SELECT pwdCombo FROM s_automated_point WHERE id='.$_SESSION['idap']);
            
            if ($sqlQuery){
                $sqlRow=  mysql_fetch_assoc($sqlQuery);
                if (($sqlRow['pwdCombo']=='')||$sqlRow['pwdCombo']==NULL){
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
                }else{
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>1)); die;
                }
            }
        }
        $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdCombo="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(143),'resmsg'=>  errmsg(143))); die;
        }       
     break;
     }
     case 'getAddComboMenu':{
        
        $sqlQuery=  mysql_query('SELECT t.itemid as id,s.name as name FROM s_automated_point as ap
                                LEFT JOIN t_menu_items as t on t.menuid=ap.menuid
                                LEFT JOIN s_items as s on s.id=t.itemid
                                WHERE s.complex=1 and ap.id='.$_SESSION['idap'].'
                                GROUP BY t.itemid');
        
        $rows = array();
        while($r = mysql_fetch_assoc($sqlQuery)) {
            $rows[] = $r;                   
        }
         
        echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'rows'=>$rows)); die;
              
     break;
     }
     case 'getAddComboGroup':{
         if (isset($_POST['itemid'])){
                $itemid=$_POST['itemid']; 
        }
        else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
              
        $sqlQuery=  mysql_query('SELECT id,name,price,mincount,maxcount,defaultitem FROM s_combo_groups WHERE itemid='.addslashes($itemid));
        
        $rows = array(); 
        while($r = mysql_fetch_assoc($sqlQuery)) {
            $rows[] = $r;                   
        }
         
        echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'rows'=>$rows)); die;
              
     break;
     }
     case 'getAddComboGroupItems':{
         if (isset($_POST['itemid'])){
                $itemid=$_POST['itemid'];
        }
        else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
        
        $sqlQuery=  mysql_query('SELECT CONCAT(i.name,if(s.price=0,"<br>Цена:0",CONCAT("<br>Цена:",s.price)),if(ISNULL(sub. NAME),"<br>Принтер:Нет",CONCAT("<br>Принтер:",sub. NAME))) AS name, s.price as price,s.id as id FROM s_combo_items as s
                                LEFT JOIN s_items as i on i.id=s.itemid
                                LEFT JOIN s_subdivision as sub on sub.id=s.printer
                                WHERE s.idcombogroup='.addslashes($itemid));
        $rows = array();
        while($r = mysql_fetch_assoc($sqlQuery)) {
            $rows[] = $r;                   
        }
         
        echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'rows'=>$rows)); die;
              
     break;
     }
     case 'addNewComboGroup':{
         if (isset($_POST['row'])){
                $row=$_POST['row'];
        }
        else{
               echo json_encode(array('rescode'=>  errcode(159),'resmsg'=>  errmsg(159))); die;
        }; 
        
        if ($row['name']==''){
            echo json_encode(array('rescode'=>  errcode(161),'resmsg'=>  errmsg(161))); die;
        }
        if ($row['price']==''||$row['price']<0){
            echo json_encode(array('rescode'=>  errcode(162),'resmsg'=>  errmsg(162))); die;
        }
        if ($row['mincount']==''||$row['mincount']<0){
            echo json_encode(array('rescode'=>  errcode(163),'resmsg'=>  errmsg(163))); die;
        }
        if ($row['maxcount']==''||$row['maxcount']<0){
            echo json_encode(array('rescode'=>  errcode(164),'resmsg'=>  errmsg(164))); die;
        }
        if ($row['itemid']==''||$row['itemid']<0){
            echo json_encode(array('rescode'=>  errcode(165),'resmsg'=>  errmsg(165))); die;
        }
        if ($row['mincount']>$row['maxcount']){
            echo json_encode(array('rescode'=>  errcode(166),'resmsg'=>  errmsg(166))); die;
        }
        $sql='INSERT INTO s_combo_groups (name,price,mincount,maxcount,itemid) 
            VALUES (
               "'.addslashes($row['name']).'", 
               '.addslashes($row['price']).',   
               '.addslashes($row['mincount']).', 
               '.addslashes($row['maxcount']).',   
               '.addslashes($row['itemid']).'    
            )';
        
        $sqlQuery =  mysql_query($sql);
        
        $q =  mysql_query('SELECT name FROM s_items WHERE id='.addslashes($row['itemid']));
        $qr =  mysql_fetch_assoc($q);
        $itemname=$qr['name'];
        
        $logStr='Добавление комбо группы:'.addslashes($row['name']).";<br> Цена:".addslashes($row['price']).';<br> 
            Минимальное кол-во:'.addslashes($row['mincount']).'<br>
            Максимальное кол-во:'.addslashes($row['maxcount']).'<br>
            Комбо-блюдо:'.$itemname;
       
        addlog($logStr,1);
        echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0))); die;
              
     break;
     }
     case 'deleteComboGroup':{
         if (isset($_POST['groupId'])){
                $groupId=$_POST['groupId'];
        }
        else{
               echo json_encode(array('rescode'=>  errcode(159),'resmsg'=>  errmsg(159))); die;
        }; 
        
        $sql='select id from s_combo_items where idcombogroup='.addslashes($groupId);
        $sqlQuery =  mysql_query($sql);
        if (mysql_num_rows($sqlQuery)==0){
            $sql='DELETE FROM s_combo_groups WHERE id='.addslashes($groupId);
            $sqlQuery =  mysql_query($sql);
        }else{
            echo json_encode(array('rescode'=>  errcode(160),'resmsg'=>  errmsg(160))); die;
        }
        
        $q =  mysql_query('SELECT name FROM s_combo_groups WHERE id='.addslashes($groupId));
        $qr =  mysql_fetch_assoc($q);
        $itemname=$qr['name'];
        
        $logStr='Удаление комбо группы:'.$itemname;
        addlog($logStr,18);
        echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0))); die;
              
     break;
     }
     case 'getItemsFromGroup_combo':{
         if (!isset($_POST['parentid'])){
              echo json_encode(array('rescode'=>  errcode(130),'resmsg'=>  errmsg(130))); die;
          }else{
                $parentid=$_POST['parentid'];
          };
        $sqltext='';    
        
        if ($_SESSION['doNotUseMenuDesign']==0){
              $sql="SELECT                       
                        c.parentid as parentid,
                        c.isgroup as isgroup
                    FROM
                        s_automated_point AS ap
                    LEFT JOIN t_menu_items as c on c.menuid=ap.menuid
                    LEFT JOIN s_items as i on i.id=c.itemid
                    WHERE  c.id=".addslashes($parentid)." and ap.id=".$_SESSION['idap']." GROUP BY c.id
                    ORDER BY c.NAME DESC";
            $sqlUpperQuery=  mysql_query($sql);
            if (mysql_num_rows($sqlUpperQuery)>0){
                $upperRow=  mysql_fetch_assoc($sqlUpperQuery);          
                    $sqltext='(SELECT
                                    '.$upperRow['parentid'].' as parentid,
                                    '.$upperRow['isgroup'].' as isgroup,
                                    '.$upperRow['parentid'].' AS itemid,
                                    "← Назад" AS itemname,
                                    0 as id,
                                    0 as printer
                                   )
                        UNION ';
            }
            
                    
                                
                $sqltext.="(SELECT                       
                                c.parentid as parentid,
                                c.isgroup as isgroup,  
                                c.id AS itemid,
                                if(c.isgroup=1,CONCAT(i.name,' ','↓'),CONCAT(i.name,' ','→'))  as itemname,
                                c.itemid as id,
                                c.printer as printer
                            FROM
                                s_automated_point AS ap
                            LEFT JOIN t_menu_items as c on c.menuid=ap.menuid
                            LEFT JOIN s_items as i on i.id=c.itemid
                            WHERE  c.parentid=".addslashes($parentid)." and ap.id=".$_SESSION['idap']." GROUP BY c.id
                            ORDER BY c.id)";  
        }else if ($_SESSION['doNotUseMenuDesign']==1){
             $sql="SELECT                       
                        i.parentid as parentid,
                        i.isgroup as isgroup
                    FROM
                        s_items AS i
                    WHERE  i.id=".addslashes($parentid)." and i.i_useInMenu=1 GROUP BY i.id 
                    ORDER BY i.NAME DESC";
            $sqlUpperQuery=  mysql_query($sql);
            if (mysql_num_rows($sqlUpperQuery)>0){
                $upperRow=  mysql_fetch_assoc($sqlUpperQuery);          
                    $sqltext='(SELECT
                                    '.$upperRow['parentid'].' as parentid,
                                    '.$upperRow['isgroup'].' as isgroup,
                                    '.$upperRow['parentid'].' AS itemid,
                                    "← Назад" AS itemname,
                                    0 as id,
                                    0 as printer
                                   )
                        UNION ';
            }
            
                    
                                
                $sqltext.="(SELECT
                            i.parentid AS parentid,
                            i.isgroup AS isgroup,
                            i.id AS itemid,

                    IF (
                            i.isgroup = 1,
                            CONCAT(i. NAME, ' ', '↓'),
                            CONCAT(i. NAME, ' ', '→')
                    ) AS itemname,
                     i.id AS id,
                     i.i_printer AS printer
                    FROM
                            s_items AS i
                    WHERE
                            i.parentid = ".addslashes($parentid)." and i.i_useInMenu=1
                    GROUP BY
                            i.id
                    ORDER BY
                            i.id)";             
        }        
                
        $selectFolderQuery =  mysql_query($sqltext);
        
        if ($selectFolderQuery){
            $rows = array();
                while($r = mysql_fetch_assoc($selectFolderQuery)) {
                    $rows[] = $r;                   
                }
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>$rows));
        }  
        else{
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>0));
        }
     break;
     }
     case 'addComboGroupItem':{ 
         if (isset($_POST['row'])){
                $row=$_POST['row']; 
        }
        else{
               echo json_encode(array('rescode'=>  errcode(159),'resmsg'=>  errmsg(159))); die;
        }; 
        if (isset($_POST['groupid'])){
                $groupid=$_POST['groupid']; 
        }
        else{
               echo json_encode(array('rescode'=>  errcode(159),'resmsg'=>  errmsg(159))); die;
        }; 
        foreach ($row as $value) {
                        $sqlInsertTable = 'Insert Into s_combo_items (itemid,price,printer,idcombogroup) Values ';
                        $value['printer']=$value['printer']+0;
                        $sqlInsertTable .= '(' . addslashes($value['itemid']) . ','
                        . '' . addslashes($value['price']) . ','
                        . '' . addslashes($value['printer']) . ','        
                        . '' . addslashes($groupid) . '),';
                        $sql = substr($sqlInsertTable, 0, strlen($sqlInsertTable) - 1); 
                        $result = mysql_query($sql);
                        
                        $q =  mysql_query('SELECT name FROM s_items WHERE id='.addslashes($value['itemid']));
                        $qr =  mysql_fetch_assoc($q);
                        $itemname=$qr['name'];
                                   
                        $logStr='Товар:'.$itemname.'; Цена:'.addslashes($value['price']).';';
                    }      
                    
        $q =  mysql_query('SELECT name FROM s_combo_groups WHERE id='.addslashes($groupid));
        $qr =  mysql_fetch_assoc($q);
        $itemname=$qr['name'];
        
        addlog('Добавление товаров в комбо группу '.$itemname.' <br>'+$logStr,1);             
        echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0))); die;
              
     break;
     }  
     case 'deleteComboGroupItem':{
         if (isset($_POST['comboItemId'])){
                $comboItemId=$_POST['comboItemId'];
        }
        else{
               echo json_encode(array('rescode'=>  errcode(159),'resmsg'=>  errmsg(159))); die; 
        }; 
        
        if (isset($_POST['comboItemName'])){
                $comboItemName=$_POST['comboItemName'];
        }
        else{
               echo json_encode(array('rescode'=>  errcode(159),'resmsg'=>  errmsg(159))); die; 
        }; 
        
        $sql='DELETE FROM s_combo_items WHERE id='.addslashes($comboItemId);
        $sqlQuery =  mysql_query($sql);
        
        if ($sqlQuery){
            $logStr='Комбо блюда:'.$comboItemName;
            addlog($logStr,2);
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0))); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(169),'resmsg'=>  errmsg(169))); die;
        }
        
              
     break;
     }
     case 'setDefaultComboItem':{
         if (isset($_POST['defid'])){
                $defid=$_POST['defid']; 
        }
        else{
               echo json_encode(array('rescode'=>  errcode(159),'resmsg'=>  errmsg(159))); die; 
        }; 
        if (isset($_POST['groupid'])){
                $groupid=$_POST['groupid'];
        }
        else{
               echo json_encode(array('rescode'=>  errcode(159),'resmsg'=>  errmsg(159))); die; 
        }; 
        
        $sql='UPDATE s_combo_groups SET defaultitem='.addslashes($defid).' WHERE id='.addslashes($groupid);
        $sqlQuery =  mysql_query($sql);
        
        if($sqlQuery){
            $logStr=' комбо блюда по умолчанию.';
            addlog($logStr,2);
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0))); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(169),'resmsg'=>  errmsg(169))); die;
        }
        
        
              
     break;
     }
     case 'getComboGroupProperties':{    
        if (isset($_POST['groupid'])){
                $groupid=$_POST['groupid'];
        }
        else{
               echo json_encode(array('rescode'=>  errcode(159),'resmsg'=>  errmsg(159))); die; 
        }; 
        
        $sql='SELECT name,mincount,maxcount,price FROM s_combo_groups WHERE id='.addslashes($groupid);
        $sqlQuery =  mysql_query($sql);
       
        $rows = array(); 
        while($r = mysql_fetch_assoc($sqlQuery)) {
            $rows[] = $r;                   
        }
        
        echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'rows'=>$rows)); die;
              
     break;
     }
     case 'updateComboGroupProperties':{    
        if (isset($_POST['row'])){
                $row=$_POST['row'];
        }
        else{
               echo json_encode(array('rescode'=>  errcode(159),'resmsg'=>  errmsg(159))); die;
        }; 
        
        if ($row['name']==''){
            echo json_encode(array('rescode'=>  errcode(161),'resmsg'=>  errmsg(161))); die;
        }
        if ($row['price']==''||$row['price']<0){
            echo json_encode(array('rescode'=>  errcode(162),'resmsg'=>  errmsg(162))); die;
        }
        if ($row['mincount']==''||$row['mincount']<0){
            echo json_encode(array('rescode'=>  errcode(163),'resmsg'=>  errmsg(163))); die;
        }
        if ($row['maxcount']==''||$row['maxcount']<0){
            echo json_encode(array('rescode'=>  errcode(164),'resmsg'=>  errmsg(164))); die;
        }
        if ($row['itemid']==''||$row['itemid']<0){
            echo json_encode(array('rescode'=>  errcode(165),'resmsg'=>  errmsg(165))); die;
        }
        if ($row['mincount']>$row['maxcount']){
            echo json_encode(array('rescode'=>  errcode(166),'resmsg'=>  errmsg(166))); die;
        }
        $sql='UPDATE s_combo_groups SET name="'.addslashes($row['name']).'",
            price='.addslashes($row['price']).',
            mincount='.addslashes($row['mincount']).',
            maxcount='.addslashes($row['maxcount']).'
            WHERE id='.addslashes($row['itemid']); 
        $sqlQuery =  mysql_query($sql);
        
        if ($sqlQuery){
            $logStr="свойст комбо-группы Имя комбо группы:".addslashes($row['name']);
            $logStr.=" <br>Цена группы: ".addslashes($row['price']);
            $logStr.=" <br>Минимальное количество группы: ".addslashes($row['mincount']);
            $logStr.=" <br>Максимальное количество  группы: ".addslashes($row['maxcount']);
            addlog($logStr,2);
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0))); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(168),'resmsg'=>  errmsg(168))); die;
        }
              
     break;
     }
     case 'checkDiscountPassword':{
        if (isset($_POST['pwd'])){
            if ($_POST['pwd']!='')
               $pwd = md5(FISH.md5($_POST['pwd']));
            else{
                $pwd=$_POST['pwd'];
            }
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
        
       $logStr="Выбор ручных скидок";
       addlog($logStr,17);
        $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdDiscount="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(170),'resmsg'=>  errmsg(170))); die;
        }       
     break;
     }
     case 'loadDivisionsComboItem':{
        $result = mysql_query("SELECT id,name FROM s_subdivision ORDER BY id");
        if ($result){            
             while($r = mysql_fetch_assoc($result)) {
                    $rows[] = $r;
             }
             echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'division'=>$rows)); die;
        }else{
          echo json_encode(array('rescode'=>  errcode(16),'resmsg'=>  errmsg(16))); die;  
        }  
    break;
    } 
    
    case 'setPrinterComboItem':{
         if (isset($_POST['divid'])){
                $divid=$_POST['divid']; 
        }
        else{
               echo json_encode(array('rescode'=>  errcode(159),'resmsg'=>  errmsg(159))); die; 
        }; 
        if (isset($_POST['comboItemId'])){
                $comboItemId=$_POST['comboItemId'];
        }
        else{
               echo json_encode(array('rescode'=>  errcode(159),'resmsg'=>  errmsg(159))); die; 
        }; 
        
        $sql='UPDATE s_combo_items SET printer='.addslashes($divid).' WHERE id='.addslashes($comboItemId);
        $sqlQuery =  mysql_query($sql);
        
        if($sqlQuery){
            $logStr=' комбо блюда принтер.';
            addlog($logStr,2);
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0))); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(169),'resmsg'=>  errmsg(169))); die;
        }
     break;
     }
     case 'doReportSalon':{
          reportSalon(1);
     break;
     }
     
     case 'printReportSalon':{
                $output=reportSalon(-1);
                echo '<!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Справочники</title>
                        <script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
                       <style>*{font:20px Tahoma;margin:0;padding:0} h1{font:bold 20px Tahoma} b{font-weight:bold} table{margin: 0; padding: 0; border: 1px solid #000;border-collapse:collapse; width: 200px;}
                    table td{border: 1px dotted #CCCCCC; margin: 0; padding: 3px; border: 1px solid #000;}
                    .tableheader{background: #F3F3F3;}</style>
                    <script>$(document).ready(function() {
                    window.print();
                    setTimeout(function() {

                            window.close();                      

                    }, 200); 
                        });</script>
                    </head>
                    <body>';
                echo $output;
                echo '</body></html>';
         die;
     break;
     }  

     case 'getEmployeesFromGroup':{
                 $sqltext="SELECT                       
                                    e.parentid as parentid,
                                    e.isgroup as isgroup,  
                                    e.id AS employeeid,
                                    e.name as empname
                    FROM
                                    t_employee_workplace AS wp
                    LEFT JOIN s_employee as e on e.id=wp.employeeid
                    WHERE e.isgroup=0 and wp.wpid=".$_SESSION['wid']." GROUP BY e.id
                    ORDER BY e.id";  
                 $selectFolderQuery=  mysql_query($sqltext);    
                 if ($selectFolderQuery){
                        $rows = array();
                            while($r = mysql_fetch_assoc($selectFolderQuery)) {
                                $rows[] = $r;                   
                            }
                        echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>$rows));
                }  
                else{
                    echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>0));
                }
        break;
     }
     case 'pwdDeleteFromOrder':{
        if (isset($_POST['pwd'])){
            if ($_POST['pwd']!='')
               $pwd = md5(FISH.md5($_POST['pwd']));
            else{
                $pwd=$_POST['pwd'];
            }
        }else{
               echo json_encode(array('rescode'=>  errcode(85),'resmsg'=>  errmsg(85))); die;
        }; 
        $sqlQuery=  mysql_query('SELECT id FROM s_automated_point WHERE pwdDeleteFromOrder="'.$pwd.'" and id='.$_SESSION['idap']);
        if (mysql_num_rows($sqlQuery)>0){
            echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'usepassword'=>0)); die;
        }else{
            echo json_encode(array('rescode'=>  errcode(172),'resmsg'=>  errmsg(172))); die;
        }       
     break;
     } 
     case 'loadMastersIntoBsJournalTable':{
         
        $date=$_POST['date'];
        
        if (isset($_POST['interval'])){
            $interval=$_POST['interval'];
        }else{
            $interval=30;
        }
        
        
        
//        $interval=15;
        $sqltext="SELECT e.name as name,e.id as id
                   FROM s_employee as e
                   LEFT JOIN t_employee_workplace as et on et.employeeid=e.id
                   WHERE et.wpid=".$_SESSION['wid'].' ORDER BY e.name';    
        $selectEmp =  mysql_query($sqltext);
        
        $sqlP =  mysql_query('SELECT HOUR(jTimeStart)*60+MINUTE(jTimeStart) as jTimeStart,HOUR(jTimeEnd)*60+MINUTE(jTimeEnd) as jTimeEnd FROM s_automated_point WHERE id='.$_SESSION['idap']);
        $period = mysql_fetch_assoc($sqlP);
        
        $minTime=$period['jTimeStart']/60%60;
        $maxTime=$period['jTimeEnd']/60%60;
        if (!(($minTime>=0)&&($maxTime<24)&&($minTime<$maxTime)))
        {
            $minTime=7;
            $maxTime=22;
        }
        
        $sqltext2='SELECT
                            j.*,
                            cl.name as clientname,
                            HOUR(j.dtstart)*60+MINUTE(j.dtstart) as timeStart,
                            HOUR(j.dtend)*60+MINUTE(j.dtend) as timeEnd
                    FROM s_journal as j 
                    LEFT JOIN s_employee as e ON e.id=j.employeeid
                    LEFT JOIN s_clients as cl on cl.id=j.clientid
                    WHERE DATE(j.dtstart)="'.$date.'" and idap='.$_SESSION['idap'].' and HOUR(j.dtstart)*60+MINUTE(j.dtstart)>='.$period['jTimeStart'].' and HOUR(j.dtend)*60+MINUTE(j.dtend)<='.$period['jTimeEnd'].' and j.objectid=-1
                    ORDER BY
                            j.dtStart,e. NAME';
        $selectJournal=  mysql_query($sqltext2);

        
//        $content.='<table class="jurnal_css" style="float:left" >';
//        $content.='<tr>';
//        $content.='<td class="h_css">Время</td>';
//        $content.='</tr>';
//        for($j=$minTime;$j<$maxTime;$j++){
//                for($k=1;$k<=floor(60/$interval);$k++){
//                    $content.='<tr><td class="h_css">'.$j.':'.(($k-1)*($interval%60)).'</td></tr>';
//                }   
//        }
//        
//        $content.='</table>';
        
       
        if ($selectEmp){
            $content='<table class="jurnal_css" >';
            $content.='<tr>';
            $content.='<td class="h_css">Время</td>';
            $mCount=0;
            
            $array=array();
                while($r = mysql_fetch_assoc($selectEmp)) {
                    $array[$mCount]['id']=$r['id'];
                    $content.="<td>".$r['name']."</td>";
                    $mCount++;
                }
            $content.='</tr>';
            $rowTime = mysql_fetch_assoc($selectJournal);
            for($j=$minTime;$j<$maxTime;$j++){
                for($k=1;$k<=floor(60/$interval);$k++){
                    $content.='<tr>';
                    $q=0;
                        for ($i=0;$i<$mCount+1;$i++){
                            if ($i==0){
                                $str1='';
                               if ((($k-1)*($interval%60))==0){
                                   $str1='00';
                               }else{
                                   $str1=(($k-1)*($interval%60));
                               }
                               $content.='<td class="h_css">'.$j.':'.$str1.'</td>';
                               
                            }else{
                                $curMin=($j*60)+(($k-1)*($interval%60));
                                if ($rowTime['timeStart']>=$curMin&&$rowTime['timeStart']<$curMin+$interval){
                                    if ($rowTime['employeeid']==$array[$i-1]['id']){
                                        
                                        $content.='<td><div onmousedown="showJournalInf(this)" jID="'.$rowTime['id'].'" style="height:'.(floor(20*($rowTime['timeEnd']-$rowTime['timeStart'])/$interval)+1).'px;">'.
                                        $rowTime['clientname'].'<br>'.$rowTime['note'].'</div></td>';
              
                                        $rowTime = mysql_fetch_assoc($selectJournal);
                                    }else{
                                        $content.='<td></td>';
                                    }
                                }else{
                                    $content.='<td></td>'; 
                                }
                            }
                        }
                    $content.='</tr>';
                }
               
            }
            $content.='</table>';
            
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'content'=>$content));
        }  
        else{
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'content'=>0));
        }
     break;
     }
     case 'getEmployeeListForJournal':{
        $sqltext="SELECT e.id as id,e.name as name
                   FROM s_employee as e
                   LEFT JOIN t_employee_workplace as et on et.employeeid=e.id
                   WHERE et.wpid=".$_SESSION['wid'];    
        $selectEmp =  mysql_query($sqltext);
        if ($selectEmp){
                $rows=array();
                while($r = mysql_fetch_assoc($selectEmp)) {
                    $rows[]=$r;
                }
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>$rows));
        }  
        else{
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'content'=>0));
        }
     break;
     }
     case 'saveRecordToJournal':{
//        if (isset($_POST['typeJ'])){
//            $typeJ=$_POST['typeJ'];
//        }else{
//            echo json_encode(array('rescode'=>  errcode(175),'resmsg'=>  errmsg(175))); die;
//        }; 
        if (isset($_POST['type'])){
            $type=$_POST['type'];
        }else{
            echo json_encode(array('rescode'=>  errcode(175),'resmsg'=>  errmsg(175))); die;
        };    
        if (isset($_POST['dtBegin'])){
            $dtBegin=$_POST['dtBegin'];
        }else{
            echo json_encode(array('rescode'=>  errcode(176),'resmsg'=>  errmsg(176))); die;
        }; 
        if (isset($_POST['employeeid'])){
            $employeeid=$_POST['employeeid'];
        }else{
            echo json_encode(array('rescode'=>  errcode(177),'resmsg'=>  errmsg(177))); die;
        }; 
        if (isset($_POST['note'])){
            $note=$_POST['note'];
        }else{
            echo json_encode(array('rescode'=>  errcode(178),'resmsg'=>  errmsg(178))); die;
        }; 
        if (isset($_POST['clientid'])){
            $clientid=$_POST['clientid'];  
        }else{
            echo json_encode(array('rescode'=>  errcode(179),'resmsg'=>  errmsg(179))); die;
        }; 
        if (isset($_POST['objId'])){
            $objId=$_POST['objId'];  
        }else{
            echo json_encode(array('rescode'=>  errcode(179),'resmsg'=>  errmsg(179))); die;
        };
        if (isset($_POST['timeBegin'])){
            $timeBegin=$_POST['timeBegin'];  
        }else{
            echo json_encode(array('rescode'=>  errcode(180),'resmsg'=>  errmsg(180))); die;
        }; 
        if (isset($_POST['timeEnd'])){
            $timeEnd=$_POST['timeEnd'];  
        }else{
            echo json_encode(array('rescode'=>  errcode(181),'resmsg'=>  errmsg(181))); die;
        }; 
        
        $uTime1=explode(':', $timeBegin);
        $uTime2=explode(':', $timeEnd);
        
        $uTime1=$uTime1[0]*60+$uTime1[1];
        $uTime2=$uTime2[0]*60+$uTime2[1];
         
        
        $sqlP =  mysql_query('SELECT HOUR(jTimeStart)*60+MINUTE(jTimeStart) as jTimeStart,HOUR(jTimeEnd)*60+MINUTE(jTimeEnd) as jTimeEnd FROM s_automated_point WHERE id='.$_SESSION['idap']);
        $period = mysql_fetch_assoc($sqlP);
        
        $pTimeStart=$period['jTimeStart'];
        $pTimeEnd=$period['jTimeEnd'];
        
        
        if (((int)date('H',strtotime($timeBegin))*60)<$pTimeStart||((int)date('H',strtotime($timeEnd))*60)>$pTimeEnd){
            echo json_encode(array('rescode'=>  errcode(173),'resmsg'=>  'Время начала или время окончания выходят за рабочий диапазон!')); die;
        }
        
        $curDate = strtotime(date('Y-m-d'));
        $comeDate =  strtotime($dtBegin);
        
        $dtEnd=$dtBegin.' '.$timeEnd;
        $dtBegin=$dtBegin.' '.$timeBegin;
        
        if ($comeDate<$curDate){
           echo json_encode(array('rescode'=>  errcode(173),'resmsg'=>  'Дата записи меньше текущей даты!')); die; 
        }
        
        $curTime=strtotime(date('Y-m-d H:i'));
        $comeTime=strtotime($dtBegin);
        
        
        
        if ($timeEnd<=$timeBegin){
            echo json_encode(array('rescode'=>  errcode(173),'resmsg'=>  'Время начала не может быть больше времени окончания!')); die;
        }
        if ($curTime>$comeTime){
            echo json_encode(array('rescode'=>  errcode(173),'resmsg'=>  'Время записи меньше рабочего времени!')); die; 
        }
        
//        echo json_encode(array('rescode'=>  errcode(173),'resmsg'=> ' ДатаК'.$dtEnd.' ДатаН'.$dtBegin.' ВремяН'.$timeBegin.' ВремяК'.$timeEnd)); die;
        
        if ($type=='insert'){ 
            $sqlText='INSERT INTO s_journal SET
                      dtstart="'.addslashes($dtBegin).'",
                      dtend="'.addslashes($dtEnd).'",
                      authorid='.$_SESSION['employeeid'].',
                      employeeid='.addslashes($employeeid).',
                      objectid='.addslashes($objId).',    
                      clientid='.addslashes($clientid).',
                      note="'.addslashes($note).'",
                      idap='.$_SESSION['idap']; 
        }else if ($type=='update'){
            if (isset($_POST['jid'])){
                $jid=$_POST['jid'];  
            }else{
                echo json_encode(array('rescode'=>  errcode(182),'resmsg'=>  errmsg(182))); die;
            }; 
            $sqlText='UPDATE s_journal SET
                      dtstart="'.addslashes($dtBegin).'",
                      dtend="'.addslashes($dtEnd).'",
                      authorid='.$_SESSION['employeeid'].',
                      employeeid='.addslashes($employeeid).',
                      objectid='.addslashes($objId).',
                      clientid='.addslashes($clientid).',
                      note="'.addslashes($note).'",
                      idap='.$_SESSION['idap'].'
                      WHERE id='.$jid;
        }
        $insertQuery=  mysql_query($sqlText);
        if ($insertQuery){
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0)));
        }else{
           echo json_encode(array('rescode'=>  errcode(174),'resmsg'=>  $sqlText)); die; 
        }
        
     break;
     }
     case 'showJournalRecord':{
        $sqltext="SELECT 
                    DATE_FORMAT(j.dtstart, '%Y-%m-%d') AS dtstart,
                    DATE_FORMAT(j.dtstart, '%T') AS timestart,
                    DATE_FORMAT(j.dtend, '%Y-%m-%d') AS dtend,
                    DATE_FORMAT(j.dtend, '%T') AS timeend,
                    j.note,
                    cl. NAME AS clientname,
                    cl. id AS clientid,
                    emp. NAME AS employeename,
                    emp.id as employeeid,
                    cl.phone as tel
                FROM s_journal as j
                LEFT JOIN s_clients as cl on cl.id=j.clientid
                LEFT JOIN s_employee as emp on emp.id=j.employeeid
                WHERE j.id=".$_POST['jID'];    
        $selectEmp =  mysql_query($sqltext);
        if ($selectEmp){
            $r = mysql_fetch_assoc($selectEmp);
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>$r));
        }  
        else{
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'content'=>0));
        }
     break;
     }
     
     case 'cookDvs':{
        $str='';
            $sbDv=0;
            $sqltext1='SELECT id,name FROM s_subdivision'; 
            $subDiv=  mysql_query($sqltext1);
            $i=0;
            $str='';
            while ($g=  mysql_fetch_assoc($subDiv)){
                    $str.="<label style='margin-left: 20px;font-size: 20px;margin: 1em 0em 0em 2em;'>".$g['name']."</label><input id='".$g['id']."checkBxCookDv' idDv=".$g['id']."  type='checkbox' ></input>";
            } 
        echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'sel'=>$str));
     break;
     }
     
              
     case 'cookContent':{
         
        if (isset($_POST['subDiv'])){
                $sbDv=$_POST['subDiv'];  
        }else{
            echo json_encode(array('rescode'=>  errcode(182),'resmsg'=>  errmsg(182))); die;
        }; 
//        $str='';
//        
//        if ($sbDv==-1){
//            $sbDv=0;
//            $str='<option value="0">Все</option>'; 
//            $sqltext1='SELECT id,name FROM s_subdivision'; 
//            $subDiv=  mysql_query($sqltext1);
//            $i=0;
//            while ($g=  mysql_fetch_assoc($subDiv)){
////                    $str.="<input type='checkbox' style='margin-left: 20px;font-size: 40px;'>".$g['name']."</input>";
//                    $str.='<option value="'.$g['id'].'">'.$g['name'].'</option>';
//            } 
//        }
        
        $tmparray=checkChage();
        
        $sqltext="SELECT 
                        i.name as itemname,
                        t.quantity as count,
                        DATE_FORMAT(t.dt, '%T') as time,
                        e.name as fio,
                        t.note as note,
                        t.id as tid,
                        sub.name as subname
                FROM
                        d_order as d
                LEFT JOIN t_order as t on t.orderid=d.id
                LEFT JOIN s_employee as e on e.id=d.employeeid
                LEFT JOIN s_items as i on i.id=t.itemid
                LEFT JOIN d_changes as ch on ch.id=d.changeid
                LEFT JOIN s_subdivision as sub on sub.id=t.printerid
                WHERE (t.coocked<>1 or ISNULL(t.coocked)) and ch.id=".$tmparray['idchange']." and t.printerid<>0 ";
        
        
        if ($sbDv!=0){
            $sqltext.='and (';
            $g=0;
            foreach ($sbDv as $arr){
                if ($g==0){
                    $sqltext.="t.printerid=".$arr;
                }else{
                    $sqltext.=" or t.printerid=".$arr;
                }
                $g++;
            }
            $sqltext.=")";
        }
        $selectEmp =  mysql_query($sqltext);
        if ($selectEmp){
            $cont='<div class="cookbody">';
            
            while ($r = mysql_fetch_assoc($selectEmp)){
              if ($r['count']>0){
               $cont.= "<div onmousedown='cookItem(this)' tID='".$r['tid']."' class='infolabel infolabel-wait'>
                        <div class='countdiv'>x".$r['count']."</div>
                        <div class='timediv t1'>".$r['time']."</div>
                        <span class='infofio'>".$r['fio']."</span>
                        <span class='infoname'>".$r['itemname']."</span>
                        <span class='infolalka'><i class='glyphicon glyphicon-paperclip'></i>".$r['note']."</span>
                        <span style='font-family:  Verdana, Arial, sans-serif; font-size:8pt; font-weight:900; color:black'> Подразделение: ".$r['subname']."</span>
                        </div>";
              }else{
                  $cont.= "<div onmousedown='cookItem(this)' tID='".$r['tid']."' class='infolabel infolabel-danger'>
                        <div class='countdiv'>x".$r['count']."</div>
                        <div class='timediv t1'>".$r['time']."</div>
                        <span class='infofio'>".$r['fio']."</span>
                        <span class='infoname'>".$r['itemname']."</span>
                        <span class='infolalka'><i class='glyphicon glyphicon-paperclip'></i>".$r['note']."</span>
                        <span style='font-family:  Verdana, Arial, sans-serif; font-size:8pt; font-weight:900; color:black'> Подразделение: ".$r['subname']."</span>
                        </div>";
              }
            }
            $cont.="</div>";
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'cont'=>$cont));
        }  
        else{
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'cont'=>"err"));
        }
     break;
     }
     case 'cookingItem':{
        if (isset($_POST['tid'])){
                $tid=$_POST['tid'];  
        }else{
            echo json_encode(array('rescode'=>  errcode(182),'resmsg'=>  errmsg(182))); die;
        };      
        $sqltext="UPDATE t_order SET
                 coocked=1
                 WHERE id=".$tid;    
        $updCckd =  mysql_query($sqltext);
        if ($updCckd){
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0)));
        }  
        else{
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0)));
        }
     break;
     }
     case 'refreshMenu_getTime_check_ver_updateConf':{
         
          if (!isset($_SESSION['lastupdate'])){
              $needRefreshMenu=false;
          };  
          
            $lastUpdateQuery =  mysql_query('SELECT c.value as lastupdate_conf  FROM s_config as c WHERE c.key="front_lastupdate_conf"');
            if ($lastUpdateQuery){

                if (mysql_num_rows($lastUpdateQuery)==0){
                    $lastUpdateQuery1 =  mysql_query('INSERT INTO s_config SET `name`="автообновление конфига",`key`="front_lastupdate_conf",`value`=NOW()');
                    $lastUpdateQuery1 =  mysql_query('SELECT  `value` FROM s_config WHERE `key`="front_lastupdate_conf"');
                    $laR=  mysql_fetch_assoc($lastUpdateQuery1);
                    $_SESSION['lastupdate_conf']=$laR['value'];
                    $needRefreshConf=1;
                }else{
                    $tmprow=  mysql_fetch_assoc($lastUpdateQuery);
                    $t1=new DateTime($tmprow['lastupdate_conf']);
                    $t2=new DateTime($_SESSION['lastupdate_conf']);
                    if ($t1!=$t2){
                        $_SESSION['lastupdate_conf']=$tmprow['lastupdate_conf'];
                        //echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  $t1.'---'.$t2,'needRefresh'=>1)); die;        
                        $needRefreshConf=1;
                    }else{
                        $needRefreshConf=0;
                    }
                }
            }else{
               echo json_encode(array('rescode'=>  errcode(121),'resmsg'=>  errmsg(121))); die; 
            }
          
          if ($_SESSION['doNotUseMenuDesign']==0){
                $lastUpdateQuery =  mysql_query('SELECT lastupdate FROM s_menu');
                if ($lastUpdateQuery){
                    $tmprow=  mysql_fetch_assoc($lastUpdateQuery);
                    $t1=new DateTime($tmprow['lastupdate']);
                    $t2=new DateTime($_SESSION['lastupdate']);
                    if ($t1!=$t2){
                        $_SESSION['lastupdate']=$tmprow['lastupdate'];
                        //echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  $t1.'---'.$t2,'needRefresh'=>1)); die;        
                        $needRefreshMenu=1;
                    }else{
                        $needRefreshMenu=0;
                    }
                }else{
                   echo json_encode(array('rescode'=>  errcode(121),'resmsg'=>  errmsg(121))); die; 
                }
          }else{
              $lastUpdateQuery =  mysql_query('SELECT c.value as lastupdate  FROM s_config as c WHERE c.key="front_lastupdate_menu"');
                if ($lastUpdateQuery){
                    
                    if (mysql_num_rows($lastUpdateQuery)==0){
                        $lastUpdateQuery1 =  mysql_query('INSERT INTO s_config SET `name`="автообновление меню без дизайнера",`key`="front_lastupdate_menu",`value`=NOW()');
                        $lastUpdateQuery1 =  mysql_query('SELECT  `value` FROM s_config WHERE `key`="front_lastupdate_menu"');
                        $laR=  mysql_fetch_assoc($lastUpdateQuery1);
                        $_SESSION['lastupdate']=$laR['value'];
                        $needRefreshMenu=1;
                    }else{
                        $tmprow=  mysql_fetch_assoc($lastUpdateQuery);
                        $t1=new DateTime($tmprow['lastupdate']);
                        $t2=new DateTime($_SESSION['lastupdate']);
                        if ($t1!=$t2){
                            $_SESSION['lastupdate']=$tmprow['lastupdate'];
                            //echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  $t1.'---'.$t2,'needRefresh'=>1)); die;        
                            $needRefreshMenu=1;
                        }else{
                            $needRefreshMenu=0;
                        }
                    }
                }else{
                   echo json_encode(array('rescode'=>  errcode(121),'resmsg'=>  errmsg(121))); die; 
                }
          } 
          
          echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'needRefresh'=>$needRefreshMenu,'needRefreshConf'=>$needRefreshConf,'time'=>date("d.m.y H:i"),'version'=>VERSION)); die; 
     break;
     }
     
     
      case 'getStickerInf':{        
          $shtrih=$_POST['shtrih'];
            
          $objListQuery =  mysql_query('SELECT id,name,price,mainShtrih FROM s_items WHERE  mainShtrih="'.$shtrih.'"');
          
          if ($objListQuery){
                $r=mysql_fetch_assoc($objListQuery);   
                echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'arr'=>$r)); die; 
          }else{
             echo json_encode(array('rescode'=>  errcode(121),'resmsg'=>  errmsg(121))); die; 
          }
     break;
     }
      case 'canCloseChange':{        
            $divChange=divChangeWorkplace();
            if ($divChange==0){
                $sqlchange =  mysql_query('select id,closed from d_changes where idautomated_point='.$_SESSION['idap'].' order by id desc limit 1');
            }else{
                $sqlchange =  mysql_query('select id,closed from d_changes where idautomated_point='.$_SESSION['idap'].' and idworkplace='.$_SESSION['wid'].' order by id desc limit 1');
            }
            if (mysql_num_rows($sqlchange)>0){
               $tmparray = mysql_fetch_assoc($sqlchange);
               $idchange=$tmparray['id'];
               $closed=$tmparray['closed'];
            }else{
               $idchange=0;
               $closed=1;
            }              
                
            $tmp =  mysql_query('select COUNT(id) as count from d_order where closed<>1 and changeid='.$idchange);
            $haveorders = mysql_fetch_assoc($tmp);

            if ($haveorders['count']==0){
                echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'canClose'=>1)); die; 
            }else{
                echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'canClose'=>0)); die;  
            } 
     break;
     }
     case 'getEmployee':{        
            $sqltext="SELECT e.id,e.fio FROM s_employee as e
                        LEFT JOIN t_employee_workplace as te on te.employeeid=e.id
                        WHERE te.wpid=".$_SESSION['wid'];  
                 $selectFolderQuery=  mysql_query($sqltext);    
                 if ($selectFolderQuery){
                            $str='<option value="0" >Выберите сотрудника</option>';
                            while($r = mysql_fetch_assoc($selectFolderQuery)) {
                                $str.='<option value="'.$r['id'].'" >'.$r['fio'].'</option>';
                            }
                        echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>$str));
                }  
                else{
                    echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0),'rows'=>0));
                }
     break;
     }
     case 'changeEmployee':{ 
                    
                if (isset($_POST['orderid'])){
                    $orderid=$_POST['orderid'];  
                }else{
                    echo json_encode(array('rescode'=>  errcode(2),'resmsg'=>  errmsg(2))); die;
                };
                
                if (isset($_POST['empid'])){
                    $empid=$_POST['empid'];  
                }else{
                    echo json_encode(array('rescode'=>  errcode(19),'resmsg'=>  errmsg(19))); die;
                };
                
                $sqltext="SELECT 
                                emp.name as name
                        FROM
                                d_order as d
                        LEFT JOIN s_employee as emp ON emp.id=d.employeeid 
                        WHERE
                                d.id=".$orderid;  
                
                
                
                $Query=mysql_query($sqltext);
                $row=  mysql_fetch_assoc($Query);
                $oldEmpName=$row['name'];
                
                $sqltext="SELECT name FROM s_employee WHERE id=".$empid;  
                $Query=mysql_query($sqltext);
                $row=  mysql_fetch_assoc($Query);
                $newEmpName=$row['name'];
                
                $sqltext="UPDATE d_order SET employeeid=".$empid." WHERE id=".$orderid;  
                $Query=mysql_query($sqltext);    
                 if ($Query){
                        addlog('Смена сорудника c '.$oldEmpName.' на '.$newEmpName,21);
                        echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0)));
                }  
                else{
                    echo json_encode(array('rescode'=> errcode(183),'resmsg'=>  errmsg(183)));
                }
     break;
     }  
     case 'doConductAfterSale':{
            $sqltext="SELECT warehouseid FROM s_automated_point WHERE id=".$_SESSION['idap'];  
            $Query=mysql_query($sqltext);
            $row=  mysql_fetch_assoc($Query);
            if ($row['warehouseid']!=0){
                conductLastChange();   
            }
     break;
     }
default :{   
    echo json_encode(array('rescode'=>  errcode(1),'resmsg'=>  errmsg(1))); die;
    break;
}
    
}



?>
