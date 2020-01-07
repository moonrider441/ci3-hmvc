<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Professional Development Management Core
 *
 * Load Modules Config Autoload library for Code Igniter.
 *
 * @author		Afshin Mansourzadeh
 * @version		1.0
 *
 */
 
class Pdm
{
    public $configs_data = [];//array();
	public $module;// 	= array();
	public $modules_data = [];// 	= array();
	public $controler;
	public $function;
	private $asset_bank	= array();
	private $my_config	= array();
	private $pci;
	private $data;


	function __construct()
    {
        global $RTR, $IN, $CFG, $cnf;
        $config =& $CFG->config;
        $this->pci =& get_instance();

        $this->module = $RTR->fetch_module();
        $this->controler = $this->pci->router->fetch_class();
        $this->function = $this->pci->router->fetch_method();
        $this->segments = $this->pci->uri->segments;

        if (!defined('_LOCAL_'))
            define('_LOCAL_', !!($_SERVER['SERVER_NAME'] == 'localhost'));
        if (!defined('_MODULE_'))
            define('_MODULE_', $this->module);
        if (!defined('_CONTROLER_'))
            define('_CONTROLER_', $this->controler);
        if (!defined('_FUNCTIONS_'))
            define('_FUNCTIONS_', $this->function);
        if (!defined('_SEGMENTS_'))
            define('_SEGMENTS_', urldecode(implode('/', $this->segments)));


        if($this->function==='asset')
        {
            $this->asset_show();
        }
        else {
            // load default language
            $this->pci->lang->load('default');
        }

        $this->_init();

        // If AJAX Request
        if ($IN->is_ajax_request()) {
            $this->_ajax_route();
            return false;
        }

        // Redirect handler
        if ($this->module == 'admin' && $this->module == 'redirect') {
            $url = get_value('url', '');
            if ($url != '')
                redirect(base_url($url));
        }

        if (!defined('_TOKEN_KEY_'))
            define('_TOKEN_KEY_', $this->module . '@' . $this->controler . '@' . $this->function);

        return true;
    }

	/* Private function */

	function _init()
	{
        $this->pci->config->load($this->module.'/config');
	}

    /**
     * @return bool
     */
    function _ajax_route()
	{
        $module = get_value('ajax_type', '');
        if($module){
            $page = get_value('p', 'index');
            if(isset($this->modules_data[$module])) {
                $this->get_path($page, $module, 'ajax/');
                return true;
            }
            exit('No direct script access allowed');
        }
        return false;
	}

    /**
     * @return bool
     */
    function asset_show()
	{

		if(sizeof($this->segments)>1) {

			switch ($this->segments[0]) {
				case 'jc':
					//remove jc from segments
					array_shift($this->segments);
					//remove modules from segments
					$module = array_shift($this->segments);
					$params = explode('_pd_',$this->segments[0]);
					$this->get_path('asset', $this->module);
					$this->pci->load->library('assets',array('module'=>$module,'asset_bank'=>$this->asset_bank));
					return $this->pci->assets->exec($params[0],$params[1]);
					break;
				default:
					//remove module from segment
					$address_state = array_shift($this->segments);
					switch($address_state){
						case 'home':
							$path = FCPATH .'assets/';
							break;
						case 'upload':
							$path = FCPATH.'uploads/' ;
							break;
						default:
							$path = APPPATH .'modules/'.$address_state.'/assets/';
							break;
					}
					$file = $path.implode($this->segments, '/');


				if(is_dir($path) && file_exists($file)){

					$this->pci->load->helper('file');
					$filename = basename($file);
					$ext = pathinfo($filename, PATHINFO_EXTENSION);
					$type = get_mime_by_extension($filename);

					if ($type) {
						header('Content-type: ' . $type);
						header('Access-Control-Allow-Origin: *');
						header("Access-Control-Allow-Methods: GET, OPTIONS");
						header('Length: ' . filesize($file));
						readfile($file);
					} else
						header('HTTP/1.0 404 Not Found');
					exit();
				}
					break;
			}
		}
		return false;

	}

    /**
     * @param $view
     * @param null $data
     * @param bool $returnhtml
     * @return bool|string
     */
    function render_page($view, $data=null, $returnhtml=false)
	{
        global $CFG;
        $config =& $CFG->config;
		$view_html = '';
		$data = (empty($data)) ? (isset($this->pci->data)?$this->pci->data:[]): $data;

		$data['list_mode'] = isset($this->segments[1]) ? ($this->segments[1]==='list' ? 1 : 0) : 0;
		$data['currnet_module'] = $this->module;
		$data['currnet_controller'] = $this->controler;
		$data['currnet_function'] = $this->function;

        if(isset($this->pci->lang))
            $data['lang']   = $this->pci->lang->language;

        $data['lang_dir']   = $config['language_dir'];
        $data['lang_abbr']   = $config['language_abbr'];

        $has_header = isset($data['no_header']) ? 0 : 1;
        $has_footer = isset($data['no_footer']) ? 0 : 1;

		$view_name = $this->controler.'_'.$this->function;

		if($has_header && $call_header = $this->get_config_includes($view_name))
			$view_html .= $this->pci->parser->parse($call_header,$data, $returnhtml);
		$view_html .= $this->pci->parser->parse($view,$data, $returnhtml);
		if($has_footer && $call_footer = $this->get_config_includes($view_name,'footer'))
			$view_html .= $this->pci->parser->parse($call_footer,$data, $returnhtml);

		if ($returnhtml) return $view_html;//This will return html on 3rd argument being true
		return FALSE;
	}

    /**
     * @param $file_name
     * @param null $module
     * @param string $base
     */
    function get_path($file_name, $module=NULL, $base='config/')
	{
		$module = $module ? $module : $this->module;
		$location = APPPATH.'modules/';
		if (is_dir($config_source = $location.$module.'/'.$base)){

			if (list($path, $file) = Modules::find($file_name, $module, $base))
			{
				require_once ($path.$file.'.php');
			}
		}
	}

    /**
     * @param $view_name
     * @param string $state
     * @return bool
     */
    function get_config_includes($view_name, $state ='header')
	{
        global $CFG;
        $config =& $CFG->config;
        if ( stripos($view_name, '.')) { $view_name = basename($view_name,'.tpl'); }
		$view_config = $view_name . '_' . $state;

		if(!isset($config[$view_config]))
			$view_config = 'def_' . $state;

		return ((isset($config[$view_config]) && $config[$view_config]!='nope') ? $config[$view_config] : FALSE);
	}

    /**
     * @param $view_name
     * @return string
     */
    function get_config_asset($view_name)
	{
        global $CFG;
        $config =& $CFG->config;
        if ( stripos($view_name, '.')) { $view_name = basename($view_name,'.tpl'); }
		$asset_config = $view_name . '_asset';
		if(!$config[$asset_config])
			$asset_config = 'def_asset';
		return (($config[$asset_config]) ? $config[$asset_config] : 'default');
	}


}