
function otchet_close(){
    $('#ochert').hide();
        $("#ochert").remove();
}
function otchet_print(){
    href=$('.printota').attr('href');
    $('#otchet_print').attr('href',href);

    return false;
}
    function otchet(vid){
        $("#ochert").remove();
        switch (vid) {
            case 'akt':
                link="/company/report/report.php?do=otchet&type=akt";
                break;
             case 'poschetam':
                link="/company/report/report.php?do=otchet&type=poschetam";
                break;
             case 'itog':
                link="/company/report/report.php?do=otchet2&metod=oficiant";
                break;
             case 'refuse':
                link="/company/report/report.php?do=otchet&type=refuse";
                break;
             case 'refuse_and_orders':
                link="/company/report/report.php?do=otchet&type=refuse_and_orders";
                break;
        }
        $.ajax({ 
                  type: "POST",
                  url: link,
                  dataType:"html"
                }).success(function(dataz) {
                     $("body").append('<div id="ochert" class="otchet_div">'+dataz+'<br /><a id="otchet_print" onclick="otchet_print()" class="button green" href="#" target="_blank">Печать</a><button id="otchet_close" class="button red" onclick="otchet_close()">Выход</button></div>');    
                });
        }
