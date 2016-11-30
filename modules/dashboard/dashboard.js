function ChangeDashboardChannel(obj) {
	var id = $(obj).find(':selected').attr('id'); 
	var channelname = $(obj).val();
	var channeltype = $('select#dashboardchanneltype').children(':selected').attr('id');
	if(id > 0 || id =='All')AJAXCallModule('Dashboard','dashboardpage', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}


function ChangeDashboardFranchisesChannel(obj) {
	var id = $(obj).find(':selected').attr('id'); 
	var channelname = $(obj).val();
	var channeltype = 'Franchises';
	if(id > 0 || id =='All')AJAXCallModule('Dashboard','dashboardpage', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}
function ChangeDashboardRetailChannel(obj) {
	var id = $(obj).find(':selected').attr('id'); 
	var channelname = $(obj).val();
	var channeltype = 'Retail';
	if(id > 0 || id =='All')AJAXCallModule('Dashboard','dashboardpage', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}

function ChangeDashboardCommercialChannel(obj) {
	var id = $(obj).find(':selected').attr('id'); 
	var channelname = $(obj).val();
	var channeltype = 'Commercial';
	if(id > 0 || id =='All')AJAXCallModule('Dashboard','dashboardpage', 'Channel=' + id+'&ChannelType='+channeltype+'& Channelname='+channelname);
}