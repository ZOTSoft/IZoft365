<?php

////////////////////////////////////////////
//////////   ОТЧЁТЫ ИСТОРИЯ   //////////////
////////////////////////////////////////////

//фильтр выборки данных истории посещения
function filtrReport($dtStart , $dtFinish , $employee, $state , $role , $category, $location , $kpp)
{  
	$filtr = '';
	
	if(!empty($employee)){
		//если переменная не пуста то присваиваем ей значение and через функцию
		$filtr = issetFiltrAND($filtr);
		$filtr .= " UPPER(emp.fio) IN (SELECT UPPER(emp.fio) FROM s_employee WHERE UPPER(emp.fio) LIKE UPPER('%".$employee."%') )";
	}
	
	if(!empty($state)){
		//если переменная не пуста то присваиваем ей значение and через функцию
		$filtr = issetFiltrAND($filtr);
		if($state == 2) {
			$state = 0;
		}
		$filtr .= " ut.state = ". $state;
	}
	
	if(!empty($role)){
		//если переменная не пуста то присваиваем ей значение and через функцию
		$filtr = issetFiltrAND($filtr);
		$filtr .= " UPPER(p.name) IN (SELECT UPPER(p.name) FROM s_position WHERE UPPER(p.name) LIKE UPPER('%".$role."%') )";
	}
	
	if(!empty($category)) {
		$filtr = issetFiltrAND($filtr);
		$filtr .= " emp.parentid = ". $category;
	}
	
	//фильтр локации (место положения точки урв)
	if(!empty($location)){
		$filtr = issetFiltrAND($filtr);
		$filtr .= " ut.id_location = ". $location;
	}

	//фильтр локации (место положения точки урв)
	if(!empty($kpp)){
		$filtr = issetFiltrAND($filtr);
		$filtr .= " ut.id_pointurv = ". $kpp ;
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
		
		$filtr .= " ut.dt BETWEEN '". $dtStart_filtr ."' AND '". $dtFinish_filtr ."' ";
	}
	
	return $filtr;
}

////////////////////////////////////////////
//////////   ТАБЕЛЬ ПОСЕЩЕНИЯ   ////////////
////////////////////////////////////////////

// выборка пользователя и его графика
function selectAllUserANDgrafic($employee='')
{
	if(!empty($employee))
		$filtr = "WHERE emp.id = ". $employee;
	else
		$filtr = '';
			
	$sql = "	
		SELECT	emp.id, emp.fio, emp.grafic_id AS grafic,
				tt.timeStart, tt.timeEnd, tt.name
		FROM
			s_employee as emp
		LEFT JOIN u_timeTable tt ON tt.id = emp.grafic_id
		". $filtr ."
		ORDER BY
			UPPER(emp.fio)";
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}

////////////////////////////////////////////
////////   Сотрудники на работе   //////////
////////////////////////////////////////////

function filtrEmpWork($dtStart, $dtFinish, $employee, $state, $role, $category, $location, $kpp) {
	$filtr = '';
	
	if(!empty($employee)){
		//если переменная не пуста то присваиваем ей значение and через функцию
		$filtr = issetFiltrAND($filtr);
		$filtr .= " UPPER(emp.fio) IN (SELECT UPPER(emp.fio) FROM s_employee WHERE UPPER(emp.fio) LIKE UPPER('%".$employee."%') )";
	}
	
	if(!empty($state)){
		//если переменная не пуста то присваиваем ей значение and через функцию
		$filtr = issetFiltrAND($filtr);
		if($state == 2) {
			$state = 0;
		}
		$filtr .= " ut.state = ". $state;
	}
	
	if(!empty($role)){
		//если переменная не пуста то присваиваем ей значение and через функцию
		$filtr = issetFiltrAND($filtr);
		$filtr .= " UPPER(p.name) IN (SELECT UPPER(p.name) FROM s_position WHERE UPPER(p.name) LIKE UPPER('%".$role."%') )";
	}
	
	if(!empty($category)) {
		$filtr = issetFiltrAND($filtr);
		$filtr .= " emp.parentid = ". $category;
	}
	
	//фильтр локации (место положения точки урв)
	if(!empty($location)){
		$filtr = issetFiltrAND($filtr);
		$filtr .= " ut.id_location = ". $location;
	}

	//фильтр локации (место положения точки урв)
	if(!empty($kpp)){
		$filtr = issetFiltrAND($filtr);
		$filtr .= " ut.id_pointurv = ". $kpp ;
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
		
		$filtr .= " ut.dt BETWEEN '". $dtStart_filtr ."' AND '". $dtFinish_filtr ."' ";
	}
	
	return $filtr;
}

function selectEmpWork( $where, $offset , $rowcount ){
	if(!empty($where)) {
		$where = "WHERE ".$where;
	}
	
	$sql = "SELECT MAX(ut.dt) AS dt, ut.state as state, emp.fio as name
			FROM d_urv_transactions ut
			LEFT JOIN (SELECT id, fio, position, parentid FROM s_employee) emp ON ut.id_employee = emp.id
			LEFT JOIN (SELECT id, name FROM s_employee) p ON p.id = emp.position
			".$where."
			GROUP BY emp.fio
			ORDER BY ut.dt DESC
			LIMIT ".$offset.",".$rowcount."";
	return $sql;
}

function countEmpWork( $where ){
	if(!empty($where)) {
		$where = "WHERE ".$where;
	}
	
	$sql = "SELECT COUNT(tt.countt)
			FROM (
				SELECT COUNT(emp.id) AS countt
				FROM d_urv_transactions ut
				LEFT JOIN (SELECT id, fio, position, parentid FROM s_employee) emp ON ut.id_employee = emp.id
				LEFT JOIN (SELECT id, name FROM s_employee) p ON p.id = emp.position
				".$where."
				GROUP BY emp.fio 
				ORDER BY ut.id DESC
				) AS tt";
	return $sql;
}

// выборка транзакций прихода/ухода сотрудников
function selectTraczationUser($filtr)
{
	$sql = "
		SELECT
			ut.state, Unix_timestamp(ut.dt) AS 'unixtime' , ut.id_employee
		FROM
			d_urv_transactions ut
			". $filtr ."
		ORDER BY
			ut.dt"; 
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}

// выборка графиков
function selectAllschedule()
{
	$sql = "SELECT
				tt.timeStart, tt.timeEnd, tt.id
			FROM
				u_timeTable tt
			ORDER BY
				tt.id"; 
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}

//
function selectInfoGrafic($dtStart, $dtFinish)
{
	$sql = "
		SELECT
			UNIX_TIMESTAMP(tab1.dt) as time,
			tab1.state,
			s_employee.id AS id_employee,
			s_employee.grafic_id AS grafic
		FROM
			d_urv_transactions AS tab1
		LEFT JOIN s_employee ON tab1.id_employee = s_employee.id
		WHERE
			(tab1.dt >= '" . date('Y-m-d', $dtStart) . "')
			AND(tab1.dt <= '" . date('Y-m-d', $dtFinish) . "')
		ORDER BY
			UPPER(s_employee.name),
			tab1.dt";
	$tr = mysql_query($sql);
	
	return $tr;
}

// выборка всех графиков
function selectAllGraphic()
{
	$sql = "SELECT
				`id`, `name`, `description`, `for`, `point`, `type`
			FROM
				urv_graphic
			ORDER BY id"; 
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);
}

// временные ограничения для выборки сотрудников
function TimeValidURV($param = '')
{
	$timeValid = 172800;
	switch ($param) {
		case 'valid'	:	return $timeValid;
		break;
	
		default			:
							$thisTime = date('U');
							$doTime = date('Y-m-d H:i:s',$thisTime);
							$toTime = date('Y-m-d H:i:s',($thisTime - $timeValid));

							return array($doTime, $toTime);
	}
}

// выборка сотрудников из точки УРВ
function selectEmployeeURVPoint( $sh , $urvid ) {
	list($doTime, $toTime) = TimeValidURV();
	
	# выборка пользователя
	$sql = "
	SELECT	emp.id, fio as `name`,
			tr.dt as dt, emp.id_location AS emp_local,
			IF(ISNULL(state), 0, state)AS state, tid,
			(
				SELECT id_location
				FROM s_pointurv pu
				WHERE pu.id = ". $urvid ."
				LIMIT 1
			) AS thisLocal
	FROM s_employee emp
	LEFT JOIN(
		(
			SELECT id AS tid, state, id_employee, dt
			FROM d_urv_transactions
			WHERE dt BETWEEN STR_TO_DATE('". $toTime ."', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('". $doTime ."', '%Y-%m-%d %H:%i:%s') 
				AND (
						SELECT id_location
						FROM s_pointurv pu
						WHERE pu.id = ".$urvid."
						LIMIT 1
					) = id_location AND (
						SELECT id
						FROM s_employee
						WHERE ident = '".$sh."'
						LIMIT 1
					)
			ORDER BY id DESC
			LIMIT 5000
		)AS tr
	)ON tr.id_employee = emp.id
	WHERE emp.ident ='". $sh ."'
	ORDER BY tid DESC
	LIMIT 1";
	return $sql;
	
	
	/*$sql = "
		SELECT	emp.id, fio as `name`,
				tr.dt as dt, emp.id_location AS emp_local,
				IF(ISNULL(state), 0, state)AS state, tid,
				(
					SELECT id_location
					FROM s_pointurv pu
					WHERE pu.id = ".$urvid."
					LIMIT 1
				) AS thisLocal
		FROM
			s_employee emp
			LEFT JOIN(
				(
					SELECT 	trans.id AS tid, state,	id_employee, dt
					FROM d_urv_transactions AS trans
					WHERE (
							SELECT id_location
							FROM s_pointurv pu
							WHERE pu.id = ".$urvid."
							LIMIT 1
						) = trans.id_location AND (
							SELECT id
							FROM s_employee
							WHERE password = '".$sh."'
							LIMIT 1
						)
				)AS tr
			)ON tr.id_employee = emp.id
		WHERE emp.password ='".$sh."'
		ORDER BY tid DESC
		LIMIT 1";
	return $MySQL->DB_sql_query($sql);*/
	
	/*	$sql = "
		SELECT	emp.id,
				fio as `name`,
				tr.dt as dt,
				IF(ISNULL(state), 0, state)AS state,
				tid
		FROM
			s_employee emp
			LEFT JOIN(
				(
					SELECT 	trans.id AS tid, state,	id_employee, dt
					FROM d_urv_transactions AS trans
					WHERE (trans.id_pointurv =".$urvid.")
						AND(
							SELECT id
							FROM s_employee
							WHERE password = '".$sh."'
						)
				)AS tr
			)ON tr.id_employee = emp.id
		WHERE emp.password ='".$sh."'
		ORDER BY tid DESC
		LIMIT 1";
	$result = mysql_query($sql) or die(mysql_error());
	//echo $sql;
	return $result;*/
}

// выборка сотрудников находящихся на работе
function selectEmployeeWorkThisURVpoint($idURV, $limit) {
	list($doTime, $toTime) = TimeValidURV();
	
	$sql = "
	SELECT  tab.id as id, tab.NAME as name, tab.state as state, tab.dt
	FROM
	(
		SELECT emp.id, emp.fio AS NAME, lastin.state, lastin.dt
		FROM s_employee emp
		LEFT JOIN(
			SELECT *
			FROM
			(
				SELECT max(ut.dt), ut.id_employee, ut.state, ut.dt AS dt, ut.id
				FROM d_urv_transactions ut
				WHERE (
					SELECT id_location
					FROM s_pointurv pu
					WHERE pu.id = ". $idURV ." 
					LIMIT 1
				) = ut.id_location
					AND
						ut.dt BETWEEN STR_TO_DATE('". $toTime ."', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('". $doTime ."', '%Y-%m-%d %H:%i:%s')
				GROUP BY ut.id_employee, ut.dt
				ORDER BY ut.dt DESC
				LIMIT 10000
			)AS t
			GROUP BY t.id_employee
		)AS lastin ON emp.id = lastin.id_employee
		WHERE state = 1
		ORDER BY emp.fio
	)AS tab
	WHERE state = 1 
	ORDER BY dt DESC
	LIMIT ". $limit ."
	";
	
	return $sql;
	
	/*$Flimit = '';
	if(!empty($limit)) {
		$Flimit = "LIMIT ". $limit;
	}
		
	$sql = "
	SELECT  tab.id as id, tab.NAME as name, tab.state as state, tab.dt
	FROM
	(
		SELECT emp.id, emp.fio AS NAME,
			 lastin.state,	lastin.dt
		FROM s_employee emp
			LEFT JOIN(
				SELECT *
				FROM
				(
					SELECT max(ut.dt), ut.id_employee, ut.state, ut.dt AS dt, ut.id
					FROM d_urv_transactions ut
					WHERE (
							SELECT id_location
							FROM s_pointurv pu
							WHERE pu.id = $idURV
							LIMIT 1
						) = ut.id_location
					GROUP BY ut.id_employee, ut.dt
					ORDER BY ut.dt DESC
				)AS t
				GROUP BY
					t.id_employee
			)AS lastin ON emp.id = lastin.id_employee
		WHERE state = 1
		ORDER BY emp.fio
	)AS tab
	WHERE state = 1
	ORDER BY dt desc
	". $Flimit .""; */
	
	//echo $sql; 
	/*SELECT max(ut.dt),  ut.id_employee, ut.state, ut.dt AS dt, ut.id
							FROM d_urv_transactions ut
							WHERE (
										SELECT id_location
										FROM s_pointurv pu
										WHERE pu.id = $idURV
										LIMIT 1
									) = ut.id_location
							GROUP BY ut.id_employee, ut.dt
							ORDER BY ut.dt DESC*/
	
	/*
	 $sql = "
	SELECT  tab.id as id, tab.NAME as name, tab.state as state, tab.dt
	FROM
	(
		SELECT emp.id, emp.fio AS NAME,
			IF(	CURRENT_DATE()= date(lastin.dt), state, NULL )AS state,	lastin.dt
		FROM s_employee emp
			LEFT JOIN(
				SELECT *
				FROM
				(
					SELECT ut.id_employee, ut.state, ut.dt AS dt, ut.id
					FROM d_urv_transactions ut
					WHERE (
							SELECT id_location
							FROM s_pointurv pu
							WHERE pu.id = $idURV
							LIMIT 1
						) = ut.id_location
					GROUP BY ut.id_employee, ut.dt
					ORDER BY ut.dt DESC
				)AS t
				GROUP BY
					t.id_employee
			)AS lastin ON emp.id = lastin.id_employee
		WHERE state = 1
		ORDER BY emp.fio
	)AS tab
	WHERE state = 1
	ORDER BY dt desc
	". $Flimit ."";
	 
	$result = mysql_query($sql) or die(mysql_error());
	return db2Array($result);*/
}

// все сотрудники на предприятии
function selectEmployeeWorkAll( $location ) {
	list($doTime, $toTime) = TimeValidURV();
	
	$sql = "
	SELECT  tab.id as id, tab.NAME as name, tab.state as state, tab.dt
	FROM
	(
		SELECT emp.id, emp.fio AS NAME, lastin.state, lastin.dt
		FROM s_employee emp
		LEFT JOIN(
			SELECT *
			FROM
			(
				SELECT max(ut.dt), ut.id_employee, ut.state, ut.dt AS dt, ut.id
				FROM d_urv_transactions ut
				WHERE	ut.id_location = ". $location ."
					AND
						ut.dt BETWEEN STR_TO_DATE('". $toTime ."', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('". $doTime ."', '%Y-%m-%d %H:%i:%s')
				GROUP BY ut.id_employee, ut.dt
				ORDER BY ut.dt DESC
				LIMIT 1000
			)AS t
			GROUP BY t.id_employee
		)AS lastin ON emp.id = lastin.id_employee
		WHERE state = 1
		ORDER BY emp.fio
	)AS tab
	WHERE state = 1
	ORDER BY dt DESC
	";
	
	return $sql;
}

// фильтр для выгрузки транзакций
function filtrFileURVTranzaction($dtStart,$dtFinish) {
	$filtr = '';
	//фильтр дата старта
	if((!empty($dtStart)) or (!empty($dtFinish))){
		$filtr = issetFiltrAND($filtr);

		if(!empty($dtStart)){
			$dtStart_filtr = formatDateRandomToTIMESTAMP($dtStart." 00:00:00", 1);
		}else{
			$dtStart_filtr = date("Y-m-d")." 00:00:00";
		}

		if(!empty($dtFinish)){
			$dtFinish_filtr = formatDateRandomToTIMESTAMP($dtFinish." 23:59:59", 1);
		}else{
			$dtFinish_filtr = date("Y-m-d")." 23:59:59";
		}

		$filtr .= " ut.dt BETWEEN STR_TO_DATE('". $dtStart_filtr ."', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('". $dtFinish_filtr ."', '%Y-%m-%d %H:%i:%s') ";
	}

	return $filtr;
}
?>
