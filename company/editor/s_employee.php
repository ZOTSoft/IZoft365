<ul class="nav nav-tabs" id="my_s_employee">
  <li class="active"><a href="#s_employee1">Основное</a></li>
  <li><a href="#s_employee2">Настройки</a></li>
  <li><a href="#s_employee3">Доступы</a></li>
  <li><a href="#s_employee4">Задачи</a></li>
  <li><a href="#s_employee5">УРВ</a></li>
</ul>

<div class="tab-content tabbg">
  <div class="tab-pane active" id="s_employee1">
        <?=showfield($table,'idout',$data,$id);?>      
        <?=showfield($table,'parentid',$data,$id);?>      
        <?=showfield($table,'fio',$data,$id);?>      
        <?=showfield($table,'isuser',$data,$id);?>
        <?=showfield($table,'name',$data,$id);?>
        <?=showfield($table,'password',$data,$id);?> 
        <?=showfield($table,'position',$data,$id);?> 
        <?=showfield($table,'phone',$data,$id);?> 
        <?=showfield($table,'email',$data,$id);?> 
       
  </div>
  <div class="tab-pane" id="s_employee2">
            
        <?=showfield($table,'e_servicepercent',$data,$id);?>      
        <?=showfield($table,'e_itempercent',$data,$id);?>      
        <?=showfield($table,'e_itemservicepercent',$data,$id);?>              
  </div>
  <div class="tab-pane" id="s_employee3">
        <?=showfield($table,'front2company',$data,$id);?>  
        <?=showfield($table,'multiselect',$data,$id);?>      
        <?=showfield($table,'multiselect2',$data,$id);?>      
        <?=showfield($table,'multiselect3',$data,$id);?>      
        <?=showfield($table,'multiselect4',$data,$id);?>      
        <?=showfield($table,'userrights',$data,$id);?>      
        <?=showfield($table,'userrights2',$data,$id);?>        
  </div>
  <div class="tab-pane" id="s_employee4">
        <?=showfield($table,'task',$data,$id);?>
  </div>
  <div class="tab-pane" id="s_employee5">
        <?=showfield($table,'grafic_id',$data,$id);?>
        <?=showfield($table,'id_depart',$data,$id);?>
        <?=showfield($table,'id_location',$data,$id);?>
        <?=showfield($table,'ident',$data,$id);?>
  </div>

</div>

<script>
  $('#my_s_employee a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    });
</script>

