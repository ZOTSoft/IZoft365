<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of URV
 *
 * @author SunwelLight
 */
class URV extends MySQL {

	public $daycount;				// на сколько дней состовляеться график
	public $tranzaction;			// приходы и уходы сотрудников
	public $employeeInfo;			// информация о пользователях
	public $dtStartStr;				// начальная дата графика
	public $dtFinishStr;			// дата завершения графика

	protected $DATA_DAY;			// массив с информацией за день

	protected $CHANGE;				// массив с информацией за смену
	protected $LUNCH = array();		// массив обеда за смену
	protected $PROCESSING = array();// массив переработки за смену

	protected $DATA_USER;			// массив с информацией за всё время
	protected $interval = 0;		// текущий день в графике
	protected $thisday;				// текущий день(дата)
	protected $ofDay = FALSE;		// текущий день выходной, да | нет
	public $viewParam = array();    // массив содержащий данные о том какие поля выводить


	//put your code here

	// фильтр для транзакций
	private function filtrReportGraf($dtStart, $dtFinish, $employee, $role, $category, $kpp, $location, $depart) {
		$filtr = '';

		if(!empty($employee)){
			$filtr = issetFiltrAND($filtr);
			$filtr .= " UPPER(emp.fio) IN (SELECT UPPER(fio) FROM s_employee WHERE UPPER(fio) LIKE  UPPER('%".$employee."%') )";
		}

		//фильтр локации (место положения точки урв)
		if(!empty($kpp)){
			$filtr = issetFiltrAND($filtr);
			$filtr .= " ut.id_pointurv = $kpp ";
		}

		if(!empty($location)){
			$filtr = issetFiltrAND($filtr);
			$filtr .= " ut.id_location = $location ";
		}
		
		//фильтр дата старта
		if((!empty($dtStart)) or (!empty($dtFinish))){
			$filtr = issetFiltrAND($filtr);

			if(!empty($dtStart)){
				$dtStart_filtr = formatDateRandomToTIMESTAMP($dtStart, 1);
			}else{
				$dtStart_filtr = date("Y-m-d H-i-s");
			}

			if(!empty($dtFinish)){
				$dtFinish_filtr = formatDateRandomToTIMESTAMP($dtFinish, 1);
			}else{
				$dtFinish_filtr = date("Y-m-d H-i-s");
			}

			$filtr .= " ut.dt BETWEEN STR_TO_DATE('". $dtStart_filtr ."', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('". $dtFinish_filtr ."', '%Y-%m-%d %H:%i:%s') ";
		}

		return $filtr;
	}

	// фильтр для основного запроса
	private function filtrReportEmployee($dtStart, $dtFinish, $employee, $role, $category, $kpp, $location, $depart) {
		$filtr = 'emp.isgroup = 0';

		if(!empty($category)) {
			$filtr = issetFiltrAND($filtr);
			$filtr .= " emp.parentid = ". $category ." ";
		}

		if(!empty($role)) {
			$filtr = issetFiltrAND($filtr);
			$filtr .= " UPPER(p.name) LIKE UPPER('%". $role ."%') ";
		}

		if(!empty($depart)){
			$filtr = issetFiltrAND($filtr);
			$filtr .= " emp.id_depart = $depart ";
		}
		
		//фильтр приоритета
		if(!empty($employee)){
			//если переменная не пуста то присваиваем ей значение and через функцию
			$filtr = issetFiltrAND($filtr);
			$filtr .= " UPPER(emp.fio) LIKE UPPER('%".$employee."%') ";
		}

		//фильтр локации (место положения точки урв)
		if(!empty($location)){
			$filtr = issetFiltrAND($filtr);
			$filtr .= " l.id = $location ";
		}

		//фильтр локации (место положения точки урв)
		if(!empty($kpp)){
			$filtr = issetFiltrAND($filtr);
			$filtr .= " pu.id = $kpp ";
		}

		return $filtr;
	}

	// обработка данных БД
	private function db2DBArray($data)	{
		$arr = array();
		$id_user = 0; $i = 0;

		if(!empty($data)) {
			while($row = mysql_fetch_assoc($data)){

				if($row['id'] != $id_user) {
					$i++;

					$arr[] = Array	(
									'id'		=> $row['id'],
									'fio'		=> $row['fio'],
									'grafic'	=> $row['grafic'],
									'point'		=> $row['point'],
									'for'		=> $row['for'],
									'name'		=> $row['name'],
									'type'		=> $row['type'],
									'position'	=> $row['position'],
									'depart'	=> $row['depart'],
									'location'	=> $row['location'],
									'id_location'=> $row['id_location'],
									'timeStart'	=> Array	(
															$row['timeStart']
															),
									'timeEnd'	=> Array	(
															$row['timeEnd']
															),
									'startLunch'	=> Array	(
															$row['startLunch']
															),
									'endLunch'	=> Array	(
															$row['endLunch']
															),
									);
				} else {
					$arr[$i-1]['timeStart'][]	= $row['timeStart'];
					$arr[$i-1]['timeEnd'][]		= $row['timeEnd'];
					$arr[$i-1]['startLunch'][]	= $row['startLunch'];
					$arr[$i-1]['endLunch'][]	= $row['endLunch'];
				}

				$id_user = $row['id'];
			}
			return $arr;
		} else {
			die('Нет данных для формирования табеля');
		}
	}

	// подсчёт интервала
	private function countInterval( $rowEmp ) {

		if($rowEmp['point'] != 0) {
			$this->interval = $this->thisday - $rowEmp['point'];
			$this->interval = (int)($this->interval/86400);
			$this->interval = (int)($this->interval%$rowEmp['for']);

			if($this->interval < 0) {
				$this->interval = 0;
			}
		} else {
			$this->interval = 0;
		}
		return $this->interval;
	}

	// обрезание троки после 3го символа
	private function removeSTRto3char( $str ) {
		$result = '('.substr( $str, 0 , 8 ).')';
		return $result;
	}

	// если ячейка не пуста добавляю разделительную черту
	private function addHr($parem = 'default') {
		switch ($parem) {
			case 'default' :
							if(!empty ( $this->DATA_DAY['tdDay'] ) && $this->DATA_DAY['tdDay'] != '<span></span>') {
								$this->DATA_DAY['tdDay'] .= '<hr />';
							}
			break;

			case 'forcibly' :
								$this->DATA_DAY['tdDay'] .= '<hr />';
			break;
		}
	}

	// массив с параметрами вывода
	private function viewParam($setting) {
		$arr = array();
		foreach ($setting AS $row) {
			$arr[$row] = '1';
		}
		return $arr;
	}
	
	// текущий день недели
	private function NumDay($num) {
		switch ($num) {
			case '0' : $result = 'Воскресенье';		break;
			case '1' : $result = 'Понедельник';		break;
			case '2' : $result = 'Вторник';			break;
			case '3' : $result = 'Среда';			break;
			case '4' : $result = 'Четверг';			break;
			case '5' : $result = 'Пятница';			break;
			case '6' : $result = 'Суббота';			break;
		}
		return $result;
	}

	public function __construct(){}




	#||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
	#                 ГРАФИКИ

	#|||||     ВЫВОД ПРИХОДА И УХОДА ЕСЛИ НЕТ ГРАФИКА
	public function userNoGrafic( $rowTranz) {

		// создание массива смены
		if(Date_unix($this->thisday,4) != $this->CHANGE['point']) {

			$day = Date_unix($this->thisday,4);

			$this->CHANGE[$day ] = array();
			$this->CHANGE[$day]['Date'] = $day;

			$this->DATA_DAY['countDay']++;
		}

		//  1 ПРИХОД
		if($rowTranz['state'] == 1) {
			$this->DATA_DAY['countAll']++;
			$this->DATA_DAY['tdDay'] .= '<p>'.date('H:i:s',$rowTranz['unixtime']).' - Приход</p>';
			$this->DATA_DAY['arrComing'][$this->DATA_DAY['countAll']]  = $rowTranz['unixtime'];

		// 0 УХОД
		} else {
			$this->DATA_DAY['tdDay'] .= '<p>'.date('H:i:s',$rowTranz['unixtime']).' - Уход</p>';
			$this->DATA_DAY['arrAttendance'][$this->DATA_DAY['countAll']] = $rowTranz['unixtime'];
		}

		return $this->DATA_DAY;
	}


	#|||||     ВЫВОД ПРИХОДА И УХОДА ЕСЛИ ГРАФИК НЕ СУТОЧНЫЙ
	public function userGraficDay( $rowTranz, $rowEmp ) {

		// создание массива смены
		if(Date_unix($this->thisday,4) != $this->CHANGE['point']) {

			$day = Date_unix($this->thisday,4);

			if(isset($rowEmp['startLunch'])) {
				$this->CHANGE['lunch']		= '1';
			} else {
				$this->CHANGE['lunch']		= '0';
			}

			$this->CHANGE[$day ]				= array();
			$this->CHANGE['point']				= Date_unix($this->thisday,4);

			$this->CHANGE[$day]['timeStart']	= $rowEmp['timeStart'][$this->interval];
			$this->CHANGE[$day]['timeEnd']		= $rowEmp['timeEnd'][$this->interval];
			$this->CHANGE[$day]['grafSL']		= $rowEmp['startLunch'][$this->interval];//$rowEmp['startLunch'];
			$this->CHANGE[$day]['grafEL']		= $rowEmp['endLunch'][$this->interval];
			$this->CHANGE[$day]['typeDay']		= self::ofDay( 'day' , $rowEmp );
			$this->CHANGE[$day]['Date']			= $day;

			$this->DATA_DAY['countDay']++;
		}

		// 1 ПРИХОД
		if($rowTranz['state'] == 1) {
			$this->DATA_DAY['tdDay'] .= '<p>'.date('H:i:s',$rowTranz['unixtime']).' - Приход '.self::removeSTRto3char($rowTranz['nameLoc']).'</p>';

			# если область прихода совпадает с областью заданной пользователю
			if($rowTranz['id_location'] == $rowEmp['id_location'] ){

				$this->DATA_DAY['countAll']++;
				$this->CHANGE[Date_unix($this->thisday,4)]['Coming'][$this->DATA_DAY['countAll']] = $rowTranz['unixtime'];
			}
		// 0 УХОД
		} else {
			$this->DATA_DAY['tdDay'] .= '<p>'.date('H:i:s',$rowTranz['unixtime']).' - Уход '.self::removeSTRto3char($rowTranz['nameLoc']).'</p>';

			# если область прихода совпадает с областью заданной пользователю
			if($rowTranz['id_location'] == $rowEmp['id_location'] ){
				$this->CHANGE[Date_unix($this->thisday,4)]['Attendance'][$this->DATA_DAY['countAll']] = $rowTranz['unixtime'];
			}
		}

	}


	#|||||     ВЫВОД ПРИХОДА И УХОДА ЕСЛИ ГРАФИК СУТОЧНЫЙ
	public function userGraficAllDayNight( $rowTranz, $rowEmp ) {

		// создание массива смены
		if(Date_unix($this->thisday,4) != $this->CHANGE['point']) {

			$day = Date_unix($this->thisday,4);

			if(!isset($this->CHANGE['grafStart'])) {
				$this->CHANGE['grafStart']	= $rowEmp['timeStart'];
				$this->CHANGE['grafEnd']	= $rowEmp['timeEnd'];
				if(!empty($rowEmp['startLunch']) && $rowEmp['startLunch'] != '00:00:00') {
					$this->CHANGE['grafSL']		= $rowEmp['startLunch'];
					$this->CHANGE['grafEL']		= $rowEmp['endLunch'];
					$this->CHANGE['lunch']		= '1';
				} else {
					$this->CHANGE['lunch']		= '0';
				}
				$this->CHANGE['grafPoint']	= $day;
			}
			$this->CHANGE['point'] = Date_unix($this->thisday,4);

			$this->CHANGE[$day] = array();

			if(isset($this->CHANGE['lunch']) && $this->CHANGE['lunch'] == '1') {
				$this->CHANGE[$day]['timeSL']		= $rowEmp['startLunch'][$this->interval];
				$this->CHANGE[$day]['timeEL']		= $rowEmp['endLunch'][$this->interval];
			}

			$this->CHANGE[$day]['timeStart'] = $rowEmp['timeStart'][$this->interval];
			$this->CHANGE[$day]['timeEnd'] = $rowEmp['timeEnd'][$this->interval];
			$this->CHANGE[$day]['typeDay'] = self::ofDay( 'day' , $rowEmp );
			$this->CHANGE[$day]['Date'] = $day;

			$this->DATA_DAY['countDay']++;
			$this->DATA_USER['totalFactD']++;
		}

		// 1 ПРИХОД
		if($rowTranz['state'] == 1) {
			$this->DATA_DAY['tdDay'] .= '<p>'.date('H:i:s',$rowTranz['unixtime']).' - Приход '.self::removeSTRto3char($rowTranz['nameLoc']).'</p>';

			# если область прихода совпадает с областью заданной пользователю
			if($rowTranz['id_location'] == $rowEmp['id_location'] ){

				$this->DATA_DAY['countAll']++;
				$this->CHANGE[Date_unix($this->thisday,4)]['Coming'][$this->DATA_DAY['countAll']] = $rowTranz['unixtime'];

			}
		// 0 УХОД
		} else {
			$this->DATA_DAY['tdDay'] .= '<p>'.date('H:i:s',$rowTranz['unixtime']).' - Уход '.self::removeSTRto3char($rowTranz['nameLoc']).'</p>';

			# если область прихода совпадает с областью заданной пользователю
			if($rowTranz['id_location'] == $rowEmp['id_location'] ){
				$this->CHANGE[Date_unix($this->thisday,4)]['Attendance'][$this->DATA_DAY['countAll']] = $rowTranz['unixtime'];
			}
		}

	}

	
	#|||||     ВЫВОД ПРИХОДА И УХОДА ЕСЛИ ГРАФИК СМЕННЫЙ
	public function userGraficAllChange( $rowTranz, $rowEmp ) {
		
		$day = Date_unix($this->thisday,4);
		//echo $day.' == '.$this->CHANGE['point'].'<br />';
		// создание массива смены
		if($day != $this->CHANGE['point']) {

			if(!isset($this->CHANGE['grafStart'])) {
				$this->CHANGE['grafStart']	= $rowEmp['timeStart'];
				$this->CHANGE['grafEnd']	= $rowEmp['timeEnd'];
				if(!empty($rowEmp['startLunch']) && $rowEmp['startLunch'] != '00:00:00') {
					$this->CHANGE['grafSL']		= $rowEmp['startLunch'];
					$this->CHANGE['grafEL']		= $rowEmp['endLunch'];
					$this->CHANGE['lunch']		= '1';
				} else {
					$this->CHANGE['lunch']		= '0';
				}
				$this->CHANGE['grafPoint']	= $day;
			}
			$this->CHANGE['point'] = $day;

			$this->CHANGE[$day] = array();

			if(isset($this->CHANGE['lunch']) && $this->CHANGE['lunch'] == '1') {
				$this->CHANGE[$day]['timeSL']	= $rowEmp['startLunch'][$this->interval];
				$this->CHANGE[$day]['timeEL']	= $rowEmp['endLunch'][$this->interval];
			}

			$this->CHANGE[$day]['timeStart']	= $rowEmp['timeStart'][$this->interval];
			$this->CHANGE[$day]['timeEnd']		= $rowEmp['timeEnd'][$this->interval];
			$this->CHANGE[$day]['typeDay']		= self::ofDay( 'day' , $rowEmp );
			$this->CHANGE[$day]['Date']			= $day;

			$this->DATA_DAY['countDay']++;
			$this->DATA_USER['totalFactD']++;
			
			$this->DATA_DAY['arrTdDay'][$day] = '';
		}
		

		// 1 ПРИХОД  
		if($rowTranz['state'] == 1) {
			$this->DATA_DAY['arrTdDay'][$day][$rowTranz['unixtime']] = '<p>'.$day .' '. date('H:i:s',$rowTranz['unixtime']).' - Приход '.self::removeSTRto3char($rowTranz['nameLoc']).'</p>';
			
			
			# если область прихода совпадает с областью заданной пользователю
			if($rowTranz['id_location'] == $rowEmp['id_location'] ){
				$this->DATA_DAY['countAll']++;
				$this->CHANGE[$day]['Coming'][$this->DATA_DAY['countAll']] = $rowTranz['unixtime'];
			}
		// 0 УХОД
		} else {
			$this->DATA_DAY['arrTdDay'][$day][$rowTranz['unixtime']] = '<p>'.$day .' '. date('H:i:s',$rowTranz['unixtime']).' - Уход '.self::removeSTRto3char($rowTranz['nameLoc']).'</p>';

			# если область прихода совпадает с областью заданной пользователю
			if($rowTranz['id_location'] == $rowEmp['id_location'] ){
				$this->CHANGE[$day]['Attendance'][$this->DATA_DAY['countAll']] = $rowTranz['unixtime'];
			}
		}
	}

	
	
	#||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
	#                 ПЕРЕРАСЧЁТ ГРАФИКОВ
	
	# НЕТ ГРАФИКА
	public function Type0graf() {
		
		self::addHr();
		$this->DATA_DAY['countDay'] = 0;

		if(!empty($this->DATA_DAY['arrComing']) && !empty($this->DATA_DAY['arrAttendance'])) {

			foreach($this->DATA_DAY['arrComing'] as $rowC => $keyC) {

				if(isset($this->DATA_DAY['arrAttendance'][$rowC])) {
					$this->CHANGE['totalFact'] += $this->DATA_DAY['arrAttendance'][$rowC] - $keyC;
				}
			}
		}

		if(!empty($this->DATA_DAY['tdDay'])) {
			$this->DATA_USER['totalFact'] += $this->CHANGE['totalFact'];
			$this->DATA_DAY['tdDay']  .='<p>'.Date_unix($this->CHANGE['totalFact'],7).' - Итог по факту</p> ';
		}
		
	}
		
	# ГРАФИК ДНЕВНОЙ
	public function Type1graf( $rowEmp ){
		
		self::addHr();
		$this->DATA_DAY['countDay'] = 0;
		$c = 0;
		foreach($this->CHANGE AS $date => $arr) {

			if(isset($arr['Coming']) or isset($arr['Attendance'])) {

				# текущие приходы и уходы
				if(!empty($arr['Coming']) && is_array($arr['Coming'])) {
					foreach($arr['Coming'] as $rowC => $keyC) {

						if(isset($arr['Attendance'][$rowC])) {

							# итоги по факту
							if(isset($this->viewParam['totalFact'])) {
								$this->CHANGE['totalFact'] += $arr['Attendance'][$rowC] - $keyC;
							}

							# обед
							if(isset($this->viewParam['lunch'])) {
								self::lunch($rowEmp['type'], $arr , $rowC );
							}

							# опоздание
							if(isset($this->viewParam['delayH'])) {
								$c = self::delayH ( $rowEmp['type'] , $c , $keyC , $arr , $rowC );
							}

							# переработка до
							if(isset($this->viewParam['processingDo'])) {
								self::processingDo($rowEmp['type'], $arr, $rowC, $keyC);
							}
							# переработка после
							if(isset($this->viewParam['processingAfter'])) {
								self::processingAfter($rowEmp['type'], $arr, $rowC, $keyC);
							}

							# итог по графику
							if(isset($this->viewParam['totalGraf'])) {
								self::totalGraf ( $rowEmp['type'] , $arr, $rowC );
							}

							# преждевременный уход
							if($arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeStart'])) && $arr['Attendance'][$rowC] < (strtotime($arr['Date'].$arr['timeEnd']))) {
								$this->DATA_DAY['premature'] = (strtotime($arr['Date'].$arr['timeEnd'])) - $arr['Attendance'][$rowC];
							} else {
								if($arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeStart'])) && $arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeEnd']))) {
									$this->DATA_DAY['prematureNo'] = 1;
								}
							}
						}
					}
				}
			}
		}

		# преждевременный уход
		if( in_array( 'premature', $this->viewParam ) ) {
			if(!empty($this->DATA_DAY['premature'])) {
				if(empty($this->DATA_DAY['prematureNo'])) {
					$this->DATA_DAY['tdDay']  .='<p>'.Date_unix($this->DATA_DAY['premature'],7).' - Преждевременный уход</p> ';
				}
			}
		}

		# обед
		if(isset($this->viewParam['lunch'])) {
			self::lunch($rowEmp['type'].'Count');
		}

		# переработка до
		if(isset($this->viewParam['processingDo'])) {
			self::processingDo($rowEmp['type'].'processingDo');
		}
		# переработка после
		if(isset($this->viewParam['processingAfter'])) {
			self::processingAfter($rowEmp['type'].'processingAfter');
		}

		# итоги по факту
		if(isset($this->viewParam['totalFact'])) {
			if(!empty($this->CHANGE['totalFact'])) {
				$this->DATA_USER['totalFact'] += $this->CHANGE['totalFact'];
				$this->DATA_DAY['tdDay']  .='<p>'.Date_unix($this->CHANGE['totalFact'],7).' - Итог по факту</p> ';
			}
		}

		# переработка по факту или недоработка по факту
		if(isset($this->viewParam['processingFact']) or isset($this->viewParam['flawFact'])) {
			self::flaw($rowEmp['type'], $rowEmp);
		}

		# итог по графику
		if(isset($this->viewParam['totalGraf'])) {
			self::totalGraf ( $rowEmp['type'].'totalGraf', $arr);
		}

		//$this->DATA_DAY = self::flaw( '1' , $rowEmp );  
		//list($this->DATA_USER, $this->DATA_DAY) = self::formationOfAnAdditionalInformation( '1' ); 
	}
	
	# ГРАФИК СУТОЧНЫЙ
	public function Type2graf( $rowEmp ){
		
		if(($rowEmp['for'] - 1) == $this->interval) {
			self::addHr('forcibly');

			$testComing = array();
			$c = 0; // счётчик опоздания
			//print_r($this->CHANGE); 

			foreach($this->CHANGE AS $date => $arr) {

				if(isset($arr['Coming']) or isset($arr['Attendance'])) {

					# приходы прошлого дня сравниваю с приходами текущего
					if(isset($testComing) && count($testComing) > 0) {

						if(!empty($arr['Attendance'])){
							if(is_array($arr['Attendance'])) {
								foreach($arr['Attendance'] as $rowA => $keyA) {
									if(isset($testComing[$rowA])) {

										# итоги по факту
										if(isset($this->viewParam['totalFact'])) {
											$this->CHANGE['totalFact'] += $keyA - $testComing[$rowA];
										} 

										# обед
										if(isset($this->viewParam['lunch'])) {
											self::lunch($rowEmp['type'].'tc', $arr , $rowA, $testComing );
										}

										# итог по графику
										if(isset($this->viewParam['totalGraf'])) {
											self::totalGraf ( $rowEmp['type'].'tc' , $arr, $rowA, $testComing );
										}
									}
								}
							}
						}
						unset($testComing);
					}

					# текущие приходы и уходы
					if(!empty($arr['Coming']) && is_array($arr['Coming'])) {
						foreach($arr['Coming'] as $rowC => $keyC) {

							if(isset($arr['Attendance'][$rowC])) {

								# итоги по факту
								if(isset($this->viewParam['totalFact'])) {
									$this->CHANGE['totalFact'] += $arr['Attendance'][$rowC] - $keyC;
								}

								# итог по графику
								if(isset($this->viewParam['totalGraf'])) {
									self::totalGraf ( $rowEmp['type'] , $arr, $rowC );
								}

								# опоздание
								if(isset($this->viewParam['delayH'])) {
									$c = self::delayH ( $rowEmp['type'] , $c , $keyC , $arr , $rowC );
								}

								# обед
								if(isset($this->viewParam['lunch'])) {
									self::lunch($rowEmp['type'], $arr , $rowC );
								}

								# переработка до
								if(isset($this->viewParam['processingDo'])) {
									self::processingDo($rowEmp['type'], $arr, $rowC, $keyC);
								}
								# переработка после
								if(isset($this->viewParam['processingAfter'])) {
									self::processingAfter($rowEmp['type'], $arr, $rowC, $keyC);
								}

							} else {
								$testComing[$rowC] = $arr['Coming'][$rowC];
							}
						}
					}
				}
			}

			# переработка до
			if(isset($this->viewParam['processingDo'])) {
				self::processingDo($rowEmp['type'].'processingDo');
			}
			# переработка после
			if(isset($this->viewParam['processingAfter'])) {
				self::processingAfter($rowEmp['type'].'processingAfter');
			}

			# переработка по факту или недоработка
			if(isset($this->viewParam['processingFact']) or isset($this->viewParam['flawFact'])) {
				self::flaw($rowEmp['type'], $rowEmp);
			}

			# обед
			if(isset($this->viewParam['lunch'])) {
				self::lunch($rowEmp['type'].'Count');
			}

			# итоги по факту
			if(isset($this->viewParam['totalFact'])) {
				$this->DATA_USER['totalFact'] += $this->CHANGE['totalFact'];
				$this->DATA_DAY['tdDay']  .='<p>'.Date_unix($this->CHANGE['totalFact'],7).' - Итог по факту</p> ';
			}

			# итог по графику
			if(isset($this->viewParam['totalGraf'])) {
				self::totalGraf ( $rowEmp['type'].'totalGraf', $arr);
			} 

			$this->DATA_DAY['countDay'] = 0;
		}
	}
	
	# ГРАФИК СМЕННЫЙ
	public function Type3graf( $param, $rowEmp=array(), $header='', $arr=array(), $thisdate=0, $olddate=0, $com=0, $kcom=0, $c = 0) {
		
		switch ($param) {
			case '2' :
					if($header == $olddate) {
						if(!empty($arr['Coming']) && is_array($arr['Coming'])) {

							foreach($arr['Coming'] as $rowC => $keyC) {

								if(isset($arr['Attendance'][$rowC])) {

									# итог по факту
									if(isset($this->viewParam['totalFact'])) {
										$this->CHANGE['totalFact'] += $arr['Attendance'][$rowC] - $keyC;
									}
								} else {
									$com = $keyC;
									$kcom = $rowC;
								}
							}
						}
						//return array($com,$kcom);
					}
			case '1' :
					if($header == $thisdate) {

						if($com != 0) {
							if(isset($arr['Attendance'][$kcom])) {
								# итог по факту
								if(isset($this->viewParam['totalFact'])) {
									$this->CHANGE['totalFact'] += $arr['Attendance'][$kcom] - $com;
								}
							}
						}

						if(!empty($arr['Coming']) && is_array($arr['Coming'])) {

							foreach($arr['Coming'] as $rowC => $keyC) {

								if(isset($arr['Attendance'][$rowC])) {
									
									# итог по факту
									if(isset($this->viewParam['totalFact'])) {
										$this->CHANGE['totalFact'] += $arr['Attendance'][$rowC] - $keyC;
									}

									# итог по графику
									if(isset($this->viewParam['totalGraf'])) {
										self::totalGraf ( $rowEmp['type'] , $arr, $rowC );
									}

									# опоздание
									if(isset($this->viewParam['delayH'])) {
										$c = self::delayH ( $rowEmp['type'] , $c , $keyC , $arr , $rowC );
									}

									# обед
									if(isset($this->viewParam['lunch'])) {
										self::lunch($rowEmp['type'], $arr , $rowC );
									}

									# переработка до
									if(isset($this->viewParam['processingDo'])) {
										self::processingDo($rowEmp['type'], $arr, $rowC, $keyC);
									}
									# переработка после
									if(isset($this->viewParam['processingAfter'])) {
										self::processingAfter($rowEmp['type'], $arr, $rowC, $keyC);
									}
								}
							}
						}
						
						# переработка до
						if(isset($this->viewParam['processingDo'])) {
							self::processingDo($rowEmp['type'].'processingDo');
						}
						# переработка после
						if(isset($this->viewParam['processingAfter'])) {
							self::processingAfter($rowEmp['type'].'processingAfter');
						}

						# переработка по факту или недоработка
						if(isset($this->viewParam['processingFact']) or isset($this->viewParam['flawFact'])) {
							self::flaw($rowEmp['type'], $rowEmp);
						}

						# обед
						if(isset($this->viewParam['lunch'])) {
							self::lunch($rowEmp['type'].'Count');
						}

						# итоги по факту
						if(isset($this->viewParam['totalFact'])) {
							$this->DATA_USER['totalFact'] += $this->CHANGE['totalFact'];
							$this->DATA_DAY['tdDay']  .='<p>'.Date_unix($this->CHANGE['totalFact'],7).' - Итог по факту</p> ';
						}

						# итог по графику
						if(isset($this->viewParam['totalGraf'])) {
							self::totalGraf ( $rowEmp['type'].'totalGraf', $arr);
						} 
			
						$com = 0;//print_r($arr);
						return array($com,$kcom);
					}
			break;
		}
	}
	
	# ПОСТРОЕНИЕ СМЕНЫ ДЛЯ СМЕННОГО ГРАФКА
	public function Type3grafVisual( $rowEmp ) {
		$thisdate = Date_unix($this->thisday,4);
		$olddate = Date_unix($this->thisday - 86400,4);
		$nextdate = Date_unix($this->thisday + 86400,4);
		$unuxDate = 0;
		$c = 0;

		$this->DATA_USER['tr'][$thisdate] = "<td></td>";
		//print_r($this->DATA_DAY['arrTdDay']);		
		$com = 0;
		$kcom = 0;

		//print_r($this->CHANGE);
		//print_r($this->DATA_DAY['arrTdDay']);

		# если первый приход больше ухода
		if( !empty($this->CHANGE[$thisdate]['Coming']) && !empty($this->CHANGE[$thisdate]['Attendance'])  && 
			(array_slice($this->CHANGE[$thisdate]['Coming'],0,1) < array_slice($this->CHANGE[$thisdate]['Attendance'],0,1) )) {

			foreach($this->CHANGE AS $header => $arr) {
				self::Type3graf( '1', $rowEmp, $header, $arr, $thisdate, $olddate, $com, $kcom, $c);
			}

			$str = '';
			foreach($this->DATA_DAY['arrTdDay'][$thisdate] AS $row) {
				$str .= $row;
			}
			unset($row);

			$this->DATA_USER['tr'][$thisdate] = '';
			$this->DATA_USER['tr'][$thisdate] .= "<td><p>захват на 1 сутки</p>";
			$this->DATA_USER['tr'][$thisdate] .= $str;
			$this->DATA_USER['tr'][$thisdate] .= '<hr />';
			$this->DATA_USER['tr'][$thisdate] .= $this->DATA_DAY['tdDay'];
			$this->DATA_USER['tr'][$thisdate] .= "<p> - 1 - 1 - </p>";
			$this->DATA_USER['tr'][$thisdate] .= "</td>";
			$this->DATA_DAY['countDay'] = 0;
			return;
		} 


		# если первый приход меньше ухода
		if( !empty($this->CHANGE[$thisdate]['Coming']) && !empty($this->CHANGE[$thisdate]['Attendance'])  && 
			(array_slice($this->CHANGE[$thisdate]['Coming'],0,1) > array_slice($this->CHANGE[$thisdate]['Attendance'],0,1) )) {

			foreach($this->CHANGE AS $header => $arr) {
				//print_r($header);
				list($com, $kcom) = self::Type3graf('2', $header, $arr, $thisdate, $olddate, $com, $kcom);
				/*if($header == $olddate) {
					if(!empty($arr['Coming']) && is_array($arr['Coming'])) {

						foreach($arr['Coming'] as $rowC => $keyC) {

							if(isset($arr['Attendance'][$rowC])) {

								# итоги по факту
								if(isset($this->viewParam['totalFact'])) {
									$this->CHANGE['totalFact'] += $arr['Attendance'][$rowC] - $keyC;
								} 

								# обед
								if(isset($this->viewParam['lunch'])) {
									self::lunch($rowEmp['type'].'tc', $arr);
								}

								# итог по графику
								if(isset($this->viewParam['totalGraf'])) {
									self::totalGraf ( $rowEmp['type'] , $arr, $rowC );
								}
								//$this->CHANGE['totalFact'] += $arr['Attendance'][$rowC] - $keyC;
							} else {
								$com = $keyC;
								$kcom = $rowC;
							}
						}
					}
				}
				if($header == $thisdate) {

					if($com != 0) {
						if(isset($arr['Attendance'][$kcom])) {
							# итоги по факту
							if(isset($this->viewParam['totalFact'])) { 
								$this->CHANGE['totalFact'] += $arr['Attendance'][$kcom] - $com;
							}
							# итог по графику
							if(isset($this->viewParam['totalGraf'])) {
								self::totalGraf ( $rowEmp['type'].'tc' , $arr, $kcom, $com );
							}
						}
					}

					if(!empty($arr['Coming']) && is_array($arr['Coming'])) {

						foreach($arr['Coming'] as $rowC => $keyC) {

							if(isset($arr['Attendance'][$rowC])) {
								
								# итоги по факту
								if(isset($this->viewParam['totalFact'])) { 
									$this->CHANGE['totalFact'] += $arr['Attendance'][$rowC] - $keyC;
								}

								# итог по графику
								if(isset($this->viewParam['totalGraf'])) {
									self::totalGraf ( $rowEmp['type'] , $arr, $rowC );
								}

								# опоздание
								if(isset($this->viewParam['delayH'])) {
									$c = self::delayH ( $rowEmp['type'] , $c , $keyC , $arr , $rowC );
								}

								# обед
								if(isset($this->viewParam['lunch'])) {
									self::lunch($rowEmp['type'], $arr , $rowC );
								}

								# переработка до
								if(isset($this->viewParam['processingDo'])) {
									self::processingDo($rowEmp['type'], $arr, $rowC, $keyC);
								}
								# переработка после
								if(isset($this->viewParam['processingAfter'])) {
									self::processingAfter($rowEmp['type'], $arr, $rowC, $keyC);
								}
								//$this->CHANGE['totalFact'] += $arr['Attendance'][$rowC] - $keyC;
							}
						}
					}
					$com = 0;//print_r($arr);
				}/**/
			}

			# переработка до
			if(isset($this->viewParam['processingDo'])) {
				self::processingDo($rowEmp['type'].'processingDo');
			}
			# переработка после
			if(isset($this->viewParam['processingAfter'])) {
				self::processingAfter($rowEmp['type'].'processingAfter');
			}

			# переработка по факту или недоработка
			if(isset($this->viewParam['processingFact']) or isset($this->viewParam['flawFact'])) {
				self::flaw($rowEmp['type'], $rowEmp);
			}

			# обед
			if(isset($this->viewParam['lunch'])) {
				self::lunch($rowEmp['type'].'Count');
			}

			# итоги по факту
			if(isset($this->viewParam['totalFact'])) {
				$this->DATA_USER['totalFact'] += $this->CHANGE['totalFact'];
				$this->DATA_DAY['tdDay']  .='<p>'.Date_unix($this->CHANGE['totalFact'],7).' - Итог по факту</p> ';
			}

			# итог по графику
			if(isset($this->viewParam['totalGraf'])) {
				self::totalGraf ( $rowEmp['type'].'totalGraf', $arr);
			} 

			if(isset($this->DATA_DAY['arrTdDay'][$olddate])){

				# если время начала смены больше времени завершения, то в этот же день начинаеться 2я смена
				/*$unuxDate = strtotime($thisdate.$this->CHANGE[$thisdate]['timeStart']) - 3600;
				$thisArr = array();
				$nextArr = array();

				# проход по массиву приходов уходов
				foreach($this->DATA_DAY['arrTdDay'][$thisdate] AS $row => $key) {

					# транзакции за текущую смену
					if($unuxDate > $row)  {
						$thisArr[$row] = $key;

					# транзакции за следующию смену
					} else {
						$nextArr[$row] = $key;
					}
				}
				$this->DATA_DAY['arrTdDay'][$thisdate] = $thisArr;
				$this->DATA_DAY['arrTdDay'][$thisdate] = $nextArr;*/


				$str = '';
				foreach($this->DATA_DAY['arrTdDay'][$olddate] AS $row) {
					$str .= $row;
				}
				unset($row);
				foreach($this->DATA_DAY['arrTdDay'][$thisdate] AS $row) {
					$str .= $row;
				}
				unset($row);

				$this->DATA_USER['tr'][$olddate] = '';

				$this->DATA_USER['tr'][$olddate] .= "<td><p>захват на 2е суток</p>";
				$this->DATA_USER['tr'][$olddate] .= $str;
				$this->DATA_USER['tr'][$olddate] .= '<hr />';
				$this->DATA_USER['tr'][$olddate] .= $this->DATA_DAY['tdDay'];
				$this->DATA_USER['tr'][$olddate] .= "<p> - 2 - 2 - </p>";	

				# обнуление
				$this->CHANGE['totalFact']=0;
				# передача транзакций за слудующию смену
				/*if(isset($nextArr)){
					$this->DATA_DAY['arrTdDay'][$thisdate] = array();
					$this->DATA_DAY['arrTdDay'][$thisdate] = $nextArr;
					unset($nextArr);
				}*/
			}
			//unset($this->CHANGE);
			return;
		} else {

			foreach($this->CHANGE AS $header => $arr) {
				//print_r($header);

				//list($com, $kcom) = self::Type3graf('3', $header, $arr, $thisdate, $olddate, $com, $kcom);
				if($header == $olddate) { 
					if(!empty($arr['Coming']) && is_array($arr['Coming'])) {

						foreach($arr['Coming'] as $rowC => $keyC) {

							if(isset($arr['Attendance'][$rowC])) {

								$this->CHANGE['totalFact'] += $arr['Attendance'][$rowC] - $keyC;
							} else {
								$com = $keyC;
								$kcom = $rowC;

							}
						}
					}
				}
				if($header == $thisdate) {
		//echo $com.' '.$kcom.' ';				
					if($com != 0) {
						if(isset($arr['Attendance'][$kcom])) {
							$this->CHANGE['totalFact'] += $arr['Attendance'][$kcom] - $com;
						}
					}

					if(!empty($arr['Coming']) && is_array($arr['Coming'])) {

						foreach($arr['Coming'] as $rowC => $keyC) {

							if(isset($arr['Attendance'][$rowC])) {

								$this->CHANGE['totalFact'] += $arr['Attendance'][$rowC] - $keyC;
							}
						}
					}
					$com = 0;//print_r($arr);
				}/**/
			}
			//if($this->CHANGE[$thisdate]['timeStart'] < $this->CHANGE[$thisdate]['timeEnd']) {
			//}

			if(isset($this->DATA_DAY['arrTdDay'][$olddate])){

				if($this->CHANGE['totalFact'] != 0) { 
					if(!empty($this->DATA_DAY['arrTdDay'][$olddate])) {
						$str = '';
						foreach($this->DATA_DAY['arrTdDay'][$olddate] AS $row) {
							$str .= $row;
						}
						unset($row);
					}
					if(!empty($this->DATA_DAY['arrTdDay'][$thisdate])) {
						foreach($this->DATA_DAY['arrTdDay'][$thisdate] AS $row) {
							$str .= $row;
						}
						unset($row);
					}

					$this->DATA_USER['tr'][$olddate] = '';

					$this->DATA_USER['tr'][$olddate] .= "<td><p>захват на 2е суток</p>";
					$this->DATA_USER['tr'][$olddate] .= $str;
					$this->DATA_USER['tr'][$olddate] .= '<hr />';
					$this->DATA_USER['tr'][$olddate] .= $this->DATA_DAY['tdDay'];
					$this->DATA_USER['tr'][$olddate] .= "<p> - 3 - 3 - </p>";	
					$this->DATA_USER['tr'][$olddate] .= "<p>". Date_unix($this->CHANGE['totalFact'],7) ." - Итог по факту</p>";	

					# обнуление
					$this->CHANGE['totalFact']=0;
					# передача транзакций за слудующию смену
					/*if(isset($nextArr)){
						$this->DATA_DAY['arrTdDay'][$thisdate] = array();
						//$this->DATA_DAY['arrTdDay'][$thisdate] = $nextArr;
						unset($nextArr);
					}*/
				}
			}
		}	
	}

	

	#||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
	#                 ПОДСЧЁТЫ

	#|||||     ОПРЕДЕЛЕНИЕ ВЫХОДНОГО
	public function ofDay( $param , $rowEmp ) {
		switch ( $param ) {

			# для дневного
			case '1':
					if($rowEmp['timeStart'][$this->interval] == '00:00:00' && $rowEmp['timeEnd'][$this->interval] == '00:00:00') {
						$this->DATA_DAY['tdDay'] .= '<span></span>';
						$ofDay = FALSE;
					} else {
						$ofDay = TRUE;
					}
			break;
			# для суточного
			case '2':
					//$this->DATA_DAY['tdDay'] .= $rowEmp['timeStart'][$this->interval].' == 00:00:00 && '.$rowEmp['timeEnd'][$this->interval].' == 00:00:00 ';
					if($rowEmp['timeStart'][$this->interval] == '00:00:00' && $rowEmp['timeEnd'][$this->interval] == '00:00:00') {
						$this->DATA_DAY['tdDay'] .= '<span></span>';
						$ofDay = FALSE;
					} else {
						$ofDay = TRUE;
					}
			break;

			case 'day':
					//$this->DATA_DAY['tdDay'] .= $rowEmp['timeStart'][$this->interval].' == 00:00:00 && '.$rowEmp['timeEnd'][$this->interval].' == 00:00:00 ';
					if($rowEmp['timeStart'][$this->interval] == '00:00:00' && $rowEmp['timeEnd'][$this->interval] == '00:00:00') {
						$ofDay = '0'; //echo 'sdsadasdasdas';
					} else {
						$ofDay = '1';
					}
					return $ofDay;
			break;

			default : $ofDay = FALSE;
		}

		return array( $ofDay , $this->DATA_DAY );

	}

	#|||||      НЕДОРОБОТКИ / ПЕРЕРАБОТКИ
	public function flaw( $param , $rowEmp = array()) {

		// ПРОВЕРКА, СЧИТАТЬ ЭТОТ ДЕНЬ В ИТОГАХ ИЛИ НЕТ
		switch ( $param ) {
			case '1' :
						if(isset($this->CHANGE[$this->CHANGE['point']])) {

							$this->CHANGE['grafH'] = strtotime($this->CHANGE[$this->CHANGE['point']]['timeEnd']) - strtotime($this->CHANGE[$this->CHANGE['point']]['timeStart']);
							$difference = $this->CHANGE['grafH'] - $this->CHANGE['totalFact'];

							if($difference < 0) {
								if(isset($this->viewParam['processingFact'])) {
									$this->DATA_DAY['tdDay'] .= '<p>'.Date_unix(abs($difference),7).' - Переработка по факту</p> ';
									$this->DATA_USER['processingFactH'] += abs($difference);
									$this->DATA_USER['processingFactD']++;
								}
							} else {
								if(isset($this->viewParam['flawFact'])) {
									$this->DATA_DAY['tdDay'] .= '<p>'.Date_unix($difference,7).' - Недоработка по факту</p> ';
									$this->DATA_USER['flawFactH'] += $difference;
									$this->DATA_USER['flawFactD']++;
								}
							}
						}
			break;

			case '2' :	if(!empty($this->CHANGE['grafStart']) && !empty($this->CHANGE['grafEnd'])) {

							foreach ($this->CHANGE['grafEnd'] AS $row => $key) {
								if($key != '00:00:00') {
									if( $row > 0 ) {
										$row = 86400 * $row;
									}
									$TF = (strtotime($this->CHANGE['grafPoint'] . $key)) + $row;
									//$this->DATA_DAY['tdDay']  .='<p>'.date('d.m.Y H:i:s',$TF).' - уход по графику</p>';
								}
							}
							foreach ($this->CHANGE['grafStart'] AS $row => $key) {
								if($key != '00:00:00') {
									if( $row > 0 ) {
										$row = 86400 * $row;
									}
									$TS = (strtotime($this->CHANGE['grafPoint'] . $key)) + $row;
								}
							}

							$dif = $TF - $TS;
							//$this->DATA_DAY['tdDay']  .='<p>'.$this->CHANGE['totalGraf'] .'-'. $this->CHANGE['totalFact'].' - Переработка</p> ';  
							$difference = $dif - $this->CHANGE['totalFact'];

							if($difference < 0) {
								if(isset($this->viewParam['processingFact'])) {
									$this->DATA_DAY['tdDay'] .= '<p>'.Date_unix(abs($difference),7).' - Переработка по факту</p> ';
									$this->DATA_USER['processingFactH'] += abs($difference);
									$this->DATA_USER['processingFactD']++;
								}
							} else {
								if(isset($this->viewParam['flawFact'])) {
									$this->DATA_DAY['tdDay'] .= '<p>'.Date_unix($difference,7).' - Недоработка по факту</p> ';
									$this->DATA_USER['flawFactH'] += $difference;
									$this->DATA_USER['flawFactD']++;
								}
							}
						}
			break;

			case 'ofDay' :
						$this->DATA_DAY['tdDay'] .='<p>'.Date_unix(abs($this->DATA_DAY['process']),7).' - Переработка в выходной</p> ';
						$this->DATA_USER['allProcessingH'] += abs($this->DATA_DAY['process']);
						$this->DATA_USER['allProcessingD']++;
			break;
		}

		return $this->DATA_DAY;
	}

	#|||||      ИТОГИ
	public function total( $param ) {
		// ПРОВЕРКА, СЧИТАТЬ ЭТОТ ДЕНЬ В ИТОГАХ ИЛИ НЕТ
		switch ( $param ) {
			case '1' :
						$test = $this->DATA_DAY['attendance'] - $this->DATA_DAY['coming'];
						if($test > 0 && $test < 86000) {
							$this->DATA_DAY['dayAttendance'] += $this->DATA_DAY['attendance'];
							$this->DATA_DAY['dayComing'] += $this->DATA_DAY['coming'];
						}
			break;
		}

		return $this->DATA_DAY;
	}

	#|||||     ВЫВОД ПРИХОДА, УХОДА, ОПОЗДАНИЙ и Т.Д.
	public function formationOfAnAdditionalInformation( $param ) {

		// ЕСЛИ ЕСТЬ ПРИХОД И УХОД
		/*if($this->DATA_DAY['coming'] != 0 or $this->DATA_DAY['attendance'] != 0)
		{
			// ПОДСЧИТЫВАЮ РАЗНИЦУ ВО ВРЕМЕНИ
			$countTime = $this->DATA_DAY['attendance'] - $this->DATA_DAY['coming'];

			//$tdCont .= "<p>". $countTime ."=". $ifcoming ."-". $ifattendance ."</p>";
			switch ($countTime) {
				// ЕСЛИ ЧИСЛО БОЛЬШЕ СУТОК
				case $countTime > 86400:
						//$this->DATA_DAY['tdDay'] .= "<p> Сотрудник не приходил </p>";
				break;
				// ЕСЛИ ЧИСЛО МЕНЬШЕ СУТОК
				case $countTime < 0:
						//$this->DATA_DAY['tdDay'] .= "<p> Сотрудник не уходил </p>";
				break;
			}

			// ПОДСЧЁТ ОБЩЕЙ СУММЫ ПРИХОДОВ И УХОДОВ
			$this->DATA_USER['allComingH'] += $this->DATA_DAY['dayComing'];
			$this->DATA_USER['allAttendanceH'] += $this->DATA_DAY['dayAttendance'];
		}
		// ЕСЛИ ЧИСЛО ПОЛОЖИТЕЛЬНОЕ И НЕ БОЛЬШЕ СУТОК ВЫВОЖУ ИТОГ
		$itogCount = $this->DATA_DAY['dayAttendance'] - $this->DATA_DAY['dayComing'];
		if($itogCount > 0 && $itogCount < 86400) {
			$this->DATA_DAY['tdDay'] .= "<p>". Date_unix($itogCount, 6) ." - Итог</p>";

			# счётчик дней в которые сотрудник работал
			$this->DATA_USER['totalD']++;
		}

		return array($this->DATA_USER, $this->DATA_DAY);*/
	}

	#|||||     ВЫВОД ИТОГОВ ПО ПРЕДПОЧТЕНИЮ ПОЛЬЗОВАТЕЛЯ
	private function viewItog( $param ) {
		$result = '';

		switch( $param ) {
			case 'tr' :
					foreach ($this->viewParam as $row => $key) {
						switch ($row) {
							
							case 'delayH' :
										$result .= "<td>". Date_unix($this->DATA_USER['allDelayH'], 7) ."</td>";
							break;
							case 'flawH' :
										$result .= "<td>". Date_unix($this->DATA_USER['allFlawH'], 7) ."</td>";
							break;
							case 'processingH' :
										$result .= "<td>". Date_unix($this->DATA_USER['allProcessingH'], 7) ."</td>";
							break;
							case 'delayD' :
										$result .= "<td>". $this->DATA_USER['allDelayD'] ."</td>";
							break;
							case 'flawD' :
										$result .= "<td>". $this->DATA_USER['allFlawD'] ."</td>";
							break;
							case 'processingD' :
										$result .= "<td>". $this->DATA_USER['allProcessingD'] ."</td>";
							break;
							case 'detalInfo' :

							break;
							
							####
							case 'totalFact' :
										$result .= "<td>". $this->DATA_USER['totalFactD'] ."</td>";
										$result .= "<td>". Date_unix($this->DATA_USER['totalFact'], 7) ."</td>";
							break;
							case 'totalGraf' :
										$result .= "<td>". Date_unix($this->DATA_USER['totalGraf'], 7) ."</td>";
							break;
							case 'processingDo' :
										$result .= "<td>". $this->DATA_USER['processingDoD'] ."</td>";
										$result .= "<td>". Date_unix($this->DATA_USER['processingDoH'], 7) ."</td>";
							break;
							case 'processingAfter' :
										$result .= "<td>". $this->DATA_USER['processingAfterD'] ."</td>";
										$result .= "<td>". Date_unix($this->DATA_USER['processingAfterH'], 7) ."</td>";
							break;
							case 'flawFact' :
										$result .= "<td>". $this->DATA_USER['flawFactD'] ."</td>";
										$result .= "<td>". Date_unix($this->DATA_USER['flawFactH'], 7) ."</td>";
							break;
							case 'processingFact' :
										$result .= "<td>". $this->DATA_USER['processingFactD'] ."</td>";
										$result .= "<td>". Date_unix($this->DATA_USER['processingFactH'], 7) ."</td>";
							break;
						}
					}
			break;
			case 'th' :
					foreach ($this->viewParam as $row => $key) {  
						switch ($row) {
							
							case 'delayH' :
										$result .= "<th>Опоздания в часах</th>";
							break;
							case 'flawH' :
										$result .= "<th>Недороботка в часах</th>";
							break;
							case 'processingH' :
										$result .= "<th>Переработка в часах</th>";
							break;
							case 'delayD' :
										$result .= "<th>Опоздания в днях</th>";
							break;
							case 'flawD' :
										$result .= "<th>Недороботка в днях</th>";
							break;
							case 'processingD' :
										$result .= "<th>Переработка в днях</th>";
							break;
							###
							case 'totalFact' :
										$result .= "<th>Итог по факту в днях</th>";
										$result .= "<th>Итог по факту в часах</th>";
							break;
							case 'totalGraf' :
										$result .= "<th>Итог по графику</th>";
							break;
							case 'processingDo' :
										$result .= "<th>Переработка До в днях</th>";
										$result .= "<th>Переработка До в часах</th>";
							break;
							case 'processingAfter' :
										$result .= "<th>Переработка После в днях</th>";
										$result .= "<th>Переработка После в часах</th>";
							break;
							case 'flawFact' :
										$result .= "<th>Недороботка в днях</th>";
										$result .= "<th>Недороботка После в часах</th>";
							break;
							case 'processingFact' :
										$result .= "<th>Переработка После в днях</th>";
										$result .= "<th>Переработка После в часах</th>";
							break;
						}
					}
			break;
		}

		return $result;
	}


	#|||||		ПЕРЕРАБОТКА ДО
	private function processingDo ( $param , $arr=array() , $rowC='' , $keyC='' ) {

		switch( $param ) {
			case '1' : case '2' : case '3' : 
						if($keyC < (strtotime($arr['Date'].$arr['timeStart'])) && $arr['Attendance'][$rowC] < (strtotime($arr['Date'].$arr['timeStart']))) {
							$this->PROCESSING['processingDo'] += $arr['Attendance'][$rowC] - $keyC;
							
							$this->DATA_USER['processingDoD']++;
							$this->DATA_USER['processingDoH'] += $arr['Attendance'][$rowC] - $keyC;
						} else{
							if($keyC < (strtotime($arr['Date'].$arr['timeStart'])) && $arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeStart']))) {
								$this->PROCESSING['processingDo'] += (strtotime($arr['Date'].$arr['timeStart'])) - $keyC;
								
								$this->DATA_USER['processingDoD']++;
								$this->DATA_USER['processingDoH'] += $arr['Attendance'][$rowC] - $keyC;
							}
						}
			break;

			case '1processingDo': case '2processingDo': case '3processingDo':
						if(isset($this->PROCESSING['processingDo']) && $this->PROCESSING['processingDo'] != 0) {
							$this->DATA_DAY['tdDay']  .='<p>'.Date_unix($this->PROCESSING['processingDo'],7).' - Переработка до</p> ';
						}
			break;
		}


	}

	#|||||		ПЕРЕРАБОТКА ПОСЛЕ
	private function processingAfter ( $param , $arr=array() , $rowC='' , $keyC='' ) {

		switch( $param ) {
			case '1' :
						if($keyC > (strtotime($arr['Date'].$arr['timeEnd'])) && $arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeEnd']))) {
							$this->PROCESSING['processingAfter'] += $arr['Attendance'][$rowC] - $keyC;
							
							$this->DATA_USER['processingAfterD']++;
							$this->DATA_USER['processingAfterH'] += $arr['Attendance'][$rowC] - $keyC;
						} else{
							if($keyC < (strtotime($arr['Date'].$arr['timeEnd'])) && $arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeEnd']))) {
								$this->PROCESSING['processingAfter'] += $arr['Attendance'][$rowC] - (strtotime($arr['Date'].$arr['timeEnd']));
								
								$this->DATA_USER['processingAfterD']++;
								$this->DATA_USER['processingAfterH'] += $arr['Attendance'][$rowC] - $keyC;
							}
						}
			break;

			case '1processingAfter':
						if(isset($this->PROCESSING['processingAfter']) && $this->PROCESSING['processingAfter'] != 0) {
							$this->DATA_DAY['tdDay']  .='<p>'.Date_unix($this->PROCESSING['processingAfter'],7).' - Переработка после</p> ';
						}
			break;

			case '2' : case '3' :
						if($keyC > (strtotime($arr['Date'].$arr['timeEnd'])) && $arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeEnd'])) && $arr['timeEnd'] != '00:00:00') {
							$this->PROCESSING['processingAfter'] += $arr['Attendance'][$rowC] - $keyC;
							
							$this->DATA_USER['processingAfterD']++;
							$this->DATA_USER['processingAfterH'] += $arr['Attendance'][$rowC] - $keyC;
						} else{
							if($keyC < (strtotime($arr['Date'].$arr['timeEnd'])) && $arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeEnd']))) {
								$this->PROCESSING['processingAfter'] += $arr['Attendance'][$rowC] - (strtotime($arr['Date'].$arr['timeEnd']));
								
								$this->DATA_USER['processingAfterD']++;
								$this->DATA_USER['processingAfterH'] += $arr['Attendance'][$rowC] - $keyC;
							}
						}
			break;

			case '2processingAfter': case '3processingAfter':
						if(isset($this->PROCESSING['processingAfter']) && $this->PROCESSING['processingAfter'] != 0) {
							$this->DATA_DAY['tdDay']  .='<p>'.Date_unix($this->PROCESSING['processingAfter'],7).' - Переработка после</p> ';
						}
			break;
		}
	}

	#|||||      ОПОЗДАНИЯ В ЧАСАХs
	public function delayH ( $param , $c , $keyC , $arr , $rowC ) {

		switch ($param) {

		case '1' :
				if($c == 0){
					if($keyC < (strtotime($arr['Date'].$arr['timeStart'])) && $arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeStart']))) {
						$c++;
					} else {
						if($keyC > (strtotime($arr['Date'].$arr['timeStart'])) && $arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeStart']))) {
							$this->DATA_DAY['dayDelay'] = $keyC - (strtotime($arr['Date'].$arr['timeStart']));

							if($this->DATA_DAY['dayDelay'] != 0) {
								if($this->ofDay) {
									$this->DATA_DAY['tdDay'] .= '<p>'.Date_unix($this->DATA_DAY['dayDelay'], 6).' - Опоздание</p>';
								}
							}

							$c++;
						}
					}
				}
		break;

		case '2' : case '3' :
				if($c == 0){
					if($keyC < (strtotime($arr['Date'].$arr['timeStart'])) && $arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeStart']))) {
						$c++;
					} else {
						if($keyC > (strtotime($arr['Date'].$arr['timeStart'])) && $arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeStart']))) {
							$this->DATA_DAY['dayDelay'] = $keyC - (strtotime($arr['Date'].$arr['timeStart']));

							if($this->DATA_DAY['dayDelay'] > 0 ) {
								$this->DATA_DAY['tdDay'] .= '<p>'.Date_unix($this->DATA_DAY['dayDelay'], 6).' - Опоздание</p>';

								$this->DATA_USER['allDelayH'] += $this->DATA_DAY['dayDelay'];
								# счётчик дней в которые сотрудник опаздывал
								$this->DATA_USER['allDelayD']++;
							}

							$c++;
						}
					}
				}
		break;
		}

		return $c;
	}

	#|||||      ОБЕД
	public function lunch ( $param , $arr = '' , $rowC = '', $testComing = array() ) {

		switch ($param) {

			case '1' : 
					if($this->CHANGE['lunch'] == '1') { 	
	
						# уход на обед правильный
						if(	$arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['grafSL'])) && $arr['Attendance'][$rowC] < (strtotime($arr['Date'].$arr['grafEL']))) {
							$this->LUNCH['lunchAtTrue'] = $arr['Attendance'][$rowC];
						}
						# уход на обед НЕправильный
						if($arr['Attendance'][$rowC] < (strtotime($arr['Date'].$arr['grafSL'])) && $arr['Attendance'][$rowC] < (strtotime($arr['Date'].$arr['grafEL']))) {
							$this->LUNCH['lunchAtFalse'] = $arr['Attendance'][$rowC];
						}
						# приход с обеда правильный
						if($arr['Coming'][$rowC] > (strtotime($arr['Date'].$arr['grafSL'])) && $arr['Coming'][$rowC] < (strtotime($arr['Date'].$arr['grafEL']))) {
							$this->LUNCH['lunchCoTrue'] = $arr['Coming'][$rowC];
						}
						# приход с обеда НЕправильный
						if($arr['Coming'][$rowC] > (strtotime($arr['Date'].$arr['grafSL'])) && $arr['Coming'][$rowC] > (strtotime($arr['Date'].$arr['grafEL']))) {
							$this->LUNCH['lunchCoFalse'] = $arr['Coming'][$rowC];
						}

					} else {
						$this->DATA_DAY['tdDay']  .='<p>Обеда нет</p> ';
					}
			break;

			case '2' : case '3' :
					if($this->CHANGE['lunch'] == '1') {
						# уход на обед правильный
						if(	$arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeSL'])) && $arr['Attendance'][$rowC] < (strtotime($arr['Date'].$arr['timeEL']))) {
							$this->LUNCH['lunchAtTrue'] = $arr['Attendance'][$rowC];
						}
						# уход на обед НЕправильный
						if($arr['Attendance'][$rowC] < (strtotime($arr['Date'].$arr['timeSL'])) && $arr['Attendance'][$rowC] < (strtotime($arr['Date'].$arr['timeEL']))) {
							$this->LUNCH['lunchAtFalse'] = $arr['Attendance'][$rowC];
						}
						# приход с обеда правильный
						if($arr['Coming'][$rowC] > (strtotime($arr['Date'].$arr['timeSL'])) && $arr['Coming'][$rowC] < (strtotime($arr['Date'].$arr['timeEL']))) {
							$this->LUNCH['lunchCoTrue'] = $arr['Coming'][$rowC];  $this->DATA_DAY['tdDay']  .='<p>'.  Date_unix($arr['Coming'][$rowC],8).'</p> ';
						}
						# приход с обеда НЕправильный
						if($arr['Coming'][$rowC] > (strtotime($arr['Date'].$arr['timeSL'])) && $arr['Coming'][$rowC] > (strtotime($arr['Date'].$arr['timeEL']))) {
							$this->LUNCH['lunchCoFalse'] = $arr['Coming'][$rowC];
						}
					} else {
						$this->DATA_DAY['tdDay']  .='<p>Обеда нет</p> ';
					}
			break;

			case '2tc' : case '3tc' :
					if($this->CHANGE['lunch'] == '1') {
						$rowA = $rowC; 
						
						# уход на обед правильный
						if(	$arr['Attendance'][$rowA] > (strtotime($arr['Date'].$arr['timeSL'])) && $arr['Attendance'][$rowA] < (strtotime($arr['Date'].$arr['timeEL']))) {
							$this->LUNCH['lunchAtTrue'] = $arr['Attendance'][$rowC]; 
						}
						# уход на обед НЕправильный
						if($arr['Attendance'][$rowA] < (strtotime($arr['Date'].$arr['timeSL'])) && $arr['Attendance'][$rowA] < (strtotime($arr['Date'].$arr['timeEL']))) {
							$this->LUNCH['lunchAtFalse'] = $arr['Attendance'][$rowA];
						}
						# приход с обеда правильный
						if($testComing[$rowA] > (strtotime($arr['Date'].$arr['timeSL'])-86400) && $testComing[$rowA] < (strtotime($arr['Date'].$arr['timeEL'])-86400)) {
							$this->LUNCH['lunchCoTrue'] = $testComing[$rowA];  $this->DATA_DAY['tdDay']  .='<p>'.  Date_unix($testComing[$rowA],8).'</p> '; 
						}
						# приход с обеда НЕправильный
						if($testComing[$rowA] > (strtotime($arr['Date'].$arr['timeSL'])-86400) && $testComing[$rowA] > (strtotime($arr['Date'].$arr['timeEL'])-86400)) {
							$this->LUNCH['lunchCoFalse'] = $testComing[$rowC];
						}
					}
			break;

			case '1Count' :
			case '2Count' :
			case '3Count' :
					if(isset($this->CHANGE['lunch']) && $this->CHANGE['lunch'] == '1') {

						# уход на обед правильный
						if(isset($this->LUNCH['lunchAtTrue']) && isset($this->LUNCH['lunchCoTrue'])) {
							$this->LUNCH['CeneralLunch'] = $this->LUNCH['lunchCoTrue'] - $this->LUNCH['lunchAtTrue'];

						} else {
							# уход на обед правильный
							if(isset($this->LUNCH['lunchAtFalse']) && isset($this->LUNCH['lunchCoTrue'])) {
								$this->LUNCH['CeneralLunch'] = $this->LUNCH['lunchCoTrue'] - $this->LUNCH['lunchAtFalse'];
							}
							if(isset($this->LUNCH['lunchAtTrue']) && isset($this->LUNCH['lunchCoFalse'])) {
								$this->LUNCH['CeneralLunch'] = $this->LUNCH['lunchCoFalse'] - $this->LUNCH['lunchAtTrue'];
							}
						}

						# вывод обеда если он есть
						if(isset($this->LUNCH['CeneralLunch'])) {
							if( $this->LUNCH['CeneralLunch'] > 0 ) {
								$this->DATA_DAY['tdDay']  .='<p>'.Date_unix($this->LUNCH['CeneralLunch'],7).' - обед</p> ';
							} else {
								$this->DATA_DAY['tdDay']  .='<p> перерасчитать обед</p> ';
							}
						}
					}
			break;

			/*case '2Count' :
					if(isset($this->CHANGE['lunch']) && $this->CHANGE['lunch'] == '1') {

						# уход на обед правильный
						if(isset($this->LUNCH['lunchAtTrue']) && isset($this->LUNCH['lunchCoTrue'])) {
							$this->LUNCH['CeneralLunch'] = $this->LUNCH['lunchCoTrue'] - $this->LUNCH['lunchAtTrue'];

						} else {
							# уход на обед правильный
							if(isset($this->LUNCH['lunchAtFalse']) && isset($this->LUNCH['lunchCoTrue'])) {
								$this->LUNCH['CeneralLunch'] = $this->LUNCH['lunchCoTrue'] - $this->LUNCH['lunchAtFalse'];
							}
							if(isset($this->LUNCH['lunchAtTrue']) && isset($this->LUNCH['lunchCoFalse'])) {
								$this->LUNCH['CeneralLunch'] = $this->LUNCH['lunchCoFalse'] - $this->LUNCH['lunchAtTrue'];
							}
						}

						# вывод обеда если он есть
						if(isset($this->LUNCH['CeneralLunch'])) {
							if( $this->LUNCH['CeneralLunch'] > 0 ) {
								$this->DATA_DAY['tdDay']  .='<p>'.Date_unix($this->LUNCH['CeneralLunch'],7).' - обед</p> ';
							} else {
								$this->DATA_DAY['tdDay']  .='<p> перерасчитать обед</p> ';
							}
						}
					}
			break;*/
		}
	}
	
	#|||||      ИТОГ ПО ГРАФИКУ
	public function totalGraf ( $param , $arr = array() , $rowC = '', $testComing = array() ) {
		
		switch ($param) {
			
			case '1':
					if( ($arr['Coming'][$rowC] > (strtotime($arr['Date'].$arr['timeStart'])) or $arr['timeStart'] == '00:00:00') && 
						($arr['Attendance'][$rowC] < (strtotime($arr['Date'].$arr['timeEnd'])) or $arr['timeEnd'] == '00:00:00') ) {
						$this->CHANGE['totalGraf'] += $arr['Attendance'][$rowC] - $arr['Coming'][$rowC];
					} else {
						if( $arr['Coming'][$rowC] < (strtotime($arr['Date'].$arr['timeStart'])) && 
							$arr['Attendance'][$rowC] < (strtotime($arr['Date'].$arr['timeEnd'])) && 
							$arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeStart']))) 
						{
							$this->CHANGE['totalGraf'] += $arr['Attendance'][$rowC] - (strtotime($arr['Date'].$arr['timeStart']));
						}
						if( $arr['Coming'][$rowC] > (strtotime($arr['Date'].$arr['timeStart'])) && 
							$arr['Coming'][$rowC] < (strtotime($arr['Date'].$arr['timeEnd'])) && 
							$arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeEnd']))) 
						{
							$this->CHANGE['totalGraf'] += (strtotime($arr['Date'].$arr['timeEnd'])) - $arr['Coming'][$rowC];
						}
					}
			break;
			
			case '2' : 
					if( ($arr['Coming'][$rowC] > (strtotime($arr['Date'].$arr['timeStart'])) or $arr['timeStart'] == '00:00:00') && 
						($arr['Attendance'][$rowC] < (strtotime($arr['Date'].$arr['timeEnd'])) or $arr['timeEnd'] == '00:00:00') ) {
						
						# если есть обед
						/*if(!empty($arr['timeSL']) && $arr['timeSL'] != '00:00:00' && !empty($arr['timeEL']) && $arr['timeEL'] != '00:00:00') {
							
							# если время прихода или ухода ухода совпадает то отнимаю его
							if( $arr['Coming'][$rowC] > (strtotime($arr['Date'].$arr['timeSL'])) && $arr['Coming'][$rowC] < (strtotime($arr['Date'].$arr['timeEL'])) or
								$arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeSL'])) && $arr['Attendance'][$rowC] < (strtotime($arr['Date'].$arr['timeEL'])))  {
								$this->CHANGE['linchGraf'] = (strtotime($arr['Date'].$arr['timeEL'])) - (strtotime($arr['Date'].$arr['timeSL']));
							}
							
						# если нет обеда
						} else {
							$this->CHANGE['totalGraf'] += $arr['Attendance'][$rowC] - $arr['Coming'][$rowC];
						}*/
						$this->CHANGE['totalGraf'] += $arr['Attendance'][$rowC] - $arr['Coming'][$rowC];
					} else {
						if( $arr['Coming'][$rowC] < (strtotime($arr['Date'].$arr['timeStart'])) && 
							$arr['Attendance'][$rowC] < (strtotime($arr['Date'].$arr['timeEnd'])) && 
							$arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeStart']))) 
						{
							$this->CHANGE['totalGraf'] += $arr['Attendance'][$rowC] - (strtotime($arr['Date'].$arr['timeStart']));
						}
						if( $arr['Coming'][$rowC] > (strtotime($arr['Date'].$arr['timeStart'])) && 
							$arr['Coming'][$rowC] < (strtotime($arr['Date'].$arr['timeEnd'])) && 
							$arr['Attendance'][$rowC] > (strtotime($arr['Date'].$arr['timeEnd']))) 
						{
							$this->CHANGE['totalGraf'] += (strtotime($arr['Date'].$arr['timeEnd'])) - $arr['Coming'][$rowC];
						}
					}
			break;
			
			case '3' :
					$timeStart = strtotime($arr['Date'].$arr['timeStart']);
					$timeEnd = strtotime($arr['Date'].$arr['timeEnd']);
					$thisDate = strtotime($arr['Date']);
					$oldDate = strtotime($arr['Date']) - 84600;
					
					if($timeStart > $timeEnd) {
						
					} else {
						# если временной интервал больше графика
						if( ($arr['Coming'][$rowC] < $timeStart) && 
								($arr['Attendance'][$rowC] > $timeEnd) && 
								($arr['timeEnd'] != '00:00:00') && 
								($arr['timeStart'] != '00:00:00')) {

							if($timeStart < $timeEnd) {
								$this->CHANGE['totalGraf'] += $timeEnd - $timeStart;

								break;
							}
						}

						# если временной интервал больше графика
						if( ($arr['Coming'][$rowC] > $timeStart or $arr['timeStart'] == '00:00:00') && 
							($arr['Attendance'][$rowC] < $timeEnd or $arr['timeEnd'] == '00:00:00') ) {
							$this->CHANGE['totalGraf'] += $arr['Attendance'][$rowC] - $arr['Coming'][$rowC];

							break;
						}

						if( $arr['Coming'][$rowC] < $timeStart && 
							$arr['Attendance'][$rowC] < $timeEnd && 
							$arr['Attendance'][$rowC] > $timeStart) 
						{
							$this->CHANGE['totalGraf'] += $arr['Attendance'][$rowC] - $timeStart;
							break;
						}

						if( $arr['Coming'][$rowC] > $timeStart && 
							$arr['Coming'][$rowC] < $timeEnd && 
							$arr['Attendance'][$rowC] > $timeEnd) 
						{
							$this->CHANGE['totalGraf'] += $timeEnd - $arr['Coming'][$rowC];
							break;
						}
					}
					
			break;
			
			case '2tc' : 
					$rowA = $rowC;
					if( ($testComing[$rowA]  > (strtotime($arr['Date'].$arr['timeStart'])) or $arr['timeStart'] == '00:00:00') && 
					($arr['Attendance'][$rowA] < (strtotime($arr['Date'].$arr['timeEnd'])) or $arr['timeEnd'] == '00:00:00') ) 
					{
						$this->CHANGE['totalGraf'] += $arr['Attendance'][$rowA] - $testComing[$rowA];
					} else {
						if( $testComing[$rowA] < (strtotime($arr['Date'].$arr['timeStart'])) && 
							$arr['Attendance'][$rowA] < (strtotime($arr['Date'].$arr['timeEnd'])) && 
							$arr['Attendance'][$rowA] > (strtotime($arr['Date'].$arr['timeStart']))) 
						{
							$this->CHANGE['totalGraf'] += $arr['Attendance'][$rowA] - (strtotime($arr['Date'].$arr['timeStart']));
						}
						if( $testComing[$rowA] > (strtotime($arr['Date'].$arr['timeStart'])) && 
							$testComing[$rowA] < (strtotime($arr['Date'].$arr['timeEnd'])) && 
							$arr['Attendance'][$rowA] > (strtotime($arr['Date'].$arr['timeEnd']))) 
						{
							$this->CHANGE['totalGraf'] += (strtotime($arr['Date'].$arr['timeEnd'])) - $testComing[$rowA];
						}
					}
			break;
			
			case '3tc' :
					/*$kcom = $rowC;
					$com = $testComing;
					$timeStart = strtotime($arr['Date'].$arr['timeStart']);
					$timeEnd = strtotime($arr['Date'].$arr['timeEnd']);
					$this->DATA_DAY['tdDay'] .='<p>'.$arr['Date'].$arr['timeStart'].' '.$arr['Date'].$arr['timeEnd'].'</p> ';
					if($timeStart>$timeEnd) {

						# если временной интервал больше графика
						if( ($com < $timeStart or $arr['timeStart'] == '00:00:00') && 
							($arr['Attendance'][$rowC] > $timeEnd or $arr['timeEnd'] == '00:00:00') ) {
							$this->CHANGE['totalGraf'] += $arr['Attendance'][$rowC] - $com;

							break;
						}

						if( $com < $timeStart && 
							$arr['Attendance'][$rowC] > $timeEnd && 
							$arr['Attendance'][$rowC] < $timeStart) 
						{
							$this->CHANGE['totalGraf'] += $arr['Attendance'][$rowC] - $timeStart;
							break;
						}

						if( $com > $timeStart && 
							$arr['Coming'][$rowC] > $timeEnd && 
							$arr['Attendance'][$rowC] < $timeEnd) 
						{
							$this->CHANGE['totalGraf'] += $timeEnd - $com;
							break;
						}
					}*/
			break;
			
			case '1totalGraf' : 
					if(!empty($this->CHANGE['totalGraf'])) {
						
						# отнимаю обеденное время от графика если стоит галочка
						if(isset($this->viewParam['minLunch'])) {
							if(!empty($arr['grafEL']) && !empty($arr['grafSL'])) {
								$lunch = (strtotime($arr['Date'].$arr['grafEL'])) - (strtotime($arr['Date'].$arr['grafSL']));
								if($lunch > 0 && $lunch < $this->CHANGE['totalGraf'])  {
									//$this->DATA_DAY['tdDay'] .='<p>'.Date_unix($lunch,7).' - Обед по графику</p> ';
									$this->CHANGE['totalGraf'] = $this->CHANGE['totalGraf'] - $lunch;
								}
							}
						}
						$this->DATA_USER['totalGraf'] += $this->CHANGE['totalGraf'];
						$this->DATA_DAY['tdDay'] .='<p>'.Date_unix($this->CHANGE['totalGraf'],7).' - Итог по графику</p> ';
					}
			break;
			
			case '2totalGraf' : case '3totalGraf' :
					if(!empty($this->CHANGE['totalGraf'])) {
						
						# отнимаю обеденное время от графика если стоит галочка
						if(isset($this->viewParam['minLunch'])) {
							if(!empty($arr['timeEL']) && !empty($arr['timeSL'])) {
								$lunch = (strtotime($arr['Date'].$arr['timeEL'])) - (strtotime($arr['Date'].$arr['timeSL']));
								if($lunch > 0 && $lunch < $this->CHANGE['totalGraf'])  {
									//$this->DATA_DAY['tdDay'] .='<p>'.Date_unix($lunch,7).' - Обед по графику</p> ';
									$this->CHANGE['totalGraf'] = $this->CHANGE['totalGraf'] - $lunch;
								}
							}
						}
						$this->DATA_USER['totalGraf'] += $this->CHANGE['totalGraf'];
						$this->DATA_DAY['tdDay'] .='<p>'.Date_unix($this->CHANGE['totalGraf'],7).' - Итог по графику</p> ';
					}
			break;
		}
		
	}


	
	#||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
	#|||||     ФОРМИРОВАНИЕ ОТЧЁТА ПОСЕЩЕНИЯ ДЛЯ ПОЛЬЗОВАТЕЛЯ
	public function formationTdUser( $rowEmp ) {

		for ($i=0; $i <= $this->daycount; $i++)
		{
			$this->thisday = strtotime("+".$i." day", $this->dtStartStr); // текущая дата

			# продолжение смены
			if($this->DATA_DAY['countDay'] != 0) {
				$this->DATA_DAY['tdDay'] = '';

			# новый день или начало смены
			} else {
				unset($this->DATA_DAY);
				unset($this->CHANGE);
				unset($this->LUNCH);
				unset($this->PROCESSING);

				$this->DATA_DAY = array
				(
					'tdDay'			=> '', // ячейка TD
					
					'arrTdDay'		=> array(),
					
					'countAll'		=> 0, // счётчик приходов и уходов
					'countDay'		=> 0, //счётчик суточного времени

					'arrComing'		=> array(), // общее время приходов за день
					'arrAttendance'	=> array(),	// общее время уходов за день

					'premature'		=> '', // Преждевременный уход
					'prematureNo'	=> '',
					
					'DateChange'	=> array()
				);

				$this->CHANGE = array(
					'point'		=> ($this->thisday - 100000),
					'totalFact'	=> '',
					'totalGraf'	=> ''
				);

				$this->LUNCH = array();

				$this->PROCESSING = array(
					'processingDo'		=> 0,
					'processingAfter'	=> 0
				);

			}
			
			
			// подсчёт интервала (переодичность повторения графика)
			$this->interval = self::countInterval( $rowEmp );
			// определение выходного дня
			list($this->ofDay, $this->DATA_DAY) = self::ofDay( $rowEmp['type'] , $rowEmp );

			# вывод прихода и ухода
			foreach($this->tranzaction as $rowTranz)
			{
				if($rowTranz['id_employee'] == $rowEmp['id'])
				{
					if( date('d.m.Y', $this->thisday) == date('d.m.Y',$rowTranz['unixtime'] )) {

						if($rowEmp['grafic'] == 0) {
							$this->DATA_DAY  = self::userNoGrafic( $rowTranz);
						} else {
							switch ($rowEmp['type']) {
								// не суточный график
								case 1	: self::userGraficDay( $rowTranz, $rowEmp ); break;
								// суточный
								case 2	: self::userGraficAllDayNight( $rowTranz, $rowEmp );break;
								// сменный 
								case 3	: self::userGraficAllChange( $rowTranz, $rowEmp );break;
							}
						}
					# если не прошло условие, прирываю итерацию
					} else {
						continue;
					}
				# если не прошло условие, прирываю итерацию
				} else {
					continue;
				}
			}

			# ЕСЛИ НЕТ ГРАФИКА
			if( $rowEmp['grafic'] == 0 or empty($rowEmp['grafic']) ) {
				
				self::Type0graf();
				
			# ЕСЛИ ЕСТЬ ГРАФИК
			} else {
				switch ($rowEmp['type']) {

					# ДНЕВНОЙ ГРАФИК
					case 1	:	self::Type1graf( $rowEmp );	break;

					# СУТОЧНЫЙ ГРАФИК
					case 2	:	self::Type2graf( $rowEmp );	break;
					
					# СМЕННЫЙ ГРАФИК
					//case 3	:	self::Type3graf( $rowEmp );	break;
					//default : $this->DATA_DAY['tdDay'] = '<p>неизвестный график</p>';
				
				}
			}
			
			# ВЫВОД ИНФОРМАЦИИ В СТРОКУ
			switch ($rowEmp['type']) {
				case 3	:	
						self::Type3grafVisual( $rowEmp );
				break;
				case 2	:
				case 1	:
				default : $this->DATA_USER['td'] .= "<td>".$this->DATA_DAY['tdDay']."</td>";
			}
		}

		return $this->DATA_USER;
	}


	#||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
	#|||||     ФОРМИРОВАНИЕ ОТЧЁТА ПОСЕЩЕНИЯ ДЛЯ ПОЛЬЗОВАТЕЛЯ
	public function formationTrUser() {

		$j=1; $tr=''; 
		foreach ($this->employeeInfo as $rowEmp)
		{
			unset($this->DATA_USER);
			
			$this->DATA_USER = array
			(
				'td'			=> '',
				'countTd'		=> 0,
				'totalFact'		=> 0, //итоги по факту
				'totalFactD'	=> 0, 
				'totalGraf'		=> 0, //итоги по графику
				'totalGrafD'	=> 0, //итоги в днях

				'allDelayH'		=> 0, //все опоздания в часах
				'allDelayD'		=> 0, //все опоздания в днях

				'flawFactH'		=> 0, //все недоработки в часах
				'flawFactD'		=> 0, //все недоработки в днях

				'processingFactH'=> 0, //все переработки в часах
				'processingFactD'=> 0, //все переработки в днях

				'allComingH'	=> 0, //обещее время прихода в часах

				'allAttendanceH'=> 0, //общее время ухода в часах
				
				'processingDoD' => 0,
				'processingDoH'	=> 0,
				
				'processingAfterD' => 0,
				'processingAfterH' => 0,
				
				'tr'			=> array()
			);

			# ФОРМИРОВАНИЕ ОТЧЁТА ЗА ДЕНЬ ИЛИ ЗА СМЕНУ
			$this->DATA_USER = self::formationTdUser($rowEmp);

			# Формирование и вывод всех итогов
			$this->DATA_USER['totalH'] = Date_unix( $this->DATA_USER['allAttendanceH'] - $this->DATA_USER['allComingH'], 7 );
			$viewItog = self::viewItog('tr');
			
			if(!empty($this->DATA_USER['tr'])) {
				foreach ($this->DATA_USER['tr'] AS $row) {
					$this->DATA_USER['td'] .= $row;
				}
			} 
			//print_r($this->DATA_USER['tr']);
						
			// ФОРМИРОВАНИЕ СТРОКИ ПОЛЬЗОВАТЕЛЯ
			$tr .=
			"<tr>
				<td>". $j++ ."</td>
				<td>
					<p><b>". $rowEmp['fio'] ." </b></p> 
					<p>	". $rowEmp['name'] ." </p>
					<p>	". $rowEmp['depart'] ." </p>
					<p>	". $rowEmp['position'] ." </p>
					<p>	". $rowEmp['location'] ." </p>
				</td>
				". $this->DATA_USER['td'] ."
				". $viewItog ."
			</tr>";
		}

		return $tr;
	}


	#|||||     ФОРМИРОВАНИЕ ТАБЛИЦЫ
	private function formationFrameTable( $tr='') {

		// ФОРМИРОВАНИЕ ЗАГОЛОВОКА
		for ($i=0, $th=''; $i<= $this->daycount ;$i++) {
			$curdt = strtotime("+".$i." day", $this->dtStartStr);
			$NumDay = self::NumDay(date('w',$curdt));
			
			$th .= "<th>".date('d.m.Y', $curdt)." ". $NumDay ."</th>";
		}

		// ФОРМИРОВАНИЕ ТАБЛИЦЫ
		$answer = "
			<table class='table table-bordered'>
				<thead>
					<tr><th>№</th><th>Сотрудники</th>". $th . $viewItog = self::viewItog('th') ."</tr>
				</thead>
				<tbody>
					". $tr ."
				</tbody>
			</table>
		";

		return $answer;
	}


	#||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
	#|||||    ОБРАБОТКА ПРИШЕДШИХ ДАННЫХ И ВЫВОД ГОТОВОГО КОНТЕНТА
	#||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
	public function createdTable($dtStart, $dtFinish, $employee, $role, $category, $kpp, $location, $checked, $depart) {

		# условия вывода таблицы
		$this->viewParam = self::viewParam($checked);

		# привидение дат в нужный формат
		$this->dtStartStr = strtotime($dtStart);
		$this->dtFinishStr = strtotime($dtFinish);
		$this->daycount = ($this->dtFinishStr - $this->dtStartStr) / 86400;

		# ФИЛЬТР
		$filtrTranz		= self::filtrReportGraf($dtStart, $dtFinish, $employee, $role, $category, $kpp, $location, $depart);
		$filtrEmp		= self::filtrReportEmployee($dtStart, $dtFinish, $employee, $role, $category, $kpp, $location, $depart);

		# ВЫБОРКА ИЗ БД
		$employeeInfo	=	parent::DB_select('
								emp.id, emp.fio, emp.grafic_id AS `grafic`, emp.id_location,
								UNIX_TIMESTAMP(g.point) AS point, g.`for`, g.`name`, g.`type`,
								tt.`timeStart`,tt.`timeEnd`,tt.`startLunch`,tt.`endLunch`,
								d.`name` AS depart,
								p.`name` AS position,
								l.`name` AS location',
								's_employee as emp',
								'LEFT JOIN urv_graphic g ON g.id = emp.grafic_id
								LEFT JOIN (SELECT *	FROM urv_timetable) tt ON tt.id_graphic = emp.grafic_id
								LEFT JOIN (SELECT id,name FROM s_location) AS l ON l.id = emp.id_location
								LEFT JOIN (SELECT id,name FROM s_position) AS p ON p.id = emp.position
								LEFT JOIN (SELECT id,name FROM s_department) AS d ON d.id = emp.id_depart',
								$filtrEmp,
								' UPPER(emp.fio), tt.id','','not');
		//print_r($employeeInfo);
		$this->employeeInfo	= self::db2DBArray($employeeInfo);
		$this->tranzaction	= parent::DB_select('	ut.state, Unix_timestamp(ut.dt) AS `unixtime` , ut.id_employee, ut.id_location, l.name AS nameLoc',
													'd_urv_transactions ut',
													'LEFT JOIN (SELECT id, fio FROM s_employee) as emp ON emp.id = ut.id_employee
													LEFT JOIN (SELECT id, name FROM s_location) as l ON l.id = ut.id_location',
													$filtrTranz, 'ut.dt');
		//print_r($this->employeeInfo	);
		# ПОЛУЧЕНИЕ ДАННЫХ О ЗАДАНЫХ ПОЛЬЗОВАТЕЛЯХ
		$tr = self::formationTrUser();

		return self::formationFrameTable( $tr );
	}
}