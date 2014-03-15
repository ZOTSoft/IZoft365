<?
require("class.phpmailer.php");

$mail = new PHPMailer();

$mail->IsSMTP();                                      // set mailer to use SMTP
$mail->Host = "213.180.193.38";  // specify main and backup server
$mail->Port = "25";  // specify main and backup server
$mail->SMTPDebug  = 2;
$mail->SMTPAuth = true;     // turn on SMTP authentication
$mail->Username = "noreply@isoftik.kz";  // SMTP username
$mail->Password = "N0r3ply"; // SMTP password

$mail->From = "noreply@isoftik.kz";
$mail->FromName = "ISOFTIK.kz";
$mail->AddAddress('zottig@bk.ru');
//$mail->AddAddress("ellen@example.com");                  // name is optional
//$mail->AddReplyTo("info@example.com", "Information");

//$mail->WordWrap = 50;                                 // set word wrap to 50 characters
//$mail->AddAttachment("/var/tmp/file.tar.gz");         // add attachments
//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");    // optional name
$mail->IsHTML(true);                                  // set email format to HTML

$mail->Subject = "Ключи активации OPH1005";
$mail->Body    = '12312312';
//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";

if(!$mail->Send())
{
   echo "Message could not be sent. <p>";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}

echo "Message has been sent";

?>