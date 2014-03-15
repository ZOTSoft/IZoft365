<?php

//хуй



//неебическая штука на проверку проведения документа
function isConducted( $tablename, $id ){
    $result = true;
    if ( substr( $tablename, 0, 2 ) == 't_' ){
        $q = mysql_query( "SELECT documentid FROM `".addslashes( $tablename )."` WHERE id=".addslashes( $id )." LIMIT 1" );
        $result = false;
        if ( $q ){
            $row = mysql_fetch_row( $q );
            $id = $row[0];
            $result = true;
        } else return 'big trouble';
    }
    
    if ( $result ){
        $q = mysql_query( "SELECT conducted FROM `".addslashes( 'd_'.( substr( $tablename, 2 ) ) )."` WHERE id=".addslashes( $id )." LIMIT 1" );
        $result = false;
        if ( $q ){
            $row = mysql_fetch_row( $q );
            $result = (int)$row[0] == 1;
        }
    }
    
    return $result;
}


//отмена проведения      таблица        не надо   ид   начинать ранзакцию, потому что может быть в отмене своя транзация    
function cancelConduct( $documentname, $doctype, $id, $changeFlag = false, $reg = 'r_remainder' ){
    $doctype = -1;
    switch ( $documentname ){
//СКЛАДСКИЕ ДОКУМЕНТЫ
        case 'd_order':         $doctype = 0; break;
        case 'd_receipt':       $doctype = 1; break;
        case 'd_selling':       $doctype = 2; break;
        case 'd_posting':       $doctype = 3; break;
        case 'd_cancellation':  $doctype = 4; break;
        case 'd_inventory':     $doctype = 5; break;
        case 'd_movement':      $doctype = 6; break;
        case 'd_production':    $doctype = 7; break;
        case 'd_regrading':     $doctype = 8; break;
////СКЛАДСКИЕ ДОКУМЕНТЫ
        
//КАССОВЫЕ ДОКУМЕНТЫ
        case 'd_cash_income':   $doctype = 1; break;
        case 'd_cash_outcome':  $doctype = 2; break;
        case 'd_cash_movement': $doctype = 3; break;
////КАССОВЫЕ ДОКУМЕНТЫ
    }
    
    if ( $changeFlag ) mysql_query( 'START TRANSACTION' );

    $result = mysql_query( 'DELETE FROM '.$reg.' WHERE documenttype='.$doctype.' AND documentid='.$id );
    if ( $result && $changeFlag ) // снимаем флажок провидения
        $result = mysql_query( 'UPDATE '.$documentname.' SET conducted=0 WHERE id='.$id );
    
    if ( $result ){
        zlog( json_encode( array(
            'table' => $documentname,
            'row' => array( 'id' => $id ) 
        ) ), 1402 );
        if ( $changeFlag ) mysql_query( 'COMMIT' );
    } else {
        zlog( json_encode( array(
            'table' => $documentname,
            'row' => array( 'id' => $id ) 
        ) ), 1403 );
        if ( $changeFlag ) mysql_query( 'ROLLBACK' );
    }

    return $result;
}

function get_d_inventory_dt($documentid){
    $q=mysql_query("SELECT dt FROM d_inventory WHERE id='".intval($documentid)."'");
    if (mysql_numrows($q)){
        $row=mysql_fetch_assoc($q);
        return $row['dt'];
    }
}

function have_no_warehouse($id){
    $stopdate='';
    if ($date=getConfigVal('stopconductdate')){
        if ($date!='')
            $stopdate=" AND creationdt>'".date('Y-m-d H:i:s',strtotime($date))."'";
    }
    $q=mysql_query("SELECT idautomated_point 
                    FROM d_order 
                    LEFT JOIN s_automated_point ON d_order.idautomated_point=s_automated_point.id
                    WHERE d_order.id='".intval($id)."' AND  warehouseid>0 ".$stopdate);
    return !mysql_numrows($q);
}



// собсна проведение доку ментов
function conduct( $documentname, $id ){
    $answer = false;

    if ( substr( $documentname, 0, 7 ) == 'd_cash_' )
        $answer = conductCash( $documentname, $id );
    else
        $answer = conductWarehouse( $documentname, $id );
    
    return $answer;
}



// проведение складксих документов нуждается в тюнинге
function conductWarehouse( $documentname, $id ){
    //echo have_no_warehouse($id);
    if ($documentname=='d_order' && have_no_warehouse($id) ) return false;
    $answer = false;

    $tablename = 't_'.( substr( $documentname, 2 ) );
    
    $doctype = -1;
    $fields = '';
    switch ( $documentname ){
        case 'd_order':
            $doctype = 0;
            $fields = ' 0 AS warehouseid, clientid, creationdt AS dt, idautomated_point AS apid  ';
        break;
        case 'd_receipt':
            $doctype = 1;
            $fields = ' warehouseid, clientid, dt, 0 AS apid ';
        break;
        case 'd_selling':
            $doctype = 2;
            $fields = ' warehouseid, clientid, dt, 0 AS apid ';
        break;
        case 'd_posting':
            $doctype = 3;
            $fields = ' warehouseid, 0 AS clientid, dt, 0 AS apid ';
        break;
        case 'd_cancellation':
            $doctype = 4;
            $fields = ' warehouseid, 0 AS clientid, dt, 0 AS apid ';
        break;
//ИНВЕНТАРИЗАЦИЯ НЕ ПРОВОДИТСЯ - НА ЕЕ ОСНОВАНИИ МОГУТ СОЗДАВАТЬСЯ ОПРИЗОДОВАНИЕ И СПИСАНИЕ
        case 'd_inventory':
            $doctype = 5;
            $fields = ' warehouseid, 0 AS clientid, dt, 0 AS apid ';
        break;
//С ПЕРЕМЕЩЕНИЕМ ОСОБЫЙ СЛУЧАЙ: СНАЧАЛА СПИСАНИЕ С warehouseid, ПОТОМ ОПРИХОДОВАНИЕ НА warehousetoid
        case 'd_movement':
            $doctype = 6;
            $fields = ' warehouseid, warehousetoid, dt, 0 AS apid ';
        break;
//ПОЧТИ КАК СЧЕТА, НО СПИСЫВАЮТСЯ ТОЛЬКО ИНГРЕДИЕНТЫ/ПОЛУФАБРИКАТЫ, А НЕ БЛЮДА
        case 'd_production':
            $doctype = 7;
            $fields = ' warehouseid, 0 AS clientid, dt, 0 AS apid ';
        break;
        case 'd_regrading':
            $doctype = 8;
            $fields = ' warehouseid, 0 AS clientid, dt, 0 AS apid ';
        break;
    }
    
    if ( $doctype != -1 && $doctype != 5 ){
//ОТМЕНЯЕМ ПРОВЕДЕНИЕ, ЕСЛИ ТАКОЕ БЫЛО
//        if ( isConducted( $documentname, $id ) ){
//            $answer = cancelConduct( $documentname, $doctype, $id );
//        } else
            $answer = true;

        if ( $answer ){
        
            mysql_query( 'START TRANSACTION' );
            
            if ( isConducted( $documentname, $id ) ){
                $answer = cancelConduct( $documentname, $doctype, $id );
            }

//ПОЛУЧАЕМ СКЛАД И КЛИЕНТА ИЗ ДОКУМЕНТА
            $warehouseid = 0;
            $warehouseid2 = 0;
            $clientid = 0;
            $apid = 0;
            $docdt = time();

            $result = mysql_query( 'SELECT '.$fields.' FROM '.$documentname.' WHERE id='.$id.' LIMIT 1' );
            //echo 'SELECT '.$fields.' FROM '.$documentname.' WHERE id='.$id.' LIMIT 1'  ;
            if ( mysql_num_rows( $result ) > 0 ){
                $row = mysql_fetch_array( $result );

                if ( $doctype == 6 ){
                    $warehouseid = $row['warehouseid'];
                    $warehouseid2 = $row['warehousetoid'];
                } else {
                    $warehouseid = $row['warehouseid'];
                    $clientid = $row['clientid'];
                }
                
                if ( $row['dt'] == '' )
                    $docdt = 'NOW()';
                else
                    $docdt = '"'.$row['dt'].'"';
                
                $apid = $row['apid'];
            }

//ПОЛУЧАЕМ ТАБЛИЧНУЮ ЧАСТЬ ДОКУМЕНТА
            if ( $doctype == 0 ){//  d_order
                $result = mysql_query( 'SELECT t.itemid, SUM(t.quantity) AS quantity, SUM(t.quantity * IFNULL(r.costprice, 0)) AS costsum, IFNULL(SUM(t.salesum),0) AS salesum, 
IF(ISNULL(d.warehouseid) OR d.warehouseid = 0, ap.warehouseid, d.warehouseid) AS warehouseid, IFNULL(sc.id, 0) AS hasCalculation, t.specificationid 
FROM t_order AS t 
LEFT JOIN (SELECT id, idautomated_point AS apid FROM d_order) AS dor ON dor.id = t.orderid 
LEFT JOIN (SELECT id, warehouseid FROM s_automated_point) AS ap ON ap.id = dor.apid 
LEFT JOIN s_subdivision AS d ON d.id = t.printerid 
LEFT JOIN (SELECT warehouseid, itemid, SUM(costsum) / SUM(quantity) AS costprice 
FROM r_remainder
WHERE dt<'.$docdt.'
GROUP BY warehouseid, itemid 
) AS r ON r.itemid = t.itemid AND r.warehouseid = IF(ISNULL(d.warehouseid) OR d.warehouseid = 0, ap.warehouseid, d.warehouseid)  
LEFT JOIN (
 SELECT c1.id, c1.itemid, c1.dt
 FROM t_calculations_ap AS ap
 LEFT JOIN s_calculations AS c1 ON c1.id=ap.calculationid
 LEFT JOIN (
  SELECT itemid, MAX(dt) AS dt FROM s_calculations
  WHERE dt<='.$docdt.'
  GROUP BY itemid
 ) AS c2 ON c1.itemid=c2.itemid
 WHERE ap.automatedpointid='.$apid.' AND c1.dt=c2.dt
) AS sc ON sc.itemid = t.itemid 
WHERE orderid='.$id
.' GROUP BY t.itemid, t.specificationid, t.price 
ORDER BY hasCalculation, t.itemid' );



                
//                $result2 = mysql_query( 'SELECT t.itemid AS parent, tc.itemid, SUM(t.quantity) * tc.quantity * IFNULL(m.multip,1) / sc.quantity AS quantity, 
//SUM(t.quantity) * tc.quantity * IFNULL(m.multip,1) / sc.quantity * IFNULL(r.costprice, 0) AS costsum, 0 AS salesum, 
//IF(d.warehouseid = 0, ap.warehouseid, d.warehouseid) AS warehouseid 
//FROM t_order AS t 
//LEFT JOIN s_calculations AS sc ON sc.itemid = t.itemid 
//LEFT JOIN t_calculations AS tc ON tc.calculationid = sc.id 
//LEFT JOIN (SELECT id, idautomated_point AS apid FROM d_order) AS dor ON dor.id = t.orderid 
//LEFT JOIN (SELECT id, warehouseid FROM s_automated_point) AS ap ON ap.id = dor.apid 
//LEFT JOIN s_subdivision AS d ON d.id = t.printerid 
//LEFT JOIN ( SELECT warehouseid, itemid, SUM(costsum) / SUM(quantity) AS costprice 
//FROM r_remainder 
//GROUP BY warehouseid, itemid 
//) AS r ON r.itemid = tc.itemid AND r.warehouseid = IFNULL(d.warehouseid, ap.warehouseid) 
//LEFT JOIN ( SELECT id, measurement FROM s_items ) AS i ON i.id=tc.itemid
//LEFT JOIN s_multipliers AS m ON m.frommeasureid=tc.measureid AND m.tomeasureid=i.measurement
//WHERE orderid='.$id.' AND calculationid > 0 
//GROUP BY t.itemid, tc.itemid 
//ORDER BY t.itemid' );
//
//                $ingredients = array();
//                if ( $result2 && mysql_num_rows( $result2 ) > 0 )
//                    while ( $row2 = mysql_fetch_array( $result2 ) )
//                        $ingredients[] = $row2;
//                $currentIngredient = 0;
            } else if ( $doctype == 8 ){
                $result = mysql_query( 'SELECT t.destitemid AS itemid, t.destspecificationid AS specificationid, SUM(t.quantity) AS quantity, IFNULL(SUM(t.quantity * t.price), 0) AS costsum
FROM t_regrading AS t 
WHERE documentid='.$id.' 
GROUP BY t.destitemid, t.destmeasureid, t.price' );
                
                $result2 = mysql_query( 'SELECT t.srcitemid AS itemid, t.srcspecificationid AS specificationid, (-1) * SUM(t.quantity) AS quantity, IFNULL((-1) * SUM(t.quantity * r.costprice), 0) AS costsum
FROM t_regrading AS t 
LEFT JOIN ( SELECT warehouseid, itemid, SUM(costsum) / SUM(quantity) AS costprice, SUM(salesum) / SUM(quantity) AS saleprice
FROM r_remainder 
WHERE documenttype IN (0,1,3,6) AND costsum>0 AND dt<'.$docdt.' 
GROUP BY warehouseid, itemid
) AS r ON r.itemid = t.srcitemid AND r.warehouseid='.$warehouseid.'
WHERE documentid='.$id.' 
GROUP BY t.srcitemid, t.srcmeasureid, t.price' );
            } else {
                $costprice = 't.price';
                $joincostsum = '';
                $hasCalc = '';
                $joinCalc = '';
                if ( $doctype == 7 ){
                    $costprice = '0';
                    $hasCalc = ', IFNULL(sc.id, 0) AS hasCalculation ';
                    $joinCalc = 'LEFT JOIN (
 SELECT c1.id, c1.itemid, c1.dt
 FROM s_calculations AS c1
 LEFT JOIN (
  SELECT itemid, MAX(dt) AS dt FROM s_calculations
  WHERE dt<='.$docdt.'
  GROUP BY itemid
 ) AS c2 ON c1.itemid=c2.itemid
 WHERE c1.dt=c2.dt
) AS sc ON sc.itemid = t.itemid ';
                } else if ( $doctype == 2 || $doctype == 4 || $doctype == 6 ){
                    $costprice = 'r.costprice';
                    $joincostsum = 
'LEFT JOIN ( SELECT warehouseid, itemid, IF(SUM(quantity) > 0, SUM(costsum) / SUM(quantity), 0) AS costprice
FROM r_remainder 
WHERE dt<'.$docdt.' 
GROUP BY warehouseid, itemid
) AS r ON r.itemid = t.itemid AND r.warehouseid='.$warehouseid;
                }
                $saleprice = $doctype == 1 || $doctype == 3 || $doctype == 6 || $doctype == 7 ? '0' : 'SUM(t.quantity * t.multip * t.price)';
                $gprice = $doctype == 6 || $doctype == 7 ? '' : ', t.price';

                $result = mysql_query( 'SELECT t.itemid, SUM(t.quantity * t.multip) AS quantity, IFNULL(SUM(t.quantity * t.multip * '.$costprice.'), 0) AS costsum, '.$saleprice.' AS salesum, t.specificationid'
.$hasCalc
.' FROM '.$tablename.' AS t LEFT JOIN s_items AS i ON i.id = t.itemid '
.$joincostsum
.$joinCalc
.' WHERE documentid='.$id.' 
GROUP BY t.itemid, t.measureid'.$gprice );


            }

            if ( mysql_num_rows( $result ) > 0 ){
                $values = array();
                $values2 = array();

                if ( $doctype == 6 ){
                    while ( $row = mysql_fetch_array( $result ) ){
                        $values[] = '('.$docdt.', '.$doctype.', '.$id.', '.$warehouseid.', 0, '.$row['itemid'].', -'.$row['quantity'].', -'.$row['costsum'].', '.$row['salesum'].', '.$row['specificationid'].')';
                        $values2[] = '('.$docdt.', '.$doctype.', '.$id.', '.$warehouseid2.', 0, '.$row['itemid'].', '.$row['quantity'].', '.$row['costsum'].', '.$row['salesum'].', '.$row['specificationid'].')';
                    }

                    $query = 'INSERT INTO r_remainder (dt, documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum, specificationid) VALUES '.join( ',', $values );
                    $answer = mysql_query( $query );

                    if ( $answer ){
                        $query = 'INSERT INTO r_remainder (dt, documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum, specificationid) VALUES '.join( ',', $values2 );
                        $answer = mysql_query( $query );
                    }
                } else if ( $doctype == 8 ){
                    while ( $row = mysql_fetch_array( $result ) ){
                        $values[] = '('.$docdt.', '.$doctype.', '.$id.', '.$warehouseid.', 0, '.$row['itemid'].', '.$row['quantity'].', '.$row['costsum'].', '.'0'/*$row['salesum']*/.', '.$row['specificationid'].')';
                    }
                    
                    while ( $row = mysql_fetch_array( $result2 ) ){
                        $values2[] = '('.$docdt.', '.$doctype.', '.$id.', '.$warehouseid.', 0, '.$row['itemid'].', '.$row['quantity'].', '.$row['costsum'].', '.'0'/*$row['salesum']*/.', '.$row['specificationid'].')';
                    }

                    $query = 'INSERT INTO r_remainder (dt, documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum, specificationid) VALUES '.join( ',', $values );
                    $answer = mysql_query( $query );

                    if ( $answer ){                        
                        $query = 'INSERT INTO r_remainder (dt, documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum, specificationid) VALUES '.join( ',', $values2 );
                        $answer = mysql_query( $query );
                    }
                } else if ( $doctype == 0 ) {
                    while ( $row = mysql_fetch_array( $result ) ){
                        $warehouseid = $row['warehouseid'];
                            
                        if ( $row['hasCalculation'] == 0 )
                            $values[] = '('.$docdt.', '.$doctype.', '.$id.', '.$warehouseid.', '.$clientid.', '.$row['itemid'].', -'.$row['quantity'].', -'.$row['costsum'].', '.$row['salesum'].', '.$row['specificationid'].')';
                        else {
//                            if ( $withIngredients ){
                                //типа оприходовал
                                $query = 'INSERT INTO r_remainder (dt, documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum, specificationid) VALUES 
('.$docdt.', '.$doctype.', '.$id.', '.$warehouseid.', '.$clientid.', '.$row['itemid'].', '.$row['quantity'].', 0, 0, '.$row['specificationid'].')';
                                $answer = mysql_query( $query );// or die( mysql_error().'<br />'.$query );

                                if ( $answer ){
                                    $last_id = mysql_insert_id();
                                    $currentItem = $row['itemid'];

//                                $values3 = array();
//                                $costsum = 0;
//                                while ( $currentIngredient < count( $ingredients ) && $ingredients[$currentIngredient]['parent'] == $currentItem ){
//                                    $values3[] = '('.$doctype.', '.$id.', '.$ingredients[$currentIngredient]['warehouseid'].', '.$clientid.', '.$ingredients[$currentIngredient]['itemid'].', -'
//                                            .$ingredients[$currentIngredient]['quantity'].', -'.$ingredients[$currentIngredient]['costsum'].', 0, '.$last_id.')';
//
//                                    $costsum += $ingredients[$currentIngredient]['costsum'];
//
//                                    $currentIngredient++;
//                                }
//
//                                $query = 'INSERT INTO r_remainder (documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum, parentid) VALUES '.  join( ',', $values3 );
//                                $answer = mysql_query( $query );

                                    $a = get_calculation_sql( $row['hasCalculation'], $row['quantity'], false/*$lastPrices*/, $docdt, $doctype, $id, $warehouseid, $clientid, $last_id, $apid );
                                   //print_r($a);
                                    //echo 1;
                                    //print_r($a);
                                    $costsum = $a['costsum'];
                                    //$answer=true;
                                    if (!empty($a['lines'])){
                                        $query = 'INSERT INTO r_remainder (dt, documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum, parentid) VALUES '.$a['lines'];
                                        //echo $query;
                                        $answer = mysql_query( $query );// or die( mysql_error().'<br />'.$query );
                                    }
                                //    if ( $answer ){ 2013-11-24 21:12:34
                                        $query = 'UPDATE r_remainder SET costsum='.$costsum.' WHERE id='.$last_id;
                                        $answer = mysql_query( $query ) or die( mysql_error().'<br />'.$query );

                                        if ( $answer ){
                                            $query = 'INSERT INTO r_remainder (dt, documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum, parentid) VALUES 
('.$docdt.', '.$doctype.', '.$id.', '.$warehouseid.', '.$clientid.', '.$row['itemid'].', -'.$row['quantity'].', -'.$costsum.', '.$row['salesum'].', '.$last_id.')';
                                            $answer = mysql_query( $query );// or die( mysql_error().'<br />'.$query );
                                        }
                                   // }
                                }
//                            } else {
//                                $a = get_calculation_sql( $row['hasCalculation'], $row['quantity'], false/*$lastPrices*/, $docdt, $doctype, $id, $warehouseid, $clientid, 0, $apid, true );
//                                $query = 'INSERT INTO r_remainder (dt, documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum) VALUES 
//('.$docdt.', '.$doctype.', '.$id.', '.$warehouseid.', '.$clientid.', '.$row['itemid'].', -'.$row['quantity'].', -'.$a['costsum'].', '.$row['salesum'].')';
//                                $answer = mysql_query( $query );// or die( mysql_error().'<br />'.$query );
//                            }
                        }
                    }

                    if ( !empty( $values ) ){
                        $query = 'INSERT INTO r_remainder (dt, documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum, specificationid) VALUES '.  join( ',', $values );
                        
                        $answer = mysql_query( $query );// or die( mysql_error().'<br />'.$query );
                    }
                } else if ( $doctype == 7 ){
                    while ( $row = mysql_fetch_array( $result ) ){
                        if ( $row['hasCalculation'] == 0 )
                            //$values[] = '('.$docdt.', '.$doctype.', '.$id.', '.$warehouseid.', '.$clientid.', '.$row['itemid'].', -'.$row['quantity'].', -'.$row['costsum'].', '.$row['salesum'].', '.$row['specificationid'].')';
                            $values[] = '('.$docdt.', '.$doctype.', '.$id.', '.$warehouseid.', '.$clientid.', '.$row['itemid'].', '.$row['quantity'].', 0, '.$row['salesum'].', '.$row['specificationid'].')';
                        else {
                            $query = 'INSERT INTO r_remainder (dt, documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum, specificationid) VALUES 
('.$docdt.', '.$doctype.', '.$id.', '.$warehouseid.', '.$clientid.', '.$row['itemid'].', '.$row['quantity'].', 0, 0, '.$row['specificationid'].')';
                            $answer = mysql_query( $query );

                            if ( $answer ){
                                $last_id = mysql_insert_id();

                                $a = get_calculation_sql( $row['hasCalculation'], $row['quantity'], false/*$lastPrices*/, $docdt, $doctype, $id, $warehouseid, $clientid, $last_id );
                                $costsum = $a['costsum'];
                                $query = 'INSERT INTO r_remainder (dt, documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum, parentid) VALUES '.$a['lines'];
                                $answer = mysql_query( $query );
                                
                                if ( $answer ){
                                    $query = 'UPDATE r_remainder SET costsum='.$costsum.' WHERE id='.$last_id;
                                    $answer = mysql_query( $query );
                                }
                            }
                        }
                    }

                    if ( !empty( $values ) ){
                        $query = 'INSERT INTO r_remainder (dt, documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum, specificationid) VALUES '.  join( ',', $values );
                        $answer = mysql_query( $query );
                    }
                } else {
                    $minus = $doctype == 2 || $doctype == 4 ? '-' : '';

                    while ( $row = mysql_fetch_array( $result ) ){
                        if ( $doctype == 0 ) $warehouseid = $row['warehouseid'];
                        $values[] = '('.$docdt.', '.$doctype.', '.$id.', '.$warehouseid.', '.$clientid.', '.$row['itemid'].', '.$minus.$row['quantity'].', '.$minus.$row['costsum'].', '.$row['salesum'].', '.$row['specificationid'].')';
                    }

                    $query = 'INSERT INTO r_remainder (dt, documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum, specificationid) VALUES '.  join( ',', $values );
                    //echo $query;
                    $answer = mysql_query( $query );
                }

                if ( $answer )
                    $answer = mysql_query( 'UPDATE '.$documentname.' SET conducted=1 WHERE id='.$id );
            }

            if ( $answer ){
                zlog( json_encode( array(
                    'table' => $documentname,
                    'row' => array( 'id' => $id ) 
                ) ), 1400 );
                mysql_query( 'COMMIT' );
            } else {
                zlog( json_encode( array(
                    'table' => $documentname,
                    'row' => array( 'id' => $id ) 
                ) ), 1401 );
                mysql_query( 'ROLLBACK' );
            }
        }
    }
    
    return $answer;
}

// проведение кассовых документов
function conductCash( $documentname, $id ){
    $answer = false;

    $doctype = -1;
    // надо убрать филдс
    $fields = '';
    switch ( $documentname ){
        case 'd_cash_income':   $doctype = 1;   break;
        case 'd_cash_outcome':  $doctype = 2;   break;
        case 'd_cash_movement': $doctype = 3;   break;
    }
    
    mysql_query( 'START TRANSACTION' );
    
    $result = mysql_query( 'SELECT dt, organizationid, cashid, '.( $doctype == 3 ? 'cashtoid, ' : '' ).'amount, paymentid, conducted FROM `'.$documentname.'` WHERE id="'.$id.'"' );
    $answer = mysql_num_rows( $result ) > 0;
    
    if ( $answer ){
        $row = mysql_fetch_array( $result );
        
        if ( $row['conducted'] == '1' ) $answer = cancelConduct( $documentname, $doctype, $id, false, 'r_cash' );
        
        if ( $answer ){
            if ( $doctype == 3 ){//ПЕРЕМЕЩЕНИЕ
                $answer = mysql_query( 'INSERT INTO r_cash (dt, documenttype, documentid, organizationid, cashid, paymentid, amount) VALUES 
("'.$row['dt'].'", '.$doctype.', '.$id.', '.$row['organizationid'].', '.$row['cashid'].', '.$row['paymentid'].', -'.$row['amount'].')' );
                
                if ( $answer )
                    $answer = mysql_query( 'INSERT INTO r_cash (dt, documenttype, documentid, organizationid, cashid, paymentid, amount) VALUES 
("'.$row['dt'].'", '.$doctype.', '.$id.', '.$row['organizationid'].', '.$row['cashtoid'].', '.$row['paymentid'].', '.$row['amount'].')' );
            } else {//ПОСТУПЛЕНИЕ / ИЗЪЯТИЕ
                $minus = $doctype == 1 ? '' : '-';
                
                $answer = mysql_query( 'INSERT INTO r_cash (dt, documenttype, documentid, organizationid, cashid, paymentid, amount) VALUES 
("'.$row['dt'].'", '.$doctype.', '.$id.', '.$row['organizationid'].', '.$row['cashid'].', '.$row['paymentid'].', '.$minus.$row['amount'].')' );
            }

            if ( $answer ) $answer = mysql_query( 'UPDATE '.$documentname.' SET conducted=1 WHERE id='.$id );
        }
    }
    
    if ( $answer ){
        zlog( json_encode( array(
            'table' => $documentname,
            'row' => array( 'id' => $id ) 
        ) ), 1400 );
        mysql_query( 'COMMIT' );
    } else {
        zlog( json_encode( array(
            'table' => $documentname,
            'row' => array( 'id' => $id ) 
        ) ), 1401 );
        mysql_query( 'ROLLBACK' );
    }
}

//сумма табличной части по столбцам                              
function get_db_select_sum( $table, $idfield, $id, $sumfield, $precision ){
    if ( $id > 0 ){
        $query = mysql_query( "SELECT SUM(".$sumfield.") AS total FROM `".addslashes( $table )."` WHERE ".$idfield."=".$id );  
        if ( $query ){
            $row = mysql_fetch_array( $query );
            return !empty( $row['total'] ) ? round( $row['total'], $precision ) : '0';
        } else return '0';
    } else {
        return '0';
    }  
}
// формирование таблицы для печати
function getDocTable( $table, $id, $idfield ){
    global $fields;
    
    $wherestr = " WHERE id=".$id;
    $query = mysql_query( "SELECT * FROM `".addslashes( $table )."` WHERE ".$idfield."=".$id );

    $res[] = '<table class="doctable" style="border: 1px solid #555; margin: 0; margin-bottom: 10px; padding: 0; border-collapse: collapse; width: '.getConfigVal('otchet_width').'px;">';

    $a = '<tr class="tableheader" style="background: #F3F3F3;"><td>№</td>';
    foreach( $fields[$table] as $k => $v )
        if ( $v['in_grid'] )
            $a .= '<td>'.$v['title'].'</td>';
    $a .= '</tr>';

    $res[] = $a;
    
    $i = 0;
    
    $doctd = ' style="margin: 0; padding: 3px; border: 1px solid #CCC;"';
    $doctdname = ' style="min-width: 120px; margin: 0; padding: 3px; border: 1px solid #CCC;"';
    $doctdnum = ' style="margin: 0; padding: 3px; text-align: right; border: 1px solid #CCC;"';
    
    while ( $row = mysql_fetch_array( $query ) ){
        $i++;
        $a = '<tr><td'.$doctdnum.'>'.$i.'</td>';
        
        foreach( $fields[$table] as $k => $v ){
            if ( $v['in_grid'] ){
                switch( $v['type'] ){
                    case 'label':
                        $a .= '<td'.$doctd.'>'.get_select_val( $v['db_select'], $row[$k] ).'</td>';
                    break;
                    case 'sum':
                        $a .= '<td class="numeric" style="margin: 0; padding: 3px; text-align: right; border: 1px solid #CCC;">'.get_db_select_sum( $v['db_select'], $v['idfield'], $row["id"], $v['sumfield'], 2 ).'</td>';
                    break;
                    case 'rowsum':
                        if ( $k == 'totalplanned' )
                            $a .= '<td'.$doctdnum.'>'.round( $row["plannedquantity"] * $row["price"] * $row["multip"], 2 ).'</td>';
                        else
                            $a .= '<td'.$doctdnum.'>'.round( $row["quantity"] * $row["price"] * $row["multip"], 2 ).'</td>';
                    break;
                    case 'diff':
                        $a .= '<td'.$doctdnum.'>'.( $row["quantity"] - $row["plannedquantity"] ).'</td>';
                    break;
                    case 'date':
                    case 'datetime':
                        $a .= '<td'.$doctd.'>'.$row[$k].'</td>';
                    break;
                    case 'input':
                        if ( $k == 'name' || $k == 'multip' )
                            $a .= '<td'.( $k == 'name' ? $doctdname : $doctd ).'>'.$row[$k].'</td>';
                        else
                            $a .= '<td'.$doctdnum.'>'.round( $row[$k], 2 ).'</td>';
                    break;
                    case 'db_select':
                    case 'db_groupselect':
                        if ( $k == 'documentid' ){
                            $a .= '<td'.$doctdnum.'>'.round( $row[$k], 2 ).'</td>';
                        } else {
                            $a .= '<td'.$doctd.'>'.get_select_val( $v['db_select'], $row[$k] ).'</td>';
                        }
                    break;
                }
            }
        }
        $a .= '</tr>';
        
        $res[] = $a;
    }
    
    $res[] = '</table>';
    
    return join( '', $res );
}


// снести нахер
function showfieldA( $table, $field, $data, $id ){
    global $fields;
    $res = array();
    $type = $fields[$table][$field]['type'];
        
    $res[] = '<div class="form-group">
<label class='.( $type == 'db_grid' ? '"col-lg-12 control-label" style="text-align: left;"' : '"col-lg-4 control-label"' ).'>'
.( $fields[$table][$field]['required'] ? '<span style="color: #F00; font-size: 15px;">*</span>' : '' )
.$fields[$table][$field]['title']
.( !empty( $fields[$table][$field]['alt'] ) ? ' (<a href="#" data-toggle="tooltip" data-placement="right" data-animation="false" class="tytip" title="'.$fields[$table][$field]['alt'].'">i</a>)' : '' )
.':</label>
<div class="col-lg-'.( $type == 'db_grid' ? '12' : '8' ).'">';
    
    switch ( $type ){
       case 'textarea': 
            $res[] = '<textarea name="'.$field.'" '.( !empty( $fields[$table][$field]['readonly'] ) ? 'readonly="readonly"' : '').' class="form-control" '.( $v['required'] ? 'required="true"' : '' ).' '
.' style="min-width: 527px; max-width: 527px;'.( !empty( $fields[$table][$field]['field_width'] ) ? ' width:'.$fields[$table][$field]['field_width'].'px;' : '' ).'">'
.( isset( $data[$field] ) ? htmlspecialchars( $data[$field] ) : '' ).'</textarea>';
        break;
        case 'sum':
            $res[] = '<input id="'.$field.'" class="form-control" readonly value="'.get_db_select_sum( $fields[$table][$field]['db_select'], $fields[$table][$field]['idfield'], $id, $fields[$table][$field]['sumfield'], ( $fields[$table][$field]['precision'] ? $fields[$table][$field]['precision'] : 2 ) ).'">';
        break;
        case 'rowsum':
            if ( $table == 't_calculations' ){
                $loss = 0;
                if ( $field == 'loss_cold_quantity' )
                    $loss = round( $data['quantity'] - $data['quantity'] * $data['loss_cold'] / 100, 3 );
                else
                    $loss = round( ( $data['quantity'] - $data['quantity'] * $data['loss_cold'] / 100 ) - $data['quantity'] * $data['loss_hot'] / 100, 3 );

                $res[] = '<input id="'.$field.'" class="form-control" readonly value="'.$loss.'">';
            }
        break;
        case 'label':
            if ( $id == 0 ){
                $res[] = '<input class="form-control" readonly value="'.( isset( $_SESSION['employeeid'] ) ? getfiobyid( $_SESSION['employeeid'] ) : $_SESSION["user"] ).'">
<input name="'.$field.'" type="hidden" value="'.( isset( $_SESSION['employeeid'] ) ? $_SESSION['employeeid'] : $_SESSION["userid"] ).'">';
            } else
                $res[] = '<input class="form-control" readonly value="'.get_db_select_value( $fields[$table][$field]['db_select'], isset( $data[$field] ) ? $data[$field] : 0 ).'">
<input name="'.$field.'" type="hidden" value="'.$data[$field].'">';
        break;
        case 'input': 
            $res[] = '<input name="'.$field.'" class="form-control" '.( $fields[$table][$field]['required'] ? 'required="true"' : '' ).' '.( $fields[$table][$field]['readonly'] ? 'readonly' : '' )
.' value="'.( isset( $data[$field] ) ? ( $fields[$table][$field]['precision'] ? round( $data[$field], $fields[$table][$field]['precision'] ) : htmlspecialchars( $data[$field] ) ) : $fields[$table][$field]['default'] )
.'"'.( !empty( $fields[$table][$field]['field_width'] ) ? ' style="width:'.$fields[$table][$field]['field_width'].'px;float:left;"' : '' ).'>';
        break;
        case 'date':
            $res[] = '<input name="'.$field.'" class="form-control" type="datetime" '.( $fields[$table][$field]['required'] ? 'required="true"' : '' )
.' value="'.( isset( $data[$field] ) ? date( 'd.m.Y', strtotime( $data[$field] ) ) : date( 'd.m.Y', time() ) ).'" '
.( !empty( $fields[$table][$field]['field_width'] ) ? ' style="width:'.$fields[$table][$field]['field_width'].'px;float:left;"' : '' ).'>';
        break;
        case 'datetime':
            $res[] = '<div class="dpicker"><div class="input-group date datetimep">
<input type="text" '.( $fields[$table][$field]['required'] ? 'required="true"' : '' )
.' value="'.( isset( $data[$field] ) ? date( 'd.m.Y H:i:s', strtotime( $data[$field] ) ) : date( 'd.m.Y H:i:s', time() ) )
.'" class="form-control" data-format="dd.MM.yyyy hh:mm:ss" name="'.$field.'" />
<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
</div></div>';
            $res[] = '<script>$( document ).ready( function (){ $(\'.datetimep\').datetimepicker(); })</script>';
        break;
        case 'password': 
            $res[] = '<input name="'.$field.'" type="password" class="form-control" '.( !empty( $data[$field] ) ? 'placeholder="******"' : '' )
.'  value="'.( !empty( $data[$field] ) ? 'zottig' : '' ).'"'.( !empty( $fields[$table][$field]['field_width'] ) ? ' style="width:'.$fields[$table][$field]['field_width'].'px;float:left;"' : '' ).'>';
        break;
        case 'checkbox': 
            $res[] = '<div style="width:50px"><input name="'.$field.'" type="checkbox" class="form-control checkselect" '.( $data[$field] == 1 ? 'checked="checked"' : '' ).' value="1"></div>';
        break;
        case 'db_select':
            $selectfield = 'name';
            if ( isset( $fields[$table][$field]['selectfield'] ) ) $selectfield = $fields[$table][$field]['selectfield'];
            
            $default_id = '';
            $default_title = '';
            if ( $id == 0 && $defaults = get_def_value_for_field( $field ) ){
                $default_id = $defaults['id'];
                $default_title = htmlspecialchars( $defaults['title'] );
            }else{
                $default_title = htmlspecialchars( get_db_select_value( $fields[$table][$field]['db_select'], isset( $data[$field] ) ? $data[$field] : 0, $selectfield ) );
                $default_id = ( isset( $data[$field] ) ? $data[$field] : '' );
            }
            
            $res[]= '<div class="input-group">
<input id="'.$table.'_'.$field.'" class="form-control"
    onkeyup="oninputchange( event, this, \''.$fields[$table][$field]['db_select'].'\' );" 
    onblur="getlastvalue(this)" 
    sval="'.$default_title.'" '
    .( $fields[$table][$field]['required'] ? 'required="true"' : '' )
    .' value="'.$default_title.'"'
    .( !empty( $fields[$table][$field]['field_width'] ) ? ' style="width:'.$fields[$table][$field]['field_width'].'px"' : '' ).' > 
<input name="'.$field.'" '.$onchange.' type="hidden" value="'.$default_id.'">
<div class="input-group-btn">
<button type="button" class="btn btn-default" tabindex="-1" onclick="show_dbselect_window(\''.$fields[$table][$field]['db_select'].'\',\''.$table.'_'.$field.'\',\''.$selectfield.'\')">...</button>'
.( $field == 'itemid' ? '<button type="button" class="btn btn-default" tabindex="-1" onclick="showBarcodeSeeker( this ); return false;"><i class="glyphicon glyphicon-barcode"></i></button>' : '' )
.'<button type="button" class="btn btn-default" tabindex="-1" onclick="clear_db_select(this); return false;"> X </button>
</div>
</div>';
        break;
        case 'db_groupselect':
            if ( isset( $_GET['parentid'] ) ){
                if ( empty( $data ) )
                    $data=array();
                $data[$field] = $_GET['parentid'];
            }
            $res[] = '<select class="form-control" name="'.$field.'"'.( !empty( $fields[$table][$field]['field_width'] ) ? ' style="width:'.$fields[$table][$field]['field_width'].'px"' : '' ).'>'
.db_groupselect_options( $fields[$table][$field]['db_select'], $data[$field] ).'</select>';
        break;
        case 'timezone':
            $res[] = '<select class="form-control" name="'.$field.'"'.( !empty($fields[$table][$field]['field_width'] ) ? ' style="width:'.$fields[$table][$field]['field_width'].'px"' : '' ).'>'
.getTimeZones( $data[$field] ).'</select>';
        break;
        case 'db_multiselect':
            $arr = get_multiselect_tablevalues( $fields[$table][$field]['db_selectto'], $fields[$table][$field]['to_field'], $fields[$table][$field]['select_field'], $data['id'] );
            $res[] = '<select multiple class="form-control" name="'.$field.'[]"'
.( !empty($fields[$table][$field]['field_width'] ) ? ' style="width:'.$fields[$table][$field]['field_width'].'px;height:'.$fields[$table][$field]['field_height'].'px"' : '' ).'>'
.db_multiselect_options( $fields[$table][$field]['db_select'], $fields[$table][$field]['selectto_field'], $arr).'</select>';
        break;
        case 'db_multicheckbox':
            $arr = array();
            if ( $data != 0 )
                $arr = get_multiselect_tablevalues( $fields[$table][$field]['db_selectto'], $fields[$table][$field]['to_field'], $fields[$table][$field]['select_field'], $data['id'] );

            $res[] = '<fieldset class="highlight">'
.db_multichechbox_options( $fields[$table][$field]['db_select'], $field, $arr )
.'</fieldset>';
        break;
        case 'db_grid':
            $res[] = '<div id="toolbar-'.$fields[$table][$field]['db_grid'].'">
<a href="javascript:void(0)" class="btn btn-default" onclick="createA(\''.$fields[$table][$field]['db_grid'].'\',\''.$id.'\')">Добавить элемент</a>
<a href"javascript:void(0)" class="btn btn-default" onclick="editA(\''.$fields[$table][$field]['db_grid'].'\')">Изменить</a>
<a href="javascript:void(0)" class="btn btn-default" onclick="deleteA(\''.$fields[$table][$field]['db_grid'].'\')">Удалить</a>
</div><div class="highlight">';

            $res[] = "<table id='table-".$fields[$table][$field]['db_grid']."'></table>
<script>
\$('#table-".$fields[$table][$field]['db_grid']."').myTreeView({
    url:'/company/warehouse/warehouse.php?do=get&table=".$fields[$table][$field]['db_grid']."&idfield=".$fields[$table][$field]['idfield']."&".$fields[$table][$field]['idfield']."=".$id."&nolimit=topgear', 
    headers: [";
            foreach( $fields[$fields[$table][$field]['db_grid']] as $field1 => $v1 ){
                if ( $v1['in_grid'] ){
                    $res[] = "{name:'".$field1."',title:'".$v1['title']."'".( !empty($v1['width'] ) ? ",width:'".$v1['width']."'" : '' )."},";
                }
            }
            $res[] = "],
    tree: false,
    numeration: true,
    dblclick : function (){ editA( '".$fields[$table][$field]['db_grid']."' ); }
});
</script></div>";
        break;
    }
    $res[]='</div></div>';

    return join("\n",$res);
}


// рекурсивный поиск родителей
function get_parents( $id, $table = 's_items' ){
    $answer = $id.',';
    $result = mysql_query( 'SELECT id FROM '.$table.' WHERE parentid='.$id.' AND isgroup=1' );
    if ( $result ){
        if ( mysql_num_rows( $result ) > 0 ){
            while ( $row = mysql_fetch_row( $result ) ){
                $answer .= get_parents( $row[0] );
            }
        }
        return $answer;
    } else return '';
}


// печать калькуляции
function get_calculation( $id, $quantity, $lvl, $lastPrices ){
    $clr = 255 - 8 * ( $lvl - 1 );
    $trbg = ' style="background-color: #'.dechex( $clr ).dechex( $clr ).dechex( $clr ).';"';
    $taright = ' style="text-align: right; min-width: 48px;"';
    $answer = array();
    $answer['lines'] = '';
    $answer['costprice'] = 0;
    $answer['costsum'] = 0;
    
    $joinCosts = 'SELECT itemid, SUM(costsum) / SUM(quantity) AS costprice
FROM r_remainder AS r
GROUP BY itemid';
    if ( $lastPrices )
        $joinCosts = 'SELECT r.itemid, costsum / quantity AS costprice
FROM (
SELECT MAX(id) AS id, itemid
FROM r_remainder
WHERE documenttype IN (1, 3)
GROUP BY itemid
) AS r
LEFT JOIN r_remainder AS r2 ON r2.id=r.id';
    
    $result = mysql_query( 'SELECT tc.itemid, ci.name AS item, ('.$quantity.' * tc.quantity * tc.multip) AS quantity, m.name AS measure, costs.costprice, 
('.$quantity.' * tc.quantity * tc.multip * costs.costprice) AS costsum, IFNULL(tcc.id, 0) AS hasCalculation, tc.loss_cold, tc.loss_hot
FROM t_calculations AS tc
LEFT JOIN (
 SELECT c1.id, c1.itemid, c1.dt
 FROM s_calculations AS c1
 LEFT JOIN (SELECT itemid, MAX(dt) AS dt FROM s_calculations WHERE dt<=NOW() GROUP BY itemid) AS c2 ON c1.itemid=c2.itemid
 WHERE c1.dt=c2.dt
) AS tcc ON tcc.itemid=tc.itemid
LEFT JOIN s_items AS ci ON ci.id=tc.itemid
LEFT JOIN s_units_of_measurement AS m ON m.id=ci.measurement
LEFT JOIN ('.$joinCosts.') AS costs ON costs.itemid=tc.itemid
WHERE tc.calculationid='.$id.' 
ORDER BY tc.calculationid, ci.name' );

    while ( $row = mysql_fetch_array( $result ) ){
        $childs = '';
        $costprice = $row['costprice'];
        $costsum = $row['costsum'];
        
        if ( $row['hasCalculation'] > 0 && $row['hasCalculation'] != $id ){
            $a = get_calculation( $row['hasCalculation'], $row['quantity'], $lvl + 1, $lastPrices );
            
            $childs = $a['lines'];
            $costsum = $a['costsum'];
            $costprice = $costsum / $row['quantity'];
        }
        
        $lossc = $row['quantity'] - $row['quantity'] * $row['loss_cold'] / 100;
        $lossh = $lossc - $lossc * $row['loss_hot'] / 100;
        
        $answer['lines'] .= '<tr'.$trbg.'>
<td style="padding-left: '.( 16 * $lvl ).'px;">'.$row['item'].'</td>
<td'.$taright.'>'.round( $row['quantity'], 3 ).'</td>
<td>'.$row['measure'].'</td>
<td'.$taright.'>'.$row['loss_cold'].'</td>
<td'.$taright.'>'.round( $lossc, 3 ).'</td>
<td'.$taright.'>'.$row['loss_hot'].'</td>
<td'.$taright.'>'.round( $lossh, 3 ).'</td>
<td'.$taright.'>'.round( $costprice, 2 ).'</td>
<td'.$taright.'>'.round( $costsum, 2 ).'</td>
</tr>'
.$childs;
        
        $answer['costsum'] += $costsum;
    }
    
    $answer['costprice'] = $answer['costsum'] / $quantity;
    
    return $answer;
}

// рекурсивный поиск калькуляций
function get_calculation_sql( $id, $quantity, $lastPrices, $docdt, $doctype, $docid, $warehouseid, $clientid, $parentid, $apid = 0, $onlyCostSum = false ){
    $answer = array();
    $answer['lines'] = '';
    $answer['costprice'] = 0;
    $answer['costsum'] = 0;

    $joinCalc = ' SELECT c1.id, c1.itemid, c1.dt
FROM s_calculations AS c1
LEFT JOIN (
 SELECT itemid, MAX(dt) AS dt
 FROM s_calculations
 WHERE dt<='.$docdt.'
 GROUP BY itemid
) AS c2 ON c1.itemid=c2.itemid
WHERE c1.dt=c2.dt';
    if ( $apid > 0 )
        $joinCalc = ' SELECT DISTINCT c1.id, c1.itemid, c1.dt
FROM t_calculations_ap AS ap
LEFT JOIN s_calculations AS c1 ON c1.id=ap.calculationid
LEFT JOIN (
 SELECT itemid, MAX(dt) AS dt FROM s_calculations
 WHERE dt<='.$docdt.'
 GROUP BY itemid
) AS c2 ON c1.itemid=c2.itemid
WHERE c1.dt=c2.dt AND ap.automatedpointid='.$apid;
    
    $joinCosts = 'SELECT itemid, SUM(costsum) / SUM(quantity) AS costprice
FROM r_remainder AS r
WHERE r.dt<'.$docdt.' 
GROUP BY itemid';
    if ( $lastPrices )
        $joinCosts = 'SELECT r.itemid, costsum / quantity AS costprice
FROM (
SELECT MAX(id) AS id, itemid
FROM r_remainder
WHERE documenttype IN (1, 3) AND r.dt<'.$docdt.'
GROUP BY itemid
) AS r
LEFT JOIN r_remainder AS r2 ON r2.id=r.id';
    
    $result = mysql_query( 'SELECT tc.itemid, IFNULL('.$quantity.' * tc.quantity * tc.multip / c.quantity, 0) AS quantity, 
IFNULL('.$quantity.' * tc.quantity * tc.multip / c.quantity * costs.costprice, 0) AS costsum, IFNULL(tcc.id, 0) AS hasCalculation
FROM t_calculations AS tc
LEFT JOIN s_calculations AS c ON c.id=tc.calculationid
LEFT JOIN ('.$joinCalc.') AS tcc ON tcc.itemid=tc.itemid
LEFT JOIN s_items AS ci ON ci.id=tc.itemid
LEFT JOIN s_units_of_measurement AS m ON m.id=ci.measurement
LEFT JOIN ('.$joinCosts.') AS costs ON costs.itemid=tc.itemid
WHERE c.id='.$id
.' ORDER BY tc.calculationid' );



    while ( $row = mysql_fetch_array( $result ) ){
        $childs = '';
        $costsum = $row['costsum'];
        
        if ( $row['hasCalculation'] > 0 && $row['hasCalculation'] != $id  ){
            if ( !$onlyCostSum ){
// сохраняем полуфабрикат
                $query = 'INSERT INTO r_remainder (dt, documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum, parentid) VALUES 
('.$docdt.', '.$doctype.', '.$docid.', '.$warehouseid.', '.$clientid.', '.$row['itemid'].', '.$row['quantity'].', 0, 0, '.$parentid.')';
                mysql_query( $query );
                $last_id = mysql_insert_id();
            }

// получаем ингредиенты полуфабриката и его себестоимость
            $a = get_calculation_sql( $row['hasCalculation'], $row['quantity'], $lastPrices, $docdt, $doctype, $docid, $warehouseid, $clientid, $last_id, $apid );
            
            $childs = $a['lines'];
            $costsum = $a['costsum'];
            //print_r($onlyCostSum); 
            if ( !$onlyCostSum ){
                mysql_query( 'INSERT INTO r_remainder (dt, documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum, parentid) VALUES '.$childs );
                

// изменяем себестоимость полуфабриката
                mysql_query( 'UPDATE r_remainder SET costsum='.$costsum.' WHERE id='.$last_id );

// списываем полуфабрикат
                $query = 'INSERT INTO r_remainder (dt, documenttype, documentid, warehouseid, clientid, itemid, quantity, costsum, salesum, parentid) VALUES 
('.$docdt.', '.$doctype.', '.$docid.', '.$warehouseid.', '.$clientid.', '.$row['itemid'].', -'.$row['quantity'].', -'.$costsum.', '.$costsum.', '.$last_id.')';

                mysql_query( $query );
            }
        } else {
// просто заполняем ингредиенты
            if ( $answer['lines'] != '' ) $answer['lines'] .= ',';
            
            $answer['lines'] .= '('.$docdt.', '.$doctype.', '.$docid.', '.$warehouseid.', '.$clientid.', '.$row['itemid'].', -'.$row['quantity'].', -'.$row['costsum'].', 0, '.$parentid.')'
.$childs;

        }

        $answer['costsum'] += $costsum;
    }
    
    return $answer;

}


// проверка ингредиентов калькуляции на отсутствие кольцевых ссылок, типа чтобы товар из себя не состоял
function checkCircles( $id, $ingredients ){
    $answer = 0;
    
    if ( $ingredients == '' ) $answer == 1;

    $result = mysql_query( 'SELECT c.id FROM s_calculations AS c
LEFT JOIN t_calculations AS tc ON tc.calculationid=c.id
WHERE c.itemid IN ('.$ingredients.') AND tc.itemid='.$id );

    if ( mysql_num_rows( $result ) == 0 ) $answer = 1;

    return $answer;
}


// грууповое проведение счетов в последней смене, эксклюзивно для Элтона Джона (Пеший)
function conductLastChange(){
    $query = 'SELECT id 
FROM d_changes 
WHERE idautomated_point='.$_SESSION['idap'].' 
    AND IF((SELECT divChangeWorkplace FROM s_automated_point WHERE id='.$_SESSION['idap'].') = 1, idworkplace='.$_SESSION['wid'].', true ) 
ORDER BY id DESC LIMIT 1';

    $result = mysql_query( $query );

    if ( mysql_num_rows( $result ) > 0 ){
        $row = mysql_fetch_array( $result );
        $query = 'SELECT id FROM d_order WHERE changeid='.$row['id'].' AND idautomated_point='.$_SESSION['idap'].' AND closed=1 AND conducted=0';
        //echo $query; die;
        $result = mysql_query( $query );

        if ( mysql_num_rows( $result ) > 0 ){
            $t = true;
            
            while ( ($row = mysql_fetch_assoc( $result )) && $t ) 
                $t = conduct( 'd_order', $row['id'] );
        }
    }
}

function getActualDt(){
    $result = mysql_query( 'SELECT `value` FROM s_config WHERE `key`="actual_dt"' );
    if ( mysql_num_rows( $result ) > 0 ){
        $row = mysql_fetch_assoc( $result );
        return $row['value'];
    } else {
        $result = mysql_query( 'SELECT MIN(dt) AS dt FROM r_remainder' );
        if ( mysql_num_rows( $result ) > 0 ){
            $row = mysql_fetch_assoc( $result );
            mysql_query( 'INSERT INTO s_config (`key`, `value`) VALUES ("actual_dt", "'.$row['dt'].'")' );
            return $row['dt'];
        }
    }
}

function setActualDt( $newdt ){
//    if ( getActualTime() )
        mysql_query( 'UPDATE s_config SET `value`="'.$newdt.'" WHERE `key`="actual_dt"' );
//    else
//        mysql_query( 'INSERT INTO s_config (`key`, `value`) VALUES ("actual_dt", "'.$newdt.'")' );
}
?>
