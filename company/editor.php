<?php
$editor=array();
$editor['s_automated_point']=1;
$editor['s_employee']=1;

function form_design($table,$data,$id=0){
    $res=array();
    
    switch($table){
        case 's_automated_point': include('editor/s_automated_point.php');break;
        case 's_employee': include('editor/s_employee.php');break;
        
    }
return join("\n",$res);
}


function show_form(){
$ret=array();
            $check=$_GET['table'];
            if ($_GET['table']=='s_menu_items') $check='show_design_menu';
            if ($_GET['table']=='t_menu_items') $check='show_design_menu';
            if (!checkrights($check,2)) die(PERMISSION_DENIED);
 
            
            $data=array();
            $tablename=$_GET['table'];
            if (isset($_GET['edit'])&&isset($_GET['id'])) {
                $query=mysql_query("SELECT * FROM `".addslashes($_GET['table'])."` WHERE id='".$_GET['id']."' LIMIT 1");
                $data=mysql_fetch_assoc($query);
                
                $ret[]='<div class="formp"><form role="form" class="form-horizontal" id="form_edit-'.$tablename.'" method="post" action="/company/ajax.php?do=edit&table='.$tablename.'&id='.$_GET['id'].'" novalidate>';          
            }else{
                $ret[]= '<div class="formp"><form role="form" class="form-horizontal" id="form-'.$tablename.'" method="post" action="/company/ajax.php?do=add&table='.$tablename.'&parentid='.$_GET['parentid'].'" novalidate>';      
            }
            $ret[]= form_design($_GET['table'],$data);
            
            if (!empty($_GET['orderid'])){
                $ret[]= '<input type="hidden" name="orderid" value="'.$_GET['orderid'].'">';
            }
            if (!empty($_GET['iddoc'])&&($tablename=='t_workplace')){
                $ret[]= '<input type="hidden" name="apid" value="'.$_GET['iddoc'].'">';
            }
            if (isset($_GET['itemid'])){
                $ret[]= '<input type="hidden" name="itemid" value="'.$_GET['itemid'].'">';
            }
                
                
            $ret[]= '<input type="hidden" name="isgroup" value="0">
                
             
                
                </form></div>';    
            return join("\n",$ret);
}








?>