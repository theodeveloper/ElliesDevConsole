 //Loading
function ajaxindicatorstart(text){
    if(jQuery('body').find('#resultLoading').attr('id') != 'resultLoading'){
    jQuery('body').append('<div id="resultLoading" style="display:none"><div><img src="images/loading.gif"><div>'+text+'</div></div><div class="bg"></div></div>');
    }
    jQuery('#resultLoading').css({
        'width':'100%',
        'height':'100%',
        'position':'fixed',
        'z-index':'10000000',
        'top':'0',
        'left':'0',
        'right':'0',
        'bottom':'0',
        'margin':'auto'
    });

    jQuery('#resultLoading .bg').css({
        'background':'#000000',
        'opacity':'0.7',
        'width':'100%',
        'height':'100%',
        'position':'absolute',
        'top':'0'
    });
    jQuery('#resultLoading>div:first').css({
        'width': '250px',
        'height':'75px',
        'text-align': 'center',
        'position': 'fixed',
        'top':'0',
        'left':'0',
        'right':'0',
        'bottom':'0',
        'margin':'auto',
        'font-size':'16px',
        'z-index':'10',
        'color':'#ffffff'
    });
    jQuery('#resultLoading .bg').height('100%');
    jQuery('#resultLoading').fadeIn(300);
    jQuery('body').css('cursor', 'wait');
}

function ajaxindicatorstop(){
    jQuery('#resultLoading.bg').height('100%');
    jQuery('#resultLoading').fadeOut(300);
    jQuery('body').css('cursor', 'default');
}

jQuery(document).ajaxStart(function () {
    //show ajax indicator
    ajaxindicatorstart('loading data.. please wait..');
}).ajaxStop(function () {
    //hide ajax indicator
    ajaxindicatorstop();
});

function AJAXCallNewsLetterTypeInfo(call, data) {
    jQuery.ajax({
    type: "POST",
    url: "modules/newsletter/functions.php",
    data: "aj=1&call="+call+"&" + encodeURI(data),
    success: function(data) {
        try {
            var obj = JSON.parse(data);
            $('input#newslettertype_edit').val(obj['newsletter_type']);
            $('input#newslettersubject_edit').val(obj['subject']);
            $('iframe').contents().find('.wysihtml5-editor').html(obj['newsletter_layout']);
            $('select#editnewslettertypechannel').val(obj['channel']);
            $('input#editnewslettertypechannel').val(obj['channel']);     
        } catch (err) {
            alert("Error running JS code from module: AJAXCallNewsLetterTypeInfo Edit");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallNewsLetterType(call, data) {
    jQuery.ajax({
    type: "POST",
    url: "modules/newsletter/functions.php",
    data: "aj=1&call="+call+"&" + encodeURI(data),
    success: function(data) {
        try {
           var obj = JSON.parse(data);
           var count = obj.length;
           var element ="";
           var name = ""; 
           var id = "";
          $('select#newslettertype_edit').empty();
          $('select#newslettertype_edit').append("<option value='' selected='selected'>[Please select]</option>");
          for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                name =  element['newsletter_type'];
                id =  element['id'];
                $('select#newslettertype_edit').append("<option id="+id+" value="+id+">"+name+"</option>");
          } 
        } catch (err) {
            alert("Error running JS code from module: AJAXCallNewsLetterType");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}