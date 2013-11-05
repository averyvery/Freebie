<?php

/**
 * Freebie Extension Class for ExpressionEngine 2
 *
 * @package  Freebie
 * @author   Doug Avery <doug.avery@viget.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html
 */
class Freebie_ext {

	/**
	 * Required vars
	 */
	var $name = 'Freebie';
	var $description = 'Tell EE to ignore specific segments when routing URLs';
	var $version = '0.2.2';
	var $settings_exist = 'y';
	var $docs_url = 'http://github.com/averyvery/Freebie#readme';

	/**
	 * Settings
	 */
	var $settings = array();
	var $settings_default = array(
		'to_ignore'			 => 'success|error|preview',
		'ignore_beyond'  => '',
		'break_category' => 'no',
		'remove_numbers' => 'no',
		'always_parse'	 => '',
		'always_parse_pagination' => 'no'
	);

	function settings(){
		$settings['to_ignore']			= array('t', null, $this->settings_default['to_ignore']);
		$settings['ignore_beyond']	= array('t', null, $this->settings_default['ignore_beyond']);
		$settings['break_category'] = array('r', array('yes' => 'yes', 'no' => 'no'),
																				 $this->settings_default['break_category']);
		$settings['remove_numbers'] = array('r', array('yes' => 'yes', 'no' => 'no'),
																				 $this->settings_default['remove_numbers']);
		$settings['always_parse_pagination'] = array('r', array('yes' => 'yes', 'no' => 'no'),
																				 $this->settings_default['always_parse_pagination']);
		$settings['always_parse']		= array('t', null, $this->settings_default['always_parse']);
		return $settings;
	}

	/**
	 * Extension constructor
	 *	 the unaltered URI is 'dirty' — it potentially has /segments/ that will break our site
	 *	 the final URI and segments will be 'clean' — EE will use them for routing, and all will be well
	 */
	function Freebie_ext($settings='')
	{
		// get EE global instance and extension settings
		$this->EE =& get_instance();
		$this->settings = $settings;

		if($this->should_execute()){

			// clear cache if necessary
			$this->clear_cache();

			// remove any url params
			$this->remove_and_store_params();

		  /**
			 * EE 2.0 relies on an internal array of segments for routing
			 *		we'll be 'cleaning' our URI and producing shiny new segments from it,
			 *		but we still want to have access to the 'dirty' segments as {segment_1}, etc.
			 */
			$this->set_dirty_segments_as_global_vars();

			// prep the user settings to use for cleaning the URI
			$this->settings['to_ignore']			= $this->parse_settings($this->settings['to_ignore']);
			$this->settings['ignore_beyond'] = $this->parse_settings($this->settings['ignore_beyond']);
			$this->EE->config->_global_vars['freebie_debug_settings_to_ignore'] = $this->settings['to_ignore'];
			$this->EE->config->_global_vars['freebie_debug_settings_ignore_beyond'] = $this->settings['ignore_beyond'];
			$this->EE->config->_global_vars['freebie_debug_settings_break_category'] = $this->settings['break_category'];
			$this->EE->config->_global_vars['freebie_debug_settings_remove_numbers'] = $this->settings['remove_numbers'];
			$this->EE->config->_global_vars['freebie_debug_settings_always_parse'] = $this->settings['always_parse'];
			$this->EE->config->_global_vars['freebie_debug_settings_always_parse_pagination'] = $this->settings['always_parse_pagination'];

			// if category breaking is on, retrieve the category url indicator and set it as a break segment
			$this->break_on_category_indicator();

			// determine which segments to ALWAYS parse, and to always parse beyond
			$this->get_always_parse();

			// remove the 'dirty' bits from the URI, which a user has specified in the settings
			$this->clean_uri();

			// re-fill the segment arrays from our new, clean URI
			$this->that_was_a_freebie();

			// re-execute the routing based on clean segments
			$RTR =& load_class('Router', 'core');
			$RTR->_parse_routes();

			// re-indexing segments (moving 0 to 1, 1 to 2, etc) is required after routing
			$this->EE->uri->_reindex_segments();

			// re-add params to url
			$this->restore_params();

		}

	}


	/**
	 * check to see if the conditions are in place to run freebie
	 */
	function should_execute(){

		// is a URI? (lame test for checking to see if we're viewing the CP or not)
		return isset($this->EE->uri->uri_string) &&
					 substr($this->EE->uri->uri_string, 0) != '?' &&

					 // Freebie actually executes twice - but the second time,
					 // the "settings" object isn't an array, which breaks it.
					 // (No idea why). Checking type fixes this.
					 gettype($this->settings) == 'array';

	}

	/**
	 * Remove any variables from the segments
	 */
	function remove_and_store_params(){

		// Store URI for debugging
		$this->EE->config->_global_vars['freebie_original_uri'] = $this->EE->uri->uri_string;

		$this->param_pattern  = '#(';    // begin match group
		$this->param_pattern .=   '\?';    // match a '?';
		$this->param_pattern .=   '|';   // OR
		$this->param_pattern .=   '\&';    // match a '?';
		$this->param_pattern .= ')';    // end match group
		$this->param_pattern .= '.*$';   // continue matching characters until end of string
		$this->param_pattern .= '#';    // end match

		$matches = Array();
		preg_match($this->param_pattern, $this->EE->uri->uri_string, $matches);
		$this->url_params = (isset($matches[0])) ? $matches[0] : '';
		$this->EE->uri->uri_string = preg_replace($this->param_pattern, '', $this->EE->uri->uri_string);

		// Store stripped URI for debugging
		$this->EE->config->_global_vars['freebie_stripped_uri'] = $this->EE->uri->uri_string;
	}

	/**
	 * Clear the cache on the first (uncached) pageload since saving
	 */
	function clear_cache(){

		$results = $this->EE->db->query("SELECT * FROM exp_extensions WHERE class='Freebie_ext'");
		$db_settings = array();

		if ( $results->num_rows() > 0 ) {
			foreach( $results->result_array() as $row ) {
				$db_settings = ( unserialize( $row['settings'] ) );
			}
		}

		if ( ! isset( $db_settings['cache_cleared'] ) ) {

			// clear the DB cache
			$this->EE->functions->clear_caching('db');

			// add 'cache_cleared' to the settings
			$db_settings['cache_cleared'] = 'yes';
			$data = array('settings' => serialize($db_settings) );

			$sql = $this->EE->db->update_string('exp_extensions', $data, "class = 'Freebie_ext'");
			$this->EE->db->query($sql);

		}

	}

	/**
	 * convert the original segments from the URI to {segment_n}-type global variables
	 */
	function set_dirty_segments_as_global_vars(){

		$segments = $this->EE->uri->segments;
		$this->store_last_segment($segments);
		$segments = array_pad($segments, 10, '');
		for ($i = 1; $i <= count($segments); $i++){
			$segment = $segments[$i - 1];
			$segment = $this->strip_params_from_segment($segment);
			$this->EE->config->_global_vars['freebie_'.$i] = $segment;
		}

		// Store original segments for debugging
		$this->EE->config->_global_vars['freebie_debug_segments'] = implode('+', $this->EE->uri->segments);

	}

	/**
	 * remove any parameters from a segment
	 */
	function strip_params_from_segment($segment = ''){

		$segment = preg_replace($this->param_pattern, '', $segment);
		return $segment;

	}

	/**
	 * translate user settings to stuff we can use in the code
	 */
	function parse_settings($original_str){

		// convert newline- and space-delimited settings to pipe-delimited ones
		$str = preg_replace('/(\n| )/', '|', $original_str, -1);

		// turn *s into true regex wildcards
		return preg_replace('/\*/', '.*?', $str, -1);

	}

	/**
	 * add the category url indicator to the "break" array
	 */
	function break_on_category_indicator(){

		// did user set 'break category' to 'yes'?
		$break_category = isset( $this->settings['break_category'] ) &&
											$this->settings['break_category'] == 'yes' &&
											$this->EE->config->config['use_category_name'] == 'y';

		if ( $break_category ) {
			$this->settings['to_ignore']		 .= '|'.$this->EE->config->config['reserved_category_word'];
			$this->settings['ignore_beyond'] .= '|'.$this->EE->config->config['reserved_category_word'];
		}

	}

	/**
	 * preserve the last segment
	 */
	function store_last_segment($segments){

		$this->EE->config->_global_vars['freebie_last'] = isset($segments[count($segments)]) ? $segments[count($segments)] : '';

	}

	/**
	 * get specific segments that we ALWAYS want to parse, and to parse beyond
	 */
	function get_always_parse(){

		if ($this->settings['always_parse_pagination'] == 'yes')
		{
			$dirty_array		= explode('/', $this->EE->uri->uri_string);
			$clean_array		= array();

			foreach ($dirty_array as $segment){
				if(preg_match("#^P(\d+)$#", $segment)){
					$this->settings['always_parse'] .= '|' . $segment;
				}
			}
		}

		$this->settings['always_parse'] .= '|' . $this->EE->config->config['profile_trigger'];


	}
	/**
	 * remove segments, based on the user's settings
	 */
	function clean_uri(){

		// make an array full of "original" segments,
		// and a blank array to move the good ones too
		$dirty_array		= explode('/', $this->EE->uri->uri_string);
		$clean_array		= array();

		// did user set 'remove numbers' to 'yes'?
		$remove_numbers = isset($this->settings['remove_numbers']) &&
											$this->settings['remove_numbers'] == 'yes';

		$break = false;
		$parse_all_remaining = false;
		$count = 0;

		// move any segments that don't match patterns to clean array
		foreach ($dirty_array as $segment){

			$is_not_a_always_parse_segment =
				preg_match('#^('.$this->settings['always_parse'].')$#', $segment ) == false;

			if( $is_not_a_always_parse_segment && $parse_all_remaining == false ){

				$should_be_ignored = preg_match('#^('.$this->settings['to_ignore'].')$#', $segment ) == false;

				if( $should_be_ignored && $break == false ){

					// if this segment isn't killed by the "no numbers" setting,
					// move it to the new array
					if(!$remove_numbers || !is_numeric($segment)){
						array_push($clean_array, $segment);
					}

				}

				// if this segment is one of the breakers, stop looping
				if( preg_match('#^('.$this->settings['ignore_beyond'].')$#', $segment) ){
					$break = true;
					$this->set_remaining_segments_as_postbreaks($count, $dirty_array);
				}

			} else {

				array_push( $clean_array, $segment );
				$parse_all_remaining = true;

			}

			$count++;

		}

		if(count($clean_array) != 0){
			$this->EE->uri->uri_string = implode('/', $clean_array);
		} else {
			$this->EE->uri->uri_string = '';
		}

		// Store 'cleaned' uri_string for debugging
		$this->EE->config->_global_vars['freebie_debug_uri_cleaned'] = $this->EE->uri->uri_string;

	}

	/**
	 * Sets all segments after a break as postbreak segments
	 */
	function set_remaining_segments_as_postbreaks($count, $segments){

		$segments = array_slice($segments, $count + 1);
		$segments = array_pad($segments, 10, '');

		for ($i = 1; $i <= count($segments); $i++){
			$segment = $segments[$i - 1];
			$segment = $this->strip_params_from_segment($segment);
			$this->EE->config->_global_vars['freebie_break_'.$i] = $segment;
		}

	}

	/**
	 * Unset existing internal segment arrays,
	 *	 fetch new ones from the clean URI
	 */
	function that_was_a_freebie(){
		$this->EE->uri->segments = array();
		$this->EE->uri->rsegments = array();
		$this->EE->uri->_explode_segments();
	}

	/**
	 * Re-add params to the uri
	 */
	function restore_params(){
		$this->EE->uri->uri_string .= $this->url_params;
		$this->EE->config->_global_vars['freebie_final_uri'] = $this->EE->uri->uri_string;
	}

	/**
	 * Activate Extension
	 */
	function activate_extension()
	{

		$data = array(
			'class'				=> 'Freebie_ext',
			'hook'				=> 'sessions_start',
			'method'			=> 'Freebie_ext',
			'settings'		=> serialize($this->settings_default),
			'priority'		=> 10,
			'version'			=> $this->version,
			'enabled'			=> 'y'
		);

		// insert in database
		$this->EE->functions->clear_caching('db');
		$this->EE->db->insert('exp_extensions', $data);

	}

	/**
	 * Update Extension
	 */
	function update_extension()
	{
		$this->activate_extension();
	}

	/**
	 * Delete extension
	 */
	function disable_extension()
	{
		$this->EE->functions->clear_caching('db');
		$this->EE->db->where('class', 'Freebie_ext');
		$this->EE->db->delete('exp_extensions');
	}


}

?>
