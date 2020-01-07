<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Assets
{
	/**
	 *
	 * GzipIt 1.2
	 *
	 * Single file solution for CSS and JavaScript combination,
	 * minimization, gzipping and caching.
	 *
	 * For documentation, requirements, updates and support please visit:
	 * http://code.google.com/p/gzipit/
	 *
	 * Inspired by CSS and Javascript Combinator by Niels Leenheer
	 * (http://rakaz.nl/code/combine)
	 *
	 * See copyright and licences below for bundled components.
	 *
	 * --
	 * Copyright (c) 2010-2012 Artem Volk (www.artvolk.sumy.ua)
	 *
	 * Permission is hereby granted, free of charge, to any person obtaining a copy of
	 * this software and associated documentation files (the "Software"), to deal in
	 * the Software without restriction, including without limitation the rights to
	 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
	 * of the Software, and to permit persons to whom the Software is furnished to do
	 * so, subject to the following conditions:
	 *
	 * The above copyright notice and this permission notice shall be included in all
	 * copies or substantial portions of the Software.
	 *
	 *
	 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	 * SOFTWARE.
	 * --
	 *
	 * @package gzipit
	 * @author Artem Volk <artvolk@gmail.com>
	 * @license http://opensource.org/licenses/mit-license.php MIT License
	 * @version 1.0 ($Id: gzipit.php 24 2012-03-30 19:55:45Z artvolk $)
	 * @link http://code.google.com/p/gzipit/
	 */


	/**
	 * Configuration section
	 * *****************************************************************************************************************
	 */
// Use gzip compression
	private $define;
    private $var;
	private $base_url;
	private $app_url;
	private $module_url;
	private $module_name;

    private $type;
    private $files = NULL;
    private $asset = NULL;


	function __construct($params)
	{
        if(!is_array($params) || !isset($params['asset_bank'])){pre('wrong request !',TRUE);}
		$CI =& get_instance();
		$this->base_url = getcwd().'/';
		$this->app_url = APPPATH;
		$this->module_name = (isset($params['module'])?$params['module']:array_shift($CI->uri->segments));
		$this->module_url = $this->app_url.'modules/'.$params['module'].'/';
        $CI->parser->setModule($this->module_name);
        $this->define_maker($params['asset_bank']);
	}

    function exec($state='manual', $type='css'){
        $this->type = $type;

        if($state!='manual') {$this->asset = $state;}
        else{$this->files = $this->get_param('files');}
        $this->start_gzip();
        $this->end_gzip();
    }

    /**
     *
     */
    function define_maker($asset_bank){
        $this->define['GZIPIT_COMPRESSION'] = true;
        $this->define['GZIPIT_COMPRESSION_FOR_IE6'] = true;
        $this->define['GZIPIT_GZIP_LEVEL'] = 9;
        $this->define['GZIPIT_DISK_CACHE'] = true;
        $this->define['GZIPIT_CSSMIN'] = true;
        $this->define['GZIPIT_JSMIN'] = true;
        $this->define['GZIPIT_INCLUDE_FILENAME'] = true;
        $this->define['GZIPIT_DIR_CACHE'] = $this->app_url.'cache/assets';
        $this->define['GZIPIT_DIR_CSS'] = $this->module_url.'/assets/css';
        $this->define['GZIPIT_DIR_JS'] = $this->module_url.'/assets/js';
        $this->define['GZIPIT_HEADER_ETAG'] = true;
        $this->define['GZIPIT_HEADER_LAST_MODIFIED'] = true;
        $this->define['GZIPIT_HEADER_CACHE_CONTROL'] = true;
        $this->define['GZIPIT_HEADER_CACHE_CONTROL_VALUE'] = 'max-age=315360000';
        $this->define['GZIPIT_HEADER_EXPIRES'] = true;
        $this->define['GZIPIT_HEADER_EXPIRES_VALUE'] = 'Thu, 31 Dec '.date('Y',strtotime('+15 year')).' 23:55:55 GMT';
//        $this->define['GZIPIT_ASSETS_FILE'] = $this->module_url.'/controllers/asset_bank.php';
        $this->define['GZIPIT_ASSETS'] = $asset_bank ? $asset_bank : array();
        $this->define['GZIPIT_FILELIST_DELIMITER'] = ',';
        $this->define['GZIPIT_ENCODING_NONE'] = 'none';
        $this->define['GZIPIT_ENCODING_GZIP'] = 'gzip';
        $this->var['GZIPIT_ENCODING_TYPES'] = array($this->define['GZIPIT_ENCODING_NONE'],$this->define['GZIPIT_ENCODING_GZIP']);
        $this->define['GZIPIT_TYPE_CSS'] = 'css';
        $this->define['GZIPIT_TYPE_JS'] = 'js';
        $this->var['GZIPIT_TYPES'] = array($this->define['GZIPIT_TYPE_CSS'],$this->define['GZIPIT_TYPE_JS']);
        $this->var['GZIPIT_CONTENT_TYPES'] = array($this->define['GZIPIT_TYPE_CSS']=>'text/css',$this->define['GZIPIT_TYPE_JS']=> 'text/javascript');
        $this->var['GZIPIT_EXTENSIONS'] = array($this->define['GZIPIT_TYPE_CSS']=>'css',$this->define['GZIPIT_TYPE_JS']=>'js');
        $this->var['GZIPIT_PATHES'] = array($this->define['GZIPIT_TYPE_CSS']=>$this->define['GZIPIT_DIR_CSS'],$this->define['GZIPIT_TYPE_JS']=>$this->define['GZIPIT_DIR_JS']);

//        if ($this->define['GZIPIT_ASSETS_FILE'] != NULL && $this->define['GZIPIT_ASSETS_FILE'] != '' && $this->define['GZIPIT_ASSETS_FILE'] !== false)
//        {
//            require_once (realpath($this->define['GZIPIT_ASSETS_FILE']));
//        }
    }

    function start_gzip(){
        ob_start();
//        $this->default = false;
        // Check if asset name specified
        if ($this->asset != NULL && $this->type != NULL) {
//            $typejc = $this->type;//substr($this->asset, 0, 2);
            $page = $this->asset;

//            pre($page);
//            if (isset($this->define['GZIPIT_ASSETS']['default'])) {
//                $this->files = $this->define['GZIPIT_ASSETS']['default'][$this->type]['files'];
//                $this->default = true;
//            }
            //	if (isset($this->define['GZIPIT_ASSETS'][$this->asset]))

//            if(!isset($GZIPIT_ASSETS[$page][$typejc]['default']))
//                $GZIPIT_ASSETS[$page][$typejc]['default'] = 1;
            if (isset($this->define['GZIPIT_ASSETS'][$page])) {
//                $this->files = $this->default ? $this->files + $this->define['GZIPIT_ASSETS'][$page][$this->type]['files'] : $this->define['GZIPIT_ASSETS'][$page][$this->type]['files'];
                $this->files = $this->define['GZIPIT_ASSETS'][$page][$this->type]['files'];
                //                pre($this->define['GZIPIT_ASSETS']);
//                vde(isset($this->define['GZIPIT_ASSETS']['default']));
                if (!isset($this->define['GZIPIT_ASSETS'][$page][$this->type]['default'])
                    || (isset($this->define['GZIPIT_ASSETS'][$page][$this->type]['default'])
                        && $this->define['GZIPIT_ASSETS'][$page][$this->type]['default']
                        && !empty($this->define['GZIPIT_ASSETS']['default'][$this->type]['files']) ))
                    $this->files = $this->define['GZIPIT_ASSETS']['default'][$this->type]['files']+$this->files;
                $this->type = $this->define['GZIPIT_ASSETS'][$page][$this->type]['type'];
        //		$this->files = $this->define['GZIPIT_ASSETS'][$this->asset]['files'];
        //		$this->type = $this->define['GZIPIT_ASSETS'][$this->asset]['type'];
            } else {
                $this->give_404('Incorrect asset name');
                exit;
            }
        }

        // Get files list and type
        if ($this->files != NULL && $this->type != NULL) {
            if (in_array($this->type, $this->var['GZIPIT_TYPES'])) {
                if ($this->asset == NULL) {
        //			$elements = explode($this->define['GZIPIT_FILELIST_DELIMITER'], $this->files);
                    foreach (explode($this->define['GZIPIT_FILELIST_DELIMITER'], $this->files) as $elem) {
                        $elemarr = explode('@pd@', $elem);
                        $elements[array_shift($elemarr)] = array_shift($elemarr);
                    }
                } else {
                    $elements = $this->files;//$this->define['GZIPIT_ASSETS'][$this->asset]['files'];
                }
            } else {
                $this->give_404('Incorrect type specified');
                exit;
            }
        } else {
            if ($this->asset == NULL) {
                $this->give_404('Incorrect files and type parameters');
            } else {
                $this->give_404('Incorrect asset definition');
            }
            exit;
        }
        /**
         * Determine supported compression
         *
         */
        if ($this->define['GZIPIT_COMPRESSION']) {
            $temp = $this->getAcceptedEncoding();

            if ($temp[0] == $this->define['GZIPIT_ENCODING_GZIP']) {
                $encoding = $this->define['GZIPIT_ENCODING_GZIP'];
                $encoding_header = $temp[1];
            } else {
                $encoding = $this->define['GZIPIT_ENCODING_NONE'];
                $encoding_header = NULL;
            }
        } else {
            $encoding = $this->define['GZIPIT_ENCODING_NONE'];
            $encoding_header = NULL;
        }
        $last_modified = 0;
        $elems = array();
        $base_path = realpath($this->var['GZIPIT_PATHES'][$this->type]);
        $ext = $this->var['GZIPIT_EXTENSIONS'][$this->type];
        foreach ($elements as $element => $gziptype) {
            $elems[] = $element;
            $source = explode('@',$gziptype);
            $gziptype = array_shift($source);
            $source = sizeof($source)>0 ? array_shift($source) : 'safe';
            $path = '';
            switch($source){
                case 'safe':$path_url = '';break;
                case 'home':$path_url = $this->base_url.'assets/';break;
                case 'me':$path_url = $this->module_url.'assets/';break;
                default:
                    if(is_dir($this->app_url.'modules/'.$source)){$path_url = $this->app_url.'modules/'.$source.'/assets/';}
                    else{$path_url = $this->module_url;}
                    break;
            }
            $path = realpath($path_url.$element);

            if ($path === false ||
                substr($path, -1 * strlen($ext)) != $ext ||
                realpath(substr($path, 0, strlen($path_url))) != realpath($path_url) ||
                !file_exists($path)
            ) {
                $message = sprintf('File "%s" in "%s" not found', htmlspecialchars($element), $source);
                $this->give_404($message);
                exit;
            }

            $last_modified = max($last_modified, filemtime($path));
        }

        $etag = sprintf('%s-%s', $last_modified, md5(implode($this->define['GZIPIT_FILELIST_DELIMITER'], $elems) . $this->type . (string)($this->define['GZIPIT_CSSMIN'] || $this->define['GZIPIT_JSMIN']) . $encoding_header));
        if ($this->define['GZIPIT_HEADER_ETAG']) {
            header('Etag: "' . $etag . '"');
        }
        /**
         * Let's do it!
         */
// Check Etag
        if ($this->define['GZIPIT_HEADER_ETAG'] && isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
            stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) == '"' . $etag . '"'
        ) {
            header("HTTP/1.0 304 Not Modified");
            ob_end_clean();
            exit;
        } else // No Etag specified
        {
            // Send headers
            header('Content-Type: ' . $this->var['GZIPIT_CONTENT_TYPES'][$this->type]);

            if ($this->define['GZIPIT_HEADER_LAST_MODIFIED']) {
                header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $last_modified) . " GMT");
            }

            if ($this->define['GZIPIT_HEADER_EXPIRES']) {
                header('Expires: ' . $this->define['GZIPIT_HEADER_EXPIRES_VALUE']);
            }

            if ($this->define['GZIPIT_HEADER_CACHE_CONTROL']) {
                header('Cache-Control: ' . $this->define['GZIPIT_HEADER_CACHE_CONTROL_VALUE']);
            }


            $cached_file =
                realpath($this->define['GZIPIT_DIR_CACHE']) .
                DIRECTORY_SEPARATOR .
                sprintf('cache-%s%s.%s%s',
                    $etag,
                    (($this->type == $this->define['GZIPIT_TYPE_CSS'] && $this->define['GZIPIT_CSSMIN']) || ($this->type == $this->define['GZIPIT_TYPE_JS'] && $this->define['GZIPIT_JSMIN'])) ? '-min' : '',
                    $this->var['GZIPIT_EXTENSIONS'][$this->type],
                    ($encoding != $this->define['GZIPIT_ENCODING_NONE']) ? '.' . $encoding : ''
                );


            // If we have cached file, return it to the client
            if ($this->define['GZIPIT_DISK_CACHE'] && file_exists($cached_file)) {
                if ($fp = fopen($cached_file, 'rb')) {
                    if ($encoding_header != NULL) {
                        header('Content-Encoding: ' . $encoding);
//                        header('Accept-Encoding: ' . $encoding);
                    }
                    header('Content-Length: ' . filesize($cached_file));
//                    fread($fp, filesize($cached_file));
                    fpassthru($fp);
                    fclose($fp);
                    ob_end_flush();
                    exit;
                } else {
                    $this->give_404('Error reading cached file');
                    exit;
                }
            }

            // Perform combining, minimization and compression
            $content = '';
            foreach ($elements as $element => $gziptype) {
                $source = explode('@',$gziptype);
                $gziptype = array_shift($source);
                $source = sizeof($source)>0 ? array_shift($source) : 'safe';
                $path_url = '';
                switch($source){
                    case 'safe':$path_url = '';break;
                    case 'home':$path_url = $this->base_url.'assets/';$path_add = base_url('assets'); break;
                    case 'me':$path_url = $this->module_url.'assets/';$path_add = base_url('application/modules/'.$this->module_name.'/assets');break;
                    default:
                        if(is_dir($this->app_url.'modules/'.$source)){
                            $path_url = $this->app_url.'modules/'.$source.'/assets/';
                            $path_add = base_url('application/modules/'.$source.'/assets/');
                        }
                        else{$path_url = $this->module_url;$path_add = base_url('assets');}
                        break;
                }

                $path = realpath($path_url.$element);
//                $path = $gziptype != 'safe' ? realpath($base_path . DIRECTORY_SEPARATOR . $element) : $element;

                $temp = file_get_contents(realpath($path));

                $content .= "\n\n";

                if ($this->define['GZIPIT_INCLUDE_FILENAME']) {
                    $content .= sprintf("/* %s */\n", $element);
                }
                $temp = str_replace('@pd@', base_url('asset'), $temp);

                if ($this->type == $this->define['GZIPIT_TYPE_CSS'] && $this->define['GZIPIT_CSSMIN']) {
//                    $temp = str_replace('@pd@/css', $path_url . '/css/cs', $temp);
//                    if($gziptype!='safe')
                        $temp = CompactText($temp);
                }

                if ($this->type == $this->define['GZIPIT_TYPE_JS'] && $this->define['GZIPIT_JSMIN'] && $gziptype != 'safe') {
                    $temp = JSMin::minify($temp);
                }
//                pr($element);
//                pre($temp);

                $content .= $temp;
            }

            if ($encoding != $this->define['GZIPIT_ENCODING_NONE']) {
                $content = gzencode($content, $this->define['GZIPIT_GZIP_LEVEL'], FORCE_GZIP);
                header('Content-Encoding: ' . $encoding_header);
            }
            header('Content-Length: ' . strlen($content));
            echo $content;

            if ($this->define['GZIPIT_DISK_CACHE']) {
                if ($fp = fopen($cached_file, 'wb')) {
//                    if ($encoding_header != NULL) {
//                        header('Content-Encoding: ' . $encoding);
//                    }
//                    header('Content-Length: ' . filesize($cached_file));
                    fwrite($fp, $content);
                    fclose($fp);
                }
            }

        } //else (no Etag)

    }

    function end_gzip(){

        /**
         * The End
         */
        ob_end_flush();
        exit;
    }

    ///////////////////////////////////// ta injaaaaaaaaaaaaaaaaaaaa

/**
 * Find last date and time of last modification of files
 */

/**
 * Construct and send ETag if enabled
 */


/**
 * Utility functions
 */

/**
 * Renders 404 error to client
 *
 * @param string $message Detailed error message
 * @return void
 */
function give_404($message)
{
	printf('
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html>
	<head>
		<title>404 Not Found</title>
	</head>
	<body>
		<h1>Not Found</h1>
		<p>%s</p>
	</body>
</html>
', $message);
	header("HTTP/1.0 404 Not Found");
	ob_end_flush();
}

/**
 * Parses HTTP GET params
 *
 * @param string $param Parameter name
 * @param bool $trim Convert parameter value to lowercase and trim it
 * @return string|NULL Returns NULL if parameter doesn't exist
 */
function get_param($param, $trim = false)
{
	return isset($_GET[$param]) ? ($trim ? strtolower(trim($_GET[$param])) : $_GET[$param]) : NULL;
}

/**
 * Returns client's accepted encoding
 * Code taken from Minify (http://code.google.com/p/minify/)
 *
 * @return void bool If client supports gzip
 */
function getAcceptedEncoding()
{
	// @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html

	if (!isset($_SERVER['HTTP_ACCEPT_ENCODING'])
		|| $this->isBuggyIe()
	) {
		return array('', '');
	}
	$ae = $_SERVER['HTTP_ACCEPT_ENCODING'];
	// gzip checks (quick)
	if (0 === strpos($ae, 'gzip,')             // most browsers
		|| 0 === strpos($ae, 'deflate, gzip,') // opera
	) {
		return array('gzip', 'gzip');
	}
	// gzip checks (slow)
	if (preg_match(
		'@(?:^|,)\\s*((?:x-)?gzip)\\s*(?:$|,|;\\s*q=(?:0\\.|1))@'
		, $ae
		, $m)) {
		return array('gzip', $m[1]);
	}
}

/**
 * Detect IE with buggy compression support (version earlier than 6 SP2)
 * Code taken from Minify (http://code.google.com/p/minify/)
 *
 * @link http://code.google.com/p/minify/
 * @return bool If client uses IE with buggy gzip support
 */
function isBuggyIe()
{
	$ua = $_SERVER['HTTP_USER_AGENT'];
	// quick escape for non-IEs
	if (0 !== strpos($ua, 'Mozilla/4.0 (compatible; MSIE ')
		|| false !== strpos($ua, 'Opera')
	) {
		return false;
	}
	// no regex = faaast
	$version = (float)substr($ua, 30);
	return $this->define['GZIPIT1_COMPRESSION_FOR_IE6']
		? ($version < 6 || ($version == 6 && false === strpos($ua, 'SV1')))
		: ($version < 7);
}

}
/**
 * JSmin
 * http://github.com/rgrove/jsmin-php/
 * *****************************************************************************************************************
 */
/**
 * jsmin.php - PHP implementation of Douglas Crockford's JSMin.
 *
 * This is pretty much a direct port of jsmin.c to PHP with just a few
 * PHP-specific performance tweaks. Also, whereas jsmin.c reads from stdin and
 * outputs to stdout, this library accepts a string as input and returns another
 * string as output.
 *
 * PHP 5 or higher is required.
 *
 * Permission is hereby granted to use this version of the library under the
 * same terms as jsmin.c, which has the following license:
 *
 * --
 * Copyright (c) 2002 Douglas Crockford  (www.crockford.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * The Software shall be used for Good, not Evil.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * --
 *
 * @package JSMin
 * @author Ryan Grove <ryan@wonko.com>
 * @copyright 2002 Douglas Crockford <douglas@crockford.com> (jsmin.c)
 * @copyright 2008 Ryan Grove <ryan@wonko.com> (PHP port)
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @version 1.1.1 (2008-03-02)
 * @link http://code.google.com/p/jsmin-php/
 */

class JSMin {
  const ORD_LF    = 10;
  const ORD_SPACE = 32;

  protected $a           = '';
  protected $b           = '';
  protected $input       = '';
  protected $inputIndex  = 0;
  protected $inputLength = 0;
  protected $lookAhead   = null;
  protected $output      = '';

  // -- Public Static Methods --------------------------------------------------

  public static function minify($js) {
	$jsmin = new JSMin($js);
	return $jsmin->min();
  }

  // -- Public Instance Methods ------------------------------------------------

  public function __construct($input) {
	$this->input       = str_replace("\r\n", "\n", $input);
	$this->inputLength = strlen($this->input);
  }

  // -- Protected Instance Methods ---------------------------------------------



  /* action -- do something! What you do is determined by the argument:
		  1   Output A. Copy B to A. Get the next B.
		  2   Copy B to A. Get the next B. (Delete A).
		  3   Get the next B. (Delete B).
	 action treats a string as a single character. Wow!
	 action recognizes a regular expression if it is preceded by ( or , or =.
  */
  protected function action($d) {
	switch($d) {
	  case 1:
		$this->output .= $this->a;

	  case 2:
		$this->a = $this->b;

		if ($this->a === "'" || $this->a === '"') {
		  for (;;) {
			$this->output .= $this->a;
			$this->a       = $this->get();

			if ($this->a === $this->b) {
			  break;
			}

			if (ord($this->a) <= self::ORD_LF) {
			  throw new JSMinException('Unterminated string literal.');
			}

			if ($this->a === '\\') {
			  $this->output .= $this->a;
			  $this->a       = $this->get();
			}
		  }
		}

	  case 3:
		$this->b = $this->next();

		if ($this->b === '/' && (
			$this->a === '(' || $this->a === ',' || $this->a === '=' ||
			$this->a === ':' || $this->a === '[' || $this->a === '!' ||
			$this->a === '&' || $this->a === '|' || $this->a === '?' ||
			$this->a === '{' || $this->a === '}' || $this->a === ';' ||
			$this->a === "\n" )) {

		  $this->output .= $this->a . $this->b;

		  for (;;) {
			$this->a = $this->get();

			if ($this->a === '[') {
			  /*
				inside a regex [...] set, which MAY contain a '/' itself. Example: mootools Form.Validator near line 460:
				  return Form.Validator.getValidator('IsEmpty').test(element) || (/^(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]\.?){0,63}[a-z0-9!#$%&'*+/=?^_`{|}~-]@(?:(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\])$/i).test(element.get('value'));
			  */
			  for (;;) {
				$this->output .= $this->a;
				$this->a = $this->get();

				if ($this->a === ']') {
					break;
				} elseif ($this->a === '\\') {
				  $this->output .= $this->a;
				  $this->a       = $this->get();
				} elseif (ord($this->a) <= self::ORD_LF) {
				  throw new JSMinException('Unterminated regular expression set in regex literal.');
				}
			  }
			} elseif ($this->a === '/') {
			  break;
			} elseif ($this->a === '\\') {
			  $this->output .= $this->a;
			  $this->a       = $this->get();
			} elseif (ord($this->a) <= self::ORD_LF) {
			  throw new JSMinException('Unterminated regular expression literal.');
			}

			$this->output .= $this->a;
		  }

		  $this->b = $this->next();
		}
	}
  }

  protected function get() {
	$c = $this->lookAhead;
	$this->lookAhead = null;

	if ($c === null) {
	  if ($this->inputIndex < $this->inputLength) {
		$c = substr($this->input, $this->inputIndex, 1);
		$this->inputIndex += 1;
	  } else {
		$c = null;
	  }
	}

	if ($c === "\r") {
	  return "\n";
	}

	if ($c === null || $c === "\n" || ord($c) >= self::ORD_SPACE) {
	  return $c;
	}

	return ' ';
  }

  /* isAlphanum -- return true if the character is a letter, digit, underscore,
		dollar sign, or non-ASCII character.
  */
  protected function isAlphaNum($c) {
	return ord($c) > 126 || $c === '\\' || preg_match('/^[\w\$]$/', $c) === 1;
  }

  protected function min() {
	$this->a = "\n";
	$this->action(3);

	while ($this->a !== null) {
	  switch ($this->a) {
		case ' ':
		  if ($this->isAlphaNum($this->b)) {
			$this->action(1);
		  } else {
			$this->action(2);
		  }
		  break;

		case "\n":
		  switch ($this->b) {
			case '{':
			case '[':
			case '(':
			case '+':
			case '-':
			  $this->action(1);
			  break;

			case ' ':
			  $this->action(3);
			  break;

			default:
			  if ($this->isAlphaNum($this->b)) {
				$this->action(1);
			  }
			  else {
				$this->action(2);
			  }
		  }
		  break;

		default:
		  switch ($this->b) {
			case ' ':
			  if ($this->isAlphaNum($this->a)) {
				$this->action(1);
				break;
			  }

			  $this->action(3);
			  break;

			case "\n":
			  switch ($this->a) {
				case '}':
				case ']':
				case ')':
				case '+':
				case '-':
				case '"':
				case "'":
				  $this->action(1);
				  break;

				default:
				  if ($this->isAlphaNum($this->a)) {
					$this->action(1);
				  }
				  else {
					$this->action(3);
				  }
			  }
			  break;

			default:
			  $this->action(1);
			  break;
		  }
	  }
	}

	return $this->output;
  }

  /* next -- get the next character, excluding comments. peek() is used to see
			 if a '/' is followed by a '/' or '*'.
  */
  protected function next() {
	$c = $this->get();

	if ($c === '/') {
	  switch($this->peek()) {
		case '/':
		  for (;;) {
			$c = $this->get();

			if (ord($c) <= self::ORD_LF) {
			  return $c;
			}
		  }

		case '*':
		  $this->get();

		  for (;;) {
			switch($this->get()) {
			  case '*':
				if ($this->peek() === '/') {
				  $this->get();
				  return ' ';
				}
				break;

			  case null:
				throw new JSMinException('Unterminated comment.');
			}
		  }

		default:
		  return $c;
	  }
	}

	return $c;
  }

  protected function peek() {
	$this->lookAhead = $this->get();
	return $this->lookAhead;
  }
}

// -- Exceptions ---------------------------------------------------------------
class JSMinException extends Exception {}


/**
 * CSSMin
 * http://code.google.com/p/cssmin/
 * *****************************************************************************************************************
 */

?>