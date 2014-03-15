<?php


//определение к какой группе относятся пользователи
function definitionUserGroup()
{
	$id_user = definitionSESSION();
	
	$sql = "SELECT id_mgr
				FROM crm_mgr_view
				WHERE id_mgr = ". $id_user ."
				LIMIT 1";
	$result = mysql_query($sql) or die(mysql_error());
	if (mysql_num_rows($result) > 0) {
		$mgrNumber = 1;
	} else {
		$mgrNumber = 5;
	}
		
	$sql = "SELECT rollid
				FROM t_employee_interface
				WHERE employeeid = ". $id_user ." 
				LIMIT 5";
	$result = mysql_query($sql) or die(mysql_error());
	$minNumber = 5;
	while($row = mysql_fetch_assoc($result)){
		if($row['rollid'] < $minNumber) { 
			$minNumber = $row['rollid'];
		}
	}
	
	if($minNumber < $mgrNumber) {
		$numberGroup = $minNumber;
	} else {
		$numberGroup = $mgrNumber;
	}
	
	return $numberGroup;
}

//
function definitionCountZ_W_NW($W = 0, $NW = 0)
{
	$count_w = clearData($W,'i');
	$count_nw = clearData($NW ,'i');
	return $CountZ = array('count_w'=>$count_w,'count_nw'=>$count_nw);
}

//обозначение статуса строковым значением
function statusIntToString($status)
{
	/*switch($status)
	{
		case 1 : $name = 'в работе';break;
		case 2 : $name = 'выполнено';break;
		case 3 : $name = 'в доработке';break;
		case 4 : $name = 'завершен';break;
		case 5 : $name = 'отменено';break;
	}
	return $name;*/
	
	$sql = "SELECT name_status
				FROM crm_z_status
				WHERE id_status = $status
				LIMIT 1";
	$result = mysql_query($sql) or die(mysql_error());
	return db2String($result);
}

//обозначение Приоритета строковым значением
function prioritetIntToString($prioritet)
{
	$sql = "SELECT name_prioritet
				FROM crm_z_prioritet
				WHERE id_prioritet = $prioritet
				LIMIT 1";
	$result = mysql_query($sql) or die(mysql_error());
	return db2String($result);
}

//изменений цвета по приоритету
function colorPrioritet($prioritet)
{
	switch($prioritet){
		case 1 : $class_status = 'class_status_low' ;break;
		case 2 : $class_status = 'class_status_normal' ;break;
		case 3 : $class_status = 'class_status_hign' ;break;
		case 4 : $class_status = 'class_status_fire' ;break;
		default : $class_status = '';
	}
	return $class_status;
}

//функция форматирования даты формата ЧЧ:мм дд.мм.ГГ в юникс метку
function processingDateToUnix($date)
{
	if(isset($date)){
		$dt_el  = explode(" ",$date);
		$d_el = explode(".",clearData($dt_el[0]));
		
		$mount = (int)clearData($d_el[1],'i');
		$day = (int)clearData($d_el[0],'i');
		$year = (int)clearData($d_el[2],'i');
		
		if(isset($dt_el[1]))
		{
			$t_el = explode(":",clearData($dt_el[1]));
			$hour = (int)clearData($t_el[0],'i');
			$minute = (int)clearData($t_el[1],'i');
			
			if(isset($t_el[2]))
				$sek = (int)clearData($t_el[2],'i');
			else
				$sek = 0;
		}
		else
		{
			$t_el = 0;
			$hour = 0;
			$minute = 0;
			$sek = 0;
		}		
			
		$date = mktime($hour,$minute,$sek,$mount,$day,$year);
	}else{
		$date = date("U");
	}
	return($date);
}

//функция форматирования даты TIMESTAMP в юникс метку
function processingTimestampToUnix($date)
{
	$dt_el  = explode(" ",$date);
	$d_el = explode("-",$dt_el[0]);
	$t_el = explode(":",$dt_el[1]);
	$unix_date = mktime((int)$t_el[0],(int)$t_el[1],(int)$t_el[2],(int)$d_el[1],(int)$d_el[2],(int)$dt_el[0]);
	return($unix_date);
}

//функция форматирования метки юникс в дату TIMESTAMP
function processingUnixToTimestamp($date)
{
	$timestamp = date("Y-m-d H:i:s",$date);
	return($timestamp);
}

//выбираю пользователя по ИД
function selectUser($id)
{
	$sql = "SELECT F,I
				FROM s_employee
				WHERE id = $id
				LIMIT 1";	
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}

//разделение ФИО в массив строк
function splitFIOtoMassiv($FIO)
{
	$name = '';
	$massiv = explode(" ", $FIO);
	if(isset($massiv[0]))
	{
		 $name = $massiv[0];
		 if(isset($massiv[1]))
		 {
			  $name .= ' '. $massiv[1];
		 }
	}
	return $name; 	
}
			
////////////////////////////////////////////
//////////  СТРАНИЦА ОТЧЁТОВ  //////////////
////////////////////////////////////////////

//выбор списка сотрудников для фильтра
function selectUserReport($filtr = '')
{
	if(definitionUserGroup() == 1)
	{
		$filtr .= "WHERE ds.id_user IN (SELECT id_user
								FROM crm_mgr_view
								WHERE id_mgr = '". definitionSESSION() ."')";
    }
	
	$sql = "SELECT DISTINCT ds.id_user, u.fio
				FROM s_employee u
					INNER JOIN crm_data_sheed ds ON u.id = ds.id_user
				".$filtr."
				ORDER BY u.fio";
	//echo $sql;
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);				
}

//выборка данных для редактирования отчёта
function updateReport($id)
{
	$sql = "SELECT report
				FROM crm_data_sheed
				WHERE id_ds = $id
				LIMIT 1";
	$result = mysql_query($sql) or die(mysql_error());
	return db2String($result);
}

//добавление обнавлённых данных отчёта в базу
function insertReport($text, $id)
{
	$sql = "UPDATE crm_data_sheed
				SET report = '$text'
				WHERE id_ds = $id ";
	mysql_query($sql) or die(mysql_error());
}

//выборка файлов для отчёта
function selectFilesDS($id_ds)
{
	$sql = "SELECT id_f, name, f_type
				FROM crm_files
				WHERE id_ds = $id_ds";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}

//Формирование запроса для фильтра для Отчётов
function filtrAllReport($vs,$s_s_date,$s_f_date)
{
	$filtr='';
	
	//если пользователь то выбираем отчёты по ид данного пользоватея
	if(definitionUserGroup() != 1 &&  definitionUserGroup() != 0)
	{ 
		$filtr .= " ds.id_user='".definitionSESSION()."' ";
	}
	//если администратор то выбираем отчёты по выбранному ид пользователя
	if(definitionUserGroup() == 0)
	{ 
		if(!empty($vs))
		{
			$filtr .= " ds.id_user='".$vs."' ";
		}
	}
	//если управляющий отдела 
	if(definitionUserGroup() == 1)
	{ 
		$filtr .= " ds.id_user IN (SELECT id_user
								FROM crm_mgr_view
								WHERE id_mgr = ". definitionSESSION() .")";
		if(!empty($vs)){
			$filtr .= " AND ds.id_user='".$vs."' ";
		}
	}	
	
	//фильтр дата старта
	if((!empty($s_s_date)) or (!empty($s_f_date)))
	{
		$filtr = issetFiltrAND($filtr);
		//если поле не пусто то фильтруем значение, если пусто присваиваем текущуую дату
		if(!empty($s_s_date))
		{
			$s_Unix = processingDateToUnix($s_s_date);
			$ds_s_filtr = processingUnixToTimestamp($s_Unix);
		}
		else
		{
			//если значение "от" пусто а "до" содержит значение, то выводим в переменную "от" текущее время 
			$ds_s_filtr = processingUnixToTimestamp(date("U"));
		}
		//если поле не пусто то фильтруем значение, если пусто присваиваем текущуую дату
		if(!empty($s_f_date))
		{
			$f_Unix = processingDateToUnix($s_f_date);
			$ds_f_filtr = processingUnixToTimestamp($f_Unix);
		}
		else
		{
			//если значение "до" пусто а "от" содержит значение, то выводим в переменную "до" текущее время 
			
			$test = processingDateToUnix(Date_unix('',4)) + 86399;
			$ds_f_filtr = processingUnixToTimestamp($test);
		}
		
		//выборка даты в базе от и до
		$filtr .= " ds.add_date BETWEEN '".$ds_s_filtr."' AND '".$ds_f_filtr."' ";
		//echo $filtr;
	//если дата не введена, выводим дату за текущий день
	}
	else
	{
		$filtr = issetFiltrAND($filtr);
		//дата за текущий день
		$today = date("d.m.Y");
		$date_elements  = explode(".",$today);
		//форматирование в юникс дату
		$today = mktime(0,0,0,$date_elements[1],$date_elements[0],$date_elements[2]);
		//время "до"
		$tomorrow = $today + 86399;
		
		$today = processingUnixToTimestamp($today);
		$tomorrow = processingUnixToTimestamp($tomorrow);
		
		$filtr = $filtr. " ds.add_date BETWEEN '".$today."' AND '".$tomorrow."' ";
	}
	
	/*if ($filtr!=''){
		$filtr=' WHERE '.$filtr;
		//echo $filtr;
	}*/
	
	return($filtr);
}
					
////////////////////////////////////////////
//////////  СТРАНИЦА СОЦ СЕТИ //////////////
////////////////////////////////////////////

//выборка вывода основных статей на экран
function selectSocSeti()
{
	$sql = "SELECT 	ss.date_dob,ss.id_user,ss.text,ss.id_c,ss.id_p_c,
					u.fio,u.id 
				FROM crm_soc_seti ss
					INNER JOIN s_employee u ON u.id = ss.id_user
				ORDER BY ss.date_dob DESC";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}

//выборка данных из таблицы коментариев, для вывода комментариев статей
function selectComentSS()
{
	$sql = "SELECT 	c.date_dob,c.id_user,c.text_coment,c.id_c,
					u.fio,u.id 
				FROM crm_coment c
					INNER JOIN s_employee u ON u.id = c.id_user
				ORDER BY date_dob DESC";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);	
}

//добавление статьи в t_soc_seti
function insertSocSeti($date_dob,$text_coment,$id_user)
{
	$sql = "INSERT INTO crm_soc_seti
			  SET 
				  date_dob = '$date_dob',
				  text = '$text_coment',
				  id_user = '$id_user' ";
	mysql_query($sql) or die(mysql_error());
}

//добавляем комент в таблицу t_coment
function insertComentSS($date_dob,$text_coment,$id_user,$id_ss)
{
	$sql = "INSERT INTO crm_coment
			  SET 
				  date_dob = '$date_dob',
				  text_coment = '$text_coment',
				  id_user = '$id_user',
				  id_c = $id_ss ";	
	mysql_query($sql) or die(mysql_error());
}

//выбираем файлы для просмотра в соц сетях
function selectFilesToSS($id_ss)
{
	$sql = "SELECT name,id_f,f_type
				FROM crm_files
				WHERE id_ss = $id_ss";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}
					
////////////////////////////////////////////
//////////   СТРАНИЦА ЗАДАЧ   //////////////
////////////////////////////////////////////

#||||||||||  view_zadacha.inc.php  |||||||||

//выборка статуса для фильтра
function selectAllStatusZ()
{
	$sql = "SELECT DISTINCT status, name_status 
				FROM crm_zadacha z
					LEFT JOIN crm_z_status zs ON z.status = zs.id_status";
	$result = mysql_query($sql) OR die(mysql_error());
	return db2Array($result);			
}

//перевод в текст значения статуса
function statusIntToStr($int)
{
	switch($int)
	{
		case 1 : $status =	'в работе'; break;
		case 2 : $status =	'выполнено'; break;
		case 3 : $status =	'в доработке'; break;
		case 4 : $status =	'завершен'; break;
		case 5 : $status =	'отменено'; break;
		case 'все' : $status =	'все'; break;
		//default :  $status = 'без статуса'; break;
	}
	return($status);
}

//перевод в текст значения приоритета
function prioritetIntToStr($int)
{
	switch($int)
	{
		case 1 : $prioritet =	'низкий'; break;
		case 2 : $prioritet =	'обычный'; break;
		case 3 : $prioritet =	'высокий'; break;
		case 4 : $prioritet =	'горит'; break;
	}
	return($prioritet);
}

//выборка приоритета для фильтра
function selectAllPrioritetZ()
{
	$sql = "SELECT DISTINCT prioritet, name_prioritet
				FROM crm_zadacha z
					LEFT JOIN crm_z_prioritet zp ON z.prioritet = zp.id_prioritet";
	$result = mysql_query($sql) OR die(mysql_error());
	return db2Array($result);
}

//выборка для фильтра инициатора
function selectAllIniciator()
{
	$filtr = '';
	#условия выборки для управляющих подразделениями
	if(definitionUserGroup() == 1){
		$filtr = "WHERE z.iniciator IN (SELECT id_user
							FROM crm_mgr_view
							WHERE id_mgr = ". definitionSESSION() .")
						OR
						z.ispolnitel IN (SELECT id_user
							FROM crm_mgr_view
							WHERE id_mgr = ". definitionSESSION() .")";
	}
	#условия для Админа
	if(definitionUserGroup() == 0){
		$filtr = ""; //"WHERE u.id_com = $id_com";	
	}
	#выборка
	$sql = "SELECT DISTINCT
					z.iniciator,
					u.fio 
				FROM crm_zadacha z
					LEFT JOIN s_employee u ON u.id = z.iniciator
					$filtr
				ORDER BY u.fio ";				
	$result = mysql_query($sql) OR die(mysql_error());
	return db2Array($result);
	
}

//выборка для фильтра исполнителя
function selectAllIspolnitel()
{
	$filtr = '';
	#условия выборки для управляющих подразделениями
	if(definitionUserGroup() == 1){
		$filtr = "WHERE z.ispolnitel IN (SELECT id_user
							FROM crm_mgr_view
							WHERE id_mgr = ". definitionSESSION() .")
						OR
						z.iniciator IN (SELECT id_user
							FROM crm_mgr_view
							WHERE id_mgr = ". definitionSESSION() .")";
	}
	#условия для Админа
	if(definitionUserGroup() == 0){
		$filtr = ""; //"WHERE u.id_com = $id_com";	
	}
	#выборка
	$sql = "SELECT DISTINCT
					z.ispolnitel,
					u.fio 
				FROM crm_zadacha z
					LEFT JOIN s_employee u ON u.id = z.ispolnitel
				$filtr
				ORDER BY u.fio ";				
	$result = mysql_query($sql) OR die(mysql_error());
	return db2Array($result);
}

//подсчёт колличества комментарив
function countCommentZ($id_z)
{
	$sql = "SELECT id_z, COUNT(text_coment) AS t_c
				FROM crm_coment 
				WHERE id_z = $id_z ";
	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	
	//проверка на наличие комментариев, если они есть выводим колличество, если нет то иконку добавления
    /*if($row['id_z'] == $id_z){
        $com = "<td class='zadacha-table-coment' onclick='callForum(". $id_z .")'>". $row['t_c'] ."</td>";
    }elseif($row['id_z'] != $id_z){
        $com = "<td class='zadacha-table-coment' onclick='callForum(". $id_z .")'><img src='../task/task/images/add.png' width='20' height='20'/></td>";
	}*/
	if($row['id_z'] == $id_z){
		$com = "<br/><span class='commentZ'>Комментариев ". $row['t_c'] ."</span>";
    }elseif($row['id_z'] != $id_z){
        $com = "";
	}
	
	return $com;	
}
		
//Формирование запроса фильтра для задач
function filtrAllZadacha($s_s_date,$s_f_date,$f_s_date,$f_f_date,$status,$prioritet,$ispolnitel,$iniciator)
{
	$all_filtr='';
	
	//фильтр статуса
	if(!empty($status)){
		//если переменная не пуста то присваиваем ей значение and через функцию
		$all_filtr = issetFiltrAND($all_filtr);
		//если переменная равна 999 (все) то добавляем пустату
		if($status == 6){			
		//если переменная развна другому значению, то добавляю его в выборку
		}else{
			$all_filtr .= " z.`status` = '$status' ";
		}
	//если зничение статуса не присвоено, то добавляю в выборку статус в работе
	}else{
		$all_filtr = issetFiltrAND($all_filtr);
		$all_filtr .= " z.`status` = '1' ";
	}
	
	/* * if(!empty($status)){
		echo $status;
	//если зничение статуса не присвоено, то добавляю в выборку статус в работе
	}else{
		$all_filtr = issetFiltrAND($all_filtr);
		$all_filtr .= " z.status = 1 ";
	}*/
		
	//фильтр приоритета
	if(!empty($prioritet)){
		//если переменная не пуста то присваиваем ей значение and через функцию
		$all_filtr = issetFiltrAND($all_filtr);
		$all_filtr .= " z.prioritet = '$prioritet' ";
	}

	//фильтр дата старта
	if((!empty($s_s_date)) or (!empty($s_f_date))){
		$all_filtr = issetFiltrAND($all_filtr);
		//если поле не пусто то фильтруем значение, если пусто присваиваем текущуую дату
		if(!empty($s_s_date)){
			//фильтрация значения переменной "от" для ввода в базу
			/*$date_elements  = explode(".",$s_s_date);
			$ds_s_filtr = mktime(0,0,0,$date_elements[1],$date_elements[0],$date_elements[2]);*/
			$ds_s_filtr = processingDateToUnix($s_s_date);
		}else{
			$ds_s_filtr = date("U");
		}
		//если поле не пусто то фильтруем значение, если пусто присваиваем текущуую дату
		if(!empty($s_f_date)){
			//фильтрация значения переменной "до" для ввода в базу
			$ds_f_filtr = processingDateToUnix($s_f_date);
			/*$date_elements  = explode(".",$s_f_date);
			$ds_f_filtr = mktime(0,0,0,$date_elements[1],$date_elements[0],$date_elements[2]);*/
		}else{
			$ds_f_filtr = date("U");
		}
		//выборка даты в базе от и до
		$all_filtr .= " z.date_start BETWEEN ".$ds_s_filtr." AND ".$ds_f_filtr." ";
	}
	//значение по умолчанию, текущий месяц
	/*else{
		$all_filtr = issetFiltrAND($all_filtr);
		//дата за текущий месяц
		$today = date("m.Y");
		$date_elements  = explode(".",$today);
		//форматирование в юникс дату
		$today = mktime(0,0,0,$date_elements[0],0,$date_elements[1]);
		$today += 86400;
		//время "до"
		(int)$tomorrow = date("U");
		
		$all_filtr .= " z.date_start BETWEEN ".$today." AND ".$tomorrow." ";
	}*/
	
	//фильтр дата финиш
	if((!empty($f_s_date)) or (!empty($f_f_date))){
		$all_filtr = issetFiltrAND($all_filtr);
		//если поле не пусто то фильтруем значение, если пусто присваиваем текущуую дату
		if(!empty($f_s_date)){
			//фильтрация значения переменной "от" для ввода в базу
			$dt_el  = explode(".",$f_s_date);

			#$date_elements  = explode(".",$f_s_date);
			$df_s_filtr = mktime(0,0,0,(int)$dt_el[1],(int)$dt_el[0],(int)$dt_el[2]);
		}else{
			$df_s_filtr = date("U");
		}
		//если поле не пусто то фильтруем значение, если пусто присваиваем текущуую дату
		if(!empty($f_f_date)){
			//фильтрация значения переменной "до" для ввода в базу
			$dt_el  = explode(".",$f_f_date);
			
			#$date_elements  = explode(".",$f_f_date);
			$df_f_filtr = mktime(0,0,0,$dt_el[1],$dt_el[0],$dt_el[2]);
		}else{
			$df_f_filtr = date("U");
		}
		
		//выборка даты в базе от и до
		$all_filtr .= " z.date_finish BETWEEN ".$df_s_filtr." AND ".$df_f_filtr." ";
	}
	
	##############################
	#	Группы пользователей
	##############################
	//если Админ то открываем доступ к инициаторам и исполнителям
	if(definitionUserGroup() == 0){
		//фильтр исполнителя
		if(!empty($ispolnitel)){
			$all_filtr = issetFiltrAND($all_filtr);
			$all_filtr .= " z.ispolnitel = '$ispolnitel' ";
		}
		
		//фильтр инициатора
		if(!empty($iniciator)){
			$all_filtr = issetFiltrAND($all_filtr);
			$all_filtr .= " z.iniciator = '$iniciator' ";
		}
	}
	
	//если Руководитель отдела то открываем доступ к инициаторам и исполнителям
	if(definitionUserGroup() == 1){
		//фильтр инициатора
		if(!empty($iniciator)){
			$all_filtr = issetFiltrAND($all_filtr);
			$all_filtr .= "  z.iniciator=".$iniciator." 
							AND  
								((iniciator IN 
									(SELECT id_user
										FROM crm_mgr_view
										WHERE id_mgr = ". definitionSESSION() ."))
								OR 
								(ispolnitel IN 
									(SELECT id_user
										FROM crm_mgr_view
										WHERE id_mgr = ". definitionSESSION() .")))";
		}
		//фильтр исполнителя
		if(!empty($ispolnitel)){
			$all_filtr = issetFiltrAND($all_filtr);
			$all_filtr .= " z.ispolnitel=".$ispolnitel." 
							AND  
								((ispolnitel IN 
									(SELECT id_user
										FROM crm_mgr_view
										WHERE id_mgr = ". definitionSESSION() ."))
								OR
								(iniciator IN
									(SELECT id_user
										FROM crm_mgr_view
										WHERE id_mgr = ". definitionSESSION() .")))";
		}
		
		//если переменная инициатора и исполнителя пуста 
		if(empty($iniciator) && empty($ispolnitel)){
			if(empty($all_filtr)){
				$so = ''; $sc = '';
			}else{
				$so = '('; $sc = ')';
			}
			$all_filtr = issetFiltrAND($all_filtr);
			$all_filtr .= $so ." ispolnitel IN (SELECT id_user
										FROM crm_mgr_view
										WHERE id_mgr = ". definitionSESSION() .")
								OR
								iniciator IN (SELECT id_user
										FROM crm_mgr_view
										WHERE id_mgr = ". definitionSESSION() .")
						". $sc;
		}
	}
	
	//если пользователь то делаем выборку по его ид
	if(definitionUserGroup() != 1 &&  definitionUserGroup() != 0){
		if(empty($all_filtr)){
			$so = ''; $sc = ''; 
		}else{
			$so = '('; $sc = ')';
		}
		$all_filtr = issetFiltrAND($all_filtr);
		$all_filtr .= $so ." z.ispolnitel = ". definitionSESSION() ." OR z.iniciator = ". definitionSESSION() ." ". $sc;
		
	}
	
	/*//Фильтр по компаниям, выбираем текущюю
	if(!empty($id_com)){
		$all_filtr = issetFiltrAND($all_filtr);
		$all_filtr .= " u.id_com = $id_com ";
	}
		
	//если переменная фильтра не пуста то присваиваем ей есловие sql WHERE и ранее склееный фильтр 
	if ($all_filtr!=''){
		$all_filtr=' WHERE '.$all_filtr;
	}*/
	//echo "<pre>".$all_filtr."</pre>";
	return($all_filtr);
}
	
//выборка данных для ответа клиенту при добавлении задачи
function selectLastZadachaUser($id_user)
{
	$sql = "SELECT isp.fio AS ispolnitel, id_z
				FROM crm_zadacha
					LEFT JOIN s_employee isp ON ispolnitel = isp.id
				WHERE id_z = (SELECT MAX(id_z) 
								FROM crm_zadacha
								WHERE iniciator = $id_user
								LIMIT 1)
				LIMIT 1";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);	 	
}
		
#|||||||||||||  add_z.inc.php  |||||||||||||

//Добавление задачи
function addZadacha($dt_s,$ini,$text_z,$is,$dt_f,$pr,$cap_z)
{
	$sql = "INSERT INTO crm_zadacha
				SET date_start = $dt_s,
				iniciator = $ini,
				text_zadacha = '$text_z',
				ispolnitel = $is,
				date_finish = $dt_f,
				prioritet = '$pr',
				caption_zadacha = '$cap_z' ";
	mysql_query($sql) or die (mysql_error());
}

//Выборка пользователей для скиска выдачи задачи
function selectAllUserToAddZ()
{
	$sql = "SELECT id, fio
				FROM s_employee u
				ORDER BY u.fio ";
	$result = mysql_query($sql) OR die(mysql_error());
	return db2Array($result);
}
			
#|||||||||||||  forum_z.inc.php  |||||||||||
				
//Вывод задачи на экран
function selectZadachaForum($id)
{
	$sql = "SELECT 		z.id_z,z.ispolnitel,z.iniciator,z.text_zadacha,z.caption_zadacha,z.status,z.date_finish,z.prioritet,
						u.fio
				FROM crm_zadacha z
					INNER JOIN s_employee u ON z.ispolnitel = u.id
				WHERE id_z = ".$id."
				LIMIT 1";
	$result = mysql_query($sql) OR die(mysql_error());
	return db2Array($result);
}

//Вывод файлов задачи
function selectZadachaForumFiles($id)
{
	$sql = "SELECT name,id_f, f_type
				FROM crm_files
				WHERE id_z = $id ";
	$result = mysql_query($sql) OR die(mysql_error());
	return db2Array($result);
}

//Выборка коментариев из базы по ИД задачи
function selectZadachaForumComent($id)
{
	$sql = "SELECT 	u.id, u.fio,
					c.id_user, c.date_dob, c.text_coment,
					er.rollid
				FROM s_employee u
					INNER JOIN crm_coment c ON c.id_user = u.id
					INNER JOIN t_employee_role er ON u.id = er.employeeid
				WHERE c.id_z = $id 
				ORDER BY c.date_dob";
	$result = mysql_query($sql) OR die(mysql_error());
	return db2Array($result);
}

//Добавление коментариев форума в базу
function insertZadachaForumComent($date_dob,$text_coment,$id_user,$id_z)
{
	$sql = "INSERT INTO crm_coment
				  SET date_dob = $date_dob,
				  text_coment  = '$text_coment',
				  id_user = $id_user,
				  id_z = $id_z ";
	mysql_query($sql) OR die(mysql_error());
}

//Удаление задания
function deleteZadacha($id)
{
	$sql = "DELETE FROM crm_zadacha WHERE id_z = $id ";
	mysql_query($sql) OR die(mysql_error());	
}

//цветовое разделение комментариев
function viewCommentToGroup($user,$thisUser,$group)
{
	//*** проверка группы, если группа другая меняем цвет ***//
	if($thisUser == $user){
		$colorClass = 'div_coment';
	}else{
		switch($group){
			case 1 : $colorClass = 'div_coment_admin'; break;
			case 2 : $colorClass = 'div_coment_user'; break;
			case 4 : $colorClass = 'div_coment_mgr'; break;
			default: $colorClass = 'div_coment_default';break;
		}
	}
	return $colorClass;	
}
						
//выбор иконки по Ид приоритета
function selectImgPrioritet($id)
{
	switch($id){
		case 1 : $link = "../task/crm/images/prioritet_low.png";break;
		case 2 : $link = "../task/crm/images/prioritet_normal.png";break;
		case 3 : $link = "../task/crm/images/prioritet_hign.png";break;
		case 4 : $link = "../task/crm/images/prioritet_fire.png";break;
	}
	return $link;
}
#||||||||||  processing_Z.php ||||||||||||||

//Выборка описания задачи для добавления нового описания
function selectZOpisanie($id)
{
	$sql = "SELECT text_zadacha
				FROM crm_zadacha
				WHERE id_z = $id 
				LIMIT 1";
	$result = mysql_query($sql) or die(mysql_error());
	return db2String($result);
}

//Обновление описания
function insertZOpisanie($text,$id)
{
	$sql = "UPDATE crm_zadacha
				SET text_zadacha = '$text'
				WHERE id_z = $id ";
	mysql_query($sql) or die(mysql_error());
}

//смена статуса в задании
function updateStatusLC($status,$id_z,$date_complete='')
{
	$filtr = '';
	if(!empty($date_complete)){
		$filtr = ", date_complete = ".$date_complete;
	}
	$sql = "UPDATE crm_zadacha
				SET status = '$status'
				$filtr
				WHERE id_z = $id_z";
	mysql_query($sql) or die(mysql_error());
	//если функция выпонена, передаю 1цу в ответ обработчику
	return 1;
}

//изменение приоритета задания
function updatePrioritetZ($prioritet,$id_z)
{
	$sql = "UPDATE crm_zadacha
				SET prioritet = '$prioritet'
				WHERE id_z = $id_z ";
	mysql_query($sql) or die(mysql_error());
}

//опеределение приоритета по номеру
function detectedNumberPrioritet($int)
{
	switch($int){
		case 1 : $nameP = 'низкий'; break;
		case 2 : $nameP = 'обычный'; break;	
		case 3 : $nameP = 'высокий'; break;	
		case 4 : $nameP = 'горит'; break;
		default : $nameP = 'не выставлен'; break;
	}
	
	return($nameP);	
}
						
////////////////////////////////////////////
//////////   АДМИНКА САЙТА    //////////////
////////////////////////////////////////////

//Обнавление группы  и подразделения для Т_пользователя
function updateGroupUserTo($id_user,$id_group,$pd)
{
	$sql = "UPDATE s_employee
				SET id_pd = $pd,
				id_group = $id_group
				WHERE id = $id_user ";
	mysql_query($sql) OR die(mysql_error());	
}

#|||||||||  processing_Com.php |||||||||||||

//Создание списка пользователей для просмотра менеджеру
function createMgrGroup($insert,$mgr)
{
	$sql = "DELETE FROM t_employee_interface
				WHERE employeeid = ". $mgr ." AND rollid = 1";
	mysql_query($sql) OR die(mysql_error());
	
	$sql = "INSERT t_employee_interface
				SET rollid = 1, employeeid = ". $mgr ."";
	mysql_query($sql) OR die(mysql_error());
	
	$sql = "DELETE FROM crm_mgr_view
				WHERE id_mgr = ". $mgr ."";
	mysql_query($sql) OR die(mysql_error());

	$sql = "INSERT INTO crm_mgr_view (id_mgr, id_user) VALUES ". $insert ."";
	mysql_query($sql) OR die(mysql_error());
}

//Выборка менеджеров для просмотра
function selectViewGroupMgr()
{
	$sql = "SELECT u.id, u.fio
				FROM s_employee u
					INNER JOIN t_employee_interface ei ON ei.employeeid = u.id
				WHERE ei.rollid = 1";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
} 

//выбор списка пользователей доступных для просмотра менеджеру
function selectViewGroupMgrToUsers($id_mgr)
{
	$sql = "SELECT u.id, u.fio
				FROM s_employee u
					INNER JOIN crm_mgr_view mv ON mv.id_user = u.id
				WHERE mv.id_mgr = ". $id_mgr ." ";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}
				
////////////////////////////////////////////
//////////        ФАЙЛЫ       //////////////
////////////////////////////////////////////

#||||||||||  view_fiels.inc.php    |||||||||

//Выборка файлов для просмотра
function selectAllViewFiles($id_com)
{
	$sql = "SELECT DISTINCT	f.dirs,f.id_user,f.size,f.name,f.date_up,f.id_f,f.file_info,
							u.email,u.I,u.F
				FROM crm_files f
					INNER JOIN s_employee u ON f.id_user = u.id
				WHERE u.id_com = $id_com
				ORDER BY f.date_up DESC";		
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}
			
#||||||||||||  download.php  |||||||||||||||

//добавление файлов в базу
function loadFilesToBase($date,$dirs,$size,$type,$name,$id_user,$id_z,$id_ds,$id_ss)
{
	$sql = "INSERT INTO crm_files 
			SET date_up = '$date',
				dirs = '$dirs',
				size = '$size',
				f_type = '$type',
				name = '$name',
				id_user = $id_user,
				id_z = $id_z,
				id_ds = $id_ds,
				id_ss = $id_ss";
	mysql_query($sql) or die (mysql_error());
}

//выбор последнего ИД отчёта данного пользователя
function selectToIdToDsUser($id_user)
{
	$sql = "SELECT id_ds
				FROM crm_data_sheed
				WHERE id_ds = (SELECT MAX(id_ds)
								FROM crm_data_sheed
								WHERE id_user = $id_user
								LIMIT 1)
				LIMIT 1	";
	$result = mysql_query($sql) or die (mysql_error());
	return db2String($result);
}

//выбор последнего ИД задачи данного пользователя
function selectToIdToZUser($id_user)
{
	$sql = "SELECT id_z
				FROM crm_zadacha
				WHERE id_z = (SELECT MAX(id_z)
								FROM crm_zadacha
								WHERE iniciator = $id_user 
								LIMIT 1)
				LIMIT 1	";
	$result = mysql_query($sql) or die (mysql_error());
	return db2String($result);
}

//выбор последнего ИД статьи в соцсети данного пользователя
function selectToIdToSSUser($id_user)
{
	$sql = "SELECT id_c
				FROM crm_soc_seti
				WHERE id_c = (SELECT MAX(id_c)
								FROM crm_soc_seti
								WHERE id_user = $id_user 
								LIMIT 1)
				LIMIT 1	";
	$result = mysql_query($sql) or die (mysql_error());
	return db2String($result);
}

//выбор расширения файла
function getExtension1($filename)
{
    $arr = explode(".", $filename);
	$type = end($arr);
	return($type);
}

//проверка на расширение файлов
function detectedFormatFile($type, $file, $id_f)
{
	switch($type)
	{
		case "pdf": $name_img = 'pdf.png'; break;
		case "exe": $name_img = 'exe.png'; break;
		case "zip": $name_img = 'zip.png'; break;
		case "doc": $name_img = 'doc.png'; break;
		case "xls": $name_img = 'xls.png'; break;
		case "ppt": $name_img = 'ppt.png'; break;
		case "mp3": $name_img = 'mp3.png'; break;
		case "gif": $name_img = 'gif.png'; break;
		case "png": $name_img = 'png.png'; break;
		case "jpeg":
		case "jpg": $name_img = 'jpg.png'; break;
		case "sql": $name_img = 'sql.png'; break;
		case "css": $name_img = 'css.png'; break;
		default: $name_img = 'default.png';break;
	}
	$mini_img = '<div><a class="a_files" href="/task/company/files/save.php?file=s&id_f='.$id_f.'"><img src="/task/crm/images/mini_icon_type/'.$name_img.'"><br><p>'.$file.'</p></a></div>';
	return $mini_img;
}
				
#|||||||||||     save.php      |||||||||||||

//выборка информации о файле из базы
function selectInfoUploadFile($id)
{
	$sql = "SELECT name,id_f,dirs
				FROM crm_files 
				WHERE id_f = $id
				LIMIT 1 ";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}
////////////////////////////////////////////
//////////   ЛИЧНЫЙ КАБИНЕТ   //////////////
////////////////////////////////////////////

//функция считающая сколько задач необходимо проверить
function countCompliteZ($id_user)
{
	$sql = "SELECT COUNT(status)
				FROM crm_zadacha z
				WHERE iniciator = $id_user AND status = 2 ";
	$result = mysql_query($sql) OR die(mysql_error());
	return db2String($result);
}

//функция считающая сколько зачач находятся в работе
function countWorkZ($id_user)
{
	$sql = "SELECT COUNT(status)
				FROM crm_zadacha z
				WHERE (ispolnitel = $id_user AND status = 1) OR (ispolnitel = $id_user AND status = 3) ";
	$result = mysql_query($sql) or die(mysql_error());
	return db2String($result);
}

//функция считающая сколько коментариев написано для задач относящихся к пользователю
function countComentZ($id_user)
{
	$sql = "";
}

/*
//вывод выполненых задач
function selectCompliteZU($id_user)
{
	$sql = "SELECT 	z.caption_zadacha,z.id_z,z.prioritet,z.date_start,z.date_finish,z.date_complete,
					u.fio, u.id
				FROM crm_zadacha z
					LEFT JOIN s_employee u ON u.id = z.ispolnitel
				WHERE iniciator = $id_user AND status = 2
				ORDER BY z.date_complete DESC ";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
} 

//вывод задач находящихся в работе
function selectWorkZU($id_user)
{
	$sql = "SELECT 	z.caption_zadacha,z.id_z,z.prioritet,z.date_start,z.date_finish,z.date_complete,
					u.fio, u.id
				FROM crm_zadacha z
					LEFT JOIN s_employee u ON u.id = z.iniciator
				WHERE (z.ispolnitel = $id_user AND z.status = 1) OR (z.ispolnitel = $id_user AND z.status = 3)
				ORDER BY z.date_start DESC ";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}*/

//сортировка по приоритету
function sortPrioritet($massiv)
{
	//определение пустых массивов
	$z_fire = array();
	$z_hign = array();
	$z_normal = array();
	$z_low = array();
	
	//сортировка по значению в массиве, на каждый приоритет заполняеться отдельный массив
	foreach($massiv as &$pr){
		if($pr['prioritet'] == 'горит'){
			$z_fire[] = $pr;
		}
		if($pr['prioritet'] == 'обычный'){
			$z_normal[] = $pr;
		}
		if($pr['prioritet'] == 'высокий'){
			$z_hign[] = $pr;
		}
		if($pr['prioritet'] == 'низкий'){
			$z_low[] = $pr;
		}
	}
	
	//склеивание массивов по порядку
	$sort_massiv = array_merge((array)$z_fire,(array)$z_hign,(array)$z_normal,(array)$z_low);
	return($sort_massiv);	
}

#|||||||||| view_profile.inc.php  ||||||||||

//вывод информации о пользователе
function selectInfoToUser($id_user)
{
	$sql = "SELECT * 
				FROM crm_user_info
				WHERE id_user = $id_user
				LIMIT 1";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}

#|||||||||| edit_profile.inc.php ||||||||||

//изменение личной информации пользователя
function updateInfoToUser($email,$tel,$skype,$doljnost,$helps,$id_user)
{
	$sql = "UPDATE crm_user_info 
				SET email = '$email',
					tel = '$tel',
					skype = '$skype',
					doljnost = '$doljnost',
					helps = $helps
				WHERE id_user = $id_user ";
	mysql_query($sql) or die(mysql_error());	
}

#||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
#|||||||||||||||||||||||   ТЕСТОВЫЕ ФУНКЦИИ   |||||||||||||||||||||
#||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||

//функция заполнения данными БД
function insertDataRandomToBD($number)
{
	
	//создание массивов с данными для задачи
	$m_prioritet = array( 1, 2, 3, 4);
	$m_status = array( 1, 2, 3, 4, 5);
	$m_iniciator = array(2, 1, 3, 43, 44, 47, 50, 52, 54, 55, 57, 58, 59, 60, 61, 62, 63, 65, 71, 72, 73);
	$m_ispolnitel = array(2, 1, 3, 43, 44, 47, 50, 52, 54, 55, 57, 58, 59, 60, 61, 62, 63, 65, 71, 72, 73);
	
	for($i=0; $i <= $number; $i++)
	{
		$m_date_start = array(rand(0,1400000444));
			
		//создание даты старта
		$date_start = $m_date_start[array_rand($m_date_start)];
		//создание даты окончания сроков
		$i_f = rand(1,9999999);
		$date_finish = $date_start + $i_f;
		//создание даты завершения задания
		$i_c = rand(1,$i_f );
		$date_complete = $date_start + $i_c;
		
		//выбор из массивов случайного числа
		$prioritet = $m_prioritet[array_rand($m_prioritet)];
		$status = $m_status[array_rand($m_status)];
		$iniciator = $m_iniciator[array_rand($m_iniciator)];
		$ispolnitel = $m_ispolnitel[array_rand($m_ispolnitel)];

		/*$i_rand = rand(1,50);
		for($i=0; $i <= $i_rand ;$i++)
		{
			$caption_zadacha[] = array_merge( range('A', 'Z'));
		}*/
		
		$text_zadacha = array_merge( range('A', 'Z')); 
		
		$sql = "INSERT INTO crm_zadacha
					SET id_p = 0,
						date_start = $date_start,
						iniciator = $iniciator,
						ispolnitel = $ispolnitel,
						date_finish = $date_finish,
						status = $status,
						prioritet = $prioritet,
						caption_zadacha = '$i',
						date_complete = $date_complete ";
		mysql_query($sql) or die(mysql_error());	  
	}
}

//функция форматирования даты из юникс метки в TAMISTAMP напрямую в базу, в другую ячейку
function UdateToDatestamp()
{
	$sql = "SELECT date_dob,id_ds
				FROM crm_data_sheed ";
	$select = mysql_query($sql) or die(mysql_error());
	while($row = mysql_fetch_assoc($select)){
		$new_date = date("Y-m-d H:i:s",$row['date_dob']);
		$sql_ins = "UPDATE crm_data_sheed
						SET add_date = '".$new_date."'
						WHERE id_ds = ".$row['id_ds']." ";	
		mysql_query($sql_ins) or die(mysql_error());				
	}
}

//функция заполнения данными БД
function insertDataRandomToBDclient($number){
	function DBinsert($insert) {
		$insert = substr_replace($insert, ';', strrpos($insert, ','));
		$sql = "INSERT INTO s_clients
					(name, birthday, email, phone)
						VALUE 
					". $insert ."
		";
		mysql_query($sql) or die(mysql_error());	
	}
	//создание массивов с данными для задачи
	$m_prioritet = array( 1, 2, 3, 4);
	$m_status = array( 1, 2, 3, 4, 5);
	$m_iniciator = array(2, 1, 3, 43, 44, 47, 50, 52, 54, 55, 57, 58, 59, 60, 61, 62, 63, 65, 71, 72, 73);
	$m_ispolnitel = array(2, 1, 3, 43, 44, 47, 50, 52, 54, 55, 57, 58, 59, 60, 61, 62, 63, 65, 71, 72, 73);
	$I = array	(	"William", "Henry", "Filbert", "John", "Pat", 'Глеб', 'Григорий', 'Давид', 'Дамир', 'Даниил',
					'Денис', 'Джамал', 'Гюзель', 'Дамира', 'Дарина', 'Дарья', 'Джамиля', 'Диана', 'Диля', 'Дилярам',
					'Дина',  'Динара', 'Ева',  'Евгения', 'Евдокия', 'Екатерина', 'Елена', 'Елизавета', 'Есения'
				);
	$F = array( "Smith", "Jones", "Winkler", "Cooper", "Cline");
	
	function make_seed()
	{
		list($usec, $sec) = explode(' ', microtime());
		return (float) $sec + ((float) $usec * 100000);
	}
		
	srand(make_seed());
	$insert='';
	
	for($i=0,$j=0; $i <= $number; $i++)
	{
		#$m_date_start = array(rand(0,1400000444));
			
		//создание даты старта
		#$date_start = $m_date_start[array_rand($m_date_start)];
		//создание даты окончания сроков
		#$i_f = rand(1,9999999);
		#$date_finish = $date_start + $i_f;
		//создание даты завершения задания
		#$i_c = rand(1,$i_f );
		#$date_complete = $date_start + $i_c;
		
		//выбор из массивов случайного числа
		#$prioritet = $m_prioritet[array_rand($m_prioritet)];
		#$status = $m_status[array_rand($m_status)];
		#$iniciator = $m_iniciator[array_rand($m_iniciator)];
		$ispolnitel = $m_ispolnitel[array_rand($m_ispolnitel)];
		//'87". rand(100000000,999999999)."'),
		$insert .= "('".$I[array_rand($I)] . " " . $F[array_rand($F)]."',
					'".rand(1960,2000)."-".rand(1,12)."-".rand(1,31)."',
					'".$I[array_rand($I)].$ispolnitel."@test.ru',
					'87273861199'),
					";
		$j++;
		if($j == 100)
		{
			DBinsert($insert);
			$j = 0;
			$insert = '';
		}
	}
	DBinsert($insert);  
	unset($insert);
}
?>