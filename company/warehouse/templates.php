<?php
$template=array();
include('warehouse/warehouse_templates.php');
$template['gethtml_itogovy']=<<<HTML
<div class="wndw">
    <div class="wn1">
    <form action="/company/ajax.php?do=otchet2" method="post" role="form">
    
        <div class="tobj1"><h4>Торговая точка</h4></div> 
        <div class="tobj2"><select name="idautomated_point"  class="form-control loaded_idautomated_point" onchange="filterOnIdAutomatedPointChange(this)" ></select></div>
        <div class="clear"></div>
        
        
        <div class="leftm">
        
            <div>
                <fieldset>
                    <legend>Параметры</legend>
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" checked="checked"  value="zasmenu" class="zasmenu"> за смену</div>
                        <div class="inpd1"><select name="chb_zasmenu" class="form-control loaded_chb_zasmenu" onchange="onChangeChbZaSmenu(this)"></select></div>
                        <div class="clear"></div>
                        <div class="titd1">Дата <br />открытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control datestart"></div>
                        
                        <div class="titd2">Дата <br />закрытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control dateend"></div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod"> за период с</div>
                        <div class="inpd1">
                            
                <div class="dpicker">
                    <div class='input-group date zdata'>
                        <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>


                            
                        </div>
                        <div class="clear"></div>
                        <div class="titd1">по </div>
                        <div class="inpd1">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd2"><input type="radio" class="smenperiod" name="chb" value="smenperiod">смены за период с</div>
                        <div class="inpd3">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod1" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="titd3">по</div>
                        <div class="inpd3">
                        <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        
                        
                        </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkbd1" style="display:none"><input type="checkbox" name="groupbydate"> Группировать по дате</div>
                        <!--  ##### -->
                        
                </fieldset>
                
            </div>
             
        </div>
        <div class="rightm">
            <div style="display:none"s>
                <fieldset>
                    <legend>Сортировать (отлично пашет!)</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="order" checked="checked" value="name"> Наименование</div>
                        <div><input type="radio" name="order" value="quantity"> Количество</div>
                        <div><input type="radio" name="order" value="price"> Цена</div>
                        <div><input type="radio" name="order" value="sumitem"> Сумма</div>
                        <div><input type="checkbox" name="orderdesc" value="orderdesc"> По убыванию</div>
                        <div><input type="checkbox" name="noorder" value="noorder"> Без сортировки</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:none">
                <fieldset>
                    <legend>Группировать</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="group"> По категориям</div>
                        <div><input type="radio" name="group"> По клиентам</div>
                        <div><input type="radio" name="group"> По принтерам</div>
                        <div><input type="radio" name="group"> Только по категориям клиентов</div>
                        <div><input type="radio" name="group"> По сменам</div>
                        <div><input type="radio" name="group"> По столам</div>
                        <div><input type="radio" name="group"> Не использовать группировку</div>
                        <!--  ##### -->
                </fieldset>
            </div>
             <div style="display:none">
                <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox"> Включать состав комплексов</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:none">
                 <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox" value="1" name="datailed"> Расширенные данные по счетам</div>
                        <!--  ##### -->
                </fieldset>
                </div>
        </div>
        <div class="clear"></div>
        <a href="#" class="btn btn-primary" onclick="submit_btn(this,'Итоговый отчет');return false"><b>Сформировать</b></a> 
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
$template['gethtml_posotr']=<<<HTML
<div class="wndw">
    <div class="wn1">
    <form action="/company/ajax.php?do=otchet&type=posotrudnikam" method="post" role="form">
    
        <div class="tobj1"><h4>Торговая точка</h4></div> 
        <div class="tobj2"><select name="idautomated_point"  class="form-control loaded_idautomated_point" onchange="filterOnIdAutomatedPointChange(this)" ></select></div>
        <div class="clear"></div>
        
        
        <div class="leftm">
        
            <div>
                <fieldset>
                    <legend>Параметры</legend>
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" checked="checked"  value="zasmenu" class="zasmenu"> за смену</div>
                        <div class="inpd1"><select name="chb_zasmenu" class="form-control loaded_chb_zasmenu" onchange="onChangeChbZaSmenu(this)"></select></div>
                        <div class="clear"></div>
                        <div class="titd1">Дата <br />открытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control datestart"></div>
                        
                        <div class="titd2">Дата <br />закрытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control dateend"></div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod"> за период с</div>
                        <div class="inpd1">
                            
                <div class="dpicker">
                    <div class='input-group date zdata'>
                        <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>


                            
                        </div>
                        <div class="clear"></div>
                        <div class="titd1">по </div>
                        <div class="inpd1">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd2"><input type="radio" class="smenperiod" name="chb" value="smenperiod">смены за период с</div>
                        <div class="inpd3">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod1" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="titd3">по</div>
                        <div class="inpd3">
                        <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        
                        
                        </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkbd1" style="display:none"><input type="checkbox" name="groupbydate"> Группировать по дате</div>
                        <!--  ##### -->
                        
                </fieldset>
                
            </div>
             
        </div>
        <div class="rightm">
            <div style="display:none"s>
                <fieldset>
                    <legend>Сортировать (отлично пашет!)</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="order" checked="checked" value="name"> Наименование</div>
                        <div><input type="radio" name="order" value="quantity"> Количество</div>
                        <div><input type="radio" name="order" value="price"> Цена</div>
                        <div><input type="radio" name="order" value="sumitem"> Сумма</div>
                        <div><input type="checkbox" name="orderdesc" value="orderdesc"> По убыванию</div>
                        <div><input type="checkbox" name="noorder" value="noorder"> Без сортировки</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:none">
                <fieldset>
                    <legend>Группировать</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="group"> По категориям</div>
                        <div><input type="radio" name="group"> По клиентам</div>
                        <div><input type="radio" name="group"> По принтерам</div>
                        <div><input type="radio" name="group"> Только по категориям клиентов</div>
                        <div><input type="radio" name="group"> По сменам</div>
                        <div><input type="radio" name="group"> По столам</div>
                        <div><input type="radio" name="group"> Не использовать группировку</div>
                        <!--  ##### -->
                </fieldset>
            </div>
             <div style="display:none">
                <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox"> Включать состав комплексов</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:none">
                 <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox" value="1" name="datailed"> Расширенные данные по счетам</div>
                        <!--  ##### -->
                </fieldset>
                </div>
        </div>
        <div class="clear"></div>
        <a href="#" class="btn btn-primary" onclick="submit_btn(this,'Отчет по сотрудникам');return false"><b>Сформировать</b></a> 
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
$template['gethtml_poschetam']=<<<HTML
<div class="wndw">
    <div class="wn1">
    <form action="/company/ajax.php?do=otchet&type=poschetam" method="post" role="form">
    
        <div class="tobj1"><h4>Торговая точка</h4></div> 
        <div class="tobj2"><select name="idautomated_point"  class="form-control loaded_idautomated_point" onchange="filterOnIdAutomatedPointChange(this)" ></select></div>
        <div class="clear"></div>
        
        
        <div class="leftm">
        
            <div>
                <fieldset>
                    <legend>Параметры</legend>
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" checked="checked"  value="zasmenu" class="zasmenu"> за смену</div>
                        <div class="inpd1"><select name="chb_zasmenu" class="form-control loaded_chb_zasmenu" onchange="onChangeChbZaSmenu(this)"></select></div>
                        <div class="clear"></div>
                        <div class="titd1">Дата <br />открытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control datestart"></div>
                        
                        <div class="titd2">Дата <br />закрытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control dateend"></div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod"> за период с</div>
                        <div class="inpd1">
                            
                <div class="dpicker">
                    <div class='input-group date zdata'>
                        <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>


                            
                        </div>
                        <div class="clear"></div>
                        <div class="titd1">по </div>
                        <div class="inpd1">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd2"><input type="radio" class="smenperiod" name="chb" value="smenperiod">смены за период с</div>
                        <div class="inpd3">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod1" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="titd3">по</div>
                        <div class="inpd3">
                        <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        
                        
                        </div>
                        <!--  ##### -->
                        
                        
                        
                        <div class="separ"></div>
                        
                        
                       
                        
                </fieldset>
                
            </div>
             
        </div>
        <div class="rightm">
            <div style="display:none"s>
                <fieldset>
                    <legend>Сортировать (отлично пашет!)</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="order" checked="checked" value="name"> Наименование</div>
                        <div><input type="radio" name="order" value="quantity"> Количество</div>
                        <div><input type="radio" name="order" value="price"> Цена</div>
                        <div><input type="radio" name="order" value="sumitem"> Сумма</div>
                        <div><input type="checkbox" name="orderdesc" value="orderdesc"> По убыванию</div>
                        <div><input type="checkbox" name="noorder" value="noorder"> Без сортировки</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:none">
                <fieldset>
                    <legend>Группировать</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="group"> По категориям</div>
                        <div><input type="radio" name="group"> По клиентам</div>
                        <div><input type="radio" name="group"> По принтерам</div>
                        <div><input type="radio" name="group"> Только по категориям клиентов</div>
                        <div><input type="radio" name="group"> По сменам</div>
                        <div><input type="radio" name="group"> По столам</div>
                        <div><input type="radio" name="group"> Не использовать группировку</div>
                        <!--  ##### -->
                </fieldset>
            </div>
             <div style="display:none">
                <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox"> Включать состав комплексов</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:block">
                 <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox" value="1" name="datailed"> Расширенные данные по счетам</div>
                        
                        <!--  ##### -->
                        <!--  ##### -->
                        <div class="checkbd1" style="display:block"><input type="checkbox" name="groupbydate"> Группировать по дате</div>
                        <!--  ##### -->
                        <div><input type="checkbox" name="groupByChange"> Группировать по сменам</div>
                </fieldset>
                </div>
        </div>
        <div class="clear"></div>
        <a href="#" class="btn btn-primary" onclick="submit_btn(this,'Отчет по счетам');return false"><b>Сформировать</b></a> 
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
$template['gethtml_akt_real']=<<<HTML
<div class="wndw">
    <div class="wn1">
    <form action="/company/ajax.php?do=otchet&type=akt" method="post" role="form">
    
        <div class="tobj1"><h4>Торговая точка</h4></div> 
        <div class="tobj2"><select name="idautomated_point"  class="form-control loaded_idautomated_point" onchange="filterOnIdAutomatedPointChange(this)" ></select></div>
        <div class="clear"></div>
        
        
        <div class="leftm">
        
            <div>
                <fieldset>
                    <legend>Параметры</legend>
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" checked="checked"  value="zasmenu" class="zasmenu"> за смену</div>
                        <div class="inpd1"><select name="chb_zasmenu" class="form-control loaded_chb_zasmenu" onchange="onChangeChbZaSmenu(this)"></select></div>
                        <div class="clear"></div>
                        <div class="titd1">Дата <br />открытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control datestart"></div>
                        
                        <div class="titd2">Дата <br />закрытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control dateend"></div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod"> за период с</div>
                        <div class="inpd1">
                            
                <div class="dpicker">
                    <div class='input-group date zdata'>
                        <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>


                            
                        </div>
                        <div class="clear"></div>
                        <div class="titd1">по </div>
                        <div class="inpd1">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd2"><input type="radio" class="smenperiod" name="chb" value="smenperiod">смены за период с</div>
                        <div class="inpd3">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod1" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="titd3">по</div>
                        <div class="inpd3">
                        <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        
                        
                        </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkbd1" ><input type="checkbox" name="groupByDate"> Группировать по дате</div>
                        <!--  ##### --> 
                        <div><input type="checkbox" name="dateInRow"> Показывать даты/смены в строке</div>          
                        
                        <!--  ##### -->
                        <div><input type="checkbox" name="groupByAP"> Группировать по торговым объектам</div>
                        <!--  ##### -->
                        
                        <!--  ##### -->
                        <div><input type="checkbox" name="apInRow"> Показывать торговые объекты в строке</div>
                        <!--  ##### -->
                        
                        <!--  ##### -->
                        <div><input type="checkbox" name="showIdLink"> Показывать коды товаров</div>
                        <!--  ##### -->           
                </fieldset>
                
            </div>
             
        </div>
        <div class="rightm">
            <div style="display:none"s>
                <fieldset>
                    <legend>Сортировать (отлично пашет!)</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="order" checked="checked" value="name"> Наименование</div>
                        <div><input type="radio" name="order" value="quantity"> Количество</div>
                        <div><input type="radio" name="order" value="price"> Цена</div>
                        <div><input type="radio" name="order" value="sumitem"> Сумма</div>
                        <div><input type="checkbox" name="orderdesc" value="orderdesc"> По убыванию</div>
                        <div><input type="checkbox" name="noorder" value="noorder"> Без сортировки</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:none">
                <fieldset>
                    <legend>Группировать</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="group"> По категориям</div>
                        <div><input type="radio" name="group"> По клиентам</div>
                        <div><input type="radio" name="group"> По принтерам</div>
                        <div><input type="radio" name="group"> Только по категориям клиентов</div>
                        <div><input type="radio" name="group"> По сменам</div>
                        <div><input type="radio" name="group"> По столам</div>
                        <div><input type="radio" name="group"> Не использовать группировку</div>
                        <!--  ##### -->
                </fieldset>
            </div>
             <div style="display:none">
                <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox"> Включать состав комплексов</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div >
                 <fieldset>
                    <legend>Дополнительные параметры </legend>

                        
                        <div><input type="checkbox" name="showComplex"> Включать состав комплексов</div>
                        
                        <div><input type="checkbox" name="groupByChange"> Группировать по сменам</div>
                        
                </fieldset>
                </div>
        </div>
        <div class="clear"></div>
        <a href="#" class="btn btn-primary" onclick="submit_btn(this,'Акт реализации');return false"><b>Сформировать</b></a> 
    </form>
    </div>
</div><script>

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
$template['html_exchange_data']=<<<HTML
<div class="wndw">
    <div class="wn1">
    <form action="/company/ajax.php?do=html_exchange_data" method="post" role="form" target="_blank">
    
        <div class="tobj1"><h4>Торговая точка</h4></div> 
        <div class="tobj2"><select name="idautomated_point"  class="form-control loaded_idautomated_point" onchange="filterOnIdAutomatedPointChange(this)" ></select></div>
        <div class="clear"></div>
        
        
        <div class="leftm">
        
            <div>
                <fieldset>
                    <legend>Параметры</legend>
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" checked="checked"  value="zasmenu" class="zasmenu"> за смену</div>
                        <div class="inpd1"><select name="chb_zasmenu" class="form-control loaded_chb_zasmenu" onchange="onChangeChbZaSmenu(this)"></select></div>
                        <div class="clear"></div>
                        <div class="titd1">Дата <br />открытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control datestart"></div>
                        
                        <div class="titd2">Дата <br />закрытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control dateend"></div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod"> за период с</div>
                        <div class="inpd1">
                            
                <div class="dpicker">
                    <div class='input-group date zdata'>
                        <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>


                            
                        </div>
                        <div class="clear"></div>
                        <div class="titd1">по </div>
                        <div class="inpd1">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd2"><input type="radio" class="smenperiod" name="chb" value="smenperiod">смены за период с</div>
                        <div class="inpd3">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod1" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="titd3">по</div>
                        <div class="inpd3">
                        <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        
                        
                        </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkbd1" style="display:none"><input type="checkbox" name="groupbydate"> Группировать по дате</div>
                        <!--  ##### -->
                        
                </fieldset>
                
            </div>
             
        </div>
        <div class="rightm">
            <div style="display:none"s>
                <fieldset>
                    <legend>Сортировать (отлично пашет!)</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="order" checked="checked" value="name"> Наименование</div>
                        <div><input type="radio" name="order" value="quantity"> Количество</div>
                        <div><input type="radio" name="order" value="price"> Цена</div>
                        <div><input type="radio" name="order" value="sumitem"> Сумма</div>
                        <div><input type="checkbox" name="orderdesc" value="orderdesc"> По убыванию</div>
                        <div><input type="checkbox" name="noorder" value="noorder"> Без сортировки</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:none">
                <fieldset>
                    <legend>Группировать</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="group"> По категориям</div>
                        <div><input type="radio" name="group"> По клиентам</div>
                        <div><input type="radio" name="group"> По принтерам</div>
                        <div><input type="radio" name="group"> Только по категориям клиентов</div>
                        <div><input type="radio" name="group"> По сменам</div>
                        <div><input type="radio" name="group"> По столам</div>
                        <div><input type="radio" name="group"> Не использовать группировку</div>
                        <!--  ##### -->
                </fieldset>
            </div>
             <div style="display:none">
                <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox"> Включать состав комплексов</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:none">
                 <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox" value="1" name="datailed"> Расширенные данные по счетам</div>
                        <!--  ##### -->
                </fieldset>
                </div>
        </div>
        <div class="clear"></div>
        <input class="btn btn-primary" type="submit" value="Выгрузить">
    </form>
    </div>
</div><script>

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
$template['html_exchange_template']=<<<HTML
<div class="wndw">
    <div class="wn1">
    <form action="/company/ajax.php?do=html_exchange_template" method="post" role="form">
    <input type="hidden" name="id" value="0">
    <h2>Добавление новой выгрузки</h2>
    <div class="tobj1"><h4>Название:</h4></div> 
    <div class="tobj2"><input name="name"  class="form-control"></div>
        <div class="clear"></div>
    <div class="highlight">
    
        
        <div class="tobj1"><h4>Торговая точка</h4></div> 
        <div class="tobj2"><select name="idautomated_point"  class="form-control loaded_idautomated_point" onchange="filterOnIdAutomatedPointChange(this)" ></select></div>
        <div class="clear"></div>
        
        
        <div class="leftm">
        
            <div>
                <fieldset>
                    <legend>Параметры</legend>
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" checked="checked"  value="zasmenu" class="zasmenu"> за смену</div>
                        <div class="inpd1"><select name="chb_zasmenu" class="form-control loaded_chb_zasmenu" onchange="onChangeChbZaSmenu(this)"></select></div>
                        <div class="clear"></div>
                        <div class="titd1">Дата <br />открытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control datestart"></div>
                        
                        <div class="titd2">Дата <br />закрытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control dateend"></div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod"> за период с</div>
                        <div class="inpd1">
                            
                <div class="dpicker">
                    <div class='input-group date zdata'>
                        <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>


                            
                        </div>
                        <div class="clear"></div>
                        <div class="titd1">по </div>
                        <div class="inpd1">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd2"><input type="radio" class="smenperiod" name="chb" value="smenperiod">смены за период с</div>
                        <div class="inpd3">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod1" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="titd3">по</div>
                        <div class="inpd3">
                        <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        
                        
                        </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkbd1" style="display:none"><input type="checkbox" name="groupbydate"> Группировать по дате</div>
                        <!--  ##### -->
                        
                </fieldset>
                
            </div>
             
        </div>
        <div class="rightm">
            <div style="display:none"s>
                <fieldset>
                    <legend>Сортировать (отлично пашет!)</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="order" checked="checked" value="name"> Наименование</div>
                        <div><input type="radio" name="order" value="quantity"> Количество</div>
                        <div><input type="radio" name="order" value="price"> Цена</div>
                        <div><input type="radio" name="order" value="sumitem"> Сумма</div>
                        <div><input type="checkbox" name="orderdesc" value="orderdesc"> По убыванию</div>
                        <div><input type="checkbox" name="noorder" value="noorder"> Без сортировки</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:none">
                <fieldset>
                    <legend>Группировать</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="group"> По категориям</div>
                        <div><input type="radio" name="group"> По клиентам</div>
                        <div><input type="radio" name="group"> По принтерам</div>
                        <div><input type="radio" name="group"> Только по категориям клиентов</div>
                        <div><input type="radio" name="group"> По сменам</div>
                        <div><input type="radio" name="group"> По столам</div>
                        <div><input type="radio" name="group"> Не использовать группировку</div>
                        <!--  ##### -->
                </fieldset>
            </div>
             <div style="display:none">
                <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox"> Включать состав комплексов</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:none">
                 <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox" value="1" name="datailed"> Расширенные данные по счетам</div>
                        <!--  ##### -->
                </fieldset>
                </div>
        </div>
        <div class="clear"></div>
    </div>
        <input class="btn btn-primary" type="submit" value="Сохранить">
    </form>
    
   
    </div>
    
    
</div><script>

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
$template['gethtml_refuse']=<<<HTML
<div class="wndw">
    <div class="wn1">
    <form action="/company/ajax.php?do=otchet&type=refuse" method="post" role="form">
    
        <div class="tobj1"><h4>Торговая точка</h4></div> 
        <div class="tobj2"><select name="idautomated_point"  class="form-control loaded_idautomated_point" onchange="filterOnIdAutomatedPointChange(this)" ></select></div>
        <div class="clear"></div>
        
        
        <div class="leftm">
        
            <div>
                <fieldset>
                    <legend>Параметры</legend>
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" checked="checked"  value="zasmenu" class="zasmenu"> за смену</div>
                        <div class="inpd1"><select name="chb_zasmenu" class="form-control loaded_chb_zasmenu" onchange="onChangeChbZaSmenu(this)"></select></div>
                        <div class="clear"></div>
                        <div class="titd1">Дата <br />открытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control datestart"></div>
                        
                        <div class="titd2">Дата <br />закрытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control dateend"></div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod"> за период с</div>
                        <div class="inpd1">
                            
                <div class="dpicker">
                    <div class='input-group date zdata'>
                        <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>


                            
                        </div>
                        <div class="clear"></div>
                        <div class="titd1">по </div>
                        <div class="inpd1">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd2"><input type="radio" class="smenperiod" name="chb" value="smenperiod">смены за период с</div>
                        <div class="inpd3">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod1" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="titd3">по</div>
                        <div class="inpd3">
                        <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        
                        
                        </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkbd1" style="display:none"><input type="checkbox" name="groupbydate"> Группировать по дате</div>
                        <!--  ##### -->
                        
                </fieldset>
                
            </div>
             
        </div>
        <div class="rightm">
            <div style="display:none"s>
                <fieldset>
                    <legend>Сортировать (отлично пашет!)</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="order" checked="checked" value="name"> Наименование</div>
                        <div><input type="radio" name="order" value="quantity"> Количество</div>
                        <div><input type="radio" name="order" value="price"> Цена</div>
                        <div><input type="radio" name="order" value="sumitem"> Сумма</div>
                        <div><input type="checkbox" name="orderdesc" value="orderdesc"> По убыванию</div>
                        <div><input type="checkbox" name="noorder" value="noorder"> Без сортировки</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:none">
                <fieldset>
                    <legend>Группировать</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="group"> По категориям</div>
                        <div><input type="radio" name="group"> По клиентам</div>
                        <div><input type="radio" name="group"> По принтерам</div>
                        <div><input type="radio" name="group"> Только по категориям клиентов</div>
                        <div><input type="radio" name="group"> По сменам</div>
                        <div><input type="radio" name="group"> По столам</div>
                        <div><input type="radio" name="group"> Не использовать группировку</div>
                        <!--  ##### -->
                </fieldset>
            </div>
             <div style="display:none">
                <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox"> Включать состав комплексов</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:none">
                 <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox" value="1" name="datailed"> Расширенные данные по счетам</div>
                        <!--  ##### -->
                </fieldset>
                </div>
        </div>
        <div class="clear"></div>
        <a href="#" class="btn btn-primary" onclick="submit_btn(this,'Итоговый отчет');return false"><b>Сформировать</b></a> 
    </form>
    </div>
</div><script>

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
$template['gethtml_refuse_and_orders']=<<<HTML
<div class="wndw">
    <div class="wn1">
    <form action="/company/ajax.php?do=otchet&type=refuse_and_orders" method="post" role="form">
    
        <div class="tobj1"><h4>Торговая точка</h4></div> 
        <div class="tobj2"><select name="idautomated_point"  class="form-control loaded_idautomated_point" onchange="filterOnIdAutomatedPointChange(this)" ></select></div>
        <div class="clear"></div>
        
        
        <div class="leftm">
        
            <div>
                <fieldset>
                    <legend>Параметры</legend>
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" checked="checked"  value="zasmenu" class="zasmenu"> за смену</div>
                        <div class="inpd1"><select name="chb_zasmenu" class="form-control loaded_chb_zasmenu" onchange="onChangeChbZaSmenu(this)"></select></div>
                        <div class="clear"></div>
                        <div class="titd1">Дата <br />открытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control datestart"></div>
                        
                        <div class="titd2">Дата <br />закрытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control dateend"></div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod"> за период с</div>
                        <div class="inpd1">
                            
                <div class="dpicker">
                    <div class='input-group date zdata'>
                        <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>


                            
                        </div>
                        <div class="clear"></div>
                        <div class="titd1">по </div>
                        <div class="inpd1">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd2"><input type="radio" class="smenperiod" name="chb" value="smenperiod">смены за период с</div>
                        <div class="inpd3">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod1" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="titd3">по</div>
                        <div class="inpd3">
                        <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        
                        
                        </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkbd1" style="display:none"><input type="checkbox" name="groupbydate"> Группировать по дате</div>
                        <!--  ##### -->
                        
                </fieldset>
                
            </div>
             
        </div>
        <div class="rightm">
            <div style="display:none"s>
                <fieldset>
                    <legend>Сортировать (отлично пашет!)</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="order" checked="checked" value="name"> Наименование</div>
                        <div><input type="radio" name="order" value="quantity"> Количество</div>
                        <div><input type="radio" name="order" value="price"> Цена</div>
                        <div><input type="radio" name="order" value="sumitem"> Сумма</div>
                        <div><input type="checkbox" name="orderdesc" value="orderdesc"> По убыванию</div>
                        <div><input type="checkbox" name="noorder" value="noorder"> Без сортировки</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:none">
                <fieldset>
                    <legend>Группировать</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="group"> По категориям</div>
                        <div><input type="radio" name="group"> По клиентам</div>
                        <div><input type="radio" name="group"> По принтерам</div>
                        <div><input type="radio" name="group"> Только по категориям клиентов</div>
                        <div><input type="radio" name="group"> По сменам</div>
                        <div><input type="radio" name="group"> По столам</div>
                        <div><input type="radio" name="group"> Не использовать группировку</div>
                        <!--  ##### -->
                </fieldset>
            </div>
             <div style="display:none">
                <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox"> Включать состав комплексов</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:none">
                 <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox" value="1" name="datailed"> Расширенные данные по счетам</div>
                        <!--  ##### -->
                </fieldset>
                </div>
        </div>
        <div class="clear"></div>
        <a href="#" class="btn btn-primary" onclick="submit_btn(this,'Итоговый отчет');return false"><b>Сформировать</b></a> 
    </form>
    </div>
</div><script>

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
$template['gethtml_hoursales']=<<<HTML
<div class="wndw">
    <div class="wn1">
    <form action="/company/ajax.php?do=otchet&type=hoursales" method="post" role="form">
    
        <div class="tobj1"><h4>Торговая точка</h4></div> 
        <div class="tobj2"><select name="idautomated_point"  class="form-control loaded_idautomated_point" onchange="filterOnIdAutomatedPointChange(this)" ></select></div>
        <div class="clear"></div>
        
        
        <div class="leftm">
        
            <div>
                <fieldset>
                    <legend>Параметры</legend>
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" checked="checked"  value="zasmenu" class="zasmenu"> за смену</div>
                        <div class="inpd1"><select name="chb_zasmenu" class="form-control loaded_chb_zasmenu" onchange="onChangeChbZaSmenu(this)"></select></div>
                        <div class="clear"></div>
                        <div class="titd1">Дата <br />открытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control datestart"></div>
                        
                        <div class="titd2">Дата <br />закрытия</div>
                        <div class="inpd2"><input disabled="disabled" class="form-control dateend"></div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd1"><input type="radio" name="chb" class="zaperiod" value="zaperiod"> за период с</div>
                        <div class="inpd1">
                            
                <div class="dpicker">
                    <div class='input-group date zdata'>
                        <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod1" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>


                            
                        </div>
                        <div class="clear"></div>
                        <div class="titd1">по </div>
                        <div class="inpd1">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_zaperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_zaperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkd2"><input type="radio" class="smenperiod" name="chb" value="smenperiod">смены за период с</div>
                        <div class="inpd3">
                            <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod1" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="titd3">по</div>
                        <div class="inpd3">
                        <div class="dpicker">
                                <div class='input-group date zdata'>
                                    <input type='text' class="form-control" onclick="chb_smenperiod_click(this)" data-format="dd.MM.yyyy hh:mm:ss" name="chb_smenperiod2" />
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        
                        
                        </div>
                        <!--  ##### -->
                        
                        <div class="separ"></div>
                        
                        <!--  ##### -->
                        <div class="checkbd1" style="display:none"><input type="checkbox" name="groupbydate"> Группировать по дате</div>
                        <!--  ##### -->
                        
                </fieldset>
                
            </div>
             
        </div>
        <div class="rightm">
            <div style="display:none"s>
                <fieldset>
                    <legend>Сортировать (отлично пашет!)</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="order" checked="checked" value="name"> Наименование</div>
                        <div><input type="radio" name="order" value="quantity"> Количество</div>
                        <div><input type="radio" name="order" value="price"> Цена</div>
                        <div><input type="radio" name="order" value="sumitem"> Сумма</div>
                        <div><input type="checkbox" name="orderdesc" value="orderdesc"> По убыванию</div>
                        <div><input type="checkbox" name="noorder" value="noorder"> Без сортировки</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:none">
                <fieldset>
                    <legend>Группировать</legend>
                        <!--  ##### -->
                        <div><input type="radio" name="group"> По категориям</div>
                        <div><input type="radio" name="group"> По клиентам</div>
                        <div><input type="radio" name="group"> По принтерам</div>
                        <div><input type="radio" name="group"> Только по категориям клиентов</div>
                        <div><input type="radio" name="group"> По сменам</div>
                        <div><input type="radio" name="group"> По столам</div>
                        <div><input type="radio" name="group"> Не использовать группировку</div>
                        <!--  ##### -->
                </fieldset>
            </div>
             <div style="display:none">
                <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox"> Включать состав комплексов</div>
                        <!--  ##### -->
                </fieldset>
            </div>
            <div style="display:none">
                 <fieldset>
                    <legend>Дополнительные параметры </legend>
                        <!--  ##### -->
                        <div><input type="checkbox" value="1" name="datailed"> Расширенные данные по счетам</div>
                        <!--  ##### -->
                </fieldset>
                </div>
        </div>
        <div class="clear"></div>
        <a href="#" class="btn btn-primary" onclick="submit_btn(this,'Итоговый отчет');return false"><b>Сформировать</b></a> 
    </form>
    </div>
</div><script>

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
$template['getmain_charts']=<<<HTML
<br />
<div class="chart_div" style=" float:left">
                        <div id="chart_div1t"></div>
                        <div id="chart_div1" style="width: 500px; height: 400px; float: left;"></div>
                        <div id="chart_div2" style="width: 500px; height: 400px; float: left;"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                    <div class="chart_div" style="height: 500px; float:left">
                        <div id="chart_div2t"></div>
                        <div id="chart_div2g" style="width: 850px; height: 500px; float: left;"></div>
                    </div>
                    
                    <div class="clear"></div>
                    
HTML;
$template['get_window_feedback']=<<<HTML
<form method="post" action="ajax.php?do=save_feedback" id="feed_form"><div class="feeddiv">
                <h3>Ваши замечания, пожелания, соображения.</h3>
                Здесь Вы можете написать разработчикам свои замечания, пожелания, соображения по работе системы.<br />
Так же Вы можете указать на неработоспособность функционала или ошибки его работы.<br />
Система находится в постоянном развитии и, возможно, Ваши пожелания будут учтены в следующих релизах.<br />
<textarea name="message" id="message_feedback"></textarea><br /><br />
<a href="#" class="btn btn-success" onclick="post_feedback()">Отправить</a>
            </div></form>
HTML;
?>
