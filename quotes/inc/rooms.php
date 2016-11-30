<script>
function PopupDiv(id, value, room){
	$('#'+room).css('display','block');
	$('#inputField').val(id);
	$('#roomField').val(room);
}
function closePopup(value){
	$('#'+$('#roomField').val()).css('display','none');
	$('#'+$('#inputField').val()).val(value)
}
</script>

<div id="LightingRoom">
	<div id="PopupDiv" class="ui-overlay-c">
		<ul data-role="listview" data-divider-theme="a" data-ajax="false" data-rel="dialog" data-close-btn-text="close" data-inline="false" class="ui-listview">
			<li data-role="list-divider" role="heading" class="ui-li ui-li-divider ui-bar-a ui-first-child">Please Select Room</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Bedroom 1')" >Bedroom 1</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Bedroom 2')" >Bedroom 2</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Bedroom 3')" >Bedroom 3</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Bedroom 4')" >Bedroom 4</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Bedroom 5')" >Bedroom 5</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Bathroom 1')" >Bathroom 1</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Bathroom 2')" >Bathroom 2</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Bathroom 3')" >Bathroom 3</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Bathroom 4')" >Bathroom 4</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Bathroom 5')" >Bathroom 5</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Dining Room')" >Dining Room</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Entrance')" >Entrance</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Kitchen')" >Kitchen</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Lounge')" >Lounge</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Outside')" >Outside</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Passage')" >Passage</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Patio')" >Patio</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Staff Quarters')" >Staff Quarters</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Study')" >Study</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Other')">Other</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
		</ul>
		<input type="hidden" id="inputField" value="">
		<input type="hidden" id="roomField" value="">
	</div>
</div>
<div id="WaterRoom">
	<div id="PopupDiv" class="ui-overlay-c">
		<ul data-role="listview" data-divider-theme="a" data-ajax="false" data-rel="dialog" data-close-btn-text="close" data-inline="false" class="ui-listview">
			<li data-role="list-divider" role="heading" class="ui-li ui-li-divider ui-bar-a ui-first-child">Please Select Room</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Bathroom 1')" >Bathroom 1</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Bathroom 2')" >Bathroom 2</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Bathroom 3')" >Bathroom 3</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Bathroom 4')" >Bathroom 4</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Bathroom 5')" >Bathroom 5</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Kitchen')" >Kitchen</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Outside')" >Outside</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Staff Quarters')" >Staff Quarters</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
			<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ">
				<div class="ui-btn-inner ui-li">
					<div class="ui-btn-text" onclick="closePopup('Other')">Other</div>
					<span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div>
			</li>
		</ul>
		<input type="hidden" id="inputField" value="">
		<input type="hidden" id="roomField" value="">
	</div>
</div>
