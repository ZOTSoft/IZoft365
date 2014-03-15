function myTable(myTable,funclicktr){
    this.name = myTable;
    this.edit = false;
    var tableid = this.name || null;
    this.fillTable = function (data){
        var myArray = new Array();
        myArray = data || null;
        if(myArray!==null && myTable!==null){
            this.clearTable();
            var count=myArray.length;
            for (var i=0;i<count;i++){
                this.addRow(myArray[i]);
            }
        }else{
            console.log("Что то случилось!");
        }         
    };
    this.addRow = function (data){
        var myArray = new Array();
        myArray = data || null;
        if(myArray!==null && tableid!==null){
            var count=$("#"+tableid+" thead tr").children().length;
            var row ='';
            if (myArray[5]==1){
                row="<tr style='background:#42c842;color:white'>"
            }else{
                row="<tr>";
            }
            for (var i=0;i<count;i++){
                row+="<td>"+myArray[i]+"</td>";
            }
            row+="</tr>";
            $("#"+tableid+" tbody").append(row);
            $("#"+tableid+" tbody tr td").addClass("right"); 
            $("#"+tableid+" tbody tr td:first-child").removeClass("right");
            $("#"+tableid+" .selected-row").removeClass("selected-row");
            $("#"+tableid+" tbody tr:last-child").addClass("selected-row");
            
            this.clickRecall();
        }else{
            console.log("Что то случилось!");
        }        
    };
    this.delRow = function(){
        if ($("#"+tableid+" .selected-row").prev().length>0){
            $("#"+tableid+" .selected-row").prev().addClass("to-this");
            $("#"+tableid+" .selected-row").remove(); 
            $("#"+tableid+" .to-this").addClass("selected-row").removeClass("to-this");    
        }
        else{
            $("#"+tableid+" .selected-row").remove();
            $("#"+tableid+" tbody tr:first-child").addClass("selected-row");
        }        
    };
    this.clearTable = function (){
        if(tableid!==null){
            $("#"+tableid+" tbody").html("");
        }else{
            console.log("Что то случилось!");
        }          
    };    
    this.getNum =function (){
        if(tableid!==null){
            return parseInt($("#"+tableid+" tbody .selected-row").index());
        }else{
            console.log("Что то случилось!");
        }             
    };
    this.getRowCount = function (){
        if(tableid!==null){
            return parseInt($("#"+tableid+" tbody").children().length);
        }else{
            console.log("Что то случилось!");
        }            
    };
    this.selectRow = function (index){        
        if(tableid!==null){
            if (index!==""){
                var v = parseInt(index);
                v+=1;
                index=v;
                if (this.getRowCount()>=v){
                    $("#"+tableid+" tbody tr").removeClass("selected-row");
                    $("#"+tableid+" tbody tr:nth-child("+index+")").addClass('selected-row');
                }
            }
        }else{
            console.log("Что то случилось!");
        }              
    };
    this.clickRecall = function (){        
        $("#"+tableid+" tbody tr").off('click');
        $("#"+tableid+" tbody tr td").off('dblclick');
        $("#"+tableid+" tbody tr").click(function (){
            $("#"+tableid+" tbody tr").removeClass("selected-row");
            $(this).addClass('selected-row');
            if (funclicktr!=undefined){
                funclicktr();
            }
        });
        if (this.edit){
            $("#"+tableid+" tbody tr td").dblclick(function (){
                $("#curredit").remove();
                 var td = $(this);
                 $("<input type='text' id='curredit'>").insertBefore("#"+tableid+" thead");
                 $("#curredit").offset($(this).offset());
                 $("#curredit").width($(this).innerWidth());
                 $("#curredit").height($(this).height());
                 $("#curredit").val(td.html());
                 $("#curredit").focus();
                 $("#curredit").select();
                 $("#curredit").keypress(function (event){
                    if (event.which == 13){
                        td.html($("#curredit").val());
                        $("#curredit").remove();
                    }
                 });
            });
        }
    };
};