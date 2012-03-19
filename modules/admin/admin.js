$(document).ready(function() {
	/*
	 * $('.tree, .sort').sortable( { cursor : 'move', connectWith: ".sort",
	 * opacity : 0.5, tolerance : "pointer" });
	 */
	var ids = 111;
	$('#tree').livequery(function() {
		$(this).NestedSortable( {
			accept : 'sort',
			helperclass : 'helper',
			onStart : function() {
				ids = $(this).attr('id');

			},
			onChange : function(ser) {
				$.post("/admin/", 'ids=' + ids + '&m=' + ser[0].hash);
			}
		});
	});

	function dialog_close() {
		$("#dialog").dialog("close");
		$('#dialog_main').remove();
		$('#dialog_meta_tags').remove();
		$('#dialog_options').remove();
		$("#tabs").tabs("destroy");
	}

	$("ul#tree li").livequery(function() {
		$(this).contextMenu( {
			menu : 'myMenu'
		}, function(action, el, pos) {
			switch (action) {
			case 'edit':

				$("#dialog").dialog( {
					title : 'Редактирование ' + $(el).attr('alt'),
					resizable : false,
					//modal : true,
					width : 500,
					position : 'center',

					open : function(event, ui) {
						$.get('__admin', {
							EDIT : $(el).attr('id')
						}, function(data) {
							$('#tabs').livequery(function() {
								$(this).append(data);
								present_checked();
								$(this).tabs( {
									show : function(event, ui) {
										alignDialog();
									}
								});
							});

						});
					},

					beforeclose : function(event, ui) {
						$('#dialog_message').empty();
						$('#dialog_main').remove();
						$('#dialog_meta_tags').remove();
						$('#dialog_options').remove();
						$("#tabs").tabs("destroy");
					},

					buttons : {
						"Сохранить" : function() {
							var options = {
								// target : '#dialog_message',
								url : '__admin/?SAVE=' + $(el).attr('id'),
								success : function(data) {
									// alert(data);
								if (data) {
									$('#dialog_message').html(data);
								} else {
									$("#dialog").dialog("close");
									$.get('__admin/?REFRESH', function(data) {
										$('#div_tree').replaceWith(data);
									});
								}
							}
							};

							$('#options-form').ajaxSubmit(options);
						},
						"Отменить" : function() {
							$(this).dialog("close");
						}
					}

				});

				$(window).resize(function() {
					alignDialog();
				});

				break;

			case 'new':

				$("#dialog").dialog( {
					title : 'Создание подраздела в ' + $(el).attr('alt'),
					resizable : false,
					//modal : true,
					width : 500,
					position : 'center',

					open : function(event, ui) {
						$.get('__admin', {
							EDIT : ''
						}, function(data) {
							$('#tabs').livequery(function() {
								$(this).append(data);
								present_checked();
								$(this).tabs( {
									show : function(event, ui) {
										alignDialog();
									}
								});
							});
						});
					},

					beforeclose : function(event, ui) {
						$('#dialog_message').empty();
						$('#dialog_main').remove();
						$('#dialog_meta_tags').remove();
						$('#dialog_options').remove();
						$("#tabs").tabs("destroy");
					},

					buttons : {
						"Сохранить" : function() {
							var options = {
								// target : '#dialog_message',
								url : '__admin/?ADD&SAVE=' + $(el).attr('id'),
								success : function(data) {
									// alert(data);
								if (data) {
									$('#dialog_message').html(data);
								} else {
									$("#dialog").dialog("close");
									$.get('__admin/?REFRESH', function(data) {
										$('#div_tree').replaceWith(data);
									});
								}
							}
							};
							$('#options-form').ajaxSubmit(options);
						},
						"Отменить" : function() {
							$(this).dialog("close");
						}
					}

				});

				$(window).resize(function() {
					alignDialog();
				});

				break;

			case 'delete':
				var title = 'Удаление';
				jConfirm('Вы уверены, что хотите удалить?', title, function(r) {
					if (r) {
						$.get('__admin', {
							DELETE : $(el).attr('id')
						}, function() {
							$.get('__admin/?REFRESH', function(data) {
								$('#div_tree').replaceWith(data);
							});
						});
					}
				});
				break;

			default:
				break;
			}
		});

	});
});

function present_checked() {
	$('.section_present_param').keypress(function() {
		$("#sp" + $(this).attr('id')).attr("checked", "checked");
	});

	/*$('#present_main').click(function() {
		if ($(this).attr('checked'))
			$('#present_anywhere').attr('checked', false);
	});
	$('#present_anywhere').click(function() {
		if ($(this).attr('checked'))
			$('#present_main').attr('checked', false);
	});*/
}

function alignDialog() {
	var height = $(window).height() - 173 - (100 * 2);
	if (height < 150) {
		height = 150;
	}
	$("#dialog_options").livequery(function() {
		$(this).css('height', height + 'px');
	});
	$("div[role='dialog']").center();
}
