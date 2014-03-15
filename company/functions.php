<?php
  define('VERSION','10027');
  @define('FISH','13f31e2bc4948db0'); 

  include($_SERVER['DOCUMENT_ROOT'].'/partner/functions.php');
  include($_SERVER['DOCUMENT_ROOT'].'/company/warehouse/functions.php');
  
  
  function db_select_options($table,$selected_id=0){
      $result=array();
      
      $query=mysql_query("SELECT id,name FROM `".addslashes($table)."` ORDER by isgroup DESC,id");  
      $selected_one=false;
      if (mysql_numrows($query)==1){
          $selected_one=true;
      }else{
          if (checkNFrights($table,$row['id'],'view')) 
          $result[]='<option value="0" '.($selected_id==0?'selected="selected"':'').'>Корень</option>';
      }
      while($row=mysql_fetch_array($query)){
          $selected=($selected_id==$row['id']?'selected="selected"':'');
          if ($selected_one) $selected='selected="selected"';
        if (checkNFrights($table,$row['id'],'view'))  
        $result[]='<option value="'.$row['id'].'" '.$selected.'>'.$row['name'].'</option>';
      }
      return join('',$result);
  } 
  
  
  function generate_ean13( $digits ){
       $digits = ( string ) $digits;
    // 1. Add the values of the digits in the even-numbered positions: 2, 4, 6, etc.
    $even_sum = $digits{1} + $digits{3} + $digits{5} + $digits{7} + $digits{9} + $digits{11};
    // 2. Multiply this result by 3.
    $even_sum_three = $even_sum * 3;
    // 3. Add the values of the digits in the odd-numbered positions: 1, 3, 5, etc.
    $odd_sum = $digits{0} + $digits{2} + $digits{4} + $digits{6} + $digits{8} + $digits{10};
    // 4. Sum the results of steps s2 and 3.
    $total_sum = $even_sum_three + $odd_sum;
    // 5. The check character is the smallest number which, when added to the result in step 4,  produces a multiple of 10.
    $next_ten = ( ceil( $total_sum / 10 ) ) * 10;
    $check_digit = $next_ten - $total_sum;
    return $digits.$check_digit;
}

  
  function db_groupselect_options($table,$selected_id=0){
      $result=array();
      $result[]='<option value="0" '.($selected_id==0?'selected="selected"':'').'>Корень</option>';
      $query=mysql_query("SELECT id,name FROM `".addslashes($table)."` WHERE isgroup=1 ORDER by isgroup DESC,id");  
      while($row=mysql_fetch_array($query)){
        $result[]='<option value="'.$row['id'].'" '.($selected_id==$row['id']?'selected="selected"':'').'>'.$row['name'].'</option>';
      }
      return join('',$result);
  } 
  
  
  
  function get_db_select_value($table,$selected_id=0,$selectfield='name'){
      
      
      if ($selected_id>0){
          if ($table=='s_combo_items'){
                $query=mysql_query("SELECT s_combo_items.id,`s_items`.name FROM `s_combo_items` LEFT JOIN s_items ON s_items.id=s_combo_items.itemid  WHERE s_combo_items.id='".$selected_id."' LIMIT 1");    
          }else
            $query=mysql_query("SELECT id,`".$selectfield."` FROM `".addslashes($table)."` WHERE id='".$selected_id."' LIMIT 1");  
          $row=mysql_fetch_array($query);
          return $row['name'];
      }else{
          return '';
      }
  }   
  
  
  function get_defaults_field_list(){
      global $fields;
      $list=array();
      foreach($fields['z_default_values'] as $k=>$field){
          if($field['type']=='defaults'){
              $list[$field['fieldname']]=array(
                                        'src_title'=>$field['src_title'],
                                        'src_table'=>$field['src_table'],
                                        'conf_id'=>$k,
              );
          }
      }
      return $list;
  }
  
  
  function get_def_value_for_field($fieldname){
      if (isset($_SESSION['main'])){
        $list=get_defaults_field_list();
        //check user
        if (isset($list[$fieldname])){
            $field=$list[$fieldname];
            
            
             $query=mysql_query("SELECT src.id, src.`".$field['src_title']."`,dv.employeeid FROM z_default_config as dc
                                         LEFT JOIN `z_default_values` as dv ON dv.id=dc.default_id
                                         LEFT JOIN `".$field['src_table']."` as src ON dc.`value`=src.id
                                         LEFT JOIN `s_employee` as em ON em.id=dv.employeeid OR em.parentid=dv.employeeid                                         
                                         WHERE 
                                             dc.conf_id='".$field['conf_id']."' and  ( em.id='".$_SESSION['userid']."' OR dv.employeeid=0 )
                                         ORDER BY dv.employeeid DESC
                                         LIMIT 1"); 
            if (mysql_numrows($query)){
                $row=mysql_fetch_assoc($query);
                return array('title'=>$row[$field['src_title']],'id'=>$row['id']);
            }else{
                return false;
            }
        }
      }
  }
  
  
  function get_default_value($field_id,$default_id,$getid=0){
      global $fields;
      //$default_id - родитель
      //$field_id - id поля в $fields['z_default_values'] - > defaulst
      if ($default_id>0){
          $field=$fields['z_default_values'][$field_id];
          
          $query=mysql_query("SELECT src.id, src.`".$field['src_title']."` FROM z_default_config as dc
                                         LEFT JOIN `".$field['src_table']."` as src ON dc.`value`=src.id
                                         WHERE 
                                            dc.default_id='".$default_id."' AND dc.conf_id='".$field_id."' 
                                         LIMIT 1"); 
                                         
        
          $row=mysql_fetch_array($query);
          if ($getid)
            return $row['id'];
          else
            return $row[$field['src_title']];
      }else{
          return '';
      }
  } 
  
  function checkNFrights($table,$tid,$field){
      if (isset($_SESSION['admin'])) return true;
      if(getMyInterface($_SESSION['userid'])==1) return true;
      if (($table=='s_automated_point')||($table=='s_menu')){
        $query=mysql_query("SELECT id FROM `z_user_right` WHERE `table`='".$table."' AND `uid`='".$_SESSION['userid']."' AND `tid`='".$tid."' AND `".addslashes($field)."`=1");  
        
        return mysql_num_rows($query);
      }else{
          return true;
      }
  }
  
  function db_smena_select_options($ap){
      $result=array();
      $ss='';
      if ($ap>0) {
          $ss=' WHERE idautomated_point="'.$ap.'"';
          //$result[]='<option value="0" '.($selected_id==0?'selected="selected"':'').'> </option>';
          $query=mysql_query("SELECT `d_changes`.id,employeeid,dtopen,dtclosed,closed,fio FROM `d_changes` LEFT JOIN `s_employee` ON `d_changes`.`employeeid`=`s_employee`.`id` ".$ss." ORDER by dtopen DESC,`d_changes`.`id`");  
          while($row=mysql_fetch_array($query)){
              
            $result[]='<option value="'.$row['id'].'">'.$row['fio'].' за '.(isset($row['dtopen'])?date('d.m.Y H:i:s',strtotime($row['dtopen'])):'').' - '.(isset($row['dtclosed'])?date('d.m.Y H:i:s',strtotime($row['dtclosed'])):'').'</option>';
          }
      }
      return join('',$result);
  }
  
  function db_multiselect_options($table,$select_field,$selected_array=array()){
      $result=array();
      $result[]='<option value="0" '.(empty($selected_array)?'selected="selected"':'').'> </option>';
      $query=mysql_query("SELECT name,id FROM `".addslashes($table)."` ORDER by isgroup DESC,id");  
      while($row=mysql_fetch_array($query)){
          if (!empty($selected_array))
            $result[]='<option value="'.$row[$select_field].'" '.(in_array($row[$select_field],$selected_array)?'selected="selected"':'').'>'.$row['name'].'</option>';
          else
            $result[]='<option value="'.$row[$select_field].'">'.$row['name'].'</option>';
      }
      return join('',$result);
  }
  

  
  
  
  function db_multichechbox_options($table,$name,$selected_array=array()){
      $result=array();
      
      $where='';
      //if($table=='s_role') $where=' WHERE id>'.myroll($_SESSION['userid']);
      if($table=='s_interfaces') $where='WHERE id>'.myinterface($_SESSION['userid']);
      
      $query=mysql_query("SELECT name,id FROM `".addslashes($table)."`".$where." ORDER by isgroup DESC,id");  
      while($row=mysql_fetch_array($query)){
          if (!empty($selected_array))
            $result[]='<input type="checkbox" class="checkbx chbchecktozero" name="'.$name.'[]" value="'.$row['id'].'" '.(in_array($row['id'],$selected_array)?'checked="checked"':'').'> '.$row['name'].'<br />';
          else
            $result[]='<input type="checkbox" class="checkbx chbchecktozero" name="'.$name.'[]" value="'.$row['id'].'"> '.$row['name'].'<br />';
      }
      return join('',$result);
  }
  
  
  function get_select_val($table,$id,$selectfield='name'){
      if ($id==0){
          if($table=='s_employee') return 'Аккаунт';
          return '';
      }else{
          $query=mysql_query("SELECT `".$selectfield."` FROM `".addslashes($table)."` WHERE id='".addslashes($id)."'");   
          $row=mysql_fetch_assoc($query);
          return $row['name'];
      }
  }  
  
  function get_multiselect_tablevalues($db_selectto,$to_field,$select_field,$val){
      $res=array();
      
          $query=mysql_query("SELECT `".addslashes($select_field)."` FROM `".addslashes($db_selectto)."` WHERE ".addslashes($to_field)."='".addslashes($val)."'"); 
          
          //echo "SELECT `".addslashes($select_field)."` FROM `".addslashes($db_selectto)."` WHERE ".addslashes($to_field)."='".addslashes($val)."'";
          while($row=mysql_fetch_array($query)){
            $res[]=$row[$select_field];
          }
      return $res;
  } 
  
  
  function get_userrights_tablevalues($table,$uid){
      $res=array();
      
          $query=mysql_query("SELECT * FROM `z_user_right` WHERE `uid`='".addslashes($uid)."' AND `table`='".addslashes($table)."'"); 

          //echo "SELECT `".addslashes($select_field)."` FROM `".addslashes($db_selectto)."` WHERE ".addslashes($to_field)."='".addslashes($val)."'";
          while($row=mysql_fetch_array($query)){
            $res[$row['tid']]['add']=$row['add'];
            $res[$row['tid']]['edit']=$row['edit'];
            $res[$row['tid']]['view']=$row['view'];
            $res[$row['tid']]['delete']=$row['delete'];
            $res[$row['tid']]['print']=$row['print'];
          }
      return $res;
  } 
  
  function get_multiselect_val($table,$id){
      if ($id==0){
          return '';
      }else{
          $query=mysql_query("SELECT name FROM `".addslashes($table)."` WHERE id='".addslashes($id)."'");   
          $row=mysql_fetch_assoc($query);
          return $row['name'];
      }
  } 
  


  
  function get_grid($table,$id,$idfield){
      $array=array();
      if ($id==0){
          
          return '';
      }else{
          $array=array();
          $query=mysql_query("SELECT * FROM `".addslashes($table)."` WHERE ".$idfield."='".addslashes($id)."'");   
          while($row=mysql_fetch_array($query)){
            $array[]=$row;
          }
          return $array;
      }
  }
  
  function getLastIdout($table){
      $query=mysql_query("SELECT MAX(CONVERT(idout, SIGNED)) as idout FROM `".addslashes($table)."`");   

      $row=mysql_fetch_assoc($query);

      if (!empty($row['idout']))
        return ++$row['idout']; //(intval($row['idout'])+1);
        else
        return 1;
  }
  
  
  function checkIdout($table,$id){
      $query=mysql_query("SELECT idout FROM `".addslashes($table)."` WHERE idout='".addslashes($id)."'");   
      $row=mysql_fetch_assoc($query);
      if (!empty($row['idout']))
        return getLastIdout($table);
        else
        return $id;
  }
  
  
  function getAllIdByParentid($id){
      $array='';
      $query=mysql_query("SELECT id,parentid FROM `s_items` WHERE parentid='".addslashes($id)."'");   
      while($row=mysql_fetch_array($query)){
        $array.=$row['id'].',';
        $array.=getAllIdByParentid($row['id']);
      }
      return  $array;
  }
  
  function getAllIdByParentid2($id){
      $str=getAllIdByParentid($id);
    return substr($str, 0, strlen($str)-1);

  }
  
  function CopyNode($fromNode, $toNode,$menuid,$printer){
        $query=mysql_query("SELECT * FROM `s_items` WHERE id='".addslashes($fromNode)."'");    
        $node=mysql_fetch_assoc($query);
        
        if ($node['isgroup'])
            $pr=''; 
        else $pr=$printer;
         mysql_query("INSERT into `t_menu_items` SET menuid='".addslashes($menuid)."',idout='".addslashes($node['idout'])."', idlink='".addslashes($node['idlink'])."',parentid='".addslashes($toNode)."',isgroup='".addslashes($node['isgroup'])."',name='".addslashes($node['name'])."',price='".addslashes($node['price'])."',itemid='".addslashes($node['id'])."', printer='".$pr."'");
         //echo "INSERT into `t_menu_items` SET menuid='".addslashes($menuid)."',idout='".addslashes($node['idout'])."', idlink='".addslashes($node['idlink'])."',parentid='".addslashes($toNode)."',isgroup='".addslashes($node['isgroup'])."',name='".addslashes($node['name'])."',price='".addslashes($node['price'])."',itemid='".addslashes($node['id'])."', printer='".$pr."'";
         //die;
         $last_id=mysql_insert_id();
         
         
         $query=mysql_query("SELECT id,parentid FROM `s_items` WHERE parentid='".addslashes($fromNode)."'");   
         while($row=mysql_fetch_array($query)){
            CopyNode($row['id'],$last_id,$menuid,$printer);
         }
         s_menu_lastupdate('t_menu_items',$last_id);
  }
  
    function deleteMenu($id,$menuid,$Artur=1){
        $query=mysql_query("SELECT id FROM `t_menu_items` WHERE parentid='".addslashes($id)."' AND menuid='".addslashes($menuid)."'"); 
         
        while($row=mysql_fetch_array($query)){
            deleteMenu($row['id'],$menuid);
        }
        s_menu_lastupdate('t_menu_items',$id);
        if ($Artur)
            mysql_query("DELETE FROM `t_menu_items` WHERE id='".addslashes($id)."'");         
    }
  
  function nbsp($el,$level){
      $nbsp='';
       if ($el==1)
          for($i=1;$i<=$level;$i++)
            $nbsp.=' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
      return $nbsp;
  } 
  
  function getmultiselectvidoplaty(){
      $res=array();
      $query=mysql_query("SELECT * FROM `s_types_of_payment`");   
         while($row=mysql_fetch_assoc($query)){
            $res[]='<option value="'.$row['id'].'">'.$row['name'].'</option>';
         }
      return '<select multiple name="vidoplaty[]" class="form-control">'.join('',$res).'</select>';
  } 
  
  function bold($val, $isgroup,$isname){
      if ($isgroup){
          if ($isname)
            return '<b>'.$val.'</b>';
          else
            return '';
      }
      else{
        return $val;
      }
  }
  function getfvalue($value,$field){
      $result='';
      switch($field['type']){
          case 'input': 
            $result=$value;
          break;
          case 'date': 
            $result=date('d.m.Y',strtotime($value));;
          break;
          case 'datetime': 
            $result=date('d.m.Y H:i:s',strtotime($value));
          break;
          case 'db_select': 
            $query=mysql_query("SELECT id,name FROM `".addslashes($field['db_select'])."` WHERE id='".addslashes($value)."' LIMIT 1");  
            $row=mysql_fetch_array($query);
            $result=$row['name'];
          break; 
          case 'label': 
            $query=mysql_query("SELECT id,name FROM `".addslashes($field['db_select'])."` WHERE id='".addslashes($value)."' LIMIT 1");  
            $row=mysql_fetch_array($query);
            $result=$row['name'];
          break;
          case 'checkbox': 
            $result=($value?'Да':'Нет');
          break;
          
      }
      return $result;
  }
  
  function getprintel($table,$parentid,$level,$fields){
      $query=mysql_query("SELECT * FROM `".addslashes($table)."` WHERE parentid='".addslashes($parentid)."'");
      if (mysql_numrows($query))
      while($row=mysql_fetch_assoc($query)){
            $res.='<tr>';
            $i=1;
            foreach ($fields as $k=>$v){
                if ($v['in_grid']){
                    $res.='<td>'.nbsp($i,$level).bold(getfvalue($row[$k],$fields[$k]),$row['isgroup'],($k=='name')).'</td>';
                    $i++;
                }
            }
            $res.='</tr>';
            $res.=getprintel($table,$row['id'],$level+1,$fields);
        } 
      return $res;
      
        
  }
  
  
  function getprintdezmenu($id,$menu_id,$level=0){
      $res='';
      $nbsp='';
      for($i=0;$i<$level;$i++){ 
            $nbsp.='&nbsp;&nbsp;&nbsp;'; 
  
      }
      $query=mysql_query("SELECT * FROM `t_menu_items` WHERE parentid='".addslashes($id)."' AND menuid='".addslashes($menu_id)."'");
      if (mysql_numrows($query)){
          while($row=mysql_fetch_assoc($query)){
             $res.='<tr '.($row['isgroup']==1?'class="isgroup"':'').'><td>'.$row['idout'].'</td><td>'.$nbsp.$row['name'].'</td><td>'.$row['price'].'</td></tr>'.getprintdezmenu($row['id'],$menu_id,$level+1);
          }
      }
      return $res;
       
  }
  
  
  function checkIdout2Edit($table,$id,$idout){
      $query=mysql_query("SELECT idout,id FROM `".addslashes($table)."` WHERE idout='".addslashes($idout)."'");   
      $row=mysql_fetch_assoc($query);
      if (!empty($row['idout'])){
        if ($row['id']==$id)
            return $idout;
        else
            return getLastIdout($table);
      }
        else
        return $idout;
  }
  

  

  
  function check_users_available_fields($userid,$table,$field){
      if (isset($_SESSION['admin'])) return 1;
      $pipiska=getMyInterface($userid);
      
      if (($table=='s_employee') && ($field=='front2company') && ($pipiska>1)) return 0;
      if (($table=='s_employee') && ($field=='multiselect2') && ($pipiska>1)) return 0;
      
      return 1;
      
  }
  


  
  function getConfigVal($key){
    $query=mysql_query("SELECT value FROM `s_config` WHERE `key`='".addslashes($key)."' LIMIT 1"); 
    if (mysql_numrows($query)){  
        $row=mysql_fetch_assoc($query);  
        return $row['value'];
    }else{
        return false;
    }

  }
  
  
  function setSubDivision($printer, $cat,$table){
      $ipeshiymudak='printer';
      if ($table=='s_items') $ipeshiymudak='i_printer'; 
      mysql_query("UPDATE `".$table."` SET ".$ipeshiymudak."='".addslashes($printer)."' WHERE parentid='".addslashes($cat)."' OR id='".addslashes($cat)."' AND isgroup=0");
      $query=mysql_query("SELECT * FROM `".$table."` WHERE parentid='".addslashes($cat)."'"); 
      while($row=mysql_fetch_array($query)){
            setSubDivision($printer,$row['id'],$table);
      }
  }
  
  $zones=array("Pacific/Midway"=>'(GMT-11:00) Мидуэй',"Pacific/Midway"=>'(GMT-11:00) Мидуэй',"Pacific/Niue"=>'(GMT-11:00) Ниуэ',"Pacific/Pago_Pago"=>'(GMT-11:00) Паго-Паго',"Pacific/Honolulu"=>'(GMT-10:00) Гавайское время',"Pacific/Johnston"=>'(GMT-10:00) атолл Джонстон',"Pacific/Rarotonga"=>'(GMT-10:00) Раротонга',"Pacific/Tahiti"=>'(GMT-10:00) Таити',"Pacific/Marquesas"=>'(GMT-09:30) Маркизские острова',"America/Anchorage"=>'(GMT-09:00) Время Аляски',"Pacific/Gambier"=>'(GMT-09:00) Гамбир',"America/Los_Angeles"=>'(GMT-08:00) Тихоокеанское время',"America/Tijuana"=>'(GMT-08:00) Тихоокеанское время – Тихуана',"America/Vancouver"=>'(GMT-08:00) Тихоокеанское время – Ванкувер',"America/Whitehorse"=>'(GMT-08:00) Тихоокеанское время – Уайтхорс',"Pacific/Pitcairn"=>'(GMT-08:00) Питкэрн',"America/Dawson_Creek"=>'(GMT-07:00) Горное время – Доусон Крик',"America/Denver"=>'(GMT-07:00) Горное время',"America/Edmonton"=>'(GMT-07:00) Горное время – Эдмонтон',"America/Hermosillo"=>'(GMT-07:00) Горное время – Эрмосильо',"America/Mazatlan"=>'(GMT-07:00) Горное время – Чиуауа, Мазатлан',"America/Phoenix"=>'(GMT-07:00) Горное время – Аризона',"America/Yellowknife"=>'(GMT-07:00) Горное время – Йеллоунайф',"America/Belize"=>'(GMT-06:00) Белиз',"America/Chicago"=>'(GMT-06:00) Центральное время',"America/Costa_Rica"=>'(GMT-06:00) Коста-Рика',"America/El_Salvador"=>'(GMT-06:00) Сальвадор',"America/Guatemala"=>'(GMT-06:00) Гватемала',"America/Managua"=>'(GMT-06:00) Манагуа',"America/Mexico_City"=>'(GMT-06:00) Центральное время – Мехико',"America/Regina"=>'(GMT-06:00) Центральное время – Реджайна',"America/Tegucigalpa"=>'(GMT-06:00) Центральное время – Тегусигальпа',"America/Winnipeg"=>'(GMT-06:00) Центральное время – Виннипег',"Pacific/Easter"=>'(GMT-06:00) остров Пасхи',"Pacific/Galapagos"=>'(GMT-06:00) Галапагос',"America/Bogota"=>'(GMT-05:00) Богота',"America/Cayman"=>'(GMT-05:00) Каймановы острова',"America/Grand_Turk"=>'(GMT-05:00) Гранд Турк',"America/Guayaquil"=>'(GMT-05:00) Гуаякиль',"America/Havana"=>'(GMT-05:00) Гавана',"America/Iqaluit"=>'(GMT-05:00) Восточное время – Икалуит',"America/Jamaica"=>'(GMT-05:00) Ямайка',"America/Lima"=>'(GMT-05:00) Лима',"America/Montreal"=>'(GMT-05:00) Восточное время – Монреаль',"America/Nassau"=>'(GMT-05:00) Нассау',"America/New_York"=>'(GMT-05:00) Восточное время',"America/Panama"=>'(GMT-05:00) Панама',"America/Port-au-Prince"=>'(GMT-05:00) Порт-о-Пренс',"America/Toronto"=>'(GMT-05:00) Восточное время – Торонто',"America/Caracas"=>'(GMT-04:30) Каракас',"America/Anguilla"=>'(GMT-04:00) Ангилья',"America/Antigua"=>'(GMT-04:00) Антигуа',"America/Aruba"=>'(GMT-04:00) Аруба',"America/Asuncion"=>'(GMT-04:00) Асунсьон',"America/Barbados"=>'(GMT-04:00) Барбадос',"America/Boa_Vista"=>'(GMT-04:00) Боа-Виста',"America/Campo_Grande"=>'(GMT-04:00) Кампу-Гранди',"America/Cuiaba"=>'(GMT-04:00) Куяба',"America/Curacao"=>'(GMT-04:00) Кюрасао',"America/Dominica"=>'(GMT-04:00) Доминика',"America/Grenada"=>'(GMT-04:00) Гренада',"America/Guadeloupe"=>'(GMT-04:00) Гваделупа',"America/Guyana"=>'(GMT-04:00) Гайана',"America/Halifax"=>'(GMT-04:00) Атлантическое время – Галифакс',"America/La_Paz"=>'(GMT-04:00) Ла-Пас',"America/Manaus"=>'(GMT-04:00) Манаус',"America/Martinique"=>'(GMT-04:00) Мартиника',"America/Montserrat"=>'(GMT-04:00) Монсеррат',"America/Port_of_Spain"=>'(GMT-04:00) Порт-оф-Спейн',"America/Porto_Velho"=>'(GMT-04:00) Порто-Велью',"America/Puerto_Rico"=>'(GMT-04:00) Пуэрто-Рико',"America/Rio_Branco"=>'(GMT-04:00) Риу-Бранку',"America/Santiago"=>'(GMT-04:00) Сантьяго',"America/Santo_Domingo"=>'(GMT-04:00) Санто-Доминго',"America/St_Kitts"=>'(GMT-04:00) Сент-Китс',"America/St_Lucia"=>'(GMT-04:00) Сент-Люсия',"America/St_Thomas"=>'(GMT-04:00) Сент-Томас',"America/St_Vincent"=>'(GMT-04:00) Сент-Винсент',"America/Thule"=>'(GMT-04:00) Тули',"America/Tortola"=>'(GMT-04:00) Тортола',"Antarctica/Palmer"=>'(GMT-04:00) Палмер',"Atlantic/Bermuda"=>'(GMT-04:00) Бермуды',"America/St_Johns"=>'(GMT-03:30) Ньюфаундлендское время – Сент-Джонс',"America/Araguaina"=>'(GMT-03:00) Арагуайна',"America/Argentina/Buenos_Aires"=>'(GMT-03:00) Буэнос-Айрес',"America/Bahia"=>'(GMT-03:00) Сальвадор',"America/Belem"=>'(GMT-03:00) Белен',"America/Cayenne"=>'(GMT-03:00) Кайенна',"America/Fortaleza"=>'(GMT-03:00) Форталеза',"America/Godthab"=>'(GMT-03:00) Годхоб',"America/Maceio"=>'(GMT-03:00) Масейо',"America/Miquelon"=>'(GMT-03:00) Микелон',"America/Montevideo"=>'(GMT-03:00) Монтевидео',"America/Paramaribo"=>'(GMT-03:00) Парамарибо',"America/Recife"=>'(GMT-03:00) Ресифи',"America/Sao_Paulo"=>'(GMT-03:00) Сан-Пауло',"Antarctica/Rothera"=>'(GMT-03:00) Ротера',"Atlantic/Stanley"=>'(GMT-03:00) Стэнли',"America/Noronha"=>'(GMT-02:00) Норонха',"Atlantic/South_Georgia"=>'(GMT-02:00) Южная Георгия',"America/Scoresbysund"=>'(GMT-01:00) Скорсби',"Atlantic/Azores"=>'(GMT-01:00) Азорские острова',"Atlantic/Cape_Verde"=>'(GMT-01:00) Кабо-Верде',"Africa/Abidjan"=>'(GMT+00:00) Абиджан',"Africa/Accra"=>'(GMT+00:00) Аккра',"Africa/Bamako"=>'(GMT+00:00) Бамако',"Africa/Banjul"=>'(GMT+00:00) Банжул',"Africa/Bissau"=>'(GMT+00:00) Бисау',"Africa/Casablanca"=>'(GMT+00:00) Касабланка',"Africa/Conakry"=>'(GMT+00:00) Конакри',"Africa/Dakar"=>'(GMT+00:00) Дакар',"Africa/El_Aaiun"=>'(GMT+00:00) Эль-Аюн',"Africa/Freetown"=>'(GMT+00:00) Фритаун',"Africa/Lome"=>'(GMT+00:00) Ломе',"Africa/Monrovia"=>'(GMT+00:00) Монровия',"Africa/Nouakchott"=>'(GMT+00:00) Нуакшот',"Africa/Ouagadougou"=>'(GMT+00:00) Уагадугу',"Africa/Sao_Tome"=>'(GMT+00:00) Сан-Томе',"America/Danmarkshavn"=>'(GMT+00:00) Данмаркшавн',"Atlantic/Canary"=>'(GMT+00:00) Канарские острова',"Atlantic/Faroe"=>'(GMT+00:00) Фарерские острова',"Atlantic/Reykjavik"=>'(GMT+00:00) Рейкьявик',"Atlantic/St_Helena"=>'(GMT+00:00) остров Святой Елены',"Etc/GMT"=>'(GMT+00:00) Время по Гринвичу (без перехода на летнее время)',"Europe/Dublin"=>'(GMT+00:00) Дублин',"Europe/Lisbon"=>'(GMT+00:00) Лиссабон',"Europe/London"=>'(GMT+00:00) Лондон',"Africa/Algiers"=>'(GMT+01:00) Алжир',"Africa/Bangui"=>'(GMT+01:00) Банги',"Africa/Brazzaville"=>'(GMT+01:00) Браззавиль',"Africa/Ceuta"=>'(GMT+01:00) Сеута',"Africa/Douala"=>'(GMT+01:00) Дуала',"Africa/Kinshasa"=>'(GMT+01:00) Киншаса',"Africa/Lagos"=>'(GMT+01:00) Лагос',"Africa/Libreville"=>'(GMT+01:00) Либревиль',"Africa/Luanda"=>'(GMT+01:00) Луанда',"Africa/Malabo"=>'(GMT+01:00) Малабо',"Africa/Ndjamena"=>'(GMT+01:00) Нджамена',"Africa/Niamey"=>'(GMT+01:00) Ниамей',"Africa/Porto-Novo"=>'(GMT+01:00) Порто-Ново',"Africa/Tripoli"=>'(GMT+01:00) Триполи',"Africa/Tunis"=>'(GMT+01:00) Тунис',"Africa/Windhoek"=>'(GMT+01:00) Виндхук',"Europe/Amsterdam"=>'(GMT+01:00) Амстердам',"Europe/Andorra"=>'(GMT+01:00) Андорра',"Europe/Belgrade"=>'(GMT+01:00) Центральноевропейское время – Белград',"Europe/Berlin"=>'(GMT+01:00) Берлин',"Europe/Brussels"=>'(GMT+01:00) Брюссель',"Europe/Budapest"=>'(GMT+01:00) Будапешт',"Europe/Copenhagen"=>'(GMT+01:00) Копенгаген',"Europe/Gibraltar"=>'(GMT+01:00) Гибралтар',"Europe/Luxembourg"=>'(GMT+01:00) Люксембург',"Europe/Madrid"=>'(GMT+01:00) Мадрид',"Europe/Malta"=>'(GMT+01:00) Мальта',"Europe/Monaco"=>'(GMT+01:00) Монако',"Europe/Oslo"=>'(GMT+01:00) Осло',"Europe/Paris"=>'(GMT+01:00) Париж',"Europe/Prague"=>'(GMT+01:00) Центральноевропейское время – Прага',"Europe/Rome"=>'(GMT+01:00) Рим',"Europe/Stockholm"=>'(GMT+01:00) Стокгольм',"Europe/Tirane"=>'(GMT+01:00) Тирана',"Europe/Vaduz"=>'(GMT+01:00) Вадуц',"Europe/Vienna"=>'(GMT+01:00) Вена',"Europe/Warsaw"=>'(GMT+01:00) Варшава',"Europe/Zurich"=>'(GMT+01:00) Цюрих',"Africa/Blantyre"=>'(GMT+02:00) Блантайр',"Africa/Bujumbura"=>'(GMT+02:00) Бужумбура',"Africa/Cairo"=>'(GMT+02:00) Каир',"Africa/Gaborone"=>'(GMT+02:00) Габороне',"Africa/Harare"=>'(GMT+02:00) Хараре',"Africa/Johannesburg"=>'(GMT+02:00) Йоханнесбург',"Africa/Kigali"=>'(GMT+02:00) Кигали',"Africa/Lubumbashi"=>'(GMT+02:00) Лубумбаши',"Africa/Lusaka"=>'(GMT+02:00) Лусака',"Africa/Maputo"=>'(GMT+02:00) Мапуту',"Africa/Maseru"=>'(GMT+02:00) Масеру',"Africa/Mbabane"=>'(GMT+02:00) Мбабане',"Asia/Amman"=>'(GMT+02:00) Амман',"Asia/Beirut"=>'(GMT+02:00) Бейрут',"Asia/Damascus"=>'(GMT+02:00) Дамаск',"Asia/Gaza"=>'(GMT+02:00) Газа',"Asia/Jerusalem"=>'(GMT+02:00) Иерусалим',"Asia/Nicosia"=>'(GMT+02:00) Никосия',"Europe/Athens"=>'(GMT+02:00) Афины',"Europe/Bucharest"=>'(GMT+02:00) Бухарест',"Europe/Chisinau"=>'(GMT+02:00) Кишинев',"Europe/Helsinki"=>'(GMT+02:00) Хельсинки',"Europe/Istanbul"=>'(GMT+02:00) Стамбул',"Europe/Kiev"=>'(GMT+02:00) Киев',"Europe/Riga"=>'(GMT+02:00) Рига',"Europe/Sofia"=>'(GMT+02:00) София',"Europe/Tallinn"=>'(GMT+02:00) Таллинн',"Europe/Vilnius"=>'(GMT+02:00) Вильнюс',"Africa/Addis_Ababa"=>'(GMT+03:00) Аддис-Абеба',"Africa/Asmara"=>'(GMT+03:00) Асмера',"Africa/Dar_es_Salaam"=>'(GMT+03:00) Дар-эс-Салам',"Africa/Djibouti"=>'(GMT+03:00) Джибути',"Africa/Kampala"=>'(GMT+03:00) Кампала',"Africa/Khartoum"=>'(GMT+03:00) Хартум',"Africa/Mogadishu"=>'(GMT+03:00) Могадишо',"Africa/Nairobi"=>'(GMT+03:00) Найроби',"Antarctica/Syowa"=>'(GMT+03:00) Сиова',"Asia/Aden"=>'(GMT+03:00) Аден',"Asia/Baghdad"=>'(GMT+03:00) Багдад',"Asia/Bahrain"=>'(GMT+03:00) Бахрейн',"Asia/Kuwait"=>'(GMT+03:00) Кувейт',"Asia/Qatar"=>'(GMT+03:00) Катар',"Asia/Riyadh"=>'(GMT+03:00) Эр-Рияд',"Europe/Kaliningrad"=>'(GMT+03:00) Москва-01 – Калининград',"Europe/Minsk"=>'(GMT+03:00) Минск',"Indian/Antananarivo"=>'(GMT+03:00) Антананариву',"Indian/Comoro"=>'(GMT+03:00) Коморские острова',"Indian/Mayotte"=>'(GMT+03:00) Майотта',"Asia/Tehran"=>'(GMT+03:30) Тегеран',"Asia/Baku"=>'(GMT+04:00) Баку',"Asia/Dubai"=>'(GMT+04:00) Дубай',"Asia/Muscat"=>'(GMT+04:00) Мускат',"Asia/Tbilisi"=>'(GMT+04:00) Тбилиси',"Asia/Yerevan"=>'(GMT+04:00) Ереван',"Europe/Moscow"=>'(GMT+04:00) Москва +00',"Europe/Samara"=>'(GMT+04:00) Москва +00 – Самара',"Indian/Mahe"=>'(GMT+04:00) Маэ',"Indian/Mauritius"=>'(GMT+04:00) Маврикий',"Indian/Reunion"=>'(GMT+04:00) Реюньон',"Asia/Kabul"=>'(GMT+04:30) Кабул',"Antarctica/Mawson"=>'(GMT+05:00) Моусон',"Asia/Aqtau"=>'(GMT+05:00) Актау',"Asia/Aqtobe"=>'(GMT+05:00) Актобе',"Asia/Ashgabat"=>'(GMT+05:00) Ашгабат',"Asia/Dushanbe"=>'(GMT+05:00) Душанбе',"Asia/Karachi"=>'(GMT+05:00) Карачи',"Asia/Tashkent"=>'(GMT+05:00) Ташкент',"Indian/Kerguelen"=>'(GMT+05:00) Кергелен',"Indian/Maldives"=>'(GMT+05:00) Мальдивы',"Asia/Calcutta"=>'(GMT+05:30) Индийское время',"Asia/Colombo"=>'(GMT+05:30) Коломбо',"Asia/Katmandu"=>'(GMT+05:45) Катманду',"Antarctica/Vostok"=>'(GMT+06:00) Восток',"Asia/Almaty"=>'(GMT+06:00) Алматы',"Asia/Bishkek"=>'(GMT+06:00) Бишкек',"Asia/Dhaka"=>'(GMT+06:00) Дхака',"Asia/Thimphu"=>'(GMT+06:00) Тхимпху',"Asia/Yekaterinburg"=>'(GMT+06:00) Москва +02 – Екатеринбург',"Indian/Chagos"=>'(GMT+06:00) Чагос',"Asia/Rangoon"=>'(GMT+06:30) Рангун',"Indian/Cocos"=>'(GMT+06:30) Кокосовые острова',"Antarctica/Davis"=>'(GMT+07:00) Дейвис',"Asia/Bangkok"=>'(GMT+07:00) Бангкок',"Asia/Hovd"=>'(GMT+07:00) Ховд',"Asia/Jakarta"=>'(GMT+07:00) Джакарта',"Asia/Omsk"=>'(GMT+07:00) Москва +03 – Омск, Новосибирск',"Asia/Phnom_Penh"=>'(GMT+07:00) Пномпень',"Asia/Saigon"=>'(GMT+07:00) Ханой',"Asia/Vientiane"=>'(GMT+07:00) Вьентьян',"Indian/Christmas"=>'(GMT+07:00) Рождественские острова',"Antarctica/Casey"=>'(GMT+08:00) Кейси',"Asia/Brunei"=>'(GMT+08:00) Бруней',"Asia/Choibalsan"=>'(GMT+08:00) Чойбалсан',"Asia/Hong_Kong"=>'(GMT+08:00) Гонконг',"Asia/Krasnoyarsk"=>'(GMT+08:00) Москва +04 – Красноярск',"Asia/Kuala_Lumpur"=>'(GMT+08:00) Куала-Лумпур',"Asia/Macau"=>'(GMT+08:00) Макау',"Asia/Makassar"=>'(GMT+08:00) Макасар',"Asia/Manila"=>'(GMT+08:00) Манила',"Asia/Shanghai"=>'(GMT+08:00) Китайское время – Пекин',"Asia/Singapore"=>'(GMT+08:00) Сингапур',"Asia/Taipei"=>'(GMT+08:00) Тайбэй',"Asia/Ulaanbaatar"=>'(GMT+08:00) Улан-Батор',"Australia/Perth"=>'(GMT+08:00) Западное время – Перт',"Asia/Dili"=>'(GMT+09:00) Дили',"Asia/Irkutsk"=>'(GMT+09:00) Москва +05 – Иркутск',"Asia/Jayapura"=>'(GMT+09:00) Джапура',"Asia/Pyongyang"=>'(GMT+09:00) Пхеньян',"Asia/Seoul"=>'(GMT+09:00) Сеул',"Asia/Tokyo"=>'(GMT+09:00) Токио',"Pacific/Palau"=>'(GMT+09:00) Палау',"Australia/Adelaide"=>'(GMT+09:30) Центральное время – Аделаида',"Australia/Darwin"=>'(GMT+09:30) Центральное время – Дарвин',"Antarctica/DumontDUrville"=>'(GMT+10:00) Дюмон-Дюрвиль',"Asia/Yakutsk"=>'(GMT+10:00) Москва +06 – Якутск',"Australia/Brisbane"=>'(GMT+10:00) Восточное время – Брисбен',"Australia/Hobart"=>'(GMT+10:00) Восточное время – Хобарт',"Australia/Sydney"=>'(GMT+10:00) Восточное время – Мельбурн, Сидней',"Pacific/Guam"=>'(GMT+10:00) Гуам',"Pacific/Port_Moresby"=>'(GMT+10:00) Порт-Морсби',"Pacific/Saipan"=>'(GMT+10:00) Сайпан',"Pacific/Truk"=>'(GMT+10:00) Трук',"Asia/Vladivostok"=>'(GMT+11:00) Москва +07 – Южно-Сахалинск',"Pacific/Efate"=>'(GMT+11:00) Эфате',"Pacific/Guadalcanal"=>'(GMT+11:00) Гвадалканал',"Pacific/Kosrae"=>'(GMT+11:00) Косраэ',"Pacific/Noumea"=>'(GMT+11:00) Нумеа',"Pacific/Ponape"=>'(GMT+11:00) Понапе',"Pacific/Norfolk"=>'(GMT+11:30) Норфолк',"Asia/Kamchatka"=>'(GMT+12:00) Москва +08 – Петропавловск-Камчатский',"Asia/Magadan"=>'(GMT+12:00) Москва +08 – Магадан',"Pacific/Auckland"=>'(GMT+12:00) Оклэнд',"Pacific/Fiji"=>'(GMT+12:00) Фиджи',"Pacific/Funafuti"=>'(GMT+12:00) Фунафути',"Pacific/Kwajalein"=>'(GMT+12:00) Кваджелейн',"Pacific/Majuro"=>'(GMT+12:00) Маджуро',"Pacific/Nauru"=>'(GMT+12:00) Науру',"Pacific/Tarawa"=>'(GMT+12:00) Тарава',"Pacific/Wake"=>'(GMT+12:00) остров Вэйк',"Pacific/Wallis"=>'(GMT+12:00) Уоллис',"Pacific/Apia"=>'(GMT+13:00) Апия',"Pacific/Enderbury"=>'(GMT+13:00) острова Эндербери',"Pacific/Fakaofo"=>'(GMT+13:00) Факаофо',"Pacific/Tongatapu"=>'(GMT+13:00) Тонгатапу',"Pacific/Kiritimati"=>'(GMT+14:00) Киритимати');
  
  
  function getTimeZones($selected=0){
      global $zones;
      $ret=array();
      $ret[]='<option value="0" '.($selected===0?'selected="selected"':"").'> </option>';
      foreach($zones as $k=>$v){
          $ret[]='<option value="'.$k.'"'.($selected===$k?'selected="selected"':"").'>'.$v.'</option>';
      }
      return join('',$ret);
  }
  
    function getTimeZoneValue($selected){
      global $zones;
      if ($selected){
          return $zones[$selected];
      }else{
          return '';
      }
  }
  

   function check_in_table($tablename,$fieldname,$value){
       $q=mysql_query("SELECT id FROM `".addslashes($tablename)."` WHERE ".addslashes($fieldname)."='".addslashes($value)."' LIMIT 1");
       return !mysql_num_rows($q);
   }
   
   function delete_subdata($tablename,$fieldname,$value){
       mysql_query("DELETE FROM `".addslashes($tablename)."` WHERE ".addslashes($fieldname)."='".addslashes($value)."'");
   }
   
   function zlog($desc,$type,$base=NULL){
       if (!isset($base)){
            $base=$_SESSION['base']; 
       }
       $usertype='';
       if (isset($_SESSION['main'])) $usertype.='main';
       if (isset($_SESSION['front'])) $usertype.='front';
       if (isset($_SESSION['admin'])) $usertype.='admin';

       $userid=0;
       if (isset($_SESSION['userid'])) $userid=$_SESSION['userid'];
       if (isset($_SESSION['admin'])) $userid=0;
       mysql_query("INSERT INTO `".$base."`.`z_logs` SET `desc`='".$desc."',ip='".ip2long($_SERVER['REMOTE_ADDR'])."',type='".$type."', userid='".$userid."', usertype='".$usertype."' ");
   
   }
   
   function recovery_account($email){
      $q=mysql_query("SELECT id FROM `dbisoftik`.`s_accounts`  WHERE email='".addslashes($email)."' LIMIT 1");
      if (mysql_num_rows($q)){
          $key=md5('Плюшевая Борода одобряет '.time());
          mysql_query("UPDATE `dbisoftik`.`s_accounts` SET `recovery_key`='".$key."' WHERE email='".addslashes($email)."' LIMIT 1");
          $message='Для восстановления пароля перейдите по ссылке <a href="http://'.$_SERVER['SERVER_NAME'].'/recovery.php?key='.$key.'">http://'.$_SERVER['SERVER_NAME'].'/recovery.php?key='.$key.'</a>';
          sendmail('Восстановление пароля',$message,$email);
          return true;
      }else{
          return false;
      }
   }
   
   function sendmail($subject,$body,$to){
        require("pdf/class.phpmailer.php");
        $mail = new PHPMailer();
        $mail->CharSet = "UTF-8";
        $mail->IsSMTP();
        $mail->Host = "213.180.193.38";
        $mail->Port = "25";
        $mail->SMTPDebug  = 0;
        $mail->SMTPAuth = true;  
        $mail->Username = "noreply@paloma365.kz";  
        $mail->Password = "noreply.paloma365.kz"; 
        $mail->From = "noreply@paloma365.kz";
        $mail->FromName = "Paloma365";
        $mail->AddAddress($to);
        $mail->IsHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        return $mail->Send();
   }
   
   function mail_new_password_account($key){
       if (($key!='')&&($key!==0)&&($key!=' ')){
          $q=mysql_query("SELECT email,username FROM `dbisoftik`.`s_accounts`  WHERE `recovery_key`='".addslashes($key)."' LIMIT 1");

          if (mysql_num_rows($q)){
              $r=mysql_fetch_array($q);
              $pass=substr(md5('Плюшевая Борода одобряет '.time()),1,8);
              mysql_query("UPDATE `dbisoftik`.`s_accounts` SET `recovery_key`='',password='".md5(FISH.md5($pass))."' WHERE `recovery_key`='".addslashes($key)."' LIMIT 1");
              $message='Ваш компания: '.$r['username'].'<br>Ваш новый пароль: <b>'.$pass.'</b>';
              sendmail('Новый пароль',$message,$r['email']);
              return true;
          }else{
              return false;
          }
       }else 
       return false;
   }
   
   
                    

   function check_employee_interface($id,$base){
       $query=mysql_query("SELECT `id` FROM `".$base."`.`t_employee_interface` WHERE employeeid='".addslashes($id)."' AND ((`rollid`=1) OR (`rollid`=2))");
       //echo "SELECT `id` FROM `".$base."`.`t_employee_interface` WHERE employeeid='".addslashes($id)."' AND ((`rollid`=1) OR (`rollid`=2))"; die;
       return mysql_numrows($query);
   }
   
   function mail_to_zottig_and_his_friends($mail,$message){
        $message='<b>Email:</b><br />'.$mail.'<br /><br /><b>Сообщение:</b><br />'.$message;
        sendmail('Нам пишут',$message,'info@paloma365.kz'); 
   }
   
   function setConfig($key,$value){
       $query=mysql_query("SELECT `id` FROM `s_config` WHERE `key`='".addslashes($key)."' LIMIT 1");
       if (mysql_numrows($query)){
            mysql_query("UPDATE `s_config` SET `value`='".addslashes($value)."' WHERE `key`='".addslashes($key)."' LIMIT 1");
       }else{
            mysql_query("INSERT INTO `s_config` SET `value`='".addslashes($value)."',`key`='".addslashes($key)."'");
       }
   }
   
   function getConfig($key){
       $query=mysql_query("SELECT `value` FROM `s_config` WHERE `key`='".addslashes($key)."' LIMIT 1");
       if (mysql_numrows($query)){
           $row=mysql_fetch_assoc($query);
           return $row['value'];
       }else{
           return false;
       }
   }
   
   function s_menu_lastupdate($tablename,$id)
    {
        /////vlad           
            if ($tablename=='t_menu_items'){
                zlog('обновить меню таблица='.$tablename.' id='.$id,1202);           
                mysql_query("UPDATE `s_menu` SET lastupdate=now() WHERE s_menu.id=(select t_menu_items.menuid from t_menu_items where t_menu_items.id='".addslashes($id)."')");            
            }
        ////vlad
    }  
    
   function checkfromfront(){
       if (isset($_SESSION['point'])&&isset($_SESSION['employeeid'])&&isset($_SESSION['interfaces'])&&(($_SESSION['interfaces']==0) ||($_SESSION['interfaces']==1))){
           $_SESSION['userid']=$_SESSION['employeeid'];
           $query=mysql_query("SELECT `id` FROM `".$_SESSION['base']."`.`s_employee` WHERE id=".$_SESSION['employeeid']." AND `front2company`=1");

           if (mysql_numrows($query)==0){
               return false;
           }else
           return check_employee_interface($_SESSION['employeeid'],$_SESSION['base']);
       }else{
           return false;
       }
   }
   
   function checkuniqfield($table,$field,$val){
        $query=mysql_query("SELECT `".$field."` FROM `".$table."` WHERE `".$field."`='".addslashes($val)."'");
        return mysql_numrows($query);
   }
   
   function getfiobyid($id){
       $query=mysql_query("SELECT fio FROM `s_employee` WHERE id='".addslashes($id)."' LIMIT 1");  
       if (mysql_numrows($query)){
            $row=mysql_fetch_array($query);
            return $row['fio'];
       }else{
            return '';
       }
   }
   
   function getParents($table,$parentid){
    $answer = array();
    global $fields;
    
    $result = mysql_query("SELECT * FROM `".$table."` WHERE id = ".$parentid." ORDER BY isgroup DESC, name");
    
    if ($result){
        $row = mysql_fetch_assoc($result);
        
        $a = array();
        $a['id']=$row['id']; 
        $a['parentid']=$row['parentid'];
        $a['isgroup']=$row['isgroup'];
        $a['name']=$row['name'];
 
        
        
        foreach($fields[$table] as $k=>$v){
            if ($v['in_group'])
                $a[$k]=$row[$k];
        }
        
        $answer[] = $a;
        
        if ($row["parentid"] > 0){
            $b = getParents($table,$row["parentid"]);
            array_splice( $answer, 0, 0, $b );
        }
        
    } else;
    
    return $answer;
}

function getServiceRemain(){
    //тут арфолс напишет крутой запрос
    $q=mysql_query("SELECT DATEDIFF(MIN(date),NOW()) as x  FROM ((SELECT  MIN(expiration_date) AS date FROM t_workplace) UNION (SELECT  MIN(expiration_date) AS date FROM s_automated_point)) AS lalal");
    $rrrrr=mysql_fetch_assoc($q);
    if ($rrrrr['x']!=''){
        return $rrrrr['x'];
    }else{
        return 'error';
    }
    
    
}

function getfilterfields($table){
global $fields,$loger;
$res=array();
    foreach($fields[$table] as $k=>$v){
        
        if ($v['in_grid'])
            switch($v['type']){
                case 'input':
                   $res[]='<div class="zfilter"><label>'.$v['title'].'</label><input type="input" name="'.$k.'" class="form-control"></div>';
                   break;
                   case 'barcode':
                   $res[]='<div class="zfilter"><label>'.$v['title'].'</label><input type="input" name="'.$k.'" class="form-control"></div>';
                   break;
                case 'db_select':
                    $res[]='<div class="zfilter">
                    <label>'.$v['title'].'</label>
                    <div class="input-group">
                        <input id="'.$table.'_'.$k.'_filter" class="form-control" onkeyup="oninputchange(event,this,\''.$fields[$table][$k]['db_select'].'\')"> 
                        <input name="'.$k.'"  type="hidden">
                        <div class="input-group-btn">
                          <button type="button" class="btn btn-default" tabindex="-1" onclick="show_dbselect_window(\''.$fields[$table][$k]['db_select'].'\',\''.$table.'_'.$k.'_filter\')">...</button>
                          <button type="button" class="btn btn-default" tabindex="-1" onclick="clear_db_select(this);return false"> X </button>
                        </div>
                        </div>
                    </div>';
                   break;  
                /*case 'db_groupselect':
                   $a[$k]=get_select_val($v['db_select'],$row[$k]);
                   break;
                case 'db_multiselect':
                   $a[$k]=get_multiselect_val($v['db_select'],$row[$k]);
                   break; */
                case 'checkbox':
                   $res[]='<div class="zfilter"><label>'.$v['title'].'</label><select name="'.$k.'" class="form-control"><option> </option><option value="1">Да</option><option value="0">Нет</option></select></div>';
                break; 
                case 'logtype':
                   $res[]='<div class="zfilter"><label>'.$v['title'].'</label><select name="'.$k.'" class="form-control"><option> </option>';
                   
                   $log_sorted=$loger;
                   asort($log_sorted);
                   foreach($log_sorted as $kl=>$vl)
                        if (intval($kl)<1300){
                            $res[]='<option value="'.$kl.'">'.$vl.'</option>';
                        }
                   
                   $res[]='</select></div>';
                break; 
            }
    }
return join('',$res);
}

function getRow($table,$id){
    $res=array();
    $result = mysql_query("SELECT * FROM `".$table."` WHERE id = ".$id." LIMIT 1"); 
    if(mysql_num_rows($result)){
        $res=mysql_fetch_assoc($result);
    }
    return $res;
}



//shit code by Art
/*
function conductLastChange(){
    $query = 'SELECT id FROM d_changes WHERE idautomated_point='.$_SESSION['idap'].' AND IF((SELECT divChangeWorkplace FROM s_automated_point WHERE id='.$_SESSION['idap'].') = 1, idworkplace="'.$_SESSION['wid'].'", true ) ORDER BY id DESC LIMIT 1';
    $result = mysql_query( $query );
    var_dump(mysql_num_rows( $result ));
    if ( mysql_num_rows( $result ) > 0 ){
        $row = mysql_fetch_array( $result );
        $query = 'SELECT id FROM d_order WHERE changeid="'.$row['id'].'" AND idautomated_point="'.$_SESSION['idap'].'" AND closed="1"';
        $result = mysql_query( $query );
        if ( mysql_num_rows( $result ) > 0 ){
            $t = true;
            while ( $row = mysql_fetch_array( $result ) && $t )
                $t = conduct( 'd_order', $row['id'] );
        }
    }
    
}
*/
//shit code by Art


//Поехали логи
$loger=array();
$loger[1001]='Добавление';
$loger[1002]='Изменение';
$loger[1003]='Удаление';
$loger[1006]='Печать чека';
$loger[1008]='Назначены права для группы';
$loger[1009]='Изменены личные данные';
//авторизация
$loger[1100]='Вышел из системы';
$loger[1101]='Авторизовался в системе';
//Дизайнер меню
$loger[1200]='Дизайнер меню. Добавлен пункт меню';
$loger[1201]='Дизайнер меню. Назначены принтер для группы товаров';
$loger[1202]='Дизайнер меню. Изменен пункт меню';
//Системные логи
$loger[1300]='Авторизация. Записаны в базу куки';  
$loger[1301]='Авторизация. Присвоены пользователю куки'; 
$loger[1302]='Авторизация. Введен неверный пароль'; 
$loger[1303]='Авторизация. Имеется активная сессия при попытке авторизации';  
$loger[1304]='Авторизация. Нет доступных интерфейсов при попытке авторизации';  
$loger[1305]='Отправлено сообщение в обратную связь';        
$loger[1306]='Зарегистрировался и Авторизовался в системе';        

$loger[1]='Создание';
$loger[2]='Изменение';
$loger[3]='Просмотр';
$loger[4]='Отказ';
$loger[5]='Удаление позиции в счете';
$loger[6]='Разблокировка счета';
$loger[7]='Оплата счета';
$loger[8]='Печать счета на оплату';
$loger[9]='Печать подзаказника';
$loger[10]='Начисленно баллов';
$loger[11]='Снято баллов';
$loger[12]='Печать счета об оплате';
$loger[13]='Открытие смены';
$loger[14]='Закрытие смены';
$loger[15]='Вход в интерфейс';
$loger[16]='Выход из интерфеса';
$loger[17]='Ввод пароля';
$loger[18]='Удаление';
$loger[19]='Возврат'; 
$loger[19]='Регистрация чека'; 
$loger[20]='Регистрация чека';
$loger[21]='Смена сотрудника';
?>
