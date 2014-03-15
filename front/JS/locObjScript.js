function selectTablesFromRoom(idLoc) {
    $('#tableObjectsList').html("");
    var objectsDiv = document.getElementById("tableObjectsList");
    for (i=0;i<preTableArray.length;i++){
        if (idLoc==preTableArray[i].locationid){
            objectsDiv.appendChild(createTablesButtons(preTableArray[i].id, preTableArray[i].name, "button white font-black p active",i));
        }
    }
}

function selectTablesWithOutRoom(){
    var objectsDiv = document.getElementById("tableObjectsList");
    for (i=0;i<preTableArray.length;i++){
            objectsDiv.appendChild(createTablesButtons(preTableArray[i].id, preTableArray[i].name, "button white font-black p active",i));
    }
}

function createTablesButtons(id, inner, cl,pnum){    
                    var newBtn;
                    newBtn = document.createElement("button");
                    newBtn.setAttribute("class", cl);
                    newBtn.setAttribute("id", id + "objBTN");
                    newBtn.setAttribute("onmousedown", 'selectOkTable(this)');
                    newBtn.setAttribute("style","font-size:12px");
                    newBtn.setAttribute("pNumObj",pnum);
                    newBtn.setAttribute("style", 'margin: 5px');
                    if (cl != "menubtnst") {
                        newBtn.innerHTML = inner;
                    } 
                    return newBtn;
                }

function createLocationTabs(id, inner, cl){
                    var newBtn;
                    newBtn = document.createElement("button");
                    newBtn.setAttribute("class", cl);
                    newBtn.setAttribute("id", id + "locBTN");
                    newBtn.setAttribute("onmousedown", 'locBTNClick(this)');
                    newBtn.setAttribute("style","font-size:12px");
                    newBtn.setAttribute("idLoc",id);
                    newBtn.setAttribute("style", 'margin: 5px');
                    if (cl != "menubtnst") {
                        newBtn.innerHTML = inner;
                    } else {
                        newBtn.innerHTML = "<p>" + inner + "</p>";
                        var newDiv = document.createElement("div");
                        newDiv.setAttribute("class", "btnpricecell");
                        newDiv.innerHTML = price;
                        newBtn.appendChild(newDiv);
                    }
                    return newBtn;
                } 

function selectLocObjectForm(){
    if (configArray.useLocation == 1) {
                $("#tableLocationtList").html("");
                $("#tableObjectsList").html("");
                var locDiv = document.getElementById("tableLocationtList");
                $.each(preLocArray, function (index, value) {
                    if (preLocArray.length > 1) {
                        locDiv.appendChild(createLocationTabs(value.id, value.name, "button grey p active"));
                    }
                });
                selectTablesFromRoom();
                showForm("tableForm");
          }else if (configArray.useLocation == 0) {
                      $("#tableLocationtList").html("");  
                      $("#tableObjectsList").html("");
                      selectTablesWithOutRoom();  
                      showForm("tableForm");
                };    
}

function locBTNClick(el){
    $('.buttonClicked').removeClass('buttonClicked');
    $(el).addClass('buttonClicked grey p active');
    selectTablesFromRoom($(el).attr('idLoc')); 
}


function selectOkTable(el) {
        orderdata.tableid = preTableArray[$(el).attr('pNumObj')].id;
        orderdata.tablename = preTableArray[$(el).attr('pNumObj')].name;
        if (preTableArray[$(el).attr('pNumObj')].servicepercent != -1) {
            orderdata.servicepercent = parseInt(preTableArray[$(el).attr('pNumObj')].servicepercent);
            document.getElementById('servicediv').innerHTML = orderdata.servicepercent + '%';
        }
        document.getElementById('tablediv').innerHTML = orderdata.tablename;
        modyfied = 1;
        selectCancelTable();
}

