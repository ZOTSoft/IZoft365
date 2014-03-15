<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>test</title>
  </head>
<body>

<?php

function db_array($sql){
    $r = mysql_query($sql);
    $res = array();

    while($row=mysql_fetch_assoc($r)){
        $res[] = $row;
    }
    return $res;
}

include('../company/mysql_connect.php');

$source_base = 'db_Paloma';
$target_base = 'db_zot1';

$raw_source_tables = db_array("SHOW TABLES FROM $source_base");
$raw_target_tables = db_array("SHOW TABLES FROM $target_base");


foreach($raw_source_tables as $r) $source_tables[] = array_pop($r);
foreach($raw_target_tables as $r) $target_tables[] = array_pop($r);

foreach($source_tables as $tbl_name)
{
    if(in_array($tbl_name, $target_tables))
    {
        $raw_source_columns = db_array("SHOW COLUMNS FROM `$tbl_name` FROM `$source_base`");
        $raw_target_columns = db_array("SHOW COLUMNS FROM `$tbl_name` FROM `$target_base`");

        if($raw_source_columns !== $raw_target_columns){
            echo "Таблица <b>$tbl_name:</b> <br />";
            // check columns
            unset($source_columns);
            unset($target_columns);
            
            foreach($raw_source_columns as $r) $source_columns[$r['Field']] = $r;
            foreach($raw_target_columns as $r) $target_columns[$r['Field']] = $r;
            
            foreach($source_columns as $col_name => $column)
            {
                unset($sql);
                if(@$target_columns[$col_name] !== $source_columns[$col_name]){
                    echo "\n &nbsp; &nbsp;поле: <b>$col_name</b> ";
                    
                    if(empty($target_columns[$col_name]))
                    {
                        // create column
                        echo "не существует<br />";
                        //$sql = "ALTER TABLE `$target_base`.`$tbl_name` ADD COLUMN `$col_name` ";
                    }
                    else
                    {
                        // modify column
                        echo "не соответсвует тип";
                        
                        $null = ('YES' == $column['Null'])? 'NULL': 'NOT NULL';
                        
                        $default = $column['Default'];
                        if('' === $default) $default = "''";
                        if(null === $default) $default = 'NULL';
                        
                        
                        $null2 = ('YES' == $target_columns[$col_name]['Null'])? 'NULL': 'NOT NULL';
                        
                        $default2 = $target_columns[$col_name]['Default'];
                        if('' === $default2) $default2 = "''";
                        if(null === $default2) $default2 = 'NULL';
                        

                            
                        echo ' надо: '.$column['Type']." $null DEFAULT $default)   &nbsp;&nbsp;&nbsp;";
                        echo ' а стоит : '.$target_columns[$col_name]['Type']." $null2 DEFAULT $default2) <br />  ";
                        //$sql = "ALTER TABLE `$target_base`.`$tbl_name` CHANGE COLUMN `$col_name` `$col_name` ";
                    }
                    
                    

                    //echo " ($sql)<br />";
                    
                }
            }
        }
    }
    else
    {
        // create tbl
        echo "Таблицы <b>$tbl_name</b> не существует <br />";
        
        //$tbl = mysql_fetch_assoc(mysql_query("SHOW CREATE TABLE `$source_base`.`$tbl_name`;"));
        //$sql = $tbl['Create Table'];
        //$sql = str_replace("`$tbl_name`", "`$target_base`.`$tbl_name`", $sql);
        //mysql_query($sql);
    }
}

?>
</body>
</html>