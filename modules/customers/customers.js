function ChangeChannelSelection(obj) {
    AJAXCallModule('Customers','view_customers', 'Channel=' + encodeURIComponent($(obj).val()));
}