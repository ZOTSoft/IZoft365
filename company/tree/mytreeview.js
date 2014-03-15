(function( $ ){  
    
    var methods = {
            init: function (options){
              options = $.extend({
                   url : '',
                   headers : Array(),
                   dblclick : function () {},
                   tree: true,
                   numeration: false,
                   pagination : false,
                   pagecount : [50,100,200]
              },options);
           return this.each(function(){
              var $this = $(this),
                  data = $this.data('myTreeView'),
                  myTreeView = $('<div />', {
                    text : $this.attr('title'),
                  });   
              if ( ! data ) {
                $this.html('');
                $this.addClass('container-bg');
                $this.append("<div class='css_tree'></div>");
                if (!options.tree){
                    $this.find('.css_tree').hide();
                }
                $this.append("<div class='container_my-table'><table id='"+$this.attr('id')+"table' class='my-table hovered thebestofthebest'></table></div>");
                if (options.pagination){
                    var str = '';
                    for (var i = 0 ; i<options.pagecount.length;i++){
                        str += '<li><a href="#">'+options.pagecount[i]+'</a></li>';
                    }
                    $this.append('<div style="position:relative" class="pag">'+
                        '<ul style="float:left" class="pagination">'+
                          '<li><a type="la" href="#">&laquo;</a></li>'+
                          '<li><a type="i" href="#">1</a></li>'+
                          '<li><a type="ra" href="#">&raquo;</a></li>'+
                        '</ul>'+
                        '<div class="input-group">'+
                        '<div style="padding:20px" class="input-group-btn">'+                    
                        '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'+
                        options.pagecount[0]+
                        '<span class="caret"></span>'+
                        '</button>'+
                            '<ul class="dropdown-menu" role="menu">'+
                              str+
                            '</ul>'+
                            '</div>'+
                        '<input style="margin: 20px 0 0;width:80px;" data-mask="999" type="text" class="form-control">'+
                        '</div>'+                       
                        '</div>');
                }
                $this.append('<div class="hFooter"></div>');
                $('<div class="my-footer"></div>').insertAfter($this);
                var table = new myTable($this.attr('id')+'table',options);
                table.fillHeaders(options.headers);    
                $(this).data('myTreeView', {
                    target : $this,
                    myTreeView : myTreeView,
                    url: options.url,
                    table : table,
                    curroptions : options,
                    tabledata : null,
                    filter: null,
                    showtree: options.tree,
                    pagecount : options.pagecount,
                    pagination : options.pagination,
                    curpage : 1,
                    totalpages: 1,
                    pagesize: options.pagecount[0]
                });
                data = $this.data('myTreeView'); 
                methods.loadData.apply($this,[0]);
              }    
         });
        },
        getSelected: function (options){
            var $this = $(this),
             data = $this.data('myTreeView');
              options = $.extend({
                   type : 'table',
              },options);
            switch (options.type) {
               case 'table':
                   var id = data.table.getSelectedId() || null;
                    if (id !=null){
                        for (var i = 0 ;i<data.tabledata.length;i++){
                            if (parseInt(data.tabledata[i]['id']) == id){
                                return data.tabledata[i];
                            }
                        }
                    }
               break;
               case 'tree':
                   return $('#'+$this.attr('id')+' .css_tree .label-primary').attr('idgroup');
               break;
            }
        }, 
        append: function (rowdata){
                var $this = $(this),
                 data = $this.data('myTreeView');
                rowdata = $.extend({
                    parentid: null,
                    data: Array() 
                },rowdata);
                data.tabledata.push(rowdata.data);
                data.tabledata[data.tabledata.length-1]['parentid']=rowdata.data.parentid;
                var sourcemass = data.tabledata[data.tabledata.length-1];
                var mass = new Array();
                for (var j=0;j<data.table.headers.length;j++){
                     mass.push(sourcemass[data.table.headers[j]]);
                }
                if (rowdata.data.isgroup==1){
                    $('#'+$this.attr('id')+' .css_tree span[idgroup='+rowdata.data.parentid+']').parent().find('ul').append("<li><span class='label label-success' idgroup='"+rowdata.data.id+
                            "' parent='"+rowdata.data.parentid+
                            "'><i class='glyphicon glyphicon-plus-sign'></i> "+rowdata.data.name+
                            "</span></li>");     
                }
                data.table.addRow(mass,sourcemass['isgroup'],sourcemass['id']);
                methods.clickRecall.apply($this);
                
                //zottig
                $("#"+$this.attr('id')+"table tbody tr").off('click');
                $("#"+$this.attr('id')+"table tbody tr td").off('dblclick');
                $("#"+$this.attr('id')+"table tbody tr").click(function (){
                    $("#"+$this.attr('id')+"table tbody tr").removeClass("selected-row");
                    $(this).addClass('selected-row');
                    //funclicktr();
                });
                
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
                    if (rowdata.data.isgroup==1){
                        $('#'+$this.attr('id')+' .css_tree span[idgroup='+sourcemass["id"]+']').html(sourcemass["name"])
                    }
                    methods.clickRecall.apply($this);
                      
                }
            }
        },
        remove: function (id){
            var $this = $(this),
            data = $this.data('myTreeView');      
            for (var i=0;i<data.tabledata.length;i++){
                if (data.tabledata[i]['id']==id){
                    data.tabledata.splice(i,1);
                    //console.log(data.tabledata);
                    data.table.delRow(id);
                    $('#'+$this.attr('id')+' .css_tree span[idgroup='+id+']').parent().remove();
                }
            } 
        },
        changeUrl: function (url){
            var $this = $(this),
            data = $this.data('myTreeView');
            data.url=url;
        },
        reload : function () {
            var $this = $(this),
            data = $this.data('myTreeView');     
            data.table.fillHeaders(data.curroptions.headers);
            methods.loadData.apply($this,[0]);             
        },
        getData: function (){
            var $this = $(this),
            data = $this.data('myTreeView');
            return data.tabledata;
        },
        loadData: function (pid) {
            var $this = $(this),
            data = $this.data('myTreeView');        
            //console.log($this);
            $('#loading').show();
            $.ajax({
                url: data.url,
                type: "POST",
                data: {parentid:pid,filter:data.filter,rows:data.pagesize,page:data.curpage},
                dataType: "json",
                async: false
            }).success(function(dataz){
                //console.log(dataz);
                if (dataz === "Не получилось");
                else {
                        if (data.pagination){
                            if (dataz.totalrows<=data.pagesize){
                                var pc = 1;
                            }else{
                                var pc = Math.ceil(dataz.totalrows/data.pagesize);
                            }
                            data.totalpages= pc;
                            var pag = $('#'+$this.attr('id')+' .pagination');
                            pag.html('<li><a type="la" href="#">&laquo;</a></li>');
                            //Тетовая 
                            if (data.totalpages> 2*4+1){
                                if (data.curpage < 1+2*2){
                                    for (var i = 1;i< parseInt(data.curpage)+2;i++){
                                        if (i==data.curpage){
                                           pag.append('<li><a class="active" type="i" href="#">'+i+'</a></li>'); 
                                        } else{
                                           pag.append('<li><a type="i" href="#">'+i+'</a></li>');
                                        }
                                    }
                                    pag.append('<li><a type="hl" href="#">&hellip;</a></li>');
                                    for (var i = data.totalpages-2+1;i<= data.totalpages;i++){i
                                        pag.append('<li><a type="i" href="#">'+i+'</a></li>');
                                    }
                                } 
                                else{
                                        if (data.curpage>=data.totalpages-2*2){
                                            for (var i = 1;i< 2+1;i++){
                                               pag.append('<li><a type="i" href="#">'+i+'</a></li>');
                                            }
                                            pag.append('<li><a type="hl" href="#">&hellip;</a></li>');
                                            for (var i = data.curpage-2;i<= data.totalpages;i++){
                                                if (i==data.curpage){
                                                   pag.append('<li><a class="active" type="i" href="#">'+i+'</a></li>'); 
                                                } else{
                                                   pag.append('<li><a type="i" href="#">'+i+'</a></li>');
                                                }
                                            }                                    
                                        }else{
                                            for (var i = 1;i< 2+1;i++){
                                               pag.append('<li><a type="i" href="#">'+i+'</a></li>');
                                            }
                                            pag.append('<li><a type="hl" href="#">&hellip;</a></li>');
                                            for (var i = data.curpage-2;i<= data.curpage+2;i++){
                                                if (i==data.curpage){
                                                   pag.append('<li><a class="active" type="i" href="#">'+i+'</a></li>'); 
                                                } else{
                                                   pag.append('<li><a type="i" href="#">'+i+'</a></li>');
                                                }
                                            }
                                            pag.append('<li><a type="hl" href="#">&hellip;</a></li>');
                                            for (var i = data.totalpages-2+1;i<= data.totalpages;i++){
                                                pag.append('<li><a type="i" href="#">'+i+'</a></li>');
                                            }                                    
                                        }
                                    }
                            }else{
                                for (var i = 1 ; i<= pc ;i++){
                                    if (i==data.curpage){
                                       pag.append('<li><a class="active" type="i" href="#">'+i+'</a></li>'); 
                                    } else{
                                       pag.append('<li><a type="i" href="#">'+i+'</a></li>');
                                    }
                                }                                
                            }                    
                            pag.append('<li><a type="ra" href="#">&raquo;</a></li>');
                            //pag.append('<li><a type="hl" href="#">&hellip;</a></li>');
                            pag = null;
                            $('#'+$this.attr('id')+' .pag input').val(data.curpage);
                        }
                    }
                    methods.fillData.apply($this,[pid,dataz.rows]);
                    $('#loading').hide();
            })
            .fail( function (data) {console.log(data.responseText);});          
        },
        clickRecall: function (){
            var $this = $(this),
            data = $this.data('myTreeView');
            if (data.pagination){
                //console.log(123);
                $('#'+$this.attr('id')+' .pag input').off('keypress');
                $('#'+$this.attr('id')+' .pag input').keypress(function (e){
                    if (e.which == 13 && parseInt($(this).val())<= data.totalpages){          
                        data.curpage=parseInt($(this).val());
                        methods.loadData.apply($this,[$('#'+$this.attr('id')+' .css_tree .label-primary').attr('idgroup')]);
                    }
                });
                $('#'+$this.attr('id')+' .pag .dropdown-menu a').click(function () {
                       data.pagesize=$(this).text();
                       data.curpage=1;
                       $('#'+$this.attr('id')+' button.btn.btn-default.dropdown-toggle').html($(this).text()+'<span class="caret"></span>');
                       methods.loadData.apply($this,[$('#'+$this.attr('id')+' .css_tree .label-primary').attr('idgroup')]);
                });
                $('#'+$this.attr('id')+' .pagination a').click(function () {
                    switch ($(this).attr('type')){
                        case 'i':                           
                            data.curpage=parseInt($(this).html());
                            data.pagesize=$('#'+$this.attr('id')+' button.btn.btn-default.dropdown-toggle').text();
                            methods.loadData.apply($this,[$('#'+$this.attr('id')+' .css_tree .label-primary').attr('idgroup')]);
                            break;
                        case 'la':
                            if (data.curpage>1){
                                data.curpage--;
                                data.pagesize=$('#'+$this.attr('id')+' button.btn.btn-default.dropdown-toggle').text();
                                methods.loadData.apply($this,[$('#'+$this.attr('id')+' .css_tree .label-primary').attr('idgroup')]);
                            }
                            break;
                        case 'ra':
                            if (data.curpage<data.totalpages){
                                data.curpage++;
                                data.pagesize=$('#'+$this.attr('id')+' button.btn.btn-default.dropdown-toggle').text();
                                methods.loadData.apply($this,[$('#'+$this.attr('id')+' .css_tree .label-primary').attr('idgroup')]);
                            }
                            break;                            
                    }
                });
            }
            $('#'+$this.attr('id')+' .my-table tbody tr td').off('click');
            $('#'+$this.attr('id')+' .my-table tbody tr td').dblclick(function (){         
                $('#'+$this.attr('id')+'.css_tree span[idgroup='+$(this).parent().attr('idgroup')+']').click();
                data.curroptions.dblclick();
            });
            
            $('#'+$this.attr('id')+' .css_tree span').off('click');
            $('#'+$this.attr('id')+' .css_tree span').click(function (e){
                    $('#loading').show();
                    data.curpage=1;
                    $('.css_tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
                    //console.log('');
                    $('#'+$this.attr('id')+' .css_tree span').removeClass('label-primary').addClass('label-success');
                    $(this).addClass('label-primary').removeClass('label-success');                  
                    methods.loadData.apply($this,[$(this).attr('idgroup')]);
                    var children = $(this).parent('li.parent_li').find(' > ul > li');
                      if ($(this).find(' > i').hasClass('glyphicon-minus-sign')) {
                          children.hide('fast');
                          $(this).attr('title', 'Expand this branch').find(' > i').addClass('glyphicon-plus-sign')
                          .removeClass('glyphicon-minus-sign');
                      } else {                
                          $(this).parents('li').find(' > ul > li').show('fast').css('overflow','visible');
                          children.hide().show('fast');
                          $(this).attr('title', 'Expand this branch').find(' > i').addClass('glyphicon-minus-sign')
                          .removeClass('glyphicon-plus-sign');
                          children.css('overflow','visible');
                      }
                      e.stopPropagation();
            });
        },
        fillData : function (z,dataz) {
            var $this = $(this),
            data = $this.data('myTreeView');  
//            $('#'+$this.attr('id')+' .selected').removeClass('selected');
//            $('#'+$this.attr('id')+' .css_tree span[idgroup='+z+']').addClass('selected');
            if (z !=0){
                   $('#'+$this.attr('id')+' .css_tree span[idgroup='+z+']').parent().find('ul').remove();
                   $('#'+$this.attr('id')+' .css_tree span[idgroup='+z+']').parent().append('<ul></ul>');
            }else{
                 $('#'+$this.attr('id')+' .css_tree').html("<ul><li><span class='label label-primary' idgroup='0' parentid='-1'><i class='glyphicon glyphicon-minus-sign'></i> Корень</span>"+
                     "<ul></ul></li></ul>");   
            }    
//                $('#subfolder'+z).attr('checked',true);
//                $('.css_tree label[idgroup='+z+']').parent().find('ol').remove();
//                $('.css_tree label[idgroup='+z+']').parent().append(data); 
//console.log(dataz.length);
            //zottig
            appto=$('#'+$this.attr('id')+' .css_tree span[idgroup='+z+']').parent().find('ul');
            for (var i=0;i<dataz.length;i++){
                if (dataz[i].isgroup==1 && dataz[i].parentid==z){
                  appto.append("<li><span class='label label-success' idgroup='"+dataz[i].id+
                          "' parent='"+dataz[i].parentid+
                          "'><i class='glyphicon glyphicon-plus-sign'></i> "+dataz[i].name+
                          "</span></li>");
                }          
            }
            data.tabledata = new Array;
            data.tabledata = dataz;
            if (data.showtree){
                data.tabledata.splice(0,0,{'name':'Корень','id':0,'parentid':-1,'isgroup':1});
            }
            data.table.fillTable(dataz,z);
            methods.clickRecall.apply($this);    
        },
        applyFilter: function (value) {
            var $this = $(this),
            data = $this.data('myTreeView');
            data.filter = value;
        }
    };

    function myTable(myTable,options){
        this.name = myTable;
        this.edit = false;
        var tableid = this.name || null;
        var numb = 1;
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
                if (options.numeration){
                    
                        $("#"+tableid+" thead tr td:first-child").before("<th class=\"fixwidth\">#</th> <th class=\"fixwidth\"></th>");
                    
                }else {
                    $("#"+tableid+" thead tr td:first-child").before("<th class=\"fixwidth\"></th>");
                }
                //$("#"+tableid+" thead tr td:first-child").before("<th>#</th>"); //console.log(); 
                $("#"+tableid+" thead tr th").addClass("right");
                $("#"+tableid+" thead tr th:first-child").removeClass("right");
                this.headers = headers;
            }else{
                console.log("Что то случилось!");
            }  
        };
        this.fillTable = function (data,idg){
        numb=1;   
            var myArray = new Array();
            myArray = data || null;
            if(myArray!==null && myTable!==null){
                if ($("#"+tableid+" tbody").length>0){
                    this.clearTable();
                }
                else{
                    $("#"+tableid+" tbody").remove();
                    $("#"+tableid).append('<tbody></tbody>');
                }
                var count=myArray.length;
                //console.log(count);
                //tnow=jQuery.now();
                //console.log(1);
                for (var i=0;i<count;i++){ 
                    var mass = new Array();
                    var isgroup = myArray[i]['isgroup'];
                    var id = myArray[i]['id'];
                    for (var j=0;j<headers.length;j++){
                        mass.push(myArray[i][headers[j]]);
                    }
                    this.addRow(mass,isgroup,id);
                }
                //console.log((jQuery.now()-tnow)/1000);
                //console.log(2);
                //zottig
                $("#"+tableid+" tbody tr").off('click');
                $("#"+tableid+" tbody tr td").off('dblclick');
                $("#"+tableid+" tbody tr").click(function (){
                    $("#"+tableid+" tbody tr").removeClass("selected-row");
                    $(this).addClass('selected-row');
                    //funclicktr();
                });
                $("#"+tableid+" tbody tr[idgroup="+idg+"]").addClass('cat-bg').prevAll().addClass('cat-bg');
                if (!options.numeration){                  
                        $("#"+tableid+" tbody tr[idgroup="+idg+"] td:first-child >i").addClass('glyphicon-folder-open').removeClass('glyphicon-folder-close');
                }
            }else{
                console.log("Что то случилось!"); 
            }         
        };
        this.addRow = function (data,isgroup,id){
            //console.log('blya');
            var myArray = new Array();
            myArray = data || null;
            if(myArray!==null && tableid!==null){
                
                var count=$("#"+tableid+" thead tr").children().length-1;
                if (options.numeration) count=count-1;
                if (isgroup==1){
                    var row = "<tr idgroup="+id+" class='category'>";
                }else{
                     var row = "<tr idgroup="+id+">";
                }
                for (var i=0;i<count;i++){
                    myArray[i]==null ? myArray[i]="" : null; 
                    row+="<td>"+myArray[i]+"</td>";
                }
                row+="</tr>";
                var newrow = null;
                if (isgroup==1){
                    if ($("#"+tableid+" tbody tr.category").length==0){
                        newrow=$("#"+tableid+" tbody").append(row);
                    }else{
                        $("#"+tableid+" tbody .category").last().after(row);
                        newrow=$("#"+tableid+" tbody .category").last();
                    }
                    newrow.find('td:first-child').before("<td><i style='color:#5cb85c' class='glyphicon glyphicon-folder-close'></td>");
                    if (options.numeration){
                        newrow.find('td:first-child').before("<td>"+numb+"</td>");
                        numb++;
                    }
                    $("#"+tableid+" .selected-row").removeClass("selected-row");
                    
                    $("#"+tableid+" tbody .category").first().addClass("selected-row");
                }else{
                    $("#"+tableid+" tbody").append(row);
                    
                        $("#"+tableid+" tbody tr:last-child > td:first-child").before("<td><i style='color:#B85C5C' class='glyphicon glyphicon-file'></td>");
                    if (options.numeration){
                        $("#"+tableid+" tbody tr:last-child > td:first-child").before("<td class=\"lp\">"+numb+"</td>");
                        numb++;
                    }
                    $("#"+tableid+" .selected-row").removeClass("selected-row");
                    $("#"+tableid+" tbody tr:first-child").addClass("selected-row");                    
                }
                $("#"+tableid+" tbody tr td").addClass("right");
                $("#"+tableid+" tbody tr td:first-child").removeClass("right");                
                this.clickRecall();
                
            }else{
                console.log("Что то случилось!");
            }        
        };
        this.delRow = function(id){
             if ($("#"+tableid+" tbody tr[idgroup="+id+"]").prev().length>0){
                 $("#"+tableid+" tbody tr[idgroup="+id+"]").prev().addClass("selected-row");
             }else{
                 $("#"+tableid+" tbody tr:first-child").addClass("selected-row");
             }
             $("#"+tableid+" tbody tr[idgroup="+id+"]").remove();
             if (options.numeration){
                 numb=1;
                  $("#"+tableid+" tbody tr > td:first-child").each(function(){
                      $(this).html(numb);
                      numb++;
                  });
             }
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
                    if (this.getRowCount>=v){
                        $("#"+tableid+" tbody tr").removeClass("selected-row");
                        $("#"+tableid+" tbody tr:nth-child("+index+")").addClass('selected-row');
                    }
                }
            }else{
                console.log("Что то случилось!");
            }              
        };
        this.updateRow = function (newdata,id,oldid){
            //console.log(newdata);
            $("#"+tableid+" tbody tr[idgroup="+oldid+"]").attr("idgroup",id);
            $("#"+tableid+" tbody tr[idgroup="+id+"] td:not(:first)").each(function (){
                if (options.numeration){
                    $(this).html(newdata[$(this).index()-2]);
                }else{
                    $(this).html(newdata[$(this).index()-1]); 
                }
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