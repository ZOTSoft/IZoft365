<?php
    session_start();
    include('../company/check.php');
    checksessionpassword();
    include('../company/functions.php');
    if (!isset($_SESSION['point'])){
        header("Location: /login.php");
        die;
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Paloma365</title>
<!--<link rel="stylesheet" type="text/css" href="/company/bootstrap/css/bootstrap.css">-->
<link href="CSS/jquery-ui.css" rel="stylesheet" type="text/css">
<link href="CSS/style1.css" rel="stylesheet" type="text/css">
<link href="CSS/officiantframe.css" rel="stylesheet" type="text/css">
<link href="CSS/cashierframe.css" rel="stylesheet" type="text/css">
<link href="CSS/chosebill.css" rel="stylesheet" type="text/css">
<link href="CSS/colorstyles.css" rel="stylesheet" type="text/css">
<link href="CSS/tablecss.css" rel="stylesheet" type="text/css">
<link href="CSS/regcheck.css" rel="stylesheet" type="text/css">
<script src="JS/jquery.js"></script> 
<script src="JS/jquery-ui.js"></script>
<script src="JS/tablejs.js"></script>
<script src="JS/locObjScript.js"></script>
<script src="JS/script1.js"></script>
<script src="JS/fitnes.js"></script>
<script src="JS/posfunctions.js"></script>
<script src="JS/zottig.js"></script>
<script type="text/javascript" src="/company/bootstrap/js/bootstrap-inputmask.min.js"></script>
<script>
        const version=<?=VERSION?>; 
</script>

</head>
 
<body id="mainBody" onload="loadIrest()" class="bgstyle">
    
    
    <div id="msg" title="Уведомление"></div>
    <div onmousedown="formClick('calcdiv')" id="calcdiv" class="calcdiv" style="background: url('logo.png') no-repeat 100% 0%;background-size:220px; display: none;">
        <button id="unloginbtn" class="frontbtn unl button red" onmousedown="noshowcalc=1;logoutExit('logout')">Выход</button>  
        <button class="frontbtn cl button red" onmousedown="noshowcalc=1;logoutExit('exit')">Закрыть</button>
        <button id="reloadbtn" class="frontbtn rel button orange" onmousedown="window.location.reload()">Обновить</button>  
        <button id="urvbtn" class="frontbtn urv button blue">УРВ</button>  
        <!--<button id="fitbtn" class="frontbtn urv button blue">Фитнес</button>-->  
        <div class="contactinfo">Наши телефоны:<br>Сотовый: <span>8-701-111-97-23</span><br>Городской: <span>327-01-74</span></div>
        <div class="apinfo" id="apinfo"></div>
    </div>    
    <div id="chooseInterface" style="display: block; left: 433px; top: 283.5px;"></div>   
    <div id="oficaintframe" class="oficaintframe" style="display: none;">
        <table class="oficiantmain">
            <tbody><tr>
                <td colspan="2" class="headercell div grey">
                    <div class="headerlable hpos1">Сеанс пользователя: <span id="user" class="chstyle input white chpos1" style="white-space: nowrap;overflow: hidden;">fastfood</span></div>
                    <div class="headerlable hpos2">Счет: № <span id="chid" class="chstyle input white chpos2"></span></div>
                    <div class="headerlable hpos3">от</div>
                    <span id="chdt" class="chstyle input white chpos3">05.04.2013 18:41</span>
                    <div id="offtime" class="time">
<!--                        <div class="clock">
                            <ul>
                                <li id="hours">20</li>
                                <li id="point">:</li>
                                <li id="min">04</li>
                                <li id="point">:</li>
                                <li id="sec">37</li>
                            </ul>
                        </div>-->
                    </div>
                </td>
            </tr>                
            <tr>
                <td class="infocell div darkwhite"> 
                    
                    <div class="labelstyles lpos1">Клиент</div>
                    <div id="labelTable" class="labelstyles lpos2">Стол</div>
                    <div class="labelstyles lpos3">Скидка</div>
                    <div id="labelService" class="labelstyles lpos4">Сервис</div>
                    <div id="labelPeopleCount" class="labelstyles lpos5">Кол-во чел.</div>
                    <div class="labelstyles lpos6">Штрих-код</div>
                    <div class="labelstyles lpos7">Итого:</div>
                    <div id="printdiv" class="printdiv input white">0</div>    
                    <input type="text" id="clientdiv" class="clientdiv input white"></div>
                    <div id="tablediv" class="tablediv input white"></div>
                    <div id="servicediv" class="servicediv input white"></div>
                    <div id="salediv" class="salediv input white"></div>
                    <div id="countdiv" class="countdiv input white"></div>
                    <label id="employeeSelectDivLabel" style="
                        position: absolute;  height: 22px;  text-decoration: none;  color: #000;  font-size: 19px;
                        width: 210px;  
                        top: 55px;  left: 225px;
                     ">Выберите сотрудника</label>
                    <div id="employeeSelectDiv" class="employeeSelectDiv input white"></div>
                    <input type="text" id="shtrihOF" class="shtrihOF input white">
                    <button  id="countbtn" class="countbtn button blue edit"></button>
                    <button  id="employeeSelectBtn" class="employeeSelectBtn button blue edit"></button>
                    <button  id="servicebtn" class="servicebtn  button blue edit"></button>
                    <button  id="salebtn" class="salebtn  button blue edit"></button>
                    <button  id="tablebtn" class="tablebtn  button blue edit"></button>
                    <button  id="clientbtn" class="clientbtn  button blue edit"></button>
                    <button  id="shtrihbtnOF" class="shtrihbtnOF  button blue edit"></button>
                    <button  id="backbtn" class="backbtn button blue">Назад</button>
                    <button  id="giftbtn" class="backbtn button blue">Подарок</button>
                    <button  id="printbtn" class="printbtn button green">Печать счета</button>
                    <button  id="exitbtn" class="exitbtn button red">Выход</button>
                    <button id="bsJournalbtn" class="bsJournal button blue">Журнал</button>
                </td>  
                <td rowspan="4" id="menucell" class="menucell div darkwhite">
                    <div id="searchDiv" style="display: none">
                        <input type="text" id="searchField" class="searchField" placeholder="Поиск по наименованию товара" style="position:relative; float:left;">
                        <button id="resetSearch" style="height: 28px;width: 40px;">X</button>
                    </div>
                    <div id="menudiv"></div>
                </td>
            </tr>
            <tr>
                <td id="td1" class="ordercell div darkwhite">
                     <div>
                        <table id="ordertable" class="my-table hovered">
                        <thead>
                            <tr>
                            <th>#</th>
                            <th>Наименование</th>
                            <th>Цена</th>
                            <th>Кол-во</th>
                            <th>Сумма</th>
                            <!--<th class="orderidcell">id</th>-->
                            </tr>                    
                        </thead>
                        <tbody>
                        </tbody>
                        </table>
                        </div>
<!--                    <table id="ordertable" class="table">
                         <tbody><tr>
                             <th>#</th>
                            <th>Наименование</th>
                            <th>Цена</th>
                            <th>Кол-во</th>
                            <th>Сумма</th>
                            <th class="orderidcell">id</th>
                         </tr>                       
                    </tbody></table>-->
                </td>
            </tr>
            <tr>
                <td class="orderbtns div darkwhite">
                    <button  id="repeatbtn" class="button green">Повторить</button>
                    <button  id="deletebtn" class="button red">Удалить</button>
                    <button  id="commentbtn" class="button blue">Примечание</button>
                    <button  id="changePrice" class="button blue">Изм. Цену</button>
                    <button  id="materialsbtn" class="button green">Материалы</button>
                </td>
            </tr>
        </tbody></table>
       <div id="glass" class="glass" style="display: none;"></div>
        <div id="selectdiv" class="selectdiv div darkwhite" style="display: none;">
                        <div style="overflow: auto; position: relative; overflow: auto; height: 80%">
                        <table id="selectgrid" class="my-table hovered">
                        <thead>
                            <tr>
                            <th>#</th>
                            <th>Наименование</th>
                            </tr>                    
                        </thead>
                        <tbody>
                        </tbody>
                        </table>
                        </div>
          <div class="selectbtns">
              <button id="okbtn" class="button green active">ОК</button>
              <button id="cancelbtn" class="button red active">Отмена</button>
              <button id="addbtnNote" class="button blue active">Добавить</button>
          </div>
        </div>
       
       
       <div id="bsJournalForm" class="bsJournalForm div darkwhite" style="display: none; top:1%;left:1%">
            <div style="float:left; position:absolute; top:10px">
                <button id="addNewRecordToJournalBtn"  style="top:10px; lefp:5px;" class="button green active">Создать запись</button>
                <label style="margin-left:255px;font-size: 22px">Записи на </label>
                <input id="journalMainDate" style="position:relative" type="date" value="2013-01-01"  value="" ></input>
                <label style="margin-left:15px;font-size: 22px">Интервал (мин): </label>
                <select id="intervalSelect">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="60">60</option>
                </select>
            </div>
            <div id="tableDivJournal" class="bsTableDivJournal" style="border-color: gray; border: solid 1px;border-bottom: solid 1px;">           
<!--                <table id="bsTableJournal" class="my-table hovered">
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                </table>-->
            </div>
            
            <div class="selectbtns">
                <button id="cancelbtnBsJournalForm" class="button orange active">Закрыть</button>
            </div>
        </div>
       <div id="formAddToBsJournal" class="div darkwhite" style="border:solid 1px; top:5px; left:5px; height:590px; width:790px; display: none; position:absolute;">
            <div style="margin:10px 0 0 250px;">
                 <div style="margin:10px 0 0 10px;">
                   <label>Запись на дату </label>
                   <input id="dtStartJournal" style="position:relative" type="date" value="2013-01-01">
                   <br>
                   <div style="margin:20px 0 0 0px;position:relative">
                       <label>Время с </label>
                       <input id="timeStartJournal" style="margin:0 0 0 20px;position:relative" type="time">     
                       <label style="margin:0 0 0 0px;position:relative">по</label>
                       <input id="timeEndJournal" style="position:relative" type="time">
                   </div>
                </div>
                <div style="margin:10px 0 0 10px;">
                    <label style="position:relative">Клиент:</label>
                    <input id="clientJournalAdd" style="position:relative" readonly="readonly"></input>
                    <button id="bsJournalAddClientBtn" class="clientbtn  button blue edit" style="font-size:7px"></button>
                    <br>
                    <label style="margin:0 0 0 5px;position:relative">Дополнительная информация</label>
                    <br>
                    <input id="supInfoAboutClient" style="margin:0 0 0 0;position:relative;width: 300px;" readonly="readonly"></input>
                </div>
                <div style="margin:10px 0 0 10px;">
                    <label id="labelSelectEmpOrObject" style="position:relative">Сотрудник:</label>
                    <select id="journalEmp" style="position:relative">
                        <option>Выберите сотрудника</option>
                    </select> 
                    <br>
                    <label style="margin:0 0 0 120px;position:relative">Примечание</label>
                    <br> 
                    <textarea id="journalAddNote" style="margin:0 0 0 27px;position:relative;height: 100px;width: 235px;"></textarea>
                </div>
                <div class="selectbtns" style="margin:0 0 0 150px;">
                    <button id="formAddToBsJournalOkBtn"  class="button green active">Сохранить</button>
                    <button id="formAddToBsJournalCancelBtn" class="button red active">Отмена</button>   
                </div>
             </div>
          </div>
<!--       <div id="formAddToBsJournal" class="div darkwhite" style="border:solid 1px; top:5px; left:5px; height:590px; width:790px; display: none; position:absolute;z-index:10 ">
            <div style="margin:10px 0 0 250px;">
                 <div style="margin:10px 0 0 10px;">
                   <label>Запись на дату </label>
                   <input id="dtStartJournal" style="position:relative" type="date" value="2013-01-01">
                   <br>
                   <div style="margin:20px 0 0 0px;position:relative">
                       <label>Время с </label>
                       <input id="timeStartJournal" style="margin:0 0 0 20px;position:relative" type="time">     
                       <label style="margin:0 0 0 0px;position:relative">по</label>
                       <input id="timeEndJournal" style="position:relative" type="time">
                   </div>
                </div>
                <div style="margin:10px 0 0 10px;">
                    <label style="position:relative">Клиент:</label>
                    <input id="clientJournalAdd" style="position:relative" readonly="readonly"></input>
                    <button id="bsJournalAddClientBtn" class="clientbtn  button blue edit" style="font-size:7px"></button>
                    <br>
                    <label style="margin:0 0 0 5px;position:relative">Дополнительная информация</label>
                    <br>
                    <input id="supInfoAboutClient" style="margin:0 0 0 0;position:relative;width: 300px;" readonly="readonly"></input>
                </div>
                <div style="margin:10px 0 0 10px;">
                    <label style="position:relative">Сотрудник:</label>
                    <select id="journalEmp" style="position:relative">
                        <option>Выберите сотрудника</option>
                    </select> 
                    <br>
                    <label style="margin:0 0 0 120px;position:relative">Примечание</label>
                    <br> 
                    <textarea id="journalAddNote" style="margin:0 0 0 27px;position:relative;height: 100px;width: 235px;"></textarea>
                </div>
                <div class="selectbtns" style="margin:0 0 0 150px;">
                    <button id="formAddToBsJournalOkBtn"  class="button green active">Сохранить</button>
                    <button id="formAddToBsJournalCancelBtn" class="button red active">Отмена</button>   
                </div>
             </div>
          </div>-->
       
        <div id="comboMenuForm" class="comboMenuFormStyle div darkwhite" style="display: none;">
             <div class="tableComboGroup" style="border-color: gray; border-right: solid 1px;border-bottom: solid 1px;">           
             <table id="selectComboGroupTable" class="my-table hovered">
             <thead>
                 <tr>
                     <th>#</th>
                     <th>Наименование группы</th>
                     <th>Цена</th>
                 </tr>                    
             </thead>
                 <tbody>
                 </tbody>
            </table>
            <!--<button id="btnSelectLevelGift">Выбрать</buton>-->
           </div>
            <div class="tableComboMenu" style="border-color: gray; border-right: solid 1px;border-bottom: solid 1px;height: 75%"> 
                 <tr>
                 <td id="menucell" class="menucellCombo div darkwhite">
                     <div id="menudivCombo"></div>
                 </td>
                 </tr>
            </div>
            <div class="tableSelectedCombo" style="border-color: gray; border-bottom: solid 1px;"> 
            <table id="selectedCombo" class="my-table hovered">
            <thead><tr>
                    <th>#</th>
                     <th>Выбранные товары</th>
                  </tr>                    
             </thead>
                 <tbody>
                 </tbody>
             </table>
             </div>
            
            <div style="position:absolute;left:1%;top:77%;width:490px;height:60px; border: solid 1px">
                <label id="ComboLabelSum" style="position:absolute;top:20%;left:10%;font-size: 30px"> Итог:0 </label>
            </div>    
            
            <button id="deletebtnComboForm" class="button orange active" style="position:absolute;left:67%;top:77%;width:255px;height:65px;">Удалить</button>
             <div class="selectbtns">
              <button id="okbtncomboMenuForm" class="button green active">ОК</button>
              <button id="cancelbtncomboMenuForm" class="button red active">Отмена</button>
               <button id="countbtncomboMenuForm" class="button blue active">Количество</button>
          </div>
        </div>
       <div id="shtrihSelectForm" class="selectdiv div darkwhite" style="display: none;">
                        <div style="overflow: auto; position: relative; overflow: auto; height: 80%">
                        <table id="shtrihGrid" class="my-table hovered">
                        <thead>
                            <tr>
                            <th>#</th>
                            <th>Наименование</th>
                            <th>Цена</th>
                            </tr>                    
                        </thead>
                        <tbody>
                        </tbody>
                        </table>
                        </div>
          <div class="selectbtns">
              <button id="okbtnShtrih" class="button green active">ОК</button>
              <button id="cancelbtnShtrih" class="button red active">Отмена</button>
          </div>
        </div>
<!--       <div id="clientForm" class="selectGiftForm div darkwhite" style="display: none;">
           <div>
                <label id="labelFilterClient" style="
                 top: 5%;
                 left: 55%;
                 position: absolute;
                 font-size: 20px;">Выберите фильтр:</label>
                <select id="filterOptions" style="top: 15%;
                                                 position: absolute;
                                                 left: 55%;
                                                 height: 50px;
                                                 width: 250px;
                                                 font-size: 32px;">
                      <option value="1">По имени</option>
                      <option value="2">По карте</option>
                      <option value="3">По телефону</option>
                      <option value="4">По email</option>
                      </select>
                 <input id="filterClientInput" type="text" val="" placeholder="Введите фильтр" style="left: 55%;width: 245px;top: 40%;">
                 <div class="selectbtnsTableClientFilter">
                 <button  id="okbtnClientFilter" class="button green active">Поиск</button>
                 <button  id="cancelbtnClientFilter" class="button red active">Сброс</button>            
                </div>
                <button  id="addNewClient" class="button green active" style="
                position: absolute;
                height: 40px;
                width: 255px;
                top: 65%;
                left: 55%;">Добавить клиента</button> 
           </div>     
            <div class="tableClientList"> 
                <table id="clientL" class="my-table hovered">
                 <h3>Вывыфвыывфывфыв</h3>
                 <thead><tr>
                         <th>#</th>
                         <th>Выберите клиента</th>
                      </tr>                    
                 </thead>
                     <tbody>
                     </tbody>
                 </table>
                    <button onClick="addRowToSelectedItems(giftItemsarray[getMytableRowNum('selectGiftItem')])" id="btnSelectItemGift">Выбрать</buton>
                </div>
 
            <div class="selectbtnsTableClient">
            <button  id="okbtnClient" class="button green active">ОК</button>
            <button  id="cancelbtnClient" class="button red active">Отмена</button>            
          </div>
        </div>-->
       
       <div id="employeeForm" class="selectdiv div darkwhite" style="display: none;">
                    <div style="overflow: auto; position: relative; overflow: auto; height: 80%">
                        <table id="employeeGrid" class="my-table hovered">
                        <thead>
                            <tr>
                            <th>#</th>
                            <th>Наименование</th>
                            </tr>                    
                        </thead>
                        <tbody>
                        </tbody>
                        </table>
                    </div>
                    <div class="selectbtns">
                        <button id="okbtnEmployee" class="button green active">ОК</button>
                        <button id="cancelbtnEmployee" class="button red active">Отмена</button>
                    </div>
        </div>
       
       
       <div id="MaterialsForm" class="comboMenuFormStyle div darkwhite" style="display: none;">
           <input type="text" id="shtrihOFmaterials" class="shtrihOFmaterials input white">
           <button id="findbtnMaterialsForm" class="button green active" style="position: absolute;
            position: absolute;
            top: 3px;
            left: 266px;
            width: 100px;
            height: 40px;
            ">ОК</button>
            <div class="tableMaterials" style="border-color: gray; border-right: solid 1px;border-bottom: solid 1px;height: 69%"> 
                 <tr>
                 <td id="menucell" class="menucellMaterials div darkwhite">
                     <div id="menudivMaterials"></div>
                 </td>
                 </tr>
            </div>
            <div class="tableSelectedCombo" style="border-color: gray; border-bottom: solid 1px;"> 
            <table id="selectedMaterials" class="my-table hovered">
            <thead><tr>
                    <th>#</th>
                    <th>Выбранные товары</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                  </tr>                    
             </thead>
                 <tbody>
                 </tbody>
             </table>
             </div>
            
            <div style="position:absolute;left:1%;top:77%;width:490px;height:60px; border: solid 1px">
                <label id="materialsLabelSum" style="position:absolute;top:20%;left:10%;font-size: 30px"> Итог:0 </label>
            </div>    
            
            <button id="deletebtnMaterialsForm" class="button orange active" style="position:absolute;left:67%;top:77%;width:255px;height:65px;">Удалить</button>
             <div class="selectbtns">
              <button id="okbtnMaterialsForm" class="button green active">ОК</button>
              <button id="cancelbtnMaterialsForm" class="button red active">Отмена</button>
          </div>
        </div>
       
       
       <div id="noteAddForm" class="selectGiftForm div darkwhite" style="display: none;">
            <label style="position: absolute;left: 35%;top: 10%;">Введите новое примечание</label>
            <br>
            <div class="formAddNote" style="position: relative;left: 20%;top: 15%;">
              <style>
                  .formAddNote label{
                      float: left;
                      padding-left: 1em;
                      padding-top: 6px;
                      display: block;
                      clear: both;
                  }
                 .formAddNote input{
                      margin:.33em 0 0 13em;
                      display: table;
                      position:relative;
                      width:207px;
                  }
              </style>    
              <label>
                Примечание:
              </label><input type="text" value="" id="newNote" ></input>
          </div>
          <div class="selectbtnsTableClient">
                <button  id="okbtnAddNote" class="button green active">ОК</button>
                <button  id="cancelbtnAddNote" class="button red active">Отмена</button>            
          </div>
        </div>


<!-- 
    <div id="tableForm" class="selectGiftForm div darkwhite" style="display: none;">
            <div class="tableClientList"> 
                <table id="tableL" class="my-table hovered">
                 <thead><tr>
                         <th>#</th>
                         <th>Выберите помещение</th>
                      </tr>                    
                 </thead>
                     <tbody>
                     </tbody>
                 </table>                   
                </div>
                <div class="tableClientSelected"> 
                <table id="tableS" class="my-table hovered">
                <thead><tr>
                         <th>#</th>
                         <th>Выберите стол</th>
                      </tr>                    
                 </thead>
                     <tbody>
                     </tbody>
                 </table>
            </div>
            <div class="selectbtnsTableClient">
            <button  id="okbtnTable" class="button green active">ОК</button>
            <button  id="cancelbtnTable" class="button red active">Отмена</button>            
          </div>
        </div> 
-->



       <div id="tableForm" class="selectGiftForm div darkwhite" style="display: none;padding-right: 5px">
            <div id="tableLocationtList" style="/*border:solid 1px;*/ height: 85px;margin-top: 15px;overflow-x: scroll;white-space: nowrap;"> 
                
            </div>    
            <div id="tableObjectsList" style="border-top:solid 1px; height: 65%;overflow:auto; /*margin-top: 15px*/"> 
               
            </div>
            <!--class="selectbtnsTableClient"--> 
           <div style="position:absolute; height:65px; width: 40%; display: inline-table;  padding-left: 5px;">            
                <button  id="cancelbtnTable" class="button red active" style="height: 55px;width: 150px;">Отмена</button>            
            </div>
        </div> 


       <div id="selectGiftForm" class="selectGiftForm div darkwhite" style="display: none;">
           <div class="tableGiftLevel" style="border-color: gray; border-right: solid 1px;border-bottom: solid 1px;">           
            <table id="selectLevelGift" class="my-table hovered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Наименование уровня</th>
                </tr>                    
            </thead>
                <tbody>
                </tbody>
           </table>
           <!--<button id="btnSelectLevelGift">Выбрать</buton>-->
          </div>
           <div class="tableGiftItem" style="border-color: gray; border-right: solid 1px;border-bottom: solid 1px;"> 
           <table id="selectGiftItem" class="my-table hovered">
            <thead><tr>
                    <th>#</th>
                    <th>Наименование подарков</th>
                 </tr>                    
            </thead>
                <tbody>
                </tbody>
            </table>
               <!--<button onClick="addRowToSelectedItems(giftItemsarray[getMytableRowNum('selectGiftItem')])" id="btnSelectItemGift">Выбрать</buton>-->
           </div>
           <div class="tableGiftSelected" style="border-color: gray; border-bottom: solid 1px;"> 
           <table id="selectedGiftItems" class="my-table hovered">
           <thead><tr>
                   <th>#</th>
                    <th>Выбранные подарки</th>
                 </tr>                    
            </thead>
                <tbody>
                </tbody>
            </table>
            </div>
          <div class="selectbtnsGift">
            <button  id="okbtnGift" class="button green active">ОК</button>
            <button  id="cancelbtnGift" class="button red active">Отмена</button>            
          </div>
           <div id="userPoints" style="top: 90%;position: absolute;left: 45%;height: 20px;width: 185px;"></div>
           <div id="userPoints" style="top: 90%;position: absolute;left: 75%;height: 65px;width: 100px;">
               <button  id="deletebtnGift" class="button red active">Удалить</button>
           </div>
        </div>
    </div>
    <div id="cashierframediv" class="cashierframediv" style="display: none;">   
        <table class="cashiertable">    
            <tbody><tr>
                <td class="filterbtnsC div grey" colspan="2">     
                <div  id="cashierClock"class="datetimecashier">
<!--                        <div  class="clock">
                            <ul>
                                <li id="Date"></li>
                                <br>
                                <li id="hours2">17</li>
                                <li>:</li>
                                <li id="min2">42</li>
                                <li>:</li>
                                <li id="sec2">29</li>
                            </ul>
                        </div>-->
                    </div>               
                    <button  id="btnchangeOpen" class="btnchangeOpen button green disabled" disabled="disabled" style="font-size: 15px;">Открыть Смену</button>
                    <button  id="btnchangeClose" class="btnchangeClose button red disabled" style="font-size: 15px;">Закрыть Смену</button>
                    <button peshey='pay'  id="btnchangePayed" class="filterbtn fbtnpos1 button blue">Оплаченные</button>
                    <button peshey='nopay'  id="btnchangeNotPayed" class="filterbtn fbtnpos2 button blue">Неоплаченные</button>
                    <button peshey='all'  id="btnchangeAll" class="filterbtn fbtnpos3 button blue">Все</button>
                    <button  id="optionBtn" class="optionBtnC button orange" style="font-size: 15px;">Доп. Опции </button>
                    <!--<div style="bottom: 10%;left:10%;z-index: 10000000;position: absolute; border:solid 1px; height:30px;width:30px">12312312</div>-->
                            
        </div> 
                    <div class="headercashiername hposС1">Сеанс пользователя: <span id="userCash" class="cashiername input white chposС1"></span></div>                      
                </td>
            </tr
            <tr>
                <td class="ordersgridC div darkwhite">
                        <div>
                        <table id="ordertableC" class="my-table hovered">
                        <thead>
                            <tr>
                                        <!--<th>Блок</th>-->
                                        <th>Статус</th>
                                        <th>Номер</th>
                                        <th>Вид Оплаты</th>
                                        <th>Стол</th>
                                        <th>Итого</th>
                                        <th>Клиент</th>
                                        <th>Сотрудник</th>
                            </tr>                    
                        </thead>
                        <tbody>
                        </tbody>
                        </table>
                        </div>
<!--                    <table id="ordertableC" class="table">
                         <tbody><tr>
                            <th>Блок</th>
                            <th>Статус</th>
                            <th>Номер</th>
                            <th>Вид Оплаты</th>
                            <th>Стол</th>
                            <th>Итого</th>
                            <th>Клиент</th>
                            <th>Официант</th>
                         </tr>                       
                    </tbody></table>                -->
                </td>
                <td class="reportsC div darkwhite" style="overflow:auto">
                    <div class="labelstyles reportlabelpos">Отчеты:</div>
                    <button  id="printbtnC" class="printbtnC button green">Оплата</button>
                    <button  id="showOrderFromCash" class="viewebtnC button blue" style="font-size: 15px;">Просмотреть счет</button>
                    <button  id="unblockbtn" class="unlockbtnC button orange" style="font-size: 15px;">Разблокировать счет</button>
                    <div style="position:absolute;top:170px;margin-left: 20px">
                        <button id="reportAkt"       class="reportsbtnC  button blue" style="font-size: 15px;">Акт Реализации</button>
                        <button id="reportPoschetam" class="reportsbtnC  button blue" style="font-size: 15px;">Отчет по счетам</button>
                        <button id="reportItog"      class="reportsbtnC  button blue" style="font-size: 15px;" >Итоговый отчет</button>
                        <button id="reportRefuse"    class="reportsbtnC  button blue" style="font-size: 15px;">Отчет по отказам</button>
                        <button id="reportRefuse_and_orders"  class="reportsbtnC  button blue" style="font-size: 15px;">Заказы и отказы</button>
                        <button id="reportXreport"   class="reportsbtnC  button blue" style="font-size: 15px;">Х отчет</button>
                        <button id="reportSalon"     class="reportsbtnC  button blue" style="font-size: 15px;">Отчет салон</button>
                        <button id="remainSee"       class="reportsbtnC  button blue" style="font-size: 15px;">Материальная ведомость</button>
                    </div>
                    <!--<button onmousedown="" id="returnbtn" class="returnbtnC button orange">Возврат</button>-->
                </td>
            </tr>
            <tr>
                <td class="statuscashier div darkwhite" colspan="2">  
                    <div id="changeInfo" style="float:left; "></div>
                    <br>
                        <div id="pagginationOptions" style="float:left" >
                            <button id="prevPag" class="button blue" style=" margin-left: 10px">Предыдущая</button>
                            <label>Страница: </label>
                            <input id="pageNumCPag" style=" width:50px; margin-left: 10px; height:20px" type="numeric" value="1">
                            <label id="labelPag" style=" width:70px; margin-left: 10px;margin-left: 5em"> из 20</label>
                            <button id="nextPag" class="button blue" style="  margin-left: 10px"> Следующая</button>
                        </div>
                    <button id="exitbtnC" class="exitbtnC button red">Выход</button>
                </td>
            </tr>
        </tbody>
        </table>
        
        
        
        
        <div id="glass2" class="glass" style="display: none;"></div>
        
        <div id="optionsForm" class="selectGiftForm div darkwhite" style="display: none;">
            <button  id="printStickerBtn" class="printStickerBtn button green" style="font-size: 15px;display:block">Печать этикеток</button>
            <button  id="stopListBtn" class="stopListbtnC button green" style="font-size: 15px;display:block">Стоп лист</button>
            <button  id="divideOrderBtn" class="divideOrderbtnC button green" style="font-size: 15px;display:block">Разбиение счета</button>
            <button  id="returnBtn" class="returnbtnC button green" style="font-size: 15px;display:block">Возврат</button>
            <button  id="comboCreateBtn" class="comboCreateBtnC button green" style="font-size: 15px;display:block">Комбо меню</button>
            <button  id="changeEmployee" class="changeEmployeeC button green" style="font-size: 15px;display:block">Сменить сотрудника</button>
            
            <div class="selectbtnsTableClient">
                <button  id="btnCanceloptionsForm" class="button red active" style="width: 310px;">Отмена</button>            
            </div>
        </div>
        
        <div id="printStickerForm" class="selectGiftForm div darkwhite" style="display: none;">
                <label style="position: absolute;left: 35%;top: 10%;">Печать этикеток</label>
                <br>
                <div class="formAddClient" style="position: relative;left: 20%;top: 15%;">
                 <style>
                        .formAddClient label{
                                  float: left;
                                  padding-left: 1em;
                                  padding-top: 6px;
                                  display: block;
                                  clear: both;
                        }
                        .formAddClient input{
                             margin:.33em 0 0 13em;
                             display: table;
                             position:relative;
                             width:207px;
                         }   
                  </style>    
                  <label>Введите штрих-код товара:</label>
                  <input type="text" value="" id="stickerItemShtrih" ></input>  
                  <button  id="findItemSticker" class="button green active" style="margin-top: 10px;margin-left:360px;height:40px;width:100px;">Найти</button>
                  <br>
                  <label id="stickerFindedItem">Найденный товар:</label>
                  <br>
                  <label>Введите кол-во этикеток:</label>
                  <input type="text" value="" id="stickerItemCount" ></input>  
                  </div>
            <div class="selectbtnsTableClient">
                <button  id="btnOkprintStickerForm" class="button green active">Печать</button>
                <button  id="btnCancelprintStickerForm" class="button red active">Отмена</button>            
          </div>
         </div>
        
        
        <div id="changeEmployeeForm" class="selectGiftForm div darkwhite" style="display: none;">
                <label style="position: absolute;left: 35%;top: 10%;">Смена сотрудника у счета</label>
                <br>
                <div class="formAddClient" style="position: relative;left: 20%;top: 15%;">
                 <style>
                        .formAddClient label{
                                  float: left;
                                  padding-left: 1em;
                                  padding-top: 6px;
                                  display: block;
                                  clear: both;
                        }
                        .formAddClient input{
                             margin:.33em 0 0 13em;
                             display: table;
                             position:relative;
                             width:207px;
                         }   
                  </style>
                  <label>
                    Выберите сотрудника:
                  </label><select id="employeeOfOrder" >

                  </select>
                  </div>
            <div class="selectbtnsTableClient">
                <button  id="btnOkchangeEmployeeForm" class="button green active">Сменить</button>
                <button  id="btnCancelchangeEmployeeForm" class="button red active">Отмена</button>            
          </div>
         </div>
        
        
        <div id="comboMenuAddForm" class="comboMenuFormStyle div darkwhite" style="display: none;">
             <div class="tableComboGroup" style="border-color: gray; border-bottom: solid 1px;">           
             <table id="comboMenuAddGrid" class="my-table hovered">  
             <thead> 
                 <tr>
                     <th>#</th>
                     <th>Комбоменю</th> 
                 </tr>                    
             </thead>
                 <tbody>
                 </tbody> 
            </table>
            <!--<button id="btnSelectLevelGift">Выбрать</buton>-->
           </div>
            
            
            <div class="tableComboMenu" style="border-color: gray; border-right: solid 1px;border-bottom: solid 1px;border-left: solid 1px;"> 
            <table id="comboGroupAddGrid" class="my-table hovered">
             <thead>
                 <tr>
                     <th>#</th> 
                     <th>Комбо группы</th>
                 </tr>                    
             </thead>
                 <tbody>
                 </tbody>
            </table>     
            </div>
    
            <div id="formPropertiesCombo" style="border-color: gray; border-bottom: solid 1px; top:1px; left:526px;width:33%; height:26%; position:absolute; border-color: gray;">
                <label id="minCountComboGroup" style="position:absolute; top: 1px;left: 1%;"></label>
                <label id="maxCountComboGroup" style="position:absolute; top: 20px;left: 1%;"></label>
                <label id="priceComboGroup"    style="position:absolute; top: 40px;left: 1%;"></label>
                <div style="position: absolute;left: 1%;top: 60px;width: 195px;overflow:auto;height: 55px;">
                    <label id="defaultComboGroupItem" ></label>
                </div>
                <button id="setDefaultComboItem" style="position: absolute;left: 180px;top: 115px;width: 70px;">Выбрать</button>
            </div>
            
                   
            
            <div class="tableComboItemAdd" style="border-color: gray; border-bottom: solid 1px; border-left: solid 1px;"> 
            <table id="comboItemAddGrid" class="my-table hovered">
            <thead><tr>
                    <th>#</th>
                    <th>Элементы группы</th>
                  </tr>                    
             </thead>
                 <tbody>
                 </tbody>
             </table>
             </div>
            <label id="comboPriceLabel" style="position:absolute;left:10%;top:80%;width:535px;height:60px;"></label>
             <div style="position:absolute;left:31%;top:88%;width:535px;height:60px; border: solid 1px">
                 <div style="position: absolute;top: 1%;left: 10%;width: 210px;">
                    <button id="btnaddComboGroup" class="button green active">Добавить</button>
                    <button id="btndeleteComboGroup" class="button red active">Удалить</button>
                    <button id="btnchangeComboGroup" class="button orange active">Изменить</button>
                 </div>
                 <div style="position:absolute;top:1%;left:60%">
                    <button id="btnaddComboGroupItem" class="button green active">Добавить</button>
                    <button id="btndeleteComboGroupItem" class="button red active">Удалить</button>
                    <button id="btnchangeComboGroupItem" class="button orange active">Принтер</button>
                 </div>
            </div>
             <div class="selectbtnsTableClient" style="position:absolute; left:1%">
              <!--<button id="btnComboMenuAddOk" class="button green active">ОК</button>-->
              <button id="btnComboMenuAddCancel" class="button red active">Закрыть</button>
            </div>
        </div>
         <div id="setPrinterComboItemForm" class="selectGiftForm div darkwhite" style="display: none;">
            <label style="position: absolute;left: 35%;top: 10%;">Выберите подразделение принтера для печати</label>
            <br>
                        <div class="formAddClient" style="position: relative;left: 20%;top: 15%;">
                         <style>
                              .formAddClient label{
                                  float: left;
                                  padding-left: 1em;
                                  padding-top: 6px;
                                  display: block;
                                  clear: both;
                              }
                             .formAddClient select{
                                  margin:.33em 0 0 13em;
                                  display: table;
                                  position:relative;
                                  width:207px;
                              }
                              .formAddClient select{
                                  margin: .33em 0 0 18.5em;
                                  width: 210px;
                              }
                          </style>    
                          <label>
                            Выберите подразделение:
                          </label><select id="printerComboItemList" >

                          </select>
                      </div>
            <div class="selectbtnsTableClient">
                <button  id="btnOksetPrinterComboItemForm" class="button green active">ОК</button>
                <button  id="btnCancelsetPrinterComboItemForm" class="button red active">Отмена</button>            
          </div>
         `</div>    
        <div id="setDefaultComboItemForm" class="selectGiftForm div darkwhite" style="display: none;">
            <label style="position: absolute;left: 35%;top: 10%;">Выберите элемент по умолчанию</label>
            <br>
                        <div class="formAddClient" style="position: relative;left: 20%;top: 15%;">
                         <style>
                              .formAddClient label{
                                  float: left;
                                  padding-left: 1em;
                                  padding-top: 6px;
                                  display: block;
                                  clear: both;
                              }
                             .formAddClient select{
                                  margin:.33em 0 0 13em;
                                  display: table;
                                  position:relative;
                                  width:207px;
                              }
                              .formAddClient select{
                                  margin: .33em 0 0 18.5em;
                                  width: 210px;
                              }
                          </style>    
                          <label>
                            Выберите элемент группы по умолчанию:
                          </label><select id="defaultComboItemList" >

                          </select>
                      </div>
            <div class="selectbtnsTableClient">
                <button  id="btnOksetDefaultComboItemForm" class="button green active">ОК</button>
                <button  id="btnCancelsetDefaultComboItemForm" class="button red active">Отмена</button>            
          </div>
         </div>       
         <div id="addComboGroupForm" class="selectGiftForm div darkwhite" style="display: none;">
            <label style="position: absolute;left: 35%;top: 10%;">Введите информацию о новой группе</label>
            <br>
                        <div class="formAddClient" style="position: relative;left: 20%;top: 15%;">
                          <style>
                              .formAddClient label{
                                  float: left;
                                  padding-left: 1em;
                                  padding-top: 6px;
                                  display: block;
                                  clear: both;
                              }
                             .formAddClient input{
                                  margin:.33em 0 0 13em;
                                  display: table;
                                  position:relative;
                                  width:207px;
                              }                             
                          </style>    
                          
                          <label>
                            Наименование группы:
                          </label><input type="text" value="" id="groupName" ></input>  
                          <label>
                            Цена:
                          </label><input type="number" value="" id="groupPrice" ></input>        
                          <label>
                            Минимальное количество:
                          </label><input type="number" value="" id="groupMinCount" ></input>        
                          <label>
                            Максимальное количество:
                          </label><input type="number" value="" id="groupMaxCount" ></input>        
                      </div>
            <div class="selectbtnsTableClient">
                <button  id="btnOkAddComboGroupForm" class="button green active">ОК</button>
                <button  id="btnCancelAddComboGroupForm" class="button red active">Отмена</button>            
          </div>
        </div>
        
        <div id="addComboGroupItemForm" class="selectGiftForm div darkwhite" style="display: none;">
                   <div class="tableClientList" style="border-color: gray; border-right: solid 1px;border-bottom: solid 1px;">           
                    <table id="comboMenuGroupsItemsSelectTable" class="my-table hovered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Выберите товар</th>
                        </tr>                    
                    </thead>
                        <tbody>
                        </tbody> 
                   </table>
                  </div>                   
                  <div class="tableClientSelected" style="border-color: gray; border-bottom: solid 1px;"> 
                   <table id="selectedComboMenuGroupsItemsSelectTable" class="my-table hovered">
                   <thead><tr>
                            <th>#</th>
                            <th>Выбранные комбо-элементы</th>
                            <th>Цена</th>
                         </tr>                     
                    </thead>
                        <tbody>
                        </tbody>
                    </table>
                    </div>   
            
                  <button  id="deletebtnSelectedComboItems" class="button red active" style="position:absolute;top:80%;left:86%">Удалить</button>
                   
                  <div class="selectbtnsGift">
                    <button  id="okbtnaddComboGroupItem" class="button green active">Готово</button>
                    <button  id="cancelbtnaddComboGroupItem" class="button red active">Отмена</button>            
                  </div>
         </div>
        
        
        <div id="stopListForm" class="selectGiftForm div darkwhite" style="display: none;">
                   <div class="tableClientList" style="border-color: gray; border-right: solid 1px;border-bottom: solid 1px;">           
                    <table id="menuGroup" class="my-table hovered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Выберите товар</th>
                        </tr>                    
                    </thead>
                        <tbody>
                        </tbody>
                   </table>
                   <!--<button id="btnSelectLevelGift">Выбрать</buton>-->
                  </div>                   
                   <div class="tableClientSelected" style="border-color: gray; border-bottom: solid 1px;"> 
                   <table id="selectedMenuItems" class="my-table hovered">
                   <thead><tr>
                            <th>#</th>
                            <th>Товары в стоп листе</th>
                         </tr>                    
                    </thead>
                        <tbody>
                        </tbody>
                    </table>
                    </div>
                  
                  <div class="selectbtnsGift">
                    <button  id="okbtnStopList" class="button green active">Готово</button>
                    <!--<button onmousedown="" id="cancelbtnStopList" class="button red active">Отмена</button>-->            
                  </div>
                   <div style="top: 90%;position: absolute;left: 75%;height: 65px;width: 100px;">
                            <button  id="deletebtnStopList" class="button red active">Удалить</button>
                  </div>
         </div>
            <div id="divideOrderForm" class="selectGiftForm div darkwhite" style="display: none;">
                <label id="divideInfoLabel">#Счет</label>
                       <div class="tableClientList" style="border-color: gray; border-right: solid 1px;border-bottom: solid 1px;">   
                        
                        <table id="dividingOrder" class="my-table hovered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th style="width: 170px;">Товар</th>
                                <th>Цена</th>
                                <th>Кол-во</th>
                                <th>Сумма</th>
                            </tr>                    
                        </thead>
                            <tbody>
                            </tbody>
                       </table>
                       <!--<button id="btnSelectLevelGift">Выбрать</buton>-->
                      </div>                   
                       <div class="tableClientSelected" style="border-color: gray; border-bottom: solid 1px;"> 
                       <table id="dividedOrder" class="my-table hovered">
                       <thead><tr>
                                <th>#</th>
                                <th style="width: 170px;">Товар</th>
                                <th>Цена</th>
                                <th>Кол-во</th>
                                <th>Сумма</th>
                             </tr>                    
                        </thead>
                            <tbody>
                            </tbody>
                        </table>
                        </div>
                    <label id="divideSumOne" style="position: absolute;top: 80%; left: 35%;">Сумма1</label>
                    <label id="divideSumTwo" style="position: absolute;top: 80%; left: 85%;">Сумма2</label>
                      <div class="selectbtnsGift">
                        <button  id="okbtnDivideOrder" class="button green active">Готово</button>
                        <button  id="cancelbtnDivideOrder" class="button red active">Отмена</button>            
                      </div>
                       <div style="top: 90%;position: absolute;left: 75%;height: 65px;width: 100px;">
                                <button  id="deletebtnDivideOrder" class="button red active">Удалить</button>
                      </div>
             </div>
       
        <div id="orderSelectReturnForm" class="selectdiv div darkwhite" style="display: none;">
                    <label style="position: absolute;top: 10%;left: 5%;">Выберите дату:</label>
                    <input id="dateReturnOrder" type="date" value="2013-01-01" style="top: 15%;left: 5%;"/> 
                    <label style="position: absolute;top: 30%;left: 5%;">Выберите номер счета:</label>
                    <input id="numReturnOrder" type="text" value="" style="top: 35%;left: 5%;"/> 
                    <div class="selectbtns">
                        <button id="okbtnSelectReturnOrder" class="button green active">ОК</button>
                        <button id="cancelbtnSelectReturnOrder" class="button red active">Отмена</button>
                    </div>
        </div>
        <div id="returnForm" class="selectGiftForm div darkwhite" style="display: none;">
                <label id="returnInfoLabel">#Счет</label>
                       <div class="tableClientList" style="border-color: gray; border-right: solid 1px;border-bottom: solid 1px;">   
                        
                        <table id="returnOrder" class="my-table hovered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th style="width: 170px;">Товар</th>
                                <th>Цена</th>
                                <th>Кол-во</th>
                                <th>Сумма</th>
                            </tr>                    
                        </thead>
                            <tbody>
                            </tbody>
                       </table>
                       <!--<button id="btnSelectLevelGift">Выбрать</buton>-->
                      </div>                   
                       <div class="tableClientSelected" style="border-color: gray; border-bottom: solid 1px;"> 
                       <table id="selectReturnOrder" class="my-table hovered">
                       <thead><tr> 
                                <th>#</th>
                                <th style="width: 170px;">Товар</th>
                                <th>Цена</th>
                                <th>Кол-во</th>
                                <th>Сумма</th>
                             </tr>                    
                        </thead>
                            <tbody>
                            </tbody>
                        </table>
                        </div>    
                    <label id="returnSumOne" style="position: absolute;top: 80%; left: 35%;">Сумма1</label>
                    <label id="returnSumTwo" style="position: absolute;top: 80%; left: 85%;">Сумма2</label>
                      <div class="selectbtnsGift">
                        <button  id="okbtnReturn" class="button green active">Готово</button>
                        <button  id="cancelbtnReturn" class="button red active">Отмена</button>            
                      </div>
                       <div style="top: 90%;position: absolute;left: 75%;height: 65px;width: 100px;">
                                <button  id="deletebtnReturn" class="button red active">Удалить</button>
                      </div>
             </div>   
    </div>
    <div id="chosebill" class="chosebill" style="display: none;">
        <table class="billtable">    
            <tbody><tr>
                <td class="topbtnsCB div grey">
                    <button  id="newbillbtn" class="newbillbtn button green">Новый Счет</button>
                    <div class="headerlable hpos1">Сеанс пользователя: <span id="userOf" class="chstyle input white chpos1">officiant</span></div>
                    <button  id="showOrderFromOff" class="viewbtnCB button blue">Просмотреть счет</button>
                    <button  id="exitbtnCB" class="exitbtnCB button red">Выход</button>
                </td>
            </tr>
            <tr>
                <td class="ordersgridCB div darkwhite" style="overflow:auto">
                    <div>
                        <table id="billsorder" class="my-table hovered">
                        <thead>
                            <tr>
                                <th>№</th>
                                <th>Статус</th>
                                <th>Номер</th>
                                <th>Сотрудник</th>
                                <th>Стол</th>
                                <th>Сумма</th>
                                <th>Клиент</th>
                            </tr>                    
                        </thead>
                        <tbody>
                        </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </tbody></table>
    </div>
    
        <div id="regcheckForm" style="display: none;">
         <div id="alertdiv" style="display: none;"></div>
            <label>Ввод штрихкодов чеков:</label>
            <button class="bg red active hover" id="exitbtnRegCheck">Выход</button>
            <button class="bg forestGreen active hover" id="reportbtnRegCheck">Отчет</button>
            <div class="headdiv">
                <input id="shinput" type="text" val="" placeholder="Введите штрих код">
            </div>
            <label id="labelRegCheck" style="">Список ваших счетов:</label>
            <div class="bodydiv">                
                <table id="tableRegCheck" class="my-table hovered">
                <thead><tr>
                         <th>#</th>
                         <th>Дата создания</th>
                         <th>Дата регистрации</th>
                         <th>Номер</th>
                         <th>Сумма</th>
                         <th>Содержимое<br> Товар цена х кол-во = сумма</th>
                      </tr>                    
                 </thead>
                 <tbody>
                 </tbody>  
                </table>
            </div>
        </div> 
    
    
    <div id="cookInterface" style="display: none;">
            <label id="cookName" >Сотрудник</label>
            <div style="margin-top: 25px;">
                  <label style="float: left; margin:0 0 0 50px;"> Подразделение:</label>
                  <div id="sbDv"> </div>
                   
<!--                  <select id="sbDv" style="margin-left: 20px;font-size: 40px;width: 350px;">
                      <option value="-1"></option>
                  </select>-->
            </div>
            <div class="bodydiv" id="cookBodyDiv" style="border-color: gray;border:solid 1px">                
            </div>
            <button class="bg red active hover" id="exitbtnCookInterface">Выход</button>
    </div> 
    
   
    
       <div id="clientForm" class="selectGiftForm div darkwhite" style="display: none;">
                <label id="labelFilterClient" style="
                 top: 5%;
                 left: 55%;
                 position: absolute;
                 font-size: 20px;">Выберите фильтр:</label>
                <select id="filterOptions" style="top: 15%;
                                                 position: absolute;
                                                 left: 55%;
                                                 height: 50px;
                                                 width: 250px;
                                                 font-size: 32px;">
                      <option value="1">По имени</option>
                      <option value="2">По карте</option>
                      <option value="3">По телефону</option>
                      <option value="4">По email</option>
                      </select>
                 <input id="filterClientInput" type="text" val="" placeholder="Введите фильтр" style="left: 55%;width: 245px;top: 40%;">
                 <button  id="showCalcSeekClient" class="button blue edit" style="position:absolute;left: 85%;top: 40%;font-size:8px"></button>    
                 <div class="selectbtnsTableClientFilter">
                 <button  id="okbtnClientFilter" class="button green active">Поиск</button>
                 <button  id="cancelbtnClientFilter" class="button red active">Сброс</button>            
                </div>
                <button  id="addNewClient" class="button green active" style="
                position: absolute;
                height: 40px;
                width: 255px;
                top: 65%;
                left: 55%;">Добавить клиента</button> 
            <div class="tableClientList"> 
                <table id="clientL" class="my-table hovered">
                 <thead><tr>
                         <th>#</th>
                         <th>Выберите клиента</th>
                      </tr>                    
                 </thead>
                     <tbody>
                     </tbody>
                 </table>
                </div>
 
            <div class="selectbtnsTableClient">
            <button  id="okbtnClient" class="button green active">ОК</button>
            <button  id="cancelbtnClient" class="button red active">Отмена</button>            
          </div>
        </div>
    
    <div id="clientAddForm" class="selectGiftForm div darkwhite" style="display: none;">
            <label style="position: absolute;left: 35%;top: 10%;">Введите информацию о новом клиенте</label>
            <br>
                        <div class="formAddClient" style="position: relative;left: 20%;top: 15%;">
                          <style>
                              .formAddClient label{
                                  float: left;
                                  padding-left: 1em;
                                  padding-top: 6px;
                                  display: block;
                                  clear: both;
                              }
                             .formAddClient input,.formAddClient select{
                                  margin:.33em 0 0 13em;
                                  display: table;
                                  position:relative;
                                  width:207px;
                              }
                              .formAddClient select{
                                  margin: .33em 0 0 18.5em;
                                  width: 210px;
                              }
                          </style>    
                          <label>
                            Группа:
                          </label><select id="clientGroup" >

                          </select>
                          <label>
                            ФИО клиента:
                          </label><input type="text" value="" id="clientFIO" ></input>
                           <label>
                            Код карты:
                          </label><input type="text" value="" id="clientMap" ></input>
                              <label>
                            Дата рожденья:
                          </label><input type="date" value="2013-01-01"  value="" id="clientBirth" ></input>
                              <label>
                            Email:
                          </label><input type="text" value="" id="clientEmail" ></input>
                          <label>
                            Телефон:
                          </label><input type="text" value="" id="clientTel" data-mask="+7(999)999-99-99"></input>
                              <label>
                            Адрес:
                          </label><input type="text" value="" id="clientAdress" ></input>
                          <label>
                            Дополнительная информация:
                          </label><input type="text" value="" id="clientSupInf" ></input>
                      </div>
            <div class="selectbtnsTableClient">
                <button  id="okbtnAddClient" class="button green active">ОК</button>
                <button  id="cancelbtnAddClient" class="button red active">Отмена</button>            
          </div>
        </div>
    
    
        <div id="incalcdiv" class="div darkwhite incalcdivstyle" style="display: none;">
        <div id="promptcalc" style="font-size: 18px;width: 335px;text-align: center;"></div>
             <div id="chosepaybtns" class="chosepaybtns div darkwhite" style="display: none;">
                <div class="chosepaylabel chosepaylabelpos1">К оплате:</div>
                <div class="chosepaylabel chosepaylabelpos2">Сдача:</div>
                <!--<button class="chosepaybtn button blue chosepaybtnpos1">Наличными</button>
                <button class="chosepaybtn button blue chosepaybtnpos2">Картой</button>-->
                <select id="typepaylist" style="top: 220px;position: absolute;left: 10px;height: 50px;width: 200px; font-size: 32px">
                 </select>
                <button onmousedown="stopCalc()" class="chosepaybtn button red chosepaybtnpos3">Отмена</button>
                <button id="noSaldoBtn" onmousedown="payClick(1)" class="chosepaybtn button blue chosepaybtnpos4">Без сдачи</button>
                <button onmousedown="payClick(0)" class="chosepaybtnend button green chosepaybtnpos5">Оплата</button>
                <div id="chosepaysumdiv" class="chosepaysumdivstyle input white sumdivpos1">0</div>
                <div id="chosepaybalanse" class="chosepaysumdivstyle input white balansedivpos1">0</div>
            </div>
          <table id="incalc" class="incalcstyle">
            <tbody><tr>
                    <td colspan="4"> <input type="text" id="inpwdcalc"  class="input white" style="position:relative; width:300px; height:50px; font-size:48px"> </td>
            </tr>
            
            <tr>
                <td><button onmousedown="btninClick(7)" class="button grey active incalcnumstyle">7</button></td>
                <td><button onmousedown="btninClick(8)" class="button grey active incalcnumstyle">8</button></td>
                <td><button onmousedown="btninClick(9)" class="button grey active incalcnumstyle">9</button> </td>
                <td rowspan="2"><button onmousedown="btninClick('bs')" class="button grey active inbtnV Hsave2"></button> </td>
            </tr>
            
            <tr>
                <td><button onmousedown="btninClick(4)" class="button grey active incalcnumstyle">4</button></td>
                <td><button onmousedown="btninClick(5)" class="button grey active incalcnumstyle">5</button></td>
                <td><button onmousedown="btninClick(6)" class="button grey active incalcnumstyle">6</button> </td>
            </tr>
            
             <tr>
                <td><button onmousedown="btninClick(1)" class="button grey active incalcnumstyle">1</button></td>
                <td><button onmousedown="btninClick(2)" class="button grey active incalcnumstyle">2</button></td>
                <td><button onmousedown="btninClick(3)" class="button grey active incalcnumstyle">3</button> </td>
                <td rowspan="2"><button onmousedown="btninClick('ent')" class="button grey active inbtnV Hsave1"></button> </td>
            </tr>
            
            <tr>
                <td colspan="2"><button onmousedown="btninClick(0)" class="button grey active inbtnH">0</button></td>
                <td><button onmousedown="btninClick('.')" class="button grey active incalcnumstyle">.</button></td>
            </tr>  
         </tbody></table>
          
        </div> 
    
</body></html>