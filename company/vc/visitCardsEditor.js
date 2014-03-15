//$(function () {
//    /*$( "#contentEditor" ).ckeditor();*/
//    /*CKEDITOR.replace( 'contentEditor' );*/
//    CKEDITOR.replace( 'editor1', {filebrowserUploadUrl: '../vce.php'});
//});

function showVC(){
    $.ajax({
        url: "vc/vce.php?do=showCutaway"
    }).success(function (data){
        $("#previewDiv").html(data);
    });
}

function showVCWindow(table){
    width=$('.righttd').width()-10;
    height=$('.righttd').height()-85;
    $.ajax({ 
      type: "POST",
      url: "vc/vce.php?do=gettable",
      data: {table: table},
      dataType: 'json'
    }).success(function(dataz) {
        $('#window-'+dataz.name).remove();

        if($('#tabs').tabs('exists', dataz.title)){
            $('#tabs').tabs('close', dataz.title);
        }

        cont = '<div class="bggrey"><h2>' + dataz.title + '</h2></div><div id="' + table + 'Div" height="500px"></div>';

        $('#tabs').tabs('add',{  
            title: dataz.title,  
            content: cont,  
            closable: true
        });  
        
        $("#" + table + "Div").html( dataz["fields"] );

        if (table === "visitcard"){
            //CKEDITOR.replace( 'contentEditor' );
            CKEDITOR.replace( 'contentEditor',
            {
                filebrowserBrowseUrl: "../company/vc/vce.php?do=getImagesList",
                filebrowserWindowWidth : '640',
                filebrowserWindowHeight : '480'
            });
        } else if (table === "visitcard_contents"){
            CKEDITOR.replace( 'pageContentEditor',
            {
                filebrowserBrowseUrl: "../company/vc/vce.php?do=getImagesList",
                filebrowserWindowWidth : '640',
                filebrowserWindowHeight : '480'
            });            
        } else if (table === "visitcard_images"){
            
        }
        
        console.log(dataz);
    });
}

/*function uploadImage(){
    var btn = $("#uploadBtn1"), interval;
    $("#uploadBtn1").parent().css("display", "inline-block");

    $.ajax_upload(btn, {
        action : "vce.php?do=uploadImage",
        name: "file",
        onSubmit: function(file, ext) {
            if (! (ext && /^(jpg|png|jpeg|bmp)$/.test(ext))){ 
                $("#infoBox").html("Поддерживаемые форматы BMP, JPG и PNG");
                return false;
            }
            this.disable();
            $("#infoBox").html("Изображение загружается...");
        },
        onComplete: function(file, response) {
            this.enable();
            console.log(response);
            response = $.parseJSON(response);
            $("#infoBox").html("Результат загрузки: " + response["response"]);
            if (response["response"] === "OK"){
                $('<img height="100px" src="' + response["filename"] + '" alt="" /><br />').appendTo("#infoBox");
                $("#logo").val(response["filename"]);
            } 
        }
    });
}*/

function selectImage(){
    if($("#dialog-imageList").length > 0) {
        $('#dialog-imageList').dialog('close');
        $('#dialog-imageList').html('');
        $('#dialog-imageList').remove();
    }

    $("#dialogs").append('<div id="dialog-imageList"></div>');   
    $('#dialog-imageList').dialog({  
        title: 'Выберите изображение',  
        width: screen.width - 200,
        maxheight: screen.height - 200,
        hcenter: true,
        top: 50,  
        closed: false,
        href: 'vc/vce.php?do=getImagesList&mode=0'
    });  
}

function closeImageList(selected){
    if (selected === 1){
        $("#vcelogo").attr("src", $("#imagesList2 .selectedImage").attr("src"));
        $("#logo").val($("#imagesList2 .selectedImage").attr("src"));
    }
    console.log(1);
    $('#dialog-imageList').dialog('close');
    $('#dialog-imageList').html('');
    $('#dialog-imageList').remove();
}

function removeImage(){
    var fnames = new Array();

    $.each($("img.selectedImage"), function(index, value){
        s = $(value).attr("src");
        fnames.push(s);
        $(this).remove();
    });

    fnames = JSON.stringify(fnames);console.log(fnames);

    $.ajax({
        url: "vc/vce.php?do=removeImage",
        type: "POST",
        data: "fnames=" + fnames
    }).success(function(data){
        console.log(data);
    });
}

function saveCutaway(){
    $('#form-cutaway').form('submit', {  
        success: function(data){
            alert(data);   
        }
    });    
}

function getContent(elem){
console.log($(elem).attr('cid'));
    $.ajax({
        type: 'GET',
        url: 'vc/vce.php?do=getContent&id=' + $(elem).attr('cid')
    }).success(function (data){
        console.log(data);
        if (data === 'error'){
        } else {
            data = $.parseJSON(data);
            $('#removeContentBtn').attr('cid', data['id']);
            $('#saveContentBtn').attr('cid', data['id']);
            $('#contentName').val(data['name']);
            //$('#pageContentEditor').html(data['content']);
            CKEDITOR.instances.pageContentEditor.setData(data['content']);
            $('#pce').attr('style', 'display: block;');
        }
    });
}

function addContent(vc){
    $.ajax({
        type: 'GET',
        url: 'vc/vce.php?do=addContent&vcId=' + vc
    }).success(function (data){
        if (data === 'error'){
        } else {
            console.log(data);
            data = $.parseJSON(data);
            $(data['btn']).appendTo('#contentList');
            $('#removeContentBtn').attr('cid', data['id']);
            $('#saveContentBtn').attr('cid', data['id']);
            $('#contentName').text(data['name']);
            CKEDITOR.instances.pageContentEditor.setData(data['content']);
        }
    });
}

function removeContent(elem){
    cid = parseInt($(elem).attr('cid'));
    if (cid === 0){
    } else {
        $.ajax({
            type: 'GET',
            url: 'vc/vce.php?do=removeContent&cId=' + cid
        }).success(function (data){
            if (data === 'error'){
            } else {
                $('#removeContentBtn').attr('cid', 0);
                $('#saveContentBtn').attr('cid', 0);
                $('#contentName').text('');
                $('#contentEditor').text('');
                $('#btncontent' + cid).remove();
                $('#pce').attr('style', 'display: none;');
            }
        });
    }
}

function saveContent(elem){
    cid = parseInt($(elem).attr('cid'));
    data = "name=" + $("#contentName").val() + "&content=" + CKEDITOR.instances.pageContentEditor.getData();
    $.ajax({
        type: 'POST',
        url: 'vc/vce.php?do=saveContent&cId=' + $(elem).attr('cid'),
        data: ({name : $("#contentName").val(), content: CKEDITOR.instances.pageContentEditor.getData() })
    }).success(function (data){
        $('#btncontent' + cid).html($("#contentName").val());
        alert(data);
    });
}