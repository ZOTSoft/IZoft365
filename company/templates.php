<?php
$template=array();
include('warehouse/warehouse_templates.php');
if (isset($_SESSION['userid'])){
    include('report/templates.php');
} 
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
$template['get_window_combo']=<<<HTML
<table width="100%" class="combo">
    <tr>
        <td class="combo2">
            <h4>Комбо меню</h4>
            <div id="s_items_combo"></div>
        </td>
        <td class="combo2">
            <h4>Комбо группы</h4>
            <a href="javascript:void(0)" class="btn btn-success" onclick="combo_create_gr()"><i class="glyphicon glyphicon-plus"></i> Добавить</a>
            <a href="javascript:void(0)" class="btn btn-warning" onclick="combo_edit_gr()"><i class="glyphicon glyphicon-remove"></i> Изменить</a> 
            <a href="javascript:void(0)" class="btn btn-danger" onclick="combo_delete('s_items_combo_group')"><i class="glyphicon glyphicon-trash"></i> Удалить</a> 
            <div id="s_items_combo_group"></div>
        </td>
        <td class="combo2">
            <h4>Элементы группы</h4>
            <a href="javascript:void(0)" class="btn btn-success" onclick="combo_create_el()"><i class="glyphicon glyphicon-plus"></i> Добавить</a>
            <a href="javascript:void(0)" class="btn btn-warning" onclick="combo_edit_el()"><i class="glyphicon glyphicon-remove"></i> Изменить</a> 
            <a href="javascript:void(0)" class="btn btn-danger" onclick="combo_delete('s_items_combo_items')"><i class="glyphicon glyphicon-trash"></i> Удалить</a> 
            <div id="s_items_combo_items"></div>
        </td>
    </tr>
</table>
HTML;
$template['html_import_csv']=<<<HTML
<div style="padding:20px">
<form method="post" action="ajax.php?do=uploadcsvspr"  enctype="multipart/form-data" role="form">
    <table>
        <tr>
            <td class="tright">Файл</td>
            <td><input type="file" id="csv_file" class="form-control" name="csv_file"></td>
        </tr>
        <tr>
            <td class="tright">Заменять названия </td>
            <td><div style="width:50px"><input type="checkbox" name="change_name" class="form-control"></div></td>
        </tr>
        <tr>
            <td class="tright">Использовать в меню </td>
            <td><div style="width:50px"><input type="checkbox"  checked="checked" name="i_useInMenu" class="form-control"></div></td>
        </tr>
    </table>
    
    <button type="button" class="btn btn-success" onclick="form_submit(this); return false">Импортировать</button>

</form>
</div>
HTML;
?>
