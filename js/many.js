$(function() {
	$('.add').click(function(e) {
		e.preventDefault();

		var field = $('.' + $(this).data('field') + '_clone');

		var id = parseInt($('.field_container').last().attr('id')) + 1;
		var html = field.html().replace(/REPLACE_ID/gi, id);

		$(html).insertBefore('.input .add');
	});

	$('.remove').live('click', function(e) {
		e.preventDefault();

		$('#' + $(this).data('field')).remove();
	});
})