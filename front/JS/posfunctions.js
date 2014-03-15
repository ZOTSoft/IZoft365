function centralizeElement(obj){
    element = $("#"+obj);
    allwidth = $(window).width();
    allheight = $(window).height();
    element.css("left",((allwidth-element.width())/2)+"px");
    element.css("top", ((allheight-element.height())/2)+"px");
}