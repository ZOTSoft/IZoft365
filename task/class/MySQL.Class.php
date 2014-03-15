<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MySQL
 *
 * @author SunwelLight
 */

class MySQL {
	
	public $sql;
	public $debug = FALSE;
	public $error = FALSE;
	public $answer;	
	
	// создание класса, (по умолчанию вывод ошибок отключен)
	public function __construct( $error = FALSE ) {
		$this->error = $error;
	}
	
	//конвертируем данные в массив
	private function db2Array($data) {
		$arr = array();
		while($row = mysql_fetch_assoc($data)){
			$arr[] = $row;
		}
		return $arr;
	}

	//конвертируем данные в строку
	private function db2String($data) {
		return mysql_result($data,0);
	}
	
	// просмотр выборки
	private function showInfo($sql) {
		echo "<h1>запрос в Mysql</h1><hr /><pre>". $sql ."</pre>";
	}
	
	// просмотр ишбки выборки
	private function showError($err) {
		echo '<hr /><h1>Ошибка в запросе </h1><br /><pre>'. $err .'</pre><hr />';
	}
	
	// обработка выборки
	private function SQL_proccess($debag, $typeView='') {
		
		switch ($debag) {
			case 0 :	
						if($this->error) {
							$result = mysql_query($this->sql) OR die( self::showError(mysql_error()) . self::showInfo($this->sql) );
						} else {
							$result = mysql_query($this->sql);
						}
						
						if(isset($typeView)) {
							switch ($typeView) {
								case 'arr'	:	$this->answer = self::db2Array($result); break;
								case 'str'	:	$this->answer = self::db2String($result);break;
								case 'not'	:	$this->answer = $result;break;
								default : break;
							} //print_r($this->answer);
							return $this->answer;
						}
			break;
			
			case 1 :	self::showInfo($this->sql);						
			break;
			
			case 2 :	fb($this->sql);	
			break;
		}
		
	}
	
	// шаблон SELECT
	public function DB_select($data, $table, $inner='', $where='', $orderBy='', $limit='', $typeView='arr', $debag=0) {
		
		if(is_array($inner)) {
			foreach ($inner AS $row) {
				if($row == 'LEFT') {
					$join .= "LEFT JOIN ". $row ." ";
				}
				if($row = 'INNER') {
					$join .= "INNER JOIN ". $row ." ";
				}
				if($row = 'RIGHT') {
					$join .= "RIGHT JOIN ". $row ." ";
				}
			}
			$inner = $join;
		}
		
		if(!empty($where))
			$where = "WHERE ". $where;
		if(!empty($orderBy))
			$orderBy = "ORDER BY ". $orderBy;
		if(!empty($limit))
			$limit = "LIMIT ". $limit;
		
		$this->sql = "
		SELECT ". $data ."
			FROM ". $table ."
				". $inner ."
			". $where ."
			". $orderBy ."
			". $limit ."
		";
		
		return self::SQL_proccess($debag, $typeView);
	}
	
	// шаблон INSERT
	public function DB_insert($table, $template, $data, $debag=0) {
		$this->sql = "
		INSERT INTO ". $table ."
				( ". $template ."
				) VALUES 
				  ". $data	;
		
		return self::SQL_proccess($debag);
	}
	
	// шаблон UPDATE
	public function DB_update($table, $data, $where, $debag = 0) {
		$this->sql = "
		UPDATE ".  $table ."
			SET
				". $data ."
			WHERE ". $where 
		;
		
		self::SQL_proccess($debag);
	}
	
	// шаблон DELETE
	public function DB_delete( $table , $where = '' , $debag = 0 ) {
		if(!empty($where))
			$where = "WHERE ". $where;
		
		$this->sql = "
		DELETE FROM ". $table ."
				". $where ;
		
		return self::SQL_proccess($debag);
	}
	
	// говый запрос для обработки
	public function DB_sql_query( $sql , $debag = 0, $typeView='arr' ) {
		$this->sql = $sql;
		return self::SQL_proccess( $debag, $typeView );
	}
	
	// подключение к базе
	public function DB_conect( $host, $user, $pass, $time, $base ) {
		
		@mysql_connect($host, $user, $pass) or die ("MySQL Error: " . mysql_error());
		@mysql_query("set names utf8") or die ("<br>Invalid query: " . mysql_error());
		if (isset($time)){ 
			date_default_timezone_set($time); 
			mysql_query("SET `time_zone` = '".date('P')."'"); 
		}     
		@mysql_select_db($base) or die("<br>Invalid query: " . mysql_error());
	}
}