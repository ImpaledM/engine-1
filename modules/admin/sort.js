$(document).ready(function() {
	var order = 0;
	$("#sortable").sortable({
		axis : 'y',
		opacity : 0.5,
		update: function() {
      sort = $('#sortable').sortable('toArray'); // массив с порядком
      $.post('__sort',{cmd: 'save', sort: sort});
   }
	});
});