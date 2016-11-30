// Most of the following code will be moved to users.php

function ChangeTeamSelectionProfiles(obj) {
    var id = $(obj).find(':selected').attr('id'); 
    if(id > 0)AJAXCallModule("Users",'view_team_profiles', 'Team='+id);
}


jQuery(function () {
    jQuery(".toggle-branch-list").live ("click", function () {
        if(jQuery("#branch-list").is(":visible")) {
            jQuery("#branch-list").hide(0);
            jQuery(this).attr("src", "images/treeClosed.gif");
        } else {
            jQuery("#branch-list").show(0);
            jQuery(this).attr("src", "images/treeOpen.gif");
        }
    });
    
    jQuery(".all-branches").live ("click", function () {
        jQuery(".all-branches").removeClass("selected-branches");
        jQuery(this).addClass("selected-branches");
        //jQuery("#main-branch-raquo").html("&nbsp;&nbsp;&nbsp;&raquo;&nbsp;&nbsp;&nbsp;" + jQuery(this).children(".branch-names").val());
        //jQuery("#branch-description").html(jQuery(this).children(".branch-descriptions").val());
        var chosenBranch = jQuery(this).children(".branch-ids").val();
        //jQuery("#chosen-branch").val(chosenBranch);
        //jQuery("#permissions-li, #upermissions-tab").show(0);
        AJAXCallModuleJSOnly("Users","printTabContent", "branchid=" + chosenBranch + "&page=1");
    });

    jQuery(".main-branch").live ("click", function () {
        jQuery("#branch-more-actions").hide(0);
        jQuery("#chosen-branch").val("0");
        jQuery(".all-branches").removeClass("selected-branches");
        jQuery("#main-branch-raquo").html("");
        jQuery("#branch-description").html("Super administrators");
        jQuery("#permissions-li, #upermissions-tab").hide(0);
        jQuery("#tabs").tabs( "select" , 0 );
        AJAXCallModuleJSOnly("Users","printTabContent", "branchid=0&page=1");
    });
    
    jQuery("#add-branch").live ("click", function () {
        jQuery("#create-branch-error").html("&nbsp;");
        jQuery("#new-branchname").val('');
        jQuery("#new-branchaddress").val('');;
        jQuery("#new-branchcode").val('');
        //jQuery("#create-branch-title").html("Add a branch");
       // jQuery("#save-branch").html("Add branch");
        blockDialog("#create-branch-dialog");
    });
    
    jQuery("#add-store").live ("click", function () {
        jQuery("#create-store-error").html("&nbsp;");
        jQuery("#new-storename").val('');
        jQuery("#new-storeaddress").val('');
        jQuery("#new-contactnumber").val('');
        jQuery("#new-manager").val(''); 
        jQuery("#new-storecode").val('');    
        jQuery("#new-gpscoords").val('');
        jQuery("#new-regionid").val('');
        //jQuery("#create-store-title").html("Add a store");
        //jQuery("#save-store").html("Add store");
        blockDialog("#create-store-dialog");
    });
	
	jQuery("#add-channel").live ("click", function () {
        jQuery("#create-channel-error").html("&nbsp;");
        jQuery("#new-channelname").val('');
        //jQuery("#create-channel-title").html("Add a Channel");
        //jQuery("#save-channel").html("Add Channel");   
        blockDialog("#create-channel-dialog");
    });
    
    jQuery("#save-branch").live ("click", function () {
        var branchname = jQuery.trim(jQuery("#new-branchname").val());
        var branchaddress = jQuery.trim(jQuery("#new-branchaddress").val());
        var branchcode = jQuery.trim(jQuery("#new-branchcode").val());
        var branchchannel = jQuery.trim(jQuery("#new-branchchannel").val());
        var approvalsetting = jQuery.trim(jQuery("#new-approvalsetting").find("option:selected").val());
   
        if (branchname == "") {
            jQuery("#create-branch-error").html("Please enter a valid name");
        }else if (branchaddress == "") {
            jQuery("#create-branch-error").html("Please enter a valid  address");
        }else if (approvalsetting == "") {
            jQuery("#create-branch-error").html("Please select a valid approval setting");
        }else if (branchcode == "") {
            jQuery("#create-branch-error").html("Please enter a valid code");
        }else if (branchchannel == ""|| branchchannel == "0") {
            jQuery("#create-branch-error").html("Please enter a valid channel");
        } else {
           var edit = (jQuery(this).text()).indexOf("Add") > -1 ? 0 : 1;
            AJAXCallModuleJSOnly("Users", "addNewBranch", "branchid="+jQuery("#chosen-branch").val()+"&branchname="+encodeURIComponent(branchname)+"&branchchannel="+encodeURIComponent(branchchannel)+"&branchaddress="+encodeURIComponent(branchaddress)+"&approvalsetting="+encodeURIComponent(approvalsetting)+"&branchcode="+encodeURIComponent(branchcode)+"&edit="+edit);
            location.href = 'Javascript: AJAXCallModule("users","manage_users", "");';
        }
    });

    jQuery("#save-store").live ("click", function () {
        var storename = jQuery.trim(jQuery("#new-storename").val());
		var gpscoords = jQuery.trim(jQuery("#new-gpscoords").val());
        if (gpscoords == "")gpscoords ="0,0";
        var storeaddress = jQuery.trim(jQuery("#new-storeaddress").val());
        var contactnumber = jQuery.trim(jQuery("#new-contactnumber").val());
        var storecode = jQuery.trim(jQuery("#new-storecode").val());
        var manager = jQuery.trim(jQuery("#new-manager").val());
        var regionid = jQuery.trim(jQuery("#new-regionid").val());
        var branchid = jQuery.trim(jQuery("#new-branchid").val());
        if (storename == "") {
            jQuery("#create-store-error").html("Please enter a valid name");
        }else if (gpscoords == "") {
            jQuery("#create-store-error").html("Please enter a valid GPS co-ordinates");
        }else if (storeaddress == "") {
            jQuery("#create-store-error").html("Please enter a valid address");
        }else if (storecode == "") {
            jQuery("#create-store-error").html("Please enter a valid code");
        }else if (contactnumber == "") {
            jQuery("#create-store-error").html("Please enter a valid contact number");
        }else if (manager == "") {
            jQuery("#create-store-error").html("Please enter a valid manager");
        }else {
            var edit = (jQuery(this).text()).indexOf("Add") > -1 ? 0 : 1;  
            AJAXCallModuleJSOnly("Users", "addNewStore", "branchid="+encodeURIComponent(branchid)+"&storename="+encodeURIComponent(storename)+"&gpscoords="+encodeURIComponent(gpscoords)+"&storeaddress="+encodeURIComponent(storeaddress)+"&contactnumber="+encodeURIComponent(contactnumber)+"&storecode="+encodeURIComponent(storecode)+"&manager="+encodeURIComponent(manager)+"&regionid="+encodeURIComponent(regionid)+"&edit="+edit);
            location.href = 'Javascript: AJAXCallModule("users","manage_users", "");';
        }
    });

	jQuery("#save-channel").live ("click", function () {
        var name = jQuery.trim(jQuery("#new-channelname").val());
        var channeltypeCheck =  $('input[name=new-channeltype]').is(":checked");
        var valid = false; 
        var channeltype = "";

        if(channeltypeCheck){
        valid = true;
        channeltype = $('input[name=new-channeltype]:checked').val();
        }  

        if (valid == false) {
            jQuery("#create-channel-error").html("Please select one type of channel");
            $('#new-channelretail').is(":checked");

        }else if(name == ""){
            jQuery("#create-channel-error").html("Please enter a valid channel name");
        }else {
            var edit = (jQuery(this).text()).indexOf("Add") > -1 ? 0 : 1;
            AJAXCallModuleJSOnly("Users", "addNewChannel", "name="+encodeURIComponent(name)+"&channeltype="+channeltype);
            location.href = 'Javascript: AJAXCallModule("users","manage_users", "");';
        }
    });

    jQuery("#show-create-user-anchor").live("click", function(){
        if(jQuery("#create-user-password-tr").is(":visible")) {
            jQuery("#create-user-password-tr").css("display", "none");
            jQuery("#edit-user-password").val("0");
            jQuery("#show-create-user-anchor").html("Change Password");
        } else {
            jQuery("#create-user-password-tr").css("display", "table-row");
            jQuery("#edit-user-password").val("1");
            jQuery("#show-create-user-anchor").html("Don't change Password");
        }
    });
    
    jQuery("#create-user").live ("click", function () {
        jQuery("#create-user-error").html("&nbsp;");
        jQuery("#create-user-password").html("Password");
        jQuery("#activate-user-span").html("&nbsp;Activate now");
        jQuery("#create-user-password-tr").css("display", "table-row");
        jQuery("#show-create-user-password").css("display", "none");
        jQuery(".disable-create-user").removeAttr("disabled");
        jQuery("#save-user").removeAttr("disabled");
        jQuery("#new-branch option[value=0]").attr("selected", true);
        jQuery("#save-user").html("Create user");
        jQuery("#new-firstname").val('');
        jQuery("#new-lastname").val('');
        jQuery("#new-email").val('');
        jQuery("#new-username").val('');
        jQuery("#new-password1").val('');
        jQuery("#new-password2").val('');
        jQuery("#edit-user-password").val('1');
        jQuery("#new-user-active").attr("checked", true);
        if (jQuery("#chosen-branch").val() == "0") {
            jQuery("#new-user-active").attr("disabled", true);
            jQuery("#show-branch-tr").css("display", "table-row");
            jQuery("#create-user-title").html("Create Super admin");
        }
        else{
            jQuery("#new-user-active").removeAttr("disabled");
            jQuery("#show-branch-tr").css("display", "none");
            jQuery("#create-user-title").html("Create a new user");
        }
        blockDialog("#create-user-dialog");
    });
    
    jQuery(".close-dialog").live ("click", function() {
        jQuery.unblockUI({ fadeOut: 0 });
        return false;
    });
    
    jQuery("#save-user").live ("click", function () {
        var firstname = jQuery.trim(jQuery("#new-firstname").val());
        var lastname = jQuery.trim(jQuery("#new-lastname").val());
        var email = jQuery.trim(jQuery("#new-email").val());
        var username = jQuery.trim(jQuery("#new-username").val());
        var password1 = jQuery("#new-password1").val();
        var password2 = jQuery("#new-password2").val();
        var branchid = jQuery("#chosen-branch").val();
        var storeid = jQuery("#storeid").val();
        var newbranchid = jQuery("#new-branch").val();
        var active = jQuery("#new-user-active").is(":checked") ? 1 : 0;
        var pattern = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
        
        var edit = (jQuery(this).text()).indexOf("Create") > -1 ? 0 : 1;
        var userid = edit == 1 ? jQuery("#edit-user-id").val() : "0";
        var checkpassword = jQuery("#edit-user-password").val();
        
        if (firstname == "") {
            jQuery("#create-user-error").html("Please enter a valid first name");
        } else if (lastname == "") {
            jQuery("#create-user-error").html("Please enter a valid last name");
        }  else if (email.length > 0 && !pattern.test(email)) {
            //if (!pattern.test(email)) {
                jQuery("#create-user-error").html("Please enter a valid email address");
            //}
        } else if (username.length < 3) {
            jQuery("#create-user-error").html("Please provide a longer username");
        } else if (password1.length < 5 &&  checkpassword == 1) {
            jQuery("#create-user-error").html("Please provide a longer password");
        } else if (password1 != password2 &&  checkpassword == 1) {
            jQuery("#create-user-error").html("The two passwords do not match");
        } else {
            AJAXCallModuleJSOnly("Users","createNewUser", "firstname="+encodeURIComponent(firstname)+"&lastname="+encodeURIComponent(lastname)+"&email="+encodeURIComponent(email)+"&username="+encodeURIComponent(username)+"&password="+encodeURIComponent(password1)+"&branchid="+branchid+"&storeid="+storeid+"&newbranchid="+newbranchid+"&active="+active+"&edit="+edit+"&userid="+userid+"&cp="+checkpassword);
        }
    });
    
    jQuery("#check-all-users").live ("change", function () {
        if(jQuery(this).is(":checked")) {
            jQuery(".user-checkboxes:enabled").attr("checked", true);
        } else {
            jQuery(".user-checkboxes:enabled").removeAttr("checked");
        }
    });
    
    jQuery("#user-more-actions").live ("click", function () {
        jQuery("#user-actions").css("display", "inline-block");
    });
    
    jQuery("#user-more-group").live ("click", function () {
        if(jQuery(".user-checkboxes:checked").length == 0) {
            alert("No users seleted!");
        } else {
            jQuery("#add-to-group-select").val(jQuery("#add-to-group-select option:first").val());
            blockDialog("#user-group-dialog");
        }
    });
    
    jQuery("#add-user-to-group").live ("click", function () {
        var allSelected = jQuery("#add-to-group-select").val();
        var groups = "";
        var users = "";
        for (i in allSelected) {
            groups += allSelected[i] + "|";
        }
        jQuery(".user-checkboxes:checked").each(function (index) {
            users += jQuery(this).val() + "|";
        });
        AJAXCallModuleJSOnly("Users","saveInGroup", "users=" + users + "&groups=" + groups);
    });
    
    jQuery(".user-checkboxes, #check-all-users").live ("change", function () {
        if(jQuery(".user-checkboxes:checked").length > 0) {
            jQuery("#user-more-actions, #move-user").removeAttr("disabled");
        } else {
            jQuery("#user-more-actions, #move-user").attr("disabled", true);
        }
    });
    
    jQuery("#user-more-admin").live ("click", function () {
        if(jQuery(".user-checkboxes:checked").length == 0) {
            alert("No users seleted!");
        } else {
            if(confirm("This will grant access to all system modules. This action is irreversible.")){
                var selected_users = "";
                jQuery(".user-checkboxes:checked").each(function (index) {
                    selected_users += jQuery(this).val() + "|";
                });
                AJAXCallModuleJSOnly("Users","makeSuperadmin", "users=" + selected_users);
            }
        }
    });
    
    jQuery(".useranchor").live ("click", function () {
        var jObjid = jQuery(this).attr("id");
        var userid = jObjid.substring(jObjid.lastIndexOf("-")+1);
        jQuery("#edit-user-id").val(userid);
        
        //var jQObj = jQuery("#all-branches-" + jQuery("#chosen-branch").val());
        //
        //jQuery("#create-user-error").html("");
        //jQuery("#new-branchname").val(jQObj.children(".branch-names").val());
        //jQuery("#new-branchdescription").val(jQObj.children(".branch-descriptions").val());
        //jQuery("#new-branchaddress").val(jQObj.children(".branch-addresses").val());
        //
        //jQuery("#create-user-title").html("Edit user");
        //jQuery("#save-user").html("Save changes");
        //
        //blockDialog("#create-user-dialog");
        AJAXCallModuleJSOnly("Users","editUser", "userid=" + userid);
    });
    
    jQuery("#user-more-deactivate").live ("click", function () {
        if(jQuery(".user-checkboxes:checked").length == 0) {
            alert("No users seleted!");
        } else {
            if(confirm("Are you sure you want to deactivate all selected users?")){
                var selected_users = "";
                jQuery(".user-checkboxes:checked").each(function (index) {
                    selected_users += jQuery(this).val() + "|";
                });
                AJAXCallModuleJSOnly("Users","deactivateUser", "users=" + selected_users);
            }
        }
    });
    
    jQuery("#branch-more-actions").live ("click", function () {
        var jQObj = jQuery("#branch-more-actions-div");
        if(jQObj.is(":visible")) {
            jQObj.css("display", "none");
        } else {
            jQObj.css("display", "inline-block");
        }
        //jQuery(this).addClass("dropdownbuttonclicked");
    });
    
    jQuery("#edit-branch").live ("click", function () {
        
        var jQObj = jQuery("#all-branches-" + jQuery("#chosen-branch").val());
        jQuery("#create-branch-error").html("&nbsp;");
        jQuery("#new-branchname").val(jQObj.children(".branch-names").val());
        jQuery("#new-branchaddress").val(jQObj.children(".branch-addresses").val());
		
		//alert("branch : "+jQObj.children(".branch-channels").val());
		jQuery("#new-branchchannel").val(jQObj.children(".branch-channels").val());
        
        jQuery("#create-branch-title").html("Edit branch");
        jQuery("#save-branch").html("Save changes");
        
        blockDialog("#create-branch-dialog");
    });
    
    jQuery("#move-user").live ("click", function () {
        jQuery(".branch-move").removeClass("selected-move-to-branch");
        jQuery(".first-move-to-branch-div").addClass("selected-move-to-branch");
        
        jQuery("#selected-move-to-branch").val(jQuery(".first-move-to-branch-hidden").val());
        
        if(jQuery(".user-checkboxes:checked").length == 0){
            jQuery("#move-user-button").attr("disabled", true);
        } else {
            jQuery("#move-user-button").removeAttr("disabled");
        }
        
        jQuery("#branch-move-to").css("display", "inline-block");
    });
    
    jQuery(".branch-move").live ("click", function() {
        jQuery(".branch-move").removeClass("selected-move-to-branch");
        jQuery(this).addClass("selected-move-to-branch");
        jQuery("#selected-move-to-branch").val(jQuery(this).children(".selected-branch-move").val());
    });
    
    jQuery("#move-user-button").live ("click", function () {
        var selected_users = "";
        jQuery(".user-checkboxes:checked").each(function (index) {
            selected_users += jQuery(this).val() + "|";
        });
        AJAXCallModuleJSOnly("Users","moveUsers", "users="+selected_users+"&storeid=" + jQuery("#selected-move-to-branch").val());
    });
    
    jQuery("#delete-branch").live ("click", function () {
        if(confirm("Are you sure you want to delete this branch?")){
            AJAXCallModuleJSOnly("Users","deleteBranch", "branchid=" + jQuery("#chosen-branch").val());
        }
    });
    
    jQuery(".useranchor, .useranchor-no-edit").live ("mouseover", function () { 
        //jQuery(this).parents('tr').children('td').addClass('trhighlight');
        //jQuery(this).parents('tr').children('td').addClass('trhighlight');
        //var jObjid = jQuery(this).attr("id");
        //var userid = jObjid.substring(jObjid.lastIndexOf("-")+1);
        //jQuery("#user-tr-" + userid).addClass('trhighlight');
        jQuery(this).parent('td').parent('tr').addClass('trhighlight');
    });
    
    jQuery(".useranchor, .useranchor-no-edit").live ("mouseout", function(){
        //jQuery(this).parents('tr').children('td').removeClass('trhighlight');
        //var jObjid = jQuery(this).attr("id");
        //var userid = jObjid.substring(jObjid.lastIndexOf("-")+1);
        //jQuery("#user-tr-" + userid).removeClass('trhighlight');
        jQuery(this).parent('td').parent('tr').removeClass('trhighlight');
    });
    
    jQuery("#branch-users-permissions").live ("change", function () {
        jQuery(".group-permission-main-checkbox, .group-permission-sub-checkbox").removeAttr("checked");
        jQuery(".group-permissions-td").html("");
        
        if (jQuery(this).val() == 0) {
            jQuery(".group-permission-main-checkbox, .group-permission-sub-checkbox, #save-user-permissions-button").attr("disabled", true);
        } else {
            AJAXCallModuleJSOnly("Users","getUserPermissions", "userid=" + jQuery(this).val());
        }
    });
    
    jQuery("#save-user-permissions-button").live ("click", function () {
        var userpermissions = "";
        jQuery(".group-permission-sub-checkbox:enabled:checked").each(function(i){
            userpermissions += jQuery(this).val() + "|";
        });
        AJAXCallModuleJSOnly("Users","saveUserPermissions", "userid=" + jQuery("#branch-users-permissions").val()+"&permissions="+userpermissions);
    });
    
    jQuery("body").click (function(e) {
        if((e.target.className).indexOf("branch-move") == -1) {
            jQuery(".branch-more-actions-div").css("display", "none");
        }
    });
});
var intvl;
function getActiveUsers(module, call, data) {
    if (jQuery('#online-user').length){
        jQuery.ajax({
            type: "POST",
            url: "index.php",
            data: "aj=1&module="+module+"&call="+call+"&" + encodeURI(data),
            success: function(status) {
                try {
                    eval(status);
                } catch (err) {
                    alert('Error running JS code from module');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                    alert("url: " + url + " error: " + jqXHR.responseText + " status: " + textStatus + " errorThrown: " + errorThrown);
            },
            complete: function() {
                
            }
        });
    } else {
        intvl = window.clearInterval(intvl);
    }
}

function ListStores(branchid) {
    //alert(branchid);
    AJAXCallModuleJSOnly("Users","listStores", "branchid=" + branchid);
}

function selectPerson(obj) {
    AJAXCallModule('Techitems','edit_tech_info', 'TechType=' + encodeURIComponent($(obj).val()));
}

function GetStore(branchid, storeid) {
    jQuery(".branch-store-item").removeClass("selected-branches");
    jQuery("#branch-store-item-" + storeid).addClass("selected-branches");
    console.log("storeid:" + storeid);
    AJAXCallModuleJSOnly("Users","printTabContent", "branchid=" + branchid + "&storeid=" + storeid + "&page=1");
    
    //AJAXCallModuleJSOnly("Users","showStore", "branchid=" + branchid + "&storeid=" + storeid);
}