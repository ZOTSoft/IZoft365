function regSerial(){
	if ($('#serials').val() == "")
		alert("Введите серийный номер(-а)!");
	else {
		$('#okBtn').html("");
		$('#form-serial').form('submit', {  
			success: function(data){
				$('#licenses').html(data);    
			}
		});
	}
}

function clearForm(){
	$('#licenses').html('');
	$('#form-serial').form('clear');
	$('#okBtn').html('<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="regSerial()"><b>OK</b></a>');
}