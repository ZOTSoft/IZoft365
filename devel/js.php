<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>ЖивоСкрипт</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <script type="text/javascript" src="/company/js/jquery-1.8.0.min.js"></script>
    <link rel="stylesheet" href="themes/base/jquery.ui.all.css">
    <script src="jquery-ui-1.10.3.custom.min.js"></script>
    <style type="text/css">
     body {
        margin: 0;
        padding: 0;
       }
        .point{width: 20px;height: 20px;background: red;border-radius: 10px; position: absolute; z-index: 9999;}
        #selectable .ui-selected{ border-spacing: 10px; border-collapse: separate; border: 2px solid #000000; }
        #selectable{background: #e6e6fa;
        border-left: 1px solid #000;
        border-right: 1px solid #000;
        padding: 0 20px 0 20px;
        margin: auto;
        font: 14px arial, verdana, sans-serif;
        width: 100%;
        height: 300px}
    </style>
    <script type="text/javascript">
        $(function(){
            $("body").selectable();

            $('.point').click(function(){
                $('.point').removeClass('ui-selected');
                $(this).addClass('ui-selected');
                    
            });
            $('body').bind("contextmenu", function(e){ 
                $('.ui-selected').animate({top:e.pageY+'px',left:e.pageX+'px'},{step: function(now, fx) {
                    //$(this).animate
                    console.log(now);
                }});
             return false; });
             
        });
    </script>
</head>
<body>
<div id="selectable">
    <div class="point" style="left: 10px; top: 90px; margin:25px 0 0 0 ;"></div>
    <div class="point" style="left: 20px; top: 40px; margin:0 50px 0 0 ;"></div>
    <div class="point" style="left: 15px; top: 69px; margin:0 0 0 25px;"></div>
    <div class="point" style="left: 99px; top: 50px; margin:0 0 25px 0 ;"></div>
</div>
</body>
</html>