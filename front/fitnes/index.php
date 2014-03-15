<?php
    session_start();
    include('../../company/check.php');
    checksessionpassword();
    include('../../company/functions.php');
    if (!isset($_SESSION['point'])){
        header("Location: /login.php");
        die;
}
?>
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Paloma365</title>
<!--<link rel="stylesheet" type="text/css" href="/company/bootstrap/css/bootstrap.css">-->
<link href="../CSS/jquery-ui.css" rel="stylesheet" type="text/css">
<link href="../CSS/style1.css" rel="stylesheet" type="text/css">
<link href="../CSS/officiantframe.css" rel="stylesheet" type="text/css">
<link href="../CSS/cashierframe.css" rel="stylesheet" type="text/css">
<link href="../CSS/chosebill.css" rel="stylesheet" type="text/css">
<link href="../CSS/colorstyles.css" rel="stylesheet" type="text/css">
<link href="../CSS/tablecss.css" rel="stylesheet" type="text/css">
<link href="../CSS/regcheck.css" rel="stylesheet" type="text/css">
<script src="../JS/jquery.js"></script> 
<script src="../JS/jquery-ui.js"></script>
<script src="../JS/tablejs.js"></script>
<script src="../JS/script1.js"></script>
<script src="JS/fitnes.js"></script>
<script src="../JS/posfunctions.js"></script>
<script src="../JS/zottig.js"></script>
<script type="text/javascript" src="/company/bootstrap/js/bootstrap-inputmask.min.js"></script>
<script>
        const version=<?=VERSION?>; 
</script>

</head>
    <body onload="prepareFitnessInterface()">
    
<div id="fitness" style="display: block;">
            <div style="margin-top: 25px;">
                  <label style="float: left; margin:0 0 0 50px;font-size: 40px;"> Помещение:</label>
                  <select id="locList" style="margin-left: 20px;font-size: 40px;width: 350px;float:left">
                      <option value="0">Выберите помещение...</option>
                  </select>
                  <label style="float: left; margin:0 0 0 50px;font-size: 40px;"> Дата:</label>
                  <input id="dtFitness" style="position:relative;font-size: 40px; height: 50px" type="date" value="2013-01-01" > 
<!--                  <label style="float: left; margin:0 0 0 50px;font-size: 40px;"> Объект:</label>
                  <select  style="margin-left: 20px;font-size: 40px;width: 350px;">
                      <option value="0">Выберите объект...</option>
                  </select>-->
            </div>
            <div  id="fitnessBodyDiv" style="margin-top:15px">                
            </div>
        
                <div id="formAddToFitnessJournal" class="div darkwhite" style="border:solid 1px; top:5px; left:5px; height:590px; width:790px; display: none; position:absolute;">
                    <div style="margin:10px 0 0 250px;">
                         <div style="margin:10px 0 0 10px;">
                           <label>Запись на дату </label>
                           <input id="dtJournalFitness" style="position:relative" type="date" value="2013-01-01">
                           <br>
                           <div style="margin:20px 0 0 0px;position:relative">
                               <label>Время с </label>
                               <input id="timeStartJournalFitness" style="margin:0 0 0 20px;position:relative" type="time">     
                               <label style="margin:0 0 0 0px;position:relative">по</label>
                               <input id="timeEndJournalFitness" style="position:relative" type="time">
                           </div>
                        </div>
                        <div style="margin:10px 0 0 10px;">
                            <label style="position:relative">Клиент:</label>
                            <input id="clientJournalAddFitness" style="position:relative" readonly="readonly"></input>
                            <button id="fitnessJournalAddClientBtn" class="clientbtn  button blue edit" style="font-size:7px"></button>
                            <br>
                            <label style="margin:0 0 0 5px;position:relative">Дополнительная информация</label>
                            <br>
                            <input id="supInfoAboutClientFitness" style="margin:0 0 0 0;position:relative;width: 300px;" readonly="readonly"></input>
                        </div>
                        <div style="margin:10px 0 0 10px;">
                            <label id="labelSelectObject" style="position:relative">Объект:</label>
                            <select id="journalObj" style="position:relative">
                                <option>Выберите объект</option>
                            </select> 
                            <br>
                            <label style="margin:0 0 0 120px;position:relative">Примечание</label>
                            <br> 
                            <textarea id="journalAddNoteFitness" style="margin:0 0 0 27px;position:relative;height: 100px;width: 235px;"></textarea>
                        </div>
                        <div class="selectbtns" style="margin:0 0 0 150px;">
                            <button id="formAddToFitnessJournalOkBtn"  class="button green active">Сохранить</button>
                            <button id="formAddToFitnessJournalCancelBtn" class="button red active">Отмена</button>   
                        </div>
                     </div>
                </div>
            <div id="showFitnessReсordDiv" class="showFitnessRecordInf div darkwhite">
                <div>
                    
                    <button peshey="recordMainInfDiv" id="" class="fitnesShowRecordBtn fbtnpos1 button blue">Главная</button>
                    <button peshey="recordClientInfDiv" id="" class="fitnesShowRecordBtn fbtnpos2 button blue">О клиент</button>
                    <button peshey="recordServiceInfDiv" id="" class="fitnesShowRecordBtn fbtnpos3 button blue">Уcлуги</button>
                    <button peshey="recordPaymentInfDiv" id="" class="fitnesShowRecordBtn fbtnpos4 button blue">Оплата</button>
                    
                    <div id="recordMainInfDiv" class="recordInfDivStyle" style="display:none">
<!--                        <label id="showRecord_labelObjReserv" style="margin-top:20px;margin-left:20px;display:block">Объект резервирования:</label>
                        <label id="showRecord_labelDateReserv" style="margin-top:30px;margin-left:20px;display:block">Дата:</label>
                        <label id="showRecord_labelTimeStartReserv" style="margin-top:20px;margin-left:20px;">Время начала:</label>
                        <label id="showRecord_labelTimeEndReserv" style="margin-top:20px;margin-left:20px;">Время окончания:</label>
                        <label id="showRecord_labelTimeDuringReserv" style="margin-top:40px;margin-left:20px;display:block">Продолжительность:</label>
                        <label id="showRecord_labelDateRegReserv" style="margin-top:60px;margin-left:20px;display:block">Дата регистрации:</label>-->
                      <div style="margin:10px 0 0 100px;">
                         <div style="margin:10px 0 0 10px;">
                           <label>Запись на дату </label>
                           <input id="dtJournalFitness_edit" style="position:relative" type="date" value="2013-01-01">
                           <br>
                           <div style="margin:20px 0 0 0px;position:relative">
                               <label>Время с </label>
                               <input id="timeStartJournalFitness_edit" style="margin:0 0 0 20px;position:relative" type="time">     
                               <label style="margin:0 0 0 0px;position:relative">по</label>
                               <input id="timeEndJournalFitness_edit" style="position:relative" type="time">
                           </div>
                        </div>
                        <div style="margin:10px 0 0 10px;">
                            <label style="position:relative">Клиент:</label>
                            <input id="clientJournalAddFitness_edit" style="position:relative" readonly="readonly"></input>
                            <button id="fitnessJournalAddClientBtn_edit" class="clientbtn  button blue edit" style="font-size:7px"></button>
                            <br>
                            <label style="margin:0 0 0 5px;position:relative">Дополнительная информация</label>
                            <br>
                            <input id="supInfoAboutClientFitness_edit" style="margin:0 0 0 0;position:relative;width: 300px;" readonly="readonly"></input>
                        </div>
                        <div style="margin:10px 0 0 10px;">
                            <label id="labelSelectObject_edit" style="position:relative">Объект:</label>
                            <select id="journalObj_edit" style="position:relative">
                                <option>Выберите объект</option>
                            </select> 
                            <br>
                            <label style="margin:0 0 0 120px;position:relative">Примечание</label>
                            <br> 
                            <textarea id="journalAddNoteFitness_edit" style="margin:0 0 0 27px;position:relative;height: 100px;width: 235px;"></textarea>
                            <label id="showRecord_labelTimeDuringReserv" style="margin-top:20px;margin-left:20px;display:block">Продолжительность:</label>
                        </div>
                     </div>
                    </div>
                    <div id="recordClientInfDiv" class="recordInfDivStyle" style="display:none">
                        <label id="showRecord_labelClientName" style="margin-top:20px;margin-left:20px;display:block">ФИО:</label>
                        <label id="showRecord_labelClientBirth" style="margin-top:30px;margin-left:20px;display:block">Дата рождения:</label>
                        <label id="showRecord_labelClientTel" style="margin-top:20px;margin-left:20px;display:block">Телефон:</label>
                        <label id="showRecord_labelClientAddress" style="margin-top:20px;margin-left:20px;display:block">Адресс:</label>
                    </div>
                    <div id="recordServiceInfDiv" class="recordInfDivStyle" style="display:none">
                        <div style="margin:40px 40px 5px 40px;border:solid 1px;height: 220px;">
                        <table id="fitnesServiceTable" class="my-table hovered">
                        <thead>
                            <tr>
                                <th>Наименование</th>
                                <th>Сумма</th>
                                <th>Исполнитель</th>
                            </tr>                    
                        </thead>
                        <tbody>
                        </tbody>
                        </table>
                        </div>
                        <button id="btnAddServiceToRecord" class="button green" style="margin-left: 40px;">Добавить</button>
                        <button id="btnDeleteServiceToRecord" class="button red">Удалить</button>
                        <div style="margin-left: 40px;">
                            <label>Оплачено:0</label>
                            <br>
                            <label id="saldoLabelShowFitnesRecord">Сальдо:</label>
                            <br>
                            <label id="itogLabelShowFitnesRecord">Итого:</label>
                            <br>
                            <label>Скидка:0%</label>
                            <br>
                            <label id="paymentSumLabelShowFitnesRecord">К оплате:</label>
                        </div>
                    </div>
                    <div id="recordPaymentInfDiv" class="recordInfDivStyle" style="display:none">

                    </div>
                </div>
                <div class="selectbtnsTableClient">
                    <button  id="okbtnShowFitnessRecord" class="button green active">ОК</button>
                    <button  id="cancelbtnShowFitnessRecord" class="button red active">Отмена</button>            
                </div>
            </div>     
            <div id="addServiceToFitnesRecord" class="selectGiftForm div darkwhite" style="display: none;">
                       <div class="tableClientList" style="border-color: gray; border-right: solid 1px;border-bottom: solid 1px;">           
                        <table id="menuGroupServiceToFitnes" class="my-table hovered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Выберите услугу</th>
                                <th>Цена</th>
                            </tr>                    
                        </thead>
                            <tbody>
                            </tbody>
                       </table>
                       <!--<button id="btnSelectLevelGift">Выбрать</buton>-->
                      </div>                   
                       <div class="tableClientSelected" style="border-color: gray; border-bottom: solid 1px;"> 
                       <table id="selectedGroupServiceToFitnes" class="my-table hovered">
                       <thead><tr>
                                <th>#</th>
                                <th>Выбранные услуги</th>
                                <th>Цена</th>
                             </tr>                    
                        </thead>
                            <tbody>
                            </tbody>
                        </table>
                        </div>
                      
                      <div class="selectbtnsGift">
                        <button  id="okbtnServiceToFitnes" class="button green active">Готово</button>
                        <button  id="cancelbtnServiceToFitnes" class="button red active">Отмена</button>            
                      </div>
                       <div style="top: 90%;position: absolute;left: 75%;height: 65px;width: 100px;">
                                <label id="sumServToFit" style="margin-bottom:15px">Итого:</label>
                                <button  id="deletebtnServiceToFitnes" class="button red active">Удалить</button>
                      </div>
             </div>
            <button class="button green" id="createRecordFitnessInterface">Создать</button>
            <button class="bg red active hover" id="exitbtnFitnessInterface">Выход</button>
    </div>
        
    <div id="clientForm_fitnes" class="selectGiftForm div darkwhite" style="display: none;">
        <!--<div>-->
             <label id="labelFilterClient_fitnes" style="
              top: 5%;
              left: 55%;
              position: absolute;
              font-size: 20px;">Выберите фильтр:</label>
             <select id="filterOptions_fitnes" style="top: 15%;
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
              <input id="filterClientInput_fitnes" type="text" val="" placeholder="Введите фильтр" style="left: 55%;width: 245px;top: 40%;">
              <div class="selectbtnsTableClientFilter">
              <button  id="okbtnClientFilter_fitnes" class="button green active">Поиск</button>
              <button  id="cancelbtnClientFilter_fitnes" class="button red active">Сброс</button>            
             </div>
             <button  id="addNewClient_fitnes" class="button green active" style="
             position: absolute;
             height: 40px;
             width: 255px;
             top: 65%;
             left: 55%;">Добавить клиента</button> 
        <!--</div>-->     
         <div class="tableClientList"> 
             <table id="clientL_fitnes" class="my-table hovered">
              <thead><tr>
                      <th>#</th>
                      <th>Выберите клиента</th>
                   </tr>                    
              </thead>
                  <tbody>
                  </tbody>
              </table>
                 <!--<button onClick="addRowToSelectedItems(giftItemsarray[getMytableRowNum('selectGiftItem')])" id="btnSelectItemGift">Выбрать</buton>-->
             </div>

         <div class="selectbtnsTableClient">
         <button  id="okbtnClient_fitnes" class="button green active">ОК</button>
         <button  id="cancelbtnClient_fitnes" class="button red active">Отмена</button>            
       </div>
     </div>
    
    <div id="clientAddForm_fitnes" class="selectGiftForm div darkwhite" style="display: none;">
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
                          </label><select id="clientGroup_fitnes" >

                          </select>
                          <label>
                            ФИО клиента:
                          </label><input type="text" value="" id="clientFIO_fitnes" ></input>
                           <label>
                            Код карты:
                          </label><input type="text" value="" id="clientMap_fitnes" ></input>
                              <label>
                            Дата рожденья:
                          </label><input type="date" value="2013-01-01"  value="" id="clientBirth_fitnes" ></input>
                              <label>
                            Email:
                          </label><input type="text" value="" id="clientEmail_fitnes" ></input>
                          <label>
                            Телефон:
                          </label><input type="text" value="" id="clientTel_fitnes" data-mask="+7(999)999-99-99"></input>
                              <label>
                            Адрес:
                          </label><input type="text" value="" id="clientAdress_fitnes" ></input>
                          <label>
                            Дополнительная информация:
                          </label><input type="text" value="" id="clientSupInf_fitnes" ></input>
                      </div>
        <div class="selectbtnsTableClient">
            <button  id="okbtnAddClient_fitnes" class="button green active">ОК</button>
            <button  id="cancelbtnAddClient_fitnes" class="button red active">Отмена</button>            
      </div>
    </div>
</body></html>