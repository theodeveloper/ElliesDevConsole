// Most of the following code will be moved to groups.php

jQuery(function () {
    jQuery("#check-all-groups").live ("change", function () {
        if(jQuery(this).is(":checked")) {
            jQuery(".group-checkbox:enabled").attr("checked", true);
        } else {
            jQuery(".group-checkbox:enabled").removeAttr("checked");
        }
    });
    
    jQuery(".group-checkbox, #check-all-groups").live ("change", function () {
        if(jQuery(".group-checkbox:checked").length > 0) {
            jQuery(".delete-groups").removeAttr("disabled");
        } else {
            jQuery(".delete-groups").attr("disabled", true);
        }
    });
    
    jQuery(".delete-groups").live ("click", function () {
        var no_selected = jQuery(".group-checkbox:checked").length;
        var s = no_selected > 1 ? "s" : "";
        if(confirm("Delete " + no_selected + " selected group" + s + "? This will remove all group permissions from current group members.")) {
            var selected_groups = "";
            jQuery(".group-checkbox:checked").each(function (index) {
                selected_groups += jQuery(this).val() + "|";
            });
            AJAXCallModuleJSOnly("Groups","deleteGroups", "groups=" + selected_groups);
        }
    });
    
    jQuery("#delete-this-group").live ("click", function () {
        if(confirm("Delete group? This will remove all group permissions from current group members.")) {
            AJAXCallModuleJSOnly("Groups","deleteGroups", "groups=" + jQuery("#groupid").val());
        }
    });
    
    jQuery("#create-group").live ("click", function () {
        AJAXCallModule("Groups","editCreateGroup", "edit=0");
    });
    
    jQuery("#back-to-groups, #cancel-group").live ("click", function () {
        AJAXCallModule('Groups','printGroupList','page=1');
    });
    
    jQuery(".groupanchor").live ("click", function () {
        var jObjid = jQuery(this).attr("id");
        var groupid = jObjid.substring(jObjid.lastIndexOf("-")+1);
        AJAXCallModule("Groups","editCreateGroup", "edit=1&groupid=" + groupid);
    });
    
    jQuery("#all-branch-group").live ("change", function () {
        var groupid = jQuery("#groupid").val();
        var edit = groupid == 0 ? 0 : 1;
        AJAXCallModuleJSOnly("Groups","filterGroupBranch", "branchid=" + jQuery(this).val()+"&groupid="+groupid+"&edit="+edit);
    });
    
    jQuery("#moveright, #moveleft").live ("click", function() {
        var id = jQuery(this).attr("id");
        var selectFrom = id == "moveright" ? "#user-branch-group-left" : "#user-branch-group-right";
        var moveTo = id == "moveright" ? "#user-branch-group-right" : "#user-branch-group-left";
        var selectedItems = jQuery(selectFrom + " :selected").toArray();
        jQuery(moveTo).append(selectedItems);
        selectedItems.remove;
    });
    
    jQuery("#save-group-button").live ("click", function () {
        var groupname = jQuery.trim(jQuery("#group-name").val());
        if(groupname == "") {
            jQuery("#group-name").addClass("error-input");
            jQuery("#error-message").html("Please provide a group name").show(0).delay(4000).hide(0);
        } else {
            jQuery("#group-name").removeClass("error-input");
            var groupid = jQuery("#groupid").val();
            var edit = groupid == 0 ? 0 : 1;
            
            var members = "";
            jQuery("#user-branch-group-left option").each ( function (index) {
                members += jQuery(this).val() + "|";
            });
            AJAXCallModuleJSOnly("Groups","saveGroup", "groupname="+encodeURIComponent(jQuery("#group-name").val())+"&groupdescription="+encodeURIComponent(jQuery("#group-description").val())+"&groupid="+groupid+"&members="+members+"&edit="+edit);
        }
    });
    
    jQuery(".group-permission-main-checkbox").live ("change", function () {
        var jQObj = jQuery(this);
        if(jQObj.is(":checked"))
            jQObj.closest("table").find(".group-permission-sub-checkbox:enabled").attr("checked", true);
        else
            jQObj.closest("table").find(".group-permission-sub-checkbox:enabled").removeAttr("checked");
    });
    
    jQuery("#save-group-permissions-button").live ("click", function () {
        var permissions = "";
        jQuery(".group-permission-sub-checkbox:checked").each ( function (index) {
            permissions += jQuery(this).val() + "|";
        });
        AJAXCallModuleJSOnly("Groups", "saveGroupPermissions", "groupid=" + jQuery("#groupid").val() + "&permissions=" + permissions);
    });
    
    //jQuery(".toggle-branch-list").live ("click", function () {
    //    if(jQuery("#branch-list").is(":visible")) {
    //        jQuery("#branch-list").hide(0);
    //        jQuery(this).attr("src", "images/treeClosed.gif");
    //    } else {
    //        jQuery("#branch-list").show(0);
    //        jQuery(this).attr("src", "images/treeOpen.gif");
    //    }
    //});
    //
    //jQuery(".all-branches").live ("click", function () {
    //    jQuery("#branch-more-actions").show(0);
    //    jQuery(".all-branches").removeClass("selected-branches");
    //    jQuery(this).addClass("selected-branches");
    //    jQuery("#main-branch-raquo").html("&nbsp;&nbsp;&nbsp;&raquo;&nbsp;&nbsp;&nbsp;" + jQuery(this).children(".branch-names").val());
    //    jQuery("#branch-description").html(jQuery(this).children(".branch-descriptions").val());
    //    var chosenBranch = jQuery(this).children(".branch-ids").val();
    //    jQuery("#chosen-branch").val(chosenBranch);
    //    AJAXCallModuleJSOnly("Users","printTabContent", "branchid=" + chosenBranch);
    //});
    //
    //jQuery(".main-branch").live ("click", function () {
    //    jQuery("#branch-more-actions").hide(0);
    //    jQuery("#chosen-branch").val("0");
    //    jQuery(".all-branches").removeClass("selected-branches");
    //    jQuery("#main-branch-raquo").html("");
    //    jQuery("#branch-description").html("Super administrators");
    //    AJAXCallModuleJSOnly("Users","printTabContent", "branchid=0");
    //});
    //
    //jQuery("#add-branch").live ("click", function () {
    //    jQuery("#create-branch-error").html("");
    //    jQuery("#new-branchname").val('');
    //    jQuery("#new-branchdescription").val('');
    //    jQuery("#new-branchaddress").val('');
    //    
    //    jQuery("#create-branch-title").html("Add a branch");
    //    jQuery("#save-branch").html("Add branch");
    //    
    //    blockDialog("#create-branch-dialog");
    //});
    //
    //jQuery("#save-branch").live ("click", function () {
    //    var branchname = jQuery.trim(jQuery("#new-branchname").val());
    //    var branchaddress = jQuery.trim(jQuery("#new-branchaddress").val());
    //    var branchdescription = jQuery.trim(jQuery("#new-branchdescription").val());
    //    
    //    if (branchname == "") {
    //        jQuery("#create-branch-error").html("Please enter a valid branch name");
    //    } else {
    //        var edit = (jQuery(this).text()).indexOf("Add") > -1 ? 0 : 1;
    //        AJAXCallModuleJSOnly("Users", "addNewBranch", "branchid="+jQuery("#chosen-branch").val()+"&branchname="+branchname+"&branchaddress="+branchaddress+"&branchdescription="+branchdescription+"&edit="+edit);
    //    }
    //});
    //
    //jQuery("#show-create-user-anchor").live("click", function(){
    //    if(jQuery("#create-user-password-tr").is(":visible")) {
    //        jQuery("#create-user-password-tr").css("display", "none");
    //        jQuery("#edit-user-password").val("0");
    //        jQuery("#show-create-user-anchor").html("Change Password");
    //    } else {
    //        jQuery("#create-user-password-tr").css("display", "table-row");
    //        jQuery("#edit-user-password").val("1");
    //        jQuery("#show-create-user-anchor").html("Don't change Password");
    //    }
    //});
    //
    //jQuery("#create-user").live ("click", function () {
    //    jQuery("#create-user-error").html("");
    //    jQuery("#create-user-password").html("Password");
    //    jQuery("#activate-user-span").html("&nbsp;Activate now");
    //    jQuery("#create-user-password-tr").css("display", "table-row");
    //    jQuery("#show-create-user-password").css("display", "none");
    //    jQuery(".disable-create-user").removeAttr("disabled");
    //    jQuery("#save-user").removeAttr("disabled");
    //    jQuery("#new-branch option[value=0]").attr("selected", true);
    //    jQuery("#save-user").html("Create user");
    //    jQuery("#new-firstname").val('');
    //    jQuery("#new-lastname").val('');
    //    jQuery("#new-email").val('');
    //    jQuery("#new-username").val('');
    //    jQuery("#new-password1").val('');
    //    jQuery("#new-password2").val('');
    //    jQuery("#edit-user-password").val('1');
    //    jQuery("#new-user-active").attr("checked", true);
    //    if (jQuery("#chosen-branch").val() == "0") {
    //        jQuery("#new-user-active").attr("disabled", true);
    //        jQuery("#show-branch-tr").css("display", "table-row");
    //        jQuery("#create-user-title").html("Create Super admin");
    //    }
    //    else{
    //        jQuery("#new-user-active").removeAttr("disabled");
    //        jQuery("#show-branch-tr").css("display", "none");
    //        jQuery("#create-user-title").html("Create a new user");
    //    }
    //    blockDialog("#create-user-dialog");
    //});
    //
    //jQuery(".close-dialog").live ("click", function() {
    //    jQuery.unblockUI({ fadeOut: 0 });
    //    return false;
    //});
    //
    //jQuery("#save-user").live ("click", function () {
    //    var firstname = jQuery.trim(jQuery("#new-firstname").val());
    //    var lastname = jQuery.trim(jQuery("#new-lastname").val());
    //    var email = jQuery.trim(jQuery("#new-email").val());
    //    var username = jQuery.trim(jQuery("#new-username").val());
    //    var password1 = jQuery.trim(jQuery("#new-password1").val());
    //    var password2 = jQuery.trim(jQuery("#new-password2").val());
    //    var branchid = jQuery("#chosen-branch").val();
    //    var newbranchid = jQuery("#new-branch").val();
    //    var active = jQuery("#new-user-active").is(":checked") ? 1 : 0;
    //    var pattern = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
    //    
    //    var edit = (jQuery(this).text()).indexOf("Create") > -1 ? 0 : 1;
    //    var userid = edit == 1 ? jQuery("#edit-user-id").val() : "0";
    //    var checkpassword = jQuery("#edit-user-password").val();
    //    
    //    if (firstname == "") {
    //        jQuery("#create-user-error").html("Please enter a valid first name");
    //    } else if (lastname == "") {
    //        jQuery("#create-user-error").html("Please enter a valid last name");
    //    }  else if (!pattern.test(email)) {
    //        jQuery("#create-user-error").html("Please enter a valid email address");
    //    } else if (username.length < 3) {
    //        jQuery("#create-user-error").html("Please provide a longer username");
    //    } else if (password1.length < 5 &&  checkpassword == 1) {
    //        jQuery("#create-user-error").html("Please provide a longer password");
    //    } else if (password1 != password2 &&  checkpassword == 1) {
    //        jQuery("#create-user-error").html("The two passwords do not match");
    //    } else {
    //        AJAXCallModuleJSOnly("Users","createNewUser", "firstname="+firstname+"&lastname="+lastname+"&email="+email+"&username="+username+"&password="+password1+"&branchid="+branchid+"&newbranchid="+newbranchid+"&active="+active+"&edit="+edit+"&userid="+userid+"&cp="+checkpassword);
    //    }
    //});
    //
    //jQuery("#check-all-users").live ("change", function () {
    //    if(jQuery(this).is(":checked")) {
    //        jQuery(".user-checkboxes:enabled").attr("checked", true);
    //    } else {
    //        jQuery(".user-checkboxes:enabled").removeAttr("checked");
    //    }
    //});
    //
    //jQuery("#user-more-actions").live ("click", function () {
    //    jQuery("#user-actions").css("display", "inline-block");
    //});
    //
    //jQuery("#user-more-group").live ("click", function () {
    //    if(jQuery(".user-checkboxes:checked").length == 0) {
    //        alert("No users seleted!");
    //    } else {
    //        blockDialog("#user-group-dialog");
    //    }
    //});
    //
    //jQuery(".user-checkboxes, #check-all-users").live ("change", function () {
    //    if(jQuery(".user-checkboxes:checked").length > 0) {
    //        jQuery("#user-more-actions, #move-user").removeAttr("disabled");
    //    } else {
    //        jQuery("#user-more-actions, #move-user").attr("disabled", true);
    //    }
    //});
    //
    //jQuery("#user-more-admin").live ("click", function () {
    //    if(jQuery(".user-checkboxes:checked").length == 0) {
    //        alert("No users seleted!");
    //    } else {
    //        if(confirm("This will grant access to all system modules. This action is irreversible.")){
    //            var selected_users = "";
    //            jQuery(".user-checkboxes:checked").each(function (index) {
    //                selected_users += jQuery(this).val() + "|";
    //            });
    //            AJAXCallModuleJSOnly("Users","makeSuperadmin", "users=" + selected_users);
    //        }
    //    }
    //});
    //
    //jQuery(".useranchor").live ("click", function () {
    //    var jObjid = jQuery(this).attr("id");
    //    var userid = jObjid.substring(jObjid.lastIndexOf("-")+1);
    //    jQuery("#edit-user-id").val(userid);
    //    
    //    //var jQObj = jQuery("#all-branches-" + jQuery("#chosen-branch").val());
    //    //
    //    //jQuery("#create-user-error").html("");
    //    //jQuery("#new-branchname").val(jQObj.children(".branch-names").val());
    //    //jQuery("#new-branchdescription").val(jQObj.children(".branch-descriptions").val());
    //    //jQuery("#new-branchaddress").val(jQObj.children(".branch-addresses").val());
    //    //
    //    //jQuery("#create-user-title").html("Edit user");
    //    //jQuery("#save-user").html("Save changes");
    //    //
    //    //blockDialog("#create-user-dialog");
    //    AJAXCallModuleJSOnly("Users","editUser", "userid=" + userid);
    //});
    //
    //jQuery("#user-more-deactivate").live ("click", function () {
    //    if(jQuery(".user-checkboxes:checked").length == 0) {
    //        alert("No users seleted!");
    //    } else {
    //        if(confirm("Are you sure you want to deactivate all selected users?")){
    //            var selected_users = "";
    //            jQuery(".user-checkboxes:checked").each(function (index) {
    //                selected_users += jQuery(this).val() + "|";
    //            });
    //            AJAXCallModuleJSOnly("Users","deactivateUser", "users=" + selected_users);
    //        }
    //    }
    //});
    //
    //jQuery("#branch-more-actions").live ("click", function () {
    //    var jQObj = jQuery("#branch-more-actions-div");
    //    if(jQObj.is(":visible")) {
    //        jQObj.css("display", "none");
    //    } else {
    //        jQObj.css("display", "inline-block");
    //    }
    //    //jQuery(this).addClass("dropdownbuttonclicked");
    //});
    //
    //jQuery("#edit-branch").live ("click", function () {
    //    
    //    var jQObj = jQuery("#all-branches-" + jQuery("#chosen-branch").val());
    //    
    //    jQuery("#create-branch-error").html("");
    //    jQuery("#new-branchname").val(jQObj.children(".branch-names").val());
    //    jQuery("#new-branchdescription").val(jQObj.children(".branch-descriptions").val());
    //    jQuery("#new-branchaddress").val(jQObj.children(".branch-addresses").val());
    //    
    //    jQuery("#create-branch-title").html("Edit branch");
    //    jQuery("#save-branch").html("Save changes");
    //    
    //    blockDialog("#create-branch-dialog");
    //});
    //
    //jQuery("#move-user").live ("click", function () {
    //    jQuery(".branch-move").removeClass("selected-move-to-branch");
    //    jQuery(".first-move-to-branch-div").addClass("selected-move-to-branch");
    //    
    //    jQuery("#selected-move-to-branch").val(jQuery(".first-move-to-branch-hidden").val());
    //    
    //    if(jQuery(".user-checkboxes:checked").length == 0){
    //        jQuery("#move-user-button").attr("disabled", true);
    //    } else {
    //        jQuery("#move-user-button").removeAttr("disabled");
    //    }
    //    
    //    jQuery("#branch-move-to").css("display", "inline-block");
    //});
    //
    //jQuery(".branch-move").live ("click", function() {
    //    jQuery(".branch-move").removeClass("selected-move-to-branch");
    //    jQuery(this).addClass("selected-move-to-branch");
    //    jQuery("#selected-move-to-branch").val(jQuery(this).children(".selected-branch-move").val());
    //});
    //
    //jQuery("#move-user-button").live ("click", function () {
    //    var selected_users = "";
    //    jQuery(".user-checkboxes:checked").each(function (index) {
    //        selected_users += jQuery(this).val() + "|";
    //    });
    //    AJAXCallModuleJSOnly("Users","moveUsers", "users="+selected_users+"&branchid=" + jQuery("#selected-move-to-branch").val());
    //});
    //
    //jQuery("#delete-branch").live ("click", function () {
    //    if(confirm("Are you sure you want to delete this branch?")){
    //        AJAXCallModuleJSOnly("Users","deleteBranch", "branchid=" + jQuery("#chosen-branch").val());
    //    }
    //});
    //
    //jQuery(".useranchor").live ("mouseover", function () { 
    //    //jQuery(this).parents('tr').children('td').addClass('trhighlight');
    //    //jQuery(this).parents('tr').children('td').addClass('trhighlight');
    //    //var jObjid = jQuery(this).attr("id");
    //    //var userid = jObjid.substring(jObjid.lastIndexOf("-")+1);
    //    //jQuery("#user-tr-" + userid).addClass('trhighlight');
    //    jQuery(this).parent('td').parent('tr').addClass('trhighlight');
    //});
    //
    //jQuery(".useranchor").live ("mouseout", function(){
    //    //jQuery(this).parents('tr').children('td').removeClass('trhighlight');
    //    //var jObjid = jQuery(this).attr("id");
    //    //var userid = jObjid.substring(jObjid.lastIndexOf("-")+1);
    //    //jQuery("#user-tr-" + userid).removeClass('trhighlight');
    //    jQuery(this).parent('td').parent('tr').removeClass('trhighlight');
    //});
    //
    //jQuery("body").click (function(e) {
    //    if((e.target.className).indexOf("branch-move") == -1) {
    //        jQuery(".branch-more-actions-div").css("display", "none");
    //    }
    //});
});