<?php 
    # This code is written by Tradepage Pty Ltd for Ellies Pty Ltd (the 'parties' mentioned below)
    # The code is provided based on the the terms specified within the agreed NDA between both parties.
    # Both parties have agreed the code is strictly confidential
    # and only by mutal agreement of both parties may the code be exposed to outside parties.
    #
    # Any changes made to the code other than by Tradepage Pty Ltd during the NDA Agreement void support of the code
    #
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
    
    function html_start() {
		print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
	        print "<HTML xmlns=\"http://www.w3.org/1999/xhtml\">\n";
	        print " <HEAD>\n";
		print "  <META http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" >\n";
	        print "  <TITLE>".SITE_TITLE."</TITLE>\n";
	        print "  <SCRIPT type='text/javascript' SRC='js/jquery-1.7.1.min.js'></SCRIPT>\n";
	        print "  <SCRIPT type='text/javascript' SRC='js/jquery-ui-1.8.18.custom.min.js'></SCRIPT>\n";
	        print "  <SCRIPT type='text/javascript' SRC='js/cloudgroup.js'></SCRIPT>\n";
		print "  <SCRIPT type='text/javascript' SRC='js/jquery.blockUI.js'></SCRIPT>\n";
		print "  <SCRIPT type='text/javascript' SRC='js/jquery.jqplot.min.js'></SCRIPT>\n";
		print "  <script type='text/javascript' src='libs/tinymce/jscripts/tiny_mce/tiny_mce.js'></script>\n";

		//DataTables
		print '<link rel="stylesheet" type="text/css" href="modules/libraries/css/jquery.dataTables.css"/>';
	    print '<script type="text/javascript" language="javascript" src="modules/libraries/js/jquery.dataTables.js"></script>';

		//FusionCharts
		print '<script type="text/javascript" src="fusioncharts/js/fusioncharts.js"></script>';
		print '<script type="text/javascript" src="fusioncharts/js/themes/fusioncharts.theme.ocean.js"></script>';
		print '<script type="text/javascript" src="fusioncharts/js/themes/fusioncharts.theme.zune.js"></script>';
		print '<script type="text/javascript" src="fusioncharts/js/themes/fusioncharts.theme.fint.js"></script>';
		print '<script type="text/javascript" src="fusioncharts/js/themes/fusioncharts.theme.carbon.js"></script>';

		//Notifications
		print '<link rel="stylesheet" type="text/css" href="js/jquery-ui.css"/>';
		print '<script type="text/javascript" src="js/jquery-ui.js"></script>';

		//Newsletter
		print '<link rel="stylesheet" type="text/css" href="js/jquery.multiselect.css"/>';
		print '<script type="text/javascript" src="js/jquery.multiselect.js"></script>';


	    if (is_authenticated()) { include_module_js_scripts(); }     
	   	 	print "  <LINK TYPE='text/css' href='css/site.css' rel='stylesheet' />\n";
	     	print "  <LINK TYPE='text/css' href='css/jquery-ui-1.8.18.custom.css' rel='stylesheet' media='all' />\n";

	     	$ColourList = array(
                "aliceblue" => "#F0F8FF", "antiquewhite" => "#FAEBD7", "aqua" => "#00FFFF", "aquamarine" => "#7FFFD4", "azure" => "#F0FFFF", "beige" => "#F5F5DC", "bisque" => "#FFE4C4", "black" => "#000000", "blanchedalmond" => "#FFEBCD", "blue" => "#0000FF", "blueviolet" => "#8A2BE2", "brown" => "#A52A2A", "burlywood" => "#DEB887", "cadetblue" => "#5F9EA0", "chartreuse" => "#7FFF00", "chocolate" => "#D2691E", "coral" => "#FF7F50", "cornflowerblue" => "#6495ED", "cornsilk" => "#FFF8DC", "crimson" => "#DC143C", "cyan" => "#00FFFF", "darkblue" => "#00008B", "darkcyan" => "#008B8B", "darkgoldenrod" => "#B8860B", "darkgray" => "#A9A9A9", "darkgrey" => "#A9A9A9", "darkgreen" => "#006400", "darkkhaki" => "#BDB76B", "darkmagenta" => "#8B008B", "darkolivegreen" => "#556B2F", "darkorange" => "#FF8C00", "darkorchid" => "#9932CC", "darkred" => "#8B0000", "darksalmon" => "#E9967A", "darkseagreen" => "#8FBC8F", "darkslateblue" => "#483D8B", "darkslategray" => "#2F4F4F", "darkslategrey" => "#2F4F4F", "darkturquoise" => "#00CED1", "darkviolet" => "#9400D3", "deeppink" => "#FF1493", "deepskyblue" => "#00BFFF", "dimgray" => "#696969", "dimgrey" => "#696969", "dodgerblue" => "#1E90FF", "firebrick" => "#B22222", "floralwhite" => "#FFFAF0", "forestgreen" => "#228B22", "fuchsia" => "#FF00FF", "gainsboro" => "#DCDCDC", "ghostwhite" => "#F8F8FF", "gold" => "#FFD700", "goldenrod" => "#DAA520", "gray" => "#808080", "grey" => "#808080", "green" => "#008000", "greenyellow" => "#ADFF2F", "honeydew" => "#F0FFF0", "hotpink" => "#FF69B4", "indianred " => "#CD5C5C", "indigo " => "#4B0082", "ivory" => "#FFFFF0", "khaki" => "#F0E68C", "lavender" => "#E6E6FA", "lavenderblush" => "#FFF0F5", "lawngreen" => "#7CFC00", "lemonchiffon" => "#FFFACD", "lightblue" => "#ADD8E6", "lightcoral" => "#F08080", "lightcyan" => "#E0FFFF", "lightgoldenrodyellow" => "#FAFAD2", "lightgray" => "#D3D3D3", "lightgrey" => "#D3D3D3", "lightgreen" => "#90EE90", "lightpink" => "#FFB6C1", "lightsalmon" => "#FFA07A", "lightseagreen" => "#20B2AA", "lightskyblue" => "#87CEFA", "lightslategray" => "#778899", "lightslategrey" => "#778899", "lightsteelblue" => "#B0C4DE", "lightyellow" => "#FFFFE0", "lime" => "#00FF00", "limegreen" => "#32CD32", "linen" => "#FAF0E6", "magenta" => "#FF00FF", "maroon" => "#800000", "mediumaquamarine" => "#66CDAA", "mediumblue" => "#0000CD", "mediumorchid" => "#BA55D3", "mediumpurple" => "#9370D8", "mediumseagreen" => "#3CB371", "mediumslateblue" => "#7B68EE", "mediumspringgreen" => "#00FA9A", "mediumturquoise" => "#48D1CC", "mediumvioletred" => "#C71585", "midnightblue" => "#191970", "mintcream" => "#F5FFFA", "mistyrose" => "#FFE4E1", "moccasin" => "#FFE4B5", "navajowhite" => "#FFDEAD", "navy" => "#000080", "oldlace" => "#FDF5E6", "olive" => "#808000", "olivedrab" => "#6B8E23", "orange" => "#FFA500", "orangered" => "#FF4500", "orchid" => "#DA70D6", "palegoldenrod" => "#EEE8AA", "palegreen" => "#98FB98", "paleturquoise" => "#AFEEEE", "palevioletred" => "#D87093", "papayawhip" => "#FFEFD5", "peachpuff" => "#FFDAB9", "peru" => "#CD853F", "pink" => "#FFC0CB", "plum" => "#DDA0DD", "powderblue" => "#B0E0E6", "purple" => "#800080", "red" => "#FF0000", "rosybrown" => "#BC8F8F", "royalblue" => "#4169E1", "saddlebrown" => "#8B4513", "salmon" => "#FA8072", "sandybrown" => "#F4A460", "seagreen" => "#2E8B57", "seashell" => "#FFF5EE", "sienna" => "#A0522D", "silver" => "#C0C0C0", "skyblue" => "#87CEEB", "slateblue" => "#6A5ACD", "slategray" => "#708090", "slategrey" => "#708090", "snow" => "#FFFAFA", "springgreen" => "#00FF7F", "steelblue" => "#4682B4", "tan" => "#D2B48C", "teal" => "#008080", "thistle" => "#D8BFD8", "tomato" => "#FF6347", "turquoise" => "#40E0D0", "violet" => "#EE82EE", "wheat" => "#F5DEB3", "white" => "#FFFFFF", "whitesmoke" => "#F5F5F5", "yellow" => "#FFFF00", "yellowgreen" => "#9ACD32");
               
			$sql = "SELECT `theme_colour` FROM branches WHERE branch_id ='".$GLOBALS['system_user']->branchID."'";
            $sqlres = mysqli_query($GLOBALS["link"],$sql);
            $theme_colour = "";
            while($row = mysqli_fetch_assoc($sqlres)) {
            	$theme_colour  = $row['theme_colour'];
            }
            if($theme_colour  ==""){
            	$theme_colour ="#6dad1f";
            }else{
            	 $theme_colour  = $ColourList[$theme];
            }
          
			print "<style>";
			print ".accordion > li:hover > a,
					.accordion > li:target > a,
					.accordion > li > a.active {
						color: #fcfcfc;
						text-shadow: 1px 1px 1px rgba(255,255,255, .2);
						
						/*background: url(../img/active.png) repeat-x;*/
						background: $theme_colour;
						background: -moz-linear-gradient(top,  $theme_colour 0%, $theme_colour 100%);
						background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,$theme_colour), color-stop(100%,$theme_colour));
						background: -webkit-linear-gradient(top,  $theme_colour 0%,$theme_colour 100%);
						background: -o-linear-gradient(top,  $theme_colour 0%,$theme_colour 100%);
						background: -ms-linear-gradient(top,  $theme_colour 0%,$theme_colour 100%);
						background: linear-gradient(top,  $theme_colour 0%,$theme_colour 100%);	
					}";
			print "</style>";

		if(is_authenticated()) { include_module_css_files(); }
		
	        print " </HEAD>\n";
	        print "<BODY style='height: 100%;'>\n";
		print "<div id='alert-dialog' style='display:none;'></div>\n";
    }

    function draw_menu() {
        global $registered_menus;
        print "<div id='sidemenu' name='sidemenu'>";
		//sortRegisteredMenus("custom", array('CaptureVouchers', 'RMR',  'CapturePayment', 'CaptureCustomerInfo', 'installers', 'users', 'groups', 'reports', 'settings', 'errors'));

       $sql = "SELECT `type` FROM `channels` WHERE `id`=".$GLOBALS['system_user']->retailChannel;
	   $sqlres = mysqli_query($GLOBALS["link"],$sql);
	   $row = mysqli_fetch_assoc($sqlres);
	   $channeltype = $row['type'];

	   //Menu
	   //$channeltype == "Commercial" || 
	   if ($channeltype =="Franchises"){
	   	//sortRegisteredMenus("custom", array('Dashboard','groups','Quotations','Techitems','Customers','Registration','CompanyProfiles','Newsletters','users','Settings'));
	   	sortRegisteredMenus("custom", array('Dashboard','groups','Quotations','Techitems','Customers','Notification','CompanyProfiles','Newsletters','users','Settings'));

	   }else{
	   	//sortRegisteredMenus("custom", array('Dashboard','groups','Quotations','Techitems','Customers','Newsletters','users','Settings'));
	   	sortRegisteredMenus("custom", array('Dashboard','groups','Quotations','Techitems','Customers','Notification','Newsletters','users','Settings'));
	   }

	   //Logo
	   $sql = "SELECT `logo` FROM `branches` WHERE `id`=".$GLOBALS['system_user']->branchID;
	   $sqlres = mysqli_query($GLOBALS["link"],$sql);
	   $row = mysqli_fetch_assoc($sqlres);
	   if($row['logo'] !=""){
	   	$logo = 'modules/companyprofiles/logos/'.$row['logo'];
	   }else{
	   	$logo = "images/logo.png";
	   }
	   
		print "<BR/>";
		print "<BR/>";
		print "<div style='width: 230px; text-align: center; font: bold 14px/32px Arial,sans-serif; color: #555555; text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.2);'>";
	       // print "<img src='images/logo.png' width='200' align='absmiddle' />&nbsp;";
	        print "<img src='".$logo."' width='200' align='absmiddle' />&nbsp;";
		print "</div>";
		print "<BR/>";
		
		print "<ul class='accordion'>\n";
		$count  = 0;
		$access = false;
        foreach ($registered_menus as $menu) {
		    if ($menu['type'] == 'submenu') {
				$access = false;
				foreach ($menu['subitems'] as $subitem) {
				    if ($GLOBALS['system_user']->hasPermission($subitem['acl'])) {
					$access = true;
					break;
				    }
				}
				
				if (!$access) continue;
				print "	<li id='menuitem".$count."' class='".$menu['module']."'>\n";
				print "		<a href='#'>".$menu['parenttitle']."</a>\n";
				print "		<ul class='sub-menu'>\n";
				foreach ($menu['subitems'] as $subitem) {
				    if ($subitem['location'] == "" || $subitem['title'] == "") continue;
				    if (!$GLOBALS['system_user']->hasPermission($subitem['acl'])) continue;
				    print "			<li><a class='sub-menu-item' href=\"Javascript: AJAXCallModule('".$menu['module']."','".$subitem['location']."', '');\"><em>---</em>".$subitem['title']."</a></li>\n";
				}
				print "		</ul>\n";
				print "	</li>\n";
		    } else {
				if (!$GLOBALS['system_user']->hasPermission($menu['module'])) continue;
				print "	<li id='menuitem".$count."' class='".$menu['module']."'>\n";
				print "		<a id='".$menu['module']."' name='parentMenu' href=\"#\">".$menu['parenttitle']."</a>\n";
				print "	</li>\n";
		    }
		    $count++;
        }
        
        print "	<li id='four' class='sign'>\n";
        print "		<a id='signout' href='#'>Sign Out</a>\n";
        print "	</li>\n";
        print "	<li style='text-align: center;padding-top: 20px;'>\n";
        print "		<label style='font-size:12px;font-weight:bold;color:#8dd03f;'>Powered by Ellies</label>\n";
        print "	</li>\n";
        print "</ul>\n";
        print "";
        print "</div>";
?>

<script type='text/javascript'>

	$(document).ready(function() {
	    // Store variables
	    var accordion_head = $('.accordion > li > a'),
		accordion_body = $('.accordion li > .sub-menu'),
		accordion_item = $(".sub-menu-item");
	    // Open the first tab on load
		//accordion_head.first().addClass('active').next().slideDown('fast');
	    // Click function
	    $(accordion_body).css({"display":"none"});

	    accordion_head.on('click', function(event) {
			// Disable header links
			event.preventDefault();

			if ($(this).parent().find("ul").length==1) {
				$(accordion_body).css({"display":"none"});
		   		$(this).parent().find("ul").show();
		    }

			if ($(this).attr('name') == 'parentMenu') {
			    //accordion_body.slideUp('fast');
			    accordion_head.removeClass('active');
			    accordion_item.removeClass('active-sub');
			    $(this).addClass('active');
			    AJAXCallModule($(this).attr('id'),'main', '');
			   // $(accordion_body).css({"display":"block"});
			    return;
			} else if ($(this).attr('class') == 'active') {
			    //accordion_body.slideUp('fast');
			    //$(this).next().stop(true,true).slideToggle('fast');
			}
			if ($(this).attr('id') == 'signout') {
			    if (confirm('Are you sure you want to sign out?')) {
				document.location.href='login.php?logout=1';
			    }
			    return;
			}
			// Show and hide the tabs on click
			if ($(this).attr('class') != 'active') {
			    //accordion_body.slideUp('fast');
			   //$(this).next().stop(true,true).slideToggle('fast');
			}
	    });
	    
	    accordion_item.on('click', function(event) {
		accordion_head.removeClass('active');
		accordion_item.removeClass('active-sub');
		$(this).addClass('active-sub');
		$(this).parents('li').parents('li').children('a').addClass('active');
	    });    
	});
</script>

<?php 

    }

    function html_end() {
    	//<!--FancyBox-->
        print '<script src="quotes/fancybox/jquery.fancybox.js"></script>';
        print '<script src="quotes/fancybox/jquery.fancybox-buttons.js"></script>';
        print '<script src="quotes/fancybox/jquery.fancybox-thumbs.js"></script>';
        print '<script src="quotes/fancybox/jquery.mousewheel-3.0.6.pack.js"></script>';
        print "<script>
        		var count =   $('input#notification').val(); 
        		if(count >0){
    				$('div#dialog').dialog({  width: 350 });
    			}else{
    				$('div#dialog').hide();
    			} 
		            $('img#notification').click(function(){
		            $('div#dialog').dialog(); 
		        })
			</script>";
        print "</BODY>\n";
        print "</HTML>";
    }
?>