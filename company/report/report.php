<?PHP  header("Content-Type: text/html; charset=utf-8");
session_start();
include('../check.php');
checksessionpassword();
include('../mysql.php');
include('../errors.php');
include('../functions.php');
include('../core.php');

error_reporting(E_ALL ^ E_NOTICE);

if (isset($_SESSION['timezone'])){ 
    date_default_timezone_set($_SESSION['timezone']); 
    mysql_query("SET `time_zone` = '".date('P')."'"); 
}                                       

switch($_GET['do']){
    case 'otchet':
                $ztime=time();
                if ($_GET['type']=='akt') $checktt='gethtml_akt_real';
                if ($_GET['type']=='poschetam') $checktt='gethtml_poschetam';
                if ($_GET['type']=='refuse') $checktt='gethtml_refuse';
                if ($_GET['type']=='refuse_and_orders') $checktt='gethtml_refuse_and_orders';
                if ($_GET['type']=='hoursales') $checktt='gethtml_hoursales';
                if ($_GET['type']=='posotrudnikam') $checktt='gethtml_posotr';
                if (!checkrights($checktt,1)) die(PERMISSION_DENIED);
                
            //Отчеты из 1 таблицы 
            
             //Вывод на экран
             $output='';
             //Сортировка
             $order='';
             //Условие
             $where='';
             //Группировка
             $group='';
             //В эксель выводим бордеры у таблиц
             $inxls='';
             //Тип отчета 
             $global_type=$_GET['type'];
             
             if (isset($_SESSION['idap'])&&(!isset($_SESSION['fromfront']))){
                 //echo '123';
                 $idwp = '';
                 $result = mysql_query( 'SELECT divChangeWorkplace FROM s_automated_point WHERE id='.addslashes($_SESSION['idap']).' LIMIT 1');
                 $row = mysql_fetch_array( $result );
                 $idwp = $row['divChangeWorkplace'] == '1' ? ' AND idworkplace='.( isset( $_SESSION['wid'] ) ? $_SESSION['wid'] : '1' ) : '';
                 
                 $result=mysql_query('select id, closed from d_changes where idautomated_point='.addslashes($_SESSION['idap']).$idwp.' order by id desc limit 1');
                 if (mysql_numrows($result)){
                     $row=mysql_fetch_array($result);
                     $_POST['chb']='zasmenu';
                     $_POST['chb_zasmenu']=$row['id'];
                     $_POST['idautomated_point']=$_SESSION['idap'];
                     //$_POST['order']='name';
                     $output[]='<style>.ttda{margin: 0; padding: 0; }
       .ttda td{border: 1px dotted #CCCCCC; margin: 0; padding: 3px;}</style>';
                 }
                 
             }
             

             
             
             //Принт версия
             if(isset($_GET['print'])){
                 $inxls='border="1"';
                 if (isset($_GET['chb'])) $_POST['chb']=$_GET['chb'];
                 if (isset($_GET['chb_zasmenu'])) $_POST['chb_zasmenu']=$_GET['chb_zasmenu'];
                 if (isset($_GET['chb_zaperiod1'])) $_POST['chb_zaperiod1']=$_GET['chb_zaperiod1'];
                 if (isset($_GET['chb_zaperiod2'])) $_POST['chb_zaperiod2']=$_GET['chb_zaperiod2'];
                 if (isset($_GET['chb_smenperiod1'])) $_POST['chb_smenperiod1']=$_GET['chb_smenperiod1'];
                 if (isset($_GET['chb_smenperiod2'])) $_POST['chb_smenperiod2']=$_GET['chb_smenperiod2'];
                 if (isset($_GET['datailed'])) $_POST['datailed']=$_GET['datailed'];
                 if (isset($_GET['idautomated_point'])) $_POST['idautomated_point']=$_GET['idautomated_point'];
                 if (isset($_GET['noorder'])) $_POST['noorder']=$_GET['noorder'];
                 if (isset($_GET['order'])) $_POST['order']=$_GET['order'];
                 if (isset($_GET['groupByDate'])) $_POST['groupByDate']=$_GET['groupByDate'];
                 if (isset($_GET['groupByChange'])) $_POST['groupByChange']=$_GET['groupByChange'];
                 if (isset($_GET['dateInRow'])) $_POST['dateInRow']=$_GET['dateInRow'];
                 if (isset($_GET['dontShowPrice'])) $_POST['dontShowPrice']=$_GET['dontShowPrice'];
                 if (isset($_GET['dontShowQuantity'])) $_POST['dontShowQuantity']=$_GET['dontShowQuantity'];
                 if (isset($_GET['showCostPrice'])) $_POST['showCostPrice']=$_GET['showCostPrice'];
                 if (isset($_GET['showEarnings'])) $_POST['showEarnings']=$_GET['showEarnings'];
                 if (isset($_GET['dateInRow'])) $_POST['dateInRow']=$_GET['dateInRow'];
                 if (isset($_GET['showIdLink'])) $_POST['showIdLink']=$_GET['showIdLink'];
                 if (isset($_GET['groupByAP'])) $_POST['groupByAP']=$_GET['groupByAP'];
//ФИЛЬТРЫ
                 if (isset($_GET['itemid'])) $_POST['itemid']=$_GET['itemid'];
                 if (isset($_GET['notitem'])) $_POST['notitem']=$_GET['notitem'];
                 if (isset($_GET['divisionid'])) $_POST['divisionid']=$_GET['divisionid'];
                 if (isset($_GET['notdiv'])) $_POST['notitem']=$_GET['notdiv'];
                 if (isset($_GET['employeeid'])) $_POST['employeeid']=$_GET['employeeid'];
                 if (isset($_GET['notemp'])) $_POST['notemp']=$_GET['notemp'];
                 if (isset($_GET['clientid'])) $_POST['clientid']=$_GET['clientid'];
                 if (isset($_GET['notcl'])) $_POST['notcl']=$_GET['notcl'];
                 if (isset($_GET['paymentid'])) $_POST['paymentid']=$_GET['paymentid'];
                 if (isset($_GET['notpay'])) $_POST['notpay']=$_GET['notpay'];
                 

                 
                 
             }else{
                 //Формирование ссылки на кнопку "В файл"
                $query_string=array();
                if (isset($_POST['chb'])) $query_string[]='chb='.$_POST['chb'];
                if (isset($_POST['chb_zasmenu'])) $query_string[]='chb_zasmenu='.$_POST['chb_zasmenu'];
                if (isset($_POST['chb_zaperiod1'])) $query_string[]='chb_zaperiod1='.$_POST['chb_zaperiod1'];
                if (isset($_POST['chb_zaperiod2'])) $query_string[]='chb_zaperiod2='.$_POST['chb_zaperiod2'];
                if (isset($_POST['chb_smenperiod1'])) $query_string[]='chb_smenperiod1='.$_POST['chb_smenperiod1'];
                if (isset($_POST['datailed'])) $query_string[]='datailed='.$_POST['datailed'];
                if (isset($_POST['chb_smenperiod2'])) $query_string[]='chb_smenperiod2='.$_POST['chb_smenperiod2'];
                if (isset($_POST['idautomated_point'])) $query_string[]='idautomated_point='.$_POST['idautomated_point'];
                if (isset($_POST['groupByAP'])) $query_string[]='groupByAP='.$_POST['groupByAP'];
                if (isset($_POST['noorder'])) $query_string[]='noorder='.$_POST['noorder'];
                if (isset($_POST['order'])) $query_string[]='order='.$_POST['order'];
                if (isset($_GET['type'])) $query_string[]='type='.$_GET['type'];
                if (isset($_POST['groupByDate'])) $query_string[]='groupByDate='.$_POST['groupByDate'];
                if (isset($_POST['groupByChange'])) $query_string[]='groupByChange='.$_POST['groupByChange'];
                if (isset($_POST['dateInRow'])) $query_string[]='dateInRow='.$_POST['dateInRow'];
                if (isset($_POST['dontShowPrice'])) $query_string[]='dontShowPrice='.$_POST['dontShowPrice'];
                if (isset($_POST['dontShowQuantity'])) $query_string[]='dontShowQuantity='.$_POST['dontShowQuantity'];
                if (isset($_POST['showCostPrice'])) $query_string[]='showCostPrice='.$_POST['showCostPrice'];
                if (isset($_POST['showEarnings'])) $query_string[]='showEarnings='.$_POST['showEarnings'];
//ФИЛЬТРЫ
                if (isset($_POST['showIdLink'])) $query_string[]='showIdLink='.$_POST['showIdLink'];
                if (isset($_POST['apInRow'])) $query_string[]='apInRow='.$_POST['apInRow'];
                if (isset($_POST['itemid'])) $query_string[]='itemid='.$_POST['itemid'];
                if (isset($_POST['notitem'])) $query_string[]='notitem='.$_POST['notitem'];
                if (isset($_POST['divisionid'])) $query_string[]='divisionid='.$_POST['divisionid'];
                if (isset($_POST['notdiv'])) $query_string[]='notdiv='.$_POST['notdiv'];
                if (isset($_POST['employeeid'])) $query_string[]='employeeid='.$_POST['employeeid'];
                if (isset($_POST['notemp'])) $query_string[]='notemp='.$_POST['notemp'];
                if (isset($_POST['clientid'])) $query_string[]='clientid='.$_POST['clientid'];
                if (isset($_POST['notcl'])) $query_string[]='notcl='.$_POST['notcl'];
                if (isset($_POST['paymentid'])) $query_string[]='paymentid='.$_POST['paymentid'];
                if (isset($_POST['notpay'])) $query_string[]='notpay='.$_POST['notpay'];
                
                
                
             }
            $output[]='<span style="font:10px Tahoma;">Время формирования отчета: '.date('d.m.Y H:i:s').'</span>';
             $output[]='<div class="div_otchet">';
             if (!isset($_POST['noorder'])){
                 $desc='';
                 if (isset($_POST['orderdesc'])){
                     $desc=' DESC';
                 }
                 if (isset($_POST['order'])){
                    $order=addslashes($_POST['order']).$desc;
                 }else $order='id';
             }else{
                 $order='id';
             }
             
             
             
             if (isset($_POST['groupbydate'])){
                 $group='date';
             }
             
             switch ($global_type){
                 case 'akt':
                    $output[]=' <h1>Акт реализации</h1>';
                 break;
                 case 'poschetam':
                    $output[]=' <h1>Отчет по счетам</h1>';
                 break;
                 case 'refuse':
                    $output[]=' <h1>Отчет по отказам</h1>';
                 break;
                 case 'refuse_and_orders':
                    $output[]=' <h1>Отчет по зазакам и отказам</h1>';
                 break;
                 case 'hoursales':
                    $output[]=' <h1>Отчет по продажам по часам</h1>';
                 break;
                 case 'posotrudnikam':
                    $output[]=' <h1>Отчет по сотрудникам</h1>';
                 break;
             }
             
             
             
            
             $i=1;
             $title='';
             
             //ФИЛЬТРА 
             $where=array();
             if ($_POST['idautomated_point']>0){
                 $where[]='d_order.idautomated_point="'.addslashes($_POST['idautomated_point']).'"';
             } else if ( !isset( $_SESSION['admin'] ) ){
                 $res = mysql_query( 'SELECT rollid, GROUP_CONCAT(DISTINCT tid) AS tid FROM `t_employee_interface` AS i LEFT JOIN `z_user_right` AS r ON r.uid=i.employeeid AND `view`=1 WHERE i.employeeid='.$_SESSION['userid'] );
                 $tid = mysql_fetch_array( $res );
                 if ( $tid['rollid'] == 2 ){
                    if ( $tid['tid'] == '' )
                        $where[]='d_order.idautomated_point="-1"';//die(PERMISSION_DENIED)
                    else 
                        $where[]='d_order.idautomated_point IN ('.$tid['tid'].')';
                 }
             }
                    
             switch($_POST['chb']){
                 case 'zasmenu':
                     if ($_POST['chb_zasmenu']>0){
                        $where[]='changeid="'.addslashes($_POST['chb_zasmenu']).'"';
                         
                         $query=mysql_query("SELECT * FROM `d_changes` WHERE id='".addslashes($_POST['chb_zasmenu'])."' LIMIT 1");
                         $row=mysql_fetch_assoc($query);
                         $title='За смену: '.$row['name'].'_'.$row['dtopen'].'_'.$row['dtclosed'];
                     }
                 break;
                 case 'zaperiod':
                     if (($_POST['chb_zaperiod1']!='')&&($_POST['chb_zaperiod2']!='')){
                        $where[]='((d_order.creationdt>="'.date('Y-m-d H:i:s',strtotime($_POST['chb_zaperiod1'])).'") AND (d_order.creationdt<="'.date('Y-m-d H:i:s',strtotime($_POST['chb_zaperiod2'])).'"))';
                        
                         $title='За период: с '.$_POST['chb_zaperiod1'].' по '.$_POST['chb_zaperiod2'];
                         
                     }
                 break;
                 case 'smenperiod':
                     if (($_POST['chb_smenperiod1']!='')&&($_POST['chb_smenperiod2']!='')){
                        $where[]='((d_changes.dtopen>="'.date('Y-m-d H:i:s',strtotime($_POST['chb_smenperiod1'])).'") AND (d_changes.dtopen<="'.date('Y-m-d H:i:s',strtotime($_POST['chb_smenperiod2'])).'"))';
                        $title='Смены за период: с '.$_POST['chb_smenperiod1'].' по '.$_POST['chb_smenperiod2'];
                     }
                 break;
             }

             $output[]=$title.'<br />';
             
            $apInRow = isset( $_POST["apInRow"] );
            $dateInRow = isset( $_POST["dateInRow"] );
            $showIdLink = isset( $_POST["showIdLink"] );
            $showPrice = !isset( $_POST["dontShowPrice"] );
            $showQuantity = !isset( $_POST["dontShowQuantity"] );
            $showCostPrice = isset( $_POST["showCostPrice"] );
            $showEarnings = $showCostPrice && isset( $_POST["showEarnings"] );

             switch ($global_type){
                 case 'akt':
                    $output[]='<table class="ttda" '.$inxls.'>
                        <tr class="tableheader">
                            <td>#</td>
                            '.( $apInRow ? '<td>Торговый объект</td>' : '' ).'
                            '.( $dateInRow ? '<td>Дата</td>' : '' ).'
                            '.( $showIdLink ? '<td>Код товара</td>' : '' ).'
                            <td>Наименование</td>
                            <td>Кол-во</td>
                            '.( $showCostPrice ? '<td>Себестоимость</td>' : '' ).'
                            '.( $showCostPrice ? '<td>Сумма себ.</td>' : '' ).'
                            <td>Цена прод.</td>
                            <td>Сумма прод.</td>
                            '.( $showEarnings ? '<td>Доход</td>' : '' ).'
                            '.( $showEarnings ? '<td>Доход, %</td>' : '' ).'
                        </tr>';
                 break;
                 case 'poschetam':
                 $output[]='<table class="ttda" '.$inxls.'>
                        <tr class="tableheader">
                            <td>#</td>
                            <td>№счета</td>
                            <td>Официант</td>
                            <td>Торговый объект.</td>
                            <td>Клиент</td>
                            <td>Гостей</td>
                            <td>Вид оплаты</td>
                            <td>Дата открытия</td>
                            <td>Сумма</td>
                            <td>Сумма обслуживания</td>
                            <td>Сумма скидки</td>
                            <td>Итоговая сумма</td>
                        </tr>';
                 break;
                 case 'refuse':
                 $output[]='<table class="ttda" '.$inxls.'>
                        <tr class="tableheader">
                            <td>#</td>
                            <td>№счета</td>
                            <td>Официант</td>
                            <td>Дата открытия</td>
                            <td>Товар</td>
                            <td>Количество</td>
                            <td>Сумма</td>
                            <td>Ед. изм.</td>   
                            <td>Примечание</td>                       
                        </tr>';
                 break; 
                 case 'refuse_and_orders':
                 $output[]='<table class="ttda" '.$inxls.'>
                        <tr class="tableheader">
                            <td>#</td>
                            <td>№счета</td>
                            <td>Официант</td>
                            <td>Дата открытия</td>
                            
                            <td>Товар</td>
                            <td>Количество</td>
                            <td>Сумма</td>
                            <td>Ед. изм.</td>     
                            <td>Примечание</td>                     
                        </tr>';
                 break;
                 case 'hoursales':
                    $output[]='<table class="ttda" '.$inxls.'>
                           <tr class="tableheader">
                               <td>#</td>
                               <td>Товар</td>';
                    for ($i=0;$i<=23; $i++)
                       $output[]='<td>'.$i.'</td>';
                    $output[]='<td>Итого</td></tr>';
                 break;
                 case 'posotrudnikam':
                    $output[]='<table class="ttda" '.$inxls.'>';
                 break;
             }
            
            switch ($global_type){
                 case 'akt':                     
                    $showComplex = isset( $_POST["showComplex"] );
                    $groupByAP = isset( $_POST["groupByAP"] );
                    $groupByDate = isset( $_POST["groupByDate"] );
                    $groupByChange = !isset( $_POST["groupByDate"] ) && isset( $_POST["groupByChange"] );
                    $groupByClient = isset( $_POST["groupByClient"] );
                    $groupBy = $groupByAP ? ' apname, ' : '';
                    $groupBy .= $groupByDate || $groupByChange ? ' dt, ' : '';
                    $groupBy .= $groupByClient ? ' clientname, ' : '';
                    $dtfield = "DATE(d_order.creationdt)";
                    $joinChanges = " d_changes ";
                    
                    $itemid = isset( $_POST['itemid'] ) ? intval( $_POST['itemid'] ) : 0;
                    if ( $itemid > 0 ) $where[] = '(t_order.itemid'.( isset( $_POST['notitem'] ) ? '!' : '' ).'='.$itemid
.' '.( isset( $_POST['notitem'] ) ? 'AND ' : 'OR ' ).'s_items.parentid '.( isset( $_POST['notitem'] ) ? 'NOT ' : '' ).'IN ('.substr( get_parents( $itemid ), 0, -1 ).'))';
                    
                    $divisionid = isset( $_POST['divisionid'] ) ? intval( $_POST['divisionid'] ) : 0;
                    if ( $divisionid > 0 ) $where[] = 't_order.printerid'.( isset( $_POST['notdiv'] ) ? '!' : '' ).'='.$divisionid;
                    
                    $clientid = isset( $_POST['clientid'] ) ? intval( $_POST['clientid'] ) : 0;
                    if ( $clientid > 0 ) $where[] = '(d_order.clientid'.( isset( $_POST['notcl'] ) ? '!' : '' ).'='.$clientid
.' '.( isset( $_POST['notcl'] ) ? 'AND ' : 'OR ' ).'s_clients.parentid '.( isset( $_POST['notcl'] ) ? 'NOT ' : '' ).'IN ('.substr( get_parents( $clientid, 's_clients' ), 0, -1 ).'))';
                    
                    $employeeid = isset( $_POST['employeeid'] ) ? intval( $_POST['employeeid'] ) : 0;
                    if ( $employeeid > 0 ) $where[] = 'd_order.employeeid'.( isset( $_POST['notemp'] ) ? '!' : '' ).'='.$employeeid;
                    
                    $paymentid = isset( $_POST['paymentid'] ) ? intval( $_POST['paymentid'] ) : 0;
                    if ( $paymentid > 0 ) $where[] = 'd_order.paymentid'.( isset( $_POST['notpay'] ) ? '!' : '' ).'='.$paymentid;

                    $costfields1 = "";
                    $costfields2 = "";
                    $joinRemains = "";
                    
                    $colspan = 0;
                    if ( $dateInRow ) $colspan++;
                    if ( $apInRow ) $colspan++;
                    if ( $showIdLink ) $colspan++;
                    if ( $showCostPrice ){                        
                        $costfields1 = ", IFNULL(ROUND(t.costprice), 0) AS costprice, IFNULL(ROUND(SUM(t.quantity * t.costprice)), 0) AS costsumitem ";
                        $costfields2 = ", r.costprice, SUM(t_order.quantity) * r.costprice AS costsum ";
                        $joinRemains = " LEFT JOIN (
SELECT documentid, itemid, SUM(costsum) / SUM(quantity) AS costprice
FROM r_remainder
WHERE documenttype IN (1,3,7)
GROUP BY itemid
) AS r ON r.itemid=t_order.itemid ";//WHERE documenttype=0 AND costsum < 0 ////salesum=0 //////ON r.documentid=t_order.orderid AND 
                    }
                    
                    $dateHeader = "Дата:";
                    $dateFooter = "дату";
                    if ( $groupByChange ){
                        $dateHeader = "Смена:";
                        $dateFooter = "смену";
                        $dtfield = 'd_changes.name';
                        if ( $_POST['idautomated_point'] > 0 )
                            $joinChanges = " (SELECT c.id, c.dtopen, c.dtclosed, CONCAT(IFNULL(e.name, ''), '_', c.dtopen, '_', IFNULL(c.dtclosed, '')) AS name
FROM d_changes AS c
LEFT JOIN s_employee AS e ON e.id=c.employeeid) AS d_changes ";
                        else
                            $joinChanges = " (SELECT c.id, c.dtopen, c.dtclosed, CONCAT(IFNULL(p.name, ''), '_', IFNULL(e.name, ''), '_', c.dtopen, '_', IFNULL(c.dtclosed, '')) AS name
FROM d_changes AS c
LEFT JOIN s_employee AS e ON e.id=c.employeeid
LEFT JOIN s_automated_point AS p ON p.id=c.idautomated_point) AS d_changes ";
                    }
                     
                    if ( $showComplex ){
                        $notComplex = ' AND t_order.parentid=0 AND children.cnumber IS NULL';
                        
                        $query = mysql_query( "SELECT tt.apname, tt.dt, tt.idlink, tt.itemid, tt.parentid, tt.isComplex, tt.name, SUM(tt.quantity) AS quantity, 
ROUND(SUM(tt.price)/SUM(tt.quantity)) AS price, ROUND(SUM(tt.price)) AS sumitem".$costfields1
.( $groupByClient ? ", tt.clientname" : "" )
." FROM (SELECT ".$dtfield." AS dt, t.id, t.itemid, IF(t.parentid = 0, 0, parents.itemid) AS parentid, i.name, SUM(t.quantity) AS quantity,
 SUM(t.quantity * t.price) * (1 + (d_order.servicepercent * i.service - d_order.discountpercent * i.discount) / 100) AS price,
 IF(ISNULL(children.cnumber), IF(t.parentid = 0, 0, parents.itemid), t.itemid) AS isComplex, s_automated_point.name AS apname, i.idlink".$costfields2
.( $groupByClient ? ", s_clients.name AS clientname" : "" )
." FROM t_order AS t
LEFT JOIN d_order ON d_order.id=t.orderid
LEFT JOIN s_items AS i ON i.id=t.itemid
LEFT JOIN s_automated_point ON d_order.idautomated_point = s_automated_point.id
LEFT JOIN s_clients ON s_clients.id=d_order.clientid 
LEFT JOIN ".$joinChanges." ON d_order.changeid = d_changes.id
LEFT JOIN ( SELECT parentid, COUNT(*) AS cnumber FROM t_order WHERE parentid>0 GROUP BY parentid ) AS children ON children.parentid=t.id
LEFT JOIN ( SELECT id, itemid FROM t_order WHERE parentid=0 ) AS parents ON parents.id=t.parentid"
.$joinRemains
.( !empty( $where ) ? ' WHERE '.join( ' AND ', $where ) : '' )
." GROUP BY t.orderid, t.itemid
ORDER BY isComplex, t.parentid, i.name) AS tt
GROUP BY ".$groupBy."tt.isComplex, tt.parentid, tt.itemid
ORDER BY ".$groupBy."tt.isComplex, tt.parentid, tt.name" );
                        
                        $i = 0;
                        $sum['quantity'] = 0;
                        $sum['costsumitem'] = 0;
                        $sum['sumitem'] = 0;
                        $apquantity = 0;
                        $apcostsum = 0;
                        $apsum = 0;
                        $curAP = '';
                        $dtquantity = 0;
                        $dtcostsum = 0;
                        $dtsum = 0;
                        $curDt = '';
                        $clquantity = 0;
                        $clcostsum = 0;
                        $clsum = 0;
                        $curCl = '';
                        $dtstyle = ' style="background-color: #eeeeee; border: 1px solid #888888"';
                        $curParent = -1;
                        $j = 0;
                        while( $row = mysql_fetch_assoc( $query ) ){
                            if ( $groupByClient && $row['clientname'] != $curCl ){
//футер для клиентов
                                if ( $groupBy != '' && $i > 1 ){
                                    $earningsPercent = 0;
                                    if ( $apsum > 0 ){
                                        if ( $clcostsum > 0 )
                                            $earningsPercent = $clsum / $clcostsum * 100 - 100;
                                        else
                                            $earningsPercent = 100;
                                    } else if ( $clcostsum > 0 )
                                        $earningsPercent = -100;

                                    $output[] = '<tr'.$dtstyle.'>
<td></td>
<td colspan="'.( 1 + $colspan ).'"><b>Итого по клиенту '.$curCl.'</b></td>
<td style="text-align: right;"><b>'.round( $clquantity, 3 ).'</b></td>
'.( $showCostPrice ? '<td></td><td style="text-align: right;"><b>'.round( $clcostsum, 2 ).'</b></td>' : '' ).'
<td></td>
<td style="text-align: right;"><b>'.round( $clsum, 2 ).'</b></td>
'.( $showEarnings ? '<td style="text-align: right;"><b>'.round( $clsum - $clcostsum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $clsum / ( $clcostsum == 0 ? $clsum : $clcostsum ) * 100 - 100, 2 ).'%</b></td>' : '' ).'
</tr>';
                                }
                             
                                if ( !$groupByAP && !$groupByDate && !$groupByChange ){
                                    $sum['quantity'] += $clquantity;
                                    $sum['costsumitem'] += $clcostsum;
                                    $sum['sumitem'] += $clsum;
                                }

                                $clquantity = 0;
                                $clcostsum = 0;
                                $clsum = 0;
                            }
//Футер по датам/сменам
                            if ( ( $groupByDate || $groupByChange ) && $row['dt'] != $curDt ){
                                if ( !$dateInRow && $groupBy != '' && $i > 1 ){
                                    $earningsPercent = 0;
                                    if ( $dtsum > 0 ){
                                        if ( $dtcosttsum > 0 )
                                            $earningsPercent = $dtsum / $dtcosttsum * 100 - 100;
                                        else
                                            $earningsPercent = 100;
                                    } else if ( $dtcosttsum > 0 )
                                        $earningsPercent = -100;

                                    $output[] = '<tr'.$dtstyle.'>
<td></td>
<td colspan="'.( 1 + $colspan ).'"><b>Итого за '.$dateFooter.' '.$curDt.'</b></td>
<td style="text-align: right;"><b>'.round( $dtquantity, 3 ).'</b></td>
'.( $showCostPrice ? '<td></td><td style="text-align: right;"><b>'.round( $dtcosttsum, 2 ).'</b></td>' : '' ).'
<td></td>
<td style="text-align: right;"><b>'.round( $dtsum, 2 ).'</b></td>
'.( $showEarnings ? '<td style="text-align: right;"><b>'.round( $dtsum - $dtcosttsum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $earningsPercent, 2 ).'%</b></td>' : '' ).'
</tr>';
                                }
                             
                                if ( !$groupByAP ){
                                    $sum['quantity'] += $dtquantity;
                                    $sum['costsumitem'] += $dtcostsum;
                                    $sum['sumitem'] += $dtsum;
                                }

                                $dtquantity = 0;
                                $dtcostsum = 0;
                                $dtsum = 0;
                            }
//Хидер и футер по торговым объектам
                            if ( $groupByAP && !$apInRow && $row['apname'] != $curAP ){
                                if ( !$apInRow && $groupBy != '' && $i > 1 ){
                                    $earningsPercent = 0;
                                    if ( $apsum > 0 ){
                                        if ( $apcostsum > 0 )
                                            $earningsPercent = $apsum / $apcostsum * 100 - 100;
                                        else
                                            $earningsPercent = 100;
                                    } else if ( $apcostsum > 0 )
                                        $earningsPercent = -100;

                                    $output[] = '<tr'.$dtstyle.'>
<td></td>
<td colspan="'.( 1 + $colspan ).'"><b>Итого по '.$curAP.'</b></td>
<t style="text-align: right;"d><b>'.round( $apquantity, 2 ).'</b></td>
'.( $showCostPrice ? '<td></td><td style="text-align: right;"><b>'.round( $apcostsum, 2 ).'</b></td>' : '' ).'
<td></td>
<td style="text-align: right;"><b>'.round( $apsum, 2 ).'</b></td>
'.( $showEarnings ? '<td style="text-align: right;"><b>'.round( $apsum - $apcostsum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $earningsPercent, 2 ).'%</b></td>' : '' ).'
</tr>';
                                }
                             
                                $sum['quantity'] += $apquantity;
                                $sum['costsumitem'] += $apcostsum;
                                $sum['sumitem'] += $apsum;

                                $apquantity = 0;
                                $apcostsum = 0;
                                $apsum = 0;

                                $curAP = $row['apname'];

                                if ( !$apInRow && $groupBy != '' ){
                                    $output[] = '<tr'.$dtstyle.'><td></td><td colspan="'.( 4 + $colspan + ( $showCostPrice ? 2 : 0 ) ).'"><b>Торговая точка '.$curAP.'</b></td></tr>';
                                }
                            }
//Хидер по датам/сменам
                            if ( ( $groupByDate || $groupByChange ) && $row['dt'] != $curDt ){
                                $curDt = $row['dt'];
                                if ( !$dateInRow && $groupBy != '' ){
                                    $output[] = '<tr'.$dtstyle.'><td></td><td colspan="'.( 4 + $colspan + ( $showCostPrice ? 2 : 0 ) ).'"><b>'.$dateHeader.' '.$curDt.'</b></td></tr>';
                                }
                            }
//Хидер для клиентов
                            if ( $groupByClient && $row['clientname'] != $curCl ){
                                $curCl = $row['clientname'];
                                if ( $groupBy != '' ){
                                    $output[] = '<tr'.$dtstyle.'><td></td><td colspan="'.( 4 + $colspan + ( $showCostPrice ? 2 : 0 ) ).'"><b>Клиент '.$curCl.'</b></td></tr>';
                                }
                            }
                            
                            if ( (int)$row['isComplex'] == 0 ){
                                $i++;
                                $dtquantity += $row['quantity'];
                                $dtcostsum += $row['costsumitem'];
                                $dtsum += $row['sumitem'];
                                $apquantity += $row['quantity'];
                                $apcostsum += $row['costsumitem'];
                                $apsum += $row['sumitem'];
                                $clquantity += $row['quantity'];
                                $clcostsum += $row['costsumitem'];
                                $clsum += $row['sumitem'];
                                $j = 0;
                                $color = '';
                            } else if ( (int)$row['parentid'] != $curParent ){
                                $i++;
                                $dtquantity += $row['quantity'];
                                $dtcostsum += $row['costsumitem'];
                                $dtsum += $row['sumitem'];
                                $apquantity += $row['quantity'];
                                $apcostsum += $row['costsumitem'];
                                $apsum += $row['sumitem'];
                                $clquantity += $row['quantity'];
                                $clcostsum += $row['costsumitem'];
                                $clsum += $row['sumitem'];
                                $j = 0;
                                $color = 'style="background-color: #eeeeee"';
                                
                                $curParent = $row['isComplex'];
                            } else $color = 'style="background-color: #fafafa"';
                            
                            $earningsPercent = 0;
                            if ( $row['sumitem'] > 0 ){
                                if ( $row['costsumitem'] > 0 )
                                    $earningsPercent = $row['sumitem'] / $row['costsumitem'] * 100 - 100;
                                else
                                    $earningsPercent = 100;
                            } else if ( $row['costsumitem'] > 0 )
                                $earningsPercent = -100;

                            $output[] = '<tr '.$color.'>
<td>'.$i.( $j > 0 ? '.'.$j : '').'</td>
'.( $apInRow ? '<td>'.$curAP.'</td>' : '' ).'
'.( $dateInRow ? '<td>'.$curDt.'</td>' : '' ).'
'.( $showIdLink ? '<td>'.$row['idlink'].'</td>' : '' ).'
<td>'.$row['name'].'</td>
<td style="text-align: right;">'.round( $row['quantity'], 3 ).'</td>
'.( $showCostPrice ? '<td style="text-align: right;">'.round( $row['costprice'], 2 ).'</td><td style="text-align: right;">'.round( $row['costsumitem'], 2 ).'</td>' : '' ).'
<td style="text-align: right;">'.round( $row['price'], 2 ).'</td>
<td style="text-align: right;">'.round( $row['sumitem'], 2 ).'</td>
'.( $showEarnings ? '<td style="text-align: right;">'.round( $row['sumitem'] - $row['costsumitem'], 2 ).'</td>
<td style="text-align: right;">'.round( $earningsPercent, 2 ).'%</td>' : '' ).'
</tr>';
                            $j++;
                        }
                        
                        if ( $groupByAP ){
                            $sum['quantity'] += $apquantity;
                            $sum['costsumitem'] += $apcostsum;
                            $sum['sumitem'] += $apsum;
                        } else if ( $groupByDate || $groupByChange ){
                            $sum['quantity'] += $dtquantity;
                            $sum['costsumitem'] += $dtcostsum;
                            $sum['sumitem'] += $dtsum;
                        } else {
                            $sum['quantity'] += $clquantity;
                            $sum['costsumitem'] += $clcostsum;
                            $sum['sumitem'] += $clsum;
                        }
                        
                        if ( $groupByClient && $groupBy != '' ){
                            $earningsPercent = 0;
                            if ( $clsum > 0 ){
                                if ( $clcostsum > 0 )
                                    $earningsPercent = $clsum / $clcostsum * 100 - 100;
                                else
                                    $earningsPercent = 100;
                            } else if ( $lcostsum > 0 )
                                $earningsPercent = -100;

                            $output[] = '<tr'.$dtstyle.'>
<td></td>
<td colspan="'.( 1 + $colspan ).'"><b>Итого по клиенту '.$groupFooter.' '.$curCl.'</b></td>
<td style="text-align: right;"><b>'.round( $clquantity, 3 ).'</b></td>
'.( $showCostPrice ? '<td></td><td style="text-align: right;"><b>'.round( $clcostsum, 2 ).'</b></td>' : '' ).'
<td></td>
<td style="text-align: right;"><b>'.round( $clsum, 2 ).'</b></td>
'.( $showEarnings ? '<td style="text-align: right;"><b>'.round( $clsum - $clcostsum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $earningsPercent, 2 ).'%</b></td>' : '' ).'
</tr>';
                        }
                        
                        if ( ( $groupByDate || $groupByChange ) && !$dateInRow && $groupBy != '' ){
                            $earningsPercent = 0;
                            if ( $dtsum > 0 ){
                                if ( $dtcostsum > 0 )
                                    $earningsPercent = $dtsum / $dtcostsum * 100 - 100;
                                else
                                    $earningsPercent = 100;
                            } else if ( $dtcostsum > 0 )
                                $earningsPercent = -100;

                            $output[] = '<tr'.$dtstyle.'>
<td></td>
<td colspan="'.( 1 + $colspan ).'"><b>Итого за '.$dateFooter.' '.$curDt.'</b></td>
<td style="text-align: right;"><b>'.round( $dtquantity, 3 ).'</b></td>
'.( $showCostPrice ? '<td></td><td style="text-align: right;"><b>'.round( $dtcostsum, 2 ).'</b></td>' : '' ).'
<td></td>
<td style="text-align: right;"><b>'.round( $dtsum, 2 ).'</b></td>
'.( $showEarnings ? '<td style="text-align: right;"><b>'.round( $dtsum - $dtcostsum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $earningsPercent, 2 ).'%</b></td>' : '' ).'
</tr>';
                        }
                        
                        if ( $groupByAP && !$apInRow && $groupBy != '' ){
                            $earningsPercent = 0;
                            if ( $apsum > 0 ){
                                if ( $apcostsum > 0 )
                                    $earningsPercent = $apsum / $apcostsum * 100 - 100;
                                else
                                    $earningsPercent = 100;
                            } else if ( $apcostsum > 0 )
                                $earningsPercent = -100;

                            $output[] = '<tr'.$dtstyle.'>
<td></td>
<td colspan="'.( 1 + $colspan ).'"><b>Итого по '.$curAP.'</b></td>
<td style="text-align: right;"><b>'.round( $apquantity, 3 ).'</b></td>
'.( $showCostPrice ? '<td></td><td style="text-align: right;"><b>'.round( $apcostsum, 2 ).'</b></td>' : '' ).'
<td></td>
<td style="text-align: right;"><b>'.round( $apsum, 2 ).'</b></td>
'.( $showEarnings ? '<td style="text-align: right;"><b>'.round( $apsum - $apcostsum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $earningsPercent, 2 ).'%</b></td>' : '' ).'
</tr>';
                        }
                        
                        $earningsPercent = 0;
                        if ( $sum['sumitem'] > 0 ){
                            if ( $sum['costsumitem'] > 0 )
                                $earningsPercent = $sum['sumitem'] / $sum['costsumitem'] * 100 - 100;
                            else
                                $earningsPercent = 100;
                        } else if ( $sum['costsumitem'] > 0 )
                            $earningsPercent = -100;

                        $output[] = '<tr>
<td colspan="'.(2 + $colspan).'"><b>Итого</b></td>
<td style="text-align: right;"><b>'.round( $sum['quantity'], 3 ).'</b></td>
'.( $showCostPrice ? '<td></td><td style="text-align: right;"><b>'.round( $sum['costsumitem'], 2 ).'</b></td>' : '' ).'
<td></td>
<td style="text-align: right;"><b>'.round( $sum['sumitem'], 2 ).'</b></td>
'.( $showEarnings ? '<td style="text-align: right;"><b>'.round( $sum['sumitem'] - $sum['costsumitem'], 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $earningsPercent, 2 ).'%</b></td>' : '' ).'
</tr>';

                    } else {                        
                        $query = mysql_query( "SELECT t.apname, t.dt, t.id, t.idlink, t.name, ROUND(SUM(t.quantity), 3) AS quantity, ROUND(SUM(t.possum)/SUM(t.quantity), 2) AS price, 
ROUND(SUM(t.possum), 2) AS sumitem".$costfields1
.( $groupByClient ? ", t.clientname" : "" )
." FROM (SELECT ".$dtfield." AS dt, t_order.id, t_order.orderid, s_items.id AS itemid, s_items.`name`, SUM(t_order.quantity) AS quantity,
SUM(t_order.quantity) * t_order.price * (1 + (d_order.servicepercent * s_items.service - d_order.discountpercent * s_items.discount) / 100) AS possum, s_automated_point.name AS apname, s_items.idlink".$costfields2
.( $groupByClient ? ", s_clients.name AS clientname" : "" )
." FROM t_order
LEFT JOIN d_order ON d_order.id = t_order.orderid
LEFT JOIN s_items ON t_order.itemid = s_items.id
LEFT JOIN s_automated_point ON d_order.idautomated_point = s_automated_point.id
LEFT JOIN s_clients ON s_clients.id=d_order.clientid 
LEFT JOIN ".$joinChanges." ON d_order.changeid = d_changes.id"
.$joinRemains
.( !empty( $where ) ? ' WHERE t_order.parentid=0 AND '.join( ' AND ', $where ) : '' )
." GROUP BY t_order.orderid, s_items.id,t_order.price
ORDER BY name) AS t 
GROUP BY ".$groupBy." t.itemid 
HAVING SUM(t.quantity) > 0 
ORDER BY ".$groupBy." t.name" );

                        $i = 1;
                        
                        $sum['quantity'] = 0;
                        $sum['costsumitem'] = 0;
                        $sum['sumitem'] = 0;
                        $dtquantity = 0;
                        $dtcostsum = 0;
                        $dtsum = 0;
                        $curDt = '';
                        $apquantity = 0;
                        $apcostsum = 0;
                        $apsum = 0;
                        $curAP = '';
                        $clquantity = 0;
                        $clcostsum = 0;
                        $clsum = 0;
                        $curCl = '';
                        $dtstyle = ' style="background-color: #eeeeee; border: 1px solid #888888"';
                        while( $row = mysql_fetch_assoc( $query ) ){
                            if ( $groupByClient && $row['clientname'] != $curCl ){
//футер для клиентов
                                if ( $groupBy != '' && $i > 1 ){
                                    $earningsPercent = 0;
                                    if ( $apsum > 0 ){
                                        if ( $clcostsum > 0 )
                                            $earningsPercent = $clsum / $clcostsum * 100 - 100;
                                        else
                                            $earningsPercent = 100;
                                    } else if ( $clcostsum > 0 )
                                        $earningsPercent = -100;

                                    $output[] = '<tr'.$dtstyle.'>
<td></td>
<td colspan="'.( 1 + $colspan ).'"><b>Итого по клиенту '.$curCl.'</b></td>
<td style="text-align: right;"><b>'.round( $clquantity, 3 ).'</b></td>
'.( $showCostPrice ? '<td></td><td style="text-align: right;"><b>'.round( $clcostsum, 2 ).'</b></td>' : '' ).'
<td></td>
<td style="text-align: right;"><b>'.round( $clsum, 2 ).'</b></td>
'.( $showEarnings ? '<td style="text-align: right;"><b>'.round( $clsum - $clcostsum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $clsum / ( $clcostsum == 0 ? $clsum : $clcostsum ) * 100 - 100, 2 ).'%</b></td>' : '' ).'
</tr>';
                                }
                             
                                if ( !$groupByAP && !$groupByDate && !$groupByChange ){
                                    $sum['quantity'] += $clquantity;
                                    $sum['costsumitem'] += $clcostsum;
                                    $sum['sumitem'] += $clsum;
                                }

                                $clquantity = 0;
                                $clcostsum = 0;
                                $clsum = 0;
                            }
//футер для дат/смен
                            if ( ( $groupByDate || $groupByChange ) && $row['dt'] != $curDt ){
                                if ( !$dateInRow && $groupBy != '' && $i > 1 ){
                                    $earningsPercent = 0;
                                    if ( $dtsum > 0 ){
                                        if ( $dtcostsum > 0 )
                                            $earningsPercent = $dtsum / $dtcostsum * 100 - 100;
                                        else
                                            $earningsPercent = 100;
                                    } else if ( $dtcostsum > 0 )
                                        $earningsPercent = -100;
                                    
                                    $output[] = '<tr'.$dtstyle.'>
<td></td>
<td colspan="'.( 1 + $colspan ).'"><b>Итого за '.$dateFooter.' '.$curDt.'</b></td>
<td style="text-align: right;"><b>'.round( $dtquantity, 3 ).'</b></td>
'.( $showCostPrice ? '<td></td><td style="text-align: right;"><b>'.round( $dtcostsum, 2 ).'</b></td>' : '' ).'
<td></td>
<td style="text-align: right;"><b>'.round( $dtsum, 2 ).'</b></td>
'.( $showEarnings ? '<td style="text-align: right;"><b>'.round( $dtsum - $dtcostsum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $earningsPercent, 2 ).'%</b></td>' : '' ).'
</tr>';
                                }
                             
                                if ( !$groupByAP ){
                                    $sum['quantity'] += $dtquantity;
                                    $sum['costsumitem'] += $dtcostsum;
                                    $sum['sumitem'] += $dtsum;
                                }

                                $dtquantity = 0;
                                $dtcostsum = 0;
                                $dtsum = 0;
                            }
                            if ( $groupByAP && $row['apname'] != $curAP ){
//футер и хидер для торговых объектов
                                if ( !$apInRow && $groupBy != '' && $i > 1 ){
                                    $earningsPercent = 0;
                                    if ( $apsum > 0 ){
                                        if ( $apcostsum > 0 )
                                            $earningsPercent = $apsum / $apcostsum * 100 - 100;
                                        else
                                            $earningsPercent = 100;
                                    } else if ( $apcostsum > 0 )
                                        $earningsPercent = -100;

                                    $output[] = '<tr'.$dtstyle.'>
<td></td>
<td colspan="'.( 1 + $colspan ).'"><b>Итого по '.$curAP.'</b></td>
<td style="text-align: right;"><b>'.round( $apquantity, 3 ).'</b></td>
'.( $showCostPrice ? '<td></td><td style="text-align: right;"><b>'.round( $apcostsum, 2 ).'</b></td>' : '' ).'
<td></td>
<td style="text-align: right;"><b>'.round( $apsum, 2 ).'</b></td>
'.( $showEarnings ? '<td style="text-align: right;"><b>'.round( $apsum - $apcostsum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $apsum / ( $apcostsum == 0 ? $apsum : $apcostsum ) * 100 - 100, 2 ).'%</b></td>' : '' ).'
</tr>';
                                }
                             
                                $sum['quantity'] += $apquantity;
                                $sum['costsumitem'] += $apcostsum;
                                $sum['sumitem'] += $apsum;

                                $apquantity = 0;
                                $apcostsum = 0;
                                $apsum = 0;
                                
                                $curAP = $row['apname'];
                                if ( !$apInRow && $groupBy != '' ){
                                    $output[] = '<tr'.$dtstyle.'><td></td><td colspan="'.( 4 + $colspan + ( $showCostPrice ? 2 : 0 ) ).'"><b>Торговая точка '.$curAP.'</b></td></tr>';
                                }
                            }
//Хидер для дат/смен
                            if ( ( $groupByDate || $groupByChange ) && $row['dt'] != $curDt ){
                                $curDt = $row['dt'];
                                if ( !$dateInRow && $groupBy != '' ){
                                    $output[] = '<tr'.$dtstyle.'><td></td><td colspan="'.( 4 + $colspan + ( $showCostPrice ? 2 : 0 ) ).'"><b>'.$dateHeader.' '.$curDt.'</b></td></tr>';
                                }
                            }
//Хидер для клиентов
                            if ( $groupByClient && $row['clientname'] != $curCl ){
                                $curCl = $row['clientname'];
                                if ( $groupBy != '' ){
                                    $output[] = '<tr'.$dtstyle.'><td></td><td colspan="'.( 4 + $colspan + ( $showCostPrice ? 2 : 0 ) ).'"><b>Клиент '.$curCl.'</b></td></tr>';
                                }
                            }
                         
                            $dtquantity += (int)$row['quantity'];
                            $dtcostsum += (int)$row['costsumitem'];
                            $dtsum += (int)$row['sumitem'];
                            $apquantity += (int)$row['quantity'];
                            $apcostsum += (int)$row['costsumitem'];
                            $apsum += (int)$row['sumitem'];
                            $clquantity += (int)$row['quantity'];
                            $clcostsum += (int)$row['costsumitem'];
                            $clsum += (int)$row['sumitem'];
                            
                            $earningsPercent = 0;
                            if ( $row['sumitem'] > 0 ){
                                if ( $row['costsumitem'] > 0 )
                                    $earningsPercent = $row['sumitem'] / $row['costsumitem'] * 100 - 100;
                                else
                                    $earningsPercent = 100;
                            } else if ( $row['costsumitem'] > 0 )
                                $earningsPercent = -100;
                            
                            $output[] = '<tr>
<td>'.$i.'</td>
'.( $apInRow ? '<td>'.$curAP.'</td>' : '' ).'
'.( $dateInRow ? '<td>'.$curDt.'</td>' : '' ).'
'.( $showIdLink ? '<td>'.$row['idlink'].'</td>' : '' ).'
<td>'.$row['name'].'</td>
<td style="text-align: right;">'.round( $row['quantity'], 3 ).'</td>
'.( $showCostPrice ? '<td style="text-align: right;">'.round( $row['costprice'], 2 ).'</td><td style="text-align: right;">'.round( $row['costsumitem'], 2 ).'</td>' : '' ).'
<td style="text-align: right;">'.round( $row['price'], 2 ).'</td>
<td style="text-align: right;">'.round( $row['sumitem'], 2 ).'</td>
'.( $showEarnings ? '<td style="text-align: right;">'.round( $row['sumitem'] - $row['costsumitem'], 2 ).'</td>
<td style="text-align: right;">'.round( $earningsPercent, 2 ).'%</td>' : '' ).'
</tr>';
                            $i++;
                        }
                        
                        if ( $groupByAP ){
                            $sum['quantity'] += $apquantity;
                            $sum['costsumitem'] += $apcostsum;
                            $sum['sumitem'] += $apsum;
                        } else if ( $groupByDate || $groupByChange ){
                            $sum['quantity'] += $dtquantity;
                            $sum['costsumitem'] += $dtcostsum;
                            $sum['sumitem'] += $dtsum;
                        } else {
                            $sum['quantity'] += $clquantity;
                            $sum['costsumitem'] += $clcostsum;
                            $sum['sumitem'] += $clsum;
                        }
                        
                        if ( $groupByClient && $groupBy != '' ){
                            $earningsPercent = 0;
                            if ( $clsum > 0 ){
                                if ( $clcostsum > 0 )
                                    $earningsPercent = $clsum / $clcostsum * 100 - 100;
                                else
                                    $earningsPercent = 100;
                            } else if ( $lcostsum > 0 )
                                $earningsPercent = -100;

                            $output[] = '<tr'.$dtstyle.'>
<td></td>
<td colspan="'.( 1 + $colspan ).'"><b>Итого по клиенту '.$groupFooter.' '.$curCl.'</b></td>
<td style="text-align: right;"><b>'.round( $clquantity, 3 ).'</b></td>
'.( $showCostPrice ? '<td></td><td style="text-align: right;"><b>'.round( $clcostsum, 2 ).'</b></td>' : '' ).'
<td></td>
<td style="text-align: right;"><b>'.round( $clsum, 2 ).'</b></td>
'.( $showEarnings ? '<td style="text-align: right;"><b>'.round( $clsum - $clcostsum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $earningsPercent, 2 ).'%</b></td>' : '' ).'
</tr>';
                        }
                        
                        if ( ( $groupByDate || $groupByChange ) && !$dateInRow && $groupBy != '' ){
                            $earningsPercent = 0;
                            if ( $dtsum > 0 ){
                                if ( $dtcostsum > 0 )
                                    $earningsPercent = $dtsum / $dtcostsum * 100 - 100;
                                else
                                    $earningsPercent = 100;
                            } else if ( $dtcostsum > 0 )
                                $earningsPercent = -100;

                            $output[] = '<tr'.$dtstyle.'>
<td></td>
<td colspan="'.( 1 + $colspan ).'"><b>Итого за '.$groupFooter.' '.$curDt.'</b></td>
<td style="text-align: right;"><b>'.round( $dtquantity, 3 ).'</b></td>
'.( $showCostPrice ? '<td></td><td style="text-align: right;"><b>'.round( $dtcostsum, 2 ).'</b></td>' : '' ).'
<td></td>
<td style="text-align: right;"><b>'.round( $dtsum, 2 ).'</b></td>
'.( $showEarnings ? '<td style="text-align: right;"><b>'.round( $dtsum - $dtcostsum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $earningsPercent, 2 ).'%</b></td>' : '' ).'
</tr>';
                        }  
                        
                        if ( $groupByAP && !$apInRow && $groupBy != '' ){
                            $earningsPercent = 0;
                            if ( $apsum > 0 ){
                                if ( $apcostsum > 0 )
                                    $earningsPercent = $apsum / $apcostsum * 100 - 100;
                                else
                                    $earningsPercent = 100;
                            } else if ( $apcostsum > 0 )
                                $earningsPercent = -100;

                            $output[] = '<tr'.$dtstyle.'>
<td></td>
<td colspan="'.( 1 + $colspan ).'"><b>Итого по '.$curAP.'</b></td>
<td style="text-align: right;"><b>'.round( $apquantity, 3 ).'</b></td>
'.( $showCostPrice ? '<td></td><td style="text-align: right;"><b>'.round( $apcostsum, 2 ).'</b></td>' : '' ).'
<td></td>
<td style="text-align: right;"><b>'.round( $apsum, 2 ).'</b></td>
'.( $showEarnings ? '<td style="text-align: right;"><b>'.round( $apsum - $apcostsum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $earningsPercent, 2 ).'%</b></td>' : '' ).'
</tr>';
                        }                      
                        
                        $earningsPercent = 0;
                        if ( $sum['sumitem'] > 0 ){
                            if ( $sum['costsumitem'] > 0 )
                                $earningsPercent = $sum['sumitem'] / $sum['costsumitem'] * 100 - 100;
                            else
                                $earningsPercent = 100;
                        } else if ( $sum['costsumitem'] > 0 )
                            $earningsPercent = -100;

                        $output[] = '<tr>
<td colspan="'.(2 + $colspan).'"><b>Итого</b></td>
<td style="text-align: right;"><b>'.round( $sum['quantity'], 3 ).'</b></td>
'.( $showCostPrice ? '<td></td><td style="text-align: right;"><b>'.round( $sum['costsumitem'], 2 ).'</b></td>' : '' ).'
<td></td>
<td style="text-align: right;"><b>'.round( $sum['sumitem'], 2 ).'</b></td>
'.( $showEarnings ? '<td style="text-align: right;"><b>'.round( $sum['sumitem'] - $sum['costsumitem'], 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $earningsPercent, 2 ).'%</b></td>' : '' ).'
</tr>';
                    }
                    
                break;
                case 'poschetam':
                    $groupByDate = isset( $_POST["groupByDate"] );
                    $groupByChange = !$groupByDate && isset( $_POST["groupByChange"] );
                    $groupBy = $groupByDate || $groupByChange ? ' dt,' : '';
                    $groupByClient = isset( $_POST["groupByClient"] );
                    $groupBy .= $groupByClient ? ' clientname,' : '';
                    $dtfield = "DATE(d_order.creationdt)";
                    $joinChanges = " d_changes ";
                    
                    $employeeid = isset( $_POST['employeeid'] ) ? intval( $_POST['employeeid'] ) : 0;
                    if ( $employeeid > 0 ) $where[] = 'd_order.employeeid'.( isset( $_POST['notemp'] ) ? '!' : '' ).'='.$employeeid;
                    
                    //$clientid = isset( $_POST['clientid'] ) ? intval( $_POST['clientid'] ) : 0;
                    if ( !empty( $_POST['clientid'] ) ) 
                        $where[]='d_order.clientid '.(!empty($_POST['notcl'])?'NOT':'').' IN ('.getAllIdByParentid($_POST['clientid']).addslashes($_POST['clientid']).')';
                    

                    
                    $paymentid = isset( $_POST['paymentid'] ) ? intval( $_POST['paymentid'] ) : 0;
                    if ( $paymentid > 0 ) $where[] = 'd_order.paymentid'.( isset( $_POST['notpay'] ) ? '!' : '' ).'='.$paymentid;
                    
                     $join_t_order='';
                    if ( !empty( $_POST['itemid'] ) ){
                        //$where[] = 't_order.itemid'.( isset( $_POST['notitemid'] ) ? '!' : '' ).'='.$itemid;
                        $where[]='t_order.itemid '.(!empty($_POST['notitem'])?'NOT':'').' IN ('.getAllIdByParentid($_POST['itemid']).addslashes($_POST['itemid']).')';
                        $join_t_order=' LEFT JOIN t_order ON t_order.orderid=d_order.id ';
                    }
                    
                    $groupHeader = "Дата:";
                    $groupFooter = "дату";
                    if ( $groupByChange ){
                        $groupHeader = "Смена:";
                        $groupFooter = "смену";
                        $dtfield = 'd_changes.name';
                        if ( $_POST['idautomated_point'] > 0 )
                            $joinChanges = " (SELECT c.id, c.dtopen, c.dtclosed, CONCAT(IFNULL(e.name, ''), '_', c.dtopen, '_', IFNULL(c.dtclosed, '')) AS name
FROM d_changes AS c
LEFT JOIN s_employee AS e ON e.id=c.employeeid) AS d_changes ";
                        else
                            $joinChanges = " (SELECT c.id, c.dtopen, c.dtclosed, CONCAT(IFNULL(p.name, ''), '_', IFNULL(e.name, ''), '_', c.dtopen, '_', IFNULL(c.dtclosed, '')) AS name
FROM d_changes AS c
LEFT JOIN s_employee AS e ON e.id=c.employeeid
LEFT JOIN s_automated_point AS p ON p.id=c.idautomated_point) AS d_changes ";
                    }
                     
                    $query=mysql_query("SELECT 
                       cclients.name,
                       d_order.id,
                       cclients.name AS clientgroup,
                       d_order.idout AS numschet,
                       d_order.guestcount,
                       s_employee.name AS waitername,
                       s_clients.name AS clientname,
                       s_types_of_payment.name AS typepayname,
                       d_order.creationdt,
                       ".$dtfield." AS dt,
                       ROUND(d_order.totalsum - d_order.servicesum + d_order.discountsum) AS summa,
                       d_order.servicesum,
                       d_order.discountsum,
                       ROUND(d_order.totalsum) as totalsum,
                       d_order.changeid,
                       s_automated_point.name as apname,
                       d_changes.name AS changename
                FROM d_order
                     LEFT JOIN s_employee ON d_order.employeeid = s_employee.id
                     LEFT JOIN s_clients AS s_clients ON d_order.clientid = s_clients.id
                     LEFT JOIN s_clients AS cclients ON s_clients.parentid = cclients.id
                     LEFT JOIN s_types_of_payment ON d_order.paymentid = s_types_of_payment.id
                     LEFT JOIN s_automated_point ON d_order.idautomated_point = s_automated_point.id
                     LEFT JOIN ".$joinChanges." ON d_changes.id = d_order.changeid
                     ".$join_t_order."
                ".(!empty($where)?'WHERE '.join(' AND ',$where):'')."
                    ORDER BY ".$groupBy."creationdt");
                    
                  /*  echo "SELECT 
                       cclients.name,
                       d_order.id,
                       cclients.name AS clientgroup,
                       d_order.idout AS numschet,
                       d_order.guestcount,
                       s_employee.name AS waitername,
                       clients.name AS clientname,
                       s_types_of_payment.name AS typepayname,
                       d_order.creationdt,
                       ".$dtfield." AS dt,
                       ROUND(d_order.totalsum - d_order.servicesum + d_order.discountsum) AS summa,
                       d_order.servicesum,
                       d_order.discountsum,
                       ROUND(d_order.totalsum) as totalsum,
                       d_order.changeid,
                       s_automated_point.name as apname,
                       d_changes.name AS changename
                FROM d_order
                     LEFT JOIN s_employee ON d_order.employeeid = s_employee.id
                     LEFT JOIN s_clients AS clients ON d_order.clientid = clients.id
                     LEFT JOIN s_clients AS cclients ON clients.parentid = cclients.id
                     LEFT JOIN s_types_of_payment ON d_order.paymentid = s_types_of_payment.id
                     LEFT JOIN s_automated_point ON d_order.idautomated_point = s_automated_point.id
                     LEFT JOIN ".$joinChanges." ON d_changes.id = d_order.changeid
                ".(!empty($where)?'WHERE '.join(' AND ',$where):'')."
                    ORDER BY ".$groupBy."creationdt";*/
                    
                    if(isset($_POST['datailed'])){
                        $where[]='t_order.parentid=0';
                        $query2=mysql_query("SELECT 
                                t_order.orderid, 
                                s_items.name, 
                                t_order.itemid, 
                                SUM(t_order.quantity) AS quantity, 
                                ROUND(t_order.price * (1 + (d_order.servicepercent * s_items.service - d_order.discountpercent * s_items.discount) / 100)) AS price,
                                ROUND(SUM(t_order.quantity) * t_order.price * (1 + (d_order.servicepercent * s_items.service - d_order.discountpercent * s_items.discount) / 100)) AS possum
                            FROM d_order
                                LEFT JOIN t_order ON d_order.id=t_order.orderid
                                LEFT JOIN s_items ON s_items.id=t_order.itemid
                            ".(!empty($where)?'WHERE '.join(' AND ',$where):'')."
                            GROUP BY t_order.orderid, t_order.itemid, t_order.price"); 
                            
                           
                            $det=array();
                            while($row2=mysql_fetch_assoc($query2)){
                                $det[$row2['orderid']][$row2['itemid']]=$row2;
                            }
                    }
                   
                    $i=1;
                    
                     $sum['guestcount'] = 0;
                     $sum['summa'] = 0;
                     $sum['servicesum'] = 0;
                     $sum['discountsum'] = 0;
                     $sum['totalsum'] = 0;

                     $dtguestcount = 0;
                     $dtsumma = 0;
                     $dtservicesum = 0;
                     $dtdiscountsum = 0;
                     $dttotalsum = 0;
                     $curDt = '';
                     
                     $clguestcount = 0;
                     $clsumma = 0;
                     $clservicesum = 0;
                     $cldiscountsum = 0;
                     $cltotalsum = 0;
                     $curCl = '';
                     
                     $dtstyle = ' style="background-color: #eeeeee; border: 1px solid #888888"';
                     while($row=mysql_fetch_assoc($query)){
                         if ( $groupByClient && $row['clientname'] != $curCl ){
//футер для клиентов
                            if ( $groupBy != '' && $i > 1 ){
                                $output[] = '<tr'.$dtstyle.'>
<td colspan="2"><b>Итого по</b></td>
<td colspan="3"><b>'.$curCl.'</b></td>
<td style="text-align: right;"><b>'.round( $clguestcount ).'</b></td>
<td colspan="2"></td>
<td style="text-align: right;"><b>'.round( $clsumma, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $clservicesum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $cldiscountsum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $cltotalsum, 2 ).'</b></td>
</tr>';
                            }

                            $clguestcount = 0;
                            $clsumma = 0;
                            $clservicesum = 0;
                            $cldiscountsum = 0;
                            $cltotalsum = 0;
                        }
// футер и хидер по датам/сменам
                        if ( $groupByDate || $groupByChange ){
                            if ( $row['dt'] != $curDt ){
                                if ( $groupBy != '' && $i > 1 ){
                                   $output[] = '<tr'.$dtstyle.'>
<td colspan="2"><b>Итого за</b></td>
<td colspan="3"><b>'.$curDt.'</b></td>
<td style="text-align: right;"><b>'.round( $dtguestcount ).'</b></td>
<td colspan="2"></td>
<td style="text-align: right;"><b>'.round( $dtsumma, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $dtservicesum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $dtdiscountsum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $dttotalsum, 2 ).'</b></td>
</tr>';
                                }

                                $dtguestcount = 0;
                                $dtsumma = 0;
                                $dtservicesum = 0;
                                $dtdiscountsum = 0;
                                $dttotalsum = 0;

                                $curDt = $row['dt'];

                                if ( $groupBy != '' ){
                                    $output[] = '<tr'.$dtstyle.'><td colspan="2"><b>'.$groupHeader.'</b></td><td colspan="10"><b>'.$curDt.'</b></td></tr>';
                                }
                            }
                        }
                         
                        if ( $groupByClient && $row['clientname'] != $curCl ){
                            $curCl = $row['clientname'];
                            if ( $groupBy != '' ){
                                $output[] = '<tr'.$dtstyle.'><td colspan="2"><b>Клиент </b></td><td colspan="10"><b>'.$curCl.'</b></td></tr>';
                            }
                        }
                         
                        $sum['guestcount'] += $row['guestcount'];
                        $sum['summa'] += $row['summa'];
                        $sum['servicesum'] += $row['servicesum'];
                        $sum['discountsum'] += $row['discountsum'];
                        $sum['totalsum'] += $row['totalsum'];
                        
                        if ( $groupByDate || $groupByChange ){
                            $dtguestcount += $row['guestcount'];
                            $dtsumma += $row['summa'];
                            $dtservicesum += $row['servicesum'];
                            $dtdiscountsum += $row['discountsum'];
                            $dttotalsum += $row['totalsum'];
                        }
                        
                        if ( $groupByClient ){
                            $clguestcount += $row['guestcount'];
                            $clsumma += $row['summa'];
                            $clservicesum += $row['servicesum'];
                            $cldiscountsum += $row['discountsum'];
                            $cltotalsum += $row['totalsum'];
                        }
                            
                         $output[]='<tr><td style="text-align: right;">'.$i.'</td>
<td style="text-align: right;">'.$row['numschet'].'</td>
<td>'.$row['waitername'].'</td>
<td>'.$row['apname'].'</td>
<td>'.$row['clientname'].'</td>
<td style="text-align: right;">'.round( $row['guestcount'] ).'</td>
<td>'.$row['typepayname'].'</td>
<td>'.$row['creationdt'].'</td>
<td style="text-align: right;">'.round( $row['summa'], 2 ).'</td>
<td style="text-align: right;">'.round( $row['servicesum'], 2 ).'</td>
<td style="text-align: right;">'.round( $row['discountsum'], 2 ).'</td>
<td style="text-align: right;">'.round( $row['totalsum'], 2 ).'</td>
</tr>';
                            if(isset($_POST['datailed'])){
                                if (isset($det[$row['id']])){
                                    $mmm=1;
                                  $output[]='<tr><td colspan=12 style="padding:0 0 0 100px;">
<table width="100%"><tr class="tableheader">
<td width="20">#</td>
<td>Товар</td>
<td width="84">Количество</td>
<td width="84">Цена</td>
<td width="84">Сумма</td>
</tr>';
                                  foreach($det[$row['id']] as $k=>$v){
                                     $output[]='<tr>
<td>'.$mmm.'</td>
<td>'.$v['name'].'</td>
<td style="text-align: right;">'.round( $v['quantity'], 3 ).'</td>
<td style="text-align: right;">'.round( $v['price'], 2 ).'</td>
<td style="text-align: right;">'.round( $v['possum'], 2 ).'</td>
</tr>'; 
                                     $mmm++;
                                  }
                                  $output[]='</table></td></tr>';
                                }
                            }
                            
                            
                         $i++;
                     }
                     
                    if ( $groupByClient && $i > 1 ){
//футер для клиентов
                        $output[] = '<tr'.$dtstyle.'>
<td colspan="2"><b>Итого по</b></td>
<td colspan="3"><b>'.$curCl.'</b></td>
<td style="text-align: right;"><b>'.round( $clguestcount ).'</b></td>
<td colspan="2"></td>
<td style="text-align: right;"><b>'.round( $clsumma, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $clservicesum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $cldiscountsum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $cltotalsum, 2 ).'</b></td>
</tr>';
                    }
                    
                    if ( ( $groupByDate || $groupByChange ) && $i > 1 ){
                       $output[] = '<tr'.$dtstyle.'>
<td colspan="2"><b>Итого за</b></td>
<td colspan="3"><b>'.$curDt.'</b></td>
<td style="text-align: right;"><b>'.round( $dtguestcount ).'</b></td>
<td colspan="2"></td>
<td style="text-align: right;"><b>'.round( $dtsumma, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $dtservicesum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $dtdiscountsum, 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $dttotalsum, 2 ).'</b></td>
</tr>';
                    }
                    
                     $output[]='<tr>
<td colspan="5"><b>Итого</b></td>
<td style="text-align: right;"><b>'.round( $sum['guestcount'] ).'</b></td>
<td colspan="2"></td>
<td style="text-align: right;"><b>'.round( $sum['summa'], 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $sum['servicesum'], 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $sum['discountsum'], 2 ).'</b></td>
<td style="text-align: right;"><b>'.round( $sum['totalsum'], 2 ).'</b></td>
</tr>';
                         
                 break;
                 case 'refuse':
                 $where[]='t_order.parentid=0';
                    $query=mysql_query("SELECT d_order.id AS id,
                                               s_employee.name AS waitername,
                                               t_order.dt,
                                               t_order.note,
                                               s_items.name AS itemname,
                                               t_order.quantity,
                                               round(t_order.quantity * t_order.price,0) as summa,
                                               s_units_of_measurement.name AS measurename
                                        FROM t_order
                                             LEFT JOIN d_order ON d_order.id = t_order.orderid
                                             LEFT JOIN s_employee ON d_order.employeeid = s_employee.id
                                             LEFT JOIN d_changes ON d_order.changeid = d_changes.id
                                             LEFT JOIN s_items ON s_items.id = t_order.itemid
                                             LEFT JOIN s_units_of_measurement ON s_units_of_measurement.id = s_items.measurement
                                        WHERE t_order.quantity <= 0 ".(!empty($where)?' AND '.join(' AND ',$where):''));
                                        
                  
                                        echo mysql_error();

                   
                    $i=1;
                     $sum['quantity']=0;
                     $sum['summa']=0;
                     while($row=mysql_fetch_assoc($query)){
                         $sum['summa']+=$row['summa'];
                         $sum['quantity']+=$row['quantity'];
                         $output[]='<tr><td>'.$i.'</td>
                            <td>'.$row['id'].'</td>
                            <td>'.$row['waitername'].'</td>
                            <td>'.$row['dt'].'</td>
                            
                            <td>'.$row['itemname'].'</td>
                            <td>'.$row['quantity'].'</td>
                            <td>'.$row['summa'].'</td>
                            <td>'.$row['measurename'].'</td>     
                            <td>'.$row['note'].'</td>                       
                            </tr>';
                         $i++;
                     }
                     $output[]='<tr>
                            <td colspan="2"><b>Итого</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>'.$sum['quantity'].'</b></td>
                            <td><b>'.$sum['summa'].'</b></td>
                            <td></td>
                            <td></td>
                         </tr>';
                         
                 break;
                 case 'posotrudnikam':
                 $where[]='t_order.parentid=0';
                        $query=mysql_query("SELECT t.id, DATE_FORMAT(t.dtclose, '%d.%m.%y') as dtclose, t.employee, t.name, ROUND(t.quantity) as quantity, ROUND(t.price) AS price, ROUND(t.quantity * t.price) AS sumitem FROM
                            (SELECT
                            t_order.id,
                            t_order.orderid,
                            d_order.dtclose,
                            s_employee.name AS employee,
                            s_items.id AS itemid,
                            s_items.`name`,
                            SUM(t_order.quantity) AS quantity,
                            t_order.price * (1 + (d_order.servicepercent * s_items.service - d_order.discountpercent * s_items.discount) / 100) AS price
                            FROM t_order
                            LEFT JOIN d_order ON d_order.id = t_order.orderid
                            LEFT JOIN s_items ON t_order.itemid = s_items.id
                            LEFT JOIN s_automated_point ON d_order.idautomated_point = s_automated_point.id
                            LEFT JOIN d_changes ON d_order.changeid = d_changes.id
                            LEFT JOIN s_employee ON s_employee.id = d_order.employeeid
                            ".(!empty($where)?'WHERE '.join(' AND ',$where):'')."
                            GROUP BY t_order.orderid, s_items.id, t_order.price
                            ORDER BY employee, dtclose, name) AS t
                            WHERE t.quantity > 0");

                    
                    $dataz=array();
                    while($row=mysql_fetch_assoc($query)){      
                        $arr[$row['employee']][$row['dtclose']][]=$row;
                        $dataz[$row['dtclose']]=1;
                    }
                    
                    $output[]='<tr class="tableheader">
                            <td>Сотрудник</td>';
                    foreach($dataz as $k=>$v)
                        $output[]='<td>'.$k.'</td>';    
                    $output[]='<td>Итого</td></tr>';
                   
                    $i=1;
          
                     $sum['totalsum']=0;
                     foreach($arr as $k=>$v){
                         $sum[$k]=0;

                         $output[]='<tr><td>'.$k.'</td>';
                         foreach($dataz as $d=>$mk){
                             $totalsum[$k][$d]=0;
                             
                             $output[]='<td>';
                             
                             if (isset($v[$d])){
                                 $output[]='<table>
                                    <tr>
                                        <td width="50%">Наименование</td>
                                        <td width="15%">Цена</td>
                                        <td width="15%">Кол-во</td>
                                        <td width="20%">Сумма</td>
                                    </tr>';
                                             
                                 foreach($v[$d] as $row){  
                                    $output[]='<tr><td>'.$row['name'].'</td><td>'.$row['price'].'</td><td>'.$row['quantity'].'</td><td>'.$row['sumitem'].'</td></tr>';
                                    
                                    //$sum[$k][$d]+=$row['sumitem'];
                                    $totalsum[$k][$d]+=$row['sumitem']; 
                                    $totalsumday[$d]+=$row['sumitem'];
                                 } 
                                 
                                  $output[]='<tr><td colspan=3>Итого</td><td><b>'.$totalsum[$k][$d].'</b></td></tr></table>';
                             }
                             $output[]='</td>';   
                         }
                        $output[]='<td><b>'.array_sum($totalsum[$k]).'</b></td></tr>'; 
                        

                            
                            
                         $i++;
                     }
                     $vsego=0;
                     $output[]='<tr>
                            <td><b>Итого</b></td>';
                     foreach($dataz as $k=>$v){
                         $output[]='<td align=right><b>'.$totalsumday[$k].'</b></td>';
                         $vsego+=$totalsumday[$k];
                     }
                     $output[]='<td><b>'.$vsego.'</b></td></tr>';
                         
                 break;
                 case 'refuse_and_orders':
                 $where[]='t_order.parentid=0';
                   $query=mysql_query("SELECT d_order.id AS id,
                                               s_employee.name AS waitername,
                                               t_order.dt,
                                               t_order.note,
                                               s_items.name AS itemname,
                                               t_order.quantity,
                                               round(t_order.quantity * t_order.price,0) as summa,
                                               s_units_of_measurement.name AS measurename
                                        FROM t_order
                                             LEFT JOIN d_order ON d_order.id = t_order.orderid
                                             LEFT JOIN s_employee ON d_order.employeeid = s_employee.id
                                             LEFT JOIN d_changes ON d_order.changeid = d_changes.id
                                             LEFT JOIN s_items ON s_items.id = t_order.itemid
                                             LEFT JOIN s_units_of_measurement ON s_units_of_measurement.id = s_items.measurement
                                          ".(!empty($where)?'WHERE '.join(' AND ',$where):''));

                   
                    $i=1;
                     $sum['quantity']=0;
                     $sum['summa']=0;
                     while($row=mysql_fetch_assoc($query)){
                         $sum['summa']+=$row['summa'];
                         $sum['quantity']+=$row['quantity'];
                         $output[]='<tr><td>'.$i.'</td>
                            <td>'.$row['id'].'</td>
                            <td>'.$row['waitername'].'</td>
                            <td>'.$row['dt'].'</td>
                            
                            <td>'.$row['itemname'].'</td>
                            <td>'.$row['quantity'].'</td>
                            <td>'.$row['summa'].'</td>
                            <td>'.$row['measurename'].'</td>        
                            <td>'.$row['note'].'</td>                    
                            </tr>';
                         $i++;
                     }
                     $output[]='<tr>
                            <td colspan="2"><b>Итого</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            
                            <td><b>'.$sum['quantity'].'</b></td>
                            <td><b>'.$sum['summa'].'</b></td>
                            <td></td>
                            <td></td>
                         </tr>';
                 break;
                 case 'hoursales':
                 $where[]='t_order.parentid=0';
                    $query=mysql_query("SELECT ap.id,
                            ap.name,
                            s_items.id as itid,
                            DATE_FORMAT(d_order.dtclose, '%k') AS `hours`,
                            s_items.name AS itemname,
                            SUM(t_order.quantity) AS totalquantity,
                            ROUND(SUM(t_order.price*t_order.quantity)/SUM(t_order.quantity)) AS avgprice,
                            ROUND(SUM(t_order.quantity) * SUM(t_order.price*t_order.quantity)/SUM(t_order.quantity), 0) AS totalsum
                        FROM s_automated_point AS ap
                        LEFT JOIN d_order ON d_order.idautomated_point = ap.id
                        LEFT JOIN t_order ON t_order.orderid = d_order.id
                        LEFT JOIN s_items ON s_items.id = t_order.itemid
                        ".(!empty($where)?'WHERE '.join(' AND ',$where):'')."
                        GROUP BY hours, ap.id, s_items.id
                        ORDER BY itemname");
                        
                        
                        
                        
                        $res=array();
                        $gr=array();
                        while($row=mysql_fetch_assoc($query)){
                            $gr[$row['itid']]['itemname']=$row['itemname'];
                            $gr[$row['itid']][$row['hours']]=$row;
                        }
                     $i=1;
                     //$sum['quantity']=0;
                     $daysum=array();
                     $totalsum=0;
                     foreach($gr as $k=>$v){
                         $sum=0;
                         //$sum['quantity']+=$row['quantity'];
                         
                         $output[]='<tr>
                            <td>'.$i.'</td>
                            <td>'.$gr[$k]['itemname'].'</td>
                            ';
                            for ($j=0; $j<=23; $j++){
                                if (!isset($daysum[$j])) $daysum[$j]=$gr[$k][$j]['totalsum']; else $daysum[$j]+=$gr[$k][$j]['totalsum'];
                                $sum+=$gr[$k][$j]['totalsum'];
                                
                                ($apInRow ? '<td>Торговый объект</td>' : '' );
                                
                               $output[]='<td><center>
                               '.( $showQuantity ? $gr[$k][$j]['totalquantity'].'<br />' : '' ).'
                               '.( $showPrice ? $gr[$k][$j]['avgprice'].'<br />' : '' ).'
                               <b>'.$gr[$k][$j]['totalsum'].'</b></center>
                               </td>'; 
                            }
                            
                         $totalsum+=$sum;   
                         $output[]='<td><b>'.$sum.'</b></td></tr>';
                         $i++;
                     }
                     $output[]='<tr><td></td><td></td>';
                     for ($j=0; $j<=23; $j++){
                        $output[]='<td>'.$daysum[$j].'</td>';
                     }
                     $output[]='<td><b>'.$totalsum.'</b></td></tr>';
                     /*$output[]='<tr>
                            <td colspan="2"><b>Итого</b></td>
                            <td><b>'.$sum['quantity'].'</b></td>
                            <td></td>
                            <td><b>'.$sum['sumitem'].'</b></td>
                         </tr>';*/
                     
                 break;       
             }
             
             $output[]='</table><br />';
             if (isset($_GET['print'])){
              switch($_GET['ftype']){
                    case 'xls':  
                        header("Content-Type: application/download; charset=utf-8\n"); 
                        header("Content-Disposition: attachment; filename=".time().'.xls');
                        $res=join("",$output);
                        //переводим в вин-1251
                        //echo iconv("UTF-8", "windows-1251", $res);
                        echo $res;
                    break;
                    case 'html': 
                      echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Справочники</title>
    <script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
    <style>*{font:'.getConfigVal('otchet_font_size').'px Tahoma;margin:0;padding:0} h1{font:bold '.getConfigVal('otchet_h1_size').'px Tahoma} b{font-weight:bold} table{margin: 0; padding: 0; border: 1px solid #000;border-collapse:collapse; width: '.getConfigVal('otchet_width').'px;}
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
                        echo join("",$output);
                        echo '</body></html>';
                    break;
                     case 'pdf': 
                        require_once('pdf/mpdf.php');
                        $mpdf = new mPDF('utf-8', 'A4', '8', '', 2, 2, 1, 1, 2, 2); /*задаем формат, отступы и.т.д.*/
                        $mpdf->charset_in = 'utf8'; /*не забываем про русский*/
                        $stylesheet = file_get_contents('pdf/mpdf.css'); /*подключаем css*/
                        $mpdf->WriteHTML($stylesheet, 1);
                        $mpdf->list_indent_first_level = 0; 
                        $mpdf->WriteHTML(join("\n",$output), 2); /*формируем pdf*/
                        $mpdf->Output('mpdf.pdf', 'I');
                     break;
                }
             }else{
                 echo join("\n",$output);
                // echo '<br />Время формирования отчета: '.(time()-$ztime).' сек.';
             }
              echo' </div>';  
             if (empty($_GET['print'])){
                 echo '<a href="/company/report/report.php?do=otchet&print=1&ftype=pdf&'.join('&',$query_string).'" target="_blank"><img src="/company/i/icon_pdf.png"></a> 
                 <a href="/company/report/report.php?do=otchet&print=1&ftype=xls&'.join('&',$query_string).'" target="_blank"><img src="/company/i/xls.gif"></a>
                 <a href="/company/report/report.php?do=otchet&print=1&ftype=html&'.join('&',$query_string).'" target="_blank" class="printota"><img src="/company/i/printer.gif"></a>
                 ';
             }
        break;
         case 'otchet2': 
            if (!checkrights('gethtml_itogovy',1)) die(PERMISSION_DENIED);
            //Итоговый отчет
            $ztime=time();
             $output='';
             $order='';
             $where='';
             $group='';
             $inxls='';
              if (isset($_SESSION['idap'])&&(!isset($_SESSION['fromfront']))){
                 $_POST['chb_zasmenu']=$_SESSION['idap'];
                 $_POST['order']='name';
                 $_POST['idautomated_point']=$_SESSION['idap'];
                 
                 //echo '123';
                 $idwp = '';
                 $result = mysql_query( 'SELECT divChangeWorkplace FROM s_automated_point WHERE id='.addslashes($_SESSION['idap']).' LIMIT 1');
                 $row = mysql_fetch_array( $result );
                 $idwp = $row['divChangeWorkplace'] == '1' ? ' AND idworkplace='.( isset( $_SESSION['wid'] ) ? $_SESSION['wid'] : '1' ) : '';
                 
                 $result=mysql_query('select id, closed from d_changes where idautomated_point='.addslashes($_SESSION['idap']).$idwp.' order by id desc limit 1');
                 if (mysql_numrows($result)){
                     $row=mysql_fetch_array($result);
                     $_POST['chb']='zasmenu';
                     $_POST['chb_zasmenu']=$row['id'];
                     //$_POST['order']='name';
                     $output[]='<style>.ttda{margin: 0; padding: 0; }
       .ttda td{border: 1px dotted #CCCCCC; margin: 0; padding: 3px;}</style>';
                 }
                 
             }
             
             
             
             if(isset($_GET['print'])){
                 $inxls='border="1"';
                 if (isset($_GET['chb'])) $_POST['chb']=$_GET['chb'];
                 if (isset($_GET['chb_zasmenu'])) $_POST['chb_zasmenu']=$_GET['chb_zasmenu'];
                 if (isset($_GET['chb_zaperiod1'])) $_POST['chb_zaperiod1']=$_GET['chb_zaperiod1'];
                 if (isset($_GET['chb_zaperiod2'])) $_POST['chb_zaperiod2']=$_GET['chb_zaperiod2'];
                 if (isset($_GET['chb_smenperiod1'])) $_POST['chb_smenperiod1']=$_GET['chb_smenperiod1'];
                 if (isset($_GET['chb_smenperiod2'])) $_POST['chb_smenperiod2']=$_GET['chb_smenperiod2'];
                 if (isset($_GET['idautomated_point'])) $_POST['idautomated_point']=$_GET['idautomated_point'];
                 if (isset($_GET['noorder'])) $_POST['noorder']=$_GET['noorder'];
                 if (isset($_GET['order'])) $_POST['order']=$_GET['order'];
                 if (isset($_GET['groupbydate'])) $_POST['groupbydate']=$_GET['groupbydate'];
                 if (isset($_GET['order'])) $_POST['order']=$_GET['order'];
                 if (isset($_GET['groupbydate'])) $_POST['groupbydate']=$_GET['groupbydate'];
             }else{
                $query_string=array();
                if (isset($_POST['chb'])) $query_string[]='chb='.$_POST['chb'];
                if (isset($_POST['chb_zasmenu'])) $query_string[]='chb_zasmenu='.$_POST['chb_zasmenu'];
                if (isset($_POST['chb_zaperiod1'])) $query_string[]='chb_zaperiod1='.$_POST['chb_zaperiod1'];
                if (isset($_POST['chb_zaperiod2'])) $query_string[]='chb_zaperiod2='.$_POST['chb_zaperiod2'];
                if (isset($_POST['chb_smenperiod1'])) $query_string[]='chb_smenperiod1='.$_POST['chb_smenperiod1'];
                if (isset($_POST['chb_smenperiod2'])) $query_string[]='chb_smenperiod2='.$_POST['chb_smenperiod2'];
                if (isset($_POST['idautomated_point'])) $query_string[]='idautomated_point='.$_POST['idautomated_point'];
                if (isset($_POST['noorder'])) $query_string[]='noorder='.$_POST['noorder'];
                if (isset($_POST['order'])) $query_string[]='order='.$_POST['order'];
             }
            
             if (!isset($_POST['noorder'])){
                 $desc='';
                 if (isset($_POST['orderdesc'])){
                     $desc=' DESC';
                 }
                 $order=addslashes($_POST['order']).$desc;
             }else{
                 $order='id';
             }
             if (isset($_POST['groupbydate'])){
                 $group='date';
             }
             $output[]='<span style="font:10px Tahoma;">Время формирования отчета: '.date('d.m.Y H:i:s').'</span>';
             $output[]='<div class="div_otchet"> <h1>Итоговый отчет</h1>';
            
             $i=1;
             $title='';
             $where=array();
             
             if ($_POST['idautomated_point']>0){
                 $where[]='d_order.idautomated_point="'.addslashes($_POST['idautomated_point']).'"';
             } else if ( !isset( $_SESSION['admin'] ) ){
                 $res = mysql_query( 'SELECT rollid, GROUP_CONCAT(DISTINCT tid) AS tid FROM `t_employee_interface` AS i LEFT JOIN `z_user_right` AS r ON r.uid=i.employeeid AND `view`=1 WHERE i.employeeid='.$_SESSION['userid'] );
                 $tid = mysql_fetch_array( $res );
                 if ( $tid['rollid'] == 2 ){
                    if ( $tid['tid'] == '' )
                        $where[]='d_order.idautomated_point="-1"';//die(PERMISSION_DENIED)
                    else 
                        $where[]='d_order.idautomated_point IN ('.$tid['tid'].')';
                 }
             }
                      
             switch($_POST['chb']){
                 case 'zasmenu':
                     if ($_POST['chb_zasmenu']>0){
                        $where[]='changeid="'.addslashes($_POST['chb_zasmenu']).'"';
                         
                         $query=mysql_query("SELECT * FROM `d_changes` WHERE id='".addslashes($_POST['chb_zasmenu'])."' LIMIT 1");
                         $row=mysql_fetch_assoc($query);
                         $title='За смену: '.$row['name'].'_'.$row['dtopen'].'_'.$row['dtclosed'];
                     }
                 break;
                 case 'zaperiod':
                     if (($_POST['chb_zaperiod1']!='')&&($_POST['chb_zaperiod2']!='')){
                        $where[]='((d_order.creationdt>="'.date('Y-m-d H:i:s',strtotime($_POST['chb_zaperiod1'])).'") AND (d_order.creationdt<="'.date('Y-m-d H:i:s',strtotime($_POST['chb_zaperiod2'])).'"))';
                        
                         $title='За период: с'.$_POST['chb_zaperiod1'].' по'.$_POST['chb_zaperiod2'];
                         
                     }
                 break;
                 case 'smenperiod':
                     if (($_POST['chb_smenperiod1']!='')&&($_POST['chb_smenperiod2']!='')){
                        $where[]='((d_changes.dtopen>="'.date('Y-m-d H:i:s',strtotime($_POST['chb_smenperiod1'])).'") AND (d_changes.dtopen<="'.date('Y-m-d H:i:s',strtotime($_POST['chb_smenperiod2'])).'"))';
                        $title='Смены за период: с'.$_POST['chb_smenperiod1'].' по'.$_POST['chb_smenperiod2'];
                     }
                 break;
             }
             $output[]=$title.'<br />';
             
             /* 
             * 
             * Статистика
             * 
             */
             $query=mysql_query("(SELECT COUNT(*) AS quantity, 0 AS summa FROM d_order 
                        LEFT JOIN d_changes ON d_changes.id=d_order.changeid 
                        ".(!empty($where)?'WHERE '.join(' AND ',$where):'')." ) 
                        UNION ALL 
                        (SELECT SUM(t1.quantity) AS quantity, SUM(t1.saleprice) AS summa 
                        FROM ( SELECT t_order.itemid, SUM(t_order.quantity) AS quantity, 
                            (SUM(t_order.quantity * t_order.price) *(1 + d_order.servicepercent * s_items.service / 100) *(1 - d_order.discountpercent * s_items.discount / 100)) AS saleprice, 
                            IF(t_order.complex = 0, 0, t_order.id) AS ssid FROM t_order 
                            LEFT JOIN d_order ON d_order.id = t_order.orderid 
                            LEFT JOIN s_items ON s_items.id = t_order.itemid 
                            LEFT JOIN d_changes ON d_changes.id = d_order.changeid 
                            WHERE ".(!empty($where)?' '.join(' AND ',$where).' AND':'')."  t_order.quantity>0 AND t_order.parentid=0
                            GROUP BY t_order.orderid, t_order.itemid, ssid ) 
                        AS t1)
                        UNION ALL 
                        (SELECT SUM(t1.quantity) AS quantity, SUM(t1.saleprice) AS summa 
                        FROM ( SELECT SUM(t_order.quantity) AS quantity, 
                            (SUM(t_order.quantity * t_order.price) *(1 + d_order.servicepercent * s_items.service / 100) *(1 - d_order.discountpercent * s_items.discount / 100)) as saleprice, 
                            IF(t_order.complex = 0, 0, t_order.id) AS ssid FROM t_order 
                            LEFT JOIN d_order ON d_order.id = t_order.orderid 
                            LEFT JOIN s_items ON s_items.id = t_order.itemid 
                            LEFT JOIN d_changes ON d_changes.id = d_order.changeid 
                            WHERE ".(!empty($where)?''.join(' AND ',$where).' AND':'')."  t_order.quantity<0 AND  t_order.parentid=0
                            GROUP BY t_order.orderid, t_order.itemid, ssid ) 
                        AS t1)");

            
             $totals=array();
             while($row=mysql_fetch_array($query)){
                $totals[]=$row;
             }
                        
             $output[]='<table class="ttda" '.$inxls.'>
                <tr><td>Всего счетов:</td><td>'.intval($totals[0][0]).'</td></tr>
                <tr><td>Всего заказов:</td><td>'.intval($totals[1][0]).'</td></tr>
                <tr><td>Всего отказов:</td><td>'.intval($totals[2][0]).'</td></tr>
                <tr><td>Сумма отказов:</td><td>'.intval($totals[2][1]).'</td></tr>
                </table><br />';
             $output[]="<b>Отчет по видам оплаты</b><br />";
             
             /* 
             * 
             * /Статистика 
             * 
             */
             
             
             /* 
             * 
             * Отчет по видам оплат 
             * 
             */
             
             $query=mysql_query("SELECT r.apname AS toname,
                       r.pname as typepayname,
                       SUM(r.summa) as summa
                FROM (
                    SELECT s_automated_point.name AS apname,
                        s_types_of_payment.name AS pname,
                        s_types_of_payment.id AS pid,
                        ROUND(SUM(d_order.totalsum),0) AS summa
                    FROM d_order
                    LEFT JOIN s_automated_point ON d_order.idautomated_point = s_automated_point.id
                    LEFT JOIN d_changes ON d_changes.id = d_order.changeid
                    LEFT JOIN s_types_of_payment ON s_types_of_payment.id = d_order.paymentid
                    WHERE ".(!empty($where)?''.join(' AND ',$where).' AND':'')."  d_order.closed = 1
                    GROUP BY d_order.paymentid, d_order.idautomated_point 
                ) AS r
                GROUP BY r.pid, r.apname");


            
            $output[]='<table class="ttda" '.$inxls.'><tr class="tableheader">
                            <td>#</td>
                            <td>Торговый объект</td>
                            <td>Вид оплаты</td>
                            <td>Сумма</td>
                            </tr>';
             $i=1;
             $sum=0;
             while($row=mysql_fetch_array($query)){
                $output[]='<tr><td>'.$i.'</td><td>'.$row['toname'].'</td><td>'.$row['typepayname'].'</td><td>'.$row['summa'].'</td></tr>'; 
                $i++;
                $sum+=$row['summa'];
             }  
             $output[]='<tr><td colspan="2"><b>Итого</b></td><td></td><td><b>'.$sum.'</b></td></tr>';   
             $output[]='</table>'; 
                    
             /* 
             * 
             * /Отчет по видам оплат 
             * 
             */ 
             
             
             
             /* 
             * 
             * Отчет по официантам 
             * 
             */    
                  
             $query=mysql_query("SELECT s_employee.name AS waitername,
                COUNT(*) AS billcount,
                ROUND(SUM(d_order.totalsum),0) AS summa,
                ROUND(SUM(d_order.servicesum),0) AS handsum
            FROM d_order
            LEFT JOIN s_employee ON d_order.employeeid = s_employee.id
            LEFT JOIN d_changes ON d_order.changeid = d_changes.id
            ".(!empty($where)?'WHERE '.join(' AND ',$where):'')."
            GROUP BY d_order.employeeid");
            
            


            $output[]="<br /><b>Отчет по сотрудникам</b><br />";
            $output[]='<table class="ttda" '.$inxls.'><tr class="tableheader">
                    <td>#</td>
                    <td>Сотрудник</td>
                    <td>Кол</td>
                    <td>Обсл</td>
                    <td>Сумма</td>
                    </tr>';
             $i=1;
             $sum=0;
             $handsum=0;
             $billcount=0;
             while($row=mysql_fetch_array($query)){
                $output[]='<tr><td>'.$i.'</td><td>'.$row['waitername'].'</td><td>'.$row['billcount'].'</td><td>'.$row['handsum'].'</td><td>'.$row['summa'].'</td></tr>'; 
                $sum+=$row['summa'];
                $handsum+=$row['handsum'];
                $billcount+=$row['billcount'];
                $i++;
             }     
             $output[]='<tr><td colspan="2"><b>Итого</b></td><td><b>'.$billcount.'</b></td><td><b>'.$handsum.'</b></td><td><b>'.$sum.'</b></td></tr>'; 
             $output[]='</table>'; 
                     
             /* 
             * 
             * /Отчет по официантам 
             * 
             */               
                            
                            
             /* 
             * 
             * Отчет по подразделениям 
             * 
             */
             //$where[]='t_order.parentid=0';               
             $query=mysql_query("SELECT
                                      t.printers AS category,
                                      SUM(t.quantity) AS quantity,
                                      ROUND(SUM(t.quantity * t.price)) AS avgsum,
                                      ROUND(SUM(t.quantity * t.service)) AS servicesum,
                                      t.ssid
                                    FROM (
                                      SELECT
                                        s_subdivision.name AS printers,
                                        s_items.name,
                                        t_order.itemid,
                                        SUM(t_order.quantity) AS quantity,
                                        t_order.price * (1 + (d_order.servicepercent * s_items.service - d_order.discountpercent * s_items.discount) / 100) AS price,
                                        t_order.price * (d_order.servicepercent * s_items.service) / 100 AS service,
                                        IF (t_order.complex = 0,0,t_order.id) AS ssid
                                      FROM t_order
                                      LEFT JOIN d_order ON d_order.id = t_order.orderid
                                      LEFT JOIN s_items ON s_items.id = t_order.itemid
                                      LEFT JOIN s_subdivision ON s_subdivision.id = t_order.printerid
                                      LEFT JOIN d_changes ON d_changes.id = d_order.changeid
                                      WHERE  t_order.parentid=0 ".(!empty($where)?'AND '.join(' AND ',$where):'')."
                                      GROUP BY t_order.orderid, t_order.itemid,t_order.price
                                    ) AS t
                                    GROUP BY category
                                    ORDER BY category");

             $output[]="<br /><b>Отчет по подразделениям</b><br />";
             $output[]='<table class="ttda" '.$inxls.'><tr class="tableheader">
                    <td>#</td>
                    <td>Подразделение</td>
                    <td>Кол</td>
                    <td>Сумма</td>
                    <td>Обслуживание</td>
                    </tr>';
              $i=1;
              $avgsum=0;
              $servicesum=0;
              $quantity=0;
              while($row=mysql_fetch_array($query)){
                $output[]='<tr><td>'.$i.'</td><td>'.$row['category'].'</td><td>'.$row['quantity'].'</td><td>'.$row['avgsum'].'</td><td>'.$row['servicesum'].'</td></tr>'; 
                $avgsum+=$row['avgsum']; 
                $servicesum+=$row['servicesum'];
                $quantity+=$row['quantity'];
                $i++;
              }  
              $output[]='<tr><td colspan="2"><b>Итого</b></td><td><b>'.$quantity.'</b></td><td><b>'.$avgsum.'</b></td><td><b>'.$servicesum.'</b></td></tr>';    
              $output[]='</table>';          
             /* 
             * 
             * /Отчет по подразделениям 
             * 
             */ 
             
             
             
                                         
             /* 
             * 
             * Отчет по клиентам 
             * 
             */               
             $query=mysql_query("SELECT s_clients.name AS clientname,
                    COUNT(*) AS billcount,
                    SUM(ss.q) AS poscount,
                    ROUND(SUM(d_order.totalsum),0) AS summa,
                    ROUND(SUM(d_order.totalsum + d_order.discountsum), 0) AS summa2
                FROM d_order
                LEFT JOIN s_clients ON d_order.clientid = s_clients.id
                LEFT JOIN d_changes ON d_order.changeid = d_changes.id
                LEFT JOIN ( SELECT orderid, SUM(quantity) AS q
                    FROM t_order
                    GROUP BY orderid ) AS ss ON d_order.id = ss.orderid
                 ".(!empty($where)?'WHERE '.join(' AND ',$where):'')."
                GROUP BY d_order.clientid");


             $output[]="<br /><b>Отчет по клиентам</b><br />";
             $output[]='<table class="ttda" '.$inxls.'><tr class="tableheader">
                        <td>#</td>
                        <td>Клиент</td>
                        <td>Сч-в</td>
                        <td>Зак</td>
                        <td>Сумма</td>
                        <td>Сумма без скидки</td>
                        </tr>';
             $i=1;
             $billcount=0;
             $poscount=0;
             $summa=0;
             $summa2=0;
             while($row=mysql_fetch_array($query)){
                $output[]='<tr><td>'.$i.'</td><td>'.$row['clientname'].'</td><td>'.$row['billcount'].'</td><td>'.$row['poscount'].'</td><td>'.$row['summa'].'</td><td>'.$row['summa2'].'</td></tr>'; 
                $billcount+=$row['billcount'];
                $poscount+=$row['poscount'];
                $summa+=$row['summa'];
                $summa2+=$row['summa2'];
                $i++;
             }     
             $output[]='<tr><td colspan="2"><b>Итого</b></td><td><b>'.$billcount.'</b></td><td><b>'.$poscount.'</b></td><td><b>'.$summa.'</b></td><td><b>'.$summa2.'</b></td></tr>'; 
             $output[]='</table>';          
             /* 
             * 
             * /Отчет по клиентам 
             * 
             */                
                            
                            
             /* 
             * 
             * Акт реализации 
             * 
             */                     
             $query=mysql_query("SELECT t.itemid,
                                   t.name,
                                   SUM(t.quantity) AS quantity,
                                   ROUND(SUM(t.quantity * t.price)) AS avgsum,
                                   t.ssid
                                FROM (
                                  SELECT s_items.name,
                                    t_order.itemid,
                                    SUM(t_order.quantity) as quantity,
                                    t_order.price * (1 + (d_order.servicepercent * s_items.service - d_order.discountpercent * s_items.discount) / 100) AS price,
                                    IF (t_order.complex = 0, 0, t_order.id) AS ssid
                                  FROM t_order
                                    LEFT JOIN d_order ON d_order.id = t_order.orderid
                                    LEFT JOIN s_items ON s_items.id = t_order.itemid
                                    LEFT JOIN d_changes ON d_changes.id = d_order.changeid
                                  WHERE  t_order.parentid=0 ".(!empty($where)?'AND '.join(' AND ',$where):'')."
                                  GROUP BY t_order.orderid, t_order.itemid,t_order.price
                                  ) AS t
                                WHERE t.quantity > 0
                                GROUP BY t.itemid
                                ORDER BY name");
             if (!isset($_SESSION['idap'])&&(!isset($_SESSION['fromfront'])))
             {
             $output[]="<br /><b>Акт реализации</b><br />";
             $output[]='<table class="ttda" '.$inxls.'><tr class="tableheader">
                    <td>#</td>
                    <td>Наименование</td>
                    <td>Кол-во</td>
                    <td>Сумма</td>
                    </tr>';
             $i=1;
             $quantity=0;
             $avgsum=0;
             while($row=mysql_fetch_array($query)){
                $output[]='<tr><td>'.$i.'</td><td>'.$row['name'].'</td><td>'.$row['quantity'].'</td><td>'.$row['avgsum'].'</td></tr>'; 
                $quantity+=$row['quantity'];
                $avgsum+=$row['avgsum'];
                $i++;
             }    
             $output[]='<tr><td colspan="2"><b>Итого</b></td><td><b>'.$quantity.'</b></td><td><b>'.$avgsum.'</b></td></tr>';  
             $output[]='</table>';          
             }
                                                  
             /* 
             * 
             * /Акт реализации 
             * 
             */                
           
                
             $output[]='<br />';
             if (isset($_GET['print'])){
              switch($_GET['ftype']){
                    case 'xls':  
                        header("Content-Type: application/download; charset=utf-8\n"); 
                        header("Content-Disposition: attachment; filename=".time().'.xls');
                        $res=join("",$output);
                        //echo iconv("UTF-8", "windows-1251", $res);;
                        echo $res;
                    break;
                     case 'html':  
                        echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Справочники</title>
    <script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
   <style>*{font:'.getConfigVal('otchet_font_size').'px Tahoma;margin:0;padding:0} h1{font:bold '.getConfigVal('otchet_h1_size').'px Tahoma} b{font-weight:bold} table{margin: 0; padding: 0; border: 1px solid #000;border-collapse:collapse; width: '.getConfigVal('otchet_width').'px;}
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
                        echo join("",$output);
                        echo '</body></html>';
                    break;
                     case 'pdf': 
                        require_once('pdf/mpdf.php');
                        $mpdf = new mPDF('utf-8', 'A4', '8', '', 0, 0, 0, 0, 0, 0); /*задаем формат, отступы и.т.д.*/
                        $mpdf->charset_in = 'utf8'; /*не забываем про русский*/
                        $stylesheet = file_get_contents('pdf/mpdf.css'); /*подключаем css*/
                        $mpdf->WriteHTML($stylesheet, 1);
                        $mpdf->list_indent_first_level = 0; 
                        $mpdf->WriteHTML(join("\n",$output), 2); /*формируем pdf*/
                        $mpdf->Output('mpdf.pdf', 'I');
                     break;
                }
             }else{
                 
                 echo join("\n",$output);
                 //echo '<br />Время формирования отчета: '.(time()-$ztime).' сек.';
             }
                
             if (empty($_GET['print'])){
                 echo '</div><a href="/company/report/report.php?do=otchet2&print=1&ftype=pdf&'.join('&',$query_string).'" target="_blank"><img src="/company/i/icon_pdf.png"></a> 
                 <a href="/company/report/report.php?do=otchet2&print=1&ftype=xls&'.join('&',$query_string).'" target="_blank"><img src="/company/i/xls.gif"></a>
                 <a href="/company/report/report.php?do=otchet2&print=1&ftype=html&'.join('&',$query_string).'" target="_blank" class="printota"><img src="/company/i/printer.gif"></a>
                 ';
                 
             }
        break; 
}

?>
