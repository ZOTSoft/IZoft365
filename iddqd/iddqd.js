function idd_showtab(tab){

}

function showclient(elem){
    tr_this=$(elem).parents('tr');
    tr=tr_this.next();
    status=tr.css('display');
    $('.showclient').prev().addClass('active').removeClass('success');
    $('.showclient').hide();
    if (status=='none'){
        tr.show();
        tr_this.addClass('success').removeClass('active');
    }else{ 
        tr_this.addClass('active').removeClass('success');
        tr.hide();
    }
}

function iddqd_submit(){
    $('#lalala').click();
}

function addcompany(id){
     $.ajax({
                  type: "POST", 
                  url: 'core.php?do=addcompany&id='+id,
                  dataType:"html"
                }).success(function(dataz) {
                    
                bootbox.dialog({
                  message: dataz,
                  title: 'Добавление клиента',
                  className: 'invss',
                  buttons: {
                    pay: {
                      label: "Закрыть",
                      className: "btn-success",
                      callback: function() {
                        true;
                      }
                    },
                    print: {
                      label: "Добавить",
                      className: "btn-primary",
                      callback: function() {
                        iddqd_submit();
                      }
                    }
                  }
                });
                
    });
}
    
        
$(function() {

$("body,html").animate({"scrollTop":0},1);


bootbox.setDefaults({
  locale: "ru",
  }); 

$('a[href='+window.location.hash+']').click();


//show_account_settings();
$('#loading').hide();

$('#loading').click(function(){
    $(this).hide();
});
      
});
