<?php
session_start();

if(empty($_SESSION)) {
	include '../../company/check.php';
	checksessionpassword();
}

header('Content-type: text/html, charset=utf-8;');

$debug = TRUE;
if($debug) {
	require_once('../projectPaloma/FirePHPCore/FirePHP.class.php');
	$firephp = FirePHP::getInstance(true);
	require_once('../projectPaloma/FirePHPCore/fb.php');
}

include_once('../config.php');
//include_once('../../company/config.php');;
include_once('../common.lib.php');
include_once('delivery.lib.php');

function __autoload($name)
{
    include "../class/". $name .".Class.php";
}

// переменные для возврата
$answer		= '';
$typeAnswer = 'json';
$MySQL		= new MySQL();
$NewContent = new formationContent();
$Errors		= new Errors();

if( isset( $_GET['sl'] ) ) {
	$connection = $_GET['sl'];
} else {
	switch ($_SERVER['REQUEST_METHOD']) {
		case 'POST'	: $connection = $_POST['sl']; break;
		case 'GET'	: $connection = $_GET['sl']; break;
		default : $connection = '';
	}
}
include_once('../common.lib.php');
include_once('delivery.lib.php'); 

if(!empty($_POST['loading']))
{
	echo json_encode('');
}

switch ($connection){
	
			//////////////////////////////////////////////
			//////         КАРКАС | КОНТЕНТ         //////
			//////////////////////////////////////////////
	case 'contentSMS' : //'contentZadacha'  : 

			# ФИЛЬТР, если данные фильтровались то заходим в условие, обрабатываю пришедшие данные
			if(!empty($_POST['FstartContent']))
			{
				$name		= !empty($_POST['name'])		? $name = clearData($_POST['name'])				: $name = '';
				$phone		= !empty($_POST['phone'])		? $phone = clearData($_POST['phone'])			: $phone = '';
				$birthday_s = !empty($_POST['birthday_s'])	? $birthday_s = clearData($_POST['birthday_s']) : $birthday_s = '';
				$birthday_f = !empty($_POST['birthday_f'])	? $birthday_f = clearData($_POST['birthday_f']) : $birthday_f = '';
				$email		= !empty($_POST['email'])		? $email = clearData($_POST['email'])			: $email = '';
				$city		= !empty($_POST['city'])		? $city = clearData($_POST['city'])				: $city = '';
				$id_mask	= !empty($_POST['id_mask'])		? $id_mask = clearData($_POST['id_mask'])		: $id_mask = '';
				$selCount	= !empty($_POST['selCount'])	? $selCount = clearData($_POST['selCount'],'i') : $selCount = 50;
								
				if(!empty($_POST['thisPage'])){
					$thispage = clearData($_POST['thisPage'],'i');
					# подсчёт начального колличества записей для выборки в базе
					$thispage = ($selCount * $thispage) - $selCount;
				}else{
					$thispage = 0;
				}

				// фильтрованные данные
				# выборка клиентов
				$allClient = selectAllClient($name, $phone, $birthday_s, $birthday_f, $email, $city, $id_mask, $thispage, $selCount);
				# посчёт записей пагинации
				$countCl = countClientsToScrollbar($name, $phone, $birthday_s, $birthday_f, $email, $city, $id_mask, $thispage, $selCount);
				# выбор ИД пользователей
				//$IDAllClient = selectIdClientFilter($name, $phone, $birthday_s, $birthday_f, $email, $city, $id_mask);
				# подсчёт страниц
				$countPage = ceil(($countCl/$selCount)/1) * 1;

			}
			else
			{
				// данные без фильтра
				$allClient = selectAllClient();
				$countCl = countClientsToScrollbar();
				//$IDAllClient = selectIdClientFilter();

				$selCount = 50;
				$countPage = ceil(($countCl/$selCount)/1) * 1;
			}

			# ВЫБОРКА И ЗАПИСЬ ИД ВЫБРАННЫХ КЛИЕНТОВ  
			/*$_SESSION['id_select_client'] ='';
			foreach ($IDAllClient as $row)
			{
				$_SESSION['id_select_client'] .= $row['id']."|";
			}
			unset($row);*/

			# ОСНОВНОЙ Контент 
			$tr = ''; 
			foreach ($allClient as $row) {
				$tr .= "
					<tr>
						<td>
							<input type='checkbox' name='client' value='". $row['id'] ."'>
						</td>
						<td>
							". $row['name'] ."</label>
						</td>
						<td>
							". $row['phone'] ."
						</td>
						<td>
							". $row['birthday'] ."
						</td>
						<td>
							". $row['email'] ."
						</td>
					</tr>
				";
			}
			unset($row);

			# если данные фильтровались то вывожу только массив строк
			if(!empty($_POST['FstartContent']))
			{
				$answer = array( 'tr' => $tr, 'countPage' => $countPage, 'count' => $countCl);
				break;
			}

			# ОКНО ОТРАВКИ СООБЩЕНИЙ
			$option = infoTamplateToOptions();

			$cont = "
				<div id='divSms'>
					
					". $NewContent->createdForm('toolbar', array('message','filtr','loadmask')) ."
					". $NewContent->createdFiltr('smsFiltr', $MySQL->DB_select('name, id', 's_clients', '', 'isgroup = 1')) ."
					
					<table class='table table-hover mainTable'>
						<thead>
							<tr>
								<td class='col-lg-1'>
									<input type='checkbox' name='client' value='all'>
								</td>
								<td class='col-lg-6'>
									ФИО
								</td>
								<td class='col-lg-3'>
									Телефон
								</td>
								<td class='col-lg-5'>
									День Рожденья
								</td>
								<td class='col-lg-4'>
									email
								</td>
							</tr>
						</thead>
						<tbody >
							". $tr ."
						</tbody>
					</table>
					". $NewContent->createdForm('pagiLine', array('nameCount' => 'Клиентов','countPage'=>$countPage, 'count' => $countCl )) ."
				</div>
			";
			
			$balans = "
				<div class='balans'>". SMS_SEND('','',1) ."
					<button class='btn btn-default' name='reflash'>Обновить</button>
				</div>";

			$answer = array( 'cont' => $cont, 'option' => $option, 'balans' => $balans);
	break;
	
			//////////////////////////////////////////////
			////////		ОТПРАВКА СМС		   ///////
			//////////////////////////////////////////////
	case 'sendSMS' :  
			if( empty($_POST['id_client']) or empty($_POST['message']) ) {
				die ('данные введены не корректно');
			}

			# обработка пришедшего массива
			$message	= clearData($_POST['message']);			//сообщение
			$save		= clearData($_POST['save']);			//сохранять сообщение в шаблоны или нет
			$client		= clearData($_POST['id_client']);		//ИД пользователей
			$msgToName	= clearData($_POST['messageToName'],'i');


			# если all то выбираем все ИДшники
			if($client == 'all') {
				//$client = $_SESSION['id_select_client'];
				//unset($_SESSION['id_select_client']);

				$name		= !empty($_POST['name'])		? $name = clearData($_POST['name'])				: $name = '';
				$phone		= !empty($_POST['phone'])		? $phone = clearData($_POST['phone'])			: $phone = '';
				$birthday_s = !empty($_POST['birthday_s'])	? $birthday_s = clearData($_POST['birthday_s']) : $birthday_s = '';
				$birthday_f = !empty($_POST['birthday_f'])	? $birthday_f = clearData($_POST['birthday_f']) : $birthday_f = '';
				$email		= !empty($_POST['email'])		? $email = clearData($_POST['email'])			: $email = '';
				$city		= !empty($_POST['city'])		? $city = clearData($_POST['city'])				: $city = '';
				$id_mask	= !empty($_POST['id_mask'])		? $id_mask = clearData($_POST['id_mask'])		: $id_mask = '';
				
				$arrclient = selectIdClientFilter($name, $phone, $birthday_s, $birthday_f, $email, $city, $id_mask);
				//$id_client = array();
				//while($row = mysql_fetch_assoc($client)){
				//	$id_client[] = $row;
				//} 
				$client = '';
				foreach($arrclient as $row)
				{
					$client .= $row['id'].'|';
				}
			}
			$client = substr_replace($client,'',-1);
			$id_client = explode('|',$client);

			#обработка пришедших ИДшников, формирование массива для запроса в базу
			$arrIdPhone = ''; 
			foreach($id_client as $row_phone)
			{
				$row_phone = clearData($row_phone,'i');

				$arrIdPhone .= $row_phone.",";
			}
			$arrIdPhone = substr_replace($arrIdPhone,'',-1); 
			unset($row_phone);
			//print_r($id_client);die();

			# добавление текста сообщения в базу
			if(isset($_SESSION['admin']))
				$idUser = 0;
			else
				$idUser = $_SESSION['userid'];


			# добавление шаблона в базу
			if(!empty($save)) {
				$nameTemplate = 'сохранённый шаблон';
				insertTemplateNewSMS($message, $nameTemplate);
			} 

			# добавление сообщение в базу и вобор его ИД для создания лога
			$lastIDmsg = insertSMSText($message);

			# выбор телефонов и имен клиентов
			$infoClient = selectAllPhoneClient($arrIdPhone);

			// ОТПРАВКА ЧЕРЕЗ LittleSMS
			if($msgToName == 1)
			{
				$arrCharDel = "/\D/"; 
				$insert = ''; $i = 0;
				foreach ($infoClient as &$row) 
				{
					# если телефона нет, выходим из цикла
					if(!empty($row['phone']))
					{
						# фильтрование телефонов
						$row['phone'] = preg_replace($arrCharDel,'',$row['phone']);

						#замена первого симфола на 7ку
						$row['phone']{0} = '7';

						# создание запроса дабавления в базу
						$insert .= " (". $idUser .", '". $row['phone'] ."', ". $lastIDmsg .", '". $row['id'] ."'),"; 

						# добавление логов группами если больше 300
						$i++;
						if($i == 300)
						{
							$insert = substr_replace($insert,';',-1);
							insertSMSLogsNotMessage($insert);
							$insert = ''; $i = 0;
						}
					}
				}
				$insert = substr_replace($insert,';',-1); 
				insertSMSLogsNotMessage($insert);
			}

			# отправка на телефоны записанныхе в логах
			$dispatch = selectPhoneToDispatch();
			$infoAPI = settigsAPIsms(); 
			foreach ($infoAPI as $row) { 
				if($row['key'] == 'sms_typeClient') {$operator = $row['value'];} 
				if($row['key'] == 'sms_name')		{$name = $row['value'];}
			}

			if($operator == 'KazInfoTech')
			{
				//если сообщений несколько
				$urls = ''; $i = 0;
				if(count($dispatch) > 0)
				{
					foreach($dispatch as $row)
					{
						$urls .= "<msg id='". $row['id'] ."' recipient='". $row['phone'] ."' >". $message ."</msg>";
						$i++;

						if($i > 100)
						{
							$xml = SMS_SEND( '', $urls, 2 );
							parserXML($xml);
							$urls = ''; $i = 0;
						}
					}

					$xml = SMS_SEND( '', $urls, 2 );
					parserXML($xml);
				}
			}
			if($operator == 'Little SMS')
			{
				// рабочая версия по 1 телефону
				foreach($dispatch as $row)
				{
					$message_ID = SMS_SEND( $row['phone'], $_POST['message'], 2 ); // реальная отправка
					$query = "UPDATE sms_logs SET message_ID = '". $message_ID ."' WHERE id = ". $row['id'] ." ";
					mysql_query($query) or die(mysql_error());
				}
				/*$tel = array(); 
				//$tel = ''; 
				$i = 0;
				foreach($dispatch as $row)
				{
					//$tel .= $row['phone'].',';
					$tel[] = $row['phone'];
					$i++;
				//	if($i > 300){
						//$message_ID = SMS_SEND( $tel, $message, 2 ); // реальная отправка
				//	}

				//	$query = "UPDATE sms_logs SET message_ID = '". $message_ID ."' WHERE id = ". $row['id'] ." ";
				//	mysql_query($query) or die(mysql_error());
				}
				//$tel = substr_replace($tel,'',-1);
				$message_ID = SMS_SEND( $tel, $message, 4 ); // реальная отправка
				//
				//$result = array_diff($tel, $message_ID);
				//print_r($tel);
				print_r($tel);
				print_r($message_ID); */
			}

			$answer = 'сообщение отправлено';
			$typeAnswer = 'html';
	break;
	
			//////////////////////////////////////////////
			////////		ПОЛЯ МАСКИ ВВОДА		//////
			//////////////////////////////////////////////
	case 'selectMask' :
			$option = "";
			$massiv = selectToMaskOptions();
			foreach($massiv as $row) {
				$option .= "
					<tr>
						<td><input type='checkbox' name='mask' value='". $row['id'] ."'></td>
						<td>". $row['name'] ."</td>
						<td>". $row['description'] ."</td>
					</tr>"
				;
			}
			$answer = "
			<thead>
				<tr>
					<td><input type='checkbox' name='mask' value='all'></td>
					<td>Маска</td>
					<td>Описание</td>
				</tr>
			</thead>
			<tbody>
				". $option ."
			</tbody>
			";
			$typeAnswer = 'html';
	break;
	
			//////////////////////////////////////////////
			//////   ОБНАВЛЕНИЕ СТАТУСОВ СООБЩЕНИЙ  //////
			//////////////////////////////////////////////
	case 'updateStatus' :
			
			$message_ID = selectLogsToStatusEmpty();
			//print_r($message_ID);
			$infoAPI = settigsAPIsms(); foreach ($infoAPI as $row){	if($row['key'] == 'sms_typeClient'){$operator = $row['value'];	}}			

			//если оператор KazInfoTech
			if($operator == 'KazInfoTech')
			{
				$xml = '';
				foreach($message_ID as $row)
				{
					$xml .= "<msg sms_id='". $row ."'/>";
				}
				//echo $xml;
				$answer = SMS_SEND( '', $xml , 3 ); //echo $answer;
				parserXML($answer, 2);
			}

			//если оператор Little SMS
			if($operator == 'Little SMS')
			{
				$answer = SMS_SEND( $message_ID, '' , 3 );

				//переотправка не отправленных сообщений
				$dispatch = selectPhoneToDispatch();
				if(is_array($dispatch))
				{
					foreach($dispatch as $row)
					{
						$messageID = SMS_SEND( $row['phone'], $row['name'], 2 ); // реальная отправка
						$query = "UPDATE sms_logs SET message_ID = '". $messageID ."' WHERE id = ". $row['id'] ." ";
						mysql_query($query) or die(mysql_error());
					}
				}

				$query = '';
				if(is_array($answer))
				{
					foreach ($answer as $message_ID => $status) 
					{
						$status = translationStatus($status);
						$query = "UPDATE sms_logs SET status = '". $status ."' WHERE message_ID = ". $message_ID ." ";
						mysql_query($query) or die(mysql_error());
					}
				}

				//обновление статусов
				$falseItem = selectLogsToStatus0();
				if(is_array($falseItem))
				{
					foreach($falseItem as $row)
					{
						$query = "UPDATE sms_logs SET status = 'Неверный номер' WHERE id = ". $row['id'] ." ";
						mysql_query($query) or die(mysql_error());
					}
				}
			}


			$answer = SMS_SEND('','',1);
			$typeAnswer = 'html';
	break;

			//////////////////////////////////////////////
			//////    ВЫБОР ИНФОРМАЦИИ О ШАБЛОНЕ    //////
			//////////////////////////////////////////////
	case 'infoTemplate' :
			if(!empty($_POST['thisSelect']))
			{
				$thisSelect = clearData($_POST['thisSelect'],'i');
				$thisSelect = infoTamplateSMS($thisSelect);
				$text = ''; $fio = '';
				foreach ($thisSelect as $row)
				{
					$text = $row['name'];
				}
				$answer = $text;
			}
	break;
	
			//////////////////////////////////////////////
			//////          НАСТРОКА СМС API        //////
			//////////////////////////////////////////////
	case 'sms_settings' :
			
			# Построение формы
			$settings = settigsAPIsms();
			$cont = '';
			foreach($settings as $row)
			{
				# Select
				if($row['key'] == 'sms_typeClient')
				{
					if($row['value'] == 'Little SMS') {
						$option = "	<option value='Little SMS'>Little SMS</option>
									<option value='KazInfoTech'>KazInfoTech</option>";
					}
					elseif($row['value'] == 'KazInfoTech') {
						$option = "	<option value='KazInfoTech'>KazInfoTech</option>
									<option value='Little SMS'>Little SMS</option>";
					}
					elseif($row['value'] == '') {
						$option = "	<option value=''></option>
									<option value='Little SMS'>Little SMS</option>
									<option value='KazInfoTech'>KazInfoTech</option>"; 
					}
					$rowHtml = "	
					<div class='col-md-6'><label>". $row['name'] ."</label></div>
					<div class='col-md-6'><select name='sms_typeClient' required class='form-control' > ". $option ."</select></div>";
				}
				else
				{
					# Input
					$rowHtml = "
					<div class='col-md-6'><label>". $row['name'] ."</label></div>
					<div class='col-md-6'><input class='form-control' type='text' name='". $row['key'] ."' value='". $row['value'] ."'></div>";
				}
				$cont .= "	<div class='row row_bot'>
								". $rowHtml ."
							</div>"; 
			}

			$answer = "
				<div class='settingsAPI'>
					". $cont ."
					<button type='button' class='btn btn-primary' name='saveSettings'>Сохранить</button>
				</div>
			";
			
	break;
			//////////////////////////////////////////////
			//////          НАСТРОКА СМС API        //////
			//////////////////////////////////////////////
	case 'smsSetingSave' :
		
			$sms_typeClient = clearData($_POST['typeClient']);
			$sms_API		= clearData($_POST['sms_API']);
			$sms_email		= clearData($_POST['sms_email']);
			$sms_name		= clearData($_POST['sms_name']);

			$settings = settigsAPIsms();
			foreach($settings as $row)
			{
				if($row['key'] == 'sms_name')
				{
					$oldSms_name = $row['value'];
				}
			}

			settigsAPIsms($sms_API, $sms_email, $sms_name, $sms_typeClient);

			$answer = 'Данные обновлены';
			
			/*if($sms_name != $oldSms_name and !empty($sms_API) and !empty($sms_email))
			{
				header('Location: https://littlesms.ru/api/sender/create?user='.$sms_email.'&apikey='.$sms_API.'&name='.$sms_name.'&description=Название+нашей+компании&use_default=1');
			}
			exit(); */
			
	break;


default : $answer = '';
}

# ТИП ВОЗВРАЩАЕМОГО КОНТЕНТА
switch ($typeAnswer)
{
	case 'html':	echo $answer;				break;
	case 'json':	echo json_encode($answer);	break;
}