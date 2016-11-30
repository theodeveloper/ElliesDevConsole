$(document).bind("mobileinit", function(){
   //$.mobile.ajaxEnabled = false;
    $.extend(  $.mobile , {
    defaultPageTransition: 'none'
  });
  $.mobile.dialog.prototype.options.closeBtnText = "close";
});
