// плагин для выбора тренеров, дакладчиков и контактных лиц
jQuery.fn.add_item = function(options) {
	var settings = jQuery.extend({
		name : '',
		type : 'type_sc',
		open : false
	}, options);

	$('#add_' + settings.name).dialog({
		autoOpen : false,
		width : 600,
		zIndex : 3999,
		bgiframe : true,
		buttons : {
			"Отменить" : function() {
				$(this).dialog("close");
			},
			"Добавить" : function() {
				var options = {
					target : "",
					url : '/__' + settings.name + '/?ADMIN&ajax',

					success : function(data) {
						if (data) {
							if (data.indexOf('error') > 0) {
								$('#add_' + settings.name + ' ul').remove();
								$('#add_' + settings.name + ' form').before(data);
							} else {
								$('#add_' + settings.name).dialog("close");
								if (settings.open) {
									$('#link_select_' + settings.name).click();
								}
								if (settings.name == 'places') {
									$.post("/__" + settings.name, {
										method : 'refresh_' + settings.name
									}, function(data) {
										$('#' + settings.name).replaceWith(data);
									});
								}
							}
						} else 
							jAlert('Ошибка соединения с сервером', '');
					}
				};

				$('#add_' + settings.name + ' form').ajaxSubmit(options);
			}
		}
	});

	function add_item() {
		$.post("/__" + settings.name, {
			method : 'add_' + settings.name,
			type : settings.type
		}, function(data) {
			$('#add_' + settings.name).empty();
			$('#add_' + settings.name).append(data);
		});
		$('#add_' + settings.name).dialog('open');
	}

	if (settings.open) {
		add_item();
	} else {
		$('#link_add_' + settings.name).click(function() {
			add_item();
			return false;
		});
	}
};

jQuery.fn.select_item = function(options) {
	var settings = jQuery.extend({
		name : ''
	}, options);

	$("a.unselect").live('click', function() {
		$(this).parent().remove();
		$($(this).attr('href')).remove();
		return false;
	});

	function select_item() {
		var str = '';
		$('#' + settings.name).empty();
		$('#select_' + settings.name + '_dialog input').each(function() {
			if ($(this).attr('checked')) {
				str += '<div>' + '<div style="width:300px;float:left;">' + $(this).siblings().text() + '</div>' + '<a href="#id_' + settings.name + '_' + $(this).val() + '"class="unselect"> Отменить выбор</a>' + '</div>';
				$('#' + settings.name).append('<input type="hidden" id="id_' + settings.name + '_' + $(this).val() + '" name="id_' + settings.name + '[' + $(this).val() + '][name]" value="' + $(this).siblings().text() + '">');
			}
		});
		$('#' + settings.name).append(str);
		$('#select_' + settings.name).dialog("close");
	}

	var options = {
		autoOpen : false,
		width : 500,
		zIndex : 3999,
		bgiframe : true
	}

	switch (settings.name) {
		case 'trainers':
			options.buttons = {
				"Отменить" : function() {
					$(this).dialog("close");
				},
				"Выбрать" : function() {
					select_item();
				},
				"Добавить нового тренера" : function() {
					$('#select_' + settings.name).dialog("close");
					$('#add_' + settings.name).add_item({
						name : settings.name,
						open : true
					});
				}
			}
		break;

		case 'contacts':
			options.buttons = {
				"Отменить" : function() {
					$(this).dialog("close");
				},
				"Выбрать" : function() {
					select_item();
				},
				"Добавить нового представителя" : function() {
					$('#select_' + settings.name).dialog("close");
					$('#add_' + settings.name).add_item({
						name : settings.name,
						open : true
					});
				}
			}
		break;

		case 'speakers':
			options.buttons = {
				"Отменить" : function() {
					$(this).dialog("close");
				},
				"Выбрать" : function() {
					select_item();
				},
				"Добавить нового докладчика" : function() {
					$('#select_' + settings.name).dialog("close");
					$('#add_' + settings.name).add_item({
						name : settings.name,
						open : true
					});
				}
			}
		break;

		case 'sponsors':
			options.buttons = {
				"Отменить" : function() {
					$(this).dialog("close");
				},
				"Выбрать" : function() {
					select_item();
				},
				"Добавить нового спонсора" : function() {
					$('#select_' + settings.name).dialog("close");
					$('#add_' + settings.name).add_item({
						name : settings.name,
						open : true
					});
				}
			}
		break;

		case 'advertise':
			options.buttons = {
				"Отменить" : function() {
					$(this).dialog("close");
				},
				"Выбрать" : function() {
					select_item();
				}
			}
		break;

		default:
			options.buttons = {
				"Отменить" : function() {
					$(this).dialog("close");
				},
				"Выбрать" : function() {
					select_item();
				}
			}
			if ('button_add' in settings) {
				options.buttons[settings.button_add] = function() {
					$('#select_' + settings.name).dialog("close");
					$('#add_' + settings.name).add_item({
						name : settings.name,
						open : true
					});
				}
			}
		break;
	}

	$('#select_' + settings.name).dialog(options);

	$('#link_select_' + settings.name).click(function() {
		$.post("/__" + settings.name, {
			method : 'select_' + settings.name
		}, function(data) {
			if (data != '') {
				$('#select_' + settings.name).empty();
				$('#select_' + settings.name).append(data);
				$('#select_' + settings.name + '_dialog input').each(function() {
					if ($('#' + settings.name).children().is('#id_' + settings.name + '_' + $(this).val())) {
						$(this).attr('checked', 'checked');
					}
				});
				$('#select_' + settings.name).dialog('open');
			} else {
				$('#add_' + settings.name).add_item({
					name : settings.name,
					open : true
				});
			}
		});
		return false;
	});

	// hover states on the static widgets
	$('#link_select_' + settings.name + ', ul#icons li').hover(function() {
		$(this).addClass('ui-state-hover');
	}, function() {
		$(this).removeClass('ui-state-hover');
	});
};