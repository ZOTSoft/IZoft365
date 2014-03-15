function show_warehouse_tree( table ){
    $( '#loading' ).show();
    width = $( '.righttd' ).width() - 10;
    height = $( '.righttd' ).height() - 85;
    $.ajax({ 
      type: "POST",
      url: "ajax.php?do=gettableazorchik",
      data: { table: table },
      dataType: 'json'
    }).success( function ( dataz ){
        $( '#window-' + dataz.name ).remove();
        
        removeTabIfExist( '#tab_' + table );
        
        showEl = 'viewA(\'' + table + '\')';
        createEl = 'createA(\'' + table + '\')';
        createCopy = 'createBasedA( \'' + table + '\', -1, \'' + table + '\' )';//'zcreate_elcopy(\'' + table + '\')';
        editEl   = 'editA(\'' + table + '\')';
        deleteEl = 'deleteA(\'' + table + '\')';
        gridUrl = '/company/warehouse/warehouse.php?do=get&table=' + dataz.name;

        cont = '<div class="bggrey"><h4>' + dataz.title + '</h4></div><div class="toolbar">\n\<div>' +
( dataz.rights.view == 1 ? '<a href="javascript:void(0)" class="btn btn-default" onclick="' + showEl + '"><i class="icon-search"></i> Просмотр</a> ' : '' );
        cont += ( dataz.rights.add == 1 ? '<div class="btn-group">' +
'<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Добавить <span class="caret"></span></button>' +
'<ul class="dropdown-menu" role="menu">' +
'<li><a href="#" onclick="' + createEl + '">Элемент</a></li>' +
'<li><a href="#" onclick="' + createCopy + '">Копированием</a></li>' +
'</ul></div>' : '' ) +
(dataz.rights.edit == 1 ? '<a href="javascript:void(0)" class="btn btn-default" onclick="' + editEl + '"><i class="icon-edit"></i> Изменить</a> ' : '' ) +
(dataz.rights.deletez ==1 ? '<a href="javascript:void(0)" class="btn btn-default"  onclick="' + deleteEl + '"><i class="icon-trash"></i> Удалить</a> ' : '' ) +
(dataz.rights.print == 1 ? '<a href="#" onclick="window.open(\'ajax.php?do=getfile&type=html&table=' + table + '\',\'\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no\');" class="btn btn-default" ><i class="icon-print"></i> Печать</a> ' : '')+
'<a href="#" onclick="togglefilter(this)" class="btn btn-default dropdown-toggle" data-toggle="button"><span class="glyphicon glyphicon-filter"></span>Фильтр</a><div style="display:none" class="well well-sm filtertoggle">';
      
        cont += '<form id="form-' + table + '">' + dataz.filter + '</form>';
        cont += '<div class="clear"></div>';
        cont += '<div class="zfilterok"><button type="button" class="btn btn-success" onclick="setfilter(\'' + table + '\',1)">Применить</button><button type="button" class="btn btn-link" onclick="setfilter(\'' + table + '\',2)">Очистить</button></div>';
        cont += '<div class="clear"></div>';
        cont+='</div></div></div><div class="righttd-content"><div id="table-' + dataz.name + '"> </div></div></div>';

        addTab( dataz.title, table, cont );
        
        $( '#table-' + dataz.name ).myTreeView({
            url: gridUrl,
            headers : dataz.fields,
            tree: dataz.create_group,
            pagination:true,
            pagecount: [50, 100, 200],
            dblclick : function (){ editA( table ); }
        });
        
        $('.righttd-content').height($('.righttd').height()-$('.righttd-content').last().offset().top+30);
    });

    return false;   
}

function getColumnSum( table, col ){
    sum = 0;
    tbl = $( '#table-' + table );
    rc = tbl.myTreeView( 'rowCount' );
    for ( i = 0; i < rc; i++ ){
        tbl.myTreeView( 'selectRow', i );
        sum += tbl.myTreeView( 'getSelected' )[col];
    }
    return sum;
}
// при выборе товара подставляется коэффф
function getMultip( elem ){
    form = $( elem ).parents( 'form' );
    
    var mId = form.find( 'input[name=measureid]' ).val();
    var iId = form.find( 'input[name=itemid]' ).val();
    if ( mId != '' && iId != '' ){
        $.ajax({
            type: "POST",
            url: "/company/warehouse/warehouse.php?do=getMultip",
            data: {
                itemid: iId,
                measureid: mId
            }
        }).success( function ( data ){
            data = data || null;
            if ( data != null ){
                mId = form.find( 'input[name=multip]' ).val( data );
            }
        });
    }
}

//вывод окна просмотра документа
function viewA( table, iddoc ){
    var id = iddoc || null;
    if ( id != null || $( '#table-' + table ).myTreeView( 'getSelected' ) != null){
        if ( id == null ) id = $( '#table-' + table ).myTreeView( 'getSelected' ).id;
        if( $( "#dialog_view-" + table ).exists() ) {
             $( '#dialog_view-' + table).modal( 'hide' );
             $( '#dialog_view-' + table).data( 'modal', null );
             $( '#dialog_view-' + table).html( '' );
             $( '#dialog_view-' + table).remove();
        }  
        
        $.ajax({
            url: '/company/warehouse/warehouse.php?do=view_el&table=' + table + '&id=' + id
        }).success( function ( form ){
            $( "body" ).append( form );
            $( '#dialog_view-' + table ).modal( 'show' );
        });
    } else {
        bootbox.alert( 'Выберите элемент!' )
    }
}

//вывод окна добавления элемента
function createA( table, iddoc ){
    iddocstr = '';
    tabind = '';
    if ( iddoc != null ){
        iddocstr = '&documentid=' + iddoc;       
        tabind = '1';
    }
    //получаем parentid
    
    if( $( "#dialog_add-" + table ).exists() ) {
        d = $( '#dialog_add-' + table );
        d.modal( 'hide' );
        d.data( 'modal', null );
        d.html( '' );
        d.remove();
   }
    
    $.ajax({
        url: '/company/warehouse/warehouse.php?do=create_el&table=' + table + '&parentid=0' + iddocstr  
    }).success( function ( form ){
        $( "body" ).append( form );   
        $( '#dialog_add-' + table ).modal( 'show' );

        $('#dialog_add-' + table).on( 'hidden.bs.modal', function (){
            $( this ).modal( 'hide' );
            $( this ).data( 'modal', null );
            $( this ).html( '' );
            $( this ).remove();
        });
    });
}

//вывод окна изменения
function editA( table, iddoc ){
    $('#loading').show();
    if ( $( '#table-' + table ).myTreeView( 'getSelected' ) != null ){
//        t = false;
//        if ( table.substring( 0, 2 ) == 't_' ){
//            t = $('#form_edit-d_' + table.substring( 2 ) + ' input[name=conduct]').val() == '1';
//        } else {
//            t = $( '#table-' + table + tabind ).treegrid( 'getSelected' ).conducted == 'Да';
//        }
        
//        if ( t ){
//            $.messager.confirm('Подтверждение', 'Чтобы изменить документ, необходимо отменить его проведение. Продолжить?', function ( r ){  
//                if ( r ){  

        if ( table.indexOf( 't_' ) == 0 ) editable = 1;
        else editable = $( '#table-' + table ).myTreeView( 'getSelected' ).editable;
        if ( editable == 1 ){
            rowdata = JSON.stringify( $( '#table-' + table ).myTreeView( 'getSelected' ) );
            
            console.log( rowdata );
            
            id = $( '#table-' + table ).myTreeView( 'getSelected' ).id;
            if( $( "#dialog_edit-" + table ).exists()) {
                d = $( '#dialog_edit-' + table );
                d.modal( 'hide' );
                d.data( 'modal', null );
                d.html( '' );
                d.remove();
            }

            $.ajax({
                type: 'POST',
                url: '/company/warehouse/warehouse.php?do=edit_el&table=' + table + '&id=' + id,
                data: { rowdata: rowdata }
            }).success( function ( form ){
                $( "body" ).append( form );   
                $( '#dialog_edit-' + table ).modal( 'show' );
                $('#loading').hide();
                $( '#dialog_edit-' + table ).on( 'hidden.bs.modal', function (){
                    d = $( this );
                    d.modal( 'hide' );
                    d.data( 'modal', null );
                    d.html( '' );
                    d.remove();
                });
            });
        } else
            bootbox.alert( 'Редактирование документа запрещено!' );
    } else {
        bootbox.alert( 'Выберите элемент!' );
    }
}

//сохранение нового элемента
function saveA( btn, table ){
    $( btn ).attr( 'disabled', 'disabled' );
    
    upd = false;//typeof( id ) != 'undefined';
    dialog = $( btn ).parents( '#dialog_' + ( upd ? 'edit' : 'add' ) + '-' + table );
    form = $( dialog ).find( 'form' );
    isSubDoc = $( form ).find( 'input[name=documentid]' ).exists();
    if ( isSubDoc ){
        parentForm = $( 'form_add-d_' + table.substring( 2 ) ) || $( 'form_edit-d_' + table.substring( 2 ) ) || null;
    }
    
    if ( table != 'd_order' && table.indexOf( 'd_', 0 ) == 0 || table == 's_calculations' ){        
        $( form ).find( 'input[name=conduct]' ).val( '0' );
        if ( $( '#table-t_' + table.substring( 2 ) ).length > 0 ){
            $( form ).find( 'input[name=t_' + table.substring( 2 ) + ']' ).val( JSON.stringify( $( '#table-t_' + table.substring( 2 ) ).myTreeView( 'getData' ) ) );
        }
    }
    
    canSave = true;
    a = $( form ).find( 'input[required]' );
    for ( var i = 0; i < a.length; i++ ){
        if ( $( a[i] ).val() == '' ) canSave = false;
        else if ( $( a[i] ).attr( 'name' ) == 'quantity' && parseFloat( $( a[i] ).val() ) == 0 ) canSave = false;
    }

    if ( canSave ){
        $( form ).ajaxSubmit( function ( data ){
            console.log( data );
            data = $.parseJSON( data );

            if ( data['rescode'] == 1 ){
                bootbox.alert( data['resmsg'] );
                return;
            }

            if ( $( '#table-' + table ).exists() )
                $( '#table-' + table ).myTreeView( 'append', { data: data.row } );

            if ( isSubDoc ){//( table.indexOf( 't_', 0 ) == 0 && table != 't_calculations' ){
                $( parentForm ).find( '#total' ).val( data.total );

                if ( table == 't_inventory' ){
                    $( parentForm ).find( '#totalplanned' ).val( data.totalplanned );
                    $( parentForm ).find( '#totaldiff' ).val( data.totaldiff );
                }
                
                if ( $( form ).find( 'input[name=documentid]' ).val() != '0' ){//( $( '#form_edit-d_' + table.substring( 2 ) ).length > 0 ){
                    if ( $( parentForm ).find( 'input[name=conduct]' ).val() == '1' )
                        $( parentForm ).find( 'input[name=modified]' ).val( '1' );
                }
            }

            $( form ).clearForm();
            $( dialog ).modal( 'hide' );    
            $( dialog ).data( 'modal', null );
        });
    } else {
        $( btn ).removeAttr( 'disabled' );
        bootbox.alert( 'Заполните отмеченные поля формы!' );
    }
}

//проведение нового документа
function conductA( btn, table ){
    $( btn ).attr( 'disabled', 'disabled' );
    
    upd = false;//typeof( id ) != 'undefined';
    dialog = $( btn ).parents( '#dialog_' + ( upd ? 'edit' : 'add' ) + '-' + table );
    form = $( dialog ).find( 'form' );
    
    //короче вся разниц в этой фигне от  saveA, тут 2 ставится а там нет
    if ( table != 'd_order' && table.indexOf( 'd_', 0 ) == 0 ){
        $( form ).find( 'input[name=conduct]').val( '2' );
        if ( $( '#table-t_' + table.substring( 2 ) ).length > 0 ){
            $( form ).find( 'input[name=t_' + table.substring( 2 ) + ']' ).val( JSON.stringify( $( '#table-t_' + table.substring( 2 ) ).myTreeView( 'getData' ) ) );
        }
    }

    canSave = true;
    a = $( form ).find( 'input[required]' );
    for ( var i = 0; i < a.length; i++ ){
        if ( $( a[i] ).val() == '' ) canSave = false;
    }

    if ( canSave ){
        $( form ).ajaxSubmit( function ( data ){ 
            console.log( data );
            data = $.parseJSON( data );

            if ( data['rescode'] == 1 ){
                bootbox.alert( data['resmsg'] );
                return;
            }
            if($('#table-' + table).exists()){
                $( '#table-' + table ).myTreeView( 'append', { data: data.row } );
            }
            $( form ).clearForm();
            $( dialog ).modal( 'hide' );        
            $( dialog ).data( 'modal', null );
        });
    } else {
        $( btn ).removeAttr( 'disabled' );
        bootbox.alert( 'Заполните отмеченные поля формы!' );
    }
}

//сохранение редактирования
function saveA1( btn, table, id ){
    $( btn ).attr( 'disabled', 'disabled' );
    
    upd = typeof( id ) != 'undefined';
    dialog = $( btn ).parents( '#dialog_' + ( upd ? 'edit' : 'add' ) + '-' + table );
    form = $( dialog ).find( 'form' );

    if ( table != 'd_order' && table.indexOf( 'd_', 0 ) == 0 || table == 's_calculations' ){
        $( form ).find( 'input[name=conduct]' ).val( '0' );
        if ( $( '#table-t_' + table.substring( 2 ) ).length > 0 ){
            $( form ).find( 'input[name=t_' + table.substring( 2 ) + ']' ).val( JSON.stringify( $( '#table-t_' + table.substring( 2 ) ).myTreeView( 'getData' ) ) );
        }
    }
    
    canSave = true;
    a = $( form ).find( 'input[required]' );
    for ( var i = 0; i < a.length; i++ ){
        if ( $( a[i] ).val() == '' ) canSave = false;
        else if ( $( a[i] ).attr( 'name' ) == 'quantity' && parseFloat( $( a[i] ).val() ) == 0 ) canSave = false;
    }
    docModified = canSave && table.indexOf( 'd_', 0 ) == 0 && table != 'd_order' && $( form ).find( 'input[name=modified]' ).val() == '1';

    if ( docModified ){
        bootbox.confirm( "Документ будет перепроведен. Сохранить изменения?", function ( r ){
            if ( r ){
                $( form ).ajaxSubmit( function ( data ){
                    console.log( data );
                    data = $.parseJSON( data );

                    if ( data['rescode'] == 1 ){
                        bootbox.alert( data['resmsg'] );
                        return;
                    }

                    rowdata =  $( '#table-' + table ).myTreeView( 'getSelected' );
                    rowdata = $.extend( rowdata, data.row );

                    //if ( parseInt( rowdata['op'] ) == 0 ){
                        rowdata['op'] = 2;
                    //}

                    $( '#table-' + table ).myTreeView( 'update', {
                        id: id,
                        data: rowdata
                    });

                    $( form ).clearForm();
                    $( dialog ).modal( 'hide' );   
                    $( dialog ).data( 'modal', null );
                });
            } else
                $( btn ).removeAttr( 'disabled' );
        });
    } else if ( canSave ){
        $( form ).ajaxSubmit( function ( data ){
            console.log( data );
            data = $.parseJSON( data );

            if ( data['rescode'] == 1 ){
                bootbox.alert( data['resmsg'] );
                return;
            }

            rowdata =  $( '#table-' + table ).myTreeView( 'getSelected' );
            rowdata = $.extend( rowdata, data.row );

            if ( parseInt( rowdata['op'] ) == 0 ){
                rowdata['op'] = 2;
            }

            $( '#table-' + table ).myTreeView( 'update', {
                id: id,
                data: rowdata
            });

            if ( table.indexOf( 't_', 0 ) == 0 && table != 't_calculations' ){
                if ( $( form ).find( 'input[name=documentid]' ).val() != '0' ){//( $( '#form_edit-d_' + table.substring( 2 ) ).length > 0 ){
                    if ( $( '#form_edit-d_' + table.substring( 2 ) + ' input[name=conduct]' ).val() == '1' )
                        $( '#form_edit-d_' + table.substring( 2 ) + ' input[name=modified]' ).val( '1' );
                }
                
                $( form ).find( '#total' ).val( data.total );
                if ( table == 't_inventory' ){
                    $( form ).find( '#totalplanned' ).val( data.totalplanned );
                    $( form ).find( '#totaldiff' ).val( data.totaldiff );
                }
            }

            $( form ).clearForm();
            $( dialog ).modal( 'hide' );   
            $( dialog ).data( 'modal', null );
        });
    } else {
        $( btn ).removeAttr( 'disabled' );
        bootbox.alert( 'Заполните отмеченные поля формы!' );
    }
}

//проведение нового документа
function conductA1( btn, table, id ){
    $( btn ).attr( 'disabled', 'disabled' );  
    
    upd = typeof( id ) != 'undefined';
    dialog = $( btn ).parents( '#dialog_' + ( upd ? 'edit' : 'add' ) + '-' + table );
    form = $( dialog ).find( 'form' );
    
    if ( table != 'd_order' && table.indexOf( 'd_', 0 ) == 0 ){
        $( form ).find( 'input[name=conduct]' ).val( '2' );
        if ( $( '#table-t_' + table.substring( 2 ) ).length > 0 ){
            $( form ).find( 'input[name=t_' + table.substring( 2 ) + ']' ).val( JSON.stringify( $( '#table-t_' + table.substring( 2 ) ).myTreeView( 'getData' ) ) );
        }
    }
    
    canSave = true;
    a = $( form ).find( 'input[required]' );
    for ( var i = 0; i < a.length; i++ ){
        if ( $( a[i] ).val() == '' ) canSave = false;
    }

    if ( canSave ){
        $( form ).ajaxSubmit( function ( data ){
            console.log( data );
            data = $.parseJSON( data );

            id = $( form ).find( 'input[name=id]' ).val();
            $( '#table-' + table ).myTreeView( 'update', {
                id: id,
                data: data.row
            });

            $( form ).clearForm();
            $( dialog ).modal( 'hide' );   
            $( dialog ).data( 'modal', null );
        });
    } else {
        $( btn ).removeAttr( 'disabled' );
        bootbox.alert( 'Заполните отмеченные поля формы!' );
    }
}

//Создание документа на основании
function createBasedA( baseDoc, id, newDoc ){    
    if( $( "#dialog_add-" + newDoc ).exists() ) {
        var d = $( '#dialog_add-' + newDoc );
        d.modal( 'hide' );
        d.data( 'modal', null );
        d.html( '' );
        d.remove();
    }
    
    if ( id == -1 ) id = $( '#table-' + baseDoc ).myTreeView( 'getSelected' ).id;
    
    $.ajax({
        url: '/company/warehouse/warehouse.php?do=create_based_doc&basedoc=' + baseDoc + '&id=' + id + '&newdoc=' + newDoc 
    }).success( function ( form ){
        $( "body" ).append( form );   
        $( '#dialog_add-' + newDoc ).modal( 'show' );

        $('#dialog_add-' + newDoc).on( 'hidden.bs.modal', function (){
            var d = $( this );
            d.modal( 'hide' );
            d.data( 'modal', null );
            d.html( '' );
            d.remove();
        });
    });
}

function ZConductInventoy( baseDoc, id ){    
    $.ajax({
        url: '/company/warehouse/warehouse.php?do=ZConductInventoy&basedoc=' + baseDoc + '&id=' + id 
    }).success( function ( message ){
        alert(message)
    });
}

//Вывод окна удаления
function deleteA( table, mode ){    
    if ( $( '#table-' + table ).myTreeView( 'getSelected' ) != null ){
        id = $( '#table-' + table ).myTreeView( 'getSelected' ).id || null;
        op = parseInt( $( '#table-' + table ).myTreeView( 'getSelected' ).op );
        
        if ( op != 1 ){
            $.post( '/company/warehouse/warehouse.php?do=getcounts&table=' + table, { id: id } ).success( function ( dataz ) {
                if ( dataz == 0 ){
                    var name = '';

                    if ( table == 's_calculations' ){
                        name = 'калькуляцию блюда ' + $( '#table-' + table ).myTreeView( 'getSelected' ).itemid;
                    } else if ( table != 'd_order' && table.substring( 0, 2 ) == 'd_' ){
                        docType = $( '#tab_' + table + ' h4' ).html();
                        docCode = $( '#table-' + table ).myTreeView( 'getSelected' ).idlink;
                        docDate = $( '#table-' + table ).myTreeView( 'getSelected' ).dt;
                        name = 'документ "' + docType + ' ' + docCode + '" от ' + docDate;//$( '#table-' + table ).treegrid( 'getSelected' ).name;
                    } else if ( table == 't_regrading' ){
                        name = $( '#table-' + table ).myTreeView( 'getSelected' ).srcitemid;
                    } else
                        name = $( '#table-' + table ).myTreeView( 'getSelected' ).itemid; 
                    
                    bootbox.confirm( "Вы действительно хотите удалить " + name + "?", function ( r ){
                        if ( r ){
                            if ( table.substring( 0, 2 ) == 't_' ){
                                if ( table == 't_calculations' )
                                    subtable = 's_calculations';
                                else
                                    subtable = 'd_' + table.substring( 2 );
                
                                if ( $( '#form_edit-' + subtable ).length > 0 ){
                                    if ( $( '#form_edit-' + subtable + ' input[name=conduct]' ).val() == '1' )
                                        $( '#form_edit-' + subtable + ' input[name=modified]' ).val( '1' );
                                }

                                $( '#form_edit-' + subtable + ' input[name=conduct]' ).val( '0' );
                                deleted = $( '#form_edit-' + subtable + ' input[name=' + table + '_deleted]' ) || null;
                                if ( deleted != null ){
                                    s = deleted.val();
                                    if ( s == '' )  s = '' + id;
                                    else s += ';' + id;
                                    deleted.val( s );
                                }
                                $( '#table-' + table ).myTreeView( 'remove', id );
                            } else {
                                $.post( '/company/warehouse/warehouse.php?do=delete', { id: id, table: table } ).success( function ( dataz ) {
                                    dataz = $.parseJSON( dataz );
                                    $( '#table-' + table ).myTreeView( 'remove', id );
                                    bootbox.alert( dataz.result );
                                });
                            }
                        }
                    });
                } else {
                    bootbox.alert( 'Невозможно удалить документ, так как он был проведен!' );
                }
            });
        } else {
//ЕСЛИ УДАЛЕНИЕ ИЗ НОВОГО ДОКУМЕНТА - УДАЛИТЬ ТОЛЬКО ИЗ ГРИДА
//ИЗ СУЩЕСТВУЮЩЕГО - НА СЕРВЕРЕ
            $( '#table-' + table ).myTreeView( 'remove', id );
        }
    } else {
        bootbox.alert( 'Выберите элемент!' );
    }
}
//неведомая
function groupConduct(){
    $.ajax({ 
        type: "POST",
        url: "/company/warehouse/warehouse.php?do=conductOrders"
    }).success( function ( data ){
        data = $.parseJSON( data );
        bootbox.alert( data.resultCode + '. ' + data.resultDescription );
    });
}

function showBarcodeSeeker( elem ){
    if( $( "#dialog_barcodeSeeker" ).exists() ) {
        d = $( '#dialog_barcodeSeeker' );
        d.modal( 'hide' );
        d.data( 'modal', null );
        d.html( '' );
        d.remove();
    }
    
    sender = $( elem ).parents( 'form' ).attr( 'id' );
    
    $.ajax({ 
        type: "POST",
        url: '/company/warehouse/warehouse.php?do=barcodeSeeker',
        data: { sender: sender }
    }).success( function ( form ){
        $( "body" ).append( form );   
        $( '#dialog_barcodeSeeker' ).modal( 'show' );
        
        $( '#dialog_barcodeSeeker input[name=barcode]' ).die( 'keypress' );
        $( '#dialog_barcodeSeeker input[name=barcode]' ).live( 'keypress', function ( e ){
            if ( e.which == 13 ) {
                e.preventDefault();
                $( '#dialog_barcodeSeeker #seekBtn' ).click();
            }
        });

        $('#dialog_barcodeSeeker' ).on( 'hidden.bs.modal', function (){
            $( this ).modal( 'hide' );
            $( this ).data( 'modal', null );
            $( this ).html( '' );
            $( this ).remove();
        });
    });
}

function seekBarcode( elem ){
    $( '#form_barcodeSeeker').ajaxSubmit( function ( data ){
        console.log( data );
        data = $.parseJSON( data );
        
        if ( data.itemid == 0 ){
            bootbox.alert( data.item );
        } else {
            form = $( '#' + elem );//$( '#' + table + '_itemid' ).parent().parent().parent().attr('id');
            table = elem.substring( elem.indexOf( '-' ) + 1 );
            form.find( 'input[name=itemid]' ).val( data.itemid );// $( '#' + form + ' input[name=itemid]' ).val( data.itemid );
            form.find( '#' + table + '_itemid' ).val( data.item );// $( '#' + form + ' #' + table + '_itemid' ).val( data.item );
            form.find( 'input[name=specificationid]' ).val( data.specificationid );// $( '#' + form + ' input[name=specificationid]' ).val( data.specificationid );
            form.find( '#' + table + '_specificationid' ).val( data.specification );// $( '#' + form + ' #' + table + '_specificationid' ).val( data.specification );
            form.find( 'input[name=measureid]' ).val( data.measureid );// $( '#' + form + ' input[name=measureid]' ).val( data.measureid );
            form.find( '#' + table + '_measureid' ).val( data.measure );// $( '#' + form + ' #' + table + '_measureid' ).val( data.measure );
            form.find( 'input[name=price]' ).val( data.price );// $( '#' + form + ' input[name=price]' ).val( data.price );
            
            getMultip( elem );

            $( '#form_barcodeSeeker' ).clearForm();
            $( '#dialog_barcodeSeeker' ).modal( 'hide' );   
            $( '#dialog_barcodeSeeker' ).data( 'modal', null );
        }
    });
}

function getMeasure( elem ){
    form = $( elem ).parents( 'form' );
    id = form.attr( 'id' );
    table = id.substring( id.indexOf( '-' ) + 1 );
    inpitem = 'itemid';
    inpmeasure = 'measureid';
    
    if ( table == 't_regrading' ){
        inpitem = $( elem ).attr( 'name' );
        if ( inpitem.substr( 0, 3 ) == 'src' )
            inpmeasure = 'srcmeasureid';
        else
            inpmeasure = 'destmeasureid';
        
        inpitem2 = 'destitemid';
        pref = 'dest';
        if ( inpitem == 'destitemid' ){
            inpitem2 = 'srcitemid';
            pref = 'src';
        }
        
        id1 = form.find( 'input[name=' + inpitem +']' ).val();
        id2 = form.find( 'input[name=' + inpitem2 +']' ).val();
        
        if ( id1 == id2 ){
            form.find( 'input[name=' + inpitem +']' ).val( '' );
            form.find( 'input#t_regrading_' + pref + 'itemid' ).val( '' );
            
            bootbox.alert( 'Нельзя списать и оприходовать один и тот же товар!' );
            
            return;
        }
    }
    
    var iId = form.find( 'input[name=' + inpitem +']' ).val();
    var mId = '';// form.find( 'input[name=measureid]' ).val();
    if ( iId != '' && mId == '' ){
        $.ajax({
            type: "POST",
            url: "/company/warehouse/warehouse.php?do=getMeasure",
            data: {
                itemid: iId
            }
        }).success( function ( data ){
            data = data || null;
            if ( data != null ){
                data = $.parseJSON( data );
                form.find( 'input[name=' + inpmeasure +']' ).val( data.id );
                form.find( '#' + table + '_'  + inpmeasure ).val( data.name );
                
                if ( table != 's_calculations' && table != 't_regrading' )
                    getMultip( elem );
            }
        });
    }
}

function getRemains( table, mode ){
    docid=$("#form_add-d_inventory input[name=id]" ).val();
    if (docid==undefined) docid=$("#form_edit-d_inventory input[name=id]" ).val();
    
    dt=$("#form_add-d_inventory input[name=dt]" ).val();
    if (dt==undefined) dt=$("#form_edit-d_inventory input[name=dt]" ).val();
    
    warehouse=$("#form_add-d_inventory input[name=warehouseid]" ).val();
    if (warehouse==undefined) warehouse=$("#form_edit-d_inventory input[name=warehouseid]" ).val();
    
    url = "/company/warehouse/warehouse.php?do=get&table=t_inventory&remains=" + mode + "&warehouse=" + warehouse+"&zdocid="+docid+'&dt='+dt;
    
    console.log(url);
    $('#table-t_inventory').myTreeView( 'changeUrl', url );
    $('#table-t_inventory').myTreeView( 'reload' );
}

//заполнить по остаткам
function getReRemains(table){

    //grid=JSON.stringify( $( '#table-t_' + table.substring( 2 ) ).myTreeView( 'getData' ));

    dt=$("#form_add-d_inventory input[name=dt]" ).val();
    if (dt==undefined) dt=$("#form_edit-d_inventory input[name=dt]" ).val();
    
    docid=$("#form_add-d_inventory input[name=id]" ).val();
    if (docid==undefined) docid=$("#form_edit-d_inventory input[name=id]" ).val();
    
    warehouse=$("#form_add-d_inventory input[name=warehouseid]" ).val();
    warehouse=$("#form_add-d_inventory input[name=warehouseid]" ).val();
    if (warehouse==undefined) warehouse=$("#form_edit-d_inventory input[name=warehouseid]" ).val();
    
    url = "/company/warehouse/warehouse.php?do=get&table=t_inventory&idfield=documentid&documentid="+docid+'&warehouse=' + warehouse+'&nolimit=topgear&zreset=1'+'&dt='+dt;
    
    console.log(url);
    $('#table-t_inventory').myTreeView( 'changeUrl', url );
    $('#table-t_inventory').myTreeView( 'reload' );
}

function submit_btn_warehouse( elem, title ){
    
    
    
    
    
    $('#loading').show();
    
    form = $( elem ).parents( 'form' );
    
//    t = false;
    errmsg = '';
    
//    if ( title == 'Отчет по калькуляциям блюд' ){
//        t = parseInt( form.find( 'input[name=itemid]' ).val() ) > 0;
//        errmsg = 'Выберите блюдо или группу блюд!';
//    }
    t = true;
    if ( t ){
        $( form ).ajaxSubmit( function ( data ){  
//            bootbox.dialog({
//                message: data,
//                title: title,
//                className: 'zdialog',
//                buttons: {
//                    close: {
//                        label: "Закрыть",
//                        className: "btn-primary",
//                        callback: function() { return true; }
//                    }
//                }
//            });
            $( form ).parents( '.wndw' ).children( '.result' ).html( data );

            $( '#loading' ).hide();
        });
    } else {
        $( '#loading' ).hide();
        bootbox.alert( errmsg );
    }
}

function cancelConductA( btn, table, id ){
    bootbox.confirm( "Вы действительно хотите отменить проведение документа?", function ( r ){
        if ( r ){
            $( btn ).attr( 'disabled', 'disabled' ); 
            id = $( '#table-' + table ).myTreeView( 'getSelected' ).id;
            $.ajax({
                type: "POST",
                url: "/company/warehouse/warehouse.php?do=cancelConduct",
                data: {
                    table: table,
                    id: id
                }
            }).success( function ( data ){
                data = $.parseJSON( data );
                if ( data.code == 0 ){
                    rowdata = $( '#table-' + table ).myTreeView( 'getSelected' );
                    rowdata['conducted'] = 'Нет';
                    $( '#table-' + table ).myTreeView( 'update', {
                        id: rowdata['id'],
                        data: rowdata
                    });
                }
                $( btn ).removeAttr( 'disabled' );
                bootbox.alert( data.msg );
            });
        }
    });
}
//пересчитывает в форме
function calcLoss( elem ){
    form = $( elem ).parents( 'form' );
    name = $( elem ).attr( 'name' );
    if ( name == 'quantity' ) name = 'loss_cold';
    id = name + '_quantity';
    lossp = parseFloat( form.find( 'input[name=' + name + ']' ).val() );
    quantity = parseInt( form.find( 'input[name=quantity]' ).val() );
    
    if ( name == 'loss_cold' ){
        loss = quantity - quantity * lossp / 100;
        form.find( '#' + id ).val( loss.toFixed( 3 ) );

        lossp = parseFloat( form.find( 'input[name=loss_hot]' ).val() );
        loss = loss - loss * lossp / 100;
        form.find( '#loss_hot_quantity' ).val( loss.toFixed( 3 ) );
    } else {
        loss = quantity - quantity * lossp / 100;
        form.find( '#' + id ).val( loss.toFixed( 3 ) );
    }
}
//неведомая
function remainsReport(){
    $.post( '/company/warehouse/warehouse.php?do=remains', { chb: 'zasmenu', chb_zasmenu: 0, warehouseid: 0 } ).success( function ( dataz ) {
        console.log( dataz );
    });
}