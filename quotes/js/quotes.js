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

function ReturnToMain(module, call) {
    //alert(module);
    //alert(call);
    var udata = "module=" + module;
    udata += "&call=" + call;
    udata += "&id=" + 4;
    jQAJAXCall("../", udata);
}

function ShowLitreCalc() {
    $("#inpcalc_container_ml").val('');
    $("#inpcalc_filltime_sec").val('');
    $("#btnOpenCalculator").hide();
    $('#inp-calculator').show();
}

function CalculateLitreUsage() {
    var vval = 0;
    vval = (($("#inpcalc_container_ml").val() * 1)/1000)/(($("#inpcalc_filltime_sec").val() * 1)/60);
    if (vval != "NaN") {
	$(".calctarget").val(vval);
	$('#inp-calculator').hide();
	$("#btnOpenCalculator").show();
    }
}

function SaveProductInput(quoteid, quoteitemid, newid, oldid) {
    var urldata = 'step=SaveProductInput';
    urldata += '&quoteid=' + quoteid;
    urldata += '&quoteitemid=' + quoteitemid;
    urldata += '&newid=' + newid;
    urldata += '&oldid=' + oldid;
    $('#page1').find('input[type=text],select').each(function() {
        if ($(this).val() != '') {
            urldata += '&' + $(this).attr('id') + '=' + encodeURIComponent($(this).val());
        }
    });
    console.log(urldata);
    jQAJAXCall('selectproduct.php', urldata);
    
}

function MaxNumCheck(obj, maxnum) {
    if (isNaN($(obj).val())) {
        $(obj).val(0);
    }else{
        if ($(obj).val() > maxnum) {
            $(obj).val(maxnum);
        }
    }
}

function NumOnlyCheck(obj, defaultnumber) {
    if (isNaN($(obj).val())) {
        $(obj).val(defaultnumber);
    }
}

function CloseDialogs() {
    $('.ui-dialog').dialog('close');
}

//shows slider to change default values of hours per day, days per week etc
function toggle_visibility(id) {
	document.getElementById(id + '_val').style.display = 'none';
	$('#'+ id + '_qty').css({opacity: 1.0, overflow:'visible', height:'106px'})
	$('#' + id + '_div').css({display:'none'});

}

