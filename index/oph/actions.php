<?

include("conn.php");

if (isset($_GET["do"])){    
    switch ($_GET["do"]){
        case "reg":    $srls = $_POST["serials"];
                    $serials = explode(",", $srls);
                    
                    $answer = '<TABLE class="licenses">';
                    for ($i = 0; $i < count($serials); $i++){
                        $serials[$i] = trim($serials[$i]);
                        $result = mysql_query("SELECT id, license FROM tbserials WHERE serial='".$serials[$i]."'");
                        if (mysql_num_rows($result) == 0) {
                            $answer .= '<TR><TD colspan="2">Серийный номер '.$serials[$i].' не найден.</TD></TR>';
                        } else {
                            $row = mysql_fetch_row($result);
                            $serialId = $row[0];
                            $license = $row[1];
                            $result = mysql_query("SELECT id FROM tblicenses WHERE serialId='".$serialId."'");
                            if (mysql_num_rows($result) > 0) {
                                $answer .= '<TR><TD colspan="2">Код лицензии для серийного номера '.$serials[$i].' - '.$license.' уже выдан.</TD></TR>';                                
                            } else {
                                $result = mysql_query("INSERT INTO tblicenses (serialId, client, name, email, phone) 
                                    VALUES (".$serialId.", '".$_POST["client"]."', '".$_POST["name"]."', '".$_POST["email"]."', '".$_POST["phone"]."')");
                                $answer .= '<TR><TD>'.$serials[$i].'</TD><TD>'.$license.'</TD></TR>';                                
                            }
                        }
                    }
//                                ///////////////////////////////////////                               
                                
require("class.phpmailer.php");

$mail = new PHPMailer();

$mail->IsSMTP();                                      // set mailer to use SMTP
$mail->Host = "93.158.134.38";//"smtp.yandex.ru";  // specify main and backup server
$mail->SMTPAuth = true;     // turn on SMTP authentication
$mail->Username = "noreply@paloma365.kz";  // SMTP username
$mail->Password = "noreply.paloma365.kz"; // SMTP password

$mail->From = "noreply@paloma365.kz";
$mail->FromName = "Paloma365.kz";
$mail->AddAddress($_POST["email"]);
$mail->CharSet = "UTF-8";
//$mail->AddAddress("ellen@example.com");                  // name is optional
//$mail->AddReplyTo("info@example.com", "Information");

$mail->WordWrap = 50;                                 // set word wrap to 50 characters
//$mail->AddAttachment("/var/tmp/file.tar.gz");         // add attachments
//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");    // optional name
$mail->IsHTML(true);                                  // set email format to HTML

$mail->Subject = "Ключи активации для ISOFT ТСД для Dos модель OPH1005";
$mail->Body    = $answer;
$mail->AltBody = "This is the body in plain text for non-HTML mail clients";

if(!$mail->Send())
{
   echo "Неудалось отправить письмо. <p>";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}

echo "Письмо отправлено на Вашу почту";

                                ///////////////////////////////////////
                    $answer .= '<TR><TD colspan="2">
                        <DIV id="okBtn" style="margin-top:20px"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="clearForm()"><b>OK</b></a></DIV>
                        </TD></TR></TABLE>';

                    echo $answer;
        break;
    }
}
?>