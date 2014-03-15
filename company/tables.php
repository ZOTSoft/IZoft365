<?php
/*

Все типы полей 

input
checkbox
db_select "selectfield" => 'fio',
file
db_multicheckbox

*/

include('warehouse/warehouse_tables.php');


  //Название таблицы
  $tables['s_items']['name']='Товары';  
  //Возможность создать каталог
  $tables['s_items']['create_group']=true;  
  //Ширина грида
  $tables['s_items']['width']=700;  
  //Высота грида
  $tables['s_items']['height']=550;  
  //Описание полей
  $fields['s_items'] = array (
    //название поля
    "id" => array(
            //тип поля (возможные: input, db_select)
            "type" => "input",
            //выводимое имя поля
            "title" => "ID",
            //вывод поля в таблице
            "in_grid" => false,
            //вывод поля в Edit
            "in_edit" => false,
            //обязательно ли поле для заполнения
            "required" =>true,
            //Значение по дефолту
            "default" =>1,
            //Ширина поля в окне редактирования
            "field_width" =>100,
            //Ширина поля в гриде
            "width" =>100,
            //подсказка
            "alt" => "Айди товара",
            //тект после поля
            "after_text" => "",
            //поле в группе
            "in_group" => false
        ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_group" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "parentid" => array(
            "type" => "db_groupselect",
            "title" => "Группа",
            "in_grid" => false,
            "in_edit" => true,
            "in_group" => true,
            "db_select"=>'s_items',
            "alt" => "Папка в которой находится товар"
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 300,
            "alt" => "Название товара"
            
        ),
    "price" => array(
            "type" => "input",
            "title" => "Цена",
            "alt" => "Цена продажи",
            "in_grid" => true,
            "valuetype" => 'float',
            "in_edit" => true,
            "field_width" =>155
            //"alt" => "Цена в тенге"
        ),
    /*"parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),*/
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
     "measurement" => array(
            "type" => "db_select",
            "title" => "Ед. измерения",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_units_of_measurement'
        ),
        
        "i_useInMenu" => array(
            "type" => "checkbox",
            "title" => "Использовать в меню",
            "alt" => "Отображать в режиме продаж",
            "in_grid" => true,
            "in_group" => true,
            "in_edit" => true
        ),
     "weight" => array(
            "type" => "checkbox",
            "title" => "Весовой",
            "in_grid" => true,
            "in_edit" => true,
            "alt" => "Можно получать вес через подключенные весы"
        ),
     "complex" => array(
            "type" => "checkbox",
            "title" => "Комплекс",
            "in_grid" => false,
            "in_edit" => false,
        ),     
     "isservice" => array(
            "type" => "checkbox",
            "title" => "Услуга",
            "in_grid" => true,
            "in_edit" => true,
            "alt" => "Товар является услугой"
        ),
     
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => true,
            "in_group" => true,
            "in_edit" => true,
            "required"=> false,
            "alt" => "Код для выгрузки"
        ),    
    "i_printer" => array(
            "type" => "db_select",
            "title" => "Подразделение принтеров",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_subdivision'
        ),
    
    "complex" => array(
            "type" => "checkbox",
            "title" => "Комбо",
            "alt" => "Является составным товаром, в который могут входить другие товары, определяющиеся в момент продажи",
            
            "in_grid" => true,
            "in_edit" => true
        ),
    "mainShtrih" => array(
            "type" => "barcode",
            "title" => "Основной штрих-код",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),   
         "loss_cold" => array(
            "type" => "input",
            "title" => "Потери хол., %",
            "default" => 0.00,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "loss_hot" => array(
            "type" => "input",
            "title" => "Потери гор., %",
            "default" => 0.00,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "s_calculations" => array(
            "type" => "db_grid",
            "title" => "Калькуляции",
            "idfield" =>"itemid",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
            "db_grid"=>'s_calculations',
        ), 
  );
  
  
  $tables['d_urv_transactions']['name']='История';  
  $tables['d_urv_transactions']['create_group']=false;  
  $tables['d_urv_transactions']['width']=700;  
  $tables['d_urv_transactions']['height']=550; 
  $fields['d_urv_transactions'] = array (
 "id" => array(
            "type" => "input",
            "title" => "id",
            "in_grid" => false,
            "in_edit" => false
        ),
    "dt" => array(
            "type" => "input",
            "title" => "дата/время",
            "in_grid" => true,
            "in_edit" => false
        ),
    "state" => array(
            "type" => "checkbox",
            "title" => "статус",
            "in_grid" => true,
            "in_edit" => false
        ),
    "id_employee" => array(
            "type" => "db_select",
            "title" => "Сотрудник ",
            "in_grid" => true,
            "selectfield" => 'fio',
            "db_select"=>'s_employee',
            "in_edit" => false
        ),
    "id_pointurv" => array(
            "type" => "db_select",
            "title" => "КПП",
            "in_grid" => true,
            "in_edit" => false,
            "db_select"=>'s_pointurv'
        )
  );

  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  $tables['s_types_of_payment']['name']='Виды оплаты';  
  $tables['s_types_of_payment']['create_group']=false;  
  $tables['s_types_of_payment']['width']=400;  
  $tables['s_types_of_payment']['height']=550;  
  $fields['s_types_of_payment'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "alt" => "Код для выгрузки"
        ),
  );
  
  $tables['s_position']['name']='Должности';  
  $tables['s_position']['create_group']=true;  
  $tables['s_position']['width']=600;  
  $tables['s_position']['height']=550;  
  $fields['s_position'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
  );
  
  
  
  $tables['s_units_of_measurement']['name']='Единицы измерения';  
  $tables['s_units_of_measurement']['create_group']=false;  
  $tables['s_units_of_measurement']['width']=600;  
  $tables['s_units_of_measurement']['height']=550;  
  $fields['s_units_of_measurement'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
  );
  
  
  
  $tables['s_interfaces']['name']='Роли';  
  $tables['s_interfaces']['create_group']=false;  
  $tables['s_interfaces']['width']=600;  
  $tables['s_interfaces']['height']=550;  
  $fields['s_interfaces'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),

    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
  );
  
  $tables['s_role']['name']='Права';  
  $tables['s_role']['create_group']=false;  
  $tables['s_role']['width']=600;  
  $tables['s_role']['height']=550;  
  $fields['s_role'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
  );
  
  
  $tables['s_discount']['name']='Скидки';  
  $tables['s_discount']['create_group']=false;  
  $tables['s_discount']['width']=600;  
  $tables['s_discount']['height']=550;  
  $fields['s_discount'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idpartner" => array(
            "type" => "db_select",
            "title" => "Клиент или группа клиентов",
            "in_grid" => false,
            "in_edit" => false,
            "db_select"=> 's_clients',
            "required"=> false
        ),
    "iditem" => array(
            "type" => "input",
            "title" => "Наименование товара",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "dtstart" => array(
            "type" => "input",
            "title" => "Дата начала",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "dtend" => array(
            "type" => "input",
            "title" => "Дата окончания",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "dayofweek" => array(
            "type" => "input",
            "title" => "День недели",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),    
    "sumvalue" => array(
            "type" => "input",
            "title" => "Сумма скидки",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "usediscountsincafe" => array(
            "type" => "checkbox",
            "title" => "Иcпользовать скидку в кафе",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "usediscountsinfastfood" => array(
            "type" => "checkbox",
            "title" => "Иcпользовать скидку в режиме продаж",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "percentvalue" => array(
            "type" => "input",
            "title" => "Процент скидки",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "useserviceincafe" => array(
            "type" => "checkbox",
            "title" => "Иcпользовать обслуживание в кафе",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "useserviceinfastfood" => array(
            "type" => "checkbox",
            "title" => "Иcпользовать обслуживание в режимe продаж",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),     
      "servicepercent" => array(
            "type" => "input",
            "title" => "Процент обслуживания",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "usegiftsincafe" => array(
            "type" => "checkbox",
            "title" => "Иcпользовать подарок в кафе",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
     "usegiftsinfastfood" => array(
            "type" => "checkbox",
            "title" => "Иcпользовать подарок в режимe продаж",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),     
      "usebalanceincafe" => array(
            "type" => "checkbox",
            "title" => "Иcпользовать накопительную подарочную систему в кафе",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
      "usebalanceinfastfood" => array(
            "type" => "checkbox",
            "title" => "Иcпользовать накопительную подарочную систему в режимe продаж",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
        "t_discount_clients" => array(
            "type" => "db_grid",
            "title" => "Список",
            "in_grid" => false,
            "in_edit" => true,
            "idfield" =>"discountid",
            "required"=> false,
            "db_grid"=>'t_discount_clients'
        ),
        "t_discount_ap" => array(
            "type" => "db_grid",
            "title" => "Точки продаж",
            "in_grid" => false,
            "in_edit" => true,
            "idfield" =>"discountid",
            "required"=> false,
            "db_grid"=>'t_discount_ap'
        ),
  );
  
  $tables['t_discount_ap']['name']='Клиенты в скидках';  
  $tables['t_discount_ap']['create_group']=false;  
  $tables['t_discount_ap']['width']=600;  
  $tables['t_discount_ap']['height']=550;  
  $fields['t_discount_ap'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => false,
            "in_edit" => false,
            "in_group" => false,
            "width" => 190,
            "required"=> false
        ),
     "apid" => array(
            "type" => "db_select",
            "title" => "Торговая точка",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true,
            "db_select"=>'s_automated_point'
        ),
    
  ); 
  
  
  
  $tables['z_logs']['name']='Журнал действий';  
  $tables['z_logs']['create_group']=false;  
  $tables['z_logs']['width']=600;  
  $tables['z_logs']['height']=550;  
  $fields['z_logs'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "date" => array(
            "type" => "input",
            "title" => "Дата",
            "in_grid" => true,
            "in_edit" => false,
            "in_group" => false,
            "width" => 190,
            "required"=> false
        ),
    "desc" => array(
            "type" => "logdesc",
            "title" => "Описание",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "ip" => array(
            "type" => "input",
            "title" => "IP",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "userid" => array(
            "type" => "db_select",
            "title" => "Пользователь",
            "in_grid" => true,
            "in_edit" => false,
            "db_select"=> 's_employee',
            "selectfield" => 'name',
            "required"=> false
        ),
    "type" => array(
            "type" => "logtype",
            "title" => "Тип лога",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    
  );
  
  
  $tables['t_discount_clients']['name']='Клиенты в скидках';  
  $tables['t_discount_clients']['create_group']=false;  
  $tables['t_discount_clients']['width']=600;  
  $tables['t_discount_clients']['height']=550;  
  $fields['t_discount_clients'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => false,
            "in_edit" => false,
            "in_group" => false,
            "width" => 190,
            "required"=> false
        ),
     "clientid" => array(
            "type" => "db_select",
            "title" => "Клиент",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true,
            "db_select"=>'s_clients'
        ),
    
  ); 
  
  $tables['s_warehouse']['name']='Склады';  
  $tables['s_warehouse']['create_group']=false;  
  $tables['s_warehouse']['width']=600;  
  $tables['s_warehouse']['height']=550;  
  $fields['s_warehouse'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
  );  
  
  
  $tables['s_automated_point']['name']='Торговая точка';  
  $tables['s_automated_point']['create_group']=false;  
  $tables['s_automated_point']['width']=610;  
  $tables['s_automated_point']['height']=550;  
  $fields['s_automated_point'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false,
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
   "organisation" => array(
            "type" => "db_select",
            "title" => "Организация",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_organizations'
        ),
   "jTimeStart" => array(
            "type" => "datetime",
            "title" => "Начало рабочего дня",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
        ),
   "jTimeEnd" => array(
            "type" => "datetime",
            "title" => "Конец рабочего дня",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
        ),
   "login" => array(
            "type" => "input",
            "title" => "Логин для входа",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false,
            "alt" => "Логин под которым будет производится вход торговой точки"
        ),
    "password" => array(
            "type" => "password",
            "title" => "Пароль для входа",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ), 
    "pwdexit" => array(
            "type" => "password",
            "title" => "Пароль на выход",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
            "alt" => "Пароль для выхода из торговой точки"
        ),
    "pwdClose" => array(
            "type" => "password",
            "title" => "Пароль на закрытие",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
        ),
    "pwdorderunlock" => array(
            "type" => "password",
            "title" => "Пароль на разблокировку счета",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
            "alt" => "Пароль на разблокировку счета, который был заблокирован командой печати счета"
        ),
    "pwdrefuse" => array(
            "type" => "password",
            "title" => "Пароль на отказ",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
            "alt" => "Пароль на отказ позиций в счете (В режиме официанта)"
        ),
    "pwdclient" => array(
            "type" => "password",
            "title" => "Пароль на выбор клиентов из списка",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "pwdStopList" => array(
            "type" => "password",
            "title" => "Пароль на стоп лист",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "pwdDivide" => array(
            "type" => "password",
            "title" => "Пароль на разделение счета",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
        
    "pwdReturn" => array(
            "type" => "password",
            "title" => "Пароль возврат",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "pwdservicepercent" => array(
            "type" => "password",
            "title" => "Пароль на изменение обслуживания",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "menuid" => array(
            "type" => "db_select",
            "title" => "Меню",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_menu',
            "alt" => "Меню которое используется на данный торговой точке"
        ),
    "cashid" => array(
            "type" => "db_select",
            "title" => "Вид оплаты для оплаты наличными",
            "in_grid" => false,
            "in_edit" => true,
            "db_select"=>'s_types_of_payment',
            "alt" => "Вид оплаты наличными подставляется по умолчанию во всех счетах"
        ),
    "slipid" => array(
            "type" => "db_select",
            "title" => "Вид оплаты для оплаты картой",
            "in_grid" => false,
            "in_edit" => true,
            "db_select"=>'s_types_of_payment',
            "alt" => "Вид оплаты картой используется для печати чека на кассовом аппарате"
        ),
    "cashclientid" => array(
            "type" => "db_select",
            "title" => "Клиент по умолчанию",
            "in_grid" => false,
            "in_edit" => true,
            "db_select"=>'s_clients',
            "alt" => "Клиент который подставляется в счетах по умолчанию(Частное лицо)"
        ),
    "warehouseid" => array(
            "type" => "db_select",
            "title" => "Склад",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_warehouse',
        ),
    "servicepercent" => array(
            "type" => "input",
            "title" => "Процент обслуживания",
            "in_grid" => false,
            "in_edit" => true,
            "alt" => "Процент обслуживания который поставляется по умолчанию"
        ),
    "useservicepercent" => array(
            "type" => "checkbox",
            "title" => "Начислять обслуживание в режимe продаж",
            "in_grid" => false,
            "in_edit" => true
        ),
    "rememberAboutDiscount" => array(
            "type" => "checkbox",
            "title" => "Напоминать о скидках",
            "in_grid" => false,
            "in_edit" => true
        ),
    "useFR" => array(
            "type" => "checkbox",
            "title" => "Использовать Фискальный принтер",
            "in_grid" => false,
            "in_edit" => true
        ),
    "switchOffCompAfterClose" => array(
            "type" => "checkbox",
            "title" => "Выключать компьютер после закрытия программы",
            "in_grid" => false,
            "in_edit" => true
        ),        
    "alwaysUseNote" => array(
            "type" => "checkbox",
            "title" => "Всегда использовать примечания",
            "in_grid" => false,
            "in_edit" => true
        ),
    "zreportonclose" => array(
            "type" => "checkbox",
            "title" => "Снимать Z-отчет при закрытие смены",
            "in_grid" => false,
            "in_edit" => true
        ),
    "printsubord" => array(
            "type" => "checkbox",
            "title" => "Печать подзаказника в кафе",
            "in_grid" => false,
            "in_edit" => true
        ),
    "printsubordinfastfood" => array(
            "type" => "checkbox",
            "title" => "Печать подзаказника в режимe продаж",
            "in_grid" => false,
            "in_edit" => true
        ),
    "printorder" => array(
            "type" => "checkbox",
            "title" => "Печать счета на оплату",
            "in_grid" => false,
            "in_edit" => true,
            "alt" => "Печатать счет на оплату(предчек)"
        ),
    "printorderpay" => array(
            "type" => "checkbox",
            "title" => "Печать счета об оплате",
            "in_grid" => false,
            "in_edit" => true,
            "alt" => "Печатать счет обоплате(финальный чек)"
        ),
    "usechoosetable" => array(
            "type" => "checkbox",
            "title" => "Запрашивать стол при новом счете",
            "in_grid" => true,
            "in_edit" => true
        ),       
    "divChangeWorkplace" => array(
            "type" => "checkbox",
            "title" => "Раздельные смены для рабочих мест",
            "in_grid" => true,
            "in_edit" => true
        ),       
    "useservicepercent" => array(
            "type" => "checkbox",
            "title" => "Начислять обслуживание в режимe продаж",
            "in_grid" => true,
            "in_edit" => true
        ),
    "timezone" => array(
            "type" => "timezone",
            "title" => "Часовой пояс",
            "in_grid" => true,
            "in_edit" => true,
            "alt" => "Часовой пояс торговой точки в зависимости от расположения"
        ),
    "t_workplace" => array(
            "type" => "db_grid",
            "title" => "Рабочее место",
            "idfield" =>"apid",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
            "db_grid"=>'t_workplace',
            "alt" => "Рабочие места которые есть на точке"
        ),
    "with_gifts" => array(
            "type" => "checkbox",
            "title" => "Разрешить использование подарков",
            "in_grid" => false,
            "in_edit" => true
        ),
    "uselocation" => array(
            "type" => "checkbox",
            "title" => "Использовать помещения",
            "in_grid" => false,
            "in_edit" => true,
            "alt" => "При выборе столов, столы будут разделятся на помещения(зал, летка, терасса)"
        ),
    "giftpaytype" => array(
            "type" => "db_select",
            "title" => "Тип оплаты для подарков",
            "in_grid" => false,
            "in_edit" => true,
            "db_select"=>'s_types_of_payment',
            "alt" => "Типо оплаты счетов которые формируются в качестве подарков подарков"
        ),
    "printserverip" => array(
            "type" => "input",
            "title" => "Адрес Print сервера",
            "in_grid" => false,
            "in_edit" => false,
            "width" => 190,
            "required"=> false
        ),
    "nameForPrint" => array(
            "type" => "input",
            "title" => "Отображаемое имя на чеке",
            "in_grid" => false,
            "in_edit" => true,
            "width" => 190,
            "alt" => "Название торговой точки. которое будет печататься в счетах"
        ),
    "askCount" => array(
            "type" => "checkbox",
            "title" => "Запрашивать кол-во при выборе товара",
            "in_grid" => false,
            "in_edit" => true
        ),
    "useChangePrice" => array(
            "type" => "checkbox",
            "title" => "Разрешить изменять цену",
            "in_grid" => true,
            "in_edit" => true
        ),
    "pwdChangePrice" => array(
            "type" => "password",
            "title" => "Пароль для изменения цены",
            "in_grid" => false,
            "in_edit" => true
        ),
    "pwdReportAktReal" => array(
            "type" => "password",
            "title" => "Пароль на отчет - акт реализации",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "pwdReportPoSchetam" => array(
            "type" => "password",
            "title" => "Пароль на - отчет по счетам",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "pwdReportItogoviyReport" => array(
            "type" => "password",
            "title" => "Пароль на - итоговый отчет",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ), 
    "pwdReportZakazOtkaz" => array(
            "type" => "password",
            "title" => "Пароль на отчет - заказы и отказы",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "pwdReportOtkaz" => array(
            "type" => "password",
            "title" => "Пароль на отчет - отказы",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ), 
    "pwdReportX" => array(
            "type" => "password",
            "title" => "Пароль на - Х отчет",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "pwdAddClientFromFront" => array(
            "type" => "password",
            "title" => "Пароль для добавление клиентов в режиме продаж",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "defaultFolderDuringAddClient" => array(
            "type" => "db_select",
            "title" => "Папка по умолчанию при добавление клиентов",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_clients'
        ),
    "useURV" => array(
            "type" => "checkbox",
            "title" => "Использовать УРВ",
            "in_grid" => false,
            "in_edit" => true
        ),     
    "idpointurv" => array(
            "type" => "db_select",
            "title" => "УРВ",
            "in_grid" => false,
            "in_edit" => true,
            "db_select"=>'s_pointurv'                       
        ),
    "pwdCombo" => array(
            "type" => "password",
            "title" => "Пароль на комбо",
            "alt" => "Пароль на редактирование справочника комбо (В режиме кассира)",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "pwdDeleteFromOrder" => array(
            "type" => "password",
            "title" => "Пароль на удаление позиции в заказе",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "infostring" => array(
            "type" => "input",
            "title" => "Информационная строка",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "pwdservicepercent" => array(
            "type" => "password",
            "title" => "Пароль на обслуживание",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "pwdDiscount" => array(
            "type" => "password",
            "title" => "Пароль на скидку",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "doNotUseMenuDesign" => array(
            "type" => "checkbox",
            "title" => "Не использовать дизайнер",
            "in_grid" => false,
            "in_edit" => true
        ),
    "blockZeroSale" => array(
            "type" => "checkbox",
            "title" => "Запретить продажу товаров по нулевым ценам",
            "in_grid" => false,
            "in_edit" => true
        ),
    "searchInMenu" => array(
            "type" => "checkbox",
            "title" => "Поиск по наименованию товара",
            "in_grid" => false,
            "in_edit" => true
        ),
    "materialsSumMoreServiceSum" => array(
            "type" => "checkbox",
            "title" => "Разрешить затраты больше цены услуги",
            "in_grid" => false,
            "in_edit" => true
        ), 
  );
  

        
  $tables['s_pointurv']['name']='КПП';  
  $tables['s_pointurv']['create_group']=false;  
  $tables['s_pointurv']['width']=610;  
  $tables['s_pointurv']['height']=550;  
  $fields['s_pointurv'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false,
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "login" => array(
            "type" => "input",
            "title" => "Логин для входа",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
            "alt" => "Логин под которым будет производится вход торговой точки"
        ),
    "password" => array(
            "type" => "password",
            "title" => "Пароль для входа",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ), 
    "id_location" => array(
            "type" => "db_select",
            "title" => "Помещение",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_location'
        )
    );
  
  $tables['t_workplace']['name']='Рабочее место';  
  $tables['t_workplace']['create_group']=false;  
  $tables['t_workplace']['width']=600;  
  $tables['t_workplace']['height']=550;  
  $fields['t_workplace'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "login" => array(
            "type" => "input",
            "title" => "Логин для входа",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "password" => array(
            "type" => "password",
            "title" => "Пароль",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        )
  );  
  
  
  $tables['s_menu']['name']='Меню';  
  $tables['s_menu']['create_group']=false;  
  $tables['s_menu']['width']=600;  
  $tables['s_menu']['height']=550;  
  $fields['s_menu'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        )
  );  
  
  $tables['s_organizations']['name']='Организации';  
  $tables['s_organizations']['create_group']=false;  
  $tables['s_organizations']['width']=600;  
  $tables['s_organizations']['height']=550;  
  $fields['s_organizations'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),  
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "details" => array(
            "type" => "textarea",
            "title" => "Реквизиты",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
  );  
  
  
  $tables['s_note']['name']='Примечания';  
  $tables['s_note']['create_group']=false;  
  $tables['s_note']['width']=600;  
  $tables['s_note']['height']=550;  
  $fields['s_note'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Товар",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false,
            "db_select"=>'s_items'
        ),
  );  
  
  
  $tables['s_employee']['name']='Сотрудники';  
  $tables['s_employee']['create_group']=true;  
  $tables['s_employee']['width']=820;  
  $tables['s_employee']['height']=550;  
  $fields['s_employee'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ), 
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),  
    "parentid" => array(
            "type" => "db_groupselect",
            "title" => "Группа",
            "in_grid" => false,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false,
            "db_select"=>'s_employee',
            "alt" => "Группа в которой находится сотрудник"
        ), 
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "fio" => array(
            "type" => "input",
            "title" => "ФИО",
            
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "alt" => "ФИО сотрудника"
        ),
    "isuser" => array(
            "type" => "checkbox",
            "title" => "Пользователь системы",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "alt" => "Может входить в систему"
        ),
    "name" => array(
            "type" => "input",
            "title" => "Логин для входа",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "group_title" => "Наименование",
            "width" => 190,
            "required"=> false
        ),
    "password" => array(
            "type" => "password",
            "title" => "Пароль",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
            "alt" => "Пароль под которым будет входить в систему"
        ),
    "phone" => array(
            "type" => "input",
            "title" => "Телефон",
            "mask" => "+7(999)999-99-99",
            "in_grid" => false,
            "in_edit" => true,

            "width" => 190,
            "required"=> false
        ),
    "email" => array(
            "type" => "input",
            "title" => "Емейл",
            "in_grid" => false,
            "in_edit" => true,

            "width" => 190,
            "required"=> false
        ),
    "ident" => array(
            "type" => "input",
            "title" => "Идентификатор",
            "alt" =>   "Ключ для авторизации",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "front2company" => array(
            "type" => "checkbox",
            "title" => "Разрешить вход в режим управления",
            "in_grid" => false,
            "in_edit" => true,
        ), 
    "position" => array(
            "type" => "db_select",
            "title" => "Должность",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false,
            "db_select"=>'s_position'
        ),
    "e_servicepercent" => array(
            "type" => "input",
            "title" => "Процент с обслуживания",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "e_itempercent" => array(
            "type" => "input",
            "title" => "Процент с товаров",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "e_itemservicepercent" => array(
            "type" => "input",
            "title" => "Процент c услуг",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "multiselect" => array(
            "type" => "db_multicheckbox",
            "title" => "Интерфейсы",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
            "field_height"=>'100',
            "field_width"=>'190',
            "selectto_field"=>'id',
            "db_select"=>'s_interfaces',
            "db_selectto"=>'t_employee_interface',
            "to_field"=>'employeeid',
            "select_field"=>'rollid',
            "alt" => "Доступные интерфейсы для пользователя"
        ),
    "multiselect2" => array(
            "type" => "db_multicheckbox",
            "title" => "Права",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
            "field_height"=>'100',
            "field_width"=>'190',
            "selectto_field"=>'id',
            "db_select"=>'s_role',
            "db_selectto"=>'t_employee_role',
            "to_field"=>'employeeid',
            "select_field"=>'rollid',
            "alt" => "Права пользователя, распространяются только на интерфейс менеджер"
        ),
    "multiselect3" => array(
            "type" => "db_multicheckbox",
            "title" => "Торговая точка",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false,
            "field_height"=>'100',
            "field_width"=>'190',
            "selectto_field"=>'id',
            "db_select"=>'s_automated_point',
            "db_selectto"=>'t_employee_automated_point',
            "to_field"=>'employeeid',
            "select_field"=>'automated_pointid',
        ),
    "multiselect4" => array(
            "type" => "db_multicheckbox",
            "title" => "Рабочее место",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
            "field_height"=>'100',
            "field_width"=>'190',
            "selectto_field"=>'id',
            "db_select"=>'t_workplace',
            "db_selectto"=>'t_employee_workplace',
            "to_field"=>'employeeid',
            "select_field"=>'wpid',
            "alt" => "Рабочие места на которых пользователь может входить под своим паролем"
        ),
    "userrights" => array(
            "type" => "userrights",
            "title" => "Права - Торговая точка",
            "in_grid" => false,
            "in_edit" => true,
            "alt" => "Разрешается просматривать данные торговых точек (счета, отчеты)",
            "required"=> false,
            "rightlist"=>array("view"=>"Просмотр"/*,"edit"=>"Редактирование","add"=>"Добавление","delete"=>"Удаление","print"=>"Печать"*/),
            "table"=>'s_automated_point',
            "db_select"=>'t_workplace',
            "db_selectto"=>'t_employee_workplace',
            "to_field"=>'employeeid',
            "select_field"=>'wpid',
        ),
        
    "userrights2" => array(
            "type" => "userrights",
            "title" => "Права - Меню",
            "in_grid" => false,
            "alt" => "Разрешается редактирование меню (дизайнер меню)",
            "in_edit" => true,
            "required"=> false,
            "rightlist"=>array("view"=>"Просмотр"/*,"edit"=>"Редактирование","add"=>"Добавление","delete"=>"Удаление","print"=>"Печать"*/),
            "table"=>'s_menu',
            "db_select"=>'t_workplace',
            "db_selectto"=>'t_employee_workplace',
            "to_field"=>'employeeid',
            "select_field"=>'wpid',
        ),
    "task" => array(
            "type" => "db_multicheckbox",
            "title" => "Управление пользователями",
            "alt" => "Ползователи, задачи которых можно просматривать",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
            "rightlist"=>array("view"=>"Просмотр"/*,"edit"=>"Редактирование","add"=>"Добавление","delete"=>"Удаление","print"=>"Печать"*/),
            "selectto_field"=>'id',
            "db_select"=>'s_employee',
            "db_selectto"=>'crm_mgr_view',
            "to_field"=>'id_mgr',
            "select_field"=>'id_user',

        ),
    "grafic_id" => array(
            "type" => "db_select",
            "title" => "График",
            "in_grid" => false,
            "in_edit" => true,
            "db_select"=>'urv_graphic'
        ),
    "id_depart" => array(
            "type" => "db_select",
            "title" => "Отдел",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_department'
        ),
    "id_location" => array(
            "type" => "db_select",
            "title" => "Помещение",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_location'
        )

  ); 
  
 $tables['s_department']['name']='Отделение';  
 $tables['s_department']['create_group']=true;  
 $tables['s_department']['width']=700;  
 $tables['s_department']['height']=550; 
 $fields['s_department'] = array (
     "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
     "name" => array(
            "type" => "input",
            "title" => "Название",
            "group_title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true
        )
  );
  
  $tables['urv_graphic']['name']='График';  
  $tables['urv_graphic']['create_group']=true;  
  $tables['urv_graphic']['width']=630;  
  $tables['urv_graphic']['height']=550;  
  $fields['urv_graphic'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        )
    );
  
   
  
  $tables['s_goods']['name']='Товары';  
  $tables['s_goods']['create_group']=true;  
  $tables['s_goods']['width']=630;  
  $tables['s_goods']['height']=550;  
  $fields['s_goods'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),  
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "measurement" => array(
            "type" => "db_select",
            "title" => "Еденица измерения",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_units_of_measurement'
        ),
    "price" => array(
            "type" => "input",
            "title" => "Цена",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "weight" => array(
            "type" => "checkbox",
            "title" => "Весовой",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
        ),
    "complex" => array(
            "type" => "checkbox",
            "title" => "Комплексный",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
        )
  );  
  
  
  $tables['t_menu_items']['name']='Меню';  
  $tables['t_menu_items']['create_group']=false;  
  $tables['t_menu_items']['width']=600;  
  $tables['t_menu_items']['height']=550;  
  $fields['t_menu_items'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "itemmenu",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false,
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "price" => array(
            "type" => "input",
            "title" => "Цена",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "printer" => array(
            "type" => "db_select",
            "title" => "Подразделение",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_subdivision'
        ),
    "sortid" => array(
            "type" => "input",
            "title" => "Поле сортировки",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
  );  
  
  $tables['s_printers']['name']='Принтер';  
  $tables['s_printers']['create_group']=false;  
  $tables['s_printers']['width']=400;  
  $tables['s_printers']['height']=550;  
  $fields['s_printers'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_group" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),      
    "sysname" => array(
            "type" => "input",
            "title" => "Системное имя",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false,
            "alt" => "Имя принтера из операционной системы"
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),    
  ); 
  
  $tables['s_subdivision']['name']='Подразделения принтеров';  
  $tables['s_subdivision']['create_group']=false;  
  $tables['s_subdivision']['width']=400;  
  $tables['s_subdivision']['height']=550;  
  $fields['s_subdivision'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),  
    "printerid" => array(
            "type" => "db_select",
            "title" => "Принтер",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_printers'
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "alt" => "Код для выгрузки"
        ),
    "warehouseid" => array(
            "type" => "db_select",
            "title" => "Склад",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_warehouse'
        ),   
  ); 
  
  
  
  $tables['s_location']['name']='Помещения';  
  $tables['s_location']['create_group']=false;  
  $tables['s_location']['width']=400;  
  $tables['s_location']['height']=550;  
  $fields['s_location'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "pointid" => array(
            "type" => "db_select",
            "title" => "Торговая точка",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_automated_point'
        ),
  ); 
  
  $tables['s_objects']['name']='Столы';  
  $tables['s_objects']['create_group']=false;  
  $tables['s_objects']['width']=400;  
  $tables['s_objects']['height']=550;  
  $fields['s_objects'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "locationid" => array(
            "type" => "db_select",
            "title" => "Помещение",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_location'
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    "servicepercent" => array(
            "type" => "input",
            "title" => "Обслуживание(-1)",
            "in_grid" => false,
            "in_edit" => false,
            "width" => 190,
            "required"=> false
        ),
    
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "alt" => "Код для выгрузки"
        ),
   /* "locationid" => array(
            "type" => "db_select",
            "title" => "Помещения",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_location'
        ),*/
  ); 
  
  
  $tables['d_order']['name']='Счет заказы';  
  $tables['d_order']['create_group']=false;  
  $tables['d_order']['width']=850;  
  $tables['d_order']['height']=550;  
  $fields['d_order'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ), 
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),  
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => false,
            "in_edit" => false,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),        
     "idautomated_point" => array(
            "type" => "db_select",
            "title" => "Торговая точка",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_automated_point'
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "creationdt" => array(
            "type" => "input",
            "title" => "Дата создания",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "employeeid" => array(
            "type" => "db_select",
            "title" => "Официант",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "selectfield" => 'fio',
            "db_select"=>'s_employee'
        ), 
    "clientid" => array(
            "type" => "db_select",
            "title" => "Клиент",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_clients'
        ),
    "guestcount" => array(
            "type" => "input",
            "title" => "Количество гостей",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "paymentid" => array(
            "type" => "db_select",
            "title" => "Вид оплаты",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_types_of_payment'
        ),
    "objectid" => array(
            "type" => "db_select",
            "title" => "Стол",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_objects'
        ),
    "dtclose" => array(
            "type" => "input",
            "title" => "Дата закрытия",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ), 
     "totalsum" => array(
            "type" => "input",
            "title" => "Сумма",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
     "servicepercent" => array(
            "type" => "input",
            "title" => "Процент обслуживания",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),    
     "servicesum" => array(
            "type" => "input",
            "title" => "Сумма обслуживания",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ), 
     "discountpercent" => array(
            "type" => "input",
            "title" => "Скидка",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "discountsum" => array(
            "type" => "input",
            "title" => "Сумма скидки",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "printed" => array(
            "type" => "input",
            "title" => "Распечатан",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "closed" => array(
            "type" => "input",
            "title" => "Закрыт",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),  
    "changeid" => array(
            "type" => "db_select",
            "title" => "Смена",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'d_changes'
        ),
    "barcode" => array(
            "type" => "input",
            "title" => "Баркод",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),   
/*    "handservicepercent" => array(
            "type" => "input",
            "title" => "Процент на руки",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),*/
    "t_order" => array(
            "type" => "db_grid",
            "title" => "Список",
            "in_grid" => false,
            "in_edit" => true,
            "idfield" =>"orderid",
            "required"=> false,
            "db_grid"=>'t_order'
        ),
  ); 
  
  $tables['s_clients']['name']='Клиенты';  
  $tables['s_clients']['create_group']=true;  
  $tables['s_clients']['width']=400;  
  $tables['s_clients']['height']=550;  
  $fields['s_clients'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "parentid" => array(
            "type" => "db_groupselect",
            "title" => "Группа",
            "in_grid" => false,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false,
            "db_select"=>'s_clients',
            "alt" => "Группа в которой находится клиент"
        ),
    "name" => array(
            "type" => "input",
            "title" => "ФИО клиента",
            "group_title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
/*    "discount" => array(
            "type" => "input",
            "title" => "Скидка",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),*/
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "alt" => "Код для выгрузки"
        ),
    /*"servicepercent" => array(
            "type" => "input",
            "title" => "Процент обслуживания",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),*/
    "shtrih" => array(
            "type" => "input",
            "title" => "Код карты",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "alt" => "Код карты по которой можно найти и выбрать клиента"
        ),
    "birthday" => array(
            "type" => "date",
            "title" => "Дата рождения",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "email" => array(
            "type" => "input",
            "title" => "Емайл",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "phone" => array(
            "type" => "input",
            "title" => "Телефон",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "address" => array(
            "type" => "input",
            "title" => "Адрес",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "info" => array(
            "type" => "input",
            "title" => "Дополнительная информация",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "details" => array(
            "type" => "textarea",
            "title" => "Реквизиты",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
  ); 
  
  $tables['t_order']['name']='Подзаказ';  
  $tables['t_order']['create_group']=true;  
  $tables['t_order']['width']=400;  
  $tables['t_order']['height']=550;  
  $fields['t_order'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => false,
            "in_edit" => false,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Товар",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_items'
        ),
    "orderid" => array(
            "type" => "db_select",
            "title" => "Номер документа",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false,
            "db_select"=>'d_order'
        ),
    "price" => array(
            "type" => "input",
            "title" => "Цена",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ), 
    "quantity" => array(
            "type" => "input",
            "title" => "Количество",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "sum" => array(
            "type" => "input",
            "title" => "Сумма",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "note" => array(
            "type" => "input",
            "title" => "Примечание",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    
  );
  
  $tables['d_changes']['name']='Смена';  
  $tables['d_changes']['create_group']=false;  
  $tables['d_changes']['width']=400;  
  $tables['d_changes']['height']=550;  
  $fields['d_changes'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "employeeid" => array(
            "type" => "db_select",
            "title" => "Сотрудник",
            "in_grid" => true,
            "in_edit" => true,
            "selectfield" => 'fio',
            "db_select"=>'s_employee',
            "required"=> false
        ),
    "dtopen" => array(
            "type" => "input",
            "title" => "Дата открытия",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "dtclosed" => array(
            "type" => "input",
            "title" => "Дата закрытия",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "closed" => array(
            "type" => "checkbox",
            "title" => "Открыта/закрыта",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
  ); 
  
  $tables['s_config']['name']='Конфиг';  
  $tables['s_config']['create_group']=true;  
  $tables['s_config']['width']=400;  
  $tables['s_config']['height']=550;  
  $fields['s_config'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    "value" => array(
            "type" => "input",
            "title" => "Значение",
            "in_grid" => true,
            "in_edit" => true,
        )
  ); 
  
  
  $tables['s_gifts']['name']='Подарки';  
  $tables['s_gifts']['create_group']=false;  
  $tables['s_gifts']['width']=400;  
  $tables['s_gifts']['height']=550;  
  $fields['s_gifts'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "levelid" => array(
            "type" => "db_select",
            "title" => "Уровень",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_giftlevels'
        ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Товар",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_items'
        ),
     "quantity" => array(
            "type" => "input",
            "title" => "Количество",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        )
  );
  
  
  $tables['s_giftlevels']['name']='Уровни подарков';  
  $tables['s_giftlevels']['create_group']=false;  
  $tables['s_giftlevels']['width']=400;  
  $tables['s_giftlevels']['height']=550;  
  $fields['s_giftlevels'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_group" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    "leveldiscountid" => array(
            "type" => "db_select",
            "title" => "Скидка ",
            "in_grid" => true,
            "in_edit" => true,
            "width" =>250,
            "db_select"=>'s_discount'
        ),
    
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),

    "leveltype" => array(
            "type" => "db_select",
            "title" => "Тип уровня",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'v_typegift'
        ),
    "levelnum" => array(
            "type" => "input",
            "title" => "Номер уровня",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "pointscount" => array(
            "type" => "input",
            "title" => "Кол-во баллов",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "itemcount" => array(
            "type" => "input",
            "title" => "Кол-во товара",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
       ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Товар",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_items'
        ),
    "ballcount" => array(
            "type" => "input",
            "title" => "Количество начисляемых баллов",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "price" => array(
            "type" => "input",
            "title" => "Цена балла",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),    
  );
  
  
  
  
  
  $tables['sms_logs']['name']='SMS логи';  
  $tables['sms_logs']['create_group']=false;  
  $tables['sms_logs']['width']=700;  
  $tables['sms_logs']['height']=550;  
  $fields['sms_logs'] = array (
    "add_date" => array(
            "type" => "input",
            "title" => "дата отправления",
            "in_grid" => true,
            "in_edit" => false,
            "width" =>100,
        ),
    "id_user" => array(
            "type" => "db_select",
            "title" => "отправитель",
            "in_grid" => true,
            "in_edit" => false,
            "width" =>250,
            "selectfield" => 'fio',
            "db_select"=>'s_employee'
        ),
    "id_client" => array(
            "type" => "db_select",
            "title" => "Клиент ",
            "in_grid" => true,
            "in_edit" => false,
            "width" =>250,
            "db_select"=>'s_clients'
        ),
    "phone" => array(
            "type" => "input",
            "title" => "телефон",
            "in_grid" => true,
            "in_edit" => false,
            "width" =>100,
        ),
     "id_text_msg" => array(
            "type" => "db_select",
            "title" => "текст сообщения ",
            "in_grid" => true,
            "in_edit" => false,
            "db_select"=>'sms_textMsg'
        ),
    "status" => array(
            "type" => "input",
            "title" => "отправлено ",
            "in_grid" => true,
            "in_edit" => false,
            "width" =>100
        ),
  );


  $tables['sms_mask']['name']='Редактор масок';  
  $tables['sms_mask']['create_group']=false;  
  $tables['sms_mask']['width']=500;  
  $tables['sms_mask']['height']=450;  
  $fields['sms_mask'] = array (
    "name" => array(
            "type" => "input",
            "title" => "маска ",
            "in_grid" => true,
            "in_edit" => true,
            "width" =>250,
            "alt" => "маска для фильтрации телефонов"
        ),
    "description" => array(
            "type" => "input",
            "title" => "название маски",
            "in_grid" => true,
            "in_edit" => true,
            "width" =>250
        )
  );
  
  
  

  
  
  $tables['sms_template']['name']='Редактор шаблонов';  
  $tables['sms_template']['create_group']=false;  
  $tables['sms_template']['width']=700;  
  $tables['sms_template']['height']=550;  
  $fields['sms_template'] = array (
    "nameTemplate" => array(
            "type" => "input",
            "title" => "Название шаблона",
            "in_grid" => true,
            "in_edit" => true,
            "required" => true,
            "width" =>100,
            "alt" => "название шаблона вводится для его выбора при отправке собщений",
            "after_text" => "",
            "in_group" => false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Текст шаблона ",
            "in_grid" => true,
            "in_edit" => true,
            "width" =>250
        )
  );
  /*
  *,
    "good" => array(
            "type" => "db_select",
            "title" => "Товар",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=>'s_goods'
        ),
    "price" => array(
            "type" => "input",
            "title" => "Цена",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ), 
  */
  
  
  
  
  
  
  $tables['s_tarifs']['name']='Тарифы';  
  $tables['s_tarifs']['create_group']=false;  
  $tables['s_tarifs']['width']=600;  
  $tables['s_tarifs']['height']=550;  
  $fields['s_tarifs'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    "dtstart" => array(
            "type" => "datetime",
            "title" => "Начало действия тарифа",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Товар",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=> 's_items'
        ),    
    "interval_default" => array(
            "type" => "input",
            "title" => "Интервал (мин.)",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ), 
    "price_default" => array(
            "type" => "input",
            "title" => "Цена",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "t_tarifs" => array(
            "type" => "db_grid",
            "title" => "Тариф",
            "idfield" =>"tarifid",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
            "db_grid"=>'t_tarifs'
    ),
    "t_object_tarif" => array(
            "type" => "db_grid",
            "title" => "Объект",
            "idfield" =>"tarifid",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false,
            "db_grid"=>'t_object_tarif'
  ));  
  
  
  
  $tables['t_object_tarif']['name']='Тарифъ';  
  $tables['t_object_tarif']['create_group']=false;  
  $tables['t_object_tarif']['width']=600;  
  $tables['t_object_tarif']['height']=550;  
  $fields['t_object_tarif'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
    ), 
    "idobject" => array(
            "type" => "db_select",
            "title" => "Объект",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false,
            "db_select"=> 's_objects'
  )
    
    );
        
  $tables['t_tarifs']['name']='Тарифы';  
  $tables['t_tarifs']['create_group']=false;  
  $tables['t_tarifs']['width']=600;  
  $tables['t_tarifs']['height']=550;  
  $fields['t_tarifs'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    "mon" => array(
            "type" => "checkbox",
            "title" => "Понедельник",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ), 
    "tue" => array(
            "type" => "checkbox",
            "title" => "Вторник",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "wed" => array(
            "type" => "checkbox",
            "title" => "Среда",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "thu" => array(
            "type" => "checkbox",
            "title" => "Четверг",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "fri" => array(
            "type" => "checkbox",
            "title" => "Пятница",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "sat" => array(
            "type" => "checkbox",
            "title" => "Суббота",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "sun" => array(
            "type" => "checkbox",
            "title" => "Воскресенье",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ), 
    "timeStart_tar" => array(
            "type" => "time",
            "title" => "Время начала",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ), 
    "timeEnd_tar" => array(
            "type" => "time",
            "title" => "Время окончания",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "interval" => array(
            "type" => "input",
            "title" => "Интервал",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "price" => array(
            "type" => "input",
            "title" => "Цена",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
  );  
  
  
  $tables['s_combo_groups']['name']='Комбо группы';  
  $tables['s_combo_groups']['create_group']=false;  
  $tables['s_combo_groups']['width']=600;  
  $tables['s_combo_groups']['height']=550;  
  $fields['s_combo_groups'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "width" => 190,
            "required"=> false
        ),
    "price" => array(
            "type" => "input",
            "title" => "Цена",
            "in_grid" => false,
            "numeric" => true,
            "in_edit" => true,
        ),
    "mincount" => array(
            "type" => "input",
            "title" => "Минимальное кол-во",
            "in_grid" => false,
            "in_edit" => true,
        ),
    "maxcount" => array(
            "type" => "input",
            "title" => "Максимальное кол-во",
            "in_grid" => false,
            "in_edit" => true,
        ),
    "defaultitem" => array(
            "type" => "db_select",
            "db_select" => 's_combo_items',
            "title" => "Блюдо по умолчанию",
            "selectfield" => 'itemid',
            "in_grid" => false,
            "in_edit" => true,
        )
    );
    
    
  $tables['s_combo_items']['name']='Элементы комбо';  
  $tables['s_combo_items']['create_group']=false;  
  $tables['s_combo_items']['width']=600;  
  $tables['s_combo_items']['height']=550;  
  $fields['s_combo_items'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Товар",
            "in_grid" => true,
            "db_select" => 's_items',
            "in_edit" => true,
            "in_group" => true,
        ),
    "price" => array(
            "type" => "input",
            "title" => "Цена",
            "in_grid" => false,
            "in_edit" => true,
        ),
    "printer" => array(
            "type" => "db_select",
            "title" => "Подразделение",
            "in_grid" => true,
            "db_select" => 's_subdivision',
            "in_edit" => true,
            "in_group" => true,
        )
    ); 
    
  $tables['z_default_values']['name']='Значения по умолчанию';  
  $tables['z_default_values']['create_group']=false;  
  $tables['z_default_values']['width']=600;  
  $tables['z_default_values']['height']=550;  
  $fields['z_default_values'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
            ),
    "idout" => array(
            "type" => "input",
            "title" => "Код",
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "in_group" => true,
            "required"=> false
        ),
    "employeeid" => array(
            "type" => "db_select",
            "title" => "Пользователь или группа",
            "in_grid" => true,
            "db_select" => 's_employee',
            "in_edit" => true,
            "in_group" => true,
        ),
    "1" => array(
            "type" => "defaults",
            "fieldname" => "i_printer",
            "title" => "Принтер",
            "src_table" => "s_subdivision",
            "src_title" => "name",
            "in_grid" => false,
            "in_edit" => true,
        ),
    "2" => array(
            "type" => "defaults",
            "title" => "Ед.из",
            "fieldname" => "measurement",
            "src_table" => "s_units_of_measurement",
            "src_title" => "name",
            "in_grid" => false,
            "in_edit" => true,
        ),
    "3" => array(
            "type" => "defaults",
            "title" => "Склад",
            "fieldname" => "warehouseid",
            "src_table" => "s_warehouse",
            "src_title" => "name",
            "in_grid" => false,
            "in_edit" => true,
        ), 
    "4" => array(
            "type" => "defaults",
            "title" => "Организация",
            "fieldname" => "organizationid",
            "src_table" => "s_organizations",
            "src_title" => "name",
            "in_grid" => false,
            "in_edit" => true,
        ),
    "5" => array(
            "type" => "defaults",
            "title" => "Контрагент",
            "fieldname" => "clientid ",
            "src_table" => "s_clients",
            "src_title" => "name",
            "in_grid" => false,
            "in_edit" => true,
        ),
    
    );
  
?>
