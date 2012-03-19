$(document).ready(function() {
	//$('#photo_anons').upload('news', 'photo_anons');
	$('#sendall').click(function() {
		if (!$('#senduser').attr('checked') && !$('#sendnotuser').attr('checked')) {
			$(this).attr('checked', true);
		} else {
			if ($(this).attr('checked')) {
				$('#senduser').attr('checked', false);
				$('#sendnotuser').attr('checked', false);
			}
		}
	});
	$('#senduser, #sendnotuser').click(function() {
		if ($(this).attr('checked'))
			$('#sendall').attr('checked', false);
		if (!$('#senduser').attr('checked') && !$('#sendnotuser').attr('checked'))
			$('#sendall').attr('checked', true);
		if ($('#senduser').attr('checked') && $('#sendnotuser').attr('checked')) {
			$('#sendall').attr('checked', true);
			$('#senduser').attr('checked', false);
			$('#sendnotuser').attr('checked', false);
		}
	});
});
