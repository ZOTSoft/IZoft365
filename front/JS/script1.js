// JavaScript Document 
//Переменные для оффицианта
function clone(obj) {
    if (obj == null || typeof (obj) != 'object')
        return obj;
    var temp = new obj.constructor();
    for (var key in obj)
        temp[key] = clone(obj[key]);
    return temp; 
}

var CurrentDate = new Date();

var idBtn = 0;
var tmpIdBtn = 0;
var tempselectedrow = -1;
var selectedrow = -1;
var selecdetcell;
var simplemenu;
var combototalsum = 0;
var cashierbillstore;
var officiantbillstore;
var sumfromclient = 0;
var orderdata = new Object();
var ordertabledata = new Array();
var clientarray = new Array();
var giftarray = new Array();
var giftItemsarray = new Array();
var giftSelectedItemsarray = new Array();
var refuseordertabledata = new Array();
var paymentarray = new Array();
var regCheck = new Array();
var divideOrderData = new Array();
var divideOrderTableData = new Array();
var divideNewOrderData = new Array();
var divideNewOrderTableData = new Array();
var returnOrderData = new Array();
var returnOrderTableData = new Array();
var returnNewOrderData = new Array();
var returnNewOrderTableData = new Array();
var shtrihItemsStore = new Array();
var comboGroupsStore = new Array();
var comboMenu;
var selectedComboStore = new Array();
var addComboMenuStore = new Array();
var addComboGroupsStore = new Array();
var addComboGroupsItemsStore = new Array();
var addComboGroupsItemsSelectStore = new Array();
var addComboItemToSelectedStore = new Array();
var selectEmployeeStore = new Array();
var selectedMaterialsStore = new Array();
var selectgridstatus = "";
var incalcstatus = "";
var incalcshowed = 0;
var orowcount = 1;
var srowcount = 1;
var crowcount = 1;
var offgridrowcount = 1;
var curpos = 0;
var clients = new Array();
var menuFoldersStopList = new Array();
var stopList = new Array();
var tables;
var sales;
var rooms;
var closeChange = 0;
var currentinterface = "";
var numcurrentinterface = -1;
var changeopen = 0;
var employeeid = 0;
var currentbills = new Array();
var modyfied = 0;
var modyfiedt = 0;
var cash = 0;
var giftBalans = 0;
var visileGiftBalans = 0;
var empInf = new Array();
var changeInf = '';
var oplata = 0;
var orderidClk = 0;
var refusePwd = 0;
var giftOrderId = 0;
var lastCookDiv = 0;
var preTableArray = new Array();
var preLocArray = new Array();
var protoUseLocation = -1;


//Переменные для оффицианта =END
var ch = 0;
//Variables for conf
var configArray = new Array();
var configuringInterface = '';
var noshowcalc = 0;
var itemFocus = '';
var currentCountCashPages = 0;
var maxPageCountCash = 0;
var totalPageCountCash = 0;
var currentCashFilter = '';
var tmpOtchetCont = '';
var useLocation=0;
var tmpSelectedMaterial = new Array();
var protoSimpleMenu = new Array();
var addJournalStore = new Array();
var addFitnessStore = new Array();
var stickerArr = new Array();



var cashgrid = new myTable('ordertableC');
var offgrid = new myTable('billsorder');
var ordergrid = new myTable('ordertable');


var roomsgrid = new myTable('tableL', selectTablesFromRoom);
var roomstablegrid = new myTable('tableS');
var clientOldGrid = new myTable('selectgrid');
var tablesgrid = new myTable('selectgrid');
var discountsgrid = new myTable('selectgrid');
var notesgrid = new myTable('selectgrid');

var giftLevelGrid = new myTable('selectLevelGift', getGiftItems);
var giftItemGrid = new myTable('selectGiftItem', addRowToSelectedItems);
var giftSelectedItems = new myTable('selectedGiftItems');

var regCheckGrid = new myTable('tableRegCheck');

var clientsgrid = new myTable('clientL', selectClientFromGroup);

var menuFoldersGrid = new myTable('menuGroup', selectStopListItem);
var stopListGrid = new myTable('selectedMenuItems');

var divideOrderTableDataGrid = new myTable('dividingOrder', dividingGridClick);
var divideNewOrderTableDataGrid = new myTable('dividedOrder');

var returnOrderTableDataGrid = new myTable('returnOrder', returnGridClick);
var returnNewOrderTableDataGrid = new myTable('selectReturnOrder');

var shtrihSelectGrid = new myTable('shtrihGrid');

var comboGroupsGrid = new myTable("selectComboGroupTable", createrComboMenu);
var selectedComboGrid = new myTable("selectedCombo");

var addComboMenuGrid = new myTable("comboMenuAddGrid", getAddComboGroup);
var addComboGroupsGrid = new myTable("comboGroupAddGrid", getAddComboGroupItems);
var addComboGroupsItemsGrid = new myTable("comboItemAddGrid");

var addComboGroupsItemsSelectGrid = new myTable("comboMenuGroupsItemsSelectTable", selectItemFromGroup_Combo);

var addComboItemToSelectedGrid = new myTable("selectedComboMenuGroupsItemsSelectTable");

var selectEmployeeGrid = new myTable("employeeGrid");

var selectedMaterialsGrid = new myTable("selectedMaterials");



$(function () {
    
    setInterval(function () {
        if (numcurrentinterface==8){
            loadCookContent();
        }
    }, 10000);
    setInterval(function () {
        $.ajax({
            async: true,
            url: "/front/PHP/front.php?refreshMenu_getTime_check_ver_updateConf",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: 'refreshMenu_getTime_check_ver_updateConf'
            }
        }).success(function (data) {
            if (data.rescode == 0) {
                if (data.version!=version){
                    killSession();
                    window.location.reload();
                };
                if (data.needRefresh==1){
                    createrMenu();
                }
                if (data.time!=''){
                     $("#offtime").html(data.time);
                     $("#cashierClock").html(data.time);
                }
                if (data.needRefreshConf==1){
                   getConfigInf();
                   if (configArray.useChangePrice == 0) {
                        $('#changePrice').hide();
                    } else if (configArray.useChangePrice == 1) {
                        $('#changePrice').show();
                    }
                   if (protoUseLocation!=configArray.useLocation){
                        preLoadTable();
                   }
                   
                }
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }
        });
    }, 60000);
    $('#mainBody').keypress(function (e) {
        if ($('#chooseInterface').is(':visible')) {
            if ((String.fromCharCode(e.which) == '1') || (String.fromCharCode(e.which) == '2') || (String.fromCharCode(e.which) == '3') || (String.fromCharCode(e.which) == '4') || (String.fromCharCode(e.which) == '5') ||
                (String.fromCharCode(e.which) == '6') || (String.fromCharCode(e.which) == '7')) {
                ChooseInterfaceBtnClick(parseInt(String.fromCharCode(e.which) - 1));
            }
        }
        if ($('#oficaintframe').is(':visible')) {
            if (String.fromCharCode(e.which) == '+') {
                    $('#repeatbtn').mousedown();
            }
            if (configArray.useChangePrice == 1) {
                if (String.fromCharCode(e.which) == '/') {
                    doChangePrice('');
                }
            }
            if (String.fromCharCode(e.which) == '-') { 
               $('#deletebtn').mousedown();  
            }
        } 
    }); 
    $('#mainBody').keyup(function (e) {
        if ($('#cashierframediv').is(':visible') || $('#chosebill').is(':visible')) {
            if (e.which == 27) {
                showCalc();
            }
        }
        if ($('#oficaintframe').is(':visible')) {
            if (e.keyCode == '118'){
                itemFocus="shtrihOF";
            }
            if (e.keyCode == '119'){
                itemFocus="searchField";
            }
        }
    });  
    $('#shtrihOF').keypress(function (e) {
        if (e.which == 13) {
            shtrihSelect($('#shtrihOF').val());
            $('#shtrihOF').val("");
        }
        
    });
    $('#shtrihOF').focusout(function (e) {
        $('#shtrihOF').val("");
    });
    $('#shtrihOF').keyup(function (e) {
        if (e.which == 27) {
            showCalc();
        }
    })
    $('#shinput').keyup(function (e) {
        if (e.which == 27) {
            showCalc();
        }
    })
    $('#clientdiv').mousedown(function (e) {
        $('#clientdiv').val("");
    });
    $('#clientdiv').focusout(function (e) {
        $('#clientdiv').val(orderdata.clientname);
    });
    $('#clientdiv').keypress(function (e) {
        if (e.which == 13) {
            itemFocus = 'shtrihOF';
            if ($('#selectdiv').css("display") == 'none') {
                if (selectGrid('client', null, $('#clientdiv').val(),'') != 1) {
                    $('#clientdiv').val("");
                }
            }
        }
    });

    $('#inpwdcalc').keypress(function (e) {
        var v = '';
        if (incalcstatus == 'chosepay') {
            if (String.fromCharCode(e.which) == '.') {
                return false;
            }
        }
        if (String.fromCharCode(e.which) == '.') {
            if ($("#inpwdcalc").val().indexOf('.') > 0) {
                return false;
            };
        }
        //         clientsgrid.clearTable();
        if (e.which == 13) {
            v = 'ent'
            if (incalcstatus == 'chosepay') {
                if (ordergrid.getRowCount() > 0) {
                    payClick(0);
                }
            } else if (e.which == 27) {
                stopCalc();
            } else {
                btninClick(v);
            }
        }
         if (((String.fromCharCode(e.which) == '.') || (String.fromCharCode(e.which) == '*')) && ($("#inpwdcalc").val().length == 0) && (incalcstatus == 'countbtn' || incalcstatus == 'refuseCount' || incalcstatus == 'addcount' || incalcstatus == 'countbtnShtrih')) {
            getWeigth();
        }
    });
    $('#inpwdcalc').keyup(function (e) {
         if (e.which == 27) {
            stopCalc();
        }
        if (incalcstatus == 'chosepay') {
            changeBalance();
        }
    });
    $('#countbtn').mousedown(function (e) {
        getCalc('count', null, 'Введите количество гостей', null)
    });
    $('#servicebtn').mousedown(function (e) {
        doService('');
    });
    $('#tablebtn').mousedown(function (e) {
//        selectGrid('table', null, '','')
            selectLocObjectForm();
    });
    $('#clientbtn').mousedown(function (e) {
        beginClientSeletion('');
    });
    $('#shtrihbtnOF').mousedown(function (e) {
        getCalc('shtrih', null, 'Введите штрихкод', null)
    });
    $('#repeatbtn').mousedown(function (e) {
        if ((ordergrid.getNum() >= 0) && (ordergrid.getRowCount() > 0) && (ordertabledata[ordergrid.getNum()].complex != 1)) {
            $('#shtrihOF').val("");
            getCalc('addcount', null, 'Введите кол-во повторяемых позиций', null)
        }
    });
    $('#deletebtn').mousedown(function (e) {
        if ((ordergrid.getNum() >= 0) && (ordergrid.getRowCount() > 0)) {
            deleteRow('')
        }
    });
    $('#commentbtn').mousedown(function (e) {
        if ((ordergrid.getNum() >= 0) && (ordertabledata[ordergrid.getNum()].status == 'new')) {
            selectGrid('note', null, '','')
        }
    });
    $('#okbtn').mousedown(function (e) {
        selectOk('selectgrid')
    });
    $('#cancelbtn').mousedown(function (e) {
        selectCancel('selectgrid')
    });
    $('#deletebtnGift').mousedown(function (e) {
        deleteGiftRow()
    });
    $('#btnchangeOpen').mousedown(function (e) {
        changeOpenClose('open', 'Открыть смену?')
    });
    $('#btnchangeClose').mousedown(function (e) {
        changeOpenClose('close', 'Закрыть смену?')
    });
    $('#exitbtnC').mousedown(function (e) {
        showCalc()
    });
    $('#exitbtnCB').mousedown(function (e) {
        showCalc()
    });
    $('#returnBtn').mousedown(function (e) {
        getCalc('pwdReturn', null, 'Введите пароль', 'password')
    });
    $('#cancelbtnSelectReturnOrder').mousedown(function (e) {
        cancelbtnReturnOrderClick();
    });

    $('#unblockbtn').mousedown(function (e) {
        if (cashgrid.getNum() >= 0) {
            checkUnblockingOrder()
        } else {
            Msg('Не выбран счет!');
        }
    });
    $('#cancelbtnReturn').mousedown(function (e) {
        cancelbtnReturnClick()
    });
    $('#typepaylist').focusin(function (e) {
        itemFocus = '';
        //        getOutFocus();
    });
    $('#clientdiv').focusin(function (e) {
        itemFocus = '';
        //        getOutFocus();
    });
    $('#clientdiv').focusout(function (e) {
        itemFocus = 'shtrihOF';
        //        getOutFocus();
    });
    $('#typepaylist').change(function (e) {
        getOutFocus();
    });
    $('#okbtnTable').mousedown(function (e) {
        selectOkTable();
    });
    $('#cancelbtnTable').mousedown(function (e) {
        selectCancelTable();
    });
    $('#exitbtnRegCheck').mousedown(function (e) {
        closeInterfaces();
    });
    $('#cancelbtnClient').mousedown(function (e) {
        cancelClientBtnClick();
    });
    $('#okbtnClient').mousedown(function (e) {
        okClientBtnClick();
    });
    $('#shinput').keypress(function (e) {
        if (e.which == 13) {
            checkRegistration($('#shinput').val());
            $('#shinput').val('');
        }
    });
    $('#stopListBtn').mousedown(function (e) {
        getCalc('pwdStopList', null, 'Введите пароль', 'password')

    });
    $('#okbtnStopList').mousedown(function (e) {
        btnStopListOkClick();
    });
    $('#deletebtnStopList').mousedown(function (e) {
        deleteFromStopList();
    });
    $('#divideOrderBtn').mousedown(function (e) {
        getCalc('pwdDivide', null, 'Введите пароль', 'password')

    });
    $('#okbtnDivideOrder').mousedown(function (e) {
        okbtnDivideOrderClick();
    });
    $('#deletebtnDivideOrder').mousedown(function (e) {
        deleteFromDivide();
    });
    $('#cancelbtnDivideOrder').mousedown(function (e) {
        cancelbtnDivideOrderClick();
    });
    $('#cancelbtnSelectReturnOrder').mousedown(function (e) {
        cancelbtnReturnClick();
    });
    $('#deletebtnReturn').mousedown(function (e) {
        deleteFromReturn();
    });
    $('#okbtnReturn').mousedown(function (e) {
        okbtnReturnOrderClick();
    });
    $('#okbtnClientFilter').mousedown(function (e) {
        filter = $('#filterClientInput').val();
        filtertype = document.getElementById('filterOptions').value;
        selectClientFromGroup(filter, filtertype);
    });
    $('#cancelbtnClientFilter').mousedown(function (e) {
        $('#filterClientInput').val("");
        document.getElementById('filterOptions').selectedIndex = 0;
        filter = '';
        filtertype = 'clear';
        selectClientFromGroup(filter, filtertype);
    });
    $('#reportbtnRegCheck').mousedown(function (e) {
        $.ajax({
            async: false,
            url: "/front/PHP/front.php?regCheckReport",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: 'regCheckReport'
            }
        }).success(function (data) {
            if (data.rescode == 0) {
                printTCP(data.report);
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            };
        });
    });
    $('#okbtnSelectReturnOrder').mousedown(function (e) {
        okbtnShowReturnOrderClick();
    });
    $('#changePrice').mousedown(function (e) {
        doChangePrice('');
    });
    $('#reportAkt').mousedown(function (e) {
        doReportAkt('');
    });
    $('#reportPoschetam').mousedown(function (e) {
        doReportPoschetam('');
    });
    $('#reportItog').mousedown(function (e) {
        doReportItog('');
    });
    $('#reportRefuse').mousedown(function (e) {
        doReportRefuse('');
    });
    $('#reportRefuse_and_orders').mousedown(function (e) {
        doReportRefuse_and_orders('');
    });
    $('#reportXreport').mousedown(function (e) {
        doReportXreport('');
    });
    $('#cancelbtnAddClient').mousedown(function (e) {
        cancelAddClient();
    });
    $('#addNewClient').mousedown(function (e) {
        getCalc('pwdAddClient', null, 'Введите пароль', 'password');
    });
    $('#okbtnAddClient').mousedown(function (e) {
        okAddClient();
    });
    $('#addbtnNote').mousedown(function (e) {
        getCalc('addPwdNote', null, 'Введите пароль', 'password');
    });
    $('#cancelbtnAddNote').mousedown(function (e) {
        cancelAddNote();
    });
    $('#okbtnAddNote').mousedown(function (e) {

    });
    $('#urvbtn').mousedown(function (e) {
        $.ajax({
            async: false,
            url: "/front/PHP/front.php?loginInToURV",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: 'loginInToURV'
            }
        }).success(function (data) {
            if (data.rescode == 0) {
                document.location.href = data.location;
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            };
        });
    });
//    $('#fitbtn').mousedown(function (e) {
//        document.location.href = "/front/fitnes";
//    });
    $('#okbtnShtrih').mousedown(function (e) {
        okbtnShtrihClick();
    });
    $('#cancelbtnShtrih').mousedown(function (e) {
        cancelbtnShtrihClick();
    });
    $('#cancelbtncomboMenuForm').mousedown(function (e) {
        cancelBtnComboForm();
    });
    $('#deletebtnComboForm').mousedown(function (e) {
        deleteComboItem();
    });
    $('#okbtncomboMenuForm').mousedown(function (e) {
        okbtnCombo(1);
    });
    $('#comboCreateBtn').mousedown(function (e) {
        doComboAdd('');
    });
    $('#btnComboMenuAddCancel').mousedown(function (e) {
        btnComboMenuAddCancelClick();
    });
    $('#btnaddComboGroup').mousedown(function (e) {
        doAddComboGroup();
    });
    $('#btndeleteComboGroup').mousedown(function (e) {
        doDeleteComboGroup();
    });
    $('#btnCancelAddComboGroupForm').mousedown(function (e) {
        btnCancelAddComboGroupFormClick();
    });
    //     $('#btnOkAddComboGroupForm').mousedown(function (e){ 
    //        btnOkAddComboGroupFormClick();
    //     });
    $('#btnaddComboGroupItem').mousedown(function (e) {
        doAddComboGroupItem();
    });
    $('#cancelbtnaddComboGroupItem').mousedown(function (e) {
        cancelbtnaddComboGroupItemClick();
    });
    $('#deletebtnSelectedComboItems').mousedown(function (e) {
        deletebtnSelectedComboItemsClick();
    });
    $('#okbtnaddComboGroupItem').mousedown(function (e) {
        okbtnaddComboGroupItemClick();
    });
    $('#btndeleteComboGroupItem').mousedown(function (e) {
        btndeleteComboGroupItemClick();
    });
    $('#setDefaultComboItem').mousedown(function (e) {
        doAddDefaultComboItemForm();
    });
    $('#btnCancelsetDefaultComboItemForm').mousedown(function (e) {
        btnCancelsetDefaultComboItemFormClick();
    });
    $('#btnOksetDefaultComboItemForm').mousedown(function (e) {
        btnOksetDefaultComboItemFormClick();
    });
    $('#btnchangeComboGroup').mousedown(function (e) {
        doChangeComboGroup();
    });
    $('#salebtn').mousedown(function (e) {
        getCalc('pwdDiscount', null, 'Введите пароль', 'password');
    });
    $('#prevPag').mousedown(function (e) {
        if (currentCountCashPages < 50) {

        } else {
            currentCountCashPages -= 50;
        }
        if (currentCashFilter == 'nopay') {
            refreshGridCashier(currentCountCashPages);
        } else {
            changeFilterClick(currentCashFilter, currentCountCashPages);
        }
    });
    $('#nextPag').mousedown(function (e) {
        if (maxPageCountCash * 50 > currentCountCashPages) {
            currentCountCashPages += 50;
        }
        if (currentCashFilter == 'nopay') {
            refreshGridCashier(currentCountCashPages);
        } else {
            changeFilterClick(currentCashFilter, currentCountCashPages);
        }
    });
    $('#pageNumCPag').change(function (e) {
        currentCountCashPages = ($('#pageNumCPag').val() - 1) * 50;
        refreshGridCashier($('#pageNumCPag').val() * 50);
    });
    $('.filterbtn').mousedown(function (e) {
        $('.filterbtn').css("background", "#1080dd")
        $(this).css("background", "#42c842");
        currentCashFilter = $(this).attr('peshey');
        currentCountCashPages = 0;
        changeFilterClick($(this).attr('peshey'), currentCountCashPages)
    });
    $('#btnchangeComboGroupItem').mousedown(function (e) {
        doSetPrinterFormComboItems();
    });
    $('#btnCancelsetPrinterComboItemForm').mousedown(function (e) {
        btnCancelsetPrinterComboItemFormClick();
    });
    $('#btnOksetPrinterComboItemForm').mousedown(function (e) {
        btnOksetPrinterComboItemFormClick();
    });
    $('#reportSalon').mousedown(function (e) {
        reportSalonClick();
    });
    $('#countbtncomboMenuForm').mousedown(function (e) {
        getCalc('comboCount', null, 'Введите количетво', null);
    });
    $('#employeeSelectBtn').mousedown(function (e) {
        doSelectEmployee();
    });
    $('#cancelbtnEmployee').mousedown(function (e) {
        cancelbtnEmployeeClick();
    });
    $('#okbtnEmployee').mousedown(function (e) {
        okbtnEmployeeClick();
    });
    $('#materialsbtn').mousedown(function (e) {
        materialsbtnClick();
    });
    $('#cancelbtnMaterialsForm').mousedown(function (e) {
        cancelbtnMaterialsFormClick();
    });
    $('#deletebtnMaterialsForm').mousedown(function (e) {
        deletebtnMaterialsFormClick();
    });
    $('#okbtnMaterialsForm').mousedown(function (e) {
        okbtnMaterialsFormClick();
    });
    $('#findbtnMaterialsForm').mousedown(function (e) {
        findbtnMaterialsFormClick();
    });
    $('#shtrihOFmaterials').keypress(function (e) {
        if (e.which == 13) {
            findbtnMaterialsFormClick();
        }
    });
    $('#searchField').focusin(function (e) {
        itemFocus = '';
    }); 
    $('#searchField').focusout(function (e) {
        itemFocus = 'shtrihOF';
    });
    $('#searchField').keypress(function (e) {
       if (e.which == 13) {
            if (simplemenu.length==1){
                if (configArray.askCount == 0) {
                    menuBtnClick(simplemenu[0].id,1);
                } else {
                    getCalc('countbtn',simplemenu[0].id,'Введите количество',null);
                }
            }
        }
    });
    $('#searchField').keyup(function (e) {
        sField=String.fromCharCode(e.which);
        if (e.keyCode==8&&sField.length==1){
            sField="";
        }
        if (sField.length==0){
             simplemenu=protoSimpleMenu;
             showMenu();
        }
        if ($('#searchField').val()!=""){
            getSearchingItem($('#searchField').val());
            showMenu();
        }
    });
    $('#resetSearch').mousedown(function (e) {
        simplemenu=protoSimpleMenu;
        $('#searchField').val("");
        showMenu();
    });
    $('#bsJournalbtn').mousedown(function (e) {
        doBsJournal();
    });
    $('#cancelbtnBsJournalForm').mousedown(function (e) {
        cancelbtnBsJournalFormClick();
    });
    $('#formAddToBsJournalCancelBtn').mousedown(function (e) {
        formAddToBsJournalCancelBtnClick();
    });
    $('#addNewRecordToJournalBtn').mousedown(function (e) {
        addNewRecordToJournalBtnClick();
    });
    $('#bsJournalAddClientBtn').mousedown(function (e) {
        bsJournalAddClientBtnClick();
    });
    $('#formAddToBsJournalOkBtn').mousedown(function (e) {
        formAddToBsJournalOkBtnClick();
    });
    $('#journalMainDate').change(function (e) {
        loadMastersIntoBsJournalTable();
    });
    $('#intervalSelect').change(function (e) {
        loadMastersIntoBsJournalTable();
    });
    $('#exitbtnCookInterface').mousedown(function (e) {
        closeCookForm();
    });
    $('#sbDv').change(function (e) {
        loadCookContent();
    });
    
    $('#printStickerBtn').mousedown(function (e) {
//        printStickerBtnClick();
        doSticker();
    });
    $('#optionBtn').mousedown(function (e) {
        optionBtnClick();
    });
    $('#btnCancelprintStickerForm').mousedown(function (e) {
        btnCancelprintStickerFormClick();
    });
    $('#btnCanceloptionsForm').mousedown(function (e) {
        btnCanceloptionsFormClick();
    });
    $('#btnOkprintStickerForm').mousedown(function (e) {
        btnOkprintStickerFormClick();
    });
    $('#findItemSticker').mousedown(function (e) {
        printStickerBtnClick();
    });
    $('#remainSee').mousedown(function (e) {
        if (configArray.warehouseid>0){
            $.post( '/company/warehouse/warehouse.php?do=remains', { chb: 'zasmenu', chb_zasmenu: configArray.idchange, warehouseid: configArray.warehouseid } ).success( function ( dataz ) {
                $("body").append('<div id="ochert" class="otchet_div">'+dataz+'<br /><button id="otchet_close" class="button red" onclick="otchet_close()">Выход</button></div>');
            });
        }
    });
    $('#changeEmployee').mousedown(function (e) {
        doChangeEmployee();
    });
   $('#btnOkchangeEmployeeForm').mousedown(function (e) {
        btnOkchangeEmployeeFormClick();
    });
   $('#btnCancelchangeEmployeeForm').mousedown(function (e) {
        btnCancelchangeEmployeeFormClick();
    });
    $('#showCalcSeekClient').mousedown(function (e) {
        showCalcSeekClientClick();
    });
//   $('#sc').mousedown(function (e) {
//        var el = document.getElementById( 'td1' );
//        el.scroll(20,0);
//    });
});


function loadIrest() {
    $('#calcdiv').show();
    $('#selectdiv').hide();
    $('#incalcdiv').hide();
    $('#chosebill').hide();
    $('#oficaintframe').hide();
    $('#cashierframediv').hide();
    $('#chooseInterface').hide();
    $('#regcheckForm').hide();
    $('#giftbtn').css("display", "none");
    $('#giftbtn').removeAttr("onClick");
    getCalc("front", null, 'Введите пароль для входа', 'password');
    getConfigInf();
    createrMenu();
    createrPayTypes();
    getTime();
    preLoadTable();
//    protoSimpleMenu=simplemenu;
    //$('#reportSalon').hide();menu
}


function printTCP(data) {
    
   $.ajax({
        type: "POST",
        url: "http://localhost:12345",
        data: data,
        dataType: "script"
    })
}


function sendToFR(){
//            if ($('#typepaylist').val()==configArray.cashid){
//                typePay=0
//            }else if ($('#typepaylist').val()==configArray.slipid){
//                    typePay=1
//            }else{
//                typePay=-1
//            }
//            doc =
//            "startPrintFR"+
//            "<Header>"+
//            "<order>order</order>"+            
//            "<typepay>"+typePay+"</typepay>"+
//            "<totalsum>"+orderdata.totalsum+"</totalsum>"+
//            "<servicepercent>"+orderdata.servicepercent+"</servicepercent>"+
//            "<sumfromclient>"+sumfromclient+"</sumfromclient>"+
//            "<discountpercent>"+orderdata.discountpercent+"</discountpercent>";  
//            
//            tmparray=defineNewItemsInTable();
//            k=1;
//            for (i=0;i<tmparray.length;i++){
//                doc+="\n<item"+k+"> \n";
//                doc+="<num>"+k+"</num>\n";
//                doc+="<foodname>"+tmparray[i].name+"</foodname>\n";
//                doc+="<price>"+tmparray[i].price+"</price>\n";
//                doc+="<quantity>"+tmparray[i].count+"</quantity>\n";
//                doc+="<summa>"+tmparray[i].summa+"</summa>\n";
//                doc+="</item"+k+">\n";
//                k++;
//            }
//            
//            doc+="</Header>";
//            printTCP(doc);
//            res=0;
//            $.ajax({
//                async:false,
//                type: "POST",
//                url: "http://localhost:12345",
//                data: data,
//                dataType: "script"
//            }).success(function (){
//                res=1;
//            });
//           if (res==0) {
//               alert("Не включен сервер торгового оборудования!");
//           }




            printTCP('CheckFrStatus');
}

function defineNewItemsInTable(){
                var tmparray = new Array();
                for (i = 0; i < ordertabledata.length; i++) {
                    if (ordertabledata[i].status == 'new') {
                        tmparray[tmparray.length] = ordertabledata[i];
                    }
                }   
                return tmparray;
}

//function closeCheckFr(XMLP){
//    printTCP(XMLP);
//}


function makeBill(prn, clsd) {
    rowcount = ordergrid.getRowCount();
    if (orderdata.printed == 1 && orderdata.closed == 1) {} else {
        if ((modyfied == 1) || (modyfiedt == 1)) {
            if ((rowcount > 0) || (orderdata.orderid > 0)) {
                if (orderdata.tableid == undefined) {
                    orderdata.tableid = 0;
                }
                if (orderdata.orderid == undefined) {
                    orderdata.orderid = 0;
                }
                if (orderdata.servicepercent == undefined) {
                    orderdata.servicepercent = 0;
                }
                //                orderdata.employeeid=employeeid;
                /* console.log(" Client " + orderdata.clientid + " Table " + orderdata.tableid + " bargin " + orderdata.sale +
                " Count " + orderdata.guestscount + " Service " + orderdata.servicepercent + " totalsum " + orderdata.totalsum + " Employeeid " + orderdata.employeeid
                + " printed " + prn + " closed " + clsd + " orderid " + orderdata.orderid+" discountsum"+orderdata.discountsum);*/
                /*console.log(ordertabledata) ; */
                
                tmparray=defineNewItemsInTable();
                if (tmparray.length == 0) {
                    tmparray = 'empty';
                }
                res = 0;
                $.ajax({
                    async: false, 
                    url: "/front/PHP/front.php?makebill",
                    type: "POST",
                    dataType: "json",
                    data: {
                        actionScript: 'saveOrder',
                        order: orderdata,
                        ordertable: tmparray,
                        printed: prn,
                        closed: clsd,
                        sumfromclient: sumfromclient,
                        interface: numcurrentinterface
                    }
                }).success(function (data) {
                    if (data.rescode == 0) {
                        res = 1;
                        if (data.xmlSubOrder != '') {
                            printTCP(data.xmlSubOrder);
                        }
                        if (data.xmlOrder != '') {
                            printTCP(data.xmlOrder);
                        }                        
                        modyfied = 0;
                        modyfiedt = 0;
                        orderdata.orderid = 0;
                        orderdata.printed = -1;
                        orderdata.closed = -1;
                        if (data.gifts != 0) {
                            giftOrderId = data.orderid;
                            giftBalans = data.balans;
                            getGifts(0, data.gifts, 'count');
                        };
                        $.ajax({
                                 async: true,
                                 url: "/front/PHP/front.php?conductAfterSale",
                                 type: "POST",
                                 dataType: "json",
                                 data: {actionScript: 'doConductAfterSale'}
                         })
                    } else {
                        console.log(data.rescode + ':' + data.resmsg);
                        alert(data.rescode + ':' + data.resmsg);
                    };
                });
                if (res != 0) {
                    ordergrid.clearTable();
                } else {
                    Msg("Счет не был сохранен! Повторите попытку снова!");
                }
                return res;
            } else {
                alert("Пустой счет небыл сохранен!");
                return 1;
            }
        } else {
            return res = 1;
        };
    }

}

function Msg(value) {
    $("#msg").html(value);
    $("#msg").dialog({
        resizable: false,
        height: 245,
        modal: true,
        buttons: {
            "Да": function () {
                $(this).dialog("close");
            }
        }
    });
}

function clearVariable() {
    orderdata.totalsum = 0;
    orderdata.orderid = 0;
    orderdata.discountpercent = 0;
    orderdata.discountsum = 0;
    orderdata.clientname = 0;
    orderdata.clientid = 0;
    orderdata.tableid = 0;
    orderdata.servicepercent = configArray.defaultservicepercent;
    orderdata.servicesum = 0;
    orderdata.employeeid = 0;
    orderdata.cashid = 0;
    orderdata.paymentid = 0;
    orderdata.guestscount = 1;
    orderdata.printed = 0;
    orderdata.closed = 0;
    orderdata.discountid = 0;
    ordertabledata = undefined;
    ordertabledata = new Array();
    clients = new Array();
    simplemenu=protoSimpleMenu;
}

function showGlass(event) {
    if (event == 1) {
        $("#glass").show();
        $("#glass").attr("onClick", "backInterface('readonly')");
    } else if (event == 0) {
        $("#glass").hide();
        $("#glass").removeAttr("onClick")
    }

}

function showOffButtons() {
    $("#countbtn").show();
    $("#servicebtn").show();
    $("#salebtn").show();
    $("#tablebtn").show();
    $("#clientbtn").show();
    $("#shtrihbtnOF").show();
    $("#printbtn").show();
    $("#repeatbtn").show();
    $("#deletebtn").show();
    $("#commentbtn").show();
    $("#printdiv").show();
    $("#clientdiv").show();
    $("#tablediv").show();
    $("#servicediv").show();
    $("#salediv").show();
    $("#countdiv").show();

    $("#employeeSelectDiv").hide();
    $("#employeeSelectDivLabel").hide();
    $("#employeeSelectBtn").hide();
    $('#materialsbtn').hide();
    $('#bsJournalbtn').hide();

    $('.labelstyles').show();
    clbtn = document.getElementById('clientdiv');
    shtbtn = document.getElementById('shtrihOF');
    clbtn.removeAttribute("readonly");
    shtbtn.removeAttribute("readonly");
}

function hideOffButtons() {
    $("#countbtn").hide();
    $("#servicebtn").hide();
    $("#salebtn").hide();
    $("#tablebtn").hide();
    $("#clientbtn").hide();
    $("#shtrihbtnOF").hide();
    $("#giftbtn").hide();
    $("#printbtn").hide();
    $("#repeatbtn").hide();
    $("#deletebtn").hide();
    $("#commentbtn").hide();
    $("#changePrice").hide();
    $("#employeeSelectDiv").hide();
    $("#employeeSelectDivLabel").hide();
    $("#employeeSelectBtn").hide();
    $('#materialsbtn').hide();
    clbtn = document.getElementById('clientdiv');
    shtbtn = document.getElementById('shtrihOF');
    clbtn.setAttribute("readonly", "readonly");
    shtbtn.setAttribute("readonly", "readonly");
}

function hideOffCashButtons() {
    $("#tablebtn").hide();
    $("#shtrihbtnOF").hide();
    $("#giftbtn").hide();
    $("#printbtn").hide();
    $("#repeatbtn").hide();
    $("#deletebtn").hide();
    $("#commentbtn").hide();
    $("#changePrice").hide();
    shtbtn = document.getElementById('shtrihOF');
    shtbtn.setAttribute("readonly", "readonly");
}


function showPrintedOrder() {
    $('#menudiv').html('');
    itemFocus = '';
    hideOffButtons();
}

function showPrintedOrderCash() {
    $('#menudiv').html('');
    itemFocus = '';
    hideOffCashButtons();
}

function showOrder(interfaceflag) {
    if ((interfaceflag == 'cashierframediv_oficaintframe') || (interfaceflag == 'pay')||(interfaceflag == 'roomadministrator_oficaintframe')) {
        orderid = cashierbillstore[cashgrid.getNum()].id;
    } else {
        orderid = officiantbillstore[offgrid.getNum()].id;
    }
    clearVariable();
    showMenu();
    ordergrid.clearTable();
    if (orderid == undefined) {
        orderid = 0;
    }
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?ShowOrder",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'showOrder',
            orderid: orderid,
            flag:'norm'
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            orderdata.orderid = data.order[0].orderid;
            orderdata.guestscount = data.order[0].guests;
            orderdata.totalsum = data.order[0].totalsum;
            orderdata.closed = data.order[0].closed;
            orderdata.printed = data.order[0].printed;
            orderdata.servicepercent = data.order[0].servicepercent;
            orderdata.servicesum = data.order[0].servicesum;
            orderdata.clientid = data.order[0].client;
            orderdata.clientname = data.order[0].clientname;
            orderdata.discountpercent = data.order[0].discountpercent;
            orderdata.discountsum = data.order[0].discountsum;
            orderdata.tableid = data.order[0].tableid;
            orderdata.tablename = data.order[0].tablename;
            $("#user").html(data.order[0].employeename);
            $("#countdiv").html(data.order[0].guests);
            $("#servicediv").html(data.order[0].servicepercent + '%');
            $("#salediv").html(data.order[0].discountpercent + '%');
            $("#printdiv").html(data.order[0].totalsum);
            $("#chdt").html(data.order[0].dt);
            $("#chid").html(data.order[0].visibleid);
            $("#clientdiv").val(data.order[0].clientname);
            $("#tablediv").html(data.order[0].tablename);
            bills = data.ordertable;
            if (bills != null && bills != 'empty') {
                ordertabledata = bills;
                tmparray = preperaVisibleArray('ordertable', ordertabledata);
                ordergrid.fillTable(tmparray);
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    })
    if (interfaceflag == "pay") {

    } else {
        currentinterface = interfaceflag;
        openInterface();
        if (interfaceflag == 'cashierframediv_oficaintframe'|| interfaceflag =='roomadministrator_oficaintframe') {
            if (orderdata.closed==1){
                showGlass(1);
            }else{
                showGlass(orderdata.printed);
            }
            showPrintedOrderCash();
        } else {
            if (orderdata.printed == 1) {
                showPrintedOrder();
            }
        }

    }
}

function newOrder() {
    showGlass(0);
    ordergrid.clearTable();
    fillBillHeader();
    currentinterface = "chosebill_oficaintframe";
    openInterface();
    if (configArray.usechoosetable == 1) {
        $('#tablebtn').mousedown();
    }
}


function getCalc(type, id, promptcalc, inputType) {
    centralizeElement("incalcdiv");
    $("#incalcdiv").css('background','#f1f1f1');
    $("#promptcalc").html(promptcalc);
    $("#inpwdcalc").html("");
    $("#inpwdcalc").val("");
    $("#inpwdcalc").attr("type", "text");
    itemFocus = 'inpwdcalc';
    document.getElementById('inpwdcalc').focus();
    if (inputType == "password") {
        $("#inpwdcalc").attr("type", "password");
//        $("#incalcdiv").css('background','darkorange');        
    };
    document.getElementById('typepaylist').selectedIndex = 0;
    if (type == "chosepay") {
        if (configArray.rememberAboutDiscount==1&&configArray.defaultclientid==orderdata.clientid&&numcurrentinterface==4){
            alert('Вы не выбрали клиента!');
        }
        $("#incalcdiv").css('background','darkseagreen');
        if (parseInt(orderdata.totalsum) >= 0) {
            if (configArray.useBuyerDisplay==1){
                displStr='<Header><order>displayNeedSum</order><displayNeedSum>'+orderdata.totalsum+'</displayNeedSum></Header>';
                 $.ajax({
                        type: "POST",
                        url: "http://localhost:12345", 
                        data: displStr,
                        dataType: "script"
                })
            }
            $("#glass").show();
            $("#incalcdiv").width(730);
            centralizeElement("incalcdiv");
            $("#incalcdiv").show();
            $("#chosepaybtns").show();
            $('#noSaldoBtn').show();
            if (configArray.noSaldoButton == 0) {
                $('#noSaldoBtn').show();
            } else if (configArray.noSaldoButton == 1) {
                $('#noSaldoBtn').hide();
            }
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

function showCalc(promptcalc) {
    numcurrentinterface = -1;
    killSession();
    $('#giftbtn').css("display", "none");
    $('#giftbtn').removeAttr("onClick");
    $('#calcdiv').show();
    $('#promptcalc').html(promptcalc);
    $('#incalcdiv').hide();
    $('#chosebill').hide();
    $('#oficaintframe').hide();
    $('#cashierframediv').hide();
    $('#chooseInterface').hide();
    $('#regcheckForm').hide();
    $('#cookInterface').hide();
    $('#fitness').hide();
    getCalc("front", null, 'Введите пароль для входа', 'password');
}

function stopCalc() {
    if ($('#selectdiv').css("display") != 'block') {
        $("#glass").hide();
    }
    $("#chosepaybtns").hide();
    $("#incalcdiv").hide();
    $("#incalcdiv").width(335);
    //$('#incalcdiv').stop().animate({"opacity": "0"}, "slow");
    incalcstatus = "";
    incalcshowed = 0;
    oplata = 0;
    $("#chosepaybalanse").html(0);
    itemFocus = 'shtrihOF';
}


function doMakeBillAfterCheck(){
        res = makeBill(1, 1);
        if (res == 1) {
            fillBillHeader();
            if (numcurrentinterface != 6) {
                showOffButtons();
            }
            $("#printdiv").html(0);
            orderdata.totalsum = 0;
            oplata = 0;
            stopCalc();
            refreshGridCashier(currentCountCashPages);
        };    
}


function payClick(i) {
    $("#glass").removeAttr("onClick");
    checkNoCash=0;
    if (i != 0) {
//       if (parseInt(orderdata.totalsum)<parseInt(10000)){
            sumfromclient = orderdata.totalsum;
            oplata++;
//       }else{
//            checkNoCash=1;
//       }
    }
    if (oplata != 0) {
        modyfied = 1;
        modyfiedt = 1;
        orderdata.paymentid = document.getElementById('typepaylist').value;
        if (configArray.useFR==1)
            sendToFR()
        else
            doMakeBillAfterCheck()
//        res = makeBill(1, 1);
//        if (res == 1) {
//            fillBillHeader();
//            if (numcurrentinterface != 6) {
//                showOffButtons();
//            }
//            $("#printdiv").html(0);
//            orderdata.totalsum = 0;
//            oplata = 0;
//            stopCalc();
//            refreshGridCashier(currentCountCashPages);
//        };
    } else {
//        if (checkNoCash==0){
            alert("Недостаточная сумма!");
//        }else{
//            alert("!");
//        }
    }
}



function printBillClick() {
    if ((ordergrid.getRowCount() > 0) || (orderdata.orderid > 0)) {
        modyfied = 1;
        modyfiedt = 1;
        if (makeBill(1, 0) == 1) {
            orderdata.orderid = 0;
            if (configArray.waiterCanTakePayment == 0) {
                closeInterfaces();
            } else if (configArray.waiterCanTakePayment == 1) {
                fillBillHeader();
            }
        }
    } else {
        Msg("Заказ не может быть пустым!")
    }
}


function fillBillHeader() {
    clearVariable();
    showMenu();
    $('#employeeSelectDiv').html("");
    $("#user").html(orderdata.employeename);
    $("#countdiv").html("1");
    if (configuringInterface == "fastfood") {
        if (parseInt(configArray.serviceinfastfood) == 0) {
            orderdata.servicepercent = 0;
        }
    }
    $("#servicediv").html(orderdata.servicepercent + "%");
    $("#salediv").html("0%");
    $("#chid").html("");
    $("#chdt").html("");
    $("#tablediv").html("");
    $("#printdiv").html("0");
    $("#searchField").val("");
    orderdata.clientid = configArray.defaultclientid;
    orderdata.clientname = configArray.defaultclientname;
    $("#clientdiv").val(orderdata.clientname);
    $('#giftbtn').css("display", "none");
    $('#giftbtn').removeAttr("onClick");
    
    ordergrid.clearTable();
}


function defineCurInterface() {
    switch (parseInt(numcurrentinterface)) {
    case 2:
        {
            interface = "cashierframediv";
            break;
        }
    case 3:
        {
            interface = "chosebill";
            break;
        }
    case 4:
        {
            interface = "oficaintframe";
            break;
        }
    case 5:
        {
            interface = "roomadministrator";
            break;
        }
    case 6:
        {
            interface = "selfservice";
            break;
        }
    case 7:
        {
            interface = "regcheckForm";
            break;
        }
    case 8:
        {
            interface = "cookInterface";
            break;
        }
    case 9:
        {
            interface = "fitnessInterface";
            break;
        }
    };
    return interface;
}


function backInterface(flag) {
    if (flag == 'readonly') {
        $('#' + currentinterface).hide();
        currentinterface = defineCurInterface();
        openInterface();
    } else {
        if (makeBill(0, 0) == 1) {
            $('#' + currentinterface).hide();
            if (flag == 'exit') {
                closeInterfaces();
            } else {
                currentinterface = defineCurInterface();
                openInterface();
            }
        }
    }
}

function closeInterfaces() {
    showCalc('Введите пароль');
}

function prepareInterface() {
    $('#calcdiv').hide();
    $('#incalcdiv').hide();
    $('#chosebill').hide();
    $('#oficaintframe').hide();
    $('#cashierframediv').hide();
    $('#chooseInterface').hide();
    $('#regheckForm').hide();
    $('#glass').hide;
    $('#glass2').hide;
    $('#cookInterface').hide;
    $('#fitnessInterface').hide;
    
    showMenu();
}

function openInterface() {
    selectedrow = -1;
    orderidClk = 0;
    showOffButtons();
    if (currentinterface == "oficaintframe") {
        $('#printbtn').html("Оплата");
        $('#printbtn').attr("onClick", "if (ordergrid.getRowCount()>0){getCalc('chosepay',null,'Введите сумму от клиента',null)}");
        if (configArray.waiterCanTakePayment == 1) {
            $('#exitbtn').html("Выход");
            $('#exitbtn').attr("onClick", "showCalc('Введите пароль на вход')");
            $('#exitbtn').removeClass("button green");
            $('#exitbtn').addClass("exitbtnC button red");
        } else {
            $('#exitbtn').attr("onClick", "showCalc('Введите пароль на вход')");
        }

        $('#backbtn').hide();
        if (configArray.useChangePrice == 0) {
            $('#changePrice').hide();
        } else if (configArray.useChangePrice == 1) {
            $('#changePrice').show();
        }
        numcurrentinterface = 4;
        orderdata.interfaceid = 4;
        itemFocus = 'shtrihOF';
        configuringInterface = "fastfood";
    };
    if (currentinterface == "cashierframediv") {
//        $('#glass').hide;
//        $('#glass2').hide;
        document.getElementById("glass").style.display = "none";
        document.getElementById("glass2").style.display = "none";
        $('#printbtnC').attr("onClick", "if ((cashgrid.getNum()>=0)&&(cashgrid.getRowCount()>0)){payCashier()}else{Msg('Выберите счет!')}");
        $('#showOrderFromCash').attr("onClick", "if ((cashgrid.getNum()>=0)&&(cashgrid.getRowCount()>0)){showOrder('cashierframediv_oficaintframe')}else{Msg('Выберите счет!')}");
        $('#btnchangeOpen').show();
        $('#btnchangeClose').show();
        $('#printbtnC').show();
        configuringInterface = '';
        numcurrentinterface = 2;
        orderdata.interfaceid = 2;
        $('#btnchangeNotPayed').mousedown();
    };
    if (currentinterface == "cashierframediv_oficaintframe") {
        $('#printbtn').html("Печать счета");
        $('#printbtn').attr("onClick", "");
        $('#exitbtn').html("Выход");
        $('#exitbtn').removeClass("button green");
        $('#exitbtn').addClass("exitbtnC button red");
        $('#backbtn').attr("onClick", "backInterface('" + interface + "')");
        $('#exitbtn').attr("onClick", "backInterface('exit')");
        configuringInterface = '';
        currentinterface = "oficaintframe";
        numcurrentinterface = 2;
        orderdata.interfaceid = 2;
        $('#backbtn').show();
        if (configArray.useChangePrice == 0) {
            $('#changePrice').hide();
        } else if (configArray.useChangePrice == 1) {
            $('#changePrice').show();
        }
    };
    if (currentinterface == "chosebill_oficaintframe") {
        $('#printbtn').html("Печать счета");
        $('#printbtn').attr("onClick", "printBillClick()");
        $('#backbtn').attr("onClick", "backInterface('" + interface + "')");

        $('#menudiv').html('');

        if (configArray.waiterCanTakePayment == 1) {
            $('#exitbtn').html("Оплата");
            $('#exitbtn').attr("onClick", "if (ordergrid.getRowCount()>0){getCalc('chosepay',null,'Введите сумму от клиента',null)}");
            $('#exitbtn').removeClass("exitbtnC button red");
            $('#exitbtn').addClass("button green");
        } else {
            $('#exitbtn').attr("onClick", "backInterface('exit')");
        }
        configuringInterface = '';
        currentinterface = "oficaintframe";
        numcurrentinterface = 3;
        itemFocus = 'shtrihOF';
        orderdata.interfaceid = 3;
        $('#backbtn').show();
        if (configArray.useChangePrice == 0) {
            $('#changePrice').hide();
        } else if (configArray.useChangePrice == 1) {
            $('#changePrice').show();
        }
    };
    if (currentinterface == "roomadministrator_oficaintframe") {
        
        $('#backbtn').attr("onClick", "backInterface('oficaintframe')");
        $('#exitbtn').attr("onClick", "backInterface('exit')");
        
        configuringInterface = '';
        currentinterface = "oficaintframe";
        numcurrentinterface = 5;
        itemFocus = '';
        $('#changePrice').hide();
       
    };
    if (currentinterface == "chosebill") {
        $('#newbillbtn').attr("onClick", "newOrder()");
        $('#showOrderFromOff').attr("onClick", "if ((offgrid.getNum()>=0)&&(offgrid.getRowCount()>0)){showOrder('chosebill_oficaintframe')}else{Msg('Выберите счет!')}");
        numcurrentinterface = 3;
        refreshGridOfficiant();
    };
    if (currentinterface == "roomadministrator") {
        $('#btnchangeOpen').hide();
        $('#btnchangeClose').hide();
        $('#printbtnC').hide();
        
        $('#showOrderFromCash').attr("onClick", "if ((cashgrid.getNum()>=0)&&(cashgrid.getRowCount()>0)){showOrder('roomadministrator_oficaintframe')}else{Msg('Выберите счет!')}");
        currentinterface = 'cashierframediv';
        numcurrentinterface = 5;
        $('#btnchangeNotPayed').mousedown();
    };
    if (currentinterface == "regcheckForm") {
        numcurrentinterface = 7;
        $("#labelRegCheck").html("Сотрудник: " + orderdata.employeename + "; Список ваших счетов: ");
        refreshRegCheckOrder();
        itemFocus = 'shinput';
    };
    if (currentinterface == "cookInterface") {
        $("#cookName").html("Сотрудник: " + orderdata.employeename);
        numcurrentinterface = 8;
        cookSb();
//        loadCookContent();
    };
    if (currentinterface == "fitness") {
//        numcurrentinterface = 9;
//        prepareFitnessInterface();
//        showFitnessTable();
            document.location.href = "/front/fitnes";
    };
    if (currentinterface == "selfservice") {
        $('#printbtn').html("Оплата");
        $('#printbtn').attr("onClick", "if (ordergrid.getRowCount()>0){getCalc('chosepay',null,'Введите сумму',null)}");
        $('#exitbtn').attr("onClick", "showCalc('Введите пароль на вход')");
        $('#backbtn').hide();

        $('#labelPeopleCount').hide();
        $('#countdiv').hide();
        $('#countbtn').hide();
        $('#labelTable').hide();
        $('#tablediv').hide();
        $('#tablebtn').hide();
        $('#labelService').hide();
        $('#servicebtn').hide();
        $('#servicediv').hide();

        $('#commentbtn').hide();
        $('#materialsbtn').show();

        $("#employeeSelectDiv").show();
        $("#employeeSelectDivLabel").show();
        $("#employeeSelectBtn").show();

        $('#bsJournalbtn').show();

        if (configArray.useChangePrice == 0) {
            $('#changePrice').hide();
        } else if (configArray.useChangePrice == 1) {
            $('#changePrice').show();
        }
        numcurrentinterface = 6;
        orderdata.interfaceid = 6;
        itemFocus = 'shtrihOF';
        configuringInterface = "fastfood";
        currentinterface = "oficaintframe";
    };


    prepareInterface();

    $('#' + currentinterface).show();
    checkSessionTimer();
}



function ChooseInterfaceBtnClick(id) {
    //    orderdata.employeeid = empInf[id].id;
    orderdata.employeename = empInf[id].name;
    interface = empInf[id].interfaces;
    employeeid = empInf[id].id;
    currentinterface = '';
    numcurrentinterface = -1;
    needCreateSession = true;
    //console.log(numcurrentinterface);
    switch (parseInt(interface)) {
    case 0:
        {
            document.location.href = '/company';
            break;
        }
    case 1:
        {
            document.location.href = '/company';
            break;
        }
    case 2:
        {
            currentinterface = "cashierframediv";
            configuringInterface = "";
            checkChange();
            $("#userCash").html(orderdata.employeename);
            $("#changeInfo").html('Торговая точка: ' + changeInf.apname + '; Смена: ' + changeInf.name + ' от ' + changeInf.dtopen + ' _ ' + changeInf.dtclosed);
            break;
        }
    case 3:
        {
            currentinterface = "chosebill";
            configuringInterface = "";
            if (closeChange == 1) {
                needCreateSession = false;
                getCalc("front", null, 'Введите пароль для входа', 'password');
                $("#msg").html("Cмена не открыта!");
                $("#msg").dialog({
                    resizable: false,
                    height: 180,
                    modal: true,
                    buttons: {
                        "Да": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            } else {
                $("#userOf").html(orderdata.employeename);
            }
            break;
        }
    case 4:
        {
            currentinterface = "oficaintframe";
            configuringInterface = "fastfood";
            if (closeChange == 1) {
                needCreateSession = false;
                getCalc("front", null, 'Введите пароль для входа', 'password');
                $("#msg").html("Cмена не открыта!");
                $("#msg").dialog({
                    resizable: false,
                    height: 180,
                    modal: true,
                    buttons: {
                        "Да": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            } else {
                fillBillHeader();
            }
            break;
        }
    case 5:
        {
            currentinterface = "roomadministrator";
            configuringInterface = "";
            if (closeChange == 1) {
                needCreateSession = false;
                getCalc("front", null, 'Введите пароль для входа', 'password');
                $("#msg").html("Cмена не открыта!");
                $("#msg").dialog({
                    resizable: false,
                    height: 180,
                    modal: true,
                    buttons: {
                        "Да": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            } else {
                $("#userCash").html(orderdata.employeename);
                $("#changeInfo").html('Торговая точка: ' + changeInf.apname + '; Смена: ' + changeInf.name + ' от ' + changeInf.dtopen + ' _ ' + changeInf.dtclosed);
            }


            break;
        }
    case 6:
        {
            currentinterface = "selfservice";
            configuringInterface = "";
            if (closeChange == 1) {
                needCreateSession = false;
                getCalc("front", null, 'Введите пароль для входа', 'password');
                $("#msg").html("Cмена не открыта!");
                $("#msg").dialog({
                    resizable: false,
                    height: 180,
                    modal: true,
                    buttons: {
                        "Да": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            } else {
                fillBillHeader();
            }
            break;
        }
    case 7:
        {
            currentinterface = "regcheckForm";
            configuringInterface = "";
            if (closeChange == 1) {
                needCreateSession = false;
                getCalc("front", null, 'Введите пароль для входа', 'password');
                $("#msg").html("Cмена не открыта!");
                $("#msg").dialog({
                    resizable: false,
                    height: 180,
                    modal: true,
                    buttons: {
                        "Да": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            } else {

            }
            break;
        }
        case 8:
        {
            currentinterface = "cookInterface";
            configuringInterface = "";
            if (closeChange == 1) {
                needCreateSession = false;
                getCalc("front", null, 'Введите пароль для входа', 'password');
                $("#msg").html("Cмена не открыта!");
                $("#msg").dialog({
                    resizable: false,
                    height: 180,
                    modal: true,
                    buttons: {
                        "Да": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            } else {

            }
            break;
        }
        case 9:
        {
            currentinterface = "fitness";
            configuringInterface = "";
            if (closeChange == 1) {
                needCreateSession = false;
                getCalc("front", null, 'Введите пароль для входа', 'password');
                $("#msg").html("Cмена не открыта!");
                $("#msg").dialog({
                    resizable: false,
                    height: 180,
                    modal: true,
                    buttons: {
                        "Да": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            } else {

            }
            break;
        }
    };
    
    if (empInf.length > 1 && needCreateSession) {
        empInf[id].interfaces = createSession(empInf[id].interfaces);
    }
    $("#chooseInterface").html("");
    $("#chooseInterface").hide();
    if (empInf[id].interfaces != -1) {
        if ((closeChange != 1 || currentinterface == "cashierframediv") && (currentinterface != "")) {
            openInterface();
        }
    } else if (empInf[id].interfaces == -1) {
        $('#giftbtn').css("display", "none");
        $('#giftbtn').removeAttr("onClick");
        getCalc("front", null, 'Введите пароль для входа', 'password');
    }
}


function createButtonChooseInterface(id, inner, cl, valueInterface) {
    var newBtn;
    newBtn = document.createElement("button");
    newBtn.setAttribute("class", cl);
    newBtn.setAttribute("id", id + "ChooseBTN");
    newBtn.setAttribute("onmousedown", 'ChooseInterfaceBtnClick(' + id + ')');
    newBtn.setAttribute("style","font-size:12px");
    if (cl != "menubtnst") {
        newBtn.innerHTML = parseInt(id + 1) + '. ' + inner;
    } else {
        newBtn.innerHTML = "<p>" + inner + "</p>";
        var newDiv = document.createElement("div");
        newDiv.setAttribute("class", "btnpricecell");
        newDiv.innerHTML = price;
        newBtn.appendChild(newDiv);
    }
    return newBtn;
}

function shtrihSelect(c) {
    if (c == "" && (numcurrentinterface == 4 || numcurrentinterface == 6)) {
        if (ordergrid.getRowCount() > 0) {
            getCalc('chosepay', null, 'Введите сумму от клиента', null);
        }
    } else {
        $('#shtrihOf').val("");
        $.ajax({
            async: false,
            url: "/front/PHP/front.php?SelectByShtrih",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: 'selectByShtrih',
                shtrih: c
            }
        }).success(function (data) {
            //Добавление по штрих коду из локального меню
            rowcount = ordergrid.getRowCount();
            if (data.rescode == 0) {
                if (data.row.length > 1) {
                    shtrihItemsStore = data.row;
                    tmparray = preperaVisibleArray('shtrihSelect', shtrihItemsStore);
                    shtrihSelectGrid.fillTable(tmparray);
                    shtrihSelectGrid.selectRow(0);
                    showForm('shtrihSelectForm');
                    itemFocus = "";
                } else {
                    ordertabledata[rowcount] = new Object();
                    ordertabledata[rowcount].visibleid = rowcount + 1;
                    ordertabledata[rowcount].id = data.row[0].id;
                    ordertabledata[rowcount].name = '+' + data.row[0].name;
                    ordertabledata[rowcount].price = data.row[0].price;
                    ordertabledata[rowcount].count = 1;
                    ordertabledata[rowcount].summa = 1 * data.row[0].price;
                    ordertabledata[rowcount].status = 'new';
                    ordertabledata[rowcount].note = '';
                    ordertabledata[rowcount].printer = 0;
                    ordertabledata[rowcount].weight = data.row[0].weight;

                    if (ordertabledata[rowcount].weight == 1) {
                        ordertabledata[rowcount].count = 0;
                        getCalc('countbtnShtrih', ordertabledata[rowcount].id, 'Введите количество', null);
                    }
                    tmparray = preperaVisibleArray('ordertable', ordertabledata);
                    ordergrid.fillTable(tmparray);
                    columnSum();
                    modyfied = 1;
                }
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }
        });
    }
}

function selectEmp(pwd) {
    closeChange = 0;
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?SelectEmployee",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'selectEmployee',
            shtemp: pwd
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            changeInf = data.infoChange;
            empInf = data.emp;
            closeChange = data.closeChange;
            var chooseInt = document.getElementById("chooseInterface");
            $.each(empInf, function (index, value) {
                if (empInf.length > 1) {
                    $("#glass").hide();
                    $("#incalcdiv").hide();
                    centralizeElement('chooseInterface');
                    $('#chooseInterface').show();
                    incalcshowed = 0;
                    chooseInt.appendChild(createButtonChooseInterface(index, value.interfaceName, "button grey p active"));
                } else {
                    ChooseInterfaceBtnClick(index)
                }
            });

        } else {
            getCalc("front", null, 'Введите пароль для входа', 'password');
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        };
    });
}

function changeBalance() {
    if (incalcstatus == "chosepay") {
        ch = $("#inpwdcalc").val();
        if (parseInt(ch) >= parseInt(orderdata.totalsum)) {
            $("#chosepaybalanse").html("" + (parseInt(ch) - parseInt(orderdata.totalsum)));
            sumfromclient = $("#inpwdcalc").val();
            oplata++;
        } else {
            $("#chosepaybalanse").html(0);
            oplata = 0;
        }
    }
}


function getWeigth() {
    $.ajax({
        type: "POST",
        url: "http://localhost:12345",
        data: 'weigth',
        dataType: "script"
    });
}

function btninClick(v) {
    if (v == "bs") {
        str = $("#inpwdcalc").val();
        str = str.substr(0, str.length - 1);
        $("#inpwdcalc").val(str);
        changeBalance();
    } else {
        if (v == "ent") {
            //$('#incalcdiv').stop().animate({"opacity": "0"}, "slow");           
            var count = $("#inpwdcalc").val();
            //            if (count!=""){
            //                count=parseInt(count);
            //            }else{
            //                count=0;
            //            }
            if (count[0] == '.') {
                Msg('Введены некорректные данные!');
            } else {
                if (count != "" || incalcstatus == "countbtnShtrih") {
                    switch (incalcstatus) {
                    case "count":
                        count = parseInt(count);
                        orderdata.guestscount = count;
                        $("#countdiv").html(count);
                        modyfied = 1;
                        break;
                    case "returnCount":
                        count = parseInt(count);
                        addToReturn(count);
                        break;
                    case "divideCount":
                        count = parseInt(count);
                        addToDivided(count);
                        break;
                    case "service":
                        count = parseInt(count);
                        orderdata.servicepercent = count;
                        columnSum();
                        $("#servicediv").html(count + "%");
                        modyfied = 1;
                        break;
                    case "shtrih":
                        shtrihSelect(count)
                        break;
                    case "front":
                        selectEmp(count);
                        break;
                    case "print":
                        payClick(count);
                        break;
                    case "addcount":
                        if (parseFloat(count) > 0)
                            repeatItem(count);
                        break;
                    case "countbtn":
                        menuBtnClick(idBtn, count);
                        break;
                    case "countbtnShtrih":
                        rownum = ordergrid.getNum();
                        if (count == '' && ordertabledata[rownum].count == 0) {
                            ordertabledata.splice((rownum), 1);
                        } else {
                            ordertabledata[rownum].count = parseFloat(count);
                            ordertabledata[rownum].summa = Math.round((ordertabledata[rownum].count * ordertabledata[rownum].price) * 100) / 100;
                        }
                        tmparray = preperaVisibleArray('ordertable', ordertabledata);
                        ordergrid.fillTable(tmparray);
                        columnSum();
                        break;
                    case "unblockOrder":
                        unlockBtn(count);
                        break;
                    case "refusePwd":
                        rownum = ordergrid.getNum();
                        q = ordertabledata[rownum].count;
                        checkRefuse(parseFloat(q), count);
                        break;
                    case "clientPwd":
                        rownum = ordergrid.getNum();
                        $('#filterClientInput').val("");
                        //                            clientsgrid.clearTable();
                        document.getElementById('filterOptions').selectedIndex = 0;
                        beginClientSeletion(count);
                        break;
                    case "refuseCount":
                        rownum = ordergrid.getNum();
                        q = ordertabledata[rownum].count;
                        if (parseFloat(count) > parseFloat(q)) {
                            alert('Введенное количество больше чем количество товара!');
                            return;
                        } else {
                            if (parseFloat(count) > 0) {
                                doRefuse(count, refusePwd);
                            } else {
                                alert('Нельзя отказать 0 позиций!');
                            }
                        }
                        break;
                    case "pwdReturn":
                        $.ajax({
                            async: false,
                            url: "/front/PHP/front.php?checkPwdReturn",
                            type: "POST",
                            dataType: "json",
                            data: {
                                actionScript: "checkPwdReturn",
                                pwd: count
                            }
                        }).success(function (data) {
                            if (data.rescode == 0) {
                                $('#dateReturnOrder').val(new Date().toJSON().slice(0, 10));
                                showForm('orderSelectReturnForm');
                            } else {
                                console.log(data.rescode + ':' + data.resmsg);
                                alert(data.rescode + ':' + data.resmsg);
                            }
                        });
                        break;
                    case "pwdDivide":
                        if (cashgrid.getNum() > -1) {
                            showDivideForm(count);
                        } else {
                            Msg("Не выбран счет!");
                        }
                        break;
                    case "pwdStopList":
                        stopListPwd(count); 
                        break;
                    case "pwdexit":
                        dopwdexit(count);
                        break;
                    case "pwdChangePrice":
                        doChangePrice(count);
                        break;
                    case "changePrice":
                        ordertabledata[ordergrid.getNum()].price = parseFloat(count);
                        
                        ordertabledata[ordergrid.getNum()].summa = Math.round((ordertabledata[ordergrid.getNum()].count * ordertabledata[ordergrid.getNum()].price) * 100) / 100;
                        tmparray = preperaVisibleArray('ordertable', ordertabledata); 
                        ordergrid.fillTable(tmparray);
                        columnSum();
                        break;
                    case "pwdReportAkt":
                        doReportAkt(count);
                        break;
                    case "pwdReportPoschetam":
                        doReportPoschetam(count)
                        break;
                    case "pwdReportItog":
                        doReportItog(count)
                        break;
                    case "pwdReportRefuse":
                        doReportRefuse(count);
                        break;
                    case "pwdReportRefuse_and_orders":
                        doReportRefuse_and_orders(count)
                        break;
                    case "pwdReportXreport":
                        doReportXreport(count);
                        break;
                    case "pwdAddClient":
                        showAddClientForm(count);
                        break;
                    case "pwdAddNote":
                        showAddNoteForm(count);
                        break;
                    case "pwdService":
                        doService(count);
                        return;
                        break;
                    case "pwdclose":
                        dopwdclose(count);
                        break;
                    case "comboPwd":
                        doComboAdd(count);
                        break;
                    case "addToComboItem":
                        addToComboItem(count);
                        break;
                    case "setDiscount":
                        //                            orderdata.discountid=0;
                        orderdata.discountpercent = parseInt(count);
                        columnSum();
                        document.getElementById('salediv').innerHTML = orderdata.discountpercent + '%';
                        modyfied = 1;
                        break;
                    case "comboCount":
                        okbtnCombo(count);
                        break;
                    case "countbtnMaterials":
                        menuBtnClickMaterials(idBtn, count)
                        break;
                    case "pwdDiscount":
                        if (checkDiscountPassword(count)) {
                            getCalc('setDiscount', null, 'Введите скидку', null);
                            return;
                        };
                        break;
                    case "calcInSeekClient":
                            $('#filterClientInput').val(count);
                        break;
                    case "countMaterial":
                        addMaterialToSelected(parseInt(count));
                        break;
                    case "pwdDeleteFromOrder":
                        $.ajax({
                            async: false,
                            url: "/front/PHP/front.php?pwdDeleteFromOrder",
                            type: "POST",
                            dataType: "json",
                            data: {
                                actionScript: "pwdDeleteFromOrder",
                                pwd: count
                            }
                        }).success(function (data) {
                            if (data.rescode == 0) {
                                nextDelete();
                            } else {
                                console.log(data.rescode + ':' + data.resmsg);
                                alert(data.rescode + ':' + data.resmsg);
                            }
                        });
                    break;
                    }
                }
            }
            if ((incalcstatus != "chosepay") && (incalcstatus != "refuseCount")) {
                if (!$('#clientForm').is(':visible') && !$('#tableForm').is(':visible') && !$('#shtrihSelectForm').is(':visible') && !$('#MaterialsForm').is(':visible') /*&&!$('#divideOrderForm').is(':visible')&&!$('#orderSelectReturnForm').is(':visible')*/ )
                    $("#glass").hide();

                if (incalcstatus != "front") {
                    $("#incalcdiv").hide();
                    if (incalcstatus != "clientPwd") {
                        itemFocus = 'shtrihOF';
                    }
                    if (incalcstatus == "pwdAddClient" || incalcstatus == "calcInSeekClient") {
                        itemFocus = '';
                    }
                    incalcstatus = "";
                    incalcshowed = 0;
                }
                $("#inpwdcalc").val("");

            }
        } else {
            maxi = 10;
            switch (incalcstatus) {
            case "count":
                maxi = 3;
                break;
            case "service":
                maxi = 3;
                break;
            case "shtrih":
                maxi = 11;
                break;
            case "chosepay":
                maxi = 20;
            }
            if ($("#inpwdcalc").val().length < maxi) {
                if ($("#inpwdcalc").val().indexOf('.') > -1 && v == '.') {
                    v = '';
                };
                if (incalcstatus == 'chosepay' && v == '.') {
                    v = '';
                };
                $("#inpwdcalc").val($("#inpwdcalc").val() + v);
                if ((v == '.') && ($("#inpwdcalc").val().length == 1) && (incalcstatus == 'countbtn' || incalcstatus == 'refuseCount' || incalcstatus == 'addcount' || incalcstatus == 'countbtnShtrih')) {
                    getWeigth();
                }
                //Удалить
                changeBalance();
            }
        }
    }
}

function formClick(self) {
    if (noshowcalc == 1) {
        noshowcalc = 0;
    } else {
        $("#chooseInterface").html("");
        $("#chooseInterface").hide();
        if (incalcshowed != 1) {
            getCalc("front", null, 'Введите пароль для входа', 'password');
        } else {
            //            stopCalc();
        }
    }
}








function selectGrid(status, data, value, clid) {
    selectgridstatus = status;
    centralizeElement("selectdiv");
    haveData = false;
    clientarray = undefined;
    clientarray = new Array();
//    tables = undefined;
//    tables = new Array();
    sales = undefined;
    sales = new Array();
    notes = undefined;
    notes = new Array();
    $('#addbtnNote').show();
    $('#okbtn').attr("onClick", "selectOk(clientOldGrid)");
    $('#cancelbtn').attr("onClick", "selectCancel('selectgrid')");
    doReturn = false;
    switch (status) {
    case 'client':
        {
            $.ajax({
                async: false,
                url: "/front/PHP/front.php?chooseClient",
                type: "POST",
                dataType: "json",
                data: {
                    actionScript: 'chooseTable',
                    event: 'Clients',
                    shtrih: value,
                    clid:clid
                }
            }).success(function (data) {
                if (data.rescode == 0) {

                    $('#addbtnNote').hide();
                    clients = data.rows;


                    for (k = 0; k < clients.length; k++) {
                        clientarray[k] = new Object();
                        clientarray[k].id = clients[k].clientid;
                        clientarray[k].discountid = clients[k].discountid;
                        clientarray[k].balance = clients[k].balance;
                        clientarray[k].name = clients[k].clientname;
                        clientarray[k].discountpercent = clients[k].discountpercent;
                        clientarray[k].servicepercent = clients[k].servicepercent;
                        clientarray[k].usediscountsincafe = clients[k].usediscountsincafe;
                        clientarray[k].usediscountsinfastfood = clients[k].usediscountsinfastfood;
                        clientarray[k].usegiftsincafe = clients[k].usegiftsincafe;
                        clientarray[k].usegiftsinfastfood = clients[k].usegiftsinfastfood;
                        clientarray[k].useserviceincafe = clients[k].useserviceincafe;
                        clientarray[k].useserviceinfastfood = clients[k].useserviceinfastfood;
                        clientarray[k].usegiftsinfastfood = clients[k].usegiftsinfastfood;
                        haveData = true;

                    }

                    tmparray = preperaVisibleArray('clients', clientarray);
                    clientOldGrid.fillTable(tmparray);
                } else {
                    console.log(data.rescode + ':' + data.resmsg);
                    alert(data.rescode + ':' + data.resmsg);
                };
            });
        }
        break;
    case 'table':
        
//          if (useLocation == 1) {
//                rooms = preLocArray;
//                for (k = 0; k < rooms.length; k++) {
//                    haveData = false;
//                }
//                $("#tableLocationtList").html("");
//                var locDiv = document.getElementById("tableLocationtList");
//                $.each(rooms, function (index, value) {
//                    if (rooms.length > 1) {
//                        locDiv.appendChild(createLocationTabs(value.id, value.name, "button grey p active"));
//                    }
//                });
//                
//                
////                tmparray = preperaVisibleArray('clients', rooms);
////                roomsgrid.fillTable(tmparray);
////                roomsgrid.selectRow(0);
//                
//                selectTablesFromRoom();
//                showForm("tableForm");
//                doReturn = true;
//          }else if (useLocation == 0) {
//                    tables = preTableArray;
//                    for (k = 0; k < tables.length; k++) {
//                        haveData = false;
//                    }
////                    tmparray = preperaVisibleArray('clients', tables);
////                    tablesgrid.fillTable(tmparray);
//                      $("#tableObjectsList").html("");
//                      selectTablesWithOutRoom();  
//                      showForm("tableForm");
//                      doReturn = true;
//                };
          
//        $.ajax({
//            async: false,
//            url: "/front/PHP/front.php",
//            type: "POST",
//            dataType: "json",
//            data: {
//                actionScript: 'chooseTable',
//                event: 'Tables'
//            }
//        }).success(function (data) {
//            if (data.rescode == 0) {
//                if (data.location == 1) {
//                    rooms = data.rows;
//                    for (k = 0; k < rooms.length; k++) {
//                        haveData = false;
//                    }
//                    tmparray = preperaVisibleArray('clients', rooms);
//                    roomsgrid.fillTable(tmparray);
//                    roomsgrid.selectRow(0);
//                    selectTablesFromRoom();
//                    showForm("tableForm");
//                    //                            centralizeElement("tableForm");
//                    //                            document.getElementById("glass").style.display = "block";
//                    //                            $('#tableForm').css("opacity","1");
//                    //                            document.getElementById("tableForm").style.display = "block";
//                    doReturn = true;
//                } else if (data.location == 0) {
//                    tables = data.rows;
//                    for (k = 0; k < tables.length; k++) {
//                        haveData = true;
//                    }
//                    tmparray = preperaVisibleArray('clients', tables);
//                    tablesgrid.fillTable(tmparray);
//                };
//            } else {
//                console.log(data.rescode + ':' + data.resmsg);
//                alert(data.rescode + ':' + data.resmsg);
//            };
//        });
        if (doReturn) {
            return;
        }
        break;
    case 'sale':
        $.ajax({
            async: false,
            url: "/front/PHP/front.php?chooseSale",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: 'chooseTable',
                event: 'Discount'
            }
        }).success(function (data) {
            if (data.rescode == 0) {
                sales = data.rows;
                for (k = 0; k < sales.length; k++) {
                    haveData = true;
                }
                tmparray = preperaVisibleArray('clients', sales);
                discountsgrid.fillTable(tmparray);
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            };
        });
        break;
    case 'note':
        {
            $.ajax({
                async: false,
                url: "/front/PHP/front.php?chooseNote",
                type: "POST",
                dataType: "json",
                data: {
                    actionScript: 'chooseTable',
                    event: 'note',
                    itemid: ordertabledata[ordergrid.getNum()].id
                }
            }).success(function (data) {
                if (data.rescode == 0) {
                    notes = data.rows;
                    for (k = 0; k < notes.length; k++) {
                        haveData = true;
                    }
                    tmparray = preperaVisibleArray('clients', notes);
                    notesgrid.fillTable(tmparray);
                } else {
                    console.log(data.rescode + ':' + data.resmsg);
                    alert(data.rescode + ':' + data.resmsg);
                };
            });
            break;
        }
    }
    if (!haveData) {
        selectCancel('selectgrid');
    } else {
        if (status == 'client') {
            if (clients.length == 1) {
                selectedrow = 0;
                updateOrderdata(clientOldGrid);
                selectCancel('selectgrid');
                return 1;
            } else {
                document.getElementById("glass").style.display = "block";
                $('#selectdiv').css("opacity", "1");
                document.getElementById("selectdiv").style.display = "block";
            }
        } else {
            document.getElementById("glass").style.display = "block";
            $('#selectdiv').css("opacity", "1");
            document.getElementById("selectdiv").style.display = "block";
        }
    }
}


function updateOrderdata(grid) {
    if (selectgridstatus != "") {
        switch (selectgridstatus) {
        case 'client':
            rownum = grid.getNum();
            orderdata.clientid = clients[rownum].clientid;
            orderdata.clientname = clients[rownum].clientname;
            //                orderdata.usediscountsincafe=clients[rownum].usediscountsincafe;
            //                orderdata.usediscountsinfastfood=clients[rownum].usediscountsinfastfood;
            orderdata.discountid = clients[rownum].discountid;
            orderdata.discountpercent = 0;

            $('#giftbtn').css("display", "none");
            $('#giftbtn').removeAttr("onClick");

            if ((numcurrentinterface == 4 && parseInt(clients[rownum].usegiftsinfastfood) == 1)) {
                if (clients[rownum].balance > 0) {
                    $('#giftbtn').css("display", "block");
                    $('#giftbtn').attr("onClick", "getGifts('" + orderdata.clientid + "')");
                } else if (clients[rownum].balance == 0) {
                    $('#giftbtn').css("display", "none");
                    $('#giftbtn').removeAttr("onClick");
                }
            };


            if (numcurrentinterface == 3 && parseInt(clients[rownum].usediscountsincafe) == 1) {
                orderdata.discountpercent = clients[rownum].discountpercent;
            };
            if ((numcurrentinterface == 4 && parseInt(clients[rownum].usediscountsinfastfood) == 1)) {
                orderdata.discountpercent = clients[rownum].discountpercent;
            };

            if (numcurrentinterface == 3 && parseInt(clients[rownum].usediscountsincafe) == -1) {
                orderdata.discountpercent = 0;
            };
            if ((numcurrentinterface == 4 && parseInt(clients[rownum].usediscountsinfastfood) == -1)) {
                orderdata.discountpercent = 0;
            };


            if (numcurrentinterface == 3 && parseInt(clients[rownum].useserviceincafe) == 1) {
                if (clients[rownum].servicepercent != -1) {
                    orderdata.servicepercent = parseInt(clients[rownum].servicepercent);
                } else if (parseInt(clients[rownum].servicepercent) == -1) {
                    orderdata.servicepercent = configArray.defaultservicepercent;
                }
            } else if (numcurrentinterface == 3 && parseInt(clients[rownum].useserviceincafe) == 0) {
                orderdata.servicepercent = 0;
            } else if (numcurrentinterface == 3 && parseInt(clients[rownum].useserviceincafe) == -1) {
                orderdata.servicepercent = configArray.defaultservicepercent;;
            };

            if (numcurrentinterface == 4 && parseInt(clients[rownum].useserviceinfastfood) == 1) {
                if (clients[rownum].servicepercent != -1) {
                    orderdata.servicepercent = parseInt(clients[rownum].servicepercent);
                } else if (parseInt(clients[rownum].servicepercent) == -1) {
                    orderdata.servicepercent = configArray.defaultservicepercent;
                }
            } else if (numcurrentinterface == 4 && parseInt(clients[rownum].useserviceinfastfood) == 0) {
                orderdata.servicepercent = 0;
            } else if (numcurrentinterface == 4 && parseInt(clients[rownum].useserviceinfastfood) == -1) {
                orderdata.servicepercent = configArray.defaultservicepercent;;
            };


            columnSum();
            document.getElementById('salediv').innerHTML = orderdata.discountpercent + '%';
            $("#clientdiv").val(orderdata.clientname);
            document.getElementById('servicediv').innerHTML = orderdata.servicepercent + '%';
            modyfied = 1;
            break;
        case 'table':
            rownum = tablesgrid.getNum();
            orderdata.tableid = tables[rownum].id;
            orderdata.tablename = tables[rownum].name;
            if (tables[rownum].servicepercent != -1) {
                orderdata.servicepercent = parseInt(tables[rownum].servicepercent);
                document.getElementById('servicediv').innerHTML = orderdata.servicepercent + '%';
            }
            document.getElementById('tablediv').innerHTML = orderdata.tablename;
            modyfied = 1;
            break;
        case 'sale':
            rownum = discountsgrid.getNum();
            orderdata.saleid = sales[rownum].id;
            orderdata.sale = sales[rownum].name;
            document.getElementById('salediv').innerHTML = orderdata.sale + "%";
            modyfied = 1;
            break;
        case 'note':
            rownum = notesgrid.getNum();
            rownum2 = ordergrid.getNum();
            ordertabledata[rownum2].note = notes[rownum].name;
            ordertabledata[rownum2].name = ordertabledata[rownum2].name + ' (' + notes[rownum].name + ')';
            tmparray = preperaVisibleArray('ordertable', ordertabledata);
            ordergrid.fillTable(tmparray);
            break;
        }
    }
}

function selectOk(grid) {
    updateOrderdata(grid);
    selectedrow = tempselectedrow;
    selectCancel(grid);
}


function selectCancel(grid) {
    selectgridstatus = "";
    $('#clientdiv').val(orderdata.clientname);
    document.getElementById("glass").style.display = "none";
    $('#selectdiv').stop().animate({
        "opacity": "0"
    }, "fast");
    document.getElementById("selectdiv").style.display = "none";

    tempselectedrow = -1;

}

function showMenu(menustore) {
    $('#menudiv').html("");
    var menudiv = document.getElementById("menudiv");
    for (i = 0; i < simplemenu.length; i++) {
        if (simplemenu[i].parentid == 0) {
            if (simplemenu[i].isgroup == 1) {
                menudiv.appendChild(createButton(simplemenu[i].id, simplemenu[i].name, "button grey p active"));
            } else {
                menudiv.appendChild(createButton(simplemenu[i].id, simplemenu[i].name, "button white font-black p active", simplemenu[i].price));
            }
        }
    }
}

function createrMenu() {
    var menudiv = document.getElementById("menudiv");
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?loadMenu",
        type: "POST",
        dataType: 'json',
        data: {
            actionScript: 'loadMenu'
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            simplemenu = data.simplemenu;
            protoSimpleMenu=simplemenu;
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        };
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
    } else {
        newBtn.innerHTML = "<p>" + inner + "</p>";
        if (configArray.askCount == 0) {
            newBtn.setAttribute("onClick", "menuBtnClick(id,1)");
        } else {
            newBtn.setAttribute("onClick", "if (simplemenu[findButtonPos(id)].complex==0){getCalc('countbtn',id,'Введите количество',null)}else{menuBtnClick(id,1);}");
        }
        var newDiv = document.createElement("div");
        newDiv.setAttribute("class", "pricecell div red");
        newDiv.innerHTML = Math.round(price);
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
    } else {
        id = findParent(pid);
        menudiv.appendChild(createButton(pid, simplemenu[findButtonPos(pid)].name, "button blue p active"));
        return id;
    }
}



function menuBtnClick(idMenu, c) {
    var count;
    c > 0 ? count = c : count = 1;
    count = parseFloat(count);
    var button = document.getElementById(idMenu);
    var menudiv = document.getElementById("menudiv");
    if (idMenu != 0) {
        if (simplemenu[findButtonPos(idMenu)].isgroup == 0) {
            rowcount = ordergrid.getRowCount();
            if (configArray.blockZeroSale == 1 && simplemenu[findButtonPos(idMenu)].price == 0) {
                alert('Нельзя продавать товар по нулевым ценам!');
                return;
            }
            if (simplemenu[findButtonPos(idMenu)].complex == 0) {
                ordertabledata[rowcount] = new Object();
                ordertabledata[rowcount].visibleid = rowcount + 1;
                ordertabledata[rowcount].id = simplemenu[findButtonPos(idMenu)].itemid;
                ordertabledata[rowcount].name = '+ ' + simplemenu[findButtonPos(idMenu)].name;
                ordertabledata[rowcount].price = simplemenu[findButtonPos(idMenu)].price;
                ordertabledata[rowcount].count = count;
                ordertabledata[rowcount].printer = simplemenu[findButtonPos(idMenu)].printer;
                ordertabledata[rowcount].summa = Math.round((count * simplemenu[findButtonPos(idMenu)].price) * 100) / 100;
                ordertabledata[rowcount].note = '';
                ordertabledata[rowcount].status = 'new';
                ordertabledata[rowcount].isservice = simplemenu[findButtonPos(idMenu)].isservice;
                tmprow = preperaVisibleRow('ordertable', ordertabledata[rowcount])
                ordergrid.addRow(tmprow);
                if (configArray.useBuyerDisplay==1){
                    displStr='<Header><order>display</order><itemName>'+simplemenu[findButtonPos(idMenu)].name+'</itemName><price>'+ordertabledata[rowcount].price+'</price><count>'+ordertabledata[rowcount].count+
                    '</count><summa>'+ordertabledata[rowcount].summa+'</summa></Header>';
                    $.ajax({
                        type: "POST",
                        url: "http://localhost:12345", 
                        data: displStr,
                        dataType: "script"
                    });
                }
                columnSum();
                modyfiedt = 1;
                if (configArray.alwaysUseNote==1){
                    selectGrid('note', null, '','');
                }
            } else {
                tmpIdBtn = idMenu;
                doComplex(simplemenu[findButtonPos(idMenu)].itemid);
            }
            itemFocus="shtrihOF";
            if ($('#searchField').val().length>0){
                $('#resetSearch').mousedown(); 
            }
        } else {  
            menudiv.innerHTML = "";
            menudiv.appendChild(createButton(0, "Все", "button blue p active"));
            findParent(idMenu);
            menudiv.appendChild(createButton(idMenu, simplemenu[findButtonPos(idMenu)].name, "button blue p active"));
            for (i = 0; i < simplemenu.length; i++) {
                if (simplemenu[i].parentid == idMenu) {
                    if (simplemenu[i].isgroup == 1) {
                        menudiv.appendChild(createButton(simplemenu[i].id, simplemenu[i].name, "button grey p active"));
                    } else {
                        menudiv.appendChild(createButton(simplemenu[i].id, simplemenu[i].name, "button white font-black p active", simplemenu[i].price));
                    }
                }
            }
        }
    } else {
        menudiv.innerHTML = "";
        for (i = 0; i < simplemenu.length; i++) {
            if (simplemenu[i].parentid == 0) {
                if (simplemenu[i].isgroup == 1) {
                    menudiv.appendChild(createButton(simplemenu[i].id, simplemenu[i].name, "button grey p active"));
                } else {
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
        } else {
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

function repeatItem(quantity) {
    quantity = parseFloat(quantity);
    if (quantity < 0) {
        alert('Количество не может быть меньше нуля');
        return;
    };
    rownum = ordergrid.getNum();
    if (ordertabledata[rownum].status == "old") {
        tmprow = preperaVisibleRow('ordertable', ordertabledata[rownum]);
        temptabledata = ordertabledata;
        rowcount = ordergrid.getRowCount();
        ordertabledata[rowcount] = new Object();
        ordertabledata[rowcount].visibleid = rowcount + 1;
        ordertabledata[rowcount].name = temptabledata[rownum].name;; //name temptabledata[selectedrow + 1].name;
        ordertabledata[rowcount].price = temptabledata[rownum].price;; //price temptabledata[selectedrow + 1].price;
        ordertabledata[rowcount].count = parseFloat(quantity); //count parseFloat(quantity);
        ordertabledata[rowcount].summa = parseFloat(quantity) * temptabledata[rownum].price; //summa parseFloat(quantity) * temptabledata[selectedrow + 1].price;
        ordertabledata[rowcount].printer = temptabledata[rownum].printer; //printer temptabledata[selectedrow + 1].printer;
        ordertabledata[rowcount].note = ''; //note
        ordertabledata[rowcount].status = 'new'; //status
        ordertabledata[rowcount].id = temptabledata[rownum].itemid; //
        tmprow = preperaVisibleArray('ordertable', ordertabledata);
        ordergrid.fillTable(tmprow);
        //            mytableAddrow(ordertabledata[rowcount],'ordertable');
        columnSum();
        modyfiedt = 1;

    }
    if (ordertabledata[rownum].status == "new") {
        ordertabledata[rownum].count = parseFloat(ordertabledata[rownum].count) + parseFloat(quantity);
        ordertabledata[rownum].summa = ordertabledata[rownum].count * ordertabledata[rownum].price;
        tmparray = preperaVisibleArray('ordertable', ordertabledata);
        ordergrid.fillTable(tmparray);
        columnSum();
        modyfiedt = 1;
    }
    columnSum();
}

function columnSum() {
    var sum = 0;
    var servicesum = 0;
    var discountsum = 0;
    for (i = 0; i < ordergrid.getRowCount(); i++) {
        //        sum += parseFloat(ordertabledata[i].summa); 
        sum2 = 0;
        service = 0;
        discount = 0;
        if (configArray.typeOfDiscountService == 0) {
            service = (Math.round(parseFloat(ordertabledata[i].summa) * (orderdata.servicepercent)) / 100);
            discount = (Math.round(parseFloat(ordertabledata[i].summa) * (orderdata.discountpercent)) / 100);

        } else if (configArray.typeOfDiscountService == 1) {
            service = (Math.round(parseFloat(ordertabledata[i].summa) * (orderdata.servicepercent)) / 100);
            sum2 = parseFloat(ordertabledata[i].summa) + service;
            discount = (Math.round(sum2 * (orderdata.discountpercent)) / 100);
        } else if (configArray.typeOfDiscountService == 2) {
            discount = (Math.round(parseFloat(ordertabledata[i].summa) * (orderdata.discountpercent)) / 100);
            sum2 = parseFloat(ordertabledata[i].summa) - discount;
            service = (Math.round(sum2 * (orderdata.servicepercent)) / 100);
        }
        discountsum += discount;
        servicesum += service;
        discountsum = Math.round(discountsum * 100) / 100;
        servicesum = Math.round(servicesum * 100) / 100;
        sum += parseFloat(ordertabledata[i].summa) + service - discount;
        sum = Math.round(sum * 100) / 100;
    }

    orderdata.servicesum = servicesum;
    orderdata.discountsum = discountsum;
    orderdata.totalsum = sum;
    document.getElementById("printdiv").innerHTML = orderdata.totalsum;

}
 
function deleteRow(pwd) {
    //Removerow
    $('#shtrihOF').val("");
    if ((ordertabledata[ordergrid.getNum()].status) == 'old') {
        getCalc('refusePwd', null, 'Введите пароль на отказ', 'password');
    } else {
        if (configArray.pwdDeleteFromOrder==1){
            getCalc('pwdDeleteFromOrder', null, 'Введите пароль', 'password');
        }else{
           nextDelete(); 
        }
    }
    columnSum();
}

function nextDelete(){
        ordertabledata.splice((ordergrid.getNum()), 1);
        for (i = 0; i < ordertabledata.length; i++) {
            ordertabledata[i].visibleid = i + 1;
        };
        if (orowcount == 1) {
            modyfiedt = 0;
            modyfied = 0;
        }
        tmparray = preperaVisibleArray('ordertable', ordertabledata);
        ordergrid.fillTable(tmparray);
        columnSum();
}

function checkRefuse(count, pwd) {
    refusePwd = pwd;
    //document.getElementById("printdiv").innerHTML = 0;
    if (parseFloat(count) > 1) {
        getCalc("refuseCount", null, 'Введите количество отказываемых позиций', null);
    } else {
        doRefuse(parseFloat(count), pwd);
    }
}

function doRefuse(c, pwd) {
    c = parseFloat(c);
    incalcstatus = '';
    stopCalc();
    rownum = ordergrid.getNum();
    refuseordertabledata[0] = new Object();
    refuseordertabledata[0].id = ordertabledata[rownum].itemid;
    refuseordertabledata[0].name = ordertabledata[rownum].name;
    refuseordertabledata[0].price = ordertabledata[rownum].price;
    refuseordertabledata[0].count = c;
    refuseordertabledata[0].printer = ordertabledata[rownum].printer;
    refuseordertabledata[0].summa = refuseordertabledata[0].count * refuseordertabledata[0].price;
    refuseordertabledata[0].note = '';
    refuseordertabledata[0].status = 'old';
    refuseordertabledata[0].innerId = ordertabledata[rownum].innerId;
    columnSum();
    $.ajax({
        async: false,
        url: "PHP/front.php?refuse",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'refuse',
            action: 'refuse',
            orderid: orderdata.orderid,
            orderRefuse: refuseordertabledata[0],
            pwdRefuse: pwd,
            count: c
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            printTCP(data.xml);
            afterRefuseTableUpdate(c, 'ordertable');
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        };
    });
}

function afterRefuseTableUpdate(c, grid) {
    rownum = ordergrid.getNum();
    if (ordertabledata[rownum].status == "old" && (parseFloat(ordertabledata[rownum].count) - parseFloat(c) + 1) > 0) {
        ordertabledata[rownum].count = parseFloat(ordertabledata[rownum].count) - c;
        ordertabledata[rownum].summa = parseFloat(ordertabledata[rownum].count) * ordertabledata[rownum].price;
        tmparray = preperaVisibleArray('ordertable', ordertabledata);
        ordergrid.fillTable(tmparray);
    } else {
        refuseordertabledata[0] = ordertabledata[rownum];
        ordertabledata.splice((rownum), 1);
        for (i = 0; i < ordertabledata.length; i++) {
            ordertabledata[i].visibleid = i + 1;
        };
        if (orowcount == 1) {
            modyfiedt = 0;
            modyfied = 0;
        }
        tmparray = preperaVisibleArray('ordertable', ordertabledata);
        ordergrid.fillTable(tmparray);
    }
    columnSum();
}

function checkChange() {
    if (parseInt(closeChange) == 0) {
        $('#btnchangeOpen').attr("disabled", "disabled");
        $('#btnchangeOpen').html("Смена открыта!");
        $('#btnchangeClose').html("Закрыть смену");
        $('#btnchangeClose').removeAttr("disabled");
    } else if (parseInt(closeChange) == 1) {
        $('#btnchangeClose').attr("disabled", "disabled");
        $('#btnchangeClose').html("Смена закрыта!");
        $('#btnchangeOpen').html("Открыть смену");
        $('#btnchangeOpen').removeAttr("disabled");
    }
}

function clsChng(flag){
    $.ajax({
            async: false,
            url: "PHP/front.php?changeOpenClose",
            type: "POST", 
            dataType: "json",
            data: {
                actionScript: 'changeOpenClose',
                action: flag
            }
        }).success(function (data) {
            if (data.rescode == 0) {
                closeChange = data.closeChange;
                checkChange();
                refreshGridCashier(currentCountCashPages);
                configArray.idchange=data.idchange;
                $("#changeInfo").html('Торговая точка: ' + data.infoChange.apname + '; Смена: ' + data.infoChange.name + ' от ' + data.infoChange.dtopen + ' _ ' + data.infoChange.dtclosed);
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }; 
        });
}

function canCloseChange(){
    res=false;
    $.ajax({
            async: false,
            url: "PHP/front.php?canCloseChange",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: 'canCloseChange'
            }
        }).success(function (data) {
            if (data.rescode == 0) {
                if (data.canClose==0){
                    res=false;
                }else if (data.canClose==1){
                    res=true;
                }
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            };
        });
 return res;       
}

function changeOpenClose(flag, eventMsg) {
    $("#msg").html(eventMsg)
    $("#msg").dialog({
        resizable: false,
        height: 180,
        modal: true,
        buttons: {
            "Да": function () {
                if (flag=='close'){
                    if (configArray.zreportonclose==1&&canCloseChange()){
                            $.ajax({
                                type: "POST",
                                url: "http://localhost:12345",
                                data: 'closeChange',
                                dataType: "script"
                            });
                    }else if (canCloseChange()){
                        clsChng(flag);
                    }else{
                        alert('Нельзя закрыть смену!');
                    }
                }else if (flag=='open'){
                        clsChng(flag);
                }
                $(this).dialog("close");
            },
            "Нет": function () {
                $(this).dialog("close");
            }
        }
    });
}


function changeFilterClick(event, c) {
    cashgrid.clearTable();
    $.ajax({
        async: false,
        url: "PHP/front.php?ShowOrders",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'showOrders',
            action: 'cashierOrders',
            filter: event,
            pageCount: c
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            cashierbillstore = data.rows;
            bills = data.rows;
            $('#pageNumCPag').html('');
            $('#pageNumCPag').html(data.pageNum);
            $('#pageNumCPag').val(data.pn);
            maxPageCountCash = data.pageCount;
            $('#labelPag').html('');
            $('#labelPag').html(' из ' + data.pageCount);
            if (bills != null) {
                tmparray = preperaVisibleArray('kassir', bills);
                cashgrid.fillTable(tmparray);
                cashgrid.selectRow(0);
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        };

    });
}


function refreshGridCashier(c) {
    cashgrid.clearTable();
    currentCashFilter = 'nopay';
    $.ajax({
        async: false,
        url: "PHP/front.php?RefreshGridCashier",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'showOrders',
            action: 'cashierOrders',
            filter: 'nopay',
            pageCount: c
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            cashierbillstore = data.rows;
            bills = data.rows;
            $('#pageNumCPag').html('');
            $('#pageNumCPag').html(data.pageNum);
            $('#pageNumCPag').val(data.pn);
            maxPageCountCash = data.pageCount;
            $('#labelPag').html('');
            $('#labelPag').html(' из ' + data.pageCount);
            if (bills != null) {
                tmparray = preperaVisibleArray('kassir', bills);
                cashgrid.fillTable(tmparray);
                cashgrid.selectRow(0);
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        };

    });
}

function refreshGridOfficiant() {
    offgrid.clearTable();
    $.ajax({
        async: false,
        url: "PHP/front.php?refreshGridOff",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'showOrders',
            action: 'officiantOrders',
            employeeid: employeeid
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            officiantbillstore = data.rows;
            bills = data.rows;
            if (bills != null) {
                tmparray = preperaVisibleArray('officiant', bills);
                //                            fillMytable(visibleArray,"billsorder");
                offgrid.fillTable(tmparray);
                offgrid.selectRow(0);
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        };

    });

    //Заполнить массив currentbills=data положитьв  перменную crowcount кол-во счетов
}

function payCashier() {
    showOrder("pay");
    if (orderdata.printed == 1) {
        if (orderdata.closed == 0) {
            oplata = 0;
            getCalc('chosepay', null, 'Введите сумму от клиента', null);
        }
    } else Msg("Незаблокированный счет не может быть оплачен!")
}

//Интерфейс касира -END

//Часики
function getTime() {
    $.ajax({
        async: true,
        url: "/front/PHP/front.php?getTime",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'getTime'
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            $("#offtime").html(data.time);
            $("#cashierClock").html(data.time);
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        };

    });
}
$(document).ready(function () {

    // определяем массивы имен для месяцев и дней недели
    var monthNames = ["Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря"];
    var dayNames = ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"]

    // создаем новый объект для хранения даты
    var newDate = new Date();

    // извлекаем текущую дату в новый объект
    newDate.setDate(newDate.getDate());

    // выводим день число месяц и год
    $('#Date').html(dayNames[newDate.getDay()] + " " + newDate.getDate() + ' ' + monthNames[newDate.getMonth()] + ' ' + newDate.getFullYear());

    setInterval(function () {

        if (itemFocus != '') {
            $('#' + itemFocus + '').focus()
        }
  
    }, 1000);
   
    setInterval(function () {
            if (numcurrentinterface == 2) {
                if (!$('#optionsForm').is(':visible')){
                    changeFilterClick(currentCashFilter ,currentCountCashPages);
                }
//                refreshGridCashier(currentCountCashPages)    
            }
    }, 15000);
    
});
//Доп функции


function addlog(str, st) {
    if (st == "clr") {
        console.clear();
    }
    console.log(str);
}


//-------------------

function checkUnblockingOrder() {
    rownum = cashgrid.getNum();
    if (rownum >= 0) {
        findBillParams(cashierbillstore, cashierbillstore[rownum].id);
        if ((result.printed == 1) && (result.closed == 0)) {
            getCalc('unblockOrder', null, 'Введите парольна разблокировку счета', 'password')
        } else {
            alert('Нельзя разблокировать оплаченный или не заблокированный счет!');
        }
    }
}

function unlockBtn(count) {
    orderid = cashierbillstore[cashgrid.getNum()].id;
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?unblockOrder",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'unblockOrder',
            orderid: orderid,
            pwd: count
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            refreshGridCashier(currentCountCashPages);
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        };
    });

}

function createrPayTypes() {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?LoadPayType",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'loadTypePay'
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            p = data.typePay;
            s = '';
            for (k = 0; k < p.length; k++) {
                paymentarray[k] = new Object();
                paymentarray[k].id = p[k].id;
                paymentarray[k].name = p[k].name;
                s += '<option value="' + paymentarray[k].id + '">' + paymentarray[k].name + '</option>'
            }
            document.getElementById('typepaylist').innerHTML = s;
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        };

    });
}


function getConfigInf() {
    configArray.defaultservicepercent = 0;
    configArray.printsuborderintfastfood = 0;
    configArray.serviceinfastfood = 0;
    configArray.zreportonclose = 0;
    configArray.defaultclientname = 'Частное лицо';
    configArray.defaultclientid = 1;
    configArray.usechoosetable = 0;
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?loadConf",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'loadConf'
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            configArray.defaultservicepercent = data.config.defaultservicepercent;
            configArray.printsuborderintfastfood = data.config.printsuborderintfastfood;
            configArray.serviceinfastfood = data.config.serviceinfastfood;
            configArray.zreportonclose = data.config.zreportonclose;
            configArray.defaultclientname = data.config.defaultclientname;
            configArray.defaultclientid = data.config.defaultclientid;
            configArray.apname = data.config.apname;
            configArray.wpname = data.workplace.wpname;
            configArray.usechoosetable = data.config.usechoosetable;
            configArray.askCount = data.config.askCount;
            configArray.useChangePrice = data.config.useChangePrice;
            configArray.useURV = data.config.useURV;
            configArray.typeOfDiscountService = data.config.typeOfDiscountService;
            configArray.waiterCanTakePayment = data.config.waiterCanTakePayment;
            configArray.materialsSumMoreServiceSum = data.config.materialsSumMoreServiceSum;
            configArray.blockZeroSale = data.config.blockZeroSale;
            configArray.searchInMenu=data.config.searchInMenu;
            configArray.pwdDeleteFromOrder=data.config.pwdDeleteFromOrder;
            configArray.rememberAboutDiscount=data.config.rememberAboutDiscount;
            configArray.useBuyerDisplay=data.config.useBuyerDisplay;
            configArray.switchOffCompAfterClose=data.config.switchOffCompAfterClose;
            configArray.cashid=data.config.cashid;
            configArray.slipid=data.config.slipid;
            configArray.useFR=data.config.useFR;
            configArray.alwaysUseNote=data.config.alwaysUseNote;
            configArray.warehouseid=data.config.warehouseid;
            configArray.idchange=data.idchange;
            configArray.noSaldoButton=data.config.noSaldoButton;
            configArray.useLocation=data.config.useLocation;
            
//            protoUseLocation=configArray.useLocation; 
            
            $('#remainSee').show(); 
            if (configArray.warehouseid<1){
                $('#remainSee').hide();
            }
            
            $('#reportXreport').show(); 
            if (configArray.useFR==0){
                $('#reportXreport').hide();
            }
            
            if (configArray.searchInMenu==1){
                $('#searchDiv').css("display","block");
            }
            if (configArray.useURV == 0) {
                $('#urvbtn').hide();
            } else if (configArray.useURV == 1) {
                $('#urvbtn').show();
            }
            
            $('#noSaldoBtn').show();
            if (configArray.noSaldoButton == 0) {
                $('#noSaldoBtn').show();
            } else if (configArray.noSaldoButton == 1) {
                $('#noSaldoBtn').hide();
            }
            
            infstr = "Торговая точка: " + configArray.apname;
            if (configArray.wpname != '') {
                infstr = infstr + "<br> Рабочее место: " + configArray.wpname;
            }
            $("#apinfo").html(infstr);
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        };

    });
}


function findBillId(store, id) {
    result = 0;
    result = store[selectedrow + 1].id;
    return result;
}

function findBillParams(store, id) {
    result = new Array();
    for (i = 0; i < store.length; i++) {
        if (store[i].id == id) {
            result.printed = store[i].printed;
            result.closed = store[i].closed;
        }
    };
    return result;
}

function printXZreport(report) {
//    data = '<Header>';
//    data += '<order>' + report + '</order>';
//    data += '</Header>';
    //    console.log(data);
//    $.ajax({
//        type: "POST",
//        url: "http://localhost:12345",
//        data: data,
//        dataType: "script"
//    })
    
//    $.ajax({
//       async:false,
//       type: "POST",
//       url: "http://localhost:12345",
//       data: 'xReport',
//       dataType: "script"
//   }).complete(function (){
//       res=1;
//   });
//     res=0;
//     $.ajax({
//       async:false,
//       type: "POST",
//       url: "http://localhost:12345",
//       data: 'xReport',
//       dataType: "script",
//       success: function (response) {              
//           alert("Details saved successfully!!!");      
//       },
//       error: function (xhr, ajaxOptions, thrownError) {        
//           alert(xhr.status);        alert(thrownError);     
//       }
//   });

    res=0;
         $.ajax({
            async:false,
            type: "POST",
            url: "http://localhost:12345",
            data: 'xReport',
            dataType: "script"
//            ,
//            success: function(response) {  
//               alert('ok');
//            },
//            error: function(){
//                alert('fail');
//            },
//            complete: function(){
//                alert('1');
//            }
        });
   
}

function logoutExit(event) {
    switch (event) {
    case 'exit':
        {
            /* res=useOtherPHP("onExitCheck",0,count);
                if (res==0){
                    Msg('Неверный пароль!');
                }else if (res==1){
                     window.open('', '_self', '');
                     window.close();
                } */
            $("#msg").html('Вы действительно хотите закрыть программу?')
            $("#msg").dialog({
                resizable: false,
                height: 180,
                modal: true,
                buttons: {
                    "Да": function () {
                        dopwdclose('');
                        $(this).dialog("close");
                    },
                    "Нет": function () {
                        $(this).dialog("close");
                    }
                }
            });

            break;
        }
    case 'logout':
        {
            /* res=useOtherPHP("onExitCheck",0,count);
                if (res==0){
                    Msg('Неверный пароль!');
                }else if (res==1){
                     window.location='/login.php?do=logout' 
                } */
            $("#msg").html('Вы действительно хотите выйте из торговой точки?')
            $("#msg").dialog({
                resizable: false,
                height: 180,
                modal: true,
                buttons: {
                    "Да": function () {
                        //window.location='/login.php?do=logout';
                        $(this).dialog("close");
                        dopwdexit('');

                    },
                    "Нет": function () {
                        $(this).dialog("close");
                    }
                }
            });

            break;
        }
    }
}

function dopwdclose(pwd) {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?doPwdClose",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "dopwdclose",
            pwd: pwd
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            if (data.usepassword == 1) {
                getCalc("pwdclose", null, 'Введите пароль на закрытие', 'password');
            } else if (data.usepassword == 0) {
                if (configArray.switchOffCompAfterClose==1){
                    printTCP('<Header><order>switchOff</order></Header>');
                }
                window.open('', '_self', '');
                window.close();
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}


function dopwdexit(pwd) {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?doPwdExit",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "dopwdexit",
            pwd: pwd
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            if (data.usepassword == 1) {
                getCalc("pwdexit", null, 'Введите пароль на выход', 'password');
            } else if (data.usepassword == 0) {
                window.location = '/login.php?do=logout';
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function selectOkGift() {
    if (giftSelectedItems.getRowCount() > 0) {
        giftRow = giftSelectedItemsarray;
        $.ajax({
            async: false,
            url: "/front/PHP/front.php?giftSave",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: 'giftSave',
                orderid: giftOrderId,
                ordertable: giftRow,
                balans: giftBalans
            }
        }).success(function (data) {
            if (data.rescode == 0) {
                if (data.xmlSubOrder != '') {
                    printTCP(data.xmlSubOrder);
                }
                if (data.xmlOrder != '') {
                    printTCP(data.xmlOrder);
                }
                giftOrderId = 0;
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            };
        });
        selectCancelGift();
    } else {
        Msg('Не выбран подарок!');
    }
}

function selectCancelGift() {
    selectgridstatus = "";
    document.getElementById("glass").style.display = "none";
    $('#selectGiftForm').stop().animate({
        "opacity": "0"
    }, "fast");
    document.getElementById("selectGiftForm").style.display = "none";

    giftItemGrid.clearTable();
    giftLevelGrid.clearTable();
    giftSelectedItems.clearTable();

    giftarray = undefined;
    giftarray = new Array();
    giftItemsarray = undefined;
    giftItemsarray = new Array();
    giftSelectedItemsarray = undefined;
    giftSelectedItemsarray = new Array();
}


function killSession() {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?employeeSessionDie",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'employeeSessionDie'
        }
    }).success(function (data) {
        if (data.rescode == 0) {} else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function createSession(value) {
    var res;
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?employeeSessionCreate",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'employeeSessionCreate',
            interface: value
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            //            empInf[id].interfaces=data.interface;
            res = data.interface;
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
            //           empInf[id].interfaces=0;
            res = -1;
        }
    });
    return res;
}

function checkSessionTimer() {
    if (numcurrentinterface > 0) {
        setLastAction();
        var timer = setTimeout(checkSessionTimer, 60000);
    } else {
        clearTimeout(timer);
    }
}

function setLastAction() {
    $.ajax({
        url: "/front/PHP/front.php?lastAction",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "lastAction"
        }
    }).success(function (data) {
        if (data.rescode == 0) {} else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function selectGridGifts(data) {
    if (data != 0) {
        for (k = 0; k < data.length; k++) {
            giftarray[k] = new Object();
            giftarray[k].id = data[k].id;
            giftarray[k].name = data[k].levelname;
            giftarray[k].totalpoints = data[k].totalpoints;
            haveData = true;
        }
        tmparray = preperaVisibleArray('gift', giftarray);

        giftLevelGrid.fillTable(tmparray);

        $('#okbtnGift').attr("onClick", "selectOkGift()");
        $('#cancelbtnGift').attr("onClick", "selectCancelGift()");
        $('#btnSelectLevelGift').attr("onClick", "getGiftItems()");
    }
    showForm("selectGiftForm");
}

function getGifts(clientid, data, type) {
    if (type == 'count') {
        giftOrderId = 0;
        visileGiftBalans = giftBalans;
        gift = data.giftlevel;
        $('#userPoints').html();
        if (gift != 0) {
            selectGridGifts(data);
        };
        fillBillHeader();
    } else {
        $.ajax({
            url: "/front/PHP/front.php?getGift",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: "getGift",
                clientid: clientid
            }
        }).success(function (data) {
            if (data.rescode == 0) {
                giftOrderId = 0;
                giftBalans = data.balans;
                visileGiftBalans = giftBalans;
                gift = data.giftlevel;
                if (giftBalans > 0) {
                    $('#userPoints').html("Количество баллов: " + visileGiftBalans);
                    if (gift != 0) {
                        selectGridGifts(gift);
                    }
                } else {
                    Msg('Не хватает баллов для подарков!');
                }
                fillBillHeader();
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }
        });
    }
}

function getGiftItems() {

    $.ajax({
        url: "/front/PHP/front.php?getGiftItems",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "getGiftItems",
            levelid: giftarray[giftLevelGrid.getNum()].id
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            for (k = 0; k < data.giftItems.length; k++) {

                giftItemsarray[k] = new Object();
                giftItemsarray[k].id = data.giftItems[k].id;
                giftItemsarray[k].name = data.giftItems[k].name;
                giftItemsarray[k].count = data.giftItems[k].quantity;
                giftItemsarray[k].pointscount = data.giftItems[k].pointscount;
                tmparray = preperaVisibleArray('giftlevels', giftItemsarray);
                giftItemGrid.fillTable(tmparray);
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function addRowToSelectedItems() {
    rownum = giftSelectedItems.getRowCount();
    row = giftItemsarray[giftItemGrid.getNum()]
    if (visileGiftBalans != -1) {
        if (parseInt(visileGiftBalans) >= parseInt(row.pointscount)) {
            giftSelectedItemsarray[rownum] = new Object();
            giftSelectedItemsarray[rownum].id = row.id;
            giftSelectedItemsarray[rownum].name = row.name;
            giftSelectedItemsarray[rownum].count = row.count;
            giftSelectedItemsarray[rownum].pointscount = row.pointscount;
            tmparray = preperaVisibleArray('giftlevels', giftSelectedItemsarray);
            giftSelectedItems.fillTable(tmparray);


            visileGiftBalans = visileGiftBalans - parseInt(row.pointscount);
            $('#userPoints').html("Количество баллов: " + visileGiftBalans);
        } else {
            Msg("Не хватает бонусов для подарка");
        }
    } else {
        if (parseInt(giftarray[giftLevelGrid.getNum()].totalpoints) >= parseInt(row.pointscount)) {
            giftSelectedItemsarray[rownum] = new Object();
            giftSelectedItemsarray[rownum].id = row.id;
            giftSelectedItemsarray[rownum].name = row.name;
            giftSelectedItemsarray[rownum].count = row.count;
            giftSelectedItemsarray[rownum].pointscount = row.pointscount;
            tmparray = preperaVisibleArray('giftlevels', giftSelectedItemsarray);
            giftSelectedItems.fillTable(tmparray);

            giftarray[giftLevelGrid.getNum()].totalpoints = giftarray[giftLevelGrid.getNum()].totalpoints - parseInt(row.pointscount);
            tmparray = preperaVisibleArray('gift', giftarray);
            giftLevelGrid.fillTable(tmparray);
        } else {
            Msg("Не хватает бонусов для подарка");
        }
    }
}

function deleteGiftRow() {
    if (visileGiftBalans != -1) {
        visileGiftBalans = visileGiftBalans + parseInt(giftSelectedItemsarray[giftSelectedItems.getNum()].pointscount);
        $('#userPoints').html("Количество баллов: " + visileGiftBalans);
    } else {
        giftarray[giftLevelGrid.getNum()].totalpoints = giftarray[giftLevelGrid.getNum()].totalpoints + parseInt(giftSelectedItemsarray[giftSelectedItems.getNum()].pointscount);
        tmparray = preperaVisibleArray('gift', giftarray);
        giftLevelGrid.fillTable(tmparray);
        //      $('#0c'+(gselectedrow+1)+'rselectLevelGift').html(giftarray[gselectedrow].name+' ('+giftarray[gselectedrow].totalpoints+')');  
    }
    giftSelectedItemsarray.splice((giftSelectedItems.getNum()), 1);
    tmparray = preperaVisibleArray('giftlevels', giftSelectedItemsarray);
    giftSelectedItems.fillTable(tmparray);
}


function preperaVisibleRow(type, data) {
    if (type == 'ordertable') {
        visibleRow = new Object();
        visibleRow[0] = data.visibleid;
        visibleRow[1] = data.name;
        visibleRow[2] = data.price;
        visibleRow[3] = data.count;
        visibleRow[4] = data.summa;

    }
    if (type == 'comboMenu') {
        visibleRow = new Object();
        visibleRow[0] = selectedComboGrid.getRowCount() + 1;
        visibleRow[1] = data.name;
        visibleRow[2] = data.price;

    }
    return visibleRow;
}

function preperaVisibleArray(type, data) {
    visibleArray = new Array();
    if (type == 'fitnesServiceGrid') {
        for (i = 0; i < data.length; i++) {
            visibleArray[i] = new Object();
            visibleArray[i][0] = data[i].name;
            visibleArray[i][1] = data[i].price;
            visibleArray[i][2] = '';
        }
    }
    if (type == 'comboMenu') {
        for (i = 0; i < data.length; i++) {
            visibleArray[i] = new Object();
            visibleArray[i][0] = i + 1;
            visibleArray[i][1] = data[i].name;
            visibleArray[i][2] = data[i].price;
        }
    }
    if (type == 'materials') {
        for (i = 0; i < data.length; i++) {
            visibleArray[i] = new Object();
            visibleArray[i][0] = i + 1;
            visibleArray[i][1] = data[i].name;
            visibleArray[i][2] = data[i].count;
            visibleArray[i][3] = data[i].summa;
        }
    }
    if (type == 'shtrihSelect') {
        for (i = 0; i < data.length; i++) {
            visibleArray[i] = new Object();
            visibleArray[i][0] = i + 1;
            visibleArray[i][1] = data[i].name;
            visibleArray[i][2] = data[i].count;
            visibleArray[i][2] = data[i].price;
        }
    };
    if (type == 'officiant') {
        for (i = 0; i < data.length; i++) {
            visibleArray[i] = new Object();
            visibleArray[i][0] = data[i].num;
            visibleArray[i][1] = data[i].st;
            visibleArray[i][2] = data[i].visibleid;
            visibleArray[i][3] = data[i].empname;
            visibleArray[i][4] = data[i].tablename;
            visibleArray[i][5] = data[i].dsum;
            visibleArray[i][6] = data[i].partname;
        }
    };
    if (type == 'kassir') {
        for (i = 0; i < data.length; i++) {
            visibleArray[i] = new Object();
            visibleArray[i][0] = data[i].lock;
            //                                visibleArray[i][0]=data[i].st;
            visibleArray[i][1] = data[i].visibleid;
            visibleArray[i][2] = data[i].payname;
            visibleArray[i][3] = data[i].tablename;
            visibleArray[i][4] = data[i].dsum;
            visibleArray[i][5] = data[i].partname;
            visibleArray[i][6] = data[i].employeename;
        }
    };
    if (type == 'ordertable') {
        for (i = 0; i < data.length; i++) {
            visibleArray[i] = new Object();
            visibleArray[i][0] = data[i].visibleid;
            visibleArray[i][1] = data[i].name;
            visibleArray[i][2] = data[i].price;
            visibleArray[i][3] = data[i].count;
            visibleArray[i][4] = data[i].summa;
            visibleArray[i][5] = data[i].coocked;
        }
    }
    if (type == 'clients') {
        for (i = 0; i < data.length; i++) {
            visibleArray[i] = new Object();
            visibleArray[i][0] = i + 1;
            visibleArray[i][1] = data[i].name;
        }
    }
    if (type == 'clientsNew') {
        for (i = 0; i < data.length; i++) {
            visibleArray[i] = new Object();
            visibleArray[i][0] = i + 1;
            visibleArray[i][1] = data[i].clientname;
        }
    }
    if (type == 'employee') {
        for (i = 0; i < data.length; i++) {
            visibleArray[i] = new Object();
            visibleArray[i][0] = i + 1;
            visibleArray[i][1] = data[i].empname;
        }
    }
    if (type == 'comboItemsNew') {
        for (i = 0; i < data.length; i++) {
            visibleArray[i] = new Object();
            visibleArray[i][0] = i + 1;
            visibleArray[i][1] = data[i].itemname;
        }
    }
    if (type == 'gift') {
        for (i = 0; i < data.length; i++) {
            visibleArray[i] = new Object();
            visibleArray[i][0] = i + 1;
            if (data[i].totalpoints != undefined) {
                str = data[i].name + ' (' + data[i].totalpoints + ')';
            } else {
                str = data[i].name;
            }
            visibleArray[i][1] = str;
        }
    }
    if (type == 'giftlevels') {
        for (i = 0; i < data.length; i++) {
            visibleArray[i] = new Object();
            visibleArray[i][0] = i + 1;
            visibleArray[i][1] = data[i].name + ' (' + data[i].count + ')';
        }
    }
    if (type == 'regCheck') {
        for (i = 0; i < data.length; i++) {
            visibleArray[i] = new Object();
            visibleArray[i][0] = i + 1;
            visibleArray[i][1] = data[i].creationdt;
            visibleArray[i][2] = data[i].dtclose;
            visibleArray[i][3] = data[i].num;
            visibleArray[i][4] = data[i].summa;
            visibleArray[i][5] = data[i].content;
        }
    }
//    if (type == 'comboMenu') {
//        for (i = 0; i < data.length; i++) {
//            visibleArray[i] = new Object();
//            visibleArray[i][0] = i + 1;
//            visibleArray[i][1] = data[i].name;
//            visibleArray[i][2] = data[i].price;
//        }
//    }
    if (type == 'comboGroup') {
        for (i = 0; i < data.length; i++) {
            visibleArray[i] = new Object();
            visibleArray[i][0] = i + 1;
            visibleArray[i][1] = data[i].name + '<br> Необходимо:' + data[i].mincount + ' <br> Возможно: ' + data[i].maxcount + ' <br> Выбрано:' + data[i].itemcount;
            visibleArray[i][2] = data[i].price;
        }
    }
    return visibleArray;
}

function beginReturn() {
    res = 0;
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?returnOrder",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "returnOrder",
            orderid: cashierbillstore[cashgrid.getNum()].id
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            res = 1;
        } else {
            res = 0;
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
    if (res != 0) {
        showForm('returnForm');
    }
}

function selectOkReturn() {
    saveReturn();
}


function getOutFocus() {
    itemFocus = 'inpwdcalc';
}

//function selectOkTable(el) {
//        orderdata.tableid = preTableArray[$(el).attr('pNumObj')].id;
//        orderdata.tablename = preTableArray[$(el).attr('pNumObj')].name;
//        if (preTableArray[$(el).attr('pNumObj')].servicepercent != -1) {
//            orderdata.servicepercent = parseInt(preTableArray[$(el).attr('pNumObj')].servicepercent);
//            document.getElementById('servicediv').innerHTML = orderdata.servicepercent + '%';
//        }
//        document.getElementById('tablediv').innerHTML = orderdata.tablename;
//        modyfied = 1;
//        selectCancelTable();
//}

function selectCancelTable() {
    closeForm('tableForm');
//    roomsgrid.clearTable();
//    roomstablegrid.clearTable();
//    tables = undefined;
//    tables = new Array();
//    rooms = undefined;
//    rooms = new Array();
}



function refreshRegCheckOrder() {
    regCheckGrid.clearTable(); 
    $.ajax({
        async: false,
        url: "PHP/front.php?showRegCheck",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'showRegCheck'
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            regCheck = data.rows;
            if (regCheck != null) {
                tmparray = preperaVisibleArray('regCheck', regCheck);
                regCheckGrid.fillTable(tmparray);
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        };

    });
}

function checkRegistration(shtrih) {
    $.ajax({
        async: false,
        url: "PHP/front.php?regCheck",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'regCheck',
            shtrih: shtrih
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            refreshRegCheckOrder();
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        };

    });
}

function beginClientSeletion(pwd) {
    $.ajax({
        async: false,
        url: "PHP/front.php?pwdClientCheck",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'pwdClientCheck',
            pwd: pwd
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            if (data.usepassword == 1) {
                getCalc('clientPwd', null, 'Введите пароль для выбора', 'password');
            } else if (data.usepassword == 0) {
                $("#cancelClientBtn").attr("mousedown","cancelClientBtnClick()");
                $("#okClientBtn").attr("mousedown","okClientBtnClick()");
                document.getElementById('filterOptions').selectedIndex = 0;
                clientsgrid.clearTable();
                clients = undefined;
                clients = new Array();
                selectClientFromGroup();
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        };

    });

}

function cancelClientBtnClick() {
    //    document.getElementById("glass").style.display = "none";
    //    $('#clientForm').stop().animate({"opacity": "0"}, "fast");
    //    document.getElementById("clientForm").style.display = "none";
    closeForm("clientForm");
    clientsgrid.clearTable();
    clients = undefined;
    clients = new Array();
    itemFocus = 'shtrihOF';
}

function okClientBtnClick() {
    selectgridstatus = 'client';
    if (clients[clientsgrid.getNum()].isgroup == 0) {
        updateOrderdata(clientsgrid);
        cancelClientBtnClick();
    };

}


function selectClientFromGroup(filter, filtertype) {
    var isgroup;
    if (clients.length == 0) {
        isgroup = 1;
    } else {
        isgroup = clients[clientsgrid.getNum()].isgroup;
    }

    if ((isgroup == 1 && filtertype == undefined) || (filtertype != '' && filtertype != undefined)) {
        if (clientsgrid.getRowCount() == 0 || filtertype == 'clear') {
            parentid = 0;
        } else {
            parentid = clients[clientsgrid.getNum()].clientid;
        }
        $.ajax({
            async: false,
            url: "/front/PHP/front.php?getClientsFromGroup",
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
                clients = data.rows;
                tmparray = preperaVisibleArray('clientsNew', clients);
                clientsgrid.fillTable(tmparray);
                clientsgrid.selectRow(0);
                showForm("clientForm");
                itemFocus = '';
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);

            }
        });
    } else if (isgroup == 0) {

    }

}

function showStopListForm() {
    showForm("stopListForm");
    showStopList();
}

function addToStopList() {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?addToStopList",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "addToStopList",
            itemid: menuFoldersStopList[menuFoldersGrid.getNum()].id
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            showStopList();
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function deleteFromStopList() {
    $("#msg").html('Удалить товар из стоп листа?')
    $("#msg").dialog({
        resizable: false,
        height: 180,
        modal: true,
        buttons: {
            "Да": function () {
                $.ajax({
                    async: false,
                    url: "/front/PHP/front.php?deleteToStopList",
                    type: "POST",
                    dataType: "json",
                    data: {
                        actionScript: "deleteToStopList",
                        rowid: stopList[stopListGrid.getNum()].id
                    }
                }).success(function (data) {
                    if (data.rescode == 0) {
                        showStopList();
                    } else {
                        console.log(data.rescode + ':' + data.resmsg);
                        alert(data.rescode + ':' + data.resmsg);
                    }
                });
                $(this).dialog("close");
            },
            "Нет": function () {
                $(this).dialog("close");
            }
        }
    });

}

function showStopList() {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?getStopList",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "getStopList"
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            stopList = data.rows;
            tmparray = preperaVisibleArray('clients', stopList);
            stopListGrid.fillTable(tmparray);
            stopListGrid.selectRow(0);
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}


function btnStopListOkClick() {
    closeForm("stopListForm");
    menuFoldersStopList = undefined;
    menuFoldersStopList = new Array();
    menuFoldersGrid.clearTable();
}

function showForm(form) {
    centralizeElement(form);
    document.getElementById("glass").style.display = "block";
    document.getElementById("glass2").style.display = "block";
    $('#' + form + '').css("opacity", "1");
    document.getElementById(form).style.display = "block";
}

function closeForm(form) {
    document.getElementById("glass").style.display = "none";
    document.getElementById("glass2").style.display = "none";
    $('#' + form + '').stop().animate({
        "opacity": "0"
    }, "fast");
    document.getElementById(form).style.display = "none";
}

function stopListPwd(pwd){
        $.ajax({
            async: false,
            url: "/front/PHP/front.php?stopListPwdQuery",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: "stopListPwdQuery",
                pwd:pwd
            }
        }).success(function (data) {
            if (data.rescode == 0) {
               selectStopListItem()
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }
        });    
}

function selectStopListItem() { 
    var isgroup;
    if (menuFoldersStopList.length == 0) {
        isgroup = 1;
    } else {
        isgroup = menuFoldersStopList[menuFoldersGrid.getNum()].isgroup;
    }
    if (isgroup == 1) {
        if (menuFoldersGrid.getRowCount() == 0) {
            parentid = 0;
        } else {
            parentid = menuFoldersStopList[menuFoldersGrid.getNum()].id;
        }
        $.ajax({
            async: false,
            url: "/front/PHP/front.php?getMenuFolders",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: "getMenuFolders",
                parentid: parentid
            }
        }).success(function (data) {
            if (data.rescode == 0) {
                menuFoldersStopList = data.rows;
                tmparray = preperaVisibleArray('clients', menuFoldersStopList);
                menuFoldersGrid.fillTable(tmparray);
                menuFoldersGrid.selectRow(0);
                showStopListForm();
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }
        });
    } else if (isgroup == 0) {
        $("#msg").html('Добавить товар в стоп лист?')
        $("#msg").dialog({
            resizable: false,
            height: 180,
            modal: true,
            buttons: {
                "Да": function () {
                    addToStopList()
                    $(this).dialog("close");
                },
                "Нет": function () {
                    $(this).dialog("close");
                }
            }
        });
    }
}

function showDivideForm(pwd) {
    if (cashierbillstore[cashgrid.getNum()].closed == 0 && cashierbillstore[cashgrid.getNum()].printed == 0) {
        showForm("divideOrderForm");
        getDivindingOrder(pwd);
        divideColumnSum();
    } else {
        Msg('Нельзя разбить закрытый или оплаченный счет!');
    }

}

function cancelbtnDivideOrderClick() {
    closeForm("divideOrderForm");
    divideNewOrderTableDataGrid.clearTable();
    divideOrderTableDataGrid.clearTable();

    divideNewOrderData = undefined;
    divideNewOrderTableData = undefined;
    divideOrderData = undefined;
    divideOrderTableData = undefined;

    divideNewOrderData = new Array();
    divideNewOrderTableData = new Array();
    divideOrderData = new Array();
    divideOrderTableData = new Array();
}

function okbtnDivideOrderClick() {
    if (divideNewOrderTableDataGrid.getRowCount() > 0) {
        saveDivide();
    } else {
        Msg('Не выбран товар для разбиения!');
    }
    cancelbtnDivideOrderClick();
}

function getDivindingOrder(pwd) {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?checkDividePwd",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'checkDividePwd',
            pwd: pwd
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            $.ajax({
                async: false,
                url: "/front/PHP/front.php?showOrder",
                type: "POST",
                dataType: "json",
                data: {
                    actionScript: 'showOrder',
                    orderid: cashierbillstore[cashgrid.getNum()].id,
                    flag:'norm'
                }
            }).success(function (data) {
                if (data.rescode == 0) {
                    divideOrderData.orderid = data.order[0].orderid;
                    divideOrderData.totalsum = data.order[0].totalsum;
                    divideOrderData.servicepercent = data.order[0].servicepercent;
                    divideOrderData.servicesum = data.order[0].servicesum;
                    divideOrderData.discountsum = data.order[0].discountsum;
                    divideOrderData.discountpercent = data.order[0].discountpercent;
                    divideOrderData.tablename = data.order[0].tablename;
                    if (divideOrderData.tablename == undefined) {
                        divideOrderData.tablename = 'Нет';
                    }
                    bills = data.ordertable;
                    if (bills != null && bills != 'empty') {
                        divideOrderTableData = bills;
                        tmparray = preperaVisibleArray('ordertable', divideOrderTableData);
                        divideOrderTableDataGrid.fillTable(tmparray);
                    };
                    $('#divideInfoLabel').html('Счет №:' + data.order[0].visibleid + '; Процент обслуживание:' + divideOrderData.servicepercent + '; Процент скидки:' + divideOrderData.discountpercent +
                        '; Стол:' + divideOrderData.tablename);
                    $('#divideSumOne').html('Сумма:0');
                    $('#divideSumTwo').html('Сумма:0');
                } else {

                    console.log(data.rescode + ':' + data.resmsg);
                    alert(data.rescode + ':' + data.resmsg);
                }
            })
        } else {
            cancelbtnDivideOrderClick();
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    })
}

function dividingGridClick() {
    getCalc('divideCount', null, 'Введите количество', null);
}

function divideColumnSum() {
    if (divideNewOrderTableDataGrid.getRowCount() > 0) {
        var sum = 0;
        var servicesum = 0;
        var discountsum = 0;
        divideNewOrderData.totalsum = sum;
        //    divideNewOrderData.totalsum = Math.round(sum-parseInt(divideOrderData.discountsum)+parseInt(divideOrderData.servicesum));
        $('#divideSumTwo').html('Сумма:' + divideNewOrderData.totalsum);
    } else {
        $('#divideSumTwo').html('Сумма:0');
    }
    var sum = 0;
    for (i = 0; i < divideOrderTableDataGrid.getRowCount(); i++) {
        sum += parseFloat(divideOrderTableData[i].summa);
    }
    sum2 = 0;
    if (configArray.typeOfDiscountService == 0) {
        divideOrderData.servicesum = Math.round(Math.round(sum * (divideOrderData.servicepercent)) / 100);
        divideOrderData.discountsum = Math.round(Math.round(sum * (divideOrderData.discountpercent)) / 100);
    } else if (configArray.typeOfDiscountService == 1) {
        divideOrderData.servicesum = Math.round(Math.round(sum * (divideOrderData.servicepercent)) / 100);
        sum2 = sum + divideOrderData.servicesum;
        divideOrderData.discountsum = Math.round(Math.round(sum2 * (divideOrderData.discountpercent)) / 100);
    } else if (configArray.typeOfDiscountService == 2) {
        divideOrderData.discountsum = Math.round(Math.round(sum * (divideOrderData.discountpercent)) / 100);
        sum2 = sum - odivideOrderData.discountsum;
        divideOrderData.servicesum = Math.round(Math.round(sum2 * (divideOrderData.servicepercent)) / 100);

    }
    divideOrderData.totalsum = Math.round(sum - parseInt(divideOrderData.discountsum) + parseInt(divideOrderData.servicesum));

    $('#divideSumOne').html('Сумма:' + divideOrderData.totalsum);

    var sum = 0;
    for (i = 0; i < divideNewOrderTableDataGrid.getRowCount(); i++) {
        sum += parseFloat(divideNewOrderTableData[i].summa);
    }
    sum2 = 0;

    divideNewOrderTableData.totalsum = Math.round(sum - parseInt(divideOrderData.discountsum) + parseInt(divideOrderData.servicesum));
    $('#divideSumTwo').html('Сумма:' + divideNewOrderTableData.totalsum);

}

function addToDivided(count) {
    rowcount = divideNewOrderTableDataGrid.getRowCount();
    if (count > divideOrderTableData[divideOrderTableDataGrid.getNum()].count) {
        alert('Нельзя перенести товара больше чем есть!');
    } else {
        divideNewOrderTableData[rowcount] = new Object();
        divideNewOrderTableData[rowcount] = clone(divideOrderTableData[divideOrderTableDataGrid.getNum()]);

        divideOrderTableData[divideOrderTableDataGrid.getNum()].count = parseInt(divideOrderTableData[divideOrderTableDataGrid.getNum()].count) - parseInt(count);
        divideOrderTableData[divideOrderTableDataGrid.getNum()].summa = parseInt(divideOrderTableData[divideOrderTableDataGrid.getNum()].price) * parseInt(divideOrderTableData[divideOrderTableDataGrid.getNum()].count)


        divideNewOrderTableData[rowcount].count = parseInt(count);
        divideNewOrderTableData[rowcount].summa = parseInt(count) * parseInt(divideNewOrderTableData[rowcount].price);



        tmparray = preperaVisibleArray('ordertable', divideNewOrderTableData);
        divideNewOrderTableDataGrid.fillTable(tmparray);

        tmparray = preperaVisibleArray('ordertable', divideOrderTableData);
        divideOrderTableDataGrid.fillTable(tmparray);

        divideColumnSum();
    }
}

function deleteFromDivide() {
    rownum = divideNewOrderTableDataGrid.getNum();
    if (rownum > -1) {
        for (i = 0; i < divideOrderTableDataGrid.getRowCount(); i++) {
            if (divideOrderTableData[i].itemid == divideNewOrderTableData[rownum].itemid) {
                id = i;
            }
        }

        divideOrderTableData[id].count = divideOrderTableData[id].count + divideNewOrderTableData[rownum].count;
        divideOrderTableData[id].summa = parseInt(divideOrderTableData[id].count) * parseInt(divideOrderTableData[id].price)

        divideNewOrderTableData.splice(divideNewOrderTableDataGrid.getNum(), 1);


        tmparray = preperaVisibleArray('ordertable', divideNewOrderTableData);
        divideNewOrderTableDataGrid.fillTable(tmparray);

        tmparray = preperaVisibleArray('ordertable', divideOrderTableData);
        divideOrderTableDataGrid.fillTable(tmparray);

        divideColumnSum();
    } else {
        Msg('Не выбран товар!');
    }
}

function saveDivide() {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?saveDivide",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "saveDivide",
            orderid: divideOrderData.orderid,
            ordertable: divideNewOrderTableData
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            changeFilterClick(currentCashFilter, currentCountCashPages);
        } else {
            alert(data.rescode + ':' + data.resmsg);
            console.log(data.rescode + ':' + data.resmsg);
        }
    });
}

function showReturnForm(orderid) {
    //if (cashierbillstore[cashgrid.getNum()].closed==1){
    showForm("returnForm");
    getReturningOrder(orderid);
    //}else{
    //    Msg('Нельзя осуществить возврат закрытого счета!');
    //}

}

function okbtnReturnOrderClick() {
    if (returnNewOrderTableDataGrid.getRowCount() > 0) {
        saveReturn();
    } else {
        Msg('Не выбран товар для возврата!');
    }
    cancelbtnReturnClick()
}


function cancelbtnReturnClick() {
    closeForm("returnForm");
    returnNewOrderTableDataGrid.clearTable();
    returnOrderTableDataGrid.clearTable();

    returnNewOrderTableData = undefined;
    returnOrderTableData = undefined;
    returnNewOrderData = undefined;
    returnOrderData = undefined;

    returnNewOrderTableData = new Array();
    returnOrderTableData = new Array();
    returnNewOrderData = new Array();
    returnOrderData = new Array();
}

function getReturningOrder(orderid) {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?showOrder",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'showOrder',
            orderid: orderid,
            flag:'return'
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            returnOrderData.orderid = data.order[0].orderid;
            returnOrderData.totalsum = data.order[0].totalsum;
            returnOrderData.servicepercent = data.order[0].servicepercent;
            returnOrderData.servicesum = data.order[0].servicesum;
            returnOrderData.discountsum = data.order[0].discountsum;
            returnOrderData.discountpercent = data.order[0].discountpercent;
            returnOrderData.tablename = data.order[0].tablename;
            if (returnOrderData.tablename == undefined) {
                returnOrderData.tablename = 'Нет';
            }
            bills = data.ordertable;
            if (bills != null && bills != 'empty') {
                returnOrderTableData = bills;
                tmparray = preperaVisibleArray('ordertable', returnOrderTableData);
                returnOrderTableDataGrid.fillTable(tmparray);
            };
            returnColumnSum();
            $('#returnInfoLabel').html('Счет №:' + data.order[0].visibleid + '; Процент обслуживание:' + returnOrderData.servicepercent + '; Процент скидки:' + returnOrderData.discountpercent +
                '; Стол:' + returnOrderData.tablename);
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    })
}

function returnGridClick() {
    if (returnOrderTableData[returnOrderTableDataGrid.getNum()].count > 1) {
        getCalc('returnCount', null, 'Введите количество', null);
    } else {
        addToReturn(1);
    }
}

function addToReturn(count) {
    if (count == 0) {
        alert('Нельзя вернуть товар с количетвом 0!');
        return;
    }
    rowcount = returnNewOrderTableDataGrid.getRowCount();
    if (count > returnOrderTableData[returnOrderTableDataGrid.getNum()].count) {
        alert('Нельзя отказаться от товара больше чем есть!');
    } else {
        returnNewOrderTableData[rowcount] = new Object();
        returnNewOrderTableData[rowcount] = clone(returnOrderTableData[returnOrderTableDataGrid.getNum()]);

        returnOrderTableData[returnOrderTableDataGrid.getNum()].count = parseInt(returnOrderTableData[returnOrderTableDataGrid.getNum()].count) - parseInt(count);
        returnOrderTableData[returnOrderTableDataGrid.getNum()].summa = parseInt(returnOrderTableData[returnOrderTableDataGrid.getNum()].price) * parseInt(returnOrderTableData[returnOrderTableDataGrid.getNum()].count)

        returnNewOrderTableData[rowcount].count = parseInt(count) * -1;
        returnNewOrderTableData[rowcount].summa = parseInt(count) * parseInt(returnNewOrderTableData[rowcount].price) * -1;



        tmparray = preperaVisibleArray('ordertable', returnNewOrderTableData);
        returnNewOrderTableDataGrid.fillTable(tmparray);

        tmparray = preperaVisibleArray('ordertable', returnOrderTableData);
        returnOrderTableDataGrid.fillTable(tmparray);
        returnColumnSum();
    }
}

function deleteFromReturn() {
    rownum = returnNewOrderTableDataGrid.getNum();
    if (rownum > -1) {
        for (i = 0; i < returnOrderTableDataGrid.getRowCount(); i++) {
            if (returnOrderTableData[i].itemid == returnNewOrderTableData[rownum].itemid) {
                id = i;
            }
        }

        returnOrderTableData[id].count = returnOrderTableData[id].count + returnNewOrderTableData[rownum].count * -1;
        returnOrderTableData[id].summa = parseInt(returnOrderTableData[id].count) * parseInt(returnOrderTableData[id].price)

        returnNewOrderTableData.splice(returnNewOrderTableDataGrid.getNum(), 1);


        tmparray = preperaVisibleArray('ordertable', returnNewOrderTableData);
        returnNewOrderTableDataGrid.fillTable(tmparray);

        tmparray = preperaVisibleArray('ordertable', returnOrderTableData);
        returnOrderTableDataGrid.fillTable(tmparray);
        returnColumnSum();
    } else {
        Msg('Не выбран товар!');
    }
}

function returnColumnSum() {
    if (returnNewOrderTableDataGrid.getRowCount() > 0) {
        var sum = 0;
        for (i = 0; i < returnNewOrderTableDataGrid.getRowCount(); i++) {
            sum += parseFloat(returnNewOrderTableData[i].summa);
        }
        returnNewOrderData.totalsum = Math.round(sum - parseInt(returnOrderData.discountsum) + parseInt(returnOrderData.servicesum));
        $('#returnSumTwo').html('Сумма:' + returnNewOrderData.totalsum);
    } else {
        $('#returnSumTwo').html('Сумма:0');
    }
    var sum = 0;
    for (i = 0; i < returnOrderTableDataGrid.getRowCount(); i++) {
        sum += parseFloat(returnOrderTableData[i].summa);
    }
    sum2 = 0;
    if (configArray.typeOfDiscountService == 0) {
        returnOrderData.servicesum = Math.round(Math.round(sum * (returnOrderData.servicepercent)) / 100);
        returnOrderData.discountsum = Math.round(Math.round(sum * (returnOrderData.discountpercent)) / 100);
    } else if (configArray.typeOfDiscountService == 1) {
        returnOrderData.servicesum = Math.round(Math.round(sum * (returnOrderData.servicepercent)) / 100);
        sum2 = sum + divideOrderData.servicesum;
        returnOrderData.discountsum = Math.round(Math.round(sum2 * (returnOrderData.discountpercent)) / 100);
    } else if (configArray.typeOfDiscountService == 2) {
        returnOrderData.discountsum = Math.round(Math.round(sum * (returnOrderData.discountpercent)) / 100);
        sum2 = sum - odivideOrderData.discountsum;
        returnOrderData.servicesum = Math.round(Math.round(sum2 * (returnOrderData.servicepercent)) / 100);

    }
    returnOrderData.totalsum = Math.round(sum - parseInt(returnOrderData.discountsum) + parseInt(returnOrderData.servicesum));

    $('#returnSumOne').html('Сумма:' + returnOrderData.totalsum);
}

function saveReturn() {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?saveReturn",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "saveReturn",
            orderid: returnOrderData.orderid,
            ordertable: returnNewOrderTableData
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            if (data.xmlOrder != '') {
                printTCP(data.xmlOrder);
            }
            //                                Msg('Возврат был успешно осуществлен!'); 
            changeFilterClick(currentCashFilter, currentCountCashPages);
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function cancelbtnReturnOrderClick() {
    closeForm('orderSelectReturnForm');
    $('#numReturnOrder').val("");
    $('#dateReturnOrder').val("2013-01-01");
}

function okbtnShowReturnOrderClick() {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?selectReturnOrder",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "selectReturnOrder",
            orderNum: $('#numReturnOrder').val(),
            date: $('#dateReturnOrder').val()
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            cancelbtnReturnOrderClick();
            showReturnForm(data.orderid);
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
            cancelbtnReturnOrderClick();
        }
    });

}

function doChangePrice(pwd) {
    if (ordertabledata[ordergrid.getNum()].status == 'new') {
        $.ajax({
            async: false,
            url: "/front/PHP/front.php?checkChangePricePassword",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: "checkChangePricePassword",
                pwd: pwd
            }
        }).success(function (data) {
            if (data.rescode == 0) {
                if (data.usepassword == 1) {
                    getCalc('pwdChangePrice', null, 'Введите пароль', 'password');
                } else if (data.usepassword == 0) {
                    getCalc('changePrice', null, 'Введите цену', null);
                }
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }
        });
    } else {}
}

function doReportAkt(pwd) {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?checkAktReportPassword",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "checkAktReportPassword",
            pwd: pwd
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            if (data.usepassword == 1) {
                getCalc('pwdReportAkt', null, 'Введите пароль', 'password');
            } else if (data.usepassword == 0) {
                otchet('akt')
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function doReportPoschetam(pwd) {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?checkPoschetamReportPassword",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "checkPoschetamReportPassword",
            pwd: pwd
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            if (data.usepassword == 1) {
                getCalc('pwdReportPoschetam', null, 'Введите пароль', 'password');
            } else if (data.usepassword == 0) {
                otchet('poschetam')
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function doReportItog(pwd) {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?checkItogReportPassword",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "checkItogReportPassword",
            pwd: pwd
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            if (data.usepassword == 1) {
                getCalc('pwdReportItog', null, 'Введите пароль', 'password');
            } else if (data.usepassword == 0) {
                otchet('itog');
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function doReportRefuse(pwd) {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?checkRefuseReportPassword",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "checkRefuseReportPassword",
            pwd: pwd
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            if (data.usepassword == 1) {
                getCalc('pwdReportRefuse', null, 'Введите пароль', 'password');
            } else if (data.usepassword == 0) {
                otchet('refuse');
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function doReportRefuse_and_orders(pwd) {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?checkRefuse_and_ordersReportPassword",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "checkRefuse_and_ordersReportPassword",
            pwd: pwd
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            if (data.usepassword == 1) {
                getCalc('pwdReportRefuse_and_orders', null, 'Введите пароль', 'password');
            } else if (data.usepassword == 0) {
                otchet('refuse_and_orders');
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function doReportXreport(pwd) {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?checkXreportReportPassword",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "checkXreportReportPassword",
            pwd: pwd
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            if (data.usepassword == 1) {
                getCalc('pwdReportXreport', null, 'Введите пароль', 'password');
            } else if (data.usepassword == 0) {
                if (configArray.useFR==1){
                    printXZreport('Xreport');
                }
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function cancelAddClient() {
    document.getElementById('clientGroup').value = 0;
    document.getElementById('clientFIO').value = '';
    document.getElementById('clientMap').value = '';
    document.getElementById('clientBirth').value = '2013-01-01';
    document.getElementById('clientEmail').value = '';
    document.getElementById('clientTel').value = '';
    document.getElementById('clientAdress').value = '';
    document.getElementById('clientSupInf').value = '';
    clientsgrid.clearTable();
    clients = undefined;
    clients = new Array();
    closeForm("clientAddForm");
    selectClientFromGroup();
}

function showAddClientForm(pwd) {
    itemFocus = '';
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?pwdAddClient",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "pwdAddClient",
            pwd: pwd
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            p = data.groups;
            s = '';
            for (k = 0; k < p.length; k++) {
                s += '<option value="' + p[k].id + '">' + p[k].name + '</option>'
            }
            $('#clientGroup').html(s)
            showForm("clientAddForm");
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function okAddClient() {
    clientInfo = new Object();
    clientInfo.group = document.getElementById('clientGroup').value;
    clientInfo.fio = document.getElementById('clientFIO').value;
    clientInfo.card = document.getElementById('clientMap').value;
    clientInfo.dateOfBirth = document.getElementById('clientBirth').value;
    clientInfo.email = document.getElementById('clientEmail').value;
    clientInfo.tel = document.getElementById('clientTel').value;
    clientInfo.adress = document.getElementById('clientAdress').value;
    clientInfo.supInfo = document.getElementById('clientSupInf').value;
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?doAddClient",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "doAddClient",
            clientInfo: clientInfo
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            cancelAddClient();
            cancelClientBtnClick();
            selectGrid('client',null,'',data.lastid);
//            for (i=0;i<clients.length;i++){
//               
//            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function okAddClientFromBsJournal() {
    clientInfo = new Object();
    clientInfo.group = document.getElementById('clientGroup').value;
    clientInfo.fio = document.getElementById('clientFIO').value;
    clientInfo.card = document.getElementById('clientMap').value;
    clientInfo.dateOfBirth = document.getElementById('clientBirth').value;
    clientInfo.email = document.getElementById('clientEmail').value;
    clientInfo.tel = document.getElementById('clientTel').value;
    clientInfo.adress = document.getElementById('clientAdress').value;
    clientInfo.supInfo = document.getElementById('clientSupInf').value;
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?doAddClient",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "doAddClient",
            clientInfo: clientInfo
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            cancelAddClient();
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function showAddNoteForm(pwd) {
    itemFocus = '';
    showForm("noteAddForm");
    $.ajax({
        async: false,
        url: "/front/PHP/front.php",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "",
            pwd: pwd
        }
    }).success(function (data) {
        if (data.rescode == 0) {

        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function cancelAddNote() {
    document.getElementById('newNote').value = '';
    closeForm("noteAddForm");
}

function doService(pwd) {
    itemFocus = '';
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?pwdService",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "pwdService",
            pwd: pwd
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            if (data.usepassword == 1) {
                getCalc('pwdService', null, 'Введите пароль', 'password');
            } else if (data.usepassword == 0) {
                getCalc('service', null, 'Введите процент обслуживания', null)
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function okbtnShtrihClick() {
    rowcount = ordergrid.getRowCount();
    rownum = shtrihSelectGrid.getNum();

    ordertabledata[rowcount] = new Object();
    ordertabledata[rowcount].visibleid = rowcount + 1;
    ordertabledata[rowcount].id = shtrihItemsStore[rownum].id;
    ordertabledata[rowcount].name = shtrihItemsStore[rownum].name;
    ordertabledata[rowcount].price = shtrihItemsStore[rownum].price;
    ordertabledata[rowcount].count = 1;
    ordertabledata[rowcount].summa = 1 * shtrihItemsStore[rownum].price;
    ordertabledata[rowcount].status = 'new';
    ordertabledata[rowcount].note = '';
    ordertabledata[rowcount].printer = 0;
    ordertabledata[rowcount].weight = shtrihItemsStore[rownum].weight;
    if (ordertabledata[rowcount].weight == 1) {
        ordertabledata[rowcount].count = 0;
        getCalc('countbtnShtrih', ordertabledata[rowcount].id, 'Введите количество', null);
    }
    tmparray = preperaVisibleArray('ordertable', ordertabledata);
    ordergrid.fillTable(tmparray);
    columnSum();
    modyfied = 1;
    cancelbtnShtrihClick()
}

function cancelbtnShtrihClick() {
    shtrihItemsGrid = new Array();
    shtrihSelectGrid.clearTable();
    itemFocus = 'shtrihOF';
    closeForm('shtrihSelectForm');
}

function doComplex(id) {
    showForm('comboMenuForm');
    document.getElementById("ComboLabelSum").innerHTML = 'Итог: 0';
    itemFocus = '';
    getGroups(id);
}

function getGroups(id) {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?getComboGroup",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "getComboGroup",
            itemid: id
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            comboGroupsStore = data.rows;
            tmparray = preperaVisibleArray('comboGroup', comboGroupsStore);
            comboGroupsGrid.fillTable(tmparray);
            comboGroupsGrid.selectRow(0);

            for (jj = 0; jj < comboGroupsStore.length; jj++) {
                comboMenu = comboGroupsStore[jj].subItems;
                for (ii = 0; ii < comboMenu.length; ii++) {
                    if (comboMenu[ii].checkid == comboGroupsStore[jj].defaultitem) {
                        rowcount = selectedComboGrid.getRowCount();
                        selectedComboStore[rowcount] = new Object();
                        selectedComboStore[rowcount].id = comboMenu[ii].id;
                        selectedComboStore[rowcount].name = '+ ' + comboMenu[ii].name + ' (' + comboGroupsStore[jj].name + ')';
                        selectedComboStore[rowcount].price = parseInt(comboMenu[ii].price) + parseInt(comboMenu[ii].groupprice);
                        selectedComboStore[rowcount].checkid = comboMenu[ii].checkid;
                        selectedComboStore[rowcount].pos = jj;
                        selectedComboStore[rowcount].idcombogroup = comboMenu[ii].idcombogroup;
                        selectedComboStore[rowcount].groupprice = comboMenu[ii].groupprice;
                        selectedComboStore[rowcount].parentid = comboMenu[ii].parentid;
                        selectedComboStore[rowcount].printer = comboMenu[ii].printer;
                        tmprow = preperaVisibleArray('comboMenu', selectedComboStore)
                        selectedComboGrid.fillTable(tmprow);
                        comboGroupsStore[jj].itemcount++;
                        tmparray = preperaVisibleArray('comboGroup', comboGroupsStore);
                        comboGroupsGrid.fillTable(tmparray);
                        comboGroupsGrid.selectRow(selectedComboStore[rowcount].pos);
                        comboSum();
                    }
                }
            }

        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function cancelBtnComboForm() {
    comboGroupsGrid.clearTable();
    comboGroupsStore = new Array();
    selectedComboGrid.clearTable();
    selectedComboStore = new Array();
    comboMenu = new Array();
    $('#menudivCombo').html("");
    closeForm('comboMenuForm');
    itemFocus = 'shtrihOF';
}

function showComboMenu() {
    $('#menudivCombo').html("");
    var menudivCombo = document.getElementById("menudivCombo");
    for (i = 0; i < comboMenu.length; i++) {
        menudivCombo.appendChild(createComboButton(comboMenu[i].checkid, comboMenu[i].name, "button white font-black p active", comboMenu[i].price));
    }
}

function createrComboMenu() {
    comboMenu = new Array();
    $('#menudivCombo').html("");
    var menudivCombo = document.getElementById("menudivCombo");
    comboMenu = comboGroupsStore[comboGroupsGrid.getNum()].subItems;
    showComboMenu();
    defineComboItem();
}

function defineComboItem() {
    for (jj = 0; jj < selectedComboStore.length; jj++) {
        if (selectedComboStore[jj].idcombogroup == comboGroupsStore[comboGroupsGrid.getNum()].id) {
            selectedComboGrid.selectRow(jj);
            return;
        }
    }
}

function createComboButton(id, inner, cl, price) {
    var newBtn;
    newBtn = document.createElement("button");
    newBtn.setAttribute("class", cl);
    newBtn.setAttribute("id", id);
    newBtn.setAttribute("onmousedown", 'menuComboClick(id,1)');
    if (cl != "button white font-black p active") {
        newBtn.innerHTML = "<p>" + inner + "</p>";
    } else {
        newBtn.innerHTML = "<p>" + inner + "</p>";
//        newBtn.setAttribute("onClick", "menuComboClick(id,1)");
        var newDiv = document.createElement("div");
        newDiv.setAttribute("class", "pricecell div red");
        newDiv.innerHTML = Math.round(price);
        newBtn.appendChild(newDiv);
    }
    return newBtn;
}

function findComboButtonPos(id) {
    for (i = 0; i < comboMenu.length; i++) {
        if (comboMenu[i].checkid == id) {
            return i;
        }
    }
}

function menuComboClick(idMenu, c) {
    if ((comboGroupsStore[comboGroupsGrid.getNum()].itemcount < comboGroupsStore[comboGroupsGrid.getNum()].maxcount) ||
        (comboGroupsStore[comboGroupsGrid.getNum()].maxcount == 1 && comboGroupsStore[comboGroupsGrid.getNum()].mincount == 1)) {
        var count;
        c > 0 ? count = c : count = 1;
        count = parseFloat(count);
        var button = document.getElementById(idMenu);
        var menudivCombo = document.getElementById("menudivCombo");

        if (comboGroupsStore[comboGroupsGrid.getNum()].mincount == 1 && comboGroupsStore[comboGroupsGrid.getNum()].maxcount == 1 &&
            comboGroupsStore[comboGroupsGrid.getNum()].maxcount == comboGroupsStore[comboGroupsGrid.getNum()].itemcount) {
            rowcount = selectedComboGrid.getNum();
        } else {
            rowcount = selectedComboGrid.getRowCount();
        }
        posNum = findComboButtonPos(idMenu);
        selectedComboStore[rowcount] = new Object();
        selectedComboStore[rowcount].id = comboMenu[posNum].id;
        selectedComboStore[rowcount].name = '+ ' + comboMenu[posNum].name + ' (' + comboGroupsStore[comboGroupsGrid.getNum()].name + ')';
        selectedComboStore[rowcount].price = parseInt(comboMenu[posNum].price) + parseInt(comboGroupsStore[comboGroupsGrid.getNum()].price);
        selectedComboStore[rowcount].checkid = comboMenu[posNum].checkid;
        selectedComboStore[rowcount].pos = comboGroupsGrid.getNum();
        selectedComboStore[rowcount].idcombogroup = comboMenu[posNum].idcombogroup;
        selectedComboStore[rowcount].groupprice = comboMenu[posNum].groupprice;
        selectedComboStore[rowcount].parentid = comboMenu[posNum].parentid;
        selectedComboStore[rowcount].printer = comboMenu[posNum].printer;
        tmprow = preperaVisibleArray('comboMenu', selectedComboStore)
        selectedComboGrid.fillTable(tmprow);

        if (comboGroupsStore[comboGroupsGrid.getNum()].mincount == 1 && comboGroupsStore[comboGroupsGrid.getNum()].maxcount == 1) {
            comboGroupsStore[comboGroupsGrid.getNum()].itemcount = 1;
        } else {
            comboGroupsStore[comboGroupsGrid.getNum()].itemcount++;
        }

        //        comboGroupsStore[comboGroupsGrid.getNum()].itemcount++;
        tmparray = preperaVisibleArray('comboGroup', comboGroupsStore);
        comboGroupsGrid.fillTable(tmparray);
        comboGroupsGrid.selectRow(selectedComboStore[rowcount].pos);
        defineComboItem();
        comboSum();

    }
}

function deleteComboItem() {
    if (selectedComboStore.length > 0) {
        var curpos = selectedComboStore[selectedComboGrid.getNum()].pos;
        if (selectedComboStore.length == 0) {
            comboGroupsGrid.selectRow(0);
        } else {
            comboGroupsGrid.selectRow(curpos);
        }
        selectedComboStore.splice((selectedComboGrid.getNum()), 1);
        for (i = 0; i < selectedComboStore.length; i++) {
            selectedComboStore[i].visibleid = i + 1;
        };
        tmparray = preperaVisibleArray('comboMenu', selectedComboStore);
        selectedComboGrid.fillTable(tmparray);
        comboGroupsStore[comboGroupsGrid.getNum()].itemcount--;
        tmparray = preperaVisibleArray('comboGroup', comboGroupsStore);
        comboGroupsGrid.fillTable(tmparray);
        comboGroupsGrid.selectRow(curpos);
        comboSum();
    }
}

function comboSum() {
    var sum = 0;
    for (i = 0; i < selectedComboGrid.getRowCount(); i++) {
        sum += parseInt(selectedComboStore[i].price);
    }
    //    for (i = 0; i < comboGroupsGrid.getRowCount(); i++) {
    //        for (j = 0; j < selectedComboGrid.getRowCount(); j++) {
    //            if (selectedComboStore[j].idcombogroup==comboGroupsStore[i].id){
    //                sum+=parseInt(comboGroupsStore[i].price);
    //                break;
    //            }
    //        } 
    //    }
    combototalsum = parseInt(sum);
    document.getElementById("ComboLabelSum").innerHTML = 'Итог: ' + parseInt(sum);
}

function okbtnCombo(c) {
    if (selectedComboStore.length > 0) {
        var count;
        c > 0 ? count = c : count = 1;
        count = parseFloat(count);
        check = true;
        err = '';
        for (k = 0; k < comboGroupsStore.length; k++) {
            if (comboGroupsStore[k].mincount > comboGroupsStore[k].itemcount) {
                check = false;
                err += '"' + comboGroupsStore[k].name + '" ';
            }
        }
        if (check) {
            var comboName = '';
            rowcount = ordergrid.getRowCount();
            ordertabledata[rowcount] = new Object();
            ordertabledata[rowcount].visibleid = rowcount + 1;
            ordertabledata[rowcount].id = comboGroupsStore[0].itemid;
            //        ordertabledata[rowcount].name = simplemenu[findButtonPos(tmpIdBtn)].name;
            ordertabledata[rowcount].price = combototalsum;
            ordertabledata[rowcount].count = count;
            ordertabledata[rowcount].printer = simplemenu[findButtonPos(tmpIdBtn)].printer;
            ordertabledata[rowcount].summa = count * combototalsum;
            ordertabledata[rowcount].note = '';
            ordertabledata[rowcount].complex = 1;
            ordertabledata[rowcount].status = 'new';
            ordertabledata[rowcount].comboItems = new Array();


            for (k = 0; k < selectedComboStore.length; k++) {
                ordertabledata[rowcount].comboItems[k] = new Object();
                ordertabledata[rowcount].comboItems[k].itemid = selectedComboStore[k].id;
                ordertabledata[rowcount].comboItems[k].name = selectedComboStore[k].name;
                ordertabledata[rowcount].comboItems[k].price = parseInt(selectedComboStore[k].price);
                ordertabledata[rowcount].comboItems[k].count = count;
                ordertabledata[rowcount].comboItems[k].printer = selectedComboStore[k].printer;
                ordertabledata[rowcount].comboItems[k].summa = selectedComboStore[k].groupprice * count;
                ordertabledata[rowcount].comboItems[k].note = '';
                ordertabledata[rowcount].comboItems[k].parentid = selectedComboStore[k].parentid;
                ordertabledata[rowcount].comboItems[k].status = 'new';
                comboName += selectedComboStore[k].name + '<br>'
            }
            ordertabledata[rowcount].name = simplemenu[findButtonPos(tmpIdBtn)].name + '<br>' + comboName + '';
            tmprow = preperaVisibleRow('ordertable', ordertabledata[rowcount])
            ordergrid.addRow(tmprow);
            columnSum();
            modyfiedt = 1;
            cancelBtnComboForm();
        } else {
            alert('В группе ' + err + ', не выбрано необходимое количество товара!');
        }
    }
}

function doComboAdd(pwd) {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?checkComboPwd",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "checkComboPwd",
            pwd: pwd
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            if (data.usepassword == 1) {
                getCalc('comboPwd', null, 'Введите пароль', 'password');
            } else if (data.usepassword == 0) {
                comboAddFormShow()
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function comboAddFormShow() {
    showForm('comboMenuAddForm');
    fillComboMenu();
}

function comboAddFormClose() {
    $('#groupName').val('');
    $('#groupPrice').val('');
    $('#groupMinCount').val('');
    $('#groupMaxCount').val('');
    $('#minCountComboGroup').html('');
    $('#maxCountComboGroup').html('');
    $('#priceComboGroup').html('');
    $('#defaultComboGroupItem').html('');
    addComboGroupsGrid.clearTable();
    addComboGroupsItemsGrid.clearTable();
    addComboMenuGrid.clearTable();
    addComboGroupsStore = new Array();
    addComboMenuStore = new Array();
    addComboGroupsItemsStore = new Array();
    closeForm('comboMenuAddForm');
}

function btnComboMenuAddCancelClick() {
    comboAddFormClose();
}

function fillComboMenu() {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?getAddComboMenu",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'getAddComboMenu'
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            addComboMenuStore = data.rows;
            tmparray = preperaVisibleArray('clients', addComboMenuStore);
            addComboMenuGrid.fillTable(tmparray);
            addComboMenuGrid.selectRow(0);
            getAddComboGroup();
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    })
}

function getAddComboGroup() {
    addComboGroupsItemsGrid.clearTable();
    addComboGroupsItemsStore = new Array();
    $('#minCountComboGroup').html('');
    $('#maxCountComboGroup').html('');
    $('#priceComboGroup').html('');
    $('#defaultComboGroupItem').html('');
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?getAddComboGroup",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'getAddComboGroup',
            itemid: addComboMenuStore[addComboMenuGrid.getNum()].id
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            addComboGroupsStore = data.rows;
            tmparray = preperaVisibleArray('clients', addComboGroupsStore);
            addComboGroupsGrid.fillTable(tmparray);
            addComboGroupsGrid.selectRow(0);
            getAddComboGroupItems();
            ComboPriceSum();
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    })
}

function ComboPriceSum() {
    sum = 0;
    for (i = 0; i < addComboGroupsGrid.getRowCount(); i++) {
        sum += parseInt(addComboGroupsStore[i].price);
    }
    $('#comboPriceLabel').html('Цена комбо: ' + sum)
}

function getAddComboGroupItems() {
    if (addComboGroupsGrid.getRowCount() > 0) {
        $('#minCountComboGroup').html('Минимальное количество:' + addComboGroupsStore[addComboGroupsGrid.getNum()].mincount);
        $('#maxCountComboGroup').html('Максимальное количество:' + addComboGroupsStore[addComboGroupsGrid.getNum()].maxcount);
        $('#priceComboGroup').html('Цена группы:' + addComboGroupsStore[addComboGroupsGrid.getNum()].price);
        $.ajax({
            async: false,
            url: "/front/PHP/front.php?getAddComboGroupItems",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: 'getAddComboGroupItems',
                itemid: addComboGroupsStore[addComboGroupsGrid.getNum()].id
            }
        }).success(function (data) {
            if (data.rescode == 0) {
                addComboGroupsItemsStore = data.rows;
                tmparray = preperaVisibleArray('clients', addComboGroupsItemsStore);
                addComboGroupsItemsGrid.fillTable(tmparray);
                addComboGroupsItemsGrid.selectRow(0);
                defaultname = 'Нет';
                for (i = 0; i < addComboGroupsItemsStore.length; i++) {
                    if (addComboGroupsItemsStore[i].id == addComboGroupsStore[addComboGroupsGrid.getNum()].defaultitem) {
                        defaultname = addComboGroupsItemsStore[i].name;
                        break;
                    }
                }
                $('#defaultComboGroupItem').html('По умолчанию:' + defaultname);
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }
        })
    }
}

function doAddComboGroup() {
    if (addComboMenuGrid.getNum() > -1) {
        closeForm('comboMenuAddForm');
        showForm('addComboGroupForm');
        $('#btnOkAddComboGroupForm').attr("onClick", "btnOkAddComboGroupFormClick()");
    } else {
        alert('Выберите комбо!');
    }
}

function doChangeComboGroup() {
    if (addComboMenuGrid.getNum() > -1) {
        closeForm('comboMenuAddForm');
        showForm('addComboGroupForm');
        getComboGroupProperties();
        $('#btnOkAddComboGroupForm').attr("onClick", "btnOkChangeComboGroupFormClick()");
    } else {
        alert('Выберите комбо!');
    }
}

function getComboGroupProperties() {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?getComboGroupProperties",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'getComboGroupProperties',
            groupid: addComboGroupsStore[addComboGroupsGrid.getNum()].id
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            $('#groupName').val(data.rows[0].name);
            $('#groupPrice').val(data.rows[0].price);
            $('#groupMinCount').val(data.rows[0].mincount);
            $('#groupMaxCount').val(data.rows[0].maxcount);
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    })
}

function btnOkChangeComboGroupFormClick() {
    tmpArray = new Object();
    tmpArray.itemid = addComboGroupsStore[addComboGroupsGrid.getNum()].id;
    tmpArray.name = $('#groupName').val();
    tmpArray.price = $('#groupPrice').val();
    tmpArray.mincount = $('#groupMinCount').val();
    tmpArray.maxcount = $('#groupMaxCount').val();
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?updateComboGroupProperties",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'updateComboGroupProperties',
            row: tmpArray
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            addComboGroupsItemsStore = new Array();
            addComboGroupsItemsGrid.clearTable();
            $('#groupName').val('');
            $('#groupPrice').val('');
            $('#groupMinCount').val('');
            $('#groupMaxCount').val('');
            $('#minCountComboGroup').html('');
            $('#maxCountComboGroup').html('');
            $('#priceComboGroup').html('');
            $('#defaultComboGroupItem').html('');
            getAddComboGroup();
            getAddComboGroupItems();
            btnCancelAddComboGroupFormClick();
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    })

}

function btnCancelAddComboGroupFormClick() {
    closeForm('addComboGroupForm');
    showForm('comboMenuAddForm');
}

function btnOkAddComboGroupFormClick() {
    tmpArray = new Object();
    tmpArray.itemid = addComboMenuStore[addComboMenuGrid.getNum()].id;
    tmpArray.name = $('#groupName').val();
    tmpArray.price = $('#groupPrice').val();
    tmpArray.mincount = $('#groupMinCount').val();
    tmpArray.maxcount = $('#groupMaxCount').val();
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?addNewComboGroup",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'addNewComboGroup',
            row: tmpArray
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            addComboGroupsItemsStore = new Array();
            addComboGroupsItemsGrid.clearTable();
            $('#groupName').val('');
            $('#groupPrice').val('');
            $('#groupMinCount').val('');
            $('#groupMaxCount').val('');
            $('#minCountComboGroup').html('');
            $('#maxCountComboGroup').html('');
            $('#priceComboGroup').html('');
            $('#defaultComboGroupItem').html('');
            getAddComboGroup();
            getAddComboGroupItems();
            btnCancelAddComboGroupFormClick();
            ComboPriceSum();
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    })

}

function doDeleteComboGroup() {
    if (addComboGroupsGrid.getNum() > -1) {
        $("#msg").html('Удалить группу?')
        $("#msg").dialog({
            resizable: false,
            height: 180,
            modal: true,
            buttons: {
                "Да": function () {
                    $.ajax({
                        async: false,
                        url: "/front/PHP/front.php?deleteComboGroup",
                        type: "POST",
                        dataType: "json",
                        data: {
                            actionScript: 'deleteComboGroup',
                            groupId: addComboGroupsStore[addComboGroupsGrid.getNum()].id
                        }
                    }).success(function (data) {
                        if (data.rescode == 0) {
                            addComboGroupsItemsStore = new Array();
                            addComboGroupsItemsGrid.clearTable();
                            $('#groupName').val('');
                            $('#groupPrice').val('');
                            $('#groupMinCount').val('');
                            $('#groupMaxCount').val('');
                            $('#minCountComboGroup').html('');
                            $('#maxCountComboGroup').html('');
                            $('#priceComboGroup').html('');
                            $('#defaultComboGroupItem').html('');
                            getAddComboGroup();
                        } else {
                            console.log(data.rescode + ':' + data.resmsg);
                            alert(data.rescode + ':' + data.resmsg);
                        }
                    })
                    $(this).dialog("close");
                },
                "Нет": function () {
                    $(this).dialog("close");
                }
            }
        });
    } else {
        alert('Не выбрана группа для удаления!');
    }
}

function doAddComboGroupItem() {
    if (addComboGroupsGrid.getNum() > -1) {
        closeForm('comboMenuAddForm');
        showForm('addComboGroupItemForm');
        selectItemFromGroup_Combo();
    } else {
        alert('Не выбрана комбо группа!');
    }
}

function cancelbtnaddComboGroupItemClick() {
    addComboGroupsItemsSelectStore = new Array();
    addComboGroupsItemsSelectGrid.clearTable();
    addComboItemToSelectedStore = new Array();
    addComboItemToSelectedGrid.clearTable();
    closeForm('addComboGroupItemForm');
    showForm('comboMenuAddForm');
}

function selectItemFromGroup_Combo() {
    var isgroup;
    if (addComboGroupsItemsSelectStore.length == 0) {
        isgroup = 1;
    } else {
        isgroup = addComboGroupsItemsSelectStore[addComboGroupsItemsSelectGrid.getNum()].isgroup;
    }

    if (isgroup == 1) {
        if (addComboGroupsItemsSelectGrid.getRowCount() == 0) {
            parentid = 0;
        } else {
            parentid = addComboGroupsItemsSelectStore[addComboGroupsItemsSelectGrid.getNum()].itemid;
        }
        $.ajax({
            async: false,
            url: "/front/PHP/front.php?getItemsFromGroup_combo",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: "getItemsFromGroup_combo",
                parentid: parentid
            }
        }).success(function (data) {
            if (data.rescode == 0) {
                addComboGroupsItemsSelectStore = data.rows;
                tmparray = preperaVisibleArray('comboItemsNew', addComboGroupsItemsSelectStore);
                addComboGroupsItemsSelectGrid.fillTable(tmparray);
                addComboGroupsItemsSelectGrid.selectRow(0);
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }
        });
    } else if (isgroup == 0) {
        getCalc('addToComboItem', null, 'Введите цену', null)
    }
}

function addToComboItem(count) {
    rowcount = addComboItemToSelectedGrid.getRowCount();
    addComboItemToSelectedStore[rowcount] = new Object();
    addComboItemToSelectedStore[rowcount].itemid = addComboGroupsItemsSelectStore[addComboGroupsItemsSelectGrid.getNum()].id;
    addComboItemToSelectedStore[rowcount].name = addComboGroupsItemsSelectStore[addComboGroupsItemsSelectGrid.getNum()].itemname;
    addComboItemToSelectedStore[rowcount].printer = addComboGroupsItemsSelectStore[addComboGroupsItemsSelectGrid.getNum()].printer;
    addComboItemToSelectedStore[rowcount].price = count;
    tmparray = preperaVisibleArray('shtrihSelect', addComboItemToSelectedStore);
    addComboItemToSelectedGrid.fillTable(tmparray);
}

function deletebtnSelectedComboItemsClick() {
    addComboItemToSelectedStore.splice((addComboItemToSelectedGrid.getNum()), 1);
    for (i = 0; i < addComboItemToSelectedStore.length; i++) {
        addComboItemToSelectedStore[i].visibleid = i + 1;
    };
    tmparray = preperaVisibleArray('shtrihSelect', addComboItemToSelectedStore);
    addComboItemToSelectedGrid.fillTable(tmparray);
}

function okbtnaddComboGroupItemClick() {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?addComboGroupItem",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "addComboGroupItem",
            groupid: addComboGroupsStore[addComboGroupsGrid.getNum()].id,
            row: addComboItemToSelectedStore
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            getAddComboGroupItems();
            cancelbtnaddComboGroupItemClick();
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function btndeleteComboGroupItemClick() {
    if (addComboGroupsItemsGrid.getNum() > -1) {
        $("#msg").html('Удалить элемент?')
        $("#msg").dialog({
            resizable: false,
            height: 180,
            modal: true,
            buttons: {
                "Да": function () {
                    $.ajax({
                        async: false,
                        url: "/front/PHP/front.php?deleteComboGroupItem",
                        type: "POST",
                        dataType: "json",
                        data: {
                            actionScript: 'deleteComboGroupItem',
                            comboItemId: addComboGroupsItemsStore[addComboGroupsItemsGrid.getNum()].id,
                            comboItemName: addComboGroupsItemsStore[addComboGroupsItemsGrid.getNum()].name
                        }
                    }).success(function (data) {
                        if (data.rescode == 0) {
                            addComboGroupsItemsStore = new Array();
                            addComboGroupsItemsGrid.clearTable();
                            getAddComboGroupItems();
                        } else {
                            console.log(data.rescode + ':' + data.resmsg);
                            alert(data.rescode + ':' + data.resmsg);
                        }
                    })
                    $(this).dialog("close");
                },
                "Нет": function () {
                    $(this).dialog("close");
                }
            }
        });
    } else {
        alert('Не выбран комбо-элемент для удаления!');
    }
}

function doAddDefaultComboItemForm() {
    closeForm('comboMenuAddForm');
    showForm('setDefaultComboItemForm');
    s = '<option value=0>Нет элемента по-умолчанию</option>';
    for (k = 0; k < addComboGroupsItemsStore.length; k++) {
        s += '<option value="' + addComboGroupsItemsStore[k].id + '">' + addComboGroupsItemsStore[k].name + '</option>'
    }
    document.getElementById('defaultComboItemList').innerHTML = s;
}

function btnCancelsetDefaultComboItemFormClick() {
    document.getElementById('defaultComboItemList').innerHTML = '';
    closeForm('setDefaultComboItemForm');
    showForm('comboMenuAddForm');
}

function btnOksetDefaultComboItemFormClick() {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?setDefaultComboItem",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "setDefaultComboItem",
            defid: document.getElementById('defaultComboItemList').value,
            groupid: addComboGroupsStore[addComboGroupsGrid.getNum()].id
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            btnCancelsetDefaultComboItemFormClick();
            getAddComboGroup();
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function checkDiscountPassword(pwd) {
    var useDiscPwd = false;
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?checkDiscountPassword3",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "checkDiscountPassword",
            pwd: pwd
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            useDiscPwd = true;
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
    return useDiscPwd;
}

function doSetPrinterFormComboItems() {
    closeForm('comboMenuAddForm');
    showForm('setPrinterComboItemForm');
    getPrinterDivisions();
}

function btnCancelsetPrinterComboItemFormClick() {
    closeForm('setPrinterComboItemForm');
    showForm('comboMenuAddForm');
}

function getPrinterDivisions() {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?loadDivisionsComboItem",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "loadDivisionsComboItem"
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            s = '<option value=0>Выберите элемент</option>';
            for (k = 0; k < data.division.length; k++) {
                s += '<option value="' + data.division[k].id + '">' + data.division[k].name + '</option>'
            }
            document.getElementById('printerComboItemList').innerHTML = s;
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function btnOksetPrinterComboItemFormClick() {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?setPrinterComboItem",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "setPrinterComboItem",
            divid: document.getElementById('printerComboItemList').value,
            comboItemId: addComboGroupsItemsStore[addComboGroupsItemsGrid.getNum()].id
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            btnCancelsetPrinterComboItemFormClick();
            getAddComboGroupItems()
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function reportSalonClick() {
    $("#ochert").remove();
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?doReportSalon",
        type: "POST",
        dataType: "html",
        data: {
            actionScript: "doReportSalon"
        }
    }).success(function (data) {
        tmpOtchetCont = data;
        $("body").append('<div id="ochert" class="otchet_div">' + data + '\
            <br /><a id="otchet_print" href="/front/PHP/front.php?ftype=printReportSalon" class="button green"\n\
            href="#" target="_blank">Печать</a><button id="otchet_close" \n\
            class="button red" onmousedown="otchet_close()">Выход</button></div>');
    });
};

function doSelectEmployee() {
    selectEmployeeGrid.clearTable();
    selectEmployeeStore = undefined;
    selectEmployeeStore = new Array();
    selectEmployeeFromGroup();

}

function selectEmployeeFromGroup() {
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?getEmployeesFromGroup",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: "getEmployeesFromGroup"
        }
    }).success(function (data) {
        if (data.rescode == 0) {
            selectEmployeeStore = data.rows;
            tmparray = preperaVisibleArray('employee', selectEmployeeStore);
            selectEmployeeGrid.fillTable(tmparray);
            selectEmployeeGrid.selectRow(0);
            showForm("employeeForm");
            itemFocus = '';
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);

        }
    });
}

function cancelbtnEmployeeClick() {
    closeForm("employeeForm");
    itemFocus = 'shtrihOF';
}

function okbtnEmployeeClick() {
    orderdata.employeeid = selectEmployeeStore[selectEmployeeGrid.getNum()].employeeid;
    $('#employeeSelectDiv').html(selectEmployeeStore[selectEmployeeGrid.getNum()].empname);
    cancelbtnEmployeeClick();
}

function materialsbtnClick() {
    if (ordertabledata[ordergrid.getNum()].isservice == 1 && ordertabledata[ordergrid.getNum()].complex != 1) {
        showForm("MaterialsForm");
        itemFocus = 'shtrihOFmaterials';
        $('#materialsLabelSum').html('Итого:0');
        createrMaterialsMenu();
    } else if (ordertabledata[ordergrid.getNum()].complex == 1) {
        alert('Данная услуга уже имеет расходные материалы!');
    }
}

function cancelbtnMaterialsFormClick() {
    selectedMaterialsGrid.clearTable();
    selectedMaterialsStore = new Array;
    materialsMenu = new Array();
    closeForm("MaterialsForm");
}


function getMaterialsMenu() {
    var tmpmenu = new Array();
    k = 0;
    for (i = 0; i < simplemenu.length; i++) {
        if (simplemenu[i].isservice == 0) {
            tmpmenu[k] = clone(simplemenu[i]);
            k++;
        }
    }
    return tmpmenu;
}

function createrMaterialsMenu() {
    materialsMenu = new Array();
    $('#menudivMaterials').html("");
    var menudivMaterials = document.getElementById("menudivMaterials");
    materialsMenu = getMaterialsMenu();
    showMaterialsMenu()
}

function defineMaterials() {
    for (jj = 0; jj < selectedComboStore.length; jj++) {
        if (selectedComboStore[jj].idcombogroup == comboGroupsStore[comboGroupsGrid.getNum()].id) {
            selectedComboGrid.selectRow(jj);
            return;
        }
    }
}

function createMaterialsButton(id, inner, cl, price) {
    var newBtn;
    newBtn = document.createElement("button");
    newBtn.setAttribute("class", cl);
    newBtn.setAttribute("id", id);
    newBtn.setAttribute("onClick", 'menuBtnClickMaterials(id,1)');
    if (cl != "button white font-black p active") {
        newBtn.innerHTML = "<p>" + inner + "</p>";
    } else {
        newBtn.innerHTML = "<p>" + inner + "</p>";
        newBtn.setAttribute("onClick", "getCalc('countbtnMaterials',id,'Введите количество',null)");
        //        newBtn.setAttribute("onClick", "menuBtnClickMaterials(id,1)");
        var newDiv = document.createElement("div");
        newDiv.setAttribute("class", "pricecell div red");
        newDiv.innerHTML = Math.round(price);
        newBtn.appendChild(newDiv);
    }
    return newBtn;
}

function showMaterialsMenu() {
    $('#menudivMaterials').html("");
    var menudivMaterials = document.getElementById("menudivMaterials");
    for (i = 0; i < materialsMenu.length; i++) {
        if (materialsMenu[i].parentid == 0) {
            if (materialsMenu[i].isgroup == 1) {
                menudivMaterials.appendChild(createMaterialsButton(materialsMenu[i].id, materialsMenu[i].name, "button grey p active"));
            } else {
                menudivMaterials.appendChild(createMaterialsButton(materialsMenu[i].id, materialsMenu[i].name, "button white font-black p active", materialsMenu[i].price));
            }
        }
    }
}

function findButtonPosMaterials(id) {
    for (i = 0; i < materialsMenu.length; i++) {
        if (materialsMenu[i].id == id) {
            return i;
        }
    }
}

function findParentMaterials(id) {
    var pid = 0;
    var menudivMaterials = document.getElementById("menudivMaterials");
    for (i = 0; i < materialsMenu.length; i++) {
        if (materialsMenu[i].id == id) {
            pid = materialsMenu[i].parentid;
            break;
        }
    }
    if (pid == 0) {
        return id;
    } else {
        id = findParent(pid);
        menudivMaterials.appendChild(createMaterialsButton(pid, materialsMenu[findButtonPosMaterials(pid)].name, "button blue p active"));
        return id;
    }
}

function menuBtnClickMaterials(idMenu, c) {
    var count;
    c > 0 ? count = c : count = 1;
    count = parseFloat(count);
    var button = document.getElementById(idMenu);
    var menudivMaterials = document.getElementById("menudivMaterials");
    if (idMenu != 0) {
        if (materialsMenu[findButtonPosMaterials(idMenu)].isgroup == 0) {
            rowcount = selectedMaterialsGrid.getRowCount();
            selectedMaterialsStore[rowcount] = new Object();
            selectedMaterialsStore[rowcount].visibleid = rowcount + 1;
            selectedMaterialsStore[rowcount].id = materialsMenu[findButtonPosMaterials(idMenu)].itemid;
            selectedMaterialsStore[rowcount].name = materialsMenu[findButtonPosMaterials(idMenu)].name;
            selectedMaterialsStore[rowcount].price = materialsMenu[findButtonPosMaterials(idMenu)].price;
            selectedMaterialsStore[rowcount].count = count;
            selectedMaterialsStore[rowcount].printer = materialsMenu[findButtonPosMaterials(idMenu)].printer;
            selectedMaterialsStore[rowcount].summa = Math.round((count * materialsMenu[findButtonPosMaterials(idMenu)].price) * 100) / 100;
            selectedMaterialsStore[rowcount].note = '';
            selectedMaterialsStore[rowcount].status = 'new';
            tmprow = preperaVisibleArray('materials', selectedMaterialsStore)
            selectedMaterialsGrid.fillTable(tmprow);
            columnSumMaterials();
        } else {
            menudivMaterials.innerHTML = "";
            menudivMaterials.appendChild(createMaterialsButton(0, "Все", "button blue p active"));
            findParentMaterials(idMenu);
            menudivMaterials.appendChild(createMaterialsButton(idMenu, materialsMenu[findButtonPosMaterials(idMenu)].name, "button blue p active"));
            for (i = 0; i < materialsMenu.length; i++) {
                if (materialsMenu[i].parentid == idMenu) {
                    if (materialsMenu[i].isgroup == 1) {
                        menudivMaterials.appendChild(createMaterialsButton(materialsMenu[i].id, materialsMenu[i].name, "button grey p active"));
                    } else {
                        menudivMaterials.appendChild(createMaterialsButton(materialsMenu[i].id, materialsMenu[i].name, "button white font-black p active", materialsMenu[i].price));
                    }
                }
            }
        }
    } else {
        menudivMaterials.innerHTML = "";
        for (i = 0; i < materialsMenu.length; i++) {
            if (materialsMenu[i].parentid == 0) {
                if (materialsMenu[i].isgroup == 1) {
                    menudivMaterials.appendChild(createMaterialsButton(materialsMenu[i].id, materialsMenu[i].name, "button grey p active"));
                } else {
                    menudivMaterials.appendChild(createMaterialsButton(materialsMenu[i].id, materialsMenu[i].name, "button white font-black p active", materialsMenu[i].price));
                }
            }
        }
    }
}

function deletebtnMaterialsFormClick() {
    selectedMaterialsStore.splice((selectedMaterialsGrid.getNum()), 1);
    for (i = 0; i < selectedMaterialsStore.length; i++) {
        selectedMaterialsStore[i].visibleid = i + 1;
    };
    tmprow = preperaVisibleArray('materials', selectedMaterialsStore)
    selectedMaterialsGrid.fillTable(tmprow);
    columnSumMaterials();
}

function columnSumMaterials() {
    summa = 0;
    for (i = 0; i < selectedMaterialsStore.length; i++) {
        summa += selectedMaterialsStore[i].summa;
    }
    $('#materialsLabelSum').html('Итого:' + summa);
    return summa;
}

function okbtnMaterialsFormClick() {
    rownum = ordergrid.getNum();
    if (columnSumMaterials() <= ordertabledata[rownum].summa || configArray.materialsSumMoreServiceSum == 1) {
        ordertabledata[rownum].complex = 1;
        ordertabledata[rownum].comboItems = new Array();
        sum = 0;
        iname = '(';
        for (k = 0; k < selectedMaterialsStore.length; k++) {
            ordertabledata[rownum].comboItems[k] = new Object();
            ordertabledata[rownum].comboItems[k].itemid = selectedMaterialsStore[k].id;
            ordertabledata[rownum].comboItems[k].name = selectedMaterialsStore[k].name;
            ordertabledata[rownum].comboItems[k].price = parseInt(selectedMaterialsStore[k].price);
            ordertabledata[rownum].comboItems[k].count = selectedMaterialsStore[k].count;
            ordertabledata[rownum].comboItems[k].printer = selectedMaterialsStore[k].printer;
            ordertabledata[rownum].comboItems[k].summa = parseInt(selectedMaterialsStore[k].price) * selectedMaterialsStore[k].count;
            ordertabledata[rownum].comboItems[k].note = '';
            ordertabledata[rownum].comboItems[k].status = 'new';
            sum += ordertabledata[rownum].comboItems[k].summa;
            iname += ordertabledata[rownum].comboItems[k].name + ',<br>'
        }
        iname = iname.substring(0, iname.length - 5);
        iname += ')';
        ordertabledata[rownum].name += iname;
        tmprow = preperaVisibleArray('ordertable', ordertabledata);
        ordergrid.fillTable(tmprow);
        cancelbtnMaterialsFormClick();
    } else {
        alert('Нельзя продать комплектующие на сумму больше чем сумма услуги!');
    }
}

function findbtnMaterialsFormClick() {
    c = $('#shtrihOFmaterials').val();
    $('#shtrihOFmaterials').val("");
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?selectByShtrih",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'selectByShtrih',
            shtrih: c
        }
    }).success(function (data) {
        //Добавление по штрих коду из локального меню
        if (data.rescode == 0) {
            tmpSelectedMaterial = data.row[0];
            getCalc('countMaterial', null, 'Введите количество', null)
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function addMaterialToSelected(c) {
    rowcount = selectedMaterialsGrid.getRowCount();
    selectedMaterialsStore[rowcount] = new Object();
    selectedMaterialsStore[rowcount].visibleid = rowcount + 1;
    selectedMaterialsStore[rowcount].id = tmpSelectedMaterial.id;
    selectedMaterialsStore[rowcount].name = tmpSelectedMaterial.name;
    selectedMaterialsStore[rowcount].price = tmpSelectedMaterial.price;
    selectedMaterialsStore[rowcount].count = c;
    selectedMaterialsStore[rowcount].summa = c * tmpSelectedMaterial.price;
    selectedMaterialsStore[rowcount].status = 'new';
    selectedMaterialsStore[rowcount].note = '';
    selectedMaterialsStore[rowcount].printer = 0;
    selectedMaterialsStore[rowcount].weight = tmpSelectedMaterial.weight;
    tmpSelectedMaterial = new Array();
    tmprow = preperaVisibleArray('materials', selectedMaterialsStore)
    selectedMaterialsGrid.fillTable(tmprow);
    columnSumMaterials();
}

function getSearchingItem(searchItem){
    var tmpArr=new Array();
    k=0;
    for (i=0;i<simplemenu.length;i++){
        if (simplemenu[i].name.toLowerCase().indexOf(searchItem.toLowerCase())>-1&&simplemenu[i].isgroup!=1){
            tmpArr[k]=clone(simplemenu[i]);
            tmpArr[k].parentid=0;
            k++;
        }
    }
    
    simplemenu=clone(tmpArr);
}

function doBsJournal(){
    showForm("bsJournalForm");
    $('#journalMainDate').val(new Date().toJSON().slice(0, 10));
    $('#intervalSelect').val("30");
    loadMastersIntoBsJournalTable();
    itemFocus="";
}

function cancelbtnBsJournalFormClick(){
    closeForm("bsJournalForm");
    itemFocus="shtrihOF";
}

function   loadMastersIntoBsJournalTable(){
$.ajax({
    async: false,
    url: "/front/PHP/front.php?loadMastersIntoBsJournalTable",
    type: "POST",
    dataType: "json",
    data: {
        actionScript: 'loadMastersIntoBsJournalTable',
        date:$('#journalMainDate').val(),
        interval:$('#intervalSelect').val()
    }
}).success(function (data) {
    //Добавление по штрих коду из локального меню
    if (data.rescode == 0) {
       $("#tableDivJournal").html(data.content);
    } else {
        console.log(data.rescode + ':' + data.resmsg);
        alert(data.rescode + ':' + data.resmsg);
    }
});    
}

function addNewRecordToJournalBtnClick(){
    
    closeForm("bsJournalForm");
    showForm("formAddToBsJournal");
    $("#supInfoAboutClient").val("");
    $('#clientJournalAdd').val("");
    $('#journalAddNote').val("");
    $('#dtStartJournal').val($('#journalMainDate').val());
    $('#dtEndJournal').val($('#journalMainDate').val());
    $('#timeStartJournal').val(new Date().getTime());
    $('#timeEndJournal').val(new Date().getTime());
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?getEmployeeListForJournal",
        type: "POST",
        dataType: "json",
        data: {
            actionScript: 'getEmployeeListForJournal'
        }
    }).success(function (data) {
        //Добавление по штрих коду из локального меню
        if (data.rescode == 0) {
            str="";
            for (i=0;i<data.rows.length;i++){
                str+='<option value="'+data.rows[i].id+'">'+data.rows[i].name+'</option>';
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
    $('#journalEmp').html(str); 
    
    addJournalStore.dtBegin=$('#dtStartJournal').val();
    addJournalStore.employee=document.getElementById('journalEmp').value;
    
    itemFocus="";
}


function formAddToBsJournalCancelBtnClick(){
        addJournalStore=new Array();
        $("#formAddToBsJournalOkBtn").off("mousedown");
        $("#formAddToBsJournalOkBtn").mousedown(function(){
            formAddToBsJournalOkBtnClick();
        });
        closeForm("formAddToBsJournal");
        showForm("bsJournalForm");
        itemFocus="shtrihOF";
}

function bsJournalAddClientBtnClick(){
    closeForm("formAddToBsJournal");
    $("#cancelbtnClient").off("mousedown");
    $("#cancelbtnClient").mousedown(function(){
        cancelClientBtnClick_fromJournal();
    });
    $("#okbtnClient").off("mousedown");
    $("#okbtnClient").mousedown(function(){
        okClientBtnClick_fromJournal();
    });
    $("#okbtnAddClient").off("mousedown");
    $("#okbtnAddClient").mousedown(function(){
        okAddClientFromBsJournal();
    });
    document.getElementById('filterOptions').selectedIndex = 0;
    clientsgrid.clearTable();
    clients = undefined;
    clients = new Array();
    selectClientFromGroup();    
}

function okClientBtnClick_fromJournal() {
    if (clients[clientsgrid.getNum()].isgroup == 0) {
       addJournalStore.clientid=clients[clientsgrid.getNum()].clientid;
       $("#clientJournalAdd").val(clients[clientsgrid.getNum()].clientname);
       $("#supInfoAboutClient").val('Телефон:'+clients[clientsgrid.getNum()].tel);
       cancelClientBtnClick_fromJournal();
    };
} 

function cancelClientBtnClick_fromJournal() {
    closeForm("clientForm");
    showForm("formAddToBsJournal");
    $("#cancelbtnClient").off("mousedown");
    $("#cancelbtnClient").mousedown(function(){
        cancelClientBtnClick();
    });
    $("#okbtnClient").off("mousedown");
    $("#okbtnClient").mousedown(function(){
        okClientBtnClick();
    });
    $("#okbtnAddClient").off("mousedown");
    $("#okbtnAddClient").mousedown(function(){
        okAddClient();
    });
    
    clientsgrid.clearTable();
    clients = undefined;
    clients = new Array();
    itemFocus="";
}

function formAddToBsJournalOkBtnClick(){

addJournalStore.dtBegin=$('#dtStartJournal').val();
addJournalStore.timeBegin=$('#timeStartJournal').val();
addJournalStore.timeEnd=$('#timeEndJournal').val();
addJournalStore.objId=-1;
addJournalStore.employee=document.getElementById('journalEmp').value;
typeJ='salon';
addJournalStore.note=$('#journalAddNote').val();

 $.ajax({
    async: false,
    url: "/front/PHP/front.php?saveRecordToJournal",
    type: "POST",
    dataType: "json",
    data: {
        actionScript: 'saveRecordToJournal',
        type:'insert',
        dtBegin:addJournalStore.dtBegin,
        timeBegin:addJournalStore.timeBegin,
        timeEnd:addJournalStore.timeEnd,
        clientid:addJournalStore.clientid,
        employeeid:addJournalStore.employee,
        objId:addJournalStore.objId,
        note:addJournalStore.note
    }
}).success(function (data) {
    //Добавление по штрих коду из локального меню
    if (data.rescode == 0) {
        if (!$('#fitness').is(':visible')){
            formAddToBsJournalCancelBtnClick();
            loadMastersIntoBsJournalTable();
        }else{
            closeFormFitnessAddToJournal();
            showFitnessTable();
        }
    } else {
        console.log(data.rescode + ':' + data.resmsg);
        alert(data.rescode + ':' + data.resmsg);
    }
});    
}

function showJournalInf(obj){
jid=$(obj).attr('jid');
itemFocus="";
$("#formAddToBsJournalOkBtn").off("mousedown");
$("#formAddToBsJournalOkBtn").mousedown(function(){
    updateJournalRecord(jid);
});
$.ajax({
    async: false,
    url: "/front/PHP/front.php?showJournalRecord",
    type: "POST",
    dataType: "json",
    data: {
        actionScript: 'showJournalRecord',
        jID:jid
    }
}).success(function (data) {
    //Добавление по штрих коду из локального меню
    if (data.rescode == 0) {
        closeForm("bsJournalForm"); 
        showForm("formAddToBsJournal");
        addJournalStore.clientid=data.rows.clientid;
        $("#supInfoAboutClient").val(data.rows.tel);
        $('#clientJournalAdd').val(data.rows.clientname);
        $('#journalAddNote').val(data.rows.note);
        $('#dtStartJournal').val(data.rows.dtstart);
        $('#dtEndJournal').val(data.rows.dtend);
        $('#timeStartJournal').val(data.rows.timestart);
        $('#timeEndJournal').val(data.rows.timeend);
        $.ajax({
            async: false,
            url: "/front/PHP/front.php?getEmployeeListForJournal",
            type: "POST",
            dataType: "json",
            data: {
                actionScript: 'getEmployeeListForJournal'
            }
        }).success(function (data) {
            //Добавление по штрих коду из локального меню
            if (data.rescode == 0) {
                str="";
                for (i=0;i<data.rows.length;i++){
                    str+='<option value="'+data.rows[i].id+'">'+data.rows[i].name+'</option>';
                }
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }
        });
        $('#journalEmp').html(str); 
        $('#journalEmp').val(data.rows.employeeid); 
    } else {
        console.log(data.rescode + ':' + data.resmsg);
        alert(data.rescode + ':' + data.resmsg);
    }
});    
}

function updateJournalRecord(jID){
addJournalStore.dtBegin=$('#dtStartJournal').val();
addJournalStore.timeBegin=$('#timeStartJournal').val();
addJournalStore.timeEnd=$('#timeEndJournal').val();
if ($('#fitness').is(':visible')){
    addJournalStore.objId=document.getElementById('journalEmp').value;
    addJournalStore.employee=-1;
}else{
    addJournalStore.objId=-1;
    addJournalStore.employee=document.getElementById('journalEmp').value;
}
addJournalStore.note=$('#journalAddNote').val();

 $.ajax({
    async: false,
    url: "/front/PHP/front.php?saveRecordToJournal",
    type: "POST",
    dataType: "json",
    data: {
        actionScript: 'saveRecordToJournal',
        type:'update',
        jid:jID,
        dtBegin:addJournalStore.dtBegin,
        timeBegin:addJournalStore.timeBegin,
        timeEnd:addJournalStore.timeEnd,
        clientid:addJournalStore.clientid,
        employeeid:addJournalStore.employee,
        objId:addJournalStore.objId,
        note:addJournalStore.note
    }
}).success(function (data) {
    //Добавление по штрих коду из локального меню
    if (data.rescode == 0) {
        if (!$('#fitness').is(':visible')){
            formAddToBsJournalCancelBtnClick();
            loadMastersIntoBsJournalTable();
        }else{
            closeFormFitnessAddToJournal();
            showFitnessTable();
        }
    } else {
        console.log(data.rescode + ':' + data.resmsg);
        alert(data.rescode + ':' + data.resmsg);
    }
});
}

function cookSb(){
$.ajax({
    async: false,
    url: "/front/PHP/front.php?cookDvs",
    type: "POST",
    dataType: "json",
    data: {actionScript: 'cookDvs'}
}).success(function (data) {
    //Добавление по штрих коду из локального меню
    if (data.rescode == 0) {
            $('#sbDv').html(data.sel);
            
            $('#sbDv input[type=checkbox]').each(function (){
                    if (localStorage.getItem($(this).attr('idDv')+'cookCheckBox')==1){
                       $(this).attr('checked','checked'); 
                    };
             });
            loadCookContent();
    } else {
        console.log(data.rescode + ':' + data.resmsg);
        alert(data.rescode + ':' + data.resmsg);
    }
});
}


function loadCookContent(){
tmpArr=new Array();
i=0;
$('#sbDv input[type=checkbox]').each(function (){
       localStorage.removeItem($(this).attr('idDv')+'cookCheckBox');
});
$('#sbDv input[type=checkbox]:checked').each(function (){
       tmpArr[i]=$(this).attr('idDv');
       localStorage.setItem($(this).attr('idDv')+'cookCheckBox', 1);
       i++;
})
if (tmpArr.length==0){
    tmpArr=0;
}
$.ajax({
    async: false,
    url: "/front/PHP/front.php?cookContent",
    type: "POST",
    dataType: "json",
    data: {actionScript: 'cookContent',subDiv:tmpArr}
}).success(function (data) {
    //Добавление по штрих коду из локального меню
    if (data.rescode == 0) {
        if (data.sel!=''){
            $('#sbDv').html(data.sel);
        }
        $('#cookBodyDiv').html(data.cont);
    } else {
        console.log(data.rescode + ':' + data.resmsg);
        alert(data.rescode + ':' + data.resmsg);
    }
});
}


function cookItem(obj){
    tID=$(obj).attr('tID');
    $("#msg").html('Приготовить блюдо?')
    $("#msg").dialog({
        resizable: false,
        height: 180,
        modal: true,
        buttons: {
            "Да": function () {
                lastCookDiv=$('#sbDv').val();
                $.ajax({
                    async: false,
                    url: "/front/PHP/front.php?cookingItem",
                    type: "POST",
                    dataType: "json",
                    data: {actionScript: 'cookingItem',tid:tID}
                }).success(function (data) {
                    //Добавление по штрих коду из локального меню
                    if (data.rescode == 0) {
                        loadCookContent();
                        $('#sbDv').val(lastCookDiv);
                    } else {
                        console.log(data.rescode + ':' + data.resmsg);
                        alert(data.rescode + ':' + data.resmsg);
                    }
                });
                $(this).dialog("close");
            },
            "Нет": function () {
                $(this).dialog("close");
            }
        }
    });
}

function closeCookForm(){
    $('#sbDv').html("<option value='-1'></option>");
    closeInterfaces();
}






function printStickerBtnClick(){
        $.ajax({
            async: false,
            url: "/front/PHP/front.php?getStickerInf",
            type: "POST",
            dataType: "json",
            data: {actionScript: 'getStickerInf',shtrih:$('#stickerItemShtrih').val()}
        }).success(function (data) {
            //Добавление по штрих коду из локального меню
            if (data.rescode == 0){
                stickerArr=data.arr;
                
                $('#stickerFindedItem').html('Найденный товар:'+data.arr.name);
//                printTCP(data.XML);
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }
        }); 
}
  
function optionBtnClick(){
    showForm('optionsForm');
}

function doSticker(){
    showForm("printStickerForm");
}

function btnCancelprintStickerFormClick(){
    $('#stickerItemShtrih').val('');
    $('#stickerFindedItem').html('Найденный товар:');
    closeForm("printStickerForm");
}

function btnCanceloptionsFormClick(){
    closeForm('optionsForm');
}

function btnOkprintStickerFormClick(){
    str='<Header><order>etiketka</order><itemname>'+stickerArr.name+'</itemname><price>'+stickerArr.price+'</price><barcode>'+stickerArr.mainShtrih+'</barcode></Header>';
    printTCP(str);
}

function doChangeEmployee(){
    showForm('changeEmployeeForm');
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?getEmployee",
        type: "POST",
        dataType: "json",
        data: {actionScript: 'getEmployee'}
    }).success(function (data) {
        if (data.rescode == 0){
            $('#employeeOfOrder').html(data.rows);
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    }); 
}

function changeEmployeeInOrder(){
       $.ajax({
            async: false,
            url: "/front/PHP/front.php?changeEmployee",
            type: "POST",
            dataType: "json",
            data: {actionScript: 'changeEmployee',orderid:cashierbillstore[cashgrid.getNum()].id, empid:$('#employeeOfOrder').val()}
        }).success(function (data) {
            if (data.rescode == 0){
                alert('Сотрудник у счета был изменен!');
                changeFilterClick(currentCashFilter, currentCountCashPages);
            } else {
                console.log(data.rescode + ':' + data.resmsg);
                alert(data.rescode + ':' + data.resmsg);
            }
        });    
}

function btnOkchangeEmployeeFormClick(){
    changeEmployeeInOrder();
    btnCancelchangeEmployeeFormClick();
}

function btnCancelchangeEmployeeFormClick(){
   closeForm('changeEmployeeForm') 
}

function preLoadTable(){
    $.ajax({
        async: false,
        url: "/front/PHP/front.php?preLoadTable",
        type: "POST",
        dataType: "json",
        data: {actionScript: 'preLoadTable'}
    }).success(function (data) {
        if (data.rescode == 0){
            useLocation=data.location;
            protoUseLocation=useLocation;
            if (useLocation==1){
               preTableArray=data.obj;
               preLocArray=data.loc;  
            }else{
               preTableArray=data.obj; 
            }
        } else {
            console.log(data.rescode + ':' + data.resmsg);
            alert(data.rescode + ':' + data.resmsg);
        }
    });
}

function showCalcSeekClientClick(){
    getCalc('calcInSeekClient', null, 'Введите идентификатор', null);
}


