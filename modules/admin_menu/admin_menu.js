$(document).ready(function() {

			var cookieOption = {
				path : '/',
				expires : 365
			};

			if ($.cookie('admin_menu') == 'show') {
				cls = 'show';
				$('div.cont_menu').show();
				$('#admin_pad').addClass('admin_pad');
				$('#admin_pad').removeClass('admin_pad_');
			} else {
				cls = 'hide';
				$('div.cont_menu').hide();
				$('#admin_pad').addClass('admin_pad_');
				$('#admin_pad').removeClass('admin_pad');
			}
			$('#but').addClass(cls);
			resizeMain();
			$('div.admin_menu dl dd').live('click', function() {
						$('div.cont_menu').slideToggle('fast');
						if ($('#but').hasClass('show')) {
							$('#admin_pad').addClass('admin_pad_');
							$('#admin_pad').removeClass('admin_pad');
							$('#but').removeClass('show');
							$('#but').addClass('hide');
							$.cookie('admin_menu', 'hide', cookieOption);
						} else {
							$('#admin_pad').removeClass('admin_pad_');
							$('#admin_pad').addClass('admin_pad');
							$('#but').removeClass('hide');
							$('#but').addClass('show');
							$.cookie('admin_menu', 'show', cookieOption);
						}
						resizeMain();
					});

			$(window).resize(function() {
						resizeMain();
					})

		});

function resizeMain() {
	if ($('div.main').length) {
		var current = $(window).height() - $('#admin_pad').height();
		$('div.main').css("min-height", current);
	}
}