<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * Turn Smarty debug compatible whith Ci-Smarty parse fetch method
 *
 * Responsible for sending debug Smarty final output to browser (Smarty_Internal_Debug::display_debug)
 * using debug console (pop-window)
 * (tks for Redn0x - http://www.smarty.net/docs/en/chapter.debugging.console.tpl)
 *
 * @category Output
 * @package  CodeIgniter
 * @author   Tariqul Islam <tareq@webkutir.net>
 * @license  http://directory.fsf.org/wiki/License:ReciprocalPLv1.3 Reciprocal Public License v1.3
 * @link     http://webkutir.net
 */
class MY_Output extends CI_Output
{
    /**
     * _display
     *
     * Turn Smarty debug compatible whith Ci-Smarty parse fetch method
     *
     * @param string $output output of the method
     *
     * @return void
     */
    function _display($output = '')
    {
        parent::_display($output);
        //If Smarty is active - NOTE: $this->output->enable_profiler(TRUE) active Smarty debug to simplify
        if (class_exists('CI_Controller')
            && class_exists('Smarty_Internal_Debug')
            && (config_item('smarty_debug') || $this->enable_profiler)
        ) {
            $CI =& get_instance();
            Smarty_Internal_Debug::display_debug($CI->smarty);
        }
    }
}
// END MY_Output Class
/* End of file MY_Output.php */
/* Location: ./application/core/MY_Output.php */