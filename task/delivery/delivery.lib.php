<?php


function httpRequest($url){
	$pattern = "/http...([0-9a-zA-Z-.]*).([0-9]*).(.*)/";
	preg_match($pattern,$url,$args);
	$in = "";
	$fp = fsockopen("$args[1]", $args[2], $errno, $errstr, 30);
	if (!$fp) {
	   return("$errstr ($errno)");
	} else {
		$out = "GET /$args[3] HTTP/1.1\r\n";
		$out .= "Host: $args[1]:$args[2]\r\n";
		$out .= "User-agent: PHP client\r\n";
		$out .= "Accept: */*\r\n";
		$out .= "Connection: Close\r\n\r\n" ; 

		fwrite($fp, $out);
		while (!feof($fp)) {
		   $in.=fgets($fp, 128);
		}
	}
	fclose($fp);
	return($in);
}

//Парсер XML
function parserXML($xmlString, $param=1)
{
	$String = substr($xmlString, strrpos($xmlString, '<?xml'));
	$package = new SimpleXMLElement($String);
	
	if($param == 1)
	{
		//if(is_array($package)) {
			foreach ($package->message->msg as $infoRow) {
				$query = "UPDATE sms_logs SET message_ID = '". $infoRow['sms_id'] ."' WHERE id = '". $infoRow['id'] ."' ";
				mysql_query($query) or die(mysql_error());
			}
		//} else {
		//	echo 'ошибка: пареметр не массив';
		//}
	}
	
	if($param == 2)
	{
		if(!empty($package->status->msg)) {
			foreach ($package->status->msg as $infoRow) {
				$status = translationStatus($infoRow);
				$query = "UPDATE sms_logs SET status = '". $status ."'  WHERE message_ID = '". $infoRow['sms_id'] ."' ";
				mysql_query($query) or die(mysql_error());
			}
		}
	}
}

////////////////////////////////////////////
//////////    РАБОТА С СМС    //////////////
////////////////////////////////////////////

#||||||||||			 СМС			|||||||||

//выборка клиентов
/*function selectAllClient($filtr, $thisP=0, $selC=50)
{
	$sql = "
		SELECT cl.name, cl.birthday, cl.email, cl.phone, cl.id
			FROM s_clients cl
			". $filtr ."
			ORDER BY cl.name
			LIMIT ". $thisP .",". $selC ."
	";
	//echo $sql;
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}*/

//выборка ид клиентов соответствующих фильтру без лимита
function selectIdClientFilter($name='', $phone='', $birthday_s='', $birthday_f='', $email='', $city='', $id_mask='')
{	
	$filtr = filterAllClient($name, $phone, $birthday_s, $birthday_f, $email, $city, $id_mask);
	$sql = "
		SELECT cl.id
			FROM s_clients cl
			". $filtr ."
			ORDER BY cl.name
	";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
	//return $result;
}

//выборка данных для плагинации
/*function countClientsToScrollbar($name='', $phone='', $birthday_s='', $birthday_f='', $email='', $city='', $id_mask='', $thisP='', $selC='')
{
	$filtr = '';
	$filtr = filterAllClient($name, $phone, $birthday_s, $birthday_f, $email, $city, $id_mask);

	$sql = "SELECT COUNT(cl.id) AS count_Cl
				FROM s_clients cl
				". $filtr ." ";
	$result = mysql_query($sql) or die(mysql_error());
	return db2String($result);
}*/

// фильтрация клиентов
function filterAllClient($name, $phone, $birthday_s, $birthday_f, $email, $city, $id_mask)
{
	$all_filtr='';
	
	//фильтр имени
	if(!empty($name)){
		//если переменная не пуста то присваиваем ей значение and через функцию
		$all_filtr = issetFiltrAND($all_filtr);
		$all_filtr .= " cl.name LIKE '%$name%' ";
	}
	
	//фильтр телефона
	if(!empty($phone)){
		//если переменная не пуста то присваиваем ей значение and через функцию
		$all_filtr = issetFiltrAND($all_filtr);
		$all_filtr .= " cl.phone LIKE '$phone%' ";
	}

	//фильтр емайла
	if(!empty($email)){
		//если переменная не пуста то присваиваем ей значение and через функцию
		$all_filtr = issetFiltrAND($all_filtr);
		$all_filtr .= " cl.email LIKE '%$email%' "; 
	}
	
	//фильтр города
	if(!empty($city)){
		$all_filtr = issetFiltrAND($all_filtr);
		$all_filtr .= " cl.parentid = '". $city ."' "; 
	}
	
	//фильтр маски телефонов
	if(!empty($id_mask)){
		//если переменная не пуста то присваиваем ей значение and через функцию
		$all_filtr = issetFiltrAND($all_filtr);
		// ФУНКЦИЯ 
		$mask = selectIdToMask($id_mask);
		
		$filtr_mask = ''; 
		
		foreach($mask as $row => $value)
		{
			$filtr_mask .= "( cl.phone LIKE '". $value ."%' ) OR ";
		}
		$filtr_mask	= substr_replace($filtr_mask,'',-4);
		$all_filtr .= $filtr_mask;
	}
	
	//фильтр даты рождения 
	if((!empty($birthday_s)) or (!empty($birthday_f))){
		$all_filtr = issetFiltrAND($all_filtr);
		//если поле не пусто то фильтруем значение, если пусто присваиваем текущуую дату
		if(!empty($birthday_s)){
			//фильтрация значения переменной "от" для ввода в базу
			$date_elements  = explode(".",$birthday_s);
			$b_s_filtr = $date_elements[1]."-".$date_elements[0];
		}else{
			$b_s_filtr = date("m-d");
		}
		
		//если поле не пусто то фильтруем значение, если пусто присваиваем текущуую дату
		if(!empty($birthday_f)){
			//фильтрация значения переменной "до" для ввода в базу
			$date_elements  = explode(".",$birthday_f);
			$b_f_filtr = $date_elements[1]."-".$date_elements[0];
		}else{
			$b_f_filtr = date("m-d");
		}
		//выборка даты в базе от и до
		$all_filtr .= " DATE_FORMAT(cl.birthday, '%m-%d') BETWEEN '$b_s_filtr' AND '$b_f_filtr' ";
	}
	
	// отсеивание клиентов у которых нет телефона
	if(empty($all_filtr)) {
		$all_filtr .= " cl.phone != '' ";
	} else {
		$all_filtr = issetFiltrAND($all_filtr);
		$all_filtr .= " cl.phone != '' ";
	}
	
	if(!empty($all_filtr)) {
		$all_filtr = issetFiltrAND($all_filtr);
		$all_filtr .= " CHARACTER_LENGTH(cl.phone) > 7";
	}
	
	
	
	//если переменная фильтра не пуста то присваиваем ей есловие sql WHERE и ранее склееный фильтр
	/*if ($all_filtr!='') {
		$all_filtr=' WHERE '.$all_filtr;
	}*/
	//echo "<pre>".$all_filtr."</pre>";
	return($all_filtr);
}

//выбор номеров телефонов из базы
function selectAllPhoneClient($idClientPhone)
{
	$sql = "
	SELECT cl.phone, cl.name, cl.id
		FROM s_clients cl
		WHERE cl.id IN (". $idClientPhone .")
	";
	//echo $sql;
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}

//litleSMS  отправка СМС сообщений
function SMS_SEND( $tel='', $message='', $param='' ) 
{
	// список параметров
	// 1 - просмотр баланса
	// 2 - возврат ИД строки
	// 3 - запрос статуса сообщения
	// 4 - возврат ИД массивом
	// 5 - создание имени отправителя
	
	
	// выборка данных из базы для подключения
	$infoAPI = settigsAPIsms(); 
	foreach ($infoAPI as $row)
	{
		//емаил
		if($row['key'] == 'sms_email'){
			$user = $row['value'];		// логин указанный при регистрации или логин api-аккаунта http://littlesms.ru/my/settings/api
		}
		//ключ API
		if($row['key'] == 'sms_API'){
			$key = $row['value'];		// API-key, узнать можно тут: http://littlesms.ru/my/settings/api
		}
		//выбор провайдера смс
		if($row['key'] == 'sms_typeClient'){
			$operator = $row['value'];
		}
		//имя отправителя, отображаемое в смс
		if($row['key'] == 'sms_name'){
			$name = $row['value'];
		}
	}			
	$ssl  = true;						// использовать защищенное SSL-соединение
	
	//если данные имеються, подключаемся
	if(isset($user) and isset($key) and isset($operator))
	{
		if($operator == 'KazInfoTech')
		{
			$debug = false;
			
			//отправка СМС
			if(!empty($param) && $param == 2)
			{
				$url='http://212.124.121.186:809/api?';
				$data='
					<?xml version="1.0" encoding="utf-8" ?>
					<package login="'. $user .'" password="'. $key .'">
					  <message>
						<default sender="'. $name .'" type="0"/>
						'. $message .'
					  </message>
					</package>
				';
				
				//echo $data;
				if( $curl = curl_init() ){
					
					// Задаем ссылку
					curl_setopt($curl,CURLOPT_URL, $url);
					// Скачанные данные не выводить поток
					curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);

					//тип запроса POST
					curl_setopt($curl,CURLOPT_POST,1);

					curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);

					// Нужно вывести http заголовки в массив
					curl_setopt($curl,CURLOPT_HEADER,true);

					curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
					
					// Скачиваем
					$out = curl_exec($curl);
					
					// Закрываем соединение
					curl_close($curl);
					return $out;
				}
			}
			
			if(!empty($param) && $param == 3)
			{
				$url='http://212.124.121.186:809/api?';
				$data='
					<?xml version="1.0" encoding="utf-8" ?>
					<package login="'. $user .'" password="'. $key .'">
					  <status>
						'. $message .'
					  </status>
					</package>
				';

				if( $curl = curl_init() ){

					// Задаем ссылку
					curl_setopt($curl,CURLOPT_URL, $url);
					// Скачанные данные не выводить поток
					curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);

					//тип запроса POST
					curl_setopt($curl,CURLOPT_POST,1);

					curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);

					// Нужно вывести http заголовки в массив
					curl_setopt($curl,CURLOPT_HEADER,true);

					curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 

					// Скачиваем
					$out = curl_exec($curl);
					
					// Закрываем соединение
					curl_close($curl);
					return $out;
				}

			}
		}//закрытие условия KazInfoTech
		
		if($operator == 'Little SMS')
		{
			$api = new LittleSMS($user, $key, $ssl); 

			if(!empty($param) && $param == 1) {
				$balans = '<h3> Мой баланс: '. $api->userBalance() .'</h3>';
				return $balans;
				exit();
			}

			// отправка СМС 
			// 2 возврат ИД строки
			// 4 возврат ИД массивом
			if(!empty($param) && $param == 2 or $param == 4) {
				$ids = $api->messageSend( $tel , $message );
				$result = $api->messageStatus($ids);

				//print_r($result);

				if($param == 2) {
					$masssage_ID = ''; 
					if(empty($result)) {return $masssage_ID;}
					foreach ($result as $message_id => $status) {
						$masssage_ID = $message_id;
					}
				}
				if($param == 4) {
					$masssage_ID = $result; 
					//$masssage_ID = array();
					//foreach ($result as $message_id => $status) {
					//	$masssage_ID[] = $message_id;
					//}
				}
				return $masssage_ID;
			}

			// запрос статуса сообщения
			if(!empty($param) && $param == 3) {
				$idMsg = $tel; 
				$result = $api->messageStatus($idMsg);
				return $result;  
			}
		}//закрытие условия littleSMS
	}
}

//добавление текста СМС в базу
function insertSMSText($insert)
{
	$sql = " INSERT INTO sms_textMsg 
				SET 
					name = '". $insert ."' ";
	mysql_query($sql) or die(mysql_error());
	$sql = " SELECT MAX(id)
				FROM sms_textMsg
				LIMIT 1 ";
	$result = mysql_query($sql) or die(mysql_error());
	return db2String($result);
}

//выборка данных для таблицы маски
function selectToMaskOptions()
{
	$sql = "
		SELECT id, name, description
			FROM sms_mask
	";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}

//выборка масок по ИД
function selectIdToMask($id)
{
	if($id != 'all')
	{
		$id = str_replace('|',',',$id);
		$id = substr_replace($id,'',-1);
		$filtr = "WHERE id IN (". $id .")";
	}
	else
	{
		$filtr = '';
	}

	$sql = "SELECT name
				FROM sms_mask
				". $filtr ."";
	$result = mysql_query($sql) or die(mysql_error());
	$arrResult = db2Array($result);
	
	$arrCharDel = array( '(', ')', '_');
	$clearResult = array(); $i = 0;
	foreach ($arrResult as $row)
	{
		$clearResult[] = str_replace($arrCharDel,"",$row['name']);
	}
	return $clearResult;
}

//выборка телефонов из логов
function selectPhoneToDispatch()
{
	$sql = "
		SELECT sl.id, sl.phone, tm.name
			FROM sms_logs sl
				LEFT JOIN sms_textMsg tm ON tm.id = sl.id_text_msg
			WHERE sl.message_ID IS NULL
	";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}

#||||||||||		   СМС ЛОГИ		  |||||||||

//добавление логов СМС в базу
function insertSMSLogs($insert)
{
	$sql = "
		INSERT INTO sms_logs (id_user, phone, id_text_msg, message_ID, id_client) VALUES ". $insert ."
	";
	mysql_query($sql) or die(mysql_error());
	
}
function insertSMSLogsNotMessage($insert)
{
	$sql = "INSERT INTO sms_logs (id_user, phone, id_text_msg, id_client) VALUES ". $insert ."	";
	mysql_query($sql) or die(mysql_error());
}

// обновление статуса сообщений
function selectLogsToStatusEmpty()
{
	$sql = "
		SELECT message_ID
			FROM sms_logs
			WHERE message_ID != 0 AND ((status != 'Доставлено абоненту' AND status != '(-108) Отклонено') OR (status IS NULL)) 
	";
	$result = mysql_query($sql) or die(mysql_error());
	//return db2Array($result);
	$ID = array();
	while($arr = mysql_fetch_assoc($result)) {
		$ID[] = $arr['message_ID'];
 	}
	return $ID;
}

//неверный номер
function selectLogsToStatus0()
{
	$sql = "
		SELECT id
			FROM sms_logs
			WHERE message_ID = 0
	";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}

#||||||||||		   СМС ШАБЛОНЫ		  |||||||||


// выборка данных по шаблонам
function selectNameTamplateToOptions()
{
	$sql = "
		SELECT id, nameTemplate
			FROM sms_template
	";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}

function infoTamplateToOptions()
{
	$massiv = selectNameTamplateToOptions();
	$option = "<option value=''></option>";
	foreach ($massiv as $row)
	{
		$option .= "<option value='". $row['id'] ."'>". $row['nameTemplate'] ."</option>";
	}
	return $option;
}

function infoTamplateSMS($id) 
{
	$sql = "
		SELECT nameTemplate, name
			FROM sms_template 
			WHERE id = ". $id ."
			LIMIT 1;
	";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}

// перевод статусов СМС рассылки
function translationStatus($status)
{
	switch ($status)
	{
		case '102'			: 
		case 'delivered'	: $translation = 'Доставлено абоненту';		break;
		
		case '101'			:
		case 'enqueued'		: $translation = '(-101) В очереди на отправку';	break;
		
		case '106'			:
		case 'accepted'		: $translation = '(-106) Принято оператором';		break;
		
		case '103'			:
		case 'expired'		: $translation = '(-103) Истек срок жизни SMS';	break;
		
		case '105'			:
		case 'undeliverable': $translation = '(-105) Недоставляемо';			break;
		
		case '108'			:
		case 'rejected'		: $translation = '(-108) Отклонено';				break;
		
		case '104'			:
		case 'deleted'		: $translation = '(-104) Удалено';					break;
		
		case '107'			: $translation = '(-107) Неизвестная ошибка';		break;
		default				: $translation = '';						
	}
	return $translation;
}

#||||||||||		   НАСТРОЙКИ API    	|||||||||

// 
function settigsAPIsms($api='', $email='', $name='', $typeClient='')
{
	if(!empty($api) or !empty($email) or !empty($name) or !empty($typeClient))
	{
		// изменение даныых в таблице
		$sql = "
			UPDATE s_config sc
				SET sc.value = '". $api ."'
				WHERE sc.key = 'sms_API' 
		";
		mysql_query($sql) or die(mysql_error());
		$sql = "
			UPDATE s_config sc
				SET sc.value = '". $email ."'
				WHERE sc.key = 'sms_email' 
		";
		mysql_query($sql) or die(mysql_error());
		$sql = "
			UPDATE s_config sc
				SET sc.value = '". $name ."'
				WHERE sc.key = 'sms_name' 
		";
		mysql_query($sql) or die(mysql_error());
		$sql = "
			UPDATE s_config sc
				SET sc.value = '". $typeClient ."'
				WHERE sc.key = 'sms_typeClient' 
		";
		mysql_query($sql) or die(mysql_error());
	}
	else
	{
		// выборка данных
		$sql = "
			SELECT sc.name, sc.key, sc.value
				FROM s_config sc
				WHERE sc.key = 'sms_API' OR sc.key = 'sms_email' OR sc.key = 'sms_name' OR sc.key = 'sms_typeClient'
		";
		$result = mysql_query($sql) or die(mysql_error());
		
		// проверка на существование полей
		/*if (mysql_num_rows($result) > 4){
			$delete = "DELETE FROM s_config WHERE `key` = 'sms_API'";
			mysql_query($delete) or die(mysql_error());
			$delete = "DELETE FROM s_config WHERE `key` = 'sms_email'";
			mysql_query($delete) or die(mysql_error());
			$delete = "DELETE FROM s_config WHERE `key` = 'sms_name'";
			mysql_query($delete) or die(mysql_error());
			$delete = "DELETE FROM s_config WHERE `key` = 'sms_typeClient'";
			mysql_query($delete) or die(mysql_error());
		}*/
		if (mysql_num_rows($result) < 4) {
			$insert = "INSERT INTO `s_config` (`idout` , `idlink` , `parentid` , `isgroup` , `name` , `key` , `value`) VALUES 
						( null, null, '0', '0', 'Ключ API для СМС рассылки', 'sms_API', ''),
						( null, null, '0', '0', 'Email аккаунта для СМС рассылки', 'sms_email', ''),
						( null, null, '0', '0', 'Имя для отображения в рассылке', 'sms_name', ''),
						( null, null, '0', '0', 'Провайдер СМС Рассылки', 'sms_typeClient', '')";
			mysql_query($insert) or die(mysql_error());
			
			$sql = "SELECT sc.name, sc.key, sc.value
						FROM s_config sc
						WHERE sc.key = 'sms_API' OR sc.key = 'sms_email' OR sc.key = 'sms_name' OR sc.key = 'sms_typeClient'";
			$result = mysql_query($sql) or die(mysql_error());
		}
		return db2Array($result);
	}
}
