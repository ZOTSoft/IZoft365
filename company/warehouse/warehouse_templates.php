<?php
//Материальная ведомость
$template['gethtml_remains']=<<<HTML
<div class="wndw">
    <div class="wn1">
        <form id="remainsreport" action="/company/warehouse/warehouse.php?do=remains" method="post">
            <div class="tobj1"><h4>Склад:</h4></div> 
            <div class="tobj2"><select name="warehouseid" class="form-control loaded_idautomated_point"></select></div>
            <div class="clear"></div>
           
            <div class="leftm">
                <fieldset>
                    <legend>Параметры</legend>   
                     <div class="checkd1">Торговая точка:</div> 
            <div class="inpd1"><select id="remainsapid" class="form-control loaded_idautomated_point"  onchange="filterOnIdAutomatedPointChange(this)"></select></div>
            <div class="clear"></div>
                    <!--  ##### -->
                    <div class="checkd1"><input type="radio" name="chb" checked="checked"  value="zasmenu" class="zasmenu" checked> за смену</div>
                    <div class="inpd1"><select name="chb_zasmenu" class="form-control loaded_chb_zasmenu" onchange="onChangeChbZaSmenu(this)"></select></div>
                    <div class="clear"></div>
                    <div class="titd1">Дата <br />открытия</div>
                    <div class="inpd2"><input disabled="disabled" class="form-control datestart"></div>

                    <div class="titd2">Дата <br />закрытия</div>
                    <div class="inpd2"><input disabled="disabled" class="form-control dateend"></div>
                    <!--  ##### -->
        
                    <div class="separ"></div>

                    <!--  ##### -->
                    <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod"> За период с</div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>    
                    </div>
                    <div class="clear"></div>
                    <div class="titd1">по </div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <!--  ##### -->
                     <!--  #####
                    <div class="checkbd1" ><input type="checkbox" name="groupbyclient" value="1"> Группировать по клиентам</div>
                    ##### --> 
                </fieldset>
            </div>
           <div class="rightm">
           <fieldset>
                    <legend>Фильтры</legend>
                       

           <!--  ##### -->
            <div class="tobj2">Товар или группа товаров: <input type="checkbox" value="1" name="notitem">НЕ</div> 
            <div class="tobj2">  
                <div class="input-group">
                    <input id="s_items_name2" class="form-control" onkeyup="oninputchange( event, this, 's_items' );" onblur="getlastvalue(this)" sval=""  > 
                    <input name="itemid" type="hidden">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default" tabindex="-1" onclick="show_dbselect_window('s_items', 's_items_name2', 'name')">...</button>
                        <button type="button" class="btn btn-default" tabindex="-1" onclick="clear_db_select(this); return false;"> X </button>
                    </div>
                </div>  
            </div>
                            <!--  ##### -->
                                            </fieldset>
           </div>
            <div class="clear"></div>
            <a href="#" class="btn btn-primary" onclick="submit_btn(this, 'Материальная ведомость'); return false"><b>Сформировать</b></a> 
        </form>
    </div>
        <div class="result"></div>
</div>
<script>
$( document ).ready( function(){
    $( '.zdata' ).datetimepicker();
    wind = 'remainsreport';

    $.post( "warehouse/warehouse.php?do=getselectA", { table: 's_warehouse' } ).success( function( dataz ) {
        $( "#" + wind + ' select[name="warehouseid"]' ).html( dataz );
    });
        
    $.post("ajax.php?do=getselect", { table: 's_automated_point'}).success( function( dataz ) {
        $( '#remainsapid' ).html( dataz );
        
        id = $('#remainsapid option:selected').val();
        
        $.post( "ajax.php?do=getselect_changes", { table: 'd_changes', ap: id } ).success( function ( dataz ){
            $( '.loaded_chb_zasmenu' ).html( dataz ).removeClass( 'loaded_chb_zasmenu' );
        });
    
    });
    
    $( "#" + wind + ' .chb_zaperiod' ).click( function (){
        $( "#" + wind + " .zaperiod" ).attr( "checked", "checked" );
    });
});
</script>
HTML;
////Материальная ведомость

//Отчет по списанию
$template['gethtml_cancellation']=<<<HTML
<div class="wndw">
    <div class="wn1">
        <form id="remainsreport" action="/company/warehouse/warehouse.php?do=remains" method="post">
            <div class="leftm">
                <fieldset>
                    <legend>Параметры</legend>   
                    <!--  ##### -->
                    <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod" checked> За период с</div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>    
                    </div>
                    <div class="clear"></div>
                    <div class="titd1">по </div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <!--  ##### -->
                     
                    <div class="checkbd1" ><input type="checkbox" name="groupbywarehouse" value="1"> Группировать по складам</div>
                    
                </fieldset>
            </div>
           
            <div class="clear"></div>
            <a href="#" class="btn btn-primary" onclick="submit_btn(this, 'Материальная ведомость'); return false"><b>Сформировать</b></a> 
        </form>
    </div>
        <div class="result"></div>
</div>
<script>
$( document ).ready( function(){
    $( '.zdata' ).datetimepicker();
    wind = 'remainsreport';

    $.post( "warehouse/warehouse.php?do=getselectA", { table: 's_warehouse' } ).success( function( dataz ) {
        $( "#" + wind + ' select[name="warehouseid"]' ).html( dataz );
    });
        
    $.post("ajax.php?do=getselect", { table: 's_automated_point'}).success( function( dataz ) {
        $( '#remainsapid' ).html( dataz );
        
        id = $('#remainsapid option:selected').val();
        
        $.post( "ajax.php?do=getselect_changes", { table: 'd_changes', ap: id } ).success( function ( dataz ){
            $( '.loaded_chb_zasmenu' ).html( dataz ).removeClass( 'loaded_chb_zasmenu' );
        });
    
    });
    
    $( "#" + wind + ' .chb_zaperiod' ).click( function (){
        $( "#" + wind + " .zaperiod" ).attr( "checked", "checked" );
    });
});
</script>
HTML;
////Отчет по списанию

//Движения товаров
$template['gethtml_remainsdetailed']=<<<HTML
<div class="wndw">
    <div class="wn1">
        <form id="remainsreport" action="/company/warehouse/warehouse.php?do=remainsdetailed" method="post">
            <div class="tobj1"><h4>Товар:</h4></div> 
            <div class="tobj2">
        
<div class="input-group">
<input id="s_items_name" class="form-control" onkeyup="oninputchange( event, this, 's_items' );" onblur="getlastvalue(this)" sval="{vitemname}" value="{vitemname}" > 
<input name="itemid" type="hidden" value="{vitemid}">
<div class="input-group-btn">
<button type="button" class="btn btn-default" tabindex="-1" onclick="show_dbselect_window('s_items', 's_items_name')">...</button>
<button type="button" class="btn btn-default" tabindex="-1" onclick="clear_db_select(this); return false;"> X </button>
</div>
</div>
        
            </div>
            <div class="clear"></div>
            <div class="tobj1"><h4>Склад:</h4></div> 
            <div class="tobj2">
        
<div class="input-group">
<input id="s_warehouse_name" class="form-control" onkeyup="oninputchange( event, this, 's_warehouse' );" onblur="getlastvalue(this)" sval="{vwarehousename}" value="{vwarehousename}" > 
<input name="warehouseid" type="hidden" value="{vwarehouseid}">
<div class="input-group-btn">
<button type="button" class="btn btn-default" tabindex="-1" onclick="show_dbselect_window('s_warehouse', 's_warehouse_name')">...</button>
<button type="button" class="btn btn-default" tabindex="-1" onclick="clear_db_select(this); return false;"> X </button>
</div>
</div>        
        
            </div>
            <div class="clear"></div>
            <div class="leftm">
                <fieldset>
                    <legend>Параметры</legend>   
        
                    <!--  ##### -->
                    <div class="checkd1"><input type="radio" name="chb" checked="checked"  value="zasmenu" class="zasmenu" checked> за смену</div>
                    <div class="inpd1"><select name="chb_zasmenu" class="form-control loaded_chb_zasmenu" onchange="onChangeChbZaSmenu(this)"></select></div>
                    <div class="clear"></div>
                    <div class="titd1">Дата <br />открытия</div>
                    <div class="inpd2"><input disabled="disabled" class="form-control datestart"></div>

                    <div class="titd2">Дата <br />закрытия</div>
                    <div class="inpd2"><input disabled="disabled" class="form-control dateend"></div>
                    <!--  ##### -->
        
                    <div class="separ"></div>

                    <!--  ##### -->
                    <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod" {dtcheck}> За период с</div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" {dtstart} />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>    
                    </div>
                    <div class="clear"></div>
                    <div class="titd1">по </div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" {dtend} />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <!--  ##### -->
                </fieldset>
                
            </div>
            <div class="clear"></div>
            <a href="#" class="btn btn-primary" onclick="submit_btn(this, 'МДвижение товаров'); return false"><b>Сформировать</b></a> 
        </form>
    </div>
        <div class="result">{result}</div>
</div>
<script>
$(document).ready( function(){
    $('.zdata').datetimepicker();
        
    $.post( "warehouse/warehouse.php?do=getselectA", { table: 'd_changes' } ).success( function ( dataz ){
        $( '.loaded_chb_zasmenu' ).html( dataz ).removeClass( 'loaded_chb_zasmenu' );
    });
});
</script>
HTML;
////Движения товаров


//Анализ продаж
$template['gethtml_anal_sale']=<<<HTML
<div class="wndw">
    <div class="wn1">
        <form id="analreport" action="/company/warehouse/warehouse.php?do=salesanalysis" method="post">
            <div class="tobj1"><h4>Склад:</h4></div> 
            <div class="tobj2"><select name="warehouseid" class="form-control loaded_idautomated_point"></select></div>
            <div class="clear"></div>
            
            <div class="tobj1"><h4>Тип документа:</h4></div> 
            <div class="tobj2" style="text-align:left">
                <input type="checkbox" value=1 name="documenttype0"> Счет заказы <br />
                <input type="checkbox" value=1 name="documenttype1"> Поступление товаров <br />
                <input type="checkbox" value=1 name="documenttype2"> Реализация товаров <br />
                <input type="checkbox" value=1 name="documenttype3"> Оприходование <br />
                <input type="checkbox" value=1 name="documenttype4"> Списание <br />
                <input type="checkbox" value=1 name="documenttype5"> Инвентаризация <br />
                <input type="checkbox" value=1 name="documenttype6"> Перемещение <br />
                <input type="checkbox" value=1 name="documenttype7"> Выпуск продукции <br />
            </div>
            <div class="clear"></div>
            
            <div class="leftm">
                <fieldset>
                    <legend>Параметры</legend>   
                    
                    <!--  ##### -->
                    <div class="checkd1"><input type="radio" name="chb" checked="checked"  value="zasmenu" class="zasmenu" checked> за смену</div>
                    <div class="inpd1"><select name="chb_zasmenu" class="form-control loaded_chb_zasmenu" onchange="onChangeChbZaSmenu(this)"></select></div>
                    <div class="clear"></div>
                    <div class="titd1">Дата <br />открытия</div>
                    <div class="inpd2"><input disabled="disabled" class="form-control datestart"></div>

                    <div class="titd2">Дата <br />закрытия</div>
                    <div class="inpd2"><input disabled="disabled" class="form-control dateend"></div>
                    <!--  ##### -->
        
                    <div class="separ"></div>

                    <!--  ##### -->
                    <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod"> За период с</div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>    
                    </div>
                    <div class="clear"></div>
                    <div class="titd1">по </div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <!--  ##### -->
                </fieldset>
            </div>
            <div class="clear"></div>
            <a href="#" class="btn btn-primary" onclick="submit_btn(this, 'Анализ продаж'); return false"><b>Сформировать</b></a> 
        </form>
    </div>
        <div class="result"></div>
</div>
<script>
$(document).ready( function(){
    $('.zdata').datetimepicker();
    wind = 'analreport';

    $.post( "warehouse/warehouse.php?do=getselectA", { table: 's_warehouse' } ).success( function( dataz ) {
        $( "#" + wind + ' select[name="warehouseid"]' ).html( dataz );
    });
        
    $.post( "warehouse/warehouse.php?do=getselectA", { table: 'd_changes' } ).success( function ( dataz ){
        $( '.loaded_chb_zasmenu' ).html( dataz ).removeClass( 'loaded_chb_zasmenu' );
    });
    
    $( "#" + wind + ' .chb_zaperiod' ).click( function (){
        $( "#" + wind + " .zaperiod" ).attr( "checked", "checked" );
    });
});
</script>
HTML;
////Анализ продаж


$template['gethtml_anal_sale2']=<<<HTML
<div class="wndw">
    <div class="wn1">
        <form id="analreport" action="/company/warehouse/warehouse.php?do=salesanalysis2" method="post">
            <div class="tobj1"><h4>Склад:</h4></div> 
            <div class="tobj2"><select name="warehouseid" class="form-control loaded_idautomated_point"></select></div>
            <div class="clear"></div>

            <div class="clear"></div>
            
            <div class="leftm">
                <fieldset>
                    <legend>Параметры</legend>   
                    
                    <!--  ##### -->
                    <div class="checkd1"><input type="radio" name="chb" checked="checked"  value="zasmenu" class="zasmenu" checked> за смену</div>
                    <div class="inpd1"><select name="chb_zasmenu" class="form-control loaded_chb_zasmenu" onchange="onChangeChbZaSmenu(this)"></select></div>
                    <div class="clear"></div>
                    <div class="titd1">Дата <br />открытия</div>
                    <div class="inpd2"><input disabled="disabled" class="form-control datestart"></div>

                    <div class="titd2">Дата <br />закрытия</div>
                    <div class="inpd2"><input disabled="disabled" class="form-control dateend"></div>
                    <!--  ##### -->
        
                    <div class="separ"></div>

                    <!--  ##### -->
                    <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod"> За период с</div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>    
                    </div>
                    <div class="clear"></div>
                    <div class="titd1">по </div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <!--  ##### -->
                </fieldset>
            </div>
            <div class="clear"></div>
            <a href="#" class="btn btn-primary" onclick="submit_btn(this, 'Анализ продаж'); return false"><b>Сформировать</b></a> 
        </form>
    </div>
        <div class="result"></div>
</div>
<script>
$(document).ready( function(){
    $('.zdata').datetimepicker();
    wind = 'analreport';

    $.post( "warehouse/warehouse.php?do=getselectA", { table: 's_warehouse' } ).success( function( dataz ) {
        $( "#" + wind + ' select[name="warehouseid"]' ).html( dataz );
    });
        
    $.post( "warehouse/warehouse.php?do=getselectA", { table: 'd_changes' } ).success( function ( dataz ){
        $( '.loaded_chb_zasmenu' ).html( dataz ).removeClass( 'loaded_chb_zasmenu' );
    });
    
    $( "#" + wind + ' .chb_zaperiod' ).click( function (){
        $( "#" + wind + " .zaperiod" ).attr( "checked", "checked" );
    });
});
</script>
HTML;
////Анализ продаж

//Групповое проведение счетов
$template['gethtml_conductor']=<<<HTML
<div class="wndw">
    <div class="wn1">
        <form id="remainsreport" action="/company/warehouse/warehouse.php?do=getOrdersList" method="post">
            <div class="tobj1"><h4>Торговая точка:</h4></div> 
            <div class="tobj2"><select name="idautomated_point" class="form-control loaded_idautomated_point" onchange="filterOnIdAutomatedPointChange(this)" ></select></div>
            <div class="clear"></div>
            <div class="leftm">
                <fieldset>
                    <legend>Параметры</legend>
        
                    <!--  ##### -->
                    <div class="checkd1"><input type="radio" name="chb" checked="checked"  value="zasmenu" class="zasmenu" checked> за смену</div>
                    <div class="inpd1"><select name="chb_zasmenu" class="form-control loaded_chb_zasmenu" onchange="onChangeChbZaSmenu(this)"></select></div>
                    <div class="clear"></div>
                    <div class="titd1">Дата <br />открытия</div>
                    <div class="inpd2"><input disabled="disabled" class="form-control datestart"></div>

                    <div class="titd2">Дата <br />закрытия</div>
                    <div class="inpd2"><input disabled="disabled" class="form-control dateend"></div>
                    <!--  ##### -->
        
                    <div class="separ"></div>
        
                    <!--  ##### -->
                    <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod"> За период с</div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>    
                    </div>
                    <div class="clear"></div>
                    <div class="titd1">по </div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <!--  ##### -->
        
                    <div class="separ"></div>
                        
                    <!--  ##### -->
                    <div class="checkbd1" ><input type="checkbox" name="reconduct" value="1"> Перепровести счета</div>
                    <!--  ##### --> 
                </fieldset>
            </div>
            <div class="clear"></div>
            <a href="#" class="btn btn-primary" onclick="show_progress(this, 'Групповое проведение счетов', 'warehouse/warehouse.php?do=conductOrders'); return false;"><b>Выполнить</b></a> 
        </form>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.zdata').datetimepicker();
    $.post("ajax.php?do=getselect", { table: 's_automated_point'}).success(function(dataz) {
        $('.loaded_idautomated_point').html(dataz);
        
        id=$('.loaded_idautomated_point option:selected').val();
        $('.loaded_idautomated_point').removeClass('loaded_idautomated_point');
        
        $.post("ajax.php?do=getselect_changes", { table: 'd_changes',ap:id}).success(function(dataz) {
            $('.loaded_chb_zasmenu').html(dataz).removeClass('loaded_chb_zasmenu');
        });
    
    }); 
});
</script>
HTML;
////Групповое проведение счетов

//"Типа отчет" калькуляция себестоимости блюд
$template['gethtml_tipaotchet']=<<<HTML
<div class="wndw">
    <div class="wn1">
        <form id="tipaotchetreport" action="/company/warehouse/warehouse.php?do=tipaotchet" method="post">
            <div class="leftm">
                <fieldset>
                    <legend>Параметры</legend>
                    <!--  ##### -->
                    <div class="form-group">
                        <label class="col-lg-4 control-label" style="padding-left: 0; padding-right: 0; margin-top: 5px; font-weight: 500; float: left;"> Блюдо (группа блюд):</label>
        
<div class="input-group">
<input id="tipaotchet_itemid" class="form-control" onkeyup="oninputchange( event, this, 's_items' );" onblur="getlastvalue(this)" sval="Все" value="Все" > 
<input name="itemid" type="hidden" value="0">
<div class="input-group-btn">
<button type="button" class="btn btn-default" tabindex="-1" onclick="show_dbselect_window('s_items', 'tipaotchet_itemid')">...</button>
<button type="button" class="btn btn-default" tabindex="-1" onclick="clear_db_select(this); return false;"> X </button>
</div>
</div>        
        
                    </div>
        
                    <div class="separ"></div>

                    <!--  ##### -->
        
                    <div><input type="radio" name="chb" class="zasmenu" value="avgprice" checked> По средневзвешенной стоимости</div>
                    <div><input type="radio" name="chb" class="zasmenu" value="lastprice"> По последнему поступлению</div>
        
                    <!--  ##### -->
        
                    <div class="separ"></div>
                    <!--  ##### -->
        
                    <div class="checkbd1" ><input type="checkbox" name="showIngredients" value="1"> Показывать ингредиенты</div>
                </fieldset>
            </div>
            <div class="clear"></div>
            <a href="#" class="btn btn-primary" onclick="submit_btn_warehouse(this, 'Отчет по калькуляциям блюд'); return false"><b>Сформировать</b></a> 
        </form>
    </div>
        <div class="result"></div>
</div>
<script>
$(document).ready( function(){
    wind = 'tipaotchetreport';
    
    $( "#" + wind + ' .chb_zaperiod' ).click( function (){
        $( "#" + wind + " .zaperiod" ).attr( "checked", "checked" );
    });
});
</script>
HTML;
////Групповое проведение счетов


//Перепроведение счетов
$template['gethtml_reconduct']=<<<HTML
<div class="wndw">
    <div class="wn1">
        <form id="remainsreport" action="/company/warehouse/warehouse.php?do=getReconductList" method="post">
            <div class="leftm">
                <fieldset>
                    <legend>Параметры</legend>
                    <!--  ##### -->
                    <div><input type="radio" name="chb" class="zaperiod" value="actual" checked> С точки актуальности <b>{actual_dt}</b></div>
        <div class="separ"></div>
                    <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod" > Начиная с даты</div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>    
                    </div>
                    <!--  ##### -->
                </fieldset>
            </div>
            <div class="clear"></div>
            <a href="#" class="btn btn-primary" onclick="show_progress(this, 'Перепроведение документов', 'warehouse/warehouse.php?do=reconduct'); return false"><b>Выполнить</b></a> 
        </form>
    </div>
</div>
<script>
$( document ).ready( function(){
    $( '.zdata' ).datetimepicker();
});
</script>
HTML;
////Перепроведение счетов

//Ведомость денежных средств
$template['gethtml_cash_remains']=<<<HTML
<div class="wndw">
    <div class="wn1">
        <form id="cash_remainsreport" action="/company/warehouse/warehouse.php?do=cash_remains" method="post">
            <div class="tobj1"><h4>Касса:</h4></div> 
            <div class="tobj2"><select name="cashid" class="form-control loaded_idautomated_point"></select></div>
            <div class="clear"></div>
            <div class="tobj1"><h4>Торговая точка:</h4></div> 
            <div class="tobj2"><select id="cash_remainsapid" class="form-control loaded_idautomated_point"  onchange="filterOnIdAutomatedPointChange(this)"></select></div>
            <div class="clear"></div>
            <div class="leftm">
                <fieldset>
                    <legend>Параметры</legend>   
        
                    <!--  ##### -->
                    <div class="checkd1"><input type="radio" name="chb" checked="checked"  value="zasmenu" class="zasmenu" checked> за смену</div>
                    <div class="inpd1"><select name="chb_zasmenu" class="form-control loaded_chb_zasmenu" onchange="onChangeChbZaSmenu(this)"></select></div>
                    <div class="clear"></div>
                    <div class="titd1">Дата <br />открытия</div>
                    <div class="inpd2"><input disabled="disabled" class="form-control datestart"></div>

                    <div class="titd2">Дата <br />закрытия</div>
                    <div class="inpd2"><input disabled="disabled" class="form-control dateend"></div>
                    <!--  ##### -->
        
                    <div class="separ"></div>

                    <!--  ##### -->
                    <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod"> За период с</div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>    
                    </div>
                    <div class="clear"></div>
                    <div class="titd1">по </div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <!--  ##### -->
                </fieldset>
            </div>
            <div class="clear"></div>
            <a href="#" class="btn btn-primary" onclick="submit_btn(this, 'Кассовая ведомость'); return false"><b>Сформировать</b></a> 
        </form>
    </div>
        <div class="result"></div>
</div>
<script>
$( document ).ready( function(){
    $( '.zdata' ).datetimepicker();
    wind = 'cash_remainsreport';

    $.post( "ajax.php?do=getselect", { table: 's_cash' } ).success( function( dataz ) {
        $( "#" + wind + ' select[name="cashid"]' ).html( dataz );
    });
        
    $.post("ajax.php?do=getselect", { table: 's_automated_point'}).success( function( dataz ) {
        $( '#cash_remainsapid' ).html( dataz );
        
        id = $('#cash_remainsapid option:selected').val();
        
        $.post( "ajax.php?do=getselect_changes", { table: 'd_changes', ap: id } ).success( function ( dataz ){
            $( '.loaded_chb_zasmenu' ).html( dataz ).removeClass( 'loaded_chb_zasmenu' );
        });
    
    });
    
    $( "#" + wind + ' .chb_zaperiod' ).click( function (){
        $( "#" + wind + " .zaperiod" ).attr( "checked", "checked" );
    });
});
</script>
HTML;
////Ведомость денежных средств

//Движения денежных средств
$template['gethtml_cash_remainsdetailed']=<<<HTML
<div class="wndw">
    <div class="wn1">
        <form id="cash_remainsreport" action="/company/warehouse/warehouse.php?do=cash_remainsdetailed" method="post">
            <div class="tobj1"><h4>Касса:</h4></div> 
            <div class="tobj2">
        
<div class="input-group">
<input id="s_cash_name" class="form-control" onkeyup="oninputchange( event, this, 's_cash' );" onblur="getlastvalue(this)" sval="{vcashname}" value="{vcashname}" > 
<input name="cashid" type="hidden" value="{vcashid}">
<div class="input-group-btn">
<button type="button" class="btn btn-default" tabindex="-1" onclick="show_dbselect_window('s_cash', 's_cash_name')">...</button>
<button type="button" class="btn btn-default" tabindex="-1" onclick="clear_db_select(this); return false;"> X </button>
</div>
</div>
        
            </div>
            <div class="clear"></div>
            <div class="leftm">
                <fieldset>
                    <legend>Параметры</legend>   
        
                    <!--  ##### -->
                    <div class="checkd1"><input type="radio" name="chb" checked="checked"  value="zasmenu" class="zasmenu" checked> за смену</div>
                    <div class="inpd1"><select name="chb_zasmenu" class="form-control loaded_chb_zasmenu" onchange="onChangeChbZaSmenu(this)"></select></div>
                    <div class="clear"></div>
                    <div class="titd1">Дата <br />открытия</div>
                    <div class="inpd2"><input disabled="disabled" class="form-control datestart"></div>

                    <div class="titd2">Дата <br />закрытия</div>
                    <div class="inpd2"><input disabled="disabled" class="form-control dateend"></div>
                    <!--  ##### -->
        
                    <div class="separ"></div>

                    <!--  ##### -->
                    <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod" {dtcheck}> За период с</div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" {dtstart} />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>    
                    </div>
                    <div class="clear"></div>
                    <div class="titd1">по </div>
                    <div class="inpd1">
                        <div class="dpicker">
                            <div class='input-group date zdata'>
                                <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" {dtend} />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                    <!--  ##### -->
                </fieldset>
            </div>
            <div class="clear"></div>
            <a href="#" class="btn btn-primary" onclick="submit_btn(this, 'Движение денежных средств'); return false"><b>Сформировать</b></a> 
        </form>
    </div>
        <div class="result">{result}</div>
</div>
<script>
$(document).ready( function(){
    $('.zdata').datetimepicker();
        
    $.post( "warehouse/warehouse.php?do=getselectA", { table: 'd_changes' } ).success( function ( dataz ){
        $( '.loaded_chb_zasmenu' ).html( dataz ).removeClass( 'loaded_chb_zasmenu' );
    });
});
</script>
HTML;
////Движения денежных средств
?>
