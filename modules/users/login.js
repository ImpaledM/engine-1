$(document).ready(function() {
	$('#email, #password').focus(function() {
		if ($(this).val() == $(this).attr('title')) {
			$(this).attr('value', '');
		}
	});
	$('#email, #password').blur(function() {
		if ($(this).val() == '') {
			$(this).attr('value', $(this).attr('title'));
		}
	});

	$('#link-login').colorbox({
		scrolling : false,
		opacity : 0.5,
		close : 'закрыть'
	});

	$('#enter').live('click', function() {
		var options = {
			target : "#login_message",
			url : "/__login/?cmd=login",
			success : function(data) {
				if (data) {
					$.colorbox.resize();
				} else {
					$.colorbox.close();
					window.location.reload(true);
				}
			}
		};

		$("#form_login_overlay").ajaxSubmit(options);

	});

});

VK.init({
	apiId : 2742414
});

function authInfo(response) {
	if (response.session) {
		$.post('__login', {
			cmd : 'vk_login',
			mid : response.session.mid
		}, function(data) {
			if (data) {
				window.location.reload(true);
			} else {
				VK.Api.call('getProfiles', {
					uids : response.session.mid,
					fields : "photo_rec, nickname" // fields : "sex, bdate, city,
				// country, photo_medium_rec,
				// contacts, activity, relation,
				// nickname"
				}, function(r) {
					if (r) {
						$.post('__login', {
							cmd : 'vk_signup',
							response : r.response[0]
						}, function(data) {
							window.location.reload(true);
						});
					}
				});
			}
		});
	} else {
		console.log('not auth');
	}
}

// VK.Auth.getLoginStatus(authInfo);
