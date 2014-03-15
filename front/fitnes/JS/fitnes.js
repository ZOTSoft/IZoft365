var orderidServToFit=0;
var menuFoldersServiceToFitnesListStore=new Array();
var selectedGroupServiceToFitnesStore=new Array();
var fitnesServiceTableStore=new Array();
var clientsStore_fitnes = new Array();

var menuFoldersServiceToFitnesListGrid = new myTable("menuGroupServiceToFitnes",selectServiceToFitnessItem);
var selectedGroupServiceToFitnesGrid = new myTable("selectedGroupServiceToFitnes");

var fitnesServiceTableGrid = new myTable("fitnesServiceTable");

var clientsGrid_fitnes = new myTable('clientL_fitnes', selectClientFromGroup_fitnes);


$(function () {
    $('#formAddToFitnessJournalCancelBtn').mousedown(function (e) {
        closeFormFitnessAddToJournal(); 
    });
    $('#formAddToFitnessJournalOkBtn').mousedown(function (e) {
        formAddToFitnessJournalOkBtnClick(); 
    });
    $('#fitnessJournalAddClientBtn').mousedown(function (e) {
        fitnessJournalAddClientBtnClick(); 
    });
    $('#cancelbtnShowFitnessRecord').mousedown(function (e) {
        cancelbtnShowFitnessRecordClick(); 
    });
    $('#cancelbtnServiceToFitnes').mousedown(function (e) {
        cancelbtnServiceToFitnesClick(); 
    });
    $('#btnAddServiceToRecord').mousedown(function (e) {
        btnAddServiceToRecordClick(); 
    });
    $('#deletebtnServiceToFitnes').mousedown(function (e) {
        deleteFromServiceToFitness();
    });
    $('#okbtnServiceToFitnes').mousedown(function (e) {
        okbtnServiceToFitnesClick();
    });
    $('#exitbtnFitnessInterface').mousedown(function (e) {
        exitbtnFitnessInterfaceClick();
    });
    $('#locList').change(function (e) {
        showFitnessTable();
    });
    $('#createRecordFitnessInterface').mousedown(function (e) {
        createRecordFitnessInterfaceClick();
    }); 
    $('#dtFitness').change(function (e) {
        showFitnessTable();
    });
    $('.fitnesShowRecordBtn').mousedown(function (e) {
        $('#recordMainInfDiv').hide();   
        $('#recordClientInfDiv').hide();
        $('#recordServiceInfDiv').hide();
        $('#recordPaymentInfDiv').hide();
        $('.fitnesShowRecordBtn').css("background", "#1080dd")
        $(this).css("background", "#42c842");
        $('#'+$(this).attr('peshey')+'').show();
    });    
    $('#btnDeleteServiceToRecord').mousedown(function (e) {
        deleteServiceToRecordClick();
    });    
    $('#fitnessJournalAddClientBtn_edit').mousedown(function (e) {
        fitnessJournalAddClientBtn_editClick(); 
    });
    $('#okbtnClient_fitnes').mousedown(function (e) {
        okbtnClient_fitnesClick(); 
    });
    $('#cancelbtnClient_fitnes').mousedown(function (e) {
        cancelbtnClient_fitnesClick(); 
    });
    $('#okbtnClientFilter_fitnes').mousedown(function (e) {
        filter = $('#filterClientInput_fitnes').val();
        filtertype = document.getElementById('filterOptions_fitnes').value;
        selectClientFromGroup(filter, filtertype);
    });
    $('#cancelbtnClientFilter_fitnes').mousedown(function (e) {
        $('#filterClientInput_fitnes').val("");
        document.getElementById('filterOptions_fitnes').selectedIndex = 0;
        filter = '';
        filtertype = 'clear';
        selectClientFromGroup(filter, filtertype);
    });
})

function prepareFitnessInterface(){
    $('#dtFitness').val(new Date().toJSON().slice(0, 10));
    refreshFitnessContent();
    showFitnessTable();
}

function refreshFitnessContent(){
 $.ajax({
        async: false,
        url: "/front/fitnes/PHP/fitnesPHP.php",
        type: "POST",
        dataType: "json",
        data: {actionScript: 'getLocList'}
    }).success(function (data) {
        //Добавление по штрих коду из локального меню
        if (data.rescode == 0) {
           $('#locList').html(data.cont)
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
});  
}

function exitbtnFitnessInterfaceClick(){
    killSession();
    document.location.href = "/front";
}

function showFitnessTable(){ 
$.ajax({
    async: false,
    url: "/front/fitnes/PHP/fitnesPHP.php",
    type: "POST",
    dataType: "json",
    data: {actionScript: 'fitnessContent',location:$('#locList').val(),date:$('#dtFitness').val()}
}).success(function (data) {
    //Добавление по штрих коду из локального меню
    if (data.rescode == 0) {
        $('#fitnessBodyDiv').html(data.content);
    } else {
        console.log(data.rescode + ':' + data.resmsg);
        alert(data.rescode + ':' + data.resmsg);
    }
});    
}



function createRecordFitnessInterfaceClick(){
    showForm("formAddToFitnessJournal");    
    $("#supInfoAboutClientFitness").val("");
    $('#clientJournalAddFitness').val("");
    $('#journalAddNoteFitness').val("");
    $('#dtJournalFitness').val($('#dtFitness').val());
    $('#timeStartJournalFitness').val(new Date().getTime());
    $('#timeEndJournalFitness').val(new Date().getTime());
    $('#labelSelectObject').html("Выберите объект:");
    $.ajax({
        async: false,
        url: "/front/fitnes/PHP/fitnesPHP.php",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'getObjListForJournal'
        }
    }).success(function (data) {
        if (data.rescode == 0) {
                $('#journalObj').html(data.cont);
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
    addFitnessStore.dtBegin=$('#dtJournalFitness').val();
    addFitnessStore.objectSport=document.getElementById('journalObj_edit').value;
}

function closeFormFitnessAddToJournal(){
    closeForm("formAddToFitnessJournal");
    $('#labelSelectEmpOrObject').html("Сотрудник:");
}

function closeFormFitnessAddToJournal_edit(){
    closeForm("showFitnessReсordDiv");
    $('#labelSelectEmpOrObject').html("Сотрудник:");
}

function formAddToFitnessJournalOkBtnClick(){
        addFitnessStore.dtBegin=$('#dtJournalFitness').val();
        addFitnessStore.timeBegin=$('#timeStartJournalFitness').val();
        addFitnessStore.timeEnd=$('#timeEndJournalFitness').val();
        addFitnessStore.objId=document.getElementById('journalObj').value;
        addFitnessStore.employee=-1;
        addFitnessStore.note=$('#journalAddNoteFitness').val();

         $.ajax({
            async: false,
            url: "/front/fitnes/PHP/fitnesPHP.php",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: 'saveRecordToFitnessJournal',
                type:'insert',
                dtBegin:addFitnessStore.dtBegin,
                timeBegin:addFitnessStore.timeBegin,
                timeEnd:addFitnessStore.timeEnd,
                clientid:addFitnessStore.clientid,
                employeeid:addFitnessStore.employee,
                objId:addFitnessStore.objId,
                note:addFitnessStore.note
            }
        }).success(function (data) {
            //Добавление по штрих коду из локального меню
            if (data.rescode == 0) {
                    closeFormFitnessAddToJournal();
                    showFitnessTable();
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }
        });    
}

function fitnessJournalAddClientBtnClick(){
    closeForm("formAddToFitnessJournal");
    document.getElementById('filterOptions_fitnes').selectedIndex = 0;
    clientsgrid.clearTable();
    clients = undefined;
    clients = new Array();
    selectClientFromGroup_fitnes();     
}

function cancelClientBtnClick_fromFitnessJournal(){
    closeForm("clientForm");
    showForm("formAddToFitnessJournal");
    clientsgrid.clearTable();
    clients = undefined;
    clients = new Array();
    itemFocus=""; 
}

function okClientBtnClick_fromFitnessJournal(){
    if (clients[clientsgrid.getNum()].isgroup == 0) {
       addFitnessStore.clientid=clients[clientsgrid.getNum()].clientid;
       $("#clientJournalAddFitness").val(clients[clientsgrid.getNum()].clientname);
       $("#supInfoAboutClientFitness").val('Телефон:'+clients[clientsgrid.getNum()].tel);
       cancelClientBtnClick_fromFitnessJournal();
    };
}

function showFitnessRecord(obj){
    fid=$(obj).attr('jFID');
    $("#okbtnShowFitnessRecord").off("mousedown");
    $('#okbtnShowFitnessRecord').mousedown(function (e) {
        okbtnShowFitnessRecordClick(fid); 
    });
    $.ajax({
            async: false,
            url: "/front/fitnes/PHP/fitnesPHP.php",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: 'getFitnessRecordInf',
                fid:fid
            }
    }).success(function (data) {
        //Добавление по штрих коду из локального меню
        if (data.rescode == 0) {
               orderidServToFit=data.arr.orderid; 
               addFitnessStore.clientid=data.arr.clientid;
               $('#dtJournalFitness_edit').val(data.arr.dt);
               $('#timeStartJournalFitness_edit').val(data.arr.timestart);
               $('#timeEndJournalFitness_edit').val(data.arr.timeend);
               $('#clientJournalAddFitness_edit').val(data.arr.clientname);
               $('#supInfoAboutClientFitness_edit').val(data.arr.clTel);
               
//               $('#showRecord_labelObjReserv').html('Объект резервирования: '+data.arr.objname);
//               $('#showRecord_labelDateReserv').html('Дата: '+data.arr.dt);
//               $('#showRecord_labelTimeStartReserv').html('Время начала: '+data.arr.timestart);
//               $('#showRecord_labelTimeEndReserv').html('Время окончания: '+data.arr.timeend);
               $('#showRecord_labelTimeDuringReserv').html('Продолжительность: '+data.arr.duringTime+' мин.');
//               $('#showRecord_labelDateRegReserv').html('Дата регистрации: '+data.arr.dtreg);
//               
               $('#showRecord_labelClientName').html('ФИО: '+data.arr.clientname);
               $('#showRecord_labelClientBirth').html('Дата рождения: '+data.arr.clBirthday);
               $('#showRecord_labelClientTel').html('Телефон: '+data.arr.clTel);
               $('#showRecord_labelClientAddress').html('Адресс: '+data.arr.clAddress);
               
               $.ajax({
                    async: false,
                    url: "/front/fitnes/PHP/fitnesPHP.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        actionScript: 'getObjListForJournal'
                    }
                }).success(function (data) {
                    if (data.rescode == 0) {
                            $('#journalObj_edit').html(data.cont);
                    } else {
                        console.log(data.rescode + ':' + data.resmsg);
                        alert(data.rescode + ':' + data.resmsg);
                    }
                });
               $('#journalObj_edit').val(data.arr.objid); 
               refreshServiceFitnessRecordGrid();
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
    showForm('showFitnessReсordDiv');
}

function sumAllServ(){
    s=0;
    for (i=0;i<fitnesServiceTableStore.length;i++){
        s+=parseInt(fitnesServiceTableStore[i].price);
    }
    document.getElementById("saldoLabelShowFitnesRecord").innerHTML ='Сальдо:'+parseInt(s);
    document.getElementById("itogLabelShowFitnesRecord").innerHTML ='Итого:'+parseInt(s);
    document.getElementById("paymentSumLabelShowFitnesRecord").innerHTML ='К оплате:'+parseInt(s);
}

function refreshServiceFitnessRecordGrid(){
$.ajax({
            async: false,
            url: "/front/fitnes/PHP/fitnesPHP.php",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: 'refreshFitnesRecord',
                orderID:orderidServToFit
            }
    }).success(function (data) {
        //Добавление по штрих коду из локального меню
        if (data.rescode == 0) {
            fitnesServiceTableStore = data.rows;
            tmparray = preperaVisibleArray('fitnesServiceGrid', fitnesServiceTableStore);
            fitnesServiceTableGrid.fillTable(tmparray);
            fitnesServiceTableGrid.selectRow(0);
            sumAllServ();
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function cancelbtnShowFitnessRecordClick(){
    closeForm('showFitnessReсordDiv');
}

function cancelbtnServiceToFitnesClick(){
   closeForm('addServiceToFitnesRecord'); 
   menuFoldersServiceToFitnesListGrid.clearTable();
   menuFoldersServiceToFitnesListStore=new Array();
   selectedGroupServiceToFitnesStore=new Array();
   selectedGroupServiceToFitnesGrid.clearTable();
   document.getElementById("sumServToFit").innerHTML ='Итого:0';  
}

function btnAddServiceToRecordClick(){
    showForm('addServiceToFitnesRecord');
    selectServiceToFitnessItem();
}

function selectServiceToFitnessItem() {
    var isgroup;
    if (menuFoldersServiceToFitnesListStore.length == 0) {
        isgroup = 1;
    } else {
        isgroup = menuFoldersServiceToFitnesListStore[menuFoldersServiceToFitnesListGrid.getNum()].isgroup;
    }
    if (isgroup == 1) {
        if (menuFoldersServiceToFitnesListGrid.getRowCount() == 0) {
            parentid = 0;
        } else {
            parentid = menuFoldersServiceToFitnesListStore[menuFoldersServiceToFitnesListGrid.getNum()].id;
        }
        $.ajax({
            async: false,
            url: "/front/fitnes/PHP/fitnesPHP.php",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: "getMenuFoldersServiceToFitnes",
                parentid: parentid
            }
        }).success(function (data) {
            if (data.rescode == 0) {
                menuFoldersServiceToFitnesListStore = data.rows;
                tmparray = preperaVisibleArray('comboMenu', menuFoldersServiceToFitnesListStore);
                menuFoldersServiceToFitnesListGrid.fillTable(tmparray);
                menuFoldersServiceToFitnesListGrid.selectRow(0);
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }
        });
    } else if (isgroup == 0) {
        $("#msg").html('Добавить услугу?')
        $("#msg").dialog({
            resizable: false,
            height: 180,
            modal: true,
            buttons: {
                "Да": function () {
                    addServiceToFitness();
                    $(this).dialog("close");
                },
                "Нет": function () {
                    $(this).dialog("close");
                }
            }
        });
    }
}

function sumServCol(){
    s=0;
    for (i = 0; i < selectedGroupServiceToFitnesGrid.getRowCount(); i++) {
        s=parseInt(s)+parseInt(selectedGroupServiceToFitnesStore[i].price);
    }
    document.getElementById("sumServToFit").innerHTML ='Итого:'+ parseInt(s);
}


function addServiceToFitness(){
    rowcount = selectedGroupServiceToFitnesGrid.getRowCount();
    selectedGroupServiceToFitnesStore[rowcount] = new Object();
    selectedGroupServiceToFitnesStore[rowcount] = clone(menuFoldersServiceToFitnesListStore[menuFoldersServiceToFitnesListGrid.getNum()]);
    tmparray = preperaVisibleArray('comboMenu', selectedGroupServiceToFitnesStore);
    selectedGroupServiceToFitnesGrid.fillTable(tmparray);
    sumServCol();
}

function deleteFromServiceToFitness(){
    rownum = selectedGroupServiceToFitnesGrid.getNum();
    if (rownum > -1) {
        selectedGroupServiceToFitnesStore.splice(selectedGroupServiceToFitnesGrid.getNum(), 1);
        tmparray = preperaVisibleArray('comboMenu', selectedGroupServiceToFitnesStore);
        selectedGroupServiceToFitnesGrid.fillTable(tmparray);
    } else {
        Msg('Не выбрана услуга!');
    }    
    sumServCol();
}

function okbtnServiceToFitnesClick(){
    $.ajax({
            async: false,
            url: "/front/fitnes/PHP/fitnesPHP.php",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: "saveToTableOrderServiceToFitness",
                orderid: orderidServToFit,
                orderT:selectedGroupServiceToFitnesStore
            }
        }).success(function (data) {
            if (data.rescode == 0) {
                cancelbtnServiceToFitnesClick();
                refreshServiceFitnessRecordGrid();
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }
        });
}


function deleteServiceToRecordClick(){
    $.ajax({
            async: false,
            url: "/front/fitnes/PHP/fitnesPHP.php",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: "deleteFromServiceToRecord",
                id:fitnesServiceTableStore[fitnesServiceTableGrid.getNum()].id
            }
        }).success(function (data) {
            if (data.rescode == 0) {
                refreshServiceFitnessRecordGrid();
            }else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }
        });
}        
        

function fitnessJournalAddClientBtn_editClick(){
    closeForm("showFitnessReсordDiv");
//    $("#cancelbtnClient").off("mousedown");
//    $("#cancelbtnClient").mousedown(function(){
//        cancelClientBtnClick_fromFitnessJournal_edit();
//    });
//    $("#okbtnClient").off("mousedown");
//    $("#okbtnClient").mousedown(function(){
//        okClientBtnClick_fromFitnessJournal_edit();
//    });
    document.getElementById('filterOptions_fitnes').selectedIndex = 0;
    clientsgrid.clearTable();
    clients = undefined;
    clients = new Array();
    selectClientFromGroup_fitnes();    
}

function cancelClientBtnClick_fromFitnessJournal_edit(){
    closeForm("clientForm");
    showForm("showFitnessReсordDiv");
//    $("#cancelbtnClient").off("mousedown");
//    $("#cancelbtnClient").mousedown(function(){
//        cancelClientBtnClick();
//    });
//    $("#okbtnClient").off("mousedown");
//    $("#okbtnClient").mousedown(function(){
//        okClientBtnClick();
//    });
    clientsgrid.clearTable();
    clients = undefined;
    clients = new Array();
    itemFocus=""; 
}

function okClientBtnClick_fromFitnessJournal_edit(){
    if (clients[clientsgrid.getNum()].isgroup == 0) {
       addFitnessStore.clientid=clients[clientsgrid.getNum()].clientid;
       $("#clientJournalAddFitness_edit").val(clients[clientsgrid.getNum()].clientname);
       $("#supInfoAboutClientFitness_edit").val('Телефон:'+clients[clientsgrid.getNum()].tel);
       cancelClientBtnClick_fromFitnessJournal_edit();
    };
}

function okbtnShowFitnessRecordClick(fid){
        addFitnessStore.dtBegin=$('#dtJournalFitness_edit').val();
        addFitnessStore.timeBegin=$('#timeStartJournalFitness_edit').val();
        addFitnessStore.timeEnd=$('#timeEndJournalFitness_edit').val();
        addFitnessStore.objId=document.getElementById('journalObj_edit').value;
        addFitnessStore.employee=-1;
        addFitnessStore.note=$('#journalAddNoteFitness_edit').val();

         $.ajax({
            async: false,
            url: "/front/fitnes/PHP/fitnesPHP.php",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: 'saveRecordToFitnessJournal',
                type:'update',
                dtBegin:addFitnessStore.dtBegin,
                timeBegin:addFitnessStore.timeBegin,
                timeEnd:addFitnessStore.timeEnd,
                clientid:addFitnessStore.clientid,
                employeeid:addFitnessStore.employee,
                objId:addFitnessStore.objId,
                fid:fid,
                note:addFitnessStore.note
            }
        }).success(function (data) {
            //Добавление по штрих коду из локального меню
            if (data.rescode == 0) {
                    closeFormFitnessAddToJournal_edit();
                    showFitnessTable();
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }
        });        
}

function showForm(form) {
    centralizeElement(form);
    $('#' + form + '').css("opacity", "1");
    document.getElementById(form).style.display = "block";
}

function closeForm(form) {
    $('#' + form + '').stop().animate({
        "opacity": "0"
    }, "fast");
    document.getElementById(form).style.display = "none";
}


function selectClientFromGroup_fitnes(filter, filtertype) {
    var isgroup;
    if (clientsStore_fitnes.length == 0) {
        isgroup = 1;
    } else {
        isgroup = clientsStore_fitnes[clientsGrid_fitnes.getNum()].isgroup;
    }

    if ((isgroup == 1 && filtertype == undefined) || (filtertype != '' && filtertype != undefined)) {
        if (clientsGrid_fitnes.getRowCount() == 0 || filtertype == 'clear') {
            parentid = 0;
        } else {
            parentid = clientsStore_fitnes[clientsGrid_fitnes.getNum()].clientid;
        }
        $.ajax({
            async: false,
            url: "/front/fitnes/PHP/fitnesPHP.php",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: "getClientsFromGroup",
                parentid: parentid,
                filter: filter,
                filtertype: filtertype
            }
        }).success(function (data) {
            if (data.rescode == 0) {
                clientsStore_fitnes = data.rows;
                tmparray = preperaVisibleArray('clientsNew', clientsStore_fitnes);
                clientsGrid_fitnes.fillTable(tmparray);
                clientsGrid_fitnes.selectRow(0);
                showForm("clientForm_fitnes");
                itemFocus = '';
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);

            }
        });
    } else if (isgroup == 0) {

    }
}



function okbtnClient_fitnesClick() {
    if (clientsStore_fitnes[clientsGrid_fitnes.getNum()].isgroup == 0) {
       addJournalStore.clientid=clientsStore_fitnes[clientsGrid_fitnes.getNum()].clientid;
       $("#clientJournalAdd").val(clientsStore_fitnes[clientsGrid_fitnes.getNum()].clientname);
       $("#supInfoAboutClient").val('Телефон:'+clients[clientsGrid_fitnes.getNum()].tel);
       cancelClientBtnClick_fromJournal();
    };
} 

function cancelbtnClient_fitnesClick() {
    closeForm("clientForm_fitnes");
    showForm("formAddToFitnessJournal");
    clientsGrid_fitnes.clearTable();
    clientsStore_fitnes = undefined;
    clientsStore_fitnes = new Array();
    itemFocus="";
}