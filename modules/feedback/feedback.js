$(document).ready(function() {
	$("i.close").live("click", function() {
		$("div.popup").remove();
	});

	var options = { target : "#sendMess",
	url : "__feedback"
	};

	$("#sendMess").livequery("submit", function() {
		$("#sendMess").ajaxSubmit(options);
		return false;
	});

	$(".reply_form").livequery("submit", function() {
		var id = $(this).attr('id');
		var email = $(this).attr('title');
		var options = { target : $(this).parentsUntil('tr'),
		url : "__feedback?REPLY=" + id + "&EMAIL=" + email
		};
		$(this).ajaxSubmit(options);
		return false;
	});

	$('a.reply').live("click", function() {
		var id = $(this).attr('id');
		var email = $(this).attr('title');
		$.get("__feedback?REPLY_FORM=" + id + "&EMAIL=" + email, function(data) {
			$('div.reply_' + id).html(data);
		});
		return false;
	});

	$('a.feedback_message').click(function() {
		var id = $(this).attr('id');
		var email = $(this).attr('title');
		var style = $('#message_' + id).attr('style');
		if (!style || style == 'display: none;') {
			$.get("__feedback?ITEM=" + email, function(data) {
				$('#message_' + id).html(data);
			});
			$('#message_' + id).attr('style', 'display: table-cell;');
		} else {
			$('#message_' + id).attr('style', 'display: none;');
		}
		$("#count_" + id).load("__feedback?COUNT=" + email);
		return false;
	});

	$('a.feedback, #feedback').click(function() {
		$.post("__feedback", { cmd : 'form',
		responseType : 'html'
		}, function(data) {
			$("div.popup").remove();
			$('body').prepend(data);
		});
		return false;
	});

});
