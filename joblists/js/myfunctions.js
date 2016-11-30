function jQAJAXCall(url, data){
    jQuery.ajax({
	type: "POST",
	url: url,
	data: "aj=1&" + data,
	success: function(status) {
		eval(status);
	},
	error: function(jqXHR, textStatus, errorThrown) {
		alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
	}
    });
}

function SaveChecklist() {
    var urldata = 'step=SaveChecklist';
    urldata += '&projectcode=' + $('#ProjectCode').val();
    $('.checkitem').each(function() {
        if ($(this).is(':checked')) {
            urldata += '&' + $(this).attr('id') + '=1';
        }else{
            urldata += '&' + $(this).attr('id') + '=0';
        }
    });
    //console.log(urldata);
    jQAJAXCall('index.php', urldata);
}

function showAndroidToast(toast) {
    Android.showToast(toast);
}

function GenerateSignOff() {
    Android.GenerateSignOff();
}