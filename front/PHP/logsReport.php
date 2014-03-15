
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script src="../JS/jquery.js"></script>
    <script>
        var currentCount=0;
        var pageCount=0;
        
        $(document).ready(function() {
            getRep(currentCount);
        });
        
        $(function (){
            $('#prev').click(function (e){
                if (currentCount<10){
                    
                }else{
                    currentCount-=10;
                }
                getRep(currentCount); 
            });
            $('#next').click(function (e){
               if (pageCount*10>currentCount){
                currentCount+=10;
               }
                getRep(currentCount);
                 
            });
            $('#pageNumC').change(function (e){
                 currentCount=($('#pageNumC').val()-1)*10;
                 getRep(($('#pageNumC').val()-1)*10);                 
            });
        });
        function getRep(c){
            $.ajax({
                async:false,
                url: "/front/PHP/logsPHPside.php",
                type: "POST",
                dataType:"json",
                data: {count: c}        
            }).success(function(data) {
                    $('#pageNumC').html('');
                    $('#pageNumC').html(data.pageNum);
                    $('#pageNumC').val(data.pn);
                    $('#label').html('');
                    $('#label').html(' из '+data.pageCount);
                    pageCount=data.pageCount;
                    $('#repContent').html('');
                    $('#repContent').html(data.cont);
            });
        }
   </script>
</head>
    
     
<body>
    <div id="options" style="position: relative">
        <button id="prev" style="float:left; width:50px; margin-left: 10px">Prev</button>
        <button id="next" style="float:left; width:50px; margin-left: 10px">Next</button>
        <input id="pageNumC" style="float:left; width:50px; margin-left: 10px" type="numeric" value="1">
        <label id="label" style="float:left; width:70px; margin-left: 10px"></label>
        <div style="clear: both"></div>
    </div>
    <div id="repContent" style="position: relative; margin-left: 10px;"></div>
</body>
    
</html>

