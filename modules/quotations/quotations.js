function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
};

function ValidateCustomerFormA(classname) {
    var valid = false;
    var errors = false;
    var errorfields = "";
    var urldata = "step=CustomerCreateCheck";
    $('#newquoteform').find('input[type=text],select').each(function() {
        if ($(this).val() != "") {
            console.log("id: " + $(this).attr("id"));
            console.log("value: " + $(this).val());
            urldata += "&" + $(this).attr("id") + "=" + encodeURIComponent($(this).val());
            $("#" + $(this).attr("id") + "_val_message").html("");
        }else{
            errors = true;
            errorfields += "\n" + $(this).attr("id");
            $("#" + $(this).attr("id") + "_val_message").html(" *required");
        }
    });

    if (!isValidEmailAddress($("#email").val())) {
        if (!errors) {
            $("#email_val_message").html(" *incorrect format");            
        }
        errors = true;
    }

    if (errors) {
        valid = false;
        alert("Please enter values for the required fields.");
    }else{
        AJAXCallModuleJSOnly(classname, 'NewQuotation', urldata);
        valid = true;
    }
    return valid;
}

function ConfirmCustomerChanges(classname) {
    var urldata = "step=ConfirmCustomerUpdate";
    $('#newquoteform').find('input[type=text],select').each(function() {
        if ($(this).val() != "") {
            urldata += "&" + $(this).attr("id") + "=" + encodeURIComponent($(this).val());
        }
    });
    AJAXCallModuleJSOnly(classname, 'ConfirmCustomerUpdate', urldata);
}

function ReLoadQuotes(customerid) {
    AJAXCallModuleJSOnly("Quotations", 'ReLoadQuotes', "customerid=" + customerid);
}

function LoadQuote(quoteid, customerid) {
    AJAXCallModuleJSOnly("Quotations", 'LoadQuote', "quoteid=" + quoteid + "&customerid=" + customerid);
}

function LoadQuoteItem() {
    AJAXCallModuleJSOnly("Quotations", 'LoadQuoteItem', "i1=1");
}

function BuildTechItemInput(obj) {
    AJAXCallModuleJSOnly("Quotations", 'BuildTechItemInput', "techitem=" + $(obj).val());
}

function AddQuoteItem() {
    
    var urldata = "techitem=" + $('#techitem').val();
    $('#iteminput').find('input[type=text],select').each(function() {
        if ($(this).val() != "") {
            urldata += "&" + $(this).attr("id") + "=" + encodeURIComponent($(this).val());
        }
    });
    AJAXCallModuleJSOnly("Quotations", 'AddQuoteItem', urldata);
}
//Pending Quotes
//=======================
function ChangePendingFranchisesChannel(obj) {
    var id = $(obj).find(':selected').attr('id'); 
    var channelname = $(obj).val();
    var channeltype = 'Franchises';
    if(id > 0 || id =='All')AJAXCallModule('Quotations','pending_quotations', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}
function ChangePendingRetailChannel(obj) {
    var id = $(obj).find(':selected').attr('id'); 
    var channelname = $(obj).val();
    var channeltype = 'Retail';
    if(id > 0 || id =='All')AJAXCallModule('Quotations','pending_quotations', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}

function ChangePendingCommercialChannel(obj) {
    var id = $(obj).find(':selected').attr('id'); 
    var channelname = $(obj).val();
    var channeltype = 'Commercial';
    if(id > 0 || id =='All')AJAXCallModule('Quotations','pending_quotations', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}
//Sent Quotes
//=======================
function ChangeSentFranchisesChannel(obj) {
    var id = $(obj).find(':selected').attr('id'); 
    var channelname = $(obj).val();
    var channeltype = 'Franchises';
    if(id > 0 || id =='All')AJAXCallModule('Quotations','completed_quotations', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}
function ChangeSentRetailChannel(obj) {
    var id = $(obj).find(':selected').attr('id'); 
    var channelname = $(obj).val();
    var channeltype = 'Retail';
    if(id > 0 || id =='All')AJAXCallModule('Quotations','completed_quotations', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}

function ChangeSentCommercialChannel(obj) {
    var id = $(obj).find(':selected').attr('id'); 
    var channelname = $(obj).val();
    var channeltype = 'Commercial';
    if(id > 0 || id =='All')AJAXCallModule('Quotations','completed_quotations', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}
//Rejected Quotes
//=======================
function ChangeRejectedFranchisesChannel(obj) {
    var id = $(obj).find(':selected').attr('id'); 
    var channelname = $(obj).val();
    var channeltype = 'Franchises';
    if(id > 0 || id =='All')AJAXCallModule('Quotations','rejected_quotations', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}
function ChangeRejectedRetailChannel(obj) {
    var id = $(obj).find(':selected').attr('id'); 
    var channelname = $(obj).val();
    var channeltype = 'Retail';
    if(id > 0 || id =='All')AJAXCallModule('Quotations','rejected_quotations', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}

function ChangeRejectedCommercialChannel(obj) {
    var id = $(obj).find(':selected').attr('id'); 
    var channelname = $(obj).val();
    var channeltype = 'Commercial';
    if(id > 0 || id =='All')AJAXCallModule('Quotations','rejected_quotations', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}
//Approved Quotes
//=======================
function ChangeApprovedFranchisesChannel(obj) {
    var id = $(obj).find(':selected').attr('id'); 
    var channelname = $(obj).val();
    var channeltype = 'Franchises';
    if(id > 0 || id =='All')AJAXCallModule('Quotations','approved_quotations', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}
function ChangeApprovedRetailChannel(obj) {
    var id = $(obj).find(':selected').attr('id'); 
    var channelname = $(obj).val();
    var channeltype = 'Retail';
    if(id > 0 || id =='All')AJAXCallModule('Quotations','approved_quotations', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}

function ChangeApprovedCommercialChannel(obj) {
    var id = $(obj).find(':selected').attr('id'); 
    var channelname = $(obj).val();
    var channeltype = 'Commercial';
    if(id > 0 || id =='All')AJAXCallModule('Quotations','approved_quotations', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}
//Printed Quotes
//=======================
function ChangePrintedFranchisesChannel(obj) {
    var id = $(obj).find(':selected').attr('id'); 
    var channelname = $(obj).val();
    var channeltype = 'Franchises';
    if(id > 0 || id =='All')AJAXCallModule('Quotations','printed_quotations', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}
function ChangePrintedRetailChannel(obj) {
    var id = $(obj).find(':selected').attr('id'); 
    var channelname = $(obj).val();
    var channeltype = 'Retail';
    if(id > 0 || id =='All')AJAXCallModule('Quotations','printed_quotations', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}

function ChangePrintedCommercialChannel(obj) {
    var id = $(obj).find(':selected').attr('id'); 
    var channelname = $(obj).val();
    var channeltype = 'Commercial';
    if(id > 0 || id =='All')AJAXCallModule('Quotations','printed_quotations', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}