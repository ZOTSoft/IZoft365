// плагин работы с куками
jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

function show_newtab(tab){
	$('#loading').show();
	
	var title = ''; 
	var url = '/task/processing_SUNWEL.php'; 
	var loading = ''; 
	var dataType = 'json'; 
	var type = "POST";
	var sl = tab; 
	var reception = {};  
	var process = false; 
	var async = true;
	var viewTab = 1;

    switch(tab){
        case 'requere_sotrudnik'	:	title='Сотрудники';				reception['url'] = 'crm' ;		break; 
        case 'requere_zadacha'		:	title='Задания';				reception['url'] = 'crm' ;		break;
        case 'requere_report'		:	title='Отчёты';					reception['url'] = 'crm' ;		break;
        case 'requere_soc_seti'		:	title='Социальная Сеть';		reception['url'] = 'crm' ;		break;
		case 'requere_settings_mgr'	:	title='Настройки менеджеров';	reception['url'] = 'crm' ;		break;
		case 'z_notice_nw'			:	title='Задания для проверки';	reception['url'] = 'crm' ;		break;
		case 'z_notice_w'			:	title='Актуальные задания';		reception['url'] = 'crm' ;		break;
		
		case 'sms'					:	title='СМС Рассылка';			reception['url'] = 'delivery' ;	viewTab=2; 		sl='contentSMS';	break;
		case 'sms_settings'			:	title='Настройки API';			reception['url'] = 'delivery' ;	break;
		
		case 'frameURVhistory'		:	title='История';				reception['url'] = 'urv' ;		break;
		case 'urv_employee'			:	title='Отчет по сотруднику';	reception['url'] = 'urv' ;	 	break;
		case 'urv_visits'			:	title='Табель посещения';		reception['url'] = 'urv' ;	 	break;
		case 'urv_graphic'			:	title='Управление графиками';	reception['url'] = 'urv' ;		break;
		case 'urv_graphic_employee'	:	title='Графики сотрудников';	reception['url'] = 'urv' ;		break;
		case 'frameUrvTranzaction'	:	title='Выгрузка транзакций';	reception['url'] = 'urv' ;		break;
		case 'urv_empWorks'			:	title='Сотрудники на работе';	reception['url'] = 'urv' ;		break;
	} 
    
	// ДОПОЛНИТЕЛЬНАЯ ОБРАБОТКА ПЕРЕД ОТПРАВКОЙ
	if(process) { 	
		switch(sl) {

			default : sl = 'no select';
		}
	}

	if(sl == 'no select'){bootbox.alert('marker не выбран');return false;}
						
    $.ajax({ 
		type		: type,
		url			: url,
		async		: async,
		dataType	: dataType,
		beforeSend	: function() {
						
					},
		data		:{
						sl : sl, reception : reception
					},
		success		:function(data){
						
						switch (viewTab) {
							case 1 :	removeTabIfExist('#tab_'+tab); 
										var cont='<div class="bggrey"><h4>'+title+'</h4></div>'+data; 
										addTab(title,tab,cont);
							break;
							case 2 :	
										removeTabIfExist('#tab_'+tab); 
										var cont='<div class="bggrey"><h4>'+title+'</h4></div>'; 
										addTab(title,tab,cont);
							break;
						}
						
						switch (sl) 
						{
							///  УРВ ///
							case 'frameURVhistory' : 
									$('#report_history .Sunwel_filtr button[name=sendFiltr]').attr('onclick','processingSL("filtrURVhistory");');
									$(function() {
										$('#report_history .dpicker').datetimepicker({
											language: 'pt-BR'
										});
									});
									SunwelLoadfiltr();
									pagination('#tab_frameURVhistory');
									processingSL("filtrURVhistory");
							break;
									//|||||  ТАБЕЛЬ ПОСЕЩЕНИЯ   ||||||||||
							case 'urv_visits' : 
									$('#report_visits .Sunwel_filtr button[name=sendFiltr]').attr('onclick',"processingSL('URVtabel');");
									$(function() {
										$('#report_visits .dpicker').datetimepicker({ 
											pickTime: false
										});
									});
									SunwelLoadfiltr();
							break;
							
							case 'urv_graphic' : 
									vievGraficTable(url);
									createGrafic(url,'create','#createGraphicModal');
									createGrafic(url,'edit','#editGraphicModal');
									createGrafic(url,'del','');
									//editGrafic(url);
							break;
							
							case 'urv_employee' : 
									processing_urv_Emp(url);
							break;
							
							case 'urv_graphic_employee' : 
									SunwelViewHideCont();
									grafphicViewInfo(url);
									processing_urv_Graf_Emp(url);
							break;
							
							case 'frameUrvTranzaction' :
									$(function() {
										$('#tab_frameUrvTranzaction .dpicker').datetimepicker({
											language: 'pt-BR'
										});
									});
									SunwelLoadfiltr();
							break;
							
							case 'urv_empWorks'	:
									SunwelLoadfiltr();
									pagination('#tab_urv_empWorks');
									processingSL("filtrEmpWorks");
									$(function() {
										$('#tab_urv_empWorks .dpicker').datetimepicker({
											language: 'pt-BR'
										});
									});
									$('#tab_urv_empWorks .Sunwel_filtr button[name=sendFiltr]').attr('onclick',"processingSL('filtrEmpWorks');");
							break;
							
							///  ЗАДАЧИ  ///
							case 'requere_zadacha' : 
									$('#task .Sunwel_filtr button[name=sendFiltr]').attr('onclick','processingSL("TaskFilter");');
									$('#task .panel_toolbar a[name=add]').attr('onclick','createZadacha();');
									$(function() {
										$('#task .dpicker').datetimepicker({
											language: 'pt-BR'
										});
									});
									//alertHelpMessage();
									pagination('#task');
									SunwelViewHideCont();
									SunwelLoadfiltr();
									processingSL('TaskFilter');
							break;
							
							case 'requere_report' :
									processingReport(url);
							break;
							
							case 'requere_soc_seti':
									
							break;
							
							case 'requere_sotrudnik' : 
									$('#tab_requere_sotrudnik').append('<div id="employeeCRM"></div>');
									$('#employeeCRM').myTreeView({  
										url:url+'?sl=contentSotr',
										headers: [{title:'ФИО',name:'fio'},{title:'Email',name:'email'},{title:'Доступ',name:'name'},{title:'Колличество заданий',name:'countIdZ'}],
										pagination:false,
										tree:false,
											pagecount:[50,100,200]
									});
							break;
							
							case 'requere_settings_mgr' :
									//editGroup(url);
									//editProfile(url);
									//viewGroupMgr(url);
							break;
							
							case 'z_notice_nw' : 
							case 'z_notice_w' :
									$('.tab_task').click();
									//alertHelpMessage();
									SunwelSortTable(url);
									zReturn();
									zCancel();
									zTocomplete();
									zComplete();
							break;
							
							///  РАССЫЛКА  ///
							case 'contentSMS' :
									processing_sms(data, url);
							break;
							
							case 'sms_settings' :
									$('#tab_sms_settings button[name="saveSettings"]').click(function(){
										//saveSettingsAPI_Sms(url); 
										processingSL('smsSetingSave');
									});
							break;
						}
						
						if(loading === 'contentArticle'){
							KeyButtonAddArticle();
							comboKeyaddArticle();
							containerFile();
							$.cookie('reqvestUrl','U');//куки для файлов
						}
						
						//$('.righttd-content').height($('.righttd').height()-44);//-174
						$('.righttd-content').height($('.righttd').height()-$('.righttd-content').last().offset().top+30);
					},
		error		: function(){
						bootbox.alert('что то не так');
					},
		complete	: function() {
						$('#loading').hide();
					}
	});
}

function processingSL( marker , reception )  
{	
	$('#loading').show();
	var url = '/task/processing_SUNWEL.php'; 
	var dataType = 'json'; 
	var type = "POST";
	var sl = marker; 
	if(!reception){ var reception = {}; }
	var process = false; 
	var async = true;
	
	// ОСНОВНАЯ ОБРАБОТКА
	switch(marker)
	{
		case 'TaskFilter'			: reception['url'] = 'crm' ;		process = true;		break;
		case 'reportFiltr'			: reception['url'] = 'crm' ;		process = true;		break;
		case 'new_zadacha'			: reception['url'] = 'crm' ;		break;
		case 'CreateMgrTask'		: reception['url'] = 'crm' ;		break;
		case 'saveCreateMgrTask'	: reception['url'] = 'crm' ;		process = true;		break;
		
		case 'reloadEmp'			: reception['url'] = 'urv' ;		sl = 'listEmployeeURVPoint';	process = true;		break;
		case 'fullScreenEmp'		: reception['url'] = 'urv' ;		sl = 'listEmployeeURVPoint';						break;
		case 'URVtabel'				: reception['url'] = 'urv' ;		dataType = 'html';	process = true;	break;
		case 'filtrURVhistory'		: reception['url'] = 'urv' ;		process = true;		break;
		case 'filtrEmpWorks'		: reception['url'] = 'urv' ;		process = true;		break;
		
		case 'sendSMS'				: reception['url'] = 'delivery' ;	dataType = 'html';	break;
		case 'smsSetingSave'		: reception['url'] = 'delivery' ;	process = true;		break;
		case 'filtrDeloverySMS'		: reception['url'] = 'delivery' ;	process = true;		break;
		

		default : sl = 'no select';
	}
	
	
	// ДОПОЛНИТЕЛЬНАЯ ОБРАБОТКА ПЕРЕД ОТПРАВКОЙ
	if(process)
	{
		switch(marker)
		{
		//||| УРВ |||
			case 'reloadEmp' : reception['limit'] = 15 ; break;
			
			//|||||  ОБРАБОТКА | ТАБЕЛЬ ПОСЕЩЕНИЯ   ||||||||||
			case 'URVtabel'	:	
				var checked = Array(); var i = 0;
				$('#report_visits .Sunwel_filtr input[type=checkbox][name="filtr_urv"]:checked').each(function(){
					checked[i] = $(this).val();
					i++;
				});
				reception['dtStart']	= $('#report_visits .Sunwel_filtr input[name="dtStart"]').val();
				reception['dtFinish']	= $('#report_visits .Sunwel_filtr input[name="dtFinish"]').val();
				reception['еmployee']	= $('#report_visits .Sunwel_filtr input[name="employee"]').val();
				reception['role']		= $('#report_visits .Sunwel_filtr input[name="role"]').val();
				reception['depart']		= $('#report_visits .Sunwel_filtr select[name="depart"]').val();
				reception['category']	= $('#report_visits .Sunwel_filtr select[name="category"]').val();
				reception['location']	= $('#report_visits .Sunwel_filtr select[name="location"]').val();
				reception['kpp']		= $('#report_visits .Sunwel_filtr select[name="kpp"]').val();
				reception['checked']	= checked; 
			break;
			
			//|||||  ОБРАБОТКА | ФИЛЬТР ОТЧЁТОВ(в задачах)   ||||||||||
			case 'reportFiltr' :
				reception['date_start']		= $('#report_view input[name="s_s_date"]').val();
				reception['date_finish']	= $('#report_view input[name="s_f_date"]').val();
				reception['vs']				= $('#report_view select[name="vs"]').val();	
			break;
			
			//|||||  ОБРАБОТКА | ФИЛЬТР ИСТОРИИ УРВ   ||||||||||
			case 'filtrURVhistory' : 
				reception['dtStart']	= $('#report_history .Sunwel_filtr input[name="dtStart"]').val();
				reception['dtFinish']	= $('#report_history .Sunwel_filtr input[name="dtFinish"]').val();
				reception['еmployee']	= $('#report_history .Sunwel_filtr input[name="employee"]').val();
				reception['role']		= $('#report_history .Sunwel_filtr input[name="role"]').val(); 
				reception['category']	= $('#report_history .Sunwel_filtr select[name="category"]').val();
				reception['location']	= $('#report_history .Sunwel_filtr select[name="location"]').val();
				reception['kpp']		= $('#report_history .Sunwel_filtr select[name="kpp"]').val();
				reception['thispage'] 	= $('#report_history .scroll_bar input[name="thispage"]').val();
				reception['selCount'] 	= $('#report_history .scroll_bar select[name="selCount"]').val();
				reception['state']		= $('#report_history .Sunwel_filtr select[name="state"]').val();
			break;
			
			//|||||  ОБРАБОТКА | ФИЛЬТР СОТРУДНИКИ НА РАБОТЕ  ||||||||||
			case 'filtrEmpWorks' : 
				reception['dtStart']	= $('#tab_urv_empWorks .Sunwel_filtr input[name="dtStart"]').val();
				reception['dtFinish']	= $('#tab_urv_empWorks .Sunwel_filtr input[name="dtFinish"]').val();
				reception['еmployee']	= $('#tab_urv_empWorks .Sunwel_filtr input[name="employee"]').val();
				reception['role']		= $('#tab_urv_empWorks .Sunwel_filtr input[name="role"]').val(); 
				reception['category']	= $('#tab_urv_empWorks .Sunwel_filtr select[name="category"]').val();
				reception['location']	= $('#tab_urv_empWorks .Sunwel_filtr select[name="location"]').val();
				reception['kpp']		= $('#tab_urv_empWorks .Sunwel_filtr select[name="kpp"]').val();
				reception['thispage'] 	= $('#tab_urv_empWorks .scroll_bar input[name="thispage"]').val();
				reception['selCount'] 	= $('#tab_urv_empWorks .scroll_bar select[name="selCount"]').val();
				reception['state']		= $('#tab_urv_empWorks .Sunwel_filtr select[name="state"]').val();
			break;
			
		//||| ЗАДАЧИ |||			
			//|||||  ОБРАБОТКА | ФИЛЬТР ЗАДАЧ   ||||||||||
			case 'TaskFilter' :
				reception['s_date_start'] 	= $('#task div.well input[name="s_s_date"]').val();
				reception['s_date_finish'] 	= $('#task div.well input[name="s_f_date"]').val();
				reception['f_date_start'] 	= $('#task div.well input[name="f_s_date"]').val();
				reception['f_date_finish'] 	= $('#task div.well input[name="f_f_date"]').val();
				reception['status'] 		= $('#task div.well select[name="s_status"]').val();
				reception['prioritet'] 		= $('#task div.well select[name="prioritet"]').val();
				reception['iniciator'] 		= $('#task div.well select[name="iniciator"]').val();
				reception['ispolnitel'] 	= $('#task div.well select[name="ispolnitel"]').val();
				reception['thispage'] 		= $('#task .scroll_bar input[name="thispage"]').val();
				reception['selCountZ'] 		= $('#task .scroll_bar select[name="selCount"]').val();
			break;
			
			//|||||  ОБРАБОТКА | СОХРАНЕНИЕ СОЗДАНОГО МЕНЕДЖЕРА  ||||||||||
			case 'saveCreateMgrTask' :
				reception['userMgr']		= $('#CreateMgrTask select[name=userToGroup]').val();
				reception['userView']		= new Array();
				var i = 0;
				$('#CreateMgrTask input[type=checkbox][name=user]').filter(':checked').each(function(){
					reception['userView'][i] = $(this).val();
					i++;
				});
			break;
			
		//|||  РАССЫЛКА |||
			//|||||  СОХРАНЕНИЕ | SMS API   ||||||||||
			case 'smsSetingSave' :
				reception['typeClient']	= $('#tab_sms_settings .settingsAPI select[name="sms_typeClient"]').val();
				reception['sms_API'] 	= $('#tab_sms_settings .settingsAPI input[name="sms_API"]').val();
				reception['sms_email'] 	= $('#tab_sms_settings .settingsAPI input[name="sms_email"]').val();
				reception['sms_name'] 	= $('#tab_sms_settings .settingsAPI input[name="sms_name"]').val();
			break;
			
			//|||||  ОБРАБОТКА | ФИЛЬТР КЛИЕНТОВ   ||||||||||
			case 'filtrDeloverySMS' :
				$('#divSms .Sunwel_filtr').siblings('table').find('input[type="checkbox"]').removeAttr("checked");
				reception['name'] 		= $('div#divSms div.Sunwel_filtr input[name="name"]').val();
				reception['phone'] 		= $('div#divSms div.Sunwel_filtr input[name="phone"]').val();
				reception['birthday_s'] = $('div#divSms div.Sunwel_filtr input[name="birthday_s"]').val();
				reception['birthday_f']	= $('div#divSms div.Sunwel_filtr input[name="birthday_f"]').val();
				reception['email'] 		= $('div#divSms div.Sunwel_filtr input[name="email"]').val();
				reception['city'] 		= $('div#divSms div.Sunwel_filtr select[name="city"]').val();
				reception['selCount'] 	= $('div#divSms div.scroll_bar select[name="selCount"]').val();
				reception['thisPage'] 	= $('div#divSms div.scroll_bar input[name="thispage"]').val();
				reception['id_mask']	= '';
				if($('#divSms .modal-dialog table thead input[name="mask"]').filter(':checked').val()) {
					reception['id_mask'] = $('#divSms table thead input[name="mask"]').filter(':checked').val();
				} else {
					$('#divSms .modal-dialog table tbody input[name="mask"]').filter(':checked').each(function() {
						reception['id_mask'] += $(this).val()+'|';
					});
				}
			break;
				
				
			default : sl = 'no select';
		}
	}

	if(sl == 'no select'){bootbox.alert('marker не выбран');return false;}
						
	$.ajax
	({
		url			: url,
		type		: type,
		dataType	: dataType,
		async		: async,
		beforeSend	: function() {
						
					},
		data		:{
						sl: marker,reception: reception
					},
		success		: function(data){

						switch(marker)
						{
							// УРВ
									//|||||  РВ.ГЛАВНАЯ   ||||||||||
							case 'reloadEmp' : 
									$('#wpoint').html('КПП '+data['info'][0]['kpp']+' находится в '+data['info'][0]['location']);
									$('#DScrollY').html(data['cont']);
							break;
							
									//|||||  ВЫВОД ВСЕХ СОТРУДНИКОВ   ||||||||||
							case 'fullScreenEmp':
									$('#wpoint').html('КПП '+data['info'][0]['kpp']+' находится в '+data['info'][0]['location']);
									$('#dialogs').html(data['cont']);
									$('#dialogs #allEmpUrv').modal();
							break;
									
									//|||||  ВЫВОД ФИЛЬТРОВАННЫХ ТРАНЗАКЦИЙ  ||||||||||
							case 'filtrURVhistory' : 
									$('#report_history table tbody').html(data['tr']);
									$('#report_history .scroll_bar .count_page').html('из '+data['countP']);
									$('#report_history .scroll_bar .count_z').html('Транзакций '+data['count']);
							break;
							
									//|||||  ОБРАБОТКА | ФИЛЬТР СОТРУДНИКИ НА РАБОТЕ  ||||||||||
							case 'filtrEmpWorks' : 
								$('#tab_urv_empWorks table tbody').html(data['tr']);
								$('#tab_urv_empWorks .scroll_bar .count_page').html('из '+data['countP']);
								$('#tab_urv_empWorks .scroll_bar .count_z').html('Транзакций '+data['count']);
							break;
									
									//|||||  ТАБЕЛЬ ПОСЕЩЕНИЯ   ||||||||||
							case 'URVtabel' :
									$('#report_visits #view_report_visits').empty();	
									$('#report_visits #view_report_visits').append(data);
									$('#report_visits #view_report_visits td').find('span').parent('td').css('background','#F0EEEE');
							break;
							
							// TASK 
							
									//|||||  ЗАДАЧИ | ФИЛЬТР  ||||||||||
							case 'TaskFilter' :
									$('.zadacha-table tbody tr').remove();	
									$('.zadacha-table tbody').append(data['tr']);
									$('#task .scroll_bar .count_page').html('из '+data['countP']);
									$('#task .scroll_bar .count_z').html('Заданий '+data['count']);
							break;
							
									//|||||  ЗАДАЧИ | ДОБАВЛЕНИЕ  ||||||||||
							case 'new_zadacha' :
									$('select[name="menu_is"]').attr('value',reception['ispolnitel']);
									$('select[name="prioritet"]').attr('value',reception['prioritet']);
									$('textarea[name="text_zadachi"]').val('');
									$('textarea[name="caption_zadachi"]').val('');
									if($('#filedown').length){
										$('#filedown').remove();
									}
									if($('.zadacha-table tbody tr:first').length){
										$('.zadacha-table tbody tr:first').before(data);
									} else {
										$('.zadacha-table tbody tr:first').append(data);
									}
									if(data) {
										bootbox.alert('задание добавлено');
									}
							break;
							
									//|||||  НАСТРОЙКА ЗАДАЧ | СОЗДАНИЕ МЕНЕДЖЕРА  ||||||||||
							case 'CreateMgrTask' :
									$('#dialogs #CreateMgrTask').remove();
									$('.modal-backdrop').remove(); 
									$('#dialogs').append(data);
									$('#CreateMgrTask button[name=save]').attr('onclick',"processingSL('saveCreateMgrTask');");
									$('#CreateMgrTask').modal();
							break;
									
									//|||||  НАСТРОЙКА ЗАДАЧ | СОХРАНЕНИЕ СОЗДАНОГО МЕНЕДЖЕРА  ||||||||||
							case 'saveCreateMgrTask' :
									bootbox.alert(data); 
							break;
							
									
									//|||||   ФИЛЬТР ОТЧЁТОВ(в задачах)   ||||||||||
							case 'reportFiltr' :
									$('.report-table').html(data);
							break;
				 
							// DELIVERY 
									//|||||   СМС | ОТПРАВКА СМС   ||||||||||
							case 'sendSMS' :
							case 'smsSetingSave' :
									 bootbox.alert(data);
							break;
							
									//|||||  ОБРАБОТКА | ФИЛЬТР КЛИЕНТОВ   ||||||||||
							case 'filtrDeloverySMS' :
								$('#divSms table.table-hover tbody').html(data['tr']);
								$('#divSms .scroll_bar .count_page').html('из '+data['countPage']);
								$('#divSms .scroll_bar .count').html('Клиентов '+data['count']);
								SunwelallCheckBox('#divSms ');
							break;
						}
						
						
					},
		error		: function(){
						bootbox.alert('что то не так');
					},
		complete	: function() {
						$('#loading').hide();
					}
	});
}

//////////////////////////////////////////////
////////         СОЦИАЛКА           //////////	
//////////////////////////////////////////////
		
//### функция добавление новой статьи в СоцСети ###
function comboKeyaddArticle()
{
	$('.block_new_article .textarea_width_main').keyup(function (e) {
		if(e.which === 17 || e.which === 13)
		{ 
			isCtrl=false;
		}
	}).keydown(function (e) {
		
		if($('#div_container_ss .textarea_width_main').val())
		{
			if(e.which === 17)
			{ 
				isCtrl=true;
			}
			if(e.which === 13 && isCtrl === true)
			{
				addArticle();
				return false;
			}
		}
	});
}

//### аналочная по функционалу с "добавление новой статьи в СоцСети" только по нажатию на кнопку
function KeyButtonAddArticle()
{
	$('.block_new_article button[name=new_article]').click(function(){
		if($('#div_container_ss .textarea_width_main').val())
		{
			addArticle();
		}
	});
}

//### добавление статьи
function addArticle()
{
	var textCom = $('#div_container_ss .textarea_width_main').val();
				
	$.ajax
	({
		url			: '/task/crm/processing_task.php',
		type		: 'POST',
		data		: 	{ 
						newArticle 	: 'OK',
						text 		: textCom
						},
		processData	: true,
		contentType	: "application/x-www-form-urlencoded",
		success		: function(html)
					{
							$('#div_container_ss .block_new_article').after(html);
							$('#div_container_ss .textarea_width_main').val(null);
							$('#filedown').remove();
					},
		error		: function()
					{
						alert('произошла ошибка');	
					}
	});	
}

//### функция добавления комментария в СоцСети ###
function addComment(t)
{
	//выбираю ИД статьи соц сети
	var id_ss = $(t).parents('.block_cont_ss[id]').attr('id');
	
	if($('.block_cont_ss[id='+id_ss+'] .block_cont_coment').find('textarea'))
	{
		$('.block_cont_ss[id='+id_ss+'] div.block_cont_coment textarea.form-control').remove();
	}
	
	//создаю контейнер коментария
	var containerTextarea = $('<textarea  placeholder="Введите сообщение и нажмите Ctrl + Inter"  name="text"  class="form-control"></textarea>');
	
	//передаю контейнер в блок элемента
	$('.block_cont_ss[id='+id_ss+'] .block_cont_coment').append(containerTextarea);
	
	//если блок комментариев появился иду дальше по коду
	if($('.block_cont_ss[id='+id_ss+'] .block_cont_coment').find('textarea'))
	{
		//если НАЖАТА клавиша inter или ctrl сбрасываю переменную
		$('.block_cont_ss[id='+id_ss+'] textarea').keyup(function (e) {
			if(e.which === 17 || e.which === 13)
			{ 
				isCtrl=false;
			}
		//если ОТЖАТА клавиша inter или ctrl идём дальше по коду
		}).keydown(function (e) {
			
			//если в блоке комментария есть текст идём дальше по коду
			if($('.block_cont_ss[id='+id_ss+'] textarea').val())
			{
				
				//если нажата клавиша ctrl присваиваю истину переменной
				if(e.which === 17)
				{ 
					isCtrl=true;
				}
				
				//если нажата клавиша интер и переменная истина идём дальше по коду
				if(e.which === 13 && isCtrl === true)
				{
					var textCom = $('.block_cont_ss[id='+id_ss+'] textarea').val();
					//alert(textCom);
					
					$.ajax
					({
						url			: '/task/crm/processing_task.php',
						type		: 'POST',
						data		: 	{ 
										newComent 	: 'OK',
										text 		: textCom,
										id_ss		: id_ss
										},
						processData	: true,
						contentType	: "application/x-www-form-urlencoded",
						success		: 	function(data)
										{
											$('.block_cont_ss[id='+id_ss+'] .block_cont_coment').append(data);
											$('div.block_cont_coment textarea.form-control').remove();
										},
						error		: 	function()
										{
											alert('комментарий не добавлен');
										}
					});
					
					return false;
				}
			}
		});	
	}
}

//### функция добавления ответа комментария в СоцСети ###
function addAnswerComent(answer)
{
	//получение ИД
	var id_ss = $(answer).parents('.block_cont_ss').attr('id');
	var id_ss =1;
	if($('.block_cont_ss[id='+id_ss+'] .block_cont_coment').find('textarea'))
	{
		$('.block_cont_ss[id='+id_ss+'] div.block_cont_coment textarea.form-control').remove();
	}
	
	//получение Имени
	var name = $(answer).prev().prev();
	name = $(name).find('a').text();
	alert(name)
	
	var containerTextarea = $('<textarea placeholder="Введите сообщение и нажмите Ctrl + Inter" name="answer" class="form-control" >'+name+', </textarea>');
	
	//передаю контейнер в блок элемента
	$('.block_cont_ss[id='+id_ss+'] .block_cont_coment').append(containerTextarea);
	
	//если блок комментариев появился иду дальше по коду
	if($('.block_cont_ss[id='+id_ss+'] .block_cont_coment').find('textarea[name="answer"]'))
	{
		//если НАЖАТА клавиша inter или ctrl сбрасываю переменную
		$('.block_cont_ss[id='+id_ss+'] textarea[name="answer"]').keyup(function (e) {
			if(e.which === 17 || e.which === 13)
			{ 
				isCtrl=false;
			}
		//если ОТЖАТА клавиша inter или ctrl идём дальше по коду
		}).keydown(function (e) {
			
			//если в блоке комментария есть текст идём дальше по коду
			if($('.block_cont_ss[id='+id_ss+'] textarea[name="answer"]').val())
			{
				
				//если нажата клавиша ctrl присваиваю истину переменной
				if(e.which == 17)
				{ 
					isCtrl=true;
				}
				
				//если нажата клавиша интер и переменная истина идём дальше по коду
				if(e.which == 13 && isCtrl == true)
				{
					var textCom = $('.block_cont_ss[id='+id_ss+'] textarea[name="answer"]').val();
					//alert(textCom);
					
					$.ajax
					({
						url			: '/task/crm/processing_task.php',
						type		: 'POST',
						data		: 	{ 
										newComent 	: 'OK',
										text 		: textCom,
										id_ss		: id_ss
										},
						processData	: true,
						contentType	: "application/x-www-form-urlencoded",
						success		: 	function(data)
										{
											$('.block_cont_ss[id='+id_ss+'] .block_cont_coment textarea[name="answer"]').before(data);
											$('div.block_cont_coment textarea[name="answer"]').remove();
										},
						error		: 	function()
										{
											alert('комментарий не добавлен');
										}
					});
					
					return false;
				}
			}
		});	
	}
}

//////////////////////////////////////////////
////////          ЗАДАЧИ            //////////	
//////////////////////////////////////////////

//### функция изменения статусов
function processing_status(thisObj, param)
{
	var reception = {};
	reception['_id_st']  = 0;
	reception['count_w']  = $('.z_notice_count_w').text();
	reception['count_nw']  = $('.z_notice_count_nw').text();
	reception['thisStatus']  = '';
	reception['param'] = param;
	reception['url'] = 'crm' ;
	
	if(typeof thisObj == 'number') 
	{
		reception['id_z'] = thisObj;
		var tr = $('.zadacha-table').find('tr[ondblclick="callForum('+reception['id_z']+')"]');
		reception['thisStatus'] = tr.children('.green').attr('value');
		reception['id_is'] = tr.children('._z_is').attr('value');
		reception['id_ini'] = tr.children('._z_ini').attr('value');
		reception['_id_st'] = tr.children('.green').attr('value');
	}
	else
	{
		var tr = $(thisObj).parents('.zadacha-table tr');
		reception['id_z'] = tr.attr('id');
		reception['id_is'] = tr.find('td[value]').attr('value');	
		reception['id_ini'] = tr.find('td[value]').attr('value');
	}
	
	if(reception['param'] && reception['id_z'])
	{
		//отправка запроса с данными на сервер
		$.ajax
		({
			url			: '/task/processing_SUNWEL.php',
			type		: 'POST',
			dataType	: "json",
			data		:{ 
							sl			: 'edit_status',
							reception	: reception
						},
			success		: function(dataCount){
							function divCountZ(countZ, nameClass){
								if(countZ == 0){
									$('.z_notice_count_'+nameClass).remove();
								} else {
									if($('.z_notice_count_'+nameClass).length < 1) {
										$('.z_notice_'+nameClass).append('<div class="z_notice_count_'+nameClass+'"></div>');
									}
									$('.z_notice_count_'+nameClass).text('');
									$('.z_notice_count_'+nameClass).text(countZ);
								}
							}
							divCountZ(dataCount['count_nw'], 'nw');
							divCountZ(dataCount['count_w'], 'w');
							
							//если в задачах
							if($('#tab_requere_zadacha .zadacha-table').length)
							{
								$('tr[ondblclick="callForum('+reception['id_z']+')"]').hide();
							}
							//если ЛК
							if($('#tab_z_notice_w .zadacha-table, #tab_z_notice_nw .zadacha-table').length)
							{
								$('tr[id="'+reception['id_z']+'"]').hide();
							}
							
							switch(param)
							{
								case 2 : $('.status_coment').text('Статус задачи: выполнено');		break;
								case 3 : $('.status_coment').text('Статус задачи: в доработке');	break;
								case 4 : $('.status_coment').text('Статус задачи: завершен');		break;
								case 5 : $('.status_coment').text('Статус задачи: отменено');		break;
							}
						}
		});
	}
}

//### функция обработки формы возврата задачи ###
function zReturn(id_z)
{
	if(!id_z){ id_z = 0;}
	$("[name='return_z']").one('click', function(evtObj)
	{	
		//сброс действий функции по умолчанию	
		evtObj.preventDefault();
		//если атрибут кнопки равен return_z идём дальше
		var param = 3;
		//отправка запроса с данными на сервер
		processing_status(id_z, param);
	});
}

//### функция обработки формы отмены задачи ###	
function zCancel(id_z)
{
	$("[name='cancel']").one('click', function(evtObj)
	{	
		//сброс действий функции по умолчанию	
		evtObj.preventDefault();
		//если атрибут кнопки равен return_z идём дальше
		var param = 5;
		//отправка запроса с данными на сервер
		processing_status(id_z, param);
		
		//если атрибут кнопки равен return_z идём дальше
	});
}
	
//### функция обработки формы завершения задачи ###
function zTocomplete(id_z)
{ 
	$("[name='tocomplete']").one('click', function(evtObj)
	{	
		//сброс действий функции по умолчанию	
		evtObj.preventDefault();
		var param = 2;
		//если атрибут кнопки равен return_z идём дальше
		processing_status(id_z, param);
	});
}

//### функция обработки формы выполнения задачи задачи ###
function zComplete(id_z)
{
	if(!id_z){ id_z = 0;}
	$('[name="complete"]').one('click', function(evtObj)
	{	
		//сброс действий функции по умолчанию	
		evtObj.preventDefault();
		
		var param = 2;
		processing_status(id_z, param);
	});	
}

//### Функция добавления описания задачи ###
function forumAddInfoZ(id_z)
{
	$('.viev_text_zadacha .text').dblclick(function(evtObj)
	{
		if($('.ispolnitel_coment').attr('ini') == $.cookie('id_user'))
		{
			var id_user = $.cookie('id_user');
	
			/**///сохраняем старый текст в переменную
			var oldText = $('.viev_text_zadacha .text').text();
			//отчищаю блок от текста
			$('.viev_text_zadacha .text').text('');
			//клонируем  элемент тексариа
			var textArea = $('.textarea_width').clone().attr('placeholder','добавьте описание и кликните возле поля ввода').css('border','none');
			//добавляем этот элемент
			$('.viev_text_zadacha .text').append(textArea);
			//ставим на него фокус
			$('.text .textarea_width').focus();

			$('.text textarea').blur(function()
			{
				//если текст введён то оправляем запрос на сервер
				if($('.text .textarea_width').val())
				{
					var newText = $('.text .textarea_width').val();
				
					$.ajax
					({
						url			: '/task/processing_SUNWEL.php',
						async		: true,
						cache		: false,
						type		: 'POST',
						data		: 	{ 
											edit_z 	: 'OK', 
											text	: newText,
											id		: id_z,
											id_user : id_user	
										},
						processData	: true,
						//dataType	: "json",
						success		: 	function(data, textStatus)
										{
											$('.text .textarea_width').remove();
											$('.viev_text_zadacha .text button').remove();
											$('.viev_text_zadacha .text').text(data);
										}
					});
				}
				else
				{
					$('.text .textarea_width').remove();
					$('.viev_text_zadacha .text button').remove();
					$('.viev_text_zadacha .text').text(oldText);
				}		
			});
			evtObj.preventDefault();
		}
	});	
}
	
//### Функция изменения приоретета задачи ###
function zadachaSetPrioritet(id_z)
{
	var reception = {};
	if(id_z) {
		reception['id_z'] = id_z;
	}
	if($('#tab_z_notice_w, #tab_z_notice_nw').length)
	{
		$('.zadacha-table').find('tr').hover(function()
		{
			reception['id_z'] = $(this).attr('id');
		});
	}
	
	$('span[name="set_prioritet"]').click(function(evtObj)
	{
		//переменная блока - предок кнопки статуса	
		var parent = $('span[name="set_prioritet"]').parent('.block_icon');
		//скрываю кнопку статуса
		$('#forum_zadacha span[name="set_prioritet"]').hide();
		//создаю меню селект
		var	 select = '<select name="prioritet" class="form-control">\n\
							<option value="">по умолчанию</option>\n\
							<option value="1">низкий</option>\n\
							<option value="2">обычный</option>\n\
							<option value="3">высокий</option>\n\
							<option value="4">горит</option>\n\
						</select>';
		$(parent).append(select); select = null;
		
		$('select[name="prioritet"]').change(function()
		{
			if($('select[name="prioritet"]').length)
			{
				reception['prioritet'] = $(this).attr('value');
				reception['url'] = 'crm' ;

				$.ajax
				({
					url			: '/task/processing_SUNWEL.php',
					type		: 'POST',
					data		:{ 
									sl			: 'prioritet',
									reception	: reception
								},
					success		: function(){
									//удаление списка
									$('#forum_zadacha select[name="prioritet"]').remove('select');
									//востанавливаю кнопку
									$('#forum_zadacha [name="set_prioritet"]').show();
									var classText = ''; var _class_pr = ''; var namePr = '';
									switch(reception['prioritet']){
										case '1':	classText = 'class_status_low_text'; 	_class_pr = 'class_status_low';		namePr = 'низкий';	break;
										case '2':	classText = 'class_status_normal_text'; _class_pr = 'class_status_normal';	namePr = 'обычный';	break;
										case '3':	classText = 'class_status_hign_text';	_class_pr = 'class_status_hign';	namePr = 'высокий';	break;
										case '4':	classText = 'class_status_fire_text';	_class_pr = 'class_status_fire';	namePr = 'горит';	break;
									}
									//устанавриваю рисунок
									$('span[name="set_prioritet"]').removeClass();
									$('span[name="set_prioritet"]').addClass(classText).addClass('glyphicon').addClass('glyphicon-star');
									
									//если задачи
									if($('#task .zadacha-table').length)
									{
										if($('tr[ondblclick="callForum('+reception['id_z']+')"]').length)
										{
											$('tr[ondblclick="callForum('+reception['id_z']+')"] td:nth-child(4)').removeClass().addClass(_class_pr).text(namePr);
										}
									}
									//если ЛК
									if($('#tab_z_notice_w, #tab_z_notice_nw').length)
									{
										$('tr[id="'+reception['id_z']+'"] td:nth-child(3)').removeClass().addClass(_class_pr).text(namePr);
									}
								}
				}); 
			}
		});
	});		
}
		
//### Функция вызова добавление комментариев в обсуждения при нажатии сочетания клавиш###
function komboPressCallforumAddComment(id_z)
{
	$('#forum_zadacha textarea[name="text"]').keyup(function (e) 
	{
		if(e.which == 17 || e.which == 13)
		{ 
			isCtrl=false;
		}
	}).keydown(function (e) 
	{
		if($('#forum_zadacha textarea[name="text"]').val())
		{
			if(e.which == 17)
			{ 
				isCtrl=true;
			}
			if(e.which == 13 && isCtrl == true)
			{
				forumAddComment(id_z);
			}
		}
	});
}
	
//### Аналогичная функция только по клику на кнопку ###
function keyPressCallforumAddComment(id_z)
{
	$('#forum_zadacha input[name=newComent]').click(function(evtObj)
	{
		if($('#forum_zadacha textarea[name="text"]').val())
		{
			forumAddComment(id_z);
		}
		
		evtObj.preventDefault();
	});
}

//### Функция добавления коментария в обсуждения
function forumAddComment(id_z)
{
	var reception = {};
	reception['textCom'] = $('#forum_zadacha textarea[name="text"]').val();
	reception['id_z'] =	id_z;
	reception['url'] = 'crm' ;
	
	$.ajax
	({
		url			: '/task/processing_SUNWEL.php',
		type		: 'POST',
		data		: 	{ 
							sl			: 'newComent',
							reception    : reception
						},
		processData	: true,
		success		: function(html)
					{
						$('#newZ textarea').before(html);
						$('#task #newZ textarea[name="text"]').val('');
						
						if($('table.zadacha-table').length)
						{
							if($('table.zadacha-table td[onclick="callForum('+id_z+')"]').find('span').length)
							{
								var tCC = $('table.zadacha-table td[onclick="callForum('+id_z+')"]').find('span').text().split(' ');
								var html = parseInt(tCC[1])+1;
								$('table.zadacha-table td[onclick="callForum('+id_z+')"]').find('span').text(tCC[0]+' '+html);
							}
							else
							{
								var html = "<br/><span class='commentZ'>Комментариев 1</span>";
								$('table.zadacha-table td[onclick="callForum('+id_z+')"]').append(html);
							}
						}
						
						if($('table.lc-table').length)
						{
							if($('table.lc-table td[onclick="callForum('+id_z+')"]').find('span').length)
							{
								var tCC = $('table.lc-table td[onclick="callForum('+id_z+')"]').find('span').text().split(' ');
								var html = parseInt(tCC[1])+1;
								$('table.lc-table td[onclick="callForum('+id_z+')"]').find('span').text(tCC[0]+' '+html);
							}
							else
							{
								var html = "<br/><span class='commentZ'>Комментариев 1</span>";
								$('table.lc-table td[onclick="callForum('+id_z+')"]').append(html);
							}
						}
					},
		error		: function()
					{
						alert('комментарий не добавлен');
					}
	});
	
	return false;	
}

//создание задачи	
function createZadacha()
{
	//если формы нет загружаю
	$.ajax({
		url		: '/task/processing_SUNWEL.php',
		type	: 'POST',
		data	:{
					sl 	: 'createTaskForm'
				 },
		success	: function(data)
				 {
					$('#newZ, .modal-backdrop').remove();
					//загрузка формы в блок
					$('#dialogs').append(data);
					//инициализация календарей
					$(function() {
						$('#dialogs #newZ .dpicker').datetimepicker({
							language: 'pt-BR'
						});
					});
					$('#dialogs #newZ').modal();

					$('[name="save"]').click(function(){
						var reception = {};
						//проверка на наличие даты
						if($('input[name="date_finish"]').val())
						{ 

							//формирование даты ф в юникс метку
							function date2timestamp(year, month, day, hour, min, sec) 
							{  
								return (Date.UTC(year, month-1, day, hour, min, sec) / 1000);  
							} 

							//разбиение даты для функции
							reception['dtF']= $('input[name="date_finish"]').val();
							var DateTime 	= reception['dtF'].split(' ');
							var DateArr 	= DateTime[0].split('.');
							var TimeArr 	= DateTime[1].split(':');
							var j_Unix_d_f = date2timestamp(DateArr[2],DateArr[1],DateArr[0],TimeArr[0],TimeArr[1],0);

							//выравнивание времени по часовому поясу
							j_Unix_d_f -= 21600;

							//определение текущего времени в юникс метке
							function time()
							{
								return parseInt(new Date().getTime()/1000);
							}

							//сравнение текущего времени с установленым, если установленое время больше идём дальше по коду
							if(time() < j_Unix_d_f)
							{
								//проверка введено ли название задачи
								if($('textarea[name="caption_zadachi"]').val())
								{
									reception['captionZ']		= $('textarea[name="caption_zadachi"]').val();
									reception['ispolnitel'] 	= $('select[name="menu_is"]').val();
									reception['prioritet']		= $('select[name="pr"]').val();
									reception['textZ']			= $('textarea[name="text_zadachi"]').val();

									//проверяем чекед на наличие галочки, оптравлять имейл или нет
									reception['toEmail'] = 'off';
									if($('input[name="toEmail"]').is(":checked"))
									{
										reception['toEmail'] = $('input[name="toEmail"]').val();
									}

									processingSL( 'new_zadacha' , reception );
									
								//если название задачи не введено	
								}
								else
								{
									bootbox.alert('название задачи не введено');	
								}

							//если время введено не правильно	
							}
							else
							{
								bootbox.alert('сроки исполнения меньше текушего времени');
							}

						//если дата не введена	
						}
						else
						{
							bootbox.alert('дата не введена');
						}
					});
					containerFile();		
				 },
		error	: function()
				 {
					alert('ошибка загрузки формы'); 
				 }
	});
}

//загрузка формы загрузки файлов
function containerFile()
{
	$('button.add_file').click(function()
	{
		$('#div_container_ss #loading').load( "/task/crm/other/AJAXdown/in.php");
	});
}

//вывод обсуждения в модальное окно
function callForum(id_z)
{
	var reception = {};
	reception['id_z'] = id_z;
	reception['url'] = 'crm' ;
	
	$.ajax({
		url			: '/task/processing_SUNWEL.php',
		type		: 'POST',
		dataType	: "json",
		data		:{
						sl			: 'contentforum',
						reception	: reception
				 	},
		success		: function(data){
						//загрузка формы в блок
						$('#dialogs #newZ, .modal-backdrop').remove();
						$('#dialogs').append(data);data = null;
						
						forumAddInfoZ(reception['id_z']);
						zadachaSetPrioritet(reception['id_z']);
						keyPressCallforumAddComment(reception['id_z']);
						komboPressCallforumAddComment(reception['id_z']);
						zCancel(reception['id_z']); 
						zTocomplete(reception['id_z']);
						zReturn(reception['id_z']);
						zComplete(reception['id_z']);
						//alertHelpMessage();
						
						$('#newZ').modal();
					},
		error		: function(){
						alert('ошибка загрузки формы'); 
					}
	});
}

//////////////////////////////////////////////
////////          ОТЧЁТЫ            //////////	
//////////////////////////////////////////////

function processingReport(url)
{
	$.cookie('reqvestUrl','R');
	$(function() {
		$('#tab_requere_report .dpicker').datetimepicker({
			language: 'pt-BR'
		});
	});
	
	SunwelLoadfiltr();
	SunwelViewHideCont();

	createReport(url);
	ReportEditInfo();
	
	$('#report_view button[name=sendFiltr]').click(function()
	{
		 processingSL('reportFiltr');
	});

	 processingSL('reportFiltr');
	//alertHelpMessage();
}

//### функция добавление описания в отчёт ###
function ReportEditInfo()
{
	$('.report_td').dblclick(function(evtObj)
	{
		//alert($(this).prev('.nameuser').attr('value'));
		//alert($.cookie('id_user'));
		if($(this).prev('.nameuser').attr('value') == $.cookie('id_user'))
		{
			var id_user = $.cookie('id_user');
			
			var oldText = $(this).text();
			$(this).text('');
			$(this).css({
							'padding':'0',
							'margin':'0'
						});
			var textArea = $('<textarea></textarea>').addClass('textarea_width').attr('placeholder','добавьте описание и кликните возле поля ввода').css({
																						'border':'none',
																						'margin':'0'
																					});
			$(this).append(textArea);
			$(textArea).focus();
			
			var id_ds = $(this).parents('tr').attr('id');
			
			//отправляем запрос на сервер
			//$('.text button[name="save_edit_info"]').click(function()
			$('.report_td textarea').blur(function()
			{
				//если текст введён то оправляем запрос на сервер
				if($('.textarea_width').val())
				{
					var newText = $('.textarea_width').val();
				
					$.ajax
					({
						url			: '/task/crm/company/Processing_task.php',
						type		: 'POST',
						dataType	: "json",
						data		: 	{ 
											edit_r 	: 'OK', 
											text	: newText,
											id_ds	: id_ds,
											id_user : id_user
										},
						success		: 	function(data, textStatus)
										{
											$(this,'.textarea_width').remove();
											$('tr[id="'+id_ds+'"]').children('.report_td').text(data).css('padding','0 0 0 30px');
											//$(this).text(data);
										}
					});
					
					var allText = oldText + newText;
					$(this,'.textarea_width').remove();
					$('tr[id="'+id_ds+'"]').children('.report_td').text(allText).css('padding','0 0 0 30px');
					
				}
				else
				{
					$(this,'.textarea_width').remove();
					$('tr[id="'+id_ds+'"]').children('.report_td').text(oldText).css('padding','0 0 0 30px');
				}		
			});
			//evtObj.preventDefault();
		}
	});	
}

//### вывод окна создания отчёта ###
function createReport(url)
{
	$('#report_view a[name=add]').click( function()
	{
		$.ajax({
			url		: url,
			type	: 'POST',
			dataType: 'json',
			data	: {
						sl : 'createReportForm'
					 },
			success	: function(data)
					 {
						//загрузка формы в блок
						$('#div_form_report, .modal-backdrop').remove();
						$('#report_view').append(data);
						$(function() {
							$('#div_form_report .dpicker').datetimepicker({
								pickDate: false
							});
						});
						$('#div_form_report').modal();

						$('#div_form_report button[name=save]').click(function(){
							//проверка на ввод даты в поле начала
							if($('#div_form_report input[name="date_start"]').val())
							{
								//проверка на ввод даты в поле завершения
								if($('#div_form_report input[name="date_finish"]').val())
								{

									//формирование даты ф в юникс метку
									function date2timestamp(year, month, day, hour, min, sec) 
									{  
										return (Date.UTC(year, month-1, day, hour, min, sec) / 1000);  
									} 
									
									var reception = {};
									reception['url'] = 'crm' ;
									
									//разбиение даты начала для функции
									reception['j_d_s']	= $('#div_form_report input[name="date_start"]').val();
									var DateTime_s 	= reception['j_d_s'].split(' ');
									var DateArr_s 	= DateTime_s[0].split('.');
									var TimeArr_s 	= DateTime_s[1].split(':');
									var j_Unix_d_s = date2timestamp(DateArr_s[2],DateArr_s[1],DateArr_s[0],TimeArr_s[0],TimeArr_s[1],0);

									//разбиение даты завершения для функции
									reception['j_d_f'] 	= $('#div_form_report input[name="date_finish"]').val();
									var DateTime_f 	= reception['j_d_f'].split(' ');
									var DateArr_f 	= DateTime_f[0].split('.');
									var TimeArr_f	= DateTime_f[1].split(':');
									var j_Unix_d_f = date2timestamp(DateArr_f[2],DateArr_f[1],DateArr_f[0],TimeArr_f[0],TimeArr_f[1],0);

									//сравнение текущего времени с установленым, если установленое время больше идём дальше по коду
									if(j_Unix_d_s < j_Unix_d_f)
									{

										//проверка на наличие текста
										if($('#div_form_report textarea[name="report"]').val())
										{
											reception['report'] = $('#div_form_report textarea[name="report"]').val();

											$.ajax({
												url			: url,
												type		: 'POST',
												dataType	: "json",
												data		:{
																sl			: 'newReport',
																reception	: reception
															 },
													success	: function(data)
															 {
																if($('.report_head_td').length)
																{
																	var answer = data.split('|');
																	$('.report_head_td:first').after(answer[0]);
																}
																else
																{
																	var answer = data.split('|');
																	$('.report-table').append(answer[1]);
																	$('.report_head_td:first').after(answer[0]);
																}

																$('input[name="date_start"]').val('');
																$('input[name="date_finish"]').val('');
																$('#div_form_report textarea[name="report"]').val('');
																if($('#div_form_report #filedown').length)
																{
																	$('#div_form_report #filedown').remove();
																}	
															 },
													error	: function()
															 {
																alert('ошибка при создании отчёта'); 
															 }
											});
										}
										else
										{
											bootbox.alert('напишите описание отчёта');
										}
									}
									else
									{
										bootbox.alert('время введено не правильно');	
									}
								}
								//если дата завершения не введена
								else
								{
									bootbox.alert('введите дату завершения работы');
								}
							}
							//если дата начала не введена
							else
							{
								bootbox.alert('введите дату начала работы');	
							}
						});

						//добавленее формы загрузки файлов
						containerFile();
					 },
			error	: function()
					 {
						bootbox.alert('ошибка загрузки формы'); 
					 }
		});
	});
}

//////////////////////////////////////////////
////////        СМС РАССЫЛКА        //////////	
//////////////////////////////////////////////

function allCheckBox()
{
	$('input[type="checkbox"][value="all"]').change(function()
	{
		if($(this).filter(':checked').val())
		{
			$(this).parents(3).siblings('tbody').find('input[type="checkbox"]').attr("checked","checked");
			$(this).parents(3).siblings('tbody').find('tr').css('background','#F0EEEE');
		}
		else
		{
			$(this).parents(3).siblings('tbody').find('input[type="checkbox"]').removeAttr("checked");
			$(this).parents(3).siblings('tbody').find('tr').css('background','none');
		}
	});
	/*
	$('input[type="checkbox"][value!="all"]').change(function()
	{
		$(this).parents(3).siblings('thead').find('td:first input[type="checkbox"][value="all"]').removeAttr("checked");
	});
	
	
	$('.bggrey table tbody tr td input[type="checkbox"]').click(function cheked()
	{
		if($(this).filter(':checked').val())
		{
			$(this).parents('.bggrey table tbody tr').css('background','#F0EEEE');
			$(this).attr("checked","checked");
		}
		else
		{
			$(this).parents('.bggrey table tbody tr').css('background','none');
			$(this).removeAttr("checked");
		}
	});*/
	
	$('.bggrey table tbody tr').click(function()
	{
		//$(this).find('input[type="checkbox"]').click();
		
		//$(this).find('td:first input[type="checkbox"][name="client"]').click();
		
		if($(this).find('td:first input[type="checkbox"]').filter(':checked').val())
		{
			$(this).css('background','none');
			$(this).find('td:first input[type="checkbox"]').removeAttr("checked");
			$(this).parents(2).siblings('thead').find('td:first input[type="checkbox"][value="all"]').removeAttr("checked");
		}
		else
		{
			$(this).css('background','#F0EEEE');
			$(this).find('td:first input[type="checkbox"]').attr("checked","checked");
			
		}/**/
	});
}

function limitChars()
{
	var max = 70; // максимальное кол-во символов
	var countMsg = 1; // кол-во сообщений
	
	$('#tab_sms .modal_message_send textarea[name="message"]').keyup(function(){
		var count = $(this).val().length; // кол-во уже введенных символов
		var num = max - count; // кол-во символов, которое еще можно ввести
		
		if(num > 0){
			// если не достигнут лимит символов
			$('#tab_sms .modal_message_send label[name="countChar"]').text(num+'('+countMsg+')');
		}else{
			countMsg += 1;
			max += 70;
			
			$('#tab_sms .modal_message_send label[name="countChar"]').text(num+'('+countMsg+')');
		}
	});
}

function alertHelpMessageSMS()
{
	$('a.tytip').tooltip({
		placement: 'right'
	});
}

//|||||||||   ОТПРАВКА   ||||||||||
function processing_sms(data, url)
{
	$('#tab_sms').append(data['cont']);
	$('#tab_sms .bggrey').append(data['balans']);
	
	SunwelLoadfiltr();  
	pagination('#divSms'); 
	SunwelViewHideCont(); 

	$('#divSms .Sunwel_filtr button[name="sendFiltr"]').attr('onclick',"processingSL('filtrDeloverySMS');");

	$(function() {
		$('#tab_sms .dpicker').datetimepicker({
			pickTime: false
		});
	});

	/* визуальное отображение что все клиенты выбраны */
	allCheckBox();

	$('#tab_sms div.balans button[name="reflash"]').click(function(){
		var reception = {};
		reception['url'] = 'delivery';
		
		$.ajax({ 
				type		: "POST",
				url			: url,
				data		:{
								sl			: 'updateStatus',
								reception	: reception
							},
				success		:function(data){
								$('.balans').html(data);
							}
			});
	}) ;

	/* при клике по кнопке собщение выводиться модальное окно, с дальнейшей проверкой и отправкой */
	$('#divSms .panel_toolbar a[name="message"]').click(function()
	{
		var html = '';
		html += '<div class="modal modal_message_send" style="display:none;">';
		html += '	<div class="modal-dialog">';
		html += '		<div class="modal-content">';
		html += '			<div class="modal-header">';
		html += '				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
		html += '				<h3>Окно Сообщений</h3>';
		html += '			</div>';
		html += '			<div class="modal-body">';
		html += '				<label class="col-lg-5">выбор шаблона (<a class="tytip" data-animation="false" data-placement="right" data-toggle="tooltip" href="#" title="" data-original-title="возможность выбора готового шаблона для отправки, из выпадающего списка">i</a>)</label>';
		html += '				<select name="selectTemplate" class="form-control">'+ data['option'] + '</select>';
		html += '				<label name="countChar" class="col-lg-5">70(1)</label>';
		html += '				<label class="col-lg-7"><input type="checkbox" name="seveMessage"/> Сохранить сообщение (<a class="tytip" data-animation="false" data-placement="right" data-toggle="tooltip" href="#" title="" data-original-title="сохранить сообщение в шаблоны, для возможности дальнейшего использования и редактирования">i</a>)</label>';
		html += '				<textarea class="form-control" rows="5" name="message" placeholder="введите сообщение"></textarea>';
		html += '				<button type="button" name="send" class="btn btn-primary btn-lg btn-block">Отправить</button>';
		html += '			</div>';
		html += '		</div>';
		html += '	</div>';
		html += '</div>';

		$('#divSms .modal_message_send').remove();
		$('#divSms').append(html);
		$('.modal_message_send').modal(); html = '';
		limitChars(); // подсчёт колличества сообщений
		//alertHelpMessageSMS();

		$('#divSms select[name="selectTemplate"]').change(function(){
			var reception = {};
			reception['url'] = 'delivery';
			reception['thisSelect']  = $('#divSms select[name="selectTemplate"]').val();

			$.ajax({ 
				type		: "POST",
				url			: url,
				dateType	: 'json',
				data		:{
								sl			: 'infoTemplate',
								reception	: reception
							},
				success		:function(data){
								$('#divSms textarea[name="message"]').text(data);
							}
			});
		});

		$('#divSms button[name="send"]').one('click',function()
		{
			var reception = {};
			
			/* определение ИД клиентов для рассылки */
			reception['id_client'] = '';
			if($('#divSms table thead input[name="client"]').filter(':checked').val()) {
				reception['id_client'] = $('#divSms table thead input[name="client"]').filter(':checked').val();
			} else {
				$('#divSms table tbody input[name="client"]').filter(':checked').each(function()
				{
					reception['id_client'] += $(this).val()+'|';
				});
			}
			
			reception['id_mask'] = '';
			if($('#divSms .modal-dialog table thead input[name="mask"]').filter(':checked').val()) {
				reception['id_mask'] = $('#divSms table thead input[name="mask"]').filter(':checked').val();
			} else {
				$('#divSms .modal-dialog table tbody input[name="mask"]').filter(':checked').each(function()
				{
					reception['id_mask'] += $(this).val()+'|';
				});
			}
			
			reception['name']		= $('#divSms .Sunwel_filtr input[name="name"]').val();
			reception['phone'] 		= $('#divSms .Sunwel_filtr input[name="phone"]').val();
			reception['birthday_s'] = $('#divSms .Sunwel_filtr input[name="birthday_s"]').val();
			reception['birthday_f'] = $('#divSms .Sunwel_filtr input[name="birthday_f"]').val();
			reception['email'] 		= $('#divSms .Sunwel_filtr input[name="email"]').val();
			reception['city'] 		= $('#divSms .Sunwel_filtr select[name="city"]').val();
			reception['selCount'] 	= $('#divSms .scroll_bar select[name="selCount"]').val();
			reception['thisPage'] 	= $('#divSms .scroll_bar input[name="thispage"]').val();
			reception['message']	= $('#divSms .modal_message_send textarea[name="message"]').val();
			reception['save']		= $('#divSms .modal_message_send input[type="checkbox"][name="seveMessage"]:checked').val();
			reception['messageToName'] = 1;

			if(reception['id_client']) {
				if(reception['message']) {
					processingSL( 'sendSMS' , reception );
				} else {
					bootbox.alert('сообщение рассылки не введено');
				}
			} else {
				bootbox.alert('клиенты не выбраны');
			}
		});
	});
}

//////////////////////////////////////////////
////////			 УРВ		     /////////
//////////////////////////////////////////////

//|||||  ТАБЕЛЬ ПОСЕЩЕНИЯ   ||||||||||
function sendFiltrURVvisits()
{
	var reception = {};
	var checked = Array(); var i = 0;
	$('#report_visits .Sunwel_filtr input[type=checkbox][name="filtr_urv"]:checked').each(function(){
		checked[i] = $(this).val();
		i++;
	});
	
	reception['dtStart']	= $('#report_visits .Sunwel_filtr input[name="dtStart"]').val();
	reception['dtFinish']	= $('#report_visits .Sunwel_filtr input[name="dtFinish"]').val();
	reception['еmployee']	= $('#report_visits .Sunwel_filtr input[name="employee"]').val();
	reception['role']		= $('#report_visits .Sunwel_filtr input[name="role"]').val(); 
	reception['category']	= $('#report_visits .Sunwel_filtr select[name="category"]').val();
	reception['location']	= $('#report_visits .Sunwel_filtr select[name="location"]').val();
	reception['kpp']		= $('#report_visits .Sunwel_filtr select[name="kpp"]').val();
	reception['checked']	= checked;
	

	$.ajax({
		url 	: '/task/urv/processing_urv.php',
		type	: 'POST',
		data	: 
				{
					sl			: 'filtrVisits',
					reception	: reception
				 },
		success	: function(data)
				{
						$('#report_visits #view_report_visits').empty();	
						$('#report_visits #view_report_visits').append(data);
						$('#report_visits #view_report_visits td').find('span').parent('td').css('background','#F0EEEE');
				},
		error	: function()
				{
					alert('ошибка загрузки данных');  
				}
	});
}

//|||||||||   УПРАВЛЕНИЕ ГРАФИКАМИ   ||||||||||
//создание таблицы для графика
function tableGraphic(nameBlock)
{
	var countDay = parseInt($(nameBlock+' .zfilter input[name=for]').val());
	var countTr = $(nameBlock+' .modal-body table.table tbody tr').length;
	var tr = '';
	
	var realTr = countDay - countTr;
	if(countTr > countDay)
	{
		for(var i = countDay; i < countTr; i++)
		{
			$(nameBlock+' .modal-body table.table tbody tr:last').remove();
		}		
	} 
	else 
	{
		var dateS = "<div class='dpicker'>\n\
						<div class='input-group date'>\n\
							<input class='form-control chb_zaperiod' type='text' name='st' data-format='hh:mm:ss' placeholder='от'>\n\
							<span class='input-group-addon'>\n\
								<span class='glyphicon glyphicon-calendar'></span>\n\
							</span>\n\
						</div>\n\
					</div>";
	
		var dateF = "<div class='dpicker'>\n\
						<div class='input-group date'>\n\
							<input class='form-control chb_zaperiod' type='text' name='ft' data-format='hh:mm:ss' placeholder='до'>\n\
							<span class='input-group-addon'>\n\
								<span class='glyphicon glyphicon-calendar'></span>\n\
							</span>\n\
						</div>\n\
					</div>";
		
		var lunchS = "<div class='dpicker'>\n\
						<div class='input-group date'>\n\
							<input class='form-control chb_zaperiod' type='text' name='sl' data-format='hh:mm:ss' placeholder='от'>\n\
							<span class='input-group-addon'>\n\
								<span class='glyphicon glyphicon-calendar'></span>\n\
							</span>\n\
						</div>\n\
					</div>";
		
		var lunchF = "<div class='dpicker'>\n\
						<div class='input-group date'>\n\
							<input class='form-control chb_zaperiod' type='text' name='fl' data-format='hh:mm:ss' placeholder='до'>\n\
							<span class='input-group-addon'>\n\
								<span class='glyphicon glyphicon-calendar'></span>\n\
							</span>\n\
						</div>\n\
					</div>";
		
		for(var i = (countTr + 1); i <= (countTr + realTr); i++)
		{
			tr += '<tr><td>'+ i +'</td><td>'+ dateS +'</td><td>'+ dateF +'</td><td>'+ lunchS +'</td><td>'+ lunchF +'</td></tr>';
		}
	}
	
	if($(nameBlock+' .modal-body table.table').length)
	{
		if(countTr < countDay)
		{
			$(nameBlock+' .modal-body table.table tbody').append(tr);
		}
	} 
	else 
	{
		$(nameBlock+' .modal-body table.table').remove();
		
		var table  = '<table class="table table-hover">';
			table += '<thead>';
				table += '<tr>';
					table += '<th>день</th>';
					table += '<th>начало рабочего дня</th>';
					table += '<th>конец рабочего дня</th>';
					table += '<th>начало обеда</th>';
					table += '<th>конец обеда</th>';
				table += '</tr>';
			table += '</thead>';
			table += '<tbody>';
				table += tr;
			table += '</tbody>';
		table += '</table>';
		
		$(nameBlock+' .modal-body').append(table);
	}

}

//создание графика
function createGrafic(url,nameClick,nameBlock) 
{
	SunwelViewHideCont();
	$('#tab_urv_graphic .panel_toolbar a[name='+nameClick+']').click(function()
	{
		var reception = {};
		reception['url'] = 'urv' ;
		
		var sl = 'formCreateGraphic';
				
		if(nameClick == 'edit' || nameClick == 'del' )
		{
			reception['idTable'] = $('#urv_graphic').myTreeView('getSelected').id;
		}
		if(nameClick == 'edit')
		{
			sl = 'formEditGraphic';
		}
		if(nameClick == 'del')
		{
			sl = 'formDeleteGraphic';
		}
	
		$.ajax({
			url 	: url,
			type	: 'POST',
			dataType: 'json',
			data	: 
					{
						sl		: sl,
						reception : reception
					},
			success	: function(data)
					{
						if(data == 'del')
						{
							$('#urv_graphic').myTreeView('reload');
							bootbox.alert('График успешно удалён');
							return;
						}
						
						$('#dialogs '+nameBlock).remove();
						$('#dialogs').append(data);
						$('#dialogs '+nameBlock).modal();
						
						$(nameBlock+' .zfilter input[name=for]').change(function(){
							tableGraphic(nameBlock);
							$(function() {
								$('#dialogs '+nameBlock+' table .dpicker').datetimepicker({
									pickDate: false
								});
							});
						});
						tableGraphic(nameBlock);
						
						$(function() {
							$('#dialogs '+nameBlock+' .zfilter .dpicker').datetimepicker({
								pickTime: false
							});
						});
						$(function() {
							$('#dialogs '+nameBlock+' table .dpicker').datetimepicker({
								pickDate: false
							});
						});
						
						var reception = {};
						reception['url'] = 'urv' ;
		
						$('#dialogs '+nameBlock+' button[name=save]').click(function()
						{
							reception['name'] = $('#dialogs '+nameBlock+' input[name="caption"]').val();
							if(reception['name']) 
							{
								reception['countday'] = $('#dialogs '+nameBlock+' input[name="for"]').val();
								if(reception['countday']) 
								{
									reception['dtStart'] = $('#dialogs '+nameBlock+' input[name="dtStart"]').val();
									if(reception['dtStart'] ) 
									{
										reception['typeGraphic']  = $('#dialogs '+nameBlock+' select[name="type"]').val();
										reception['description']  = $('#dialogs '+nameBlock+' textarea[name="description"]').val();
										reception['idTable'] 	  = $('#dialogs '+nameBlock+' table').attr('id');
										var num = Array();
										var st = Array();
										var ft = Array();
										var sl = Array();
										var fl = Array();
										var i = 0;
										
										$('#dialogs '+nameBlock+' table tbody tr').each(function(){
											$(this).find('td').each(function(){
												if(!$(this).find('input[name]').val())
												{
													$(this).find('input[name]').val('00:00:00');
												}
											});
											
											num[i] = $(this).first('td:first').text().trim();
											st[i] = $(this).find('input[name="st"]').val().trim();
											ft[i] = $(this).find('input[name="ft"]').val().trim();
											sl[i] = $(this).find('input[name="sl"]').val().trim();
											fl[i] = $(this).find('input[name="fl"]').val().trim();
											i++;
										});
										
										reception['num'] = num;
										reception['st'] = st;
										reception['ft'] = ft;
										reception['sl'] = sl;
										reception['fl'] = fl;
										
										if(nameClick == 'create')
											sl = 'createGraphic';
										if(nameClick == 'edit') {
											sl = 'editGraphic';
										}
												
										$.ajax({
											url 	: url,
											type	: 'POST',
											dataType: 'json',
											data	:  
													{
														sl			: sl,
														reception	: reception
													},
											success	: function(data)
													{
														bootbox.alert(data);
														vievGraficTable(url);
														 $('#urv_graphic').myTreeView('reload');
													},
											error	: function()
													{
														bootbox.alert('ошибка отправки данных');  
													}
										});
									} else {
										bootbox.alert('введите точку отсчёта');
									}
								} else {
									bootbox.alert('введите колличество дней в графике');
								}
							} else {
								bootbox.alert('введите название графика');
							}		
						});
					},
			error	: function()
					{
						bootbox.alert('ошибка загрузки данных');  
					}
		});
	});
}

function vievGraficTable(url)
{
	$('#urv_graphic').myTreeView({  
		url:url+'?sl=filtrGraphic',
		headers: [{title:'Название',name:'name'},{title:'Описание',name:'description'},{title:'Цикл',name:'for'},{title:'Точка отсчёта',name:'point'},{title:'Тип',name:'type'}],
		pagination:false,
		tree:false,
		numeration: true,
		pagecount:[50,100,200]
	});
}

function processing_urv_Emp(url)
{
	SunwelLoadfiltr();
	$(function() {
		$('#report_employee .dpicker').datetimepicker({
			language: 'pt-BR'
		});
	});
}
//|||||||||  ГЛАВНАЯ СТРАНИЦА УРВ

function activeTurniket(status){
	var data = '<Header>';
    data += '<order>turniket</order>';
	data += '<status>'+status+'</status>';
    data += '</Header>';
	
    $.ajax({
       type: "POST",
       url: "http://localhost:12345",
       data: data,
       dataType: "script"
   });
}

//проверка сотрудника
function CheckEmployee(Barcode){
	var reception = {};
	reception['Barcode'] = Barcode;
	$.ajax({
		url			: "../processing_SUNWEL.php",
		type		: 'POST',
		async		: false,
		dataType	: "json",
		data		: {
						reception	: reception, 
						sl			: "do_in_out"
					},
		success		:function(data)
					{
						if (data.spec) {
							location = '../../login.php?do=logout';
							return;
						}
						if ( data.err)  {
							alert(data.err);
							//bootbox.alert(data.err);
						} else{
							URV_Continue = 0;
							$.ajax({
								url		: data.arr["avatar_path"],
								type	:'HEAD',
								error	: function(){
											$("#JPGfoto").attr("src", "images/fotononame.jpg");
										},
								success	: function(){
											$("#JPGfoto").attr("src", data.arr["avatar_path"]);
										}
							});
							
							$("#dtd1").empty(); var stat;
							
							// УХОД
							if (data.arr["state"]==1) 
							{
								$("#dtd1").append("До свидания!");
								var stat = 0;
								$('.urv_employes .emp').each(function(){
									if($(this).text().trim() == data.arr["name"]) {
										  $(this).parents('.urv_employes').remove();
									}
								});
							  
							// ПРИХОД 
							} else {
								$("#dtd1").append("Здравствуйте!");
								
								$('.urv_employes .emp').each(function(){
									if($(this).text().trim() == data.arr["name"]) {
										  $(this).parents('.urv_employes').remove();
									}
								});
								var stat = 1;
								
								var htmlEmp = '<div class="urv_employes">\n\
													<div class="emp">\n\
														<span class="glyphicon glyphicon-user"></span> '+data.arr["name"]+'\n\
													</div>\n\
													<div class="emp_time">\n\
														<span class="glyphicon glyphicon-time"></span> '+dateTime(2)+'\n\
													</div>\n\
													<div class="clear"></div>\n\
												</div>';
								
								if($('#DScrollY .urv_employes').length) {
									$('#DScrollY .urv_employes:first').before(htmlEmp);
								} else {
									$('#DScrollY').append(htmlEmp);
								}
							}
							
							activeTurniket(stat);
							$("#dtd3").empty();
							$("#dtd3").append(data.arr["name"]);
							$("#DDP2").show("fast");
							$("#DDP").hide("fast");
							//processingSL('reloadEmp');
							setTimeout(function() {
								URV_Continue = 1;
								$("#DDP2").hide("fast");
								$("#DDP").show("fast");
							}, 2000);
						}	
					}
	});
}

// Обработчик нажатия кнопки
function pressed()
{
	if($(".horizontal_blockL #DDP").css('display') == 'block')
	{
		$(window).keypress(function(e){
		
			var shtrih = 0;
			switch (e.keyCode) {
				case 13 :
							shtrih = $('#viewTextPass label').text();
							$("#steklo").hide();
							$('#viewTextPass').hide();
							$('#viewTextPass label').text('');
							console.log(shtrih);
							if(URV_Continue == 1) {
								if (shtrih!='')
								{
									CheckEmployee(shtrih);
								}
							}
							shtrih = '';
				break;
				case 08 :
							shtrih = $('#viewTextPass label').text().slice(0,-1);
							$('#viewTextPass label').text(shtrih);
							return false;
				break;

				default :	var reges = /[0-9]/;
							if(reges.test(getChar(e))) {
								shtrih = $('#viewTextPass label').text().trim();
								shtrih += getChar(e);
								$('#viewTextPass').show();
								$('#viewTextPass label').text(shtrih);
							}
			}
			delete e;

		});
	}
} 

// Обработчик калькулятора
function calculator()
{
		$("#calc button").click(function(){
			var shtrih = 0;
			var c = $(this).html();
			 switch (true){
				 case /[0-9]/.test(c):
						$('#viewTextPass').show();
						var oldText = $('#viewTextPass label').text().trim();
						$('#viewTextPass label').text(oldText + c);
						shtrih = oldText + c;
				 break;
				 case String.fromCharCode(9224)+"<br>"+String.fromCharCode(8592)==c:
					shtrih = $('#viewTextPass label').text().slice(0,-1);
					$('#viewTextPass label').text(shtrih);
				 break;
				 case String.fromCharCode(10004)==c:
					 shtrih = $('#viewTextPass label').text().trim();
					 CheckEmployee(shtrih);
					 shtrih = '';
					$("#steklo").hide();$("#pwdcalc").val("");
					$('#viewTextPass').hide(); $('#viewTextPass label').text(shtrih); 
				 break;                
			 }
		});
}

function getChar(event) 
{
    if (event.which == null) 
      {  // IE
        if (event.keyCode < 32) return null; // спец. символ
          return String.fromCharCode(event.keyCode);
      }
    if (event.which!=0 && event.charCode!=0) 
      { // все кроме IE
        if (event.which < 32) return null; // спец. символ
          return String.fromCharCode(event.which); // остальные
      }
    return null; // спец. символ
}

function buttonExit()
{
	var isCtrl = false;
	$(document).keypress(function (e) 
	{
		if(e.which === 17 || e.which === 13)
		{ 
			isCtrl=false; 
		}
	}).keydown(function (e) 
	{
		if(e.which === 17)
		{ 
			isCtrl = true;
		}
		if(e.which === 13 && isCtrl === true)
		{
			$('body').append('<a href="../../login.php?do=logout" class="btn btn-danger btn-lg" id="ButtonYop"><span class="glyphicon glyphicon-off"></span> Выход</a>');
		}
	});
}

//////////////////////////////////////////////
////////			 ALL		     /////////
//////////////////////////////////////////////

// загрузка фильтра
function SunwelLoadfiltr()
{
	$('a[name="loadfiltr"]').click(function(){
		if($(this).is('.active'))
		{
			$(this).parent().siblings('.Sunwel_filtr').css('display','none');
		}
		else
		{
			$(this).parent().siblings('.Sunwel_filtr').css('display','block');
		}
	});
	
	$('button[name="SunwelClearFiltr"]').click(function(){
		$(this).parent().siblings('.zfilter').find('input[type="text"], select').val('');
		$(this).parent().siblings('.zfilter').find('textarea').text('');
		$(this).parent().siblings('.zfilter').find('input[type="checkbox"]').removeAttr("checked");
		$('div.filtr_Sms').siblings('table').find('input[type="checkbox"]').removeAttr("checked");
	});
	
	$('a[name="loadmask"]').click(function(){
		var reception = {};
		reception['url'] = 'delivery' ;
		$.ajax({ 
			type		: "POST",
			url			: '/task/processing_SUNWEL.php',
			data		:{
							sl			: 'selectMask',
							reception	: reception
						},
			success		:function(data){
							$('table.mainTable thead input[name="client"]').removeAttr("checked");
							
							var html = '';
							html += '<div class="modal modal_message" style="display:none;">';
							html += '	<div class="modal-dialog">';
							html += '		<div class="modal-content">';
							html += '			<div class="modal-header">';
							html += '				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
							html += '				<h3>Маски ввода</h3>';
							html += '			</div>';
							html += '			<div class="modal-body">';
							html += '				<label class="col-lg-5">выбор маски (<a class="tytip" data-animation="false" data-placement="right" data-toggle="tooltip" href="#" title="" data-original-title="выбор маски для фильтрации телефонов">i</a>)</label>';
							html += '				<table class="table table-condensed">'+ data +'</table>';
							html += '				<button type="button" name="send" class="btn btn-primary btn-lg btn-block" aria-hidden="true" data-dismiss="modal" onclick="processingSL(\'filtrDeloverySMS\');">Приминить</button>';
							html += '			</div>';
							html += '		</div>';
							html += '	</div>';
							html += '</div>';
							
							data = null;
							$('#dialogs').empty();
							$('#divSms .modal_message').remove();
							$('#divSms').append(html);
							SunwelallCheckBox('#divSms modal');
							$('.modal_message').modal(); html = '';
						}
		});
	});
}

// Pagination
function pagination(thisBlock)
{
	// ручной выбор страницы 
	$(thisBlock+' div.scroll_bar input[name="thispage"]').change(function()
	{

		//alert('1 условие '+thisBlock);
		var page = $(thisBlock+' .scroll_bar .count_page').text();		
		var maxPage = page.split(' ');	
		var thisPage = $(thisBlock+' .scroll_bar input[name="thispage"]').val(); //alert(thisPage+' '+maxPage[1]);
		if(parseInt(thisPage) <= parseInt(maxPage[1]))
		{
			switch(thisBlock)
			{
				case '#zadacha_view'		: processingSL('TaskFilter');		break;
				case '#divSms'				: processingSL('filtrDeloverySMS');	break;
				case '#tab_urv_empWorks'	: processingSL('filtrEmpWorks');	break;
				case '#tab_frameURVhistory'	: processingSL('filtrURVhistory');	break;
			}
		}
	});
	// выбор отображения записей на страницу 
	$(thisBlock+' div.scroll_bar select[name="selCount"]').change(function()
	{
		//alert('селект');
		$(thisBlock+' .scroll_bar input[name="thispage"]').val(1);
		switch(thisBlock)
		{
			case '#zadacha_view'		: processingSL('TaskFilter');		break;
			case '#divSms'				: processingSL('filtrDeloverySMS');	break;
			case '#tab_urv_empWorks'	: processingSL('filtrEmpWorks');	break;
			case '#tab_frameURVhistory'	: processingSL('filtrURVhistory');	break;
		}
	});
	// следующая страница 
	$(thisBlock+' div.scroll_bar #next').click(function()
	{
		//alert('далее');
		var page = $(thisBlock+' .scroll_bar .count_page').text();			
		var maxPage = page.split(' ');
		var thisPage = $(thisBlock+' .scroll_bar [name="thispage"]').val();
		thisPage = parseInt(thisPage) + 1;
		if(parseInt(thisPage) <= parseInt(maxPage[1]))
		{
			$(thisBlock+' .scroll_bar [name="thispage"]').val(thisPage);
			switch(thisBlock)
			{
				case '#zadacha_view'		: processingSL('TaskFilter');		break;
				case '#divSms'				: processingSL('filtrDeloverySMS');	break;
				case '#tab_urv_empWorks'	: processingSL('filtrEmpWorks');	break;
				case '#tab_frameURVhistory'	: processingSL('filtrURVhistory');	break;
			}
		}
	});
	// предыдущая страница 
	$(thisBlock+' div.scroll_bar #prev').click(function()
	{
		//alert('назад');
		var page = $(thisBlock+' .scroll_bar .count_page').text();
		var maxPage = page.split(' ');
		var thisPage = $(thisBlock+' .scroll_bar [name="thispage"]').val();
		thisPage = parseInt(thisPage) - 1;
		if(thisPage >= 1)
		{
			$(thisBlock+' .scroll_bar [name="thispage"]').val(thisPage);
			switch(thisBlock)
			{
				case '#zadacha_view'		: processingSL('TaskFilter');		break;
				case '#divSms'				: processingSL('filtrDeloverySMS');	break;
				case '#tab_urv_empWorks'	: processingSL('filtrEmpWorks');	break;
				case '#tab_frameURVhistory'	: processingSL('filtrURVhistory');	break;
			}
		}
	});
}

//изменение типа активной кнопки, и изменение видимости контента
function SunwelViewHideCont()
{
	$('div.panel_toolbar [name=add], div.panel_toolbar [name=edit], div.panel_toolbar [name=del], div.panel_toolbar [name=create]').click(function(){
		var obj = $(this);
		var marker = $(this).attr('name');
		
		$(obj).parent().siblings('div[name]').css('display','none');
		$(obj).parent().siblings('div[name='+marker+']').css('display','block');
		
		$(obj).siblings().removeClass('active');
	});
}

function SunwelallCheckBox(thisBlock)
{
	$(thisBlock+' input[type="checkbox"][value="all"]').change(function()
	{
		if($(this).filter(':checked').val())
		{
			$(this).parents(3).siblings('tbody').find('input[type="checkbox"]').attr("checked","checked");
			$(this).parents(3).siblings('tbody').find('tr').css('background','#F0EEEE');
		}
		else
		{
			$(this).parents(3).siblings('tbody').find('input[type="checkbox"]').removeAttr("checked");
			$(this).parents(3).siblings('tbody').find('tr').css('background','none');
		}
	});
	/*
	$('input[type="checkbox"][value!="all"]').change(function()
	{
		$(this).parents(3).siblings('thead').find('td:first input[type="checkbox"][value="all"]').removeAttr("checked");
	});
	
	
	$('.bggrey table tbody tr td input[type="checkbox"]').click(function cheked()
	{
		if($(this).filter(':checked').val())
		{
			$(this).parents('.bggrey table tbody tr').css('background','#F0EEEE');
			$(this).attr("checked","checked");
		}
		else
		{
			$(this).parents('.bggrey table tbody tr').css('background','none');
			$(this).removeAttr("checked");
		}
	});*/
	
	$('.bggrey table tbody tr').click(function()
	{
		//$(this).find('input[type="checkbox"]').click();
		
		//$(this).find('td:first input[type="checkbox"][name="client"]').click();
		
		if($(this).find('td:first input[type="checkbox"]').filter(':checked').val())
		{
			$(this).css('background','none');
			$(this).find('td:first input[type="checkbox"]').removeAttr("checked");
			$(this).parents(2).siblings('thead').find('td:first input[type="checkbox"][value="all"]').removeAttr("checked");
		}
		else
		{
			$(this).css('background','#F0EEEE');
			$(this).find('td:first input[type="checkbox"]').attr("checked","checked");
			
		}/**/
	});
}

// сортировка
function SunwelSortTable(url) 
{
	$('.SunwelSort thead tr th').click(function() {
		
		var reception = {};
		reception['sort'] = 'inc';
		var obj = $(this);
		var sl = '';
		
		if($(this).find('span').length) 
		{			
			if($(this).find('span').hasClass('glyphicon-sort-by-attributes-alt')) 
			{
				$(this).parents('thead').find('span').remove();
				$(this).html('<span class="glyphicon glyphicon-sort-by-attributes"></span> '+$(this).text());
				reception['text'] = $(this).text().trim();
			} else {
				$(this).parents('thead').find('span').remove();
				$(this).html('<span class="glyphicon glyphicon-sort-by-attributes-alt"></span> '+$(this).text());
				reception['text'] = $(this).text().trim();
				reception['sort'] = 'desc';
			}
		} else {
			$(this).parents('thead').find('span').remove();
			$(this).html('<span class="glyphicon glyphicon-sort-by-attributes"></span> '+$(this).text());
			reception['text'] = $(this).text().trim();
		}
		
		if(obj.parents('#tab_z_notice_w').length)
		{
			sl = 'z_notice_w';
		}
		if(obj.parents('#tab_z_notice_nw').length)
		{
			sl = 'z_notice_nw';
		}
		
		$.ajax({
			url 	: url,
			type	: 'POST',
			dataType: 'json',
			data	:  
					{
						sl			: sl,
						reception	: reception
					},
			success	: function(data)
					{
						obj.parents('.SunwelSort').find('tbody').html(data);
					},
			error	: function()
					{
						bootbox.alert('ошибка отправки данных');  
					}
		});
	});
}

//часы
function startTime() 
{
	var monthNames = [ "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря" ]; 
	var dayNames= ["Воскресенье - ","Понедельник - ","Вторник - ","Среда - ","Четверг - ","Пятница - ","Суббота - "];

	// Create a newDate() object
	var newDate = new Date();
	// Extract the current date from Date object 
	newDate.setDate(newDate.getDate());
	// Output the day, date, month and year    
	$('.urv_timediv h2').html(dayNames[newDate.getDay()] + " " + newDate.getDate() + ' ' + monthNames[newDate.getMonth()] + '<br />' + newDate.getFullYear() + ' Год');

	/*setInterval( function() {
		// Create a newDate() object and extract the seconds of the current time on the visitor's
		var seconds = new Date().getSeconds();
		// Add a leading zero to seconds value
		$("#sec").html(( seconds < 10 ? "0" : "" ) + seconds);
		},1000);*/

	setInterval( function() {
		// Create a newDate() object and extract the minutes of the current time on the visitor's
		var minutes = new Date().getMinutes();
		// Add a leading zero to the minutes value
		$("#min").html(( minutes < 10 ? "0" : "" ) + minutes);
		},1000);

	setInterval( function() {
		// Create a newDate() object and extract the hours of the current time on the visitor's
		var hours = new Date().getHours();
		// Add a leading zero to the hours value
		$("#hours").html(( hours < 10 ? "0" : "" ) + hours);
		}, 1000);
}

function dateTime(num)
{
	var curDate = new Date();
	
	var year = curDate.getFullYear();
	var months = curDate.getMonth();
	
	
	var monthString = '';
	switch (months) 
	{
		case 0: monthString = '01'; break;
		case 1: monthString = '02'; break;
		case 2: monthString = '03'; break;
		case 3: monthString = '04'; break;
		case 4: monthString = '05'; break;
		case 5: monthString = '06'; break;
		case 6: monthString = '07'; break;
		case 7: monthString = '08'; break;
		case 8: monthString = '09'; break;
		case 9: monthString = '10'; break;
		case 10: monthString = '11'; break;
		case 11: monthString = '12'; break;
	}

	var days = curDate.getDate();
	var hours = curDate.getHours();
	var minutes = curDate.getMinutes();
	var seconds = curDate.getSeconds();
	
	if (hours < 10) { hours = "0" + hours; }
	if (minutes < 10) { minutes = "0" + minutes; }
	if (seconds < 10) { seconds = "0" + seconds; }
	
	var datastr='';
	
	switch(num) 
	{
		case 1 : datastr = hours+':'+minutes + ' ' + days+'.'+monthString+'.'+year; break;
		case 2 : datastr = year+'-'+monthString+'-'+ days+' '+hours+':'+minutes+':'+seconds; break;
	}
	
	
	return(datastr);
}

function alertHelpMessage()
{
	//проверка включение подсказок (0 включены)
	if($.cookie('Hepls') == '1')
	{
		return false;
	}
	
	//### Задачи
	// кнопка добавления задач
	if($('button[name="create_zadacha"]').length)
	{
		$('button[name="create_zadacha"]').tooltip({
			placement: 'right',
			title: 'добавление задачи сотруднику'
		});
	}
	//### Админка
	// кнопка изменения профиля
	if($('button[name="edit_profile"]').length)
	{
		$('button[name="edit_profile"]').tooltip({
			placement: 'right',
			title: 'изменение личной информации'
		});
	}
	// кнопка создания менеджера
	if($('button[name="edit_group"]').length)
	{
		$('button[name="edit_group"]').tooltip({
			placement: 'bottom',
			title: 'выдача сотруднику прав менеджера, и составление списка сотрудников доступных ему для просмотра'
		});
	}
	// кнопка просмотра менеджера
	if($('button[name="view_group"]').length)
	{
		$('button[name="view_group"]').tooltip({
			placement: 'bottom',
			title: 'список сотрудников с правами менеджера'
		});
	}
	//### Панель Админки
	// кнопка насткоек
	if($('#settings').length)
	{
		$('#settings').tooltip({
			placement: 'left',
			title: 'настроки'
		});
	}
	// кнопка просмотра заданий для проверки
	if($('#z_notice_nw').length)
	{
		$('#z_notice_nw').tooltip({
			placement: 'bottom',
			title: 'список заданий для проверки'
		});
	}
	// кнопка текущих заданий
	if($('#z_notice_w').length)
	{
		$('#z_notice_w').tooltip({
			placement: 'bottom',
			title: 'текущие задания'
		});
	}
	//### Задачи/ЛК
	// кнопка отмены задания
	if($('button[name="cancel"]').length)
	{
		$('button[name="cancel"]').tooltip({
			placement: 'left',
			title: 'отменить задание'
		});
	}
	// кнопка завершения задания
	if($('button[name="tocomplete"]').length)
	{
		$('button[name="tocomplete"]').tooltip({
			placement: 'left',
			title: 'подтвердить готовность задания'
		});
	}
	// кнопка возврата задания
	if($('button[name="return_z"]').length)
	{
		$('button[name="return_z"]').tooltip({
			placement: 'left',
			title: 'отправить задание на доработку'
		});
	}
	// кнопка выполнения задания
	if($('button[name="complete"]').length)
	{
		$('button[name="complete"]').tooltip({
			placement: 'left',
			title: 'задание выполнено'
		});
	}
	// кнопка изменения приоритета
	if($('button[name="set_prioritet"]').length)
	{
		$('button[name="set_prioritet"]').tooltip({
			placement: 'left',
			title: 'изменение приоритета задания'
		});
	}
	//### Отчёты
	// кнопка добавления Отчётов
	if($('button[name="create_report"]').length)
	{
		$('button[name="create_report"]').tooltip({
			placement: 'right',
			title: 'добавление нового отчёта'
		});
	}
	// кнопка загрузка файлов везде
	if($('button .add_file').length)
	{
		$('button .add_file').tooltip({
			placement: 'bottom',
			title: 'загрузить файлы'
		});
	}
}