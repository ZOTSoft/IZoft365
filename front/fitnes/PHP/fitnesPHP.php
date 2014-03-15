<?php
    session_start();
    include('../../../company/check.php');
    include('../../../company/functions.php');
    checksessionpassword();    
    if (!isset($_SESSION['point'])){
        header("Location: /login.php");
        die;
    }
    include('../../../company/mysql.php');
    if (isset($_SESSION['timezone'])){ 
    if (!empty($_SESSION['timezone'])){
        date_default_timezone_set($_SESSION['timezone']); 
        mysql_query("SET `time_zone` = '".date('P')."'"); 
    }
//        mysql_query("SET time_zone = '+06:00'"); 
//        SET time_zone = '+03:00'
    }
//mysql_query("SET time_zone = '+06:00'");

include('../../PHP/errorsPHP.php');

include('../../PHP/frontFunctions.php');



if (isset($_GET['ftype'])){
    $act=$_GET['ftype'];
}else{
    $act=(ISSET($_POST['actionScript']))?$_POST['actionScript']:'';
}
switch ($act){
     case 'fitnessContent':{
        if (isset($_POST['location'])){
            $location=$_POST['location'];
        }else{
            echo json_encode(array('rescode'=>  errcode(501),'resmsg'=>  errmsg(501))); die;
        }
        
        if (isset($_POST['interval'])){
            $interval=$_POST['interval'];
        }else{
            $interval=30;
        }
        
        if (isset($_POST['date'])){
            $date=$_POST['date'];
        }else{
            echo json_encode(array('rescode'=>  errcode(502),'resmsg'=>  errmsg(502))); die;
        }
        
        $sqltext='SELECT obj.id,obj.name
                   FROM s_objects as obj
                   LEFT JOIN s_location as loc on loc.id=obj.locationid
                   WHERE loc.id='.$location;    
        $selectObj=  mysql_query($sqltext);
        
        
        $sqlP =  mysql_query('SELECT HOUR(jTimeStart)*60+MINUTE(jTimeStart) as jTimeStart,HOUR(jTimeEnd)*60+MINUTE(jTimeEnd) as jTimeEnd FROM s_automated_point WHERE id='.$_SESSION['idap']);
        $period = mysql_fetch_assoc($sqlP);
        
        $minTime=$period['jTimeStart']/60%60;
        $maxTime=$period['jTimeEnd']/60%60;
        if (!(($minTime>=0)&&($maxTime<24)&&($minTime<$maxTime)))
        {
            $minTime=420;
            $maxTime=1320;
        }
        
        $sqltext2='SELECT
                            j.*,
                            cl.name as clientname,
                            HOUR(j.dtstart)*60+MINUTE(j.dtstart) as timeStart,
                            HOUR(j.dtend)*60+MINUTE(j.dtend) as timeEnd
                    FROM s_journal as j 
                    LEFT JOIN s_employee as e ON e.id=j.employeeid
                    LEFT JOIN s_clients as cl on cl.id=j.clientid
                    WHERE j.objectid>0 and  DATE(j.dtstart)="'.$date.'" and idap='.$_SESSION['idap'].' and HOUR(j.dtstart)*60+MINUTE(j.dtstart)>='.$period['jTimeStart'].' and HOUR(j.dtend)*60+MINUTE(j.dtend)<='.$period['jTimeEnd'].'
                    ORDER BY
                            j.dtStart,e. NAME';
        $selectJournal=  mysql_query($sqltext2);
        if ($selectObj){
            $content='<table class="jurnal_css" >';
            $content.='<tr>';
            $content.='<td class="h_css">Время</td>';
            $mCount=0;
            $array=array();
            
            while($r=  mysql_fetch_assoc($selectObj)) {
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
                                    if ($rowTime['objectid']==$array[$i-1]['id']){
                                       $content.='<td><div onmousedown="showFitnessRecord(this)" jFID="'.$rowTime['id'].'" style="height:'.((floor(20*($rowTime['timeEnd']-$rowTime['timeStart'])/$interval))+1).'px;">'.
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
     
     case 'getLocList':{
          $locListQuery =  mysql_query('SELECT id,name FROM s_location WHERE pointid='.$_SESSION['idap']);
          
          if ($locListQuery){
                    $str='';
                    while ($r=  mysql_fetch_assoc($locListQuery)){
                         $str.='<option value="'.$r['id'].'">'.$r['name'].'</option>';
                    }
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=> errmsg(0),'cont'=>$str )); die;        
          }else{
             echo json_encode(array('rescode'=>  errcode(505),'resmsg'=>  errmsg(505))); die; 
          }
          
     break;
     }
     
     case 'getObjListForJournal':{        
          $objListQuery =  mysql_query('SELECT obj.id,obj.name FROM s_objects as obj 
                                        LEFT JOIN s_location as loc ON loc.id=obj.locationid
                                        WHERE loc.pointid='.$_SESSION['idap'].'
                                       ');
          
          if ($objListQuery){
                    $str='';
                    while ($r=  mysql_fetch_assoc($objListQuery)){
                         $str.='<option value="'.$r['id'].'">'.$r['name'].'</option>';
                    }
                    echo json_encode(array('rescode'=>  errcode(0),'resmsg'=> errmsg(0),'cont'=>$str )); die;        
          }else{
             echo json_encode(array('rescode'=>  errcode(504),'resmsg'=>  errmsg(504))); die; 
          }
          
     break;
     }
     case 'saveRecordToFitnessJournal':{

        if (isset($_POST['type'])){
            $type=$_POST['type'];
        }else{
            echo json_encode(array('rescode'=>  errcode(506),'resmsg'=>  errmsg(506))); die;
        };    
        if (isset($_POST['dtBegin'])){
            $dtBegin=$_POST['dtBegin'];
        }else{
            echo json_encode(array('rescode'=>  errcode(507),'resmsg'=>  errmsg(507))); die;
        }; 
        if (isset($_POST['employeeid'])){
            $employeeid=$_POST['employeeid'];
        }else{
            echo json_encode(array('rescode'=>  errcode(508),'resmsg'=>  errmsg(508))); die;
        }; 
        if (isset($_POST['note'])){
            $note=$_POST['note'];
        }else{
            echo json_encode(array('rescode'=>  errcode(513),'resmsg'=>  errmsg(513))); die;
        }; 
        if (isset($_POST['clientid'])){
            $clientid=$_POST['clientid'];  
        }else{
            echo json_encode(array('rescode'=>  errcode(509),'resmsg'=>  errmsg(509))); die;
        }; 
        if (isset($_POST['objId'])){
            $objId=$_POST['objId'];  
        }else{
            echo json_encode(array('rescode'=>  errcode(510),'resmsg'=>  errmsg(510))); die;
        };
        if (isset($_POST['timeBegin'])){
            $timeBegin=$_POST['timeBegin'];  
        }else{
            echo json_encode(array('rescode'=>  errcode(511),'resmsg'=>  errmsg(511))); die;
        }; 
        if (isset($_POST['timeEnd'])){
            $timeEnd=$_POST['timeEnd'];  
        }else{
            echo json_encode(array('rescode'=>  errcode(512),'resmsg'=>  errmsg(512))); die;
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
            echo json_encode(array('rescode'=>  errcode(514),'resmsg'=> errmsg(514))); die;
        }
        
        $curDate = strtotime(date('Y-m-d'));
        $comeDate =  strtotime($dtBegin);
        
        $dtEnd=$dtBegin.' '.$timeEnd;
        $dtBegin=$dtBegin.' '.$timeBegin;
        
        if ($comeDate<$curDate){
           echo json_encode(array('rescode'=>  errcode(515),'resmsg'=> errmsg(515))); die; 
        }
        
        $curTime=strtotime(date('Y-m-d H:i'));
        $comeTime=strtotime($dtBegin);
        
        
        
        if ($timeEnd<=$timeBegin){
            echo json_encode(array('rescode'=>  errcode(516),'resmsg'=> errmsg(516) )); die;
        }
        if ($curTime>$comeTime){
            echo json_encode(array('rescode'=>  errcode(517),'resmsg'=> errmsg(517) )); die; 
        }
        
//        echo json_encode(array('rescode'=>  errcode(173),'resmsg'=> ' ДатаК'.$dtEnd.' ДатаН'.$dtBegin.' ВремяН'.$timeBegin.' ВремяК'.$timeEnd)); die;
        
        if ($type=='insert'){
            $tSum=fitness_tarifSum($uTime1,$uTime2,$dtBegin,addslashes($objId));
            $q =  mysql_query('SELECT name FROM s_items WHERE id='.$tSum['itemid']);
            $qr =  mysql_fetch_assoc($q);
            $ordetT=array(0=>array('id'=>$tSum['itemid'],
                            'count'=>1,
                            'printer'=>0,
                            'summa'=>$tSum['tarifSum'],
                            'note'=>'',                                
                            'price'=>$tSum['tarifSum'],
                            'name'=>$qr['name']));
            $orderH=array('tableid'=>addslashes($objId),
                            'discountpercent'=>0,
                            'discountsum'=>0,
                            'clientid'=>addslashes($clientid),
                            'totalsum'=>$tSum['tarifSum'],
                            'guestscount'=>1,
                            'servicepercent'=>0,
                            'servicesum'=>0, 
                            'discountid'=>0, 
                            'orderid'=>0);
            
            
            
            $tmparray=checkChage();
            
//            echo $tmparray['idchange'];
//            print_r($orderH);
//            print_r($ordetT);die;
            
            $array=doSaveOrder($orderH, $ordetT, $tmparray['idchange'], 3);
              
            $sqlText='INSERT INTO s_journal SET
                      dtstart="'.addslashes($dtBegin).'",
                      dtend="'.addslashes($dtEnd).'",
                      authorid='.$_SESSION['employeeid'].',
                      employeeid='.addslashes($employeeid).',
                      objectid='.addslashes($objId).',    
                      clientid='.addslashes($clientid).',
                      note="'.addslashes($note).'",
                      orderid='.$array['orderid'].',
                      idap='.$_SESSION['idap']; 
        }else if ($type=='update'){
            if (isset($_POST['fid'])){
                $fid=$_POST['fid'];  
            }else{
                echo json_encode(array('rescode'=>  errcode(518),'resmsg'=>  errmsg(518))); die;
            }; 
            
            $qu=  mysql_query('SELECT orderid FROM s_journal WHERE id='.$fid);
            $qur=  mysql_fetch_assoc($qu);
            
            $tSum=fitness_tarifSum($uTime1,$uTime2,$dtBegin,addslashes($objId));
            
            $ordetT=array();
            $orderH=array('tableid'=>addslashes($objId),
                            'discountpercent'=>0,
                            'discountsum'=>0,
                            'clientid'=>addslashes($clientid),
                            'totalsum'=>$tSum['tarifSum'],
                            'guestscount'=>1,
                            'servicepercent'=>0,
                            'servicesum'=>0, 
                            'discountid'=>0, 
                            'orderid'=>$qur['orderid']);
            
            $tmparray=checkChage();
//            echo $tmparray['idchange'];
//            print_r($orderH);
//            print_r($ordetT);die;
            
//            $array=doSaveOrder($orderH, $ordetT, $tmparray['idchange'], 3);
            
            $sqlText='UPDATE s_journal SET
                      dtstart="'.addslashes($dtBegin).'",
                      dtend="'.addslashes($dtEnd).'",
                      authorid='.$_SESSION['employeeid'].',
                      employeeid='.addslashes($employeeid).',
                      objectid='.addslashes($objId).',
                      clientid='.addslashes($clientid).',
                      note="'.addslashes($note).'",
                      idap='.$_SESSION['idap'].'
                      WHERE id='.$fid;
        }
        $insertQuery=  mysql_query($sqlText);
        if ($insertQuery){
            echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0)));
        }else{
            echo json_encode(array('rescode'=>  errcode(519),'resmsg'=>  errmsg(519))); die; 
        }
        
     break;
     }
     case 'getFitnessRecordInf':{        
          $fid=$_POST['fid'];
          $objListQuery =  mysql_query("SELECT
                    obj. NAME AS objname,
                    obj.id as objid,
                    DATE_FORMAT(j.dtstart, '%Y-%m-%d') AS dt,
                    DATE_FORMAT(j.dtstart, '%T') AS timestart,
                    DATE_FORMAT(j.dtend, '%T') AS timeend,
                    HOUR(j.dtend)*60+MINUTE(j.dtend) - HOUR(j.dtstart)*60+MINUTE(j.dtstart) as duringTime,
                    DATE_FORMAT(j.dt_j, '%Y-%m-%d') AS dtreg,
                    cl.name as clientname,
                    cl.birthday as clBirthday,
                    cl.phone as clTel,
                    cl.address as clAddress,
                    j.orderid as orderid,
                    cl.id as clientid
                    
            FROM
                    s_journal AS j
            LEFT JOIN s_objects AS obj ON obj.id = j.objectid
            LEFT JOIN d_order AS d ON d.id = j.orderid
            LEFT JOIN s_clients as cl ON cl.id=j.clientid
            WHERE
                    j.id =".$fid);
          if ($objListQuery){
                $r=mysql_fetch_assoc($objListQuery);   
                echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'arr'=>$r)); die; 
          }else{
             echo json_encode(array('rescode'=>  errcode(520),'resmsg'=>  errmsg(520))); die; 
          }
     break;
     }
     case 'getMenuFoldersServiceToFitnes':{
         if (!isset($_POST['parentid'])){
              echo json_encode(array('rescode'=>  errcode(521),'resmsg'=>  errmsg(521))); die;
          }else{
                $parentid=  addslashes($_POST['parentid']);
          };  
        $sqltext='';   
        if ($_SESSION['doNotUseMenuDesign']==0){
            $sqlUpperQuery=  mysql_query('SELECT
                                            it.id as id,
                                            it.NAME as name,
                                            t.isgroup as isgroup,
                                            t.parentid as parentid
                                       FROM 
                                            t_menu_items as t
                                       LEFT JOIN s_menu as m on m.id=t.menuid
                                       LEFT JOIN s_automated_point as ap on ap.menuid=m.id
                                       LEFT JOIN s_items as it on it.id=t.itemid
                                       WHERE t.id = '.$parentid.' and ap.id='.$_SESSION['idap']);
            
            if (mysql_num_rows($sqlUpperQuery)>0){
                $upperRow=  mysql_fetch_assoc($sqlUpperQuery);          
                    $sqltext='(SELECT '.$upperRow['parentid'].' as id,"← Назад" as name, '.$upperRow['isgroup'].' as isgroup, '.$upperRow['parentid'].' as parentid,0 as menuid,0 as price)
                        UNION';
            }
            $sqltext.='(SELECT
                           it.id as id,
                           if(t.isgroup=1,CONCAT(it.name," ","↓"),CONCAT(it.name," ","→")) as name,
                           t.isgroup as isgroup,
                           t.parentid as parentid,
                           t.menuid as menuid,
                           round(t.price) as price
                      FROM 
                           t_menu_items as t
                      LEFT JOIN s_menu as m on m.id=t.menuid
                      LEFT JOIN s_automated_point as ap on ap.menuid=m.id
                      LEFT JOIN s_items as it on it.id=t.itemid
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
                                        0 as printer,
                                        0 as price
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
                         i.i_printer AS printer,
                         round(i.price) as price
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
     case 'saveToTableOrderServiceToFitness':{        
            if (isset($_POST['orderT'])){
                $orderT=$_POST['orderT'];
            }else{
                echo json_encode(array('rescode'=>  errcode(523),'resmsg'=>  errmsg(523))); die;
            };
            if (isset($_POST['orderid'])){
                $orderid=$_POST['orderid'];
            }else{
                echo json_encode(array('rescode'=>  errcode(522),'resmsg'=>  errmsg(522))); die;
            };
            foreach ($orderT as $value)  {
                    $sqlInsertTable = 'Insert Into t_order (orderid,parentid,itemid,quantity,printerid,sum,note,discountsum,servicesum,salesum,price) Values ';
                    $sqlInsertTable .= '(' . $orderid . ','
                            . '0,'
                            . '' . addslashes($value['id']) . ','
                            . '' . 1 . ','
                            . '' . 0 . ','
                            . '' . (addslashes($value['price'])) . ','
                            . '"",'
                            . '' . 0 . ','
                            . '' . 0 . ','
                            . '' . 0 . ','
                            . '' . addslashes($value['price']) . '),'; 
                    
                    $sql = substr($sqlInsertTable, 0, strlen($sqlInsertTable) - 1);
                    $result = mysql_query($sql);
             }
          echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0))); die;
     break;
     }
     
     case 'refreshFitnesRecord':{        
        if (isset($_POST['orderID'])){
            $orderID=$_POST['orderID'];
        }else{
            echo json_encode(array('rescode'=>  errcode(523),'resmsg'=>  errmsg(523))); die;
        };
        $sqltext="SELECT t.id as id,i.name as name,round(t.price) as price
                    FROM t_order as t
                    LEFT JOIN s_items as i on i.id=t.itemid
                    WHERE t.orderid=".$orderID;             
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
    case 'deleteFromServiceToRecord':{        
        if (isset($_POST['id'])){
            $id=$_POST['id'];
        }else{
            echo json_encode(array('rescode'=>  errcode(524),'resmsg'=>  errmsg(524))); die;
        };
        
        $sqltext="DELETE FROM t_order WHERE id=".$id;             
        $selectFolderQuery =  mysql_query($sqltext);
        if ($selectFolderQuery){
               echo json_encode(array('rescode'=> errcode(0),'resmsg'=>  errmsg(0)));
        }
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
                                    "← Назад" AS clientname)
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
                                            ),' ','+')) as clientname
                                             
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
    default :{   
        echo json_encode(array('rescode'=>  errcode(503),'resmsg'=>  errmsg(503))); die;
    break;
}
}


?>
