<?

$theRights=getRights();

function num2str($num) {
    $nul='ноль';
    $ten=array(
        array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
        array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
    );
    $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
    $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
    $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
    $unit=array( // Units
        array('тиын' ,'тиын' ,'тиын',     1),
        array('тенге'   ,'тенге'   ,'тенге'    ,0),
        array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
        array('миллион' ,'миллиона','миллионов' ,0),
        array('миллиард','милиарда','миллиардов',0),
        
        
    );
    //
    list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub)>0) {
        foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
            if (!intval($v)) continue;
            $uk = sizeof($unit)-$uk-1; // unit key
            $gender = $unit[$uk][3];
            list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
            else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
            // units without rub & kop
            if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
        } //foreach
    }
    else $out[] = $nul;
    $out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
    $out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
    return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}


function morph($n, $f1, $f2, $f5) {
    $n = abs(intval($n)) % 100;
    if ($n>10 && $n<20) return $f5;
    $n = $n % 10;
    if ($n>1 && $n<5) return $f2;
    if ($n==1) return $f1;
    return $f5;
}

function getNameById($table,$id){
   $query=mysql_query("SELECT `name` FROM `".$table."` WHERE id='".$id."'");  
   $row=mysql_fetch_array($query);
   return $row['name'];  
}

function showfield($table,$field,$data,$id,$isgroup=0){
    global $fields;
    $res=array();
    $type=$fields[$table][$field]['type'];
    if ($fields[$table][$field]['in_edit']==true){
    //запрещенные права для определенных юзеров
        if (check_users_available_fields($_SESSION['userid'],$table,$field)){
            if ($type=='db_grid')
        $res[]='<div class="form-group"><label class="col-lg-12 control-label" style="text-align:left;">'.(($isgroup==1)&&(!empty($fields[$table][$field]['group_title']))?$fields[$table][$field]['group_title']:$fields[$table][$field]['title']).(!empty($fields[$table][$field]['alt'])?' (<a href="#" data-toggle="tooltip" data-placement="right" data-animation="false" class="tytip" title="'.$fields[$table][$field]['alt'].'">i</a>)':'').( $fields[$table][$field]['required'] ? '<span style="color: #F00; font-size: 15px;">*</span>' : '' ).':</label>
            <div class="col-lg-12">';
            else
            $res[]='<div class="form-group"><label class="col-lg-4 control-label">'.(($isgroup==1)&&(!empty($fields[$table][$field]['group_title']))?$fields[$table][$field]['group_title']:$fields[$table][$field]['title']).( $fields[$table][$field]['required'] ? '<span style="color: #F00; font-size: 15px;">*</span>' : '' ).':'.(!empty($fields[$table][$field]['alt'])?'<br /><span class="zlabelspan">'.$fields[$table][$field]['alt'].'</span>':'').'</label>
            <div class="col-lg-8">';
       /*if(!empty($fields[$table][$field]['readonly'])){
           $res[]= '<p class="form-control-static" name="'.$field.'">'.$data[$field].'</p>';
       }else*/
       switch ($type){
            case 'input': 
                $numeric='';
                if (isset($fields[$table][$field]['numeric'])) $numeric='onkeypress="onlydigits(event)"';
                $res[]= '<input name="'.$field.'" '.(!empty($fields[$table][$field]['readonly'])?'readonly="readonly"':'').' '.$numeric.' class="form-control"  '.($fields[$table][$field]['required']?'required="true"':'').' value="'.(isset($data[$field])?htmlspecialchars($data[$field]):'').'"'.(!empty($fields[$table][$field]['field_width'])?' style="width:'.$fields[$table][$field]['field_width'].'px"':'').' '.(!empty($fields[$table][$field]['mask'])?'data-mask="'.$fields[$table][$field]['mask'].'"':'').' >';
                break;
            case 'textarea': 
                $res[]= '<textarea name="'.$field.'" '.(!empty($fields[$table][$field]['readonly'])?'readonly="readonly"':'').' class="form-control" '.($fields[$table][$field]['required']?'required="true"':'').' '.(!empty($fields[$table][$field]['field_width'])?' style="width:'.$fields[$table][$field]['field_width'].'px"':'').'>'.(isset($data[$field])?htmlspecialchars($data[$field]):'').'</textarea>';
                break;
            case 'itemmenu': 
                $res[]= '<p class="form-control-static">'.getNameById('s_items',$data['itemid']).'</p>';
                break;
                case 'date':
                //ART косячник
                
                $res[]='<div class="dpicker">
                        <div class="input-group date datep">
                            <input type="text" '.( $fields[$table][$field]['required'] ? 'required="true"' : '' )
    .' value="'.( isset( $data[$field] ) ? date( 'd.m.Y H:i:s', strtotime( $data[$field] ) ) : date( 'd.m.Y', time() ) ).'" class="form-control" data-format="dd.MM.yyyy" name="'.$field.'" />
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>';
                    
                $res[]='<script>$(document).ready(function() {
                            $(\'.datep\').datetimepicker({pickTime: false});
                        })</script>';
            break;
            case 'time':
                $res[]='<div class="dpicker">
                        <div class="input-group date datep">
                            <input type="text" '.( $fields[$table][$field]['required'] ? 'required="true"' : '' )
    .' value="'.( isset( $data[$field] ) ? date( 'H:i:s', strtotime( $data[$field] ) ) : date( 'H:i:s', time() ) ).'" class="form-control" data-format="hh.ii.ss" name="'.$field.'" />
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>';
                    
                $res[]='<script>$(document).ready(function() {
                            $(\'.datep\').datetimepicker({pickDate: false});
                        })</script>';
            break;
            case 'datetime':
                //ART косячник
                $res[]='<div class="dpicker">
                        <div class="input-group date datetimep">
                            <input type="text" '.( $fields[$table][$field]['required'] ? 'required="true"' : '' )
    .' value="'.( isset( $data[$field] ) ? date( 'd.m.Y H:i:s', strtotime( $data[$field] ) ) : date( 'd.m.Y H:i:s', time() ) ).'" class="form-control" data-format="dd.MM.yyyy hh:mm:ss" name="'.$field.'" />
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>';
                $res[]='<script>$(document).ready(function() {
                            $(\'.datetimep\').datetimepicker();
                        })</script>';
            break;
            case 'password': 
                $res[]= '<input name="'.$field.'" type="password" class="form-control" '.(!empty($data[$field])?'placeholder="******"':'').'  value="'.(!empty($data[$field])?'zottig':'').'"'.(!empty($fields[$table][$field]['field_width'])?' style="width:'.$fields[$table][$field]['field_width'].'px"':'').'>';
                break;
            case 'checkbox': 
                $res[]= '<div style="width:50px"><input name="'.$field.'" type="checkbox" class="zotcheckmf checkselect" '.($data[$field]==1?'checked="checked"':'').' value="1"></div>';
                break;
            case 'userrights': 
                    $arr=get_userrights_tablevalues($fields[$table][$field]['table'],$data['id']);
                    $res[]= get_userrightslist($fields[$table][$field]['table'],$field,$fields[$table][$field]['rightlist'],$arr);
                break;
            case 'db_select':
                $onchange='';
                if ($field=='itemid') $onchange='onchange="getMeasure( this ); getMultip( this );" ';
                if ($field=='measureid') $onchange='onchange="getMultip( this );" ';
                
                  $selectfield='name';
                  if (isset($fields[$table][$field]['selectfield'])) $selectfield=$fields[$table][$field]['selectfield'];
                
                $default_id='';
                $default_title='';
                if ($id==0 && $defaults=get_def_value_for_field($field)){
                   
                    $default_id=$defaults['id'];
                    $default_title=$defaults['title'];
                }else{
                    $default_title=get_db_select_value($fields[$table][$field]['db_select'],isset($data[$field])?$data[$field]:0,$selectfield);
                    $default_id=(isset($data[$field])?$data[$field]:'');
                }
                
                
                
                $res[]= '
                <div class="input-group">
                    <input id="'.$table.'_'.$field.'" class="form-control" onkeyup="oninputchange(event,this,\''.$fields[$table][$field]['db_select'].'\')" onblur="getlastvalue(this)" sval="'.$default_title.'" '.($fields[$table][$field]['required']?'required="true"':'').' value="'.$default_title.'"'.(!empty($fields[$table][$field]['field_width'])?'  style="width:'.$fields[$table][$field]['field_width'].'px"':'').' > <input name="'.$field.'" '.$onchange.' type="hidden" value="'.$default_id.'">
                    <div class="input-group-btn">
                      <button type="button" class="btn btn-default" tabindex="-1" onclick="show_dbselect_window(\''.$fields[$table][$field]['db_select'].'\',\''.$table.'_'.$field.'\',\''.$selectfield.'\')">...</button>
                      <button type="button" class="btn btn-default" tabindex="-1" onclick="clear_db_select(this);return false"> X </button>
                    </div>
                </div>';
                break;
            case 'defaults':
            $value=get_default_value($field,$id);
            $value_id=get_default_value($field,$id,1);
                $res[]= '
                <div class="input-group">
                    <input id="'.$table.'_'.$field.'" class="form-control" onkeyup="oninputchange(event,this,\''.$fields[$table][$field]['src_table'].'\')" onblur="getlastvalue(this)" sval="'.$value.'" '.($fields[$table][$field]['required']?'required="true"':'').' value="'.$value.'"> <input name="defaults['.$field.']" type="hidden" value="'.$value_id.'">
                    <div class="input-group-btn">
                      <button type="button" class="btn btn-default" tabindex="-1" onclick="show_dbselect_window(\''.$fields[$table][$field]['src_table'].'\',\''.$table.'_'.$field.'\',\''.$fields[$table][$field]['src_title'].'\')">...</button>
                      <button type="button" class="btn btn-default" tabindex="-1" onclick="clear_db_select(this);return false"> X </button>
                    </div>
                </div>';
                break;
            case 'barcode':
                $res[]= '<input name="'.$field.'" type="input" class="form-control" style="width:189px;float:left;" value="'.(isset($data[$field])?$data[$field]:'').'"> <button type="button" class="btn btn-default" onclick="getBarcode(this); return false;"><i class="glyphicon glyphicon-barcode"></i></button>  <button type="button" class="btn btn-default" onclick="printBarcode(this); return false;"><i class="glyphicon glyphicon-print"></i></button>';
                //<input type="button" value="..." style="width:36px;float:left;height: 34px;" onclick="show_dbselect_window(\''.$fields[$table][$field]['db_select'].'\',\''.$table.'_'.$field.'\')">
                break;
             case 'db_groupselect':
                if(isset($_GET['parentid'])){ if(empty($data)) $data=array(); $data[$field]=$_GET['parentid'];}
                $res[]= '<select class="form-control" name="'.$field.'"'.(!empty($fields[$table][$field]['field_width'])?' style="width:'.$fields[$table][$field]['field_width'].'px"':'').'>'.db_groupselect_options($fields[$table][$field]['db_select'],$data[$field]).'</select>';
                break;
             case 'timezone':
                $res[]= '<select class="form-control" name="'.$field.'"'.(!empty($fields[$table][$field]['field_width'])?' style="width:'.$fields[$table][$field]['field_width'].'px"':'').'>'.getTimeZones($data[$field]).'</select>';
                break;
            case 'db_multiselect':
                
                $arr=get_multiselect_tablevalues($fields[$table][$field]['db_selectto'],$fields[$table][$field]['to_field'],$fields[$table][$field]['select_field'],$data['id']);
                $res[]= '<select multiple class="form-control" name="'.$field.'[]"'.(!empty($fields[$table][$field]['field_width'])?' style="width:'.$fields[$table][$field]['field_width'].'px;height:'.$fields[$table][$field]['field_height'].'px"':'').'>'.db_multiselect_options($fields[$table][$field]['db_select'],$fields[$table][$field]['selectto_field'],$arr).'</select>';
                break;
            case 'db_multicheckbox':
                  if ((!isadmin($_SESSION['userid']) && ($v['db_select']=='s_role'))){
                       
                  }else{
                      $arr=array();
                  if ($data!=0)
                 $arr=get_multiselect_tablevalues($fields[$table][$field]['db_selectto'],$fields[$table][$field]['to_field'],$fields[$table][$field]['select_field'],$data['id']);
                 
                $res[]= '<fieldset class="highlight">
                    '.db_multichechbox_options($fields[$table][$field]['db_select'],$field,$arr).'
                      </fieldset>';
                    }
                break;
            case 'db_grid':
            if ($id!=0){
           // $res[]="ajax.php?do=get&table=".$fields[$table][$field]['db_grid']."&".$fields[$table][$field]['idfield']."=".$id."&nolimit=topgear";
            $res[]= '<div id="toolbar-'.$fields[$table][$field]['db_grid'].'1">
            <a href="javascript:void(0)" class="btn btn-default" iconCls="icon-add" plain="true" onclick="datagrid_add(\''.$fields[$table][$field]['db_grid'].'\',\''.$fields[$table][$field]['idfield'].'\',\''.$id.'\')">Добавить элемент</a><a href"javascript:void(0)" class="btn btn-default" iconCls="icon-edit" plain="true" onclick="datagrid_edit(\''.$fields[$table][$field]['db_grid'].'\')">Изменить</a><a href="javascript:void(0)" class="btn btn-default" iconCls="icon-remove" plain="true" onclick="datagrid_delete(\''.$fields[$table][$field]['db_grid'].'\')">Удалить</a></div>
            <div class="highlight">';
            /* */
            //$res[]="url:'ajax.php?do=newfuckingget&idfield=".$fields[$table][$field]['idfield']."&table=".$fields[$table][$field]['db_grid']."&".$fields[$table][$field]['idfield']."=".$id."&nolimit=topgear',";
                $res[]= " <table id='table-".$fields[$table][$field]['db_grid']."'></table>
                    <script>                                   
                        \$('#table-".$fields[$table][$field]['db_grid']."').myTreeView({  
                            url:'ajax.php?do=newfuckingget&idfield=".$fields[$table][$field]['idfield']."&table=".$fields[$table][$field]['db_grid']."&".$fields[$table][$field]['idfield']."=".$id."&nolimit=topgear', 
                            headers: [";
                            foreach($fields[$fields[$table][$field]['db_grid']] as $field1=>$v1){
                                if ($v1['in_grid']){
                                    $res[]= "{name:'".$field1."',title:'".$v1['title']."'".(!empty($v1['width'])?",width:'".$v1['width']."'":'')."},";
                                } 
                            }
                            $res[]="],
                            tree: false,
                            numeration:true
                            
                        });  
                        console.log('ajax.php?do=newfuckingget&table=".$fields[$table][$field]['db_grid']."&".$fields[$table][$field]['idfield']."=".$id."&nolimit=topgear');
                    </script></div>";
            }
                break;
        }
        $res[]='</div>';
        
        


       $res[]='</div>';
       }
    }
   return join("\n",$res);
}


function get_userrightslist($table,$field,$rightlist,$val){
//get_userrightslist
$res=array();

 $q=mysql_query("SELECT id,name FROM `".addslashes($table)."`");
 
 
 
 $res[]='<fieldset class="highlight"><table><tr class="nzrightsheader"><td class="zrgtp2">Наименование</td>';
 foreach($rightlist as $k=>$v){
     $res[]='<td>'.$v.'</td>';
 }
 $res[]='</tr>';
 while ($row=mysql_fetch_assoc($q)){
     $res[]='<tr><td class="zrgtp">'.$row['name'].'</td>';
     foreach($rightlist as $k=>$v){
        $res[]='<td><input name="'.$field.'['.$row['id'].']['.$k.']" type="checkbox" class="zotcheckmf"  '.($val[$row['id']][$k]==1?'checked="checked"':'').' ></td>';
     }

$res[]='</tr>';
 }


$res[]='</table></fieldset>';
return join('',$res);
}


  function myroll($id){

      if (isset($_SESSION['admin'])) return 0;
      
      $query=mysql_query("SELECT MIN(rollid) AS roll FROM `t_employee_role` WHERE employeeid='".$id."'"); 
      if (mysql_numrows($query)){
        $row=mysql_fetch_array($query);
        return $row['roll'];
      }else{
          return 999;
      }
      
       
  }
  
  function myinterface($id){
      
      if (isset($_SESSION['admin'])) return 0;

      $query=mysql_query("SELECT MIN(rollid) AS roll FROM `t_employee_interface` WHERE employeeid='".$id."'"); 
      if (mysql_numrows($query)){
        $row=mysql_fetch_array($query);
        return $row['roll'];
      }else{
          return 999;
      }
      
       
  }
  
    function isadmin($userid){
      if (isset($_SESSION['admin'])) return 1;
      $query=mysql_query("SELECT rollid FROM `t_employee_interface` WHERE employeeid='".addslashes($userid)."' AND rollid=1");
      return mysql_numrows($query);
  }
  

  function getMyInterface($userid){
      if (isset($_SESSION['admin'])) return 0;
      $query=mysql_query("SELECT MIN(rollid) AS rollid FROM `t_employee_interface` WHERE employeeid='".addslashes($userid)."'");
      if (mysql_numrows($query)){
          $row=mysql_fetch_array($query);
          return $row['rollid'];
      }else{
          return 9999;
      }
      
  }  
  
  function getMyWP($userid,$wpid){
      $query=mysql_query("SELECT id FROM `t_employee_workplace` WHERE employeeid='".addslashes($userid)."' AND wpid='".addslashes($apid)."'");
     return mysql_numrows($query);
  }
  
  function getgroup($userid){
      $res=array();
      $query=mysql_query("SELECT rollid FROM `t_employee_role` WHERE employeeid='".addslashes($userid)."'");
      if (mysql_numrows($query)){
          while($row=mysql_fetch_assoc($query)){
            $res[]=$row['rollid'];     
          }   
      return $res;
      }else{
          return array(0);
      }
      
  }
  
  
  function getRights(){
      $res=array();

      if (!isset($_SESSION['userid'])) return false;
      $group=getgroup($_SESSION['userid']);
      
      $query=mysql_query("SELECT rightname,
                                 MAX(`view`) as `view`, 
                                 MAX(`add`) as `add`,
                                 MAX(`edit`) as `edit`,
                                 MAX(`delete`) as `delete`,
                                 MAX(`print`) as `print`  FROM `z_rights` 
            LEFT JOIN `z_rights_category` ON z_rights.rightid=z_rights_category.id 
            WHERE groupid IN(".join(',',$group).") GROUP by rightid");
      while($row=mysql_fetch_assoc($query)){
          $res[$row['rightname']]['view']=$row['view'];
          $res[$row['rightname']]['add']=$row['add'];
          $res[$row['rightname']]['edit']=$row['edit'];
          $res[$row['rightname']]['delete']=$row['delete'];
          $res[$row['rightname']]['print']=$row['print'];
      }
     return $res;                       

  }
  
    function checkrights($rightname,$type){
          global $theRights;
          if ($rightname=='t_regrading'){$rightname='d_regrading';}
          if ($rightname=='t_order'){$rightname='d_order';}
          if ($rightname=='t_discount_clients'){$rightname='s_discount';}
          if ($rightname=='s_combo_items'){$rightname='get_window_combo';}
          if ($rightname=='s_combo_groups'){$rightname='get_window_combo';}
          if ($rightname=='t_discount_ap'){$rightname='s_discount';}
          if ($rightname=='t_object_tarif'){$rightname='s_tarifs';}
          if ($rightname=='t_tarifs'){$rightname='s_tarifs';}
          if ($rightname=='t_calculations'){$rightname='s_calculations';}
          if ($rightname=='d_changes'){$rightname='d_order';}
          /*if (($rightname=='s_employee')&&(isset($_SESSION['admin']))){return 1;}
          if (($rightname=='s_role')&&(isset($_SESSION['admin']))){return 1;}
          if (($rightname=='zrights')&&(isset($_SESSION['admin']))){return 1;}
          */
          
          
          if (isset($_SESSION['admin'])){return 1;}
          $pipiska=getMyInterface($_SESSION['userid']);

          if ($rightname=='s_role'){
               return ($pipiska==1);
          }
          if ($rightname=='zrights'){
               return ($pipiska==1);
          }
          
          if(!isset($_SESSION['fromfront'])){
              if (($rightname=='gethtml_itogovy')&&(isset($_SESSION['point'])&&(!isset($_SESSION['main'])))){return 1;}
              if (($rightname=='gethtml_akt_real')&&(isset($_SESSION['point'])&&(!isset($_SESSION['main'])))){return 1;}
              if (($rightname=='gethtml_poschetam')&&(isset($_SESSION['point'])&&(!isset($_SESSION['main'])))){return 1;}
              if (($rightname=='gethtml_refuse')&&(isset($_SESSION['point'])&&(!isset($_SESSION['main'])))){return 1;}
              if (($rightname=='gethtml_refuse_and_orders')&&(isset($_SESSION['point'])&&(!isset($_SESSION['main'])))){return 1;}
          }
          //Только редактирование конфига
          if (($rightname=='s_config')&&($type>2)){return 0;}
          switch ($type){
              case 1: $field='view'; break;
              case 2: $field='edit'; break;
              case 3: $field='add'; break;
              case 4: $field='delete'; break;
              case 5: $field='print'; break;
              default: return false;
          }
          
          if (isset($theRights[$rightname][$field])){
              return $theRights[$rightname][$field]; 
          }else{
              return false;
          }
          
    }
  
/*    function OLDcheckrights($rightname,$type){
      if ($rightname=='t_regrading'){$rightname='d_regrading';}
      if ($rightname=='t_order'){$rightname='d_order';}
      if ($rightname=='t_discount_clients'){$rightname='s_discount';}
      if ($rightname=='s_combo_items'){$rightname='get_window_combo';}
      if ($rightname=='s_combo_groups'){$rightname='get_window_combo';}
      if ($rightname=='t_discount_ap'){$rightname='s_discount';}
      if ($rightname=='t_object_tarif'){$rightname='s_tarifs';}
      if ($rightname=='t_tarifs'){$rightname='s_tarifs';}
      if ($rightname=='t_calculations'){$rightname='s_calculations';}
      if ($rightname=='d_changes'){$rightname='d_order';}
      /*if (($rightname=='s_employee')&&(isset($_SESSION['admin']))){return 1;}
      if (($rightname=='s_role')&&(isset($_SESSION['admin']))){return 1;}
      if (($rightname=='zrights')&&(isset($_SESSION['admin']))){return 1;}
      */
     /* 
      
      if (isset($_SESSION['admin'])){return 1;}
      $pipiska=getMyInterface($_SESSION['userid']);

      if ($rightname=='s_role'){
           return ($pipiska==1);
      }
      if ($rightname=='zrights'){
           return ($pipiska==1);
      }
      
      if(!isset($_SESSION['fromfront'])){
          if (($rightname=='gethtml_itogovy')&&(isset($_SESSION['point'])&&(!isset($_SESSION['main'])))){return 1;}
          if (($rightname=='gethtml_akt_real')&&(isset($_SESSION['point'])&&(!isset($_SESSION['main'])))){return 1;}
          if (($rightname=='gethtml_poschetam')&&(isset($_SESSION['point'])&&(!isset($_SESSION['main'])))){return 1;}
          if (($rightname=='gethtml_refuse')&&(isset($_SESSION['point'])&&(!isset($_SESSION['main'])))){return 1;}
          if (($rightname=='gethtml_refuse_and_orders')&&(isset($_SESSION['point'])&&(!isset($_SESSION['main'])))){return 1;}
      }
      //Только редактирование конфига
      if (($rightname=='s_config')&&($type>2)){return 0;}
      switch ($type){
          case 1: $field='view'; break;
          case 2: $field='edit'; break;
          case 3: $field='add'; break;
          case 4: $field='delete'; break;
          case 5: $field='print'; break;
          default: return false;
      }
      $group=getgroup($_SESSION['userid']);
      $query=mysql_query("SELECT MAX(`".$field."`) as `".$field."`  FROM `z_rights` LEFT JOIN `z_rights_category` ON z_rights.rightid=z_rights_category.id WHERE rightname='".addslashes($rightname)."' AND groupid IN(".join(',',$group).") GROUP by rightid");
      
      echo mysql_error();
      if (mysql_numrows($query)){
          $row=mysql_fetch_array($query);
          return $row[$field];
      }else{
          return false;
      }
  }*/
  
  
function normJsonStr($json_str) { 
$cyr_chars = array ( 
'u0430' => 'а', 'u0410' => 'А', 
'u0431' => 'б', 'u0411' => 'Б', 
'u0432' => 'в', 'u0412' => 'В', 
'u0433' => 'г', 'u0413' => 'Г', 
'u0434' => 'д', 'u0414' => 'Д', 
'u0435' => 'е', 'u0415' => 'Е', 
'u0451' => 'ё', 'u0401' => 'Ё', 
'u0436' => 'ж', 'u0416' => 'Ж', 
'u0437' => 'з', 'u0417' => 'З', 
'u0438' => 'и', 'u0418' => 'И', 
'u0439' => 'й', 'u0419' => 'Й', 
'u043a' => 'к', 'u041a' => 'К', 
'u043b' => 'л', 'u041b' => 'Л', 
'u043c' => 'м', 'u041c' => 'М', 
'u043d' => 'н', 'u041d' => 'Н', 
'u043e' => 'о', 'u041e' => 'О', 
'u043f' => 'п', 'u041f' => 'П', 
'u0440' => 'р', 'u0420' => 'Р', 
'u0441' => 'с', 'u0421' => 'С', 
'u0442' => 'т', 'u0422' => 'Т', 
'u0443' => 'у', 'u0423' => 'У', 
'u0444' => 'ф', 'u0424' => 'Ф', 
'u0445' => 'х', 'u0425' => 'Х', 
'u0446' => 'ц', 'u0426' => 'Ц', 
'u0447' => 'ч', 'u0427' => 'Ч', 
'u0448' => 'ш', 'u0428' => 'Ш', 
'u0449' => 'щ', 'u0429' => 'Щ', 
'u044a' => 'ъ', 'u042a' => 'Ъ', 
'u044b' => 'ы', 'u042b' => 'Ы', 
'u044c' => 'ь', 'u042c' => 'Ь', 
'u044d' => 'э', 'u042d' => 'Э', 
'u044e' => 'ю', 'u042e' => 'Ю', 
'u044f' => 'я', 'u042f' => 'Я', 

'\r' => '', 
'\n' => '<br />', 
'\t' => '' 
); 

foreach ($cyr_chars as $cyr_char_key => $cyr_char) { 
$json_str = str_replace($cyr_char_key, $cyr_char, $json_str); 
} 
return $json_str; 
} 

  
  function getLogDesc($v,$log,$userid){
      global $fields;
      global $tables;
      $res=array();

      switch($log){
          case 1:
          case 2:
          case 3:
          case 4:
          case 5:
          case 6:
          case 7:
          case 8:
          case 10:
          case 11:
          case 12:
          case 13:
          case 14:
          case 15:
          case 16:
          case 17:
          case 18:
          case 19:
          case 20:
          case 21:
            $res[]=$v;
          break;
          case 1001:
          case 1002:
          case 1003:
            $array=json_decode($v);
            
            
            


            $a=array();
            if (isset($fields[$array->table])){
            foreach($fields[$array->table] as $k=>$v){
                if ($v['in_grid']){
                    $a[]=$v['title'].'='.normJsonStr($array->row->$k);
                }
                
            }
            $res[]='Таблица <b>'.$tables[$array->table]['name'].'</b>';
            $res[]='Данные: '.join('; ',$a);
            }else{
            $res[]='Таблица ---';   
            }
          break;;
          case 1006:
            $res[]=$v;
          break;
          case 1008:
            $res[]='';
          break;
          case 1009:
            $res[]='';
          break;
          case 1100:
          case 1101:
            $res[]=$v;
          break;
          case 1200:
            $res[]='';
          break;
          case 1201:
            $res[]='';
          break;
          case 1202:
            $res[]='';
          break;
      }
      return join('<br />',$res);
  }
  
  
  function get_account_by_username($db){
    $query=mysql_query("SELECT `id`,`db`,`db_password`,`db_user`,`timezone` FROM `dbisoftik`.`s_accounts` WHERE UPPER(`username`)=UPPER('".$db."')");   
    if (mysql_numrows($query)){
        $row=mysql_fetch_array($query);
        return $row;
    }else{
        return false;
    }
  }
  
  function get_account_by_id($id){
    $query=mysql_query("SELECT `id`,`db`,`db_password`,`db_user`,`timezone` FROM `dbisoftik`.`s_accounts` WHERE id='".$id."'");   
    if (mysql_numrows($query)){
        $row=mysql_fetch_array($query);
        return $row;
    }else{
        return false;
    }
  }
  
    function get_employee_by_pass($base,$login,$pass){
      $query=mysql_query("SELECT `id`,`name`,`last_action`,`password`,`fio` FROM `".$base."`.`s_employee` WHERE UPPER(`name`)=UPPER('".$login."') AND `name`!='' AND password='".$pass."' AND isuser=1"); 
      if (mysql_numrows($query)){
          return mysql_fetch_array($query);
      }else{
          return false;
      }
                      
  }
  
  function get_employee_by_cookie($base,$cookie){
      $query=mysql_query("SELECT `id`,`last_action`,`name`,`password`,`fio` FROM `".$base."`.`s_employee` WHERE cookie_key='".addslashes($cookie)."' AND isuser=1");
      if (mysql_numrows($query)){
          return mysql_fetch_array($query);
      }else{
          return false;
      }
                      
  }
  
  function set_employee_cookie($employeeid,$base,$base_id,$set){
    if (isset($_COOKIE['key'])&&($_COOKIE['key']!='')&&($_COOKIE['key']!=0)){
        $cookie_key=$_COOKIE['key'];
    }else{
      $cookie_key=md5('BILL GATES WAS HERE'.time());
    }
    mysql_query("UPDATE `".$base."`.`s_employee` SET last_action=UNIX_TIMESTAMP(), `cookie_key`='".$cookie_key."' WHERE id=".$employeeid);
    zlog($cookie_key,1300);
    if ($set){
        setcookie('key',$cookie_key,time()+3600*48); //2 суток
        setcookie('zid',$base_id,time()+3600*48); //2 суток
        zlog($cookie_key,1301);
    }
  }
  
  function get_automated_point_by_pass($base,$login,$pass){
    $query=mysql_query("SELECT `id`,`name`,`last_action`,`password`,`timezone` FROM `".$base."`.`s_automated_point` WHERE UPPER(`login`)=UPPER('".$login."') AND password='".$pass."'");
    if (mysql_numrows($query)){
        return mysql_fetch_array($query);
    }
    else{
        return false;
    }
  }
  
  function get_urv_by_pass($base,$login,$pass){
    $query=mysql_query("SELECT `id`,`name`,`last_action`,`password` FROM `".$base."`.`s_pointurv` WHERE UPPER(`login`)=UPPER('".$login."') AND password='".$pass."'");
    if (mysql_numrows($query)){
        return mysql_fetch_array($query);
    }
    else{
        return false;
    }
  }
  
  function get_workplace_by_pass($base,$login,$pass){
    $query=mysql_query("SELECT `s_automated_point`.`id`,`t_workplace`.`id` as `wid`,`s_automated_point`.`name`,`t_workplace`.`last_action`,`t_workplace`.`password`, `s_automated_point`.`timezone` FROM `".$base."`.`s_automated_point` INNER JOIN `".$base."`.`t_workplace` ON `t_workplace`.`apid`=`s_automated_point`.`id` WHERE UPPER(`t_workplace`.`login`)=UPPER('".$login."') AND `t_workplace`.password='".$pass."'");
    if (mysql_numrows($query)){
        return mysql_fetch_array($query);
    }
    else{
        return false;
    }
  }
  
  function get_automated_point_by_cookie($base,$cookie){
    $query=mysql_query("SELECT `id`,`name`,`last_action`,`password`,`login`,`timezone` FROM `".$base."`.`s_automated_point` WHERE cookie_key='".addslashes($cookie)."'"); 
    if (mysql_numrows($query)){
        return mysql_fetch_array($query);
    }
    else{
        return false;
    }
  }
  
  function get_workplace_by_cookie($base,$cookie){
    $query=mysql_query("SELECT `s_automated_point`.`id`,`t_workplace`.`id` as `wid`,`s_automated_point`.`name`,`t_workplace`.`password`,`t_workplace`.`last_action`,`s_automated_point`.`timezone` FROM `".$base."`.`s_automated_point` INNER JOIN `".$base."`.`t_workplace` ON `t_workplace`.`apid`=`s_automated_point`.`id` WHERE `t_workplace`.cookie_key='".addslashes($cookie)."'"); 
    if (mysql_numrows($query)){
        return mysql_fetch_array($query);
    }
    else{
        return false;
    }
  }
  
  function get_urv_by_cookie($base,$cookie){
   $query=mysql_query("SELECT `id`,`name`,`last_action`,`password`,`login` FROM `".$base."`.`s_pointurv` WHERE cookie_key='".addslashes($cookie)."'"); 
    if (mysql_numrows($query)){
        return mysql_fetch_array($query);
    }
    else{
        return false;
    }
  }
  
  function set_urv_cookie($apid,$base,$base_id,$set){
    if (isset($_COOKIE['key'])&&($_COOKIE['key']!='')&&($_COOKIE['key']!=0)){
        $cookie_key=$_COOKIE['key'];
    }else{
      $cookie_key=md5('BILL GATES WAS HERE'.time());
    }
    mysql_query("UPDATE `".$base."`.`s_pointurv` SET last_action=UNIX_TIMESTAMP(), `cookie_key`='".$cookie_key."' WHERE id=".$apid);
    zlog($cookie_key,1300);
    if ($set){
        setcookie('key',$cookie_key,time()+3600*48); //2 суток
        setcookie('zid',$base_id,time()+3600*48); //2 суток
        zlog($cookie_key,1301);
    }
  }
  
  
  function set_automated_point_cookie($apid,$base,$base_id,$set){
    if (isset($_COOKIE['key'])&&($_COOKIE['key']!='')&&($_COOKIE['key']!=0)){
        $cookie_key=$_COOKIE['key'];
    }else{
      $cookie_key=md5('BILL GATES WAS HERE'.time());
    }
    mysql_query("UPDATE `".$base."`.`s_automated_point` SET last_action=UNIX_TIMESTAMP(), `cookie_key`='".$cookie_key."' WHERE id=".$apid);
    zlog($cookie_key,1300);
    if ($set){
        setcookie('key',$cookie_key,time()+3600*48); //2 суток
        setcookie('zid',$base_id,time()+3600*48); //2 суток
        zlog($cookie_key,1301);
    }
  }
  
  function set_workplace_cookie($apid,$base,$base_id,$set){
    if (isset($_COOKIE['key'])&&($_COOKIE['key']!='')&&($_COOKIE['key']!=0)){
        $cookie_key=$_COOKIE['key'];
    }else{
      $cookie_key=md5('BILL GATES WAS HERE'.time());
    }
    mysql_query("UPDATE `".$base."`.`t_workplace` SET last_action=UNIX_TIMESTAMP(), `cookie_key`='".$cookie_key."' WHERE id=".$apid);
    zlog($cookie_key,1300);
    if ($set){
        setcookie('key',$cookie_key,time()+3600*48); //2 суток
        setcookie('zid',$base_id,time()+3600*48); //2 суток
        zlog($cookie_key,1301);
    }
  }
  
  function get_account_user_by_pass($login,$pass){
      $query=mysql_query("SELECT * FROM `dbisoftik`.`s_accounts` WHERE UPPER(`username`)=UPPER('".addslashes($login)."') AND password='".addslashes($pass)."'");   
      if (mysql_numrows($query)){
          return mysql_fetch_array($query);
      }else{
          return false;
      }
  }
  
  function getTemplate($template){
      
  }
  


  
  function get_account_user_by_cookie($cookie){
      $query=mysql_query("SELECT * FROM `dbisoftik`.`s_accounts` WHERE cookie_key='".addslashes($cookie)."'");  
      if (mysql_numrows($query)){
          return mysql_fetch_array($query);
      }else{
          return false;
      }
  }
  function set_account_user_cookie($userid,$set){
        $cookie_key=md5('BILL GATES WAS HERE'.time());
        mysql_query("UPDATE `dbisoftik`.`s_accounts` SET last_action=UNIX_TIMESTAMP(), `cookie_key`='".$cookie_key."' WHERE id=".$userid);
        zlog($cookie_key,1300);
        if ($set){
            
            setcookie('key',$cookie_key,time()+3600*48); //2 суток
            setcookie('zid',$userid,time()+3600*48); //2 суток
            zlog($cookie_key,1301);
        }
   }
   function seek_and_destroy(){
        if (isset($_SESSION['main'])){
            mysql_query("UPDATE `".$_SESSION['base']."`.`s_employee` SET last_action=0,cookie_key='' WHERE id=".$_SESSION['userid']);
            //echo "UPDATE `".$_SESSION['base']."`.`s_employee` SET isonline=0,cookie_key='' WHERE id=".$_SESSION['userid'];
        }
        if (isset($_SESSION['admin'])){
            mysql_query("UPDATE `dbisoftik`.`s_accounts` SET last_action=0,cookie_key='' WHERE id=".$_SESSION['userid']);
        }
        if (isset($_SESSION['wplace'])){
            mysql_query("UPDATE `".$_SESSION['base']."`.`t_workplace` SET last_action=0,cookie_key='' WHERE id=".$_SESSION['userid']);
        }else
        if (isset($_SESSION['urv'])){
            mysql_query("UPDATE `".$_SESSION['base']."`.`s_pointurv` SET last_action=0,cookie_key='' WHERE id=".$_SESSION['userid']);
        }else
        if (isset($_SESSION['point'])){
            mysql_query("UPDATE `".$_SESSION['base']."`.`s_automated_point` SET last_action=0,cookie_key='' WHERE id=".$_SESSION['userid']);
        }


        unset($_SESSION['main']);
        unset($_SESSION['wid']);
        unset($_SESSION['urv']);
        unset($_SESSION['wplace']);
        unset($_SESSION['admin']);
        unset($_SESSION['userid']);
        unset($_SESSION['base']);
        unset($_SESSION['point']);
        unset($_SESSION['user']);
        unset($_SESSION['password']);
        unset($_SESSION['timezone']);

        session_destroy();
        setcookie('key','',time()-3600*48); //2 суток
        setcookie('zid','',time()-3600*48); //2 суток
        unset($_COOKIE['key']);
        unset($_COOKIE['account']);
   }
   
   function chkey($account_id,$balance){
        return md5($account_id.md5($balance));
   }
   
   
   function set_balance($account_id,$amount,$desc){
       include('mysql_connect_ajax.php');
       $q=mysql_query("SELECT `balance` FROM `dbisoftik`.`s_accounts` WHERE id='".intval($account_id)."' LIMIT 1",$db_sconn);
       if ($row=mysql_fetch_assoc($q)){
            $newbalance=$row['balance']+$amount;
            mysql_query("UPDATE `dbisoftik`.`s_accounts` SET `balance`='".intval($newbalance)."',chkey='".chkey($account_id,$newbalance)."' WHERE id='".intval($account_id)."'",$db_sconn);
            mysql_query("INSERT into `".$_SESSION['base']."`.`s_transaction` SET `name`='".addslashes($desc)."',`amount`='".intval($amount)."'");
       }
       
       
   }
   
   
?>
