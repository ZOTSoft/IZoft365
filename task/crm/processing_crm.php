<?php

include_once('crm.lib.php');

switch ($connection){
	
			//////////////////////////////////////////////
			//////         КАРКАС | ЗАДАЧИ          //////
			//////////////////////////////////////////////
	case 'requere_zadacha' : //'contentZadacha'  : 
			if(definitionSESSION() >0) {
				$arr = array('add','filtr');
			} else {
				$arr = array('filtr');
			}
			$answer = "	
				<div id='task' class='righttd-content'>
					". $NewContent->createdForm('toolbar',$arr ) ."
					". $NewContent->createdFiltr('taskFiltr') ."

					<table class='zadacha-table'>
						<thead>
							<th>Добавлено</th>
							<th>Завершено</th>
							<th class='green'>Статус</th>
							<th class='green'>Приоритет</th>
							<th>Инициатор</th>
							<th>Исполнитель</th>
							<th width='30%'>Задание</th>
						</thead>
						<tbody></tbody>
					</table>
					
					<div class='clear'></div>
					". $NewContent->createdForm('pagiLine', array('nameCount' => 'Заданий','countPage'=>0, 'count' => 0 )) ."
				</div>";
	break;
	
			//////////////////////////////////////////////////////////////////
			//////     ВЫВОД ФИЛЬТРОВАННЫХ ДАННЫХ | ОБРАБОТКА | ЗАДАЧИ  //////
			//////////////////////////////////////////////////////////////////
	case 'TaskFilter' :
			$RC['selCountZ']	= !empty($RC['selCountZ'])	? $RC['selCountZ']	: $RC['selCountZ'] = 20;
			$RC['ispolnitel']	= isset($RC['ispolnitel'])	? $RC['ispolnitel'] : $RC['ispolnitel'] = '';
			$RC['iniciator']	= isset($RC['iniciator'])	? $RC['iniciator']	: $RC['iniciator'] = '';
			if(!empty($RC['thispage'])){
				$thispage = ($RC['selCountZ'] * $RC['thispage']) - $RC['selCountZ'];
			}else{
				$thispage = 0;
			}
						
			$all_filtr = filtrAllZadacha($RC['s_date_start'],$RC['s_date_finish'],$RC['f_date_start'],$RC['f_date_finish'],$RC['status'],$RC['prioritet'],$RC['ispolnitel'],$RC['iniciator']);
			$zadacha_all = $MySQL->DB_select('	DISTINCT	
												z.status, z.date_start, z.date_finish, z.prioritet, z.iniciator, z.ispolnitel, z.id_z, z.caption_zadacha, 
												u.id, u.fio,
												zs.name_status, zs.id_status,
												zp.name_prioritet,
												isFio.fio AS fioIS',
												'crm_zadacha z',
												'LEFT JOIN s_employee u ON u.id = z.iniciator
												LEFT JOIN (SELECT fio, id FROM s_employee) isFio ON isFio.id = z.ispolnitel
												LEFT JOIN crm_z_status zs ON zs.id_status = z.status
												LEFT JOIN crm_z_prioritet zp ON z.prioritet = zp.id_prioritet
												LEFT JOIN crm_mgr_view mv ON u.id = mv.id_user',
												$all_filtr,
												'z.date_start DESC',
												$thispage." , ".$RC['selCountZ']);
			
			# формирование контента
			$tr = '';
			foreach($zadacha_all as $row) {
				
				$tr .= $NewContent->createdContent('trTask', array
				(
					'id'			=> $row['id_z'],
					'id_status'		=> $row['id_status'], 
					'nameStatus'	=> $row['name_status'],
					'dtStart'		=> Date_unix($row['date_start'], 1),
					'dtFinish'		=> Date_unix($row['date_finish'], 1),
					'class_pr'		=> colorPrioritet($row['prioritet']),
					'name_prioritet'=> $row['name_prioritet'],
					'id_Ini'		=> $row['iniciator'],
					'idIs'			=> $row['ispolnitel'],
					'nameIni'		=> splitFIOtoMassiv($row['fio']),
					'nameIs'		=> splitFIOtoMassiv($row['fioIS']),
					'com'			=> countCommentZ($row['id_z']),
					'caption'		=> $row['caption_zadacha']
				));				   
			}
			
			$countZ = $MySQL->DB_select('COUNT(z.id_z) as count_z','crm_zadacha z','',$all_filtr, '','','str'); 

			$countPageZ = ceil(($countZ/$RC['selCountZ'])/1) * 1;

			$answer = array('tr' => $tr, 'countP' => $countPageZ, 'count' => $countZ);
	break;
			
			/////////////////////////////////////////////////////
			////// 	   ДОБАВЛЕНИЕ | ОБРАБОТКА | ЗАДАЧИ    ///////
			/////////////////////////////////////////////////////
	case 'new_zadacha'  : 
			
			$iniciator = definitionSESSION();
			$date_start = date('U');
			$prioritet	= empty($RC['prioritet'])? $prioritet = 1 : $RC['prioritet'];
			

			# преобразование даты в юникс метку
			if(!empty($RC['dtF'])) {
				$date_finish = processingDateToUnix($RC['dtF']);
			} else {
				$date_finish = date("U");
			}
			
			# проверка правильности введённости сроков
			if($date_finish <= $date_start)
			{
				$firephp->error($Errors->PalomaErrors('lol2')); break;
			}
			
			# добавление данных в базу через функцию
			addZadacha($date_start,$iniciator,$RC['textZ'],$RC['ispolnitel'],$date_finish,$prioritet,$RC['captionZ']);

			# формирование ответа
			$answerIni = splitFIOtoMassiv($_SESSION['fio']);
			$answerPr = prioritetIntToString($prioritet);
			$answerSelect = selectLastZadachaUser(definitionSESSION());
			foreach($answerSelect as $row)
			{
				$answerId_z = $row['id_z'];
				$answerIs = $row['ispolnitel'];
			}

			#|||||||||||  EMAIL  ||||||||||||||||||||

			//если стоит checked то оправляеться письмо письмо 
			if(!empty($RC['toEmail']) && $RC['toEmail'] == 'on')
			{
				$to = $MySQL->DB_select('email','s_employee','','id = '.$RC['ispolnitel'],'','1','str');
				$Delivery = new Delivery();
				$Delivery->createMessageEmailZ($to, $RC['captionZ'], $RC['dtF'], $answerPr, $answerIni, $RC['textZ']);
			}

			#|||||||||||  ФОРМИРОВАНИЕ ОТВЕТА ПОЛЬЗОВАТЕЛЮ  |||||||||||||||||
			$answer = $NewContent->createdContent('trTask', array
			(
				'id'			=> $answerId_z,
				'nameStatus'	=> 'в работе',
				'dtStart'		=> date("d.m.Y H:i",$date_start),
				'dtFinish'		=> $RC['dtF'],
				'class_pr'		=> colorPrioritet($prioritet),
				'name_prioritet'=> $answerPr,
				'nameIni'		=> $answerIni,
				'nameIs'		=> $answerIs,
				'com'			=> countCommentZ($answerId_z),
				'caption'		=> $RC['captionZ']
			));		
	break;
	
	
			//////////////////////////////////////////////
			////	ОБНОВЛЕНИЕ СТАТУСОВ | ЗАДАЧИ, ЛК  ////
			////////////////////////////////////////////// 
	case 'edit_status' :
			
			if(empty($RC['count_w'])){ $RC['count_w'] = 0;}
			if(empty($RC['count_nw'])){ $RC['count_nw'] = 0;}
			if(empty($RC['thisStatus'])){ $RC['thisStatus'] = 0;}
			$answer = definitionCountZ_W_NW($RC['count_w'], $RC['count_nw']);
			$param	= $RC['param'];
			$id		= $RC['id_z'];
			$date_complete = date("U");
			
			$filtr = '';
			if(!empty($date_complete)){
				$filtr = ", date_complete = ".$date_complete;
			}
	
			$MySQL->DB_update('crm_zadacha', 'status = '. $RC['param']. $filtr, 'id_z = '. $id);
			//$test = updateStatusLC( $param, $id, $date_complete );

			switch($param){
				// COMPLETE
				case 2	:	if(!empty($RC['id_ini']) && $RC['id_ini'] == definitionSESSION())
							{
								$answer['count_nw']++;
							}
							$answer['count_w']--; 
				break;
				// RETURN_Z
				case 3	:	if(!empty($RC['id_is']) && $RC['id_is'] == definitionSESSION())
							{
								$answer['count_w']++;
							}
							if($RC['thisStatus'] != 5 )
							{
								$answer['count_nw']--;
							}
				break; 
				// ЗАВЕРШЕНО
				case 4	:	$answer['count_nw']--;
				break;
				// CENCEL
				case 5	:	if(!empty($RC['id_st']) && !empty($RC['id_is']))
							{
								switch($RC['id_st'])
								{
									case 1: 
									case 3: if($RC['id_is'] == definitionSESSION())
											{
												$answer['count_w']--;
											}
											break;
									case 2:	$answer['count_nw']--;
											break;
								}
							}
							else
							{
								$answer['count_nw']--;
							}
				break;
			}//завершение проверки существования $test
	break;
	
			//////////////////////////////////////////////
			////////  ФОРМА ПРОСМОТРА ЗАДАНИЙ	   ///////
			//////////////////////////////////////////////
	case 'z_notice_nw' :   # задания для проверки
	case 'z_notice_w' :	 # задания в работе
			$sorting = '';
			if(isset($RC['text'])) {
				switch ($RC['text']) {
					case 'Задача'		: $sortField = 'z.caption_zadacha';	break;
					case 'Инициатор'	: $sortField = 'z.iniciator';		break;
					case 'Приоритет'	: $sortField = 'z.prioritet';		break;
					case 'Сроки'		: $sortField = 'z.date_finish';		break;
					case 'Начало'		: $sortField = 'z.date_start';		break;
					case 'Выполнено'	: $sortField = 'z.date_complete';	break;
					case 'Исполнитель'	: $sortField = 'z.ispolnitel';		break;
					default :  $sortField = 'z.id_z';						break;
				}
				switch ($RC['sort']) {
					case 'inc'	: $sort = 'ASC'; break;
					case 'desc' : $sort = 'DESC'; break;
				}
				$sorting = $sortField.' '.$sort;  
			}
			
			switch ($connection) {
				case 'z_notice_nw' :
						if(empty($sorting)) {
							$sorting = 'z.date_complete DESC';
						}
						$tasks = $MySQL->DB_select('z.caption_zadacha,z.id_z,z.prioritet,z.date_start,z.date_finish,z.date_complete,u.fio, u.id',
													'crm_zadacha z',
													'LEFT JOIN s_employee u ON u.id = z.ispolnitel',
													'iniciator = '.definitionSESSION().' AND status = 2',
													$sorting);
						$tdButton = "
							<td>
								<button class='button' name='return_z' onclick='processing_status(this, 3);'><img src='/task/crm/images/return.png'  width='35' height='35' /></button>
								<button class='button' name='tocomplete' onclick='processing_status(this, 4);'><img src='/task/crm/images/tocomplete.png'  width='35' height='35'/></button>
								<button class='button' name='cancel' onclick='processing_status(this, 5);'><img src='/task/crm/images/cancel.png'  width='30' height='30'/></button>
							</td>";
						$marker = 'nw'; $position = "Исполнитель"; $dtStatus = "Выполнено";
				break;
			
				case 'z_notice_w' :
						if(empty($sorting)) {
							$sorting = 'z.date_start DESC';
						}
						$tasks = $MySQL->DB_select('z.caption_zadacha,z.id_z,z.prioritet,z.date_start,z.date_finish,z.date_complete,u.fio, u.id',
											'crm_zadacha z',
											'LEFT JOIN s_employee u ON u.id = z.iniciator',
											'(z.ispolnitel = '.definitionSESSION().' AND z.status = 1) OR (z.ispolnitel = '.definitionSESSION().' AND z.status = 3)',
											$sorting);
						$tdButton = "
							<td>
								<button class='button' name='complete' onclick='processing_status(this, 2);'><img src='/task/crm/images/complete.png'  width='35' height='35'/></button>
							</td>";
						$marker = 'w'; $dtStatus = "Сроки"; $position = "Инициатор";
				break;
			}
				
			// создание строк
			$tr = '';
			foreach($tasks as $row){
				if(!empty($row['date_finish'])) {
					$dt = "<td>". Date_unix($row['date_finish'], 1) ."</td>";
				} else {
					$dt = "<td>". Date_unix($row['date_complete'],1) ."</td>";
				}
				
				$tr .= "
				<tr id='". $row['id_z'] ."'>
					<td>". Date_unix($row['date_start'], 4) ."</td>
					". $dt ."
					<td class='". colorPrioritet($row['prioritet']) ."'>". prioritetIntToStr($row['prioritet']) ."</td>
					<td value='". $row['id'] ."'>". splitFIOtoMassiv($row['fio']) ."</td>
					<td onclick='callForum(". $row['id_z'] .")' class='zadacha blueTd'>
						". nl2br($row['caption_zadacha']) ."". countCommentZ($row['id_z']) ."
					</td>
					". $tdButton ."
				</tr>";
			}
			
			// если есть сортировка вывожу только строки
			if(isset($RC['text'])) {
				$answer = $tr; break;
			}
			
			# формирование контента
			$answer = "
			<div class='righttd-content'>
				<table class='zadacha-table SunwelSort'>
					<thead>
						<th><span class='glyphicon glyphicon-sort-by-attributes-alt'></span> Начало</th>
						<th>". $dtStatus ."</th>
						<th>Приоритет</th>
						<th>". $position ."</th>
						<th>Задача</th>
						<th class='coment'></th>
					</thead>
					". $tr ."
				</table>	
			</div>
			";
	break;
	
	#		//////////////////////////////////////////////
	#		////// 	  ДОБАВЛЕНИЕ ОПИСАНИЯ | ЗАДАЧИ    ////
	#		//////////////////////////////////////////////
	case 'edit_z' :
		
			if($_POST['id_user'] == definitionSESSION())
			{
				if(!empty($_POST['text']))
				{
					$id = clearData($_POST['id'],'i');

					$old_text = selectZOpisanie($id);
					$old_text = clearData($old_text);

					$new_text = clearData($_POST['text']);

					$answer = "$old_text \n добавлено: \n $new_text";

					insertZOpisanie($answer,$id);
					
					$typeAnswer = 'html';
				}
			}
	break;		

			//////////////////////////////////////////////
			////// 	 ИЗМЕНЕНИЕ ПРИОРИТЕТА | ЗАДАЧИ  //////
			//////////////////////////////////////////////
	case 'prioritet' :
			if(!empty($RC['id_z']))
			{
				$MySQL->DB_update('crm_zadacha',"prioritet = ".$RC['prioritet'],"id_z = ".$RC['id_z']);
			}
	break;		
	
			//////////////////////////////////////////////
			/////  ДОБАВЛЕНИЕ КОММЕНТАРИЯ | ЗАДАЧИ  //////
			//////////////////////////////////////////////			
	case 'newComent' :
			if(!empty($RC['textCom']))
			{
				$date_dob = date('U');
				$id_user = definitionSESSION();

				//добавление в базу коментария
				insertZadachaForumComent($date_dob,$RC['textCom'],$id_user,$RC['id_z']);

				$colorClass = viewCommentToGroup($id_user,$id_user,definitionUserGroup());

				$answer = "
				<div class='".$colorClass."'>
					<div class='div_coment_date'>Добавлено в: ".Date_unix($date_dob, 2)." числа</div>
					<div class='div_coment_name'>Добавил: ".$_SESSION['fio']."</div>
					<div class='clear'></div>
					<div class='text_coment'>".$RC['textCom']."</div>
				</div>
				";
				
				$typeAnswer = 'html';
			}
	break;
	
			//////////////////////////////////////////////
			///  ДОБАВЛЕНИЯ ЗАДАНИЯ | КАРКАС | ЗАДАЧИ  ///
			//////////////////////////////////////////////
	case 'createTaskForm' :
			
			# форма создания ново задачи
			$tableFormNewZ = $NewContent->createdContent('createTaskForm');
			# модальное окно
			$answer = $NewContent->createdForm('modal', array(	'id'=>'newZ',
																'width'=>'600',
																'header'=>'Новая Задача',
																'content'=>$tableFormNewZ,
																'button'=>'save')); 

			$typeAnswer = 'html';
	break;
	
			//////////////////////////////////////////////
			////   ОБСУЖДЕНИЯ | КАРКАС | ЗАДАЧИ, ЛК   ////
			//////////////////////////////////////////////
	case 'contentforum' :

			if(empty($RC['id_z']))
			{
				$firephp->error($Errors->PalomaErrors(1)); break;
			}

			$this_zadacha = selectZadachaForum($RC['id_z']);
			foreach($this_zadacha as $row){
				$nameIni = splitFIOtoMassiv($row['fio']);

				# формирование панели задач 
				$contZPanel = '';           
				//прикрепить файл к заданию или добавить описание
				/*if((isset($row['ispolnitel']) && ($row['ispolnitel']) == definitionSESSION()) or (isset($row['iniciator']) && ($row['iniciator']) == definitionSESSION()))
				{
					$contZPanel .= "
					<div class='block_icon'>
						<button class='add_file button'><img src='../task/crm/images/add_files.png' width='30' height='30' /></button>
					</div>
					";
				}	*/				

				//если статус в работе или в доработке 
				if($row['status'] == 1 or $row['status'] == 3)
				{
					//если текущий пользователь инициатор или исполнитель у него появляються права на изменения статуса
					if((isset($row['ispolnitel']) && ($row['ispolnitel']) == definitionSESSION()))
					{
						$contZPanel .= "
							<button class='button' name='complete' value='". $row['id_z'] ."'><img src='../task/crm/images/complete.png'  width='30' height='30'/></button>
						";
					}
				}

				//если статус выполнено 
				if($row['status'] == 2){
					//если текущий пользователь инициатор у него появляються права на изменения статуса
					if(isset($row['iniciator']) && $row['iniciator'] == definitionSESSION()){
						$contZPanel .= "
							<button class='button' name='return_z' value='". $row['id_z'] ."'><img src='../task/crm/images/return.png'  width='30' height='30'/></button>
							<button class='button' name='tocomplete' value='". $row['id_z'] ."'><img src='../task/crm/images/tocomplete.png'  width='30' height='30'/></button>
						";
					}
				}

				//если текущий пользователь инициатор у него появляються права на изменения приоритета
				if(isset($row['iniciator']) && $row['iniciator'] == definitionSESSION()){
					//$link = selectImgPrioritet($row['prioritet']);
					$color = colorPrioritet($row['prioritet']);

					if($row['status'] != 5){
						$contZPanel .= "
							<button class='button' name='cancel' value='". $row['id_z'] ."'><img src='../task/crm/images/cancel.png'  width='30' height='30'/></button>
						";
					}else{
						$contZPanel .= "
							<button class='button' name='return_z' value='". $row['id_z'] ."'><img src='../task/crm/images/return.png'  width='30' height='30'/></button>
						";
					}

					$contZPanel .= "
					<div class='block_icon'>
						<span class='glyphicon glyphicon-star ". $color ."_text' name='set_prioritet' value='". $row['id_z'] ."' width='30' height='30'></span>
					</div>
					";
					//<button class='button' name='set_prioritet' value='". $row['id_z'] ."'><img src='". $link ."'  width='30' height='30'/></button>
				}

				# проверка на существование файлов у этой задачи
				$select_f = selectZadachaForumFiles($RC['id_z']);
				$conZFile = '';
				foreach($select_f as $row_f)
				{
					$conZFile .= detectedFormatFile($row_f['f_type'], $row_f['name'], $row_f['id_f']);
				}

				# формирование контента задачи        
				$contZ = "
				<div class='viev_text_zadacha' id='" . $row['id_z'] . "'>
					<div class='head_coment'>
						<div class='ispolnitel_coment' ini='". $row['iniciator'] ."'>Исполнитель: ". $nameIni ."</div>
						<div class='date_coment'>Сроки до: ". Date_unix($row['date_finish'], 4) ."</div>
						<div class='status_coment'>Статус задачи: ". statusIntToStr($row['status']) ."</div>
						<div class='panel_crm'>
							". $contZPanel ."
						</div>
					</div> 
					<div class='clear'></div>	
					<h4>Задача</h4>
					<div class='text' >". $row['caption_zadacha'] ."</div>	
					<h4>Описание</h4>			
					<div class='text'>". nl2br($row['text_zadacha']) ."</div>
					<div class='viev_zadacha_file'>
						". $conZFile ."
					</div>
					<div class='clear'></div>

				</div>


				<div class='load_files'>
					<div id='loading'></div>
				</div>
				";
			}

			# вывод комментариев к задаче
			//выборка данных из из таблицы коментов по ид задачи		
				$All_coment = selectZadachaForumComent($RC['id_z']);
				$contZcom = '';
				foreach($All_coment as $row_s_c){
					$addComUser = splitFIOtoMassiv($row_s_c['fio']);
					//определение переменной для вывода данных
					$DTadd = Date_unix($row_s_c['date_dob'], 2 );

					$colorClass = viewCommentToGroup($row_s_c['id_user'],definitionSESSION(),$row_s_c['rollid']);

					$contZcom .= "
					<div class='". $colorClass ."'>
						<div class='div_coment_date'>Добавлено в: ". $DTadd ." числа</div>
						<div class='div_coment_name'>Добавил: ". $addComUser ."</div>
						<div class='clear'></div>
						<div class='text_coment'>". nl2br($row_s_c['text_coment']) ."</div>
					</div>
					";
				}

			# форма добавления комментариев
			if(definitionSESSION() != 0)
			{
				$contFomrCon = "
					<h4>Комментарии:</h4> 
					". $contZcom ."
					<textarea rows='7' name='text' required class='form-control'></textarea>
					<input type='button' value='Комментировать' class='btn btn-default' name='newComent'/>
				";
			}
			else
			{
				$contFomrCon = '';
			}

			# модальное окно
			$answer = $NewContent->createdForm('modal', array(	'id'=>'newZ',
																'width'=>'900',
																'header'=>'Описание задачи',
																'content'=>"<div id='forum_zadacha'>". $contZ ."". $contFomrCon ."</div>")); 
	break;
	
			//////////////////////////////////////////////
			////     СОЗДАНИЕ | ОБРАБОТКА | ОТЧЁТ     ////
			//////////////////////////////////////////////
	case 'newReport' : 
			$id_user = definitionSESSION();
			$report = clearData($_POST['report'],'s');
			
			$date_start		= !empty($_POST['date_start'])  ? $date_start  =  processingDateToUnix(clearData($_POST['date_start'],'s'))  : $date_start = date("U");
			$date_finish	= !empty($_POST['date_finish']) ? $date_finish =  processingDateToUnix(clearData($_POST['date_finish'],'s')) : $date_finish = date("U");

			if($date_start > $date_finish )
			{
				$firephp->error($Errors->PalomaErrors('lol2')); break;
			}

			$MySQL->DB_insert('crm_data_sheed', 'date_start, date_finish, id_user, report', "(".$date_start." , ".$date_finish." , ".$id_user." , '".$report."')");

			#||||||||  ФОРМИРОВАНИЕ ОТВEТА ПОЛЬЗОВАТЕЛЮ  ||||||||||
			$id_r = selectToIdToDsUser($id_user);
			$answerThisTime = Date_unix(date("U"), 3); 
			$answerDtSt = Date_unix($date_start, 3);
			$answerInterval = Date_unix($arrDate = array($date_finish, $date_start), 5);

			//шапка таблицы
			$trHead = "
			<tr class='report_cap_td'> 
				<td colspan='7'>
					Дата: ". Date_unix('', 4) ."
				</td>
			</tr>
			<tr class='report_head_td'>
				<td>Время добавления</td>
				<td>Начало</td>
				<td>В работе</td>
				<td>Сотрудник</td>
				<td>Отчёт</td>
				<td>файлы</td>
			</tr>
			";

			$answer = "
			<tr id='". $id_r ."'>
				<td>". $answerThisTime ."</td>
				<td>". $answerDtSt ."</td>
				<td>". $answerInterval ."</td>
				<td class='nameuser' value='1'>". splitFIOtoMassiv($_SESSION['fio']) ."</td>
				<td class='report_td'>". $report ."</td>
				<td class='report_file_td'> </td>
			</tr>
			";

			$answer = $answer."|". $trHead;
	break;

			//////////////////////////////////////////////
			/// ИЗМЕНЕНИЕ ОТЧЁТА | ОБРАБОТКА | ОТЧЁТ   ///
			//////////////////////////////////////////////
	case 'edit_r' :
			
			if($_POST['id_user'] != definitionSESSION()){
				$firephp->error($Errors->PalomaErrors('lol1')); break;
			}

			$id = clearData($_POST['id_ds'],'i');
			$old_text = updateReport($id);
			$new_text = clearData($_POST['text']);
			$answer = "$old_text 
					\n добавлено: 
					\n $new_text";

			insertReport($answer,$id);
	break;
	
			//////////////////////////////////////////////
			///       ДОБАВЛЕНИЕ | КАРКАС | ОТЧЁТ      ///
			//////////////////////////////////////////////
	case 'createReportForm' :
		
			$forma = "
				<div class='row_bot'>

					<div class='col-md-6'>
						<label>Время начало работы:</label>
						". $NewContent->createdForm('dtime',array('name'=>'date_start', 'ph'=>'от','type'=>'dd.MM.yyyy hh:mm')) ."
					</div>

					<div class='col-md-6'>
						<label>Время завершения работы:</label>
						". $NewContent->createdForm('dtime',array('name'=>'date_finish', 'ph'=>'до','type'=>'dd.MM.yyyy hh:mm')) ."
					</div>
					<div class='clear'></div>
				</div>

				<div class='row_bot'>
					<div class='col-md-12'>
						<label>Описание проделанной работы:</label>
						<textarea name='report' rows=3 class='form-control'></textarea>
					</div>
				</div>
			";

			# модальное окно
				$answer = $NewContent->createdForm('modal', array(	'id'=>'div_form_report',
																	'width'=>'600',
																	'header'=>'Новый отчёт',
																	'content'=>$forma,
																	'buton'=>'save')); 
	break;

			//////////////////////////////////////////////////
			///   ФИЛЬТРАЦИЯ ДАННЫХ | ОБРАБОТКА | ОТЧЁТ    ///
			//////////////////////////////////////////////////
	case 'reportFiltr' :
			$filtr = filtrAllReport($RC['vs'],$RC['date_start'],$RC['date_finish']);
			$All_date = $MySQL->DB_select('ds.add_date','crm_data_sheed ds','LEFT JOIN s_employee u ON ds.id_user = u.id', $filtr, 'ds.add_date DESC');		
			
			//создаю массив с датами
			$date = array();
			foreach($All_date as $row)
			{
				$add_d = processingTimestampToUnix($row['add_date']);
				$date[] = Date_unix($add_d, 4);
				//$format = explode(' ',$row['add_date']);
				//$formatDate = explode('-',$format[0]);
				//$formatTime = explode(':',$format[1]);
				//$date[] = $formatDate;
			}

			//проверка существуют ли отчёты вообще
			if(isset($date))
			{
				//удаляю одинаковые даты
				$date = array_unique($date);

				$trHead ='';
				//вывожу цикл массива по дате
				foreach($date as $d)
				{
					//шапка таблицы
					$trHead .= "
					<tr class='report_cap_td'>
						<td colspan='7'>
							Дата: ". $d ."
						</td>
					</tr>
					<tr class='report_head_td'>
						<td>Время добавления</td>
						<td>Начало</td>
						<td>В работе</td>
						<td>Сотрудник</td>
						<td>Отчёт</td>
						<td>файлы</td>
					</tr>
					";

					$All_DS = $MySQL->DB_select('	ds.id_ds,ds.report,ds.date_finish,ds.date_start,ds.id_user,ds.add_date,u.id,u.fio',
													'crm_data_sheed ds',
													'INNER JOIN s_employee u ON ds.id_user = u.id ', 
													$filtr, 
													'u.fio , ds.date_start');
					foreach($All_DS as $row_ds)
					{
						//создаём переменную для проверки
						$date_q = Date_unix($row_ds['date_start'], 4);

						$tr = '';
						//сравниваем даты, для вывода отчёта, выборка чтобы отчёты соответствовали выводимой дате
						if($d == $date_q)
						{
							//подсчёт потраченного времени
							if($row_ds['date_start'] && $row_ds['date_finish']) 
							{
								$time = Date_unix($arrDate = array($row_ds['date_finish'], $row_ds['date_start']), 5);
								$row_ds['date_start'] = Date_unix($row_ds['date_start'], 3);
							}

							$date_dob = processingTimestampToUnix($row_ds['add_date']);
							//равнение к Алматинскому времени
							$date_dob = Date_unix($date_dob, 3);

							$nameUser = splitFIOtoMassiv($row_ds['fio']);

							$file = ''; //обнуление
							#создаю переменную с файломи если она есть
							$all_files = selectFilesDS($row_ds['id_ds']);
							foreach($all_files as $row_f){
								$file .= detectedFormatFile($row_f['f_type'], $row_f['name'], $row_f['id_f']);
							}

							//формирование строк отчёта
							$tr = "
							<tr id='". $row_ds['id_ds'] ."'>
								<td>". $date_dob ."</td>
								<td>". $row_ds['date_start'] ."</td>
								<td>". $time ."</td>
								<td class='nameuser' value='". $row_ds['id_user'] ."'>". $nameUser ."</td>
								<td class='report_td'>
									". nl2br($row_ds['report']) ."
								</td>
								<td class='report_file_td'>
									". $file ."
								</td>
							</tr>
							";
							$file = ''; //обнуление
						} //закрытия условия сравнений даты

						# склииваю строки таблицы к шапке
						$trHead .= $tr;

					}//закрытие цикла вывода отчётов
				}//закрытие цикла Форэйч

				#переменная для вывода
				$answer = $trHead;
			}//проверка на существование отчётов
	break;
	
			//////////////////////////////////////////////
			/////           КАРКАС | ОТЧЁТ           /////
			//////////////////////////////////////////////
	case 'requere_report' :
			if(definitionSESSION() >0) {
				$arr = array('add','filtr');
			} else {
				$arr = array('filtr');
			}
			$answer = "
			<div id='report_view' class='righttd-content'>
				". $NewContent->createdForm('toolbar', $arr) ."
				". $NewContent->createdFiltr('reportFiltr') ."
				<table class='report-table'>	
				</table>
			</div>
			";
	break;

			////////////////////////////////////////////
			//////	    КАРКАС | СОТРУДНИКИ		 ///////
			////////////////////////////////////////////
	case 'contentSotr' :
			$res = array(); $totalrows = 0;
			$views_user = $MySQL->DB_select("u.id,u.fio, r.name, r.id AS roll, u.email, IFNULL(ss.status,0) AS status",
											"s_employee u",
											"INNER JOIN t_employee_role er ON er.employeeid = u.id
											INNER JOIN s_role r ON er.rollid = r.id
											LEFT JOIN (SELECT ispolnitel, COUNT(status) as status
														FROM crm_zadacha z
														WHERE status = 1 OR status = 3
														Group BY ispolnitel ) ss ON u.id = ss.ispolnitel",
											'','ss.status DESC');
			
			//$views_user = selectViewUserToSotrudniki();
			foreach($views_user as $row)
			{
				# формирование строк с данными сотрудников
				$a['fio'] = $row['fio'];
				$a['email'] = $row['email'];
				$a['name'] = $row['name'];
				$a['countIdZ'] = $row['status'];
				$res[]=$a;
				$totalrows++;
			}

			$answer = array();
			$answer["totalrows"] = $totalrows;
			$answer["rows"] = $res;
			echo json_encode($answer);
	break;
	
	
	
	
			//////////////////////////////////////////////
			//////   ДОБАВЛЕНИЕ СТАТЬИ СС   //////////////
			//////////////////////////////////////////////
	case 'newArticle' :
			if(!empty($_POST['text']))
			{
				$textCom = clearData($_POST['text']);
				$date_dob = date('U');
				$id_user = definitionSESSION();

				insertSocSeti($date_dob,$textCom,$id_user);

				$id_ss =selectToIdToSSUser(definitionSESSION());

				$answer = "	<div class='block_cont_ss' id=".$id_ss.">
								<div class='block_cont'>
									<div class='cont_ss_name'>
										<a href='#'>". splitFIOtoMassiv($_SESSION['fio']) ."</a>
									</div>
									<div class='cont_ss_date'>". Date_unix('',2) ."</div>
									<div class='clear'></div>
								</div>
								<div class='cont_ss_text'>". nl2br($textCom) ."</div>
								<div class='clear'></div>
								<div class='block_cont_menu'>

									<span class='add_coment_article' onclick='addComment(this)'>комментировать</span>
								</div>

								<div class='block_cont_coment'> 	

								</div>
							</div>";
			}
	break;
	
			//////////////////////////////////////////////
			/////    ДОБАВЛЕНИЕ КОММЕНТАРИЯ СС   /////////
			//////////////////////////////////////////////
	case 'newComent' :
			
			if(!empty($_POST['text']))
			{
				if(!empty($_POST['id_ss']))
				{
					$id_ss = clearData($_POST['id_ss'],'i');
					$textCom = clearData($_POST['text']);
					$date_dob = date('U');
					$id_user = $_SESSION['userid'];

					insertComentSS($date_dob,$textCom,$id_user,$id_ss);

					$cont = "
					<div class='block_cont_inner'>
						<span class='ss_name_inner'>
							<a href='#'>". splitFIOtoMassiv($_SESSION['fio']) ."</a>
						</span>
						<span>".Date_unix($date_dob, 2)."</span>
						<span>
							<span class='answer' onclick='addAnswerComent(this)'>ответить</span>
						</span>
					</div>
					<div class='cont_ss_text'>".$textCom."</div>
					<div class='clear'></div> ";

					echo $cont;
					exit();
				}
			}
	break;
	
			//////////////////////////////////////////////
			////    ФОРМИРОВАНИЕ СТАТЕЙ ДЛЯ ВЫВОДА   /////
			//////////////////////////////////////////////
	case 'requere_soc_seti' :
			//выборка данных из таблицы коментариев, и загрузка их в массив
			$row_c = selectComentSS();

			//реверсия массива c комментариями
			$row_c = array_reverse($row_c);

			//выборка данных из из таблицы соц сетей, и загрузка их в массив
			$row_s = selectSocSeti();

			$contArt = '';
			foreach($row_s as $row_art)
			{
				//обнуление переменных
				$contFiles = '';
				$icon = '';
				$img = '';
				$contCom = '';

				# Формирование списка файлов прикреплёных к статье, если они есть
				$files = selectFilesToSS($row_art['id_c']);
				if(!empty($files))
				{
					foreach($files as $files_ss)
					{
						$icon .= detectedFormatFile($files_ss['f_type'], $files_ss['name'], $files_ss['id_f']);

						//если файл изображение то выводим его в статью
						switch($files_ss['f_type'])
						{
							case 'jpg'	:
							case 'png'	:
							case 'jpeg'	:
							case 'gif'	: 	$img = "<br><img src='files/save.php?file=s&id_f=". $files_ss['id_f'] ." style='max-wight:400px; max-height:400px; margin:5px 0 0 0;'>";
						}

					}

					//иконки на скачивание файлов
					$contFiles .= "
					<div class='cont_ss_files'>
						". $icon ."
					</div>
					";
				}

				# формирование списка комментариев, если они есть
				foreach($row_c as $row_com)
				{
					if($row_art['id_c'] == $row_com['id_c'])
					{
						$comNameUser = splitFIOtoMassiv($row_com['fio']);

						$contCom .= "
						<div class='block_cont_inner'>
							<span class='ss_name_inner'><a href='#'>". $comNameUser ."</a></span>
							<span>". Date_unix($row_com['date_dob'],2) ."</span>
							<span class='answer' onclick='addAnswerComent(this)'>ответить</span>
						</div>
						<div class='cont_ss_text'>". nl2br($row_com['text_coment']) ."</div>
						<div class='clear'></div>
						";
					}
				}

				# формирование статей 
				//обработка даты
				$addDate = Date_unix($row_art['date_dob'],2);
				$artNameUser = splitFIOtoMassiv($row_art['fio']);

				$contArt .= "
				<div class='block_cont_ss' id='". $row_art['id_c'] ."'>
					<div class='block_cont'>
						<div class='cont_ss_name'>
							<a href='#'>". $artNameUser ."</a>
						</div>
						<div class='cont_ss_date'>". $addDate ."</div>
						<div class='clear'></div>
					</div>
					<div class='cont_ss_text'>
						". nl2br($row_art['text']) ."
						". $img ."
					</div>
					<div class='clear'></div>
					<div class='block_cont_menu'>
						". $contFiles ."
						<span class='add_coment_article' onclick='addComment(this)'>комментировать</span>
					</div>
					<div class='block_cont_coment'>
						". $contCom ."
					</div>
				</div> 
				";
			}

			$answer = "
			<div class='righttd-content'>
				<div id='div_container_ss'>

					<div class='block_new_article'>
						<textarea  placeholder='Введите сообщение и нажмите Ctrl + Inter'  name='text' class='textarea_width_main'></textarea>
						<button class='add_file'><img src='../task/crm/images/add_files.png' width='25' height='25' /></button>
						<button type='submit' class='btn btn-default' name='new_article' >Отправить</button>
						<div id='loading'></div>
					</div>
					". $contArt ."
				</div>
			</div>
			";
	break;

			//////////////////////////////////////////////
			////   ЗАГРУЗКА ОСНОВНОЙ ФОРМЫ НАСТРОЕК   ////
			//////////////////////////////////////////////
	case 'requere_settings_mgr' :
		/*
			$buttonEditGroup = '';
			$buttonViewGroup = '';
			
			$buttonEditGroup = "
			<button onclick=\"processingSL('CreateMgrTask')\" class='btn btn-default'>
				<img src='/task/crm/images/edit_text.png' height='20' width='20'/>
				Создание Менеджера
			</button>
			";

			$buttonViewGroup = "
			<button onclick=\"processingSL('view_group')\" class='btn btn-default'>
				<img src='/task/crm/images/view.png' height='20' width='20'/>
				Просмотр Менеджеров
			</button>
			";

			$answer = "
			<div id='block_admin'>
				". $buttonEditGroup ."
				". $buttonViewGroup ."
				<div id='block_admin_cont'></div>
			</div>
			";
			*/
			$firephp->error($Errors->PalomaErrors('outdated'));
	break;

			//////////////////////////////////////////////
			////// 	   ФОРМА ЛИЧНОЙ ИНФОРМАЦИИ     ///////
			//////////////////////////////////////////////
	case 'profile' :
	/*
			$user_info = selectInfoToUser(definitionSESSION());
			foreach($user_info as $ed){

				if($ed['helps'] == 1)
				{
					$helps = "	<label><input type='radio' name='help' value='0'/> Включить</label><br/>
								<label><input type='radio' name='help' value='1' checked /> Выключить</label>";
				}
				else 
				{
					$helps = "	<label><input type='radio' name='help' value='0' checked /> Включить</label><br/>
								<label><input type='radio' name='help' value='1'/> Выключить</label>";
				}

				$answer = "
				<div class='modal modal_message'> 
					<div class='modal-dialog' style='width:400px;'>
						<div class='modal-content'>
							<div class='modal-header'>
								<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>
								<h3>Личная иформация</h3>
							</div>
							<div class='modal-body'>

								<div class='caption row_bot'><h4>Контактная информация</h4></div>

								<div class='caption row_bot'>
									<div class='col-md-5'><b>Телефон:</b></div>
									<div class='col-md-6'><input name='tel' type='number' value='". $ed['tel'] ."' class='form-control'/></div>
									<div class='clear'></div>
								</div>

								<div class='caption row_bot'>
									<div class='col-md-5'><b>Email:</b></div>
									<div class='col-md-6'><input name='email' type='email' value='". $ed['email'] ."' class='form-control'/></div>
									<div class='clear'></div>
								</div>

								<div class='caption row_bot'>
									<div class='col-md-5'><b>Скайп:</b></div>
									<div class='col-md-6'><input name='skype' type='text' value='". $ed['skype'] ."' class='form-control'/></div>
									<div class='clear'></div>
								</div>


								<div class='caption row_bot'><h4>Статус</h4></div>

								<div class='caption row_bot'>
									<div class='col-md-5'><b>Должность:</b></div>
									<div class='col-md-6'><input name='doljnost' type='text' value='". $ed['doljnost'] ."' class='form-control'/></div>
									<div class='clear'></div>
								</div> 

								<div class='caption row_bot'>
									<div class='col-md-5'><b>Подсказки:</b></div>
									<div class='col-md-6'>". $helps ."</div> 
								</div>
								<div class='clear'></div>
							</div>

							<div class='modal-footer'>
								<button type='button' class='btn btn-default' data-dismiss='modal'>Закрыть</button>
								<button class='btn btn-primary' name='btnEditUser' type='button'>Сохранить</button>
							</div>
						</div>
					</div>
				</div>
				";
			}
	 * */
		$firephp->error($Errors->PalomaErrors('outdated'));
	break;
	
			//////////////////////////////////////////////
			////// 	  ИЗМЕНЕНИЕ ЛИЧНОГО ПРОФИЛЯ    ///////
			////////////////////////////////////////////// 
	case 'editprofile' :
		/*
			$email = clearData($_POST['email']);
			$tel = clearData($_POST['tel']);
			$skype = clearData($_POST['skype']);
			$doljnost = clearData($_POST['doljnost']);
			$helps = clearData($_POST['helps'],'i');

			$id_user = definitionSESSION();

			updateInfoToUser($email,$tel,$skype,$doljnost,$helps,$id_user);
			*/
		$firephp->error($Errors->PalomaErrors('outdated'));
	break;

			//////////////////////////////////////////////
			////////    ФОРМА ВЫДАЧИ ПРАВ ЮЗЕРАМ   ///////
			//////////////////////////////////////////////
	case 'CreateMgrTask' :
	/*			
			# формирование списков пользователей
			$usersToGroup = '';
			$users = '';
			$allUsers = selectAllUserToAddZ();
			foreach($allUsers as $row)
			{
				$nameuser = splitFIOtoMassiv($row['fio']);
				$usersToGroup .= "<option  value='". $row['id'] ."'>". $nameuser ."</option>";
				$users .= "<br/><label><input type='checkbox' name='user' value='". $row['id'] ."'> ". $nameuser ."</label>";
			}
			$selectMgr = "<select class='form-control' name='userToGroup'>". $usersToGroup ."</select>";
			
			$cont = "<div class='caption row_bot'>
						<label>Выбор менеджера для установки прав</label>
						". $selectMgr ."
					</div> 

					<div class='row_bot'>
						<label>Выбор сотрудников для просмотра</label>
						". $users ."
					</div>";
			
			$answer = $NewContent->createdForm('modal', array('id'=>'CreateMgrTask','width'=>'400','header'=>'Создание менеджера','content'=>$cont,'button'=>'save' ));
	 * */
		$firephp->error($Errors->PalomaErrors('outdated'));
	break;
	
			//////////////////////////////////////////////
			////////      СОЗДАНИЕ МЕНЕДЖЕРА       ///////
			//////////////////////////////////////////////
	case 'saveCreateMgrTask':
/*
			if(empty($RC['userView']) && empty($RC['userMgr'])) {
				$firephp->error($Errors->PalomaErrors(9));
				$answer = $Errors->PalomaErrors(9); break;
			}

			if(!in_array($RC['userMgr'],$RC['userView'])){
				$_POST['userView'][] = $RC['userMgr'];
			}

			$insert = ''; 
			foreach($RC['userView'] as $row)
			{
				$insert .= "(". $RC['userMgr'] .",". $row ."),";
			}
			$insert = substr_replace($insert,'',-1); 
			createMgrGroup($insert,$RC['userMgr']);
			$answer = 'менеджер создан';
*/
		$firephp->error($Errors->PalomaErrors('outdated'));
	break;

			//////////////////////////////////////////////
			////////  ФОРМА ПРОСМОТРА МЕНЕДЖЕРОВ   ///////
			//////////////////////////////////////////////
	case 'formViewMgr' :
		/*	# формирование списка пользователей для определённого менеджера
			# если пользователь выбрал из списка менеджера
			if(!empty($_POST['idMgr']))
			{
				$idMgr = clearData($_POST['idMgr'],'i');
				$users = selectViewGroupMgrToUsers($idMgr);
				$listUsers = '';
				foreach($users as $row)
				{
					$nameUser = splitFIOtoMassiv($row['fio']);
					$listUsers .= "<p>". $nameUser ."</p>";
				}
				unset($row);

				echo json_encode($listUsers);
				exit();
			}

			# формирование списка менеджеров
			$MgrToGroup = '';
			$allMgrs = selectViewGroupMgr();
			foreach($allMgrs as $row)
			{
				$nameMgr = splitFIOtoMassiv($row['fio']);
				$MgrToGroup .= "<label value='". $row['id'] ."' class='form-control'>". $nameMgr ."</label>";
			}
			unset($row);
			
			$forma = "
				<div class='row row_bot'>
					<div class='col-md-5'><h5>Менеджеры</h5></div>
					<div class='col-md-5'><h5>пользователи для просмотра</h5></div>
				</div> 

				<div class='row row_bot'> 
					<div class='col-md-5'>". $MgrToGroup ."</div>
					<div class='col-md-5'></div> 
				</div>	
			";
			
			$answer = $NewContent->createdForm('modal', array(	'width'=>'600',
																'header'=>'Создание менеджера',
																'content'=>$forma)); */
		$firephp->error($Errors->PalomaErrors('outdated'));
	break;
}