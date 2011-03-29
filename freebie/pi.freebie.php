<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name' => 'Freebie',
	'pi_version' => '0.1.2',
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
		$category_url = $this->EE->config->_global_vars['freebie_'.$segment];
		$query_string = "SELECT cat_id, cat_name FROM exp_categories WHERE cat_url_title = '$category_url'";
		if($group_id != ''){
			$query_string .= "AND group_id = '$group_id'";
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
