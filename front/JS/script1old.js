// JavaScript Document 
//Переменные для оффицианта
var idBtn =0;
var selectedrow = 0;
var selecdetcell;
var simplemenu;
var sumfromclient = 0;
var orderdata = new Object();
var ordertabledata = new Array();
var selectgridstatus = "";
var incalcstatus = "";
var incalcshowed = 0;
var orowcount = 1;
var srowcount = 1;
var crowcount = 1;
var offgridrowcount = 1;
var curpos = 0;
var clients;
var tables;
var sales;
var currentinterface = -1;
var changeopen = 0;
var idchange = 0;
var employeeid = 0;
var currentbills = new Array();
var from = "no";
var printed = -1;
var closed = -1;
var modyfied = 0;
var modyfiedt = 0;
var cash = 0;
var empInf = new Array();
var oplata = 0;
//Переменные для оффицианта =END
var ch = 0;


function getOrder(orderid, fill) {   
    clearRows('ordertable', orowcount);
    printed=-1;
    closed=-1;
    //console.log(orderid);
    $.ajax({
        async:false,
        url: "showOrder.php",
        type: "POST",
        dataType: "json",
        data: {orderid: orderid}
    }).success(function(data) {
        // console.log(data);
        orderdata.orderid = data[0].orderid;
        orderdata.guestscount = data[0].guests;
        orderdata.servicepercent = data[0].service;
        orderdata.sale = data[0].discount;
        orderdata.totalsum = data[0].totalsum;
        orderdata.closed = data[0].closed;
        orderdata.printed = data[0].printed;
        orderdata.service = data[0].service;
        orderdata.clientid = data[0].client;
        if (fill == 1) {
            $("#user").html(data[0].employeename);
            $("#countdiv").html(data[0].guests);
            $("#servicediv").html(data[0].service + '%');
            $("#salediv").html(data[0].discount + '%');
            $("#printdiv").html(data[0].totalsum);
            $("#chdt").html(data[0].dt);
            $("#chid").html(data[0].orderid);
        }
        printed = data[0].printed;
        addlog(printed,"clr");
        closed = data[0].closed;
        addlog("Printed:"+printed+" Closed:"+closed,"clr");
        //console.log(orderdata.printed+orderdata.closed);
        var headers = new Array('c', 'c', 'c', 'c', 'c');
        var values = new Array(5);
        bills = data;
        $.each(bills, function(index, value) {
            if (index != 0) {
                //console.log(value);
                values[0] = index;
                values[1] = value.foodname;
                values[2] = value.price;
                values[3] = value.quantity;
                values[4] = value.summa;
                ordertabledata[orowcount] = new Object();
                ordertabledata[orowcount].id = index;
                ordertabledata[orowcount].name = value.foodname;
                ordertabledata[orowcount].price = value.price;
                ordertabledata[orowcount].count = value.quantity;
                ordertabledata[orowcount].summa = value.summa;
                ordertabledata[orowcount].status = 'old';
                addRow(headers, values, "ordertable", orowcount)
                orowcount++;
            }
        })
        if (printed==1){
            $("#glass").show();
            $("#glass").attr("onClick","backOffInterface()");
        }
        else{
            $("#glass").hide();
            $("#glass").removeAttr("onClick")
        }
    })
}


function printCheck(porderid, event, typePrint) {
//console.log(porderid+' '+typePrint+' '+event);  
    if (typePrint == 1) {
        $.ajax({
            url: "printSubOrd.php",
            type: "POST",
            //dataType: "json",
            data: {orderid: porderid}
        }).success(function(data) {
          //  console.log(data);
            if (data == '')
            {
                data = 'emptyset';
                $.ajax({
                    url: "printOrd.php",
                    type: "POST",
                    //dataType: "json",
                    data: {orderid: porderid, type: "on", cash: cash}
                }).success(function(data) {
                   // console.log(data);
                    $.ajax({
                        type: "POST",
                        url: "http://localhost:12345",
                        data: data,
                        dataType: "script"
                    })
                });
            }
            else
                $.ajax({
                    type: "POST",
                    url: "http://localhost:12345",
                    data: data,
                    dataType: "script"
                }).success(function(data) {
               // console.log(data);
                    if (event == 1) {
                        $.ajax({
                            url: "printOrd.php",
                            type: "POST",
                            //dataType: "json",
                            data: {orderid: porderid, type: "on", cash: cash}
                        }).success(function(data) {
                            $.ajax({
                                type: "POST",
                                url: "http://localhost:12345",
                                data: data,
                                dataType: "script"
                            })
                        });
                    }
                });
        });
    }
    else {
        $.ajax({
            url: "printSubOrd.php",
            type: "POST",
            //dataType: "json",
            data: {orderid: porderid}
        }).success(function(data) {
          //  console.log(data);
            if (data == '')
            {
                data = 'emptyset';
                $.ajax({
                    url: "printOrd.php",
                    type: "POST",
                    //dataType: "json",
                    data: {orderid: porderid, type: "no", cash: cash}
                }).success(function(data) {
                   // console.log(data);
                    $.ajax({
                        type: "POST",
                        url: "http://localhost:12345",
                        data: data,
                        dataType: "script"
                    })
                });
            }
            else
                $.ajax({
                    type: "POST",
                    url: "http://localhost:12345",
                    data: data,
                    dataType: "script"
                }).success(function(data) {
               // console.log(data);
                    if (event == 1) {
                        $.ajax({
                            url: "printOrd.php",
                            type: "POST",
                            //dataType: "json",
                            data: {orderid: porderid,type: "no", cash: cash}
                        }).success(function(data) {
                           // console.log(data);
                            $.ajax({
                                type: "POST",
                                url: "http://localhost:12345",
                                data: data,
                                dataType: "script"
                            })
                        });
                    }
                });
        });
    }
}


function makeBill(prn, clsd, typePrint) {
    if ((currentinterface!=2 || printed == 1) && closed == 1) {
         return 1;
    } else {
        if ((modyfied == 1) || (modyfiedt == 1)) {
            if (orowcount>1) {
                orderdata.idchange = idchange;
                if (orderdata.tableid == undefined) {
                    orderdata.tableid = 0;
                }
                if (orderdata.orderid == undefined) {
                    orderdata.orderid = 0;
                }
                if (orderdata.service == undefined) {
                    orderdata.service = 0;
                }
                 console.log("Change " + orderdata.idchange + " Client " + orderdata.clientid + " Table " + orderdata.tableid + " bargin " + orderdata.sale +
                 " Count " + orderdata.guestscount + " Service " + orderdata.servicepercent + " totalsum " + orderdata.totalsum + " Employeeid " + orderdata.employeeid
                 + " printed " + prn + " closed " + clsd + " orderid " + orderdata.orderid);
                 console.log(orderdata) ;   
                 $res=0;
                $.ajax({
                    async: false,
                    url: "makeBill.php",
                    type: "POST",
                    data: {bill: orderdata, tablebill: ordertabledata, printed: prn, closed: clsd, rowcount: orowcount, modyfiedt: modyfiedt, sumfromclient: sumfromclient, typeprint: typePrint}
                }).success(function(data) {
                    if (data > 0) {
                        $res=1;
                        console.log(data,prn,typePrint);
                        printCheck(data, prn, typePrint);
                        modyfied = 0;
                        modyfiedt = 0;
                        orderdata.orderid = 0;
                        printed = -1;
                        closed = -1;
                    } else
                        console.log("Счет не был сохранен! Повторите попытку");
                        console.log('before');
                });
                console.log('after');
                if ($res!=0){
                    clearRows('ordertable', orowcount);
                }
                else
                {
                    alert('Счет не был сохранен! Повторите попытку снова!');
                }
                return $res;
            } else {
                console.log("Заказ не может быть пустым!");
            }
        }
        else{
            return 1;
        }    
    }
}




function showOrder(event, flag) {
    printed=-1;
    closed=-1;
    orderdata.orderid=0;
    if ((event == "cashier") && (flag == "old")) {
        from = "cashier";
        orderdata.orderid = $("#2c" + (selectedrow + 1) + "r" + "ordertableC").html();
        fill = 1;
    } else if ((event == "off") && (flag == "old")) {
        from = "off";
        orderdata.orderid = $("#2c" + (selectedrow + 1) + "r" + "billsorder").html();
        fill = 1;
    }
    if ((flag == "new") && (event == "off")) {
        orderdata.orderid = 0;
        from = "off";
        fillBillHeader();
    }
    if (((orderdata.orderid == 0) || (orderdata.orderid == undefined)) && (flag == "old")) {
    }
    else {
        if (orderdata.orderid != 0) {
            getOrder(orderdata.orderid, fill);
        }
        showI(4);
    }
}


function loadIrest() {
    $('#calcdiv').show();
    $("#chooseInterface").hide();
    $('#selectdiv').hide();
    $('#incalcdiv').hide();
    $('#chosebill').hide();
    $('#oficaintframe').hide();
    $('#cashierframediv').hide();
    checkingChangeInBase();
    createrMenu();
}


function changeFilterClick(event) {
    clearRows('ordertableC', crowcount);
    var headers = new Array('c', 'c', 'c', 'c', 'c', 'c', 'c', 'c');
    var values = new Array(7);
//console.log(idchange);
    $.ajax({
        url: "changeFilter.php",
        type: "POST",
        dataType: "json",
        data: {event: event, idchange: idchange}
    }).success(function(data) {
        bills = data;
        //console.log(data);  
        $.each(bills, function(index, value) {
            values[0] = value.lock;
            values[1] = value.st;
            values[2] = value.id;
            values[3] = value.payname;
            values[4] = value.tablename;
            values[5] = value.dsum;
            values[6] = value.partname;
            values[7] = value.employeename;
            //console.log(crowcount);
            addRow(headers, values, "ordertableC", crowcount)
            crowcount++;
        });
    });
}



function getCalc(type,id) {
    centralizeElement("incalcdiv");
    $("#inpwdcalc").html("");
    if (type == "chosepay") {
        if (parseInt(orderdata.totalsum) > 0) {
            $("#glass").show();
            $("#incalcdiv").width(800);
            centralizeElement("incalcdiv"); 
            $("#incalcdiv").show();
            $("#chosepaybtns").show();
            $("#chosepaysumdiv").html(orderdata.totalsum);
            $("#chosepaybalance").html("0");
        }
    } else {
        $("#glass").show();
        $("#incalcdiv").show();
    }
    //$('#incalcdiv').stop().animate({"opacity": "1"}, "slow");
    incalcstatus = type;
    idBtn = id;
    incalcshowed = 1;
}

function stopCalc() {
    $("#glass").hide();
    $("#chosepaybtns").hide();
    $("#incalcdiv").hide();
    $("#incalcdiv").width(400);
    //$('#incalcdiv').stop().animate({"opacity": "0"}, "slow");
    incalcstatus = "";
    incalcshowed = 0;
    oplata = 0;
    $("#chosepaybalanse").html(0);
}


/*function printNewOrder(){
 $.ajax({
 url:"getLastOrder.php",
 type: "POST", 
 dataType:"json",
 data: {idchange:idchange}
 }).success(function(data){
 console.log(data.id);
 print(data.id);
 });     
 }  */


function payClick(i) {
    if (i != 0) {
        sumfromclient = orderdata.totalsum;
        oplata++;
    }
    if (oplata != 0) {
        modyfied = 1;
        makeBill(1, 1, 1);
        $("#printdiv").html(0);
        orderdata.totalsum = 0;
        oplata=0;
        stopCalc();
        refreshGridCashier();
    }
    else {
        alert("Недостаточная сумма!")
    }
}



function printBillClick() {
//console.log(currentinterface);
//console.log(from);  
//console.log(orderdata.orderid);
    switch (from) {
        case "off":
            {
                modyfied = 1;
                if (makeBill(1, 0, 0)==1)
                {
                    // hideI("off", "exit");
                    orderdata.orderid = 0;
                    exitOffBillInteface();
                }
                break;
            }
        case "cashier":
            {
                orderdata.orderid = 0;
                break;
            }
        default:
            {
                /*makeBill(1, 1, 0);
                 orderdata.orderid = 0;*/
                break;
            }
    }
}


function exitCashInteface() {
    $('#oficaintframe').hide();
    $('#chosebill').hide();
    $('#cashierframediv').hide();
    $('#calcdiv').show();
    clearRows('ordertableC', crowcount);
}

function exitOffBillInteface() {
    $('#oficaintframe').hide();
    $('#chosebill').hide();
    $('#cashierframediv').hide();
    $('#calcdiv').show();
    clearRows('ordertable', orowcount);
    clearRows('billsorder', offgridrowcount);
}

function showI(variable) {
//console.log(variable);
//console.log(orderdata.orderid);
    switch (variable) {
        case 2:
            {
                $('#oficaintframe').hide();
                $('#chosebill').hide();
                $('#cashierframediv').show();
                $('#calcdiv').hide();
                break;
            }
        case 3:
            {
                $('#oficaintframe').hide();
                $('#chosebill').show();
                $('#cashierframediv').hide();
                $('#calcdiv').hide();
                fillBillHeader();
                break;
            }
        case 4:
            {
                showMenu();
                $('#oficaintframe').show();
                $('#chosebill').hide();
                $('#cashierframediv').hide();
                $('#calcdiv').hide();
                 $('#glass').hide();
                if (currentinterface == 4) {
                    $('#printbtn').html("Оплата");
                    $('#printbtn').attr("onClick", "getCalc('chosepay')");
                    $('#backbtn').hide();
                } else {
                    $('#printbtn').html("Печать счета");
                    $('#printbtn').attr("onClick", "printBillClick()");
                    $('#backbtn').show();
                }
                fillBillHeader();
                break;
            }
        default:
            {
                $('#oficaintframe').hide();
                $('#chosebill').hide();
                $('#cashierframediv').hide();
                $('#calcdiv').show();
            }
    }
}

function hideI(variable, event) {
//console.log(variable+" "+event);
document.getElementById("printdiv").innerHTML = 0;
    orderdata.orderid = 0;
    switch (variable) {
        case "off":
            {
                $('#oficaintframe').hide();
                $('#cashierframediv').hide();

                if (event == "back") {
                    $('#chosebill').show();
                    $('#calcdiv').hide();
                } else if (event == "exit") {
                    $('#chosebill').hide();
                    $('#calcdiv').show();
                }
                clearRows('ordertable', orowcount);
                refreshGridOfficiant();
                break;
            }
        case "cashier":
            {
                $('#oficaintframe').hide();
                $('#chosebill').hide();
                if (event == "back") {
                    $('#cashierframediv').show();
                    $('#calcdiv').hide();
                } else if (event == "exit") {
                    $('#cashierframediv').hide();
                    $('#calcdiv').show();
                }
                clearRows('ordertable', orowcount);
                clearRows('ordertableC', crowcount);
                refreshGridCashier();
                break;
            }
        default:
            {
                $('#oficaintframe').hide();
                $('#chosebill').hide();
                $('#cashierframediv').hide();
                $('#calcdiv').show();
                clearRows('ordertable', orowcount);
                clearRows('billtable', offgridrowcount);
            }
    }
}


function exitOffInteface(data) {
//console.log(from+" "+currentinterface);
    switch (currentinterface) {
        case "2":
            {
                hideI(from, "exit");
                /* if ((orderdata.closed==1)||(orderdata.printed==1)){        
                 }
                 else if (orderdata.closed==0){    
                 makeBill(0,0);
                 }                             
                 clearRows('ordertable',orowcount);*/
                break;
            }
        case "3":
            {
                //console.log(orowcount);
                if ((closed == 1) || (printed == 1)) {
                    hideI(from, "exit");
                }
                else if (closed == 0) {
                    if (makeBill(0, 0, 0)==1)                    
                        hideI(from, "exit");                    
                }
                
                break;
            }
        default:
            {
                hideI();
            }
    }
    
}

function backOffInterface() {
//console.log(from+" "+currentinterface);
    switch (currentinterface) {
        case "2":
            {
                hideI(from, "back");
                /* if ((orderdata.closed==1)||(orderdata.printed==1)){        
                 }
                 else if (orderdata.closed==0){    
                 makeBill(0,0);
                 }                             
                 clearRows('ordertable',orowcount);*/
                break;
            }
        case "3":
            {
                if ((closed == 1) || (printed == 1)) {
                    hideI(from, "back");
                }
                else if (closed == 0) {
                    if (makeBill(0, 0, 0)==1)
                    {
                        hideI(from, "back");
                    }
                }
                
                break;
            }
        default:
            {
                hideI();
            }
    }
}

function fillBillHeader() {
    $("#user").html(orderdata.employeename);
    $("#countdiv").html("1");
    orderdata.guestscount = 1;
    $("#servicediv").html("0%");
    orderdata.servicepercent = 0;
    $("#salediv").html("0%");
    $("#chid").html("");
    $("#chdt").html("");
    $("#tablediv").html("");
    orderdata.sale = 0;
    $.ajax({
        url: "SelectPartnerDefault.php",
        dataType: "json"
    }).success(function(data) {
        orderdata.clientid = data.id;
        orderdata.clientname = data.name;
        $("#clientdiv").html(data.name);
    });
}

function ChooseInterfaceBtnClick(id) {
    orderdata.employeeid = empInf[id].id;
    orderdata.employeename = empInf[id].name;
    currentinterface = empInf[id].interfaces;
    employeeid = empInf[id].id;
    $("#chooseInterface").html("");
    $("#chooseInterface").hide();
    if (currentinterface == "2")
    {
        showI(2);
        $.ajax({
            url: "onLoadCheckChange.php",
            type: "POST",
            dataType: "json",
            data: {employeeid: orderdata.employeeid}
        }).success(function(data) {
            checkingChangeInBase();
            checkChange();
            refreshGridCashier();
            $("#userCash").html(orderdata.employeename);
        });

    }
    if (currentinterface == "3") {
        //console.log(idchange);
        if (idchange == 0) {
            console.log('Ни одна смена не открыта!');
            $("#msg").html("Ни одна смена не открыта!");
            $("#msg").dialog({
                resizable: false,
                height: 180,
                modal: true,
                buttons: {
                    "Да": function() {
                        $(this).dialog("close");
                    }
                }
            });
        } else {
            showI(3);
            $("#userOf").html(orderdata.employeename);
            refreshGridOfficiant();
            //from="no";                                            
        }
    }
    if (currentinterface == "4") {
        //console.log(idchange);
        if (idchange == 0) {
            console.log('Ни одна смена не открыта!');
            $("#msg").html("Ни одна смена не открыта!");
            $("#msg").dialog({
                resizable: false,
                height: 180,
                modal: true,
                buttons: {
                    "Да": function() {
                        $(this).dialog("close");
                    }
                }
            });            
        } else {
            showI(4);
            from = "no";
            fillBillHeader();
        }
    }
}


function createButtonChooseInterface(id, inner, cl, valueInterface) {
    var newBtn;
    newBtn = document.createElement("button");
    newBtn.setAttribute("class", cl);
    newBtn.setAttribute("id", id + "ChooseBTN");
    newBtn.setAttribute("onClick", 'ChooseInterfaceBtnClick(' + id + ')');
    newBtn.innerHTML = "<p>" + inner + "</p>";
    return newBtn;
}

function shtrihSelect(c) {
    $.ajax({
        async: false,
        url: "shtrihSelect.php",
        dataType: "json",
        type: "POST",
        data: {shtrih: c}
    }).success(function(data) {
        //Добавление по штрих коду из локального меню
        //alert(data.id);
        //menuBtnClick(data.id);
        //ПРЯМИКОМ ИЗ БАЗЫ
        var headers = new Array('c', 'c', 'c', 'c', 'c');
        var values = new Array(5);
        values[0] = orowcount;
        values[1] = data.name;
        values[2] = data.price;
        values[3] = 1;
        values[4] = 1 * data.price;
        ordertabledata[orowcount] = new Object();
        ordertabledata[orowcount].id = data.id;
        ordertabledata[orowcount].name = data.name;
        ordertabledata[orowcount].price = data.price;
        ordertabledata[orowcount].count = 1;
        ordertabledata[orowcount].summa = 1 * data.price;
        ordertabledata[orowcount].status = 'new';
        addRow(headers, values, 'ordertable', orowcount);
        orowcount++;
        columnSum();
        modyfied = 1;
    });
}

function selectEmp(pwd) {
    $.ajax({
        url: "SelectEmployee.php",
        type: "POST",
        dataType: "json",
        data: {shtemp: pwd}
    }).success(function(data) {
        var chooseInt = document.getElementById("chooseInterface");
        centralizeElement("chooseInterface");
        $("#chooseInterface").show();
        empInf = data;
        $.each(empInf, function(index, value) {
            if (empInf.length > 1) {
                chooseInt.appendChild(createButtonChooseInterface(index, value.interfaceName, "button grey p active"));
            }
            else {
                ChooseInterfaceBtnClick(index)
            }
        });
    });
}

function changeBalance() {
    if (incalcstatus == "chosepay") {
        ch = $("#inpwdcalc").html();
        if (parseInt(ch) >= parseInt(orderdata.totalsum)) {
            $("#chosepaybalanse").html("" + (parseInt(ch) - parseInt(orderdata.totalsum)));
            sumfromclient = $("#inpwdcalc").html();
            oplata++;
        } else {
            $("#chosepaybalanse").html(0);
            oplata = 0;
        }
    }
}

function btninClick(v) {
    if (v == "bs") {
        str = $("#inpwdcalc").html();
        str = str.substr(0, str.length - 1);
        $("#inpwdcalc").html(str);
        changeBalance();
    }
    else {
        if (v == "ent") {
            //$('#incalcdiv').stop().animate({"opacity": "0"}, "slow");           
            var count = $("#inpwdcalc").html();
            if (count != "") {
                switch (incalcstatus) {
                    case "count":
                        orderdata.guestscount = count;
                        $("#countdiv").html(count);
                        modyfied = 1;
                        break;
                    case "service":
                        orderdata.service = count;
                        $("#servicediv").html(count + "%");
                        modyfied = 1;
                        break;
                    case "shtrih":
                        shtrihSelect(count);
                        break;
                    case "front":
                        selectEmp(count);
                        break;
                    case "print":
                        payClick(count);
                        break;
                    case "addcount":
                        repeatItem(count);
                        break;
                    case "countbtn":
                        menuBtnClick(idBtn,count);
                        break;                        
                }
            }
            if (incalcstatus != "chosepay") {
                $("#glass").hide();
                $("#incalcdiv").hide();
                $("#inpwdcalc").html("");
                incalcstatus = "";
                incalcshowed = 0;
            }
        }
        else {
            maxi = 5;
            switch (incalcstatus) {
                case 	"count":
                    maxi = 3;
                    break;
                case 	"service":
                    maxi = 3;
                    break;
                case 	"shtrih":
                    maxi = 11;
                    break;
                case "chosepay":
                    maxi = 20;
            }
            if ($("#inpwdcalc").html().length < maxi) {
                $("#inpwdcalc").append(v);
                //Удалить
                changeBalance();
            }
        }
    }
}

function formClick(self) {
    $("#chooseInterface").html("");
    if (incalcshowed != 1) {
        getCalc("front");
    }
    else {
        stopCalc();
    }
}

function clearArrays() {
    tables = null;
    sales = null;
    clients = null;
}

function selectGrid(status) {
    var headers = new Array('c', 'c', 'h');
    var values = new Array(3);
    srowcount = 1;
    selectgridstatus = status;
    centralizeElement("selectdiv");
    document.getElementById("glass").style.display = "block";
    document.getElementById("selectdiv").style.display = "block";
    $('#selectdiv').stop().animate({"opacity": "1"}, "slow");
    switch (status) {
        case 'client':
            {
                $.ajax({
                    url: "clients.php?ChooseTable=Clients",
                    dataType: 'json'
                }).success(function(data) {
                    clients = data;
                    for (k = 0; k < clients.length; k++) {
                        values[0] = k + 1;
                        values[1] = clients[k].name;
                        values[2] = clients[k].id;
                        addRow(headers, values, 'selectgrid', srowcount);
                        srowcount++;

                    }
                });

            }
            break;
        case 'table':
            $.ajax({
                url: "clients.php?ChooseTable=Tables",
                dataType: 'json'
            }).success(function(data) {
                tables = data;
                for (k = 0; k < tables.length; k++) {
                    values[0] = k + 1;
                    values[1] = tables[k].name;
                    values[2] = tables[k].id;
                    addRow(headers, values, 'selectgrid', srowcount);
                    srowcount++;

                }
            });

            break;
        case 'sale':
            $.ajax({
                url: "clients.php?ChooseTable=Discount",
                dataType: 'json'
            }).success(function(data) {
                sales = data;
                for (k = 0; k < sales.length; k++) {
                    values[0] = k + 1;
                    values[1] = sales[k].percentvalue;
                    values[2] = sales[k].id;
                    addRow(headers, values, 'selectgrid', srowcount);
                    srowcount++;

                }
            });

            break;
    }
}


function updateOrderdata(grid) {
    if (selectgridstatus != "") {
        selectedrow++;
        switch (selectgridstatus) {
            case 'client':
                orderdata.clientid = document.getElementById("2h" + selectedrow + "r" + grid).innerHTML;
                orderdata.clientname = document.getElementById("1c" + selectedrow + "r" + grid).innerHTML;
                document.getElementById('clientdiv').innerHTML = orderdata.clientname;
                modyfied = 1;
                break;
            case 'table':
                orderdata.tableid = document.getElementById("2h" + selectedrow + "r" + grid).innerHTML;
                orderdata.tablename = document.getElementById("1c" + selectedrow + "r" + grid).innerHTML;
                document.getElementById('tablediv').innerHTML = orderdata.tablename;
                modyfied = 1;
                break;
            case 'sale':
                orderdata.saleid = document.getElementById("2h" + selectedrow + "r" + grid).innerHTML;
                orderdata.sale = document.getElementById("1c" + selectedrow + "r" + grid).innerHTML;
                document.getElementById('salediv').innerHTML = orderdata.sale + "%";
                modyfied = 1;
                break;
        }
    }
}
function selectOk(grid) {
    updateOrderdata(grid);
    selectCancel(grid);
    selectedrow = 0;
}


function selectCancel(grid) {
    selectgridstatus = "";
    document.getElementById("glass").style.display = "none";
    $('#selectdiv').stop().animate({"opacity": "0"}, "fast");
    document.getElementById("selectdiv").style.display = "none";
    if (grid == "selectgrid") {
        table = document.getElementById(grid);
        for (z = 1; z <= srowcount; z++) {
            table.deleteRow(1);
        }
    }
    clearArrays();
}

function showMenu() {
    $('#menudiv').html("");
    for (i = 0; i < simplemenu.length; i++) {
        if (simplemenu[i].parentid == 0) {
            if (simplemenu[i].isgroup == 1) {
                menudiv.appendChild(createButton(simplemenu[i].id, simplemenu[i].name, "button grey p active"));
            }
            else {
                menudiv.appendChild(createButton(simplemenu[i].id, simplemenu[i].name, "button white font-black p active", simplemenu[i].price1));
            }
        }
    }
}

function createrMenu() {
    var menudiv = document.getElementById("menudiv");
    $.ajax({
        url: "TradeMenu.php",
        dataType: 'json'
    }).success(function(data) {
        simplemenu = data;
        showMenu();
    });
}

function createButton(id, inner, cl, price) {
    var newBtn;
    newBtn = document.createElement("button");
    newBtn.setAttribute("class", cl);
    newBtn.setAttribute("id", id);
    newBtn.setAttribute("onClick", 'menuBtnClick(id,1)');
    if (cl != "button white font-black p active") {
        newBtn.innerHTML = "<p>" + inner + "</p>";
    }
    else {
        newBtn.innerHTML = "<p>" + inner + "</p>";
        newBtn.setAttribute("onClick", 'getCalc("countbtn",id)');
        var newDiv = document.createElement("div");
        newDiv.setAttribute("class", "pricecell div red");
        newDiv.innerHTML =Math.round(price);
        newBtn.appendChild(newDiv);
    }
    return newBtn;
}

function findButtonPos(id) {
    for (i = 0; i < simplemenu.length; i++) {
        if (simplemenu[i].id == id) {
            return i;
        }
    }
}

function findParent(id) {
    var pid = 0;
    var menudiv = document.getElementById("menudiv");
    for (i = 0; i < simplemenu.length; i++) {
        if (simplemenu[i].id == id) {
            pid = simplemenu[i].parentid;
            break;
        }
    }
    if (pid == 0) {
        return id;
    }
    else {
        id = findParent(pid);
        menudiv.appendChild(createButton(pid, simplemenu[findButtonPos(pid)].name, "button blue p active"));
        return id;
    }
}



function menuBtnClick(idMenu,c) {
    var headers = new Array('c', 'c', 'c', 'c', 'c');
    var values = new Array(5);
    var count;
    c>0 ? count = c : count=1;
    var button = document.getElementById(idMenu);
    var menudiv = document.getElementById("menudiv");
    if (idMenu != 0) {
        if (simplemenu[findButtonPos(idMenu)].isgroup == 0) {
            values[0] = orowcount;
            values[1] = simplemenu[findButtonPos(idMenu)].name;
            values[2] = simplemenu[findButtonPos(idMenu)].price;
            values[3] = count;
            values[4] = count * simplemenu[findButtonPos(idMenu)].price;
            ordertabledata[orowcount] = new Object();
            ordertabledata[orowcount].id = simplemenu[findButtonPos(idMenu)].itemid;
            ordertabledata[orowcount].name = simplemenu[findButtonPos(idMenu)].name;
            ordertabledata[orowcount].price = simplemenu[findButtonPos(idMenu)].price;
            ordertabledata[orowcount].count = count;
            ordertabledata[orowcount].printer = simplemenu[findButtonPos(idMenu)].printer;
            ordertabledata[orowcount].summa = count * simplemenu[findButtonPos(idMenu)].price;
            ordertabledata[orowcount].status = 'new';
            addRow(headers, values, 'ordertable', orowcount);
            orowcount++;
            columnSum();
            modyfiedt = 1;
        }
        else {
            menudiv.innerHTML = "";
            menudiv.appendChild(createButton(0, "Все", "button blue p active"));
            findParent(idMenu);
            menudiv.appendChild(createButton(idMenu, simplemenu[findButtonPos(idMenu)].name, "button blue p active"));
            for (i = 0; i < simplemenu.length; i++) {
                if (simplemenu[i].parentid == idMenu) {
                    if (simplemenu[i].isgroup == 1) {
                        menudiv.appendChild(createButton(simplemenu[i].id, simplemenu[i].name, "button grey p active"));
                    }
                    else {
                        menudiv.appendChild(createButton(simplemenu[i].id, simplemenu[i].name, "button white font-black p active", simplemenu[i].price));
                    }
                }
            }
        }
    }
    else {
        menudiv.innerHTML = "";
        for (i = 0; i < simplemenu.length; i++) {
            if (simplemenu[i].parentid == 0) {
                if (simplemenu[i].isgroup == 1) {
                    menudiv.appendChild(createButton(simplemenu[i].id, simplemenu[i].name, "button grey p active"));
                }
                else {
                    menudiv.appendChild(createButton(simplemenu[i].id, simplemenu[i].name, "button white font-black p active", simplemenu[i].price));
                }
            }
        }
    }
}


function addRow(headers, values, grid, rowcount) {
    //Подключение к таблице3.
    //console.log(rowcount);
    var ordertable = document.getElementById(grid);
    //Создание строки к таблице
    var newRow = document.createElement("tr");
    newRow.setAttribute("onClick", "rowClick(this,'" + grid + "')");
    newRow.setAttribute("id", rowcount + "r" + grid);
    newRow.setAttribute("class", "rowbackground");
    //Создание ячеек таблицы
    for (i = 0; i <= headers.length - 1; i++) {
        var newCell = document.createElement("td");
        newCell.setAttribute("class", "orderrow");
        newCell.setAttribute("id", i + headers[i] + rowcount + "r" + grid);
        newCell.innerHTML = values[i];
        if (headers[i] == "c") {
            newRow.appendChild(newCell);
        }
        else {
            //Создание элемента таблици с id товара
            var newCell = document.createElement("td");
            newCell.setAttribute("class", "orderidcell");
            newCell.setAttribute("id", i + headers[i] + rowcount + "r" + grid);
            newCell.innerHTML = values[i];
            newRow.appendChild(newCell);
        }
    }
    ordertable.appendChild(newRow);
}

function repeatItem() {
    if (ordertabledata[selectedrow + 1].status == "new") {
        ordertabledata[selectedrow + 1].count++;
        ordertabledata[selectedrow + 1].summa = ordertabledata[selectedrow + 1].count * ordertabledata[selectedrow + 1].price;
        $("#3c" + (selectedrow + 1) + "r" + "ordertable").html(ordertabledata[selectedrow + 1].count);
        $("#4c" + (selectedrow + 1) + "r" + "ordertable").html(ordertabledata[selectedrow + 1].summa);
        columnSum();
        modyfiedt = 1;
    }
}
function rowClick(cell, grid) {
    var ordertable = document.getElementById(grid);
    row = cell.sectionRowIndex;
    selectedrow = cell.sectionRowIndex;
    //Selectedrow
    if (grid == "ordertable") {
        for (i = 1; i <= orowcount - 1; i++) {
            var cells = document.getElementById(i + "r" + grid);
            cells.removeAttribute("style");
        }
    }
    if (grid == "selectgrid") {
        for (i = 1; i <= srowcount - 1; i++) {
            var cells = document.getElementById(i + "r" + grid);
            cells.removeAttribute("style");
        }
    }
    if (grid == "ordertableC") {
        for (i = 1; i <= crowcount - 1; i++) {
            var cells = document.getElementById(i + "r" + grid);
            cells.removeAttribute("style");
        }
    }
    if (grid == "billsorder") {
        for (i = 1; i <= offgridrowcount - 1; i++) {
            var cells = document.getElementById(i + "r" + grid);
            cells.removeAttribute("style");
        }
    }
    selectedcell = document.getElementById((row + 1) + "r" + grid);
    //console.log(selectedcell);
    selectedcell.setAttribute("style", "background:#84d1f8");
    //selectedcell.style.backgroundColor='#778899';
    //End
}

function columnSum() {
    var sum = 0;
    for (i = 1; i < orowcount; i++) {
        sum += parseInt(ordertabledata[i].summa);
    }
    document.getElementById("printdiv").innerHTML = sum;
    orderdata.totalsum = sum;
}

function clearRows(grid, rowcount) {

    table = document.getElementById(grid);
    for (i = 1; i <= rowcount - 1; i++) {
        table.deleteRow(1);
    }
    if (grid == 'ordertable') {
        orowcount = 1;
    }
    ;
    if (grid == 'ordertableC') {
        crowcount = 1;
    }
    ;
    if (grid == 'billsorder') {
        offgridrowcount = 1;
    }
    ;
}

function deleteRow(id, grid) {
    //Removerow
    var ordertable = document.getElementById(grid);
    if (grid == "ordertable") {
        if (ordertabledata[selectedrow + 1].status = "new" && ordertabledata[selectedrow + 1].count > 1) {
            ordertabledata[selectedrow + 1].count--;
            ordertabledata[selectedrow + 1].summa = ordertabledata[selectedrow + 1].count * ordertabledata[selectedrow + 1].price;
            $("#3c" + (selectedrow + 1) + "r" + "ordertable").html(ordertabledata[selectedrow + 1].count);
            $("#4c" + (selectedrow + 1) + "r" + "ordertable").html(ordertabledata[selectedrow + 1].summa);
            columnSum();
        }
        else {
            ordertable.deleteRow(selectedrow + 1);
            orowcount--;
            for (i = selectedrow + 1; i <= orowcount - 1; i++) {
                firstcell = document.getElementById("0c" + (i + 1) + "r" + grid);
                firstcell.setAttribute("id", "0c" + (i) + "r" + grid);
                firstcell.innerHTML = i;
                ordertabledata[i] = ordertabledata[i + 1];
                for (j = 1; j <= 4; j++) {
                    secondcell = document.getElementById(j + "c" + (i + 1) + "r" + grid);
                    secondcell.setAttribute("id", j + "c" + (i) + "r" + grid);
                }
                selrow = document.getElementById((i + 1) + "r" + grid);
                selrow.setAttribute("id", (i) + "r" + grid);
            }
        }
    }
    else {
        ordertable.deleteRow(selectedrow + 1);
        srowcount--;
        for (i = selectedrow + 1; i <= srowcount - 1; i++) {
            firstcell = document.getElementById("0c" + (i + 1) + "r" + grid);
            firstcell.setAttribute("id", "0c" + (i) + "r" + grid);
            firstcell.innerHTML = i;
            for (j = 1; j <= 5; j++) {
                secondcell = document.getElementById(j + "c" + (i + 1) + "r" + grid);
                secondcell.setAttribute("id", j + "c" + (i) + "r" + grid);
            }
            selrow = document.getElementById((i + 1) + "r" + grid);
            selrow.setAttribute("id", (i) + "r" + grid);
        }
    }
    //Remove	
    columnSum();
}

//Интерфейс касира

//Интерфейс касира

function checkingChangeInBase() {
    $.ajax({
        url: "onLoadCheckChange.php",
        type: "POST",
        dataType: "json"
    }).success(function(data) {
        if (data == false) {
            idchange = 0;
            changeopen = 0;
        }
        else {
            idchange = data.id;
            changeopen = data.closed;
        }
        checkChange();
        orderdata.idchange = idchange;
        orderdata.closed = changeopen;
    });
}

function checkChange() {
    //console.log(idchange+' '+changeopen);
    if (idchange != 0 && changeopen == 0) {
        $('#btnchangeOpen').attr("disabled", "disabled");
        $('#btnchangeClose').removeAttr("disabled");
    }
    else {
        $('#btnchangeClose').attr("disabled", "disabled");
        $('#btnchangeOpen').removeAttr("disabled");
    }
}

function openChange() {
    $("#msg").html("Открыть Смену?")
    $("#msg").dialog({
        resizable: false,
        height: 180,
        modal: true,
        buttons: {
            "Да": function() {
                $.ajax({
                    url: "OpenCloseChange.php",
                    type: "POST",
                    dataType: "json",
                    data: {idchange: orderdata.idchange, open: 0, employeeid: employeeid}
                }).success(function(data) {
                    //console.log(data);
                    idchange = data.id;
                    changeopen = data.closed;
                    checkChange();
                });
                refreshGridCashier();
                $(this).dialog("close");
            },
            "Нет": function() {
                $(this).dialog("close");
            }
        }
    });
}

function closeChange() {
    $("#msg").html("Закрыть Смену?")
    $("#msg").dialog({
        resizable: false,
        height: 165,
        modal: true,
        buttons: {
            "Да": function() {
                $.ajax({
                    url: "OpenCloseChange.php",
                    type: "POST",
                    // dataType:"json",
                    data: {idchange: orderdata.idchange, open: 1, employeeid: employeeid}
                }).success(function(data) {
                    //console.log(data);
                    idchange = 0;
                    changeopen = data.closed;
                    checkChange();
                });
                refreshGridCashier();
                $(this).dialog("close");
            },
            "Нет": function() {
                $(this).dialog("close");
            }
        }
    });
}


function refreshGridCashier() {
    clearRows('ordertableC', crowcount);
    var headers = new Array('c', 'c', 'c', 'c', 'c', 'c', 'c', 'c');
    var values = new Array(7);
    //console.log(idchange);
    $.ajax({
        url: "ShowBills.php",
        dataType: 'json',
        type: 'POST',
        data: {idchange: idchange, event: 0, employeeid: -1}
    }).success(function(data) {
        //	console.log(data);
        bills = data;
        $.each(bills, function(index, value) {
            values[0] = value.lock;
            values[1] = value.st;
            values[2] = value.id;
            values[3] = value.payname;
            values[4] = value.tablename;
            values[5] = value.dsum;
            values[6] = value.partname;
            values[7] = value.employeename;
            addRow(headers, values, "ordertableC", crowcount);
            crowcount++;
        });
    });

    //Заполнить массив currentbills=data положитьв  перменную crowcount кол-во счетов
}

function refreshGridOfficiant() {
    clearRows('billsorder', offgridrowcount);
    var headers = new Array('c', 'c', 'c', 'c', 'c', 'c', 'c');
    var values = new Array(7);
    //console.log("c "+idchange+" em "+employeeid);
    $.ajax({
        url: "ShowBills.php",
        dataType: 'json',
        type: 'POST',
        data: {idchange: idchange, event: 1, employeeid: employeeid}
    }).success(function(data) {
        console.log(data);
        bills = data;
        $.each(bills, function(index, value) {
            values[0] = index;
            values[1] = value.st;
            values[2] = value.id;
            values[3] = value.empname;
            values[4] = value.tablename;
            values[5] = value.dsum;
            values[6] = value.partname;
            addlog(values,"clr");
            addRow(headers, values, "billsorder", offgridrowcount)
            offgridrowcount++;
        });
    });

    //Заполнить массив currentbills=data положитьв  перменную crowcount кол-во счетов
}

function payCashier(){  
    getOrder($("#2c" + (selectedrow + 1) + "r" + "ordertableC").html(),1);
    if (orderdata.printed==1){
        oplata++;
        getCalc('chosepay');
    }
}

//Интерфейс касира -END

//Часики
$(document).ready(function() {

    // определяем массивы имен для месяцев и дней недели
    var monthNames = ["Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря"];
    var dayNames = ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"]

    // создаем новый объект для хранения даты
    var newDate = new Date();

    // извлекаем текущую дату в новый объект
    newDate.setDate(newDate.getDate());

    // выводим день число месяц и год
    $('#Date').html(dayNames[newDate.getDay()] + " " + newDate.getDate() + ' ' + monthNames[newDate.getMonth()] + ' ' + newDate.getFullYear());

    //setInterval(function() {
        // создаем новый объект для хранения секунд
//        var seconds = new Date().getSeconds();
        // добавляем отсутствующий ноль
//        $("#sec").html((seconds < 10 ? "0" : "") + seconds);
//        $("#sec2").html((seconds < 10 ? "0" : "") + seconds);
//    }, 1000);

//    setInterval(function() {
        // создаем новый объект для хранения минут
//        var minutes = new Date().getMinutes();
        // добавляем отсутствующий ноль
//        $("#min").html((minutes < 10 ? "0" : "") + minutes);
//        $("#min2").html((minutes < 10 ? "0" : "") + minutes);
//    }, 1000);

//    setInterval(function() {
        // создаем новый объект для хранения часов
//        var hours = new Date().getHours();
        // добавляем отсутствующий ноль
//        $("#hours").html((hours < 10 ? "0" : "") + hours);
//        $("#hours2").html((hours < 10 ? "0" : "") + hours);
//    }, 1000);

});
//Доп функции


function addlog(str, st) {
    if (st == "clr") {
        console.clear();
    }
    console.log(str);
}