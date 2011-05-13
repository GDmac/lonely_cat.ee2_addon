<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

// Include the config file
require PATH_THIRD . 'lonely_cat/config.php';

/**
 * @package		Lonely Cat
 * @subpackage	ThirdParty
 * @category	Modules
 * @author		Wouter Vervloet
 * @link		http://www.baseworks.nl/
 */
class Lonely_cat_ft extends EE_Fieldtype {

	var $info = array(
		'name' => LONCAT_NAME,
		'version' => LONCAT_VERSION
	);

	// --------------------------------------------------------------------

	function Lonely_cat_ft()
	{
		parent::EE_Fieldtype();
	}

	// --------------------------------------------------------------------

	function display_field($data)
	{
		$this->EE->lang->loadfile('lonely_cat');

		$this->EE->load->library('api');
		$this->EE->api->instantiate(array('channel_categories', 'channel_structure'));

		// If there is no category set, fetch the default category if set.
		if (!$data)
		{
			$data = $this->EE->api_channel_structure->get_channel_info($this->EE->input->get_post('channel_id'))->row('deft_category');
		}


		$options = array();
		$cats = $this->_fetch_categories($data);

		$cat_group = NULL;

// 		 debug($cats);		

		if($this->settings['hide_none']=='no')
		{
			$options['0'] = $this->EE->lang->line('none');
		}
		
		foreach ($cats as $val) {
			$indent = ($val['5'] != 1) ? repeater(NBS . NBS . NBS, $val['5']) : '';
			$options[$val['3']][$val['0']] = $indent . $val['1'];
		}

		return form_dropdown($this->field_name, $options, $data);
	}

	// --------------------------------------------------------------------
	function validate()
	{
		return TRUE;
	}

	// --------------------------------------------------------------------
	function save($data=0)
	{
		$cats = !$data ? array() : (array) $data;

		$this->EE->api_channel_categories->cat_parents = $cats;

		return $data;
	}

	// --------------------------------------------------------------------
	function _fetch_categories($data)
	{

		return $this->EE->api_channel_categories->categories;
	}

	// --------------------------------------------------------------------
	function display_settings($settings)
	{
		$this->EE->lang->loadfile('lonely_cat');

		$this->EE->table->add_row(
			$this->EE->lang->line('hide_none'), form_checkbox('hide_none', 'yes', (isset($settings['hide_none']) && $settings['hide_none']=='yes'))
		);
	}

	// --------------------------------------------------------------------
	function save_settings($data)
	{
		return array(
			'hide_none' => ($this->EE->input->post('hide_none')=='yes' ? 'yes' : 'no')
		);
	}


}

// END Lonely_cat_ft class

/* End of file ft.lonely_cat.php */
/* Location: ./system/expressionengine/third_party/lonely_cat/ft.lonely_cat.php */