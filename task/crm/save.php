<?php
include_once('../../include/config.php');
include_once('../../lib.inc.php');
header("Content-Type: text/html; charset=utf-8");

if(isset($_GET['file']) && ($_GET['file'] == 's'))
{
	$id = $_GET['id_f'];
	
	$r = selectInfoUploadFile($id);
	
	foreach($r as $row)
	{
		$filename = "../../".$row['dirs'].$row['name'];
	}

 // нужен для Internet Explorer, иначе Content-Disposition игнорируется
	if(ini_get('zlib.output_compression'))
	{
	  	ini_set('zlib.output_compression', 'Off');
	}
	
	$file_extension = strtolower(substr(strrchr($filename,"."),1));
	 
	if( $filename == "" )
	{
		echo "ОШИБКА: не указано имя файла.";
		exit;
	} 
	elseif ( ! file_exists( $filename ) ) // проверяем существует ли указанный файл
	{
		echo "ОШИБКА: данного файла не существует.";
		exit;
	};
	switch( $file_extension )
	{
		case "pdf": $ctype="application/pdf"; break;
		case "exe": $ctype="application/octet-stream"; break;
		case "zip": $ctype="application/zip"; break;
		case "doc": $ctype="application/msword"; break;
		case "xls": $ctype="application/vnd.ms-excel"; break;
		case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
		case "mp3": $ctype="audio/mp3"; break;
		case "gif": $ctype="image/gif"; break;
		case "png": $ctype="image/png"; break;  
		case "jpeg":
		case "jpg": $ctype="image/jpg"; break;
		default: $ctype="application/force-download";
	}
	header("Pragma: public"); 
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false); // нужен для некоторых браузеров
	header("Content-Type: $ctype");
	header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
	header("Content-Transfer-Encoding: binary");
	//header("Content-Length: ".filesize($filename)); // необходимо доделать подсчет размера файла по абсолютному пути
	readfile("$filename");
	exit();
}
else
{
	echo '<h1>ERROR 404</h1>';	
}
?>