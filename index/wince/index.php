<SCRIPT src="index/wince/jquery.easyui.min.js" type="text/javascript"></SCRIPT>
<SCRIPT src="index/wince/jss.js" type="text/javascript"></SCRIPT>    
<div class="container">
<TABLE>
    <TR>
        <TD>       
                <h1>ISoft ТСД WinCE</h1>
                <div style="margin:2em 0 0 4em;">                
                    <FORM id="form-serial" method="post" action="index/wince/actions.php?do=regCE" novalidate>
                        <DIV class="fitem"><LABEL>Клиент:</LABEL>
                            <INPUT name="client" class="easyui-validatebox" type='text' required="true" maxlength="100" value=""></DIV>
                        <DIV class="fitem"><LABEL>Контактное лицо:</LABEL>
                            <INPUT name="name" class="easyui-validatebox" type='text' required="true" maxlength="100" value=""></DIV>
                        <DIV class="fitem"><LABEL>E-mail:</LABEL>
                            <INPUT name="email" class="easyui-validatebox" type='text' maxlength="100" value=""></DIV>
                        <DIV class="fitem"><LABEL>Телефон:</LABEL>
                            <INPUT name="phone" class="easyui-validatebox" type='text' maxlength="100" value=""></DIV>
                        <DIV class="fitem"><LABEL>Серийные номера (через запятую):</LABEL>
                            <TEXTAREA id="serials" name="serials" class="easyui-validatebox" required="true" value=""></TEXTAREA></DIV>
            <a href="javascript:void(0)" >
        <button style="margin:2em 0 0 -0.5em;" class="bg forestGreen active hover" onclick="regSerial(); return false;">Зарегистрировать</button> </a>        
        </FORM>
        <button style="margin: 20px 0 0px 50px;font-size: 16px;" class="bg lightblue active hover"  onclick="history.go(-1);">Назад</button>
    </DIV>
    </TD><TD id="licenses">
    </TD></TR></TABLE>
    </div>    