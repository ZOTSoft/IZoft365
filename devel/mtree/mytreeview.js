(function( $ ){  
    
    var methods = {
            init: function (options){
              options = $.extend({
                   url : '',
                   headers : Array(),
                   dblclick : function () {}
              },options);
           return this.each(function(){
              var $this = $(this),
                  data = $this.data('myTreeView'),
                  myTreeView = $('<div />', {
                    text : $this.attr('title'),
                  });   
              if ( ! data ) {
                $this.html('');
                $this.append("<div class='css_tree'></div>");
                $this.append("<div class='container_my-table'><table id='"+$this.attr('id')+"table' class='my-table hovered thebestofthebest'></table></div>");
                var table = new myTable($this.attr('id')+'table');
                table.fillHeaders(options.headers);    
                $(this).data('myTreeView', {
                    target : $this,
                    myTreeView : myTreeView,
                    url: options.url,
                    table : table,
                    curroptions : options,
                    tabledata : null
                });
                data = $this.data('myTreeView'); 
                methods.loadData.apply($this,[0]);
              }    
         });
        },
        getSelected: function (){
            var $this = $(this),
             data = $this.data('myTreeView');
            console.log($this);
            var id = data.table.getSelectedId() || null;
            if (id !=null){
                for (var i = 0 ;i<data.tabledata.length;i++){
                    if (parseInt(data.tabledata[i]['id']) == id){
                        return data.tabledata[i];
                    }
                }
            }
        }, 
        append: function (rowdata){
                var $this = $(this),
                 data = $this.data('myTreeView');
                console.log(rowdata);
                rowdata = $.extend({
                    parentid: null,
                    data: Array() 
                },rowdata);
                data.tabledata.push(rowdata.data);
                data.tabledata[data.tabledata.length-1]['parentid']=rowdata.parentid;
                var sourcemass = data.tabledata[data.tabledata.length-1];
                var mass = new Array();
                for (var j=0;j<data.table.headers.length;j++){
                     mass.push(sourcemass[data.table.headers[j]]);
                }
                data.table.addRow(mass,sourcemass['isgroup'],sourcemass['id']);
        },
        update: function (rowdata){
            var $this = $(this),
            data = $this.data('myTreeView');    
            rowdata = $.extend({
                id:null,
                data: Array()
            },rowdata);
            for (var i=0;i<data.tabledata.length;i++){
                if (data.tabledata[i]['id']==rowdata.id){
                    data.tabledata[i]=rowdata.data;
                    var sourcemass = data.tabledata[i];
                    var mass = new Array();
                    for (var j=0;j<data.table.headers.length;j++){
                         mass.push(sourcemass[data.table.headers[j]]);
                    }                   
                    data.table.updateRow(mass,rowdata.data.id,rowdata.id);
                }
            }
        },
        remove: function (id){
            var $this = $(this),
            data = $this.data('myTreeView');      
            for (var i=0;i<data.tabledata.length;i++){
                if (data.tabledata[i]['id']==id){
                    data.tabledata.splice(i,1);
                    console.log(data.tabledata);
                    data.table.delRow(id);
                }
            }
        },
        reload : function () {
            var $this = $(this),
            data = $this.data('myTreeView');     
            data.table.fillHeaders(data.curroptions.headers);
            loadData(0);             
        },
        loadData: function (pid) {
            var $this = $(this),
            data = $this.data('myTreeView');        
            console.log($this);
            $.ajax({
                url: data.url,
                type: "POST",
                data: {parentid:pid},
                dataType: "json",
                async: false
            }).success(function(dataz){
                console.log(dataz);
                if (dataz === "Не получилось");
                else {
                    console.log(data.table)
                    methods.fillData.apply($this,[pid,dataz]) 
                }
            })
            .fail( function (data) {console.log(data.responseText);});          
        },
        clickRecall: function (){
            var $this = $(this),
            data = $this.data('myTreeView');     
            $('#'+$this.attr('id')+' .my-table tbody tr td').off('click');
            $('#'+$this.attr('id')+' .my-table tbody tr td').dblclick(function (){
                $('#'+$this.attr('id')+'.css_tree label[idgroup='+$(this).parent().attr('idgroup')+']').click();
                data.curroptions.dblclick();
            });
            $('#'+$this.attr('id')+' .css_tree label').off('click');
            $('#'+$this.attr('id')+' .css_tree label').click(function (){
                $('#'+$this.attr('id')+' .css_tree label').off('click');  
                    methods.loadData.apply($this,[$(this).attr('idgroup')]);
            });
        },
        fillData : function (z,dataz) {
            var $this = $(this),
            data = $this.data('myTreeView');  
            $('#'+$this.attr('id')+' .selected').removeClass('selected');
            $('#'+$this.attr('id')+' .css_tree label[idgroup='+z+']').addClass('selected');
            if (z !=0){
                   $('#'+$this.attr('id')+' .css_tree label[idgroup='+z+']').parent().find('ol').remove();
                   $('#'+$this.attr('id')+' .css_tree label[idgroup='+z+']').parent().append('<ol></ol>');
            }else{
                 $('#'+$this.attr('id')+' .css_tree').html("<ol><li><label idgroup='0' parentid='-1' for='subfolder0'>Корень</label>"+
                     "<input type='checkbox' checked='checked' id='subfolder0'><ol></ol></li></ol>");   
            }    
//                $('#subfolder'+z).attr('checked',true);
//                $('.css_tree label[idgroup='+z+']').parent().find('ol').remove();
//                $('.css_tree label[idgroup='+z+']').parent().append(data); 
            for (var i=0;i<dataz.length;i++){
                if (dataz[i].isgroup==1 && dataz[i].parentid==z){
                  $('#'+$this.attr('id')+' .css_tree label[idgroup='+z+']').parent().find('ol').append("<li><label idgroup="+dataz[i].id+
                          " parent="+dataz[i].parentid+
                          " for='subfolder"+dataz[i].id+"'>"+dataz[i].name+
                          "</label>"+"<input type='checkbox' id='subfolder'"+dataz[i].id+"' checked='checked'></li>");
                }          
            }
            data.tabledata = dataz;
            data.tabledata.splice(0,0,{'name':'Корень','id':0,'parentid':-1,'isgroup':1});
            data.table.fillTable(dataz);
            methods.clickRecall.apply($this);    
        }
    };

    function myTable(myTable,funclicktr){
        this.name = myTable;
        this.edit = false;
        var tableid = this.name || null;
        var headers = new Array();
        this.fillHeaders = function (data){
            var myArray = new Array();
            myArray = data || null;
            if(myArray!==null && tableid!==null){
                $("#"+tableid).html('');
                $("#"+tableid).append('<thead></thead>');          
                var count=myArray.length;
                var row = "<tr>";            
                for (var i=0;i<count;i++){
                    $.each(myArray[i],function (l,k){
                        if (l=='title'){
                            row+="<td>"+k+"</td>";                        
                        }
                        if (l=='name'){
                            headers.push(k);
                        }
                    });
                }
                row+="</tr>";
                $("#"+tableid+" thead").append(row);
                $("#"+tableid+" thead tr th").addClass("right");
                $("#"+tableid+" thead tr th:first-child").removeClass("right");
                this.headers = headers;
            }else{
                console.log("Что то случилось!");
            }  
        };

        this.fillTable = function (data){
            var myArray = new Array();
            myArray = data || null;
            tabledata = data || null;
            if(myArray!==null && myTable!==null){
                if ($("#"+tableid+" tbody").length>0){
                    this.clearTable();
                }
                else{
                    $("#"+tableid+" tbody").remove();
                    $("#"+tableid).append('<tbody></tbody>');
                }
                var count=myArray.length;
                for (var i=0;i<count;i++){ 
                    var mass = new Array();
                    var isgroup = myArray[i]['isgroup'];
                    var id = myArray[i]['id'];
                    for (var j=0;j<headers.length;j++){
                        mass.push(myArray[i][headers[j]]);
                    }
                    this.addRow(mass,isgroup,id);
                }
            }else{
                console.log("Что то случилось!"); 
            }         
        };
        this.addRow = function (data,isgroup,id){
            var myArray = new Array();
            myArray = data || null;
            if(myArray!==null && tableid!==null){
                var count=$("#"+tableid+" thead tr").children().length;
                if (isgroup==1){
                    var row = "<tr idgroup="+id+" class='category'>";
                }else{
                     var row = "<tr idgroup="+id+">";
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
        this.delRow = function(id){
//            if ($("#"+tableid+" .selected-row").prev().length>0){
//                $("#"+tableid+" .selected-row").prev().addClass("to-this");
//                $("#"+tableid+" .selected-row").remove(); 
//                $("#"+tableid+" .to-this").addClass("selected-row").removeClass("to-this");    
//            }
//            else{
//                $("#"+tableid+" .selected-row").remove();
//                $("#"+tableid+" tbody tr:first-child").addClass("selected-row");
//            }     
             $("#"+tableid+" tbody tr[idgroup="+id+"]").remove();
        };
        this.clearTable = function (){
            if(tableid!==null){
                $("#"+tableid+" tbody").html("");
            }else{
                console.log("Что то случилось!");
            }          
        };
        this.clearHeaders = function (){
            if(tableid!==null){
                $("#"+tableid+" thead").html("");
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
                    if (getMytableRowCount(tableid)>=v){
                        $("#"+tableid+" tbody tr").removeClass("selected-row");
                        $("#"+tableid+" tbody tr:nth-child("+index+")").addClass('selected-row');
                    }
                }
            }else{
                console.log("Что то случилось!");
            }              
        };
        this.updateRow = function (newdata,id,oldid){
            console.log(newdata);
            $("#"+tableid+" tbody tr[idgroup="+oldid+"]").attr("idgroup",id);
            $("#"+tableid+" tbody tr[idgroup="+id+"] td").each(function (){
                $(this).html(newdata[$(this).index()]);
            });
        };
        this.getNum =function (){
            if(tableid!==null){
                return parseInt($("#"+tableid+" tbody .selected-row").index());
            }else{
                console.log("Что то случилось!");
            }             
        };
        this.getSelectedId = function (){
            if(tableid!==null){
                return parseInt($("#"+tableid+" tbody .selected-row").attr('idgroup'));
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
        this.clickRecall = function (){        
            $("#"+tableid+" tbody tr").off('click');
            $("#"+tableid+" tbody tr td").off('dblclick');
            $("#"+tableid+" tbody tr").click(function (){
                $("#"+tableid+" tbody tr").removeClass("selected-row");
                $(this).addClass('selected-row');
                //funclicktr();
            });
            if (this.edit){
                $("#"+tableid+" tbody tr td").dblclick(function (){
                    $("#curredit").remove();
                     var td = $(this);
                     //$("<input type='text' id='curredit'>").insertBefore("#"+tableid+" thead");
                     $("<div id='curredit'></div>").insertBefore("#"+tableid+" thead");
                     $("#curredit").offset($(this).offset());
                     //$("#curredit").width($(this).innerWidth());
                     $("#curredit").width($(this).outerWidth());
                     //$("#curredit").height($(this).height());
                     $("#curredit").height($(this).outerHeight());
                     $("#curredit").val(td.html());
                     $("#curredit").focus();
                     $("#curredit").select();
                     //asd
                    $("#curredit").resizable({
                            maxHeight: $(this).height(),
                            minHeight: $(this).height(),
                            minWidth: $(this).width(),
                            grid: $(this).outerWidth(),
                            containment: "#"+tableid+" tbody tr "
                    });	
                    //asd
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

    $.fn.myTreeView = function (method){
       if ( methods[method] ) {
           return methods[ method ].apply(this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
           return methods.init.apply(this, arguments );
        } else {
          $.error( 'Метод с именем ' +  method + ' не существует.' );
        } 
     };
})( jQuery );