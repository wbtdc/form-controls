jQuery(document).ready(function() {
	jQuery('.wbtdcSwitch').change(function() {
		wbtdcFcSwitchLabel(jQuery(this));
	};
});
function wbtdcFcSwitchLabel (obj) {
	alert('switch value is ' + obj.val());
}