<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
  'pi_name' => 'Freebie',
  'pi_version' => '0.0.4',
  'pi_author' => 'Doug Avery',
  'pi_author_url' => 'http://github.com/averyvery/Freebie#readme',
  'pi_description' => 'Check against any freebie segment',
  'pi_usage' => Freebie::usage()
  );

/**
 * Freebie
 *
 * @package   Freebie
 * @author    Doug Avery <doug.avery@viget.com>
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
  


  // --------------------------------------------------------------------
  /**
   * Usage
   *
   * This function describes how the plugin is used.
   *
   * @access  public
   * @return  string
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