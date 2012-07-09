$(document).ready(function() {

	$(document).everyTime(60000, 'sendOnline', function() {
		$.post('/__login/', {
			cmd : 'setOnline'
		});
	});

	$("#profile").click(function() {
	
		$.colorbox({
			opacity : 0.5,
			close : '',
			title: 'Мои данные',
			overlayClose: false,			
			transition : 'none',
			href : '/__profile/?cmd=add',
			onComplete : function() {
				boxresize($('#settings'));
			}
		});
		
		return false;
	});

});