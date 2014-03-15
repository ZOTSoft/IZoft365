<h2 class="zagolovok">Создание проекта</h2>
<div id="div_form_dz">
<?php  
if (isset($_POST['new_zadacha'])){
	$iniciator = $_SESSION['id_user'];
	$ispolnitel = $_POST['menu_is'];
	$prioritet = $_POST['prioritet'];
	
	if(isset($_POST['date_finish']) && !empty($_POST['date_finish'])){
			$date_elements  = explode("/",$_POST['date_finish']);
			$date_finish = mktime($date_elements[3],$date_elements[4],0,$date_elements[0],$date_elements[1],$date_elements[2]);
		}else{
			$date_finish = date("U");
	}

	$text_zadacha = htmlspecialchars(addslashes(trim($_POST['text_zadachi'])));
	$caption_zadacha = htmlspecialchars(addslashes(trim($_POST['caption_zadachi'])));
	$date_start = date('U');
			
	$insert = mysql_query("INSERT INTO crm_zadacha
								SET date_start = '$date_start',
								iniciator = '$iniciator',
								text_zadacha = '".mysql_real_escape_string($text_zadacha)."',
								ispolnitel = '$ispolnitel',
								date_finish = $date_finish,
								prioritet = '$prioritet',
								caption_zadacha = '$caption_zadacha' ");
	if(!$insert) { 
		return exit(mysql_error());
	}
	else{ 
		//$echo = "Данные добавлены успешно";
		echo "<script type='text/javascript'>window.location.replace('index.php?main=z&all=add_z')</script>";
	}	
} 
?>
<form method="post" class="form">
	<table class="table_form">
    	<tr><td><b>Инициатор: </b></td><td><?=$_SESSION['name'];?></td></tr>
        <tr><td colspan="2"><b>Название проекта</b></td></tr>
        <tr><td colspan="2"><textarea name="caption_p" rows=1 cols=60 required></textarea></td></tr>
        
        
        
        <tr><td><b>Исполнитель: </b></td><td><select name="menu_is" size="1">
    					<?php
							$select_u = mysql_query("SELECT id_user,I,F FROM crm_users ORDER BY id_user");
							if(!$select_u) die('Ошибка выборки'.mysql_error());
                        	while($row = mysql_fetch_array($select_u)){
							?>
        					<option value="<?=$row['id_user'];?>"><?=$row['F'];?> <?=$row['I'];?></option>
                        <?php
							}
						?>
                        </select></td></tr>
        <tr><td><b>Приоритет: </b></td><td><select name="prioritet" size="1">
    						<option selected value="low">низкий</option>
                            <option value="normal">обычный</option>
                            <option value="high">высокий</option>
        				 </select></td></tr>
        <tr><td><b>Сроки сдачи проекта</b></td><td><input type="text" name="date_finish" id="date_finish" value="" required/></td></tr>
        
        <tr><td colspan="2"><b>Описание задачи</b></td></tr>
        <tr><td colspan="2"><textarea name="text_zadachi" rows=4 cols=60 ></textarea></td></tr>
        <tr><td><input type="submit" name="new_zadacha" value="ОК" class="submit"/></td><td><input type="reset" value="Отчистить" class="submit"/></td></tr>
        <tr><td colspan="2"><?php if(isset($echo)){ echo $echo;}?></td></tr>
	</table>
</form>
</div>