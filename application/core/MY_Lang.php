<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
* Language Identifier
*
* Adds a language identifier prefix to all site_url links
*
* @copyright     Copyright (c) 2011 Wiredesignz
* @version         0.29
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*/
//function pre($aaa){
//    echo '<pre>';
//    print_r($aaa);
//    die('_end');
//}
class MY_Lang extends CI_Lang
{
    function __construct()
    {
        parent::__construct();
        global $URI, $CFG, $IN;
        $config =& $CFG->config;

        $index_page = $config['index_page'];
        $lang_ignore = $config['lang_ignore'];
        $default_abbr = $config['language_abbr'];
        $default_language = $config['language'];
        $lang_direction = $config['language_dir'];
        $lang_uri_abbr = $config['lang_uri_abbr'];

        if (!defined('_LANG_DEFAULT_ABBR_'))
            define('_LANG_DEFAULT_ABBR_', $default_abbr);
        if (!defined('_LANG_DEFAULT_'))
            define('_LANG_DEFAULT_', $default_language);
        if (!defined('_LANG_DEFAULT_DIR_'))
            define('_LANG_DEFAULT_DIR_', $lang_direction);
//        $lang_uri_abbr_db = $config['lang_uri_abbr_db'];
        $lang_exist = true;

        /* get the language abbreviation from uri */
        $uri_abbr = $URI->segment(1);

        /* adjust the uri string leading slash */
        $URI->uri_string = preg_replace("|^\/?|", '/', $URI->uri_string);

        // get saved language abbreviation from cookie
//        if($lang_uri_abbr_db && empty($lang_uri_abbr)) {
        if(empty($lang_uri_abbr)) {
            $lang_uri_abbr = $IN->cookie($config['cookie_prefix'] . 'pd_lang_uri_abbr');
            $lang_uri_abbr = $lang_uri_abbr!='' ? unserialize($lang_uri_abbr) : $this->get_lang_data_default();//[$default_abbr=>$default_language];
            $config['lang_uri_abbr'] = $lang_uri_abbr;
        }

//        pre($lang_uri_abbr);
//        vde(isset($lang_uri_abbr[$uri_abbr]));
        $sep = $_SERVER['HTTP_HOST']=='localhost' ? '/' : '';


        // ignore show
        if ($lang_ignore) {

            if (isset($lang_uri_abbr[$uri_abbr])) {

                /* set the language_abbreviation cookie */
                $IN->set_cookie('pd_lang', $uri_abbr, $config['sess_expiration']);

            }
            else {

                $lang_exist = false;

//                $IN->set_cookie($config['cookie_prefix'] . 'pd_lang', '');
                /* get the language_abbreviation from cookie */
                $lang_abbr = $IN->cookie($config['cookie_prefix'] . 'pd_lang');
            }

            if ($lang_exist && strlen($uri_abbr) == 2) {

                /* reset the uri identifier */
                $index_page .= empty($index_page) ? '' : '/';

                /* remove the invalid abbreviation */
                $URI->uri_string = preg_replace("|^\/?$uri_abbr\/?|", '', $URI->uri_string);

                /* redirect */
                header('Location: ' . $config['base_url'] . $index_page . $sep.$URI->uri_string);
                exit;
            }

        } else {

            /* set the language abbreviation */
            $lang_abbr = $uri_abbr;
        }

        /* check validity against config array */
        if (isset($lang_uri_abbr[$lang_abbr])) {

            /* reset uri segments and uri string */
            $URI->segment(array_shift($URI->segments));
            $URI->uri_string = preg_replace("|^\/?$lang_abbr|", '', $URI->uri_string);

            /* set config language values to match the user language */
            $config['language'] = $lang_uri_abbr[$lang_abbr]['name'];
            $config['language_abbr'] = $lang_abbr;
            $config['language_dir'] = $lang_uri_abbr[$lang_abbr]['dir'];

            /* if abbreviation is not ignored */
            if (!$lang_ignore) {

                /* check and set the uri identifier */
                $index_page .= empty($index_page) ? $lang_abbr : "/$lang_abbr";

                /* reset the index_page value */
                $config['index_page'] = $index_page;
            }

            /* set the language_abbreviation cookie */
            $IN->set_cookie('pd_lang', $lang_abbr, $config['sess_expiration']);

        }
        else {

            /* if abbreviation is not ignored */
            if (!$lang_ignore) {

                /* check and set the uri identifier to the default value */
                $index_page .= empty($index_page) ? $default_abbr : "/$default_abbr";

                if (strlen($lang_abbr) == 2) {

                    /* remove invalid abbreviation */
                    $URI->uri_string = preg_replace("|^\/?$lang_abbr|", '', $URI->uri_string);
                }


                /* redirect */
                header('Location: ' . $config['base_url'] . $index_page . $sep.$URI->uri_string);
                exit;
            }

            /* set the language_abbreviation cookie */
            $IN->set_cookie('pd_lang', $default_abbr, $config['sess_expiration']);
        }

        log_message('debug', "Language_Identifier Class Initialized");
    }

    function get_lang_data_default(){
        global $URI, $CFG, $IN;
        $config =& $CFG->config;
        $default_abbr = $config['language_abbr'];
        $default_language = $config['language'];
        $lang_direction = $config['language_dir'];
        $default = array( "fa" => ["name"=>$default_language, "ident"=>$default_abbr, "dir"=>$lang_direction]);

        return $default;
    }
}

/* translate helper */
function t($line)
{
    global $LANG;
    return ($t = $LANG->line($line)) ? $t : $line;
}

/* set Default lang helper */
function set_default_lang($lang_abbr=null)
{
    global $CFG;
    $config =& $CFG->config;

    $language_abbr = _LANG_DEFAULT_ABBR_;
    $language = _LANG_DEFAULT_;
    $language_dir = _LANG_DEFAULT_DIR_;
    if($lang_abbr!=null && isset($config['lang_uri_abbr'][$lang_abbr])){
        $lang_data = $config['lang_uri_abbr'][$lang_abbr];
        $language_abbr = $lang_data['ident'];
        $language = $lang_data['name'];
        $language_dir = $lang_data['dir'];
        pr('* Core * :: not null');
    }
    $config['language_abbr'] = $language_abbr;
    $config['language'] = $language;
    $config['language_dir'] = $language_dir;
}