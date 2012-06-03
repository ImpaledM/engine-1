$(document).ready(function() {

	$('a.main_ajax').click(function() {
		var ob = $(this);
		$.get(ob.attr('href'), function(data) {
			if (data) {
				if (ob.children('input').attr('title') == 'Добавить на главную') {
					ob.html('<input title="Убрать с главной" src="/engine/modules/admin/images/icn_important_on.png" type="image">');
				} else {
					ob.html('<input title="Добавить на главную" src="/engine/modules/admin/images/icn_important_off.png" type="image">');
				}
			}
		});
		return false;
	});

	$('a.hit_ajax').click(function() {
		var ob = $(this);
		$.get(ob.attr('href'), function(data) {
			if (data) {
				if (ob.children('input').attr('title') == 'Добавить в хиты') {
					ob.html('<input title="Убрать из хитов" src="/engine/modules/admin/images/icn_star_on.png" type="image">');
				} else {
					ob.html('<input title="Добавить в хиты" src="/engine/modules/admin/images/icn_star_off.png" type="image">');
				}
			}
		});
		return false;
	});
	
	$('a.new_ajax').click(function() {
		var ob = $(this);
		$.get(ob.attr('href'), function(data) {
			if (data) {
				if (ob.children('input').attr('title') == 'Добавить в новинки') {
					ob.html('<input title="Убрать из новинок" src="/engine/modules/admin/images/icn_star_on.png" type="image">');
				} else {
					ob.html('<input title="Добавить в новинки" src="/engine/modules/admin/images/icn_star_off.png" type="image">');
				}
			}
		});
		return false;
	});

	$('a.publish_ajax').click(function() {
		var ob = $(this);
		$.get(ob.attr('href'), function(data) {
			if (data) {
				if (ob.children('input').attr('title') == 'Активировать') {
					ob.html('<input title="Деактивировать" src="/engine/modules/admin/images/icn_pause.png" type="image">');
					if ($('.alert_success'))
						$('.alert_success').remove();
				} else {
					ob.html('<input title="Активировать" src="/engine/modules/admin/images/icn_play.png" type="image">');
				}
			}
		});
		return false;
	});

	$('a.delete, a.delete_ajax').live('click', function() {
		var href = $(this).attr('href');
		var title = $(this).attr('title');
		if (title == '')
			title = 'Удаление';

		jConfirm('Вы уверены, что хотите удалить?', '', function(r) {
			if (r)
				window.location = href;
		});
		return false;
	});

});