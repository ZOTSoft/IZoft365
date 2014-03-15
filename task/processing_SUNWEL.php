<?php
session_start();

include '../company/check.php';
//checksessionpassword();

header('Content-type: text/html, charset=utf-8;');

$debug = TRUE;

if($debug) {
	require_once('class/system/FirePHPCore/FirePHP.class.php');
	$firephp = FirePHP::getInstance(TRUE);
	require_once('class/system/FirePHPCore/fb.php');
}

//include_once('config.php');
//include_once('../../company/mysql.php');
include_once('common.lib.php');

function __autoload($name)
{
    include "class/". $name .".Class.php";
}

// определение глобальных переменных и классов
$answer		= '';
$typeAnswer = 'json';
$MySQL		= new MySQL($debug);
$NewContent = new formationContent();
$Errors		= new Errors();

include('../company/config.php'); 
$MySQL->DB_conect(HOST, $_SESSION['base_user'], $_SESSION['base_password'], $_SESSION['timezone'], $_SESSION['base']);

if( isset( $_GET['sl'] ) ) {
	$connection = $_GET['sl'];
} else {
	switch ($_SERVER['REQUEST_METHOD']) {
		case 'POST'	: 
						if(isset($_POST['sl'])) {
							$connection = $_POST['sl'];
							if(isset($_POST['reception'])) {
								$RC = universalProsessingData( $_POST['reception'] );
								if($debug) {$firephp->info($RC);}
							}
						} else {
							if($debug) {$firephp->warn('POST _ ',$_POST,'POST');}
							$connection = 'no select';
						}
		break;
		case 'GET'	: 
						if(isset($_GET['sl'])) {
							$connection = $_GET['sl'];
							if(isset($_GET['reception'])) {
								$RC = universalProsessingData( $_POST['reception'] );
								if($debug) {$firephp->info($RC);}
							}
						} else {
							if($debug) {$firephp->warn('GET _ ',$_GET);}
							$connection = 'no select';
						}
		break;
		case 'HEAD'	:	 if($debug) {$firephp->warn('HEAD _ ');}
		break;
	}
}

switch ($connection){
			/////////////////////////////////////////////////////////
			///////     ПРОДЛЕНИЕ СЕССИИ И ПРОВЕРКА ВЕРСИИ     //////
			/////////////////////////////////////////////////////////
	case 'check_ver'  : 
			
			$answer = array (
								'ver'=> 'sl_1.002', 
								'hours'=>dateVerification('H'), 
								'minute'=>dateVerification('i'), 
								'day' => dateVerification('dName'), 
								'numDay' => dateVerification('numDay'), 
								'mount' => dateVerification('mName'),
								'year' => dateVerification('Y')
							);
	break;

			//////////////////////////////////////////////
			////	ОБНОВЛЕНИЕ СТАТУСОВ | ЗАДАЧИ, ЛК  ////
			////////////////////////////////////////////// 
	case 'PanelZ' :
			
			$answer = array();
		
			$answer['count_nw'] = $MySQL->DB_select('COUNT(status)','crm_zadacha z','','iniciator = '.definitionSESSION().' AND status = 2','','','str');
			if($answer['count_nw'] > 0){
				$answer['count_nw'] = (int)$answer['count_nw'];
			} else {
				$answer['count_nw'] = 0;
			}
			
			$answer['count_w'] = $MySQL->DB_select('COUNT(status)','crm_zadacha z','','(ispolnitel = '.definitionSESSION().' AND status = 1) OR (ispolnitel = '.definitionSESSION().' AND status = 3)','','','str');
			if($answer['count_w'] > 0){
				$answer['count_w'] = (int)$answer['count_w'];
			} else {
				$answer['count_w'] = 0;
			}
	break;
	
			/////////////////////////////////////////////////////////
			///////     ПРОДЛЕНИЕ СЕССИИ И ПРОВЕРКА ВЕРСИИ     //////
			/////////////////////////////////////////////////////////
	case 'uploadcsvspr':
           /* if ($_FILES["csv_file"]["error"] >0 )
            {
                echo "Ошибка при загрузке!" ;
            }
            else
            {
                $file_array = explode("\n",file_get_contents($_FILES["csv_file"]["tmp_name"]));
                if (substr($file_array[0],0,3)==chr(239).chr(187).chr(191))
                $file_array[0]=substr($file_array[0],3);
                $i=0;
                $u=0;
                foreach ($file_array as $str) {
                    $sub_array=explode(";",$str);
                    if ($sub_array[4]==''){$sub_array[4]=0;}
                    $result = mysql_query("select id from s_items 
                    WHERE idlink = '".addslashes($sub_array[0])."' and idlink!='' ");

                    if(mysql_num_rows($result)){
                        
                        $name='';
                        if (isset($_POST['change_name'])){
                            $name="name='".addslashes($sub_array[3])."',";
                        }   
                        if (isset($_POST['i_useInMenu'])){
                            $name.="i_useInMenu='1',";
                        }   
                        
                        $result=mysql_query("UPDATE s_items SET                     
                    isgroup=".addslashes($sub_array[1]).",
                    parentid=ifnull((select * from 
                    (select t.id from s_items as t where t.idlink='".addslashes($sub_array[2])."' and t.idlink!='' limit 1) as t2),0),
                    ".$name."
                    price=".addslashes($sub_array[4])."
                    WHERE idlink = '".addslashes($sub_array[0])."' ");
                    
                    if(mysql_affected_rows())
                    {
                        $u=$u+mysql_affected_rows();
                        echo "Обновлена запись: Код=".$sub_array[0]." Наименование=".$sub_array[3]." Цена=".$sub_array[4]."\n";
                    }
                    }
                    else
                    {
                        $result=mysql_query("insert into s_items SET 
                    idout=ifnull((select max(cast(idout as SIGNED))+1 from s_items as t3 limit 1),1),    
                    idlink= '".addslashes($sub_array[0])."',
                    isgroup=".addslashes($sub_array[1]).",
                    parentid=ifnull((select * from 
                    (select t.id from s_items as t where t.idlink='".addslashes($sub_array[2])."' and t.idlink!='' limit 1) as t2),0),
                    name='".addslashes($sub_array[3])."',
                    price=".addslashes($sub_array[4]));                
                        if ($result){$i++;}                   
                    }
                    
                    
                }
                echo "Загрузка завершена!\nДобавлено: ".$i."товаров\nОбновлено: ".$u." товаров";
                unlink($_FILES["csv_file"]["tmp_name"]);
            }     */  
	break;      


			/////////////////////////////////////////////////////////
			/////               ПОЛИГОН ИСПЫТАНИЙ              //////
			/////////////////////////////////////////////////////////
	case 'karinaTranzBase' :
		
			# шифрование Пароля
			/*$select = $MySQL->DB_select('id, password','s_employee','','idlink IS NULL');
			foreach ($select AS $row) {
				$MySQL->DB_update('s_employee'," password = '".md5(FISH.md5($row['password']))."' , ident = '".md5(FISH.md5($row['password']))."' ", 'id = '.$row['id']);
			}
			*/
		
			# копирование имени из name в fio 
			/*$select = $MySQL->DB_select('id, name','s_employee');
			foreach ($select AS $row) {
				$MySQL->DB_update('s_employee'," fio = '".$row['name']."' , isgroup = 0 " , "id = ".$row['id']);
			}
			*/
			
			# изменение категории сотрудника
			/*$idCategory = 2069;
			$oldParent = 2;
			$select = $MySQL->DB_select('parentid','s_employee','',' parentid = '.$oldParent);
			foreach ($select AS $row) {
				$MySQL->DB_update('s_employee'," parentid = '".$idCategory."' " , "parentid = ".$oldParent);
			}
			*/
			
			# добавление времени к транзакциям
			/*$time = 3;
			$select = $MySQL->DB_select('id, dt','d_urv_transactions_copy','','idlink IS NULL','','10000');
			//fb($select);
			foreach ($select AS $row) {
				$test = explode(' ', $row['dt']);
				$d = explode('-', $test[0]);
				$t = explode(':', $test[1]);

				$hour = $t[0] + $time;

				$result = $d[0] ."-". $d[1] ."-". $d[2] ." ". $hour .":". $t[1] .":". $t[2];
					
				$MySQL->DB_update('d_urv_transactions_copy'," dt = '".$result."' , idlink = 0 " , "id = ".$row['id']);
			}
			*/
		
			//$select = $MySQL->DB_select('DISTINCT ec.id','s_employee e','LEFT JOIN s_employee_copy ec ON e.fio = ec.fio','e.fio=ec.fio');
			//foreach ($select AS $row) {
			//	$MySQL->DB_update('s_employee_copy',"idlink = '1111'",'id = '. $row['id']);
			//}
			//$MySQL->DB_delete('s_employee_copy',"idlink != '1111'");
			//$select = $MySQL->DB_select('DISTINCT id','s_employee_copy ','',"idlink != '1111'");
			//foreach ($select AS $row) {
			//	$MySQL->DB_delete('s_employee_copy','id = '. $row['id']);
			//}
	
			/*
			$select = $MySQL->DB_select('idout, id_depart','clients');
			foreach ($select AS $row) {
				$MySQL->DB_update('s_employee',"id_depart = ". $row['id_depart'] ." ","idout = '". $row['idout']."' ");
			}*/
			
			# переброс сотрудников которых нет в основной базе
			/*$select = $MySQL->DB_select('s_employee_test.*','s_employee_test','','s_employee_test.id NOT IN (	SELECT s_employee.id	FROM s_employee)');
		
			foreach ($select AS $row) {
				$MySQL->DB_insert(	's_employee',
									'id,idout,name,fio,password,id_location,position,id_depart',
									"(".$row['id'].",'".$row['idout']."','".$row['fio']."','".$row['fio']."','".$row['password']."','".$row['Id_location']."','".$row['ident']."','".$row['e_servicepercent']."')");
			}*/																							
			
			$answer = 'усё Жаксыбись ваще!';
			$typeAnswer = 'html';
	break;


			/////////////////////////////////////////////////////////
			/////               НЕТ УСЛОВИЯ УРВ                //////
			/////////////////////////////////////////////////////////	
	case 'no select' :
			$firephp->warn('no select');
	break;	


			/////////////////////////////////////////////////////////
			///////          ПОДКЛЮЧЕНИЕ ОБРАБОТЧИКОВ          //////
			/////////////////////////////////////////////////////////
	default : 
			
			# если ссылка указана, подключаю файл
			if(!empty( $RC['url'] )) {
				include_once $RC['url'] .'/processing_'. $RC['url']. '.php';
				
			# если не указана, подключаю все файлы обработчиков
			} else {
				$process = array('crm', 'delivery', 'urv');
				foreach ($process AS $row) {
					
					include_once $row.'/processing_'.$row.'.php';
					
					if(!empty( $answer )) {
						break;
					}
				}
			} 
			
			# есл переменная пуста, возвращаю пустоту
			if(empty( $answer )) {
				$answer = '';
			}
}

# ТИП ВОЗВРАЩАЕМОГО КОНТЕНТА
switch ($typeAnswer)
{
	case 'html':	echo $answer;				break;
	case 'json':	echo json_encode($answer);	break;
}