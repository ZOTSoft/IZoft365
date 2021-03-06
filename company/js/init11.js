jQuery.fn.exists = function() {
   return $(this).length;
}

var ZOTTIG = (function() {
    "use strict";

    var elem,
        hideHandler,
        that = {};

    that.init = function(options) {
        elem = $(options.selector);
    };

    that.show = function(text) {
        clearTimeout(hideHandler);

        elem.find("span").html(text);
        elem.delay(200).fadeIn().delay(4000).fadeOut();
    };

    return that;
}());
var ZOTTIGWarning    = (function() {
    "use strict";

    var elem,
        hideHandler,
        that = {};

    that.init = function(options) {
        elem = $(options.selector);
    };

    that.show = function(text) {
        clearTimeout(hideHandler);

        elem.find("span").html(text);
        elem.delay(200).fadeIn().delay(4000).fadeOut();
    };

    return that;
}());


//добавление нового элемента
function savez2(table){
    

    //if (group==1) gr='gr'; else gr='';
    
    tabind='';
    if ($('#form_add'+'-'+table+' input[name=iddoc]').val()!=undefined){      
        tabind='1';
    }
    if ($('#form_add'+'-'+table+' input[name=apid]').val()!=undefined){      
        tabind='1';
    }
    
    canSave = true;
    a = $( '#form_add-' + table + ' input[required]' );
    for ( var i = 0; i < a.length; i++ ){
        if ( $( a[i] ).val() == '' ) canSave = false;
        else if ( $( a[i] ).attr( 'name' ) == 'quantity' && parseFloat( $( a[i] ).val() ) == 0 ) canSave = false;
    }
    if ( canSave ){
    
    $('#form_add'+'-'+table).ajaxSubmit(function(data){  
            
            parentid=0;
            //получаем parentid
            //console.log(data);
            //console.log(data);
            data=$.parseJSON(data);
            
            if (data['rescode']==1){
                alert(data['resmsg']);
                return;
            }
            //console.log('#table-'+table+tabind);

            
            parentid=$('#table-'+table+tabind).myTreeView('getSelected',{type:'tree'});
            
        if (parentid>0)
            $('#table-'+table).myTreeView('append',{parent: parentid,data: data});
        else
            $('#table-'+table).myTreeView('append',{data: data});
            
        isgroup=$('#form'+'-'+table+' input[name=isgroup]').val();
        $('#form_add'+'-'+table).clearForm();
        $('#form_add'+'-'+table+' input[name=isgroup]').val(isgroup);
        $('#dialog_add'+'-'+table).modal('hide');    
        $('#dialog_addcopy'+'-'+table).modal('hide');    
        $('#dialog_add'+'-'+table).data('modal', null);
        });
    }else{
        bootbox.alert( 'Заполните отмеченные поля формы!' );
     }
    

}


function datagrid_save(table){
    $('#form'+'-'+table).ajaxSubmit(function(data){  
        
            data=$.parseJSON(data);
            $('#table-'+table).treegrid('append',{data: [data]});
            $('#dialog'+table).dialog('close');    
        } 
    );
}
//сохранение для эдита


function savez4(table,id){

    tabind='';

    
    
    
    $('#form_edit-'+table+' .checkselect:not(:checked)').each(function(){
        $(this).attr("checked", "checked");
        $(this).val('0');
        
    }); 
    
    $('#form_edit-'+table+' .chbchecktozero:not(:checked)').each(function(){
        $(this).attr("checked", "checked");
        $(this).val('0');
        
    });
    
    canSave = true;
    a = $( '#form_edit-' + table + ' input[required]' );
    for ( var i = 0; i < a.length; i++ ){
        if ( $( a[i] ).val() == '' ) canSave = false;
        else if ( $( a[i] ).attr( 'name' ) == 'quantity' && parseFloat( $( a[i] ).val() ) == 0 ) canSave = false;
    }
    if ( canSave ){
    $('#form_edit-'+table).ajaxSubmit(function(data){  
            //console.log(data);
            data=$.parseJSON(data);
            
            if (data['rescode']==1){
            alert(data['resmsg']);
            return;
            }
            

            
            parentid=$('#table-'+table).myTreeView('getSelected',{type:'tree'});

            $('#table-'+table).myTreeView('update',{
                id: id,
                data: data
            });
 
        $('#form_edit-'+table).clearForm();
        
        $('#dialog_edit-'+table).modal('hide');    
        $('#dialog_edit-'+table).data('modal', null);
   });
 
 }else{
    bootbox.alert( 'Заполните отмеченные поля формы!' );
 }
    

}

//вывод окна изменения
function editz2(table,iddoc){
    tabind='';
    if (iddoc!=null){      
        tabind='1';
    }
    if ($('#table-'+table+tabind).treegrid('getSelected')!=null){
        id=$('#table-'+table+tabind).treegrid('getSelected').id;
        if($("#dialog_edit-"+table).exists()) {
             $('#dialog_edit-'+table).dialog('close');
             $('#dialog_edit-'+table).html('');
             $('#dialog_edit-'+table).remove();
        }
        $("#dialogs").append('<div id="dialog_edit-'+table+'"></div>');   
        $('#dialog_edit-'+table).dialog({  
            title: 'Редактирование',  
            width: 900,  
            hcenter:true,
            top:50, 
            closed: false,
            href: 'ajax.php?do=edit_el&table='+table+'&id='+id
        });
        $('.easyui-linkbutton').linkbutton();
    }else{
        alert('Выберите элемент')
    }
}

function zedit(table,iddoc){
    tabind='';
    if (iddoc!=null){      
        tabind='1';
    }
    if ($('#table-'+table+tabind).myTreeView('getSelected')!=null){
        id=$('#table-'+table+tabind).myTreeView('getSelected').id;
        if($("#dialog_edit-"+table).exists()) {
             $('#dialog_edit-'+table).modal('hide');
             $('#dialog_edit-'+table).data('modal', null);
             $('#dialog_edit-'+table).html('');
             $('#dialog_edit-'+table).remove();
        }
        
        
        $.ajax({
          url: 'ajax.php?do=edit_el&table='+table+'&id='+id
        }).success(function(form) {
            $("#dialogs").append(form);   
            $('#dialog_edit-'+table).modal('show');
            $('#dialog_edit-'+table).modal('show');
            $('#dialog_edit-'+table).on('hidden.bs.modal', function () {
                $(this).data('bs.modal', null);
                $(this).data('modal', null);
                $(this).html('');
                $(this).remove();
            });
            $('.tytip').hover(function () {$(this).tooltip('toggle')}); 
            
        });
    }else{
        alert('Выберите элемент')
    }
}

function printer(table){
    if($("#dialog_printer").exists()) {
         $("#dialog_printer").dialog('close');
         $("#dialog_printer").html('');
         $("#dialog_printer").remove();
    }
    $("#dialogs").append('<div id="dialog_printer"></div>');   
    $("#dialog_printer").dialog({  
        title: 'Печать',  
        width: 160,  
        closed: false,
        href: 'ajax.php?do=get_printdialog&table='+table
    });
}

//Вывод окна удаления
function deletez(table){
    if ($('#table-'+table).treegrid('getSelected')!=null){
        id=$('#table-'+table).treegrid('getSelected').id;
        //console.log(id);
        $.post("ajax.php?do=getcounts&table="+table, { id: id}).success(function(dataz) {
            if (dataz==0){
                $('#del-name').html($('#table-'+table).treegrid('getSelected').name);
                $('#del_table').val(table);
                $('#dlg-del').dialog('open');  
            }else{
                alert('Папка не пуста');
            }
        }); 
    
        
    }else{
        alert('Выберите элемент');
    }
}

function zdelete(table){
    if ($('#table-'+table).myTreeView('getSelected')!=null){
        id=$('#table-'+table).myTreeView('getSelected').id;
        //console.log(id);
        $.post("ajax.php?do=getcounts&table="+table, { id: id}).success(function(dataz) {
            if (dataz==0){
                $('#del-name').html($('#table-'+table).myTreeView('getSelected').name);
                $('#del_table').val(table);
                $('#dlg-del').modal('show');  
            }else{
                alert('Папка не пуста');
            }
        }); 
    
        
    }else{
        alert('Выберите элемент');
    }
}

function deletez1(table){
    if ($('#table-'+table+'1').treegrid('getSelected')!=null){
        id=$('#table-'+table+'1').treegrid('getSelected').id;
        $.post("ajax.php?do=getcounts&table="+table, { id: id}).success(function(dataz) {
            if (dataz==0){
                $.messager.confirm('Подтверждение','Вы действительно хотите удалить запись?',function(r){  
                    if (r){  
                        id=$('#table-'+table+'1').treegrid('getSelected').id;
                        $.post("ajax.php?do=delete", { id: id,table:table}).success(function(dataz) {
                            if (dataz=='ok'){
                                $('#table-'+table+'1').treegrid('remove',id);
                            }else{
                                alert(dataz);
                            }
                        }); 
                    }  
                });
            }else{
                alert('Папка не пуста');
            }
        }); 
    
        
    }else{
        alert('Выберите элемент');
    }
}
//обработка нажатия на кнопку ОК в окне удаления
function deletez_ok(){
    table=$('#del_table').val();
    grid=$('#del_type').val();
    
    if (grid==1) {
        id=$('#table-'+table).myTreeView('getSelected').id; 
        $.post("ajax.php?do=delete", { id: id,table:table}).success(function(dataz) {
            //console.log(dataz);
            if (dataz=='ok'){
                $('#table-'+table).myTreeView('remove',id);
                $('#dlg-del').modal('hide');
                $('#dlg-del').data('modal', null);
            }else{
                alert(dataz);
                $('#dlg-del').modal('hide'); 
                $('#dlg-del').data('modal', null);
            }
        });
    }else {
        id=$('#table-'+table).myTreeView('getSelected').id;
        $.post("ajax.php?do=delete", { id: id,table:table}).success(function(dataz) {
            if (dataz=='ok'){
                $('#table-'+table).myTreeView('remove',id);
                $('#dlg-del').modal('hide');
                $('#dlg-del').data('modal', null);
            }else{
                alert(dataz);
                $('#dlg-del').modal('hide');
                $('#dlg-del').data('modal', null);
            }
        });
    }

}

function deletez_ok2(){
    
    id=$('#table-t_menu_items').myTreeView('getSelected').id;
    
    $.post("ajax.php?do=delete2", { id: id}).success(function(dataz) {
        //console.log(dataz);
        $('#table-t_menu_items').myTreeView('remove',id);
        $('#dlg-del2').modal('hide');
    });
}


function deletez_menu(table){
    if ($('#table-'+table).myTreeView('getSelected')!=null){
        id=$('#table-'+table).myTreeView('getSelected').id;
        $('#del-name2').html($('#table-'+table).myTreeView('getSelected').name);
        $('#del_table2').val(table);
        $('#dlg-del2').modal('show');  
    }else{
        alert('Выберите элемент');
    }
}

//вывод окна добавление элемента
function create_el2(table,iddoc){
    parentid=0;
    iddocstr='';
    tabind='';
    if (iddoc!=null){
        iddocstr='&iddoc='+iddoc;       
        tabind='1';
    }
    //console.log(iddoc+'==');
    //получаем parentid
    
    parentid=$('#table-'+table+tabind).myTreeView('getSelected',{type:'tree'});
    
    //console.log(parentid);
    //console.log($('#table-'+table).treegrid('getSelected'));
    if($("#dialog-"+table).exists()) {
             $('#dialog-'+table).dialog('close');
             $('#dialog-'+table).html('');
             $('#dialog-'+table).remove();
             
    }
    
   
        $("#dialogs").append('<div id="dialog-'+table+'"></div>');   
        
        $('#dialog-'+table).dialog({  
            title: 'Создание',  
            width: 900,  
            hcenter:true,
            top:50,  
            closed: false,
            href: 'ajax.php?do=create_el&table='+table+'&parentid='+parentid+iddocstr  
        });
        $('.easyui-linkbutton').linkbutton();
        

}

//вывод окна добавление элемента
function zcreate_el(table,idfield,iddoc){
    parentid=0;
    iddocstr='';
    tabind='';
    if (iddoc!=null){
        iddocstr='&'+idfield+'='+iddoc+"&idfield="+idfield;       
        tabind='1';
    }
    parentid=$('#table-'+table+tabind).myTreeView('getSelected',{type:'tree'});
    //получаем parentid
    
    if($("#dialog_add-"+table).exists()) {
             $('#dialog_add-'+table).modal('hide');
             $('#dialog_add-'+table).html('');
             $('#dialog_add-'+table).remove();
             
    }
    
    $.ajax({
        url: 'ajax.php?do=create_el&table='+table+'&parentid='+parentid+iddocstr  
    }).success(function(form) {
        //console.log(form);
            $("#dialogs").append(form);   
            $('#dialog_add-'+table).modal('show');
            $('.tytip').hover(function () {$(this).tooltip('toggle')}); 
            
            $('#dialog_add-'+table).on('hidden.bs.modal', function () {
                $(this).data('bs.modal', null);
                $(this).data('modal', null);
                $(this).html('');
                $(this).remove();
            });
            
    });
        

        

}

 
function zcreate_elcopy(table,iddoc){
    parentid=0;
    iddocstr='';
    tabind='';
    if (iddoc!=null){
        iddocstr='&iddoc='+iddoc;       
        tabind='1';
    }
    id=$('#table-'+table+tabind).myTreeView('getSelected').id;
    //получаем parentid
    
    if($("#dialog_addcopy-"+table).exists()){
             $('#dialog_addcopy-'+table).modal('hide');
             $('#dialog_addcopy-'+table).html('');
             $('#dialog_addcopy-'+table).remove();
             
    }
    $.ajax({
        url: 'ajax.php?do=create_elcopy&table='+table+'&id='+id+iddocstr  
    }).success(function(form) {

            $("#dialogs").append(form);   
            $('#dialog_addcopy-'+table).modal('show');
            $('.tytip').hover(function () {$(this).tooltip('toggle')}); 
            
            $('#dialog_addcopy-'+table).on('hidden.bs.modal', function () {
                $(this).data('bs.modal', null);
                $(this).data('modal', null);
                $(this).html('');
                $(this).remove();
            });
            
    });
        

        

}

function show_form(table){

    
   
        $("#dialogs").append('<div id="dialog-'+table+'"></div>');   
        $('#dialog-'+table).dialog({  
            title: 'Создание',  
            width: 900,  
            hcenter:true,
            top:50,  
            closed: false,
            href: 'ajax.php?do=show_form&table='+table+'&edit=1&id=1'  
        });
        $('.easyui-linkbutton').linkbutton();
        

}


function create_order_el(table){
    parentid=0;
    iddocstr='';
    if (iddoc!=null){
        iddocstr='&iddoc='+iddoc;       
    }
    
    //получаем parentid
    parentid=$('#table-'+table).myTreeView('getSelected',{type:'tree'});

    //console.log($('#table-'+table).treegrid('getSelected'));
    if($("#dialog-"+table).exists()) {
             $('#dialog-'+table).dialog('close');
             $('#dialog-'+table).html('');
             $('#dialog-'+table).remove();
    }
    
    
        $("#dialogs").append('<div id="dialog-'+table+'"></div>');   
        $('#dialog-'+table).dialog({  
            title: 'Создание',  
            width: 500,  
            hcenter:true,
            top:50,   
            closed: false,
            href: 'ajax.php?do=create_el&table='+table+'&parentid='+parentid+iddocstr  
        });
        $('.easyui-linkbutton').linkbutton();
        

}




//вывод окна добавления группы
function create_group2(table){
    parentid=0;
    //получаем parentid
    parentid=$('#table-'+table).myTreeView('getSelected',{type:'tree'});
    
    if($("#dialoggr-"+table).exists()) {
             $('#dialoggr-'+table).dialog('close');
             $('#dialoggr-'+table).html('');
             $('#dialoggr-'+table).remove();
    }
    
    
        $("#dialogs").append('<div id="dialoggr-'+table+'"></div>');   
        $('#dialoggr-'+table).dialog({  
            title: 'Создание',  
            width: 500,    
            hcenter:true,
            top:50, 
            closed: false,
            href: 'ajax.php?do=create_gr&table='+table+'&parentid='+parentid  
        });
        $('.easyui-linkbutton').linkbutton();
        

    
}

function zcreate_group(table){
    parentid=0;
    //получаем parentid
    parentid=$('#table-'+table).myTreeView('getSelected',{type:'tree'});
    
    
    
    if($("#dialog_add-"+table).exists()) {
             $('#dialog_add-'+table).modal('hide');
             $('#dialog_add-'+table).data('modal', null);
             $('#dialog_add-'+table).html('');
             $('#dialog_add-'+table).remove();
    }
    
    $.ajax({
        url: 'ajax.php?do=create_gr&table='+table+'&parentid='+parentid  
    }).success(function(form) {
            $("#dialogs").append(form);   
            $('#dialog_add-'+table).modal('show');
    });
}
 

function set_rights(){
    $('#loading').show();
     $('#form_z_rights').ajaxSubmit(function(data){  
                //$('#grid_z_rights').html(data);
                /*$("#groupid").change(function() {
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
                */
                $('#loading').hide();
                ZOTTIG.show("Успешно сохранено");
                return false;
            }
            
        );
        return false;
 }
 
 
function load_rights(){
    $('#loading').show();
       id=$('#groupid option:selected').val();
       $.ajax({ 
          type: "POST",
          url: "ajax.php?do=get_z_rights",
          data: {groupid: id},
          dataType:"html"
    }).success(function(data) {
        $('#grid_z_rights').html(data);
        $('#loading').hide();
    });
   }
 

//saipal
function perenoska_delai(){
    id=$("#selc :selected").val();
    if (id){
        
        parentid=$('#table-t_menu_items1').myTreeView('getSelected',{type:'tree'});
        console.log(parentid);
        //$('#table-t_menu_items1').myTreeView('getSelected');
        
    bootbox.confirm("Вы действительно хотите перенести всё меню?", function(r) {
    if (r){ 
         //console.log(id);
         $.ajax({ 
          type: "POST",
          url: "ajax.php?do=perenoska&menuid="+id,
          dataType: 'json'
        }).success(function(dataz) {       

            $('#table-t_menu_items').myTreeView('reload');
            
        });    
    }
    }); 
    }else{
        alert('Выберите меню!');
    }
}

function move(){
     menuid=$("#selc :selected").val();
     if (menuid){
     
      if ($('#table-t_menu_items1').myTreeView('getSelected')!=null) {
            idfrom=$('#table-t_menu_items1').myTreeView('getSelected').id;
            
            if ($('#table-t_menu_items').myTreeView('getSelected',{type:'tree'}))
                idto=$('#table-t_menu_items').myTreeView('getSelected',{type:'tree'});
            else 
                idto=0;
            //console.log(idto+'-'+idfrom+'-'+menuid);
        
         $.ajax({ 
          type: "POST",
          url: "ajax.php?do=move",
          data: {idfrom: idfrom,idto:idto,menuid:menuid},
          dataType: 'json'
        }).success(function(dataz) {       
            //console.log(dataz);
            
            $('#table-t_menu_items').myTreeView('reload');
            
        });
        
        //console.log('123123');     
      }else{
          alert('Выберите откуда ');
      }
    }else{
        alert('Выберите меню!');        
    }
}


function cutfromgrid(){
        
    }
    

function setprinter(tableid){
      $.ajax({ 
              type: "GET",
              url: "ajax.php?do=get_window_setprinter",
              dataType:"html"
            }).success(function(dataz) {
                
            if($("#window_setprinter").exists()) {
                    $("#window_setprinter").modal('hide');
                    $("#window_setprinter").html('');
                    $("#window_setprinter").remove();
                }
                
                
                
                     form='<div class="modal fade" id="window_setprinter"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
        form+='<div class="modal-dialog" style="width:900px" >';
            form+='<div class="modal-content">';
                form+='<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">Выберите принтер</h4></div>';
                form+='<div class="modal-body">'+dataz;
                form+='</div>';
                form+='<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button><button type="button" class="btn btn-primary" onclick="setsubdivision(\''+tableid+'\');">Выбрать</button></div>';
            form+='</div>';
        form+='</div>';
     form+='</div>';
     
     
     
        $("#dialogs").append(form);
        
        
      $('#window_setprinter').modal('show'); 
                

                
                
            });
}

function setsubdivision(tableid){
    pr_id=$("#subdivisionid :selected").val();
            //menuid=$("#selc :selected").val();
    if ($('#table-'+tableid).myTreeView('getSelected')!=null) {
            catid=$('#table-'+tableid).myTreeView('getSelected').id;
            

            $.ajax({ 
              type: "GET",
              url: "ajax.php?do=setprinter&pr_id="+pr_id+"&cat_id="+catid+'&table='+tableid,
              dataType: 'html'
            }).success(function(data) {       
                //console.log(data);
     
                    $('#table-'+tableid).myTreeView('reload');
                     $("#window_setprinter").modal('hide');
                     
                
            });     
    }
}

function lefttd(cl){
    $('.menu li').removeClass('selected');
    $('.'+cl).addClass('selected');
    $('.s_links').hide();
    $('#'+cl).show();
}

function show_dbselect_window(table,field){
    
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do=gettableazorchik",
      data: {table: table},
      dataType: 'json'
    }).success(function(dataz) {
    
        
     cont=(dataz.rights.view==1?'<a href="javascript:void(0)" class="btn btn-default" onclick="zview_el(\''+table+'\')"><i class="icon-search"></i> Просмотр</a> ':'');
     cont+=(dataz.rights.add==1?'<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Добавить <span class="caret"></span></button><ul class="dropdown-menu" role="menu"><li><a href="#" onclick="datagrid_add(\''+table+'\')">Элемент</a></li>'+(dataz.create_group==true?'<li><a href="#" onclick="datagrid_add_group(\''+table+'\')">Группу</a></li>':'')+'</ul></div>':'');
     cont+=(dataz.rights.edit==1?'<a href="javascript:void(0)" class="btn btn-default" onclick="datagrid_edit(\''+table+'\')"><i class="icon-edit"></i> Изменить</a> ':'');
     cont+=(dataz.rights.deletez==1?'<a href="javascript:void(0)" class="btn btn-default"  onclick="datagrid_delete(\''+table+'\')"><i class="icon-trash"></i> Удалить</a> ':'');
     cont+=(dataz.rights.print==1?'<a href="#" onclick="window.open(\'ajax.php?do=getfile&type=html&table='+table+'\',\'\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no\');" class="btn btn-default" ><i class="icon-print"></i> Печать</a> ':'');
     cont+='<a href="#" onclick="$(\'#modal-'+table+'\').toggle()" class="btn btn-default dropdown-toggle" data-toggle="button"><span class="glyphicon glyphicon-filter"></span>Фильтр</a>';
      cont+='<div id="modal-'+table+'" style="display:none" class="well well-sm">';
        cont+='<form id="form-'+table+'">'+dataz.filter+'</form>';
        cont+='<div class="clear"></div>';
        cont+='<div class="zfilterok"><button type="button" class="btn btn-success" onclick="setfilter(\''+table+'\',1)">Применить</button><button type="button" class="btn btn-link" onclick="setfilter(\''+table+'\',2)">Очистить</button></div>';
        cont+='<div class="clear"></div>';
      cont+='</div>';
      cont+='<div id="table-'+dataz.name+'"></div>';
      //grid=$('<div id="wrjiu9u933"></div>');
      //cont+='<script>   $(\'#table-'+dataz.name+'\').myTreeView({url:\'ajax.php?do=newfuckingget&table='+dataz.name+'\', headers : ['+dataz.fields+']}); </script>';
     // if (!$('#temp').exists){
     //messagan='<div style="display:none">'+cont+'</div>';
        //messagan=$('<div >'+cont+'</div>').appendTo('body');
        //messagan.append(grid);

     // }
     
      /*  cont='<div id="toolbar-'+dataz.name+'">'+(dataz.rights.view==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="zview_el(\''+table+'\')">Просмотр</a>':'');
     cont+=(dataz.rights.add==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="zcreate_el(\''+table+'\')">Добавить элемент</a>'+(dataz.create_group==true?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="zcreate_group(\''+table+'\')">Добавить группу</a>':''):'')+(dataz.rights.edit==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="zedit(\''+table+'\')">Изменить</a>':'')+(dataz.rights.delete==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="zdelete(\''+table+'\')">Удалить</a>':'')+(dataz.rights.print==1?'<a href="#" onclick="window.open(\'ajax.php?do=getfile&type=html&table='+table+'\',\'\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no\');" class="easyui-linkbutton" iconCls="icon-print" plain="true">Печать</a>':'')+'</div><div id="table-'+dataz.name+'"></div>';*/
     

    /* cont2=messagan.html();
     //messagan.remove();
     bootbox.dialog({
                  message: cont2,
                  title: dataz.title,
                  className: 'invss',
                  buttons: {
                    pay: {
                      label: "Закрыть",
                      className: "btn-success",
                      callback: function() {
                          messagan.remove();
                        true;
                      }
                    },
                    print: {
                      label: "Выбрать",
                      className: "btn-primary",
                      callback: function() {
                        clickgrid(grid,field);
                        messagan.remove();
                      }
                    }
                  }
                });
                */
   
     form='<div class="modal fade" id="dialoggr-'+table+'"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
        form+='<div class="modal-dialog" style="width:900px" >';
            form+='<div class="modal-content">';
                form+='<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">'+dataz.title+'</h4></div>';
                form+='<div class="modal-body">'+cont;
                form+='</div>';
                form+='<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button><button type="button" class="btn btn-primary" onclick="clickgrid(\''+dataz.name+'\',\''+field+'\');">Выбрать</button></div>';
            form+='</div>';
        form+='</div>';
     form+='</div>';
     
     
     
        $("body").append(form);
       
       
        
        
      $('#dialoggr-'+table).modal('show'); 
      //$('#dialoggr-'+table).css('z-index',99999);
      
      $('#table-'+dataz.name).myTreeView({
                        url:'ajax.php?do=newfuckingget&table='+dataz.name, 
                        headers : dataz.fields,
                        pagination:true,
                        pagecount:[50,100,200]
                    });
      
      
       $('#dialoggr-'+table).on('hidden.bs.modal', function () {
                $(this).data('bs.modal', null);
                $(this).data('modal', null);
                $(this).html('');
                $(this).remove();
            });
        

 
        
        
/*,
                dblclick : function (){
                    return  clickgrid(dataz.name,field);
                    
                    
                }*/
      

    });
    
        

    
}

function donaher(dataz){
   $('#table-'+dataz.name).myTreeView({
                        url:'ajax.php?do=newfuckingget&table='+dataz.name, 
                        headers : dataz.fields
                    }); 
}


function clickgrid(table,field){
        if (table=='s_combo_items') 
            $('#'+field).val($('#table-'+table).myTreeView('getSelected').itemid); 
        else
            $('#'+field).val($('#table-'+table).myTreeView('getSelected').name);
        $('#'+field).next().val($('#table-'+table).myTreeView('getSelected').id).change();
        //console.log('kkk');
        $('#dialoggr-'+table).modal('hide');
        $('#dialoggr-'+table).data('modal', null);
}

function show_dbselect_window1111111111111111(table,field){
    
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do=gettableazorchik",
      data: {table: table},
      dataType: 'json'
    }).success(function(dataz) {
    
        

        
        if($("#dialoggr-"+table).exists()) {
             $('#dialoggr-'+table).dialog('close');
             $('#dialoggr-'+table).html('');
             $('#dialoggr-'+table).remove();
    }
            
        
  //   if(!$("#window-"+dataz.name).exists()) {
     cont='<div class="bggrey"><h4>'+dataz.title+'</h4></div><div class="toolbar" id="window-'+dataz.name+'"><div id="toolbar-'+dataz.name+'">'+(dataz.rights.view==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="zview_el(\''+table+'\')">Просмотр</a>':'');
     cont+=(dataz.rights.add==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="zcreate_el(\''+table+'\')">Добавить элемент</a>'+(dataz.create_group==true?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="zcreate_group(\''+table+'\')">Добавить группу</a>':''):'')+(dataz.rights.edit==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="zedit(\''+table+'\')">Изменить</a>':'')+(dataz.rights.deletez==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="zdelete(\''+table+'\')">Удалить</a>':'')+(dataz.rights.print==1?'<a href="#" onclick="window.open(\'ajax.php?do=getfile&type=html&table='+table+'\',\'\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no\');" class="easyui-linkbutton" iconCls="icon-print" plain="true">Печать</a>':'')+'</div></div><div id="table-'+dataz.name+'"></div></div>';

        $("#dialogs").append('<div id="dialoggr-'+table+'"></div>');   
        $('#dialoggr-'+table).dialog({  
            title: 'Выбор элемента',  
            width: 900,    
            hcenter:true,
            top:50, 
            closed: false,
            content:cont,  
        }); 
        
        
        $('.easyui-linkbutton').linkbutton();
 
        $('#table-'+dataz.name).myTreeView({
                url:'ajax.php?do=newfuckingget&table='+dataz.name, 
                headers : dataz.fields,
                dblclick : function (){
                    $('#'+field).val($('#table-'+dataz.name).myTreeView('getSelected').name);
                    $('#'+field).next().val($('#table-'+dataz.name).myTreeView('getSelected').id);
                     $('#dialoggr-'+table).dialog('close');
                    
                    
                }
            });
        

      

    });
    
        

    
}


function show_window(table){
    
    width=$('.righttd').width()-10;
    height=$('.righttd').height()-85;
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do=gettable",
      data: {table: table},
      dataType: 'json'
    }).success(function(dataz) {
        

        
        $('#window-'+dataz.name).remove();
        
        /*if($('#tabs').tabs('exists', dataz.title)){
            $('#tabs').tabs('close',dataz.title);
        }*/
            
        
        
     //   if(!$("#window-"+dataz.name).exists()) {
     cont='<div class="bggrey"><h4>'+dataz.title+'</h4></div><div  class="toolbar" id="window-'+dataz.name+'"><div id="toolbar-'+dataz.name+'">'+(dataz.rights.view==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="view_el(\''+table+'\')">Просмотр</a>':'');
     cont+=(dataz.rights.add==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="create_el2(\''+table+'\')">Добавить элемент</a>'+(dataz.create_group==true?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="create_group2(\''+table+'\')">Добавить группу</a>':''):'');
     cont+=(dataz.rights.edit==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editz2(\''+table+'\')">Изменить</a>':'');
     cont+=(dataz.rights.deletez==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="deletez(\''+table+'\')">Удалить</a>':'');
     cont+=(dataz.rights.print==1?'<a href="#" onclick="window.open(\'ajax.php?do=getfile&type=html&table='+table+'\',\'\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no\');" class="easyui-linkbutton" iconCls="icon-print" plain="true">Печать</a>':'')+'</div><table><tr><td  style="border-right:1px solid #000"><table id="table-'+dataz.name+'2"></table></td><td><table id="table-'+dataz.name+'"></table></td></tr></table></div><div id="menu-'+dataz.name+'" class="easyui-menu" style="width:120px"></div></div>';
        
        

        
        addTab(dataz.title,table,cont);

        $('.easyui-linkbutton').linkbutton();  
        $('#table-'+dataz.name+'2').treegrid({  
            url:'ajax.php?do=getfolder&table='+dataz.name, 
            rownumbers: true,
            idField: 'id',
            treeField: 'name',
            width:220,
            height:(height-37),
            
            columns: [[{title:'Наименование',field:'name'}]],
            
            onClickRow: function(row){//onClickRow
            //console.log('ajax.php?do=get&table='+dataz.name+'&id='+row.id);
            
            
            $('#table-'+dataz.name).treegrid({
               url:'ajax.php?do=get&table='+dataz.name+'&id='+row.id
            });
            
                $('#table-'+dataz.name).treegrid("reload");
                
            } 
        });   

        $('#table-'+dataz.name).treegrid({  
            url:'ajax.php?do=get&table='+dataz.name, 
            rownumbers: true,
            idField: 'id',
            treeField: 'name',
            width:(width-320),
            pagination:true,
            pageSize:50,
            pageList:[50,100,150],
            height:(height-37),
            columns: [dataz.fields],
             onBeforeLoad: function(row,param){  
                if (!row) { 
                    param.id = 0;   
                }  
            } 
        });
    });
    
    
        
return false;  
 
}

function setfilter(table,param){
    if (param==1){
        data=$('#form-'+table).serializeArray();
    }else{
        data=null;
        $('#form-'+table)[0].reset();
    }
    
    $('#table-'+table).myTreeView('applyFilter',data);
    $('#table-'+table).myTreeView('reload');
}

function togglefilter(elem){
    $(elem).parent().find('.filtertoggle').toggle();
}

function show_tree(table){
    $('#loading').show();
    width=$('.righttd').width()-10;
    height=$('.righttd').height()-85;
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do=gettableazorchik",
      data: {table: table},
      dataType: 'json'
    }).success(function(dataz) {
        $('#window-'+dataz.name).remove();
        
        
        removeTabIfExist('#tab_'+table);
        
        
        
     //   if(!$("#window-"+dataz.name).exists()) {
     cont='<div class="bggrey"><h4>'+dataz.title+'</h4></div><div class="toolbar"><div>'+(dataz.rights.view==1?'<a href="javascript:void(0)" class="btn btn-default" onclick="zview_el(\''+table+'\')"><i class="icon-search"></i> Просмотр</a> ':'');
     cont+=(dataz.rights.add==1?'<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Добавить <span class="caret"></span></button><ul class="dropdown-menu" role="menu"><li><a href="#" onclick="zcreate_el(\''+table+'\')">Элемент</a></li>'+(dataz.create_group==true?'<li><a href="#" onclick="zcreate_group(\''+table+'\')">Группу</a></li>':'')+'<li><a href="#" onclick="zcreate_elcopy(\''+table+'\')">Копированием</a></li></ul></div>':'')+(dataz.rights.edit==1?' <a href="javascript:void(0)" class="btn btn-default" onclick="zedit(\''+table+'\')"><i class="icon-edit"></i> Изменить</a> ':'')+(dataz.rights.deletez==1?'<a href="javascript:void(0)" class="btn btn-default"  onclick="zdelete(\''+table+'\')"><i class="icon-trash"></i> Удалить</a> ':'')+(dataz.rights.print==1?'<a href="#" onclick="window.open(\'ajax.php?do=getfile&type=html&table='+table+'\',\'\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no\');" class="btn btn-default" ><i class="icon-print"></i> Печать</a> ':'')+(((dataz.rights.edit==1)&&(table=='s_items'))?'<a href="javascript:void(0)" class="btn btn-default" iconCls="icon-print" plain="true" onclick="setprinter(\''+table+'\')">Назначить подразделение</a>':'')+'<a href="#" onclick="togglefilter(this)" class="btn btn-default dropdown-toggle" data-toggle="button"><span class="glyphicon glyphicon-filter"></span>Фильтр</a> <div style="display:none" class="well well-sm filtertoggle">';
      
      cont+='<form id="form-'+table+'">'+dataz.filter+'</form>';

        
      cont+='<div class="clear"></div>';
      
      cont+='<div class="zfilterok"><button type="button" class="btn btn-success" onclick="setfilter(\''+table+'\',1)">Применить</button><button type="button" class="btn btn-link" onclick="setfilter(\''+table+'\',2)">Очистить</button></div>';
      cont+='<div class="clear"></div>';
      
      
      cont+='</div></div></div><div id="table-'+dataz.name+'"> </div></div>';

       /* $('#tabs').tabs('add',{  
            title:dataz.title,  
            content:cont,  
            closable:true,   
        });  */
        
        addTab(dataz.title,table,cont);
        
        

        
        $('#table-'+dataz.name).myTreeView({
                url:'ajax.php?do=newfuckingget&table='+dataz.name, 
                headers : dataz.fields,
                tree: dataz.create_group,
                pagination:true,
                pagecount:[50,100,200]
            });
 
    });
    
    
     
return false;   
}

function show_journal(){
    table='z_logs'
    $('#loading').show();
    width=$('.righttd').width()-10;
    height=$('.righttd').height()-85;
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do=gettableazorchik",
      data: {table: table},
      dataType: 'json'
    }).success(function(dataz) {
        $('#window-'+dataz.name).remove();
        
        
        removeTabIfExist('#tab_'+table);
        
        
        
     //   if(!$("#window-"+dataz.name).exists()) {
     cont='<div class="bggrey"><h4>'+dataz.title+'</h4></div><div class="toolbar"><div>'+(dataz.rights.view==1?'<a href="javascript:void(0)" class="btn btn-default" onclick="zview_el(\''+table+'\')"><i class="icon-search"></i> Просмотр</a> ':'')+'<a href="#" onclick="window.open(\'ajax.php?do=getfile&type=html&table='+table+'\',\'\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no\');" class="btn btn-default" ><i class="icon-print"></i> Печать</a> <a href="#" onclick="togglefilter(this)" class="btn btn-default dropdown-toggle" data-toggle="button"><span class="glyphicon glyphicon-filter"></span>Фильтр</a> <div style="display:none" class="well well-sm filtertoggle">';
      
      cont+='<form id="form-'+table+'">'+dataz.filter+'</form>';

        
      cont+='<div class="clear"></div>';
      
      cont+='<div class="zfilterok"><button type="button" class="btn btn-success" onclick="setfilter(\''+table+'\',1)">Применить</button><button type="button" class="btn btn-link" onclick="setfilter(\''+table+'\',2)">Очистить</button></div>';
      cont+='<div class="clear"></div>';
      
      
      cont+='</div></div></div><div id="table-'+dataz.name+'"> </div></div>';

       /* $('#tabs').tabs('add',{  
            title:dataz.title,  
            content:cont,  
            closable:true,   
        });  */
        
        addTab(dataz.title,table,cont);
        
        

        
        $('#table-'+dataz.name).myTreeView({
                url:'ajax.php?do=newfuckingget&table='+dataz.name, 
                headers : dataz.fields,
                tree: dataz.create_group,
                pagination:true,
                pagecount:[50,100,200]
            });
 
    });
    
     
return false;   
}

function clickto(tb){
   $('#zottabs a[href="#'+tb+'"]').click(); 
}

function removeTabIfExist(ex){
    //console.log(ex);
    if($(ex).exists()){
        $('#zottabs a[href="'+ex+'"]').parent().remove();
        $(ex).remove();
        $('#zottabs li:last-child >a').click();
    }
}

function addTab(title,table,cont){
    $('#zottabs').append('<li><a href="#tab_'+table+'">'+title+' <i class="closetab glyphicon glyphicon-remove"></i></a></li>');
    $('#zotcontent').append('<div class="tab-pane fade" id="tab_'+table+'">'+cont+'</div>');
    $('#zottabs a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    });
    $('#zottabs li:last-child > a').click();
    $('.closetab').click(function(){
        $($(this).parent().attr('href')).remove(); 
        $(this).parent().parent().remove();
        $('#zottabs li:last-child >a').click();
    });
    $('#loading').hide();   
}

function show_window2(table){
    
    width=$('.righttd').width()-10;
    height=$('.righttd').height()-85;
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do=gettable",
      data: {table: table},
      dataType: 'json'
    }).success(function(dataz) {
        

        
        $('#window-'+dataz.name).remove();
        
        /*if($('#tabs').tabs('exists', dataz.title)){
            $('#tabs').tabs('close',dataz.title);
        }*/
        removeTabIfExist('#tab_'+table);
            
        
        
     //   if(!$("#window-"+dataz.name).exists()) {
     cont='<div class="bggrey"><h4>'+dataz.title+'</h4></div><div  class="toolbar" id="window-'+dataz.name+'"><table id="table-'+dataz.name+'"></table><div id="toolbar-'+dataz.name+'">'+(dataz.rights.view==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="view_el(\''+table+'\')">Просмотр</a>':'');
     cont+=(dataz.rights.add==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="create_el2(\''+table+'\')">Добавить элемент</a>'+(dataz.create_group==true?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="create_group2(\''+table+'\')">Добавить группу</a>':''):'')+(dataz.rights.edit==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editz2(\''+table+'\')">Изменить</a>':'')+(dataz.rights.deletez==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="deletez(\''+table+'\')">Удалить</a>':'')+(dataz.rights.print==1?'<a href="#" onclick="window.open(\'ajax.php?do=getfile&type=html&table='+table+'\',\'\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no\');" class="easyui-linkbutton" iconCls="icon-print" plain="true">Печать</a>':'')+'</div></div><div id="menu-'+dataz.name+'" class="easyui-menu" style="width:120px"></div></div>';
        
        
/*        $('#tabs').tabs('add',{  
            title:dataz.title,  
            content:cont,  
            closable:true,   
        });*/  
        
        addTab(dataz.title,table,cont);

//        $('.easyui-linkbutton').linkbutton();  
        $('#table-'+dataz.name).treegrid({  
            url:'ajax.php?do=get&table='+dataz.name, 
            rownumbers: true,
            idField: 'id',
            treeField: 'name',
            width:width,
            pagination:true,
            pageSize:50,
            pageList:[50,100,150],
            height:height,
            toolbar:'#toolbar-'+dataz.name, 
            columns: [dataz.fields],
             onBeforeLoad: function(row,param){  
                if (!row) { 
                    param.id = 0;   
                }  
            } 
        });   
    });
    
    
        
return false;   
}


function show_otchet2(otchet){
$('#loading').show();
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do="+otchet,
      dataType:"html"
    }).success(function(dataz) {
    
    switch(otchet){
        case 'gethtml_akt_real': title='Акт реализации'; break;
        case 'gethtml_poschetam': title='Отчет по счетам'; break;
        case 'gethtml_itogovy': title='Итоговый отчет'; break;
        case 'gethtml_refuse': title='Отчет по отказам'; break;
        case 'gethtml_hoursales': title='Отчет по продажам по часам'; break;
        case 'gethtml_refuse_and_orders': title='Отчет по заказам и отказам'; break;
        case 'getmain_charts': title='Графики'; break;
        case 'gethtml_posotr': title='Отчет по сотрудникам'; break;
        case 'gethtml_remains': title='Материальная ведомость'; break;
        case 'gethtml_conductor': title='Провести счета'; break;
        case 'gethtml_tipaotchet': title='Отчет по калькуляции блюд'; break;
        case 'gethtml_reconduct': title='Перепроведение документов'; break;
    }
        
        
    /*if($('#tabs').tabs('exists', title)){
        $('#tabs').tabs('close',title);
    }*/
    removeTabIfExist('#tab_'+otchet);
    
    
    cont='<div class="bggrey"><h4>'+title+'</h4></div>'+dataz;
    
     /*$('#tabs').tabs('add',{  
            title:title,  
            content:cont,  
            closable:true,   
     });*/
     
     addTab(title,otchet,cont);  
     
     if (otchet=='getmain_charts'){
          $.ajax({ 
      url: "ajax.php?do=get_chart_changes",
      dataType:"json"
    }).success(function(dataz) {
        html='График за смену <select id="chartselect1">';
        //console.log(dataz);
        $.each(dataz,function(index,value){
            html+='<option value="'+value.id+'">'+value.title+'</option>';
        });
        html+='</select>';
        $('#chart_div1t').html(html);
        $("#chartselect1").change(function() {
            sel_id=$("#chartselect1 :selected").val();
                $.ajax({ 
                  url: "ajax.php?do=get_chart1&change_id="+sel_id,
                  dataType:"json"
                }).success(function(dataz) {
                    //console.log(dataz);
                    drawChart(dataz.chart1,'chart_div1','График по количеству продаж');
                    drawChart(dataz.chart2,'chart_div2','График по суммам продаж');
                    
                });
        });
    });

    

    $.ajax({ 
      url: "ajax.php?do=get_chart1",
      dataType:"json"
    }).success(function(dataz) {
        //console.log(dataz);
        drawChart(dataz.chart1,'chart_div1','График по количеству продаж');
        drawChart(dataz.chart2,'chart_div2','График по суммам продаж');
        
    });
    
    
    //Второй график
    $.ajax({ 
      url: "ajax.php?do=get_chart2_changes",
      dataType:"json"
    }).success(function(dataz) {
        html='График за смену <select id="chartselect2">';
        //console.log(dataz);
        $.each(dataz,function(index,value){
            html+='<option value="'+value.id+'">'+value.title+'</option>';
        });
        html+='</select>';
        $('#chart_div2t').html(html);
        $("#chartselect2").change(function() {
            sel_id=$("#chartselect2 :selected").val();
                $.ajax({ 
                  url: "ajax.php?do=get_chart2&date="+sel_id,
                  dataType:"json"
                }).success(function(dataz) {
                    //console.log(dataz);
                    drawColumnChart(dataz,'chart_div2g','Почасовой график по суммам продаж');
                    
                });
        });
    });
    
    
    $.ajax({ 
      url: "ajax.php?do=get_chart2",
      dataType:"json"
    }).success(function(dataz) {
        //console.log(dataz);
        drawColumnChart(dataz,'chart_div2g','Почасовой график по суммам продаж');
        
    });
     }


        
});
}




function load_form(form){
    $.ajax({ 
      url: "ajax.php?do=loadform&form="+form,
      dataType:"json"
    }).success(function(dataz) {
        /*if($('#tabs').tabs('exists', dataz.title)){
            $('#tabs').tabs('close', dataz.title);
        }*/
        removeTabIfExist('#tab_'+form);
        
        cont='<div class="bggrey"><h4>'+dataz.title+'</h4></div>'+dataz.table;
        /*$('#tabs').tabs('add',{  
                title:dataz.title,  
                content:cont,  
                closable:true,   
        });  */
        
        addTab(dataz.title,form,cont);
    }); 
}

function backtothefront(){
    $.ajax({ 
      url: "ajax.php?do=employeeSessionDieFromFront",
      dataType:"json"
    }).success(function(dataz) {
       location.href='/front';
    }); 
}

function show_design_menu2(table){
$('#loading').show();
    removeTabIfExist('#tab_'+table);
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do=design_menu",
      data: {table: table},
      dataType:"json"
    }).success(function(dataz) {
        
    /*if($('#tabs').tabs('exists', 'Дизайнер меню')){
        $('#tabs').tabs('close','Дизайнер меню');
    }*/
    //console.log(dataz);
    list='';
    $.each( dataz.fields, function(k, v){
       list+="<option value="+k+">"+ v+"</option>";
    });
            
    cont='<div class="bggrey"><h4>Дизайнер меню</h4></div><div class="toolbar" id="window-'+table+'">&nbsp;&nbsp;'+(dataz.rights.edit==1?'<a href="javascript:void(0)" class="btn btn-default" onclick="perenoska_delai()">Перенести все меню &rarr;</a> <a href="javascript:void(0)" class="btn btn-default" onclick="move()">Перенести один элемент &rarr;</a>':'')+' <select class="btn btn-default" id="selc">'+list+'</select> </div><table width="100%"><tr><td class="dizmen"><b>Номенклатура</b> <br /><table id="table-'+table+'1"></table></td><td class="dizmen"><b>Меню</b> <br /><div id="toolbar-'+table+'">'+(dataz.rights.edit==1?'<a href="javascript:void(0)" class="btn btn-default" iconCls="icon-edit" plain="true" onclick="zedit(\''+table+'\')">Изменить</a>':'')+''+(dataz.rights.deletez==1?' <a href="javascript:void(0)" class="btn btn-default" iconCls="icon-remove" plain="true" onclick="deletez_menu(\''+table+'\')">Удалить</a>':'')+''+(dataz.rights.edit==1?' <a href="javascript:void(0)" class="btn btn-default" iconCls="icon-print" plain="true" onclick="setprinter(\''+table+'\')">Назначить подразделение</a>':'')+'</div><table id="table-'+table+'"></table></td></tr></table>'; 
           
    /*
     $('#tabs').tabs('add',{  
            title:'Дизайнер меню',  
            content:cont,  
            closable:true,   
     });  */
     addTab('Дизайнер меню',table,cont);

        
        
        sel_id=$("#selc :selected").val();

        
        $('#table-'+table+'1').myTreeView({  
            url:'ajax.php?do=newfuckingget&table=s_items', 
            headers: [{title:'Наименование',name:'name'}],
            pagination:true,
                pagecount:[50,100,200]
        });
        
        
/*        $('#table-'+table+'1').treegrid({  
            url:'ajax.php?do=get&table=s_items', 
            rownumbers: true,
            idField: 'id',
            pagination:true,
            pageSize:50,
            pageList:[50,100,150],
            treeField: 'name',
            width:400,
            height:height,
            columns: [[{title:'Наименование',field:'name'},{field:'idout',title:'Код'}]],
            onBeforeLoad: function(row,param){  
                if (!row) { 
                    param.id = 0;   
                }  
            } 
        });*/
        
        $('#table-'+table).myTreeView({  
            url:'ajax.php?do=getmenuitems&menuid='+sel_id, 
            headers: [{title:'Наименование',name:'name'},{name:'price',title:'Цена'},{name:'printer',title:'Принтер'}],
            pagination:false,
        });
        
        
        $("#selc").change(function() {
            
            sel_id=$("#selc :selected").val();
            //console.log(sel_id);
            //$('#table-'+table).html('');
            $('#table-'+table).myTreeView('changeUrl','ajax.php?do=getmenuitems&menuid='+sel_id);
            $('#table-'+table).myTreeView('reload');
            
            
            
           
            
        });
        
         
            


    });
    
    
    
    
        
    
}


//вывод окна добавление элемента
function datagrid_add(table){
    itemid='';
    if ($('#table-'+table).myTreeView('getSelected')!=null){
        parent='&parentid='+($('#table-'+table).myTreeView('getSelected').id);
    }else{
         parent='&parentid=0';
    }
    
    if (table=='s_note'){
       itemid='&idfield=itemid&itemid='+($('#table-'+table+'1').myTreeView('getSelected').id); 
    }

        
        $.ajax({ 
          url: 'ajax.php?do=zcreate&table='+table+parent+itemid,
          dataType:"html"
        }).success(function(dataz) {
            
            
               bootbox.dialog({
                  message: dataz,
                  title: 'Создание',
                  className: 'invss',
                  buttons: {
                    pay: {
                      label: "Закрыть",
                      className: "btn-success",
                      callback: function() {
                        true;
                      }
                    },
                    print: {
                      label: "Добавить",
                      className: "btn-primary",
                      callback: function() {
                        datagrid_submit(table,'add');
                      }
                    }
                  }
                });
        });
        
}

function datagrid_add_group(table){

        if ($('#table-'+table).myTreeView('getSelected')!=null){
            itemid=$('#table-'+table).myTreeView('getSelected').id;
        }else{
            itemid=0;
        }
            
        $.ajax({ 
          url: 'ajax.php?do=zcreate&table='+table+'&parentid=0&idfield=itemid&itemid='+itemid,
          dataType:"html"
        }).success(function(dataz) {
            
            
               bootbox.dialog({
                  message: dataz,
                  title: 'Создание',
                  className: 'invss',
                  buttons: {
                    pay: {
                      label: "Закрыть",
                      className: "btn-success",
                      callback: function() {
                        true;
                      }
                    },
                    print: {
                      label: "Добавить",
                      className: "btn-primary",
                      callback: function() {
                        datagrid_submit(table,'add');
                      }
                    }
                  }
                });
        });
}


function datagrid_submit(table,type){
    $('#form_'+type+'-'+table).ajaxSubmit(function(data){  
        $('#table-'+table).myTreeView('reload');
    });
}

function form_submit(el){
    $(el).parents('form').ajaxSubmit(function(data){  
        alert(data);
        $(this).clearForm();
        
    });
}



function datagrid_edit(table){

    if ($('#table-'+table).myTreeView('getSelected')!=null){
        id=$('#table-'+table).myTreeView('getSelected').id;
        $("#dialogs").append('<div id="dialog_edit-'+table+'"></div>');
           
         $.ajax({ 
          url: 'ajax.php?do=zedit&table='+table+'&id='+id,
          dataType:"html"
        }).success(function(dataz) {
            
                bootbox.dialog({
                  message: dataz,
                  title: 'Редактирование',
                  className: 'invss',
                  buttons: {
                    pay: {
                      label: "Закрыть",
                      className: "btn-success",
                      callback: function() {
                        true;
                      }},
                    print: {
                      label: "Сохранить",
                      className: "btn-primary",
                      callback: function() {
                        datagrid_submit(table,'edit');
                      }
                    }
                  }
                });
        
        });   
           
       
        
        
    }else{
        alert('Выберите элемент');
    }
}

function datagrid_delete(table){
    if ($('#table-'+table).myTreeView('getSelected')!=null){
        id=$('#table-'+table).myTreeView('getSelected').id;
        //console.log(id);
        $.post("ajax.php?do=getcounts&table="+table, { id: id}).success(function(dataz) {
            if (dataz==0){
                bootbox.confirm("Вы действительно хотите удалить <b>"+$('#table-'+table).myTreeView('getSelected').name+"</b>", function(r) {
                    if (r){ 
                        $.post("ajax.php?do=delete&table="+table, { id: id,table:table}).success(function(dataz) {
                            $('#table-'+table).myTreeView('reload');
                        }); 
                    }
                });
            }else{
                alert('Папка не пуста');
            }
        }); 
    }else{
        alert('Выберите элемент');
    }
}

function view_el(table,id){
    if ($('#table-'+table).treegrid('getSelected')!=null){
        id=$('#table-'+table).treegrid('getSelected').id;
        if($("#dialog_view-"+table).exists()) {
             $('#dialog_view-'+table).dialog('close');
             $('#dialog_view-'+table).html('');
             $('#dialog_view-'+table).remove();
        }
        $("#dialogs").append('<div id="dialog_view-'+table+'"></div>');   
        $('#dialog_view-'+table).dialog({  
            title: 'Просмотр',  
            width: 500,  
            hcenter:true,
            top:50, 
            closed: false,
            href: 'ajax.php?do=view_el&table='+table+'&id='+id
        });
        $('.easyui-linkbutton').linkbutton();
    }else{
        alert('Выберите элемент')
    }
}

function zview_el(table,id){
    
    if ($('#table-'+table).myTreeView('getSelected')!=null){
        id=$('#table-'+table).myTreeView('getSelected').id;
        if($("#dialog_view-"+table).exists()) {
             $('#dialog_view-'+table).modal('hide');
             $('#dialog_view-'+table).data('modal', null);
             $('#dialog_view-'+table).html('');
             $('#dialog_view-'+table).remove();
        }
        
        $.ajax({
              url: 'ajax.php?do=view_el&table='+table+'&id='+id
        }).success(function(form) {
            $("#dialogs").append(form);   
            $('#dialog_view-'+table).modal('show');

            
        });
    }else{
        alert('Выберите элемент')
    }
}


function show_note(table){
$('#loading').show();
    height=$('.righttd').height()-85;
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do=get_note_rights",
      data: {table: table},
      dataType:"json"
    }).success(function(rights) {
        
    /*if($('#tabs').tabs('exists', 'Примечания')){
        $('#tabs').tabs('close','Примечания');
    }*/
    removeTabIfExist('#tab_'+table);
    //console.log(rights);

            
    cont='<div class="bggrey"><h4>Примечания</h4></div><div class="toolbar" id="window-'+table+'"><table width="100%"><tr><td class="dizmen"><b>Категории</b> <br /><table id="table-'+table+'1"></table></td><td class="dizmen"><b>Примечание</b> <br /><div id="toolbar-'+table+'">'+(rights.add==1?'<a href="javascript:void(0)" class="btn btn-default" iconCls="icon-add" plain="true" onclick="datagrid_add(\''+table+'\')">Добавить</a>':'')+(rights.edit==1?'<a href="javascript:void(0)" class="btn btn-default" iconCls="icon-edit" plain="true" onclick="datagrid_edit(\''+table+'\')">Изменить</a>':'')+''+(rights.deletez==1?'<a href="javascript:void(0)" class="btn btn-default" iconCls="icon-remove" plain="true" onclick="datagrid_delete(\''+table+'\')">Удалить</a>':'')+'</div><table id="table-'+table+'"></table></td></tr></table></div>'; 
           
    
     /*$('#tabs').tabs('add',{  
            title:'Примечания',  
            content:cont,  
            closable:true,   
     });  */
     
     addTab('Примечание',table,cont);

     
     $('#table-'+table+'1').myTreeView({
                url:'ajax.php?do=newfuckingget&table=s_items&note=1',
                headers: [{title:'Наименование',name:'name',width:300},{name:'idout',title:'Код'}],
                tree: true,
                pagination:true,
                pagecount:[50,100,200],
                dblclick : function (){
                    row= $('#table-'+table+'1').myTreeView('getSelected').id;
                    $('#table-'+table).myTreeView('changeUrl', 'ajax.php?do=get_note_table&id='+row);
                    $('#table-'+table).myTreeView('reload');
                    
                }
            }); 


        
        
        
                    
     $('#table-'+table).myTreeView({
                url:'ajax.php?do=get_note_table&id=0', 
                headers: [{title:'Наименование',name:'name',width:300}],
                tree: false,
                pagination:true,
                pagecount:[50,100,200]
            });

        

    });
  
}


/*//вывод окна добавление элемента
function datagrid_add(table){
    if($("#dialog-"+table).exists()) {
             $('#dialog-'+table).dialog('close');
             $('#dialog-'+table).html('');
             $('#dialog-'+table).remove();
             
    }
        $("#dialogs").append('<div id="dialog-'+table+'"></div>');   
        $('#dialog-'+table).dialog({  
            title: 'Создание',  
            width: 500,  
            hcenter:true,
            top:50,  
            closed: false,
            href: 'ajax.php?do=create_el&table='+table+'&parentid=0'  
        });
        $('.easyui-linkbutton').linkbutton();
}*/


function show_window_z_rights2(){
                $('#loading').show();            
                    $.ajax({ 
                      type: "POST",
                      url: "ajax.php?do=get_window_z_rights",
                      dataType:"html"
                    }).success(function(dataz) {
                        
                        /*if($('#tabs').tabs('exists', 'Права групп')){
                            $('#tabs').tabs('close','Права групп');
                        }*/
                        removeTabIfExist('#tab_get_window_z_rights');
                        
                        
                        cont='<div class="bggrey"><h4>Права групп</h4></div>'+dataz;
                        /*
                        $('#tabs').tabs('add',{  
                                title:'Права групп',  
                                content:cont,  
                                closable:true,   
                         });  
                         */
                         addTab('Права групп','get_window_z_rights',cont);

                        
                        //$('.easyui-linkbutton').linkbutton(); 
                        $('#loading').hide();
                    });
         }
         function show_feedback(){
                    $.ajax({ 
                      type: "POST",
                      url: "ajax.php?do=get_window_feedback",
                      dataType:"html"
                    }).success(function(dataz) {
                        
                        /*if($('#tabs').tabs('exists', 'Обратная связь')){
                            $('#tabs').tabs('close','Обратная связь');
                        }*/
                        removeTabIfExist('#tab_feedback');
                        
                        
                        cont='<div class="bggrey"><h4>Обратная связь</h4></div>'+dataz;
                        
/*                        $('#tabs').tabs('add',{  
                                title:'Обратная связь',  
                                content:cont,  
                                closable:true,   
                         }); */ 
                         addTab('Обратная связь','feedback',cont);
                        //$('.easyui-linkbutton').linkbutton(); 
                    });
         }
         
         
         function show_balance(){
                    $.ajax({ 
                      type: "POST",
                      url: "ajax.php?do=get_window_balance",
                      dataType:"html"
                    }).success(function(dataz) {
                        
                        /*if($('#tabs').tabs('exists', 'Обратная связь')){
                            $('#tabs').tabs('close','Обратная связь');
                        }*/
                        removeTabIfExist('#tab_balance');
                        
                        
                        cont='<div class="bggrey"><h4>Баланс</h4></div>'+dataz;
                        
/*                        $('#tabs').tabs('add',{  
                                title:'Обратная связь',  
                                content:cont,  
                                closable:true,   
                         }); */ 
                         addTab('Баланс','balance',cont);
                        //$('.easyui-linkbutton').linkbutton(); 
                    });
         }
function post_feedback(){
    $('#feed_form').ajaxSubmit(function(data){  
            $('#message_feedback').val('');
            alert(data); 
        }
    );
}
function show_account_settings(){
    $('#loading').show();
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do=show_account_settings",
      dataType:"html"
    }).success(function(dataz) {
        

        removeTabIfExist('#tab_show_account_settings');
        
        
        cont='<div class="bggrey"><h4>Настройки аккаунта</h4></div>'+dataz;
        
        /*$('#tabs').tabs('add',{  
                title:'Настройки аккаунта',  
                content:cont,  
                closable:true,   
         });*/
         
         addTab('Настройки аккаунта','show_account_settings',cont);
          

        
       // $('.easyui-linkbutton').linkbutton(); 
       $('#loading').hide();    
    });
}

function account_payment(){
    $('#loading').show();
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do=account_payment",
      dataType:"html"
    }).success(function(dataz) {
        

        removeTabIfExist('#tab_account_payment');
        
        
        cont='<div class="bggrey"><h4>Оплата услуг</h4></div>'+dataz;
        
        /*$('#tabs').tabs('add',{  
                title:'Настройки аккаунта',  
                content:cont,  
                closable:true,   
         });*/
         
         addTab('Оплата услуг','show_account_payment',cont);
          

        
       // $('.easyui-linkbutton').linkbutton(); 
       $('#loading').hide();    
    });
}

function save_account_settings(){
    $('#show_account_settings').ajaxSubmit(function(data){  
            alert(data); 
        }
    );    
}




// Отчеты

function makebill(tg){
    $.ajax({ url: "ajax.php?do=create_invoice&tg="+tg,dataType:"json"}).success(function(dataz) {
        bootbox.dialog({
          message: dataz.message,
          title: 'Счет',
          className: 'invss',
          buttons: {
            pay: {
              label: "Закрыть",
              className: "btn-success",
              callback: function() {
                true;
              }
            },
            print: {
              label: "Печать",
              className: "btn-primary",
              callback: function() {
                print_invoice(dataz.id);
              }
            }
          }
        });
        
        $(dataz.zrows).insertAfter('.invoices_here tr:first-child');
        
    });
}


function topup(){
    data='<p>Вы можете пополнить свой баланс, оплатив счёт, выписанный нашим сервисом.</p>';
    data+='<p>Оплатить его можно будет с помощью:';
    data+='     </p><ul>';
        data+='<li>Кредитной или дебетной карты</li>';
        data+='<li>Наличных в <a href="/contacts" target="_blank">нашем офисе</a></li>';
        data+='<li>Банковского перевода</li>';
        data+='<li>На счет Зоту</li>';
           data+='</ul>';
        data+='<p></p>';
        data+='Сумма: <input type="text" name="amount" value="5000" size="5" id="paycount"> <small>ТЕНГЕ</small>';
        data+='<p><small>Минимальная сумма пополнения: 2000 тенге. Максимальная: 200 000 тенге.</small></p>';
        data+='';
    bootbox.dialog({
      message: data,
      title: 'Пополнение баланса',
      buttons: {
        pay: {
          label: "Выписать счет",
          className: "btn-success",
          callback: function() {
            makebill($("#paycount").val());
          }
        }
      }
    });
}


function show_invoice(id){
    $.ajax({ url: "ajax.php?do=show_invoice&id="+id,dataType:"json"}).success(function(dataz) {
        bootbox.dialog({
          message: dataz.message,
          title: 'Счет',
          className: 'invss',
          buttons: {
            pay: {
              label: "Закрыть",
              className: "btn-success",
              callback: function() {
                true;
              }
            },
            print: {
              label: "Печать",
              className: "btn-primary",
              callback: function() {
                print_invoice(id);
              }
            }
          }
        });
        
        
    });
}

function print_invoice(id){
    window.open('ajax.php?do=show_invoice&id='+id+'&type=print','_blank');
}

function buy(type,id,count,zid){
    $.ajax({ url: "ajax.php?do=pay&id="+id+"&type="+type+"&amount="+count,dataType:"json"}).success(function(dataz) {
        
        if (dataz.result=='ok'){
            $('#'+zid+' > :nth-child(2)').html('<i class="glyphicon glyphicon-ok-circle" style="color:green"></i>');
            $('#'+zid+' > :nth-child(3) span').html(dataz.date);
            $('.mybalance').html(dataz.newbalance);
            //console.log(dataz);
            ZOTTIG.show(dataz.message);
        }else{
            ZOTTIGWarning.show(dataz.message);
        }
    });
}

function partner_buy(type,clientid,id,count,zid){
    //console.log("ajax.php?do=pay&clientid="+clientid+"&id="+id+"&type="+type+"&amount="+count)
    $.ajax({ url: "ajax.php?do=pay&clientid="+clientid+"&id="+id+"&type="+type+"&amount="+count,dataType:"json"}).success(function(dataz) {
        
        if (dataz.result=='ok'){
            $('#'+zid+' > :nth-child(2)').html('<i class="glyphicon glyphicon-ok-circle" style="color:green"></i>');
            $('#'+zid+' > :nth-child(3) span').html(dataz.date);
            //console.log(dataz);
            ZOTTIG.show(dataz.message);
        }else{
            ZOTTIGWarning.show(dataz.message);
        }
    });
}

function payall(){
    data='Вы оплачиваете: <b>'+type+' '+name+'</b><br>';
    data+='До: <b>'+date+'</b><br>';
    data+='Сумма: <b>'+count+'</b><br>';
    data+='Способ оплаты: <b>С баланса</b> <br>';
    
    bootbox.dialog({
      message: data,
      title: 'Оплата',
      buttons: {
        pay: {
          label: "Оплатить",
          className: "btn-success",
          callback: function() {
            buy(type,id,count,zid);
          }
        }
      }
    });
}


function pay(type,name,id,count,date,zid){
    
    data='Вы оплачиваете: <b>'+type+' '+name+'</b><br>';
    data+='До: <b>'+date+'</b><br>';
    data+='Сумма: <b>'+count+'</b><br>';
    data+='Способ оплаты: <b>С баланса</b> <br>';
    
    bootbox.dialog({
      message: data,
      title: 'Оплата',
      buttons: {
        pay: {
          label: "Оплатить",
          className: "btn-success",
          callback: function() {
            buy(type,id,count,zid);
          }
        }
      }
    });
} 

function partner_pay(type,clientid,name,id,count,date,zid){
    
    data='Вы оплачиваете: <b>'+type+' '+name+'</b><br>';
    data+='До: <b>'+date+'</b><br>';
    data+='Сумма: <b>'+count+'</b><br>';
    data+='Способ оплаты: <b>С баланса</b> <br>';
    
    bootbox.dialog({
      message: data,
      title: 'Оплата',
      buttons: {
        pay: {
          label: "Оплатить",
          className: "btn-success",
          callback: function() {
            partner_buy(type,clientid,id,count,zid);
          }
        }
      }
    });
} 

function filterOnIdAutomatedPointChange(elem){
    v=$(elem,':selected').val();
    $.post("ajax.php?do=getselect_changes", { ap: v}).success(function(dataz) {
        $(elem).parents('form').find('select[name="chb_zasmenu"]').html(dataz);
    });
}
    
function chb_zaperiod_click(elem){
   $(elem).parents('form').find(".zaperiod").attr("checked","checked");
}
function chb_smenperiod_click(elem){
   $(elem).parents('form').find(".smenperiod").attr("checked","checked");
}

function onChangeChbZaSmenu(elem){
    form=$(elem).parents('form');
    lines=$(elem).find(":selected").text().split('_');
    //console.log(lines);
    $(form).find(".datestart").val(lines[1]);
    $(form).find(".dateend").val(lines[2]);
    $(form).find(".zasmenu").attr("checked","checked");
}

function submit_btn(elem,title){
    $('#loading').show();
    form=$(elem).parents('form');
    
    chb_val=$(form).find('input[name="chb"]:checked').val();
    
    if (((chb_val=='zaperiod') && ($(form).find('input[name="chb_zaperiod1"]').val()!='') && ($(form).find('input[name="chb_zaperiod2"]').val()!='')) || ((chb_val=='smenperiod') && ($(form).find('input[name="chb_smenperiod1"]').val()!='') && ($(form).find('input[name="chb_smenperiod2"]').val()!=''))||(chb_val=='zasmenu')){
        $(form).ajaxSubmit(function(data){  
           box=bootbox.dialog({
              message: data,
              title: title,
              className:'zdialog',
              buttons: {
                close: {
                  label: "Закрыть",
                  className: "btn-primary",
                  callback: function() {
                  }
                }
              }
            });
            
            box.on( 'hidden.bs.modal', function (){
                    var d = $( this );
                    d.modal( 'hide');
                    d.data( 'modal', null );
                    //console.log('onhidden');
                    d.html( '' );
                    d.remove();
                    
                });
            

                
                
           $('#loading').hide();
        });
    }else 
        alert('выберите период');
    
   
}

// end Отчеты

function getBarcode(elem){
    $.ajax({ url: "ajax.php?do=getBarcode"}).success(function(dataz) {
        $(elem).prev().val(dataz);
    });
}



function showclient(elem){
    tr_this=$(elem).parents('tr');
    tr=tr_this.next();
    status=tr.css('display');
    $('.showclient').prev().addClass('active').removeClass('success');
    $('.showclient').hide();
    if (status=='none'){
        tr.show();
        tr_this.addClass('success').removeClass('active');
    }else{ 
        tr_this.addClass('active').removeClass('success');
        tr.hide();
    }
}
   
   
function printBarcode(elem){
    form=$(elem).parents('form');
    name=form.find('input[name=name]').val();
    price=parseFloat(form.find('input[name=price]').val());
    mainShtrih=form.find('input[name=mainShtrih]').val();
     $.ajax({
        type: "POST",
        url: "http://localhost:12345",
        data: '<Header><order>etiketka</order><itemname>'+name+'</itemname><price>'+price+'</price><barcode>'+mainShtrih+'</barcode></Header>',
        dataType: "script"
    });
}     

function savenewpassword(){
    pass=$('#newpassword').val();
    pass2=$('#newpassword2').val();
    error='';
    if ((pass=='') || (pass2=='')){
        error='Введите новый пароль';
    }
    if (pass!=pass2){
        error='Пароли не совпадают';
    }
    //console.log(error);
    if (error==''){
        $.ajax({ url: "ajax.php?do=changemypass&password="+pass,dataType:"html"}).success(function(dataz) {
            if (dataz=='error')
                alert('Неверный пароль');
                else alert('Пароль успешно изменен');
         });
    return true;
    }else{
        alert(error);
        return false;
    }
}

function change_pass(){
     $.ajax({ 
      type: "POST",
      url: "ajax.php?do=change_pass",
      dataType:"html"
    }).success(function(dataz) {
        
    bootbox.dialog({
          message: dataz,
          title: 'Смена пароля',
          buttons: {
            pay: {
              label: "Закрыть",
              className: "btn-success",
              callback: function() {
                true;
              }
            },
            print: {
              label: "Сохранить",
              className: "btn-primary",
              callback: 
              function() {
              return savenewpassword();
              }
             
            }
          }
        });
    });
}

function show_combo(){
    $('#loading').show();            
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do=get_window_combo",
      dataType:"html"
    }).success(function(dataz) {
        removeTabIfExist('#tab_get_window_combo');
        cont='<div class="bggrey"><h4>Комбо меню</h4></div>'+dataz;
         addTab('Комбо меню','get_window_combo',cont);
         
         
         $('#s_items_combo').myTreeView({  
            url:'ajax.php?do=newfuckingget&table=s_items&complex=1&idfield=complex&noparent=1', 
            headers: [{title:'Наименование',name:'name'}],
            pagination:false,
            tree:false,
            dblclick : function (){
                    row=$('#s_items_combo').myTreeView('getSelected').id;
                    $('#s_items_combo_group').myTreeView('changeUrl', 'ajax.php?do=newfuckingget&table=s_combo_groups&idfield=itemid&itemid='+row);
                    $('#s_items_combo_group').myTreeView('reload');
                    
                    
                }
        });
        
        $('#s_items_combo_group').myTreeView({  
            url:'ajax.php?do=newfuckingget&table=s_combo_groups&itemid=0&idfield=itemid', 
            headers: [{title:'Наименование',name:'name'}],
            pagination:false,
            tree:false,
            dblclick : function (){
                    row=$('#s_items_combo_group').myTreeView('getSelected').id;
                    $('#s_items_combo_items').myTreeView('changeUrl', 'ajax.php?do=newfuckingget&table=s_combo_items&idfield=idcombogroup&idcombogroup='+row);
                    $('#s_items_combo_items').myTreeView('reload');
                }
        });
        
        $('#s_items_combo_items').myTreeView({  
            url:'ajax.php?do=newfuckingget&table=s_combo_items&idcombogroup=0&idfield=idcombogroup', 
            headers: [{title:'Наименование',name:'itemid'}],
            pagination:false,
            tree:false,
        });
         
        $('#loading').hide();
    });
}

function combo_submit_group(form){
    $('#'+form).ajaxSubmit(function(data){  
        $('#s_items_combo_group').myTreeView('reload');
        $('#s_items_combo_items').myTreeView('reload');
    });
}
function combo_submit_elem(form){
    $('#'+form).ajaxSubmit(function(data){  
        $('#s_items_combo_items').myTreeView('reload');
    });
}

function combo_create_gr(){
        if ($('#s_items_combo').myTreeView('getSelected')!=null){

        itemid=$('#s_items_combo').myTreeView('getSelected').id;
       
        $.ajax({ 
          url: 'ajax.php?do=zcreate&table=s_combo_groups&parentid=0&idfield=itemid&itemid='+itemid,
          dataType:"html"
        }).success(function(dataz) {
            
            
               bootbox.dialog({
                  message: dataz,
                  title: 'Создание',
                  className: 'invss',
                  buttons: {
                    pay: {
                      label: "Закрыть",
                      className: "btn-success",
                      callback: function() {
                        true;
                      }
                    },
                    print: {
                      label: "Добавить",
                      className: "btn-primary",
                      callback: function() {
                        combo_submit_group('form_add-s_combo_groups');
                      }
                    }
                  }
                });
        });
        
    }else{
        alert('Выберите элемент');
    }
}

function combo_edit_gr(){
        if ($('#s_items_combo_group').myTreeView('getSelected')!=null){

        itemid=$('#s_items_combo_group').myTreeView('getSelected').id;
       
        $.ajax({ 
          url: 'ajax.php?do=zedit&table=s_combo_groups&id='+itemid,
          dataType:"html"
        }).success(function(dataz) {
            
            
               bootbox.dialog({
                  message: dataz,
                  title: 'Редактирование',
                  className: 'invss',
                  buttons: {
                    pay: {
                      label: "Закрыть",
                      className: "btn-success",
                      callback: function() {
                        true;
                      }
                    },
                    print: {
                      label: "Сохранить",
                      className: "btn-primary",
                      callback: function() {
                        combo_submit_group('form_edit-s_combo_groups');
                      }
                    }
                  }
                });
        });
        
    }else{
        alert('Выберите элемент');
    }
}

function combo_create_el(){
        if ($('#s_items_combo_group').myTreeView('getSelected')!=null){

        itemid=$('#s_items_combo_group').myTreeView('getSelected').id;
       $('#s_items_combo_items').myTreeView('changeUrl', 'ajax.php?do=newfuckingget&table=s_combo_items&idfield=idcombogroup&idcombogroup='+itemid);
        $.ajax({ 
          url: 'ajax.php?do=zcreate&table=s_combo_items&parentid=0&idfield=idcombogroup&idcombogroup='+itemid,
          dataType:"html"
        }).success(function(dataz) {
            
            
               bootbox.dialog({
                  message: dataz,
                  title: 'Создание',
                  className: 'invss',
                  buttons: {
                    pay: {
                      label: "Закрыть",
                      className: "btn-success",
                      callback: function() {
                        true;
                      }
                    },
                    print: {
                      label: "Добавить",
                      className: "btn-primary",
                      callback: function() {
                        combo_submit_elem('form_add-s_combo_items');
                      }
                    }
                  }
                });
        });
        
    }else{
        alert('Выберите элемент');
    }
}

function combo_delete(grid){
    if (grid=='s_items_combo_group') table='s_combo_groups';
    if (grid=='s_items_combo_items') table='s_combo_items';
    if ($('#'+grid).myTreeView('getSelected')!=null){
        id=$('#'+grid).myTreeView('getSelected').id;
        //console.log(id);
        if (grid=='s_items_combo_group') title=$('#'+grid).myTreeView('getSelected').name;  
        if (grid=='s_items_combo_items') title=$('#'+grid).myTreeView('getSelected').itemid;  
                bootbox.confirm("Вы действительно хотите удалить <b>"+title+"</b>", function(r) {
                    if (r){ 
                        $.post("ajax.php?do=delete&table="+table, { id: id,table:table}).success(function(dataz) {
                             if (grid=='s_items_combo_group') {
                                $('#s_items_combo_group').myTreeView('reload');
                                $('#s_items_combo_items').myTreeView('changeUrl', 'ajax.php?do=newfuckingget&table=s_combo_items&idfield=idcombogroup&idcombogroup=0');
                            }
                            $('#s_items_combo_items').myTreeView('reload');
                            
                        }); 
                    }
                });

    }else{
        alert('Выберите элемент');
    }
}

function combo_edit_el(){
        if ($('#s_items_combo_items').myTreeView('getSelected')!=null){

        itemid=$('#s_items_combo_items').myTreeView('getSelected').id;
       
        $.ajax({ 
          url: 'ajax.php?do=zedit&table=s_combo_items&id='+itemid,
          dataType:"html"
        }).success(function(dataz) {
            
            
               bootbox.dialog({
                  message: dataz,
                  title: 'Создание',
                  className: 'invss',
                  buttons: {
                    pay: {
                      label: "Закрыть",
                      className: "btn-success",
                      callback: function() {
                        true;
                      }
                    },
                    print: {
                      label: "Сохранить",
                      className: "btn-primary",
                      callback: function() {
                        combo_submit_elem('form_edit-s_combo_items');
                      }
                    }
                  }
                });
        });
        
    }else{
        alert('Выберите элемент');
    }
}


function onlydigits(evt) {
  var theEvent = evt || window.event;
  var key = theEvent.keyCode || theEvent.which;
  key = String.fromCharCode( key );
  var regex = /[0-9]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}

function oninputchange(event,el,table){
    if (event.which==40){
        $("#livesearchselectfordbselectbyzottig :first-child").attr("selected", "selected");
        $("#livesearchselectfordbselectbyzottig").focus();
        
    }else{
        if((event.which!=38) && (event.which!=37) && (event.which!=39) && (event.which!=13)){
            $("#livesearchselectfordbselectbyzottig").remove();
            wal=$(el).val();
            if (wal.length>2){
                $.ajax({
                  type: "POST", 
                  url: 'ajax.php?do=livesearch&table='+table,
                  dataType:"json",
                  data: {q:wal}
                }).success(function(dataz) {
                    $("#livesearchselectfordbselectbyzottig").remove();
                    if (dataz.length!==0){
                        select='';
                        count=1;
                        $.each(dataz,function(i,element){
                            select+='<option value="'+i+'">'+element+'</option>';
                            count++;
                        });
                        $('<select size='+count+' id="livesearchselectfordbselectbyzottig" onkeyup="livesearchselectfordbselect(event,this)" class="form-control" >'+select+'</select>').insertAfter(el);
                        
                        $('#livesearchselectfordbselectbyzottig option').click(function(){
                            el=$('#livesearchselectfordbselectbyzottig');
                            id=$('#livesearchselectfordbselectbyzottig :selected').val();
                             title=$('#livesearchselectfordbselectbyzottig :selected').html();
                           $(el).prev().val(title);  
                           $(el).prev().attr('sval',title);  
                           $(el).next().val(id);  
                           $(el).next().change();  
                           $("#livesearchselectfordbselectbyzottig").remove();
                        });
    
    
                    }
                });
            }
        }
    }
    
}

function livesearchselectfordbselect(event,el){
   
 if ((event.which==38) && ($('#livesearchselectfordbselectbyzottig :first-child').is(':selected'))){
     $(el).prev().focus();
 }else if ((event.which==13)){
     id=$('#livesearchselectfordbselectbyzottig :selected').val();
     title=$('#livesearchselectfordbselectbyzottig :selected').html();
   $(el).prev().val(title);  
   $(el).prev().attr('sval',title);  
   $(el).next().val(id);  
   $(el).next().change();  
   $("#livesearchselectfordbselectbyzottig").remove();
 }
}

function getlastvalue(el){
    if (!$('#livesearchselectfordbselectbyzottig').exists())
        $(el).val($(el).attr('sval'));
}

function clear_db_select(elem){
    $(elem).parent().prev().val(0).prev().val('').attr('sval','');
}
        
$(function() {
/*    $('#tabs').tabs({
                tabPosition:'top',
                width: $('.righttd').width()-5,
                height: $('.righttd').height()-10,
            });*/
    $('.bayan li a').click(function(){
        ul=$(this).next();
        ul.toggle();
        if (ul.css('display') !== 'none'){
            $(this).addClass('open').removeClass('close');
        }else{
            $(this).addClass('close').removeClass('open');
            
            
        }
        
    });

    
            
    
     
     $(window).bind('resize', function () { 
         //$('#tabs').height($('#righttd').height());
         //$('#tabs').tabs();
            /*height=$('.righttd').height()-10;
            $('#tabs').tabs({'height',height});
            console.log(height);*/

     });
    
/*     
$.ajax({ url: "ajax.php?do=time",dataType:"html"}).success(function(dataz) {
    alert(dataz);
});   */ 


bootbox.setDefaults({
  locale: "ru",
  }); 

  
  
   ZOTTIG.init({
                "selector": ".alert-info"
            });
            
   ZOTTIGWarning.init({
                "selector": ".alert-danger"
            });
            
     
setInterval(function() {
     $.ajax({ url: "ajax.php?do=check_ver",dataType:"html"}).success(function(dataz) {
        if (dataz!=version)
            window.location.reload(); 
        //else
            //console.log('ok');
     });
}, 120000);

//show_account_settings();
$('#loading').hide();

$('#loading').click(function(){
    $(this).hide();
});


      
});

