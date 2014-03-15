<?PHP 
header("Content-Type: text/html; charset=utf-8");
session_start();

include('check.php');
checksessionpassword();
include('mysql.php');
include('functions.php');
include('core.php');


include('templates.php');
if (isset($_SESSION['point'])){
    if (!checkfromfront()){
        header("Location: /front");
        die;
    }
    $_SESSION['fromfront']=1;
}else{
    if (!(isset($_SESSION['main']) || isset($_SESSION['admin']))){
        header("Location: /login.php");
        die;
    }
}



?><!DOCTYPE html>
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <title>Справочники</title>
    <link rel="stylesheet" type="text/css" href="/company/css/print.css" media="print" />
    <link rel="stylesheet" type="text/css" href="/company/css/my.css?v=3">
    
    <link rel="stylesheet" type="text/css" href="/company/bootstrap/css/bootstrap.css">
    
    <script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script> 
    <script type="text/javascript" src="/company/js/charts_init.js"></script>
    <script type="text/javascript" src="/company/mtree/mytreeview.js"></script>
    <script type="text/javascript" src="/company/js/jquery.form.min.js"></script>
    <script type="text/javascript" src="/company/js/init.js"></script>
    <script type="text/javascript" src="/company/warehouse/warehouse.js"></script>
    <script type="text/javascript" src="/company/bootstrap/js/bootstrap.js"></script>
    <link rel="shortcut icon" href="/company/i/favicon.png" type="image/x-icon" />
    <script type="text/javascript" src="/company/bootstrap/js/bootstrap-inputmask.min.js"></script>
    <script type="text/javascript" src="/company/js/bootbox.min.js"></script>
    <script type="text/javascript" src="/company/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    
    <link rel="stylesheet" type="text/css" href="/task/crm_style.css">
    <script type="text/javascript" src="/task/lib.js"></script>
    
    <script>
        const version=<?=VERSION?>;
    </script>
</head>
<body>
<div class="wrapper"> 
<div class="bb-alert alert alert-info" style="display:none;">
        <span></span>
    </div>
<div class="bb-alert alert alert-danger" style="display:none;">
        <span></span>
    </div>
    
<div class="container-loader" id="loading">
  <div class="round">
  </div>
      <div class='l'>
      Загрузка...
      </div>
</div>
<?
// if($_SESSION['base']=='db_ZOTTIG'){
     $days=getServiceRemain();
     //echo $days;
     if (($days!=='error')&&($days<8)){
        if ($days<0)
            echo '<div class="needtopay">Уважаемый пользователь, срок действия услуги закончился '.abs($days).' '.morph($days,'день','дня','дней').' назад! <a href="#" onclick="account_payment()" class="btn btn-success"><i class="glyphicon glyphicon-usd"></i> Оплатить</a></div>';
        else
        if ($days==0)
            echo '<div class="needtopay">Уважаемый пользователь, срок действия услуги заканчивается сегодня! <a href="#" onclick="account_payment()" class="btn btn-success"><i class="glyphicon glyphicon-usd"></i> Оплатить</a></div>';
        else
            echo '<div class="needtopay">Уважаемый пользователь, срок действия услуги заканчивается через '.$days.' '.morph($days,'день','дня','дней').'! <a href="#" onclick="account_payment()" class="btn btn-success"><i class="glyphicon glyphicon-usd"></i> Оплатить</a></div>';
     }
// }
?>
<table class="tab">
    <tr>
        <td class="tab_header" colspan="2" style="top: 0px; display: table-cell;">
      
            
            <div class="tab_logout">

            <? if (isset($_SESSION['wid'])){ 
                echo '<a href="#" onclick="backtothefront()">Назад</a><span>Вы вошли как <b>'.getfiobyid($_SESSION['userid']).'('.$_SESSION['user'].'.'.str_replace('db_','',$_SESSION['base']).')</b></span>';
            }else{

        echo '<div class="btn-group" style="margin-top:-1px;float: right; ">
        <div class="btn btn-primary active" id="task_panel"> 
                <div class="notice">
                    <div class="z_notice z_notice_nw" onclick="show_newtab(\'z_notice_nw\');" >
                        <img src="../task/crm/images/compliteCount.png"  />
                        <div class="z_notice_count_nw" ></div>  
                    </div>

                    <div class="z_notice z_notice_w" onclick="show_newtab(\'z_notice_w\');">
                        <img src="../task/crm/images/workCount.png" />
                        <div class="z_notice_count_w" ></div> 
                    </div>
                </div>
        </div>
  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
    <i class="glyphicon glyphicon-user"></i> <span class="caret"></span>
  </button>
  <ul class="dropdown-menu pull-right" role="menu">';
  if (isset($_SESSION['main'])){
      echo '<li><a href="#" style="text-align:left;" onclick="change_pass(); return false;"><i class="glyphicon glyphicon-cog"></i> Профиль</a></li>
    <li class="divider"></li>';
  }else{
      if ($_SESSION['admin']){
          echo '<li><a href="#" onclick="show_account_settings()" style="text-align:left;"><i class="glyphicon glyphicon-cog"></i> Настройки аккаунта</a></li>
            <li><a href="#" onclick="account_payment()" style="text-align:left;"><i class="glyphicon glyphicon-usd"></i> Оплата услуг</a></li>
    <li class="divider"></li>';
      }
  }
   echo '
    <li><a href="/login.php?do=logout" style="text-align:left;"><i class="glyphicon glyphicon-off"></i> Выход</a></li>
  </ul>
</div>';    
        
        
        
                echo '<span>Вы вошли как <b>'.$_SESSION['fio'].(!isset($_SESSION['admin'])?'('.$_SESSION['user'].'.'.str_replace('db_','',$_SESSION['base']).')':'').'</b></span>';
            }?></div>
            <ul class="menu">
                <? if (checkrights('tab_desctop',1)){?><li onclick="clickto('desctop')" class="tab_desctop"><a href="#">Рабочий стол</a></li><?}?>
                <? if (checkrights('tab_company',1)){?><li onclick="lefttd('tab_company')" class="tab_company"><a href="#">Предприятие</a></li><?}?>
                <?/* if (checkrights('tab_catalog',1)){?><li onclick="lefttd('tab_catalog')" class="tab_catalog"><a href="#">Справочники</a></li><?}*/?>
                <? if (checkrights('tab_aut_point',1)){?><li onclick="lefttd('tab_aut_point')" class="tab_aut_point"><a href="#">Точки продаж</a></li><?}?>                 
                <? if (checkrights('tab_warehouse',1)){?><li onclick="lefttd('tab_warehouse')" class="tab_warehouse"><a href="#">Склад</a></li><?}?> 
                <? if (checkrights('tab_urv',1)){?><li onclick="lefttd('tab_urv')" class="tab_urv"><a href="#">УРВ</a></li><?}?>
               
               
                <? if (checkrights('tab_visitcard',1)){?><li onclick="lefttd('tab_visitcard')" class="tab_visitcard"><a href="#">Визитка</a></li><?}?>
                <? if (checkrights('tab_task',1)){?><li onclick="lefttd('tab_task')" class="tab_task"><a href="#">Задачи</a></li><?}?>
                <? if (checkrights('tab_delivery',1)){?><li onclick="lefttd('tab_delivery')" class="tab_delivery"><a href="#">Рассылка</a></li><?}?>
                
                <li onclick="show_feedback()"><a href="javascript:void(0)">Обратная связь</a></li>                  
            </ul>
        </td>
    </tr>
    <tr>
        <td class="lefttd" style="overflow: auto; width: 220px;">
        
            <div class="td_logo" style="text-align: center;"><div class="ver">v.<?=VERSION?></div><img src="/company/i/logo_new.png" alt=""></div>
            <div class="td_links">

                <div class="s_links" id="tab_company" style="display: none;">
                   <ul>
                    <li>Справочники
                        <ul>
                            <? if (checkrights('s_employee',1)){?><li><a href="#" onclick="show_tree('s_employee')">Сотрудники</a> </li><?}?>
                            <? if (checkrights('s_clients',1)){?><li><a href="#" onclick="show_tree('s_clients')">Клиенты</a></li>  <?}?>
                            <? if (checkrights('s_organizations',1)){?><li><a href="#" onclick="show_tree('s_organizations')">Организации</a></li>  <?}?>
                            <? if (checkrights('s_types_of_payment',1)){?><li><a href="#" onclick="show_tree('s_types_of_payment')">Виды оплаты</a></li><?}?>
                            <? if (checkrights('s_items',1)){?><li><a href="#" onclick="show_tree('s_items')">Товары и услуги</a>  </li><?}?>
                            <? if (checkrights('s_items',1)){?><li><a href="#" onclick="show_tree('s_specifications')">Характеристики</a> </li> <?}?>
                            <? if (checkrights('s_units_of_measurement',1)){?><li><a href="#" onclick="show_tree('s_units_of_measurement')">Единицы измерения</a></li><?}?>
                            <li></li>
                        </ul>
                    </li>
                    <li>Управление
                        <ul>
                            <? if (checkrights('z_default_values',1)){?><li><a href="#" onclick="show_tree('z_default_values')">Значения по умолчанию</a></li> <?}?>
                            <? if (checkrights('s_role',1)){?><li><a href="#" onclick="show_tree('s_role')">Типы доступов</a></li> <?}?>
                            <? if (checkrights('zrights',1)){?><li><a href="#" onclick="show_window_z_rights2()">Права групп</a></li> <?}?>
                            <? if (checkrights('account_settings',666)){?><li><a href="#" onclick="show_account_settings()">Настройки аккаунта</a> </li><?}?>
                            <? if (checkrights('z_logs',1)){?><li><a href="#" onclick="show_journal()">Журнал действий</a> </li> <?}?>
                            <? if (checkrights('account_payment',1)){?><li><a href="#" onclick="account_payment()">Оплата услуг</a></li>  <?}?>
                        </ul>
                    </li>
                   </ul>
                    
                                        
                                        
                    
                    
                    
                    
                </div>
                <!--<div class="s_links" id="tab_catalog" style="display: none;">
                    
                    <? /*if (checkrights('s_position',1)){?><a href="#" onclick="show_tree('s_position')">Должности</a><?}*/?>
                   
                    
                    
                    
                </div>-->
                <div class="s_links" id="tab_aut_point" style="display: none;">
                    
                    
                    <ul>
                        <li>Справочники
                        <ul>
                            <? if (checkrights('s_automated_point',1)){?><li><a href="#" onclick="show_tree('s_automated_point')">Торговая точка</a></li><?}?>
                    <? if (checkrights('s_menu',1)){?><li><a href="#" onclick="show_tree('s_menu')">Меню</a></li><?}?>
                    <? if (checkrights('get_window_combo',1)){?><li><a href="#" onclick="show_combo();">Комбо меню</a></li><?}?>
                    <? if (checkrights('show_design_menu',1)){?><li><a href="#" onclick="show_design_menu2('t_menu_items')">Дизайнер меню</a></li>  <?}?>
                    <? if (checkrights('d_order',1)){?><li><a href="#" onclick="show_tree('d_order')">Счет заказ</a></li><?}?>
                    <? if (checkrights('s_printers',1)){?><li><a href="#" onclick="show_tree('s_printers')">Принтера</a></li><?}?>
                    <? if (checkrights('s_subdivision',1)){?><li><a href="#" onclick="show_tree('s_subdivision')">Подразделения принтеров</a></li><?}?>                    
                    <? if (checkrights('s_discount',1)){?><li><a href="#" onclick="show_tree('s_discount')">Скидки</a></li><?}?>
                    <? if (checkrights('s_tarifs',1)){?><li><a href="#" onclick="show_tree('s_tarifs')">Тарифы</a></li><?}?>
                    <? if (checkrights('s_location',1)){?><li><a href="#" onclick="show_tree('s_location')">Помещения</a></li><?}?>
                    <? if (checkrights('s_objects',1)){?><li><a href="#" onclick="show_tree('s_objects')">Столы</a></li><?}?>
                    <? if (checkrights('s_note',1)){?><li><a href="#" onclick="show_note('s_note')">Примечания</a></li><?}?>
                    <? if (checkrights('s_gifts',1)){?><li><a href="#" onclick="show_tree('s_gifts')">Подарки</a></li><?}?>
                    <? if (checkrights('s_giftlevels',1)){?><li><a href="#" onclick="show_tree('s_giftlevels')">Уровни подарков</a></li><?}?>
                    <? if (checkrights('s_config',1)){?><li><a href="#" onclick="show_tree('s_config')">Конфиг</a></li><?}?>
                        </ul></li>
                        <? if (checkrights('s_reports',1)){?><li>
                            Отчеты
                            <ul>
                                <? if (checkrights('gethtml_akt_real',1)){?><li><a href="#" onclick="show_otchet2('gethtml_akt_real')">Акт реализации</a></li><?}?>
                    <? if (checkrights('gethtml_poschetam',1)){?><li><a href="#" onclick="show_otchet2('gethtml_poschetam')">Отчет по счетам</a></li><?}?>
                    <? if (checkrights('gethtml_refuse_and_orders',1)){?><li><a href="#" onclick="show_otchet2('gethtml_refuse_and_orders')">Отчет по заказам и отказам</a></li><?}?>
                    <? if (checkrights('gethtml_refuse',1)){?><li><a href="#" onclick="show_otchet2('gethtml_refuse')">Отчет по отказам</a></li><?}?>
                    <? if (checkrights('gethtml_hoursales',1)){?><li><a href="#" onclick="show_otchet2('gethtml_hoursales')">Отчет по продажам по часам</a></li><?}?>
                    <? if (checkrights('gethtml_itogovy',1)){?><li><a href="#" onclick="show_otchet2('gethtml_itogovy')">Итоговый отчет</a></li><?}?>
                    <? if (checkrights('getmain_charts',1)){?><li><a href="#" onclick="show_otchet2('getmain_charts')">Графики</a></li><?}?>
                    <? if (checkrights('gethtml_posotr',1)){?><li><a href="#" onclick="show_otchet2('gethtml_posotr')">Отчет по сотрудникам</a></li><?}?>
                            </ul>
                        </li>
                        <?}?>
                         <? if (checkrights('html_exchange_data',1)){?><li>Обмен данными
                            <ul>
                                 <? if (checkrights('html_exchange_data',1)){?><li><a href="#" onclick="load_form('html_exchange_data')">Выгрузка счетов в файл</a></li><?}?>
                                 
                    <? if (checkrights('html_exchange_data',1)){?><li><a href="/company/ajax.php?do=html_spr_data" >Выгрузка товаров в файл</a></li><?}?>
                    <? /* if (checkrights('html_exchange_template',1)){?><li><a href="#" onclick="load_form('html_exchange_template')">Выгрузка</a></li><?} */?>
                    
                    <? if (checkrights('html_import_csv',1)){?><li><a href="#" onclick="load_form('html_import_csv')">Импорт товаров из файла</a></li><?}?>
                    
                            </ul>
                         </li><?}?>
                    </ul>
                    
                </div>
                <div class="s_links" id="s_reports" style="display: none;">
                    
                </div>
                <div class="s_links" id="tab_warehouse" style="display: none;">
                    <ul>
                        <li>Справочники
                            <ul>
                                <? if (checkrights('s_warehouse',1)){?><li><a href="#" onclick="show_tree('s_warehouse')">Склады</a></li><?}?>
                                <? if (checkrights('s_multipliers',1)){?><li><a href="#" onclick="show_tree('s_multipliers')">Коэффициенты</a></li><?}?>
                                <? if (checkrights('s_calculations',1)){?><li><a href="#" onclick="show_warehouse_tree('s_calculations')">Калькуляции</a></li><?}?>
                                <? if (checkrights('gethtml_tipaotchet',1)){?><li><a href="#" onclick="show_otchet2('gethtml_tipaotchet')">Отчет по калькуляции блюд</a></li><?}?>
                            </ul>
                        </li>
                        <li>Документы
                            <ul>
                            
                                <? if (checkrights('d_request',1)){?><li><a href="#" onclick="show_warehouse_tree('d_request')">Заявка на склад</a></li><?}?>
                                <? if (checkrights('d_receipt',1)){?><li><a href="#" onclick="show_warehouse_tree('d_receipt')">Поступление товаров</a></li><?}?>
                                <? if (checkrights('d_selling',1)){?><li><a href="#" onclick="show_warehouse_tree('d_selling')">Реализация товаров</a></li><?}?>
                                <? if (checkrights('d_inventory',1)){?><li><a href="#" onclick="show_warehouse_tree('d_inventory')">Инвентаризация</a></li><?}?>
                                <? if (checkrights('d_movement',1)){?><li><a href="#" onclick="show_warehouse_tree('d_movement')">Перемещение</a></li><?}?>
                                <? if (checkrights('d_posting',1)){?><li><a href="#" onclick="show_warehouse_tree('d_posting')">Оприходование</a></li><?}?>
                                <? if (checkrights('d_cancellation',1)){?><li><a href="#" onclick="show_warehouse_tree('d_cancellation')">Списание</a></li><?}?>
                                <? if (checkrights('d_production',1)){?><li><a href="#" onclick="show_warehouse_tree('d_production')">Выпуск готовой продукции</a></li><?}?>
                                <? if (checkrights('d_regrading',1)){?><a href="#" onclick="show_warehouse_tree('d_regrading')">Пересортица товаров</a><?}?>
                                
                            </ul>
                        </li>
                        <li>Отчеты
                            <ul>
                                <? if (checkrights('gethtml_remains',1)){?><li><a href="#" onclick="show_otchet2('gethtml_remains')">Материальная ведомость</a></li><?}?>
                                <? if (checkrights('gethtml_remains',1)){?><li><a href="#" onclick="show_otchet2('gethtml_remainsdetailed')">Движения товаров</a></li><?}?>
                                <? if (checkrights('gethtml_remains',1)){?><li><a href="#" onclick="show_otchet2('gethtml_anal_sale')">Отчет по движению документов</a></li><?}?>
                                <? if (checkrights('gethtml_remains',1)){?><li><a href="#" onclick="show_otchet2('gethtml_anal_sale2')">Анализ продаж</a></li><?}?>
                                <? if (checkrights('gethtml_conductor',1)){?><li><a href="#" onclick="show_otchet2('gethtml_conductor')">Провести счета</a></li><?}?>
                                <? if (checkrights('gethtml_conductor',1)){?><li><a href="#" onclick="show_otchet2('gethtml_reconduct')">Перепроведение документов</a></li><?}?>
                                
                            </ul>
                        </li>
                        <li>Кассы
                            <ul>
                                <? if (checkrights('s_cash',1)){?><li><a href="#" onclick="show_tree('s_cash')">Кассы</a></li><?}?>
                                <? if (checkrights('d_cash_income',1)){?><li><a href="#" onclick="show_warehouse_tree('d_cash_income')">Поступление</a></li><?}?>
                                <? if (checkrights('d_cash_outcome',1)){?><li><a href="#" onclick="show_warehouse_tree('d_cash_outcome')">Изъятие</a></li><?}?>
                                <? if (checkrights('d_cash_movement',1)){?><li><a href="#" onclick="show_warehouse_tree('d_cash_movement')">Перемещение</a></li><?}?>
                                <li><a href="#" onclick="show_otchet2('gethtml_cash_remains')">Кассовая ведомость</a></li>
                                <li><a href="#" onclick="show_otchet2('gethtml_cash_remainsdetailed')">Движения ден. средств</a></li>
                            </ul>
                        </li>
                    </ul>
                    
                    <b></b> 
                    
                </div>
                <div class="s_links" id="s_exchange" style="display: none;">
                   
                </div>
                <div class="s_links" id="tab_urv" style="display: none;">
                    <ul>
                        <li>Справочники
                    <ul>
                    <? if (checkrights('s_pointurv',1)){?><li><a href="#" onclick="show_tree('s_department')">Отдел</a></li> <?}?>   
                    <? if (checkrights('s_pointurv',1)){?><li><a href="#" onclick="show_tree('s_position')">Должность</a></li> <?}?>   
                    <? if (checkrights('s_pointurv',1)){?><li><a href="#" onclick="show_tree('s_pointurv')">КПП</a></li> <?}?>   
                    <? if (checkrights('s_pointurv',1)){?><li><a href="#" onclick="show_newtab('urv_graphic')">Управление графиками</a></li> <?}?>   
                    <? if (checkrights('s_pointurv',1)){?><li><a onclick="show_tree('s_employee')" href="#">Сотрудники</a> </li><?}?>   
                    
                    <? if (checkrights('s_location',1)){?><li><a href="#" onclick="show_tree('s_location')">Помещения</a></li><?}?>
</ul>   </li>
                        <li>Отчеты<ul>
                        <? if (checkrights('s_pointurv',1)){?><li><a href="#" onclick="show_newtab('frameURVhistory')">История</a></li> <?}?>
                        <? if (checkrights('s_pointurv',1)){?><li><a href="#" onclick="show_newtab('urv_employee')">Отчет по сотруднику</a> </li><?}?>
                        <? if (checkrights('s_pointurv',1)){?><li><a href="#" onclick="show_newtab('urv_visits')">Табель посещения</a></li> <?}?>
                        <? if (checkrights('s_pointurv',1)){?><li><a href="#" onclick="show_newtab('urv_empWorks')">Сотрудники на работе</a></li> <?}?>
                        </ul> </li>
                        <li><a onclick="show_newtab('frameUrvTranzaction')" href="#">Выгрузка транзакций</a></li>
                    </ul>
                      
                </div>
                 <div class="s_links" id="tab_visitcard" style="display: none;">
                    <? if (checkrights('visitcard',1)){?><a href="#" onclick="showVCWindow('visitcard')">Визитка</a><?}?>
                    <? if (checkrights('visitcard_contents',1)){?><a href="#" onclick="showVCWindow('visitcard_contents')">Доп. контент</a><?}?>
                    <? if (checkrights('visitcard_images',1)){?><a href="#" onclick="showVCWindow('visitcard_images')">Изображения</a><?}?>
                </div>
                <div class="s_links" id="tab_task" style="display: none;">
                    <?   if (checkrights('tab_task',1)){?><a href="#" onclick="createZadacha()">Добавить задание</a><?} ?>
                    <?   if (checkrights('tab_task',1)){?><a href="#" onclick="show_newtab('requere_zadacha')">Задания</a><?} ?>
                    <? if (checkrights('tab_task',1)){?><a href="#" onclick="show_newtab('requere_report')">Отчёты</a><?}?>
                    <? /* if (checkrights('tab_task',1)){?><a href="#" onclick="show_newtab('requere_soc_seti')">Социальная Сеть</a><?} */?>
                    <? /*if (checkrights('tab_task',1)){?><a href="#" onclick="show_newtab('requere_settings_mgr')">Настройки менеджеров</a><?}*/ ?>
                    
                </div>
                <div class="s_links" id="tab_delivery" style="display: none;">

                    <? if (checkrights('visitcard_images',1)){?><a href="#" onclick="show_newtab('sms')">СМС</a><?}?>
                    <? if (checkrights('sms_logs',1)){?><a href="#" onclick="show_tree('sms_logs')">СМС Логи</a><?}?>
                    <? if (checkrights('sms_template',1)){?><a href="#" onclick="show_tree('sms_template')">Редактор шаблонов</a><?}?>
                    <? if (checkrights('sms_mask',1)){?><a href="#" onclick="show_tree('sms_mask')">Редактор масок</a><?}?>
                    <? if (checkrights('visitcard_images',1)){?><a href="#" onclick="show_newtab('sms_settings')">Настройки</a><?}?>
                </div>
            </div>
        </td>
        <td class="righttd">
        
        
   <ul class="my-menu dark">
                
                <? if (checkrights('tab_desctop',1)){?><li>Рабочий стол</li><?}?>
                <? if (checkrights('tab_company',1)){?><li>Предприятие
                    <ul>
                    <li>Справочники <i class="glyphicon glyphicon-chevron-right menu_vip"></i>
                        <ul>
                            <? if (checkrights('s_employee',1)){?><li onclick="show_tree('s_employee')">Сотрудники </li><?}?>
                            <? if (checkrights('s_clients',1)){?><li onclick="show_tree('s_clients')">Клиенты</li>  <?}?>
                            <? if (checkrights('s_organizations',1)){?><li onclick="show_tree('s_organizations')">Организации</li>  <?}?>
                            <? if (checkrights('s_types_of_payment',1)){?><li onclick="show_tree('s_types_of_payment')">Виды оплаты</li><?}?>
                            <? if (checkrights('s_items',1)){?><li onclick="show_tree('s_items')">Товары и услуги  </li><?}?>
                            <? if (checkrights('s_items',1)){?><li onclick="show_tree('s_specifications')">Характеристики </li> <?}?>
                            <? if (checkrights('s_units_of_measurement',1)){?><li onclick="show_tree('s_units_of_measurement')">Единицы измерения</li><?}?>
                        </ul>
                    </li>
                    <li>Управление <i class="glyphicon glyphicon-chevron-right menu_vip"></i>
                        <ul>
                            <? if (checkrights('z_default_values',1)){?><li onclick="show_tree('z_default_values')">Значения по умолчанию</li> <?}?>
                            <? if (checkrights('s_role',1)){?><li onclick="show_tree('s_role')">Типы доступов</li> <?}?>
                            <? if (checkrights('zrights',1)){?><li onclick="show_window_z_rights2()">Права групп</li> <?}?>
                            <? if (checkrights('account_settings',666)){?><li onclick="show_account_settings()">Настройки аккаунта </li><?}?>
                            <? if (checkrights('z_logs',1)){?><li onclick="show_journal()">Журнал действий </li> <?}?>
                            <? if (checkrights('account_payment',1)){?><li onclick="account_payment()">Оплата услуг</li>  <?}?>
                        </ul>
                    </li>
                    
                        
                    
                    </ul>
                </li><?}?>
                <? if (checkrights('tab_aut_point',1)){?><li>Точки продаж
                 
                    <ul>
                    <li>Справочники <i class="glyphicon glyphicon-chevron-right menu_vip"></i>
                     <ul>
                        <? if (checkrights('s_automated_point',1)){?><li onclick="show_tree('s_automated_point')">Торговая точка</li><?}?>
                        <? if (checkrights('s_menu',1)){?><li onclick="show_tree('s_menu')">Меню</li><?}?>
                        <? if (checkrights('get_window_combo',1)){?><li onclick="show_combo();">Комбо меню</li><?}?>
                        <? if (checkrights('show_design_menu',1)){?><li onclick="show_design_menu2('t_menu_items')">Дизайнер меню</li>  <?}?>
                        <? if (checkrights('d_order',1)){?><li onclick="show_tree('d_order')">Счет заказ</li><?}?>
                        <? if (checkrights('s_printers',1)){?><li onclick="show_tree('s_printers')">Принтера</li><?}?>
                        <? if (checkrights('s_subdivision',1)){?><li onclick="show_tree('s_subdivision')">Подразделения принтеров</li><?}?>                    
                        <? if (checkrights('s_discount',1)){?><li onclick="show_tree('s_discount')">Скидки</li><?}?>
                        <? if (checkrights('s_tarifs',1)){?><li onclick="show_tree('s_tarifs')">Тарифы</li><?}?>
                        <? if (checkrights('s_location',1)){?><li onclick="show_tree('s_location')">Помещения</li><?}?>
                        <? if (checkrights('s_objects',1)){?><li onclick="show_tree('s_objects')">Столы</li><?}?>
                        <? if (checkrights('s_note',1)){?><li onclick="show_note('s_note')">Примечания</li><?}?>
                        <? if (checkrights('s_gifts',1)){?><li onclick="show_tree('s_gifts')">Подарки</li><?}?>
                        <? if (checkrights('s_giftlevels',1)){?><li onclick="show_tree('s_giftlevels')">Уровни подарков</li><?}?>
                        <? if (checkrights('s_config',1)){?><li onclick="show_tree('s_config')">Конфиг</li><?}?>
                     </ul>
                    </li>    
                    <li>Отчеты <i class="glyphicon glyphicon-chevron-right menu_vip"></i>
                     <ul>
                        <? if (checkrights('gethtml_akt_real',1)){?><li onclick="show_otchet2('gethtml_akt_real')">Акт реализации</li><?}?>
                        <? if (checkrights('gethtml_poschetam',1)){?><li onclick="show_otchet2('gethtml_poschetam')">Отчет по счетам</li><?}?>
                        <? if (checkrights('gethtml_refuse_and_orders',1)){?><li onclick="show_otchet2('gethtml_refuse_and_orders')">Отчет по заказам и отказам</li><?}?>
                        <? if (checkrights('gethtml_refuse',1)){?><li onclick="show_otchet2('gethtml_refuse')">Отчет по отказам</li><?}?>
                        <? if (checkrights('gethtml_hoursales',1)){?><li onclick="show_otchet2('gethtml_hoursales')">Отчет по продажам по часам</li><?}?>
                        <? if (checkrights('gethtml_itogovy',1)){?><li onclick="show_otchet2('gethtml_itogovy')">Итоговый отчет</li><?}?>
                        <? if (checkrights('getmain_charts',1)){?><li onclick="show_otchet2('getmain_charts')">Графики</li><?}?>
                        <? if (checkrights('gethtml_posotr',1)){?><li onclick="show_otchet2('gethtml_posotr')">Отчет по сотрудникам</li><?}?>
                      </ul>
                     </li>    
                     <li>Обмен данными <i class="glyphicon glyphicon-chevron-right menu_vip"></i>
                      <ul>
                        <? if (checkrights('html_exchange_data',1)){?><li onclick="load_form('html_exchange_data')">Выгрузка счетов в файл</li><?}?>
                        <? if (checkrights('html_exchange_data',1)){?><li><a href="/company/ajax.php?do=html_spr_data" >Выгрузка товаров в файл</a></li><?}?>
                        <? if (checkrights('html_import_csv',1)){?><li onclick="load_form('html_import_csv')">Импорт товаров из файла</li><?}?>
                      </ul>
                     </li>   
                    </ul>
                </li><?}?>                 
                <? if (checkrights('tab_warehouse',1)){?><li>Склад
                    <ul>
                        <li>Справочники <i class="glyphicon glyphicon-chevron-right menu_vip"></i>
                            <ul>
                                <? if (checkrights('s_warehouse',1)){?><li onclick="show_tree('s_warehouse')">Склады</li><?}?>
                                <? if (checkrights('s_multipliers',1)){?><li onclick="show_tree('s_multipliers')">Коэффициенты</li><?}?>
                                <? if (checkrights('s_calculations',1)){?><li onclick="show_warehouse_tree('s_calculations')">Калькуляции</li><?}?>
                                <? if (checkrights('gethtml_tipaotchet',1)){?><li onclick="show_otchet2('gethtml_tipaotchet')">Отчет по калькуляции блюд</li><?}?>
                            </ul>
                        </li>
                        <li>Документы <i class="glyphicon glyphicon-chevron-right menu_vip"></i>
                            <ul>
                                <? if (checkrights('d_request',1)){?><li onclick="show_warehouse_tree('d_request')">Заявка на склад</li><?}?>
                                <? if (checkrights('d_receipt',1)){?><li onclick="show_warehouse_tree('d_receipt')">Поступление товаров</li><?}?>
                                <? if (checkrights('d_selling',1)){?><li onclick="show_warehouse_tree('d_selling')">Реализация товаров</li><?}?>
                                <? if (checkrights('d_inventory',1)){?><li onclick="show_warehouse_tree('d_inventory')">Инвентаризация</li><?}?>
                                <? if (checkrights('d_movement',1)){?><li onclick="show_warehouse_tree('d_movement')">Перемещение</li><?}?>
                                <? if (checkrights('d_posting',1)){?><li onclick="show_warehouse_tree('d_posting')">Оприходование</li><?}?>
                                <? if (checkrights('d_cancellation',1)){?><li onclick="show_warehouse_tree('d_cancellation')">Списание</li><?}?>
                                <? if (checkrights('d_production',1)){?><li onclick="show_warehouse_tree('d_production')">Выпуск готовой продукции</li><?}?>
                                
                            </ul>
                        </li>
                        <li>Отчеты <i class="glyphicon glyphicon-chevron-right menu_vip"></i>
                            <ul>
                                <? if (checkrights('gethtml_remains',1)){?><li onclick="show_otchet2('gethtml_remains')">Материальная ведомость</li><?}?>
                                <? if (checkrights('gethtml_remains',1)){?><li onclick="show_otchet2('gethtml_remainsdetailed')">Движения товаров</li><?}?>
                                <? if (checkrights('gethtml_remains',1)){?><li onclick="show_otchet2('gethtml_anal_sale')">Анализ продаж</li><?}?>
                                <? if (checkrights('gethtml_conductor',1)){?><li onclick="show_otchet2('gethtml_conductor')">Провести счета</li><?}?>
                                <? if (checkrights('gethtml_conductor',1)){?><li onclick="show_otchet2('gethtml_reconduct')">Перепроведение документов</li><?}?>
                            </ul>
                        </li>
                    </ul>
                </li><?}?> 
                <? if (checkrights('tab_urv',1)){?><li>УРВ
                    <ul>
                        <? if (checkrights('s_pointurv',1)){?><li onclick="show_tree('s_pointurv')">КПП</li><?}?>   
                        <? if (checkrights('s_pointurv',1)){?><li onclick="show_newtab('urv_graphic')">Управление графиками</li> <?}?>   
                        <? if (checkrights('s_pointurv',1)){?><li onclick="show_tree('s_employee')">Сотрудники </li><?}?> 
                        <li onclick="show_newtab('frameUrvTranzaction')">Выгрузка транзакций</li>
                        <li>Отчеты <i class="glyphicon glyphicon-chevron-right menu_vip"></i>
                            <ul>
                                <? if (checkrights('s_pointurv',1)){?><li onclick="show_tree('d_urv_transactions')">История</li> <?}?>
                                <? if (checkrights('s_pointurv',1)){?><li onclick="show_newtab('urv_employee')">Отчет по сотруднику </li><?}?>
                                <? if (checkrights('s_pointurv',1)){?><li onclick="show_newtab('urv_visits')">Табель посещения</li> <?}?>
                            </ul> 
                        </li>
                    </ul>
                </li><?}?>
                <? if (checkrights('tab_visitcard',1)){?><li>Визитка
                    <ul>
                        <? if (checkrights('visitcard',1)){?><li onclick="showVCWindow('visitcard')">Визитка</li><?}?>
                        <? if (checkrights('visitcard_contents',1)){?><li onclick="showVCWindow('visitcard_contents')">Доп. контент</li><?}?>
                        <? if (checkrights('visitcard_images',1)){?><li onclick="showVCWindow('visitcard_images')">Изображения</li><?}?>
                    </ul>
                </li><?}?>
                <? if (checkrights('tab_task',1)){?><li>Задачи
                    <ul>
                        <? if (checkrights('tab_task',1)){?><li onclick="show_newtab('requere_zadacha')">Задания</li><?}?>
                        <? if (checkrights('tab_task',1)){?><li onclick="show_newtab('requere_report')">Отчёты</li><?}?>
                        <? if (checkrights('tab_task',1)){?><li onclick="show_newtab('requere_soc_seti')">Социальная Сеть</li><?}?>
                        <? if (checkrights('tab_task',1)){?><li onclick="show_newtab('requere_settings_mgr')">Настройки менеджеров</li><?}?>
                    </ul>
                </li><?}?>
                <? if (checkrights('tab_delivery',1)){?><li>Рассылка
                    <ul>
                    <? if (checkrights('visitcard_images',1)){?><li onclick="show_newtab('sms')">СМС</li><?}?>
                    <? if (checkrights('sms_logs',1)){?><li onclick="show_tree('sms_logs')">СМС Логи</li><?}?>
                    <? if (checkrights('sms_template',1)){?><li onclick="show_tree('sms_template')">Редактор шаблонов</li><?}?>
                    <? if (checkrights('sms_mask',1)){?><li onclick="show_tree('sms_mask')">Редактор масок</li><?}?>
                    <? if (checkrights('visitcard_images',1)){?><li onclick="show_newtab('sms_settings')">Настройки</li><?}?>
                    </ul>
                </li><?}?>
                <li onclick="show_feedback()">Обратная связь</li>                  
                
               
            </ul>            


        <i class="glyphicon glyphicon-fullscreen fullscr"></i>
        <ul class="nav nav-tabs nav-tabs-success" id="zottabs">
          <li class="active"><a href="#desctop" data-toggle="tab">Рабочий стол</a></li>
        </ul>
        <div class="tab-content" id="zotcontent">
            <div class="tab-pane fade in active" id="desctop">
                <div class="toolbar" style="padding: 1px 0 1px 11px;">
                    <h4>Справочники</h4>
                </div>
                <div class="righttd-content">
                    <div style="padding: 10px;">
                    <table>
                    <tr>
                    <td style="width: 100px;">
                    <? if (checkrights('s_items',1)){?><a href="#" onclick="show_tree('s_items')"><img src="/company/images/items.jpg" width="50px" height="50px"><br>Товары и услуги</img></a>  <?}?>
                    </td>
                    <td style="width: 100px;">
                    <? if (checkrights('s_printers',1)){?><a href="#" onclick="show_tree('s_printers')"><img src="/company/images/printer.png" width="50px" height="50px"><br>Принтера</img></a>  <?}?>
                    </td>
                    <td style="width: 100px;">
                    <? if (checkrights('s_subdivision',1)){?><a href="#" onclick="show_tree('s_subdivision')"><img src="/company/images/devisionprinter.png" width="50px" height="50px"><br>Подразделения принтеров</img></a>  <?}?>
                    </td>
                    <td style="width: 100px;">
                    <? if (checkrights('s_menu',1)){?><a href="#" onclick="show_tree('s_menu')"><img src="/company/images/menu.jpg" width="50px" height="50px"><br>Меню</img></a>  <?}?>
                    </td>
                    <td style="width: 100px;">
                    <? if (checkrights('show_design_menu',1)){?><a href="#" onclick="show_design_menu2('t_menu_items')"><img src="/company/images/designmenu.jpg" width="50px" height="50px"><br>Дизайнер меню</img></a>  <?}?>
                    </td>
                    <td style="width: 100px;">
                    <? if (checkrights('s_automated_point',1)){?><a href="#" onclick="show_tree('s_automated_point')"><img src="/company/images/tradeobjects.png" width="50px" height="50px"><br>Торговая точка</img></a> <?}?>
                    </td>
                    <td style="width: 100px;">
                    <? if (checkrights('s_employee',1)){?><a href="#" onclick="show_tree('s_employee')"><img src="/company/images/personal.png" width="50px" height="50px"><br>Сотрудники</img></a> <?}?>
                    </td>
                    <td style="width: 100px;">
                    <? if (checkrights('s_clients',1)){?><a href="#" onclick="show_tree('s_clients')"><img src="/company/images/clients.gif" width="50px" height="50px"><br>Клиенты</img></a>  <?}?>
                    </td>                
                    </tr>
                    </table>
                    </div>      
                    <div class="toolbar" style="padding: 1px 0 1px 11px;">
                    <h4>Отчеты</h4>
                </div>           
                    
                    <div style="padding: 10px;">
                    <table>
                    <tr>
                    <td style="width: 100px;">
                        <? if (checkrights('gethtml_itogovy',1)){?><a href="#" onclick="show_otchet2('gethtml_itogovy')"><img src="/company/images/report.jpg" width="50px" height="50px"><br>Итоговый отчет</img></a><?}?>
                    </td>
                    <td style="width: 100px;">
                        <? if (checkrights('gethtml_poschetam',1)){?><a href="#" onclick="show_otchet2('gethtml_poschetam')"><img src="/company/images/report.jpg" width="50px" height="50px"><br>Отчет по счетам</img></a><?}?>
                    </td>
                    <td style="width: 100px;">
                        <? if (checkrights('gethtml_akt_real',1)){?><a href="#" onclick="show_otchet2('gethtml_akt_real')"><img src="/company/images/report.jpg" width="50px" height="50px"><br>Акт реализации</img></a><?}?>
                    </td>
                    <td style="width: 100px;">
                        <? if (checkrights('gethtml_hoursales',1)){?><a href="#" onclick="show_otchet2('gethtml_hoursales')"><img src="/company/images/report.jpg" width="50px" height="50px"><br>Отчет по продажам по часам</img></a><?}?>
                    </td>
                    <td style="width: 100px;">
                        <? if (checkrights('gethtml_refuse_and_orders',1)){?><a href="#" onclick="show_otchet2('gethtml_refuse_and_orders')"><img src="/company/images/report.jpg" width="50px" height="50px"><br>Отчет по заказам и отказам</img></a><?}?>
                    </td>
                    <td style="width: 100px;">
                        <? if (checkrights('gethtml_refuse',1)){?><a href="#" onclick="show_otchet2('gethtml_refuse')"><img src="/company/images/report.jpg" width="50px" height="50px"><br>Отчет по отказам</img></a><?}?>
                    </td>
                    <td style="width: 100px;"> 
                    <? if (checkrights('getmain_charts',1)){?><a href="#" onclick="show_otchet2('getmain_charts')"><img src="/company/images/grafic.png" width="50px" height="50px"><br>Графики</img></a><?}?>
                    </td>
                    <td style="width: 100px;">
                    <? if (checkrights('gethtml_posotr',1)){?><a href="#" onclick="show_otchet2('gethtml_posotr')"><img src="/company/images/report_employee.png" width="50px" height="50px"><br>Отчет по сотрудникам</img></a><?}?>
                    </td>
                    </tr>
                    </table>
                </div>
               </div>
            </div>
        </td>
    </tr>
</table>
<!--Див со всеми окнами -->
<div id="windows"></div>
<!--Див со всеми диалогами -->
<div id="dialogs"></div>
<!--Окно удаления -->

<div class="modal fade" id="dlg-del" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Удаление</h4>
        
      </div>
      <div class="modal-body">
        <b>Вы действительно хотите удалить "<span id="del-name"></span>"?</b>
        <input type="hidden" value="0" id="del_type">
        <input type="hidden" value="0" id="del_table">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
        <button type="button" class="btn btn-danger" onclick="deletez_ok()">Удалить</button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="dlg-del2" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Удаление</h4>
        
      </div>
      <div class="modal-body">
        <b>Вы действительно хотите удалить "<span id="del-name2"></span>"?</b>
        <input type="hidden" value="0" id="del_table2">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
        <button type="button" class="btn btn-danger" onclick="deletez_ok2()">Удалить</button>
      </div>
    </div>
  </div>
</div>


<div class="contactinfo2">Наши телефоны:<br>Сотовый: <span>8-701-111-97-23</span><br>Городской: <span>8-727-3-27-01-74</span></div>
<?


if ($_SESSION['base']=='db_ZOTTIG'){
echo '<script> 
        console.log($(\'.righttd-content\').offset().top);
     </script>';   
}
if (isset($_GET['bang'])){
?>
<script>
    $(function() {
        $('body').click(function(e){
            
            var offset = $(this).offset();
              var relativeX = (e.pageX - offset.left);
              var relativeY = (e.pageY - offset.top);
  
  
            gif=$('<img src="/company/images/1.GIF" class="bigbang">').appendTo('body');
            gif.css('position','fixed');
            gif.css('top',relativeY-50);
            gif.css('left',relativeX-50);
            setTimeout(function(){ gif.remove()}, 1000);     
        });
    });
</script>
<?
}

?>

<?
if (isset($_GET['do'])){
    
    switch($_GET['do']){
        case 'paysuccess':
            echo '<script>
                account_payment();
                bootbox.alert("<center>Поздравляем!<br />Счет №'.$_GET['no'].' был успешно оплачен</center>");
                history.replaceState(null, null, \'/company/\'); 
            </script>';
        break;
        case 'payfail':
            echo '<script>
                account_payment();
                bootbox.alert("Произошла отмена оплаты счёта №'.$_GET['no'].'");
                history.replaceState(null, null, \'/company/\');
            </script>';
        break;
    }
}
?>
</div>

</body>
</html>