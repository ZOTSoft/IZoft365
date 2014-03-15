/*$(window).resize(function(){
   OnResize();
});*/

$(document).ready(function (){
	
	var calc = 0;
	URV_Continue = 1;
	
	$(".horizontal_blockL").click( function calshow(){
		if (URV_Continue==1){
			calc = 1;
			$("#steklo").show();
		};
	});  
   
	function calshow(){
		if (URV_Continue==1){
			//calc = 1;
			$("#steklo").show();
		};
	}
   
	$("#steklo").click(function(){
		if (calc ==1){
			$("#steklo").hide();$("#pwdcalc").val("");
			$('#viewTextPass').hide(); $('#viewTextPass label').html('');
		};
		if (calc==2){
			calc=1;
		}
	}); 
    
	$("#inputnumdiv").click(function(){
		calc = 2;
	}); 
	
	// Обработчики нажатия кнопок
	pressed();
	calculator();
	
	processingSL('reloadEmp');
	//startTime();
	dateVerification();
	
	buttonExit();
	
	
	setInterval(function() {
		dateVerification();
	}, 100000);
	
	setInterval(function(){
		processingSL('reloadEmp');
	}, 360000);
});

function dateVerification() {
	var version = 'sl_1.002';
	$.ajax({ url: "../processing_SUNWEL.php?sl=check_ver",dataType:"json"}).success(function(data) {
		if (data.ver!=version)
			window.location.reload();
		else
			console.log('version '+data.ver+' ok');

		$("#min").html(data.minute);
		$("#hours").html(data.hours);
		$('.urv_timediv h2').html(data.day + " " + data.numDay + ' ' + data.mount + '<br />' + data.year + ' Год');
	});
}

/*function checkSessionTimer()
{
       //setLastAction();
       var timer=setTimeout(checkSessionTimer, 60000);
}*/


//Проверка введенного кода
//Возвращает -1, если код неверен
//Возвращает индекс сотрудника массива, если сотрудник на работе
/*function CheckTheBarcode(ArrayEmployee, Barcode){
	Code = -1;
	CheckEmployee(Barcode);
	// alert(ArrayEmployee);
	// alert('Barcode '+Barcode);
	// if (!Barcode==''){
	//   for (var i=0; i< ArrayEmployee.length; i++){
	//     if (ArrayEmployee[i][0]==Barcode){
	//       Code=i;// на работе
	//       break;
	//     }
	//     else{

	//     }
	// }
	// }
	// return Code;
}*/



/*
 * function TwoSign(Sign){
  if (Sign.toString().length<2) {
    return '0'+Sign.toString()
  }
  else
  return Sign.toString();
}
 */

/**/
 
 
 /*function ResizeDVert2(){
	$(".urv_vertical_block2").height(function(){
		if ($(".urv_wrapper").height()>=500){
			return $(".urv_wrapper").height()-100;
		}
		else{
		 return 500;
		}
    });
}

function ResizeScrollY(){
   $("#DScrollY").height(function(){
      if ($(".urv_wrapper").height()>=530){
       return $("#DHorR").height()-60;}
      else{
        return 530;
      }
     });
}

function ResizeFontSize(){
  // alert($("#DDP").css("font-size"));
  var fontsize = ($("#DDP").height() /(18*20))*20;
  $("#DDP").css({fontSize: fontsize +'px'});
}

function OnResize(){
  ResizeDVert2();
  ResizeScrollY();
  ResizeFontSize();
}*/
 
 
/*
function listEmployee() 
{
	$.ajax({
		url			: "processing_urv.php",
		type		: 'POST',
		async		: true,
		dataType	: "json",
		data		: {
						sl		: "listEmployeeURVPoint"
					},
		success		: function(data)
					{
						$('#wpoint').html('КПП '+data[0][0]['kpp']+' находиться в '+data[0][0]['location']);
						$('#DScrollY').html(data[1]);
					}
	});
}*/

/*function setLastAction(){
		/*$.ajax({
            url: "/front/PHP/front.php",
            type: "POST",
            dataType: "json",
            data: {actionScript:"lastAction"}
        }).success(function(data) {        
            if (data.rescode==0){
            }else{
              console.log(data.rescode+':'+data.resmsg);
              alert(data.rescode+':'+data.resmsg);      
            }
        });
}*/

// Показать список сотрудников на работе.
// Выводит массив
/*function ShowEmployees(ArrayEmployee){
  $("#ArrayEmployee").empty();
  for (var i=0; i< ArrayEmployee.length; i++){
    // new Date().getHours()+":"+ TwoSign(new Date().getMinutes())
    // alert(ArrayEmployee[i][2]);
    // alert();
    // var d = new Date();
// d.(date1);
// alert(Date(date1));
    // alert(new Date(ArrayEmployee[i][2]));
    // alert(new Date(ArrayEmployee[i][2]).getTime());
      $("#ArrayEmployee").append('<tr id="tr'+ArrayEmployee[i][0]+'"><td class="td1" id="td'+ArrayEmployee[i][0]+'">'+ArrayEmployee[i]["name"]+'</td> <td class="td2">'+ArrayEmployee[i][2]+'</td></tr>');
      // <td class="td2">'+ArrayEmployee[i][1]+'</td>
      // '+ArrayEmployee[i][2]+'
    // }
  }
}*/

//Функция получения массива сотрудников.
/*function ArrayEmployee(){
//  $.ajax({
//  url: "php/urv_front.php",
//  async: false,
//  dataType: "json",
//  date: {"action":"show_employees"},
//  method :"POST",
//  success:function(data){
//  
  
 $.ajax({
  url: "remake/urv_front.php",
  type	: 'POST',
  async: false, 
  dataType: "json",
  data: {"action":"show_employees"},


  success:function(data){
        if (data.rescode!=0) { 
          console.log(data.rescode+':'+data.resmsg);
          alert(data.rescode+':'+data.resmsg);       
        }
        else{
            ShowEmployees(data.arr);
        }
  }
 })
}*/ 
 
 // Таблица Сотрудников
// sotrudnici
/* $(document).ready(function(){
  // console.log("sss");
  // ShowEmployees(ArrayEmployee1) ;
  //OnResize();
  startTime();
  ArrayEmployee();
  // alert(new Date().getMinutes()); 
 }); */  

// отправка кода сотрудника и его состояния.
//function ButtonOK(){
  // alert(CurentEmploye[0]);
//  $.ajax({
//  url: "php/server.php",
//  // async: false,
//  data: "id="+CurentEmploye[0],
//  method :"POST",
//
//  success:function(data){
//    // data = eval("("+data+")");
//     // alert("data = "+data);
//    if (data!=''){
//      // alert("data = "+data);
//
//       $("#DDP2").hide("fast");
//      $("#DDP").show("fast");
//    }
//    else{
//    }
//    ArrayEmployee();
//  }
// });
  // alert('OK');
//}


/*function ButtonCancel(){
  shtrih='';
  $("#DDP").show("fast");
  $("#DDP2").hide("fast");
  // alert(Cancel);
  
}*/

// function ButtonCancel(){
//   alert('Cancel');
// }

//function ConnectionToInternet()
 // {
 //   alert (navigator.onLine);
 // }



/*function UpdateEmployee(){
   console.log("qwe");
	$.ajax({
            url: "remake/urv_front.php",
         async: false,
         type : "POST",
//         dataType: "json",
         data: {action:"UpdateEmployee"},
         success:function(data){
           alert(data);  
         }
    });
}*/