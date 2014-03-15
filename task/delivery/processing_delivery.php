<?php
include_once('delivery.lib.php');

switch ($connection){
	
			//////////////////////////////////////////////
			//////         КАРКАС | КОНТЕНТ         //////
			//////////////////////////////////////////////
	case 'contentSMS' : //'contentZadacha'  : 
	case 'filtrDeloverySMS';

			# ФИЛЬТР, если данные фильтровались то заходим в условие, обрабатываю пришедшие данные
			if($connection == 'filtrDeloverySMS')
			{
				$selCount	= !empty($RC['selCount'])	? $selCount = $RC['selCount'] : $selCount = 50;
								
				if(!empty($RC['thisPage'])){
					$thispage = $RC['thisPage'];
					# подсчёт начального колличества записей для выборки в базе
					$thispage = ($selCount * $thispage) - $selCount;
				}else{
					$thispage = 0;
				}

				// фильтрованные данные
				$filtr = filterAllClient($RC['name'], $RC['phone'], $RC['birthday_s'], $RC['birthday_f'], $RC['email'], $RC['city'], $RC['id_mask']);
				# выборка клиентов
				$allClient = $MySQL->DB_select("cl.name, cl.birthday, cl.email, cl.phone, cl.id",'s_clients cl','',$filtr,'cl.name',$thispage .",". $selCount);
				# посчёт записей пагинации
				$countCl = $MySQL->DB_select('COUNT(cl.id) AS count_Cl','s_clients cl','',$filtr,'','','str');
				# подсчёт страниц
				$countPage = ceil(($countCl/$selCount)/1) * 1;
			}
			else
			{
				$selCount = 50;
				$page = 0;
				
				// данные без фильтра
				$allClient = $MySQL->DB_select("cl.name, cl.birthday, cl.email, cl.phone, cl.id",'s_clients cl','','','cl.name',$page.",".$selCount);
				$countCl = $MySQL->DB_select('COUNT(cl.id) AS count_Cl','s_clients cl','','','','','str');
				
				$countPage = ceil(($countCl/$selCount)/1) * 1;
			}

			# ОСНОВНОЙ Контент 
			$tr = ''; 
			if(is_array($allClient)){
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
			}

			# если данные фильтровались то вывожу только массив строк
			if($connection == 'filtrDeloverySMS')
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
			if( empty($RC['id_client']) or empty($RC['message']) ) {
				$firephp->error($Errors->PalomaErrors(8));
				echo $Errors->PalomaErrors(8); break;
			} else {
				$client = $RC['id_client'];
			}

			# если all то выбираем все ИДшники
			if($RC['id_client'] == 'all') {
				
				$arrclient = selectIdClientFilter($RC['name'], $RC['phone'], $RC['birthday_s'], $RC['birthday_f'], $RC['email'], $RC['city'], $RC['id_mask']);
				
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
			if(!empty($RC['save'])) {
				$nameTemplate = substr($RC['message'] , 0, 50);
				$MySQL->DB_insert('sms_template','name, nameTemplate', "('".$RC['message'] ."','". $nameTemplate ."')");
				//insertTemplateNewSMS($RC['message'], $nameTemplate);
			} 

			# добавление сообщение в базу и вобор его ИД для создания лога
			$lastIDmsg = insertSMSText($RC['message']);

			# выбор телефонов и имен клиентов
			$infoClient = selectAllPhoneClient($arrIdPhone);

			// ОТПРАВКА ЧЕРЕЗ LittleSMS
			if($RC['messageToName'] == 1)
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
						$urls .= "<msg id='". $row['id'] ."' recipient='". $row['phone'] ."' >". $RC['message'] ."</msg>";
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
					$message_ID = SMS_SEND( $row['phone'], $RC['message'], 2 ); // реальная отправка
					$MySQL->DB_update('sms_logs','message_ID = '. $message_ID ,"id = ".$row['id'] );
					/*$query = "UPDATE  SET message_ID = '". $message_ID ."' WHERE id = ". $row['id'] ." ";
					mysql_query($query) or die(mysql_error());*/
				}
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
			if(!empty($RC['thisSelect']))
			{
				$thisSelect = infoTamplateSMS($RC['thisSelect']);
				$text = ''; $fio = '';
				foreach ($thisSelect as $row)
				{
					$text = $row['name'];
				}
				$answer = $text;
				$typeAnswer = 'html';
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
		
			$settings = settigsAPIsms();
			foreach($settings as $row)
			{
				if($row['key'] == 'sms_name')
				{
					$oldSms_name = $row['value'];
				}
			}

			settigsAPIsms($RC['sms_API'], $RC['sms_email'], $RC['sms_name'], $RC['typeClient']);

			$answer = 'Данные обновлены';
	break;
}