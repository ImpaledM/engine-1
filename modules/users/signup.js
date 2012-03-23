$(document).ready(function() {
	if (typeof $().uploadify == 'function')  $('#foto').upload('users', 'foto');
/*	$("input[name='login']").blur(function() {
		if ($('#login').attr('value') != '') {
			$.post("/__signup/", { checkLogin : true,
			login : $('#login').attr('value')
			}, function(data) {
				$('ul.message').remove();
				if (data != '0')
					$('div.anonses').before('<ul class="message error"><li>' + data + '</li></ul>');
			}, "text");
		}
		return false;
	});*/
});