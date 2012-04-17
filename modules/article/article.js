$(document).ready(function() {
			$('#photo_anons').upload('article', 'photo_anons');
			$('#photo').upload('article', 'photo', {
						multi : true
					});

			$("table.client tr:nth-child(odd)").addClass("even");

			var ids = 111;
			$('#tree').livequery(function() {
						$(this).NestedSortable({
									accept : 'sort',
									helperclass : 'helper',
									onStart : function() {
										ids = $(this).attr('id');
									},
									onChange : function(ser) {
										$.post("/__article/", ser[0].hash);
									}
								});
					});
		});