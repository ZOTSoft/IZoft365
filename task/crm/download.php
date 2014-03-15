<?php
header("Content-Type: text/html; charset=utf-8");
include_once('../config.php');
include_once('../common.lib.php');
include_once('lib.inc.php');
if(!isset($_SESSION)){
	session_start();
} 

//определение максимально допустимого размера файла
$max_size = 1*1024*1024*20;
//(ini_get('post_max_size')) or (ini_get('upload_max_filesize')
/*if($_FILES['uploadFile']['size'] > $max_size)
{
	exit();	
}*/
if(isset($_FILES['uploadFile']['size']) && $_FILES['uploadFile']['size'] > $max_size)
{
	exit();
}
else
{	

	//проверка файлов на запрещённый расширения 
	//получаю расширение загружаемого файла
	if(isset($_FILES['uploadFile']['name']))
	{
		$path = getExtension1($_FILES['uploadFile']['name']);
	
		//массив запрещённых расширений
		$warning_path = array("php","php4","php3","phtml","pl");
		//проверка
		foreach($warning_path as $item)
		{
			//если раширение запрешённое, останавливаю работу скрипта
			if($item == $path)
			{
				exit();
			}
		}
	}

	# ||||||||||||||||||||||||||||||||||||
	# ЗАГРУЗКА ФАЙЛА ИЗ ОТЧЁТОВ ИЛИ ЗАДАЧ
	# ||||||||||||||||||||||||||||||||||||
	
	//если файл отправлен из отчётов или из задач
	if($_COOKIE['reqvestUrl'] == 'Z' or $_COOKIE['reqvestUrl'] == 'R' or $_COOKIE['reqvestUrl'] == 'SS')
	{
		
		//определение временной директории пока, отчёт не отправлен
		$folder_tmp = "../file_tmp/";
		
		//если папки с данным пользователем нет то создаём её
		if(!file_exists($folder_tmp))
		{
			mkdir($folder_tmp,0777,true);
		}
		
		if(isset($_FILES['uploadFile']['name']))
		{
			$uploadedFile = $folder_tmp.basename($_FILES['uploadFile']['name']); 
			//загружаем файл во временную папку
			if(is_uploaded_file($_FILES['uploadFile']['tmp_name'])
							 &&($_FILES['uploadFile']['type'])
							 &&($_FILES['uploadFile']['size'])
							 &&($_FILES['uploadFile']['name']))
			{ 
				if($_FILES['uploadFile']['error'] == 0)
				{ 
					//помещаем информацию о файле в сессию, чтобы при повторном проходе алгоритма вывести из в другой части кода
					if(move_uploaded_file($_FILES['uploadFile']['tmp_name'],$uploadedFile))
					{ 
						//помещаем информацию о файлах в сессиинный массив
						$_SESSION['massiv_file'][] = array($path,$_FILES['uploadFile']['name'],$_FILES['uploadFile']['size']);
					}  
					else
					{
						$err = 'Во  время загрузки файла произошла ошибка';
					}
				}
				else
				{ 
					$err = 'Ошибка';
				}
				return false;
			}
		}
		else
		{
			//присвоение переменных для добавления в базу
			$id_user = $_SESSION['id_user'];
			$date = date("U");
			
			//определение переменных для отчётов
			if($_COOKIE['reqvestUrl'] == 'R')
			{
				$row_id  = selectToIdToDsUser($id_user);
				$id_z = 0;
				$id_ds = $row_id;
				$id_ss = 0;
			}
	
			//определение переменных для заданий
			if($_COOKIE['reqvestUrl'] == 'Z')
			{
				//присвоение переменных для добавления в базу
				$row_id  = selectToIdToZUser($id_user);
				$id_z = $row_id;
				$id_ds = 0;
				$id_ss = 0;
			} 
			
			//определение переменных для СоцСетей
			if($_COOKIE['reqvestUrl'] == 'SS')
			{
				$row_id  = selectToIdToSSUser($id_user);
				$id_z = 0;
				$id_ds = 0;
				$id_ss = $row_id;
			}
			
			//берём из сессии информацию о ранее загруженых файлах
			$massiv_file = $_SESSION['massiv_file'];
			
			//проводим через цикл для получения ключей
			foreach($massiv_file as $massiv => $mas)
			{
				
				$arr = array();
				//присваиваем ключи массиву новому массиву
				foreach($mas as $m => $a)
				{
					$arr[] = $a;
				}
				
				//присваиваем из нового массива информацию по типу в соответствующюю переменную
				$type = $arr[0];
				$name = $arr[1];
				$size = $arr[2];
				
				//перемещаем файл
				$tmp_dir = $folder_tmp.$name;
				$dirs = "../../file_arhiv/".$_SESSION['email']."/".$name;
				
				//echo "type - $type || name - $name || size - $size || tmp_dir - $tmp_dir || dirs - $dirs || folder - $folder";
				
				rename($tmp_dir,$dirs);
				
				//диретория для записи в базу
				$dirs = "file_arhiv/".$_SESSION['email']."/";
				//добавляем его в базу
				loadFilesToBase($date,$dirs,$size,$type,$name,$id_user,$id_z,$id_ds,$id_ss);
			}
			if(!empty($_SESSION['msg_file']))
			{
				echo $_SESSION['msg_file'];
				unset($_SESSION['msg_file']);
			}
			unset($_SESSION['massiv_file']);
		}
		
	}
	else
	{
	
		# ||||||||||||||||||||||||||
		# 	ОБЫЧНАЯ ЗАГРУЗКА ФАЙЛА
		# ||||||||||||||||||||||||||
			
		//если файл отправлен не из отчётов то происходит обычная загрузка	
	
		//директория постоянного хранения файлов
		//$folder = "../../file_arhiv/".$_SESSION['email']."/";
		$folder = "../../file_arhiv/".$_SESSION['email']."/";
		
		//директоря для записи в базу
		$dirs = "file_arhiv/".$_SESSION['email']."/";
		
		//если папки с данным пользователем нет то создаём её
		if(!file_exists($folder))
		{
			mkdir($folder,0777,true);
		}
		
		//директория загрузки и имя файла
		$uploadedFile = $folder.basename($_FILES['uploadFile']['name']);
		
		if(is_uploaded_file($_FILES['uploadFile']['tmp_name'])
						 &&($_FILES['uploadFile']['size'])
						 &&($_FILES['uploadFile']['name']))
		{
			if($_FILES['uploadFile']['error'] == 0)
			{
				if(move_uploaded_file($_FILES['uploadFile']['tmp_name'],$uploadedFile))
				{
					$type = $path;
					$name = $_FILES['uploadFile']['name'];
					$size = $_FILES['uploadFile']['size'];
					
					$id_z = $_COOKIE['reqvestUrl'];
					$id_ds = 0;
					$id_ss = 0;
					$id_user = $_SESSION['id_user'];
					
					$err = 'Файл загружеh';
					$date = date("U");

					loadFilesToBase($date,$dirs,$size,$type,$name,$id_user,$id_z,$id_ds,$id_ss);
				}  
				else
				{
					$err = 'Во  время загрузки файла произошла ошибка';
				}
			}
			else
			{ 
				$err = 'Ошибка';
			}
		}
		else
		{
			$err = 'Файл не загружен';
		}
	}
}
?>