<?php
  
include('../company/mysql_connect.php');

function db_array($sql){
    $r = mysql_query($sql);
    $res = array();

    while($row=mysql_fetch_assoc($r)){
        $res[] = $row;
    }
    return $res;
}

$col_name='idout';
$query=mysql_query("select schema_name from information_schema.schemata where schema_name like 'db\_%'");
while($row=mysql_fetch_array($query)){

    $target_base = $row['schema_name'];
    
    $raw_target_tables = db_array("SHOW TABLES FROM $target_base");
    
    foreach($raw_target_tables as $tbl_name) {
        //$tbl_name=
        $tbl_name=$tbl_name['Tables_in_'.$target_base];
        $raw_target_columns = db_array("SHOW COLUMNS FROM `$tbl_name` FROM `$target_base`");
        unset($target_columns);
        foreach($raw_target_columns as $r) $target_columns[$r['Field']] = $r;
        if(!empty($target_columns[$col_name])){
            if ($tbl_name!='d_order')
            mysql_query("UPDATE `$target_base`.`$tbl_name` SET `$col_name`=`id`");
        }
            
    }
}

?>
