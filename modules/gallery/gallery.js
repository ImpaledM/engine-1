$(document).ready(function() {
	$('#photo_anons').upload('gallery', 'photo_anons');
	$('#photo').upload('gallery', 'photo', { multi : true
	});
});
