<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 if ( ! function_exists('ob_html_compress'))
{
	function ob_html_compress($buf){
		return preg_replace(array('/<!--(.*)-->/Uis',"/[[:blank:]]+/","/\/>[[:blank:]]+</"),array('',' ',' '),str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '),'',$buf));
	}
}

 if ( ! function_exists('showme'))
{
	function showme($object, $kill = true, $msg = '__END__')
	{
		echo '<pre style="text-align: left;">';
		print_r($object);
		echo '</pre><br />';
		if ($kill){if($msg){die($msg);}else{die();}}
		return ($object);
	}
}

 if ( ! function_exists('pr'))
{
	function pr($params){
		showme($params,false);
	}
}

 if ( ! function_exists('pre'))
{
	function pre($params,$no_msg=FALSE){
		showme($params,true,(!$no_msg?'_PRINT_R_':''));
	}
}

 if ( ! function_exists('vd'))
{
	function vd($params){
		showme(var_dump($params),false);
	}
}

 if ( ! function_exists('vde'))
{
	function vde($params,$no_msg=FALSE){
		showme(var_dump($params),true,(!$no_msg?'_VAR_DUMP_':''));
	}
}

 if ( ! function_exists('send_value'))
{
    function send_value($key, $mode=FALSE, $die = true){
        switch ($mode) {
            case 'json':
                $output = json_encode($key);
                break;
            case 'md5enc':
                $output = encrypt_it($key);
                break;
            case 'enc':
                $output = encrypt($key);
                break;
            default:
                $output = $key;
                break;
        }
        echo $output;
        if($die)
            die();exit();
    }	
}

if ( ! function_exists('get_value')) {

    /** Get a value from $_POST / $_GET */
    function get_value($key, $defaultValue = false, $mode='normal', $enc_key=null)
    {
        switch($mode){
            default:
            case 'normal':
                // do some thing
                break;
            case 'md5':
                $key = encrypt_it($key,$enc_key);
                break;
            case 'md5-enc':
                $key = encrypt(encrypt_it($key,$enc_key));
                break;
            case 'enc':
                $key = encrypt($key);
                break;
            case 'enc-md5':
                $key = get_input_name($key, $enc_key);
                break;
            case 'enc-arraycomp':
                $key = get_input_name($key, $enc_key);
                break;
        }
        if (!isset($key) OR empty($key) OR !is_string($key))
        {
            return $defaultValue;
        }

        $ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $defaultValue));

        if (is_string($ret) === true)
        {
            $ret = urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret)));
        }

        $result = !is_string($ret) ? $ret : trim(stripslashes($ret), ' ');
        switch ($mode){
            case 'normal-md5':
            case 'enc-md5':
                $final_result = strlen($result)>1 ? decrypt_it($result, $enc_key) : null;
                break;
            case 'normal-comp':
            case 'enc-comp':
                $final_result = strlen($result)>1 ? decompress_text($result) : null;
                break;
            case 'norm-arraycomp':
            case 'enc-arraycomp':
                $final_result = strlen($result)>1 ? array_decompress($result) : null;
//                $final_result = array_decompress($result);
                break;
            case 'norm-json':
            case 'normal-json':
                $final_result = json_decode($result, true);
                break;
            default:
                $final_result = $result;
                break;
        }
        return $final_result;
//         switch($mode){
//             default:
//             case 'normal':
//                 // do some thing
//                 break;
//             case 'md5':
//             case 'md5-enc':
//                 $key = get_input_name($key,$enc_key, $mode);
//                 break;
//             case 'enc':
//                 $key = get_input_name($key, $enc_key);
//                 break;
//             case 'enc-md5':
//                 $key = get_input_name($key, $enc_key);
//                 break;
//             case 'enc-arraycomp':
//                 $key = get_input_name($key, $enc_key);
//                 break;
//         }
//		 if (!isset($key) OR empty($key) OR !is_string($key))
//		 {
//			 return $defaultValue;
//		 }
//
////		 switch($type_get){
////			 case 'enc':$newkey = encrypt($key);break;
////			 case 'md5':$newkey = encrypt_it($key,$key_encrypt);break;
////			 default:$newkey = $key;break;
////		 }
////		 $key = $newkey;
//		 $ret = (isset($_REQUEST[$key]) ? $_REQUEST[$key] : (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $defaultValue)));
//
//		 if (is_string($ret) === true)
//		 {
//			 $ret = urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret)));
//		 }
//
//         $result = !is_string($ret) ? $ret : trim(stripslashes($ret), ' ');
//         switch ($mode){
//             case 'normal-md5':
//             case 'enc-md5':
//                 $final_result = decrypt_it($result, $enc_key);
//                 break;
//             case 'norm-arraycomp':
//             case 'enc-arraycomp':
//                 $final_result = array_decompress($result);
//                 break;
//             case 'norm-json':
//             case 'normal-json':
//                 $final_result = json_decode($result);
//                 break;
//             default:
//                 $final_result = $result;
//                 break;
//         }
//         return $final_result;
//		 return !is_string($ret) ? $ret : stripslashes($ret);
    }
}

 if ( ! function_exists('get_file_extention')) {

	 function get_file_extention($filename = NULL)
	 {
		 $filearr = explode('?', $filename);
		 $filename = array_shift($filearr);
		 $extentionArray = explode('.', $filename);
		 if (empty($filename) || !is_array($extentionArray))
			 return false;
//        $ext = array_pop($extentionArray);
//        $pos = strpos($ext,'?');
//        return trim(strtolower(($pos?substr($ext,0,strpos($ext,'?')):$ext)));
		 return trim(strtolower(array_pop($extentionArray)));
	 }
 }

 if ( ! function_exists('clean_input')) {

     /**
      * @param $input
      * @return mixed
      */
     function clean_input($input) {

         $search = array(
             '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
             '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
             '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
             '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
         );

         $output = preg_replace($search, '', $input);
         return $output;
     }

 }

 if ( ! function_exists('sanitize')) {

	 function sanitize($input) {
		 $output = '';
		 if (is_array($input)) {
			 foreach($input as $var=>$val) {
				 $output[$var] = sanitize($val);
			 }
		 }
		 else {
			 if (get_magic_quotes_gpc()) {
				 $input = stripslashes($input);
			 }
			 $input  = clean_input($input);
			 $output = mysql_real_escape_string($input);
		 }
		 return $output;
	 }
 }

 if ( ! function_exists('escape_str')) {
	 /**
	  * Escape String
	  *
	  * @access public
	  * @param string
	  * @param bool whether or not the string will be used in a LIKE condition
	  * @return string
	  */
	 function escape_str($str, $like = FALSE)
	 {
		 if (is_array($str))
		 {
			 foreach ($str as $key => $val)
			 {
				 $str[$key] = $this->escape_str($val, $like);
			 }

			 return $str;
		 }

		 if (function_exists('mysql_real_escape_string') AND is_resource($this->conn_id))
		 {
			 $str = mysql_real_escape_string($str, $this->conn_id);
		 }
		 elseif (function_exists('mysql_escape_string'))
		 {
			 $str = mysql_escape_string($str);
		 }
		 else
		 {
			 $str = addslashes($str);
		 }

		 // escape LIKE condition wildcards
		 if ($like === TRUE)
		 {
			 $str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
		 }

		 return $str;
	 }
 }

if (!function_exists('encrypt')) {

    /** Encrypt password */
    function encrypt($passwd)
    {
        return sha1(md5(md5(quote_text($passwd))));
    }
}

 if ( ! function_exists('get_input_name')) {

     /*
      * Get Input Name
      */
     function get_input_name($value, $ident='input_name_', $state='enc') {
         $value = trim($value);
         if(!$value)
             return false;

         switch($state){
             default:
             case 'enc':
                 $name = encrypt($value);
                 break;
             case 'md5':
                 $name = encrypt_it($value,$ident);
                 break;
             case 'md5-enc':
                 $name = encrypt(encrypt_it($value,$ident));
                 break;
             case 'normal':
                 $name = $ident.$value;
                 break;
         }
         return $name;
     }
 }

 if ( ! function_exists('base_url_parse')) {

     /**
      * @param $string
      * @param string $glue
      * @param string $type
      * @return mixed
      */
     function base_url_parse($string, $glue='@pd@', $type='remove') {
         if($string==='' || $string===null)
             return $string;
         $search = base_url();
         $replace = $glue;
         if($type!=='remove'){
             $search = $glue;
             $replace = base_url();
         }
         $string = url_parse($string, $search, $replace);
         return $string;
     }
 }

 if ( ! function_exists('url_parse')) {

     /**
      * @param $string
      * @param string $search
      * @param string $replace
      * @return mixed
      */
     function url_parse($string, $search='@pd@', $replace='') {
         if($string==='' || $string===null)
             return $string;
         $string = str_replace($search, $replace , $string);
         return $string;
     }
 }

 if ( ! function_exists('base_url_remove')) {

     /**
      * @param $string
      * @param string $replace
      * @return mixed
      */
     function base_url_remove($string, $replace='@pd@') {
         if($string==='' || $string===null)
             return $string;
         $string = str_replace(base_url(), $replace , $string);
         return $string;
     }
 }

if ( ! function_exists('get_client_ip')) {

    function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}

if ( ! function_exists('get_client_user_agent')) {

    function get_client_user_agent($state='full', $custom_agent=false) {
        $agent = $custom_agent===false ? $_SERVER['HTTP_USER_AGENT'] : $custom_agent;
        // Detect Device/Operating System
        if(preg_match('/Linux/i',$agent)) $os = 'Linux';
        elseif(preg_match('/Mac/i',$agent)) $os = 'Mac';
        elseif(preg_match('/iPhone/i',$agent)) $os = 'iPhone';
        elseif(preg_match('/iPad/i',$agent)) $os = 'iPad';
        elseif(preg_match('/Droid/i',$agent)) $os = 'Droid';
        elseif(preg_match('/Unix/i',$agent)) $os = 'Unix';
        elseif(preg_match('/Windows/i',$agent)) $os = 'Windows';
        else $os = 'Unknown';
        // Browser Detection
        if(preg_match('/Firefox/i',$agent)) $br = 'Firefox';
        elseif(preg_match('/Mac/i',$agent)) $br = 'Mac';
        elseif(preg_match('/Chrome/i',$agent)) $br = 'Chrome';
        elseif(preg_match('/Opera/i',$agent)) $br = 'Opera';
        elseif(preg_match('/MSIE/i',$agent)) $br = 'IE';
        else $br = 'Unknown';

        switch ($state){
            default:case 'full':$content = $agent;break;
            case 'os':$content = $os;break;
            case 'br':$content = $br;break;
            case 'array':$content = array('br'=>$br,'os'=>$os,'full'=>$agent);break;
        }
        return $content;
    }
}

if ( ! function_exists('get_client_user_ip_agent_identity')) {

    function get_client_user_info($state=null) {
        $user_ip = get_client_ip();
        $browser = get_client_user_agent();
        $user_cart_ident = compress_text($user_ip.$browser);
        switch ($state){
            default:
            case 'full':
                $result = array('ip'=>$user_ip, 'browser'=>$browser, 'ident'=>$user_cart_ident);
                break;
            case 'ip':
                $result = $user_ip;
                break;
            case 'browser':
                $result = $browser;
                break;
        }
        return $result;
    }
}

if ( ! function_exists('call_user_function')) {

	function call_user_function($code, $params, $error='', $function_params=null)
	{
//	    if($code=='get_user_for_list')
//	    pre($params);
	    if(isset($function_params['model'])) {
            $ci = &get_instance();
            $mdl_short = encrypt($function_params['model']);
            $ci->load->model($function_params['model'],$mdl_short);
            $ci->load->library('form_validation');
            return $ci->{$mdl_short}->{$code}($params);
        }

		if (!is_callable($code) || !is_array($params)){
	        if($error!=''){
	            if(is_array($error))
	                send_value($error, (isset($error['mode']) ? $error['mode'] : false));
	            else
	                die($error);
            }
            else
                die('Bad params!');
//			die(($error!=''?$error:'Bad Params!'));
        }

//		return call_user_func_array($code, $params);
		return call_user_func($code, $params);
	}
}

/**
 * Returns a random valid public IP address. For the definition of a
 * AfMa
 * @return string The IP address
 */
 if ( ! function_exists('random_valid_public_ip')) {

	 function random_valid_public_ip() {
		 // Generate a random IP
		 $ip =
			 mt_rand(0, 255) . '.' .
			 mt_rand(0, 255) . '.' .
			 mt_rand(0, 255) . '.' .
			 mt_rand(0, 255);

		 // Return the IP if it is a valid IP, generate another IP if not
		 if (
			 !ip_in_range($ip, '10.0.0.0', '10.255.255.255') &&
			 !ip_in_range($ip, '172.16.0.0', '172.31.255.255') &&
			 !ip_in_range($ip, '192.168.0.0', '192.168.255.255')
		 ) {
			 return $ip;
		 } else {
			 return random_valid_public_ip();
		 }
	 }

 }

/**
 * Returns true if the IP address supplied is within the range from
 * AfMa
 * $start to $end inclusive
 * @param string $ip The IP address to be checked
 * @param string $start The start IP address
 * @param string $end The end IP address
 * @return boolean
 */
if ( ! function_exists('ip_in_range')) {

    function ip_in_range($ip, $start, $end) {
		 // Split the IP addresses into their component octets
		 $i = explode('.', $ip);
		 $s = explode('.', $start);
		 $e = explode('.', $end);

		 // Return false if the IP is in the restricted range
		 return in_array($i[0], range($s[0], $e[0])) &&
		 in_array($i[1], range($s[1], $e[1])) &&
		 in_array($i[2], range($s[2], $e[2])) &&
		 in_array($i[3], range($s[3], $e[3]));
	 }

 }

if ( ! function_exists('encrypt_it')) {

	 function encrypt_it( $q, $key=FALSE ) {
		 $cryptKey  = $key ? $key : 'qJB0rGtIn5UB1xG03efyCp441';
         if(phpversion()>7) {
             // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
             $iv = substr(hash('sha256', $cryptKey), 0, 16);
             $encrypt_method = "AES-256-CBC";
             $qEncoded = openssl_encrypt($q, $encrypt_method, $cryptKey, 0, $iv);
             $qEncoded = base64_encode($qEncoded);
         }
         else
		    $qEncoded = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
		 return( $qEncoded );
	 }
 }

if ( ! function_exists('decrypt_it')) {

    function decrypt_it( $q, $key=FALSE ) {
        $cryptKey  = $key ? $key : 'qJB0rGtIn5UB1xG03efyCp441';
        if(phpversion()>7) {
            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $cryptKey), 0, 16);
            $encrypt_method = "AES-256-CBC";
            $qDecoded = openssl_decrypt(base64_decode($q), $encrypt_method, $cryptKey, 0, $iv);
        }
        else
            $qDecoded = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
        return( $qDecoded );
    }
}
//if ( ! function_exists('encrypt_it7')) {
//
//	 function encrypt_it7( $q, $key=FALSE ) {
//		 $cryptKey  = $key ? $key : 'qJB0rGtIn5UB1xG03efyCp441';
//             // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
//             $iv = substr(hash('sha256', $cryptKey), 0, 16);
//             $encrypt_method = "AES-256-CBC";
//             $qEncoded = openssl_encrypt($q, $encrypt_method, $cryptKey, 0, $iv);
//             $qEncoded = base64_encode($qEncoded);
//		 return( $qEncoded );
//	 }
// }
//
//if ( ! function_exists('decrypt_it7')) {
//
//    function decrypt_it7( $q, $key=FALSE ) {
//        $cryptKey  = $key ? $key : 'qJB0rGtIn5UB1xG03efyCp441';
//            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
//            $iv = substr(hash('sha256', $cryptKey), 0, 16);
//            $encrypt_method = "AES-256-CBC";
//            $qDecoded = openssl_decrypt(base64_decode($q), $encrypt_method, $cryptKey, 0, $iv);
//        return( $qDecoded );
//    }
//}

//if ( ! function_exists('array_encrypt_it')) {
//
//	 function array_encrypt_it( $array , $Object=FALSE ,$first_id=TRUE, $key=FALSE) {
//		 $cryptKey = $key ? $key : 'qJB0rGtIn5UB1xG03efyCp441';
//		 if(is_array($array)) {
//			 foreach ($array as $indx=>$arr){
//				 if($first_id && !$indx)
//				 	$output['id'] = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), 'id', MCRYPT_MODE_CBC, md5(md5($cryptKey))));
//				 $output[$arr] = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), $arr, MCRYPT_MODE_CBC, md5(md5($cryptKey))));
//			 }
//			 return ($Object ? __toObject($output) : $output);
//		 }
//		 return FALSE;
//	 }
// }

if ( ! function_exists('__toObject')) {

	function __toObject(Array $arr) {
		$obj = new stdClass();
		foreach($arr as $key=>$val) {
			if (is_array($val)) {
				$val = __toObject($val);
			}
			$obj->{$key} = $val;
		}

		return $obj;
	}
 }
if ( ! function_exists('is_lib')) {

    /**
     * @param $lib
     * @return bool
     */
    function is_lib($lib)
    {
        $CI =& get_instance();
        return is_object(@$CI->{$lib}) ? TRUE : FALSE;
    }
 }

if ( ! function_exists('replacestr')) {

    function replacestr($string, $needl='-'){
		 if(!$string)
			 return FALSE;
		 $title = str_replace(array('،',',','.','-','/','"','\''), ' ', $string);
		 return str_replace(array(' ','  ','   ',''), $needl, $title);

	 }

 }

 if ( ! function_exists('CompactText')) {

	 function CompactText($buffer){
		 /* remove comments */
		 $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
		 /* remove tabs, spaces, newlines, etc. */
		 $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
		 return $buffer;
	 }

 }

 if ( ! function_exists('shift_array_to_end')) {

	 function shift_array_to_end($array){
		 $first = array_shift($array);
		 array_push($array,$first);
		 return $array;
	 }
 }

 if ( ! function_exists('assets_show'))
{
	function assets_show(){

		$CI =& get_instance();
		//---get working directory and map it to your module
		pr(getcwd());
		pre(array_shift($CI->uri->segments));
		$file = getcwd() . '/application/modules/' . implode('/', $CI->uri->segments);
		//----get path parts form extension
		$path_parts = pathinfo( $file);
		//---set the type for the headers
		$file_type=  strtolower($path_parts['extension']);

		if (is_file($file)) {
			//----write propper headers
			switch ($file_type) {
				case 'css':
					header('Content-type: text/css');
					break;

				case 'js':
					header('Content-type: text/javascript');
					break;

				case 'json':
					header('Content-type: application/json');
					break;

				case 'xml':
					header('Content-type: text/xml');
					break;

				case 'pdf':
					header('Content-type: application/pdf');
					break;

				case 'jpg':
				case 'jpeg':
				case 'png':
				case 'gif':
//				case 'jpg' || 'jpeg' || 'png' || 'gif':
					header('Content-type: image/'.$file_type);
					readfile($file);
					exit;
					break;
			}

			include $file;
		} else {
			show_404();
		}
		exit;
	}
}

 if ( ! function_exists('select_validates'))
{
	function select_validates($param){
		pre($param);

		$CI =& get_instance();
		//---get working directory and map it to your module
		pr(getcwd());
		pre(array_shift($CI->uri->segments));
		$file = getcwd() . '/application/modules/' . implode('/', $CI->uri->segments);
		//----get path parts form extension
		$path_parts = pathinfo( $file);
		//---set the type for the headers
		$file_type=  strtolower($path_parts['extension']);

		if (is_file($file)) {
			//----write propper headers
			switch ($file_type) {
				case 'css':
					header('Content-type: text/css');
					break;

				case 'js':
					header('Content-type: text/javascript');
					break;

				case 'json':
					header('Content-type: application/json');
					break;

				case 'xml':
					header('Content-type: text/xml');
					break;

				case 'pdf':
					header('Content-type: application/pdf');
					break;

				case 'jpg':
				case 'jpeg':
				case 'png':
				case 'gif':
//				case 'jpg' || 'jpeg' || 'png' || 'gif':
					header('Content-type: image/'.$file_type);
					readfile($file);
					exit;
					break;
			}

			include $file;
		} else {
			show_404();
		}
		exit;
	}
}

 if ( ! function_exists('displayError')) {
	 /** Display an error according to an error code */
	 function displayError($string = 'Hack attempt', $htmlentities = true)
	 {
		 global $_ERRORS;
		 //if ($string == 'Hack attempt') d(debug_backtrace());
		 if (!is_array($_ERRORS)) {
			 return str_replace('"', '&quot;', $string);
		 }
		 $key = md5(str_replace('\'', '\\\'', $string));
		 $str = (isset($_ERRORS) AND is_array($_ERRORS) AND array_key_exists($key, $_ERRORS)) ? ($htmlentities ? htmlentities($_ERRORS[$key], ENT_COMPAT, 'UTF-8') : $_ERRORS[$key]) : $string;

		 return str_replace('"', '&quot;', stripslashes($str));
	 }
 }

 if ( ! function_exists('random_string_generate')) {
	 /** Display an error according to an error code */
	 function random_string_generate($len = 6) {
		 $chars = array(
			 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
			 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
			 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
			 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
			 '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '?', '!', '@', '#',
			 '$', '%', '^', '&', '*', '(', ')', '[', ']', '{', '}', '|', '/', '=', '+'//,';',
		 );

		 shuffle($chars);

//		 $num_chars = count($chars) - 1;
		 $num_chars = $len;
		 $token = '';

		 for ($i = 0; $i < $num_chars; $i++){ // <-- $num_chars instead of $len
			 $token .= $chars[mt_rand(0, $num_chars)];
		 }

		 return $token;
	 }
 }

 if ( ! function_exists('set_session')) {
	 /** Display an error according to an error code */
	 function set_session($session_name, $params) {
		 $ci =& get_instance();
        return $ci->session->set_userdata($session_name, $params);
	 }
 }

if ( ! function_exists('persian_num')) {

	/** Display persian numberic */
	function persian_num($str, $mod = 'fa', $mf = '٫')
	{
		$num_a = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.');
		$key_a = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', $mf);

		return ($mod == 'fa') ? str_replace($num_a, $key_a, $str) : str_replace($key_a, $num_a, $str);
	}

}

if ( ! function_exists('persian_chars_exist')) {

    /**
     * @param $str
     * @return bool
     */
    function persian_chars_exist($str)
	{
        $space_codepoints = '\x{0020}\x{2000}-\x{200F}\x{2028}-\x{202F}';
        $arabic_numbers_codepoints ='\x{0660}-\x{0669}';
        $additional_arabic_characters_codepoints ='\x{0629}\x{0643}\x{0649}-\x{064B}\x{064D}\x{06D5}';
        $persian_num_codepoints = '\x{06F0}-\x{06F9}';
        $persian_alpha_codepoints = '\x{0621}-\x{0628}\x{062A}-\x{063A}\x{0641}-\x{0642}\x{0644}-\x{0648}\x{064E}-\x{0651}\x{0655}\x{067E}\x{0686}\x{0698}\x{06A9}\x{06AF}\x{06BE}\x{06CC}';
        $result = preg_match('/^['.$space_codepoints.$arabic_numbers_codepoints.$additional_arabic_characters_codepoints.$persian_alpha_codepoints.$persian_num_codepoints.'\:\-\/]*$/u', $str);
        if ($result !== 1)
            return false;
        return true;
	}



}

 if ( ! function_exists('pdate_to_geo')) {
	 /** Display persian numberic */
	 function pdate_to_geo($str, $mode = 'time', $simbol = 'Y-m-d H:i')
	 {
		 $ci = &get_instance();
		 $ci->load->library('pdate');


		 //YYYY / MM / DD :: dddd
//		 $time = explode('-',substr(str_replace(' / ','-',$str),0,10));
//		 $time = $ci->pdate->mktime(0,0,0,$time[1],$time[2],$time[0]);

//		 //dddd :: DD / MM / YYYY
		 $time = explode('-',substr(str_replace(' / ','-',$str),-10));
		 $time = $ci->pdate->mktime(0,0,0,$time[1],$time[0],$time[2]);
		 switch($mode){
			 default:
			 case 'time':
				 return $time;
				 break;
			 case 'simbol':
				 return date($simbol, $time);
				 break;
		 }
	 }
 }

 if ( ! function_exists('pdate_to_jal')) {
	 /** Display persian numberic */
	 function pdate_to_jal($str=null, $mode='date', $simbol = 'l / j / F :: Y')
	 {
		 $ci = &get_instance();
		 $ci->load->library('pdate');

		 switch($mode){
			 case 'time':
				 $time = $str;
				 break;
			 case 'date':
				 $time = strtotime($str);
				 break;
			 default:
				 $time = time();
				 break;
		 }
		 return $ci->pdate->date($simbol, $time);
	 }
 }

 if ( ! function_exists('date_difference')) {
	 /** Display persian numberic */
	 function date_difference($first_date_time, $second_date_time, $mode='day')
	 {
		 $res = $first_date_time-$second_date_time;
		 switch($mode){
			 default:
			 case 'day':
				 $handl = 24;
				 break;
			 case 'month':
				 $handl = 24*30;
				 break;
			 case 'year':
				 $handl = 24*30*365;
				 break;
		 }
		 return $res/(60*60*$handl);
	 }
 }

if (!function_exists('validation_errors_array')) {

	function validation_errors_array($prefix = '', $suffix = '') {
		if (FALSE === ($OBJ = & _get_validation_object())) {
			return '';
		}

		return $OBJ->error_array($prefix, $suffix);
	}
}

if (!function_exists('get_mac')) {

	/*
	 * Get MAC Address
	 */
	function get_mac(){
		ob_start(); // Turn on output buffering
		system('ipconfig /all'); //Execute external program to display output
		$mycom=ob_get_contents(); // Capture the output into a variable
		ob_clean(); // Clean (erase) the output buffer
		$findme = "Physical";
		$pmac = strpos($mycom, $findme); // Find the position of Physical text
		$mac=substr($mycom,($pmac+36),17); // Get Physical Address
		return $mac;
	}
}

if (!function_exists('parse_number')) {

	/*
	 * Parse Number
	 */
	function parse_number($number){
		return str_replace(array(' ', ','), '', $number);
	}
}

if (!function_exists('compress_text')) {

	/*
     * Compress Text
     */
	function compress_text($input){
		return rtrim(strtr(base64_encode(gzdeflate($input, 9)), '+/', '-_'), '=');
	}
}

if (!function_exists('decompress_text')) {

	/*
     * de Compress Text
     */
	function decompress_text($input){
		return gzinflate(base64_decode(strtr($input, '-_', '+/')));
	}
}

if (!function_exists('sort_array')) {

    /**
     * @param $array
     * @param $needl
     * @param string $state
     * @param string $sort_type
     * @param bool $uniq_needl
     * @return array
     */
    function sort_array($array, $needl, $state = 'key', $sort_type = 'ASC', $uniq_needl = false)
	{
		$sort_array = $array;
		$sortable_array = array();
		if ($needl) {
			foreach ($array as $field) {
				if ($uniq_needl)
					$sortable_array[$field[$needl]] = $field;
				else
					$sortable_array[$field[$needl]][] = $field;
			}
			$sort_array = $sortable_array;
		}
		switch ($state) {
			default:
			case 'key':
				switch ($sort_type) {
					default:
					case 'asc':
					case 'ASC':
						ksort($sort_array);
						break;
					case 'desc':
					case 'DESC':
						krsort($sort_array);
						break;
				}
				break;
			case 'value':
				switch ($sort_type) {
					default:
					case 'ASC':
                    case 'asc':
						asort($sort_array);
						break;
					case 'desc':
					case 'DESC':
						arsort($sort_array);
						break;
				}
				break;

		}
		return $sort_array;
	}
}

if (!function_exists('array_compress')) {

	/*
     * array compress
     */
	function array_compress($array)
	{
		if (!is_array($array) && empty($array)) return false;
		$serialize = serialize($array);
		return compress_text($serialize);
	}
}

if (!function_exists('array_decompress')) {
	/*
     * array decompress
     */
	function array_decompress($string)
	{
		if ($string == '') return false;
		$unserial = decompress_text($string);
		return unserialize($unserial);
	}
}

if (!function_exists('array_to_select2')) {
	/*
     * array decompress
     */
	function array_to_select2($array)
	{
	    if(!is_array($array) || (is_array($array) && empty($array)))
	        return '{}';
        $resault = [];
	    foreach ( $array as $val => $item)
	        $resault[] = ['name'=>$item, 'id'=>$val];
		return __toObject($resault);
	}
}

if (!function_exists('bigintval')) {

    /*
     * bigintval
     */
    function bigintval($value) {
        $value = trim($value);
        if (ctype_digit($value)) {
            return $value;
        }
        $value = preg_replace("/[^0-9](.*)$/", '', $value);
        if (ctype_digit($value)) {
            return $value;
        }
        return 0;
    }
}

if (!function_exists('is_decimal')) {

    /**
     * @param $val
     * @return bool
     */
    function is_decimal($val )
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}

if (!function_exists('br_to_newline')) {

    /*
     * change br to newline
     */
    function br_to_newline($text) {

        $breaks = array("<br />","<br>","<br/>");
        $text = str_ireplace($breaks, "\r\n", $text);
        return $text;
    }
}

if (!function_exists('toggle_box_content')) {

    /*
     * change br to newline
     */
    function toggle_box_content($type='start-content', $params=array()) {

//        $class = isset($params['class']) ? ' '.$params['class'] : ' content-box-toggle';
        $attr = isset($params['attr']) ? ' '.$params['attr'] : '';

        switch ($type){
            default:
            case 'start-content':
                $attr .= isset($params['checked']) ? '': ' style="display:none;"';
                $class = isset($params['start_class']) ? ' '.$params['start_class'] : ' content-box-toggle';
                $result = sprintf('<div class="col-md-12%s"%s>', $class, $attr);
                break;
            case 'end-content':
                $result = '</div>';
                break;
            case 'checkbox-header':
                $class_input = isset($params['checkbox_class']) ? ' '.$params['checkbox_class'] : ' content-box-toggle-action';
                $class = isset($params['color']) ? $params['color'] : '';
                $class_input_box = isset($params['input_color']) ? $params['input_color'] : ' has-error';
                $name = isset($params['name']) ? $params['name'] : 'toggle_name';
                $id = isset($params['id']) ? $params['id'] : $name;
                $label = isset($params['label']) ? $params['label'] : 'toggle_name';
                $label_class = isset($params['label_class']) ? $params['label_class'] : 'col-md-3';
                $input_class = isset($params['input_class']) ? $params['input_class'] : 'col-md-9';
                $desc = isset($params['desc']) ? $params['desc'] : '';
                $checked = isset($params['checked']) ? ' checked="checked"' : '';
                $attr = (isset($params['attr'])?' '.$params['attr']:'').$checked;
                $attr .= ' data-content="'.(isset($params['content'])?$params['content']:'.content-box-toggle').'"';
                $result = sprintf('<div class="form-group form-md-checkboxes%s">
                                            <label class="%s control-label" for="%s">%s</label>
                                            <div class="%s%s">
                                                <div class="md-checkbox-inline">
                                                    <div class="md-checkbox">
                                                        <input type="checkbox" class="md-check%s" name="%s" id="%s"%s value="1" />
                                                        <label for="%s">
                                                            <span></span>
                                                            <span class="check"></span>
                                                            <span class="box"></span> %s</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>', $class, $label_class, $id, $label, $input_class, $class_input_box, $class_input, $name, $id, $attr, $id, $desc);
                break;
        }

        return $result;
    }
}

if (!function_exists('set_token')) {

    /*
     * Set Token
     *
     * sample time:: "now" | "10 September 2000" | "+1 day" | "+1 week" | "+1 week 2 days 4 hours 2 seconds" | "next Thursday" | "last Monday";
     */
    function set_token($type='default', $return='key', $time='+1 day'){
        if(phpversion()>=7)
            $token = bin2hex(random_bytes(16));
        else
            $token = bin2hex(openssl_random_pseudo_bytes(16));
        $uniq = encrypt($token);
        $string_time = strtotime($time);
        $_SESSION['token'][$type]['key'] = $uniq;
        $_SESSION['token'][$type]['time'] = $string_time;

        switch($return){
            case 'key':$return = $uniq;break;
            case 'time':$return = $string_time;break;
            case 'array':$return = $_SESSION['token'][$type];break;
        }
        return $return;
    }
}

if (!function_exists('get_token')) {

    /*
     * Get Token
     */
	function get_token($type='default', $return='key'){
        if(isset($_SESSION['token'][$type]) && $_SESSION['token'][$type]['time'] > time()){
            switch($return){
                case 'key':$return = $_SESSION['token'][$type]['key'];break;
                case 'time':$return = $_SESSION['token'][$type]['time'];break;
                case 'array':$return = $_SESSION['token'][$type];break;
            }
            return $return;
        }
        return set_token($type, $return, '+1 day');
    }
}

if (!function_exists('safe_output')) {


    /** Sanitize a string    */
    function safe_output($string, $html = false)
    {
        if (!$html)
        {
            $string = @htmlentities(strip_tags($string), ENT_QUOTES, 'utf-8');
        }

        return $string;
    }
}

if (!function_exists('nl2br2')) {

    /** Convert \n to <br /> */
    function nl2br2($string)
    {
        return str_replace(array("\r\n", "\r", "\n"), '<br />', $string);
    }
}

if (!function_exists('quote_text')) {

    function quote_text($string, $htmlOK = false)
    {
        if(is_array($string))
            return $string;

        $ci = &get_instance();
        $string = $ci->security->xss_clean($string);

        $string = stripslashes($string);
        if (!is_numeric($string))
        {
            if (!$htmlOK) {
                $string = strip_tags(nl2br2($string));
            }
        }
        return trim($string,' ');
    }
}

if (!function_exists('generate_random_string')) {

    function generate_random_string($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('std_merge')) {

    function std_merge() {
        $args  = func_get_args();
        $main_array = array();
        foreach ($args as $item) {
            $main_array = array_merge((array)$main_array + (array)$item);
        }
        return __toObject($main_array);
    }
}

if (!function_exists('strlenpd')) {

    function strlenpd($str)
    {
        if (is_array($str))
        {
            return false;
        }
        $str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
        if (function_exists('mb_strlen'))
        {
            return mb_strlen($str, 'utf-8');
        }

        return strlen($str);
    }
}

if (!function_exists('truncate')) {

    /**
     * Truncate strings
     *
     * @param string  $str
     * @param integer $maxLen Max length
     * @param string  $suffix Suffix optional
     *
     * @return string $str truncated
     */
    /* CAUTION : Use it only on module hookEvents.
    ** For other purposes use the smarty function instead */
    function truncate($string, $chars, $terminator = '...')
    {
        if (strlenpd($string) <= $chars)
        {
            return $string;
        }
        $cutPos = $chars - mb_strlen($terminator);
        $boundaryPos = mb_strrpos(mb_substr($string, 0, mb_strpos($string, ' ', $cutPos)), ' ');

        return mb_substr($string, 0, $boundaryPos === false ? $cutPos : $boundaryPos) . $terminator;
    }

}

if (!function_exists('trunc_word')) {

    function trunc_word($phrase, $max_words, $suffix = '...')
    {
        $phrase_array = explode(' ', $phrase);
        if (count($phrase_array) > $max_words && $max_words > 0)
        {
            $phrase = implode(' ', array_slice($phrase_array, 0, $max_words)) . $suffix;
        }

        return $phrase;
    }
}

if (!function_exists('truncate_by_params')) {

    function truncate_by_params($params){
        if(!isset($params['ident']) || !isset($params[$params['ident']]))
            return '---';

        $chars = isset($params['number']) ? $params['number'] : 100;
        $terminator = isset($params['terminator']) ? $params['terminator'] : '...';
        $full_text = $params[$params['ident']];
        $result = truncate($full_text, $chars, $terminator);
        if(isset($params['tooltip'])) {
            $attr = isset($params['attr']) ? $params['attr'] : '';
            $result = sprintf('<span class="text-default" data-toggle="tooltip" title="%s"%s>%s</span>', quote_text($full_text), $attr, quote_text($result));
        }
        return $result;
    }
}

if (!function_exists('get_field_state_by_ident')) {

    function get_field_state_by_ident($params=array()){

        if(!$params)
            return false;
        $ident = isset($params['ident']) ? $params['ident'] : 'state';
        $type = isset($params['type']) ? $params['type'] : 'none';
        $ident_val = isset($params[$ident]) ? $params[$ident] : null;
        switch ($ident_val){
            default:
                $color = 'text-danger';
                $name = '---';
                break;
            case 0:
                $color = 'text-danger';
                $name = 'غیرفعال';
                break;
            case 1:
                $color = 'text-success';
                $name = 'فعال';
                break;
            case 9:
                $color = 'text-primary';
                $name = 'آرشیوشده';
                break;
            case 10:
                $color = 'text-warning';
                $name = 'حذف‌شده';
                break;
        }

        switch ($type){
            default:
            case 'none':
                $result = $name;
                break;
            case 'html':
                $result = sprintf('<span class="%s">%s</span>', $color, $name);
                break;
        }

        return $result;
    }
}

if (!function_exists('time_to_decimal')) {

    /**
     * Convert time into decimal time.
     *
     * @param string $time The time to convert
     *
     * @return integer The time as a decimal value.
     */
    function time_to_decimal($time, $state='second') {
        if(!$time)
            return 0;
        $decTime = null;
        $timeArr = explode(':', $time);
        $hour = isset($timeArr[0]) ? $timeArr[0] : 0;
        $minute = isset($timeArr[1]) ? $timeArr[1] : 0;
        $second = isset($timeArr[2]) ? $timeArr[2] : 0;
        switch ($state){
            default:
            case 'second':
                $decTime = ($hour*3600) + ($minute * 60) + ($second);
                break;
            case 'minute':
                $decTime = ($hour*60) + ($minute) + ($second/60);
                break;
        }

        return $decTime;
    }
}

if (!function_exists('times_overlay_check')) {

    /**
     * Convert time into decimal time.
     *
     * @param string $time The time to convert
     *
     * @return boolean
     */
    function times_overlay_check($first_period_start, $first_period_end, $second_period_start, $second_period_end,$pre=false) {

        if($pre) {
            pr('FIRST ::::');
            pr('start=' . $first_period_start .'('.time_to_decimal($first_period_start).') - end=' . $first_period_end.'('.time_to_decimal($first_period_end).')');
            pr('SECOND ::::');
            pr('start=' . $second_period_start .'('.time_to_decimal($second_period_start).') - end=' . $second_period_end.'('.time_to_decimal($second_period_end).')');
        }

        $first_period_start = time_to_decimal($first_period_start);
        $first_period_end = time_to_decimal($first_period_end);
        $second_period_start = time_to_decimal($second_period_start);
        $second_period_end = time_to_decimal($second_period_end);


        /**********************************/
        /************ s1 < e1 *************/
        /**********************************/
        if ($first_period_start < $first_period_end) {

            if($pre) {
                vd($second_period_start > $first_period_start);
                vd($second_period_start >= $first_period_end);
                vd($second_period_end <= $first_period_start);
                vde(!($second_period_start > $first_period_start && $second_period_start >= $first_period_end && $second_period_end <= $first_period_start));
            }

            // s' < e' => [(s'<s1 && e'<=s1) && (s'>=e1 && e'>e)]
            if ($second_period_start < $second_period_end) {
                if (!(($second_period_start < $first_period_start && $second_period_end <= $first_period_start) || ($second_period_start >= $first_period_end && $second_period_end > $first_period_end) )) {
                    return true;
                }
            }
            // s' > e' => [s'>s1 && s'>=e1 && e'<=s1]
            else {
//            if ($second_period_start > $second_period_end) {
                if (!($second_period_start > $first_period_start && $second_period_start >= $first_period_end && $second_period_end <= $first_period_start)) {
//                	if($second_period_start<$first_period_end &&  $second_period_end>$first_period_end)
                    return true;
                }
            }
        }

        /**********************************/
        /************ s1 > e1 *************/
        /**********************************/
        else {

            // s' < e' => [s'>=e1 && e'<=s1     ]
            if ($second_period_start < $second_period_end) {
                if (!($second_period_start >= $first_period_end && $second_period_end <= $first_period_start)) {
                    return true;
                }
            }
            // s' > e'
            else {
                return true;
            }

        }

        return false;
    }
}

if (!function_exists('times_overlay_calculate')) {

    /**
     * Calculate Overley times
     *
     * @param string $time The time to convert
     *
     * @return (string | array)
     */
    function times_overlay_calculate($first_period_start, $first_period_end, $second_period_start, $second_period_end, $state_show='diff', $pre=false) {


//        $diff = times_overlay_check($first_period_start, $first_period_end, $second_period_start,$second_period_end);
//        if($diff==null)
//        	return null;

        if($pre) {
            pr('FIRST ::::');
            pr('start=' . $first_period_start . ' - end=' . $first_period_end);
            pr('SECOND ::::');
            pr('start=' . $second_period_start . ' - end=' . $second_period_end);
        }

        $diff_start_time = $diff_end_time = null;
        if($second_period_start>$second_period_end) {

            if (($first_period_start < $second_period_start && $first_period_start < $second_period_end) || ($first_period_start>$first_period_end && $first_period_start<$second_period_start) || ($first_period_start<$first_period_end && $first_period_start<$second_period_start && $first_period_end>$second_period_start)) {
                $diff_start_time = $second_period_start;
            }
            elseif ($first_period_start > $second_period_start && $first_period_start > $second_period_end) {
                $diff_start_time = $first_period_start;
            }

            if (($first_period_end > $second_period_start && $first_period_end > $second_period_end) || ($first_period_end < $second_period_start && $first_period_end < $second_period_end)) {
                $diff_end_time = $first_period_end;
            }
            elseif ($first_period_end < $second_period_start && $first_period_end > $second_period_end) {
                $diff_end_time = $second_period_end;
            }


        }
        elseif($second_period_start<$second_period_end) {

            if ( ($first_period_start>=$second_period_start && $first_period_start<$second_period_end)) {
                $diff_start_time = $first_period_start;
            }
            elseif (($first_period_start<$second_period_start) || ($first_period_start>$first_period_end && $first_period_start>$second_period_start && $first_period_end>$second_period_start && $first_period_end<$second_period_end)) {
                $diff_start_time = $second_period_start;
            }

            if ( $first_period_end > $second_period_start && $first_period_end <= $second_period_end) {
                $diff_end_time = $first_period_end;
            }
            elseif ($first_period_end > $second_period_end || ($first_period_start>$first_period_end && $first_period_end<$second_period_end)) {
                $diff_end_time = $second_period_end;
            }

        }

        if($pre) {
            pr('OVERLAY ::::');
            pr('start=' . $diff_start_time . ' - end=' . $diff_end_time);
        }

        switch ($state_show){
            default:
            case 'diff':
                return get_time_difference($diff_start_time, $diff_end_time, 'time');
                break;
            case 'time':
                return array('start'=>$diff_start_time, 'end'=>$diff_end_time);
                break;
        }
    }
}

if (!function_exists('decimal_to_time')) {

    /**
     * Convert decimal time into time in the format hh:mm:ss
     *
     * @param integer The time as a decimal value.
     *
     * @return string $time The converted time value.
     */
    function decimal_to_time($decimal, $state='second') {

        switch ($state){
            default:
            case 'second':

                $hours_float = $decimal / 3600;
                $hours = floor($decimal / 3600);
                $minutes_float = floatval($hours_float - $hours)*60;
                $minutes = floor($minutes_float);
                $seconds = ceil(($minutes_float - $minutes)*60);
                if($seconds == 60){
                    $minutes += 1;
                    $seconds = 0;
                }
                if($seconds == 1){
                    $seconds = 0;
                }
                break;
            case 'minute':
                $hours = floor($decimal / 60);
                $minutes = floor($decimal / 60);
                $seconds = $decimal - (int)$decimal;
                $seconds = round($seconds * 60);
                break;
        }

        return str_pad($hours, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":" . str_pad($seconds, 2, "0", STR_PAD_LEFT);
    }
}

if (!function_exists('time_validate')) {

    /*
     * Time Validator
     */
    function time_validate($input, $state='full'){
        $state_format = '/^(?:[01]\d|2[0-3])';
        switch($state){
            case 'sigma':
//                $state_format = '(:[0-9]|[0-5][0-9]){2}';
                $state_format = '/^(.*\d):(?:[0-9]\d):(?:[0-5]\d)';
//                pre(123);
                break;
            default:
            case 'full':
            case 'second':
//                $state_format = '(:[0-9]|[0-5][0-9]){2}';
                $state_format .= ':(?:[0-5]\d):(?:[0-5]\d)';
                break;
            case 'hour':
                $state_format .= '';
                break;
            case 'minute':
//                $state_format = '(:[0-9]|[0-5][0-9]){1}';
                $state_format .= ':(?:[0-5]\d)';
                break;
        }
//        pre("/^(?:[01]\d|2[0-3])".$state_format."$/");
        return preg_match($state_format."$/", $input);
//        return preg_match("/^(?:2[0-4]|[01][0-9]|10|0)".$state_format."/", $input);
//        return preg_match("/^([01]?[0-9]|2[0-4])".$state_format."/", $input);
//		return preg_match("/^(2[0-4]|[01][1-9]|10)".$state_format."/", $input);
    }
}

if (!function_exists('get_time_difference')) {

    /**
     * Function to calculate date or time difference.
     *
     * Function to calculate date or time difference. Returns an array or
     * false on error.
     *
     * @author       J de Silva                             <giddomains@gmail.com>
     * @copyright    Copyright &copy; 2005, J de Silva
     * @link         http://www.gidnetwork.com/b-16.html    Get the date / time difference with PHP
     * @param        string                                 $start
     * @param        string                                 $end
     * @return       (array | boolean)
     */
    function get_time_difference( $start, $end, $type='array', $state='time' )
    {
        if($start==null || $end==null)
            return false;

        $result = null;
        $end = $end==='00:00' ? '24:00' : $end;
        if($state=='date') {
            $uts['start']      =    strtotime( $start );
            $uts['end']        =    strtotime( $end );
        }
        else {
            $uts['start'] = time_to_decimal($start);
            $uts['end'] = time_to_decimal($end);
        }
//		pre($uts);
        if( $uts['start']!==-1 && $uts['end']!==-1 )
        {
            if( $uts['end'] >= $uts['start'] )
            {
                $diff_org    =    $uts['end'] - $uts['start'];
                $diff    =    $diff_org;
                if( $days=intval((floor($diff/86400))) )
                    $diff = $diff % 86400;
                if( $hours=intval((floor($diff/3600))) )
                    $diff = $diff % 3600;
                if( $minutes=intval((floor($diff/60))) )
                    $diff = $diff % 60;
                $diff    =    intval( $diff );
                switch($type){
                    default:
                    case 'array':
                        $result = array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff);
                        break;
                    case 'time':
                        $result = (strlen($hours)==1?'0':'').$hours .':'. (strlen($minutes)==1?'0':'').$minutes.':'.(strlen($diff)==1?'0':'').$diff;
                        break;
                    case 'second':
                        $result = $diff_org;
                        break;
                }
            }
            else
            {
                $uts = null;
                $half_first = get_time_difference($start, '24:00');
                $half_second = get_time_difference('00:00', $end);
                $days = $half_first['days'] + $half_second['days'];
                $hours = $half_first['hours'] + $half_second['hours'];
                $minutes = $half_first['minutes'] + $half_second['minutes'];
                $seconds = $half_first['seconds'] + $half_second['seconds'];
                if($seconds>=60){
                    $seconds = $seconds-60;
                    $minutes = $minutes+1;
                }
                if($minutes>=60){
                    $minutes = $minutes-60;
                    $hours = $hours+1;
                }

                switch($type){
                    default:
                    case 'array':
                        $result = array(
                            $days,
                            $hours,
                            $minutes,
                            $seconds
                        );
                        break;
                    case 'time':
//                        $result = (strlen($hours)==1?'0':'').$hours .':'. $minutes.':'.$seconds;
                        $result = (strlen($hours)==1?'0':'').$hours .':'. (strlen($minutes)==1?'0':'').$minutes.':'.(strlen($seconds)==1?'0':'').$seconds;
                        break;
                    case 'second':
                        $result = strtotime((strlen($hours)==1?'0':'').$hours .':'. $minutes);
                        break;
                }
            }
            return $result;
        }
        else
        {
            trigger_error( "Invalid date/time data detected", E_USER_WARNING );
        }
        return( false );
    }
}

if (!function_exists('get_file_content_type')) {
    /**
     * @param $fileType
     * @return bool|string
     */
    function get_file_content_type($fileType)
    {
        $filearr = explode('?', $fileType);
        $fileType = array_shift($filearr);

        $filearr = explode('&', $fileType);
        $fileType = array_shift($filearr);
        switch ($fileType) {
            case 'x3d':
                $type = 'application/vnd.hzn-3d-crossword';
                break;
            case '3gpp':
            case '3gp':
                $type = 'video/3gpp';
                break;
            case '3g2':
                $type = 'video/3gpp2';
                break;
            case 'mseq':
                $type = 'application/vnd.mseq';
                break;
            case 'pwn':
                $type = 'application/vnd.3m.post-it-notes';
                break;
            case 'plb':
                $type = 'application/vnd.3gpp.pic-bw-large';
                break;
            case 'psb':
                $type = 'application/vnd.3gpp.pic-bw-small';
                break;
            case 'pvb':
                $type = 'application/vnd.3gpp.pic-bw-var';
                break;
            case 'tcap':
                $type = 'application/vnd.3gpp2.tcap';
                break;
            case '7z':
                $type = 'application/x-7z-compressed';
                break;
            case 'abw':
                $type = 'application/x-abiword';
                break;
            case 'ace':
                $type = 'application/x-ace-compressed';
                break;
            case 'acc':
                $type = 'application/vnd.americandynamics.acc';
                break;
            case 'acu':
                $type = 'application/vnd.acucobol';
                break;
            case 'atc':
                $type = 'application/vnd.acucorp';
                break;
            case 'adp':
                $type = 'audio/adpcm';
                break;
            case 'aab':
                $type = 'application/x-authorware-bin';
                break;
            case 'aam':
                $type = 'application/x-authorware-map';
                break;
            case 'aas':
                $type = 'application/x-authorware-seg';
                break;
            case 'air':
                $type = 'application/vnd.adobe.air-application-installer-package+zip';
                break;
            case 'swf':
                $type = 'application/x-shockwave-flash';
                break;
            case 'fxp':
                $type = 'application/vnd.adobe.fxp';
                break;
            case 'pdf':
                $type = 'application/pdf';
                break;
            case 'ppd':
                $type = 'application/vnd.cups-ppd';
                break;
            case 'dir':
                $type = 'application/x-director';
                break;
            case 'xdp':
                $type = 'application/vnd.adobe.xdp+xml';
                break;
            case 'xfdf':
                $type = 'application/vnd.adobe.xfdf';
                break;
            case 'aac':
                $type = 'audio/x-aac';
                break;
            case 'ahead':
                $type = 'application/vnd.ahead.space';
                break;
            case 'azf':
                $type = 'application/vnd.airzip.filesecure.azf';
                break;
            case 'azs':
                $type = 'application/vnd.airzip.filesecure.azs';
                break;
            case 'azw':
                $type = 'application/vnd.amazon.ebook';
                break;
            case 'ami':
                $type = 'application/vnd.amiga.ami';
                break;
            case '/A':
                $type = 'application/andrew-inset';
                break;
            case 'apk':
                $type = 'application/vnd.android.package-archive';
                break;
            case 'cii':
                $type = 'application/vnd.anser-web-certificate-issue-initiation';
                break;
            case 'fti':
                $type = 'application/vnd.anser-web-funds-transfer-initiation';
                break;
            case 'atx':
                $type = 'application/vnd.antix.game-component';
                break;
            case 'dmg':
                $type = 'application/x-apple-diskimage';
                break;
            case 'mpkg':
                $type = 'application/vnd.apple.installer+xml';
                break;
            case 'aw':
                $type = 'application/applixware';
                break;
            case 'les':
                $type = 'application/vnd.hhe.lesson-player';
                break;
            case 'swi':
                $type = 'application/vnd.aristanetworks.swi';
                break;
            case 's':
                $type = 'text/x-asm';
                break;
            case 'atomcat':
                $type = 'application/atomcat+xml';
                break;
            case 'atomsvc':
                $type = 'application/atomsvc+xml';
                break;
            case 'atom, .xml':
                $type = 'application/atom+xml';
                break;
            case 'ac':
                $type = 'application/pkix-attr-cert';
                break;
            case 'aif':
                $type = 'audio/x-aiff';
                break;
            case 'avi':
                $type = 'video/x-msvideo';
                break;
            case 'aep':
                $type = 'application/vnd.audiograph';
                break;
            case 'dxf':
                $type = 'image/vnd.dxf';
                break;
            case 'dwf':
                $type = 'model/vnd.dwf';
                break;
            case 'par':
                $type = 'text/plain-bas';
                break;
            case 'bcpio':
                $type = 'application/x-bcpio';
                break;
            case 'bin':
                $type = 'application/octet-stream';
                break;
            case 'bmp':
                $type = 'image/bmp';
                break;
            case 'torrent':
                $type = 'application/x-bittorrent';
                break;
            case 'cod':
                $type = 'application/vnd.rim.cod';
                break;
            case 'mpm':
                $type = 'application/vnd.blueice.multipass';
                break;
            case 'bmi':
                $type = 'application/vnd.bmi';
                break;
            case 'sh':
                $type = 'application/x-sh';
                break;
            case 'btif':
                $type = 'image/prs.btif';
                break;
            case 'rep':
                $type = 'application/vnd.businessobjects';
                break;
            case 'bz':
                $type = 'application/x-bzip';
                break;
            case 'bz2':
                $type = 'application/x-bzip2';
                break;
            case 'csh':
                $type = 'application/x-csh';
                break;
            case 'c':
                $type = 'text/x-c';
                break;
            case 'cdxml':
                $type = 'application/vnd.chemdraw+xml';
                break;
            case 'pdc':
            case 'css':
                $type = 'text/css';
                break;
            case 'cdx':
                $type = 'chemical/x-cdx';
                break;
            case 'cml':
                $type = 'chemical/x-cml';
                break;
            case 'csml':
                $type = 'chemical/x-csml';
                break;
            case 'cdbcmsg':
                $type = 'application/vnd.contact.cmsg';
                break;
            case 'cla':
                $type = 'application/vnd.claymore';
                break;
            case 'c4g':
                $type = 'application/vnd.clonk.c4group';
                break;
            case 'sub':
                $type = 'image/vnd.dvb.subtitle';
                break;
            case 'cdmia':
                $type = 'application/cdmi-capability';
                break;
            case 'cdmic':
                $type = 'application/cdmi-container';
                break;
            case 'cdmid':
                $type = 'application/cdmi-domain';
                break;
            case 'cdmio':
                $type = 'application/cdmi-object';
                break;
            case 'cdmiq':
                $type = 'application/cdmi-queue';
                break;
            case 'c11amc':
                $type = 'application/vnd.cluetrust.cartomobile-config';
                break;
            case 'c11amz':
                $type = 'application/vnd.cluetrust.cartomobile-config-pkg';
                break;
            case 'ras':
                $type = 'image/x-cmu-raster';
                break;
            case 'dae':
                $type = 'model/vnd.collada+xml';
                break;
            case 'csv':
                $type = 'text/csv';
                break;
            case 'cpt':
                $type = 'application/mac-compactpro';
                break;
            case 'wmlc':
                $type = 'application/vnd.wap.wmlc';
                break;
            case 'cgm':
                $type = 'image/cgm';
                break;
            case 'ice':
                $type = 'x-conference/x-cooltalk';
                break;
            case 'cmx':
                $type = 'image/x-cmx';
                break;
            case 'xar':
                $type = 'application/vnd.xara';
                break;
            case 'cmc':
                $type = 'application/vnd.cosmocaller';
                break;
            case 'cpio':
                $type = 'application/x-cpio';
                break;
            case 'clkx':
                $type = 'application/vnd.crick.clicker';
                break;
            case 'clkk':
                $type = 'application/vnd.crick.clicker.keyboard';
                break;
            case 'clkp':
                $type = 'application/vnd.crick.clicker.palette';
                break;
            case 'clkt':
                $type = 'application/vnd.crick.clicker.template';
                break;
            case 'clkw':
                $type = 'application/vnd.crick.clicker.wordbank';
                break;
            case 'wbs':
                $type = 'application/vnd.criticaltools.wbs+xml';
                break;
            case 'cryptonote':
                $type = 'application/vnd.rig.cryptonote';
                break;
            case 'cif':
                $type = 'chemical/x-cif';
                break;
            case 'cmdf':
                $type = 'chemical/x-cmdf';
                break;
            case 'cu':
                $type = 'application/cu-seeme';
                break;
            case 'cww':
                $type = 'application/prs.cww';
                break;
            case 'curl':
                $type = 'text/vnd.curl';
                break;
            case 'dcurl':
                $type = 'text/vnd.curl.dcurl';
                break;
            case 'mcurl':
                $type = 'text/vnd.curl.mcurl';
                break;
            case 'scurl':
                $type = 'text/vnd.curl.scurl';
                break;
            case 'car':
                $type = 'application/vnd.curl.car';
                break;
            case 'pcurl':
                $type = 'application/vnd.curl.pcurl';
                break;
            case 'cmp':
                $type = 'application/vnd.yellowriver-custom-menu';
                break;
            case 'dssc':
                $type = 'application/dssc+der';
                break;
            case 'xdssc':
                $type = 'application/dssc+xml';
                break;
            case 'deb':
                $type = 'application/x-debian-package';
                break;
            case 'uva':
                $type = 'audio/vnd.dece.audio';
                break;
            case 'uvi':
                $type = 'image/vnd.dece.graphic';
                break;
            case 'uvh':
                $type = 'video/vnd.dece.hd';
                break;
            case 'uvm':
                $type = 'video/vnd.dece.mobile';
                break;
            case 'uvu':
                $type = 'video/vnd.uvvu.mp4';
                break;
            case 'uvp':
                $type = 'video/vnd.dece.pd';
                break;
            case 'uvs':
                $type = 'video/vnd.dece.sd';
                break;
            case 'uvv':
                $type = 'video/vnd.dece.video';
                break;
            case 'dvi':
                $type = 'application/x-dvi';
                break;
            case 'seed':
                $type = 'application/vnd.fdsn.seed';
                break;
            case 'dtb':
                $type = 'application/x-dtbook+xml';
                break;
            case 'res':
                $type = 'application/x-dtbresource+xml';
                break;
            case 'ait':
                $type = 'application/vnd.dvb.ait';
                break;
            case 'svc':
                $type = 'application/vnd.dvb.service';
                break;
            case 'eol':
                $type = 'audio/vnd.digital-winds';
                break;
            case 'djvu':
                $type = 'image/vnd.djvu';
                break;
            case 'dtd':
                $type = 'application/xml-dtd';
                break;
            case 'mlp':
                $type = 'application/vnd.dolby.mlp';
                break;
            case 'wad':
                $type = 'application/x-doom';
                break;
            case 'dpg':
                $type = 'application/vnd.dpgraph';
                break;
            case 'dra':
                $type = 'audio/vnd.dra';
                break;
            case 'dfac':
                $type = 'application/vnd.dreamfactory';
                break;
            case 'dts':
                $type = 'audio/vnd.dts';
                break;
            case 'dtshd':
                $type = 'audio/vnd.dts.hd';
                break;
            case 'dwg':
                $type = 'image/vnd.dwg';
                break;
            case 'geo':
                $type = 'application/vnd.dynageo';
                break;
            case 'es':
                $type = 'application/ecmascript';
                break;
            case 'mag':
                $type = 'application/vnd.ecowin.chart';
                break;
            case 'mmr':
                $type = 'image/vnd.fujixerox.edmics-mmr';
                break;
            case 'rlc':
                $type = 'image/vnd.fujixerox.edmics-rlc';
                break;
            case 'exi':
                $type = 'application/exi';
                break;
            case 'mgz':
                $type = 'application/vnd.proteus.magazine';
                break;
            case 'epub':
                $type = 'application/epub+zip';
                break;
            case 'eml':
                $type = 'message/rfc822';
                break;
            case 'nml':
                $type = 'application/vnd.enliven';
                break;
            case 'xpr':
                $type = 'application/vnd.is-xpr';
                break;
            case 'xif':
                $type = 'image/vnd.xiff';
                break;
            case 'xfdl':
                $type = 'application/vnd.xfdl';
                break;
            case 'emma':
                $type = 'application/emma+xml';
                break;
            case 'ez2':
                $type = 'application/vnd.ezpix-album';
                break;
            case 'ez3':
                $type = 'application/vnd.ezpix-package';
                break;
            case 'fst':
                $type = 'image/vnd.fst';
                break;
            case 'fvt':
                $type = 'video/vnd.fvt';
                break;
            case 'fbs':
                $type = 'image/vnd.fastbidsheet';
                break;
            case 'fe_launch':
                $type = 'application/vnd.denovo.fcselayout-link';
                break;
            case 'f4v':
                $type = 'video/x-f4v';
                break;
            case 'flv':
                $type = 'video/x-flv';
                break;
            case 'fpx':
                $type = 'image/vnd.fpx';
                break;
            case 'npx':
                $type = 'image/vnd.net-fpx';
                break;
            case 'flx':
                $type = 'text/vnd.fmi.flexstor';
                break;
            case 'fli':
                $type = 'video/x-fli';
                break;
            case 'ftc':
                $type = 'application/vnd.fluxtime.clip';
                break;
            case 'fdf':
                $type = 'application/vnd.fdf';
                break;
            case 'f':
                $type = 'text/x-fortran';
                break;
            case 'mif':
                $type = 'application/vnd.mif';
                break;
            case 'fm':
                $type = 'application/vnd.framemaker';
                break;
            case 'fh':
                $type = 'image/x-freehand';
                break;
            case 'fsc':
                $type = 'application/vnd.fsc.weblaunch';
                break;
            case 'fnc':
                $type = 'application/vnd.frogans.fnc';
                break;
            case 'ltf':
                $type = 'application/vnd.frogans.ltf';
                break;
            case 'ddd':
                $type = 'application/vnd.fujixerox.ddd';
                break;
            case 'xdw':
                $type = 'application/vnd.fujixerox.docuworks';
                break;
            case 'xbd':
                $type = 'application/vnd.fujixerox.docuworks.binder';
                break;
            case 'oas':
                $type = 'application/vnd.fujitsu.oasys';
                break;
            case 'oa2':
                $type = 'application/vnd.fujitsu.oasys2';
                break;
            case 'oa3':
                $type = 'application/vnd.fujitsu.oasys3';
                break;
            case 'fg5':
                $type = 'application/vnd.fujitsu.oasysgp';
                break;
            case 'bh2':
                $type = 'application/vnd.fujitsu.oasysprs';
                break;
            case 'spl':
                $type = 'application/x-futuresplash';
                break;
            case 'fzs':
                $type = 'application/vnd.fuzzysheet';
                break;
            case 'g3':
                $type = 'image/g3fax';
                break;
            case 'gmx':
                $type = 'application/vnd.gmx';
                break;
            case 'gtw':
                $type = 'model/vnd.gtw';
                break;
            case 'txd':
                $type = 'application/vnd.genomatix.tuxedo';
                break;
            case 'ggb':
                $type = 'application/vnd.geogebra.file';
                break;
            case 'ggt':
                $type = 'application/vnd.geogebra.tool';
                break;
            case 'gdl':
                $type = 'model/vnd.gdl';
                break;
            case 'gex':
                $type = 'application/vnd.geometry-explorer';
                break;
            case 'gxt':
                $type = 'application/vnd.geonext';
                break;
            case 'g2w':
                $type = 'application/vnd.geoplan';
                break;
            case 'g3w':
                $type = 'application/vnd.geospace';
                break;
            case 'gsf':
                $type = 'application/x-font-ghostscript';
                break;
            case 'bdf':
                $type = 'application/x-font-bdf';
                break;
            case 'gtar':
                $type = 'application/x-gtar';
                break;
            case 'texinfo':
                $type = 'application/x-texinfo';
                break;
            case 'gnumeric':
                $type = 'application/x-gnumeric';
                break;
            case 'kml':
                $type = 'application/vnd.google-earth.kml+xml';
                break;
            case 'kmz':
                $type = 'application/vnd.google-earth.kmz';
                break;
            case 'gqf':
                $type = 'application/vnd.grafeq';
                break;
            case 'gif':
                $type = 'image/gif';
                break;
            case 'gv':
                $type = 'text/vnd.graphviz';
                break;
            case 'gac':
                $type = 'application/vnd.groove-account';
                break;
            case 'ghf':
                $type = 'application/vnd.groove-help';
                break;
            case 'gim':
                $type = 'application/vnd.groove-identity-message';
                break;
            case 'grv':
                $type = 'application/vnd.groove-injector';
                break;
            case 'gtm':
                $type = 'application/vnd.groove-tool-message';
                break;
            case 'tpl':
                $type = 'application/vnd.groove-tool-template';
                break;
            case 'vcg':
                $type = 'application/vnd.groove-vcard';
                break;
            case 'h261':
                $type = 'video/h261';
                break;
            case 'h263':
                $type = 'video/h263';
                break;
            case 'h264':
                $type = 'video/h264';
                break;
            case 'hpid':
                $type = 'application/vnd.hp-hpid';
                break;
            case 'hps':
                $type = 'application/vnd.hp-hps';
                break;
            case 'hdf':
                $type = 'application/x-hdf';
                break;
            case 'rip':
                $type = 'audio/vnd.rip';
                break;
            case 'hbci':
                $type = 'application/vnd.hbci';
                break;
            case 'jlt':
                $type = 'application/vnd.hp-jlyt';
                break;
            case 'pcl':
                $type = 'application/vnd.hp-pcl';
                break;
            case 'hpgl':
                $type = 'application/vnd.hp-hpgl';
                break;
            case 'htc':
                $type = 'text/x-component';
                break;
            case 'hvs':
                $type = 'application/vnd.yamaha.hv-script';
                break;
            case 'hvd':
                $type = 'application/vnd.yamaha.hv-dic';
                break;
            case 'hvp':
                $type = 'application/vnd.yamaha.hv-voice';
                break;
            case 'sfd-hdstx':
                $type = 'application/vnd.hydrostatix.sof-data';
                break;
            case 'stk':
                $type = 'application/hyperstudio';
                break;
            case 'hal':
                $type = 'application/vnd.hal+xml';
                break;
            case 'html':
                $type = 'text/html';
                break;
            case 'irm':
                $type = 'application/vnd.ibm.rights-management';
                break;
            case 'sc':
                $type = 'application/vnd.ibm.secure-container';
                break;
            case 'ics':
                $type = 'text/calendar';
                break;
            case 'icc':
                $type = 'application/vnd.iccprofile';
                break;
            case 'ico':
                $type = 'image/x-icon';
                break;
            case 'igl':
                $type = 'application/vnd.igloader';
                break;
            case 'ief':
                $type = 'image/ief';
                break;
            case 'ivp':
                $type = 'application/vnd.immervision-ivp';
                break;
            case 'ivu':
                $type = 'application/vnd.immervision-ivu';
                break;
            case 'rif':
                $type = 'application/reginfo+xml';
                break;
            case '3dml':
                $type = 'text/vnd.in3d.3dml';
                break;
            case 'spot':
                $type = 'text/vnd.in3d.spot';
                break;
            case 'igs':
                $type = 'model/iges';
                break;
            case 'i2g':
                $type = 'application/vnd.intergeo';
                break;
            case 'cdy':
                $type = 'application/vnd.cinderella';
                break;
            case 'xpw':
                $type = 'application/vnd.intercon.formnet';
                break;
            case 'fcs':
                $type = 'application/vnd.isac.fcs';
                break;
            case 'ipfix':
                $type = 'application/ipfix';
                break;
            case 'cer':
                $type = 'application/pkix-cert';
                break;
            case 'pki':
                $type = 'application/pkixcmp';
                break;
            case 'crl':
                $type = 'application/pkix-crl';
                break;
            case 'pkipath':
                $type = 'application/pkix-pkipath';
                break;
            case 'igm':
                $type = 'application/vnd.insors.igm';
                break;
            case 'rcprofile':
                $type = 'application/vnd.ipunplugged.rcprofile';
                break;
            case 'irp':
                $type = 'application/vnd.irepository.package+xml';
                break;
            case 'jad':
                $type = 'text/vnd.sun.j2me.app-descriptor';
                break;
            case 'jar':
                $type = 'application/java-archive';
                break;
            case 'class':
                $type = 'application/java-vm';
                break;
            case 'jnlp':
                $type = 'application/x-java-jnlp-file';
                break;
            case 'ser':
                $type = 'application/java-serialized-object';
                break;
            case 'java':
                $type = 'text/x-java-source,java';
                break;
            case 'pdj':
            case 'js':
                $type = 'application/javascript';
                break;
            case 'json':
                $type = 'application/json';
                break;
            case 'joda':
                $type = 'application/vnd.joost.joda-archive';
                break;
            case 'jpm':
                $type = 'video/jpm';
                break;
            case 'jpg':
            case 'jpeg':
                $type = 'image/jpeg';
                break;
            case 'pjpeg':
                $type = 'image/pjpeg';
                break;
            case 'jpgv':
                $type = 'video/jpeg';
                break;
            case 'ktz':
                $type = 'application/vnd.kahootz';
                break;
            case 'mmd':
                $type = 'application/vnd.chipnuts.karaoke-mmd';
                break;
            case 'karbon':
                $type = 'application/vnd.kde.karbon';
                break;
            case 'chrt':
                $type = 'application/vnd.kde.kchart';
                break;
            case 'kfo':
                $type = 'application/vnd.kde.kformula';
                break;
            case 'flw':
                $type = 'application/vnd.kde.kivio';
                break;
            case 'kon':
                $type = 'application/vnd.kde.kontour';
                break;
            case 'kpr':
                $type = 'application/vnd.kde.kpresenter';
                break;
            case 'ksp':
                $type = 'application/vnd.kde.kspread';
                break;
            case 'kwd':
                $type = 'application/vnd.kde.kword';
                break;
            case 'htke':
                $type = 'application/vnd.kenameaapp';
                break;
            case 'kia':
                $type = 'application/vnd.kidspiration';
                break;
            case 'kne':
                $type = 'application/vnd.kinar';
                break;
            case 'sse':
                $type = 'application/vnd.kodak-descriptor';
                break;
            case 'lasxml':
                $type = 'application/vnd.las.las+xml';
                break;
            case 'latex':
                $type = 'application/x-latex';
                break;
            case 'lbd':
                $type = 'application/vnd.llamagraphics.life-balance.desktop';
                break;
            case 'lbe':
                $type = 'application/vnd.llamagraphics.life-balance.exchange+xml';
                break;
            case 'jam':
                $type = 'application/vnd.jam';
                break;
            case '.123':
                $type = 'application/vnd.lotus-1-2-3';
                break;
            case 'apr':
                $type = 'application/vnd.lotus-approach';
                break;
            case 'pre':
                $type = 'application/vnd.lotus-freelance';
                break;
            case 'nsf':
                $type = 'application/vnd.lotus-notes';
                break;
            case 'org':
                $type = 'application/vnd.lotus-organizer';
                break;
            case 'scm':
                $type = 'application/vnd.lotus-screencam';
                break;
            case 'lwp':
                $type = 'application/vnd.lotus-wordpro';
                break;
            case 'lvp':
                $type = 'audio/vnd.lucent.voice';
                break;
            case 'm3u':
                $type = 'audio/x-mpegurl';
                break;
            case 'm4v':
                $type = 'video/x-m4v';
                break;
            case 'hqx':
                $type = 'application/mac-binhex40';
                break;
            case 'portpkg':
                $type = 'application/vnd.macports.portpkg';
                break;
            case 'mgp':
                $type = 'application/vnd.osgeo.mapguide.package';
                break;
            case 'mrc':
                $type = 'application/marc';
                break;
            case 'mrcx':
                $type = 'application/marcxml+xml';
                break;
            case 'mxf':
                $type = 'application/mxf';
                break;
            case 'nbp':
                $type = 'application/vnd.wolfram.player';
                break;
            case 'ma':
                $type = 'application/mathematica';
                break;
            case 'mathml':
                $type = 'application/mathml+xml';
                break;
            case 'mbox':
                $type = 'application/mbox';
                break;
            case 'mc1':
                $type = 'application/vnd.medcalcdata';
                break;
            case 'mscml':
                $type = 'application/mediaservercontrol+xml';
                break;
            case 'cdkey':
                $type = 'application/vnd.mediastation.cdkey';
                break;
            case 'mwf':
                $type = 'application/vnd.mfer';
                break;
            case 'mfm':
                $type = 'application/vnd.mfmp';
                break;
            case 'msh':
                $type = 'model/mesh';
                break;
            case 'mads':
                $type = 'application/mads+xml';
                break;
            case 'mets':
                $type = 'application/mets+xml';
                break;
            case 'mods':
                $type = 'application/mods+xml';
                break;
            case 'meta4':
                $type = 'application/metalink4+xml';
                break;
            case 'mcd':
                $type = 'application/vnd.mcd';
                break;
            case 'flo':
                $type = 'application/vnd.micrografx.flo';
                break;
            case 'igx':
                $type = 'application/vnd.micrografx.igx';
                break;
            case 'es3':
                $type = 'application/vnd.eszigno3+xml';
                break;
            case 'mdb':
                $type = 'application/x-msaccess';
                break;
            case 'asf':
                $type = 'video/x-ms-asf';
                break;
            case 'exe':
                $type = 'application/x-msdownload';
                break;
            case 'cil':
                $type = 'application/vnd.ms-artgalry';
                break;
            case 'cab':
                $type = 'application/vnd.ms-cab-compressed';
                break;
            case 'ims':
                $type = 'application/vnd.ms-ims';
                break;
            case 'application':
                $type = 'application/x-ms-application';
                break;
            case 'clp':
                $type = 'application/x-msclip';
                break;
            case 'mdi':
                $type = 'image/vnd.ms-modi';
                break;
            case 'eot':
                $type = 'application/vnd.ms-fontobject';
                break;
            case 'xls':
                $type = 'application/vnd.ms-excel';
                break;
            case 'xlam':
                $type = 'application/vnd.ms-excel.addin.macroenabled.12';
                break;
            case 'xlsb':
                $type = 'application/vnd.ms-excel.sheet.binary.macroenabled.12';
                break;
            case 'xltm':
                $type = 'application/vnd.ms-excel.template.macroenabled.12';
                break;
            case 'xlsm':
                $type = 'application/vnd.ms-excel.sheet.macroenabled.12';
                break;
            case 'chm':
                $type = 'application/vnd.ms-htmlhelp';
                break;
            case 'crd':
                $type = 'application/x-mscardfile';
                break;
            case 'lrm':
                $type = 'application/vnd.ms-lrm';
                break;
            case 'mvb':
                $type = 'application/x-msmediaview';
                break;
            case 'mny':
                $type = 'application/x-msmoney';
                break;
            case 'pptx':
                $type = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
                break;
            case 'sldx':
                $type = 'application/vnd.openxmlformats-officedocument.presentationml.slide';
                break;
            case 'ppsx':
                $type = 'application/vnd.openxmlformats-officedocument.presentationml.slideshow';
                break;
            case 'potx':
                $type = 'application/vnd.openxmlformats-officedocument.presentationml.template';
                break;
            case 'xlsx':
                $type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;
            case 'xltx':
                $type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.template';
                break;
            case 'docx':
                $type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                break;
            case 'dotx':
                $type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.template';
                break;
            case 'obd':
                $type = 'application/x-msbinder';
                break;
            case 'thmx':
                $type = 'application/vnd.ms-officetheme';
                break;
            case 'onetoc':
                $type = 'application/onenote';
                break;
            case 'pya':
                $type = 'audio/vnd.ms-playready.media.pya';
                break;
            case 'pyv':
                $type = 'video/vnd.ms-playready.media.pyv';
                break;
            case 'ppt':
                $type = 'application/vnd.ms-powerpoint';
                break;
            case 'ppam':
                $type = 'application/vnd.ms-powerpoint.addin.macroenabled.12';
                break;
            case 'sldm':
                $type = 'application/vnd.ms-powerpoint.slide.macroenabled.12';
                break;
            case 'pptm':
                $type = 'application/vnd.ms-powerpoint.presentation.macroenabled.12';
                break;
            case 'ppsm':
                $type = 'application/vnd.ms-powerpoint.slideshow.macroenabled.12';
                break;
            case 'potm':
                $type = 'application/vnd.ms-powerpoint.template.macroenabled.12';
                break;
            case 'mpp':
                $type = 'application/vnd.ms-project';
                break;
            case 'pub':
                $type = 'application/x-mspublisher';
                break;
            case 'scd':
                $type = 'application/x-msschedule';
                break;
            case 'xap':
                $type = 'application/x-silverlight-app';
                break;
            case 'stl':
                $type = 'application/vnd.ms-pki.stl';
                break;
            case 'cat':
                $type = 'application/vnd.ms-pki.seccat';
                break;
            case 'vsd':
                $type = 'application/vnd.visio';
                break;
            case 'vsdx':
                $type = 'application/vnd.visio2013';
                break;
            case 'wm':
                $type = 'video/x-ms-wm';
                break;
            case 'wma':
                $type = 'audio/x-ms-wma';
                break;
            case 'wax':
                $type = 'audio/x-ms-wax';
                break;
            case 'wmx':
                $type = 'video/x-ms-wmx';
                break;
            case 'wmd':
                $type = 'application/x-ms-wmd';
                break;
            case 'wpl':
                $type = 'application/vnd.ms-wpl';
                break;
            case 'wmz':
                $type = 'application/x-ms-wmz';
                break;
            case 'wmv':
                $type = 'video/x-ms-wmv';
                break;
            case 'wvx':
                $type = 'video/x-ms-wvx';
                break;
            case 'wmf':
                $type = 'application/x-msmetafile';
                break;
            case 'trm':
                $type = 'application/x-msterminal';
                break;
            case 'doc':
                $type = 'application/msword';
                break;
            case 'docm':
                $type = 'application/vnd.ms-word.document.macroenabled.12';
                break;
            case 'dotm':
                $type = 'application/vnd.ms-word.template.macroenabled.12';
                break;
            case 'wri':
                $type = 'application/x-mswrite';
                break;
            case 'wps':
                $type = 'application/vnd.ms-works';
                break;
            case 'xbap':
                $type = 'application/x-ms-xbap';
                break;
            case 'xps':
                $type = 'application/vnd.ms-xpsdocument';
                break;
            case 'mid':
                $type = 'audio/midi';
                break;
            case 'mpy':
                $type = 'application/vnd.ibm.minipay';
                break;
            case 'afp':
                $type = 'application/vnd.ibm.modcap';
                break;
            case 'rms':
                $type = 'application/vnd.jcp.javame.midlet-rms';
                break;
            case 'tmo':
                $type = 'application/vnd.tmobile-livetv';
                break;
            case 'prc':
                $type = 'application/x-mobipocket-ebook';
                break;
            case 'mbk':
                $type = 'application/vnd.mobius.mbk';
                break;
            case 'dis':
                $type = 'application/vnd.mobius.dis';
                break;
            case 'plc':
                $type = 'application/vnd.mobius.plc';
                break;
            case 'mqy':
                $type = 'application/vnd.mobius.mqy';
                break;
            case 'msl':
                $type = 'application/vnd.mobius.msl';
                break;
            case 'txf':
                $type = 'application/vnd.mobius.txf';
                break;
            case 'daf':
                $type = 'application/vnd.mobius.daf';
                break;
            case 'fly':
                $type = 'text/vnd.fly';
                break;
            case 'mpc':
                $type = 'application/vnd.mophun.certificate';
                break;
            case 'mpn':
                $type = 'application/vnd.mophun.application';
                break;
            case 'mj2':
                $type = 'video/mj2';
                break;
            case 'mpga':
                $type = 'audio/mpeg';
                break;
            case 'mxu':
                $type = 'video/vnd.mpegurl';
                break;
            case 'mpeg':
                $type = 'video/mpeg';
                break;
            case 'm21':
                $type = 'application/mp21';
                break;
            case 'mp4a':
                $type = 'audio/mp4';
                break;
            case 'mp4':
                $type = 'video/mp4';
                break;
            case 'm3u8':
                $type = 'application/vnd.apple.mpegurl';
                break;
            case 'mus':
                $type = 'application/vnd.musician';
                break;
            case 'msty':
                $type = 'application/vnd.muvee.style';
                break;
            case 'mxml':
                $type = 'application/xv+xml';
                break;
            case 'ngdat':
                $type = 'application/vnd.nokia.n-gage.data';
                break;
            case 'n-gage':
                $type = 'application/vnd.nokia.n-gage.symbian.install';
                break;
            case 'ncx':
                $type = 'application/x-dtbncx+xml';
                break;
            case 'nc':
                $type = 'application/x-netcdf';
                break;
            case 'nlu':
                $type = 'application/vnd.neurolanguage.nlu';
                break;
            case 'dna':
                $type = 'application/vnd.dna';
                break;
            case 'nnd':
                $type = 'application/vnd.noblenet-directory';
                break;
            case 'nns':
                $type = 'application/vnd.noblenet-sealer';
                break;
            case 'nnw':
                $type = 'application/vnd.noblenet-web';
                break;
            case 'rpst':
                $type = 'application/vnd.nokia.radio-preset';
                break;
            case 'rpss':
                $type = 'application/vnd.nokia.radio-presets';
                break;
            case 'n3':
                $type = 'text/n3';
                break;
            case 'edm':
                $type = 'application/vnd.novadigm.edm';
                break;
            case 'edx':
                $type = 'application/vnd.novadigm.edx';
                break;
            case 'ext':
                $type = 'application/vnd.novadigm.ext';
                break;
            case 'gph':
                $type = 'application/vnd.flographit';
                break;
            case 'ecelp4800':
                $type = 'audio/vnd.nuera.ecelp4800';
                break;
            case 'ecelp7470':
                $type = 'audio/vnd.nuera.ecelp7470';
                break;
            case 'ecelp9600':
                $type = 'audio/vnd.nuera.ecelp9600';
                break;
            case 'oda':
                $type = 'application/oda';
                break;
            case 'ogx':
                $type = 'application/ogg';
                break;
            case 'oga':
                $type = 'audio/ogg';
                break;
            case 'ogv':
                $type = 'video/ogg';
                break;
            case 'dd2':
                $type = 'application/vnd.oma.dd2+xml';
                break;
            case 'oth':
                $type = 'application/vnd.oasis.opendocument.text-web';
                break;
            case 'opf':
                $type = 'application/oebps-package+xml';
                break;
            case 'qbo':
                $type = 'application/vnd.intu.qbo';
                break;
            case 'oxt':
                $type = 'application/vnd.openofficeorg.extension';
                break;
            case 'osf':
                $type = 'application/vnd.yamaha.openscoreformat';
                break;
            case 'weba':
                $type = 'audio/webm';
                break;
            case 'webm':
                $type = 'video/webm';
                break;
            case 'odc':
                $type = 'application/vnd.oasis.opendocument.chart';
                break;
            case 'otc':
                $type = 'application/vnd.oasis.opendocument.chart-template';
                break;
            case 'odb':
                $type = 'application/vnd.oasis.opendocument.database';
                break;
            case 'odf':
                $type = 'application/vnd.oasis.opendocument.formula';
                break;
            case 'odft':
                $type = 'application/vnd.oasis.opendocument.formula-template';
                break;
            case 'odg':
                $type = 'application/vnd.oasis.opendocument.graphics';
                break;
            case 'otg':
                $type = 'application/vnd.oasis.opendocument.graphics-template';
                break;
            case 'odi':
                $type = 'application/vnd.oasis.opendocument.image';
                break;
            case 'oti':
                $type = 'application/vnd.oasis.opendocument.image-template';
                break;
            case 'odp':
                $type = 'application/vnd.oasis.opendocument.presentation';
                break;
            case 'otp':
                $type = 'application/vnd.oasis.opendocument.presentation-template';
                break;
            case 'ods':
                $type = 'application/vnd.oasis.opendocument.spreadsheet';
                break;
            case 'ots':
                $type = 'application/vnd.oasis.opendocument.spreadsheet-template';
                break;
            case 'odt':
                $type = 'application/vnd.oasis.opendocument.text';
                break;
            case 'odm':
                $type = 'application/vnd.oasis.opendocument.text-master';
                break;
            case 'ott':
                $type = 'application/vnd.oasis.opendocument.text-template';
                break;
            case 'ktx':
                $type = 'image/ktx';
                break;
            case 'sxc':
                $type = 'application/vnd.sun.xml.calc';
                break;
            case 'stc':
                $type = 'application/vnd.sun.xml.calc.template';
                break;
            case 'sxd':
                $type = 'application/vnd.sun.xml.draw';
                break;
            case 'std':
                $type = 'application/vnd.sun.xml.draw.template';
                break;
            case 'sxi':
                $type = 'application/vnd.sun.xml.impress';
                break;
            case 'sti':
                $type = 'application/vnd.sun.xml.impress.template';
                break;
            case 'sxm':
                $type = 'application/vnd.sun.xml.math';
                break;
            case 'sxw':
                $type = 'application/vnd.sun.xml.writer';
                break;
            case 'sxg':
                $type = 'application/vnd.sun.xml.writer.global';
                break;
            case 'stw':
                $type = 'application/vnd.sun.xml.writer.template';
                break;
            case 'otf':
                $type = 'application/x-font-otf';
                break;
            case 'osfpvg':
                $type = 'application/vnd.yamaha.openscoreformat.osfpvg+xml';
                break;
            case 'dp':
                $type = 'application/vnd.osgi.dp';
                break;
            case 'pdb':
                $type = 'application/vnd.palm';
                break;
            case 'p':
                $type = 'text/x-pascal';
                break;
            case 'paw':
                $type = 'application/vnd.pawaafile';
                break;
            case 'pclxl':
                $type = 'application/vnd.hp-pclxl';
                break;
            case 'efif':
                $type = 'application/vnd.picsel';
                break;
            case 'pcx':
                $type = 'image/x-pcx';
                break;
            case 'psd':
                $type = 'image/vnd.adobe.photoshop';
                break;
            case 'prf':
                $type = 'application/pics-rules';
                break;
            case 'pic':
                $type = 'image/x-pict';
                break;
            case 'chat':
                $type = 'application/x-chat';
                break;
            case 'p10':
                $type = 'application/pkcs10';
                break;
            case 'p12':
                $type = 'application/x-pkcs12';
                break;
            case 'p7m':
                $type = 'application/pkcs7-mime';
                break;
            case 'p7s':
                $type = 'application/pkcs7-signature';
                break;
            case 'p7r':
                $type = 'application/x-pkcs7-certreqresp';
                break;
            case 'p7b':
                $type = 'application/x-pkcs7-certificates';
                break;
            case 'p8':
                $type = 'application/pkcs8';
                break;
            case 'plf':
                $type = 'application/vnd.pocketlearn';
                break;
            case 'pnm':
                $type = 'image/x-portable-anymap';
                break;
            case 'pbm':
                $type = 'image/x-portable-bitmap';
                break;
            case 'pcf':
                $type = 'application/x-font-pcf';
                break;
            case 'pfr':
                $type = 'application/font-tdpfr';
                break;
            case 'pgn':
                $type = 'application/x-chess-pgn';
                break;
            case 'pgm':
                $type = 'image/x-portable-graymap';
                break;
            case 'png':
                $type = 'image/png';
                break;
            case 'ppm':
                $type = 'image/x-portable-pixmap';
                break;
            case 'pskcxml':
                $type = 'application/pskc+xml';
                break;
            case 'pml':
                $type = 'application/vnd.ctc-posml';
                break;
            case 'ai':
                $type = 'application/postscript';
                break;
            case 'pfa':
                $type = 'application/x-font-type1';
                break;
            case 'pbd':
                $type = 'application/vnd.powerbuilder6';
                break;
            case 'pgp':
                $type = 'application/pgp-encrypted';
                break;
            case 'box':
                $type = 'application/vnd.previewsystems.box';
                break;
            case 'ptid':
                $type = 'application/vnd.pvi.ptid1';
                break;
            case 'pls':
                $type = 'application/pls+xml';
                break;
            case 'str':
                $type = 'application/vnd.pg.format';
                break;
            case 'ei6':
                $type = 'application/vnd.pg.osasli';
                break;
            case 'dsc':
                $type = 'text/prs.lines.tag';
                break;
            case 'psf':
                $type = 'application/x-font-linux-psf';
                break;
            case 'qps':
                $type = 'application/vnd.publishare-delta-tree';
                break;
            case 'wg':
                $type = 'application/vnd.pmi.widget';
                break;
            case 'qxd':
                $type = 'application/vnd.quark.quarkxpress';
                break;
            case 'esf':
                $type = 'application/vnd.epson.esf';
                break;
            case 'msf':
                $type = 'application/vnd.epson.msf';
                break;
            case 'ssf':
                $type = 'application/vnd.epson.ssf';
                break;
            case 'qam':
                $type = 'application/vnd.epson.quickanime';
                break;
            case 'qfx':
                $type = 'application/vnd.intu.qfx';
                break;
            case 'qt':
                $type = 'video/quicktime';
                break;
            case 'rar':
                $type = 'application/x-rar-compressed';
                break;
            case 'ram':
                $type = 'audio/x-pn-realaudio';
                break;
            case 'rmp':
                $type = 'audio/x-pn-realaudio-plugin';
                break;
            case 'rsd':
                $type = 'application/rsd+xml';
                break;
            case 'rm':
                $type = 'application/vnd.rn-realmedia';
                break;
            case 'bed':
                $type = 'application/vnd.realvnc.bed';
                break;
            case 'mxl':
                $type = 'application/vnd.recordare.musicxml';
                break;
            case 'musicxml':
                $type = 'application/vnd.recordare.musicxml+xml';
                break;
            case 'rnc':
                $type = 'application/relax-ng-compact-syntax';
                break;
            case 'rdz':
                $type = 'application/vnd.data-vision.rdz';
                break;
            case 'rdf':
                $type = 'application/rdf+xml';
                break;
            case 'rp9':
                $type = 'application/vnd.cloanto.rp9';
                break;
            case 'jisp':
                $type = 'application/vnd.jisp';
                break;
            case 'rtf':
                $type = 'application/rtf';
                break;
            case 'rtx':
                $type = 'text/richtext';
                break;
            case 'link66':
                $type = 'application/vnd.route66.link66+xml';
                break;
            case 'rss, .xml':
                $type = 'application/rss+xml';
                break;
            case 'shf':
                $type = 'application/shf+xml';
                break;
            case 'st':
                $type = 'application/vnd.sailingtracker.track';
                break;
            case 'svg':
                $type = 'image/svg+xml';
                break;
            case 'sus':
                $type = 'application/vnd.sus-calendar';
                break;
            case 'sru':
                $type = 'application/sru+xml';
                break;
            case 'setpay':
                $type = 'application/set-payment-initiation';
                break;
            case 'setreg':
                $type = 'application/set-registration-initiation';
                break;
            case 'sema':
                $type = 'application/vnd.sema';
                break;
            case 'semd':
                $type = 'application/vnd.semd';
                break;
            case 'semf':
                $type = 'application/vnd.semf';
                break;
            case 'see':
                $type = 'application/vnd.seemail';
                break;
            case 'snf':
                $type = 'application/x-font-snf';
                break;
            case 'spq':
                $type = 'application/scvp-vp-request';
                break;
            case 'spp':
                $type = 'application/scvp-vp-response';
                break;
            case 'scq':
                $type = 'application/scvp-cv-request';
                break;
            case 'scs':
                $type = 'application/scvp-cv-response';
                break;
            case 'sdp':
                $type = 'application/sdp';
                break;
            case 'etx':
                $type = 'text/x-setext';
                break;
            case 'movie':
                $type = 'video/x-sgi-movie';
                break;
            case 'ifm':
                $type = 'application/vnd.shana.informed.formdata';
                break;
            case 'itp':
                $type = 'application/vnd.shana.informed.formtemplate';
                break;
            case 'iif':
                $type = 'application/vnd.shana.informed.interchange';
                break;
            case 'ipk':
                $type = 'application/vnd.shana.informed.package';
                break;
            case 'tfi':
                $type = 'application/thraud+xml';
                break;
            case 'shar':
                $type = 'application/x-shar';
                break;
            case 'rgb':
                $type = 'image/x-rgb';
                break;
            case 'slt':
                $type = 'application/vnd.epson.salt';
                break;
            case 'aso':
                $type = 'application/vnd.accpac.simply.aso';
                break;
            case 'imp':
                $type = 'application/vnd.accpac.simply.imp';
                break;
            case 'twd':
                $type = 'application/vnd.simtech-mindmapper';
                break;
            case 'csp':
                $type = 'application/vnd.commonspace';
                break;
            case 'saf':
                $type = 'application/vnd.yamaha.smaf-audio';
                break;
            case 'mmf':
                $type = 'application/vnd.smaf';
                break;
            case 'spf':
                $type = 'application/vnd.yamaha.smaf-phrase';
                break;
            case 'teacher':
                $type = 'application/vnd.smart.teacher';
                break;
            case 'svd':
                $type = 'application/vnd.svd';
                break;
            case 'rq':
                $type = 'application/sparql-query';
                break;
            case 'srx':
                $type = 'application/sparql-results+xml';
                break;
            case 'gram':
                $type = 'application/srgs';
                break;
            case 'grxml':
                $type = 'application/srgs+xml';
                break;
            case 'ssml':
                $type = 'application/ssml+xml';
                break;
            case 'skp':
                $type = 'application/vnd.koan';
                break;
            case 'sgml':
                $type = 'text/sgml';
                break;
            case 'sdc':
                $type = 'application/vnd.stardivision.calc';
                break;
            case 'sda':
                $type = 'application/vnd.stardivision.draw';
                break;
            case 'sdd':
                $type = 'application/vnd.stardivision.impress';
                break;
            case 'smf':
                $type = 'application/vnd.stardivision.math';
                break;
            case 'sdw':
                $type = 'application/vnd.stardivision.writer';
                break;
            case 'sgl':
                $type = 'application/vnd.stardivision.writer-global';
                break;
            case 'sm':
                $type = 'application/vnd.stepmania.stepchart';
                break;
            case 'sit':
                $type = 'application/x-stuffit';
                break;
            case 'sitx':
                $type = 'application/x-stuffitx';
                break;
            case 'sdkm':
                $type = 'application/vnd.solent.sdkm+xml';
                break;
            case 'xo':
                $type = 'application/vnd.olpc-sugar';
                break;
            case 'au':
                $type = 'audio/basic';
                break;
            case 'wqd':
                $type = 'application/vnd.wqd';
                break;
            case 'sis':
                $type = 'application/vnd.symbian.install';
                break;
            case 'smi':
                $type = 'application/smil+xml';
                break;
            case 'xsm':
                $type = 'application/vnd.syncml+xml';
                break;
            case 'bdm':
                $type = 'application/vnd.syncml.dm+wbxml';
                break;
            case 'xdm':
                $type = 'application/vnd.syncml.dm+xml';
                break;
            case 'sv4cpio':
                $type = 'application/x-sv4cpio';
                break;
            case 'sv4crc':
                $type = 'application/x-sv4crc';
                break;
            case 'sbml':
                $type = 'application/sbml+xml';
                break;
            case 'tsv':
                $type = 'text/tab-separated-values';
                break;
            case 'tiff':
                $type = 'image/tiff';
                break;
            case 'tao':
                $type = 'application/vnd.tao.intent-module-archive';
                break;
            case 'tar':
                $type = 'application/x-tar';
                break;
            case 'tcl':
                $type = 'application/x-tcl';
                break;
            case 'tex':
                $type = 'application/x-tex';
                break;
            case 'tfm':
                $type = 'application/x-tex-tfm';
                break;
            case 'tei':
                $type = 'application/tei+xml';
                break;
            case 'txt':
                $type = 'text/plain';
                break;
            case 'dxp':
                $type = 'application/vnd.spotfire.dxp';
                break;
            case 'sfs':
                $type = 'application/vnd.spotfire.sfs';
                break;
            case 'tsd':
                $type = 'application/timestamped-data';
                break;
            case 'tpt':
                $type = 'application/vnd.trid.tpt';
                break;
            case 'mxs':
                $type = 'application/vnd.triscape.mxs';
                break;
            case 't':
                $type = 'text/troff';
                break;
            case 'tra':
                $type = 'application/vnd.trueapp';
                break;
            case 'ttf':
                $type = 'application/x-font-ttf';
                break;
            case 'ttl':
                $type = 'text/turtle';
                break;
            case 'umj':
                $type = 'application/vnd.umajin';
                break;
            case 'uoml':
                $type = 'application/vnd.uoml+xml';
                break;
            case 'unityweb':
                $type = 'application/vnd.unity';
                break;
            case 'ufd':
                $type = 'application/vnd.ufdl';
                break;
            case 'uri':
                $type = 'text/uri-list';
                break;
            case 'utz':
                $type = 'application/vnd.uiq.theme';
                break;
            case 'ustar':
                $type = 'application/x-ustar';
                break;
            case 'uu':
                $type = 'text/x-uuencode';
                break;
            case 'vcs':
                $type = 'text/x-vcalendar';
                break;
            case 'vcf':
                $type = 'text/x-vcard';
                break;
            case 'vcd':
                $type = 'application/x-cdlink';
                break;
            case 'vsf':
                $type = 'application/vnd.vsf';
                break;
            case 'wrl':
                $type = 'model/vrml';
                break;
            case 'vcx':
                $type = 'application/vnd.vcx';
                break;
            case 'mts':
                $type = 'model/vnd.mts';
                break;
            case 'vtu':
                $type = 'model/vnd.vtu';
                break;
            case 'vis':
                $type = 'application/vnd.visionary';
                break;
            case 'viv':
                $type = 'video/vnd.vivo';
                break;
            case 'ccxml':
                $type = 'application/ccxml+xml,';
                break;
            case 'vxml':
                $type = 'application/voicexml+xml';
                break;
            case 'src':
                $type = 'application/x-wais-source';
                break;
            case 'wbxml':
                $type = 'application/vnd.wap.wbxml';
                break;
            case 'wbmp':
                $type = 'image/vnd.wap.wbmp';
                break;
            case 'wav':
                $type = 'audio/x-wav';
                break;
            case 'davmount':
                $type = 'application/davmount+xml';
                break;
            case 'woff2':
            case 'woff':
                $type = 'application/x-font-woff';
                break;
            case 'wspolicy':
                $type = 'application/wspolicy+xml';
                break;
            case 'webp':
                $type = 'image/webp';
                break;
            case 'wtb':
                $type = 'application/vnd.webturbo';
                break;
            case 'wgt':
                $type = 'application/widget';
                break;
            case 'hlp':
                $type = 'application/winhlp';
                break;
            case 'wml':
                $type = 'text/vnd.wap.wml';
                break;
            case 'wmls':
                $type = 'text/vnd.wap.wmlscript';
                break;
            case 'wmlsc':
                $type = 'application/vnd.wap.wmlscriptc';
                break;
            case 'wpd':
                $type = 'application/vnd.wordperfect';
                break;
            case 'stf':
                $type = 'application/vnd.wt.stf';
                break;
            case 'wsdl':
                $type = 'application/wsdl+xml';
                break;
            case 'xbm':
                $type = 'image/x-xbitmap';
                break;
            case 'xpm':
                $type = 'image/x-xpixmap';
                break;
            case 'xwd':
                $type = 'image/x-xwindowdump';
                break;
            case 'der':
                $type = 'application/x-x509-ca-cert';
                break;
            case 'fig':
                $type = 'application/x-xfig';
                break;
            case 'xhtml':
                $type = 'application/xhtml+xml';
                break;
            case 'xml':
                $type = 'application/xml';
                break;
            case 'xdf':
                $type = 'application/xcap-diff+xml';
                break;
            case 'xenc':
                $type = 'application/xenc+xml';
                break;
            case 'xer':
                $type = 'application/patch-ops-error+xml';
                break;
            case 'rl':
                $type = 'application/resource-lists+xml';
                break;
            case 'rs':
                $type = 'application/rls-services+xml';
                break;
            case 'rld':
                $type = 'application/resource-lists-diff+xml';
                break;
            case 'xslt':
                $type = 'application/xslt+xml';
                break;
            case 'xop':
                $type = 'application/xop+xml';
                break;
            case 'xpi':
                $type = 'application/x-xpinstall';
                break;
            case 'xspf':
                $type = 'application/xspf+xml';
                break;
            case 'xul':
                $type = 'application/vnd.mozilla.xul+xml';
                break;
            case 'xyz':
                $type = 'chemical/x-xyz';
                break;
            case 'yaml':
                $type = 'text/yaml';
                break;
            case 'yang':
                $type = 'application/yang';
                break;
            case 'yin':
                $type = 'application/yin+xml';
                break;
            case 'zir':
                $type = 'application/vnd.zul';
                break;
            case 'zip':
                $type = 'application/zip';
                break;
            case 'zmm':
                $type = 'application/vnd.handheld-entertainment+xml';
                break;
            default:
                $type = '';
                break;
        }
        if ($type)
            return $type;
        else
            return false;
    }
}

if (!function_exists('get_file_info_pd')) {
    /**
     * @param $filename
     * @param string $type
     * @return mixed|string
     */
    function get_file_info_pd($filename, $type = 'extension')
    {
        if ($filename == null || $filename == '')
            return '';

        $filearr = explode('?', $filename);
        $filename = array_shift($filearr);
        $filearr = explode('&', $filename);
        $filename = array_shift($filearr);

        $path_parts = pathinfo($filename);
        switch ($type) {
            default:
            case 'all':
                $result = $path_parts;
                break;
            case 'extension':
            case 'dirname':
            case 'basename':
            case 'filename':
                $result = $path_parts[$type];
                break;
        }

        return $result;

        // OLD VERSION
//        $filearr = explode('?',$filename);
//        $filename = array_shift($filearr);
//        $extentionArray = explode('.', $filename);
//        if( Validate::isEmpty($filename) || !is_array($extentionArray) )
//            return false;
//
//        return trim(strtolower(array_pop($extentionArray)));
    }
}

if (!function_exists('get_file_time_modified')) {
    /**
     * @param $file
     * @param string $format
     * @return bool|false|string
     */
    function get_file_time_modified($file, $format = 'Y-m-d H:i:s')
    {
        clearstatcache();
        if (!file_exists($file))
            return false;

        $time = filemtime($file);
        return date($format, $time);
    }
}

if (!function_exists('get_dir_files_info')) {
    /**
     * @param null $add
     * @return array
     */
    function get_dir_files_info($add = null)
    {

        $dir_path = $_SERVER['DOCUMENT_ROOT'];
        $files = scan_dir($dir_path . $add);

        $dir_arr = array();

        foreach ($files as $file) {
            if (file_exists($dir_path . $add . '/' . $file) && $file != '.' && $file != '..' && substr($file, 0, 2) != '__') {

                if (is_dir($dir_path . $add . '/' . $file)) {
                    $data_dirs = get_dir_files_info($add . '/' . $file);
                    if (is_array($data_dirs) && !empty($data_dirs)) {
                        $dir_arr = array_merge($dir_arr, $data_dirs);
                    }
                } else {
                    $date_modified = get_file_time_modified($dir_path . $add . '/' . $file);
                    $file_data_info = get_file_info_pd($file, 'all');
                    $file_info = array(
                        'address' => $add . '/',
                        'full_address' => base_url() . $add . '/' . $file,
                        'date_modified' => $date_modified,
                        'content_type' => get_file_content_type($file_data_info['extension'])
                    );
                    $file_info = array_merge($file_info, $file_data_info);

                    $dir_arr[] = $file_info;
                }
            }
        }

        return $dir_arr;
    }
}

if (!function_exists('scan_dir')) {

    /**
     * @param $dir
     * @param bool $just_dir
     * @return array|bool
     */
    function scan_dir($dir, $just_dir=FALSE){
        if( file_exists($dir) ) {
            $files = scandir($dir);
            natcasesort($files);
            $new_file = array();
            if($just_dir && sizeof($files)>2){
                foreach($files as $key=> $file) {
                    clearstatcache();
                    if ($file != '.' && $file != '..' && is_dir($dir . '/' . $file))
                        $new_file[] = $file;
                }
                return $new_file;
            }
            return $files;
        }
        return false;

    }
}

if (!function_exists('get_pageination')) {


    /*
     * Show Number of Page
     */
    function get_pageination($needl, $pagenumbsize, $limit, $params=array()){//urlpath=''){


        $e = ceil($pagenumbsize/$limit);
        if(!intval($needl) || $needl>$pagenumbsize || $limit>$pagenumbsize)
            return '';

        $urlpath = isset($params['urlpath']) ? $params['urlpath'] : '';
        $direction = isset($params['dir']) ? $params['dir'] : 'rtl';
        $end_icon = $direction=='rtl' ? '»' : '«';
        $start_icon = $direction=='rtl' ? '«' : '»';
//        $li_class = isset($params['li_class']) ? $params['li_class'] : '';
//        $a_class = isset($params['a_class']) ? $params['a_class'] : '';
//        $active_class = isset($params['active_class']) ? $params['active_class'] : '';
//        if($li_class)
//            $li_class = ' class="'.$li_class.'"';
//        if($a_class)
//            $a_class = ' class="'.$a_class.'"';




        $start =  $needl!=1 ? '<li class="page-item"><a class="page-link" href="'.$urlpath.(1).'">'.$start_icon.'</a></li><li class="page-item"><a class="page-link" href="'.$urlpath.'1">1</a><li>' : '<li class="page-item disabled"><a class="page-link">'.$start_icon.'</a></li>';
        $span1 = ($needl)>4 ? '<li class="page-item disabled"><a class="page-link">...</a></li>' : '';
        $betw1 = ($needl)>3 ? '<li class="page-item"><a class="page-link" href="'.$urlpath.($needl-2).'">'.($needl-2).'</a></li>' : '';
        $betw2 = ($needl)>2 ? '<li class="page-item"><a class="page-link" href="'.$urlpath.($needl-1).'">'.($needl-1).'</a></li>' : '';
        $need  = '<li class="page-item active"><a class="page-link">'.$needl.'</a></li>';
        $betw4 = $needl<($e-1) ? '<li class="page-item"><a class="page-link" href="'.$urlpath.($needl+1).'">'.($needl+1).'</a></li>' : '';
        $betw5 = $needl<($e-2) ? '<li class="page-item"><a class="page-link" href="'.$urlpath.($needl+2).'">'.($needl+2).'</a></li>' : '';
        $span2 = $needl<($e-3) ? '<li class="page-item disabled"><a class="page-link">...</a></li>' : '';
        $end   =  $needl!=$e ? '<li class="page-item"><a class="page-link" href="'.$urlpath.$e.'">'.$e.'</a></li><li class="page-item"><a class="page-link" href="'.$urlpath.($e).'">'.$end_icon.'</a></li>' : '<li class="page-item disabled"><a class="page-link">'.$end_icon.'</a></li>';

        $content = '<ul class="pagination justify-content-center pagination-lg '.$direction.'">
                        '.$start.'
                        '.$span1.'
                        '.$betw1.'
                        '.$betw2.'
                        '.$need.'
                        '.$betw4.'
                        '.$betw5.'
                        '.$span2.'
                        '.$end.'
                    </ul>';
        if($direction=='rtl')
            $content .= '<style>
                            .pagination.rtl .page-item:first-child .page-link {
                                border-top-right-radius: .3rem;
                                border-bottom-right-radius: .3rem;
                                border-top-left-radius: unset;
                                border-bottom-left-radius: unset;
                            }
                            .pagination.rtl .page-item:last-child .page-link {
                                border-top-left-radius: .3rem;
                                border-bottom-left-radius: .3rem;
                                border-top-right-radius: unset;
                                border-bottom-right-radius: unset;
                            }
                        </style>';

        return $content;
    }

}

if (!function_exists('post_curl')) {

    function post_curl($_url, $_param=[], $auth_params){

        $postData = '';
        //create name value pairs seperated by &
//        foreach($_param as $k => $v)
//        {
//            $postData .= $k . '='.$v.'&';
//        }
//        rtrim($postData, '&');
        $count_params = sizeof($_param);
        if(!empty($_param))
            $postData = json_encode($_param);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
//        curl_setopt($ch, CURLOPT_POST, count($postData));
        curl_setopt($ch, CURLOPT_POST, $count_params);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        if($auth_params!='') {
//            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_USERPWD, $auth_params);
//            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }

        $output=curl_exec($ch);
        curl_close($ch);

        return $output;
    }

}

if (!function_exists('file_exist_by_header')) {

    function file_exist_by_header($_url){
        $file_headers = @get_headers($_url);
        if(isset($file_headers))
            return $file_headers[0];
        return 'HTTP/1.1 404 Not Found';
    }
}

if (!function_exists('file_exists_get_header')) {

    function file_exists_get_header($_url){

        $handle = @get_headers($_url);
        if(is_array($handle) && isset($handle[0]))
            return (strpos($handle[0], '200')!=false);
        return false;
    }
}

if (!function_exists('file_exists_post_curl')) {

    function file_exists_post_curl($_url){
        $ch = curl_init($_url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $output = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);



        $c = curl_init();
        curl_setopt($c, CURLOPT_HEADER, true);
        curl_setopt($c, CURLOPT_NOBODY, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($c, CURLOPT_URL, $url);
        curl_exec($c);
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);
        return $status;
        pr($_url);
        vd($output==200);
        vde($output);
        return ($output==200 ? true : false);
    }
}

if (!function_exists('upload_to_server_ftp')) {

    function upload_to_server_ftp($files, $_param=[]){

        if(!is_array($files) || (is_array($files) && empty($files)))
            return false;

        $ci = &get_instance();
        global $CFG;
        $config =& $CFG->config;

        //////////////////////////////////////////////////
        /////////////// FTP Connect to server/////////////
        //////////////////////////////////////////////////
        if(!is_lib('ftp'))
            $ci->load->library('ftp');
        $configs = [];
        $configs['hostname'] = isset($_param['hostname']) ? $_param['hostname'] : $config['cdn_base_url'];
        $configs['username'] = isset($_param['username']) ? $_param['username'] : $config['cdn_ftp_username'];
        $configs['password'] = isset($_param['password']) ? $_param['password'] : $config['cdn_ftp_password'];
        $configs['debug']        = TRUE;
        $conect_to_ftp = $ci->ftp->connect($configs);

        if ($conect_to_ftp) {
            $timestamp = isset($_param['timestamp']) ? $_param['timestamp'] : '';
            $address = 'uploads/temp/';
            foreach ($files as $file) {
                $ci->ftp->upload(FCPATH . $address .$timestamp.$file, $address .$timestamp.$file, 'ascii', 0775);
            }
            return true;
        }
        $ci->ftp->close();
        return false;
    }

}

if (!function_exists('checkdir')) {

    function checkdir($address){

        $full_address_dir = FCPATH;
        foreach (explode('/',trim($address,'/')) as $item) {
            $full_address_dir .= $item.'/';
            if (!is_dir($full_address_dir)) {
                $oldmask = umask(0);
                mkdir($full_address_dir, 0777);
                umask($oldmask);
            }
        }
    }

}