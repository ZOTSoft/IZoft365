<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Errors
 *
 * @author SunwelLight
 */
class Errors {
	// прифекс лол обозначает что была проверка на стороне клиента, но не прошла на стороне сервера
	
	private $error = "Код ошибки: ";
	private $cause = " Причина : ";
	private $text = '';

	public function __construct(){}
	
	public function PalomaErrors($kodeError, $dopInfo = '') {
		switch ($kodeError) {
			case 1		: $this->text = "данные на сервер переданы не были"; break;
			case 2		: $this->text = "неверный формат данных (ожидался массив)"; break;
			case 3		: $this->text = "нет СЕССИИ (пожалуйста авторизуйтесь)"; break;
			case 4		: $this->text = "нет баркода (возможно данные не были считаны)"; break;
			case 5		: $this->text = "выборка не была произведина (возможно введены неверные данные)"; break;
			case 6		: $this->text = $dopInfo ." нет такого сотрудника! (возможно введены неверные данные)"; break;
			case 7		: $this->text = "Нет сессии УРВ (возможно ошибка авторизации или нет точек УРВ)"; break;
			case 8		: $this->text = "Данные введены не корректно"; break;
			case 9		: $this->text = "Не все данные введены'"; break;
		
			case 'lol1'	: $this->text = "Ээ..не..не.. У вас нет прав для данного действия (права ограничены)"; break;
			case 'lol2'	: $this->text = "ой..ой..ой.. время установлено не правильно (время начала больше времени конца)"; break;
			case 'lol3'	: $this->text = "ой..ой..ой.. поля формы не заполнены (заполните поля)"; break;
			case 'lol4'	: $this->text = "ой..ой..ой.. таблица не заполнена (заполните таблицу)"; break;
			
			case 'outdated'	: $this->text = "данный код морально устарел, если вы дальше читаете этот текст, отправьте смс с текстом 'удали это сообщение' на номер 8777..."; break;
		}
		
		$this->error .= $kodeError . $this->cause . $this->text;
		
		return $this->error;
	}
}

?>
