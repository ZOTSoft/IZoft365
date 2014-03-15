<?php

//фильтрация данных
function clearData($data, $type='s')
{
	switch($type){
		case 's'	: return mysql_real_escape_string(trim(strip_tags($data)));	break;
		case 'sf'	: return trim(strip_tags($data));							break;
		case 'i'	: return abs((int)($data));									break;
	}
}

//конвертируем данные в массив
function db2Array($data)
{
	$arr = array();
	while($row = mysql_fetch_assoc($data)){
		$arr[] = $row;
	}
	return $arr;
}

//конвертируем данные в строку
function db2String($data)
{
	return mysql_result($data,0);
}

//функция проверки фильтр пуст или нет
function issetFiltrAND($all_filtr)
{
	if ($all_filtr!=''){
		$all_filtr .= ' AND ';
		return($all_filtr);
	}
}

//Выборка пользователей для скиска сотрудников
function selectAllUsers()
{
	$sql = "SELECT id, fio
				FROM s_employee u
				ORDER BY u.fio ";
	$result = mysql_query($sql) OR die(mysql_error());
	return db2Array($result);
}

//функция форматирования дат в TIMESTAMP
function formatDateRandomToTIMESTAMP($date, $param = 1)
{
	switch ($param)
	{
		// dd.mm.YYYY HH:ii
		case 1 :	$test = explode(' ', $date);
					$d = explode('.', $test[0]);
					$t = explode(':', $test[1]);

					$year = (int)clearData($d[2],'i');
					$mount = (int)clearData($d[1],'i');
					$day = (int)clearData($d[0],'i');

					$hour = (int)clearData($t[0],'i');
					$minute = (int)clearData($t[1],'i');

					if(isset($t_el[2]))
						$sek = (int)clearData($t[2],'i');
					else
						$sek = '00';

					$result = $year ."-". $mount ."-". $day ." ". $hour .":". $minute .":". $sek;
		break;
		// Unix
		case 2 :	$result = date("Y-m-d H:i:s",$date);
		break;
	}
	return $result;
}


function ToNum($num) {
	if(iconv_strlen($num) < 2) {
		$num = '0'.$num;
	}
	return $num;
}
	
//форматирование воемени
function Date_unix($date='', $param='8')
{
	switch ($param) {
		
		#дд.мм.ГГ чч:mm 
		case '1' :	$dt = date("d.m.Y H:i", $date); 
					break;
		
		#чч:mm дд.мм.ГГ
		case '2' :	if(empty($date)) {
						$dt = date("H:i d.m.Y");
					} else {
						$dt = date("H:i d.m.Y", $date);
					}
					break;
					
		#чч:mm    //// Date_T
		case '3' :	if(empty($date))	{
						$dt = date("H:i");
					} else {
						$dt = date("H:i", $date);
					}
					break;
					
		#дд.мм.ГГ  /// Date_D
		case '4' :	if(empty($date)) {
						$dt = date("d.m.Y");
					} else {
						$dt = date("d.m.Y", $date);
					}
					break;
					
		# вывод интервала времени между Юникс меткой в формате чч:mm			
		case '5' : 
					$interval = $date[0] - $date[1];
		
					if(strlen($H = ( ( $interval/3600 )%24 ) ) < 2 ) {
						$H = '0'.$H;
					} else {
						$H = $H;
					}

					if(strlen( $I = ( ( $interval/60 ) % 60 ) ) < 2 ) {
						$I = '0'.$I;
					} else {
						$I = $I;
					}

					$dt = $H.":".$I;
					break;
					
		# вывод времени Юникс в формате чч:mm:ss		
		case '6' : 
					if(strlen($H = ( ( $date/3600 )%24 ) ) < 2 ) {
						$H = '0'.$H;
					} else {
						$H = $H;
					}

					if(strlen( $I = ( ( $date/60 ) % 60 ) ) < 2 ) {
						$I = '0'.$I;
					} else {
						$I = $I;
					}

					if(strlen($S = ( $date%60 ) ) < 2 ) {
						$S = '0'.$S;
					} else {
						$S = $S;
					}

					$dt = $H.":".$I.":".$S;
					break;
					
		# вывод времени Юникс в формате чч:mm:ss			
		case '7' : 
					$H = (int)($date/3600) ;
		
					if(strlen( $I = ( ( $date/60 ) % 60 ) ) < 2 ) {
						$I = '0'.$I;
					} else {
						$I = $I;
					}

					if(strlen($S = ( $date%60 ) ) < 2 ) {
						$S = '0'.$S;
					} else {
						$S = $S;
					}

					$dt = $H.":".$I.":".$S;
					break;
		
		# чч:mm:ss	
		case '8' :
					$dt = date("H:i:s", $date); 
					break;
				
		# из дд.мм.ГГГГ в YYYY-mm-ii 
		case '9' :	
					$d = explode('.', $date);

					$year = (int)clearData($d[2],'i');
					$mount = (int)clearData($d[1],'i');
					$day = (int)clearData($d[0],'i');

					$dt = $year ."-". $mount ."-". $day;
		break;
	
		# из YYYY-mm-ii в дд.мм.ГГГГ 
		case '10' :	
					$d = explode('-', $date);

					$year = (int)clearData($d[0],'i');
					$mount = (int)clearData($d[1],'i');
					$mount = ToNum($mount);
					$day = (int)clearData($d[2],'i');
					$day = ToNum($day);

					$dt = $day .".". $mount .".". $year;
		break;
	}
	
	return $dt;
}

# работа с временем , возвращает текущее значение указанного типа
function dateVerification($param) {
	switch ($param) {
		case 'H' :	
					$result = date('H');
					if(strlen($result) < 2) {
						$result = '0'.$result;
					}
					break;
		case 'i' :	
					$result = date('i');
			
					if(strlen($result) < 2) {
						$result = '0'.$result;
					}
					break;
		case 's': 
					$result = date('s');
			
					if(strlen($result) < 2) {
						$result = 's'.$result;
					}
					break;
		case 'Y' : 
					$result = date('Y');
					break;
		case 'mName' :	
					$monthNames = array( 
						'01' => "января", 
						'02' => "февраля", 
						'03' => "марта", 
						'04' => "апреля",
						'05' => "мая", 
						'06' => "июня", 
						'07' => "июля", 
						'08' => "августа", 
						'09' => "сентября", 
						'10' => "октября", 
						'11' => "ноября", 
						'12' => "декабря"
					); 
					$result = $monthNames[date('m')];
					break;
		case 'dName' :
					$dayNames = array("Понедельник - ","Вторник - ","Среда - ","Четверг - ","Пятница - ","Суббота - ", "Воскресенье - ");
					$result = $dayNames[date('N')];
					break;
		case 'numDay' :
					$result = date('j');
					break;
		default : 	$result = '';
	}
	
	return $result;
}

//функция форматирования UNIX в норм. время
function formatUnixRandomToDATE($date='', $param=1)
{
	switch ($param)
	{
		// перевод чч:mm:cc в секунды
		case 1 :
				$t_el = explode(":",clearData($date));
				$result = (int)clearData(abs($t_el[2]),'i') + (((int)clearData(abs($t_el[1]),'i') + ((int)clearData(abs($t_el[0]),'i') * 60)) * 60);
				
				//$result = mktime($hour,$minute,$sek,0,0,0);
		break;
		// dd:mm:YY  в  DATE
		case 2 :
				$t_el = explode(".",clearData($date));
				$result = clearData($t_el[2],'i')."-".clearData($t_el[1],'i')."-".clearData($t_el[0],'i');
		break;
	}

	return $result;
}

// функция 
function encodestring($st)
{
    // Сначала заменяем "односимвольные" фонемы.
    $st=strtr($st,"абвгдеёзийклмнопрстуфхъыэ_","abvgdeeziyklmnoprstufh'iei");
    $st=strtr($st,"АБВГДЕЁЗИЙКЛМНОПРСТУФХЪЫЭ_","ABVGDEEZIYKLMNOPRSTUFH'IEI");
    // Затем - "многосимвольные".
    $st=strtr($st, 
                    array(
                        "ж"=>"zh", "ц"=>"ts", "ч"=>"ch", "ш"=>"sh", 
                        "щ"=>"shch","ь"=>"", "ю"=>"yu", "я"=>"ya",
                        "Ж"=>"ZH", "Ц"=>"TS", "Ч"=>"CH", "Ш"=>"SH", 
                        "Щ"=>"SHCH","Ь"=>"", "Ю"=>"YU", "Я"=>"YA",
                        "ї"=>"i", "Ї"=>"Yi", "є"=>"ie", "Є"=>"Ye"
                        )
             );
    // Возвращаем результат.
    return $st;
}

// универсальная обработка пришедших данных
function universalProsessingData( $object ) {
	$reception = array();
	foreach ($object AS $name => $key) {

		switch ($key) {
			case is_int($key)	:	$reception[$name] = !empty($key) ? $reception[$name] = clearData($key, 'i') : $reception[$name] = '';	break;
			case is_array($key) :	$reception[$name] = universalProsessingData($key);														break;
			default				:	$reception[$name] = !empty($key) ? $reception[$name] = clearData($key) : $reception[$name] = '';		break;
		}

	}
	return $reception;
}

//определение ИД пользомателя
function definitionSESSION()
{
	if(empty($_SESSION['admin']))
	{
		$id_user = $_SESSION['userid'];
	}
	else
	{
		$id_user = 0;
	}
	return $id_user;
}

//определение к какой группе относятся пользователи
function definitionUserGroup()
{
	// 1 ADMIN
	// 2 MENAGER
	// 3 USER
	
	$id_user = definitionSESSION();
	
	if($id_user == 0){
		$mgrNumber = 1;
	} else {
		$sql = "SELECT id_mgr
				FROM crm_mgr_view
				WHERE id_mgr = ". $id_user ."
				LIMIT 1";
		$result = mysql_query($sql) or die(mysql_error());
		if (mysql_num_rows($result) > 0) {
			$mgrNumber = 2;
		} else {
			$mgrNumber = 3;
		}
	}
	
	return $mgrNumber;
	/*	
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
	
	return $numberGroup;*/
}

//функция заполнения данными БД
function insertDataRandomToBDclient($number)
{
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
		
		$ispolnitel = $m_ispolnitel[array_rand($m_ispolnitel)];
		$insert .= "('".$I[array_rand($I)] . " " . $F[array_rand($F)]."',
					'".rand(1960,2000)."-".rand(1,12)."-".rand(1,31)."',
					'".$I[array_rand($I)].$ispolnitel."@test.ru',
					'". rand(1000,87779981) ."'),
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