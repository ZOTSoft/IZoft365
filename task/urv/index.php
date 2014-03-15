<?PHP
header("Content-Type: text/html; charset=utf-8");
session_start();
include '../../company/check.php';
checksessionpassword();
?>

<html>
	<head>
		
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

		<link href="../../company/bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet">
		<link href="../crm_style.css" type="text/css" rel="stylesheet">

		<title>Учет рабочего времени</title>

		<script type="text/javascript" src="../../company/js/jquery-1.8.0.min.js"></script> 
		<script type="text/javascript" src="../../company/bootstrap/js/bootstrap.js" ></script> 
		<script type="text/javascript" src="../lib.js"></script>
		<script type="text/javascript" src="remake/urv.js"></script>

	</head>
	<body>  
		<div class="urv_wrapper">
			<div id="dialogs"></div>
			<div id="loading" class="container-loader" style="display: none;">
				<div class="round"> </div>
				<div class="l"> Загрузка... </div>
			</div>
			
			<div class="urv_vertical_block1">
				
				<div class="urv">
					<div class='urv_img'><img id="PNGlogo" src="../../index/images/logo.png" ><div class="clear"></div></div>  
					<h2>УЧЕТ РАБОЧЕГО <br> ВРЕМЕНИ</h2> 
				</div>
				  
				<div class="urv_timediv">
					<div id="DTime" class="time">
						<ul>
							<li id="hours"></li>
							<li class="point">:</li> 
							<li id="min"></li>
						</ul>
					</div>

					<h2 class="date" id="DDate"></h2>
				</div> 
			</div>
			
			<div class="clear"></div>
			
			<div class="urv_vertical_block2">
				
				<div id="DHorR" class="horizontal_blockR">
					<i onclick="processingSL('fullScreenEmp');" class="glyphicon allEmpUrv glyphicon-fullscreen"></i>
					<i onclick="processingSL('reloadEmp');" class="glyphicon reloadEmpUrv glyphicon-repeat"></i>
					<!-- Правый -->
					<div id="wpoint">Точка УРВ</div>
					<div class="urv_h2"><h2> Сейчас на работе </h2></div>
					<div id="DScrollY">
						<table id="ArrayEmployee" class="ArrayEmployee">
							<tr>
								<td></td>
							</tr>
						</table>
					</div>
				</div>
				
				<div class="horizontal_blockL">
					<!-- Левый -->
					<div id="DDP" class="DDP">
						<h1>Добро пожаловать</h1>
						<h2> Пожалуйста, пройдите авторизацию</h2>
						<h4>Воспользуйтесь магнитной картой<h4>
						<h4>или  введите код вручную</h4>
						<img id="JPGreader" src="images/reader.jpg" >
					</div>
					
					<div id="DDP2">
						<h1 id="dtd1">Здравствуйте!</h1>
					    <div id="dtd2"><img id="JPGfoto" src="images/fotononame.jpg" ></div>
						<h3 id="dtd3">Имя Фамилия</h3>
					</div>
					
					<button class="btn btn-primary btn-lg" id="buttonpas"><span class="glyphicon glyphicon-th"  id="enterpassword"></span>
Ввести код вручную:</button>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		
		<div id="steklo" >
			<div id="inputnumdiv" class="div_min darkwhite inputnumdivstyle" style="display: block;">
				Введите код
				<table id="calc" class="incalcstyle">
					<tbody>
						<tr>
							<td><button class="button grey active">7</button></td>
							<td><button class="button grey active">8</button></td>
							<td><button class="button grey active">9</button> </td>
							<td rowspan="2"><button class="button grey active inbtnV Hsave2">&#9224;<br>&#8592;</button> </td>
							<!--<td colspan="2"><button class="button btn btn-primary active inbtnG">лич.дел</button> </td>-->
						</tr>
						<tr>
							<td><button class="button grey active ">4</button></td>
							<td><button class="button grey active ">5</button></td>
							<td><button class="button grey active ">6</button> </td> 
							<!--<td colspan="2"><button class="button btn btn-success active inbtnG"></button> </td>-->
						</tr>
						<tr>
							<td><button class="button grey active">1</button></td>
							<td><button class="button grey active">2</button></td>
							<td><button class="button grey active">3</button> </td>
							<td rowspan="2"><button class="button grey active inbtnV Hsave1">&#10004</button> </td>
							<!--<td colspan="2"><button class="button btn btn-warning active inbtnG"></button> </td>-->
						</tr>
						<tr>
							<td colspan="2"><button class="button grey active inbtnH">0</button></td>
							<td><button onclick="" class="button grey active">.</button></td>
							<!--<td colspan="2"><button class="button btn btn-danger active inbtnG"></button> </td>-->
						</tr>  
					</tbody>
				</table>
			</div> 
		</div> 
		
		<div id="viewTextPass">
			<label class='control-form'></label>
		</div>
	</body>
</html>