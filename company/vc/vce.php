<?php

session_start();

$visitCardName = "";

if (isset($_SESSION['base_user'])){
    include('../check.php');
    include('../mysql.php');

    
    $visitCardName = substr($_SESSION['base_user'], 2);
}/* else {
    include "company/config.php";
    $db = mysql_connect('localhost',LOGIN,PASS) or die("Database error");
    
    $s = $_SERVER["HTTP_HOST"];
    $visitCardName = substr($s, 0, stripos($s, "."));

    $vceresult = mysql_query("SELECT `id`,`db`,`db_password`,`db_user`,`timezone` FROM `dbisoftik`.`s_accounts` WHERE UPPER(`username`)=UPPER('".$visitCardName."')");
    if (mysql_num_rows($vceresult) > 0){
        $vcerow = mysql_fetch_array($vceresult);
        $db = mysql_connect('localhost', $vcerow['db_user'], $vcerow['db_password']) or die("Database error");
        mysql_select_db($vcerow['db'], $db);
        mysql_query("set names 'utf8'");
        
        $contentId = isset($_GET["content"]) ? $_GET["content"] : 0;
        
        showCutaway(1, $contentId);
    } else die(header("Location: index/index.php"));
}*/

if ( !file_exists('../../visitcard/'.$visitCardName) ){
    mkdir('../../visitcard/'.$visitCardName);
}

if (isset($_GET["do"])){
    switch ($_GET["do"]){
        case "gettable":
            $tablename = isset($_POST['table']) ? ($_POST['table']) : ''; 
            
            if ($tablename == "visitcard"){
                
                $res=array();

                $res['name'] = "s_visitcards";
                $res['title'] = "Визитка";
                $res['create_group'] = "false";
                $res['width'] = "600";
                $res['height'] = "500";
                $res['rights'] = array('view'=>true,'edit'=>true,'add'=>true,'delete'=>true,'print'=>true);

                $vceresult = mysql_query("SELECT id, name, pagetitle, logo, phonenumbers, maincontent, footertext, colortheme, enable_im, im_menuid FROM s_visitcards LIMIT 1");
                if (mysql_num_rows($vceresult) > 0){
                    $vcerow = mysql_fetch_array($vceresult);
                    $vcid = "&id=".$vcerow["id"];
                    $vcpagename = $vcerow["name"];
                    $vcpagetitle = $vcerow["pagetitle"];
                    $vclogo = $vcerow["logo"];
                    $vcphonenumbers = $vcerow["phonenumbers"];
                    $vcmaincontent = $vcerow["maincontent"];
                    $vcfootertext = $vcerow["footertext"];
                    $vccolortheme = "";
                    $vctmpcolortheme = $vcerow["colortheme"];
                    $vcenableim = (intval($vcerow["enable_im"]) == 1) ? " checked " : "";
                    $vcimmenuid = intval($vcerow["im_menuid"]);
                    
                    $vcmenulist = "";
                    $menuresult = mysql_query("(SELECT 0 AS id, '' AS name) UNION ALL 
(SELECT id, name FROM s_menu ORDER BY name)");
                    if (mysql_num_rows($menuresult)){
                        while ($row = mysql_fetch_array($menuresult))
                            if (intval($row["id"]) === $vcimmenuid)
                                $vcmenulist .= '<option value="'.$row["id"].'" selected>'.$row["name"].'</option>';
                            else
                                $vcmenulist .= '<option value="'.$row["id"].'">'.$row["name"].'</option>';
                    }

                    if ($vctmpcolortheme == "")
                        $vccolortheme .= '<option value="" selected>Своя</option>';
                    else
                        $vccolortheme .= '<option value="">Своя</option>';
                    if ($vctmpcolortheme == "colorscheme-dark")
                        $vccolortheme .= '<option value="colorscheme-dark" selected>Темная</option>';
                    else
                        $vccolortheme .= '<option value="colorscheme-dark">Темная</option>';
                    if ($vctmpcolortheme == "colorscheme-red")
                        $vccolortheme .= '<option value="colorscheme-red" selected>Красный</option>';
                    else
                        $vccolortheme .= '<option value="colorscheme-red">Красный</option>';
                    if ($vctmpcolortheme == "colorscheme-orange")
                        $vccolortheme .= '<option value="colorscheme-orange" selected>Оранжевый</option>';
                    else
                        $vccolortheme .= '<option value="colorscheme-orange">Оранжевый</option>';
                    if ($vctmpcolortheme == "colorscheme-purple")
                        $vccolortheme .= '<option value="colorscheme-purple" selected>Фиолетовый</option>';
                    else
                        $vccolortheme .= '<option value="colorscheme-purple">Фиолетовый</option>';
                    if ($vctmpcolortheme == "colorscheme-green")
                        $vccolortheme .= '<option value="colorscheme-green" selected>Зеленый</option>';
                    else
                        $vccolortheme .= '<option value="colorscheme-green">Зеленый</option>';
                    if ($vctmpcolortheme == "colorscheme-blue")
                        $vccolortheme .= '<option value="colorscheme-blue" selected>Синий</option>';
                    else
                        $vccolortheme .= '<option value="colorscheme-blue">Синий</option>';                          
                } else {
                    $vcid = "";
                    $vcpagetitle = "";
                    $vclogo = "";
                    $vcphonenumbers = "";
                    $vcmaincontent = "Наберите текст Вашей страницы";
                    $vcfootertext = "";
                    $vccolortheme = "<option value='' selected>Своя</option>
<option value='colorscheme-dark'>Темный</option>
<option value='colorscheme-red'>Красный</option>
<option value='colorscheme-orange'>Оранжевый</option>
<option value='colorscheme-purple'>Фиолетовый</option>
<option value='colorscheme-green'>Зеленый</option>
<option value='colorscheme-blue'>Синий</option>";
                }
                
                $imglogo = '';
                if ($vclogo == '') $imglogo = '<img id="vcelogo" class="logo" alt="Логотип не выбран" />';
                else $imglogo = '<img id="vcelogo" class="logo" src="'.$vclogo.'" />';
// onclick="uploadImage(this);"
                $res['fields'] = '<form id="form-cutaway" method="post" action="vc/vce.php?do=saveCutaway'.$vcid.'">
<div class="vce"><div class="padDiv"><label for="colortheme">Цветовая схема:</label>
<select id="colortheme" name="colortheme">'.$vccolortheme.'</select>
</div>
<div class="padDiv"><label for="pagename">Имя визитки:</label><input type="text" id="pagename" name="pagename" value="'.$vcpagename.'" /></div>
<div class="padDiv"><label for="pagetitle">Заголовок страницы:</label><input type="text" id="pagetitle" name="pagetitle" value="'.$vcpagetitle.'" /></div>
<div class="padDiv"><label>Логотип:</label><input type="text" id="logo" name="logo" value="'.$vclogo.'" readonly="true" hidden/>'.$imglogo.'
        <div class="logoBtns">
        <input id="uploadBtn1" type="button" class="vcebutton logobtn" value="Загрузить файл"></input>
        <input id="selectBtn" type="button" class="vcebutton logobtn" value="Выбрать на сервере" onclick="selectImage();"></input>
        <input id="removeBtn" type="button" class="vcebutton logobtn red" value="Удалить" onclick="removeImage();"></input>
        </div>
    <div id="infoBox"></div>
</div>
<div class="padDiv"><label for="phonenumbers">Номера телефонов:</label><input type="text" id="phonenumbers" name="phonenumbers" value="'.$vcphonenumbers.'" /></div>
<div class="padDiv"><label for="enableim">Интернет-магазин:</label><input type="checkbox" id="enableim" name="enableim" value="1" '.$vcenableim.' /><label>Включить</label></div>
<div class="padDiv"><label for="immenu">Меню интернет-магазина:</label><select id="immenu" name="immenu">'.$vcmenulist.'</select></div>
<div class="padDiv"><label>Наполнение страницы:</label></div>
<div class="ckeDiv"><textarea id="contentEditor" name="maincontent">'.$vcmaincontent.'</textarea></div>
<div class="padDiv"><label for="footertext">Подвал:</label><textarea id="footertext" class="footertext" name="footertext">'.$vcfootertext.'</textarea></div>
<div class="padDiv"><input type="button" class="vcebutton green" value="Сохранить" onclick="saveCutaway();"/>
<a href="vc/vce.php?do=showCutaway'.$vcid.'" target="_blank"><input id="showCutaway" type="button" class="vcebutton" value="Показать визитку" /></a></div>
</div>
</form> 
<script>
var button = $("#uploadBtn1"), interval;
$.ajax_upload(button, {
    action : "/company/vc/vce.php?do=uploadImage",
    name : "file",
    onSubmit : function(file, ext) {
            $("#uploadBtn1").html("Загрузка...");
            this.disable();
    },
    onComplete: function(file, response) {
            $("#uploadBtn1").html("Загрузить");
            this.enable();
            console.log(response);
            response = $.parseJSON(response);
            if (response["response"] === "OK"){
                $("#vcelogo").attr("src", response["filename"]);
                $("#logo").val(response["filename"]);
            } else alert(response["response"]);
        }
    });                    
</script>';
            } else if ($tablename == "visitcard_contents"){
                $vcId = 1;
                $res['name'] = "visitcard_contents";
                $res['title'] = "Дополнительные страницы визитки";
                $res['create_group'] = "false";
                $res['width'] = "600";
                $res['height'] = "500";
                $res['rights'] = array('view'=>true,'edit'=>true,'add'=>true,'delete'=>true,'print'=>true);
                
                $menuList = getMenuList(1, 0, 0);
                
                $res['fields'] = '<div class="vce"><div id="contentList">'.$menuList.'</div>
<button id="addContentBtn" class="vcebutton green" onclick="addContent('.$vcId.');">Добавить страницу</button>
<div id="pce" style="display: none;">
<div class="padDiv"><label for="contentName">Наименование:</label><input id="contentName" name="contentname"></input></div>
<div class="padDiv"><label>Наполнение страницы:</label></div>
<div><textarea id="pageContentEditor" name="contenttext"></textarea></div>
<div class="padDiv"><button id="saveContentBtn" class="vcebutton green" onclick="saveContent(this);">Сохранить страницу</button>
<button id="removeContentBtn" class="vcebutton red" onclick="removeContent(this);">Удалить страницу</button></div>
</div>
</div>';
                
            } else if ($tablename == "visitcard_images"){
                $res['name'] = "visitcard_images";
                $res['title'] = "Изображения для визитки";
                $res['create_group'] = "false";
                $res['width'] = "600";
                $res['height'] = "500";
                $res['rights'] = array('view'=>true,'edit'=>true,'add'=>true,'delete'=>true,'print'=>true);
                
                $imagesList = loadImages("../../visitcard/".$visitCardName."/", 0);

                $res['fields'] = '<div class="vce">
                    <div class="padDiv">
                        <button id="uploadBtn2" class="vcebutton">Загрузить изображение</button>
                        <button id="removeButton" class="vcebutton" onclick="removeImage();">Удалить выбранные изображения</button>
                        <button class="vcebutton" onclick="selectAll(0);">Выделить все</button>
                        <button class="vcebutton" onclick="selectAll(1);">Снять выделение</button>
                    </div>
                    <div class="padDiv imagesList" id="imagesList" class="imagesList">'.$imagesList.'</div></div>
                    <script>
                        function checkSelection(){
                            $(this).toggleClass("selectedImage");
                        }
                        function selectAll(p){
                            if (p === 0){
                                $(\'#imagesList img\').addClass(\'selectedImage\');
                            } else {
                                $(\'#imagesList img\').removeClass(\'selectedImage\');
                            }
                        }
                        $("img").bind("click", checkSelection);                    

                        var button = $("#uploadBtn2"), interval;
                        $.ajax_upload(button, {
                            action : "/company/vc/vce.php?do=uploadImage",
                            name : "file",
                            onSubmit : function(file, ext) {
                                    $("#uploadBtn2").html("Загрузка...");
                                    this.disable();
                            },
                            onComplete: function(file, response) {
                                    $("#uploadBtn2").html("Загрузить");
                                    this.enable();
                                    console.log(response);
                                    response = $.parseJSON(response);
                                    if (response["response"] === "OK"){console.log(response["filename"]);
                                        $(\'<img class="" /*style="height: 100px; outline: 0; display: inline-block; margin: 5px 0 0 10px;"*/ src="\' + response["filename"] + \'" />\').appendTo("#imagesList").bind("click", checkSelection);
                                    } else alert(response["response"]);
                                }
                            });                    
                    </script>';
            }
            
            echo json_encode($res);
        break;
        case "getContent":           
            $cid = isset($_GET["id"]) ? $_GET["id"] : 0;
            
            if ($cid === 0)
                echo "error";
            else {            
                $answer = array();

                $vceresult = mysql_query("SELECT id, contentname, contenttext FROM t_visitcards WHERE id=".$cid);
                if (mysql_num_rows($vceresult)){
                    $vcerow = mysql_fetch_assoc($vceresult);
                    $answer["id"] = $vcerow["id"];
                    $answer["name"] = $vcerow["contentname"];
                    $answer["content"] = $vcerow["contenttext"];
                }
                echo json_encode($answer);
            }
        break;
        case "saveContent":
            $cId = isset($_GET["cId"]) ? addslashes($_GET["cId"]) : 0;
            $contentname = isset($_POST["name"]) ? addslashes($_POST["name"]) : "";
            $contenttext = isset($_POST["content"]) ? addslashes($_POST["content"]) : "";
            
            $vceresult = mysql_query("UPDATE t_visitcards SET contentname='".$contentname."', contenttext='".$contenttext."' WHERE id=".$cId);
            
            if ($vceresult) echo "Выполнено";
            else echo "Не выполнено";
        break;
        case "addContent":
            $vcId = isset($_GET["vcId"]) ? addslashes($_GET["vcId"]) : 0;
            if ($vcId === 0)
                echo "error";
            else {
                $vceresult = mysql_query("INSERT INTO t_visitcards (visitcardid, contentname, contenttext) 
                    VALUES (".$vcId.", 'Новая страница', '')");
                
                if ($vceresult){
                    $vceresult = mysql_query("SELECT MAX(id) AS mid FROM t_visitcards WHERE visitcardid=".$vcId);
                    $vcerow = mysql_fetch_array($vceresult);
                    
                    $answer = array();                     
                    $answer["id"] = $vcerow["mid"];
                    $answer["btn"] = '<button class="vcebutton" id="btncontent'.$vcerow["mid"].'" cid="'.$vcerow["mid"].'" onclick="getContent(this);">Новая страница</button>';
                    $answer["name"] = 'Новая страница';
                    $answer["content"] = '';

                    echo json_encode($answer);
                } else echo "error";
            }
        break;
        case "removeContent":
            $cId = isset($_GET["cId"]) ? addslashes($_GET["cId"]) : 0;
            
            $vceresult = mysql_query("DELETE FROM t_visitcards WHERE id=".$cId);
            
            if ($vceresult) echo "Выполнено";
            else echo "error";
        break;
        case "saveCutaway":
            $colortheme = isset($_POST["colortheme"]) ? addslashes($_POST["colortheme"]) : "";
            $name = isset($_POST["pagename"]) ? addslashes($_POST["pagename"]) : "";
            $pagetitle = isset($_POST["pagetitle"]) ? addslashes($_POST["pagetitle"]) : "";
            $logo = isset($_POST["logo"]) ? addslashes($_POST["logo"]) : "";
            $phonenumbers = isset($_POST["phonenumbers"]) ? addslashes($_POST["phonenumbers"]) : "";
            $maincontent = isset($_POST["maincontent"]) ? addslashes($_POST["maincontent"]) : "";
            $footertext = isset($_POST["footertext"]) ? addslashes($_POST["footertext"]) : "";
            $enableim = isset($_POST["enableim"]) ? addslashes($_POST["enableim"]) : "0";
            $immenu = isset($_POST["immenu"]) ? addslashes($_POST["immenu"]) : "0";
            
            if (isset($_GET["id"]))
                $vceresult = mysql_query("UPDATE s_visitcards SET colortheme='".$colortheme."', name='".$name."', pagetitle='".$pagetitle."', logo='".$logo."', phonenumbers='".$phonenumbers."',
                        maincontent='".$maincontent."', footertext='".$footertext."', enable_im=".$enableim.", im_menuid=".$immenu." WHERE id=".$_GET["id"]);
            else
                $vceresult = mysql_query("INSERT INTO s_visitcards SET colortheme='".$colortheme."', name='".$name."', pagetitle='".$pagetitle."', logo='".$logo."', phonenumbers='".$phonenumbers."',
                        maincontent='".$maincontent."', footertext='".$footertext."', enable_im=".$enableim.", im_menuid=".$immenu);
            
            if ($vceresult) echo "Выполнено";
            else echo "Не выполнено";
        break;
        case "showCutaway":
            $cId = isset($_GET["id"]) ? $_GET["id"] : 0;
            
            die( header( "Location: http://".$visitCardName.".isoftik.kz" ) );
            
            //showCutaway($cId, 0);
        break;
        case "uploadImage":
            echo uploadImage();
        break;
        case "removeImage":
            $data = $_POST["fnames"];
            $fnames = json_decode($data);

            foreach ($fnames as $fname){
                unlink($fname);
            }

            echo "done";
        break;
        case "getImagesList":
            $mode = isset($_GET['mode']) ? 0 : 1;
            $funcNum = isset($_GET['CKEditorFuncNum']) ? $_GET['CKEditorFuncNum'] : '';
            
            $okClick = '';
            $cancelClick = '';
            $click = '';
            $buttonstyle = '';
            $buttonstyle2 = '';
            if ($mode === 0){
                $okClick = 'closeImageList(1);';
                $cancelClick = 'closeImageList(0);';
                $click = '<script>
function checkSelection(){
    $(".imagesList img").removeClass("selectedImage");
    $(this).toggleClass("selectedImage");
}
$(".imagesList img").bind("click", checkSelection);
</script>';
                $buttonstyle = 'class="vcebutton green" ';
                $buttonstyle2 = 'class="vcebutton red" ';
            } else {
                $okClick = 'okFileBrowser();';
                $cancelClick = 'window.close();';
                $click = '<script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
<script>
function okFileBrowser(){ 
    var url = $("img.selectedImage").attr("src");
    window.opener.CKEDITOR.tools.callFunction('.$funcNum.', url, "");
    window.close();
}
$("img").bind("click", function(){
    $("img.selectedImage").attr("style", "height: 100px; outline: 0; display: inline-block; margin: 1em 0 0 1.2em;");
    $("img").removeClass("selectedImage");
    $(this).toggleClass("selectedImage");
    if ($(this).hasClass("selectedImage"))
        $(this).attr("style", "height: 100px; outline: 3px solid #a3a3a3; display: inline-block; margin: 1em 0 0 1.2em;"); 
});
$("button").mouseenter( function(){ $(this).css("opacity", "1"); } ).mouseleave( function(){ $(this).css("opacity", "0.8"); } );

</script>';
                $buttonstyle = 'style="display: inline-block;
background-color: #0A5BC4; color: #fff; border: none; font-size: 13px;
font-weight: 700; opacity: 0.8; margin: 1em 0 .5em 1.5em; padding: .5em 1.2em .5em ;
transition: 0.2s all ease; -moz-transition: 0.2s all ease; -webkit-transition: 0.2s all ease; 
-o-transition: 0.2s all ease; -ms-transition: 0.2s all ease; -webkit-touch-callout: none;
-webkit-user-select: none; -khtml-user-select: none; -moz-user-select: moz-none;
-ms-user-select: none; user-select: none;" ';
                $buttonstyle2 = $buttonstyle;
            }
            
            $images = loadImages("../../visitcard/".$visitCardName."/", $mode);
            
            $buttons = '<div class="padDiv" style="text-align: center;"><button '.$buttonstyle.'onclick="'.$okClick.'">OK</button>
                <button '.$buttonstyle2.'onclick="'.$cancelClick.'">Cancel</button></div>';
            echo '<div class="padDiv" style="margin: .5em 0 1em 0;"></div><div id="imagesList2" class="padDiv imagesList">'.$images.'</div>'.$buttons.$click; 
        break;
    }
}

function uploadImage(){
    $valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
    $answer = array();
    $answer["response"] = "Не получилось...";
    $uploadfile = basename($_FILES['file']['name']);
    $size = $_FILES['file']['size'];
    if ($size < (1024 * 1024)){
        $ext = substr($uploadfile, stripos($uploadfile, ".", 2) + 1);
        if (in_array($ext, $valid_formats)){
            $newname = time().rand(0, 100);
            $visitCardName = substr($_SESSION['base_user'], 2);
            $uploadfile = '../../visitcard/'.$visitCardName.'/'.$newname.'.'.$ext;
            if (move_uploaded_file($_FILES['file']["tmp_name"], $uploadfile)){
                $answer["response"] = "OK";
                $answer["filename"] = $uploadfile;
            } else $answer["response"] = "Не удалось сохранить файл.";
        } else $answer["response"] = "Неверный формат файла: ".$ext.". Разрешенные форматы: JPG, PNG, BMP, GIF.";
    } else $answer["response"] = "Размер файла ".$size." не должен превышать ".(1024 * 1024).".";
    return json_encode($answer);
}

function getMenuList($vcId, $cId, $mode){
    $answer = "";
    $vceresult = mysql_query("SELECT id, contentname FROM t_visitcards WHERE visitcardid=".$vcId);
    
    if (mysql_num_rows($vceresult) > 0){
        while ($vcerow = mysql_fetch_assoc($vceresult)){
            $active = '';
            if ($vcerow["id"] === $cId)
                $active = 'class="active"';
            if ($mode === 0)
                $answer .= '<button id="btncontent'.$vcerow["id"].'" class="vcebutton" cid="'.$vcerow["id"].'" onclick="getContent(this);">'.$vcerow["contentname"].'</button>';
            else
                $answer .= '<a id="mItem'.$vcerow["id"].'" href="'.$_SERVER["PHP_SELF"].'?content='.$vcerow["id"].'"><li '.$active.'>'.$vcerow["contentname"].'</li></a>';
        }
    }
    return $answer;
}

function loadImages($path, $mode){
    $valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
    $style = '';
    if ($mode === 1)
        $style = 'style="height: 100px; outline: 0; display: inline-block; margin: 1em 0 0 1.2em;" ';

    $answer = '';//'<div style="position: absolute; height: 90%; width: 90%; z-index: 9999" onclick="$(this).hide();">';
    
    $filesList = glob($path."*.*");
    
    if (is_array($filesList))
        foreach ($filesList as $fname){
                $ext = substr($fname, stripos($fname, ".", 5) + 1);
                if (in_array($ext, $valid_formats)){
                        $answer .= '<img class="" '.$style.'src="'.$fname.'" />';
                }
        }
    else $answer = '<p style="font-size: 18px; text-align: center; font-weight: 600;">Нет загруженных изображений!</p>';
    //$answer .= '/<div>';
    return $answer;
}

function showCutaway($cutawayId, $contentId){
    $vcId = isset($cutawayId) ? $cutawayId : 1;
    $cId = isset($contentId) ? $contentId : 0;

    if ($cId === 0)
        $vceresult = mysql_query("SELECT id, colortheme, pagetitle, logo, phonenumbers, maincontent, footertext FROM s_visitcards WHERE id=".$vcId);
    else
        $vceresult = mysql_query("SELECT vc.id, vc.colortheme, vc.pagetitle, vc.logo, vc.phonenumbers, c.contenttext AS maincontent, vc.footertext 
            FROM s_visitcards AS vc LEFT JOIN t_visitcards AS c ON c.visitcardid = vc.id 
            WHERE vc.id=".$vcId." AND c.id=".$cId);

    if (mysql_num_rows($vceresult) > 0){
        $vcerow = mysql_fetch_array($vceresult);
        $colortheme = $vcerow["colortheme"] ? $vcerow["colortheme"] : "";
        $colorthemeMain = $colortheme;
        if ($colortheme != '')
            $colortheme = ' class="'.$colortheme.'" ';
        $pagetitle = $vcerow["pagetitle"] != "" ? $vcerow["pagetitle"] : "";
        $logo = $vcerow["logo"] ? $vcerow["logo"] : "";
        if ($logo != "")
            $logo = '<a href="/"><img style="max-height: 100px; max-width: 150px;" src="'.$logo.'"></a>';
        else
            $logo = '<a href="/">Главная</a>';
        $phonenumbers = $vcerow["phonenumbers"] ? $vcerow["phonenumbers"] : "";
        
        $phonenumbers = explode(";", $phonenumbers);
        for ($i = 0; $i < count($phonenumbers); $i++){
            $s1 = $phonenumbers[$i];
            //$s2 = "<span>".substr($s1, 0, 2)."(".substr($s1, 2, 3).")</span>".substr($s1, 5, 3)."-".substr($s1, 8, 2)."-".substr($s1, 10, 2);
            $s2 = substr($s1, 0, 2)."(".substr($s1, 2, 3).")".substr($s1, 5, 3)."-".substr($s1, 8, 2)."-".substr($s1, 10, 2);
            $phonenumbers[$i] = $s2;
        }
        $phonenumbers = implode("<br />", $phonenumbers);
        
        $maincontent = $vcerow["maincontent"] ? $vcerow["maincontent"] : "";
        $footertext = $vcerow["footertext"] ? $vcerow["footertext"] : "";
        
        $menuList = getMenuList($vcId, $cId, 1);

        $vcebody = '<!DOCTYPE html PUBLIC>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>'.$pagetitle.'</title>
<link href="/visitcard/CSS/mainstyle.css" rel="stylesheet" type="text/css">
<link href="/visitcard/CSS/main-menu.css" rel="stylesheet" type="text/css">
<link href="/visitcard/CSS/colors.css" rel="stylesheet" type="text/css">
<script src="/visitcard/JS/jquery.js"></script>
<script src="/visitcard/JS/jquery-ui.js"></script>
<body '.$colortheme.'>
    <div class="main-head">        
        <p><ul class="my-menu">
            <div class="phone-block">'.$phonenumbers.'</div>'
            .$logo.$menuList.'
            <a id="mItemLast" href="http://paloma365.kz/"><li>Paloma365</li></a>
        </ul></p>
   </div>   
   <div class="main-content">'.$maincontent.'</div>
    <footer>
        <div class="main-footer">
            <a href="http://i.isoftik.kz/"><img src="/index/images/logo.png"></a>'.$footertext.'
        </div>
    </footer>
</body>';

        echo $vcebody;
    } else echo "Нет данных";    
}
?>
