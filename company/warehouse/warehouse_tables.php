<?php

  $tables['s_multipliers']['name']='Коэффициенты';  
  $tables['s_multipliers']['create_group']=false;  
  $tables['s_multipliers']['width']=600;  
  $tables['s_multipliers']['height']=550;  
  $fields['s_multipliers'] = array (
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
            "required" => true,
            //Значение по дефолту
            "default" => 1,
            //Ширина поля в окне редактирования
            "field_width" => 100,
            //Ширина поля в гриде
            "width" => 100,
            //подсказка
            "alt" => "Айди товара",
            //тект после поля
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
     "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
     "frommeasureid" => array(
            "type" => "db_select",
            "db_select"=>'s_units_of_measurement',
            "title" => "Исходная ед. изм.",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "tomeasureid" => array(
            "type" => "db_select",
            "db_select"=>'s_units_of_measurement',
            "title" => "Целевая ед. изм.",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "multip" => array(
            "type" => "input",
            "title" => "Коэф.",
            "in_grid" => true,
            "in_edit" => true,
            "default" => 1.000,
            "required"=> true
        )
      );

  $tables['d_receipt']['name']='Поступление товаров';  
  $tables['d_receipt']['create_group']=false;  
  $tables['d_receipt']['width']=600;  
  $tables['d_receipt']['height']=550;  
  $fields['d_receipt'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код документа",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Код обмена",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "employeeid" => array(
            "type" => "label",
            "title" => "Автор документа",
            "db_select"=>'s_employee',
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "organizationid" => array(
            "type" => "db_select",
            "title" => "Организация",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_organizations',
            //"field_width" => 455,
            "required"=> true
        ),
    "warehouseid" => array(
            "type" => "db_select",
            "title" => "Склад",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_warehouse',
            //"field_width" => 455,
            "required"=> true
        ),
    "clientid" => array(
            "type" => "db_select",
            "title" => "Поставщик",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_clients',
            //"field_width" => 455,
            "required"=> true
        ),
    "conducted" => array(
            "type" => "checkbox",
            "title" => "Проведен",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "t_receipt" => array(
            "type" => "db_grid",
            "title" => "Товары",
            "in_grid" => false,
            "in_edit" => true,
            "idfield" =>"documentid",
            "required"=> false,
            "db_grid"=>'t_receipt'
        ),
    "total" => array(
            "type" => "sum",
            "title" => "Сумма",
            "db_select" => 't_receipt',
            "idfield" => "documentid",
            "sumfield" => "quantity * price * multip",
            "precision" => 2,
            "in_grid" => true,
            "in_edit" => true
        ),
    "note" => array(
            "type" => "input",
            "title" => "Комментарий",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        )
      );
  
  $tables['t_receipt']['name']='Товар';//'Поступившие товары';
  $tables['t_receipt']['create_group']=false;  
  $tables['t_receipt']['width']=600;  
  $tables['t_receipt']['height']=550;  
  $fields['t_receipt'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
     "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "documentid" => array(
            "type" => "db_select",
            "db_select"=>'d_receipt',
            "title" => "Документ",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> true
        ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Товар",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_items',
            //"field_width" => 455,
            "required"=> true
        ),
    "specificationid" => array(
            "type" => "db_select",
            "title" => "Характеристика",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=> 's_specifications',
            //"field_width" => 455,
            "required"=> false
        ),
    "quantity" => array(
            "type" => "input",
            "title" => "Кол-во",
            "default" => 0.000,
            "precision" => 3,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> true
        ),
    "measureid" => array(
            "type" => "db_select",
            "title" => "Ед. изм.",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_units_of_measurement',
            //"field_width" => 455,
            "required"=> true
        ),
    "multip" => array(
            "type" => "input",
            "title" => "Коэф.",
            "default" => 1.000,
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "price" => array(
            "type" => "input",
            "title" => "Цена",
            "default" => 0.000,
            "precision" => 2,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "total" => array(
            "type" => "rowsum",
            "title" => "Сумма",
            "precision" => 2,
            "in_grid" => true,
            "in_edit" => false
        )
      );
  
  $tables['d_selling']['name']='Реализация товаров';  
  $tables['d_selling']['create_group']=false;  
  $tables['d_selling']['width']=600;  
  $tables['d_selling']['height']=550;  
  $fields['d_selling'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код документа",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Код обмена",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
     "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "employeeid" => array(
            "type" => "label",
            "title" => "Автор документа",
            "db_select"=>'s_employee',
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "organizationid" => array(
            "type" => "db_select",
            "title" => "Организация",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_organizations',
            //"field_width" => 455,
            "required"=> true
        ),
    "warehouseid" => array(
            "type" => "db_select",
            "title" => "Склад",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_warehouse',
            //"field_width" => 455,
            "required"=> true
        ),
    "clientid" => array(
            "type" => "db_select",
            "title" => "Заказчик",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_clients',
            //"field_width" => 455,
            "required"=> true
        ),
    "conducted" => array(
            "type" => "checkbox",
            "title" => "Проведен",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "t_selling" => array(
            "type" => "db_grid",
            "title" => "Товары",
            "in_grid" => false,
            "in_edit" => true,
            "idfield" =>"documentid",
            "required"=> false,
            "db_grid"=>'t_selling'
        ),
    "total" => array(
            "type" => "sum",
            "title" => "Сумма",
            "db_select" => 't_selling',
            "idfield" => "documentid",
            "sumfield" => "quantity * price * multip",
            "precision" => 2,
            "in_grid" => true,
            "in_edit" => true
        ),
    "note" => array(
            "type" => "input",
            "title" => "Комментарий",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        )
      );
  
  $tables['t_selling']['name']='Товар';//'Реализуемые товары';
  $tables['t_selling']['create_group']=false;  
  $tables['t_selling']['width']=600;  
  $tables['t_selling']['height']=550;  
  $fields['t_selling'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "documentid" => array(
            "type" => "db_select",
            "title" => "Документ",
            "in_grid" => false,
            "in_edit" => false,
            "db_select"=>'d_selling',
            "required"=> true
        ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Товар",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_items',
            //"field_width" => 455,
            "required"=> true
        ),
    "specificationid" => array(
            "type" => "db_select",
            "title" => "Характеристика",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=> 's_specifications',
            //"field_width" => 455,
            "required"=> false
        ),
    "quantity" => array(
            "type" => "input",
            "title" => "Кол-во",
            "default" => 0.000,
            "precision" => 3,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> true
        ),
    "measureid" => array(
            "type" => "db_select",
            "title" => "Ед. изм.",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_units_of_measurement',
            //"field_width" => 455,
            "required"=> true
        ),
    "multip" => array(
            "type" => "input",
            "title" => "Коэф.",
            "default" => 1.000,
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "price" => array(
            "type" => "input",
            "title" => "Цена",
            "default" => 0.000,
            "precision" => 2,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "total" => array(
            "type" => "rowsum",
            "title" => "Сумма",
            "precision" => 2,
            "in_grid" => true,
            "in_edit" => false
        )
      );
  
  $tables['d_posting']['name']='Оприходование';  
  $tables['d_posting']['create_group']=false;  
  $tables['d_posting']['width']=600;  
  $tables['d_posting']['height']=550;  
  $fields['d_posting'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код документа",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Код обмена",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
     "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "employeeid" => array(
            "type" => "label",
            "title" => "Автор документа",
            "db_select"=>'s_employee',
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "organizationid" => array(
            "type" => "db_select",
            "title" => "Организация",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_organizations',
            //"field_width" => 455,
            "required"=> true
        ),
    "warehouseid" => array(
            "type" => "db_select",
            "title" => "Склад",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_warehouse',
            //"field_width" => 455,
            "required"=> true
        ),
    "conducted" => array(
            "type" => "checkbox",
            "title" => "Проведен",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "t_posting" => array(
            "type" => "db_grid",
            "title" => "Товары",
            "in_grid" => false,
            "in_edit" => true,
            "idfield" =>"documentid",
            "required"=> false,
            "db_grid"=>'t_posting'
        ),
    "total" => array(
            "type" => "sum",
            "title" => "Сумма",
            "db_select" => 't_posting',
            "idfield" => "documentid",
            "sumfield" => "quantity * price * multip",
            "precision" => 2,
            "in_grid" => true,
            "in_edit" => true
        ),
    "note" => array(
            "type" => "input",
            "title" => "Комментарий",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        )
      );
  
  $tables['t_posting']['name']='Товар';//'Оприходованные товары';
  $tables['t_posting']['create_group']=false;  
  $tables['t_posting']['width']=600;  
  $tables['t_posting']['height']=550;  
  $fields['t_posting'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
     "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "documentid" => array(
            "type" => "db_select",
            "title" => "Документ",
            "in_grid" => false,
            "in_edit" => false,
            "db_select"=>'d_posting',
            "required"=> true
        ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Товар",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_items',
            //"field_width" => 455,
            "required"=> true
        ),
    "specificationid" => array(
            "type" => "db_select",
            "title" => "Характеристика",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=> 's_specifications',
            //"field_width" => 455,
            "required"=> false
        ),
    "quantity" => array(
            "type" => "input",
            "title" => "Кол-во",
            "default" => 0.000,
            "precision" => 3,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> true
        ),
    "measureid" => array(
            "type" => "db_select",
            "title" => "Ед. изм.",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_units_of_measurement',
            //"field_width" => 455,
            "required"=> true
        ),
    "multip" => array(
            "type" => "input",
            "title" => "Коэф.",
            "default" => 1.000,
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "price" => array(
            "type" => "input",
            "title" => "Цена",
            "default" => 0.000,
            "precision" => 2,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "total" => array(
            "type" => "rowsum",
            "title" => "Сумма",
            "precision" => 2,
            "precision" => 2,
            "in_grid" => true,
            "in_edit" => false
        )
      );
  
  $tables['d_cancellation']['name']='Списание';  
  $tables['d_cancellation']['create_group']=false;  
  $tables['d_cancellation']['width']=600;  
  $tables['d_cancellation']['height']=550;  
  $fields['d_cancellation'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код документа",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Код обмена",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
     "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "employeeid" => array(
            "type" => "label",
            "title" => "Автор документа",
            "db_select"=>'s_employee',
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "organizationid" => array(
            "type" => "db_select",
            "title" => "Организация",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_organizations',
            //"field_width" => 455,
            "required"=> true
        ),
    "warehouseid" => array(
            "type" => "db_select",
            "title" => "Склад",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_warehouse',
            //"field_width" => 455,
            "required"=> true
        ),
    "conducted" => array(
            "type" => "checkbox",
            "title" => "Проведен",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "t_cancellation" => array(
            "type" => "db_grid",
            "title" => "Товары",
            "in_grid" => false,
            "in_edit" => true,
            "idfield" =>"documentid",
            "required"=> false,
            "db_grid"=>'t_cancellation'
        ),
   /* "total" => array(
            "type" => "sum",
            "title" => "Сумма",
            "db_select" => 't_cancellation',
            "idfield" => "documentid",
            "sumfield" => "quantity * price * multip",
            "precision" => 2,
            "in_grid" => true,
            "in_edit" => true
        ),*/
    "note" => array(
            "type" => "input",
            "title" => "Комментарий",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        )
      );
  
  $tables['t_cancellation']['name']='Товар';//Списываемые товары';
  $tables['t_cancellation']['create_group']=false;  
  $tables['t_cancellation']['width']=600;  
  $tables['t_cancellation']['height']=550;  
  $fields['t_cancellation'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
     "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "documentid" => array(
            "type" => "db_select",
            "title" => "Документ",
            "in_grid" => false,
            "in_edit" => false,
            "db_select"=>'d_cancellation',
            "required"=> true
        ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Товар",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_items',
            //"field_width" => 455,
            "required"=> true
        ),
    "specificationid" => array(
            "type" => "db_select",
            "title" => "Характеристика",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=> 's_specifications',
            //"field_width" => 455,
            "required"=> false
        ),
    "quantity" => array(
            "type" => "input",
            "title" => "Кол-во",
            "default" => 0.000,
            "precision" => 3,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> true
        ),
    "measureid" => array(
            "type" => "db_select",
            "title" => "Ед. изм.",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_units_of_measurement',
            //"field_width" => 455,
            "required"=> true
        ),
    "multip" => array(
            "type" => "input",
            "title" => "Коэф.",
            "default" => 1.000,
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        )/*,
    "price" => array(
            "type" => "input",
            "title" => "Цена",
            "default" => 0.000,
            "precision" => 2,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "total" => array(
            "type" => "rowsum",
            "title" => "Сумма",
            "precision" => 2,
            "in_grid" => true,
            "in_edit" => false
        )*/
      );
  
  $tables['d_inventory']['name']='Инвентаризация';  
  $tables['d_inventory']['create_group']=false;  
  $tables['d_inventory']['width']=600;  
  $tables['d_inventory']['height']=550;  
  $fields['d_inventory'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код документа",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Код обмена",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
     "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "employeeid" => array(
            "type" => "label",
            "title" => "Автор документа",
            "db_select"=>'s_employee',
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "organizationid" => array(
            "type" => "db_select",
            "title" => "Организация",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_organizations',
            //"field_width" => 455,
            "required"=> true
        ),
    "warehouseid" => array(
            "type" => "db_select",
            "title" => "Склад",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_warehouse',
            //"field_width" => 455,
            "required"=> true
        ),
    "conducted" => array(
            "type" => "checkbox",
            "title" => "Проведен",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "t_inventory" => array(
            "type" => "db_grid",
            "title" => "Товары",
            "in_grid" => false,
            "in_edit" => true,
            "idfield" =>"documentid",
            "required"=> false,
            "db_grid"=>'t_inventory'
        ),
    "totalplanned" => array(
            "type" => "sum",
            "title" => "Учетная сумма",
            "db_select" => 't_inventory',
            "idfield" => "documentid",
            "sumfield" => "plannedquantity * price * multip",
            "precision" => 2,
            "in_grid" => true,
            "in_edit" => true
        ),
    "total" => array(
            "type" => "sum",
            "title" => "Сумма",
            "db_select" => 't_inventory',
            "idfield" => "documentid",
            "sumfield" => "quantity * price * multip",
            "precision" => 2,
            "in_grid" => true,
            "in_edit" => true
        ),
    "totaldiff" => array(
            "type" => "sum",
            "title" => "Отклонение",
            "db_select" => 't_inventory',
            "idfield" => "documentid",
            "sumfield" => "quantity - plannedquantity",
            "precision" => 3,
            "in_grid" => true,
            "in_edit" => true
        ),
    "note" => array(
            "type" => "input",
            "title" => "Комментарий",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        )
    );
  
    $tables['t_inventory']['name']='Товар';//Товары инвентаризации';
    $tables['t_inventory']['create_group']=false;  
    $tables['t_inventory']['width']=600;  
    $tables['t_inventory']['height']=550;  
    $fields['t_inventory'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
     "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "documentid" => array(
            "type" => "db_select",
            "title" => "Документ",
            "in_grid" => false,
            "in_edit" => false,
            "db_select"=>'d_inventory',
            "required"=> true
        ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Товар",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_items',
            //"field_width" => 455,
            "required"=> true
        ),
    "specificationid" => array(
            "type" => "db_select",
            "title" => "Характеристика",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=> 's_specifications',
            //"field_width" => 455,
            "required"=> false
        ),
    "plannedquantity" => array(
            "type" => "input",
            "title" => "Учетное кол-во",
            "default" => 0.000,
            "precision" => 3,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "readonly" => true,
            "required"=> true
        ),
    "quantity" => array(
            "type" => "input",
            "title" => "Кол-во",
            "default" => 0.000,
            "precision" => 3,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> true
        ),
    "diff" => array(
            "type" => "diff",
            "title" => "Откл.",
            "default" => 0.000,
            "precision" => 3,
            "in_grid" => true,
            "in_edit" => false,
            "width" => 190
        ),
    "measureid" => array(
            "type" => "db_select",
            "title" => "Ед. изм.",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_units_of_measurement',
            //"field_width" => 455,
            "required"=> true
        ),
    "multip" => array(
            "type" => "input",
            "title" => "Коэф.",
            "default" => 1.000,
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "price" => array(
            "type" => "input",
            "title" => "Цена",
            "default" => 0.00,
            "precision" => 2,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "readonly" => true,
            "required"=> false
        ),
    "totalplanned" => array(
            "type" => "rowsum",
            "title" => "Учетная сумма",
            "precision" => 2,
            "in_grid" => true,
            "in_edit" => false
        ),
    "total" => array(
            "type" => "rowsum",
            "title" => "Сумма",
            "precision" => 2,
            "in_grid" => true,
            "in_edit" => false
        )
      );

  $tables['d_movement']['name']='Перемещение';  
  $tables['d_movement']['create_group']=false;  
  $tables['d_movement']['width']=600;  
  $tables['d_movement']['height']=550;  
  $fields['d_movement'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код документа",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Код обмена",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
     "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "employeeid" => array(
            "type" => "label",
            "title" => "Автор документа",
            "db_select"=>'s_employee',
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "organizationid" => array(
            "type" => "db_select",
            "title" => "Организация",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_organizations',
            //"field_width" => 455,
            "required"=> true
        ),
    "warehouseid" => array(
            "type" => "db_select",
            "title" => "Склад-отправитель",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_warehouse',
            //"field_width" => 455,
            "required"=> true
        ),
    "warehousetoid" => array(
            "type" => "db_select",
            "title" => "Склад-получатель",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_warehouse',
            //"field_width" => 455,
            "required"=> true
        ),
    "conducted" => array(
            "type" => "checkbox",
            "title" => "Проведен",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "t_movement" => array(
            "type" => "db_grid",
            "title" => "Товары",
            "in_grid" => false,
            "in_edit" => true,
            "idfield" =>"documentid",
            "required"=> false,
            "db_grid"=>'t_movement'
        ),
    "note" => array(
            "type" => "input",
            "title" => "Комментарий",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        )
      );
  
  $tables['t_movement']['name']='Товар';//'Перемещаемые товары';
  $tables['t_movement']['create_group']=false;  
  $tables['t_movement']['width']=600;  
  $tables['t_movement']['height']=550;  
  $fields['t_movement'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
     "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "documentid" => array(
            "type" => "db_select",
            "title" => "Документ",
            "in_grid" => false,
            "in_edit" => false,
            "db_select"=>'d_movement',
            "required"=> true
        ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Товар",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_items',
            //"field_width" => 455,
            "required"=> true
        ),
    "specificationid" => array(
            "type" => "db_select",
            "title" => "Характеристика",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=> 's_specifications',
            //"field_width" => 455,
            "required"=> false
        ),
    "quantity" => array(
            "type" => "input",
            "title" => "Кол-во",
            "default" => 0.000,
            "precision" => 3,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> true
        ),
    "measureid" => array(
            "type" => "db_select",
            "title" => "Ед. изм.",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_units_of_measurement',
            //"field_width" => 455,
            "required"=> true
        ),
    "multip" => array(
            "type" => "input",
            "title" => "Коэффициент",
            "default" => 1.000,
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        )
      );
  
  $tables['r_remainder']['name']='Регистр накоплений';  
  $tables['r_remainder']['create_group']=false;  
  $tables['r_remainder']['width']=600;  
  $tables['r_remainder']['height']=550;  
  $fields['r_remainder'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "documenttype" => array(
            "type" => "input",
            "title" => "Тип документа",
            "in_grid" => false,
            "in_edit" => false,
            "width" => 190,
            "required"=> true
        ),
    "documentid" => array(
            "type" => "db_select",
            "title" => "Документ",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_employee',
            "required"=> true
        ),
    "organizationid" => array(
            "type" => "db_select",
            "title" => "Организация",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_organizations',
            //"field_width" => 455,
            "required"=> true
        ),
    "warehouseid" => array(
            "type" => "db_select",
            "title" => "Склад",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_warehouse',
            //"field_width" => 455,
            "required"=> true
        ),
    "clientid" => array(
            "type" => "db_select",
            "title" => "Клиент",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_clients',
            //"field_width" => 455,
            "required"=> true
        ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Товар",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_items',
            //"field_width" => 455,
            "required"=> true
        ),
    "quantity" => array(
            "type" => "input",
            "title" => "Кол-во",
            "default" => 0.000,
            "precision" => 3,
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "costprice" => array(
            "type" => "input",
            "title" => "Себестоимость",
            "default" => 0.00,
            "precision" => 2,
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "saleprice" => array(
            "type" => "input",
            "title" => "Розничная цена",
            "default" => 0.00,
            "precision" => 2,
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        )
      );
  
  $tables['s_calculations']['name']='Калькуляции';  
  $tables['s_calculations']['create_group']=false;  
  $tables['s_calculations']['width']=600;  
  $tables['s_calculations']['height']=550;  
  $fields['s_calculations'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Товар",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_items',
            "required"=> true
        ),
    "quantity" => array(
            "type" => "input",
            "title" => "Кол-во",
            "default" => 1.000,
            "precision" => 3,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> true
        ),
    "measureid" => array(
            "type" => "db_select",
            "title" => "Ед. изм.",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_units_of_measurement',
            //"field_width" => 455,
            "required"=> true
        ),
    "composition" => array(
            "type" => "textarea",
            "title" => "Рецептура",
            "in_grid" => false,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Начало действия",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
     "multiselect" => array(
            "type" => "db_multicheckbox",
            "title" => "Торговые объекты",
            "in_grid" => false,
            "in_edit" => true,
            "required" => false,
//            "field_height"=>'100',
//            "field_width"=>'190',
            "selectto_field" => 'id',
            "db_select" => 's_automated_point',
            "db_selectto" => 't_calculations_ap',
            "to_field" => 'calculationid',
            "select_field" => 'automatedpointid',
            "alt" => "Торговые объекты, на которых будет применяться калькуляция"
        ),
    "t_calculations" => array(
            "type" => "db_grid",
            "title" => "Ингредиенты",
            "in_grid" => false,
            "in_edit" => true,
            "idfield" =>"calculationid",
            "required"=> false,
            "db_grid"=>'t_calculations'
        )
      );
  
  $tables['t_calculations']['name']='Состав';  
  $tables['t_calculations']['create_group']=false;  
  $tables['t_calculations']['width']=600;  
  $tables['t_calculations']['height']=550;  
  $fields['t_calculations'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => false,
            "in_edit" => false,
            "width" => 190,
            "required"=> false
        ),
    "calculationid" => array(
            "type" => "db_select",
            "title" => "Калькуляция",
            "in_grid" => false,
            "in_edit" => false,
            "db_select"=>'s_calculations',
            "required"=> true
        ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Ингредиент",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_items',
            "required"=> true
        ),
    "quantity" => array(
            "type" => "input",
            "title" => "Кол-во",
            "default" => 0.000,
            "precision" => 3,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> true
        ),
    "measureid" => array(
            "type" => "db_select",
            "title" => "Ед. изм.",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_units_of_measurement',
            //"field_width" => 455,
            "required"=> true
        ),
    "multip" => array(
            "type" => "input",
            "title" => "Коэффициент",
            "default" => 1.000,
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "loss_cold" => array(
            "type" => "input",
            "title" => "Потери хол., %",
            "default" => 0.00,
            "precision" => 2,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "loss_cold_quantity" => array(
            "type" => "rowsum",
            "title" => "Кол-во",
            "db_select" => 't_calculations',
            "idfield" => "calculationid",
            "sumfield" => "quantity - quantity * loss_cold / 100",
            "precision" => 3,
            "in_grid" => true,
            "in_edit" => true
        ),
    "loss_hot" => array(
            "type" => "input",
            "title" => "Потери гор., %",
            "default" => 0.00,
            "precision" => 2,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        ),
    "loss_hot_quantity" => array(
            "type" => "rowsum",
            "title" => "Кол-во",
            "db_select" => 't_calculations',
            "idfield" => "calculationid",
            "sumfield" => "quantity - quantity * loss_hot / 100",
            "precision" => 3,
            "in_grid" => true,
            "in_edit" => true
        )
      );
  
  $tables['d_production']['name']='Выпуск продукции';  
  $tables['d_production']['create_group']=false;  
  $tables['d_production']['width']=600;  
  $tables['d_production']['height']=550;  
  $fields['d_production'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код документа",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Код обмена",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "isgroup" => array(
            "type" => "checkbox",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => false,
            "in_edit" => false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "employeeid" => array(
            "type" => "label",
            "title" => "Автор документа",
            "in_grid" => false,
            "in_edit" => true,
            "db_select"=>'s_employee'
        ),
    "organizationid" => array(
            "type" => "db_select",
            "title" => "Организация",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_organizations',
            //"field_width" => 455,
            "required"=> true
        ),
    "warehouseid" => array(
            "type" => "db_select",
            "title" => "Склад",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_warehouse',
            //"field_width" => 455,
            "required"=> true
        ),
    "conducted" => array(
            "type" => "checkbox",
            "title" => "Проведен",
            "in_grid" => true,
            "in_edit" => false
        ),
    "t_production" => array(
            "type" => "db_grid",
            "title" => "Товары",
            "in_grid" => false,
            "in_edit" => true,
            "idfield" =>"documentid",
            "required"=> false,
            "db_grid"=>'t_production'
        ),
    "note" => array(
            "type" => "input",
            "title" => "Комментарий",
            "in_grid" => false,
            "in_edit" => true
        )
      );
  
  $tables['t_production']['name']='Продукция';  
  $tables['t_production']['create_group']=false;  
  $tables['t_production']['width']=600;  
  $tables['t_production']['height']=550;  
  $fields['t_production'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => false,
            "in_edit" => false
        ),
    "documentid" => array(
            "type" => "db_select",
            "title" => "Документ",
            "in_grid" => false,
            "in_edit" => false,
            "db_select"=>'d_production',
            "required"=> true
        ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Продукт",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_items',
            "required"=> true
        ),
    "specificationid" => array(
            "type" => "db_select",
            "title" => "Характеристика",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=> 's_specifications',
            //"field_width" => 455,
            "required"=> false
        ),
    "quantity" => array(
            "type" => "input",
            "title" => "Кол-во",
            "default" => 0.000,
            "precision" => 3,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "measureid" => array(
            "type" => "db_select",
            "title" => "Ед. изм.",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_units_of_measurement',
            //"field_width" => 455,
            "required"=> true
        ),
    "multip" => array(
            "type" => "input",
            "title" => "Коэффициент",
            "default" => 1.000,
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        )
    );
  
  $tables['s_specifications']['name']='Характеристики';  
  $tables['s_specifications']['create_group']=false;  
  $tables['s_specifications']['width']=600;  
  $tables['s_specifications']['height']=550;  
  $fields['s_specifications'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "kindofspecificationid" => array(
            "type" => "db_select",
            "title" => "Тип характеристики",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_kindsofspecifications',
            "required"=> true
        )
    );
  
  $tables['s_kindsofspecifications']['name']='Типы характеристик';  
  $tables['s_kindsofspecifications']['create_group']=false;  
  $tables['s_kindsofspecifications']['width']=600;  
  $tables['s_kindsofspecifications']['height']=550;  
  $fields['s_kindsofspecifications'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        )
    );
  
  $tables['s_optionsofspecifications']['name']='Параметры характеристик';  
  $tables['s_optionsofspecifications']['create_group']=false;  
  $tables['s_optionsofspecifications']['width']=600;  
  $tables['s_optionsofspecifications']['height']=550;  
  $fields['s_optionsofspecifications'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        )
    );
  
  $tables['s_specificationoptions']['name']='Наборы параметров характеристик';  
  $tables['s_specificationoptions']['create_group']=false;  
  $tables['s_specificationoptions']['width']=600;  
  $tables['s_specificationoptions']['height']=550;  
  $fields['s_specificationoptions'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "kindid" => array(
            "type" => "db_select",
            "title" => "Тип характеристики",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_kindsofspecifications',
            "required"=> true
        ),
    "optionid" => array(
            "type" => "db_select",
            "title" => "Параметр",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_optionsofspecifications',
            "required"=> true
        )
    );
  
  $tables['t_specificationoptions']['name']='Значения параметров характеристик';  
  $tables['t_specificationoptions']['create_group']=false;  
  $tables['t_specificationoptions']['width']=600;  
  $tables['t_specificationoptions']['height']=550;  
  $fields['t_specificationoptions'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "specificationid" => array(
            "type" => "db_select",
            "title" => "Характеристика",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_specifications',
            "required"=> true
        ),
    "optionid" => array(
            "type" => "db_select",
            "title" => "Параметр",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_optionsofspecifications',
            "required"=> true
        ),
    "value" => array(
            "type" => "input",
            "title" => "Значение",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        )
    );

  $tables['d_request']['name']='Заявка на склад';  
  $tables['d_request']['create_group']=false;  
  $tables['d_request']['width']=600;  
  $tables['d_request']['height']=550;  
  $fields['d_request'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код документа",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Код обмена",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "employeeid" => array(
            "type" => "label",
            "title" => "Автор документа",
            "db_select"=>'s_employee',
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "organizationid" => array(
            "type" => "db_select",
            "title" => "Организация",
            "in_grid" => false,
            "in_edit" => false,
            "db_select"=> 's_organizations',
            //"field_width" => 455,
            "required"=> false
        ),
    "warehouseid" => array(
            "type" => "db_select",
            "title" => "Склад",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_warehouse',
            //"field_width" => 455,
            "required"=> true
        ),
    "clientid" => array(
            "type" => "db_select",
            "title" => "Заказчик",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_clients',
            //"field_width" => 455,
            "required"=> true
        ),
    "t_request" => array(
            "type" => "db_grid",
            "title" => "Товары",
            "in_grid" => false,
            "in_edit" => true,
            "idfield" => "documentid",
            "required"=> false,
            "db_grid"=> 't_request'
        ),
    "note" => array(
            "type" => "input",
            "title" => "Комментарий",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        )
      );
  
  $tables['t_request']['name']='Товар';//'Поступившие товары';
  $tables['t_request']['create_group']=false;  
  $tables['t_request']['width']=600;  
  $tables['t_request']['height']=550;  
  $fields['t_request'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "documentid" => array(
            "type" => "db_select",
            "db_select"=> 'd_request',
            "title" => "Документ",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> true
        ),
    "itemid" => array(
            "type" => "db_select",
            "title" => "Товар",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_items',
            //"field_width" => 455,
            "required"=> true
        ),
    "specificationid" => array(
            "type" => "db_select",
            "title" => "Характеристика",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=> 's_specifications',
            //"field_width" => 455,
            "required"=> false
        ),
    "quantity" => array(
            "type" => "input",
            "title" => "Кол-во",
            "default" => 0.000,
            "precision" => 3,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> true
        ),
    "measureid" => array(
            "type" => "db_select",
            "title" => "Ед. изм.",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_units_of_measurement',
            //"field_width" => 455,
            "required"=> true
        ),
    "multip" => array(
            "type" => "input",
            "title" => "Коэф.",
            "default" => 1.000,
            "readonly" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        )
    );
  
  $tables['d_regrading']['name']='Пересортица';  
  $tables['d_regrading']['create_group']=false;  
  $tables['d_regrading']['width']=600;  
  $tables['d_regrading']['height']=550;  
  $fields['d_regrading'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код документа",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Код обмена",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
     "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "employeeid" => array(
            "type" => "label",
            "title" => "Автор документа",
            "db_select"=>'s_employee',
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "organizationid" => array(
            "type" => "db_select",
            "title" => "Организация",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_organizations',
            //"field_width" => 455,
            "required"=> true
        ),
    "warehouseid" => array(
            "type" => "db_select",
            "title" => "Склад",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_warehouse',
            //"field_width" => 455,
            "required"=> true
        ),
    "conducted" => array(
            "type" => "checkbox",
            "title" => "Проведен",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "t_regrading" => array(
            "type" => "db_grid",
            "title" => "Товары",
            "in_grid" => false,
            "in_edit" => true,
            "idfield" =>"documentid",
            "required"=> false,
            "db_grid"=>'t_regrading'
        ),
    "note" => array(
            "type" => "input",
            "title" => "Комментарий",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        )
      );
  
  $tables['t_regrading']['name']='Товары';//'Оприходованные товары';
  $tables['t_regrading']['create_group']=false;  
  $tables['t_regrading']['width']=600;  
  $tables['t_regrading']['height']=550;  
  $fields['t_regrading'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => false,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
     "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "documentid" => array(
            "type" => "db_select",
            "title" => "Документ",
            "in_grid" => false,
            "in_edit" => false,
            "db_select"=>'d_posting',
            "required"=> true
        ),
    "destitemid" => array(
            "type" => "db_select",
            "title" => "Приходуемый товар",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_items',
            //"field_width" => 455,
            "required"=> true
        ),
    "destspecificationid" => array(
            "type" => "db_select",
            "title" => "Характеристика",
            "in_grid" => false,
            "in_edit" => false,
            "db_select"=> 's_specifications',
            //"field_width" => 455,
            "required"=> false
        ),
    "destmeasureid" => array(
            "type" => "db_select",
            "title" => "Ед. изм.",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=> 's_units_of_measurement',
            //"field_width" => 455,
            "required"=> true
        ),
    "srcitemid" => array(
            "type" => "db_select",
            "title" => "Списываемый товар",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=> 's_items',
            //"field_width" => 455,
            "required"=> true
        ),
    "srcspecificationid" => array(
            "type" => "db_select",
            "title" => "Характеристика",
            "in_grid" => false,
            "in_edit" => false,
            "db_select"=> 's_specifications',
            //"field_width" => 455,
            "required"=> false
        ),
    "srcmeasureid" => array(
            "type" => "db_select",
            "title" => "Ед. изм.",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=> 's_units_of_measurement',
            //"field_width" => 455,
            "required"=> true
        ),
    "quantity" => array(
            "type" => "input",
            "title" => "Кол-во",
            "default" => 0.000,
            "precision" => 3,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> true
        ),
    "multip" => array(
            "type" => "input",
            "title" => "Коэф.",
            "default" => 1.000,
            "readonly" => true,
            "in_grid" => false,
            "in_edit" => false,
            "width" => 190,
            "required"=> false
        ),
    "price" => array(
            "type" => "input",
            "title" => "Цена оприходования",
            "default" => 0.000,
            "precision" => 2,
            "numeric" => true,
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        )
      );
  
//КАССЫ
  
  $tables['s_cash']['name']='Кассы';  
  $tables['s_cash']['create_group']=false;  
  $tables['s_cash']['width']=600;  
  $tables['s_cash']['height']=550;  
  $fields['s_cash'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Внешний ID",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
     "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
     "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        )
      );
  
  $tables['d_cash_income']['name']='Поступление в кассу';  
  $tables['d_cash_income']['create_group']=false;  
  $tables['d_cash_income']['width']=600;  
  $tables['d_cash_income']['height']=550;  
  $fields['d_cash_income'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код документа",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Код обмена",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => false,
            "in_edit" => false
        ),
    "conducted" => array(
            "type" => "checkbox",
            "title" => "Проведен",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "employeeid" => array(
            "type" => "label",
            "title" => "Автор документа",
            "db_select"=>'s_employee',
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "organizationid" => array(
            "type" => "db_select",
            "title" => "Организация",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_organizations',
            "required"=> true
        ),
    "cashid" => array(
            "type" => "db_select",
            "title" => "Касса",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_cash',
            "required"=> true
        ),
    "paymentid" => array(
            "type" => "db_select",
            "title" => "Вид оплаты",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_types_of_payment',
            "required"=> true
        ),
    "amount" => array(
            "type" => "input",
            "title" => "Сумма",
            "in_grid" => true,
            "in_edit" => true,
            "precision" => 2,
            "numeric" => true,
            "required"=> true
        ),
    "note" => array(
            "type" => "input",
            "title" => "Комментарий",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        )
      );
  
  $tables['d_cash_outcome']['name']='Изъятие из кассы';
  $tables['d_cash_outcome']['create_group']=false;  
  $tables['d_cash_outcome']['width']=600;  
  $tables['d_cash_outcome']['height']=550;  
  $fields['d_cash_outcome'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код документа",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Код обмена",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => false,
            "in_edit" => false
        ),
    "conducted" => array(
            "type" => "checkbox",
            "title" => "Проведен",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "employeeid" => array(
            "type" => "label",
            "title" => "Автор документа",
            "db_select"=>'s_employee',
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "organizationid" => array(
            "type" => "db_select",
            "title" => "Организация",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_organizations',
            "required"=> true
        ),
    "cashid" => array(
            "type" => "db_select",
            "title" => "Касса",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_cash',
            "required"=> true
        ),
    "paymentid" => array(
            "type" => "db_select",
            "title" => "Вид оплаты",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_types_of_payment',
            "required"=> true
        ),
    "amount" => array(
            "type" => "input",
            "title" => "Сумма",
            "in_grid" => true,
            "in_edit" => true,
            "precision" => 2,
            "numeric" => true,
            "required"=> true
        ),
    "note" => array(
            "type" => "input",
            "title" => "Комментарий",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        )
      );
  
  $tables['d_cash_movement']['name']='Перемещение между кассами';
  $tables['d_cash_movement']['create_group']=false;  
  $tables['d_cash_movement']['width']=600;  
  $tables['d_cash_movement']['height']=550;  
  $fields['d_cash_movement'] = array (
    "id" => array(
            "type" => "input",
            "title" => "ID",
            "in_grid" => false,
            "in_edit" => false,
            "required" => true,
            "default" => 1,
            "field_width" => 100,
            "width" => 100,
            "alt" => "Айди товара",
            "after_text" => ""
        ),
     "idout" => array(
            "type" => "input",
            "title" => "Код документа",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "idlink" => array(
            "type" => "input",
            "title" => "Код обмена",
            "in_grid" => false,
            "in_edit" => true,
            "required"=> false
        ),
    "isgroup" => array(
            "type" => "input",
            "title" => "Папка ли",
            "in_grid" => false,
            "in_edit" => false
        ),
    "parentid" => array(
            "type" => "input",
            "title" => "ID родителя",
            "in_grid" => false,
            "in_edit" => false
        ),
    "name" => array(
            "type" => "input",
            "title" => "Наименование",
            "in_grid" => false,
            "in_edit" => false
        ),
    "conducted" => array(
            "type" => "checkbox",
            "title" => "Проведен",
            "in_grid" => true,
            "in_edit" => false,
            "required"=> false
        ),
    "dt" => array(
            "type" => "datetime",
            "title" => "Дата",
            "in_grid" => true,
            "in_edit" => true,
            "required"=> false
        ),
    "employeeid" => array(
            "type" => "label",
            "title" => "Автор документа",
            "db_select"=>'s_employee',
            "in_grid" => true,
            "in_edit" => true,
            "required"=> true
        ),
    "organizationid" => array(
            "type" => "db_select",
            "title" => "Организация",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_organizations',
            "required"=> true
        ),
    "cashid" => array(
            "type" => "db_select",
            "title" => "Касса-отправитель",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_cash',
            "required"=> true
        ),
    "cashtoid" => array(
            "type" => "db_select",
            "title" => "Касса-получатель",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_cash',
            "required"=> true
        ),
    "paymentid" => array(
            "type" => "db_select",
            "title" => "Вид оплаты",
            "in_grid" => true,
            "in_edit" => true,
            "db_select"=>'s_types_of_payment',
            "required"=> true
        ),
    "amount" => array(
            "type" => "input",
            "title" => "Сумма",
            "in_grid" => true,
            "in_edit" => true,
            "precision" => 2,
            "numeric" => true,
            "required"=> true
        ),
    "note" => array(
            "type" => "input",
            "title" => "Комментарий",
            "in_grid" => true,
            "in_edit" => true,
            "width" => 190,
            "required"=> false
        )
      );
?>