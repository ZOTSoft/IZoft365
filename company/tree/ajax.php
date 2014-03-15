<?PHP  header("Content-Type: text/html; charset=utf-8");
session_start();
include('../check.php');
checksessionpassword();
include('../mysql.php');

include('../errors.php');
include('../tables.php');
include('../functions.php');
include('../editor.php');
include('../core.php');
include('../templates.php');
error_reporting(E_ALL ^ E_NOTICE);

if (isset($_SESSION['timezone'])){ 
    date_default_timezone_set($_SESSION['timezone']); 
    mysql_query("SET `time_zone` = '".date('P')."'"); 
}

switch($_GET['do']){
 case 'newfuckingget': 
                
                if (!checkrights($_GET['table'],1)) die(PERMISSION_DENIED);
                $res=array();
                $treeItems = '';
                
                $parentid = isset($_POST['parentid']) ? intval($_POST['parentid']) : 0;  
                if ($table[$_GET['table']]['create_group']==true)
                    if ($parentid > 0) $res = getParents($_GET['table'],$parentid);  

                //если вызвано из примечания
                if (isset($_GET['note'])&&(!isset($_POST['id']))){
                    $a=array();
                    $a['id']=-1;
                    $a['name']='Причины отказов';
                    $a['isgroup']=0; 
                    $a['parentid']=0; 
                    $res[]=$a;
                    $a['id']=-2;
                    $a['name']='Общие';
                    $res[]=$a;
                }
                    
                
                //страшная штука orderid   
                $iddoc = isset($_GET['orderid']) ? intval($_GET['orderid']) : 0;       
                $table = isset($_GET['table']) ?  $_GET['table'] : '';     
                $iddocstr='';
                
                
                
                //$fields[$table][$field]['idfield']
                
                if (isset($_GET['idfield'])){
                    $iddocstr = isset($_GET[$_GET['idfield']]) ? ' AND `'.$_GET['idfield'].'`="'.intval($_GET[$_GET['idfield']]).'"' : ' AND `'.$_GET['idfield'].'` is null';  
                }
                
              /*  if (isset($_GET['orderid'])){
                    $iddocstr=" AND orderid=".$iddoc;  
                    if ($iddoc==0) $iddocstr=' AND orderid is null';  
                }
                
                if (isset($_GET['discountid'])){
                    $iddocstr=" AND discountid=".addslashes($_GET['discountid']);  
                    if ($_GET['discountid']==0) $iddocstr=' AND discountid is null';  
                }
                
                if (isset($_GET['apid'])){
                    $iddoc = isset($_GET['apid']) ? intval($_GET['apid']) : 0;  
                    $iddocstr=" AND apid=".$iddoc;  
                    if ($iddoc==0) $iddocstr=' AND apid is null';  
                }*/
                $page = isset($_POST['page']) ? intval($_POST['page']) : 1;  
                $rowcount = isset($_POST['rows']) ? intval($_POST['rows']) : 5000; 
                $offset = ($page - 1) * $rowcount;
                
                $filter=array();
                if (!empty($_POST['filter']) && $_POST['filter']!=null){
                    foreach($_POST['filter'] as $v){
                        if ($v['value']!=''){
                            if (is_numeric($v['value']))
                                $filter[]='`'.$v['name'].'`="'.addslashes($v['value']).'"';
                            else
                                $filter[]='UPPER(`'.$v['name'].'`) LIKE UPPER("%'.addslashes($v['value']).'%")';
                        }
                    }
                }
                
                $parentid=" parentid='".addslashes((isset($_GET['id']))?$_GET['id']:$parentid)."' ";
                if ($table=='d_order') $parentid=' 1=1 ';
                                
                if (isset($_GET['noparent'])) $parentid=' 1=1 ';
                
                if ($table=='s_employee'){
                   $querytr=mysql_query("SELECT EM.*,EI.* FROM `".addslashes($table)."` AS EM LEFT JOIN (SELECT MIN(`rollid`)AS rollid, employeeid FROM `t_employee_interface` GROUP BY `employeeid`) as EI ON EI.employeeid=EM.id  WHERE   (EI.rollid > '1' OR EI.rollid is null) AND ".(!empty($filter)?join(' AND ',$filter):$parentid.addslashes($iddocstr)));
                }
                else{
                if($table=='z_logs'){
                     $querytr=mysql_query("SELECT id FROM `".addslashes($table)."`WHERE type<1300".(!empty($filter)?' AND '.join(' AND ',$filter):''));
                }else
                    $querytr=mysql_query("SELECT id FROM `".addslashes($table)."` WHERE  ".(!empty($filter)?join(' AND ',$filter):$parentid.$iddocstr)."");
                    
                    
                   
                }
                //echo "SELECT id FROM `".addslashes($table)."` WHERE  ".(!empty($filter)?join(' AND ',$filter):$parentid.$iddocstr)."";
                
               
                $totalrows=mysql_numrows($querytr);
                
                $zorder=',UPPER(`name`)';
                if ($table=='d_order') $zorder=',`id`';
                
                
                if ($table=='s_employee'){
                    $pipiska=getMyInterface($_SESSION['userid']);
                    $query=mysql_query("SELECT EM.*,EI.* FROM `".addslashes($table)."` AS EM LEFT JOIN (SELECT MIN(`rollid`)AS rollid, employeeid FROM `t_employee_interface` GROUP BY `employeeid`) as EI ON EI.employeeid=EM.id  WHERE   (EI.rollid > '".$pipiska."' OR EI.rollid is null) AND ".(!empty($filter)?join(' AND ',$filter):$parentid.addslashes($iddocstr))." ORDER by isgroup DESC".$zorder." ".(isset($_GET['nolimit'])?'':" LIMIT ".$offset.",".$rowcount));
                }else{
                if($table=='z_logs'){
                     $query=mysql_query("SELECT * FROM `".addslashes($table)."` WHERE type<1300 ".(!empty($filter)?' AND '.join(' AND ',$filter):'')."   ORDER by id DESC LIMIT ".$offset.",".$rowcount);
                }
                else
                    
                    $query=mysql_query("SELECT * FROM `".addslashes($table)."` WHERE  ".(!empty($filter)?join(' AND ',$filter):$parentid.$iddocstr)." ORDER by isgroup DESC".$zorder." ".(isset($_GET['nolimit'])?'':" LIMIT ".$offset.",".$rowcount));
                    
                  
                }
                //echo "SELECT * FROM `".addslashes($table)."` WHERE  ".(!empty($filter)?join(' AND ',$filter):$parentid.$iddocstr)." ORDER by isgroup DESC".$zorder." ".(isset($_GET['nolimit'])?'':" LIMIT ".$offset.",".$rowcount);
                
                 
                 
                 
                while($row=mysql_fetch_array($query)){
                    $a=array();
                    foreach($fields[$table] as $k=>$v){
                            if (($row['isgroup']==1)&&($v['in_group']==1)||($row['isgroup']==0)||($table=='z_logs')){
                                
                                switch($v['type']){
                                    case 'input':
                                       $a[$k]=$row[$k];
                                       break;
                                    case 'datetime':
                                       $a[$k]=date('d.m.Y H:i:s',strtotime($row[$k]));
                                       break;
                                    case 'date':
                                       $a[$k]=$row[$k];
                                       break;
                                    case 'time':
                                       $a[$k]=$row[$k];
                                       break;
                                    case 'barcode':
                                       $a[$k]=$row[$k];
                                       break;
                                    case 'itemmenu':
                                       $a[$k]=getNameById('s_items',$row['itemid']);
                                       break;
                                    case 'password':
                                       $a[$k]='';
                                       break;
                                    case 'db_select':
                                        $selectfield='name';
                                        if (isset($v['selectfield'])) $selectfield=$v['selectfield'];
                                       $a[$k]=get_select_val($v['db_select'],$row[$k],$selectfield);
                                       break;  
                                    case 'db_groupselect':
                                       $a[$k]=get_select_val($v['db_select'],$row[$k]);
                                       break;
                                    case 'db_multiselect':
                                       $a[$k]=get_multiselect_val($v['db_select'],$row[$k]);
                                       break; 
                                    case 'checkbox':
                                       $a[$k]=($row[$k]==1?'Да':'Нет');
                                       break;  
                                    case 'db_grid':
                                       $a[$k]=get_grid($v['db_grid'],$row[$k],$v['idfield']);
                                       break; 
                                    case 'timezone':
                                       $a[$k]=getTimeZoneValue($row[$k]);
                                       break;
                                    case 'logtype':
                                       $a[$k]=$loger[$row[$k]];
                                       break;
                                    case 'logdesc':
                                       $a[$k]=getLogDesc($row[$k],$row['type'],$row['userid']);
                                       break;
                                }
                            }
                            $a['id']=$row['id'];
                        if (!empty($v['after_text'])){
                           $a[$k].=$v['after_text']; 
                        }
                    }
                    
                    if (isset($row['idout'])){
                        $a['idout']=$row['idout'];
                    }
                    
                    if (!empty($a['price'])){
                        $a['price']=round($a['price']);
                        if ($row['isgroup']) $a['price']=''; 
                    }
                    
                    
                    
                    
                   
                    $a['isgroup']=$row['isgroup']; 
                    $a['parentid']=$row['parentid']; 
                    $a['editable']=1; 
                    
                    $res[]=$a;
                
                   
                }
                /*if ($parentid==0)
                    $treeItems = '<ol><li><label idgroup="0" parentid="-1" for="subfolder0" class="selected">Корень</label>
<input type="checkbox" checked="checked" id="subfolder0"><ol>'.$treeItems.'</ol></li></ol>';
                else
                    $treeItems = '<ol>'.$treeItems.'</ol>';*/
            
                $answer = array();
                $answer["totalrows"] = $totalrows;
                $answer["rows"] = $res;
                echo json_encode($answer);
        break;
        
               //получение параметров таблицы
        case 'gettableazorchik': 
                if (!checkrights($_POST['table'],1)) die(PERMISSION_DENIED);
                $tablename = isset($_POST['table']) ? ($_POST['table']) : ''; 
                $res=array();
                $res['name']=$tablename;
                $res['title']=$tables[$tablename]['name'];
                $res['create_group']=$tables[$tablename]['create_group'];
                $res['width']=$tables[$tablename]['width'];
                $res['height']=$tables[$tablename]['height'];
                $res['create_group']=($tables[$tablename]['create_group']?true:false);
                
                
                
                $res['rights']=array('view'=>checkrights($tablename,1),'edit'=>checkrights($tablename,2),'add'=>checkrights($tablename,3),'deletez'=>checkrights($tablename,4),'print'=>checkrights($tablename,5));
                
                if ($tablename=='d_order'){
                    $res['rights']['edit']=false;
                    $res['rights']['deletez']=false;
                    $res['rights']['add']=false;
                }
                
                if ($tablename=='s_config'){
                    $res['rights']['edit']=true;
                    $res['rights']['deletez']=false;
                    $res['rights']['add']=false;
                }
                
                if ($tablename=='s_automated_point'){
                    $res['rights']['edit']=false;
                    $res['rights']['deletez']=false;
                    $res['rights']['add']=false;
                    if (isadmin($_SESSION['userid'])){ 
                        $res['rights']['edit']=true;
                        $res['rights']['deletez']=true;
                        $res['rights']['add']=true;
                    }
                }
                
                
                if (isadmin($_SESSION['userid'])){
                   if ($tablename=='d_order'){
                      $res['rights']['deletez']=true; 
                   }
                         
                }
                
                $res['filter']=getfilterfields($tablename);
                
                
                
                foreach($fields[$tablename] as $k=>$v){
                    if ($v['in_grid']){
                        $f=array();
                        $f['name']=$k;
                        foreach($v as $key=>$value){
                            if ($key!='width')
                            $f[$key]=$value;
                        }
                        
                        $res['fields'][]=$f;
                    }
                     
                }
                
                echo json_encode($res);
        break;
        //добавление группы и элемента в базу
        case 'add':
       
            if (!checkrights($_GET['table'],3)) { echo json_encode(array('rescode'=>1,'resmsg'=>PERMISSION_DENIED)); die();}
            if(isset($_SESSION['last_ins'])&&(time()-$_SESSION['last_ins']<3)){
                echo 'Dublicate!';
            }else{
                
                
                
                $tablename =  isset($_GET['table']) ? $_GET['table'] : '';
                $parentid = isset($_GET['parentid']) ? $_GET['parentid'] : '0';
                $arr=array();
                

                
                
                //проверка на уникальность
                    $check=false;
                    $check_message='';
                    
                   // пароли между сотрудниками
                  //  логин - рм урв сотрудн

                        if (($tablename=='t_workplace') || ($tablename=='s_pointurv')){
                            $query=mysql_query("SELECT id FROM `s_pointurv` WHERE `login`='".addslashes($_POST['login'])."'");
                            if (mysql_numrows($query)){
                                $check=true;
                                $check_message='Данный логин уже используется';
                            }
                            $query=mysql_query("SELECT id FROM `t_workplace` WHERE `login`='".addslashes($_POST['login'])."'");
                            if (mysql_numrows($query)){
                                $check=true;
                                $check_message='Данный логин уже используется';
                            }
                            $query=mysql_query("SELECT id FROM `s_employee` WHERE `name`='".addslashes($_POST['login'])."' AND `name`!='' AND isgroup=0");
                            if (mysql_numrows($query)){
                                $check=true;
                                $check_message='Данный логин уже используется';
                            }
                        }
                        
                        if (($tablename=='s_employee')){
                            $query=mysql_query("SELECT id FROM `s_pointurv` WHERE `login`='".addslashes($_POST['name'])."'");
                            if (mysql_numrows($query)){
                                $check=true;
                                $check_message='Данный логин уже используется';
                            }
                            $query=mysql_query("SELECT id FROM `t_workplace` WHERE `login`='".addslashes($_POST['name'])."'");
                            if (mysql_numrows($query)){
                                $check=true;
                                $check_message='Данный логин уже используется';
                            }
                            $query=mysql_query("SELECT id FROM `s_employee` WHERE `name`='".addslashes($_POST['name'])."' AND `name`!='' AND isgroup=0");
                            if (mysql_numrows($query)){
                                $check=true;
                                $check_message='Данный логин уже используется';
                            }
                        }
                       
                        if ($tablename=='s_employee'){
                            $query=mysql_query("SELECT id FROM `".$tablename."` WHERE `password`='".md5(FISH.md5($_POST['password']))."' AND isgroup=0");
                            if (mysql_numrows($query)){
                                $check=true;
                                $check_message='Данный пароль уже используется';
                            }
                        }
                        
                         if ($tablename=='s_items'){
                             if ($_POST['mainShtrih']!=''){
                                $query=mysql_query("SELECT mainShtrih FROM `".$tablename."` WHERE `mainShtrih`='".addslashes($_POST['mainShtrih'])."'");
                                if (mysql_numrows($query)){
                                    $check=true;
                                    $check_message='Данный штрихкод уже используется!';

                                }
                             }
                        }
                //проверка на уникальность
               if ($check){
                    echo json_encode(array('rescode'=>  1,'resmsg'=>  $check_message));die;
               }else{
                
                
                //if (empty($_POST['idout'])){
                    $_POST['idout']=getLastIdout($tablename);
                //}else{
                //     $_POST['idout']=checkIdout($_GET['table'],$_POST['idout']);  
                //}
/*                asdasd
                if(isset($_POST['password'])){
                    if ($_POST['password']==''){
                        unset($_POST['password']);
                    }else{
                        $_POST['password']=md5(FISH.md5($_POST['password']));
                    }
                }*/
                
                $ms=array();
                $ur=array();
                $defaults=array();
                foreach ($_POST as $k=>$v){
                    if($fields[$tablename][$k]['type']=='db_multicheckbox'){
                        $ms[$k]=$_POST[$k];
                    }else 
                    if($k=='defaults'){
                        $defaults=$v;
                    }else
                    if($fields[$tablename][$k]['type']=='userrights'){
                        $ur[$k]=$_POST[$k];
                    }else
                    if ($fields[$tablename][$k]['type']=='password'){
                        if ($v!='') $arr[]='`'.addslashes($k)."`='".md5(FISH.md5($v))."'";
                    }else 
                    if ($fields[$tablename][$k]['type']=='date'){
                        $arr[]='`'.addslashes($k)."`='".date('Y-m-d',strtotime($v))."'";
                        //$_POST['cookie_key']='';
                    } 
                    else
                    if ($fields[$tablename][$k]['type']=='datetime'){
                        $arr[]='`'.addslashes($k)."`='".date('Y-m-d H:i:s',strtotime($v))."'";
                        //$_POST['cookie_key']='';
                    }
                    else{
                        if ($k=='parentid'){
                            $parentid=(int)$v;
                        }else{
                            if (isset($fields[$tablename][$k]['valuetype'])){
                                switch($fields[$tablename][$k]['valuetype']){
                                    case 'float': 
                                        $v=str_replace(',','.',$v);
                                        $v=str_replace(' ','',$v);
                                        $v=floatval($v);
                                    break;
                                    case 'int':
                                        $v=str_replace(',','.',$v);
                                        $v=str_replace(' ','',$v);
                                        $v=intval($v);
                                    break;
                                }
                            }
                            
                            $arr[]='`'.addslashes($k)."`='".addslashes($v)."'";
                        }
                    }
                }
                
                
                
                if (($tablename=='s_clients') && isset($_POST['shtrih']) && ($_POST['shtrih']<>''))
                {     
                    $query=mysql_query('select id from s_clients where shtrih="'.addslashes($_POST['shtrih']).'"');         
                   if (mysql_num_rows($query)>0)
                   {echo json_encode(array('rescode'=>  1,'resmsg'=>  'Клиент с таким кодом карты уже сушествует'));}
                                      
                }
               
                mysql_query("INSERT into `".addslashes($tablename)."` SET parentid='".addslashes($parentid)."', ".join(',',$arr));
                //echo "INSERT into `".addslashes($tablename)."` SET parentid='".addslashes($parentid)."', ".join(',',$arr);
                $_SESSION['last_ins']=time();
                
                $last_id=mysql_insert_id();
                
                if (isset($ms)){
                    foreach($ms as $k=>$v){
                        foreach($v as $item){
                            if ($item>0)
                                mysql_query("INSERT into `".addslashes($fields[$tablename][$k]['db_selectto'])."` SET ".addslashes($fields[$tablename][$k]['select_field'])."='".addslashes($item)."',".addslashes($fields[$tablename][$k]['to_field'])."='".addslashes($last_id)."'");  
                        }
                    }  
                }
                
                
                if (isset($defaults)){
                    foreach($defaults as $k=>$v){
                            if ($v>0)
                                mysql_query("INSERT into `z_default_config` SET default_id='".addslashes($last_id)."', conf_id='".addslashes($k)."', `value`='".addslashes($v)."'");  
                                 
                    }  
                }
                
/*                if (isset($ms)){
                    foreach($ms as $k=>$v){
                        foreach($v as $item){
                            if ($item>0)
                                mysql_query("INSERT into `".addslashes($fields[$tablename][$k]['db_selectto'])."` SET ".addslashes($fields[$tablename][$k]['select_field'])."='".addslashes($item)."',".addslashes($fields[$tablename][$k]['to_field'])."='".addslashes($last_id)."'");  
                        }
                    }  
                }*/
                
                
                $query=mysql_query("SELECT * FROM `".addslashes($tablename)."` WHERE id='".addslashes($last_id)."' LIMIT 1");
                
                $row=mysql_fetch_assoc($query);
                
                zlog(json_encode(array(
                            'table'=>$tablename,
                            'row'=>$row 
                )),1001); 
                
                //добавление подэлементов
                if($tablename=='s_order'){
                    mysql_query("UPDATE `t_order` SET orderid='".addslashes($last_id)."' WHERE orderid is null");  
                }
                if($tablename=='t_workplace'){
                    mysql_query("UPDATE `t_workplace` SET apid='".addslashes($last_id)."' WHERE apid is null");  
                }
                if($tablename=='s_calculations'){
                    mysql_query("UPDATE `t_calculations` SET calculationid='".addslashes($last_id)."' WHERE calculationid=0");  
                }
                

                foreach($row as $k=>$v){
                    if (!empty($fields[$tablename][$k]['type']))
                    switch ($fields[$tablename][$k]['type']){
                        case 'db_select': $row[$k]=get_select_val($fields[$tablename][$k]['db_select'],$v); break; 
                        case 'db_grid': $row[$k]=get_grid($fields[$tablename][$k]['db_grid'],$v); break; 
                        case 'checkbox': $row[$k]=($row[$k]==1?'Да':'Нет'); break;
                    }
                    if (!empty($fields[$tablename][$k]['after_text'])){
                        $row[$k].=$fields[$tablename][$k]['after_text'];
                    }
                }
                
                if ($row['isgroup']==1) $row['state']='closed'; else $row['state']='open';
                echo json_encode($row);
               }
            }
        break;
        //изменение группы и элемента в базе
        case 'edit':
            $check=$_GET['table'];
            if ($_GET['table']=='s_menu_items') $check='show_design_menu';
            if ($_GET['table']=='t_menu_items') $check='show_design_menu';
            if (!checkrights($check,2)) die(PERMISSION_DENIED);
            $arr=array();
            
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            $tablename = isset($_GET['table']) ? $_GET['table'] : '';
            
            if(!empty($_POST['idout']))
                $_POST['idout']=checkIdout2Edit($tablename,$id,$_POST['idout']);
            
            /*asdasd
            if(isset($_POST['password'])){
                if ($_POST['password']==''){
                    unset($_POST['password']);
                }else{
                    $_POST['password']=md5(FISH.md5($_POST['password']));
                    $_POST['cookie_key']='';
                }
            }*/
            
            $check=false;
                    $check_message='';
            $fieldsra=addslashes($_POST['login']);
            if ($tablename=='s_employee') $fieldsra=addslashes($_POST['name']);
             if (($tablename=='t_workplace') || ($tablename=='s_pointurv') || ($tablename=='s_employee')){
                            $wherenot=''; if ($tablename=='s_pointurv') $wherenot=' AND `id`!="'.$id.'"';
                            $query=mysql_query("SELECT id FROM `s_pointurv` WHERE `login`='".$fieldsra."'".$wherenot);
                            if (mysql_numrows($query)){
                                $check=true;
                                $check_message='Данный логин уже используется в УРВ';
                            }
                            $wherenot=''; if ($tablename=='t_workplace') $wherenot=' AND `id`!="'.$id.'"';
                            $query=mysql_query("SELECT id FROM `t_workplace` WHERE `login`='".$fieldsra."'".$wherenot);
                            if (mysql_numrows($query)){
                                $check=true;
                                $check_message='Данный логин уже используется в РМ';
                            }
                            $wherenot=''; if ($tablename=='s_employee') $wherenot=' AND `id`!="'.$id.'"';
                            $query=mysql_query("SELECT id FROM `s_employee` WHERE `name`!='' AND `name`='".$fieldsra."'".$wherenot);
                            if (mysql_numrows($query)){
                                $check=true;
                                $check_message='Данный логин уже используется в П';
                            }
                        }
                        
                                             
                        if ($tablename=='s_employee'){
                            if ($_POST['password']!='zottig'){
                                $query=mysql_query("SELECT id FROM `".$tablename."` WHERE `password`='".md5(FISH.md5($_POST['password']))."' AND id!='".$id."'");
                                if (mysql_numrows($query)){
                                    $check=true;
                                    $check_message='Данный пароль уже используется';
                                    //$check_message="SELECT id FROM `".$tablename."` WHERE `password`='".md5(FISH.md5($_POST['password']))."' AND id!='".$id."'";
                                }
                            }
                        }
                        
                        
                        if ($tablename=='s_items'){
                            if ($_POST['mainShtrih']!=''){
                                $query=mysql_query("SELECT mainShtrih FROM `".$tablename."` WHERE `mainShtrih`='".addslashes($_POST['mainShtrih'])."' AND id!='".$id."'");
                                if (mysql_numrows($query)){
                                    $check=true;
                                    $check_message='Данный штрихкод уже используется';
                                    //$check_message="SELECT id FROM `".$tablename."` WHERE `password`='".md5(FISH.md5($_POST['password']))."' AND id!='".$id."'";
                                }
                            }
                        }
                //проверка на уникальность
               if ($check){
                    echo json_encode(array('rescode'=>  1,'resmsg'=>  $check_message));die;
               }else{
            
            $ms=array();
            $ur=array();
            $defaults=array();
            foreach ($_POST as $k=>$v){
                if($fields[$tablename][$k]['type']=='db_multicheckbox'){
                    $ms[$k]=$_POST[$k];
                }else  
                if($k=='defaults'){
                    $defaults=$v;
                    /*print_r($_POST[$k]);
                    print_r($v);*/
                }else 
                if($fields[$tablename][$k]['type']=='userrights'){
                    $ur[$k]=array('val'=>$_POST[$k],'table'=>$fields[$tablename][$k]['table']);
                }else 
                if ($fields[$tablename][$k]['type']=='password'){
                    if ($v!='') {
                        if ($v!='zottig'){
                            $arr[]=addslashes($k)."='".md5(FISH.md5($v))."'";
                        }
                    }else{
                        $arr[]=addslashes($k)."=''"; 
                    }
                    //$_POST['cookie_key']='';
                }else 
                if ($fields[$tablename][$k]['type']=='date'){
                    $arr[]=addslashes($k)."='".date('Y-m-d',strtotime($v))."'";
                    //$_POST['cookie_key']='';
                }else
                if ($fields[$tablename][$k]['type']=='datetime'){
                    $arr[]='`'.addslashes($k)."`='".date('Y-m-d H:i:s',strtotime($v))."'";
                        //$_POST['cookie_key']='';
                } 
                else{
                    if (isset($fields[$tablename][$k]['valuetype'])){
                        switch($fields[$tablename][$k]['valuetype']){
                            case 'float': 
                                $v=str_replace(',','.',$v);
                                $v=str_replace(' ','',$v);
                                $v=floatval($v);
                            break;
                            case 'int':
                                $v=str_replace(',','.',$v);
                                $v=str_replace(' ','',$v);
                                $v=intval($v);
                            break;
                        }
                    }
                
                    $arr[]=addslashes($k)."='".addslashes($v)."'";
                
                }
                
            }
                    
            
            
            if (isset($ms)){
                    foreach($ms as $k=>$v){
                        mysql_query("DELETE FROM `".addslashes($fields[$tablename][$k]['db_selectto'])."` WHERE ".addslashes($fields[$tablename][$k]['to_field'])."='".addslashes($id)."'");  
                        foreach($v as $item){
                            if ($item>0)
                                mysql_query("INSERT into `".addslashes($fields[$tablename][$k]['db_selectto'])."` SET ".addslashes($fields[$tablename][$k]['select_field'])."='".addslashes($item)."',".addslashes($fields[$tablename][$k]['to_field'])."='".addslashes($id)."'");    
                        }
                    }  
            }
            
            if (isset($defaults)){
                    mysql_query("DELETE FROM `z_default_config` WHERE default_id='".addslashes($id)."'");  
                    foreach($defaults as $k=>$v){
                                if ($v>0)
                                    mysql_query("INSERT into `z_default_config` SET default_id='".addslashes($id)."', conf_id='".addslashes($k)."', `value`='".addslashes($v)."'");  
                    }  
                }
            
            if ($tablename=='s_employee')
                mysql_query("DELETE FROM `z_user_right` WHERE uid='".addslashes($id)."'");  
            if (isset($ur)){
                    foreach($ur as $k2=>$v2){
                        
                        
                        foreach($v2['val'] as $k=>$v){
                            
                            $f=array();    
                            foreach($v as $kf=>$vf){
                                $f[]="`".addslashes($kf)."`='1'";    
                            }
                                mysql_query("INSERT into `z_user_right` SET uid='".addslashes($id)."',`table`='".$v2['table']."',`tid`='".addslashes($k)."',".join(',',$f));    
                        }
                    }  
            }
            
           
           //echo "UPDATE `".addslashes($tablename)."` SET ".join(',',$arr)." WHERE id='".addslashes($id)."'"; die;                 
            mysql_query("UPDATE `".addslashes($tablename)."` SET ".join(',',$arr)." WHERE id='".addslashes($id)."'");
            
            s_menu_lastupdate($tablename,$id);
            
            $query=mysql_query("SELECT * FROM `".addslashes($tablename)."` WHERE id='".addslashes($id)."' LIMIT 1");
            $row=mysql_fetch_assoc($query);
            

            zlog(json_encode(array(
                            'table'=>$tablename,
                            'row'=>$row 
                )),1002); 

            foreach($row as $k=>$v){
                if (!empty($fields[$tablename][$k]['type']))
                switch ($fields[$tablename][$k]['type']){
                    case 'db_select': $row[$k]=get_select_val($fields[$tablename][$k]['db_select'],$v); break; 
                    case 'db_grid': $row[$k]=get_grid($fields[$tablename][$k]['db_grid'],$v); break; 
                    case 'checkbox': $row[$k]=($row[$k]==1?'Да':'Нет'); break;
                }
                 if (!empty($fields[$tablename][$k]['after_text'])){
                    $row[$k].=$fields[$tablename][$k]['after_text'];
                }
            }
            
            if ($row['isgroup']==1) $row['state']='closed'; else $row['state']='open';
            echo json_encode($row);
               }
        break;
        
        case 'delete':
            $check=$_POST['table'];
            if ($_POST['s_menu_items']) $check='show_design_menu';
            if ($_POST['t_menu_items']) $check='show_design_menu';
         if (!checkrights($check,4)) die(PERMISSION_DENIED);
            $tablename = isset($_POST['table']) ? $_POST['table'] : '';  
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;  
            
        
            $icandoit=false;
            
            switch($tablename){
                case 'd_changes':
                    $icandoit=check_in_table('d_order','changeid',$id);
                break; 
                case 's_combo_items':
                    $icandoit=true;
                break;
                case 'z_default_values':
                    $icandoit=true;
                    delete_subdata('z_default_config','default_id',$id);
                break;
                case 's_combo_groups':
                    $icandoit=true;
                    delete_subdata('s_combo_items','idcombogroup',$id);
                break;
                case 'd_order':
                     //delete with t_order
                     $icandoit=true;
                     if ($icandoit) delete_subdata('t_order','orderid',$id);
                     if ( isConducted( 'd_order', $id ) ) cancelConduct( 'd_order', 0, $id );
                break;
                case 't_order':
                    $icandoit=false;
                    //echo 'Счета не редактируются';
                break;
                case 's_automated_point':
                     $icandoit=(check_in_table('d_changes','idautomated_point',$id)&&check_in_table('d_order','idautomated_point',$id)&&check_in_table('t_workplaces','apid',$id));  
                     if ($icandoit) delete_subdata('t_employee_automated_point','automated_pointid',$id);
                break;
                case 's_tarifs':
                     $icandoit=(check_in_table('t_tarifs','tarifid',$id)&&check_in_table('t_object_tarif','tarifid',$id));  
                  
                break;
                case 's_clients':
                    $icandoit=check_in_table('d_order','clientid',$id);
                break;
                case 's_config':
                     $icandoit=false;
                break;
                case 's_discount':
                     $icandoit=true;
                break;
                case 's_employee':
                    $icandoit=(check_in_table('d_changes','employeeid',$id)&&check_in_table('d_order','employeeid',$id));  
                    if ($icandoit) {
                        delete_subdata('t_employee_automated_point','employeeid',$id); 
                        delete_subdata('t_employee_interface','employeeid',$id); 
                        delete_subdata('t_employee_role','employeeid',$id); 
                    }
                break;
                case 's_items':
                     $icandoit=(check_in_table('t_menu_items','itemid',$id)&&check_in_table('t_order','itemid',$id));  
                     if ($icandoit) delete_subdata('s_note','itemid',$id); 
                break;
                case 's_location':
                    $icandoit=check_in_table('s_objects','locationid',$id);
                break;
                case 's_menu':
                     $icandoit=(check_in_table('t_menu_items','menuid',$id)&&check_in_table('s_automated_point','menuid',$id)); 
                break;
                case 's_note':
                     $icandoit=true;
                break;
                case 's_objects':
                     $icandoit=check_in_table('d_order','objectid',$id);
                break;
                case 's_organisations':
                    $icandoit=true; //пока не используется
                break;
                case 't_discount_clients':
                    $icandoit=true;
                break;
                case 't_discount_ap':
                    $icandoit=true;
                break;
                case 't_discount_clients':
                    $icandoit=true;
                break;
                case 's_position':
                    $icandoit=check_in_table('s_employee','position',$id);
                break;
                case 's_printers':
                    $icandoit=check_in_table('s_subdivision','printerid',$id);
                break; 
                case 's_role':
                    $icandoit=check_in_table('t_employee_role','rollid',$id); 
                    if ($icandoit) delete_subdata('z_rights','rightid',$id); 
                break;
                case 's_subdivision':
                    $icandoit=(check_in_table('t_menu_items','printer',$id)&&check_in_table('t_order','printerid',$id)); 
                break;
                case 's_types_of_payment':
                    $icandoit=check_in_table('d_order','paymentid',$id);
                break;
                case 's_units_of_measurement':
                    $icandoit=check_in_table('s_items','measurement',$id);
                break;
                case 's_warehouse':
                    $icandoit=true; //пока не используется
                break;
                case 't_employee_automated_point':
                    $icandoit=true; 
                break;
                case 't_employee_interface':
                    $icandoit=true; 
                break;
                case 't_employee_role':
                    $icandoit=true; 
                break;
                case 't_menu_items':
                    $icandoit=true; 
                break;
                case 't_order':
                    $icandoit=false; 
                break;
                case 'z_rights':
                    $icandoit=true; 
                break;
                case 's_calculations':
                    $icandoit=true; 
                    delete_subdata('t_calculations','calculationid',$id);
                break;
                case 't_calculations':
                    $icandoit=true; 
                break;
                case 's_organization':
                    $icandoit=false; 
                break; 
                case 't_workplace':
                    $icandoit=true; 
                    
                    //make
                break;
                
                //системные таблицы;
                case 's_interfaces':
                     $icandoit=false;
                break;
                case 'z_logs':
                    $icandoit=false; 
                break;
                case 'z_feedback':
                    $icandoit=false; 
                break;

                case 'z_rights_category':
                    $icandoit=false; 
                break;
            } 
            if ($icandoit){
                echo 'ok';
                $query=mysql_query("SELECT * FROM `".addslashes($tablename)."` WHERE id='".addslashes($id)."' LIMIT 1");
                $row=mysql_fetch_assoc($query);
            
                mysql_query("DELETE FROM `".addslashes($tablename)."` WHERE id='".addslashes($id)."'");
                
                
                
                zlog(json_encode(array(
                            'table'=>$tablename,
                            'row'=>$row 
                )),1003); 
                
                
            }else{
                echo 'Невозможно удалить.';
            }
            
        break; 
}                           
?>