<?php
function SubOrd($action,$orderid){
    
    if ($action=='refuse'){
        
     $sqlHead="SELECT
                    p. NAME AS clientname,
                    d.idout AS id,
                    o. NAME AS tablename,
                    e. NAME AS employeename,
		    DATE_FORMAT(t.dt, '%d.%m.%Y %H:%i:%s') AS itemdt
            FROM
                    d_order AS d
            LEFT JOIN s_clients AS p ON d.clientid = p.id
            LEFT JOIN s_objects AS o ON d.objectid = o.id
            LEFT JOIN s_employee AS e ON d.employeeid = e.id
            LEFT JOIN t_order AS t ON d.id = t.orderid
            WHERE
                    d.id = ".$orderid."  AND t.quantity < 0
	    GROUP BY
                    t.printed";
        
     $sql="SELECT
                    IF (NOT ISNULL(t4.name),CONCAT(i.NAME,' (',t4.name,')'),i.NAME) AS foodname,
                    t.price AS price,
                    sum(t.quantity) AS quantity,
                    prn. NAME AS printername,
                    prn.id AS prnid,
                    prn.sysname AS prnsysname,
                    t.id AS tid,
                    t.printed AS printed,
                    dvs. NAME AS dvsname,
                    dvs.id AS dvsid,
                    t.note AS note
            FROM
                    d_order AS d
            LEFT JOIN t_order AS t ON d.id = t.orderid
            LEFT JOIN s_items AS i ON t.itemid = i.id
            LEFT JOIN s_types_of_payment AS tp ON d.paymentid = tp.id
            LEFT JOIN s_subdivision AS dvs ON t.printerid = dvs.id
            LEFT JOIN s_printers AS prn ON dvs.printerid = prn.id
            LEFT JOIN (	SELECT t.id as id,i.name as name FROM t_order as t 
                        LEFT JOIN s_items as i on i.id=t.itemid
                        WHERE t.orderid=".$orderid."    
                      ) as t4 on t4.id=t.parentid
            WHERE
                    d.id = ".$orderid."
            AND t.printed = 0
            AND t.quantity < 0
            GROUP BY
                    i.id,
                    t.printed
            ORDER BY
                    prn.id,
                    dvs.id";
//echo json_encode(array('rescode'=>  errcode(66),'resmsg'=>  errmsg(66).$sql)); die;                    
           $doc = "<Header>
           <order>refuse</order>";
    }else if ($action=='suborder'){ 
        
    $sqlHead="SELECT
                    p. NAME AS clientname,
                    d.idout AS id,
                    o. NAME AS tablename,
                    e. NAME AS employeename,
		    DATE_FORMAT(t.dt, '%d.%m.%Y %H:%i:%s') AS itemdt
            FROM
                    d_order AS d
            LEFT JOIN s_clients AS p ON d.clientid = p.id
            LEFT JOIN s_objects AS o ON d.objectid = o.id
            LEFT JOIN s_employee AS e ON d.employeeid = e.id
            LEFT JOIN t_order AS t ON d.id = t.orderid
            WHERE
                    d.id = ".$orderid." AND t.printed=0
	    GROUP BY
                    t.printed";
        
    $sql="SELECT
                    IF (NOT ISNULL(t4.name),CONCAT(i.NAME,' (',t4.name,')'),i.NAME) AS foodname,
                    t.price AS price,
                    sum(quantity) AS quantity,
                    DATE_FORMAT(t.dt, '%d.%m.%Y %H:%i:%s') AS itemdt,
                    prn. NAME AS printername,
                    prn.id AS prnid,
                    prn.sysname AS prnsysname,
                    t.id AS tid,
                    t.printed AS printed,
                    dvs. NAME AS dvsname,
                    dvs.id AS dvsid,
                    t.note AS note
            FROM
                    d_order AS d
            LEFT JOIN t_order AS t ON d.id = t.orderid
            LEFT JOIN s_items AS i ON t.itemid = i.id
            LEFT JOIN s_subdivision AS dvs ON t.printerid = dvs.id
            LEFT JOIN s_printers AS prn ON dvs.printerid = prn.id
            LEFT JOIN (	SELECT t.id as id,i.name as name FROM t_order as t 
                        LEFT JOIN s_items as i on i.id=t.itemid
                        WHERE t.orderid=".$orderid."    
                      ) as t4 on t4.id=t.parentid
            WHERE
                    d.id = ".$orderid."
            AND t.printed = 0 and (ISNULL(i.complex) or i.complex=0)
            GROUP BY
                    i.id,
                    t.printed,
                    t.parentid
            ORDER BY
                    prn.id,
                    dvs.id";
            $doc = "<Header>
            <order>suborder</order>";
    }
    
    $headRes=  mysql_query($sqlHead);
    $headRow=  mysql_fetch_assoc($headRes);
    
    $result=mysql_query($sql);
    $i=1;
//    echo json_encode(array('rescode'=>  errcode(66),'resmsg'=>  $sqlHead)); die;
    $headRow["clientname"]=str_replace('%', '%%', $headRow["clientname"]);   
        $doc.= "<numcheck>".$headRow["id"]."</numcheck>
                <dt>".$headRow["itemdt"]."</dt> 
                <waiter>".$headRow["employeename"]."</waiter> 
                <client>".$headRow["clientname"]."</client> 
                <table>".($headRow["tablename"]==''?"no":$headRow["tablename"])."</table> ";
     $k=0;  
     $c=1;
     $logstr2='';
     
                while($row1=mysql_fetch_assoc($result)) {
                    if ($row1["printed"]==0){
                        $doc.="\n<item".$c."> \n";

                                $data[$i]["num"]=$c;
                                $doc.="<num>".$c."</num>\n";

                                $data[$i]["foodname"]=$row1["foodname"];
                                $row1["foodname"]=str_replace('%', '%%', $row1["foodname"]);
                                $doc.="<foodname>". $row1["foodname"]."</foodname>\n";
                                
                                
                                $data[$i]["price"]=$row1["price"];
                                $doc.="<price>".(float)$row1["price"]."</price>\n";
                                
                                $data[$i]["quantity"]=$row1["quantity"]; 
                                $doc.="<quantity>".(float)$row1["quantity"]."</quantity>\n";

                                $data[$i]["printername"]=$row1["printername"];
                                $doc.="<printername>".$row1["printername"]."</printername>\n";

                                $data[$i]["prnid"]=$row1["prnid"];
                                $doc.="<prnid>".$row1["prnid"]."</prnid>\n";

                                $data[$i]["prnsysname"]=$row1["prnsysname"];
                                $doc.="<prnsysname>".$row1["prnsysname"]."</prnsysname>\n";

                                $data[$i]["dvsname"]=$row1["dvsname"];
                                $doc.="<dvsname>".$row1["dvsname"]."</dvsname>\n";

                                $data[$i]["dvsid"]=$row1["dvsid"];
                                $doc.="<dvsid>".$row1["dvsid"]."</dvsid>\n";

                                $data[$i]["note"]=$row1["note"];
                                $doc.="<note>".$row1["note"]."</note>\n";
                    $doc.="</item".$c.">\n";
                    
                    $logstr2.='Товар: '.$row1["foodname"].'; кол-во:'.(float)$row1["quantity"].'; подразделение:'.$row1["dvsname"].'; системный принтер:'.$row1["prnsysname"].'<br>';
                    
                    $k++;
                    $c++;
                }  
                };            
        $doc.="</Header>";
       if($k==0){
           $doc='';
       }
       setPrintedForItems($orderid);
       
       $logStr="Печать подзаказника счета №".getShiftIdOrder($orderid);
       addlog($logStr.'<br>'.$logstr2,9);

        
       
       return $doc;
       
}   

function printOrd($orderid,$typeCheck,$infRow=''){
    
//$type =$_POST["type"];
//$cash =$_POST["cash"];   
    
        $sqlHead="SELECT
                p. NAME AS clientname,
                p.id AS client,
                barcode AS barcode,
                d.idout AS id,
                d.printed AS printed,
                d.closed AS closed,
                d.discountpercent AS discountpercent,
                d.servicepercent AS servicepercent,
                d.servicesum AS servicesum,
                d.guestcount AS guests,
                o. NAME AS tablename,
                round(d.totalsum) AS totalsum,
                e. NAME AS employeename,
                DATE_FORMAT(d.creationdt,'%d.%m.%y %H:%i:%S') AS dtopen,
                DATE_FORMAT(IF (d.dtclose IS NULL,0,d.dtclose),'%H:%i:%S') AS dtclose,
                tp. NAME AS payment,
                IF ((tp.id = ap.cashid),0,IF ((tp.id = ap.slipid), 1, - 1)) AS typepay,
                ap.nameForPrint AS restname,
                d.sumfromclient AS sumfromclient,
                d.discountsum AS discountsum,
                round((totalsum + d.discountsum - d.servicesum)) AS clearsum,
                round(sumfromclient - totalsum) AS saldo,
                infostring
               FROM
                       d_order AS d
               LEFT JOIN s_clients AS p ON d.clientid = p.id
               LEFT JOIN s_objects AS o ON d.objectid = o.id
               LEFT JOIN s_employee AS e ON d.employeeid = e.id
               LEFT JOIN s_types_of_payment AS tp ON d.paymentid = tp.id
               LEFT JOIN s_automated_point AS ap ON d.idautomated_point = ap.id
               WHERE
                       d.id =".$orderid;    
$headQuery=  mysql_query($sqlHead);
$headRow=  mysql_fetch_assoc($headQuery);
        
$sql="SELECT	
	IF (ISNULL(t1.name),i.name,CONCAT(i.name,' .br.(',t1.name,')')) as foodname,
	t.price AS price,
	sum(t.quantity) AS quantity,
	sum(t.price * t.quantity) AS summa,
        dvs.name as dvsname,
        dvs.id as dvsid
       FROM
               d_order AS d
       LEFT JOIN t_order AS t ON d.id = t.orderid
       LEFT JOIN s_items AS i ON t.itemid = i.id
       LEFT JOIN s_subdivision AS dvs ON t.printerid = dvs.id
       LEFT JOIN (SELECT GROUP_CONCAT(i.name, '.br.') as name,t3.parentid as id  FROM t_order as t3
                                                       LEFT JOIN s_items as i on t3.itemid=i.id 
                                                       WHERE t3.orderid=".$orderid." and t3.parentid<>0 GROUP BY t3.parentid
                                                       )  as t1 on t1.id=t.id 
       WHERE
               orderid = ".$orderid." and t.parentid=0
       GROUP BY
               i.id,t1.id
       ORDER BY
               dvs.id"; 
//echo json_encode(array('rescode'=>  errcode(83),'resmsg'=>  $sql));die; 
$result=mysql_query($sql);

$i = 1;  

$saldo=$headRow["sumfromclient"]-$headRow["totalsum"];
$headRow["clientname"]=str_replace('%', '%%', $headRow["clientname"]); 

    $doc = "<Header>
            <order>order</order>
            <numcheck>".$headRow["id"]."</numcheck>
            <waiter>".$headRow["employeename"]."</waiter> 
            <client>".$headRow["clientname"]."</client>             
            <typepay>".$headRow["typepay"]."</typepay>
            <table>".($headRow["tablename"]==''?"no":$headRow["tablename"])."</table> 
            <totalsum>".$headRow["totalsum"]."</totalsum>
            <clearsum>".$headRow["clearsum"]."</clearsum>
            <servicepercent>".$headRow["servicepercent"]."</servicepercent>
            <servicesum>".$headRow["servicesum"]."</servicesum>
            <dtopen>".$headRow["dtopen"]."</dtopen> 
            <dtclose>".$headRow["dtclose"]."</dtclose> 
            <restname>".$headRow["restname"]."</restname>
            <barcode>".$headRow["barcode"]."</barcode>
            <sumfromclient>".$headRow["sumfromclient"]."</sumfromclient> 
            <cash>".$saldo."</cash>  
            <discountsum>".$headRow["discountsum"]."</discountsum>
            <infostring>".$headRow["infostring"]."</infostring>
            <discountpercent>".$headRow["discountpercent"]."</discountpercent>";
//    if ($infRow==''){
//        $infRow='Приходит еще!';
//    }
    
    $doc.='<infrow>'.$infRow.'</infrow>';
    
    $logStr="печать счета на оплату №".getShiftIdOrder($orderid).'
          <br>Клиент:'.$headRow["clientname"].'; 
          <br>Процент обслуживания'.$headRow["servicepercent"].'; 
          <br>Процент скидки'.$headRow["discountpercent"].';    
          <br>Итого:'.$headRow["clearsum"].'; 
          <br>Сумма обслуживания:'.$headRow["servicesum"].' 
          <br>Сумма скидки:'.$headRow["discountsum"].' 
          <br>Всего:'.$headRow["totalsum"];
    $logType=8;
    $logStr2='';
    if ($typeCheck=="of"){
      $doc.="<payment>".$headRow["payment"]."</payment>";
      $doc.="<type>on</type>"; 
      $doc.="<cash>".$headRow['saldo']."</cash>"; 
      $logStr="печать счета об оплате №".getShiftIdOrder($orderid).'
          <br>Клиент:'.$headRow["clientname"].'; 
          <br>Процент обслуживания:'.$headRow["servicepercent"].'; 
          <br>Процент скидки:'.$headRow["discountpercent"].';    
          <br>Итого:'.$headRow["clearsum"].'; 
          <br>Сумма обслуживания:'.$headRow["servicesum"].' 
          <br>Сумма скидки:'.$headRow["discountsum"].' 
          <br>Всего:'.$headRow["totalsum"].'
          <br>Сумма отклиента:'.$headRow["sumfromclient"].' 
          <br>Сдача:'.$saldo;
      $logType=12;
    }  
    if ($typeCheck=="return"){
      $doc.="<type>return</type>"; 
      $logStr="печать счета об возврате №".getShiftIdOrder($orderid).'
          <br>Клиент:'.$headRow["clientname"].'; 
          <br>Процент обслуживания'.$headRow["servicepercent"].'; 
          <br>Процент скидки'.$headRow["discountpercent"].';    
          <br>Итого:'.$headRow["clearsum"].'; 
          <br>Сумма обслуживания:'.$headRow["servicesum"].' 
          <br>Сумма скидки:'.$headRow["discountsum"].' 
          <br>Всего:'.$headRow["totalsum"].'
          <br>Сумма отклиента:'.$headRow["sumfromclient"].'
          <br>Сдача:'.$saldo;
      $logType=19;
    } 
    
    if ($result)
            {  
                while($row1=mysql_fetch_assoc($result)) {
                            $doc.="\n<item".$i."> \n";

                            $data[$i]["num"]=$i;
                            $doc.="<num>".$i."</num>\n";

                            $data[$i]["foodname"]=$row1["foodname"];
                            $row1["foodname"]=str_replace('%', '%%', $row1["foodname"]);
                            $doc.="<foodname>".$row1["foodname"]."</foodname>\n";

                            $data[$i]["price"]=$row1["price"];
                            $doc.="<price>".(float)$row1["price"]."</price>\n";

                            $data[$i]["quantity"]=$row1["quantity"]; 
                            $doc.="<quantity>".(float)$row1["quantity"]."</quantity>\n";

                            $data[$i]["summa"]=$row1["summa"];
                            $doc.="<summa>".(float)$row1["summa"]."</summa>\n";

                            $data[$i]["dvsname"]=$row1["dvsname"];
                            $row1["dvsname"]=str_replace('%', '%%', $row1["dvsname"]);
                            $doc.="<dvsname>".$row1["dvsname"]."</dvsname>\n";
                            
                            $data[$i]["dvsid"]=$row1["dvsid"];
                            $doc.="<dvsid>".(float)$row1["dvsid"]."</dvsid>\n";
                            
                            $doc.="</item".$i.">\n";
                        
                            $logStr2.='Товар:'.$row1["foodname"].'; Кол-во:'.(float)$row1["quantity"].'; Цена:'.(float)$row1["price"].'; Сумма:'.(float)$row1["summa"].'<br>';
                            
                            $i++;    
                        };            
            }
            else
            {
                $doc.="\n<item1> \n";
                            $doc.="<num>1</num>\n";                                                        
                            $doc.="<foodname>Item</foodname>\n";                            
                            $doc.="<price>0</price>\n";                            
                            $doc.="<quantity>0</quantity>\n";                                                        
                            $doc.="<summa>0</summa>\n";                                                        
                $doc.="</item1>\n";
            }
    $doc.="</Header>";
    
   
   addlog($logStr.'<br>'.$logStr2,$logType);
    
//    echo json_encode(array('rescode'=>  errcode(83),'resmsg'=> $doc));die;
   return $doc;
}

function setPrintedForItems($orderid){
    
    $setPrinted=mysql_query('UPDATE t_order set printed=1 where orderid='.$orderid);
    
    if ($setPrinted){        
    }else{
       echo json_encode(array('rescode'=>  errcode(83),'resmsg'=>  errmsg(83)));die; 
    }
} 

function calcorderadnupdate($orderid)
{
//    $summa=0;
//    if ($orderid!=0){    
//        $tempsql=  mysql_query('SELECT SUM(quantity*price) as summa FROM t_order where orderid='.addslashes($orderid)); 
//        if ($tempsql){            
//           while ($tmprow= mysql_fetch_array($tempsql)) {
//               $summa=$summa+$tmprow['summa']; 
//          } 
//        }else{
//          echo json_encode(array('rescode'=>  errcode(42),'resmsg'=>  errmsg(42))); die;  
//        }
//    }   
//  
//    $tempsql=  mysql_query('SELECT discountpercent,servicepercent,discountsum FROM d_order where id='.addslashes($orderid)); 
//        if ($tempsql){            
//           $tmprow= mysql_fetch_array($tempsql);           
//        }else{
//          echo json_encode(array('rescode'=>  errcode(66),'resmsg'=>  errmsg(66))); die;  
//        }
//  
//  $discountsum=round($summa*$tmprow['discountpercent']/100);
//  $servicesum=round($summa*$tmprow['servicepercent']/100);
//  $totalsum=round($summa-$discountsum+$servicesum);
  
    $summa=0;
    $servicesum=0;
    $discountsum=0;
    $comediscount=0;
    $comeservice=0;
    $comesum=0;
    $nowdiscount=0;
    $nowservice=0;
    $nowsum=0;
     
    $orderQ=  mysql_query('SELECT servicepercent,discountpercent FROM d_order WHERE id='.addslashes($orderid));
    $order=  mysql_fetch_assoc($orderQ);
    
    $tempsql=  mysql_query('SELECT round(sum(t.price * t.quantity)) as summa FROM t_order as t where parentid=0 and orderid='.addslashes($orderid)); 
    if ($tempsql){     
       while ($tmprow= mysql_fetch_array($tempsql)) {
            $sum2=0;
            $discount=0;
            $service=0;
            
            if  ($_SESSION['typeOfDiscountService']==0){
                $service=(round($tmprow['summa']*($order['servicepercent']))/100);
                $discount= (round($tmprow['summa']*($order['discountpercent']))/100);       
            }else if($_SESSION['typeOfDiscountService']==1){
                  $service=(round($tmprow['summa']*($order['servicepercent']))/100);
                  $sum2=$tmprow['summa']+$service;
                  $discount= (round(sum2*($order['discountpercent']))/100);
              }else if($_SESSION['typeOfDiscountService']==2){
                  $discount= (round($tmprow['summa']*($order['discountpercent']))/100);
                  $sum2=$tmprow['summa']-$discount;
                  $service=(round($sum2*($order['servicepercent']))/100);
              }              
             $nowdiscount+=$discount;
             $nowservice+=$service;
             $nowsum+=$tmprow['summa']+$service-$discount;
      } 
    }else{
      echo json_encode(array('rescode'=>  errcode(42),'resmsg'=>  errmsg(42))); die;  
    } 

    $summa=$nowsum+$comesum;
    $discountsum=$comediscount+$nowdiscount;
    $servicesum=$nowservice+$comeservice;
 
//  echo json_encode(array('rescode'=>  errcode(42),'resmsg'=>  round($discountsum).' '.round($servicesum)
//      .' '.round($summa))); die; 
    
  
  $sql='update d_order set totalsum='.$summa.', servicesum='.$servicesum.', discountsum='.$discountsum.' where id='.addslashes($orderid);
  $tempsql=  mysql_query($sql);
  if ($tempsql){
      return array('sum'=>$summa,'serv'=>$servicesum,'disc'=>$discountsum);
  }
  else{
    echo json_encode(array('rescode'=>  errcode(63),'resmsg'=>  errmsg(63))); die;
  }
  
}

function refuse($orderid,$orderRow,$pwd,$count){
    $tmpsql=  mysql_query('SELECT sum(quantity) as quantity FROM t_order where orderid='.$orderid.' and round(price)=round('.$orderRow['price'].') and itemid='. $orderRow['id']);
    if ($tmpsql){
        $row=  mysql_fetch_assoc($tmpsql);
        if ($count>$row['quantity']){
            echo json_encode(array('rescode'=>  errcode(90),'resmsg'=>  errmsg(90)));die;
        }
    }else{
       echo json_encode(array('rescode'=>  errcode(89),'resmsg'=>  errmsg(89)));die; 
    }
    
    $sqlText1='SELECT 
                d.servicepercent as servicepercent,
                d.discountpercent as discountpercent
              FROM d_order as d              
              WHERE d.id='.addslashes($orderid);

    $sqlRefuseQ=mysql_query($sqlText1);
    $rowQ=  mysql_fetch_assoc($sqlRefuseQ);
   
    $checkPwd='SELECT id from s_automated_point where pwdrefuse="'.addslashes($pwd).'" and id='.$_SESSION['idap'];
    $resultCheckPwd=  mysql_query($checkPwd);
        if ($resultCheckPwd){
                if (mysql_num_rows($resultCheckPwd)>0){
                    
                    $tmpArrSum=getTableSum($rowQ,($count*$orderRow['price']));
                    $sql='Insert Into t_order (orderid,itemid,quantity,printerid,sum,note,discountsum,servicesum,salesum,price) Values ';
                                    $orderRow['printer']=$orderRow['printer']+0;
                                    $sql .= '(' . $orderid . ',' 
                                            . '' . $orderRow['id'] . ','
                                            . '' . $count . ','
                                            . '' . $orderRow['printer'] . ','
                                            . '' . ($count*$orderRow['price']) . ','
                                            . '"' . ($orderRow['note']) . '",'
                                            . '' . ($tmpArrSum['discount']) . ','
                                            . '' . ($tmpArrSum['service']) . ','
                                            . '' . ($tmpArrSum['sum']) . ','
                                            . '' . $orderRow['price'] . ')';
                  
                   $resultRefuse =  mysql_query($sql);
                   $last_id_t=mysql_insert_id();
                   $logStr2='';
                   
                    $sqlComplex=  mysql_query("SELECT count(t.id) as count FROM t_order as t WHERE t.parentid=".addslashes($orderRow['id']));
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
                                          WHERE t.parentid='.addslashes($orderRow['innerId']);
                            $sqlComplexContent=mysql_query($sqlText);
                            
                            if ($sqlComplexContent){
                                
                                 $q =  mysql_query('SELECT name FROM s_items WHERE id='.$orderRow['id']);
                                 $qr =  mysql_fetch_assoc($q);
                                 $itemname=$qr['name'];
                                 
                                $logStr2.='Содержимое комплекса:('.$itemname.')';
                                while($combovalue = mysql_fetch_assoc($sqlComplexContent)){
                                    $tmpArrSum2=getTableSum($rowQ,($count*$combovalue['price']));
                                    $sqlInsertTable = 'Insert Into t_order (orderid,parentid,itemid,quantity,printerid,sum,note,discountsum,servicesum,salesum,price) Values ';
                                    $sqlInsertTable .= '(' . $orderid . ','
                                       . '' . $last_id_t . ',' 
                                       . '' . $combovalue['itemid'] . ','
                                       . '' . $count . ','
                                       . '' . $combovalue['printer'] . ','
                                       . '' . (($count*$combovalue['price'])) . ','
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
                                    
                                    $logStr2 .= '<br> Товар: '.$itemname.', кол-во: '.($count*-1).', цена:'.($combovalue['price']*-1).' ';
                                }
                            }
                        }    
                    }
                   
                   calcorderadnupdate($orderid);
                   
                      $q =  mysql_query('SELECT name FROM s_items WHERE id='.addslashes($orderRow['id']));
                      $qr =  mysql_fetch_assoc($q);
                      $itemname=$qr['name'];
                      
                      $logStr="Отказ в счете №".getShiftIdOrder($orderid)."
                          <br>Отказанный товар<br>
                          Товар:".$itemname."
                          Количество:".$count."
                          Цена:".($orderRow['price']*-1)."
                          Сумма:".($count*$orderRow['price']*-1);
                      addlog($logStr.'<br>'.$logStr2,4);
                      
//                      echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xml'=>SubOrd($action, $orderid)));die;
                      return;
                   }else{
                      echo json_encode(array('rescode'=>  errcode(7),'resmsg'=>  errmsg(7)));die;
                   }

                }else{
                 echo json_encode(array('rescode'=>  errcode(7),'resmsg'=>  errmsg(7)));die;
                }            

}

function checkChage(){
    
    $divChange=divChangeWorkplace();
    
    if ($divChange==0){
        $sqlCheckChange =  ('select id,closed from d_changes where idautomated_point='.$_SESSION['idap'].' order by id desc limit 1');
    }else{
        $sqlCheckChange =  ('select id,closed from d_changes where idautomated_point='.$_SESSION['idap'].' and idworkplace='.$_SESSION['wid'].' order by id desc limit 1');
    }
    
    $resultCheckChange=mysql_query($sqlCheckChange);   
   
        if ($resultCheckChange){             
            if (mysql_num_rows($resultCheckChange)>0){
                $tmparray = mysql_fetch_assoc($resultCheckChange);
                $idchange=$tmparray['id'];
                $closed=$tmparray['closed'];
                if ($closed==0){
//                   echo json_encode(array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'idchange'=>$idchange,'closed'=>$closed));die; 
                   return array('idchange'=>$idchange,'closeChange'=>0);
//                    return 0;
                }else{
//                    echo json_encode(array('rescode'=>  errcode(13),'resmsg'=>  errmsg(13)));die;
                   return array('idchange'=>$idchange,'closeChange'=>1);
                }
             }else{
//                 echo json_encode(array('rescode'=>  errcode(13),'resmsg'=>  errmsg(13))); die;                
                  return array('idchange'=>0,'closeChange'=>1);
             }
        }  else{
              echo json_encode(array('rescode'=>  errcode(14),'resmsg'=>  errmsg(14))); die;
//             return false;
        }
}

function checkEmployee($id){
    $sqlCheckEmployee = mysql_query('Select id from s_employee where id='.addslashes($id));
    if ($sqlCheckEmployee){
        if (mysql_num_rows($sqlCheckEmployee)>0){
            return 1;
        }else{
           echo json_encode(array('rescode'=>  errcode(26),'resmsg'=>  errmsg(26))); die; 
        }
    }else{
       echo json_encode(array('rescode'=>  errcode(25),'resmsg'=>  errmsg(25))); die; 
    }
}

function checkOrder($id){
    $sqlCheckOrder = mysql_query('Select id from d_order where id='.addslashes($id));
    if ($sqlCheckOrder){
        if (mysql_num_rows($sqlCheckOrder)>0){
            return 1;
        }else{
           echo json_encode(array('rescode'=>  errcode(68),'resmsg'=>  errmsg(68))); die; 
        }
    }else{
       echo json_encode(array('rescode'=>  errcode(67),'resmsg'=>  errmsg(67))); die; 
    }
}

function checkPayment($id){
    $sqlCheckPayment = mysql_query('Select id,name from s_types_of_payment where id='.addslashes($id));
    if ($sqlCheckPayment){
        if (mysql_num_rows($sqlCheckPayment)>0){
            return 1;
        }else{
           echo json_encode(array('rescode'=>  errcode(73),'resmsg'=>  errmsg(73))); die; 
        }
    }else{
       echo json_encode(array('rescode'=>  errcode(72),'resmsg'=>  errmsg(72))); die; 
    }
}

function checkClient($id){
    $sqlCheckClient = mysql_query('Select id from s_clients where id='.addslashes($id));
    if ($sqlCheckClient){
        if (mysql_num_rows($sqlCheckClient)>0){
            return 1;
        }else{
           echo json_encode(array('rescode'=>  errcode(75),'resmsg'=>  errmsg(75))); die; 
        }
    }else{
       echo json_encode(array('rescode'=>  errcode(74),'resmsg'=>  errmsg(74))); die; 
    }
}

function checkItem($id){
    $sqlCheckItem = mysql_query('Select id from s_items where id='.addslashes($id));
    if ($sqlCheckItem){
        if (mysql_num_rows($sqlCheckItem)>0){
            return 1;
        }else{
           echo json_encode(array('rescode'=>  errcode(77),'resmsg'=>  errmsg(77))); die; 
        }
    }else{
       echo json_encode(array('rescode'=>  errcode(76),'resmsg'=>  errmsg(76))); die; 
    }
}

function checkInterface($interface){    
    if ($interface==2||$interface==3||$interface==4)
        return 1;
    else{
       echo json_encode(array('rescode'=>  errcode(82),'resmsg'=>  errmsg(82))); die; 
    }
}

function checkSumOrder($order,$ordertable){
    $summa=0;
    $servicesum=0;
    $discountsum=0;
    $comediscount=0;
    $comeservice=0;
    $comesum=0;
    $nowdiscount=0;
    $nowservice=0;
    $nowsum=0;
    if ($ordertable!='empty'){
        foreach ($ordertable as $value){
            $sum2=0;
            $discount=0;
            $service=0;
            if  ($_SESSION['typeOfDiscountService']==0){
                $service=(round($value['summa']*($order['servicepercent']))/100);
                $discount= (round($value['summa']*($order['discountpercent']))/100);       

            }else if($_SESSION['typeOfDiscountService']==1){
                  $service=(round($value['summa']*($order['servicepercent']))/100);
                  $sum2=$value['summa']+$service;
                  $discount= (round(sum2*($order['discountpercent']))/100);
              }else if($_SESSION['typeOfDiscountService']==2){
                  $discount= (round($value['summa']*($order['discountpercent']))/100);
                  $sum2=$value['summa']-$discount;
                  $service=(round($sum2*($order['servicepercent']))/100);
              }
             $comediscount+=$discount;
             $comeservice+=$service;
             $comesum+=$value['summa']+$service-$discount;
        }
    }
            
    if ($order['orderid']!=0){    
        $tempsql=  mysql_query('SELECT round(sum(t.price * t.quantity)) as summa FROM t_order as t where parentid=0 and orderid='.addslashes($order['orderid'])); 
        if ($tempsql){            
           while ($tmprow= mysql_fetch_array($tempsql)) {
                $sum2=0;
                $discount=0;
                $service=0;
                if  ($_SESSION['typeOfDiscountService']==0){
                    $service=(round($tmprow['summa']*($order['servicepercent']))/100);
                    $discount= (round($tmprow['summa']*($order['discountpercent']))/100);       

                }else if($_SESSION['typeOfDiscountService']==1){
                      $service=(round($tmprow['summa']*($order['servicepercent']))/100);
                      $sum2=$tmprow['summa']+$service;
                      $discount= (round(sum2*($order['discountpercent']))/100);
                  }else if($_SESSION['typeOfDiscountService']==2){
                      $discount= (round($tmprow['summa']*($order['discountpercent']))/100);
                      $sum2=$tmprow['summa']-$discount;
                      $service=(round($sum2*($order['servicepercent']))/100);
                  }              
                 $nowdiscount+=$discount;
                 $nowservice+=$service;
                 $nowsum+=$tmprow['summa']+$service-$discount;
          } 
        }else{
          echo json_encode(array('rescode'=>  errcode(42),'resmsg'=>  errmsg(42))); die;  
        } 
    }else{
        $nowsum=0;
        $nowservice=0;
        $nowdiscount=0;
    }    
    $summa=$nowsum+$comesum;
    $discountsum=$comediscount+$nowdiscount;
    $servicesum=$nowservice+$comeservice;
 
//  echo json_encode(array('rescode'=>  errcode(42),'resmsg'=>  round($discountsum).' '.round($order['discountsum']).' '.round($servicesum)
//      .' '.round($order['servicesum']).' '.round($summa).' '.round($order['totalsum']))); die; 
    
  if (round($discountsum)!=round($order['discountsum'])){
     echo json_encode(array('rescode'=>  errcode(60),'resmsg'=>  errmsg(60))); die;  
  }  
  
  if (round($servicesum)!=round($order['servicesum'])){
     echo json_encode(array('rescode'=>  errcode(61),'resmsg'=>  errmsg(61))); die; 
  }  
  if ((round($summa)!=round($order['totalsum'])))  {
     echo json_encode(array('rescode'=>  errcode(62),'resmsg'=>  errmsg(62))); die;   
  }
  return 1;
}

function checkPrinted($orderid){
$tmpOrder= mysql_query('select printed,closed from d_order where id='. $orderid);
            if (!$tmpOrder){
               echo json_encode(array('rescode'=>  errcode(66),'resmsg'=> errmsg(66)));die;
            }                
            if (mysql_num_rows($tmpOrder)>0){
                    $tmparray = mysql_fetch_assoc($tmpOrder);
                    $printed=$tmparray['printed'];
                    $closed=$tmparray['closed'];
                    if ($tmparray['printed']==1){
//                        echo json_encode(array('rescode'=>  errcode(69),'resmsg'=> errmsg(69)));die;
                        return 1;
                    }else{
//                       return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'orderid'=>$orderid); 
                        return 0;
                    };
                    if ($tmparray['closed']==1){
                        echo json_encode(array('rescode'=>  errcode(70),'resmsg'=> errmsg(70)));die;
                    };    
            }
}

function getIfaceParams(){
    $sqlIface=mysql_query('SELECT printsubordinfastfood,printsubord,printorder,printorderpay,with_gifts,blockZeroSale FROM s_automated_point where id='.$_SESSION['idap']);
    if ($sqlIface){
        $tmparray = mysql_fetch_assoc($sqlIface);        
        return array('rescode'=>  errcode(0),'resmsg'=> errmsg(0),'printsubordinfastfood'=>$tmparray['printsubordinfastfood'],
            'printsubord'=>$tmparray['printsubord'],'printorder'=>$tmparray['printorder'],'printorderpay'=>$tmparray['printorderpay'],'with_gifts'=>$tmparray['with_gifts'],'blockZeroSale'=>$tmparray['blockZeroSale']);       
    }else{
       echo json_encode(array('rescode'=>  errcode(81),'resmsg'=> errmsg(81)));die; 
    }
    
}

function ean13_check_digit($digits){
        //first change digits to a string so that we can access individual numbers
        $digits =(string)$digits;
        // 1. Add the values of the digits in the even-numbered positions: 2, 4, 6, etc.
        $even_sum = $digits{1} + $digits{3} + $digits{5} + $digits{7} + $digits{9} + $digits{11};
        // 2. Multiply this result by 3.
        $even_sum_three = $even_sum * 3;
        // 3. Add the values of the digits in the odd-numbered positions: 1, 3, 5, etc.
        $odd_sum = $digits{0} + $digits{2} + $digits{4} + $digits{6} + $digits{8} + $digits{10};
        // 4. Sum the results of steps 2 and 3.
        $total_sum = $even_sum_three + $odd_sum;
        // 5. The check character is the smallest number which, when added to the result in step 4,  produces a multiple of 10.
        $next_ten = (ceil($total_sum/10))*10;
        $check_digit = $next_ten - $total_sum;
        return $digits . $check_digit;
}

function getTableSum($orderdata,$sum){
    $sum2=0;
    $discount=0;
    $service=0;
    if  ($_SESSION['typeOfDiscountService']==0){
        $service=(round($sum*($orderdata['servicepercent']))/100);
        $discount= (round($sum*($orderdata['discountpercent']))/100);       

    }else if($_SESSION['typeOfDiscountService']==1){
          $service=(round($sum*($orderdata['servicepercent']))/100);
          $sum2=$sum+$service;
          $discount= (round(sum2*($orderdata['discountpercent']))/100);
      }else if($_SESSION['typeOfDiscountService']==2){
          $discount= (round($sum*($orderdata['discountpercent']))/100);
          $sum2=$sum-$discount;
          $service=(round($sum2*($orderdata['servicepercent']))/100);
      }
     $comediscount=$discount;
     $comeservice=$service;
     $comesum=$sum+$comeservice-$comediscount;  
     return array('discount'=>$comediscount,'service'=>$comeservice,'sum'=>$comesum);
}


function getOrderInf($id){
    $query=  mysql_query('SELECT cl.name as name FROM d_order as d
                          LEFT JOIN s_clients as cl on cl.id=d.clientid
                          WHERE d.id='.$id.'
                          ');
    $r =  mysql_fetch_assoc($query);
    return $r['name'];
}

function doSaveOrder($order,$ordertable,$idchange,$interface){  

        if ($order['orderid'] == 0) {
            $tmp=mysql_query("select if (max(idout+0) IS NULL,1,max(idout+0)+1) as shiftId from d_order where changeid=".$idchange);
            if (!$tmp){
              echo json_encode(array('rescode'=>  errcode(65),'resmsg'=>  errmsg(65))); die;  
            }
        
            $wpid=0;
            if (isset($_SESSION['wid'])) $wpid=$_SESSION['wid'];
            
            $yes=false;
            $barcode='';
            while (!$yes){
                $barcode=rand(1000,9999).rand(1000,9999).rand(1000,9999);
                $barcode=ean13_check_digit($barcode);
                $checkBarCodeQuery=  mysql_query('SELECT id FROM d_order WHERE barcode='.$barcode);
                if (mysql_num_rows($checkBarCodeQuery)==0){
                    $yes=true;
                }
            }        
            $idout = mysql_fetch_assoc($tmp);
            $sql = 'Insert Into d_order (idout,changeid,barcode,employeeid,objectid,discountpercent,discountsum,printed,closed,
            clientid,totalsum,guestcount,idautomated_point,servicepercent,servicesum,sumfromclient,paymentid,wpid,interfaceid,discountid) Values(';
            $sql .= ''.  addslashes($idout['shiftId']).',';
            $sql .= '' . addslashes( $idchange) . ',';
            $sql .= '' .$barcode. ',';
            
            if ($interface==6){
                if ($order['employeeid']==0){
                 $sql .= '0,';
                }else{
                 $sql .= ''. addslashes($order['employeeid']).',';  
                }
                
            }else{
                $sql .= '' . addslashes( $_SESSION['employeeid']) . ',';
            }
            $sql .= '' . addslashes($order['tableid']) . ',';
            $sql .= '' . addslashes($order['discountpercent']) . ',';
            $sql .= '' . addslashes($order['discountsum']) . ',';
            $sql .= '0,';//printed
            $sql .= '0,';//closed
            $sql .= '' . addslashes($order['clientid']). ', ';
            $sql .= '' . addslashes($order['totalsum']). ',';
            $sql .= '"' .addslashes($order['guestscount']). '",';
            $sql .= '' .$_SESSION['idap']. ','; 
            $sql .= '"' .addslashes($order['servicepercent']). '",';
            $sql .= '"' .addslashes($order['servicesum']). '",';
            $sql .= '0,0,'.$wpid.','.$interface.','.addslashes($order['discountid']).')';
            
            
            $resultInsertHeader = mysql_query($sql);
            
            $logStr2='';
            if ($resultInsertHeader) {
                
                $last_id = mysql_insert_id();
                
                foreach ($ordertable as $value)  {
                    $sqlInsertTable = 'Insert Into t_order (orderid,parentid,itemid,quantity,printerid,sum,note,discountsum,servicesum,salesum,price) Values ';
                    $value['printer']=$value['printer']+0;
                    $tmparr=getTableSum($order, $value['summa']);
                    $sqlInsertTable .= '(' . $last_id . ','
                            . '0,'
                            . '' . addslashes($value['id']) . ','
                            . '' . addslashes($value['count']) . ','
                            . '' . addslashes($value['printer']) . ','
                            . '' . (addslashes($value['summa'])) . ','
                            . '"' . addslashes($value['note']) . '",'
                            . '' . addslashes($tmparr['discount']) . ','
                            . '' . addslashes($tmparr['service']) . ','
                            . '' . addslashes($tmparr['sum']) . ','
                            . '' . addslashes($value['price']) . '),'; 
                    
                    $sql = substr($sqlInsertTable, 0, strlen($sqlInsertTable) - 1);
                    $result = mysql_query($sql);
                    $last_id_t=mysql_insert_id();
                    $itemname=addslashes($value['name']);
                    if (isset($value['complex'])){
                        if ($value['complex']==1){
                            $q =  mysql_query('SELECT name FROM s_items WHERE id='.addslashes($value['id']));
                            $qr =  mysql_fetch_assoc($q);
                            $itemname=$qr['name'];
                        }
                    }
                    
                    $logStr2 .= 'Товар:('.$itemname.'), кол-во: '.addslashes($value['count']).', цена:'.addslashes($value['price']).' <br>';
                    
                     if (isset($value['complex'])){
                      if ($value['complex']==1){
                          $tmpar=$value['comboItems'];
                          $logStr2.='Содержимое комплекса:('.$itemname.')';
                          foreach ($tmpar as $combovalue)  {
                              $sqlInsertTable = 'Insert Into t_order (orderid,parentid,itemid,quantity,printerid,sum,note,discountsum,servicesum,salesum,price) Values ';
                                $tmparr=getTableSum($order, (addslashes($combovalue['count'])*addslashes($combovalue['price'])));
                                $sqlInsertTable .= '(' . $last_id . ','
                                 . '' . $last_id_t . ',' 
                                 . '' . addslashes($combovalue['itemid']) . ','
                                 . '' . addslashes($combovalue['count']) . ','
                                 . '' . addslashes($combovalue['printer']) . ','
                                 . '' . (addslashes($combovalue['count'])*addslashes($combovalue['price'])) . ','
                                 . '"' .addslashes($combovalue['note']) . '",'    
                                 . '' . addslashes($tmparr['discount']) . ','
                                 . '' . addslashes($tmparr['service']) . ','
                                 . '' . addslashes($tmparr['sum']) . ','
                                 . '' . addslashes($combovalue['price']) . '),';
                              $sql = substr($sqlInsertTable, 0, strlen($sqlInsertTable) - 1);
                              $result = mysql_query($sql);
                              $logStr2 .= '<br> Товар: '.addslashes($combovalue['name']).', кол-во: '.addslashes($combovalue['count']).', цена:'.addslashes($combovalue['price']).' ';
                          }
                      }    
                    }
                }               
                if ($result) {
                   
                    $logStr="Создание счета №".$idout['shiftId'].";
                        <br> Клиент:".getOrderInf($last_id).";
                        <br>  Процент скидки:".$order['discountpercent'].";
                        <br>  Процент обслуживания:".$order['servicepercent']."
                        <br>  Сумма скидки:".$order['discountsum']."
                        <br>  Сумма обслуживания:".$order['servicesum']."
                        <br>  Итоговая сумма:".addslashes($order['totalsum']);
                    
                    
                    addlog($logStr.' <br> '.$logStr2,1);
                    $tmparray=getIfaceParams();
                        if ($interface==3){ 
                            
                            if ($tmparray['printsubord']==1){
                                return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlSub'=>  SubOrd('suborder', $last_id),'orderid'=>$last_id);
                            }else if ($tmparray['printsubord']==0){
                               
                                setPrintedForItems($last_id);
                                
                                return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlSub'=>  '','orderid'=>$last_id);
                            }
                        }
                        if ($interface==4){
                            if ($tmparray['printsubordinfastfood']==1){
                                return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlSub'=>  SubOrd('suborder', $last_id),'orderid'=>$last_id);
                            }else if ($tmparray['printsubordinfastfood']==0){
                                setPrintedForItems($last_id);
                                return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlSub'=>  '','orderid'=>$last_id);
                            }
                        }
                        if ($interface==6){
                            if ($tmparray['printsubordinfastfood']==1){
                                return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlSub'=>  SubOrd('suborder', $last_id),'orderid'=>$last_id);
                            }else if ($tmparray['printsubordinfastfood']==0){
                                setPrintedForItems($last_id);
                                return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlSub'=>  '','orderid'=>$last_id);
                            }
                        }
//                        return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlSub'=>  SubOrd('suborder', $last_id),'orderid'=>$last_id);                       
                 }else{
                            echo json_encode(array('rescode'=>  errcode(64),'resmsg'=> errmsg(64)));die;
                 }
            }else{
                echo json_encode(array('rescode'=>  errcode(63),'resmsg'=>  errmsg(63)));die;
            }
        }else {          
                $sql = 'Update d_order Set ';
                $sql .= 'objectid=' . addslashes($order['tableid']) . ', ';
                $sql .= 'discountpercent=' . addslashes($order['discountpercent']) . ', ';
                $sql .= 'discountsum=' .addslashes($order['discountsum']) . ', ';
                $sql .= 'clientid=' . addslashes($order['clientid']) . ', ';
                $sql .= 'guestcount="' . addslashes($order['guestscount']) . '", ';
                $sql .= 'totalsum=' . addslashes($order['totalsum']) . ', ';
                $sql .= 'servicepercent=' . addslashes($order['servicepercent']) . ', ';
                $sql .= 'servicesum=' . addslashes($order['servicesum']) . ', ';
                $sql .= 'discountid='.addslashes($order['discountid']);
                $sql .= ' Where id=' . addslashes($order['orderid']);  
                
                $resultInsertHeader = mysql_query($sql);
                
                $ch = false;
                
                $logStr2='';
                
                if ($resultInsertHeader) {
                    
                    reSumAllTable(addslashes($order['orderid']));//Для перерасчета всей табличной части если изменили обслуживание или скидку
                    
                    
                    if ($ordertable!='empty'){
                    
                    
                    foreach ($ordertable as $value) {
                        $sqlInsertTable = 'Insert Into t_order (orderid,parentid,itemid,quantity,printerid,sum,note,discountsum,servicesum,salesum,price) Values ';
                        $value['printer']=$value['printer']+0;
                        $tmparr=getTableSum($order, $value['summa']);
                        if ($value['status'] == 'new') {
                            $sqlInsertTable .= '(' . addslashes($order['orderid']) . ','
                                    . '0,'
                                    . '' . addslashes($value['id']) . ','
                                    . '' . addslashes($value['count']) . ','
                                    . '' . addslashes($value['printer']) . ','
                                    . '' . (addslashes($value['summa']))  . ','
                                    . '"' . addslashes($value['note']) . '",'
                                    . '' . addslashes($tmparr['discount']) . ','
                                    . '' . addslashes($tmparr['service']) . ','
                                    . '' . addslashes($tmparr['sum']) . ','
                                    . '' . addslashes($value['price']) . '),';
                        $sql = substr($sqlInsertTable, 0, strlen($sqlInsertTable) - 1);                    
                        $result = mysql_query($sql);
                        
                        $last_id_t=mysql_insert_id();
                        
                       $itemname=addslashes($value['name']);
                       if (isset($value['complex'])){
                            if ($value['complex']==1){
                                $q =  mysql_query('SELECT name FROM s_items WHERE id='.addslashes($value['id']));
                                $qr =  mysql_fetch_assoc($q);
                                $itemname=$qr['name'];
                            }
                       }
                     
                        $logStr2 .= 'Товар:('.$itemname.'), кол-во: '.addslashes($value['count']).', цена:'.addslashes($value['price']).' <br>';
                        
                          if (isset($value['complex'])){
                           if ($value['complex']==1){
                               $tmpar=$value['comboItems'];
                                $logStr2.='Содержимое комплекса:('.$itemname.')';
                               foreach ($tmpar as $combovalue)  {
                                   $sqlInsertTable = 'Insert Into t_order (orderid,parentid,itemid,quantity,printerid,sum,note,discountsum,servicesum,salesum,price) Values ';
                                     $tmparr=getTableSum($order, (addslashes($combovalue['count'])*addslashes($combovalue['price'])));
                                     $sqlInsertTable .= '(' .  addslashes($order['orderid']) . ','
                                      . '' . $last_id_t . ',' 
                                      . '' . addslashes($combovalue['itemid']) . ','
                                      . '' . addslashes($combovalue['count']) . ','
                                      . '' . addslashes($combovalue['printer']) . ','
                                      . '' . (addslashes($combovalue['count'])*addslashes($combovalue['price']) ) . ','
                                      . '"' .addslashes($combovalue['note']) . '",'     
                                      . '' . addslashes($tmparr['discount']) . ','
                                      . '' . addslashes($tmparr['service']) . ','
                                      . '' . addslashes($tmparr['sum']) . ','
                                      . '' . addslashes($combovalue['price']) . '),';
 
                                   $sql = substr($sqlInsertTable, 0, strlen($sqlInsertTable) - 1);
                                   $result = mysql_query($sql); 
                                   
                                   $q =  mysql_query('SELECT name FROM s_items WHERE id='.addslashes($combovalue['itemid']));
                                   $qr =  mysql_fetch_assoc($q);
                                   $itemname=$qr['name'];
                                   
                                   $logStr2 .= '<br> Товар:'. addslashes($combovalue['name']) .', кол-во: '.addslashes($combovalue['count']).', цена:'.addslashes($combovalue['price']).'; ';
                               } 
                           }    
                         }
                        }
                    }  
                    
                    
                    if ($result) {
                        $q =  mysql_query('SELECT name FROM s_clients WHERE id='.addslashes($order['clientid']));
                        $qr =  mysql_fetch_assoc($q);
                        $clientname=$qr['name'];
                        
                        $logStr="Изменение счета №".  getShiftIdOrder($order['orderid']).";
                        <br> Клиент:".$clientname.";
                        <br>  Процент скидки:".$order['discountpercent'].";
                        <br>  Процент обслуживания:".$order['servicepercent']."
                        <br>  Сумма скидки:".$order['discountsum']."
                        <br>  Сумма обслуживания:".$order['servicesum']."
                        <br>  Итоговая сумма:".addslashes($order['totalsum']);
                        addlog($logStr.'<br>Добавленный товар<br>'.$logStr2,2);
                        $tmparray=getIfaceParams();
                        
                        if ($interface==3){
                            if ($tmparray['printsubord']==1){
                                return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlSub'=>  SubOrd('suborder', $order['orderid']),'orderid'=>$order['orderid']);
                            }else if ($tmparray['printsubord']==0){
                                setPrintedForItems($order['orderid']);
                                return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlSub'=>  '','orderid'=>$order['orderid']);
                            }
                        }
//                        if ($interface==4){
//                            if ($tmparray['printsubordinfastfood']==1){
//                                return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlSub'=>  SubOrd('suborder', $order['orderid']),'orderid'=>$order['orderid']);
//                            }else if ($tmparray['printsubordinfastfood']==0){
//                                setPrintedForItems($order['orderid']);
//                                return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlSub'=>  '','orderid'=>$order['orderid']);
//                            }
//                        }                                               
                    }else{
                            echo json_encode(array('rescode'=>  errcode(71),'resmsg'=> errmsg(71)));die;
                     }
                   }else{
                                             
                       $logStr="Изменение счета №".  getShiftIdOrder($order['orderid']).";
                        <br> Клиент:".  getOrderInf(addslashes($order['orderid'])).";
                        <br>  Процент скидки:".$order['discountpercent'].";
                        <br>  Процент обслуживания:".$order['servicepercent']."
                        <br>  Сумма скидки:".$order['discountsum']."
                        <br>  Сумма обслуживания:".$order['servicesum'];
                        addlog($logStr,2);
                       return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlSub'=>'','orderid'=>$order['orderid'] );
                   }
                }else{
                   echo json_encode(array('rescode'=>  errcode(63),'resmsg'=>  errmsg(63)));die; 
                }
            }
}
           

function doPrintOrder($orderid){     
            $sql = 'Update d_order Set ';              
            $sql .= 'printed=1 ';               
            $sql .= ' Where id=' . addslashes($orderid); 
            $resultInsertHeader = mysql_query($sql);
            if ($resultInsertHeader) {                    
               $tmparray=getIfaceParams(); 
               if ($tmparray['printorder']==1){
                             $logStr="Закрытие счета №".getShiftIdOrder($orderid);
                             addlog($logStr,8);
                             setLog($orderid,'');
                            return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlOrd'=>  printOrd($orderid,'no'),'orderid'=>$orderid);
               }else if ($tmparray['printorder']==0){
                            return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlOrd'=>  '','orderid'=>$orderid);
               } 
            }else{
               echo json_encode(array('rescode'=>  errcode(78),'resmsg'=>  errmsg(78)));die; 
            }
}


function giftList($clientid){
    
    $balansQuery = mysql_query("SELECT SUM(points) as points FROM d_balance WHERE clientid=".$clientid." GROUP BY clientid");
                  if ($balansQuery){
                      $balansRow = mysql_fetch_assoc($balansQuery);                
                  }else{            
                      return 0;
                  }  
                  $mysqlSelectDis=  mysql_query('SELECT
                                                        discountid
                                                FROM
                                                        t_discount_ap AS da
                                                LEFT JOIN s_discount as dis on dis.id=da.discountid
                                                LEFT join s_giftlevels as gl on gl.leveldiscountid=dis.id
                                                WHERE
                                                        da.apid = '.$_SESSION['idap'].' and NOT ISNULL(gl.id)');
                  $disRow=  mysql_fetch_assoc($mysqlSelectDis);
                    $selectPoint = mysql_query('SELECT MAX(gl.id) as id, (SELECT gl1.name FROM s_giftlevels as gl1 where gl1.id=MAX(gl.id)) as levelname 
                        FROM s_giftlevels as gl where gl.leveltype<>1 and gl.pointscount<='.$balansRow['points'].' and gl.leveldiscountid='.$disRow['discountid']);
  //                  $selectPoint = mysql_query('SELECT id, name as levelname FROM s_giftlevels ');
                    if ($selectPoint){                      
                        if (mysql_num_rows($selectPoint)>0){
  //                        $selPoint=  mysql_fetch_assoc($selectPoint);  
                          $i=0;    
                          $rows1 = array();
                          while($r2 = mysql_fetch_assoc($selectPoint)) {
                              $rows1[] = $r2;
                              if ($rows1[$i]['id']==NULL){
                                  return 0;
                              }
                          } 
                        }else{
                            return 0;                          
                        }
                    }else{
                          return 0;
                    }           
                    $_SESSION['giftType']=0;
                    return array('balans'=>$balansRow['points'],'giftlevel'=>$rows1);
                   
}

function giftListItems($levelid){
    $giftItemsSelect = mysql_query("SELECT i.id,i.name,i.price,g.quantity,IF(gl.leveltype=1,1,gl.pointscount) as pointscount FROM s_gifts as g
                                    LEFT JOIN s_items as i on i.id=g.itemid
                                    LEFT JOIN s_giftlevels as gl on gl.id=g.levelid
                                    WHERE levelid=".$levelid);
                $rows = array();
                while($r = mysql_fetch_assoc($giftItemsSelect)) {
                   $rows[] = $r;
                }                         
      return array('giftItems'=>$rows);                  
}

function gifts($orderid){
   
    $orderQuery =  mysql_query('SELECT employeeid,totalsum,clientid,discountid FROM d_order where id='.$orderid);    
    if ($orderQuery){
          $supQuery =  mysql_query('SELECT cashclientid FROM s_automated_point WHERE id='.$_SESSION['idap']);
        
            if ($supQuery){
            }else{
                return 0;
            }
        $supRow = mysql_fetch_assoc($supQuery);
        $ordRow =  mysql_fetch_assoc($orderQuery);
        if ($ordRow['totalsum']<=0){
            return 0;
        }
        if ($ordRow['clientid']!=$supRow['cashclientid']){
            $balansQuery = mysql_query("SELECT sum(points) as points FROM d_balance WHERE clientid=".$ordRow['clientid'].' GROUP BY clientid');
            if (mysql_num_rows($balansQuery)>0){
               if ($balansQuery){
                $balansRow = mysql_fetch_assoc($balansQuery);                
                }else{            
                    return 0;
               } 
            }else{
               $balansRow=array('points'=>0); 
            }
            $selectMinStep = mysql_query('SELECT price as price,pointscount as pcount,ballcount as ballcount  
                FROM s_giftlevels where pointscount>'.$balansRow['points'].' and leveltype=0 and leveldiscountid='.$ordRow['discountid'].' ORDER BY levelnum');
                
            if ($selectMinStep){               
                if (mysql_num_rows($selectMinStep)==0){
                    $selectMinStep = mysql_query('SELECT price,pointscount as pcount,ballcount as ballcount 
                    FROM s_giftlevels
                        WHERE leveldiscountid='.$ordRow['discountid'].' order by levelnum desc
                    limit 1');
                    if (!$selectMinStep){
                        return 0;
                    }
                } 
            }else{
                return 0;
            } 
            $sum=$ordRow['totalsum'];
            if (mysql_num_rows($balansQuery)>0){
                $userBalans=$balansRow['points'];
            }else{
                $userBalans=0;
            }  
            $step=0;
            $str='';
            $lastprice=0;
            $supStep=0;
            while ($minStep = mysql_fetch_assoc($selectMinStep)){                
                $str.='=== Колво баллов на уровне:'.$minStep['pcount'].' Баланс:'.$userBalans;
                $str.=' Последння цена:'.$lastprice.' Текущая цена:'.$minStep['price'].' summa:'.$sum;
                if (($lastprice>$minStep['price'])||($minStep['price']==0)){
                    return 0;
                }
                $supStep=0;
                while (($userBalans<$minStep['pcount'])){//&&($sum>$minStep['price'])){
                  $sum=$sum-$minStep['price'];
                  if ($sum<0){
                      break;
                  }
                  //$step++;
                  $supStep++;
                  $userBalans++;
                  $str.='--- price:'.$minStep['price']. ' step:'.$step.' UserBalans:'.$userBalans.' summa:'.$sum;
                }
                $step=$step+$supStep*$minStep['ballcount'];
                $str.='___step:'.$step.' supstem='.$supStep.' ballcount:'.$minStep['ballcount'];
                $lastprice=$minStep['price'];
                $lastballcount=$minStep['ballcount'];
                if ($sum<0){
                      break;
                  }
            }
            $str.='sum='.$sum.',lastprice='.$lastprice.',step='.$step.',lastballcount='.$lastballcount;
           //echo json_encode(array('rescode'=>  errcode(43),'resmsg'=>  $str)); die;   
            $t=0;
            if ($sum>0){
                $t=intval($sum/$lastprice);
                $step=$step+$t*$lastballcount;
            }            
                if ($step>0){ 
                    $sqlInsPoints =  mysql_query('INSERT INTO d_balance SET
                                                   employeeid='.$ordRow['employeeid'].',
                                                   clientid='.$ordRow['clientid'].',
                                                   orderid='.$orderid.',
                                                   points='.$step);
                    if ($sqlInsPoints){
                    }
                    else{
                       return 0;
                    }
                }else{
                }                               
                    return array('rows'=>0,'balans'=>($balansRow['points']+$step),'addedpoits'=>$step);
        }else{            
            return 0;
        }
    }else{ 
        return 0;
    }        
}

function giftCount($orderid){
           $_SESSION['giftType']=1;
           $query=  mysql_query("SELECT g.id,i.name as levelname,t.quantity,g.itemcount as pointscount,g.id as levelid,quantity DIV itemcount as totalpoints FROM d_order as o
                                            LEFT JOIN t_order as t on t.orderid=o.id
                                            LEFT JOIN s_items as i on i.id=t.itemid
                                            LEFT JOIN s_giftlevels as g on g.itemid=i.id
                                            where o.id=".$orderid." and NOT ISNULL(itemcount) and g.itemcount<=t.quantity and leveltype=1");
                    if ($query){
                        $rows1 = array();
                        $i=0;
                            while($r2 = mysql_fetch_assoc($query)) {
                                $rows1[] = $r2;
                                if ($rows1[$i]['totalpoints']==NULL){
                                    return 0;
                                }
                                $i++;
                                                                
                            } 
       
                        return array('rows'=>$rows1,'balans'=>-1,'addedpoits'=>0); 
                    }else{
                    }
}

function doPayOrder($orderid,$typePayment,$sumfromclient){
              
                if (!isset($typePayment)){
                   echo json_encode(array('rescode'=>  errcode(43),'resmsg'=>  errmsg(43))); die;           
                }else{
                    checkPayment($typePayment);
                };
               
                $tmparray=getIfaceParams();
                $sql = 'Update d_order Set ';
                $sql .= 'dtclose=NOW(), ';  
                $sql .=' paymentid=' .$typePayment. ', ';
                $sql .=' sumfromclient=' .$sumfromclient. ', ';
                $sql .= 'closed=1 ';               
                $sql .= ' Where id=' . addslashes($orderid);  
                $resultInsertHeader = mysql_query($sql);
               
                $orderInfQuery =  mysql_query('SELECT
                                                        o.interfaceid as interface,
                                                        if (ISNULL(d.percentvalue),0,d.percentvalue) AS discount,
                                                        if (ISNULL(d.usediscountsincafe),0,d.usediscountsincafe) as usediscountsincafe,
                                                        if (ISNULL(d.usediscountsinfastfood),0,d.usediscountsinfastfood) as usediscountsinfastfood,
                                                        if (ISNULL(d.usegiftsincafe),0,d.usegiftsincafe) as usegiftsincafe,
                                                        if (ISNULL(d.usegiftsinfastfood),0,d.usegiftsinfastfood) as usegiftsinfastfood,
                                                        if (ISNULL(d.useserviceincafe),0,d.useserviceincafe) as useserviceincafe,
                                                        if (ISNULL(d.useserviceinfastfood),0,d.useserviceinfastfood) as useserviceinfastfood,
                                                        if (ISNULL(d.usebalanceincafe),0,d.usebalanceincafe) as usebalanceincafe,
                                                        if (ISNULL(d.usebalanceinfastfood),0,d.usebalanceinfastfood) as usebalanceinfastfood,
                                                        o.discountid as discountid,
                                                        o.clientid as clientid
                                                FROM
                                                        d_order AS o
                                                LEFT JOIN s_discount AS d ON d.id = o.discountid
                                                WHERE
                                                        o.id = '.$orderid);
                if ($orderInfQuery){
                    $rowInfQuery=  mysql_fetch_assoc($orderInfQuery);
                    if ($rowInfQuery['discountid']==0){
                        $orderInfQuery =  mysql_query('SELECT '.$rowInfQuery['interface'].' as interface,if (ISNULL(d.usebalanceinfastfood),0,d.usebalanceinfastfood) as usebalanceinfastfood,if (ISNULL(d.usegiftsinfastfood),0,d.usegiftsinfastfood) as usegiftsinfastfood FROM s_clients AS c LEFT JOIN s_discount as d on d.idpartner=c.id  WHERE c.id='.$rowInfQuery['clientid'].' LIMIT 1');
                        if ($orderInfQuery){
                            $rowInfQuery=  mysql_fetch_assoc($orderInfQuery); 
                        }
                    }
                }else{
                    echo json_encode(array('rescode'=>  errcode(109),'resmsg'=>  errmsg(109)));die;
                }
                
                if ($resultInsertHeader) {
                    $logStr="Оплата счета №".getShiftIdOrder($orderid);
                     addlog($logStr,7);
                     setLog($orderid,'of');
                            if ($rowInfQuery['interface']==4){
                                      if ($tmparray['with_gifts']==1){
                                        if ($rowInfQuery['usegiftsinfastfood']==1){                                            
                                            
                                           if ($rowInfQuery['usebalanceinfastfood']==1){                                            
                                               
                                                $giftArray=gifts($orderid);
                                                
                                                if ($giftArray==0)  {
                                                    $giftArray=array('rows'=>0,'balans'=>0);
                                                    $infRow='';
                                                }else{
                                                     $infRow='Начисленно балов: '.$giftArray['addedpoits'].' ('.$giftArray['balans'].')';     
                                                     $q =  mysql_query('SELECT cl.name as name FROM d_order as d
                                                                        LEFT JOIN s_clients as cl ON cl.id=d.clientid
                                                                        WHERE d.id='.addslashes($orderid));
                                                     $qr =  mysql_fetch_assoc($q);
                                                     $clientName=$qr['name'];
                                                     $logStr="Счет №".getShiftIdOrder($orderid)."; Начисленно баллов:".$giftArray['addedpoits']."; Клиент:".$clientName;
                                                     addlog($logStr,11);
                                                }
                                           }else{
                                              $giftArray=array('rows'=>0,'balans'=>0);
                                              $infRow=''; 
                                           }   
                                           $giftArray=giftCount($orderid);
    //                                           $infRow=''; 
                                           if ($giftArray==0)  {
                                               $giftArray=array('rows'=>0,'balans'=>0);
    //                                                    $infRow='';
                                           };                                                                                           
                                        }else{
                                             $giftArray=array('rows'=>0,'balans'=>0);
                                             $infRow='';
                                        }  
                                      }else{
                                          $giftArray=array('rows'=>0,'balans'=>0);
                                          $infRow='';
                                      };                                      
                                  }else{
                                    $giftArray=array('rows'=>0,'balans'=>0); 
                                    $infRow='';  
                                  }
                   
                   if ($tmparray['printorderpay']==1){
                                return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'gifts'=>$giftArray['rows'],'xmlOrd'=>  printOrd($orderid,'of',$infRow),'orderid'=>$orderid,'balans'=>$giftArray['balans']);
                   }else if ($tmparray['printorderpay']==0){
                                return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'gifts'=>$giftArray['rows'],'xmlOrd'=>  '','orderid'=>$orderid,'balans'=>$giftArray['balans']);
                   }
//                   return array('rescode'=>  errcode(0),'resmsg'=>  errmsg(0),'xmlOrd'=>  printOrd($orderid,'of'),'orderid'=>$orderid); 
                }else{
                   echo json_encode(array('rescode'=>  errcode(84),'resmsg'=>  errmsg(84)));die; 
                }
}

function divChangeWorkplace(){
     $selectOption =  mysql_query('SELECT divChangeWorkplace FROM s_automated_point WHERE id='.$_SESSION['idap']);
     $rowOption=  mysql_fetch_assoc($selectOption);
     return $rowOption['divChangeWorkplace'];
}


function changeInformation(){
    $tmparray=checkChage();
    $idchange=$tmparray['idchange'];
    if ($idchange==0){
        $result=mysql_query('SELECT name FROM s_automated_point WHERE id='.$_SESSION['idap']);
        $row=  mysql_fetch_assoc($result);
        $infoChange=array('name'=>'','dtopen'=>'--:--:--','dtclosed'=>'--:--:--','apname'=>$row['name']);
        return (array('infoChange'=>$infoChange));
    }else{
    $result=mysql_query('SELECT e.name as name,ch.dtopen,if(ch.dtclosed IS NULL,"",ch.dtclosed) as dtclosed,ap.name as apname FROM d_changes as ch
                         LEFT JOIN s_employee as e on e.id=ch.employeeid
                         LEFT JOIN s_automated_point as ap on ap.id=ch.idautomated_point
                         WHERE ch.id='.$idchange);
    if ($result){
        $infoChange=mysql_fetch_assoc($result);      
        return (array('infoChange'=>$infoChange));
    }else{
      echo json_encode(array('rescode'=>  errcode(97),'resmsg'=>  errmsg(97)));die;   
    }
   } 
}

function checkEmployeeSession(){
    $checkLastSession =  mysql_query('SELECT isonline,TIMESTAMPDIFF(MINUTE,FROM_UNIXTIME(last_action),now()) as time FROM s_employee WHERE id='.$_SESSION['employeeid']);
    if ($checkLastSession){
         if (mysql_num_rows($checkLastSession)>0){
                     $tmpSessionArray =  mysql_fetch_assoc($checkLastSession);
//                     echo json_encode(array('rescode'=>  errcode(117),'resmsg'=>  $tmpSessionArray['time'])); die;
                     if ($tmpSessionArray['isonline']==0||$tmpSessionArray['time']>1){
                         return 0;
                     }else if ($tmpSessionArray['isonline']==1){
                         return 1;
                     };
          }else{
               echo json_encode(array('rescode'=>  errcode(117),'resmsg'=>  errmsg(117))); die;
          };
    }else{                     
      echo json_encode(array('rescode'=>  errcode(112),'resmsg'=>  errmsg(112))); die;
    }
}

function updateSessionEmp($flag){

    $updateSession =  mysql_query('UPDATE s_employee SET 
                                         isonline='.$flag.',
                                         last_action=UNIX_TIMESTAMP(now()) 
                                        WHERE id='.$_SESSION['employeeid']);
                                    if (!$updateSession){                                        
                                      echo json_encode(array('rescode'=>  errcode(113),'resmsg'=> errmsg(113))); die;  
                                    }
}

function addlog($descr='Пришло пустое значение описания!',$index=-1){
    try {
        $insertLogQuery =  mysql_query('INSERT INTO z_logs (z_logs.desc,userid,type) VALUES ("'.$descr.'",'.$_SESSION['employeeid'].','.$index.')');
    } catch (Exception $e) {
       return;
    }
    
}

function getShiftIdOrder($orderid){
            $getShiftId =  mysql_query('SELECT idout FROM d_order WHERE id='.$orderid);
            $rowShiftId =  mysql_fetch_assoc($getShiftId);   
            
            return $rowShiftId['idout'];
}

function showOrderTemp($orderid){
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
                    d.employeeid as employeeid,
                o.NAME AS tablename,
                IF(ISNULL(o.id),0,o.id) AS tableid,
                (d.totalsum) AS totalsum,
                e. NAME AS employeename,
                DATE_FORMAT(
                    d.creationdt,
                    '%d.%m.%y %H:%i:%S'
                ) AS dt,
                tp. NAME AS payment,
                p. NAME AS clientname,
                d.discountpercent AS discountpercent,
                d.discountsum AS discountsum,
                d.discountid as discountid,
                d.paymentid as paymentid
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
                $order['orderid'] = $row['id'];
                $order['visibleid'] = $row['visibleid'];
                $order['table'] = $row['tablename'];
                $order['tableid'] = $row['tableid'];
                $order['discount'] = $row['discount'];
                $order['servicepercent'] = $row['servicepercent'];
                $order['servicesum'] = $row['servicesum'];
                $order['guests'] = $row['guests'];
                $order['totalsum'] = (float)$row['totalsum'];
                $order['employeename'] = $row['employeename'];
                $order['employeeid'] = $row['employeeid'];
                $order['dt'] = $row['dt'];
                $order['closed'] = $row['closed'];
                $order['printed'] = $row['printed'];
                $order['client'] = $row['client'];
                $order['clientname'] = $row['clientname'];
                $order['discountpercent'] = $row['discountpercent'];
                $order['discountid']=$row['discountid'];
                $order['discountsum'] = $row['discountsum'];  
                $order['paymentid'] = $row['paymentid'];
            }else{
                echo json_encode(array('rescode'=>  errcode(66),'resmsg'=>  errmsg(66))); die;
            }; 
            
            return $order;
}

function getInterfaceName($iface){
    switch ($iface){
        case 0: return "Администратор";break;
        case 1: return "Менеджер";break;
        case 2: return "Кассир";break;
        case 3: return "Официант";break;
        case 4: return "Фастфуд";break;
        case 5: return "Администратор зала";break;
        case 6: return "Самообслуживание";break;
        case 7: return "Регистрация чеков";break;    
    }
}

function setLog($orderid,$typeCheck){
        $sql="SELECT
               p. NAME AS clientname,
               p.id AS client,
               barcode as barcode,
               d.idout AS id,
               d.printed AS printed,
               d.closed AS closed,
               d.discountpercent AS discountpercent,
               d.servicepercent AS servicepercent,
               d.servicesum AS servicesum,
               d.guestcount AS guests,
               IF (ISNULL(t1.name),i.name,CONCAT(i.name,' .br.(',t1.name,')')) as foodname,
               o. NAME AS tablename,
               round(d.totalsum) AS totalsum,
               e. NAME AS employeename,
               t.price AS price,
               sum(t.quantity) AS quantity,
               sum(t.price * t.quantity) AS summa,
               DATE_FORMAT( 
                       d.creationdt,
                       '%d.%m.%y %H:%i:%S'
               ) AS dtopen,
               DATE_FORMAT(

                       IF (
                               d.dtclose IS NULL,
                               0,
                               d.dtclose
                       ),
                       '%H:%i:%S'
               ) AS dtclose,
               tp. NAME AS payment,

       IF (
               (tp.id = ap.cashid),
               0,

       IF ((tp.id = ap.slipid), 1, -1)
       ) AS typepay,
        t.dt AS itemdt,
        ap. NAME AS restname,
        d.sumfromclient AS sumfromclient,
        d.discountsum AS discountsum,
        round(
               (
                       totalsum + d.discountsum - d.servicesum
               )
       ) AS clearsum,
       round(sumfromclient-totalsum) as saldo,
       infostring
       FROM
               d_order AS d
       LEFT JOIN t_order AS t ON d.id = t.orderid
       LEFT JOIN s_clients AS p ON d.clientid = p.id
       LEFT JOIN s_items AS i ON t.itemid = i.id
       LEFT JOIN s_objects AS o ON d.objectid = o.id
       LEFT JOIN s_employee AS e ON d.employeeid = e.id
       LEFT JOIN s_types_of_payment AS tp ON d.paymentid = tp.id 
       LEFT JOIN s_automated_point AS ap ON d.idautomated_point = ap.id
       LEFT JOIN (SELECT GROUP_CONCAT(i.name, '.br.') as name,t3.parentid as id  FROM t_order as t3
                                                       LEFT JOIN s_items as i on t3.itemid=i.id 
                                                       WHERE t3.orderid=".$orderid." and t3.parentid<>0
                                                       )  as t1 on t1.id=t.id
       WHERE
               orderid = ".$orderid." and t.parentid=0
       GROUP BY
               i.id"; 
       $result=mysql_query($sql);
       $row=mysql_fetch_assoc($result);
       $result=mysql_query($sql);
       $i = 1;  
       $saldo=$row["sumfromclient"]-$row["totalsum"];
       $row["clientname"]=str_replace('%', '%%', $row["clientname"]); 

        $logStr="Закрытие счета №".getShiftIdOrder($orderid).'
              <br>Клиент:'.$row["clientname"].'; 
              <br>Процент обслуживания'.$row["servicepercent"].'; 
              <br>Процент скидки'.$row["discountpercent"].';    
              <br>Итого:'.$row["clearsum"].'; 
              <br>Сумма обслуживания:'.$row["servicesum"].' 
              <br>Сумма скидки:'.$row["discountsum"].' 
              <br>Всего:'.$row["totalsum"];
        $logType=8;
        $logStr2='';
        if ($typeCheck=="of"){
          $logStr="Оплата счета  №".getShiftIdOrder($orderid).'
              <br>Клиент:'.$row["clientname"].'; 
              <br>Процент обслуживания:'.$row["servicepercent"].'; 
              <br>Процент скидки:'.$row["discountpercent"].';    
              <br>Итого:'.$row["clearsum"].'; 
              <br>Сумма обслуживания:'.$row["servicesum"].' 
              <br>Сумма скидки:'.$row["discountsum"].' 
              <br>Всего:'.$row["totalsum"].'
              <br>Сумма отклиента:'.$row["sumfromclient"].' 
              <br>Сдача:'.$saldo;
          $logType=12;
        }  
        if ($typeCheck=="return"){
          $logStr="Возврат №".getShiftIdOrder($orderid).'
              <br>Клиент:'.$row["clientname"].'; 
              <br>Процент обслуживания'.$row["servicepercent"].'; 
              <br>Процент скидки'.$row["discountpercent"].';    
              <br>Итого:'.$row["clearsum"].'; 
              <br>Сумма обслуживания:'.$row["servicesum"].' 
              <br>Сумма скидки:'.$row["discountsum"].' 
              <br>Всего:'.$row["totalsum"].'
              <br>Сумма отклиента:'.$row["sumfromclient"].'
              <br>Сдача:'.$saldo;
          $logType=19;
        } 

        if ($result){  
            while($row1=mysql_fetch_assoc($result)) {
                        $logStr2.='Товар №'.$i.':'.$row1["foodname"].'; Кол-во:'.(float)$row1["quantity"].'; Цена:'.(float)$row1["price"].'; Сумма:'.(float)$row1["summa"].'<br>';
                        $i++;    
                    };            
        }
        else{}
       addlog($logStr.'<br>'.$logStr2,$logType);
}


function reportSalon($np){
            $output='';
            $inxls='';
            $tmparray=checkChage();
            $idchange=$tmparray['idchange'];
            $closeChange=$tmparray['closeChange'];
            if ($closeChange==1){
                echo json_encode(array('rescode'=>  errcode(13),'resmsg'=>  errmsg(13))); die;
            };
//            $query=mysql_query("select e.`name` as waitername,
//                sum(tordservice.quantity*tordservice.price) as sumserviceorder, 
//                IFNULL(sum(torditem.quantity*torditem.price),0) as sumitemorder,
//                IFNULL((sum(tordservice.quantity*tordservice.price)-IFNULL(sum(torditem.quantity*torditem.price),0))*e.e_itemservicepercent/100,0) as sumsalary
//                from d_order as o
//                LEFT JOIN 
//                (select t_order.orderid,t_order.quantity,t_order.price from t_order,s_items 
//                where t_order.itemid=s_items.id and s_items.isservice!=0) as tordservice 
//                on o.id=tordservice.orderid
//                LEFT JOIN 
//                (select t_order.orderid,t_order.quantity,t_order.price from t_order,s_items 
//                where t_order.itemid=s_items.id and s_items.isservice=0) as torditem 
//                on o.id=torditem.orderid
//                left join s_employee as e on o.employeeid=e.id
//                WHERE o.changeid=".$idchange." 
//                group by e.id");            

                $query=mysql_query("SELECT
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
                                    o.changeid = ".$idchange." 
                            GROUP BY
                                    e.id");   
            
            
            $output[]='<div class="div_otchet"> <h1>Отчет о зарплате</h1>';
            $output[]='<span style="font:10px Tahoma;">Время формирования отчета: '.date('d.m.Y H:i:s').'</span>';
            $output[]='<table class="ttda" '.$inxls.'><tr class="tableheader">
                    <td>#</td>
                    <td>Сотрудник</td>
                    <td>Товары</td>
                    <td>Услуги</td>
                    <td>Затраты</td>
                    <td>Зарплата</td>
                    </tr>';
             $i=1;             
             $sumService=0;            
             $sumItems=0;
             $sumSalary=0;
             $sumConsumption=0;
             //$row=mysql_fetch_array($query);
             //$output[]="qq".$row['sumserviceorder'];
             while($row=mysql_fetch_assoc($query)){ 
                $output[]='<tr><td>'.$i.'</td><td>'.$row['waitername'].'</td>
                <td>'.floatval($row['sumitemorder']).'</td>    
                <td>'.floatval($row['sumserviceorder']).'</td>
                <td>'.floatval($row['consumption']).'</td>
                <td>'.floatval($row['sumsalary']).'</td></tr>'; 
                $sumService+=$row['sumserviceorder'];
                $sumItems+=$row['sumitemorder'];
                $sumSalary+=$row['sumsalary'];
                $sumConsumption+=$row['consumption'];
                $i++;
             }     
             $output[]='<tr><td colspan="2"><b>Итого</b></td><td><b>'.floatval($sumItems).'</b></td><td><b>'.floatval($sumService).'</b></td><td><b>'.floatval($sumConsumption).'</b></td><td><b>'.floatval($sumSalary).'</b></td></tr>'; 
             $output[]='<tr><td colspan="2"><b>Итого в кассе</b></td><td><b>'.floatval($sumService-$sumSalary+$sumItems);              
             $output[]='</table>'; 
             $output[]='<br />';
             echo join("\n",$output); 
             if ($np==1){
                if (empty($_GET['print'])){
                    echo '</div> <a target="_blank" href="/front/PHP/front.php?ftype=printReportSalon" class="printota"><img target="_blank" src="/company/i/printer.gif"></a>';
                }
                return $output;
             }
}




function reSumAllTable($orderid){
    $sqlOrderQuery=  mysql_query('SELECT d.servicepercent,d.discountpercent FROM d_order as d WHERE d.id='.$orderid);
    if ($sqlOrderQuery){
        $rowOrder=  mysql_fetch_assoc($sqlOrderQuery);
        $sqlOrderTableQuery=  mysql_query('SELECT t.id as id,round((t.price * t.quantity)) as summa FROM t_order as t where orderid='.$orderid);
        while ($rowOrderTable=  mysql_fetch_assoc($sqlOrderTableQuery)){
            $tmparr=getTableSum($rowOrder, $rowOrderTable['summa']);
            $updateQuery=  mysql_query('UPDATE t_order as t SET t.servicesum='.$tmparr['service'].', t.discountsum= '.$tmparr['discount'].', t.salesum='.$tmparr['sum'].' WHERE t.id='.$rowOrderTable['id']);
        }
    }
}

function fitness_tarifSum($timeU1,$timeU2,$day,$objectId){

$dayWeek=date('D',strtotime($day));

$sqlText='SELECT
                    tar.id AS tarid,
                    tar. NAME AS tarname,
                    tar.interval_default as intervaldefault,
                    tar.price_default as pricedefault,
                    HOUR(ttar.timeStart_tar)*60+MINUTE(ttar.timeStart_tar) as timeStart,
                    HOUR(ttar.timeEnd_tar)*60+MINUTE(ttar.timeEnd_tar) as timeEnd,
                    ttar.`interval` as interval_tar,
                    ttar.price as price,
                    tar.itemid as itemid
            FROM
                    t_object_tarif AS ot
            LEFT JOIN s_tarifs AS tar ON tar.id = ot.tarifid
            LEFT JOIN t_tarifs AS ttar ON ttar.tarifid = tar.id
            WHERE
                    ot.idobject = '.$objectId.' and ('.$dayWeek.'=1 and (((HOUR(ttar.timeStart_tar)*60+MINUTE(ttar.timeStart_tar))<='.$timeU1.') or (HOUR(ttar.timeEnd_tar)*60+MINUTE(ttar.timeEnd_tar))>='.$timeU2.'))';
$tarifQuery =  mysql_query($sqlText);

if ($tarifQuery){
    if (mysql_num_rows($tarifQuery)>0){
        $tarifSum=0;
        $timeIn=0;
        $allTimeIn=0;
        while ($r =  mysql_fetch_assoc($tarifQuery)){
            if ($timeU1>$r['timeStart']){
                if ($timeU2<$r['timeEnd']){
                    $timeIn+= ($timeU2-$timeU1);
                }else{
                    $timeIn+=($r['timeEnd']-$timeU1);
                }
            }else if ($timeU1<$r['timeStart']){
                if ($timeU2<$r['timeEnd']){
                    $timeIn+= ($timeU2-$r['timeStart']);
                }else{
                    $timeIn+=($r['timeStart']-$r['timeEnd']);
                }
            }
            if ($timeIn>=0){
               $tarifSum+= $timeIn/$r['interval_tar']*$r['price'];
               $allTimeIn+=$timeIn;
            }
            $defItem=$r['itemid'];
            $timeIn=0;
            $default_interval=$r['intervaldefault'];
            $default_price=$r['pricedefault'];
        }
        $tarifSum+=(($timeU2-$timeU1)-$allTimeIn)/$default_interval*$default_price;
         
        $res=array('itemid'=>$defItem,'tarifSum'=>round($tarifSum));
       return $res;
    }else{
        echo json_encode(array('rescode'=>  errcode(183),'resmsg'=>  'У данного объекта не указан тариф!')); die;
    }
}else{
    echo json_encode(array('rescode'=>  errcode(183),'resmsg'=>  errmsg(183))); die;
}

}



?>

