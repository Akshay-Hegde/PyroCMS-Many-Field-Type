$(function()
{
	$('[name="include_key"]').change(function(e) {
		e.preventDefault();

		if ($(this).prop('checked')) {
			$('[name="key_legend"]').parent().parent().slideDown();
		} else {
			$('[name="key_legend"]').parent().parent().slideUp();
		}
	}).change();
});