<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * CI Smarty
 *
 * Smarty templating for Codeigniter
 *
 * @category CodeIgniter
 * @package  CI_Smarty
 * @author   Dwayne Charrington <email@email.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0.html Apache License Version 2.0
 * @link     http://ilikekillnerds.com
 */
class MY_Parser extends CI_Parser
{
    protected $CI;
    protected $_module = '';
    protected $_template_locations = array();
    // Current theme location
    protected $_current_path = null;
    // The name of the theme in use
    protected $_theme_name = '';
    /**
     * Class Constructor
     */
    public function __construct()
    {
        // Codeigniter instance and other required libraries/files
        $this->CI =& get_instance();
        //$this->CI->load->library('smarty');
        $this->CI->load->helper('parser', true);
        // Detect if we have a current module
        if ($this->_module=='') {
            $this->_module = $this->current_module();
        }
        // What controllers or methods are in use
        $this->_controller  = $this->CI->router->fetch_class();
        $this->_method      = $this->CI->router->fetch_method();
        // If we don't have a theme name stored
        if ($this->_theme_name == '') {
            $this->set_theme(config_item('theme_name'));
        }
        // Update theme paths
        $this->_update_theme_paths();
    }
    /**
     * Set the Module
     *
     * @param string $module Name of the Module to set
     *
     * @return void
     */
    public function setModule($module)
    {
        $this->_module=$module;
        $this->_update_theme_paths();
    }
    /**
     * Get the Module
     *
     * @return void
     */
    public function getModule()
    {
        return $this->_module;
    }
    /**
     * Call
     * able to call native Smarty methods
     *
     * @param string $method Name of Method
     * @param array  $params Array of the Params
     *
     * @return mixed
     */
    public function __call($method, $params=array())
    {
        if (!method_exists($this, $method)) {
            return call_user_func_array(array($this->CI->smarty, $method), $params);
        }
    }
    /**
     * Set Theme
     *
     * Set the theme to use
     *
     * @param string $name Name of the Theme to set
     *
     * @return string
     */
    public function set_theme($name)
    {
        // Store the theme name
        $this->_theme_name = trim($name);
        // Our themes can have a functions.php file just like Wordpress
        $functions_file  = config_item('theme_path') . $this->_theme_name . '/functions.php';
        // Incase we have a theme in the application directory
        $functions_file2 = APPPATH."themes/" . $this->_theme_name . '/functions.php';
        // If we have a functions file, include it
        if (file_exists($functions_file)) {
            include_once $functions_file;
        } elseif (file_exists($functions_file2)) {
            include_once $functions_file2;
        }
        // Update theme paths
        $this->_update_theme_paths();
    }
    /**
     * Get Theme
     *
     * Does what the function name implies: gets the name of
     * the currently in use theme.
     *
     * @return string
     */
    public function get_theme()
    {
        return (isset($this->_theme_name)) ? $this->_theme_name : '';
    }
    /**
     * Current Module
     *
     * Just a fancier way of getting the current module
     * if we have support for modules
     *
     * @return string
     */
    public function current_module()
    {
        // Modular Separation / Modular Extensions has been detected
        if (method_exists($this->CI->router, 'fetch_module')) {
            $module = $this->CI->router->fetch_module();
            return (!empty($module)) ? $module : '';
        } else {
            return '';
        }
    }
    /**
     * Parse
     *
     * Parses a template using Smarty 3 engine
     *
     * @param string  $template Name of the Template
     * @param array   $data     Array of Data
     * @param boolean $return   Indicates if it return the view or not
     * @param boolean $caching  Indicates if caching is enabled or not
     * @param string  $theme    Theme Name
     *
     * @return string
     */
    public function parse($template, $data = array(), $return = false, $caching = true, $theme = '')
    {
        // Check the Permission and read dir if necessary
        /*
        if (is_array($this->CI->session->userdata('permission'))
            && in_array("all", $this->CI->session->userdata('permission'))
        ) {
            $this->CI->load->helper('file');
            $data['files']=get_filenames(APPPATH.'views/default/navigation/');
            sort($data['files']);
        }
        */
        // If we don't want caching, disable it
        if ($caching === false) {
            $this->CI->smarty->disable_caching();
        }
        // If no file extension dot has been found default to defined extension for view extensions
        if ( ! stripos($template, '.')) {
            $template = $template.".".$this->CI->smarty->template_ext;
        }
		// If no file extension dot has been found default to defined extension for view extensions
        if ( stripos($template, '@pd@') ) {
			$module_fetch = explode('@pd@',$template);
			if(is_array($module_fetch) && !empty($module_fetch)){
				$this->_template_locations = array_merge( array(APPPATH . 'modules/' . array_shift($module_fetch). '/views/'), $this->_template_locations);
//                pr((APPPATH . 'modules/' . array_shift($module_fetch). '/views/'));
//                pre($this->_template_locations);
			}
            $template = array_shift($module_fetch);
        }
		
		
        // Are we overriding the theme on a per load view basis?
        if ($theme !== '') {
            $this->set_theme($theme);
        }
        // Get the location of our view, where the hell is it?
        // But only if we're not accessing a smart resource
        if ( ! stripos($template, ':')) {
            $template = $this->_find_view($template);
        }
        // If we have variables to assign, lets assign them
        if ( ! empty($data)) {
            foreach ($data AS $key => $val) {
                $this->CI->smarty->assign($key, $val);
            }
        }

//        $template = ob_html_compress($template);

        // Load our template into our string for judgement
        $template_string = $this->CI->smarty->fetch($template);

        // If we're returning the templates contents, we're displaying the template
        if ($return === false) {
            $this->CI->output->append_output($template_string);
            return true;
        }
        // We're returning the contents, fo' shizzle
        return $template_string;
    }
    /**
     * CSS
     *
     * An asset function that returns a CSS stylesheet
     *
     * @param string $file       File Name
     * @param array  $attributes Attributes array
     *
     * @return string
     */
    public function css($file, $attributes = array())
    {
        $defaults = array(
            'media' => 'screen',
            'rel'   => 'stylesheet',
            'type'  => 'text/css'
        );
        $attributes = array_merge($defaults, $attributes);
        $return  = '<link rel="'.$attributes['rel'].'" type="'.$attributes['type'].'" ';
        $return .= 'href="'.base_url(config_item('theme_path').$this->get_theme()."/css/".$file);
        $return .= '" media="'.$attributes['media'].'">';
        return $return;
    }
    /**
     * JS
     *
     * An asset function that returns a script embed tag
     *
     * @param string $file       File Name
     * @param array  $attributes Attributes array
     *
     * @return string
     */
    public function js($file, $attributes = array())
    {
        $defaults = array(
            'type'  => 'text/javascript'
        );
        $attributes = array_merge($defaults, $attributes);
        $return = '<script type="'.$attributes['type'].'" src="'.
            base_url(config_item('theme_path').$this->get_theme()."/js/".$file).'"></script>';
        return $return;
    }
    /**
     * IMG
     *
     * An asset function that returns an image tag
     *
     * @param string $file       File Name
     * @param array  $attributes Attributes array
     *
     * @return string
     */
    public function img($file, $attributes = array())
    {
        $defaults = array(
            'alt'    => '',
            'title'  => ''
        );
        $attributes = array_merge($defaults, $attributes);
        $return = '<img src ="'.base_url(config_item('theme_path').$this->get_theme()."/img/".$file).
            '" alt="'.$attributes['alt'].'" title="'.$attributes['title'].'" />';
        return $return;
    }
    /**
     * Theme URL
     *
     * A web friendly URL for determining the current
     * theme root location.
     *
     * @param string $location Theme Location
     *
     * @return string
     */
    public function theme_url($location = '')
    {
        // The path to return
        $return = base_url(config_item('theme_path').$this->get_theme())."/";
        // If we want to add something to the end of the theme URL
        if ($location !== '') {
            $return = $return.$location;
        }
        return trim($return);
    }
    /**
     * Find View
     *
     * Searches through module and view folders looking for your view, sir.
     *
     * @param string $file File Name
     *
     * @return string The path and file found
     */
    protected function _find_view($file)
    {
        // We have no path by default
        $path = null;
        // Iterate over our saved locations and find the file
        foreach ($this->_template_locations AS $location) {
            if (file_exists($location.$file)) {
                // Store the file to load
                $path = $location.$file;
                $this->_current_path = $location;
                // Stop the loop, we found our file
                break;
            }
        }
        // Return the path
        return $path;
    }
    /**
     * Add Paths
     *
     * Traverses all added template locations and adds them
     * to Smarty so we can extend and include view files
     * correctly from a slew of different locations including
     * modules if we support them.
     *
     * @return void
     */
    protected function _add_paths()
    {
        // Iterate over our saved locations and find the file
        foreach ($this->_template_locations AS $location) {
            $this->CI->smarty->addTemplateDir($location);
        }
    }
    /**
     * Update Theme Paths
     *
     * Adds in the required locations for themes
     *
     * @return void
     */
    protected function _update_theme_paths()
    {
        // Store a whole heap of template locations
        $this->_template_locations = array(
            config_item('theme_path') . $this->_theme_name . '/views/modules/' . $this->_module .'/layouts/',
            config_item('theme_path') . $this->_theme_name . '/views/modules/' . $this->_module .'/',
            config_item('theme_path') . $this->_theme_name . '/views/layouts/',
            config_item('theme_path') . $this->_theme_name . '/views/',
            APPPATH . 'modules/' . $this->_module . '/views/layouts/',
            APPPATH . 'modules/' . $this->_module . '/views/' . $this->_theme_name . '/',
            APPPATH . 'modules/' . $this->_module . '/views/',
            APPPATH . 'views/layouts/',
            APPPATH . 'views/',
            APPPATH . 'views/' . $this->_theme_name . '/'
        );
        // Will add paths into Smarty for "smarter" inheritance and inclusion
        $this->_add_paths();
    }
    /**
     * String Parse
     *
     * Parses a string using Smarty 3
     *
     * @param string  $template   Template Name
     * @param array   $data       Data Array
     * @param boolean $return     Indicates if it will be returned or not
     * @param boolean $is_include Indicates if included or not
     *
     * @return void
     */
    public function string_parse($template, $data = array(), $return = false, $is_include = false)
    {
        return $this->CI->smarty->fetch('string:'.$template, $data);
    }
    /**
     * Parse String
     *
     * Parses a string using Smarty 3. Never understood why there
     * was two identical functions in Codeigniter that did the same.
     *
     * @param string  $template   Template Name
     * @param array   $data       Data Array
     * @param boolean $return     Indicates if it will be returned or not
     * @param boolean $is_include Indicates if included or not
     *
     * @return void
     */
    public function parse_string($template, $data = array(), $return = false, $is_include = false)
    {
        return $this->string_parse($template, $data, $return, $is_include);
    }
}