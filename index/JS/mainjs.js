$(function(){
 $('#myslider') 
  .anythingSlider({
   hashTags:false,
   checkResize :true,
   expand          : true,
   resizeContents  : true,
   autoPlay :true,
   delay: 5000,
   pauseOnHover: true,
   stopAtEnd: false, 
   autoPlayLocked: true,
   resumeDelay: 5000,
   navigationFormatter : function(i, panel){ 
    return ['', '', '', '', '', ''][i - 1]; 
   },
    onSlideBegin: function(e,slider){
       slider.$currentPage.find('.caption-top').animate({
           left:-400,
           opacity: 0
       },1);
     },
    onSlideComplete: function(slider){
       slider.$currentPage.find('.caption-top').animate({
           left:+50,
           opacity: 0.8
       },400);
    }    
  })
  .find('.panel') 
    .find('div[class*=caption]').css({ position: 'absolute'}).end() 
  //  .hover(function(){ showCaptions( $(this) ) }, function(){ hideCaptions( $(this) ); });  
});

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
  $('.start-stop').hide();
  $('#signinbtn').click(function (){ 
      location.href='/login.php';    
      
/*        $.ajax({ url: "login.php?checkauth=1",dataType:"json",type: "POST"}).success(function(dataz) {
            console.log('Click signinbtn');
            if (dataz.type=='login'){
                    location.href='/'+dataz.link;
                    console.log('signinbtn-ok');
            }else{
                if ($("#regform").width()!=600){
                    $("#regform").css("width","600px");
                    $("#regform").css("height","220px");
                }
                centralizeElement("regform");
                $('#leftregform').show();
                $('#rightregform').show();
                $('#centralregform').show();
                $('#regblock').hide();
                
                $('#logininput').val($('#zzlogin').val());
                $('#pwdinput').val($('#zzpassword').val());
                
                $(".glass").show().fadeTo("slow",0.6);
                $("#regform").show().fadeTo("slow",1);
                $('#regbtn').click(function (){
                    showRegblock();
                });             
            } 
        }); */
  });
  $('#glass').click(function(){
        $("#regbtn").off('click');
        $(".glass").hide().css("opacity","0");
        $("#regform").hide().css("opacity","0");
  });
  $('#cancelbtn').click(function (){
        hideRegblock();
  });
  $(window).scroll(function (){
      if (!$('#click_left').is('*') && $(window).scrollTop()>0){
          console.log('Add Element!!');
        $('body').append("<a href='/' id=click_left><div id='arrow_left'></div></a>");
        $('#click_left').animate({left:'+=65'},50);
        $('#click_left').click(function (){
            console.log('Delete Element!');
            $('#click_left').remove();
        });
      }
      if ($(window).scrollTop()==0){
          $("#click_left").remove();
      }
  });
});


function centralizeElement(obj){
    element = $("#"+obj);
    allwidth = $(window).width();
    allheight = $(window).height();
    element.css("left",((allwidth-element.width())/2)+"px");
    element.css("top", ((allheight-element.height())/2)+"px");
}
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
    $.ajax({ url: "/login.php",async: false,dataType:"json",type: "POST",data:{ login: login,password:password,chk:chk}}).success(function(dataz) {
        //console.log(dataz);
        console.log('Click login');
        if (dataz.type=='error'){
            //$('.login_error').html(dataz.message);
            alert(dataz.message);
        }else 
            if (dataz.type=='login'){
                //window.location=dataz.link;
                console.log('login-ok');
                $('#auth-glass').show();
                $('#zzlogin').val(login);
                $('#zzpassword').val(password);
                $('#redff').val(dataz.link);
                //$('#frmauth').submit();
                
                $('#zzsubmit').click();
            }
    
    });  

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
    $.ajax({ url: "/reg.php",dataType:"json",type: "POST",data:{ login: login,password:password,fio:fio,phone:phone,email:email}}).success(function(dataz) {
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
    $.ajax({ url: "/recovery.php",dataType:"json",type: "POST",data:{ mail: mail}}).success(function(dataz) {
        //console.log(dataz);
        if (dataz.result=='ok'){
            alert('Пароль выслан на почту');
        }else{
           alert('Такого емейла не существует'); 
        } 
            
    });  
}