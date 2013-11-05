<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name' => 'Freebie',
	'pi_version' => '0.2.2',
	'pi_author' => 'Doug Avery',
	'pi_author_url' => 'http://github.com/averyvery/Freebie#readme',
	'pi_description' => 'Check against any freebie segment',
	'pi_usage' => Freebie::usage()
	);

/**
 * Freebie
 *
 * @package		Freebie
 * @author		Doug Avery <doug.avery@viget.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html
 */

class Freebie
{

	function Freebie()
	{

	}

	function any()
	{
		$this->EE =& get_instance();
		$match = 'false';
		$name = $this->EE->TMPL->fetch_param('name');

		for ($i = 1; $i <= 11; $i++){
			if ( isset( $this->EE->config->_global_vars['freebie_'.$i] ) ) {
				if ( $this->EE->config->_global_vars['freebie_'.$i] == $name ) {
					$match = 'true';
				}
			}
		}

		return $match;
	}

	function is_number()
	{
		$this->EE =& get_instance();
		$match = 'false';
		$i = $this->EE->TMPL->fetch_param('segment');
		$freebie_seg = $this->EE->config->_global_vars['freebie_'.$i];

		if ( is_numeric( $freebie_seg ) ) {
			$match = 'true';
		}

		return $match;
	}

	function category_match($cat_key)
	{
		$this->EE =& get_instance();
		$match = '';
		$segment = $this->EE->TMPL->fetch_param('segment');
		$group_id = $this->EE->TMPL->fetch_param('group_id');
		$site_id = $this->EE->TMPL->fetch_param('site_id');
		$category_url = $this->EE->config->_global_vars['freebie_'.$segment];
		$query_string = "SELECT cat_id, cat_name, cat_description, cat_image FROM exp_categories WHERE cat_url_title = '$category_url'";
		if($group_id != ''){
			$query_string .= "AND group_id = '$group_id'";
		}
		if($site_id != ''){
			$query_string .= "AND site_id = '$site_id'";
		}

	 $query = mysql_query($query_string);
	 while( $row = mysql_fetch_assoc($query)) {
			$match = $row[$cat_key];
	 }

		return $match;
	}

  function category_name()
	{
		return $this->category_match('cat_name');
	}

  function category_id()
	{
		return $this->category_match('cat_id');
	}

  function category_description()
	{
		return $this->category_match('cat_description');
	}

  function category_image()
	{
		return $this->category_match('cat_image');
	}

	function debug()
	{
		$this->EE =& get_instance();
		if(isset($this->EE->config->_global_vars['freebie_debug_settings_to_ignore'])){
			echo('<br />To ignore: ' .
				$this->EE->config->_global_vars['freebie_debug_settings_to_ignore']);
		}
		if(isset($this->EE->config->_global_vars['freebie_debug_settings_ignore_beyond'])){
			echo('<br />Ignore beyond: ' .
				$this->EE->config->_global_vars['freebie_debug_settings_ignore_beyond']);
		}
		if(isset($this->EE->config->_global_vars['freebie_debug_settings_break_category'])){
			echo('<br />Break category: ' .
				$this->EE->config->_global_vars['freebie_debug_settings_break_category']);
		}
		if(isset($this->EE->config->_global_vars['freebie_debug_settings_remove_numbers'])){
			echo('<br />Remove numbers: ' .
				$this->EE->config->_global_vars['freebie_debug_settings_remove_numbers']);
		}
		if(isset($this->EE->config->_global_vars['freebie_debug_settings_always_parse'])){
			echo('<br />Always parse: ' .
				$this->EE->config->_global_vars['freebie_debug_settings_always_parse']);
		}
		if(isset($this->EE->config->_global_vars['freebie_debug_uri'])){
			echo('<br />URI: ' .
				$this->EE->config->_global_vars['freebie_debug_uri']);
		}
		if(isset($this->EE->config->_global_vars['freebie_debug_uri_stripped'])){
			echo('<br />URI stripped: ' .
				$this->EE->config->_global_vars['freebie_debug_uri_stripped']);
		}
		if(isset($this->EE->config->_global_vars['freebie_debug_segments'])){
			echo('<br />Segments: ' .
				$this->EE->config->_global_vars['freebie_debug_segments']);
		}
		if(isset($this->EE->config->_global_vars['freebie_debug_uri_cleaned'])){
			echo('<br />URI cleaned: ' .
				$this->EE->config->_global_vars['freebie_debug_uri_cleaned']);
		}
	}

/**
 * Compares the freebie original uri to the pagination_url in EE pagination, and returns an updated
 * url with "hidden" freebie segments
 * @return string updated pagination url
 */
	function adjust_pagination_url()
	{

		$this->EE =& get_instance();

		$pagination_url = $this->EE->TMPL->tagdata;

		if( isset($this->EE->config->_global_vars["freebie_original_uri"]) ){

			$freebie_url = $this->EE->config->_global_vars["freebie_original_uri"];

			$pagination_segments = explode("/", $pagination_url);
			$freebie_segments = explode("/", $freebie_url);

			// first, checking to see if our paginated_url segment has a pagination flag in the last segment
			if( substr($pagination_segments[ count($pagination_segments) - 1 ],0,1) === "P"){

				// next, check to see if the freebie_url has a pagination flag -- if so, replace it -- otherwise concat.
				if( substr($freebie_segments[ count($freebie_segments) - 1 ],0,1) === "P" ){
					$freebie_segments[ count($freebie_segments) - 1 ] = $pagination_segments[ count($pagination_segments) - 1 ];
				}else{
					$freebie_segments[] = $pagination_segments[ count($pagination_segments) - 1 ];
				}

				// lastly, re-stringify our newly adjusted url
				$adjusted_pagination_url = "/" . implode("/", $freebie_segments);

				return $adjusted_pagination_url;

			}

		}

		return $pagination_url;

	}


	// --------------------------------------------------------------------
	/**
	 * Usage
	 *
	 * This function describes how the plugin is used.
	 *
	 * @access	public
	 * @return	string
	 */
		function usage()
		{
		ob_start();
		?>

		Coming soon

		<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
		}
		// END

}
/* End of file pi.freebie.php */

/* Location: ./system/expressionengine/third_party/freebie/pi.freebie.php */
?>
