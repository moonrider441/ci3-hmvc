<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$action = get_value('action', 'empty');
$stoken_ident = get_value('stoken_ident', 'user@auth', 'normal-md5');
if(!defined('_TOKEN_KEY_'))
    define('_TOKEN_KEY_', $stoken_ident);

$aci =& get_instance();


if($action=='get_user'){

    send_value(['state'=>301], 'json');
}


if($action == 'empty' ){echo('Hack Attempt!');die();}