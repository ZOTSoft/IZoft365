<!-- //////////////////////ПРОЕКТ///////////////////// -->
<script>
$($.date_input.initialize);

$(function() {
    $('#date_finish').datepicker({
    	duration: '',
        showTime: true,
        constrainInput: false
     });
});
</script>

<div id="center">
<?php
///////////////////////////////
/////ПОЛЬЗОВАТЕЛИ ТАБЛИЦА//////
///////////////////////////////
if(isset($_GET['main']) &&  (($_GET['all']) == "view_p")){
	include_once('view_p.inc.php');
}

//////////////////////////////////
/////    ДОБАВЛЕНИЕ ЗАДАЧИ  //////
//////////////////////////////////
if(isset($_GET['main']) &&  (($_GET['all']) == "add_p")){
	include_once('create_p.inc.php');
}
?>
</div>