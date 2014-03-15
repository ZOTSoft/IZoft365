<?php
session_start();
include '../../company/check.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");


/*if(empty($_SESSION)) {
	//include '../../company/check.php';
	//checksessionpassword();
	if(empty( $_SESSION ) ) {
		header('Location: ../../urv.php');
	}
}*/
?>
<html>
	<head>
		<title>УРВ терминал</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link href="../../company/bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet">
		<script type="text/javascript" src="../../company/js/jquery-1.8.0.min.js"></script>
		
		<style>
			img{
				width: 700px;
				border-radius: 40px;
				border: 20px solid #F0EEEE;
				display: block;
				margin: 0 auto;
			}
			h1, h2, h3, p{
				text-align: center;
			}
			h1{
				font-size: 80px;
			}
			h2{
				font-size: 70px;
			}
			h3{
				font-size: 60px;
			}
			p{
				font-size: 50px;
			}
		</style>
		
		<script>
			/*char = '';
			$(document).keypress(function (e) {
				char += String.fromCharCode(e.which);
				
				if(e.which == 32) {
					$.ajax({
						url			: "processing_urv.php",
						type		: 'POST',
						dataType	: "json",
						async		: false,
						data		: {
										Barcode : char,  
										sl		: "urv_terminal" 
									},
						success		:function(data)
									{
										$('#status').html('<p class="btn-success">транзакция проведена упешно</p>');
										$('#employee').html(char + ' - ваш код <br />' + data);
										char = '';
									},
						error		:function()
									{
										$('#status').html('<p class="btn-danger">Ошибка передачи данных</p>');
									}
					});
				}
			});*/
			
			/**/
			
			$(document).ready(function(){
				strih = Array();
				i = 0;
				$(document).keypress(function (e) 
				{
					strih[i] = e.which;
					i++;
					if(e.which == 32) {
						$('#status').empty();
						$.ajax({
							url			:  "../processing_SUNWEL.php",
							type		: 'POST',
							dataType	: "json",
							async		: false,
							data		: {
											Barcode : strih,  
											sl		: "do_in_out",
											terminal: 'ok'
										},
							success		:function(data)
										{
											$('#status').html('<p class="btn-success">транзакция проведена упешно</p>');
											$('#employee').html(data);
										},
							error		:function()
										{
											$('#status').html('<p class="btn-danger">Ошибка передачи данных</p>');
										}
						});
						strih = Array();
						i = 0;
					}
				});
			
				setInterval(function() {
					var version = 'sl_1.002';
					$.ajax({ url: "../processing_SUNWEL.php?sl=check_ver",dataType:"html"}).success(function(data_sl) {
						if (data_sl!=version)
							window.location.reload();
						else
							console.log('version '+data_sl+' ok');
					});
				}, 360000);
			});
			
	
		</script>
	</head>
	<body>
		<div id="status"><p>Статус</p></div>
		<hr />
		<div id="employee"></div>
	</body>
</html>
