<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
class MY_Output extends CI_Output {

        public function _display($output = '')
        {
            parent::_display($output);
            // If Smarty is active - NOTE: $this->output->enable_profiler(TRUE) active Smarty debug to simplify
            if (class_exists('CI_Controller') && class_exists('Smarty_Internal_Debug') && (config_item('smarty_debug') || $this->enable_profiler)) {
                $CI =& get_instance();
                Smarty_Internal_Debug::display_debug( $CI->smarty);
            }
        }
}