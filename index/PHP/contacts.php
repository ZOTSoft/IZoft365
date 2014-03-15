<script>$('#menu-item-5').addClass('main-menu-active');</script>
<div class="container">
<table style="position: relative;margin: auto;width: 80%;margin-top: 20px;">
    <style>
        p{
            text-indent: 0!important;
        }
    </style>
    <tr>
        <td class="nomargin">
            <h2>Наши контакты:</h2>
            <p>Вы всегда можете с нами связаться по телефонам:</p>
            <p><h2>+7 701 111 97 23</h2></p>
            <p><h2>+7 727 327 01 74</p></h2>
            <p>По почте: info@paloma365.kz</h2>
            <h2>Наш адрес:</h2>
            <script type="text/javascript" charset="utf-8" src="//api-maps.yandex.ru/services/constructor/1.0/js/?sid=TlaLUcm7RHXB9dJ4Y_YG1oYKldGJUPqi&width=600&height=450"></script>
            Улица Аль-Фараби 53Б, офис 80, угол улицы Бальзака
        </td>
        <td>
            <h2>Напишите нам</h2>
            <p>Ваш email:<br>
            <input type="text" style="font-size: 30px;" id="mailinput"></p>
            <p>Сообщение:<br>
            <textarea style="width: 400px; height: 400px;font-size:22px;" id="messageinput"></textarea>
            <button class='bg lightblue active hover' style="margin: 1em 0" id="sendmail">Отправить</button></p>
        </td>
    </tr>
</table>
<script>
$(function() {
      $('#sendmail').click(function (){
        mail=$("#mailinput").val();
        message=$("#messageinput").val();
        $.post("/message.php", { mail: mail, message:message}).success(function(dataz) {
            $("#mailinput").val('');
            $("#messageinput").val('');
            alert('Сообщение отправлено');
        });
  });
});
</script>
</div>