<?php
  $tables['shop_items']['name']='Товары';  
  $tables['shop_items']['create_group']=false;  
  $tables['shop_items']['width']=600;  
  $tables['shop_items']['height']=550;  
  $fields['shop_items'] = array (
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
?>
