<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Asset Bank Guide - 441
|--------------------------------------------------------------------------
|
|-Key
| Address asset     ::	address of asset	    => css/bootstrap.css
|-Values
| First  param	    ::	Type of gzip compression=> for Css [comp]=compress text - [safe]=no any compress mode  & for Js [gzip]=gzip compress mode - [comp] - [safe]
| Second param	    ::	Asset Dir		        => type of asset dir :: [home]="root/assets/" - [me]="curent_module/assets/" - [modul_name]="modul_name/assets/" - [safe]="no prefix"
|
| Example		    :: 	'default'=>array('css' => array('type' => 'css','files' => array('js/jquery-2.1.1.js' => 'safe@home')),'js' => array('type' => 'js','files' => array(|);
| Address Example   :: 'js/jquery-2.1.1.js' => 'safe@home'
*/

$this->asset_bank = array(
    'default'=>array(
        'css' => array(
            'type' => 'css',
            'files' => array()
        ),
        'js' => array(
            'type' => 'js',
            'files' => array()
        )
    ),
    'welcome_index'=> array(
        'css' => array(
            'type' => 'css',
            'default' => 0,
            'files' => array()
        ),
        'js' => array(
            'type' => 'js',
            'default' => 0,
            'files' => array()
        )
    ),
);













?>