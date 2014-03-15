<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Delivery
 *
 * @author SunwelLight
 */
class Delivery {

	public function __construct(){}
	
	public function createMessageEmailZ($to, $caption, $Finish, $answerPr, $answerIni, $text) {

		$body = "
		<html>
			<head>
				<title>$caption</title>

				<style type='text/css'>
					body{
						font-family: 'Trebuchet MS', sans-serif;
					}
					.caption{
						font-size: 1.3em;
					}
					.zadacha{
						font-size: 1.1em;	
					}
				</style>
			</head>
			<body>
				<p>Дата завершения: ". $Finish ."</p>
				<hr width='100%'/>
				<p>Приоритет: ". $answerPr ."</p>  
				<p>Инициатор: ". $answerIni ."</p>
				<p class='caption'>Название: 
					". $caption ."
				</p><br/>
				<p class='zadacha'>Описание: ". $text ."</p>
				<br />
				<p></p>
			</body>
		</html>";

		self::sendEmail($caption,$body,$to);
	}

	public function sendEmail($subject,$body,$to) {

		require("class/phpmailer.Class.php");
		$mail = new PHPMailer();
		$mail->CharSet = "UTF-8";
		$mail->IsSMTP();
		$mail->Host = "213.180.193.38";
		$mail->Port = "25";
		$mail->SMTPDebug  = 0;
		$mail->SMTPAuth = true;  
		$mail->Username = "crm@paloma365.kz";  
		$mail->Password = "crm.paloma365.kz"; 
		$mail->From = "crm@paloma365.kz";
		$mail->FromName = "paloma365.kz";
		$mail->AddAddress($to);
		$mail->IsHTML(true);
		$mail->Subject = $subject;
		$mail->Body    = $body;
		return $mail->Send();
	}
}

?>
