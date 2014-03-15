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


$query=mysql_query("select schema_name from information_schema.schemata where schema_name like 'db\_%'");
while($row=mysql_fetch_array($query)){
    $base=$row['schema_name'];
    
        $q=mysql_query("SELECT * FROM `".$base."`.s_automated_point WHERE expiration_date is null");
        while($row=mysql_fetch_assoc($q)){
           $q2=mysql_query("SELECT * FROM `".$base."`.d_changes WHERE idautomated_point=".$row['id']." ORDER by id ASC LIMIT 1");
           if (mysql_numrows($q2)){
               $row2=mysql_fetch_array($q2);
               mysql_query("UPDATE `".$base."`.s_automated_point SET expiration_date='".$row2['dtopen']."' WHERE id=".$row['id']);
           }
        }
        
        $q=mysql_query("SELECT * FROM `".$base."`.t_workplace WHERE expiration_date is null");
        while($row=mysql_fetch_assoc($q)){
           $q2=mysql_query("SELECT * FROM `".$base."`.d_changes WHERE idautomated_point=".$row['apid']." ORDER by id ASC LIMIT 1");
           if (mysql_numrows($q2)){
               $row2=mysql_fetch_array($q2);
               mysql_query("UPDATE `".$base."`.t_workplace SET expiration_date='".$row2['dtopen']."' WHERE id=".$row['id']);
           } 
        }
            
    
}

?>
