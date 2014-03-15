jQuery.fn.exists = function() {
   return $(this).length;
}
//добавление нового элемента
function savez2(table,group){
    

    if (group==1) gr='gr'; else gr='';
    
    tabind='';
    if ($('#form'+gr+'-'+table+' input[name=iddoc]').val()!=undefined){      
        tabind='1';
    }
    if ($('#form'+gr+'-'+table+' input[name=apid]').val()!=undefined){      
        tabind='1';
    }

    $('#form'+gr+'-'+table).form('submit', {  
        success:function(data){  
            //console.log(data); return false;
            parentid=0;
            //получаем parentid
            //console.log(data);
            //console.log(data);
            data=$.parseJSON(data);
            
            if (data['rescode']==1){
            alert(data['resmsg']);
            return;
            }
            console.log('#table-'+table+tabind);
            if ($('#table-'+table+tabind).treegrid('getSelected')!=null){
                parentid=$('#table-'+table+tabind).treegrid('getSelected').id;
                //если выделен файл то создаем новый в этом же уровне
                if ($('#table-'+table+tabind).treegrid('getSelected').isgroup==0){
                    parentid=$('#table-'+table+tabind).treegrid('getSelected').parentid; 
                }
            }
            
        if (parentid>0)
            $('#table-'+table+tabind).treegrid('append',{parent: parentid,data: [data]});
        else
            $('#table-'+table+tabind).treegrid('append',{data: [data]});
            
        isgroup=$('#form'+gr+'-'+table+' input[name=isgroup]').val();
        $('#form'+gr+'-'+table).form('clear');
        $('#form'+gr+'-'+table+' input[name=isgroup]').val(isgroup);
        $('#dialog'+gr+'-'+table).dialog('close');    
        }
        
    });
    

}


function datagrid_save(table){
    $('#form'+'-'+table).form('submit', {  
        success:function(data){  
            data=$.parseJSON(data);
            $('#table-'+table).treegrid('append',{data: [data]});
            $('#dialog'+table).dialog('close');    
        } 
    });
}
//сохранение для эдита
function savez4(table,id){

    tabind='';
    if (table=='s_ord'){      
        tabind='1';
    }
    
    if (table=='t_workplace'){      
        tabind='1';
    }
    
    $('#form_edit-'+table+' .checkselect:not(:checked)').each(function(){
        $(this).attr("checked", "checked");
        $(this).val('0');
        
    }); 
    
    $('#form_edit-'+table+' .chbchecktozero:not(:checked)').each(function(){
        $(this).attr("checked", "checked");
        $(this).val('0');
        
    });
    
    
    $('#form_edit-'+table).form('submit', {  
        success:function(data){  
            
            data=$.parseJSON(data);
            
            if (data['rescode']==1){
            alert(data['resmsg']);
            return;
            }
            
            
            if ($('#table-'+table+tabind).treegrid('getSelected')!=null){
                parentid=$('#table-'+table+tabind).treegrid('getSelected').id;
                //если выделен файл то создаем новый в этом же уровне
                if ($('#table-'+table+tabind).treegrid('getSelected').isgroup==0){
                    parentid=$('#table-'+table+tabind).treegrid('getSelected').parentid; 
                }
            }
            
            $('#table-'+table+tabind).treegrid('update',{
                id: id,
                row: data
            });
 
        $('#form_edit-'+table).form('clear');
        
        $('#dialog_edit-'+table).dialog('close');    
        }
        
    });
    

}

function savez4(table,id){

    tabind='';
    if (table=='s_ord'){      
        tabind='1';
    }
    
    if (table=='t_workplace'){      
        tabind='1';
    }
    
    $('#form_edit-'+table+' .checkselect:not(:checked)').each(function(){
        $(this).attr("checked", "checked");
        $(this).val('0');
        
    }); 
    
    $('#form_edit-'+table+' .chbchecktozero:not(:checked)').each(function(){
        $(this).attr("checked", "checked");
        $(this).val('0');
        
    });
    
    
    $('#form_edit-'+table).form('submit', {  
        success:function(data){  
            
            data=$.parseJSON(data);
            
            if (data['rescode']==1){
            alert(data['resmsg']);
            return;
            }
            
            
            if ($('#table-'+table+tabind).treegrid('getSelected')!=null){
                parentid=$('#table-'+table+tabind).treegrid('getSelected').id;
                //если выделен файл то создаем новый в этом же уровне
                if ($('#table-'+table+tabind).treegrid('getSelected').isgroup==0){
                    parentid=$('#table-'+table+tabind).treegrid('getSelected').parentid; 
                }
            }
            
            $('#table-'+table+tabind).treegrid('update',{
                id: id,
                row: data
            });
 
        $('#form_edit-'+table).form('clear');
        
        $('#dialog_edit-'+table).dialog('close');    
        }
        
    });
    

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
                $('#dlg-del').dialog('open');  
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
        id=$('#table-'+table).treegrid('getSelected').id; 
        $.post("ajax.php?do=delete", { id: id,table:table}).success(function(dataz) {
            console.log(dataz);
            if (dataz=='ok'){
                $('#table-'+table).treegrid('remove',id);
                $('#dlg-del').dialog('close');
            }else{
                alert(dataz);
                $('#dlg-del').dialog('close');
            }
        });
    }else {
        id=$('#table-'+table).treegrid('getSelected').id;
        $.post("ajax.php?do=delete", { id: id,table:table}).success(function(dataz) {
            if (dataz=='ok'){
                $('#table-'+table).treegrid('remove',id);
                $('#dlg-del').dialog('close');
            }else{
                alert(dataz);
                $('#dlg-del').dialog('close');
            }
        });
    }

}

function deletez_ok2(){
    
    id=$('#table-t_menu_items').treegrid('getSelected').id;
    
    $.post("ajax.php?do=delete2", { id: id}).success(function(dataz) {
        console.log(dataz);
        $('#table-t_menu_items').treegrid('remove',id);
        $('#dlg-del2').dialog('close');
    });
}


function deletez_menu(table){
    if ($('#table-'+table).treegrid('getSelected')!=null){
        id=$('#table-'+table).treegrid('getSelected').id;
        $('#del-name2').html($('#table-'+table).treegrid('getSelected').name);
        $('#del_table2').val(table);
        $('#dlg-del2').dialog('open');  
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
    
    
    if ($('#table-'+table+tabind).treegrid('getSelected')!=null){
        parentid=$('#table-'+table+tabind).treegrid('getSelected').id;
        //если выделен файл то создаем новый в этом же уровне
        if ($('#table-'+table+tabind).treegrid('getSelected').isgroup==0)
            parentid=$('#table-'+table+tabind).treegrid('getSelected').parentid;   
    }
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
function zcreate_el(table,iddoc){
    parentid=0;
    iddocstr='';
    tabind='';
    if (iddoc!=null){
        iddocstr='&iddoc='+iddoc;       
        tabind='1';
    }
    //console.log(iddoc+'==');
    //получаем parentid
    
    
    if ($('#table-'+table+tabind).myTreeView('getSelected')!=null){
        parentid=$('#table-'+table+tabind).myTreeView('getSelected').id;
        //если выделен файл то создаем новый в этом же уровне
        if ($('#table-'+table+tabind).myTreeView('getSelected').isgroup==0)
            parentid=$('#table-'+table+tabind).myTreeView('getSelected').parentid;   
    }
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
    if ($('#table-'+table).treegrid('getSelected')!=null){
        parentid=$('#table-'+table).treegrid('getSelected').id;
        //если выделен файл то создаем новый в этом же уровне
        if ($('#table-'+table).treegrid('getSelected').isgroup==0)
            parentid=$('#table-'+table).treegrid('getSelected').parentid;   
    }
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
    if ($('#table-'+table).treegrid('getSelected')!=null){
        parentid=$('#table-'+table).treegrid('getSelected').id;
        //если выделен файл то создаем новый в этом же уровне
        if ($('#table-'+table).treegrid('getSelected').isgroup==0)
            parentid=$('#table-'+table).treegrid('getSelected').parentid;   
    }
    
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
    if ($('#table-'+table).myTreeView('getSelected')!=null){
        parentid=$('#table-'+table).myTreeView('getSelected').id;
        //если выделен файл то создаем новый в этом же уровне
        if ($('#table-'+table).myTreeView('getSelected').isgroup==0)
            parentid=$('#table-'+table).myTreeView('getSelected').parentid;   
    }
    
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
 

function set_rights(){
     $('#form_z_rights').form('submit', {  
            success:function(data){  
                $('#grid_z_rights').html(data);
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
            }
            
        });
 }
 
 
function load_rights(){
       id=$('#groupid option:selected').val();
       $.ajax({ 
          type: "POST",
          url: "ajax.php?do=get_z_rights",
          data: {groupid: id},
          dataType:"html"
    }).success(function(data) {
        $('#grid_z_rights').html(data);
    });
   }
 

//saipal
function perenoska_delai(){
    $.messager.confirm('Подтверждение','Вы действительно хотите перенести всё меню?',function(r){  
    if (r){ 
         id=$("#selc :selected").val();
         console.log(id);
         $.ajax({ 
          type: "POST",
          url: "ajax.php?do=perenoska&menuid="+id,
          dataType: 'json'
        }).success(function(dataz) {       
            console.log(dataz);
            $('#table-t_menu_items').treegrid('loadData',dataz);
            
        });    
    }
    }); 
}

function move(){

     menuid=$("#selc :selected").val();
     
      if ($('#table-t_menu_items1').treegrid('getSelected')!=null) {
            idfrom=$('#table-t_menu_items1').treegrid('getSelected').id;
            
            if ($('#table-t_menu_items').treegrid('getSelected')!=null)
                idto=$('#table-t_menu_items').treegrid('getSelected').id;
            else 
                idto=0;
            console.log(idto+'-'+idfrom+'-'+menuid);
        
         $.ajax({ 
          type: "POST",
          url: "ajax.php?do=move",
          data: {idfrom: idfrom,idto:idto,menuid:menuid},
          dataType: 'json'
        }).success(function(dataz) {       
            //console.log(dataz);
            $('#table-t_menu_items').treegrid('loadData',dataz);
            
        });
        
        //console.log('123123');     
      }else{
          alert('Выберите откуда ');
      }
}


function cutfromgrid(){
        
    }
    

function setprinter(){
      $.ajax({ 
              type: "GET",
              url: "ajax.php?do=get_window_setprinter",
              dataType:"html"
            }).success(function(dataz) {
                
            if($("#window_setprinter").exists()) {
                    $("#window_setprinter").window('close');
                    $("#window_setprinter").html('');
                    $("#window_setprinter").remove();
                }
                
                $("#windows").append('<div id="window_setprinter"">'+dataz+'</div>');
                
                
                $("#window_setprinter").window( {
                    title: 'Выберите принтер',
                    height:100,
                    width:300
                }); 
                $('.easyui-linkbutton').linkbutton(); 
                
            });
}

function setsubdivision(){
    pr_id=$("#subdivisionid :selected").val();
            menuid=$("#selc :selected").val();
    if ($('#table-t_menu_items').treegrid('getSelected')!=null) {
            catid=$('#table-t_menu_items').treegrid('getSelected').id;
            

            $.ajax({ 
              type: "GET",
              url: "ajax.php?do=setprinter&pr_id="+pr_id+"&cat_id="+catid,
              dataType: 'html'
            }).success(function(data) {       
                console.log(data);
                $.ajax({ 
                  type: "POST",
                  url: "ajax.php?do=getmenuitems&menuid="+menuid,
                  dataType: 'json'
                }).success(function(dataz) {       
                    $('#table-t_menu_items').treegrid('loadData',dataz);
                     $("#window_setprinter").window('close');
                });     
                
            });     
    }
}

function lefttd(cl){
    $('.menu li').removeClass('selected');
    $('.'+cl).addClass('selected');
    $('.s_links').hide();
    $('#'+cl).show();
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
        
        if($('#tabs').tabs('exists', dataz.title)){
            $('#tabs').tabs('close',dataz.title);
        }
            
        
        
     //   if(!$("#window-"+dataz.name).exists()) {
     cont='<div class="bggrey"><h2>'+dataz.title+'</h2></div><div id="window-'+dataz.name+'"><div id="toolbar-'+dataz.name+'">'+(dataz.rights.view==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="view_el(\''+table+'\')">Просмотр</a>':'');
     cont+=(dataz.rights.add==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="create_el2(\''+table+'\')">Добавить элемент</a>'+(dataz.create_group==true?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="create_group2(\''+table+'\')">Добавить группу</a>':''):'');
     cont+=(dataz.rights.edit==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editz2(\''+table+'\')">Изменить</a>':'');
     cont+=(dataz.rights.delete==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="deletez(\''+table+'\')">Удалить</a>':'');
     cont+=(dataz.rights.print==1?'<a href="#" onclick="window.open(\'ajax.php?do=getfile&type=html&table='+table+'\',\'\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no\');" class="easyui-linkbutton" iconCls="icon-print" plain="true">Печать</a>':'')+'</div><table><tr><td  style="border-right:1px solid #000"><table id="table-'+dataz.name+'2"></table></td><td><table id="table-'+dataz.name+'"></table></td></tr></table></div><div id="menu-'+dataz.name+'" class="easyui-menu" style="width:120px"></div></div>';
        
        
        $('#tabs').tabs('add',{  
            title:dataz.title,  
            content:cont,  
            closable:true
        });  

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
            console.log('ajax.php?do=get&table='+dataz.name+'&id='+row.id);
            
            
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


function show_tree(table){
    width=$('.righttd').width()-10;
    height=$('.righttd').height()-85;
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do=gettableazorchik",
      data: {table: table},
      dataType: 'json'
    }).success(function(dataz) {
        $('#window-'+dataz.name).remove();
        if($('#tabs').tabs('exists', dataz.title)){
            $('#tabs').tabs('close',dataz.title);
        }
     //   if(!$("#window-"+dataz.name).exists()) {
     cont='<div class="bggrey"><h2>'+dataz.title+'</h2></div><div id="window-'+dataz.name+'"><div id="toolbar-'+dataz.name+'">'+(dataz.rights.view==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="zview_el(\''+table+'\')">Просмотр</a>':'');
     cont+=(dataz.rights.add==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="zcreate_el(\''+table+'\')">Добавить элемент</a>'+(dataz.create_group==true?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="zcreate_group(\''+table+'\')">Добавить группу</a>':''):'')+(dataz.rights.edit==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="zedit(\''+table+'\')">Изменить</a>':'')+(dataz.rights.delete==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="zdelete(\''+table+'\')">Удалить</a>':'')+(dataz.rights.print==1?'<a href="#" onclick="window.open(\'ajax.php?do=getfile&type=html&table='+table+'\',\'\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no\');" class="easyui-linkbutton" iconCls="icon-print" plain="true">Печать</a>':'')+'</div></div><div id="table-'+dataz.name+'"></div></div>';
        
        $('#tabs').tabs('add',{  
            title:dataz.title,  
            content:cont,  
            closable:true,   
        });  

        $('.easyui-linkbutton').linkbutton();
 
        $('#table-'+dataz.name).myTreeView({
                url:'ajax.php?do=newfuckingget&table='+dataz.name, 
                headers : dataz.fields
            });
 
    });
    
    
        
return false;   
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
        
        if($('#tabs').tabs('exists', dataz.title)){
            $('#tabs').tabs('close',dataz.title);
        }
            
        
        
     //   if(!$("#window-"+dataz.name).exists()) {
     cont='<div class="bggrey"><h2>'+dataz.title+'</h2></div><div id="window-'+dataz.name+'"><table id="table-'+dataz.name+'"></table><div id="toolbar-'+dataz.name+'">'+(dataz.rights.view==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="view_el(\''+table+'\')">Просмотр</a>':'');
     cont+=(dataz.rights.add==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="create_el2(\''+table+'\')">Добавить элемент</a>'+(dataz.create_group==true?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="create_group2(\''+table+'\')">Добавить группу</a>':''):'')+(dataz.rights.edit==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editz2(\''+table+'\')">Изменить</a>':'')+(dataz.rights.delete==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="deletez(\''+table+'\')">Удалить</a>':'')+(dataz.rights.print==1?'<a href="#" onclick="window.open(\'ajax.php?do=getfile&type=html&table='+table+'\',\'\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no\');" class="easyui-linkbutton" iconCls="icon-print" plain="true">Печать</a>':'')+'</div></div><div id="menu-'+dataz.name+'" class="easyui-menu" style="width:120px"></div></div>';
        
        
        $('#tabs').tabs('add',{  
            title:dataz.title,  
            content:cont,  
            closable:true,   
        });  

        $('.easyui-linkbutton').linkbutton();  
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
    }
        
        
    if($('#tabs').tabs('exists', title)){
        $('#tabs').tabs('close',title);
    }
    
    cont='<div class="bggrey"><h2>'+title+'</h2></div>'+dataz;
    
     $('#tabs').tabs('add',{  
            title:title,  
            content:cont,  
            closable:true,   
     });  
     
     if (otchet=='getmain_charts'){
          $.ajax({ 
      url: "ajax.php?do=get_chart_changes",
      dataType:"json"
    }).success(function(dataz) {
        html='График за смену <select id="chartselect1">';
        console.log(dataz);
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
                    console.log(dataz);
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
        console.log(dataz);
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
                    console.log(dataz);
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
        if($('#tabs').tabs('exists', dataz.title)){
            $('#tabs').tabs('close', dataz.title);
        }
        cont='<div class="bggrey"><h2>'+dataz.title+'</h2></div>'+dataz.table;
        $('#tabs').tabs('add',{  
                title:dataz.title,  
                content:cont,  
                closable:true,   
        });  
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

    height=$('.righttd').height()-85;
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do=design_menu",
      data: {table: table},
      dataType:"json"
    }).success(function(dataz) {
        
    if($('#tabs').tabs('exists', 'Дизайнер меню')){
        $('#tabs').tabs('close','Дизайнер меню');
    }
    //console.log(dataz);
    list='';
    $.each( dataz.fields, function(k, v){
       list+="<option value="+k+">"+ v+"</option>";
    });
            
    cont='<div class="bggrey"><h2>Дизайнер меню</h2></div><div id="window-'+table+'">&nbsp;&nbsp;'+(dataz.rights.edit==1?'<a href="javascript:void(0)" class="easyui-linkbutton" onclick="perenoska_delai()">Перенести все меню &rarr;</a> <a href="javascript:void(0)" class="easyui-linkbutton" onclick="move()">Перенести один элемент &rarr;</a>':'')+'<select id="selc">'+list+'</select> <table><tr><td class="dizmen"><b>Общее меню</b> <br /><table id="table-'+table+'1"></table></td><td class="dizmen"><b>Меню</b> <br /><div id="toolbar-'+table+'">'+(dataz.rights.edit==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editz2(\''+table+'\')">Изменить</a>':'')+''+(dataz.rights.delete==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="deletez_menu(\''+table+'\')">Удалить</a>':'')+''+(dataz.rights.edit==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="true" onclick="setprinter()">Назанчить подразделение</a>':'')+'</div><table id="table-'+table+'"></table></td></tr></table></div>'; 
           
    
     $('#tabs').tabs('add',{  
            title:'Дизайнер меню',  
            content:cont,  
            closable:true,   
     });  

        
        
        sel_id=$("#selc :selected").val();
        
        $('.easyui-linkbutton').linkbutton();  
        
        $('#table-'+table+'1').treegrid({  
            url:'ajax.php?do=get&table=s_items', 
            rownumbers: true,
            idField: 'id',
            treeField: 'name',
            width:400,
            pagination:true,
            pageSize:50,
            pageList:[50,100,150],
            height:(height-37),
            columns: [[{title:'Наименование',field:'name'},{field:'idout',title:'Код'}]],
             onBeforeLoad: function(row,param){  
                if (!row) { 
                    param.id = 0;   
                }  
            } 
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
        
        $('#table-'+table).treegrid({  
            url:'ajax.php?do=getmenuitems&menuid='+sel_id, 
            rownumbers: true,
            idField: 'id',
            treeField: 'name',
            width:400,
            height:height,
            toolbar:'#toolbar-'+table, 
            columns: [[{title:'Наименование',field:'name'},{field:'idout',title:'Код'},{field:'price',title:'Цена'},{field:'printer',title:'Принтер'}]]
        });
        
        
        $("#selc").change(function() {
            sel_id=$("#selc :selected").val();
            
            $('#table-'+table).treegrid({  
                url:'ajax.php?do=getmenuitems&menuid='+sel_id, 
                rownumbers: true,
                idField: 'id',
                treeField: 'name',
                width:400,
                height:height,
                columns: [[{title:'Наименование',field:'name'},{field:'idout',title:'Код'},{field:'price',title:'Цена'}]]
            });
            
            $.ajax({ 
              type: "POST",
              url:'ajax.php?do=getmenuitems&menuid='+sel_id, 
              dataType: 'json'
            }).success(function(dataz) {       
                $('#table-t_menu_items').treegrid('loadData',dataz);
            }); 
            
           
            
        });
        
         
            


    });
    
    
    
    
        
    
}


//вывод окна добавление элемента
function datagrid_add(table){
    if ($('#table-'+table+'1').treegrid('getSelected')!=null){
        if($("#dialog-"+table).exists()) {
             $('#dialog-'+table).dialog('close');
             $('#dialog-'+table).html('');
             $('#dialog-'+table).remove();                
        }
        itemid=$('#table-'+table+'1').treegrid('getSelected').id;
        console.log(itemid);
        $("#dialogs").append('<div id="dialog-'+table+'"></div>');   
        $('#dialog-'+table).dialog({  
                    title: 'Создание',  
                    width: 500,  
                    hcenter:true,
                    top:50,  
                    closed: false,
                    href: 'ajax.php?do=create_el&table='+table+'&parentid=0&itemid='+itemid  
        });
        $('.easyui-linkbutton').linkbutton();   
    }else{
        alert('Выберите элемент');
    }
}



function datagrid_edit(table){

    if ($('#table-'+table).treegrid('getSelected')!=null){
        id=$('#table-'+table).treegrid('getSelected').id;
        if($("#dialog_edit-"+table).exists()) {
             $('#dialog_edit-'+table).dialog('close');
             $('#dialog_edit-'+table).html('');
             $('#dialog_edit-'+table).remove();
        }
        $("#dialogs").append('<div id="dialog_edit-'+table+'"></div>');   
        $('#dialog_edit-'+table).dialog({  
            title: 'Редактирование',  
            width: 500,  
            hcenter:true,
            top:50, 
            closed: false,
            href: 'ajax.php?do=edit_el&table='+table+'&id='+id
        });
        $('.easyui-linkbutton').linkbutton();
    }else{
        alert('Выберите элемент');
    }
}

function datagrid_delete(table){
    if ($('#table-'+table).treegrid('getSelected')!=null){
        id=$('#table-'+table).treegrid('getSelected').id;
        //console.log(id);
        $.post("ajax.php?do=getcounts&table="+table, { id: id}).success(function(dataz) {
            if (dataz==0){
                $('#del-name').html($('#table-'+table).treegrid('getSelected').name);
                $('#del_table').val(table);
                $('#del_type').val(1);
                $('#dlg-del').dialog('open');  
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


function show_note(table){

    height=$('.righttd').height()-85;
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do=get_note_rights",
      data: {table: table},
      dataType:"json"
    }).success(function(rights) {
        
    if($('#tabs').tabs('exists', 'Примечания')){
        $('#tabs').tabs('close','Примечания');
    }
    console.log(rights);

            
    cont='<div class="bggrey"><h2>Примечания</h2></div><div id="window-'+table+'"><table><tr><td class="dizmen"><b>Категории</b> <br /><table id="table-'+table+'1"></table></td><td class="dizmen"><b>Примечание</b> <br /><div id="toolbar-'+table+'">'+(rights.add==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="datagrid_add(\''+table+'\')">Добавить</a>':'')+(rights.edit==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="datagrid_edit(\''+table+'\')">Изменить</a>':'')+''+(rights.delete==1?'<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="datagrid_delete(\''+table+'\')">Удалить</a>':'')+'</div><table id="table-'+table+'"></table></td></tr></table></div>'; 
           
    
     $('#tabs').tabs('add',{  
            title:'Примечания',  
            content:cont,  
            closable:true,   
     });  

        $('#table-'+table+'1').treegrid({  
            url:'ajax.php?do=get&table=s_items&note=1', 
            rownumbers: true,
            idField: 'id',
            pagination:true,
            pageSize:50,
            pageList:[50,100,150],
            treeField: 'name',
            width:400,
            height:height,
            columns: [[{title:'Наименование',field:'name',width:300},{field:'idout',title:'Код'}]]
        });
        
        $('#table-'+table+'1').treegrid({
            onClickRow: function(row){
                $('#table-'+table).treegrid({  
                    url:'ajax.php?do=get_note_table&id='+row.id, 
                });  
            }
        });
        
        $('#table-'+table).treegrid({  
            
            rownumbers: true,
            singleSelect:true,
            idField: 'id',
            treeField: 'name',
            width:400,
            height:height,
            columns: [[{title:'Наименование',field:'name', width:350}]]
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
                          
                    $.ajax({ 
                      type: "POST",
                      url: "ajax.php?do=get_window_z_rights",
                      dataType:"html"
                    }).success(function(dataz) {
                        
                        if($('#tabs').tabs('exists', 'Права групп')){
                            $('#tabs').tabs('close','Права групп');
                        }
                        
                        cont='<div class="bggrey"><h2>Права групп</h2></div>'+dataz;
                        
                        $('#tabs').tabs('add',{  
                                title:'Права групп',  
                                content:cont,  
                                closable:true,   
                         });  

                        
                        $('.easyui-linkbutton').linkbutton(); 
                        
                    });
         }
         function show_feedback(){
                    $.ajax({ 
                      type: "POST",
                      url: "ajax.php?do=get_window_feedback",
                      dataType:"html"
                    }).success(function(dataz) {
                        
                        if($('#tabs').tabs('exists', 'Обратная связь')){
                            $('#tabs').tabs('close','Обратная связь');
                        }
                        
                        cont='<div class="bggrey"><h2>Обратная связь</h2></div>'+dataz;
                        
                        $('#tabs').tabs('add',{  
                                title:'Обратная связь',  
                                content:cont,  
                                closable:true,   
                         });  
                        $('.easyui-linkbutton').linkbutton(); 
                    });
         }
function post_feedback(){
    $('#feed_form').form('submit', {  
        success:function(data){  
            $('#message_feedback').val('');
            alert(data); 
        }
    });
}
function show_account_settings(){
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do=show_account_settings",
      dataType:"html"
    }).success(function(dataz) {
        
        if($('#tabs').tabs('exists', 'Настройки аккаунта')){
            $('#tabs').tabs('close','Настройки аккаунта');
        }
        
        cont='<div class="bggrey"><h2>Настройки аккаунта</h2></div>'+dataz;
        
        $('#tabs').tabs('add',{  
                title:'Настройки аккаунта',  
                content:cont,  
                closable:true,   
         });  

        
        $('.easyui-linkbutton').linkbutton(); 
        
    });
}

function save_account_settings(){
    $('#show_account_settings').form('submit', {  
        success:function(data){  
            alert(data); 
        }
    });    
}

    $.fn.datebox.defaults.formatter = function(date){
        var y = date.getFullYear();
        var m = date.getMonth()+1;
        var d = date.getDate();
        return (d<10?('0'+d):d)+'.'+(m<10?('0'+m):m)+'.'+y;
    };
    $.fn.datebox.defaults.parser = function(s){
        if (!s) return new Date();
        var ss = s.split('.');
        var d = parseInt(ss[0],10);
        var m = parseInt(ss[1],10);
        var y = parseInt(ss[2],10);
        if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
            return new Date(y,m-1,d);
        } else {
            return new Date();
        }
    };
        
        
        
$(function() {
    $('#tabs').tabs({
                tabPosition:'top',
                width: $('.righttd').width()-5,
                height: $('.righttd').height()-10,
            });
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
     
     
setInterval(function() {
     $.ajax({ url: "ajax.php?do=check_ver",dataType:"html"}).success(function(dataz) {
        if (dataz!=version)
            window.location.reload(); 
        else
            console.log('ok');
     });
}, 120000);
      
});
