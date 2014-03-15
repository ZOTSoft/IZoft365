<?
include("conn.php");

if (isset($_GET["do"])){    
    switch ($_GET["do"]){
        case "regCE":        $srls = $_POST["serials"];
                        $serials = explode(",", $srls);
                        
                        $answer = '<TABLE class="licenses">';
                        for ($i = 0; $i < count($serials); $i++){
                            $serials[$i] = trim($serials[$i]);
                            $result = mysql_query("SELECT id, license FROM tbserialsCE WHERE serial='".$serials[$i]."'") 
                                    or die('<TABLE class="licenses"><TR><TD>'.mysql_error().'</TD></TR><TR><TD>
                            <DIV id="okBtn" style="margin-top:20px"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="clearForm()"><b>OK</b></a></DIV>
                            </TD></TR></TABLE>');
                            if (mysql_num_rows($result) == 0) {
                                $answer .= '<TR><TD colspan="2">Серийный номер '.$serials[$i].' не найден.</TD></TR>';
                            } else {
                                $row = mysql_fetch_row($result);
                                $serialId = $row[0];
                                $license = $row[1];
                                $result = mysql_query("SELECT id FROM tblicensesCE WHERE serialId='".$serialId."'") 
                                        or die('<TABLE class="licenses"><TR><TD>'.mysql_error().'</TD></TR><TR><TD>
                            <DIV id="okBtn" style="margin-top:20px"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="clearForm()"><b>OK</b></a></DIV>
                            </TD></TR></TABLE>');
                                if (mysql_num_rows($result) > 0) {
                                    $answer .= '<TR><TD colspan="2">Код лицензии для серийного номера '.$serials[$i].' уже выдан.</TD></TR>';
                                } else {
                                    $result = mysql_query("INSERT INTO tblicensesCE (serialId, client, name, email, phone) 
                                        VALUES (".$serialId.", '".$_POST["client"]."', '".$_POST["name"]."', '".$_POST["email"]."', '".$_POST["phone"]."')") 
                                                or die('<TABLE class="licenses"><TR><TD>'.mysql_error().'</TD></TR><TR><TD>
                            <DIV id="okBtn" style="margin-top:20px"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="clearForm()"><b>OK</b></a></DIV>
                            </TD></TR></TABLE>');
                                    if ($result)
                                        $answer .= '<TR><TD>'.$serials[$i].'</TD><TD><a href="index/wince/actions.php?do=saveCE&lic='.$license.'" target="_blank"><img src="index/wince/images/printer.gif"></a></TD></TR>';
                                    else
                                        $answer .= '<TR><TD colspan="2">Не удалось получить код лицензии для серийного номера '.$serials[$i].'.</TD></TR>';
                                }
                            }
                        }
                        $answer .= '<TR><TD colspan="2">
                            <DIV id="okBtn" style="margin-top:20px"><a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="clearForm()"><b>OK</b></a></DIV>
                            </TD></TR></TABLE>';
                        echo $answer;
        break;
        case "saveCE":    if (isset($_GET["lic"])){
                            header("Content-Type: application/download\n"); 
                            header("Content-Disposition: attachment; filename=keyTSD.key");
                            echo iconv("UTF-8", "windows-1251", $_GET["lic"]);
                        }
        break;
    }
}
?>