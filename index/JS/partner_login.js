
$(document).ready(function (){
  function hideRegblock(){
        $('#regform').animate({height:'-=145',top: '+=72.5',width:'+=300',left:'-=150'},"800");
        $('#leftregform').show();
        $('#rightregform').show();
        $('#centralregform').show();
        $('#regblock').animate({opacity:0});
  }
  function showRegblock(){
        $('#regform').animate({height:'+=145',top: '-=72.5',width:'-=300',left:'+=150'},"800");
        $('#leftregform').hide();
        $('#rightregform').hide();
        $('#centralregform').hide();
        $('#regblock').show().fadeTo("slow",1);
  }
  
  $('#logininput').val($('#zzlogin').val());
  $('#pwdinput').val($('#zzpassword').val());
                

  $('#regbtn').click(function (){
    showRegblock();
  }); 
  $('#cancelbtn').click(function (){
    hideRegblock();
  });
                
                
  //$('.start-stop').hide();




});

function changeDiv(){
    if ($("#rightregform").css("right")=="0px"){
        $("#rightregform").animate({
            right:-300,
            opacity:0
        },'800');
        $("#centralregform").animate({
            left:+300,
            opacity:1
        },'800');
    }
    else{
        $("#centralregform").animate({
            left:-300,
            opacity:0
        },'800');         
        $("#rightregform").animate({
            right:0,
            opacity:1
        },'800');       
    }
}




function auth(){
    login=$("#logininput").val();
    password=$("#pwdinput").val();
    //chk=$("#chk").val();
    chk=0;
    flag=false;
    if ($("#chk").prop("checked"))chk=1;
    $.ajax({ url: "/partner/login_ajax.php",async: false,dataType:"json",type: "POST",data:{ login: login,password:password,chk:chk}}).success(function(dataz) {
        console.log(dataz);
        console.log('Click login');
        if (dataz.type=='error'){
            //$('.login_error').html(dataz.message);
            
            alert(dataz.message);
            
        }else 
            if (dataz.type=='login'){
                //window.location=dataz.link;
                console.log('login-ok');
                console.log(dataz);
                $('#redff').val(dataz.link);
               /* $('#auth-glass').show();
                $('#zzlogin').val(login);
                $('#zzpassword').val(password);
                */
                flag=true;
                //return true;
                //$('#zzsubmit').click();
            }
            
    
    });  
    
    return flag;

}

function registration(){
    login=$("#reglogin").val();
    password=$("#regpass").val();
    fio=$("#regfio").val();
    phone=$("#regphone").val();
    email=$("#regmail").val();
    //chk=$("#chk").val();
    chk=0;
    if ($("#chk").prop("checked"))chk=1;
    $.ajax({ url: "/partner/reg.php",dataType:"json",type: "POST",data:{ login: login,password:password,fio:fio,phone:phone,email:email}}).success(function(dataz) {
        //console.log(dataz);
        if (dataz.type=='ok'){
            location.href=dataz.link;
        }else{
           alert(dataz.message); 
        } 
            
    });  
}

function recovery(){
    mail=$("#accesinput").val();
    $.ajax({ url: "/partner/recovery.php",dataType:"json",type: "POST",data:{ mail: mail}}).success(function(dataz) {
        //console.log(dataz);
        if (dataz.result=='ok'){
            alert('Пароль выслан на почту');
        }else{
           alert('Такого емейла не существует'); 
        } 
            
    });  
}