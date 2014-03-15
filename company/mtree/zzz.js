$(function (){
    $('.fullscr').click(function (){
      if ($('.lefttd').width()>0){
         $('.lefttd').animate({'width':'0'},'fast');
         $('.tab_header').animate({'top':'-33'},'fast').css('display','none');
         $('.contactinfo2').animate({'left':'-220'},'fast');
         $(this).removeClass('glyphicon-fullscreen').addClass('glyphicon-resize-small');
         $('.my-menu').css('display','inline');
        }
      else{
         $('.lefttd').animate({'width':'220'},'fast');
         $('.tab_header').animate({'top':'0'},'fast').css('display','table-cell');     
         $('.contactinfo2').animate({'left':'0'},'fast');
         $(this).addClass('glyphicon-fullscreen').removeClass('glyphicon-resize-small');
         $('.my-menu').css('display','none');
      }
    });
});
