$(document).ready(function() {

	var button = $('#uploadButton'), interval;

	$.ajax_upload(button, {
		action : '../task/task/download.php',
		name : 'uploadFile',
		onSubmit : function(file, ext) {
			// показываем картинку загрузки файла
			$("img#load").attr("src", "../task/task/other/AJAXdown/load.gif");
			$("#uploadButton font").text('Загрузка');

			/*
			 * Выключаем кнопку на время загрузки файла
			 */
			this.disable();

		},
		onComplete : function(file, response) {
			// убираем картинку загрузки файла
			$("img#load").attr("src", "../task/task/other/AJAXdown/loadstop.gif");
			$("#uploadButton font").text('Загрузить');

			// снова включаем кнопку
			this.enable();

			// показываем что файл загружен
			$("<li>" + file + "</li>").appendTo("#files");

		},
		error: function()
		{
			alert('sdf');
		}
	});
});
