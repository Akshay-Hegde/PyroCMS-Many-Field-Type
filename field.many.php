<?php defined('BASEPATH') or exit('No direct script access allowed');

class Field_many
{
	public $field_type_slug			= 'many';
	
	public $db_col_type				= 'longtext';

	public $custom_parameters		= array('many_field_type', 'include_key', 'key_legend', 'value_legend');

	public $extra_validation        = 'validate_many';

	public $version					= '1.0';

	public $author					= array('name'=>'Chris Harvey', 'url'=>'http://www.chrisnharvey.com');

	private $cache;

	private $ci;

	public function __construct()
	{
		$this->ci =& get_instance();

		require_once __DIR__.'/validation.php';
	}

	public function param_many_field_type()
	{
		$this->ci->load->library('streams_core/type');

		$types = $this->ci->type->field_types_array();

		return form_dropdown('many_field_type', $types);
	}

	public function param_include_key()
	{
		$js = '<script type="text/javascript" src="'.site_url('streams_core/field_asset/js/many/admin.js').'"></script>';
		return $js.form_checkbox('include_key', 'Y', false);
	}

	public function param_key_legend()
	{
		return form_input('key_legend', 'Key');
	}

	public function param_value_legend()
	{
		return form_input('value_legend', 'Value');
	}

	public function form_output($data, $entry_id, $field)
	{
		$field_type = $data['custom']['many_field_type'];
		$field_type_class = "Field_{$field_type}";

		$this->field = new $field_type_class;
		$this->extra_validation = $this->field->extra_validation;

		$values = unserialize($data['value']);

		$i = 1;

		$form_output = 
		'<div class="'."{$data['form_slug']}_clone".'" style="display: none;">'.

			$this->_output(
				$data['form_slug'],
				'REPLACE_ID',
				null,
				$data['custom']['value_legend'],
				$data['custom']['include_key'] == 'Y',
				null,
				$data['custom']['key_legend'],
				true
			).

		'</div>';

		if ( ! empty($values)) {
			foreach ($values as $value) {
				$form_output .= $this->_output(
					$data['form_slug'],
					$i,
					$value['value'],
					$data['custom']['value_legend'],
					$data['custom']['include_key'] == 'Y',
					$value['key'],
					$data['custom']['key_legend']
				);

				$i++;
			}
		} else {
			$form_output .= $this->_output(
				$data['form_slug'],
				$i,
				null,
				$data['custom']['value_legend'],
				$data['custom']['include_key'] == 'Y',
				null,
				$data['custom']['key_legend']
			);
		}

		$js = '<script type="text/javascript" src="'.site_url('streams_core/field_asset/js/many/many.js').'"></script>';

		return form_hidden("field_type[{$data['form_slug']}]", $field->field_data['many_field_type']).
			$js.
			$form_output.
			'<a href="#" class="button add" data-field="'.$data['form_slug'].'">Add</a>';
	}

	private function _output($slug, $i = 1, $value = null, $value_legend = 'Value', $include_key = false, $key = null, $key_legend = 'Key', $add_remove = false)
	{
		$output_key = '';

		$add_remove = $add_remove ?: $i > 1;

		$remove_button = '<a href="#" class="button remove" data-field="'.$i.'">Remove</a>';

		if ($include_key) {
			$output_key = form_input("{$slug}[{$i}][key]", $key);
			$output_key = "<strong>{$key_legend}: </strong>".$output_key."<strong>{$value_legend}: </strong>";
		}

		$output = '<div class="field_container" id="'.$i.'">'.
			$output_key.
			$this->field->form_output(['form_slug' => "{$slug}[{$i}][value]", 'value' => $value], false, false).
			($add_remove ? $remove_button : '').
		 '</div>';

		return $output;
	}

	public function pre_save($input, $field, $stream, $row_id, $form_data)
	{
		unset($input['REPLACE_ID']);

		$field_type = $field->field_data['many_field_type'];
		$class = "Field_{$field_type}";

		$field = new $class;
		$field->CI =& get_instance();

		if (method_exists($field, 'pre_save')) {
			foreach ($input as &$value) {
				
				$value['value'] = $field->pre_save($value['value'], null, null, null, null);

			}
		}
		
		return serialize(array_values($input));
	}

	// I'm anticipating doing more with this method
	public function pre_output($input, $data)
	{
		return $input;
	}

	public function pre_output_plugin($row, $custom)
	{
		$row = unserialize($row);

		if ( ! empty($row)) {
			$field_type = $custom['many_field_type'];
			$class = "Field_{$field_type}";

			$field = new $class;
			$field->CI =& get_instance();

			foreach ($row as &$value) {
				if (method_exists($field, 'pre_output_plugin')) {
					$value['value'] = $field->pre_output_plugin($value, null);
				}
			}

			return $row;
		}
	}

}