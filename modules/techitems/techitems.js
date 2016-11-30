function ShowTechDetails(techitemid) {
    //alert("here" + techitemid);
    AJAXCallModule('Techitems','view_item_info', 'id=' + techitemid);
}

function DeleteProduct(productid) {
    if (confirm("Are you sure you want to delete this product.")) {
        AJAXCallModule('Techitems','delete_item', 'id=' + productid);
    }
}

function ChangeTypeSelection(obj) {
    AJAXCallModule('Techitems','view_tech_info', 'TechType=' + encodeURIComponent($(obj).val()));
}

function EditChangeTypeSelection(obj) {
    AJAXCallModule('Techitems','edit_tech_info', 'TechType=' + encodeURIComponent($(obj).val()));
}

function ViewProduct(productid,category) {
    AJAXCallModule('Techitems','view_tech_item', 'id='+productid+'&category='+category);
}
function Back(category) {
    AJAXCallModule('Techitems','edit_tech_info', 'TechType='+category);
}

function SaveProduct(productid,priceproduct) {
	var price  = $('input#price').val();
	if(price ==""){
		price = priceproduct;
	}
    AJAXCallModule('Techitems','save_tech_item_info', 'code='+productid+'&price='+price);
}

function ChangeTeamSelection(obj) {
    AJAXCallModule('Registration','view_team_profiles', 'Team=' + encodeURIComponent($(obj).val()));
}