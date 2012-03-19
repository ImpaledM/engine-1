$(document).ready(function() {
	var ids = 111;
	$('#tree').livequery(function() {
		$(this).NestedSortable({
			accept : 'sort',
			helperclass : 'helper',
			onStart : function() {
				ids = $(this).attr('id');
			},
			onChange : function(ser) {
				$.post("/__counters/", ser[0].hash);
			}
		});
	});
});