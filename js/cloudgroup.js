/*
# This code is written by Tradepage Pty Ltd for Ellies Pty Ltd (the 'parties' mentioned below)
# The code is provided based on the the terms specified within the agreed NDA between both parties.
# Both parties have agreed the code is strictly confidential
# and only by mutal agreement of both parties may the code be exposed to outside parties.
#
# Any changes made to the code other than by Tradepage Pty Ltd during the NDA Agreement void support of the code
#
*/
/* --------------------------------------------------------------------------
* This source code contains confidential information that is proprietary to
* CloudGroup (Pty) Ltd. No part of its contents may be used,
* copied, disclosed or conveyed to any party in any manner whatsoever
* without prior written permission from CloudGroup(Pty) Ltd.
* No part of this source code may be used, reproduced, stored in a retrieval system,
* or transmitted, in any form or by any means, electronic, mechanical,
* photocopying, recording or otherwise, without the prior written permission
* of the copyright owners.
* --------------------------------------------------------------------------
* Copyright CloudGroup (Pty) Ltd
*/

function AJAXCallModule(module, call, data) {
    $("#divContentContainer").css("background-color", "E7E7E7");
    $("#divContentContainer").css("overflow", "hidden");
    $("#divContent").html("");
    $('#divContentLoader').html("<img src='images/loader.gif' alt='loading' border='0'> <span>Loading...</span>");
    $("#divContentLoader").show();
    
    jQuery.ajax({
	type: "POST",
	url: "index.php",
	data: "aj=1&module="+module+"&call="+call+"&" + data,
	success: function(status) {
	    data = status.split("<!-- scripts , code below be eval()ed by javascript -->");
	    $("#divContent").html( data[0] );
	    $("#divContentContainer").css("background-color", "");
	    $("#divContentContainer").css("overflow", "auto");
	    $("#divContentLoader").hide();
	    try {
		eval(data[1]);
	    } catch (err) {
		alert('Error running JS code from module: AJAXCallModule');
	    }
	},
	error: function(jqXHR, textStatus, errorThrown) {
	    alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
	}
    });
}

function AJAXCall(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
        	//console.log("execute cass");
        	//console.log(data);
        	var obj = JSON.parse(data);
        	$('input#checklistitle').val(obj['Title']);
        	$('textarea#checklistdescription').val(obj['Description']);
        	var type = obj['Type'];
        	var mandatory = obj['Mandatory'];
        	$('select#checklisttype').val(type);
        	if(mandatory ==1){
        		$('select#checklistmandatory').val("Yes");
        	}
        	if(mandatory ==0){
        		$('select#checklistmandatory').val("No");
        	}	
        } catch (err) {
            alert("Error running JS code from module: AJAXCall");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}

function AJAXCallApproval(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            $('input#systemFirst').val(obj['first']);
            $('input#systemLast').val(obj['last']);
            $('input#systemEmail').val(obj['email']);
            $('input#systemBranch').val(obj['branch']);
            $('textarea#systemAddress').val(obj['address']);
        } catch (err) {
            alert("Error running JS code from module: AJAXCallApproval");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallApprovalBranch(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
           var obj = JSON.parse(data);
           var count = obj.length;
           var element ="";
           var first =  "";
           var last =  "";
           var text = ""; 
           var id = "";
          $('select#systemapproval').empty();
          $('select#systemapproval').append("<option value='' selected='selected'>[Please select]</option>");
          for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                first =  element['first'];
                last =  element['last'];
                text = first + " " + last; 
                id =  element['id'];
                $('select#systemapproval').append("<option id="+id+" value="+text+">"+text+"</option>");
          } 
        } catch (err) {
            alert("Error running JS code from module: AJAXCallApprovalBranch");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallApprovalMembers(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
           var obj = JSON.parse(data);
           var count = obj.length;
           var element ="";
           var first =  "";
           var last =  "";
           var text = ""; 
           var id = "";
          $('select#systemapproval_edit').empty();
          $('select#systemapproval_edit').append("<option value='' selected='selected'>[Please select]</option>");
          for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                first =  element['first'];
                last =  element['last'];
                text = first + " " + last; 
                id =  element['id'];
                $('select#systemapproval_edit').append("<option id="+id+" value="+text+">"+text+"</option>");
          } 
        } catch (err) {
            alert("Error running JS code from module: AJAXCallApprovalMembers");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallApprovalDelete(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            $('input#systemFirst_edit').val(obj['first']);
            $('input#systemLast_edit').val(obj['last']);
            $('input#systemEmail_edit').val(obj['email']);
            $('input#systemBranch_edit').val(obj['branch']);
            $('textarea#systemAddress_edit').val(obj['address']);
        } catch (err) {
            alert("Error running JS code from module: AJAXCallApproval Edit");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}

function AJAXCallEscalation(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            $('input#systemescalationFirst').val(obj['first']);
            $('input#systemescalationLast').val(obj['last']);
            $('input#systemescalationEmail').val(obj['email']);
            $('input#systemescalationBranch').val(obj['branch']);
            $('textarea#systemescalationAddress').val(obj['address']);
        } catch (err) {
            alert("Error running JS code from module: AJAXCallEscalation");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallEscalationBranch(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
           var obj = JSON.parse(data);
           var count = obj.length;
           var element ="";
           var first =  "";
           var last =  "";
           var text = ""; 
           var id = "";
          $('select#systemescalation').empty();
          $('select#systemescalation').append("<option value='' selected='selected'>[Please select]</option>");
          for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                first =  element['first'];
                last =  element['last'];
                text = first + " " + last; 
                id =  element['id'];
                $('select#systemescalation').append("<option id="+id+" value="+text+">"+text+"</option>");
          } 
        } catch (err) {
            alert("Error running JS code from module: AJAXCallEscalationBranch");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallEscalationMembers(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
           var obj = JSON.parse(data);
           var count = obj.length;
           var element ="";
           var first =  "";
           var last =  "";
           var text = ""; 
           var id = "";
          $('select#systemescalation_edit').empty();
          $('select#systemescalation_edit').append("<option value='' selected='selected'>[Please select]</option>");
          for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                first =  element['first'];
                last =  element['last'];
                text = first + " " + last; 
                id =  element['id'];
                $('select#systemescalation_edit').append("<option id="+id+" value="+text+">"+text+"</option>");
          } 
        } catch (err) {
            alert("Error running JS code from module: AJAXCallEscalationMembers");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallEscalationDelete(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            $('input#systemescalationFirst_edit').val(obj['first']);
            $('input#systemescalationLast_edit').val(obj['last']);
            $('input#systemescalationEmail_edit').val(obj['email']);
            $('input#systemescalationranch_edit').val(obj['branch']);
            $('textarea#systemescalationAddress_edit').val(obj['address']);
            $('input#timescalation_edit').val(obj['time_escalation']);
        } catch (err) {
            alert("Error running JS code from module: AJAXCallEscalation Edit");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}

function AJAXCallRoom(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            
            if (obj['room_type'].indexOf("^") >= 0){
                $('input#systemFloor_edit').val(res[0]);
                $('input#systemRoom_edit').val(res[1]);
                $('select#systemProperty_edit').val(obj['property_type']); 
            }else{
                $('input#systemFloor_edit').val("");
                $('input#systemRoom_edit').val(obj['room_type']);
                $('select#systemProperty_edit').val("0"); 
            }
        } catch (err) {
            alert("Error running JS code from module: AJAXCallRoom Edit");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallProperty(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            $('input#systemProperty_edit').val(obj['Type']);
        } catch (err) {
            alert("Error running JS code from module: AJAXCallApproval Edit");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}

function AJAXCallFloorPlans(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            $('input#systemFloorPlanName_edit').val(obj['name']);
            $('textarea#systemFloorPlanDescription_edit').val(obj['description']);
        } catch (err) {
            alert("Error running JS code from module: AJAXCallFloorPlans Edit");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}


function AJAXCallTeam(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            $('input#teamtitle_edit').val(obj['title']);
            $('textarea#teamdescription_edit').val(obj['description']);
            $('input#teamchannel_edit').val(obj['channel']);
        } catch (err) {
            alert("Error running JS code from module: AJAXCallTeam Edit");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallTypeTeam(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
           var obj = JSON.parse(data);
           var count = obj.length;
           var element ="";
           var first =  "";
           var last =  "";
           var text = ""; 
           var id = "";
          $('select#profile_member_edit').empty();
          $('select#profile_member_edit').append("<option value='' selected='selected'>[Please select]</option>");
          for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                name =  element['name'];
                surname =  element['surname'];
                text = name + " " + surname; 
                id =  element['id'];
                $('select#profile_member_edit').append("<option id="+id+" value="+text+">"+text+"</option>");
          } 
        } catch (err) {
            alert("Error running JS code from module: AJAXCallTypeTeam");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallTeamMember(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            $('input#profilename_edit').val(obj['name']);
            $('input#profilesurname_edit').val(obj['surname']);
            $('input#profilecellphone_edit').val(obj['cellphone']);
            $('input#profileemail_edit').val(obj['email']);
            $('textarea#profileaddress_edit').val(obj['address']);
            $('input#profileteamchannel_edit').val(obj['team']);
        } catch (err) {
            alert("Error running JS code from module: AJAXCallTeamMember Edit");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}


function AJAXCallCompaniesProfile(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        /*block('<img src="images/loader.gif" />');*/
    },
    success: function(data) {
        //unblock();
        try {
           var obj = JSON.parse(data);
           var count = obj.length;
           var element ="";
           var name = ""; 
           var id = "";
          if(count == 0){
            $('table#companyInfotable').hide();
            $('button#delete-company-profile-button').hide();
            $('button#update-company-profile-button').hide();
            $("p#nocompany").html('<a href="Javascript:AJAXCallModule(\'users\',\'manage_users\', \'\');">Click to add Company/Area and Outlet/</a>');
          }else{
            $('select#profilechannel_edit').empty();
            $('select#profilechannel_edit').append("<option value='' selected='selected'>[Please select]</option>");
            for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                name =  element['branch'];
                id =  element['id'];
                $('select#profilechannel_edit').append("<option id="+id+" value="+name+">"+name+"</option>");
            } 
            $("p#nocompany").empty();
            $('table#companyInfotable').show();
            $('button#delete-company-profile-button').show();
            $('button#update-company-profile-button').show();
          }
        } catch (err) {
            alert("Error running JS code from module: AJAXCallCompaniesProfile");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
       // unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallCompanyProfileInfo(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            $('input#companyprofilename_edit').val(obj['branch']);
            $('input#companyprofilecompanycode_edit').val(obj['prefix']);
            $('input#companyprofilecellphone_edit').val(obj['contact_number']);
            $('input#companyprofileemail_edit').val(obj['email']);
            $('textarea#companyprofileaddress_edit').val(obj['address']);
            $('select#companyprofiletheme_edit').val(obj['theme_colour']);
            $('select#companyprofileapprovalsetting_edit').val(obj['approval_setting']);
            $('textarea#companyprofilenotes_edit').val(obj['notes']);
            $('input#companyprofilechannel_edit').val(obj['channel_name']);
            $('input#companyprofile_id_edit').val(obj['branches_id']);
        } catch (err) {
            alert("Error running JS code from module: AJAXCallCompanyProfileInfo Edit");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
//Permissions
function AJAXCallCompanyProfilePermissions(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
           var obj = JSON.parse(data);
           var count = obj.length;
           var element ="";
           var name = ""; 
           var id = "";
          $('select#profilechannel_permissions').empty();
          $('select#profilechannel_permissions').append("<option  value='' selected='selected'>[Please select]</option>");
          for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                name =  element['branch'];
                id =  element['id'];
                $('select#profilechannel_permissions').append("<option id="+id+" value="+id+">"+name+"</option>");
          } 
        } catch (err) {
            alert("Error running JS code from module: AJAXCallCompanyProfilePermissions");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallCompanyProfileOwners(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
           var obj = JSON.parse(data);
           var count = obj.length;
           var element ="";
           var name = ""; 
           var surname = ""; 
           var fullname = ""; 
           var id = "";
          $('select#company_owner').empty();
          $('select#company_manageteam_user').empty();
          $('select#company_manageteam_user_2').empty();
          $('select#company_managenewsletter_user').empty();
          $('select#company_managenewsletter_user_2').empty();
          $('select#company_managenusers_user').empty();
          $('select#company_managenusers_user_2').empty();

          $('select#company_owner').append("<option id='' value='' selected='selected'>[Please select]</option>");
          $('select#company_manageteam_user').append("<option id='' value='' selected='selected'>[Please select]</option>");
          $('select#company_manageteam_user_2').append("<option id='' value='' selected='selected'>[Please select]</option>");
          $('select#company_managenewsletter_user').append("<option id='' value='' selected='selected'>[Please select]</option>");
          $('select#company_managenewsletter_user_2').append("<option id='' value='' selected='selected'>[Please select]</option>");
          $('select#company_managenusers_user').append("<option id='' value='' selected='selected'>[Please select]</option>");
          $('select#company_managenusers_user_2').append("<option id='' value='' selected='selected'>[Please select]</option>");


            var arrID = Array();
            var arrUsers = Array();
            var permissions = true;

            for (var i = 0; i < count ; i++) {
                if(obj[i] =="permissions"){
                    permissions = false;  
                    break; 
                } 

                if(permissions){
                    arrID.push(obj[i]);   
                }   
            }

            for (var i = 0; i < arrID.length ; i++) {
                //Do something
                element = arrID[i];
                name =  element['first'];
                surname =  element['last'];
                id =  element['id'];
                fullname = name + " " + surname;
                arrUsers.push(id);  
                $('select#company_owner').append("<option id="+id+" value="+id+">"+fullname+"</option>");
                $('select#company_manageteam_user').append("<option id="+id+" value="+id+">"+fullname+"</option>");
                $('select#company_manageteam_user_2').append("<option id="+id+" value="+id+">"+fullname+"</option>");
                $('select#company_managenewsletter_user').append("<option id="+id+" value="+id+">"+fullname+"</option>");
                $('select#company_managenewsletter_user_2').append("<option id="+id+" value="+id+">"+fullname+"</option>");
                $('select#company_managenusers_user').append("<option id="+id+" value="+id+">"+fullname+"</option>");
                $('select#company_managenusers_user_2').append("<option id="+id+" value="+id+">"+fullname+"</option>");
            } 

            var arrID = Array();
            permissions = false;
            for (var i = 0; i < count ; i++) {
                if(obj[i] =="permissions"){
                    permissions = true;  
                }
                if(permissions){
                    arrID.push(obj[i]);   
                }     
            }

           for (var i = 0; i < arrID.length ; i++) {
                //Do something
                element = arrID[i];
                profile_id =  element['user_id'];
                type =  element['type'];

                for (var r = 0; r < arrUsers.length ; r++) {
                    id =  arrUsers[r];
                    if(id ==profile_id && type =='Owner')$('select#company_owner').val(id);
                    if(id ==profile_id && type =='Teams')$('select#company_manageteam_user').val(id);
                    if(id ==profile_id && type =='Teams')$('select#company_manageteam_user_2').val(id);
                    if(id ==profile_id && type =='Newsletter')$('select#company_managenewsletter_user').val(id);
                    if(id ==profile_id && type =='Newsletter')$('select#company_managenewsletter_user_2').val(id);
                    if(id ==profile_id && type =='Users')$('select#company_managenusers_user').val(id);
                    if(id ==profile_id && type =='Users')$('select#company_managenusers_user_2').val(id);
                }
            }
        } catch (err) {
            alert("Error running JS code from module: AJAXCallCompanyProfilePermissions");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}


function AJAXCallAllCustomers(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            var count = obj.length;
            var element ="";
            $('select#customer_edit').empty();
            $('select#customer_edit').append("<option id='' value='' selected='selected'>[Please select]</option>");
            for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                name =  element['name'];
                surname =  element['surname'];
                text = name + " " +surname;
                id =  element['id'];
                $('select#customer_edit').append("<option id="+id+" value="+id+">"+text+"</option>");
            }   
        } catch (err) {
            alert("Error running JS code from module: AJAXCallAllCustomers");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallCustomers(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            var count = obj.length;
            var element ="";
            var list = "";

            var CustomerList = $('select#customer_channel').multiselect({
                placeholder: 'Select Customer'
            });

            for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                name =  element['name'];
                surname =  element['surname'];
                fullname =  name + " " + surname;
                id =  element['id'];
                list +="<option id="+id+" value="+id+">"+fullname+"</option>";
            }

            CustomerList.empty();
            if (data.length) CustomerList.append(list);
            CustomerList.multiselect('refresh');
            CustomerList.multiselect('update');
        } catch (err) {
            alert("Error running JS code from module: AJAXCallCustomers");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallCustomerInfo(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            var count = obj.length;
            var element ="";

            for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                name =  element['customer_name'];
                surname =  element['surname'];
                cellphone =  element['cellphone'];
                email =  element['email'];
                channel =  element['channel_name'];

                 $('input#customer_name_edit').val(name);
                 $('input#customer_surname_edit').val(surname);
                 $('input#customer_cell_edit').val(cellphone);
                 $('input#customer_email_edit').val(email);
                 $('input#customer_channel_edit').val(channel);
            }
        } catch (err) {
            alert("Error running JS code from module: AJAXCallCustomerInfo");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}

function AJAXCallFranchisesBranchesAdmin(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            var count = obj.length;
             var element ="";
            $('select#franchiseadmin_branch').empty();
            $('select#franchiseadmin_branch').append("<option id='' value='' selected='selected'>[Please select]</option>");
            $('select#franchiseadmin_branch').append("<option id='All' value='All' >All</option>");
            for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                name =  element['branch'];
                id =  element['id'];
                $('select#franchiseadmin_branch').append("<option id="+id+" value="+id+">"+name+"</option>");
            }   
        } catch (err) {
            alert("Error running JS code from module: AJAXCallFranchisesBranchesAdmin");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallCommercialBranches(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            var count = obj.length;
             var element ="";
            $('select#commercial_branch').empty();
            $('select#commercial_branch').append("<option id='' value='' selected='selected'>[Please select]</option>");
            $('select#commercial_branch').append("<option id='All' value='All' >All</option>");
            for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                name =  element['branch'];
                id =  element['id'];
                $('select#commercial_branch').append("<option id="+id+" value="+id+">"+name+"</option>");
            }   
        } catch (err) {
            alert("Error running JS code from module: AJAXCallCommercialBranches");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallRetailBranches(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            var count = obj.length;
             var element ="";
            $('select#retail_branch').empty();
            $('select#retail_branch').append("<option id='' value='' selected='selected'>[Please select]</option>");
            $('select#retail_branch').append("<option id='All' value='All' >All</option>");
            for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                name =  element['branch'];
                id =  element['id'];
                $('select#retail_branch').append("<option id="+id+" value="+id+">"+name+"</option>");
            }   
        } catch (err) {
            alert("Error running JS code from module: AJAXCallRetailBranches");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallFranchisesBranches(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            var count = obj.length;
             var element ="";

            $('select#franchise_branch').empty();
            $('select#franchise_branch').append("<option id='' value='' selected='selected'>[Please select]</option>");
            $('select#franchise_branch').append("<option id='All' value='All' >All</option>");
            for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                name =  element['branch'];
                id =  element['id'];
                $('select#franchise_branch').append("<option id="+id+" value="+id+">"+name+"</option>");
            }   
        } catch (err) {
            alert("Error running JS code from module: AJAXCallFranchisesBranches");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}


function AJAXCallNewsLetter(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            var count = obj.length;
             var element ="";
            $('select#newsletter_edit').empty();
            $('select#newsletter_edit').append("<option id='' value='' selected='selected'>[Please select]</option>");
            for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                name =  element['subject'];
                id =  element['id'];
                $('select#newsletter_edit').append("<option id="+id+" value="+id+">"+name+"</option>");
            }   
        } catch (err) {
            alert("Error running JS code from module: AJAXCallNewsLetter Edit");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallNewsLetterInfo(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            $('input#newslettertype_edit').val(obj['newsletter_type_name']);
            $('input#newslettersubject_edit').val(obj['subject']);
            $('input#newslettersentto_edit').val(obj['sent_to']);     
        } catch (err) {
            alert("Error running JS code from module: AJAXCallNewsLetterInfo Edit");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}

function AJAXCallNewsLetterType(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            $('input#addnewslettersubject').val(obj['subject']);
        } catch (err) {
            alert("Error running JS code from module: AJAXCallNewsLetterType");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallNewsLetterTypeInfo(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            $('input#newslettertype_edit').val(obj['newsletter_type']);
            $('textarea#newslettertypelayout_edit').val(obj['newsletter_layout']);
            $('select#newslettertypechannel_edit').val(obj['channel']);
            $('input#newslettertypechannel_edit').val(obj['channel']);     
        } catch (err) {
            alert("Error running JS code from module: AJAXCallCompanyProfileInfo Edit");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}


function AJAXCallChartInfo(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            $('input#charttitle_edit').val(obj['title']);
            $('textarea#chartdescription_edit').val(obj['description']);
            $('select#chartshow_edit').val(obj['show_chart']);
            $('select#chartchanneltype_edit').val(obj['channel_type']);    
        } catch (err) {
            alert("Error running JS code from module: AJAXCallChartInfo Edit");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}

function AJAXCallNotification(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            var count = obj.length;
             var element ="";
            $('select#notification_edit').empty();
            $('select#notification_edit').append("<option value='' selected='selected'>[Please select]</option>");
            for (var i = 0; i < count ; i++) {
                //Do something
                element = obj[i];
                name =  element['title'];
                id =  element['id'];
                $('select#notification_edit').append("<option id="+id+" value="+id+">"+name+"</option>");
            }   
        } catch (err) {
            alert("Error running JS code from module: AJAXCallNotification Edit");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}
function AJAXCallNotificationInfo(module, call, data) {
    jQuery.ajax({
    type: "POST",
    url: "index.php",
    data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
    beforeSend: function() {
        block('<img src="images/loader.gif" />');
    },
    success: function(data) {
        unblock();
        try {
            var obj = JSON.parse(data);
            $('input#title_edit').val(obj['title']);
            $('textarea#description_edit').val(obj['description']);
            $('input#branch_edit').val(obj['branch_name']);     
        } catch (err) {
            alert("Error running JS code from module: AJAXCallNotificationInfo Edit");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        unblock();
        alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
    },
    complete: function() {
        
    }
    });
}


function AJAXCallModuleJSOnly(module, call, data) {
    jQuery.ajax({
	type: "POST",
	url: "index.php",
	data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
	beforeSend: function() {
	    block("<img src='images/loader.gif' />");
	},
	success: function(status) {
	    unblock();
	    try {
		eval(status);
	    } catch (err) {
		alert('Error running JS code from module: AJAXCallModuleJSOnly');
	    }
	},
	error: function(jqXHR, textStatus, errorThrown) {
	    unblock();
	    alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
	},
	complete: function() {
	    
	}
    });
}

function checkPrePostLogin() {
    if ($('#tpm_passwd').val() == "") {
	$("#tpm_passwd").focus().animate({'backgroundColor' : '#aa0000'},500).animate({'backgroundColor' : '#ffffff'},500);
    }
    if ($('#tpm_userid').val() == "") {
	$("#tpm_userid").focus().animate({'backgroundColor' : '#aa0000'},500).animate({'backgroundColor' : '#ffffff'},500);
    }

    if ($('#tpm_userid').val() == "" || $('#tpm_passwd').val() == "") {
	return false;
    }
    return true;
}

function block(displayMessage) {
    jQuery.blockUI({
        message: displayMessage,
        css: { border: 'none', backgroundColor: 'transparent', opacity: 1 },
	overlayCSS: { backgroundColor: '#E6E6E6', opacity: 0.7 }
    });
}

function unblock() {
    jQuery.unblockUI();
}

var typewatch = (function(){
  var timer = 0;
  return function(callback, ms){
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
  }  
})();

function blockDialog(id, buiwidth) {
    
    var bodyWidth = jQuery(window).width();
    var myWidth	  = buiwidth ? buiwidth : '375px';
    var myLeft	  = isNaN(parseFloat(myWidth)) ? '35%' : ((bodyWidth - parseFloat(myWidth))/2) + 'px';
    
    jQuery.blockUI({
        message: jQuery(id),
        overlayCSS:  { cursor: 'default' },
        css: { width: myWidth, top: '16%', left: myLeft, textAlign: 'left', border: '3px solid #777777', cursor: 'default' },
        fadeIn: 0,
        fadeOut: 0
    });
}

function alert(message) {
    var objid = $("*:focus").attr("id");
    $( "#alert-dialog" ).html(message);
    $( "#dialog:ui-dialog" ).dialog( "destroy" );
    $( "#alert-dialog" ).dialog({
	title: "Alert",
	modal: true,
	width: 400,
	resizable: false,
	buttons: {
	    Ok: function() {
		$( this ).dialog( "close" );
		$('#' + objid).focus();
	    }
	}
    });
}

