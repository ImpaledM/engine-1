$(document).ready(function(){
	$('#photo_anons').upload('article', 'photo_anons');
	$('#photo').upload('article', 'photo', {multi: true});
});
