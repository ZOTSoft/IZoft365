<?PHP  header("Content-Type: text/html; charset=utf-8");
session_start();
include('check.php');
checksessionpassword();
include('mysql.php');

include('errors.php');
include('tables.php');
include('functions.php');
include('editor.php');
include('core.php');
include('templates.php');
error_reporting(E_ALL ^ E_NOTICE);




if (isset($_SESSION['timezone'])){ 
    date_default_timezone_set($_SESSION['timezone']); 
    mysql_query("SET `time_zone` = '".date('P')."'"); 
}                                       
    switch($_GET['do']){
        case 'time':
            $query=mysql_query("SELECT NOW()");
            $row=mysql_fetch_array($query);
            print_r($row);
        break;           
        case 'livesearch': 
            if (!checkrights($_GET['table'],1)) die(PERMISSION_DENIED);
            $res=array();
           
            $query=mysql_query("SELECT id,name FROM `".addslashes($_GET['table'])."` WHERE  UPPER(name) LIKE UPPER('".addslashes($_POST['q'])."%') ORDER by name LIMIT 10");
            while($row=mysql_fetch_assoc($query)){
                $res[$row['id']]=$row['name'];
            }
            echo json_encode($res);
        break;
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
        case 'getfolder': 
                if (!checkrights($_GET['table'],1)) die(PERMISSION_DENIED);
                $res=array();

                
                
                $parentid = isset($_POST['id']) ? intval($_POST['id']) : 0;    
                
                //страшная штука orderid   
                $iddoc = isset($_GET['orderid']) ? intval($_GET['orderid']) : 0;       
                $table = isset($_GET['table']) ?  $_GET['table'] : '';     
                $iddocstr='';

                if (isset($_POST['id'])){       
    
                $query=mysql_query("SELECT * FROM `".addslashes($table)."`  WHERE parentid='".addslashes($parentid)."' AND isgroup=1 ORDER by isgroup DESC,id");
               // echo "SELECT * FROM `".addslashes($table)."`  WHERE parentid='".addslashes($parentid)."' AND isgroup=1 ORDER by isgroup DESC,id";
                 
                while($row=mysql_fetch_array($query)){
                    $a=array();
                    
                    foreach($fields[$table] as $k=>$v){
                        switch($v['type']){
                            case 'input':
                               $a[$k]=$row[$k];
                               break;
                            case 'password':
                               $a[$k]='';
                               break;
                            case 'db_select':
                               $a[$k]=get_select_val($v['db_select'],$row[$k]);
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
                               $a[$k]=get_grid($v['db_grid'],$row[$k]);
                               break; 
                            case 'timezone':
                               $a[$k]=getTimeZoneValue($row[$k]);
                               break;
                        }
                        if (!empty($v['after_text'])){
                           $a[$k].=$v['after_text']; 
                        }
                    }
                    
                    
                    
                    //$a['_parentId']=$row['parentid']; 
                    $a['isgroup']=$row['isgroup']; 
                    $a['iconCls']=($row['isgroup']?'tree-folder':'tree-file'); 
                    $a['state']=($row['isgroup']?'closed':'open'); 
                    $res[]=$a;
                }
                }else{
                    $res[]=array('id'=>0,'name'=>'Корень','isgroup'=>1,'iconCls'=>'tree-folder','state'=>'closed');
                }
                
                
                

                    echo json_encode($res);
           
        break;
        case 'perenoska':             
            if (!checkrights('show_design_menu',3)) die(PERMISSION_DENIED);
            $menuid = isset($_GET['menuid']) ? intval($_GET['menuid']) : 0;    
            
            $to=isset($_GET['to'])? intval($_GET['to']) : 0;   
            $from=isset($_GET['id'])? intval($_GET['id']) : 0;   
            
            $query=mysql_query("SELECT id FROM `s_items` WHERE parentid='".$from."'");
            
            $q1=mysql_query("SELECT id FROM `s_subdivision` ORDER by id DESC LIMIT 1");
            $printer=mysql_fetch_array($q1);
            //Полюшко-поле, Полюшко, широко поле.
            while($row=mysql_fetch_array($query)){
                //mysql_query("INSERT into `".$base."`.`t_menu_items` SET menuid='".$menuid."',idout='".$row['idout']."', idlink='".$row['idlink']."',parentid='".$row['parentid']."',isgroup='".$row['isgroup']."',name='".$row['name']."',price='".$row['price']."',itemid='".$row['id']."'");
                CopyNode($row['id'],$to,$menuid,$printer['id']);
            }
            


        case 'move': 
            if (!checkrights('show_design_menu',3)) die(PERMISSION_DENIED);
            if ($_GET['do']=='move'){
                $idfrom = isset($_POST['idfrom']) ? intval($_POST['idfrom']) : 0;  
                $menuid = isset($_POST['menuid']) ? intval($_POST['menuid']) : 0;   
                $idto = isset($_POST['idto']) ? intval($_POST['idto']) : 0;    
                
                $q1=mysql_query("SELECT id FROM `s_subdivision` ORDER by id DESC LIMIT 1");
                $printer=mysql_fetch_array($q1);
            
            
                CopyNode($idfrom,$idto,$menuid,$printer['id']);    
                
                $s_menu=getRow('s_menu',$menuid);
                $s_items=getRow('s_items',$idfrom);
                $t_menu_items=getRow('t_menu_items',$idto);
                $s_subdivision=getRow('s_subdivision',$printer['id']);
                
                zlog(json_encode(array(
                                    'from'=>$idfrom,
                                    'from_name'=>$s_items['name'],
                                    'to'=>$idto,
                                    'to_name'=>$t_menu_items['name'],
                                    'menuid'=>$menuid,
                                    'menu_name'=>$s_menu['name'],
                                    'printer'=>$printer['id'],
                                    'printer_name'=>$s_subdivision['name'],
                                    )),1200); 
                                
                      
                $_GET['do']='getmenuitems';   
                $_GET['menuid']=$menuid;
            }
        case 'getmenuitems': 
                if (!checkrights('show_design_menu',1)) die(PERMISSION_DENIED);
                $res=array();
                
                $menuid = isset($_GET['menuid']) ? intval($_GET['menuid']) : 0; 
                
                $parentid = isset($_POST['parentid']) ? intval($_POST['parentid']) : 0;  

                if ($parentid > 0) $res = getParents('t_menu_items',$parentid);  

                    
                $query=mysql_query("SELECT 
                    `t_menu_items`.id,
                    `t_menu_items`.idout,
                    `t_menu_items`.parentid,
                    `s_items`.name,
                    `t_menu_items`.price,
                    `t_menu_items`.printer,
                    `t_menu_items`.isgroup
                
                
                 FROM `t_menu_items` 
                                          LEFT JOIN `s_items` ON `s_items`.`id`=`t_menu_items`.`itemid`  
                      WHERE `t_menu_items`.parentid='".addslashes($parentid)."' 
                        AND `t_menu_items`.menuid='".addslashes($menuid)."' ORDER by `t_menu_items`.isgroup DESC,`s_items`.name");   
               
               //$totalrows
                while($row=mysql_fetch_array($query)){
                    $a=array();
                    
                    $a['id']=$row['id'];
                    $a['idout']=$row['idout'];
                    $a['name']=$row['name'];
                    

                                       
                    $a['price']=$row['price'];
                    //$a['price']=($row['isgroup']?'':$row['price']);
                    $a['price']=round($a['price']);
                    $query2=mysql_query("SELECT name FROM s_subdivision WHERE id='".$row['printer']."'");  
                    if (mysql_numrows($query2))
                        $a['printer']=mysql_result($query2,0);
                    else
                        $a['printer']='';
                        
                        
                    
                    if ($row['isgroup']) $a['price']=''; 
                    
                    
                    $a['isgroup']=$row['isgroup']; 
                    $a['parentid']=$row['parentid']; 
                    $res[]=$a;
                }
                //echo "123";
                $answer=array();
                $answer['rows']=$res;
                //$answer['totalrows']=$totalrows;
                
                echo json_encode($answer);
        break;
        //получение кол-ва дочерних элементов 
        case 'getcounts': 
                if (!checkrights($_GET['table'],1)) die(PERMISSION_DENIED);
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;       
                $tablename = isset($_GET['table']) ? $_GET['table'] : '';       
                $query=mysql_query("SELECT count(*) as counts FROM `".addslashes($tablename)."` WHERE parentid='".$id."'");   
                $row=mysql_fetch_array($query);
                echo ($row[0]['counts']);
        break;
        case 'design_menu': 
                if (!checkrights('show_design_menu',1)) die(PERMISSION_DENIED);
                $res=array();
                $query=mysql_query("SELECT * FROM `s_menu`");
                while($row=mysql_fetch_array($query)){
                    if (checkNFrights('s_menu',$row['id'],'view')) 
                        $res[$row['id']]=$row['name'];
                }
                $ret=array();
                $ret['fields']=$res;
                $ret['rights']=array('edit'=>checkrights('show_design_menu',2),'add'=>checkrights('show_design_menu',3),'deletez'=>checkrights('show_design_menu',4));
                echo json_encode($ret);
        break; 
        case 'get_note_rights': 
                //if (!checkrights('s_note',1)) die(PERMISSION_DENIED);
                //$res=array();
                //$res[0]='Общие примечания';
                //$query=mysql_query("SELECT * FROM `s_items`");
                 //while($row=mysql_fetch_array($query)){
                 //    $res[$row['id']]=$row['name'];
                 //}
                 //$ret=array();
                 //$ret['fields']=$res;
                 $ret=array('view'=>checkrights('s_note',1),'edit'=>checkrights('s_note',2),'add'=>checkrights('s_note',3),'deletez'=>checkrights('s_note',4));
                 echo json_encode($ret);
        break; 
        
        case 'getfile': 

                $table=$_GET['table'];
                
                $res='';
                $title='';
                foreach ($fields[$table] as $k=>$v){    
                    if ($v['in_grid']){ 
                        $title.='<td>'.$v['title'].'</td>';
                    }
                }
               
                $res=getprintel($table,0,0,$fields[$table]); 
                        
                switch($_GET['type']){
                    case 'xls':  
                        $table='<table border="1"><tr>'.$title.'</tr>'.$res.'</table>';
                        header("Content-Type: application/download\n"); 
                        header("Content-Disposition: attachment; filename=".time().'.xls');
                        echo $table;
                    break;
                    case "html":
                        $table='<table cellspacing="0" cellpadding="0"><tr class="tableheader">'.$title.'</tr>'.$res.'</table>';
                        echo '<html><meta charset="UTF-8"><title>Просмотр таблицы</title><link rel="stylesheet" type="text/css" href="/company/pdf/mpdf.css"><body><a href="/company/ajax.php?do=getfile&type=pdf&table='.$_GET['table'].'" target="_blank"><img src="/company/i/icon_pdf.png"></a> <a href="/company/ajax.php?do=getfile&type=xls&table='.$_GET['table'].'" target="_blank"><img src="/company/i/xls.gif"></a><br />
               '.$table.'</body></html>';
                    break;
                     case 'pdf': 
                        require_once('pdf/mpdf.php');
                       
                        $table='<table cellspacing="0" cellpadding="0"><tr class="tableheader">'.$title.'</tr>'.$res.'</table>';
                        
                        $mpdf = new mPDF('utf-8', 'A4', '8', '', 10, 10, 7, 7, 10, 10); /*задаем формат, отступы и.т.д.*/
                        $mpdf->charset_in = 'utf8'; /*не забываем про русский*/

                        $stylesheet = file_get_contents('pdf/mpdf.css'); /*подключаем css*/
                        $mpdf->WriteHTML($stylesheet, 1);

                        $mpdf->list_indent_first_level = 0; 
                        $mpdf->WriteHTML($table, 2); /*формируем pdf*/
                        $mpdf->Output('mpdf.pdf', 'I');
                     break;
                }
                
                 
                 

        break;
        
        case 'print_dez_menu': 

                $table='t_menu_items';
                $menu_id=$_GET['menu_id'];
                
                $res='';
                $title='';
                /*foreach ($fields[$table] as $k=>$v){    
                    if ($v['in_grid']){ 
                        $title.='<td>'.$v['title'].'</td>';
                    }
                }*/
               
               $title='<td>Код</td><td>Наименование</td><td>Цена</td>';
               
                $res=getprintdezmenu(0,$menu_id); 
                        
                switch($_GET['type']){
                    case 'xls':  
                        $table='<table border="1"><tr>'.$title.'</tr>'.$res.'</table>';
                        header("Content-Type: application/download\n"); 
                        header("Content-Disposition: attachment; filename=".time().'.xls');
                        echo $table;
                    break;
                    case "html":
                        $table='<table cellspacing="0" cellpadding="0"><tr class="tableheader">'.$title.'</tr>'.$res.'</table>';
                        echo '<html><meta charset="UTF-8"><title>Просмотр таблицы</title><link rel="stylesheet" type="text/css" href="/company/pdf/mpdf.css"><body><a href="/company/ajax.php?do=print_dez_menu&type=pdf&menu_id='.$_GET['menu_id'].'" target="_blank"><img src="/company/i/icon_pdf.png"></a> <a href="/company/ajax.php?do=print_dez_menu&type=xls&menu_id='.$_GET['menu_id'].'" target="_blank"><img src="/company/i/xls.gif"></a><br />
               '.$table.'</body></html>';
                    break;
                     case 'pdf': 
                        require_once('pdf/mpdf.php');
                       
                        $table='<table cellspacing="0" cellpadding="0"><tr class="tableheader">'.$title.'</tr>'.$res.'</table>';
                        
                        $mpdf = new mPDF('utf-8', 'A4', '8', '', 10, 10, 7, 7, 10, 10); /*задаем формат, отступы и.т.д.*/
                        $mpdf->charset_in = 'utf8'; /*не забываем про русский*/

                        $stylesheet = file_get_contents('pdf/mpdf.css'); /*подключаем css*/
                        $mpdf->WriteHTML($stylesheet, 1);

                        $mpdf->list_indent_first_level = 0; 
                        $mpdf->WriteHTML($table, 2); /*формируем pdf*/
                        $mpdf->Output('mpdf.pdf', 'I');
                     break;
                }
                
                 
                 

        break;
        
        case 'get_printdialog': 
            //Устаревший запрос, нигде не используется
               $table=$_GET['table'];
               echo '<table width="100%"><tr>
               <td><a href="/company/ajax.php?do=getfile&type=pdf&table='.$table.'" target="_blank"><img src="/company/i/icon_pdf.png"></a></td>
               <td><a href="/company/ajax.php?do=getfile&type=xls&table='.$table.'" target="_blank"><img src="/company/i/xls.gif"></a></td>
               <td><a href="/company/ajax.php?do=getfile&type=html&table='.$table.'" target="_blank"><img src="/company/i/html.png"></a></td>
               </tr></table>';
        break;
        
        //получение параметров таблицы
        case 'gettable': 
                if (!checkrights($_POST['table'],1)) die(PERMISSION_DENIED);
                $tablename = isset($_POST['table']) ? ($_POST['table']) : ''; 
                $res=array();
                $res['name']=$tablename;
                $res['title']=$tables[$tablename]['name'];
                $res['create_group']=$tables[$tablename]['create_group'];
                $res['width']=$tables[$tablename]['width'];
                $res['height']=$tables[$tablename]['height'];
                
                $res['rights']=array('view'=>checkrights($tablename,1),'edit'=>checkrights($tablename,2),'add'=>checkrights($tablename,3),'deletez'=>checkrights($tablename,4),'print'=>checkrights($tablename,5));
                
                
                
                foreach($fields[$tablename] as $k=>$v){
                    if ($v['in_grid']){
                        $f=array();
                        $f['field']=$k;
                        foreach($v as $key=>$value){
                            if ($key!='width')
                            $f[$key]=$value;
                        }
                        $res['fields'][]=$f;
                    } 
                }
                
                echo json_encode($res);
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
                

                if (($tablename=='s_items')&& isset($_POST['i_useInMenu']) && ($_POST['i_useInMenu']==1)){
                    setConfig('front_lastupdate_menu',date('d.m.Y H:i:s'));
                }
                

                
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
                
                //Полюшко-поле, Полюшко, широко поле.
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
            
            
            if (($tablename=='s_items')){
                    setConfig('front_lastupdate_menu',date('d.m.Y H:i:s'));
            }else{
                if ($tablename=='s_automated_point'){
                    setConfig('front_lastupdate_conf',date('d.m.Y H:i:s'));
                }
            }
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
            
            //Полюшко-поле, Полюшко, широко поле.
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
        //удаление элемента и группы из базы
        case 'truncate_menu':
            if (!checkrights('show_design_menu',4)) die(PERMISSION_DENIED);
                if (isset($_GET['id']))
                    deleteMenu(intval($_GET['id']),intval($_GET['menuid']),0);
        break;
        case 'delete':
            $check=$_POST['table'];
            if ($_POST['s_menu_items']) $check='show_design_menu';
            if ($_POST['t_menu_items']) $check='show_design_menu';
         if (!checkrights($check,4)) die(PERMISSION_DENIED);
            $tablename = isset($_POST['table']) ? $_POST['table'] : '';  
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;  
            
            //ну маааам 
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
        case 'getselect_changes':
            $ap=0;
            if (isset($_POST['ap'])){
                $ap=(int)$_POST['ap'];
            }
            echo db_smena_select_options($ap);
        break;
        case 'getselect':
            $tablename = isset($_POST['table']) ? $_POST['table'] : ''; 
            echo db_select_options($tablename);
        break;
        case 'delete2':
            //поидеи функция удаления меню
            if (!checkrights('show_design_menu',4)) die(PERMISSION_DENIED);
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;  
            $menuid = isset($_POST['menuid']) ? intval($_POST['menuid']) : 0;  
            deleteMenu($id,$menuid);
            echo 'ok';
        break;
        
        //создание элемента
        case 'show_form': 
            $check=$_GET['table'];
            if ($_GET['table']=='s_menu_items') $check='show_design_menu';
            if ($_GET['table']=='t_menu_items') $check='show_design_menu';
            if (!checkrights($check,2)) die(PERMISSION_DENIED);
 
            
            $data=array();
            $tablename=$_GET['table'];
            if (isset($_GET['edit'])&&isset($_GET['id'])) {
                $query=mysql_query("SELECT * FROM `".addslashes($_GET['table'])."` WHERE id='".$_GET['id']."' LIMIT 1");
                $data=mysql_fetch_assoc($query);
                
                echo '<div class="formp"><form id="form_edit-'.$tablename.'" method="post" action="/company/ajax.php?do=edit&table='.$tablename.'&id='.$_GET['id'].'" novalidate>';          
            }else{
                echo '<div class="formp"><form id="form-'.$tablename.'" method="post" action="/company/ajax.php?do=add&table='.$tablename.'&parentid='.$_GET['parentid'].'" novalidate>';      
            }
            echo form_design($_GET['table'],$data);
            
            if (!empty($_GET['orderid'])){
                echo '<input type="hidden" name="orderid" value="'.$_GET['orderid'].'">';
            }
            if (!empty($_GET['iddoc'])&&($tablename=='t_workplace')){
                echo '<input type="hidden" name="apid" value="'.$_GET['iddoc'].'">';
            }
            if (isset($_GET['itemid'])){
                echo '<input type="hidden" name="itemid" value="'.$_GET['itemid'].'">';
            }
                
                
            echo '<input type="hidden" name="isgroup" value="0">
                
                <div style="margin-top:20px"><a href="javascript:void(0)" class="btn btn-success" iconCls="icon-ok" onclick="savez2(\''.$tablename.'\',0)"><b>ОК</b></a> 
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="btn btn-danger" onclick="javascript:$(\'#dialog-'.$tablename.'\').dialog(\'close\')">Отмена</a></div>
                
                </form></div>';
        break;
        case 'create_el': 
                if (!checkrights($_GET['table'],3)) die(PERMISSION_DENIED);
                $tablename = isset($_GET['table']) ? $_GET['table'] : '';  
                $parentid = isset($_GET['parentid']) ? $_GET['parentid'] : 0;  

                echo '<div class="modal fade"  id="dialog_add-'.$tablename.'"  tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog" style="width:900px" ><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">Добавление '.$tables[$tablename]['name'].'</h4></div><div class="modal-body"><div class="formp"><form class="form-horizontal" role="form" id="form_add-'.$tablename.'" method="post" action="/company/ajax.php?do=add&table='.$tablename.'&parentid='.$parentid.'"  novalidate>';      
                
                
                if (isset($editor[$tablename])){
                    echo form_design($tablename,0,0);
                }else{
                    
                    foreach($fields[$tablename] as $k=>$v){
                        if ($v['in_edit']){
                            if ($v['type']!='userrights')
                            echo showfield($tablename,$k,0,0);

                        }
                    }
                }
                
                if (!empty($_GET['idfield'])){
                    echo '<input type="hidden" name="'.$_GET['idfield'].'" value="'.$_GET[$_GET['idfield']].'">';
                }
                
                
            /*    if (!empty($_GET['orderid'])){
                    echo '<input type="hidden" name="orderid" value="'.$_GET['orderid'].'">';
                }
                if (!empty($_GET['iddoc'])&&($tablename=='t_workplace')){
                    echo '<input type="hidden" name="apid" value="'.$_GET['iddoc'].'">';
                } 
                if (!empty($_GET['iddoc'])&&($tablename=='t_discount_clients')){
                    echo '<input type="hidden" name="discountid" value="'.$_GET['iddoc'].'">';
                }
                if (!empty($_GET['iddoc'])&&($tablename=='t_discount_ap')){
                    echo '<input type="hidden" name="discountid" value="'.$_GET['iddoc'].'">';
                }
                if (isset($_GET['itemid'])){
                    echo '<input type="hidden" name="itemid" value="'.$_GET['itemid'].'">';
                }*/
                
                echo '<input type="hidden" name="isgroup" value="0"></form></div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button><button type="button" class="btn btn-primary" onclick="savez2(\''.$tablename.'\',0)">Добавить</button></div></div></div></div>';
        break;
        case 'zcreate': 
                if (!checkrights($_GET['table'],3)) die(PERMISSION_DENIED);
                $tablename = isset($_GET['table']) ? $_GET['table'] : '';  
                $parentid = isset($_GET['parentid']) ? $_GET['parentid'] : 0;  
                $idfield='';
                if (!empty($_GET['idfield'])) $idfield=$_GET['idfield'];
                 
                
                echo '<form class="form-horizontal" role="form" id="form_add-'.$tablename.'" method="post" action="/company/ajax.php?do=add&table='.$tablename.'&parentid='.$parentid.'">';      
                
                
                if (isset($editor[$tablename])){
                    echo form_design($tablename,0,0);
                }else{
                    
                    foreach($fields[$tablename] as $k=>$v){
                        if ($v['in_edit']){
                            if ($v['type']!='userrights')
                                if ($idfield!=$k){
                                    echo showfield($tablename,$k,0,0);
                                //echo $k;    
                                }
                                
                                     

                        }
                    }
                }
                
                if (!empty($_GET['idfield'])){
                    echo '<input type="hidden" name="'.$_GET['idfield'].'" value="'.$_GET[$_GET['idfield']].'">';
                }
                
                
            /*    if (!empty($_GET['orderid'])){
                    echo '<input type="hidden" name="orderid" value="'.$_GET['orderid'].'">';
                }
                if (!empty($_GET['iddoc'])&&($tablename=='t_workplace')){
                    echo '<input type="hidden" name="apid" value="'.$_GET['iddoc'].'">';
                } 
                if (!empty($_GET['iddoc'])&&($tablename=='t_discount_clients')){
                    echo '<input type="hidden" name="discountid" value="'.$_GET['iddoc'].'">';
                }
                if (!empty($_GET['iddoc'])&&($tablename=='t_discount_ap')){
                    echo '<input type="hidden" name="discountid" value="'.$_GET['iddoc'].'">';
                }
                if (isset($_GET['itemid'])){
                    echo '<input type="hidden" name="itemid" value="'.$_GET['itemid'].'">';
                }*/
                
                echo '<input type="hidden" name="isgroup" value="0"></form>';
        break;
                //создание группы
        case 'zgroup_create': 
                if (!checkrights($_GET['table'],3)) die(PERMISSION_DENIED);
                $tablename = isset($_GET['table']) ? $_GET['table'] : '';  
                $parentid = isset($_GET['parentid']) ? $_GET['parentid'] : '';  
                echo '
         <form class="form-horizontal" id="form_add-'.$tablename.'" method="post" action="/company/ajax.php?do=add&table='.$tablename.'&parentid='.$parentid.'" novalidate>';
                    
                foreach($fields[$tablename] as $k=>$v){
                    if ($v['in_group'])
                        echo showfield($tablename,$k,$data,$data['id'],1);
                }
                
                echo '<input type="hidden" name="isgroup" value="1"></form>';
        break;
         case 'create_elcopy': 
                if (!checkrights($_GET['table'],3)) die(PERMISSION_DENIED);
                $tablename = isset($_GET['table']) ? $_GET['table'] : '';  
                $id = isset($_GET['id']) ? $_GET['id'] : 0;  

                echo '<div class="modal fade"  id="dialog_addcopy-'.$tablename.'"  tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog" style="width:900px" ><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">Добавление копированием  '.$tables[$tablename]['name'].'</h4></div><div class="modal-body"><div class="formp"><form class="form-horizontal" role="form" id="form_add-'.$tablename.'" method="post" action="/company/ajax.php?do=add&table='.$tablename.'&parentid='.$parentid.'"  novalidate>';      
                
                $data=array();
                $query=mysql_query("SELECT * FROM `".addslashes($tablename)."` WHERE id='".addslashes($id)."' LIMIT 1");
                $data=mysql_fetch_assoc($query);
                
                
                if (isset($editor[$tablename])){
                    echo form_design($tablename,$data,$id);
                }else{
                    
                    foreach($fields[$tablename] as $k=>$v){
                        if((($data['isgroup']==1)&&$v['in_group'])||(($data['isgroup']==0)&&$v['in_edit'])){
                            
                            echo showfield($tablename,$k,$data,$id);
                        }

                        
                    }
                }
                
                if (!empty($_GET['orderid'])){
                    echo '<input type="hidden" name="orderid" value="'.$_GET['orderid'].'">';
                }
                if (!empty($_GET['iddoc'])&&($tablename=='t_workplace')){
                    echo '<input type="hidden" name="apid" value="'.$_GET['iddoc'].'">';
                }
                if (isset($_GET['itemid'])){
                    echo '<input type="hidden" name="itemid" value="'.$_GET['itemid'].'">';
                }
                
                echo '<input type="hidden" name="isgroup" value="0"></form></div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button><button type="button" class="btn btn-primary" onclick="savez2(\''.$tablename.'\',0)">Добавить</button></div></div></div></div>';
        break;
        //создание группы
        case 'create_gr': 
                if (!checkrights($_GET['table'],3)) die(PERMISSION_DENIED);
                $tablename = isset($_GET['table']) ? $_GET['table'] : '';  
                $parentid = isset($_GET['parentid']) ? $_GET['parentid'] : '';  
                echo '
        <div class="modal fade"  id="dialog_add-'.$tablename.'"  tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog" style="width:900px" ><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">Добавление '.$tables[$tablename]['name'].'</h4></div><div class="modal-body"><div class="formp"> <form class="form-horizontal" id="form_add-'.$tablename.'" method="post" action="/company/ajax.php?do=add&table='.$tablename.'&parentid='.$parentid.'" novalidate>';
                    
                foreach($fields[$tablename] as $k=>$v){
                    if ($v['in_group'])
                        echo showfield($tablename,$k,$data,$data['id'],1);
                }
                
                echo '<input type="hidden" name="isgroup" value="1"></form></div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button><button type="button" class="btn btn-primary" onclick="savez2(\''.$tablename.'\',1)">Добавить</button></div></div></div></div>';
        break;

        //редактирование элемента
                case 'zedit': 
                $check=$_GET['table'];
                if ($_GET['table']=='s_menu_items') $check='show_design_menu';
                if ($_GET['table']=='t_menu_items') $check='show_design_menu';
                if (!checkrights($check,2)) die(PERMISSION_DENIED);
                $tablename = isset($_GET['table']) ? $_GET['table'] : '';  
                $id = isset($_GET['id']) ? $_GET['id'] : '';  
               
               if (isset($_GET['table'])&&isset($_GET['id'])) {
                echo '<form class="form-horizontal" id="form_edit-'.$tablename.'" method="post" action="/company/ajax.php?do=edit&table='.$tablename.'&id='.$_GET['id'].'">';
                
                $data=array();
                $query=mysql_query("SELECT * FROM `".addslashes($_GET['table'])."` WHERE id='".$_GET['id']."' LIMIT 1");
                $data=mysql_fetch_assoc($query);
                
                if (isset($editor[$tablename])){
                    echo form_design($tablename,$data,$id);
                }else{
  
                    foreach($fields[$tablename] as $k=>$v){
                        if ($data['isgroup']==1) {
                            if ($v['in_group'])
                                echo showfield($tablename,$k,$data,$data['id']);
                        }
                        else
                        if ($v['in_edit']){
                            echo showfield($tablename,$k,$data,$data['id']);
                        }
                    }
                }
                if (!empty($_GET['orderid'])){
                    echo '<input type="hidden" name="orderid" value="'.$_GET['orderid'].'">';
                }
                if (!empty($_GET['iddoc'])&&($tablename=='t_workplace')){
                    echo '<input type="hidden" name="apid" value="'.$_GET['iddoc'].'">';
                }
                if (isset($_GET['itemid'])){
                    echo '<input type="hidden" name="itemid" value="'.$_GET['itemid'].'">';
                }
                
                    echo '<input type="hidden" name="id" value="'.$id.'"></form>';
                }
        break;
        case 'edit_el': 
                $check=$_GET['table'];
                if ($_GET['table']=='s_menu_items') $check='show_design_menu';
                if ($_GET['table']=='t_menu_items') $check='show_design_menu';
                if (!checkrights($check,2)) die(PERMISSION_DENIED);
                $tablename = isset($_GET['table']) ? $_GET['table'] : '';  
                $id = isset($_GET['id']) ? $_GET['id'] : '';  
               
               if (isset($_GET['table'])&&isset($_GET['id'])) {
                echo '<div class="modal fade"  id="dialog_edit-'.$tablename.'"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog" style="width:900px" ><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">Редактирование '.$tables[$tablename]['name'].'</h4></div><div class="modal-body"><div class="formp"><form class="form-horizontal" id="form_edit-'.$tablename.'" method="post" action="/company/ajax.php?do=edit&table='.$tablename.'&id='.$_GET['id'].'" novalidate>';
                
                $data=array();
                $query=mysql_query("SELECT * FROM `".addslashes($_GET['table'])."` WHERE id='".$_GET['id']."' LIMIT 1");
                $data=mysql_fetch_assoc($query);
                
                if (isset($editor[$tablename])){
                    echo form_design($tablename,$data,$id);
                }else{
  
                    foreach($fields[$tablename] as $k=>$v){
                        if ($data['isgroup']==1) {
                            if ($v['in_group'])
                                echo showfield($tablename,$k,$data,$data['id']);
                        }
                        else
                        if ($v['in_edit']){
                            echo showfield($tablename,$k,$data,$data['id']);
                        }
                    }
                }
                if (!empty($_GET['orderid'])){
                    echo '<input type="hidden" name="orderid" value="'.$_GET['orderid'].'">';
                }
                if (!empty($_GET['iddoc'])&&($tablename=='t_workplace')){
                    echo '<input type="hidden" name="apid" value="'.$_GET['iddoc'].'">';
                }
                if (isset($_GET['itemid'])){
                    echo '<input type="hidden" name="itemid" value="'.$_GET['itemid'].'">';
                }
                
                    echo '<input type="hidden" name="id" value="'.$id.'"></form></div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button><button type="button" class="btn btn-primary" onclick="savez4(\''.$tablename.'\',\''.$id.'\')">Сохранить</button></div></div></div></div>';
                }
        break;
        //просмотр элемента и печать 
         case 'view_el': 
                $js='';
                $result=array();
                
                
                $check=$_GET['table'];
                
                if ($_GET['table']=='s_menu_items') $check='show_design_menu';
                if ($_GET['table']=='t_menu_items') $check='show_design_menu';
                if (!checkrights($check,1)) die(PERMISSION_DENIED);
                $tablename = isset($_GET['table']) ? $_GET['table'] : '';  
                $id = isset($_GET['id']) ? $_GET['id'] : '';  
             /*   $result[]='
        <div class="modal fade"  id="dialog_view-'.$tablename.'"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog" style="width:900px" ><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">Просмотр '.$tables[$tablename]['name'].'</h4></div><div class="modal-body"><div class="formp">';   */
                 
                $query=mysql_query("SELECT * FROM `".addslashes($tablename)."` WHERE id='".$id."' LIMIT 1");
                $row=mysql_fetch_assoc($query);
               
                if ($row['isgroup']==1){
                   $result[]='<div class="fitem">
                    <label>Наименование:'.(!empty($fields[$tablename]['name']['alt'])?' (<a href="#" class="easyui-tooltip" title="'.$fields[$tablename]['name']['alt'].'">i</a>)':'').'</label>
                    '.$row['name'].'</div>
                   <div class="fitem"><label>Код:'.(!empty($fields[$tablename]['idout']['alt'])?' (<a href="#" class="easyui-tooltip" title="'.$fields[$tablename]['idout']['alt'].'">i</a>)':'').'</label>
                   '.$row['idout'].'</div>'; 
                }else{  
                    foreach($fields[$tablename] as $k=>$v){
                        if ($v['in_edit']){
                            $result[]='<div class="fitem"><label>'.$v['title'].(!empty($v['alt'])?' (<a href="#" class="easyui-tooltip" title="'.$v['alt'].'">i</a>)':'').':</label>';

                            switch ($v['type']){
                                case 'input': 
                                    $result[]=$row[$k];
                                    break;
                                case 'checkbox': 
                                    $result[]=($row[$k]==1?'Да':'Нет');
                                    break;
                                case 'db_select':
                                    $result[]=get_db_select_value($v['db_select'],$row[$k]);
                                    break;
                                case 'timezone':
                                    $result[]=getTimeZoneValue($row[$k]);
                                    break;

                                    
                                    //$("#myModal").on("show", function() { 
                                case 'db_grid':
                                    $result[]=" 
                                        <table id='tableview-".$k."1'></table>";
                                    $js.="$('#tableview-".$k."1').myTreeView({url:'ajax.php?do=newfuckingget&table=".$k."&".$v['idfield']."=".$id."&idfield=".$v['idfield']."&nolimit=topgear', headers: [";
                                                    foreach($fields[$k] as $k1=>$v1){
                                                        if ($v1['in_grid']){
                                                            $js.="{name:'".$k1."',title:'".$v1['title']."'},";
                                                        } 
                                                    }
                                                    $js.="],tree:false,pagination:false}); ";
                                    break;
                            }
                            if (!empty($v['after_text'])){
                                $result[]=$v['after_text'];
                            }
                            
                            $result[]='</div>  ';
                        }
                    }
                }
                $result[]='</div>';
                if (!isset($_GET['print'])){ 
                    $result[]='<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>';
                    if ($tablename=='d_order'){
                        $result[]='
                        <a href="ajax.php?do=print_order&id='.$id.'" target="_blank"class="btn btn-primary" iconCls="icon-print">Печать</a>';
                    }
                    else
                    {
                        $result[]='
                        
                        <a href="ajax.php?do=view_el&table='.$tablename.'&id='.$id.'&print=true" target="_blank" class="btn btn-primary" iconCls="icon-print">Печать</a>';
                    }
                   $result[]='</div>';
                }
                /*$result[]=' 
                        
                    </div>
                </div>
             </div>
             
             ';*/
                
                if (!isset($_GET['print'])){
                    echo json_encode(array('view'=>join('',$result),'js'=>$js));
                }else{
                    
                    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Печать</title>
    <script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/company/mtree/mytreeview.js"></script>
    
<script>$(document).ready(function() {
window.print();
setTimeout(function() {

        window.close();                      
   
}, 200); 
    });</script>
</head>
<body>'.join('',$result).'</body></html>';
                }
        break;
        case 'print_order':
        

         echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Печать</title>
    <script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
    
<script>$(document).ready(function() {
window.print();
setTimeout(function() {

        window.close();                      
   
}, 200); 
    });</script>
</head>
<body>';
            $id = isset($_GET['id']) ? $_GET['id'] : ''; 
            
            $query=mysql_query("SELECT p.name as clientname,
                ap.name as apoint, 
                p.id as client, 
                d.id as id, 
                d.printed as printed,
                d.closed as closed, 
                d.discountsum as discount, 
                d.servicesum as service, 
                d.guestcount as guests, 
                p.fullname as employeename,  
                o.name as tablename, 
                round(d.totalsum) as totalsum,
                sumfromclient,
                e.name as employeename,  
                DATE_FORMAT(d.creationdt, '%d.%m.%y')  as dtopen, 
                DATE_FORMAT(d.creationdt, '%H:%i:%S')  as timeopen, 
                tp.name as payment 
            FROM d_order as d 
                LEFT JOIN s_clients AS p ON d.clientid = p.id 
                LEFT JOIN s_automated_point AS ap ON d.idautomated_point = ap.id 
                LEFT JOIN s_objects AS o ON d.objectid=o.id 
                LEFT JOIN s_employee AS e ON d.employeeid=e.id 
                LEFT JOIN s_types_of_payment AS tp ON d.paymentid=tp.id 
            WHERE d.id=".(int)$id);
            
            $query2=mysql_query("SELECT 
                i.name as foodname, 
                t.price as price, 
                quantity,  
                t.price*t.quantity as summa
            FROM t_order as t 
                LEFT JOIN s_items as i on t.itemid = i.id 
            WHERE orderid=".(int)$id."");
            
            $order=mysql_fetch_assoc($query);
            echo '<h1>'.$order['apoint'].'</h1>
                  <h2>Счет №:'.$order['id'].'</h2>
                  Дата: <b>'.$order['dtopen'].' '.$order['timeopen'].'</b><br />
                  Официант: <b>'.$order['employeename'].'</b><br />
                  Клиент: <b>'.$order['clientname'].'</b><br />
                  ----------------------------------------<br />';
            $i=1;
            while($row=mysql_fetch_assoc($query2)){
                echo $i.':'.$row['foodname'].'<br />'.$row['price'].' x '.$row['quantity'].' = '.$row['summa'].'<br />';
                ++$i;
            } 
            echo '----------------------------------------<br />
                  ВСЕГО: <b>'.($order['totalsum']-$order['service']+$order['discount']).'</b><br />
                  ИТОГО: <b>'.$order['totalsum'].'</b><br />
                  СУММА ОТ КЛИЕНТА: <b>'.$order['sumfromclient'].'</b><br />
                  СДАЧА: <b>'.($order['sumfromclient']-$order['totalsum']).'</b><br />
                  ----------------------------------------
                  ';
            echo '</body></html>';
            zlog($id,1006); 
            
        break;
        
        
        case 'gethtml_akt_real':
            if (!checkrights('gethtml_akt_real',1)) die(PERMISSION_DENIED);
            echo $template['gethtml_akt_real'];
        break; 
        case 'gethtml_cancellation':
            if (!checkrights('gethtml_cancellation',1)) die(PERMISSION_DENIED);
            echo $template['gethtml_cancellation'];
        break; 
        case 'gethtml_anal_sale':
           // if (!checkrights('gethtml_akt_real',1)) die(PERMISSION_DENIED);
            echo $template['gethtml_anal_sale'];
        break; 
        case 'gethtml_anal_sale2':
           // if (!checkrights('gethtml_akt_real',1)) die(PERMISSION_DENIED);
            echo $template['gethtml_anal_sale2'];
        break;
        case 'gethtml_remainsdetailed':
           // if (!checkrights('gethtml_akt_real',1)) die(PERMISSION_DENIED);
            echo $template['gethtml_remainsdetailed'];
        break; 
        case 'gethtml_reconduct':
            if (!checkrights('gethtml_akt_real',1)) die(PERMISSION_DENIED);
            $filterContent = str_replace( '{actual_dt}', date('d.m.Y H:i:s',strtotime(getActualDt())), $template['gethtml_reconduct'] );
            echo $filterContent;
        break;
        case 'gethtml_poschetam':
        if (!checkrights('gethtml_poschetam',1)) die(PERMISSION_DENIED);
            echo $template['gethtml_poschetam'];
        break;
        case 'gethtml_hoursales':
        if (!checkrights('gethtml_hoursales',1)) die(PERMISSION_DENIED);
            echo $template['gethtml_hoursales'];
        break;
        case 'gethtml_refuse':
        if (!checkrights('gethtml_refuse',1)) die(PERMISSION_DENIED);
            echo $template['gethtml_refuse'];
        break;
        case 'gethtml_refuse_and_orders':
        if (!checkrights('gethtml_refuse_and_orders',1)) die(PERMISSION_DENIED);
            echo $template['gethtml_refuse_and_orders'];
        break;
        case 'gethtml_itogovy':
        if (!checkrights('gethtml_itogovy',1)) die(PERMISSION_DENIED);
            echo $template['gethtml_itogovy'];
        break;
        case 'gethtml_remains':
            echo $template['gethtml_remains'];
        break;
        case 'gethtml_cash_remains':
            echo $template['gethtml_cash_remains'];
        break;
        case 'gethtml_cash_remainsdetailed':
            echo $template['gethtml_cash_remainsdetailed'];
        break;
        case 'gethtml_tipaotchet':
            echo $template['gethtml_tipaotchet'];
        break;
        case 'z_rights':
            if (!checkrights('zrights',1)) die(PERMISSION_DENIED);
            //print_r($_POST);
            
            $groupid=$_POST['groupid'];
            mysql_query("DELETE FROM `z_rights` WHERE groupid=".addslashes($groupid));  
             if (isset($_POST['rights'])){
                foreach($_POST['rights'] as $k=>$v){
                    $arr=array();
                    foreach($v as $k1=>$v1){
                        $arr[]='`'.addslashes($k1).'`="'.addslashes($v1).'"';
                    }
                    mysql_query("INSERT into `z_rights` SET groupid=".addslashes($groupid).", rightid='".addslashes($k)."', ".join(",",$arr));  
                    zlog(json_encode(array(
                            'id'=>$groupid,
                            'rightid'=>$k,
                            'arr'=>$arr 
                    )),1008);  
                }
             }
             $query=mysql_query("SELECT `z_rights_category`.`id` ,`rightname`,`name`,`view`,`edit`,`add`,`delete`,`sortid`,`print`,z_rights_category.`parentid` FROM z_rights_category LEFT JOIN z_rights ON z_rights.rightid=z_rights_category.id AND groupid=".addslashes($groupid).' ORDER by sortid');
            echo '<table><tr><td>Название</td><td>Просмотр <input type="checkbox" id="z_rights_view"> </td><td>Изменение <input type="checkbox" id="z_rights_edit"></td><td>Добавление <input type="checkbox" id="z_rights_add"></td><td>Удаление <input type="checkbox" id="z_rights_delete"></td><td>Печать <input type="checkbox" id="z_rights_print"></td></tr>';
            while($row=mysql_fetch_assoc($query)){
                echo '<tr class="z_right'.$row['parentid'].'"><td>'.$row['name'].'</td>
                <td><input type="checkbox" name="rights['.$row['id'].'][view]" class="z_rights_view" value="1" '.($row['view']==1?'checked="checked"':'').'></td>
                <td><input type="checkbox" name="rights['.$row['id'].'][edit]" class="z_rights_edit" value="1" '.($row['edit']==1?'checked="checked"':'').'></td>
                <td><input type="checkbox" name="rights['.$row['id'].'][add]" class="z_rights_add" value="1" '.($row['add']==1?'checked="checked"':'').'></td>
                <td><input type="checkbox" name="rights['.$row['id'].'][delete]" class="z_rights_delete"  value="1" '.($row['delete']==1?'checked="checked"':'').'></td>
                <td><input type="checkbox" name="rights['.$row['id'].'][print]" class="z_rights_print"  value="1" '.($row['print']==1?'checked="checked"':'').'></td>
                </tr>';
            }  
            echo '</table>';
           
        break;
        case 'get_z_rights':
            if (!checkrights('zrights',1)) die(PERMISSION_DENIED);
            $groupid=$_POST['groupid'];
            $query=mysql_query("SELECT `z_rights_category`.`id` ,`rightname`,`name`,`view`,`edit`,`add`,`delete`,`print`,`sortid`,z_rights_category.`parentid` FROM z_rights_category LEFT JOIN z_rights ON z_rights.rightid=z_rights_category.id AND groupid=".addslashes($groupid).' WHERE `z_rights_category`.`parentid`=0 ORDER by sortid');
                
             echo '<div id="grid_z_rights" class="grid_z_rights"><table><tr><td>Название</td><td>Просмотр <input type="checkbox" id="z_rights_view"> </td><td>Изменение <input type="checkbox" id="z_rights_edit"></td><td>Добавление <input type="checkbox" id="z_rights_add"></td><td>Удаление <input type="checkbox" id="z_rights_delete"></td><td>Печать <input type="checkbox" id="z_rights_print"></td></tr>';
            while($row=mysql_fetch_assoc($query)){
                echo '<tr class="z_right'.$row['parentid'].'"><td>'.$row['name'].'</td>
                <td class="dright"><input type="checkbox" name="rights['.$row['id'].'][view]" class="z_rights_view" value="1" '.($row['view']==1?'checked="checked"':'').'></td>
                <td colspan="4"></td>
                </tr>';
                 
                 $query2=mysql_query("SELECT `z_rights_category`.`id` ,`rightname`,`name`,`view`,`edit`,`add`,`delete`,`print`,`sortid`,z_rights_category.`parentid` FROM z_rights_category LEFT JOIN z_rights ON z_rights.rightid=z_rights_category.id AND groupid=".addslashes($groupid).' WHERE `z_rights_category`.`parentid`="'.$row['id'].'" ORDER by sortid');
                 while($row2=mysql_fetch_assoc($query2)){
                        echo '<tr class="z_right'.$row2['parentid'].'"><td>'.$row2['name'].'</td>
                        <td class="dright"><input type="checkbox" name="rights['.$row2['id'].'][view]" class="z_rights_view" value="1" '.($row2['view']==1?'checked="checked"':'').'></td>
                        <td class="dright"><input type="checkbox" name="rights['.$row2['id'].'][edit]" class="z_rights_edit" value="1" '.($row2['edit']==1?'checked="checked"':'').'></td>
                        <td class="dright"><input type="checkbox" name="rights['.$row2['id'].'][add]" class="z_rights_add" value="1" '.($row2['add']==1?'checked="checked"':'').'></td>
                        <td class="dright"><input type="checkbox" name="rights['.$row2['id'].'][delete]" class="z_rights_delete"  value="1" '.($row2['delete']==1?'checked="checked"':'').'></td>
                        <td class="dright"><input type="checkbox" name="rights['.$row2['id'].'][print]" class="z_rights_print"  value="1" '.($row2['print']==1?'checked="checked"':'').'></td>
                        </tr>';
                 }
                 
            }  
            echo '</table>';
            echo '<script>
                $(function() {
                    $("#groupid").change(function() {
                        load_rights();
                    });
                    $("#z_rights_view").click(function(){
                        if (this.checked){
                            $(".z_rights_view").attr("checked","checked");
                        }else{
                            $(".z_rights_view").removeAttr("checked");
                        }
                        
                    });
                    $("#z_rights_edit").click(function(){
                        if (this.checked){
                            $(".z_rights_edit").attr("checked","checked");
                        }else{
                            $(".z_rights_edit").removeAttr("checked");
                        }
                        
                    });
                    $("#z_rights_delete").click(function(){
                        if (this.checked){
                            $(".z_rights_delete").attr("checked","checked");
                        }else{
                            $(".z_rights_delete").removeAttr("checked");
                        }
                        
                    });
                    $("#z_rights_add").click(function(){
                        if (this.checked){
                            $(".z_rights_add").attr("checked","checked");
                        }else{
                            $(".z_rights_add").removeAttr("checked");
                        }
                        
                    });
                    $("#z_rights_print").click(function(){
                        if (this.checked){
                            $(".z_rights_print").attr("checked","checked");
                        }else{
                            $(".z_rights_print").removeAttr("checked");
                        }
                        
                    });
        
    });
    </script>';  
        break;
        case 'get_window_combo':
            if (!checkrights('get_window_combo',1)) die(PERMISSION_DENIED);
            echo $template['get_window_combo'];
        break;
        case 'get_window_z_rights':
            if (!checkrights('zrights',1)) die(PERMISSION_DENIED);
            echo '<form method="post" id="form_z_rights" action="/company/ajax.php?do=z_rights" role="form">';
             $query1=mysql_query("SELECT id,name FROM `s_role` ORDER by id ASC");
             echo '<div class="disturbed"><select name="groupid" id="groupid" class="form-control">';
             $id=-1;
             while($row1=mysql_fetch_assoc($query1)){
                 if ($id<0) $id=$row1['id'];
                echo '<option value="'.$row1['id'].'">'.$row1['name'].'</option>';
             }
             echo '</select></div><div class="righttd-content">';
             $query=mysql_query("SELECT `z_rights_category`.`id` ,`rightname`,`name`,`view`,`edit`,`add`,`delete`,`print`,`sortid`,z_rights_category.`parentid` FROM z_rights_category LEFT JOIN z_rights ON z_rights.rightid=z_rights_category.id AND groupid=".addslashes($id).' WHERE `z_rights_category`.`parentid`=0 ORDER by sortid');
                
             echo '<div id="grid_z_rights" class="grid_z_rights"><table><tr><td>Название</td><td>Просмотр <input type="checkbox" id="z_rights_view"> </td><td>Изменение <input type="checkbox" id="z_rights_edit"></td><td>Добавление <input type="checkbox" id="z_rights_add"></td><td>Удаление <input type="checkbox" id="z_rights_delete"></td><td>Печать <input type="checkbox" id="z_rights_print"></td></tr>';
            while($row=mysql_fetch_assoc($query)){
                echo '<tr class="z_right'.$row['parentid'].'"><td>'.$row['name'].'</td>
                <td class="dright"><input type="checkbox" name="rights['.$row['id'].'][view]" class="z_rights_view" value="1" '.($row['view']==1?'checked="checked"':'').'></td>
                <td colspan="4"></td>
                </tr>';
                 
                 $query2=mysql_query("SELECT `z_rights_category`.`id` ,`rightname`,`name`,`view`,`edit`,`add`,`delete`,`print`,`sortid`,z_rights_category.`parentid` FROM z_rights_category LEFT JOIN z_rights ON z_rights.rightid=z_rights_category.id AND groupid=".addslashes($id).' WHERE `z_rights_category`.`parentid`="'.$row['id'].'" ORDER by sortid');
                 while($row2=mysql_fetch_assoc($query2)){
                        echo '<tr class="z_right'.$row2['parentid'].'"><td>'.$row2['name'].'</td>
                        <td class="dright"><input type="checkbox" name="rights['.$row2['id'].'][view]" class="z_rights_view" value="1" '.($row2['view']==1?'checked="checked"':'').'></td>
                        <td class="dright"><input type="checkbox" name="rights['.$row2['id'].'][edit]" class="z_rights_edit" value="1" '.($row2['edit']==1?'checked="checked"':'').'></td>
                        <td class="dright"><input type="checkbox" name="rights['.$row2['id'].'][add]" class="z_rights_add" value="1" '.($row2['add']==1?'checked="checked"':'').'></td>
                        <td class="dright"><input type="checkbox" name="rights['.$row2['id'].'][delete]" class="z_rights_delete"  value="1" '.($row2['delete']==1?'checked="checked"':'').'></td>
                        <td class="dright"><input type="checkbox" name="rights['.$row2['id'].'][print]" class="z_rights_print"  value="1" '.($row2['print']==1?'checked="checked"':'').'></td>
                        </tr>';
                 }
                 
            }  
            echo '</table></div><br /> <a href="#" class="btn btn-success" onclick="set_rights();return false;">Сохранить</a> <br /><br /></form>';   
            echo '<script>
            $(function() {
                $("#groupid").change(function() {
                    load_rights();
                });
                $("#z_rights_view").click(function(){
                    if (this.checked){
                        $(".z_rights_view").attr("checked","checked");
                    }else{
                        $(".z_rights_view").removeAttr("checked");
                    }
                    
                });
                $("#z_rights_edit").click(function(){
                    if (this.checked){
                        $(".z_rights_edit").attr("checked","checked");
                    }else{
                        $(".z_rights_edit").removeAttr("checked");
                    }
                    
                });
                $("#z_rights_delete").click(function(){
                    if (this.checked){
                        $(".z_rights_delete").attr("checked","checked");
                    }else{
                        $(".z_rights_delete").removeAttr("checked");
                    }
                    
                });
                $("#z_rights_add").click(function(){
                    if (this.checked){
                        $(".z_rights_add").attr("checked","checked");
                    }else{
                        $(".z_rights_add").removeAttr("checked");
                    }
                    
                });
                $("#z_rights_print").click(function(){
                    if (this.checked){
                        $(".z_rights_print").attr("checked","checked");
                    }else{
                        $(".z_rights_print").removeAttr("checked");
                    }
                    
                });
                
            });
            </script></div>';  
                    
        break;
        case 'get_window_setprinter':
                if (!checkrights('show_design_menu',2)) die(PERMISSION_DENIED);
                echo '<div>';
                $query1=mysql_query("SELECT id,name FROM `s_subdivision` ORDER by id ASC");
                echo '<select id="subdivisionid"  class="form-control">';
                while($row1=mysql_fetch_assoc($query1)){
                    echo '<option value="'.$row1['id'].'">'.$row1['name'].'</option>';
                }
                echo '</select></div>';
                
                
                
        break;
        
        case 'setprinter':
            if (!checkrights('show_design_menu',2)) die(PERMISSION_DENIED);
            $subid=$_GET['pr_id'];
            $catid=$_GET['cat_id'];
            $table=$_GET['table'];
            setSubDivision($subid,$catid,$table);
            zlog($catid,1201); 
            
            $query1=mysql_query("SELECT * FROM ".$table." WHERE id=".$catid);
            if (mysql_numrows($query1)){
                $row=mysql_fetch_array($query1);
                
                
                //Полюшко-поле, Полюшко, широко поле.
                foreach($row as $k=>$v){
                    if (!empty($fields[$table][$k]['type']))
                    switch ($fields[$table][$k]['type']){
                        case 'db_select': $row[$k]=get_select_val($fields[$table][$k]['db_select'],$v); break; 
                        case 'db_grid': $row[$k]=get_grid($fields[$table][$k]['db_grid'],$v); break; 
                        case 'checkbox': $row[$k]=($row[$k]==1?'Да':'Нет'); break;
                    }
                     if (!empty($fields[$table][$k]['after_text'])){
                        $row[$k].=$fields[$table][$k]['after_text'];
                    }
                }
                
                if ($row['isgroup']==1) $row['state']='closed'; else $row['state']='open';
              
                
                echo json_encode($row);
            }
            
            
            //mysql_query("UPDATE `t_menu_items` SET printer='".$subid."' WHERE parentid='".$catid."' AND isgroup=0");
           
            
        break;
        case 'check_ver':
            echo VERSION;
        break;
        case 'get_window_feedback':
            echo $template['get_window_feedback'];
        break;
        case 'save_feedback':
            mysql_query("INSERT into `z_feedback` SET message='".addslashes($_POST['message'])."',`date`='".time()."', user='".$_SESSION['user']."',userid='".$_SESSION['userid']."'");
            zlog(mysql_insert_id(),1305); 
            $message='<b>Аккаунт</b><br />'.$_SESSION['base'].'<br /><br />
            <b>Юзер</b><br />'.$_SESSION['user'].'<br /><br />
            <b>Сообщение</b><br />'.$_POST['message'].'<br /><br />';
            sendmail('Обратная связь с бэка',$message,'info@paloma365.kz');
            echo 'Сообщение успешно отправлено';  
        break;
        case 'loadform':
            $form=$_GET['form'];
            if (!checkrights($form,1)) die(PERMISSION_DENIED); 
            $res=array();
            switch($form){
                case 'html_exchange_data':
                    $res['title']='Выгрузка счетов в файл';
                    $res['table']=$template['html_exchange_data'];
                break; 
                case 'html_exchange_template':
                    $res['title']='Выгрузка';
                    $res['table']=$template['html_exchange_template'];
                break; 
                case 'html_import_csv':
                    $res['title']='Импорт товаров из файла';
                    $res['table']=$template['html_import_csv'];
                break;                
            }
                echo json_encode($res);
        break;
        case 'gethtml_conductor':
        //if (!checkrights('gethtml_conductor',1)) die(PERMISSION_DENIED);
            echo $template['gethtml_conductor'];
        break;
        case 'html_exchange_data':
            if (!checkrights('html_exchange_data',1)) die(PERMISSION_DENIED); 
            

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
            
             $where=array();
             if ($_POST['idautomated_point']>0){
                 $where[]='d_order.idautomated_point="'.addslashes($_POST['idautomated_point']).'"';
             }   
             
             if (isset($_POST['vidoplaty'])){
                 $where[]='d_order.paymentid '.(isset($_POST['nevid'])?'NOT':'').' IN ('.join(',',$_POST['vidoplaty']).')';
             }  
             
             if (!isset($_POST['exportCombo'])){
                 $where[]='t_order.parentid=0';
             }                       
             switch($_POST['chb']){
                 case 'zasmenu':
                     if ($_POST['chb_zasmenu']>0){
                        $where[]='changeid="'.addslashes($_POST['chb_zasmenu']).'"';
                         
                         $query=mysql_query("SELECT * FROM `d_changes`  
                         LEFT JOIN s_employee ON d_changes.employeeid=s_employee.id 
                         WHERE d_changes.id='".addslashes($_POST['chb_zasmenu'])."'
                         LIMIT 1");
                         $row=mysql_fetch_assoc($query);
                         // Смена_Кассир_от_08.05.2013 09_16_47_по_08.05.2013 19_58_40.txt
                         $title='Смена_'.str_replace(' ','_',$row['fio']).'_от_'.str_replace(' ','_',date('d.m.Y H_i_s',strtotime($row['dtopen']))).'_по_'.str_replace(' ','_',date('d.m.Y H_i_s',strtotime($row['dtclosed'])));
                         
                     }
                 break;
                 case 'zaperiod':
                     if (($_POST['chb_zaperiod1']!='')&&($_POST['chb_zaperiod2']!='')){
                        $where[]='d_order.creationdt BETWEEN STR_TO_DATE("'.addslashes($_POST['chb_zaperiod1']).'","%d.%m.%Y %H:%i:%s") AND STR_TO_DATE("'.addslashes($_POST['chb_zaperiod2']).'","%d.%m.%Y %H:%i:%s")';

                        
                         //$title='За период: с'.$_POST['chb_zaperiod1'].' по'.$_POST['chb_zaperiod2'];
                         $title='За_период_'.date('d.m.Y H_i_s',strtotime($_POST['chb_zaperiod1'])).'_по_'.date('d.m.Y H_i_s',strtotime($_POST['chb_zaperiod2']));
                         
                     }
                 break;
                 case 'smenperiod':
                     if (($_POST['chb_smenperiod1']!='')&&($_POST['chb_smenperiod2']!='')){
                        $where[]='d_changes.dtopen BETWEEN STR_TO_DATE("'.addslashes($_POST['chb_smenperiod1']).'","%d.%m.%Y %H:%i:%s") AND STR_TO_DATE("'.addslashes($_POST['chb_smenperiod2']).'","%d.%m.%Y %H:%i:%s")';
                        //$title='Смены за период: с'.$_POST['chb_smenperiod1'].' по'.$_POST['chb_smenperiod2'];
                        $title='Смены_за_период_'.$row['name'].'_от_'.date('d.m.Y H_i_s',strtotime($_POST['chb_smenperiod1'])).'_по_'.date('d.m.Y H_i_s',strtotime($_POST['chb_smenperiod2']));
                     }
                 break;
             }
             
               
            
               
            $res=mysql_query("SELECT *
                                FROM ( SELECT d_order.id,
                                       DATE_FORMAT(d_order.dtclose,'%d.%m.%Y %H:%i:%s'),
                                       s_clients.idlink AS idclient,
                                       s_types_of_payment.idlink AS idtypepay,
                                       d_order.guestcount,
                                       s_objects.idlink AS idtable,
                                       s_employee.idlink AS idemployee,
                                       ROUND(d_order.totalsum,0),
                                       ROUND(d_order.servicepercent,0),
                                       ROUND(d_order.servicesum,0),
                                       d_order.discountpercent,
                                       ROUND(d_order.discountsum,0),
                                       d_order.handservicepercent,
                                       ROUND(d_order.handservicesum,0),
                                       s_items.idlink AS idfood,
                                       SUM(t_order.quantity) AS quantity,
                                       s_units_of_measurement.idlink AS measureid,
                                       ROUND(t_order.price,0) AS price,
                                       ROUND(t_order.price * (1 + (d_order.servicepercent * s_items.service - d_order.discountpercent * s_items.discount) / 100), 0) AS saleprice,
                                       s_printers.idlink AS printerid,
                                       ROUND(s_items.price,0) AS defaultprice,
                                       IF(ISNULL(children.cnumber), 0, 1) AS cccombo
                                       FROM t_order
                                            LEFT JOIN d_order ON d_order.id = t_order.orderid
                                      LEFT JOIN s_automated_point ON s_automated_point.id = d_order.idautomated_point
                                            LEFT JOIN s_clients ON s_clients.id = d_order.clientid
                                            LEFT JOIN s_types_of_payment ON s_types_of_payment.id = d_order.paymentid
                                            LEFT JOIN s_objects ON s_objects.id = d_order.objectid
                                            LEFT JOIN s_employee ON s_employee.id = d_order.employeeid
                                      LEFT JOIN d_changes ON d_changes.id = d_order.changeid
                                            LEFT JOIN s_subdivision as s_printers ON s_printers.id = t_order.printerid 
                                            LEFT JOIN s_items ON s_items.id = t_order.itemid
                                            LEFT JOIN s_units_of_measurement ON s_units_of_measurement.id = s_items.measurement
                                            LEFT JOIN ( SELECT parentid, COUNT(*) AS cnumber FROM t_order WHERE parentid>0 GROUP BY parentid ) AS children ON children.parentid=t_order.id
                                       ".(!empty($where)?'WHERE '.join(' AND ',$where):'')."
                                       GROUP BY t_order.itemid,
                                                t_order.orderid,
                                                t_order.price
                                ) AS t
                                WHERE t.quantity != 0
                                ORDER BY printerid");
                                echo mysql_error();
             
            header("Content-Type: application/download\n"); 
            
            header("Content-Disposition: attachment; filename=".$title.'.txt');
            while ($row=mysql_fetch_row($res)){
                echo join(';',$row).";\r\n";
            }
                        
        break;
        case 'html_exchange_template':
            if (!checkrights('html_exchange_template',1)) die(PERMISSION_DENIED); 
            $name=$_POST['name'];
            unset($_POST['name']);
            mysql_query("INSERT INTO s_exchange SET `name`='".$name."',`values`='".json_encode($_POST)."'");
                                    
        break;
        //////////////////vlad
        case 'html_spr_data':
            //if (!checkrights('html_exchange_data',1)) die(PERMISSION_DENIED);             
               
            $res=mysql_query("select if(t1.isgroup=1,1,2) as type,(t1.idlink) as id,if(ISNULL(t2.id),0,t2.idlink) as idgroup,t1.name,'шт',t1.price from s_items as t1
left JOIN s_items as t2 on t1.parentid=t2.id
order by t1.parentid");
                                echo mysql_error();
             
            header("Content-Type: application/download\n"); 
            $title='Товары_'.date('d.m.Y H:i:s');
            header("Content-Disposition: attachment; filename=".$title.'.txt');
            while ($row=mysql_fetch_row($res)){
                echo join(';',$row).";\r\n";
            }
                        
        break;
        ///////////////////////////////////////////////////vlad
        case 'get_chart_changes':
            $res=array();
            $query1=mysql_query("SELECT d_changes.id, s_employee.fio, d_changes.dtopen, d_changes.dtclosed,d_changes.idautomated_point  
                FROM `d_changes` LEFT JOIN s_employee ON d_changes.employeeid=s_employee.id 
                ORDER by d_changes.id DESC");
            while($row1=mysql_fetch_assoc($query1)){
                if (checkNFrights('s_automated_point',$row['idautomated_point'],'view')) 
                    $res[]=array('id'=>$row1['id'],'title'=>'Смена '.$row1['fio'].' от '.date('d.m.Y H:i:s',strtotime($row1['dtopen'])).' по '.date('d.m.Y H:i:s',strtotime($row1['dtclosed'])));;
            }
            echo json_encode($res);
        break;
        case 'get_chart1':
             if (isset($_GET['change_id'])){
                 $id=(int)$_GET['change_id'];
             }
             else{
                $query1=mysql_query("SELECT d_changes.id, s_employee.fio, d_changes.dtopen, d_changes.dtclosed,d_changes.idautomated_point   FROM `d_changes` LEFT JOIN s_employee ON d_changes.employeeid=s_employee.id ORDER by d_changes.id DESC LIMIT 1");
                if (checkNFrights('s_automated_point',$row['idautomated_point'],'view')){
                    $row1=mysql_fetch_assoc($query1);        
                    $id=$row1['id'];
                }
             }
            $query=mysql_query("SELECT s_items.name,
                SUM(t_order.quantity) as counts,  
                SUM(t_order.price*t_order.quantity) as bablo
                FROM t_order 
                LEFT JOIN d_order ON t_order.orderid=d_order.id 
                LEFT JOIN s_items ON s_items.id=t_order.itemid 
                WHERE d_order.changeid='".$id."' GROUP by itemid"); 
            //echo mysql_error();
            $res=array();
            $res2=array();
            while($row=mysql_fetch_assoc($query)){
                $res[]=array('c'=>array(array('v'=>$row['name']),array('v'=>(int)$row['counts'])));              
                $res2[]=array('c'=>array(array('v'=>$row['name']),array('v'=>(int)$row['bablo'])));              
            }
           
        $ret=array();
        $ret['chart1']['cols']=array(
            array('label'=>'Topping','type'=>'string'),
            array('label'=>'Slices','type'=>'number')
        );
        $ret['chart1']['rows']=$res;
        
        $ret['chart2']['cols']=array(
            array('label'=>'Topping','type'=>'string'),
            array('label'=>'Slices','type'=>'number')
        );
        $ret['chart2']['rows']=$res2;
        
            //echo '['.join(',',$res).']';
            echo json_encode($ret);
            //print_r($ret);
            
        break;
        case 'get_chart2_changes':
            $res=array();
            $query1=mysql_query("SELECT id, dtopen FROM `d_changes` GROUP by date(dtopen) ORDER by dtopen DESC");
            while($row1=mysql_fetch_assoc($query1)){
                $res[]=array('id'=>date('d.m.Y',strtotime($row1['dtopen'])),'title'=>date('d.m.Y',strtotime($row1['dtopen'])));
            }
            echo json_encode($res);
        break;
        case 'get_chart2':
             if (isset($_GET['date'])){
                 $date=$_GET['date'];
             }
             else{
                $query1=mysql_query("SELECT id, dtopen FROM `d_changes` GROUP by date(dtopen) ORDER by dtopen DESC LIMIT 1");
                $row1=mysql_fetch_assoc($query1);        
                $date=date('d.m.Y',strtotime($row1['dtopen']));
             }
             
             $automated_point=array();
             $querys=mysql_query("SELECT id,name FROM s_automated_point");
             while($sm=mysql_fetch_assoc($querys)){
                $automated_point[$sm['id']]=$sm['name'];
             }
             
                    
                    
            $query=mysql_query("SELECT ap.id, ap.name, DATE_FORMAT(d_order.dtclose, '%Y.%m.%d %H:00') AS dt, DATE_FORMAT(d_order.dtclose, '%k') AS `hours`, round(SUM(d_order.totalsum),0) as totalsum
                    FROM s_automated_point AS ap
                    LEFT JOIN d_order ON d_order.idautomated_point = ap.id
                    WHERE DATE_FORMAT(d_order.dtclose,'%d.%m.%Y')='".$date."'
                    GROUP BY hours, ap.id
                    ORDER BY dt"); 
            //echo mysql_error();
            $res=array();
            $gr=array();
            while($row=mysql_fetch_assoc($query)){
                
                $gr[$row['hours']][$row['id']]=(int)$row['totalsum'];
                
            }
            
            
            
            for ($i=0; $i<=23; $i++){
                $rr=array();
                $rr[]=array('v'=>$i);
                foreach($automated_point as $aid=>$v){
                    $rr[]=array('v'=>(int)$gr[$i][$aid]);
                }
                $res[]=array('c'=>$rr);
            }

           
        $ret=array();
        $automated_pointarray=array();
        $automated_pointarray[]=array('label'=>'Смена','type'=>'string');
        foreach($automated_point as $k=>$aid){
            $automated_pointarray[]=array('label'=>$aid,'type'=>'number');
        }
        $ret['cols']=$automated_pointarray;
        $ret['rows']=$res;
        
            //echo '['.join(',',$res).']';
            echo json_encode($ret);
            //print_r($ret);
            
        break;
        case 'get_note_table': 
                if (!checkrights('s_note',1)) die(PERMISSION_DENIED);
                $res=array();
                if (isset($_GET['id'])){
                    $query=mysql_query("SELECT id,name FROM `s_note` WHERE itemid='".addslashes($_GET['id'])."'"); 
                    $totalrows=mysql_numrows($query);   
                    while($row=mysql_fetch_array($query)){
                        $row['iconCls']='tree-file'; 
                        $row['state']='open'; 
                        $res[]=$row;
                    }
                }

                //echo json_encode($res);
                $answer = array();
                $answer["totalrows"] = $totalrows;
                $answer["rows"] = $res;
                echo json_encode($answer);
        break;
        case 'approve_mail':
            if (isset($_SESSION['admin'])){
            include('mysql_connect_ajax.php');
            //set_balance($_SESSION['userid'],'5000','Пополнение баланса через Зота');
            $query=mysql_query("SELECT `regkey`,`email`,`status` FROM `dbisoftik`.`s_accounts` WHERE id='".$_SESSION['userid']."' LIMIT 1",$db_sconn);
            $row=mysql_fetch_assoc($query);
            if ($row['status']!=1){
                $message='
                        Вас приветствует облачный сервис <a href="http://paloma365.kz">Paloma365</a>. <br />
                        Для подтверждение регистрации перейдите по следующей <a href="http://paloma365.kz/mail.php?key='.$row['regkey'].'">ссылке</a><br />
                        <br />Желаем удачной работы.';
                sendmail('Paloma365 – Подтверждение почты.',$message,$row['email']); 
                echo 'Вам выслано письмо на почту';
            }else{
                echo 'Ваш емейл уже потвержден';
            }
            }
        break;
        case 'show_account_settings':
            if (isset($_SESSION['admin'])){
            include('mysql_connect_ajax.php');
            //set_balance($_SESSION['userid'],'5000','Пополнение баланса через Зота');
            $query=mysql_query("SELECT `fio`,`phone`,`email`,`timezone`,`balance`,`details`,`status` FROM `dbisoftik`.`s_accounts` WHERE id='".$_SESSION['userid']."' LIMIT 1",$db_sconn);
            
            echo mysql_error();
            $row=mysql_fetch_array($query); 
            
            
            
            $query_p=mysql_query("SELECT * FROM `dbisoftik`.`prices`",$db_sconn);
            $prices=array();
            while($rowp=mysql_fetch_assoc($query_p)){
                $prices[$rowp['type']]=$rowp['price'];
            }
            
            echo '<div class="account_settings"><form action="ajax.php?do=save_account_settings" method="post" id="show_account_settings">
            <table>
                <tr><td class="settingstext">ФИО</td><td><input value="'.$row['fio'].'" class="settingsinput" name="fio"></td></tr>
                <tr><td class="settingstext">Телефон</td><td><input value="'.$row['phone'].'" class="settingsinput" name="phone"></td></tr>
                <tr><td class="settingstext">Емейл</td><td><input value="'.$row['email'].'" class="settingsinput" name="email"> </td></tr>
                <tr><td class="settingstext">Временная зона</td><td><select class="settingsinput" name="timezone">'.getTimeZones($row['timezone']).'</select></td></tr>
                <tr><td class="settingstext">Пароль</td><td><input value="" type="password" name="password" class="settingsinput"></td></tr>
                <tr><td class="settingstext">Реквизиты</td><td><textarea name="details" style="width: 300px;height: 104px;">'.$row['details'].'</textarea></td></tr>
            </table>
            <br />
            <a href="#" class="btn btn-primary"  onclick="save_account_settings()">Сохранить</a>
            </form></div><br /><br />
            ';
            
            if ($row['status']!=1) echo '<a href="#" class="btn btn-primary"  onclick="approve_mail()">Подтвердить емейл</a>
            ';
            
            /*
            echo '
            <br /><br />Аккаунт <b>'.str_replace('db_','',$_SESSION['base']).'</b> (ID '.$_SESSION['userid'].')<br />';
                            echo 'Ваш баланс: '.$row['balance'].'.  <a href="#" onclick="topup();return false;">Пополнить баланс</a></div>';
            
            echo '
            
                            
                            
<ul class="nav nav-tabs" id="sett_tabs">
  <li class="active"><a href="#settings_info" data-toggle="tab">Общая информация об аккаунте</a></li>
  <li><a href="#settings_hystory" data-toggle="tab">История транзакций</a></li>
  <li><a href="#settings_invoice" data-toggle="tab">Счета</a></li>
</ul>

<div class="tab-content" id="sett_content">
  <div class="tab-pane fade in active" id="settings_info" style="background:#f4f4f4; padding:20px;">
          <p class="tranz">';
                            
                            echo '<table class="melkiynubas">
                                <tr>
                                    <td>Объект</td>
                                    <td>Статус</td>
                                    <td>Окончание</td>
                                    <td>Цена/мес.</td>
                                    <td>Действие</td>
                                </tr>';
                            $itogo=0;
                            $query=mysql_query("SELECT * FROM `s_automated_point`",$db);
                            while($row=mysql_fetch_assoc($query)){
                                $query2=mysql_query("SELECT * FROM `t_workplace` WHERE apid='".$row['id']."'",$db);
                                $price=$prices['automated_point'];
                                $itogo+=$price;
                                if ($row['expiration_date']!=''){
                                    $exp_date=date('d.m.Y',strtotime('+1 month',strtotime($row['expiration_date'])));
                                }else{
                                    $exp_date=date('d.m.Y',strtotime('+1 month',time()));
                                }
                                
                                $remain = strtotime($row['expiration_date']) - time();
                                $days = floor($remain/86400);
                                $str='';
                                if($days > 0) $str=' (осталось '.$days.' '.morph($days,'день','дня','дней').')';
                                
                                //$exp_date=date('d.m.Y',strtotime('+1 month',strtotime($row['expirate_date'])));
                                echo '<tr id="pay'.$row['id'].'"><td><i class="glyphicon glyphicon-home" style="color:#FF740A"></i> '.$row['name']. '</td><td>'.($row['status']==1?'<i class="glyphicon glyphicon-ok-circle" style="color:green"></i>':'<i class="glyphicon glyphicon-remove-circle" style="color:red"></i>').'</td><td><span class="expdate">'.($row['expiration_date']!=''?date('d.m.Y',strtotime($row['expiration_date'])):'-').$str.'</span></td><td>'.$price.'</a><td>
                                
                                        
                                        <a href="#" onclick=\'pay("Торговая точка","'.$row['name'].'","'.$row['id'].'","'.$price.'","'.$exp_date.'","pay'.$row['id'].'"); return false;\'><i class="glyphicon glyphicon-usd"></i></a> </td></tr>';
                                if (mysql_num_rows($query2)){
                                    while($row2=mysql_fetch_assoc($query2)){ 
                                        
                                        $price=$prices['workplace'];
                                        $itogo+=$prices['workplace'];
                                        if ($row2['expiration_date']!=''){
                                            $exp_date=date('d.m.Y',strtotime('+1 month',strtotime($row2['expiration_date'])));
                                        }else{
                                            $exp_date=date('d.m.Y',strtotime('+1 month',time()));
                                        }
                                        
                                        $remain = strtotime($row2['expiration_date']) - time();
                                        $days = floor($remain/86400);
                                        $str='';
                                        if($days > 0) $str=' (осталось '.$days.' '.morph($days,'день','дня','дней').')';
                                        
                                        $exp_date=$row['expiration_date'];
                                        echo '<tr id="zpay'.$row2['id'].'"><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="glyphicon glyphicon-user" style="color:#617C9C"></i> '.$row2['name']. '</td><td>'.($row2['status']==1?'<i class="glyphicon glyphicon-ok-circle" style="color:green"></i>':'<i class="glyphicon glyphicon-remove-circle" style="color:red"></i>').'</td><td><span class="expdate">'.($row2['expiration_date']!=''?date('d.m.Y',strtotime($row2['expiration_date'])):'-').$str.'</span></td><td>'.$price.'</a><td>
                                        
                                        
                                        <a href="#" onclick=\'pay("Рабочее место","'.$row2['name'].'","'.$row2['id'].'","'.$price.'","'.$exp_date.'","zpay'.$row2['id'].'"); return false;\'><i class="glyphicon glyphicon-usd"></i></a> 
                                    </td></tr>';                              
                                    }                           
                                }                    
                            }
                            //<a href="#"><i class="glyphicon glyphicon-off" style="color:green"></i></a> <a href="#" onclick="payall()">Оплатить всё</a>
                            echo '<tr><td colspan="3"><b>Итого к оплате:</b></td><td><b>'.$itogo.'</b></td><td></td></tr>';
                            
                            
                            echo '</table>

          </div>
          <div class="tab-pane" id="settings_hystory" style="background:#f4f4f4;padding:20px">
          ';
                    
                    $query=mysql_query("SELECT * FROM `s_transaction` ORDER by id DESC",$db);
                    if (mysql_numrows($query)){
                        echo '<ul type="square">';
                        while($row=mysql_fetch_assoc($query)){
                            echo '<li><b>'.$row['date'].'</b> '.$row['name'].' <b>'.$row['amount'].'</b></li>';
                        }
                        echo '</ul>';
                    }else{
                        echo 'Пусто.';
                    }
                    echo '
          </div>
          <div class="tab-pane" id="settings_invoice" style="background:#f4f4f4;padding:20px">';
          $query=mysql_query("SELECT * FROM `dbisoftik`.`z_invoice` WHERE acid='".$_SESSION['userid']."' and usertype=0 ORDER by id DESC",$db_sconn);
          if (mysql_numrows($query)){
              echo '<table class="melkiynubas invoices_here">
                            <tr>
                                <td>#</td>
                                <td>Дата</td>
                                <td>Сумма</td>
                                <td>Описание</td>
                                <td>Статус</td>
                            </tr>
                        ';
                        
                        
                        while($row=mysql_fetch_assoc($query)){
                            echo '<tr>
                            <td><a href="#" onclick="show_invoice(\''.$row['id'].'\'); return false;"><b>P'.str_pad($row['id'], 10, '0', STR_PAD_LEFT).'</b></a></td>
                                <td>'.date('d.m.Y H:i:s',strtotime($row['date'])).'</td> 
                                <td>'.str_replace(' ',"&nbsp;",number_format($row['amount'],2,'.',' ')).'</td> 
                                <td>'.$row['description'].'</td> 
                                <td>'.($row['status']?'<i class="glyphicon glyphicon-ok-circle" style="color:green"></i>':'<i class="glyphicon glyphicon-remove-circle" style="color:red"></i>').'</td> 
                            </tr>';
                        }
                        echo '</table>';
          }else{
              echo 'Пусто.';
          }
          echo '
          </div>
        </div>';*/
            
            }
            //mysql_close($db_sconn);
        break;
        case 'change_pass':
            
            
            
            if (isset($_SESSION['admin'])){
                $query=mysql_query("SELECT `id`,`fio`,`phone`,`email`,`timezone`,`balance` FROM `dbisoftik`.`s_accounts` WHERE id='".$_SESSION['userid']."' LIMIT 1");
            }else{
                $query=mysql_query("SELECT  s_employee.`id`,
                                            s_employee.`fio`,
                                            s_employee.`name`,
                                            GROUP_CONCAT(DISTINCT s_role.name SEPARATOR ', ') AS role, 
                                            GROUP_CONCAT(DISTINCT s_interfaces.name SEPARATOR ', ') AS interface,
                                            GROUP_CONCAT(DISTINCT t_workplace.name SEPARATOR ', ') AS wp  
                    FROM `s_employee`
                    LEFT JOIN t_employee_role ON t_employee_role.employeeid=s_employee.id
                    LEFT JOIN s_role ON s_role.id=t_employee_role.rollid
                    LEFT JOIN t_employee_interface ON t_employee_interface.employeeid=s_employee.id
                    LEFT JOIN s_interfaces ON s_interfaces.id=t_employee_interface.rollid
                    LEFT JOIN t_employee_workplace ON t_employee_workplace.employeeid=s_employee.id
                    LEFT JOIN t_workplace ON t_workplace.id=t_employee_workplace.wpid                 
                    WHERE s_employee.id='".$_SESSION['userid']."' LIMIT 1");
                $row=mysql_fetch_assoc($query);
                echo '<b>Фамилия:</b> '.$row['fio'].'<br />';
                echo '<b>Логин:</b> '.$row['name'].'<br />';
                echo '<b>Права:</b> '.$row['role'].'<br />';
                echo '<b>Интерфейсы:</b> '.$row['interface'].'<br />';
                echo '<b>Рабочие места:</b> '.$row['tw'].'<br /><br />';
            }
            
            echo '<table>
                <tr>
                    <td>Введите новый пароль</td>
                    <td> <input id="newpassword" type="password"></td>
                </tr>
                <tr>
                    <td>Подтвердите пароль</td>
                    <td> <input id="newpassword2" type="password"></td>
                </tr>
                </table>';
        break;
        case 'account_payment':
            if (!checkrights('account_payment',1)) die(PERMISSION_DENIED);
            include('mysql_connect_ajax.php');
            
            if (isset($_SESSION['admin'])){
                $query=mysql_query("SELECT `id`,`fio`,`phone`,`email`,`timezone`,`balance` FROM `dbisoftik`.`s_accounts` WHERE id='".$_SESSION['userid']."' LIMIT 1",$db_sconn);
            }else{
                $query=mysql_query("SELECT `id`,`fio`,`phone`,`email`,`timezone`,`balance` FROM `dbisoftik`.`s_accounts` WHERE db='".$_SESSION['base']."' LIMIT 1",$db_sconn);
            }
            
            
            
            echo mysql_error();
            $row=mysql_fetch_array($query); 
            $account_id=$row['id'];
            //echo "SELECT `fio`,`phone`,`email`,`timezone`,`balance` FROM `dbisoftik`.`s_accounts` WHERE db='".$_SESSION['base']."' LIMIT 1";
            
            
            $query_p=mysql_query("SELECT * FROM `dbisoftik`.`prices`",$db_sconn);
            $prices=array();
            while($rowp=mysql_fetch_assoc($query_p)){
                $prices[$rowp['type']]=$rowp['price'];
            }          
            
            echo '<div class="righttd-content"><div style="padding:20px;">
            Аккаунт <b>'.str_replace('db_','',$_SESSION['base']).'</b> (ID '.$account_id.')<br />';
                            echo 'Ваш баланс: <span class="mybalance">'.$row['balance'].'</span>.  <a href="#" onclick="topup();return false;">Пополнить баланс</a>';
            
            echo '</div>
            
                            
                            
<ul class="nav nav-tabs" id="sett2_tabs">
  <li class="active"><a href="#settings_info1" data-toggle="tab">Общая информация об аккаунте</a></li>
  <li><a href="#settings_hystory1" data-toggle="tab">История транзакций</a></li>
  <li><a href="#settings_invoice1" data-toggle="tab">Счета</a></li>
</ul>

<div class="tab-content" id="sett2_content">
  <div class="tab-pane fade in active" id="settings_info1" style="background:#f4f4f4; padding:20px;">
          <p class="tranz">';
                            
                            echo '<table class="melkiynubas table-striped">
                                <tr>
                                    <td>Объект</td>
                                    <td>Статус</td>
                                    <td>Окончание</td>
                                    <td>Цена/мес.</td>
                                    <td>Действие</td>
                                </tr>';
                            $itogo=0;
                            $query=mysql_query("SELECT * FROM `s_automated_point`",$db);
                            while($row=mysql_fetch_assoc($query)){
                                $query2=mysql_query("SELECT * FROM `t_workplace` WHERE apid='".$row['id']."'",$db);
                                $price=$prices['automated_point'];
                                $itogo+=$price;
                                if ($row['expiration_date']!=''){
                                    $exp_date=date('d.m.Y',strtotime('+1 month',strtotime($row['expiration_date'])));
                                }else{
                                    $exp_date=date('d.m.Y',strtotime('+1 month',time()));
                                }
                                
                                $remain = strtotime($row['expiration_date']) - time();
                                $days = floor($remain/86400);
                                $str='';
                                if($days > 0) $str=' (осталось '.$days.' '.morph($days,'день','дня','дней').')';
                                
                                //$exp_date=date('d.m.Y',strtotime('+1 month',strtotime($row['expirate_date'])));
                                echo '<tr id="pay'.$row['id'].'"><td><i class="glyphicon glyphicon-home" style="color:#FF740A"></i> '.$row['name']. '</td><td>'.($row['status']==1?'<i class="glyphicon glyphicon-ok-circle" style="color:green"></i>':'<i class="glyphicon glyphicon-remove-circle" style="color:red"></i>').'</td><td><span class="expdate">'.($row['expiration_date']!=''?date('d.m.Y',strtotime($row['expiration_date'])):'-').$str.'</span></td><td>'.$price.'</a><td>
                                
                                        
                                        <a href="#" onclick=\'pay("Торговая точка","'.$row['name'].'","'.$row['id'].'","'.$price.'","'.$exp_date.'","pay'.$row['id'].'"); return false;\'><i class="glyphicon glyphicon-usd"></i></a> </td></tr>';
                                if (mysql_num_rows($query2)){
                                    while($row2=mysql_fetch_assoc($query2)){ 
                                        
                                        $price=$prices['workplace'];
                                        $itogo+=$prices['workplace'];
                                        if ($row2['expiration_date']!=''){
                                            $exp_date=date('d.m.Y',strtotime('+1 month',strtotime($row2['expiration_date'])));
                                        }else{
                                            $exp_date=date('d.m.Y',strtotime('+1 month',time()));
                                        }
                                        
                                        $remain = strtotime($row2['expiration_date']) - time();
                                        $days = floor($remain/86400);
                                        $str='';
                                        if($days > 0) $str=' (осталось '.$days.' '.morph($days,'день','дня','дней').')';
                                        
                                        $exp_date=$row['expiration_date'];
                                        echo '<tr id="zpay'.$row2['id'].'"><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="glyphicon glyphicon-user" style="color:#617C9C"></i> '.$row2['name']. '</td><td>'.($row2['status']==1?'<i class="glyphicon glyphicon-ok-circle" style="color:green"></i>':'<i class="glyphicon glyphicon-remove-circle" style="color:red"></i>').'</td><td><span class="expdate">'.($row2['expiration_date']!=''?date('d.m.Y',strtotime($row2['expiration_date'])):'-').$str.'</span></td><td>'.$price.'</a><td>
                                        
                                        
                                        <a href="#" onclick=\'pay("Рабочее место","'.$row2['name'].'","'.$row2['id'].'","'.$price.'","'.$exp_date.'","zpay'.$row2['id'].'"); return false;\'><i class="glyphicon glyphicon-usd"></i></a> 
                                    </td></tr>';                              
                                    }                           
                                }                    
                            }
                            //<a href="#"><i class="glyphicon glyphicon-off" style="color:green"></i></a> <a href="#" onclick="payall()">Оплатить всё</a>
                            echo '<tr><td colspan="3"><b>Итого к оплате:</b></td><td><b>'.$itogo.'</b></td><td></td></tr>';
                            
                            
                            echo '</table>

          </div>
          <div class="tab-pane" id="settings_hystory1" style="background:#f4f4f4;padding:20px">
          ';
                    
                    $query=mysql_query("SELECT * FROM `s_transaction` ORDER by id DESC",$db);
                    if (mysql_numrows($query)){
                        echo '<ul type="square">';
                        while($row=mysql_fetch_assoc($query)){
                            echo '<li><b>'.$row['date'].'</b> '.$row['name'].' <b>'.$row['amount'].'</b></li>';
                        }
                        echo '</ul>';
                    }else{
                        echo 'Пусто.';
                    }
                    echo '
          </div>
          <div class="tab-pane" id="settings_invoice1" style="background:#f4f4f4;padding:20px">';
          $query=mysql_query("SELECT * FROM `dbisoftik`.`z_invoice` WHERE acid='".$account_id."' and usertype=0 ORDER by id DESC",$db_sconn);
          if (mysql_numrows($query)){
              echo '<table class="melkiynubas invoices_here">
                            <tr>
                                <td>#</td>
                                <td>Дата</td>
                                <td>Сумма</td>
                                <td>Описание</td>
                                <td>Статус</td>
                            </tr>
                        ';
                        
                        
                        while($row=mysql_fetch_assoc($query)){
                            echo '<tr>
                            <td><a href="#" onclick="show_invoice(\''.$row['id'].'\'); return false;"><b>P'.str_pad($row['id'], 10, '0', STR_PAD_LEFT).'</b></a></td>
                                <td>'.date('d.m.Y H:i:s',strtotime($row['date'])).'</td> 
                                <td>'.str_replace(' ',"&nbsp;",number_format($row['amount'],2,'.',' ')).'</td> 
                                <td>'.$row['description'].'</td> 
                                <td>'.($row['status']?'<i class="glyphicon glyphicon-ok-circle" style="color:green"></i>':'<i class="glyphicon glyphicon-remove-circle" style="color:red"></i>').'</td> 
                            </tr>';
                        }
                        echo '</table>';
          }else{
              echo 'Пусто.';
          }
          echo '
          </div>
          </div>
          </div>
        </div>';
            
            
            mysql_close($db_sconn);
        break;
        case 'pay':
            if (!checkrights('account_payment',1)) die(PERMISSION_DENIED);
            include('mysql_connect_ajax.php');
            
            if (isset($_SESSION['admin'])){
                $query=mysql_query("SELECT `id`,`fio`,`phone`,`email`,`timezone`,`balance` FROM `dbisoftik`.`s_accounts` WHERE id='".$_SESSION['userid']."' LIMIT 1",$db_sconn);
            }else{
                $query=mysql_query("SELECT `id`,`fio`,`phone`,`email`,`timezone`,`balance` FROM `dbisoftik`.`s_accounts` WHERE db='".$_SESSION['base']."' LIMIT 1",$db_sconn);
            }
            
            echo mysql_error();
            $row=mysql_fetch_array($query); 
            $account_id=$row['id'];
            

           
                if (isset($_GET['type'])&&isset($_GET['amount'])&&isset($_GET['id'])){
                    
                    if ($_GET['type']=='Торговая точка'){
                        $type='s_automated_point';
                    }else{
                        $type='t_workplace';
                    }
                    $query=mysql_query("SELECT `balance` FROM `dbisoftik`.`s_accounts` WHERE id='".$account_id."' LIMIT 1",$db_sconn);
           
                    $b=mysql_fetch_assoc($query);
                    
                    $balance=intval($b['balance']);
                    $amount=intval($_GET['amount']);                                        
                                        
                                        
                    if ($balance>=$amount){
                        $qw=mysql_query("SELECT `id`,`name`,`status`,`expiration_date` FROM `".$type."` WHERE id='".addslashes($_GET['id'])."'",$db);
                        $row=mysql_fetch_assoc($qw);
                        
                        mysql_query("UPDATE `".$type."` SET status=1, expiration_date=(IF (expiration_date is null, DATE_ADD(NOW(), INTERVAL 1 MONTH), DATE_ADD(expiration_date, INTERVAL 1 MONTH))) WHERE id='".addslashes($_GET['id'])."'",$db);
                        
                        set_balance($account_id,(abs($amount)*-1),'Продление '.$_GET['type'].' '.$row['name'].'('.$row['id'].')');    
                        
                        $qw=mysql_query("SELECT `expiration_date` FROM `".$type."` WHERE id='".addslashes($_GET['id'])."'",$db);
                        $row=mysql_fetch_assoc($qw);      
                        
                        $remain = strtotime($row['expiration_date']) - time();
                        $days = floor($remain/86400);
                        if($days > 0) $str=' (осталось '.$days.' '.morph($days,'день','дня','дней').')';
                                
                                
                        echo json_encode(array('result'=>'ok','newbalance'=>($balance-$amount),'message'=>'Оплата успешно произведена','date'=>date('d.m.Y',strtotime($row['expiration_date'])).$str));
                    }else{
                        echo json_encode(array('result'=>'error','message'=>'Недостаточно средств'));        
                    }
                }
                
            
        break;
        case 'changemypass':
            $pass='';
            if ($_GET['password']!=''){
                $q=mysql_query("SELECT id FROM `s_employee` WHERE `password`='".md5(FISH.md5($_GET['password']))."' AND id!='".$_SESSION['userid']."'");
                if (!mysql_numrows($q)){
                 mysql_query("UPDATE  `s_employee` SET `password`='".md5(FISH.md5($_GET['password']))."' WHERE id='".$_SESSION['userid']."'");
                 $_SESSION['password']=md5(md5(FISH.md5($_GET['password'])));
                }else{
                    echo 'error';
                }
            }
        break;
        case 'save_account_settings':
            $pass='';
            if ($_POST['password']!=''){
                $pass=',`password`="'.md5(FISH.md5($_POST['password'])).'" ';
            }
            include('mysql_connect_ajax.php');
            mysql_query("UPDATE  `dbisoftik`.`s_accounts` SET `fio`='".addslashes($_POST['fio'])."',`phone`='".addslashes($_POST['phone'])."',`details`='".addslashes($_POST['details'])."',`email`='".addslashes($_POST['email'])."',`timezone`='".addslashes($_POST['timezone'])."'".$pass." WHERE id='".$_SESSION['userid']."'",$db_sconn);
            echo 'Данные успешно сохранены.';
            zlog($_SESSION['userid'],1009); 
            mysql_close($db_sconn);
        break;
        case 'gobackinfront':
            $_SESSION['userid']=$_SESSION['wid'];
            unset($_SESSION['fromfront']);
        break;
        case 'getBarcode':
            $q=mysql_query("SELECT MAX( SUBSTR( mainShtrih, 1, 12 ) ) as code FROM s_items WHERE mainShtrih LIKE '2%'");
            $row=mysql_fetch_assoc($q);
            if ($row['code']!=''){
                $barcode=$row['code']+1;
            }else{
                $barcode='200000000000';
            }
            echo generate_ean13($barcode);
        break;
        case 'getmain_charts':
            echo $template['getmain_charts'];
        break;
        case 'gethtml_posotr':
            echo $template['gethtml_posotr'];
        break;
        case 'employeeSessionDieFromFront':
            mysql_query('UPDATE s_employee SET isonline=0,last_action=UNIX_TIMESTAMP(now()) WHERE id='.$_SESSION['employeeid']);
            unset($_SESSION['employeeid']);
            unset($_SESSION['fromfront']);
            unset($_SESSION['interfaces']);
        break;
        case 'create_invoice':
            if (!checkrights('account_payment',1)) die(PERMISSION_DENIED);
            include('mysql_connect_ajax.php');
            
            if (isset($_SESSION['admin'])){
                $query=mysql_query("SELECT `id`,`fio`,`phone`,`email`,`timezone`,`balance` FROM `dbisoftik`.`s_accounts` WHERE id='".$_SESSION['userid']."' LIMIT 1",$db_sconn);
            }else{
                $query=mysql_query("SELECT `id`,`fio`,`phone`,`email`,`timezone`,`balance` FROM `dbisoftik`.`s_accounts` WHERE db='".$_SESSION['base']."' LIMIT 1",$db_sconn);
            }
            
            echo mysql_error();
            $row=mysql_fetch_array($query); 
            $account_id=$row['id'];
            
            
            $tg=intval($_GET['tg']);
            $query=mysql_query("INSERT into `dbisoftik`.`z_invoice` SET `amount`='".$tg."',`description`='Предоплата за услуги автоматизации',`acid`='".($account_id)."' ",$db_sconn);
            $_GET['id']=mysql_insert_id();
            $_GET['do']='show_invoice';
            $_GET['zrow']='<tr>
                            <td><a href="#" onclick="show_invoice(\''.$_GET['id'].'\'); return false;"><b>P'.str_pad($_GET['id'], 10, '0', STR_PAD_LEFT).'</b></a></td>
                            <td>'.date('d.m.Y H:i:s').'</td> 
                            <td>'.number_format($tg,2,'.',' ').'</td> 
                            <td>Предоплата за услуги автоматизации</td> 
                            <td><i class="glyphicon glyphicon-remove-circle" style="color:red"></i></td> 
                        </tr>';
        case 'show_invoice':
        if (isset($_GET['id'])){
            
            $month=array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
            
            
            $id=intval($_GET['id']);
        
            include('mysql_connect_ajax.php');
            $query=mysql_query("SELECT * FROM `dbisoftik`.`z_invoice` WHERE id='".$id."' LIMIT 1",$db_sconn);
            $row=mysql_fetch_assoc($query);
            $invoice_status=$row['status'];
            $amountnf=$row['amount'];
            $amount=number_format($amountnf,2,'.',' ');
            $amount_text=num2str($amountnf);
            $invoice_num='P'.str_pad($row['id'], 10, '0', STR_PAD_LEFT);
            
            $m=$month[(date('n',strtotime($row['date']))-1)];
            $date=date('j '.$m.' Y',strtotime($row['date']));
            
            $message= <<<HTML
            <body class="thiswindow">
            <style>
            .thiswindow *{font:12px Arial;vertical-align:top;}
            .thiswindow b{font-weight:bold;}
            .coolblacktable{border-spacing:0;border-collapse:collapse;}
            .coolblacktable td{border:1px solid #000;}
            .coolestblacktr td{font-weight:bold; text-align:center;}
            .worldwidewhitepride td{border:none;font-weight:bold;}
            .coolblacktable :nth-child(3),.coolblacktable :nth-child(5),.coolblacktable :nth-child(6){text-align:right;}
            </style>
            <table width="900" style="font:12px Arial;">
                <tr>
                    <td style="text-align: center;padding: 7px 0 0 212px;font: 12px Arial;"> Внимание! Оплата данного счета означает согласие с условиями поставки товара. Уведомление об оплате<br />
     обязательно, в противном случае не гарантируется наличие товара на складе. Товар отпускается по факту<br />  прихода денег на р/с Поставщика, самовывозом, при наличии доверенности и документов удостоверяющих<br /> личность.</td>
                </tr>
                <tr>
                    <td style="padding:30px 0 0 0">
                        <span style="font:bold 15px Arial;">Образец платежного поручения</span>
                        <table style="width:100%; border:1px solid #000;border-collapse: collapse;border-spacing:0">
                            <tr>
                                <td style="border-right:1px solid #000;"><b>Бенефициар:</b></td>
                                <td style="border-right:1px solid #000; text-align:center;"><b>ИИК</b></td>
                                <td style="text-align:center;" ><b>Кбе</b></td>
                            </tr>
                            <tr>
                                <td style="border-right:1px solid #000;"><b>Товарищество с ограниченной ответственностью  "Paloma service"</b></td>
                                <td style="border-right:1px solid #000; text-align:center;vertical-align:middle;border-bottom:1px solid #000;" rowspan="2"><b>KZ598560000003951196</b></td>
                                <td style="text-align:center; vertical-align:middle;border-bottom:1px solid #000;" rowspan="2"><b>17</b></td>
                            </tr>
                            <tr>
                                <td style="border-right:1px solid #000;border-bottom:1px solid #000;">БИН: 100740000739</td>
                                
                            </tr>
                            <tr>
                                <td style="border-right:1px solid #000;">Банк бенефициара:</td>
                                <td style="border-right:1px solid #000; text-align:center;"><b>БИК</b></td>
                                <td style="text-align:center;"><b>Код назначения платежа</b></td>
                            </tr>
                            <tr>
                                <td style="border-right:1px solid #000;">АО "Банк ЦентрКредит"</td>
                                <td style="border-right:1px solid #000; text-align:center;"><b>KCJBKZKX</b></td>
                                <td> </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="border-bottom:1px solid #000; font:bold 18px Arial; padding:20px 0 10px 0 ">Счет на оплату № {$invoice_num} от {$date} г.</td>
                </tr>
                 <tr>
                    <td>
                        <table width="100%">
                            <tr>
                                <td>Поставщик:</td>
                                <td style="padding-bottom:10px; font-weight:bold;">БИН / ИИН 100740000739,Товарищество с ограниченной ответственностью  "Paloma

service",Республика Казахстан, Алматы, Чайковского, дом № 22, к.202, тел.: +7-702-111-9723, 327-

01-74</td>
                            </tr>
                            <tr>
                                <td>Покупатель:</td>
                                <td style="padding-bottom:10px; font-weight:bold;">Частное лицо</td>
                            </tr><tr>
                                <td>Договор:</td>
                                <td style="padding-bottom:10px; font-weight:bold;">Без договора</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="100%" class="coolblacktable">
                            <tr class="coolestblacktr">
                                <td>#</td>
                                <td>Наименование</td>
                                <td>Кол-во</td>
                                <td>Ед.</td>
                                <td>Цена</td>
                                <td>Сумма</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Предоплата за услуги автоматизации</td>
                                <td>1.000</td>
                                <td>шт</td>
                                <td>{$amount}</td>
                                <td>{$amount}</td>
                            </tr>
                            <tr class="worldwidewhitepride">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Итого:</td>
                                <td>{$amount}</td>
                            </tr> 
                            <tr class="worldwidewhitepride">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Без налога (НДС)</td>
                                <td>-</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>Всего наименований 1, на сумму {$amount} KZT</td>
                </tr>
                <tr>
                    <td style="font-weight:bold; border-bottom:1px solid #000;">Всего к оплате: {$amount_text} </td>
                </tr>
                <tr>
                    <td style="padding:17px 0 0 0">
                        <table width="100%">
                            <td style="font-weight:bold; width:100px">Исполнитель</td>
                            <td style="border-bottom:1px solid #000; width:300px" ></td>
                            <td>/Бухгалтер   /</td>
                        </table>
                    </td>
                </tr>
                
            </table>
            
            </body>
            
HTML;

    if (isset($_GET['type'])&&($_GET['type']=='print')){
        
    echo '<html>
<head>
    <meta charset="UTF-8">
    <title>Печать</title>
    <script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
<script>$(document).ready(function() {
window.print();
setTimeout(function() {

        window.close();                      
   
}, 200); 
    });</script>
</head>
<body>';
     echo $message;
     echo '</body></html>';
    }else{
   
        
        $query=mysql_query("SELECT email,phone FROM `dbisoftik`.`s_accounts` WHERE db='".$_SESSION['base']."' LIMIT 1",$db_sconn);
        $row=mysql_fetch_assoc($query);
        
        $mail=$row['email'];
        $phone=$row['phone'];
        $phone=str_replace('-','',$phone);
        $phone=str_replace('+','',$phone);
        $phone=str_replace(' ','',$phone);
        
        
        require_once("pay/paysys/kkb.utils.php");
        $self = $_SERVER['PHP_SELF'];
        $currency_id = "398";
        $path1 = 'pay/paysys/config.txt';
        $content = process_request($id,$currency_id,$amountnf,$path1); 
        
        $kkbpay='<form name="SendOrder" method="post" action="https://epay.kkb.kz/jsp/process/logon.jsp">
                <input type="hidden" name="Signed_Order_B64" value="'.$content.'">
                <input type="hidden" name="email" size=50 maxlength=50  value="'.$mail.'">
                <input type="hidden" name="Language" value="rus">
                <input type="hidden" name="BackLink" value="http://'.$_SERVER['SERVER_NAME'].'/company/?do=paysuccess&no='.$id.'">
                <input type="hidden" name="PostLink" value="http://'.$_SERVER['SERVER_NAME'].'/company/postlink.php">
                <input type="hidden" name="FailureBackLink" value="http://'.$_SERVER['SERVER_NAME'].'/company/?do=payfail&no='.$id.'">
                <div style="display:none"><input type="submit"  name="GotoPay" value="Да, перейти к оплате" class="payform"></div>
             </form>';
        
        
         $qiwipay='<form name="QiwiSendOrder" method="get" action="https://w.qiwi.com/order/external/create.action">
                <input type="hidden" name="from" value="253737">
                <input type="hidden" name="to"  value="'.$phone.'">
                <input type="hidden" name="summ" value="'.$amountnf.'">
                <input type="hidden" name="currency" value="KZT">
                <input type="hidden" name="comm" value="">
                <input type="hidden" name="txn_id" value="'.$id.'">
                <input type="hidden" name="successUrl" value="http://'.$_SERVER['SERVER_NAME'].'/company/?do=paysuccess&no='.$id.'">
                <input type="hidden" name="failUrl" value="http://'.$_SERVER['SERVER_NAME'].'/company/?do=payfail&no='.$id.'">
                 <div style="display:none"><input type="submit"  name="GotoPay" value="Да, перейти к оплате" class="qiwiform"></div>
             </form>';
        
        $res=array();
        $res['message']=$message.$kkbpay.$qiwipay;
        $res['status']=$invoice_status;
        $res['id']=$row['id'];
        if (isset($_GET['zrow']))
            $res['zrows']=$_GET['zrow'];
            
        echo json_encode($res);
        }
    }  
        break;
        case 'uploadcsvspr':
            if ($_FILES["csv_file"]["error"] >0 )
            {
                echo "Ошибка при загрузке!" ;
            }
            else
            {
                $file_array = explode("\n",file_get_contents($_FILES["csv_file"]["tmp_name"]));
                if (substr($file_array[0],0,3)==chr(239).chr(187).chr(191))
                $file_array[0]=substr($file_array[0],3);
                $i=0;
                $u=0;
                foreach ($file_array as $str) {
                    $sub_array=explode(";",$str);
                    if ($sub_array[4]==''){$sub_array[4]=0;}
                    $result = mysql_query("select id from s_items 
                    WHERE idlink = '".addslashes($sub_array[0])."' and idlink!='' ");

                    if(mysql_num_rows($result)){
                        
                        $name='';
                        if (isset($_POST['change_name'])){
                            $name="name='".addslashes($sub_array[3])."',";
                        }   
                        if (isset($_POST['i_useInMenu'])){
                            $name.="i_useInMenu='1',";
                        }   
                        
                        $result=mysql_query("UPDATE s_items SET                     
                    isgroup=".addslashes($sub_array[1]).",
                    parentid=ifnull((select * from 
                    (select t.id from s_items as t where t.idlink='".addslashes($sub_array[2])."' and t.idlink!='' limit 1) as t2),0),
                    ".$name."
                    price=".addslashes($sub_array[4])."
                    WHERE idlink = '".addslashes($sub_array[0])."' ");
                    
                    if(mysql_affected_rows())
                    {
                        $u=$u+mysql_affected_rows();
                        echo "Обновлена запись: Код=".$sub_array[0]." Наименование=".$sub_array[3]." Цена=".$sub_array[4]."\n";
                    }
                    }
                    else
                    {
                        $result=mysql_query("insert into s_items SET 
                    idout=ifnull((select max(cast(idout as SIGNED))+1 from s_items as t3 limit 1),1),    
                    idlink= '".addslashes($sub_array[0])."',
                    isgroup=".addslashes($sub_array[1]).",
                    parentid=ifnull((select * from 
                    (select t.id from s_items as t where t.idlink='".addslashes($sub_array[2])."' and t.idlink!='' limit 1) as t2),0),
                    name='".addslashes($sub_array[3])."',
                    price=".addslashes($sub_array[4]));                
                        if ($result){$i++;}                   
                    }
                    
                    
                }
                echo "Загрузка завершена!\nДобавлено: ".$i."товаров\nОбновлено: ".$u." товаров";
                unlink($_FILES["csv_file"]["tmp_name"]);
            }       
        break; 
        case 'getqueue':
            $res=array();
            for($i=1;$i<=500;$i++){
                $res[]=array('doctype'=>rand(100,999),'id'=>$i);
            }
            echo json_encode(array('m'=>$res,'count'=>count($res)));
        break;  
        case 'dowithqueue':
            echo 'id=>'.$_GET['id'].' doctype='.$_GET['doctype'].'<br />';
        break;  
        case 'magic':
            $qwery="SELECT

    SUM(r.quantity)AS magic0

FROM
    r_remainder AS r

WHERE
    (r.warehouseid = \"1\")
AND (r.dt <= \"2014-01-01 15:24:45\")
GROUP BY
    r.warehouseid,
    r.itemid,
    r.specificationid";
            $q=mysql_query($qwery);
            while($row=mysql_fetch_assoc($q)){
                print_r($row);
            }
            echo '<br /><br />'.$qwery;
        break;      
    }
             
    
?>