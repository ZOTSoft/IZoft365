<ul class="nav nav-tabs" id="my_s_autometed_point">
  <li class="active"><a href="#s_automated_point1">Основное</a></li>
  <li><a href="#s_automated_point2">Настройки</a></li>
  <li><a href="#s_automated_point3">Доступы</a></li>
  <li><a href="#s_automated_point4">Рабочие места</a></li>
</ul>

<div class="tab-content tabbg">
  <div class="tab-pane active" id="s_automated_point1">
        <?=showfield($table,'name',$data,$id);?>      
        <?=showfield($table,'menuid',$data,$id);?>      
        <?=showfield($table,'cashid',$data,$id);?>      
        <?=showfield($table,'slipid',$data,$id);?>
        <?=showfield($table,'cashclientid',$data,$id);?>
        <?=showfield($table,'warehouseid',$data,$id);?> 
        <?=showfield($table,'timezone',$data,$id);?> 
        <?=showfield($table,'nameForPrint',$data,$id);?>
        <?=showfield($table,'organisation',$data,$id);?>
        
  </div>
  <div class="tab-pane" id="s_automated_point2">
        <?=showfield($table,'servicepercent',$data,$id);?>      
             
        <?=showfield($table,'useservicepercent',$data,$id);?>      
        <?=showfield($table,'printsubord',$data,$id);?>      
        <?=showfield($table,'printsubordinfastfood',$data,$id);?>      
        <?=showfield($table,'printorder',$data,$id);?>      
        <?=showfield($table,'printorderpay',$data,$id);?>      
        <?=showfield($table,'usechoosetable',$data,$id);?>      
        <?=showfield($table,'zreportonclose',$data,$id);?>      
        <?=showfield($table,'useFR',$data,$id);?>           
            
        <?=showfield($table,'with_gifts',$data,$id);?>      
        <?=showfield($table,'uselocation',$data,$id);?>      
        <?=showfield($table,'giftpaytype',$data,$id);?>      
        <?=showfield($table,'askCount',$data,$id);?>      
        <?=showfield($table,'useChangePrice',$data,$id);?>      
        <?=showfield($table,'useURV',$data,$id);?>      
        <?=showfield($table,'rememberAboutDiscount',$data,$id);?>      
        <?=showfield($table,'idpointurv',$data,$id);?>   
        <?=showfield($table,'infostring',$data,$id);?>   
        <?=showfield($table,'doNotUseMenuDesign',$data,$id);?> 
        <?=showfield($table,'defaultFolderDuringAddClient',$data,$id);?> 
        <?=showfield($table,'divChangeWorkplace',$data,$id);?> 
        <?=showfield($table,'blockZeroSale',$data,$id);?> 
        <?=showfield($table,'searchInMenu',$data,$id);?> 
        <?=showfield($table,'materialsSumMoreServiceSum',$data,$id);?> 
        <?=showfield($table,'switchOffCompAfterClose',$data,$id);?> 
        <?=showfield($table,'alwaysUseNote',$data,$id);?> 
         
        <?=showfield($table,'jTimeStart',$data,$id);?> 
        <?=showfield($table,'jTimeEnd',$data,$id);?> 
  </div>
  <div class="tab-pane" id="s_automated_point3">
        <?=showfield($table,'pwdexit',$data,$id);?>      
        <?=showfield($table,'pwdClose',$data,$id);?>      
        <?=showfield($table,'pwdorderunlock',$data,$id);?>      
        <?=showfield($table,'pwdrefuse',$data,$id);?>      
        <?=showfield($table,'pwdclient',$data,$id);?>      
        <?=showfield($table,'pwdStopList',$data,$id);?>      
        <?=showfield($table,'pwdDivide',$data,$id);?>      
        <?=showfield($table,'pwdReturn',$data,$id);?>      
        <?=showfield($table,'pwdservicepercent',$data,$id);?>
        <?=showfield($table,'pwdChangePrice',$data,$id);?>      
        <?=showfield($table,'pwdReportAktReal',$data,$id);?>      
        <?=showfield($table,'pwdReportPoSchetam',$data,$id);?>      
        <?=showfield($table,'pwdReportItogoviyReport',$data,$id);?>      
        <?=showfield($table,'pwdReportZakazOtkaz',$data,$id);?>      
        <?=showfield($table,'pwdReportOtkaz',$data,$id);?>      
        <?=showfield($table,'pwdReportX',$data,$id);?>      
        <?=showfield($table,'pwdAddClientFromFront',$data,$id);?>        
        <?=showfield($table,'pwdCombo',$data,$id);?>        
        <?=showfield($table,'pwdDiscount',$data,$id);?>        
        <?=showfield($table,'pwdDeleteFromOrder',$data,$id);?>        
  </div>
  <div class="tab-pane" id="s_automated_point4">
        <?=showfield($table,'t_workplace',$data,$id);?>
  </div>

</div>

<script>
  $('#my_s_autometed_point a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    });
</script>

