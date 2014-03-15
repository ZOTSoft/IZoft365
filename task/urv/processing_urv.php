<?php

include_once('urv.lib.php');

switch ($connection){	

			/////////////////////////////////////////////////////////
			///////////     КАРКАС | ИСТОРИЯ    /////////////////////
			/////////////////////////////////////////////////////////
	case 'frameURVhistory'  : 
			$toolbar = $NewContent->createdForm('toolbar', array('filtr'));

			$answer = "	
			<div id='report_history' class='righttd-content'>
				". $toolbar ."
				". $NewContent->createdFiltr('urvFiltr', array('state')) ."
				<div id='view_report_history'>
					<table class='table'>
						<thead class='thead'>
							<th>#</th><th>Сотрудник</th><th>Время</th><th>Событие</th>
						</thead>
						<tbody>
						
						</tbody>
					</table>
				</div>
				". $NewContent->createdForm('pagiLine', array('nameCount' => 'Транзакций','countPage'=>0, 'count' => 0 )) ."
			</div>";
			
	break;


			/////////////////////////////////////////////////////////
			///////////       ДАННЫЕ | ИСТОРИИ       ////////////////
			/////////////////////////////////////////////////////////
	case 'filtrURVhistory'  : 
			
			$page = isset($RC['thispage']) ? $RC['thispage'] : 1; 
			$rowcount = isset($RC['selCount']) ? $RC['selCount'] : 50; 
			$offset = ($page - 1) * $rowcount;
			
			$where = filtrReport($RC['dtStart'] , $RC['dtFinish'] , $RC['еmployee'], $RC['state'], $RC['role'], $RC['category'], $RC['location'], $RC['kpp']);

			$res = array();
			$tranz = $MySQL->DB_select(	'ut.dt as dt, ut.state as state, emp.fio as name',
										'd_urv_transactions ut',
										'LEFT JOIN (SELECT id, fio, position, parentid FROM s_employee) emp ON ut.id_employee = emp.id
										LEFT JOIN (SELECT id, name FROM s_employee) p ON p.id = emp.position',
										$where,
										'ut.dt DESC',
										$offset.",".$rowcount) ;
			
			$count = $MySQL->DB_select(	'COUNT(ut.id)',
										'd_urv_transactions ut',
										'LEFT JOIN (SELECT id, fio, position, parentid FROM s_employee) AS emp ON ut.id_employee = emp.id
										LEFT JOIN (SELECT id, name FROM s_employee) p ON p.id = emp.position',
										$where, '', '','str') ;
			
			$tr = ''; $i = 0;
			foreach($tranz AS $row) {
				$i++;
				if($row['state'] == 1) {
					$state = 'приход';
					$class = 'greenMid';
				} else {
					$state = 'уход';
					$class = 'redMid';
				}
				$tr .= "<tr class='". $class ."'><td>". $i ."</td><td>". $row['name'] ."</td><td>". $row['dt'] ."</td><td>". $state ."</td></tr>";
			}
			
			$countPageZ = ceil(($count/$rowcount)/1) * 1;
			
			$answer = array('tr' => $tr, 'countP' => $countPageZ, 'count' => $count);
	break;
	
	
			/////////////////////////////////////////////////////////
			///////////     КАРКАС | ТАБЕЛЬ ПОСЕЩЕНИЯ    ////////////
			/////////////////////////////////////////////////////////	
	case 'urv_visits'  : 
			$answer = "	
			<div id='report_visits' class='righttd-content'>
				". $NewContent->createdForm('toolbar', array('filtr')) ."
				". $NewContent->createdFiltr('urvFiltr', array('cheked')) ."
				<div id='view_report_visits' ></div> 
			</div>";
				
	break;


			/////////////////////////////////////////////////////////
			///////////     ДАННЫЕ | ТАБЕЛЬ ПОСЕЩЕНИЯ    ////////////
			/////////////////////////////////////////////////////////
	case 'URVtabel'  : 
			
			if(empty($_POST['reception'])) {
				$firephp->error($Errors->PalomaErrors(1)); break;
			}
			
			$dtStart	= !empty($RC['dtStart'])	? $RC['dtStart'].' 00:00'	: $RC['dtStart'] = date('d.m.Y').' 00:00';
			$dtFinish	= !empty($RC['dtFinish'])	? $RC['dtFinish'].' 23:59'	: $RC['dtFinish'] = date('d.m.Y').' 23:59';
			$checked	= !empty($RC['checked'])	? $RC['checked']			: $RC['checked'] = array();
			
			$URV = new URV();
			$answer = $URV->createdTable($dtStart, $dtFinish, $RC['еmployee'], $RC['role'], $RC['category'], $RC['kpp'], $RC['location'], $checked, $RC['depart']);
			$typeAnswer = 'html';
	break;


			/////////////////////////////////////////////////////////
			///////////    КАРКАС | УПРАВЛЕНИЯ ГРАФИКОВ    //////////
			/////////////////////////////////////////////////////////
	case 'urv_graphic'  : 	
			$toolbar = $NewContent->createdForm('toolbar', array('create','edit','del'));

			$answer = "
			". $toolbar ."
			<div id='urv_graphic'></div>";
	break;	


			/////////////////////////////////////////////////////////
			///////////    ДАННЫЕ | УПРАВЛЕНИЯ ГРАФИКОВ    //////////
			/////////////////////////////////////////////////////////	
	case 'filtrGraphic'  : 
			$res = array(); $totalrows = 0;
			$views_graphic = $MySQL->DB_select('`id`,`name`,`description`,`for`,UNIX_TIMESTAMP(point) AS point,`type`', 'urv_graphic') ;

			foreach($views_graphic as $row)
			{
				# формирование строк с данными сотрудников
				$a['name'] = $row['name'];
				$a['id'] = $row['id'];
				$a['description'] = $row['description'];
				$a['for'] = $row['for'];
				$a['point'] = date('d.m.Y',$row['point']);
				switch ($row['type']) {
					case '1' : $a['type'] = 'фиксированный'; break;
					case '2' : $a['type'] = 'суточный'; break;
					case '3' : $a['type'] = 'сменный'; break;
					default : $a['type'] = 'ошибка графика';
				}
				
				$res[]=$a;
				$totalrows++;
			}

			$answer = array();
			$answer["totalrows"] = $totalrows;
			$answer["rows"] = $res;
	break;	
	

			/////////////////////////////////////////////////////////
			///   ФОРМА СОЗДАНИЕ ГРАФИКА | УПРАВЛЕНИЯ ГРАФИКОВ   ////
			/////////////////////////////////////////////////////////
	case 'formCreateGraphic'  : 
			$content = $NewContent->createdContent('createGraphic');
			$answer = $NewContent->createdForm('modal', array(	'id'=>'createGraphicModal'	,'width'=>'865','header'=>'Создание графика','content'=>$content,'button'=>'save')); 
	break;	


			///////////////////////////////////////////////////////////////
			///   ФОРМА РЕДАКТИРОВАНИЯ ГРАФИКА | УПРАВЛЕНИЯ ГРАФИКОВ   ////
			///////////////////////////////////////////////////////////////
	case 'formEditGraphic'  : 
			if(empty($RC)) {
				$firephp->error($Errors->PalomaErrors(1)); break;
			}
			
			/*foreach ($RC as $row => $key) {
				switch ($row) {
					case 'point'	:	$point	= date('Y-m-d',strtotime(clearData($key)));	
										$oldPoint = $key;
					break;
					case 'type'		:	switch ($row['type']) {
											case 'фиксированный' : $type = '1'; break;
											case 'суточный'		 : $type = '2'; break;
											case '3' : $a['type'] = 'сменный'; break;
											default : $type = '';
										}
					break;
				}
			}*/
			
			$infoGraf = $MySQL->DB_select('	g.`id`,tt.`timeStart`,tt.`timeEnd`,g.name,g.description,g.`for`,g.point,g.type,tt.`startLunch`,tt.`endLunch`',
											'urv_graphic as g',
											'LEFT JOIN (SELECT * FROM urv_timetable) tt ON tt.id_graphic = g.id',
											"g.id = ".$RC['idTable'] ,
											'tt.id');
			
			//$date = Date_unix(strtotime($infoGraf[0]['point']),4);
			$date = Date_unix($infoGraf[0]['point'],10); 
			
			$table = $NewContent->createdContent('timeTable', $infoGraf);
			$content = $NewContent->createdContent('createGraphic', array('name'=>$infoGraf[0]['name'], 'for'=>$infoGraf[0]['for'], 'description'=>$infoGraf[0]['description'], 'point'=>$date, 'type'=>$infoGraf[0]['type'], 'table'=>$table));
			$answer = $NewContent->createdForm('modal', array('id'=>'editGraphicModal','width'=>'865','header'=>'Изменение графика','content'=>$content, 'button'=>'save')); 
			
	break;	


			/////////////////////////////////////////////////////////
			/////    СОЗДАНИЕ ГРАФИКА | УПРАВЛЕНИЯ ГРАФИКОВ    //////
			/////////////////////////////////////////////////////////
	case 'createGraphic'  : 
			if( !empty($RC['num']) && !empty($RC['st']) && !empty($RC['ft']) ) {
				if( !empty($RC['name']) && !empty($RC['countday']) && !empty($RC['dtStart']) && !empty($RC['typeGraphic']) ) {

					$dtStart	= formatUnixRandomToDATE($RC['dtStart'],2);

					# создание графика
					$MySQL->DB_insert(	'urv_graphic', 
										'`name`,`description`,`for`,`point`,`type`', 
										"('".$RC['name']."','".$RC['description']."',".$RC['countday'].",'".$dtStart."',".$RC['typeGraphic'].")") ;
					# выбор ИД последнего графика
					$lastIDGraf = $MySQL->DB_select('`id`', 'urv_graphic','','','id DESC','1','str') ;

					$insert = '';

					for($i = 0; $i < count($RC['num']); $i++) {
						$insert .= "('".$RC['st'][$i]."','".$RC['ft'][$i]."','".$RC['sl'][$i]."','".$RC['fl'][$i]."',".$lastIDGraf."),";
					}
					$insert = substr_replace($insert,'',-1);

					$MySQL->DB_insert(	'urv_timetable', 
										'`timeStart`,`timeEnd`,`startLunch`,`endLunch`,`id_graphic`', 
										$insert) ;

					$answer = 'График успешно добавлен'; 
				} else {
					$firephp->error($Errors->PalomaErrors('lol3')); break;
				}
			} else {
				$firephp->error($Errors->PalomaErrors('lol4')); break;
			}
	break;	
	
	
			/////////////////////////////////////////////////////////
			///      ИЗМЕНЕНИЯ ГРАФИКА | УПРАВЛЕНИЯ ГРАФИКОВ      ///
			/////////////////////////////////////////////////////////
	case 'editGraphic'  : 
			if( empty($RC['num']) && empty($RC['st']) && empty($RC['ft']) ) {
				$firephp->error($Errors->PalomaErrors('lol4')); break;
			}
			if( empty($RC['name']) && empty($RC['countday']) && empty($RC['dtStart']) && empty($RC['typeGraphic']) ) {
				$firephp->error($Errors->PalomaErrors('lol3')); break;
			}
			
			//$dtStart	= date('Y-m-d',strtotime($RC['dtStart']));
			$dtStart	= Date_unix($RC['dtStart'],9);
			
			# пересоздание графика
			$MySQL->DB_delete('urv_graphic','id = '.$RC['idTable']);
			$MySQL->DB_insert(	'urv_graphic', 
								'id,`name`,`description`,`for`,`point`,`type`', 
								"(".$RC['idTable'].",'".$RC['name']."','".$RC['description']."',".$RC['countday'].",'".$dtStart."',".$RC['typeGraphic'].")") ;
			
			$insert = '';

			for($i = 0; $i < count($RC['num']); $i++) {
				$insert .= "('".$RC['st'][$i]."','".$RC['ft'][$i]."','".$RC['sl'][$i]."','".$RC['fl'][$i]."',".$RC['idTable']."),";
			}
			$insert = substr_replace($insert,'',-1);
			
			$MySQL->DB_delete('urv_timetable','id_graphic = '.$RC['idTable']);
			$MySQL->DB_insert('urv_timetable', '`timeStart`,`timeEnd`,`startLunch`,`endLunch`,`id_graphic`', $insert) ; 
			
			$answer = 'График успешно изменён'; 
	break;
	
	
			/////////////////////////////////////////////////////////
			///         УДАЛЕНИЯ ГРАФИКА | УПРАВЛЕНИЯ ГРАФИКОВ    ///
			/////////////////////////////////////////////////////////
	case 'formDeleteGraphic'  : 
			if(empty($RC)) {
				$firephp->error($Errors->PalomaErrors(1)); break;
			}
			
			$MySQL->DB_delete('urv_graphic','id = '.$RC['idTable']);
			$MySQL->DB_delete('urv_timetable','id_graphic = '.$RC['idTable']);

			$answer = 'del';
	break;	
	

			/////////////////////////////////////////////////////////
			/////         КАРКАС | ОТЧЁТ ПО СОТРУДНИКУ         //////
			/////////////////////////////////////////////////////////
	case 'urv_employee' :
			$answer = "	
			<div id='report_employee'>
				". $NewContent->createdForm('toolbar', array('filtr')) ."
				". $NewContent->createdFiltr('urvFiltr') ."
				<div id='view_report_employee'>в разработке</div>
			</div>";
	break;


			/////////////////////////////////////////////////////////
			/////         КАРКАС | ГРАФИКИ СОТРУДНИКОВ         //////
			/////////////////////////////////////////////////////////
	case 'urv_graphic_employee' :
			/*# вывод информации о графике
			if(isset($_POST['graphic'])) {
				$query = $MySQL->DB_select('*', 'urv_graphic', '', 'id = '. clearData(($_POST['graphic']), 'i').'','','1');
				$answer = $NewContent->createdContent('all_Info_Graphic', $query);

				break;
			}

			# если нет доп.параметров вывожу каркас
			$toolbar = $NewContent->createdForm('toolbar', array('add','edit','del')); 
			$add = $NewContent->createdContent('setting_grafic_employee', array('add'));
			$edit = $NewContent->createdContent('setting_grafic_employee', array('edit'));
			$del = $NewContent->createdContent('setting_grafic_employee', array('del'));

			$answer = $toolbar.$add.$edit.$del; */
	break;
	

			/////////////////////////////////////////////////////////
			/////        ОБРАБОТКА | ГРАФИКИ СОТРУДНИКОВ       //////
			/////////////////////////////////////////////////////////
	case 'urv_graphic_employee_processing' :
			/*if( !empty($_POST['marker']) && !empty($_POST['employee']) && !empty($_POST['graphic']) OR $_POST['marker'] == 'del' ) {

				switch ($_POST['marker']) {
					case 'add' : 
					case 'edit':
						foreach ( $_POST['employee'] AS $row ) {
							$MySQL->DB_update('s_employee','grafic_id = '.clearData($_POST['graphic'], 'i'),'id = '.clearData($row, 'i'));
						}
					break;
					case 'del':
						foreach ( $_POST['employee'] AS $row ) {
							$MySQL->DB_update('s_employee','grafic_id = 0','id = '. clearData($row, 'i'));
						}
					break;
				}
				$answer = 'данные успешно изменены';
			} else {
				$answer = $Errors->PalomaErrors('lol3');
				$firephp->error($Errors->PalomaErrors('lol3'));
			} */
	break;
	
	
			/////////////////////////////////////////////////////////
			/////        ОБРАБОТКА | ГЛАВНАЯ СТРАНИЦА УРВ      //////
			/////////////////////////////////////////////////////////		
	case "do_in_out":
			$answer['err'] = '';
		#||| ПРИХОД БАРКОДА СО СТОРОНЫ КЛИЕНТА |||
			# ТЕРМИНАЛ
			if(isset($_POST['terminal'])) {
				if (!empty($_POST['Barcode'])){ 
					$barcode = $_POST['Barcode'];
					$keyChars = array(	'48' => '0', '49' => '1', '50' => '2', '51' => '3', '52' => '4', 
										'53' => '5', '54' => '6', '55' => '7', '56' => '8', '57' => '9');

					$pass = '';
					for($i = 0; $i < count($barcode); $i++) { 
						foreach ($keyChars AS $key => $value ) {
							if( $barcode[$i] == $key ) {
								$pass .= $value;
							}
						}
					}
				} else {
					$answer = $Errors->PalomaErrors(4);break;
				}
			
			# КОМПЬЮТЕР
			} else {
				if (isset($RC['Barcode'])){
					$pass = $RC['Barcode'];
				}else{
					$answer['err'] .= $Errors->PalomaErrors(4); break;
					$firephp->error($Errors->PalomaErrors(4));
				} 
			}
			
			if (! isset($_SESSION["urvid"])){
				if (isset($RC['Barcode'])){
					$answer = $Errors->PalomaErrors(3);break;
				}else{
					$answer['err'] .= $Errors->PalomaErrors(3); break;
					$firephp->error($Errors->PalomaErrors(3));
				}
			} 
			
			# ПРОВЕРКА НА СЦЕЦ КОМАНДЫ
			switch ($pass) {
				case '2211591' : $answer['spec'] = TRUE; break;
			}
			
			#||| СОЗДАНИЕ ПАРОЛЯ |||
			$sh=md5(FISH.md5($pass));
						
			// выборка и её передача классу на выполнение
			$res = $MySQL->DB_sql_query( selectEmployeeURVPoint($sh,$_SESSION["urvid"]) ,'','not');
			
			if(!empty($res)) {
				if ($row = mysql_fetch_assoc($res)){ 
					$arr = array();
					$arr["rescode"] = 1;
					$id_employee = $row["id"];
					$arr["name"] = $row["name"];
					$arr["state"] = $row["state"];

					if ( $row["state"] == 1 ){
						$state = 0;
						$tranzaction = 'До свиданья!';
					}else{
						$state = 1;
						$tranzaction = 'Здравствуйте!';
					}
					
					# если сотрудник не уходил больше 2х суток то при следующе транзакции делаю приход
					if ( strtotime($row["dt"]) < (strtotime(date('d.m.Y')) - TimeValidURV('valid')) ){
						$state = 1;
						$arr["state"] = 0;
					}
					
					$arr["avatar_path"] = "images/". $id_employee .".jpg";
					$MySQL->DB_insert(	'd_urv_transactions', 
										'state, id_employee,id_location,id_pointurv', 
										"(".$state.", ".$id_employee.", ".$row['thisLocal'].", ".$_SESSION['urvid'].")");
					
					#если терминал
					if(isset($_POST['terminal'])) {
						$answer .= "
							<div><h1>". $tranzaction ."</h1></div>
							<div><h2>". $row["name"] ."</h2></div>";
							//<div><img src='images/".$id_employee."_mini.jpg' /></div>
					} else {
						$answer['arr'] = $arr ;
					}
					
				# если данные из базы не выбраны
				} else {
					if(isset($_POST['terminal'])) {
						$answer .= '<h3>'. $Errors->PalomaErrors(6,$pass) .'</h3>';
					} else {
						$answer['err'] .= $Errors->PalomaErrors(6,$pass);
						$firephp->error($Errors->PalomaErrors(6,$pass));
					}
				}

			# если выборка из базы не произошла
			} else {
				if(isset($_POST['terminal'])) {
					$answer .= '<h3>'. $Errors->PalomaErrors(5) .'</h3>';
				} else {
					$answer['err'] .= $Errors->PalomaErrors(6,$pass);
					$firephp->error($Errors->PalomaErrors(6,$pass));
				}
			}
	break;
	
	
			/////////////////////////////////////////////////////////
			/////        ОБРАБОТКА | ГЛАВНАЯ СТРАНИЦА УРВ      //////
			/////////////////////////////////////////////////////////		
	case "reloadEmp":
	case "fullScreenEmp" :
			if(empty($_SESSION["urvid"])) {
				$firephp->error($Errors->PalomaErrors(7)); break;
			}
			
			# выборка точки УРВ и локации (КПП и месторасположения) 
			$thisKPP = $MySQL->DB_select(	'pu.`name` AS kpp, l.`name` AS location', 
											's_pointurv pu',
											'LEFT JOIN (SELECT id,name FROM s_location) AS l ON l.id = pu.id_location',
											"pu.id = ".$_SESSION["urvid"],
											'','1');
			
			// Пользователи для списка терминала
			if(!empty($RC['limit'])) {
				$sql = selectEmployeeWorkThisURVpoint($_SESSION["urvid"], $RC['limit']);
				$empWork = $MySQL->DB_sql_query($sql);
				$content = $NewContent->createdContent('listEmployee',$empWork);
			
			// Пользователи для общего списка присутствующих
			} else {
				$allLocation = $MySQL->DB_select('name AS location, id','s_location');
				$allLoc = ''; 
				foreach ($allLocation AS $row) {
					$sql = selectEmployeeWorkAll($row['id']);
					$empWork = $MySQL->DB_sql_query($sql);
					$allLoc .= "<div class='row_bot row'><h2>". $row['location'] ."</h2></div>". $NewContent->createdContent('listEmployee',$empWork);
				}
				
				$content = $NewContent->createdForm('modal', array(	'id'	 =>'allEmpUrv',
																	'width'	 =>'865',
																	'header' =>'Сейчас на работе',
																	'content'=> $allLoc ));
			}
						
			$answer = array('info' => $thisKPP, 'cont' => $content);
	break;
	
	
			/////////////////////////////////////////////////////////
			/////        КАРКАС | ВЫГРУЗКА ТРАНЗАКЦИЙ          //////
			/////////////////////////////////////////////////////////		
	case "frameUrvTranzaction" :
			$answer = "	
				". $NewContent->createdForm('toolbar', array('filtr')) ."
				<div class='Sunwel_filtr'>
					<div class='well well-sm'>
						<form target='_blank' role='form' method='post' action='/task/urv/processing_urv.php?sl=fileUrvTranzaction'>
							
							<div class='zfilter'>
								<label>Период</label>
								". $NewContent->createdForm('dtime',array('name'=>'dtStart', 'ph'=>'от','type'=>'dd.MM.yyyy')) ."
								". $NewContent->createdForm('dtime',array('name'=>'dtFinish', 'ph'=>'до','type'=>'dd.MM.yyyy')) ."
							</div>

							<div class='clear'></div>

							<div class='zfilterok'>
								<input class='btn btn-primary' type='submit' name='sendFiltr' value='Выгрузить' />
							</div>
						</form>
						<div class='clear'></div>
					</div>
				</div>
				";
	break;		
	
	
			/////////////////////////////////////////////////////////
			/////        ФОРМИРОВАНИЕ | ВЫГРУЗКА ТРАНЗАКЦИЙ    //////
			/////////////////////////////////////////////////////////		
	case "fileUrvTranzaction" :	
			$dtStart	= clearData($_POST['dtStart']);
			$dtFinish	= clearData($_POST['dtFinish']);
			
			$filtr = filtrFileURVTranzaction($dtStart,$dtFinish);
			
			$selectUT = $MySQL->DB_select(	'emp.idout,ut.dt,ut.state,ut.id_location',
											's_employee as emp',
											'LEFT JOIN d_urv_transactions as ut on ut.id_employee=emp.id',
											$filtr,
											'ut.dt');
		
			header("Content-Type: application/download\n"); 
            $title='Транзакции_'.$dtStart."-".$dtFinish."_Создан-".date('d.m.Y_H:i');
            header("Content-Disposition: attachment; filename=".$title.'.txt');
			foreach($selectUT AS $row) {
				echo join(';',$row).";\r\n";
			}
			exit();
	break;	
	
			/////////////////////////////////////////////////////////
			/////        КАРКАС | СОТРУДНИКИ НА РАБОТЕ	       //////
			/////////////////////////////////////////////////////////		
	case "urv_empWorks" :
			$toolbar = $NewContent->createdForm('toolbar', array('filtr'));

			$answer = "	
			<div class='righttd-content'>
				". $toolbar ."
				". $NewContent->createdFiltr('urvFiltr', array('state')) ."
				<div>
					<table class='table'>
						<thead class='thead'>
							<th>#</th><th>Сотрудник</th><th>Время</th><th>Событие</th>
						</thead>
						<tbody>
						
						</tbody>
					</table>
				</div>
				". $NewContent->createdForm('pagiLine', array('nameCount' => 'Транзакций','countPage'=>0, 'count' => 0 )) ."
			</div>";
	break;	


			/////////////////////////////////////////////////////////
			///////       ДАННЫЕ | СОТРУДНИКИ НА РАБОТЕ       ///////
			/////////////////////////////////////////////////////////
	case 'filtrEmpWorks'  : 
			
			$page = isset($RC['thispage']) ? $RC['thispage'] : 1; 
			$rowcount = isset($RC['selCount']) ? $RC['selCount'] : 50; 
			$offset = ($page - 1) * $rowcount;
			
			$where = filtrReport($RC['dtStart'] , $RC['dtFinish'] , $RC['еmployee'], $RC['state'], $RC['role'], $RC['category'], $RC['location'], $RC['kpp']);

			$res = array();
			$tranz = $MySQL->DB_sql_query( selectEmpWork( $where, $offset , $rowcount )	) ;
			
			$count = $MySQL->DB_sql_query( countEmpWork( $where ),'','str' ) ;
						
			$tr = ''; $i = 0;
			foreach($tranz AS $row) {
				
				$i++;
				
				# если последняя транзакция сотрудника меньше текущего времени ( с минус интервалом ), то сотрудник неактивен
				if( strtotime($row['dt']) < (date('U') - TimeValidURV('valid')) ) {
					$state = 'неактивен';
					$class = 'greyMid';
				} else {
					if($row['state'] == 1) {
						$state = 'приход';
						$class = 'greenMid';
					} else {
						$state = 'уход';
						$class = 'redMid';
					}
				}
				$tr .= "<tr class='". $class ."'><td>". $i ."</td><td>". $row['name'] ."</td><td>". $row['dt'] ."</td><td>". $state ."</td></tr>";
			}
			
			$countPageZ = ceil(($count/$rowcount)/1) * 1;
			
			$answer = array('tr' => $tr, 'countP' => $countPageZ, 'count' => $count);
	break;
}