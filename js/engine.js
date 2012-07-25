jQuery.fn.upload = function(table, field, options) {
	var settings = jQuery.extend({
		multi : false,
		width : 1000,
		height : 750,
		buttonText : 'Upload',
		buttonImg : '/engine/modules/ajax/uploadify/upload.gif',
		widthBtn : 118,
		type : 'image',
		ext : '*.jpeg;*.gif;*.png;*.jpg',
		move : false,
		destination : '/uploads/' + table
	}, options);
	if (settings.multi) {
		$('#' + field + '_show').sortable({
			cursor : 'move',
			opacity : 0.5,
			tolerance : "pointer",
			revert : false
		});
	}
	$('#' + field + '_show' + ' a.del_img').live('click', function() {
		if (settings.multi) {
			id_parent = $('#' + field + '_show').attr('alt');
			deleteFile($(this).attr('href'), table, field, settings.multi, id_parent);
			$(this).closest('li.upload_li').remove();
		} else {
			deleteFile($(this).attr('href'), table, field, settings.multi);
			$(this).closest('span').remove();
		}
		return false;
	});
	$(this)
			.uploadify(
					{
						'uploader' : '/engine/modules/ajax/uploadify/uploadify.swf',
						'cancelImg' : '/engine/modules/ajax/uploadify/cancel.png',
						'script' : '/__ajax/',
						'auto' : true,
						'folder' : '/temp',
						'scriptData' : {
							'width' : settings.width,
							'height' : settings.height,
							'type' : settings.type,
							'move' : settings.move,
							'cmd' : 'uploadify',
							'prefix' : table,
							'destination' : settings.destination
						},
						'sizeLimit' : '14097152',
						'wmode' : 'transparent',
						'multi' : settings.multi,
						'buttonText' : settings.buttonText,
						'buttonImg' : settings.buttonImg,
						'width' : settings.widthBtn,
						'fileDesc' : settings.ext,
						'fileExt' : settings.ext,
						onComplete : function(evt, queueID, fileObj, fname, data) {
							var path = fileObj.filePath;
							path = path.replace(fileObj.name, fname);
							if (settings.type == 'image') {
								if (settings.multi) {
									$('#' + field + '_show')
											.append(
													'<li class="upload_li"><span class="photo-item"><a href="/uploads'
															+ path
															+ '" rel="lb"><img src="/200x100'
															+ path
															+ '" alt="/200x100'
															+ path
															+ '" /></a> <a href="'
															+ path
															+ '" class="del_img">Удалить</a> <span class="adjustment"><a href="'
															+ path
															+ '" class="note_img"><img src="/engine/images/u_note_gray.png" /><input type="hidden" name="note[]" value=""/></a> <i class="empty"></i> <i class="empty"></i> <a href="'
															+ path + '" class="ccw"><img src="/engine/images/u_rotate_ccw.png" /></a>  <a href="' + path
															+ '" class="cw"><img src="/engine/images/u_rotate_cw.png" /></a><input type="hidden" name="' + field + '[]" value="'
															+ path + '"/></span></span>');
								} else {

									deleteFile($('#' + field + '_show' + ' a.del_img').attr('href'), table, field, settings.multi);
									$('#' + field + '_show')
											.html(
													'<span class="photo-item"><a href="/uploads'
															+ path
															+ '" rel="lb"><img src="/200x100'
															+ path
															+ '" alt="/200x100'
															+ path
															+ '" /></a> <a href="'
															+ path
															+ '" class="del_img">Удалить</a> <span class="adjustment"><i class="empty"></i><i class="empty"></i> <i class="empty"></i> <a href="'
															+ path + '" class="ccw"><img src="/engine/images/u_rotate_ccw.png" /></a>  <a href="' + path
															+ '" class="cw"><img src="/engine/images/u_rotate_cw.png" /></a><input type="hidden" name="' + field + '" value="'
															+ path + '"/></span></span>');
								}

							} else {
								ar = path.split('.');
								ext = ar[1];
								if (settings.multi) {
									$('#' + field + '_show')
											.append(
													'<li class="upload_li"><span class="photo-item"><img src="/engine/images/file_ico/'
															+ ext
															+ '.png" alt="'
															+ path
															+ '"  width="64"/><a href="'
															+ path
															+ '" class="del_img">Удалить</a> <span class="adjustment"><a href="'
															+ path
															+ '" class="note_img"><img src="/engine/images/u_note_gray.png" /><input type="hidden" name="note[]" value=""/></a><input type="hidden" name="'
															+ field + '[]" value="' + path + '"/></span></span>');
								} else {

									deleteFile($('#' + field + '_show' + ' a.del_img').attr('href'), table, field, settings.multi);
									$('#' + field + '_show').html(
											'<span class="photo-item"><img src="/engine/images/file_ico/' + ext + '.png" alt="' + path + '"    width="64"/><a href="'
													+ path + '" class="del_img">Удалить</a> <span class="adjustment"><input type="hidden" name="' + field + '" value="' + path
													+ '"/></span></span>');
								}
								if (typeof uploadFileEvent == 'function') {
									uploadFileEvent(path);
								}

							}

						}
					});
}

function deleteFile(file, table, field, multi, id_parent) {
	$.post("/__ajax/", {
		cmd : "deleteFile",
		file : file,
		table : table,
		field : field,
		multi : multi,
		id_parent : id_parent
	});
}

jQuery.fn.limit = function(options) {
	var settings = jQuery.extend({
		maxChars : $(this).attr('limit'),
		leftChars : "осталось",
		chars : 'символов',
		show : true
	}, options);

	return this.each(function() {
		var me = $(this);
		var l = settings.maxChars;
		if (settings.show)
			me.after('<span class="limit" style="display:block;">' + settings.leftChars + '  ' + settings.maxChars + '  ' + settings.chars + '</span>');
		l = settings.maxChars - me.val().length;
		if (settings.show)
			me.next('span').html(settings.leftChars + '  ' + l + '  ' + settings.chars + '</span>');
		me.bind('keydown keypress keyup change', function(e) {
			l = settings.maxChars - me.val().length;
			if (settings.show)
				me.next('span').html(settings.leftChars + '  ' + l + ' ' + settings.chars + '</span>');
			if (l < 0)
				me.next('span').addClass('red');
			else
				me.next('span').removeClass('red');
		});
	});
};

jQuery.fn.center = function() {
	var w = $(window);
	this.css("position", "absolute");
	this.css("top", (w.height() - this.height()) / 2 + w.scrollTop() + "px");
	this.css("left", (w.width() - this.width()) / 2 + w.scrollLeft() + "px");
	return this;
};

function intval(val) {
	tmp = parseInt(val);
	return (isNaN(tmp) || !isFinite(tmp)) ? 0 : tmp;

}

function trim(sInString) {
	sInString = sInString.replace(/&nbsp;/g, ' ');
	return sInString.replace(/(^\s+)|(\s+$)/g, '');
}

function explode(delimiter, string) {

	var emptyArray = {
		0 : ''
	};

	if (arguments.length != 2 || typeof arguments[0] == 'undefined' || typeof arguments[1] == 'undefined') {
		return null;
	}

	if (delimiter === '' || delimiter === false || delimiter === null) {
		return false;
	}

	if (typeof delimiter == 'function' || typeof delimiter == 'object' || typeof string == 'function' || typeof string == 'object') {
		return emptyArray;
	}

	if (delimiter === true) {
		delimiter = '1';
	}

	return string.toString().split(delimiter.toString());
}

function notice(title, text, options) {
	var settings = jQuery.extend({
		show : true,
		timer : false
	}, options);
	var ID = $('div.popup').length + 1;
	var iden = '#p_' + ID + '.popup';
	$('body').append(
			'<div class="popup" style="display: none;" id="p_' + ID + '">' + '<div class="popup-fon">' + '<div class="popup-in link-friend">' + '<h2>'
					+ title + '</h2>' + '<h3>' + text + '</h3>' + '</div>' + '<i class="closes"></i>' + '</div>' + '</div>');
	$(iden).center();
	if (settings.show)
		$(iden).fadeIn();
	$(iden + ' i.closes').live("click", function() {
		$(iden).fadeOut(function() {
			$(iden).remove();
		});
	});
	if (settings.timer) {
		$(iden).oneTime(settings.timer * 1000, function(i) {
			$(this).fadeOut(function() {
				$(this).remove();
			});
		});
		$(iden).mouseover(function() {
			$(this).stopTime();
		});
		$(iden).mouseout(function() {
			$(iden).oneTime(settings.timer * 1000, function(i) {
				$(this).fadeOut(function() {
					$(this).remove();
				});
			});
		});
	}
}

function loader(cmd) {
	var cmd = cmd || false;
	if (!cmd) {
		$('body')
				.append(
						'<div id="mLoader" style="opacity:0; width: 100%; height: '
								+ $(document).height()
								+ 'px; position: absolute; top:0px; left:0px; z-index: 9998;   background-color: #fff;"></div><div id="dLoader" style="z-index: 9999; position:absolute; opacity:0;"><img src="/img/mloader.gif" /></div>');
		$('#dLoader').center();
		$('#mLoader').animate({
			opacity : 0.5
		}, 300, function() {
			$('#dLoader').animate({
				opacity : 1
			}, 300);
		});
	} else {
		$('#dLoader').animate({
			opacity : 0
		}, 300, function() {
			$('#mLoader').animate({
				opacity : 0
			}, 300, function() {
				$('#dLoader img').remove();
				$('#dLoader').remove();
				$('#mLoader').remove();
			});
		});
	}
}

(function(a) {
	a.fn.autoResize = function(j) {
		var b = a.extend({
			onResize : function() {},
			animate : true,
			animateDuration : 150,
			animateCallback : function() {},
			extraSpace : 20,
			limit : 1000
		}, j);
		this.filter('textarea').each(function() {
			var c = a(this).css({
				resize : 'none',
				'overflow-y' : 'hidden'
			}), k = c.height(), f = (function() {
				var l = [ 'height', 'width', 'lineHeight', 'textDecoration', 'letterSpacing' ], h = {};
				a.each(l, function(d, e) {
					h[e] = c.css(e)
				});
				return c.clone().removeAttr('id').removeAttr('name').css({
					position : 'absolute',
					top : 0,
					left : -9999
				}).css(h).attr('tabIndex', '-1').insertBefore(c)
			})(), i = null, g = function() {
				f.height(0).val(a(this).val()).scrollTop(10000);
				var d = Math.max(f.scrollTop(), k) + b.extraSpace, e = a(this).add(f);
				if (i === d) {
					return

				}
				i = d;
				if (d >= b.limit) {
					a(this).css('overflow-y', '');
					return

				}
				b.onResize.call(this);
				b.animate && c.css('display') === 'block' ? e.stop().animate({
					height : d
				}, b.animateDuration, b.animateCallback) : e.height(d)
			};
			c.unbind('.dynSiz').bind('keyup.dynSiz', g).bind('keydown.dynSiz', g).bind('change.dynSiz', g)
		});
		return this
	}
})(jQuery);

$(document).ready(function() {
	$("img").error(function() {
		$(this).hide();
	});
	$("textarea[limit]").livequery(function() {
		$(this).limit({
			"leftChars" : "Осталось"
		});
	});
	$("input[limit]").livequery(function() {
		$(this).limit({
			"show" : false
		});
	});
	if ($('input[rel="date"], input[rel="datetime"], input.date, #datepicker').length) {
		$('head').append('<link href="/css/ui.custom.css" rel="stylesheet" type="text/css" />');
		$.datepicker.regional['ru'] = {
			closeText : 'Закрыть',
			prevText : '&#x3c;Пред',
			nextText : 'След&#x3e;',
			currentText : 'Сегодня',
			monthNames : [ 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь' ],
			monthNamesShort : [ 'Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек' ],
			dayNames : [ 'воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота' ],
			dayNamesShort : [ 'вск', 'пнд', 'втр', 'срд', 'чтв', 'птн', 'сбт' ],
			dayNamesMin : [ 'Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб' ],
			weekHeader : 'Не',
			dateFormat : 'dd.mm.yy',
			firstDay : 1,
			isRTL : false,
			showMonthAfterYear : false,
			yearSuffix : ''
		};
		$.datepicker.setDefaults($.datepicker.regional['ru']);
		$('input[rel="date"], input[rel="datetime"], input.date').livequery(function() {
			$(this).datepicker({
				showOn: "button",
				buttonImage: "/i/calend.png",
				buttonImageOnly: true,
				gotoCurrent : true
				/*,
				changeMonth : true,
				changeYear : true,
				yearRange : '2010:2020'*/
			});
		});
	}
	if ($('a[rel^="lb"], a.lb').livequery(function() {
		return this
	}).length) {
		$('head').append('<link href="/css/colorbox/example3/colorbox.css" rel="stylesheet" type="text/css" />');
		$.getScript('/engine/js/jquery.colorbox.js', function() {
			$('a[rel^="lb"], a.lb').colorbox({
				current : "фото {current} из {total}",
				loop : false,
				opacity : 0.75,
				onClosed : function() {
					$('#cboxOverlay').hide();
				}
			});
		});
	}
	$('a.note_img').live('click', function() {
		var href = $(this).attr('href');
		var init = $('a[href="' + href + '"].note_img input').attr('value');
		init = (init == '') ? '' : init;
		jPrompt('', init, 'Введите описание к фото', function(r) {
			if (typeof (r) == 'string') {
				if (r != '') {
					$('a[href="' + href + '"].note_img input').attr('value', r);
					$('a[href="' + href + '"].note_img img').attr('src', '/engine/images/u_note.png');
				} else {
					$('a[href="' + href + '"].note_img input').attr('value', '');
					$('a[href="' + href + '"].note_img img').attr('src', '/engine/images/u_note_gray.png');
				}
			}
		});
		return false;
	});
	$('a.ccw, a.cw').live('click', function() {
		im = $(this).parents('.photo-item').find('img').get(0);
		var src = $(im).attr('alt');
		switch ($(this).attr('class')) {
		case 'ccw':
			deg = 90;
			break;
		case 'cw':
			deg = -90;
			break;
		default:
			deg = 0;
			break;
		}
		$.post("/__ajax/", {
			cmd : "rotateImg",
			file : $(this).attr('href'),
			rot : deg
		}, function(dat) {
			$(im).attr('src', src + '?' + Math.random());
		}, 'html');
		return false;
	});
});

jQuery.preloadImages = function () {
  if (typeof arguments[arguments.length - 1] == 'function') {
      var callback = arguments[arguments.length - 1];
  } else {
      var callback = false;
  }
  if (typeof arguments[0] == 'object') {
      var images = arguments[0];
      var n = images.length;
  } else {
      var images = arguments;
      var n = images.length - 1;
  }
  var not_loaded = n;
  for (var i = 0; i < n; i++) {
      jQuery(new Image()).attr('src', images[i]).load(function() {
          if (--not_loaded < 1 && typeof callback == 'function') {
              callback();
          }
      });
  }
}
