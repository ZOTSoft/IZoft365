<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of formationContent
 *
 * @author SunwelLight
 */
class formationContent extends MySQL{
	public $form;
	public $cont;

	public function createdForm($param, $array='') {
		switch ($param) {
			
			#######################################
			#####			ПАНЕЛЬ
			#######################################
			case 'toolbar'	:	
					$this->form = "<div class='panel_toolbar'>";
				
					if( is_array($array) ) {
						foreach ( $array as $row ) {
							switch ($row) {
								case 'create' :
										$this->form .= "<a class='btn btn-default dropdown-toggle' href='#' name='create' data-toggle='button'>Создать</a>";
								break;
								case 'edit' :
										$this->form .= "<a class='btn btn-default dropdown-toggle' href='#' name='edit' data-toggle='button'>Изменить</a>";
								break;
								case 'add' :
										$this->form .= "<a class='btn btn-default dropdown-toggle' href='#' name='add' data-toggle='button'>Добавить</a>";
								break;
								case 'del' :
										$this->form .= "<a class='btn btn-default dropdown-toggle' href='#' name='del' data-toggle='button'>Удалить</a>";
								break;
								case 'filtr' :
										$this->form .= "<a class='btn btn-default dropdown-toggle' href='#' name='loadfiltr' data-toggle='button'><span class='glyphicon glyphicon-filter'></span>фильтр</a>";
								break;
								case 'message' :
										$this->form .= "<a class='btn btn-default dropdown-toggle' href='#' name='message' data-toggle='button'>Сообщение</a>";
								break;
								case 'loadmask' :
										$this->form .= "<a class='btn btn-default dropdown-toggle' href='#' name='loadmask' data-toggle='button'><span class='glyphicon glyphicon-filter'></span>маски</a>";
								break;
							}
						}
					}
					$this->form .= "</div>";	
			break;
		
			#######################################
			#####		МОДАЛЬНОЕ ОКНО
			#######################################
			case 'modal'	:	
					$button = '';
					$valId = '';
					if(!empty($array['button'])) {
						$button = "<button type='button' class='btn btn-primary' name='save'>Сохранить</button>";
					}
					if(!empty($array['valId'])) {
						$valId = "value='". $array['valId'] ."' ";
					}
					
					$this->form  = "
					<div class='modal fade' id='". $array['id'] ."' ". $valId .">
						<div class='modal-dialog' style='width:". $array['width'] ."px'>
							<div class='modal-content'>
								<div class='modal-header'>
								<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
								<h4 class='modal-title'>". $array['header'] ."</h4>
							</div>
							<div class='modal-body'>
								". $array['content'] ."
							</div>
							<div class='modal-footer'>
								<button type='button' class='btn btn-default' data-dismiss='modal'>Закрыть</button>
								". $button ."
							</div>
						 </div>
						</div>
					</div>
					";
			break;
		
			#######################################
			#####		ФОРМА ВВОДА ВРЕМЕНИ
			#######################################
			case 'dtime'	:	
					$name = ''; $type = ''; $placeholder = ''; $value = '';
					if( is_array($array) ) {
						foreach ( $array as $row => $key ) {
							switch ($row) {
								case 'name'			: $name			= $key;	break;
								case 'type'			: $type			= $key;	break;
								case 'ph'			: $placeholder	= $key;	break;
								case 'value'		: $value		= $key;	break;	
							}
						}
					}
					$this->form  = "
						<div class='dpicker'>
							<div class='input-group date'>
								<input class='form-control' type='text' name='".$name."' data-format='".$type."' placeholder='".$placeholder."' value='".$value."'>
								<span class='input-group-addon'>
									<span class='glyphicon glyphicon-calendar'></span>
								</span>
							</div>
						</div>
					";
			break;
		
			#########################################
			######  ГОРИЗАНТАЛЬНАЯ ПАГИНАЦИЯ
			#########################################
			case 'pagiLine' :
					$countPage = 0; $count = 0; $nameCount = ''; $value = 1;
					
					if( is_array($array) ) {
						foreach ( $array as $row => $key ) {
							switch ($row) {
								case 'nameCount'	: $nameCount	= $key;	break;
								case 'countPage'	: $countPage	= $key;	break;
								case 'count'		: $count		= $key;	break;
								case 'value'		: $value		= $key;	break;	
							}
						}
					}
					
					$this->form  = "
						<div class='scroll_bar'>
							<select name='selCount' >
								<option value='50'>50</option>
								<option value='100'>100</option>
								<option value='200'>200</option>
							</select>

							<div id='prev'>
								<img src='/task/crm/images/arrow-left.png' width=20 height=20>
							</div>
							<label>стр. </label>
							<input type='text' name='thispage' value='". $value ."' class='text'/>
							<label class='count_page'> из ". $countPage ."</label>
							<div id='next'>
								<img src='/task/crm/images/arrow-right.png' width=20 height=20>
							</div>
							<label class='count_z'>".$nameCount." ". $count ."</label>
						</div> ";
			break;
		
			#########################################
			######   ЭЛЕМЕНТ ФИЛЬТРА
			#########################################
			case 'fBlocks' :
				$label = ''; $name = ''; $data = ''; $type = '';
				
				if( is_array($array) ) {
						foreach ( $array as $row => $key ) {
							switch ($row) {
								case 'label'	:	$label	= $key;	break;
								case 'name'		:	$name	= $key;	break;
								case 'data'		:	$data	= $key;	break;
								case 'type'		:	switch ($key) {
														case 'input' : if(empty($data)) { $val = ''; } else { $val = "value='".$data."'"; }
																		$type = "<input type='text' name='". $name ."'' class='form-control' ".$val." />"; break;
														case 'select': $type = "<select name='". $name ."' class='form-control'>". $data ."</select>"; break;
													}	
													break;	
																
							}
						}
					}
				
				$this->form = "
					<div class='zfilter'>
						<label>". $label ."</label>
						". $type ."
					</div>";
			break;
		
			#########################################
			######   СПИСОК ДЛЯ СЕЛЕКТА
			#########################################
			case 'fSelect' :
				
				$this->form = "<option value=''></option>";
				foreach ($array as $row)
				{
					$this->form .= "<option value='". $row['id'] ."'>". $row['name'] ."</option>";
				}
				
			break;
		}
		
		return $this->form;
	}
	
	public function createdFiltr($param, $array='') {
		$contfiltr = '';
		switch ($param) {
			
				/////////////////////////////////////////////////////
				/////			 ФИЛЬТР УРВ ТАБЕЛЬ
				////////////////////////////////////////////////////
			case 'urvFiltr' :
				
					$All_category = parent::DB_select('name, id', 's_employee','','isgroup != 0');
					$category = "<option value=''></option>";
					foreach($All_category AS $row) {
						$category .= "<option value='".$row['id']."'>". $row['name'] ."</option>";
					}
					$All_location = parent::DB_select('id, name','s_location');
					$location = "<option value=''></option>";
					foreach($All_location AS $row) {
						$location .= "<option value='".$row['id']."'>". $row['name'] ."</option>";
					}
					$All_KPP = parent::DB_select('id, name','s_pointurv');
					$KPP = "<option value=''></option>";
					foreach($All_KPP AS $row) {
						$KPP .= "<option value='".$row['id']."'>". $row['name'] ."</option>";
					}
					$All_Dep = parent::DB_select('id, name','s_department');
					$Dep = "<option value=''></option>";
					foreach($All_Dep AS $row) {
						$Dep .= "<option value='".$row['id']."'>". $row['name'] ."</option>";
					}
					
					$contfiltr = "
						<div class='zfilter'>
							<label>Время добавления</label>
							". self::createdForm('dtime',array('name'=>'dtStart', 'ph'=>'от','type'=>'dd.MM.yyyy')) ."
							". self::createdForm('dtime',array('name'=>'dtFinish', 'ph'=>'до','type'=>'dd.MM.yyyy')) ."
						</div>

						". self::createdForm('fBlocks',array('name'=>'employee', 'label'=>'Сотрудник', 'type'=>'input')) ."
						". self::createdForm('fBlocks',array('name'=>'role', 'label'=>'Должноть', 'type'=>'input')) ."
						". self::createdForm('fBlocks',array('name'=>'depart', 'label'=>'Отдел', 'data' => $Dep, 'type'=>'select')) ."
						". self::createdForm('fBlocks',array('name'=>'category', 'label'=>'Категория', 'data' => $category,'type'=>'select')) ."
						". self::createdForm('fBlocks',array('name'=>'location', 'label'=>'Помещение', 'data' => $location,'type'=>'select')) ."
						". self::createdForm('fBlocks',array('name'=>'kpp', 'label'=>'КПП', 'data' => $KPP,'type'=>'select')) ."

					";
					
					if( is_array($array) ) {
						foreach ( $array as $row ) {
							switch ($row) {
								case 'cheked' : 
									$contfiltr .= "
										<div class='zfilter'>
											<p><label><input type='checkbox'  checked='checked' name='filtr_urv' value='totalFact' /> Итого по факту</label></p>
											<p><label><input type='checkbox'  checked='checked' name='filtr_urv' value='totalGraf' /> Итого по графику</label></p>
										</div>

										<div class='zfilter'>
											<p><label><input type='checkbox'  name='filtr_urv' value='delayH' /> Опоздания</label></p>
											<p><label><input type='checkbox'  name='filtr_urv' value='flawFact' /> Недороботка по факту</label></p>
											<p><label><input type='checkbox'  name='filtr_urv' value='premature' /> Преждевременный уход</label></p> 
										</div>

										<div class='zfilter'>
											<p><label><input type='checkbox'  name='filtr_urv' value='lunch' /> Обед</label></p>
											<p><label><input type='checkbox'  name='filtr_urv' value='minLunch' /> отнимать Обед</label></p>
											<p><label><input type='checkbox'  name='filtr_urv' value='detalInfo' /> Детализация</label></p>
										</div>
										
										<div class='zfilter'>
											<p><label><input type='checkbox'  name='filtr_urv' value='processingDo' /> Переработка до</label></p>
											<p><label><input type='checkbox'  name='filtr_urv' value='processingAfter' /> Переработка после</label></p>
											<p><label><input type='checkbox'  name='filtr_urv' value='processingFact' /> Переработка по факту</label></p> 
										</div>
									";
								break;
								case 'state' :
									$contfiltr .= "
										<div class='zfilter'>
											<label>Событие</label>
											<select name='state' class='form-control'>
												<option value=''></option>
												<option value='1'>Приход</option>
												<option value='2'>Уход</option>
											</select>
										</div>";
								break;
							}
						}
					}
					
			break;
			
				/////////////////////////////////////////////////////
				/////			 ФИЛЬТР ЗАДАЧ
				////////////////////////////////////////////////////
			case 'taskFiltr' :
					
					# форма статуса
					$sd = selectAllStatusZ();
					//создаю список
					$selectStatus = '';
					foreach($sd as $s){
						if($s['status'] != 1){
							$selectStatus .= "<option value='". $s['status'] ."'>". $s['name_status'] ."</option>";
						}
					}
					$formStatus = "
						<select name='s_status' form='filtr' class='form-control'>
							<option value='1'>в работе</option>
							<option value='6'>все</option>
							" . $selectStatus . "
						</select>
					";

					# форма приоритета
					$pr = selectAllPrioritetZ();
					//создаю список
					$selectPrioritet = '';
					foreach($pr as $r){
						$selectPrioritet .= "<option value='". $r['prioritet'] ."'>". $r['name_prioritet'] ."</option>";
					}
					$formPrioritet = "
						<select name='prioritet' form='filtr' class='form-control'>
							<option value=''>все</option>
							" . $selectPrioritet . "
						</select> 
					";

					# форма инициаторов
					$selectIniciator = '';
					switch(definitionUserGroup())
					{
						//если админ
						case 1 : 
						case 2 : $All_ini = selectAllIniciator();

								 //формирую список инициаторов
								 foreach($All_ini as $row_ini)
								 {
									$name_U = splitFIOtoMassiv($row_ini['fio']);
									$selectIniciator .= "<option value='". $row_ini['iniciator'] ."'>". $name_U ."</option>";
								 }

								 $formIniciator = "
									<select name='iniciator' form='filtr' class='form-control'>
										<option value=''>все</option>
										". $selectIniciator . "
									</select>
									";break;

						default: $formIniciator = "недостаточно прав" ;break;
					}

					# форма исполнителей
					$formIspolnitel = '';
					switch(definitionUserGroup())
					{
						//если админ
						case 1 : 
						case 2 : $All_is = selectAllIspolnitel();

								 //формирую список исполнителей
								  foreach($All_is as $row_is){
									$name_U = splitFIOtoMassiv($row_is['fio']);
									$formIspolnitel .= "<option value='". $row_is['ispolnitel'] ."'>". $name_U ."</option>";
								 }

								 $formIspolnitel = "
									<select name='ispolnitel' form='filtr' class='form-control'>
										<option value=''>все</option>
										". $formIspolnitel . "
									</select>
									";break;

						default: $formIspolnitel = "недостаточно прав" ;break;
					}
								
					$contfiltr = 
						"<div class='zfilter'>
							<label>Время добавления</label>
							". self::createdForm('dtime',array('name'=>'s_s_date', 'ph'=>'от','type'=>'dd.MM.yyyy hh:mm')) ."
							". self::createdForm('dtime',array('name'=>'s_f_date', 'ph'=>'до','type'=>'dd.MM.yyyy hh:mm')) ."
						</div>

						<div class='zfilter'>
							<label>Сроки завершения</label>
							". self::createdForm('dtime',array('name'=>'f_s_date', 'ph'=>'от','type'=>'dd.MM.yyyy hh:mm')) ."
							". self::createdForm('dtime',array('name'=>'f_f_date', 'ph'=>'до','type'=>'dd.MM.yyyy hh:mm')) ."
						</div>

						<div class='zfilter'>
							<label>Статус</label>
							". $formStatus ."
						</div>
						<div class='zfilter'>
							<label>Приоритет</label>
							". $formPrioritet ."
						</div>
						<div class='zfilter'>
							<label>Инициатор</label>
							". $formIniciator ."
						</div>
						<div class='zfilter'>
							<label>Исполнитель</label>
							". $formIspolnitel ."
						</div>";
			break;
			
				/////////////////////////////////////////////////////
				/////			 ФИЛЬТР ОТЧЁТОВ
				////////////////////////////////////////////////////
			case 'reportFiltr' :
					# || формирование формы сотрудников ||
					$selectSotrudnik = '';
					switch(definitionSESSION())
					{
						case 0 :
						case 1 : $All_user_ds = selectUserReport();
								  foreach($All_user_ds as $row_s){
									  $nameS = splitFIOtoMassiv($row_s['fio']);
									  $selectSotrudnik .= "<option value=". $row_s['id_user'] .">". $nameS ."</option>";
								  }

								 $formSotrudnik = "
								<label>Cотрудники</label>
								<select name='vs' class='form-control'>
									<option value=''>все</option>
									". $selectSotrudnik ."
								</select>
								 ";
								  ;break;
						default: $formSotrudnik = '' ;break;	  
					}

					# || формирование формы даты ||

					$contfiltr = "
						<div class='zfilter'>
							<label>Временной период</label>
							". self::createdForm('dtime',array('name'=>'s_s_date', 'ph'=>'от','type'=>'dd.MM.yyyy hh:mm','value'=> Date_unix('', 4))) ."
							". self::createdForm('dtime',array('name'=>'s_f_date', 'ph'=>'до','type'=>'dd.MM.yyyy hh:mm')) ."	
						</div>

						<div class='zfilter'>
							". $formSotrudnik ."
						</div>
					";
			break;
			
				/////////////////////////////////////////////////////
				/////			 ФИЛЬТР SMS РАССЫЛКИ
				/////////////////////////////////////////////////////
			case 'smsFiltr' :
				
				$contfiltr = "
					". self::createdForm('fBlocks',array('name'=>'name', 'label'=>'ФИО клиента', 'type'=>'input')) ."
					". self::createdForm('fBlocks',array('name'=>'phone', 'label'=>'Телефон', 'type'=>'input')) ."
					
					<div class='zfilter'>
						<label>День рожденья</label>
						". self::createdForm('dtime',array('name'=>'birthday_s', 'ph'=>'от','type'=>'dd.MM.yyyy')) ."
						". self::createdForm('dtime',array('name'=>'birthday_f', 'ph'=>'до','type'=>'dd.MM.yyyy')) ."	
					</div>
					
					". self::createdForm('fBlocks',array('name'=>'email', 'label'=>'Емайл', 'type'=>'input')) ."
					
					". self::createdForm('fBlocks',array(	'name'=>'city', 
															'label'=>'Группы', 
															'data' => self::createdForm('fSelect', $array),
															'type' =>'select')) ." ";

			break;
		
				
		}
		
		$filtr = "
			<div class='Sunwel_filtr'>
				<div class='well well-sm'>
						
					". $contfiltr ."
					
					<div class='clear'></div>
					
					<div class='zfilterok'>
						<button class='btn btn-success' type='button' name='sendFiltr' >Принять</button>
						<button class='btn btn-default' type='button' name='SunwelClearFiltr'>Очистить</button>
					</div>
					
					<div class='clear'></div>
				</div>
			</div>";
		
		return $filtr;
	}

	public function createdContent($param, $array='') {
		switch ($param) {
			#######################################
			#####	СОЗДАНИЕ ГРАФИКОВ
			#######################################
			case 'createGraphic' :
				
					# значения по умолчанию 
					$name			= '';
					$for			= 7;
					$point			= date('d.m.Y');
					$description	= '';
					$type			= "<option value='1'>фиксированый график</option><option value='2'>суточный график</option><option value='3'>сменный график</option>";
					$table			= '';
				
					if( is_array($array) ) {
						foreach ( $array as $row => $key ) {
							switch ($row) {
								case 'name'			:	$name = $key; break;
								case 'for'			:	$for = $key; break;
								case 'point'		:	$point = $key; break;
								case 'description'	:	$description = $key; break;
								case 'type'			:	if($key == 2 ) {
															$type = "<option value='2'>суточный график</option><option value='3'>сменный график</option><option value='1'>фиксированый график</option>"; break;
														}
														if($key == 3 ) {
															$type = "<option value='3'>сменный график</option><option value='2'>суточный график</option><option value='1'>фиксированый график</option>"; break;
														}
								case 'table'		:	$table = $key; break;
							}
						}
					}
					
					$this->cont = "
					<div class='well well-sm'>
					
						<div class='zfilter'>
							<label>Тип графика</label>
							<select class='form-control' name='type'>
								". $type ."
							</select>
						</div>
						
						". self::createdForm('fBlocks',array('name'=>'caption', 'label'=>'Название', 'data'=>$name,'type'=>'input')) ."
						". self::createdForm('fBlocks',array('name'=>'for', 'label'=>'Переодичность', 'data'=>$for,'type'=>'input')) ."
						<div class='zfilter'>
							<label>Действует ст</label>
							". self::createdForm('dtime',array('name'=>'dtStart', 'ph'=>'действует от','type'=>'dd.MM.yyyy', 'value'=>$point)) ."
						</div>
						
						<div class='clear'></div>
						<div>
							<label>Описание</label>
							<textarea class='form-control' name='description'>". $description ."</textarea>
						</div>
						
						<div class='clear'></div>
					</div>
					". $table ."
					";
			break;
		
			#######################################
			#####	СОЗДАНИЕ ФОРМЫ ИЗМЕНЕНИЯ ГРАФИКА СОТРУДНИКАМ
			#######################################
			case 'setting_grafic_employee' : 
					$graphic = ''; $employee = ''; $marker = ''; $button = '';
					if( is_array($array) ) {
						foreach ( $array as $row ) {
							
							if( $row == 'add' ) {
								$marker = $row;
								$All_user = parent::DB_select('id, fio','s_employee','','grafic_id = 0');
								$button = '<button type="button" class="btn btn-primary" name="add">Добавить</button>';
							}
							if( $row == 'edit' ) {
								$marker = $row;
								$All_user = parent::DB_select('id, fio','s_employee','','grafic_id != 0');
								$button = '<button type="button" class="btn btn-success" name="edit">Изменить</button>';
							}
							if( $row == 'del' ) {
								$marker = $row;
								$All_user = parent::DB_select('id, fio','s_employee');
								$button = '<button type="button" class="btn btn-danger" name="del">Удалить</button>';
							}
							
							if( $row == 'add' or $row == 'edit' ) {
								$All_graphic = selectAllGraphic();

								$graphic = "<select class='form-control' name='type'><option value=''></option>";
								foreach($All_graphic as $row){
									$graphic .= "<option value='".$row['id']."'>". $row['name'] ."</option>";
								}
								$graphic .= "</select>";
							}
							
							if(isset($marker)) {
								foreach($All_user as $row){
									$employee .= "<label><input type='checkbox' name='employee' value='".$row['id']."' /> ". $row['fio'] ."</label><br />";
								}
							}
						}
					}
					
					$this->cont = "
						<div name='". $marker ."' style='display:none;' class='_sl_block_hormal'>
							<div class='row_bot row'>
								<div class='col-xs-6'>
									<label>Выбор сотрудников</label>
								</div>
								<div class='col-xs-6'>
									<label>Выбор графика</label>
								</div>
							</div>
							
							<div class='row_bot row'>
								<div class='col-xs-6'>
									". $employee ."
								</div>
								<div class='col-xs-6'>
									". $graphic ."	
								</div>
							</div>
							
							<hr />
							". $button ."
							<div class='clear'></div>
						</div>
						";
			break;
			
			###########################################
			#####	ДОПОЛНИеТЕЛЬНАЯ ИНФОРМАЦИ О ГРАФИКЕ
			###########################################
			case 'all_Info_Graphic' : 
				$name = ''; $description = ''; $for = ''; $point = ''; $type = '';$cont = '';
					if( is_array($array) ) {
						$i = 0; $key = array(); $value = array();
						
						foreach ($array AS $row) {
							if( isset($row['name']) ) {
								$key[] = 'Название'; $value[] = $row['name']; $i++; 
							}
							if( isset($row['description']) ) {
								$key[] = 'Описание'; $value[] = $row['description']; $i++;
							}
							if( isset($row['for']) ) {
								$key[] = 'Дней в графике'; $value[] = $row['for']; $i++;
							} 
							if( isset($row['point']) ) {
								$key[] = 'точка отсчёта'; $value[] = $row['point']; $i++;
							}
							if( isset($row['type']) ) { 
								$key[] = 'Тип'; $value[] = $row['type']; $i++;
							}
						}
						
						for($j = 0; $j < $i; $j++) {
							$cont .= "
							<div class='form-group'>
								<label class='col-xs-5 control-label'>". $key[$j] ."  :</label>
								<label class='col-xs6 control-label'>". $value[$j] ."</label>
							</div>";
						}
					}
					$this->cont = "<form class='form-horizontal' role='form'>". $cont ."</form>";
					
			break;
			###########################################
			#####	    СПИСОК СОТРУДНИКОВ
			###########################################
			case 'listEmployee' : 
					if( is_array($array) ) {
						$this->cont = '';
						foreach($array AS $row) {
							$this->cont .= "
								<div class='urv_employes'> 
									<div class='emp'><span class='glyphicon glyphicon-user'></span> ". $row['name'] ."</div>
									<div class='emp_time'><span class='glyphicon glyphicon-time'></span> ". $row['dt'] ."</div>
									<div class='clear'></div>
								</div>";
						}
					}
			break;
			
			###########################################
			#####	    ТАБЛИЦА ГРАФИКА
			###########################################
			case 'timeTable' : 
					$tr = ''; $i = 0; $id = '';
					if( is_array($array) ) {
						foreach($array AS $row => $key) {
							$i++;
							$id = $key['id'];
							$tr .= '<tr>
										<td>'. $i .'</td>
										<td>'. self::createdForm('dtime',array('value'=>$key['timeStart'], 'name'=>'st', 'ph'=>'от','type'=>'hh:mm:ss')) .'</td>
										<td>'. self::createdForm('dtime',array('value'=>$key['timeEnd'], 'name'=>'ft', 'ph'=>'до','type'=>'hh:mm:ss')) .'</td>
											
										<td>'. self::createdForm('dtime',array('value'=>$key['startLunch'], 'name'=>'sl', 'ph'=>'до','type'=>'hh:mm:ss')) .'</td>
										<td>'. self::createdForm('dtime',array('value'=>$key['endLunch'], 'name'=>'fl', 'ph'=>'до','type'=>'hh:mm:ss')) .'</td>
									</tr>';
						}
					}
					
					$this->cont .= "<table class='table table-hover' id='". $id ."'>
										<thead>
											<tr>
												<th>день</th>
												<th>начало рабочего дня</th>
												<th>конец рабочего дня</th>
												
												<th>начало обеда</th>
												<th>конец обеда</th>
											</tr>
										</thead>
										<tbody>
											". $tr ."
										</tbody>
									</table>";
			break;
			
			###########################################
			#####	    СТРОКА ТАБЛИЦЫ ЗАДАЧ 
			###########################################
			case 'trTask' : 
					$id = 0; $id_status = 0; $nameStatus = ''; $dtStart = ''; $dtFinish = ''; $class_pr = ''; $name_prioritet = '';
					$id_Ini = 0; $idIs = 0; $nameIni = ''; $nameIs = ''; $com = ''; $caption = '';
					if( is_array($array) ) {
						foreach ( $array as $row => $key ) {
							switch ($row) {
								case 'id'				: $id				= $key;	break;
								case 'id_status'		: $id_status		= $key;	break;
								case 'nameStatus'		: $nameStatus		= $key;	break;
								case 'dtStart'			: $dtStart			= $key;	break;	
								case 'dtFinish'			: $dtFinish			= $key;	break;
								case 'class_pr'			: $class_pr			= $key;	break;
								case 'name_prioritet'	: $name_prioritet	= $key;	break;
								case 'id_Ini'			: $id_Ini			= $key;	break;	
								case 'idIs'				: $idIs				= $key;	break;
								case 'nameIni'			: $nameIni			= $key;	break;
								case 'nameIs'			: $nameIs			= $key;	break;
								case 'com'				: $com				= $key;	break;
								case 'caption'			: $caption			= $key;	break;	
							}
						}
					}
					
					$this->cont = 
					"<tr ondblclick='callForum(". $id .")'>
						<td>". $dtStart ."</td>
						<td>". $dtFinish ."</td>
						<td class='green' value='". $id_status ."'>". $nameStatus ."</td>
						<td class='". $class_pr ."'>". $name_prioritet ."</td>
						<td class='_z_ini' value='". $id_Ini ."'>". $nameIni ."</td>
						<td class='_z_is' value='". $idIs ."'>". $nameIs ."</td>
						<td class='blueTd' onclick='callForum(". $id .")'>". nl2br($caption) ."". $com ."</td>
					</tr>";
			break;
			
			###########################################
			#####	    СТРОКА ТАБЛИЦЫ ЗАДАЧ 
			###########################################
			case 'createTaskForm' :
					$All_user = selectAllUserToAddZ();

					$is = '';
					foreach($All_user as $row){
						$nameIs = splitFIOtoMassiv($row['fio']);
						$is .= "<option value='".$row['id']."'>". $nameIs ."</option>";
					}

					# форма создания ново задачи
					$this->cont = "
						<div>
							<div class='row row_bot'>
								<div class='col-md-5'><b>Инициатор: </b></div>
								<div class='col-md-7'>".$_SESSION['fio']."</div>
							</div>
							<div class='row row_bot'>
								<div class='col-md-5'><b>Исполнитель: </b></div>
								<div class='col-md-7'>
									<select name='menu_is' class='form-control'>
										". $is ."
									</select> 
								</div>
							</div>
							<div class='row row_bot'>
								<div class='col-md-5'><b>Приоритет: </b></div>
								<div class='col-md-7'>
									<select name='pr' class='form-control'>
										<option value='1'>низкий</option>
										<option value='2'>обычный</option>
										<option value='3'>высокий</option>
										<option value='4'>горит</option>
									</select>
								</div>
							</div>
							<div class='row row_bot'>
								<div class='col-md-5'><b>Сроки сдачи проекта: </b></div>
								<div class='col-md-7'>
									<div class='dpicker'>
										<div class='input-group date'>
											<input class='form-control chb_zaperiod' type='text' name='date_finish' data-format='dd.MM.yyyy hh:mm' placeholder='сроки завершения'>
											<span class='input-group-addon'>
												<span class='glyphicon glyphicon-calendar'></span>
											</span>
										</div>
									</div>
								</div>
							</div>
							<div class='row row_bot'>
								<div class='col-md-5'><b>Название задачи: </b></div>
								<div class='col-md-7'>уведомление на имейл <input type='checkbox' name='toEmail' /></div>
							</div>
							<div class='row row_bot'>
								<div class='col-md-12'>
									<textarea name='caption_zadachi' rows=1 required placeholder='Введите название задачи' class='form-control'></textarea>
								</div>
							</div>
							<div class='row row_bot'>
								<div class='col-md-5'><b>Описание задачи: </b></div>
								<div class='col-md-7'>
									
								</div>
							</div>
							<div class='row row_bot'>
								<div class='col-md-12'>
									<textarea name='text_zadachi' rows=4 placeholder='Подробное описание' class='form-control'></textarea>
								</div>
							</div>
						</div>";
					//<button class='add_file'>Прикрепить файл  <img src='../task/task/images/add_files.png' width='20' height='20' /></button>
					//<div id='loading'></div>
			break;
		}
		
		return $this->cont;
	}
	
	public function __construct() {
		
	}
}