$(document).ready(function() {
	$('a[rel^="cb"], a.cb').colorbox({
		previous : '<',
		next : '>',
		close : 'закрыть',
		current : "фото {current} из {total}",
		loop : false,
		opacity : 0.75,
		onClosed : function() {
			$('#cboxOverlay').hide();
		}
	});

});
