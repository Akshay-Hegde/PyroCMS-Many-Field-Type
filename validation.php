<?php defined('BASEPATH') or exit('No direct script access allowed');

function validate_many($value)
{
	static $processed = [];

	$ci = get_instance();
	$post = $ci->input->post();

	$field = false;

	foreach ($post as $key => $data) {
		if ($data == $value) {
			$field = $key;
			break;
		}
	}

	unset($value['REPLACE_ID']);

	if ($field and ! in_array($field, $processed)) {
		$field_type = isset($post['field_type'][$field]) ? $post['field_type'][$field] : false;

		if ($field_type) {
			$field_class = "Field_{$field_type}";
			$obj = new $field_class;

			foreach ($value as $entry) {
				$_POST['check_field'] = $entry['value'];
				$ci->form_validation->set_rules('check_field', $field, $obj->extra_validation);

				$processed[] = $field;

				if ( ! $ci->form_validation->run()) {
					return false;
				} else {
					return true;
				}
			}
		}
	}
}