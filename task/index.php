<html>
	<head>
		<title>test SL system</title>
		<script type="text/javascript" src="../company/js/jquery-1.8.0.min.js"></script> 
		
		<script>
		function processingSL( marker , reception )
			{	
				$('#loading').show();
				var url = 'processing_SUNWEL.php'; 
				var dataType = 'json'; 
				var type = "POST";
				var sl = marker; 
				if(!reception){ var reception = {}; }
				var process = false; 
				var async = true;

				// ОСНОВНАЯ ОБРАБОТКА
				switch(marker)
				{
					case 'testSL'			: process = true;		break;

					default : sl = 'no select';
				}


				// ДОПОЛНИТЕЛЬНАЯ ОБРАБОТКА ПЕРЕД ОТПРАВКОЙ
				if(process)
				{
					switch(marker)
					{
					//||| УРВ |||
						case 'testSL' : reception['url'] = 'crm' ; break;


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
										//||| УРВ |||
												//|||||  РВ.ГЛАВНАЯ   ||||||||||
										case 'testSL' : 
												$('body').html(data);
										break;

									}


								},
					error		: function(){
									//bootbox.alert('что то не так');
								},
					complete	: function() {
									$('#loading').hide();
								}
				});
			}
			
			$(document).ready(function() {
				processingSL('testSL');
			});
			
		</script>
	</head>
	<body>
		
	</body>
</html>