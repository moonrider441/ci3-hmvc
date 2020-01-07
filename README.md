<p align="center">
    <a href="https://codeigniter.com/" target="_blank">
        <img src="https://codeigniter.com/assets/images/ci-logo-big.png" height="100px">
    </a>
    <h1 align="center">codeigniter v3.x hmvc pro</h1>
    <br>
</p>
CodeIgniter 3.x pro with HMVC (With ORM and PHP helber and main libraries for improve code optimisations)


Features
--------

- ***HMVC** use and development*

- ***MINI ORM** easy use to any query*

- ***PDM** perfesional development manager* 

- ***Smarty** strong, popular and easy template engine* 

- ***Multi Language Support** translate to any lannguage you like* 

- ***List Builder** build your filterable list with bootstrap structure* 

- ***Helper Library** fast access to anything* 

---

OUTLINE
-------


- [Modular Extensions - HMVC](#modular-extensions---hmvc)
    - [Modular Extensions installation](#modular-extensions-installation)
    - [Installation Guide Hints](#installation-guide-hints)
    - [Source Modular Extensions](#source-modular-extensions)
- [MINI ORM](#mini-orm)
    - [Definition](#definition)
    - [Query Builder](#query-builder)
    - [Validate data](#validate-data)
    - [Auto Save (Insert OR Update)](#auto-save-insert-or-update)
    - [Manually Save (Insert OR Update)](#manually-save-insert-or-update)
    - [Delete data](#delete-data)
    - [MINI ORM installation](#mini-orm-installation)
- [PDM](#pdm)  
    - [Manage Header & Footer](#manage-header--footer)
    - [Manage Assets](#manage-assets)
    - [Ajax Requests](#ajax-requests)
    - [Rendering pages](#rendering-pages)
- [Smarty](#smarty)
    - [Source Smarty integration](#source-smarty-integration)
- [Multi Language Support](#multi-language-support)
- [List Builder](#list-builder)
    - [Options](#options)
    - [All list Options](#all-list-options)
- [Helper Library](#helper-library)
    - [Tools Helper](#tools-helper)
    - [Validation Helper](#validation-helper)
    - [Persian Date Helper](#persian-date-helper)
- [Requirements](#requirements)
- [Installation](#installation)

---



# Modular Extensions - HMVC
Modular Extensions makes the CodeIgniter PHP framework modular. Modules are groups of independent components, typically model, controller and view, arranged in an application modules sub-directory that can be dropped into other CodeIgniter applications.
HMVC stands for Hierarchical Model View Controller.
Module Controllers can be used as normal Controllers or HMVC Controllers and they can be used as widgets to help you build view partials.

## Modular Extensions installation
1- Start with a clean CI install

2- Set $config[‘base_url’] correctly for your installation

3- Access the URL /index.php/welcome => shows Welcome to CodeIgniter

4- Drop Modular Extensions third_party files into the CI 2.0 application/third_party directory

5- Drop Modular Extensions core files into application/core, the MY_Controller.php file is not required unless you wish to create your own controller extension

6- Access the URL /index.php/welcome => shows Welcome to CodeIgniter

7- Create module directory structure application/modules/welcome/controllers

8- Move controller application/controllers/welcome.php to application/modules/welcome/controllers/welcome.php

9- Access the URL /index.php/welcome => shows Welcome to CodeIgniter

10- Create directory application/modules/welcome/views

11- Move view application/views/welcome_message.php to application/modules/welcome/views/welcome_message.php

12- Access the URL /index.php/welcome => shows Welcome to CodeIgniter

You should now have a running Modular Extensions installation.

## Installation Guide Hints:
-Steps 1-3 tell you how to get a standard CI install working - if you have a clean/tested CI install, skip to step 4.

-Steps 4-5 show that normal CI still works after installing MX - it shouldn’t interfere with the normal CI setup.

-Steps 6-8 show MX working alongside CI - controller moved to the “welcome” module, the view file remains in the CI application/views directory - MX can find module resources in several places, including the application directory.

-Steps 9-11 show MX working with both controller and view in the “welcome” module - there should be no files in the application/controllers or application/views directories.


## Source Modular Extensions
<a href="https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc/" target="_blank">Wiredesignz</a>


# MINI ORM
We developed Mini ORM for access to data and build any query that you want just by array params.

## Definition
- At first you have to create your model extend from core model "MP_Model".
- After that you need to define table columns and table name and table identified and field validation rules.
- Create your model construct and call parent construct

<pre>
Class User_model extends MP_Model
{
    /** Fields name */
    public $user_fullname;
    public $user_name;
    public $user_password;
    public $user_admin;
    public $user_email;
    public $user_state;

    /**	 Inhariet from parent */
    protected $table = 'user';
    protected $ident = 'user_id';
    protected $field_required = array(
        'user_name' => array('trim|required','نام کاربری'),
        'role_id' => array('trim|required','نقش'),
        'user_fullname' => array('trim|required|email','نام و نام خانوادگی'),
    );
    
    public function __construct()
    {
        parent::__construct();
    }
</pre>

## Query Builder
you can get any model data by passing array params to "get_row" or "get_rows" or other method.

<pre>
    function get_data(){
    
        // get last user table row limit 1
        $this->get_row('user_table');
        
        // get All user table rows
        $this->get_rows('user_table');
        
        // get Your custom rows
        $params = [];
        $params['field'] = 'user_fullname, user_name, user_admin, user_email';
        $params['where'] = ['user_id'=>10, 'user_admin' => 0, 'user_state' => 1, ...];
        $params['exist'] = ['EXISTS (SELECT 1 FROM ci_shop t2 WHERE t1.user_id=t2.user_id')', ...];
        $params['join'] = [['ci_product', 'user_id', 'inner'],['ci_product_detail', 'product_id', 'left']];
        $params['order'] = [['user_id'=>'desc'],['user_fullname'=>'asc']];
        $params['group'] = ['user_id', 'product_id'];
        
	// call user table without prefix table. / you can defie prefix in database.php in config folder of ci
        $this->get_rows('user t1', $params);
        
        // or get data by write your query
        $this->query('SELECT user_name FROM ci_user WHERE ...');
        
        //get last insert id
        $this->insert_id();
        
        // get count of data in table
        $this->count_all('user_table');
	
	.
	.
	.
    }
}
</pre>


## Validate data
validation of posted data and fill table fields in class just by calling method "do_validate()" in your controller.
<pre>
    function register() {
        // get all data from post and validate by defined rule on model class and return array of errors
        $errors = $this->do_validate();
</pre>

## Auto Save (Insert OR Update)
you can insert or update your posted data from client by identify ident automaticlly
- First you have to call user model and run initiallize method "intro($id)" for check insert or update mode in your controller. if you pass null or 0 for "$id" arg, means you want to insert data, else if you pass identify ident like 1 for "$id", means you want to update your row with your custom identify. 
- Second call "do_validate" for validation posted data and fill model variables;
- At last if you have no error, call "save()" method;

<pre>
class User extends MX_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->parser->setModule('admin');
	}
    
	function register($id=0) {
    
		// load user model
		$this->load->model('user_model', 'mdl');

		// run initiallize core model / check if $id=0 then "insert" else "update"
		$this->mdl->intro($id);

		//validate posted data from client 
		$errors = $this->mdl->do_validate();

		if(empty($errors)){

		    // if we dont have any error => save data
		    $this->mdl->save();
	    	}
		else 
		.
		.
		.
        }
</pre>

## Manually Save (Insert OR Update)
If you need insert or update data in your database manually, you can call method "execute($params, $table_name=null,$WHERE=null, $NULL_value=false)"

<pre>
Class User_model extends MP_Model
{
	function __construct()
	{
		parent::__construct();
		$this->parser->setModule('admin');
	}
		
    	public function save($NULL_VALUE = true){
        	$this->id = parent::save($NULL_VALUE);
		
		// after save user => insert or update in other table
		$this->manuallysave();
	}
    
	function manuallysave() {
    
    		// set your data for insert or update in first arg "$params"
    		$params = ['other_field'=>'data', 'other_field2'=>'data2', ...];
		
		// set you table name (if table is same as your model / in this sample "user_model" you can pass null to this arg)
		$table_name = 'table_name';
		
		// if you want to insert, pass null to "$WHERE" arg or if you want to update, pass your conditions to "$WHERE" arg
		$WHERE = null; // INSERT mode
		$WHERE = ['sample_field'=>'data', ...]; // UPDATE mode
		
		// if some data was null value and you want to save the null value in database, set "$NULL_value" arg => true 
		$NULL_value = true;
		// if you want to remove data that has null value, set "$NULL_value" arg => false
		$NULL_value = false;
		
		// Call method for execute your query (insert or update)
		$this->execute($params, $table_name, $WHERE, $NULL_value);
        }
</pre>

## Delete data
For delete data we have "delete($where=array(), $table_name='default_model_table_name')" with two arg.
- At first you have to fill your first arg "$where" in array mode like "['user_id'=>12]".
- If you want to delete from other table in other model you have to pass second arg "$table_name".

<pre>
class User extends MX_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->parser->setModule('admin');
	}
    
	function delete($id=0) {
    
		// load user model
		$this->load->model('user_model', 'mdl');
		
		// delete user row WHERE => user_id=12
		$this->mdl->delete(['user_id'=>12]);
		
		// delete from other table. sample:: a product row WHERE product_id=20
		$this->mdl->delete(['product_id'=>20], 'product_table');
        }
</pre>


## MINI ORM installation

1- Copy MP_Model in "application -> core"

2- Create your model and extend to "MP_Model"

3- Read and do the [Definition] instructions (Create table fields variable, table name variable, identify variable , ...)

4- Your model synced up with mini ORM and you can use all method easily.


#  PDM
<p>We called it perfesional development manager because you can manage your web application.</p>
<p>Here we see the features of this library.</p>
<p>To run this library you have to define it in "autoload.php".</p>

<pre>
	$autoload['libraries'] = array('pdm', ...);
</pre>


## Manage Header & Footer
<p>Any page you have, can has custom header and footer that load automaticlly by PDM.</p>
<p>You have to define header and footer in each modulde config folder -> "config.php" file.</p>
<p>After that you can define your module page header and footer by this structure: </p>

- "controller_function_header" for header

- "controller_function_footer" for footer 

<p>The value of each row has two section that seprate by "@pd@"</p>
<p>First section are module name that you wanted to load header or footer from that.</p>
<p>Second section are address of header or footer view in your project.</p>
<p>If you dont set any page header or footer in config file, the PDM load default value of current module header or footer for that page.</p>

<pre>
<?php
	$config['def_header'] = 'admin@pd@_header.tpl';
	$config['def_footer'] = 'admin@pd@_footer.tpl';

	$config['user_login_header'] = 'admin@pd@admin/_login_header.tpl';
	$config['user_login_footer'] = 'admin@pd@admin/_login_footer.tpl';
	.
	.
	.
?>
</pre>

## Manage Assets

<p>At first you have to create "asset.php" file in config folder of each module and write like these codes for all pages of your module.</p>
<p>The value of each asset has two section that seprate by "@".</p>
<p>First section are type of compressing mode that we have 3 type to compress and those are gzip, compress and safe mode.</p>

- ***gzip** compress with gzip algoritm.*

- ***compress** compressing with remove free space of asset that we recommended to use for style-sheets.*

- ***safe** not any compress method. that mean just remove the compressing proccess for this asset.*


<p>Second section are asset base module address that you can address your assets in 3 mode to load from current module or any where you want like core or other modules.</p>

- ***me** that mean use base address from current module for this asset.*

- ***home** that mean use base address from base or core assets in root of project. of course if you have this folder.*

- ***module_name** each exist module you want to use for base address like shop or admin.*


<p>We have simple example from shop module is here that are default asset and another page "shop_product" that means shop controller and product function.</p>

<pre>
<?php
	$this->asset_bank = array(
		'default'=>array(
			'css' => array(
				'type' => 'css',
				'files' => array(
					'vendor/bootstrap/css/bootstrap.min.css'=> 'safe@home',
					'vendor/fontawesome-free/css/all.min.css'=>'safe@home',
					'css/fonts-style/fonts.css'=>'comp@home',
					'vendor/animate/animate.min.css'=> 'safe@home',
					'vendor/owl.carousel/assets/owl.carousel.min.css'=> 'safe@home',
					'vendor/owl.carousel/assets/owl.theme.default.min.css'=> 'safe@home',
					'vendor/magnific-popup/magnific-popup.min.css'=> 'safe@home'
				)
			),
			'js' => array(
				'type' => 'js',
				'files' => array(
					'vendor/jquery/jquery.min.js'=>'safe@admin',
					'vendor/jquery.appear/jquery.appear.min.js'=>'safe@admin',
					'vendor/jquery.easing/jquery.easing.min.js'=>'safe@home',
					'vendor/jquery.cookie/jquery.cookie.min.js'=>'safe@home',
					'js/user_auth.js'=>'gzip@admin',
				)
			)
		),
		'shop_product'=> array(
			'css' => array(
				'type' => 'css',
				'files' => array(
					'css/front/skins/default.css'=> 'safe@me',
					'css/front/custom.css'=> 'safe@me'
				)
			),
			'js' => array(
				'type' => 'js',
				'files' => array(
					'js/front/views/view.shop.js'=>'safe@me',
					'js/front/custom.js'=>'safe@me',
					'js/front/product_front.js'=>'safe@me',
					'js/front/theme.init.js'=>'safe@me',
				)
			)
		)
		.
		.
		.
</pre>

<p>After you doing top setting, its time to show asset in view page of project.</p>
<p>For showing Style sheet or javascript files we have to create address that connected to Assets library and load your custom asset.</p>
<p>We have good news for you cause we done this addressing level in PDM library on "render_page()" method.</p>
<p>The full address of assets in client has some parameter that help you to hide your file dir address and separate your asset request from other requests.</p>
<p>You can use Assets library in 2 type view:</p>


- <b>Using for combine several assets to one file.</b>

<p>This type is combining form four segment</p>
<p>First segment : "asset" is used for identification our request is for asset files.</p>
<p>Second segment : "js" means we have a package of assets with different compression mode for Assets library. like admin module user page assets.</p>
<p>Third segment : "module_name" means that current request come from this module. for example "admin" module.</p>
<p>Fourth segment : "PageName_pd_css" this segment has two section that separate by "_pd_". first section is name of the module page that created at "module -> config -> asset.php" and  the second section is type of asset in this package like "css" or "js".</p>

<pre>
	 // look at this sample. we want to show "admin module -> admin controler -> user function" style sheet files.
     < link rel="stylesheet" href="http://site.com/asset/jc/admin/admin_user_pd_css" / >
	 
	 // look at this sample. we want to show "admin module" default package javascript files.
     < script src="http://site.com/asset/jc/admin/default_pd_js" type="text/javascript" ></ script >
</pre>


- <b>Using for just some files except other page assets and other place in page source. for example show "modernize.js" plugin in top of page.</b>

<p>This type is combining form three segment</p>
<p>First segment : "asset" is used for identification our request is for asset files.</p>
<p>Second segment : "module_name" means file is belong to this module and if belong to core asset we use "home". for example "admin" module.</p>
<p>Third segment : "address_path" is address path of file that start from asset folder in dir of module folder in second segment. for example "vendor/modernizr/modernizr.min.js".</p>

<pre>
	// look at this sample. we want to show a plugin js file in core assets (home) and "vendor/modernizr/modernizr.min.js" path.
     < script src="{base_url('asset/home/vendor/modernizr/modernizr.min.js')}" type="text/javascript" ></ script >
</pre>

<p>Remember to add these line to your route in (application -> config).</p>

<pre>
$route['asset/(?!jc/)(.*)'] = $route['default_controller'].'/asset/$1';
$route['asset/jc/([a-zA-Z]+)/(.*)'] = '$1/asset/$2';
</pre>

## Ajax Requests

<p>We separate Ajax requests from other requests for improve loading speed and decrease bandwidth usage.</p>
<p>Any Ajax request you want to send must having 2 parameter.</p>

- ***First parameter: "ajax_type"** value of this parameter is telling us which module folder is destination of request.*

- ***Second parameter: "p"** value of this parameter is telling us which php file is destination of request.*

<pre>
	// in this sample we want to send ajax request to "Shop" module and "admin_shop.php" file
	$.getJSON('?ajax_type=shop&p=admin_shop', {...} ,function (rdata) { ... })
</pre>

<p>All of Ajax requests are handle in PDM library by "_ajax_route()" method.</p>



## Rendering pages

<p>For rendering page in your controller just call "render_page($view_name, $data)" from pdm library.</p>

<pre>
<?php 
	class Shop extends MX_Controller
	{
	
		function product(){
			
			.
			.
			.
			
			// Render "product.tpl" view with "$data" assigned data.
			$this->pdm->render_page('product.tpl', $data);
		}
	}
?>
</pre>

<p>In rendering method we have three level:</p>

- ***First level:** Rendering the header of page.*

- ***Second level:** Rendering the content of view page.*

- ***Third level:** Rendering the footer of page.*


#  Smarty

Integrate Smarty into your Codeigniter applications.
We choosed Smarty template engine for developers, because it's powerful, easy use and popular in search engines.

## Source Smarty integration
<a href="https://github.com/Vheissu/Ci-Smarty" target="_blank">Vheissu</a>


# Multi Language Support

<p>You need to set needle params in application directory "config.php" file like this sample.</p>

<pre>
/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
|
| This determines which set of language files should be used. Make sure
| there is an available translation if you intend to use something other
| than english.
|
*/
$config['language']	= 'persian';

/* default language abbreviation */
$config['language_abbr'] = "fa";

/* default language abbreviation direction */
$config['language_dir'] = 'rtl';

/* set available language abbreviations */
$config['lang_uri_abbr'] =
    [
        "fa" => ["name"=>"persian", "ident"=>"fa", "dir"=>"rtl"],
        "en" => ["name"=>"english", "ident"=>"en", "dir"=>"ltr"]
    ];

/* hide the language segment (use cookie) */
$config['lang_ignore'] = TRUE;
</pre>

<p>if you set "lang_ignore" value true, the language segment in url automatically will be remove.</p>
<p>You can manage the storage method in "MY_Lang.php" in (application -> core), but in default mode we save the language info in cookie.</p>

<p>Remember to add these line to your route in (application -> config).</p>

<pre>
$route['(\w{2})/(.*)'] = '$2';
$route['(\w{2})'] = $route['default_controller'];
</pre>


# List Builder

<p>We Created a list builder with some features for control and getting report from data with any custom query</p>


## Options

<p>The List builder library has main option to initiallize.</p>
<p>We have two "state" mode for using list builder:</p>

- ***handler** - your data is array and dont need to fetch from database.*

<pre>

// Create list with Custom data
	
$this->load->library('pd_list', array(
	'state' => 'handler',
	'data' => [ 
		['name'=>'afshin','id'=>1, 'phone'=>'913xxxxxxx'],
		['name'=>'ali','id'=>2, 'phone'=>'912xxxxxxx'],
		['name'=>'hadi','id'=>3, 'phone'=>'914xxxxxxx'],
		['name'=>'mohamad','id'=>4, 'phone'=>'915xxxxxxx'],
		['name'=>'rasool','id'=>5, 'phone'=>'916xxxxxxx']
	],
	'cols' => [
		array('type' => 'field',
			'name' => 'شناسه',
			'action' => 'id',
			'align' => 'center',
			'width' => 90),
		array('type' => 'field',
			'name' => 'عنوان کاربر',
			'action' => 'name',
			'align' => 'center',
			'width' => 150),
		array('type' => 'field',
			'name' => 'تلفن',
			'action' => 'phone',
			'align' => 'center',
			'width' => 150)
	],
	'ident' => 'id',
	'order' => ['name'=>'ASC', 'id'=>'DESC'],
	'action_form' => 'mylist',
	'search' => ['عنوان'=>'name', 'شناسه'=>'id'],
	'limit' => '3'
	)
);

// Render list with your params
$user_list_content = $this->pd_list->render();
</pre>

- ***database** conneting to database and fetch data to build list. (default value is database)*

<p>Let's see the most importants options of table that most of them are using in database mode:</p>


### 1- fields

<p>You can fetch your data with custom field from database for increase performance.</p>
<p>If you leave this field, automaticlly will be set "*".</p>
<pre>
$this->load->library('pd_list', array(
		'table' => 'ci_user t1 INNER JOIN ci_user_detail t2 USING (user_id)',
		'fields' => 't1.user_id, t1.user_name, t2_user_detail_id, ...'
		.
		.
		.
</pre>


### 2- table

<p>You have to set your database table in this field. of course you can add your join here</p>
<pre>
$this->load->library('pd_list', array(
		'state' => 'database',
		'table' => 'ci_user t1 INNER JOIN ci_user_detail t2 USING (user_id)',
		.
		.
		.
</pre>


### 3- cols

<p>we have two type column: "field" type that you can find value in data, or "function" type that you pass the data to another method in other model to calculate value.</p>
<p>In function mode we pass row data to your method, of course you can pass your custom param too.</p>

<pre>
/*************************************/
/************* Field type ************/
/*************************************/
$fields = [
	[
		'type' => 'field', // type of field
		'name' => 'شناسه', // name you want to show
		'action' => 'id', // name of data
		'align' => 'center', // aligne placement
		'width' => 90 // with of column
	],
	[
		'type' => 'field',
		'name' => 'عنوان کاربر',
		'action' => 'name',
		'align' => 'center',
		'width' => 150
	]
];

/*************************************/
/********** Function type ************/
/*************************************/
$fields = [
	[
		'type' => 'function',
		'name' => 'تصویر',
		'function' => array("name" => 'get_user_thumbnail', 'params' => array('size'=>'50x50')),
		'model' => 'admin/user_model',
		'action' => 'user_id',
		'align' => 'center',
		'width' => 150
	],
];

// Show sample method called in table field
Class User_model extends MP_Model
{
	.
	.
	.
    public function get_user_thumbnail($params){
	
		// the "$params" arg is one row data + your custom params that you defined
        $id = $params['user_id'];
		
		// this is custom param
        $size = isset($params['size']) ? $params['size'] : '50x50';
		.
		.
		.
    }
</pre>


### 4- last_row

<p>You can set some last row for your table that need to be calculate data in your custom method.</p>
<pre>
$this->load->library('pd_list', array(
	'table' => 'ci_user t1',
	
	'last_row' => [
		// You can set static mode and pass your html in "content" param
		[ 'type'=>'static','content'=>'<tr><td>...</td></tr>' ],
		
		// You can set dynamic mode and fill "method" and "model" params for calculate your html
		[ 'type'=>'dynamic','method'=>'get_user_footer_info', 'model' => 'admin/user_model', 'params'=>array('length'=>$last_row_lenght, 'state'=>$transaction_state) ]
	]
	.
	.
	.
</pre>


### 5- ident

<p>You have to set your identify field of database table in this field.</p>
<pre>
$this->load->library('pd_list', array(
		'table' => 'ci_user t1 INNER JOIN ci_user_detail t2 USING (user_id)',
		'ident' => 'user_id',
		.
		.
		.
</pre>


### 5- where

<p>You have two where param to handle your condition in query.</p>
<p>First one is "where" that need to passing array params.</p>
<p>Second one is "where_custom" that need to passing string param.</p>
<pre>
// you can see both where condition in this sample
$this->load->library('pd_list', array(
		'table' => 'ci_user t1 INNER JOIN ci_user_detail t2 USING (user_id)',
		'where' =>  [
			['t1.user_state'=>1],
			['t2.user_detail_state'=>1]
		]
		'where_custom' => 'EXISTS (select 1 FROM pd_user u WHERE t1.user_id=u.user_id AND user_state<>1'
		.
		.
		.
</pre>


### 6- order

<p>For using Order by in your query pass the array to this param.</p>

<pre>
$this->load->library('pd_list', array(
		'table' => 'ci_user t1 INNER JOIN ci_user_detail t2 USING (user_id)',
		'order' =>  [
			['t1.user_id'=> 'ASC'],
			['t2.user_detail_id'=> 'DESC']
		]
		.
		.
		.
</pre>


### 7- group

<p>For using Group by in your query pass the string to this param.</p>

<pre>
$this->load->library('pd_list', array(
		'table' => 'ci_user t1 INNER JOIN ci_user_detail t2 USING (user_id)',
		'group' =>  't1.user_id'
		.
		.
		.
</pre>


### 8- search

<p>We have search box for your data table in top of table that configuring in this param.</p>
<p>You can use "CONCAT" in this option for searching too.</p>

<pre>
$this->load->library('pd_list', array(
		'table' => 'ci_user t1 INNER JOIN ci_user_detail t2 USING (user_id)',
		'search' =>  [
			['شناسه' => 't1.user_id'],
			['نام یا موبایل' => 'CONCAT(t1.user_id, " ", t1.user_phone)']
		]
		.
		.
		.
</pre>


### 8- action_link

<p>We have ability to creating any custom action link for each row of data and you have to pass array to this param.</p>
<p>The list builder library has default action links that you can using from them.</p>

<pre>
$action_link = [];
$action_link[] = [
	'title' => 'برای ویرایش فیلد کلیک کنید', // for title of button
	'name' => 'ویرایش', // for content name of button
	'icon' => 'fa-edit',
	'color' => 'btn-warning custom-class',
	'class' => 'edit-class',
	'link' => 'user/edit/@id@', // library automatically replace row identify inserted to "@id@" characters
	'target' => '_blank', // target of link action that default value is "_self"
	'translate' => 'enc', // you can encrypt your identifier in "link" param
]

// translate has  4 type "enc", "md5", "comp", "nope"
// "enc" => Non-return encryption of  identifier
// "md5" => Reversible encryption data that you can decrypt it in server with your key if you have
// "comp" => just compress identifier that you can decompress the text at server side
// "nope" => no encryption action to doing with identifier.  that is default value if you leave param


$this->load->library('pd_list', array(
		'table' => 'ci_user t1 INNER JOIN ci_user_detail t2 USING (user_id)',
		'action_link' =>  $action_link
		.
		.
		.
</pre>

<p>List builder library has default action links for decrease coding time of developer.</p>
<pre>
$action_link = [];
$action_link[] = [
	['link'=>'edit/@id@','type'=>'edit'],
	['link'=>'@id@','type'=>'delete'], // just update the state field of table to 10, means is deleted for user but you can see that.
	['link'=>'@id@','type'=>'full-delete'], // remove data from database
	['link'=>'@id@','type'=>'view']
]

$this->load->library('pd_list', array(
		'table' => 'ci_user t1 INNER JOIN ci_user_detail t2 USING (user_id)',
		'action_link' =>  $action_link
		.
		.
		.
</pre>


### 9- limit

<p>You can set limit for showing data in list and the other data is beeing on pagination.</p>
<p>If you dont like showing pagination html content and limit condition you can set "limit_box" to false.</p>

<pre>
$this->load->library('pd_list', array(
		'table' => 'ci_user t1 INNER JOIN ci_user_detail t2 USING (user_id)',
		'limit' =>  150 // default limit is 10
		.
		.
		. 
</pre>


### 10- before_delete

<p>Maybe you want to handle some actions before delete your data in list, so you can use this param by array setting.</p>
<p>You have to define method and model of your handler action like below sample.</p>

<pre>

$this->load->library('pd_list', array(
		'table' => 'ci_user t1 INNER JOIN ci_user_detail t2 USING (user_id)',
		'before_delete' => ['function' => 'before_delete_user_row', 'params' => array('state' => 'user_row'), 'model'=>'user/user_model']
		.
		.
		.
		
	
// Show sample method called in "before_delete" table field
Class User_model extends MP_Model
{
	.
	.
	.	
		
    public function before_delete($params){
		
		// "$params" variable is array and has these structure::
		// $params = [
		// 	 'base' => [...], // your custom params if you set in list initiallize param that is
		//	 'db' => [...], // selected rows from client in interface that are array mode
		//	 'full_delete' => 0 or 1  // if you set "full_delete" params 1 or "true" means all of delete request are full-delete and want to remove row from database
		// ]
		
        foreach($params['db'] as $param_row) {
			
			// do your actions
			.
			.
			.
        }
		// if you dont have any error
        return array('error'=>0);
    }
		
</pre>

<p>If you dont have any error, return below array to compelete action.</p>
<pre>
 return ['error' => 0];
</pre>

<p>If you have any errors in your actions, then return below array to showing user client errors and canceling the action.</p>
<pre>
 return ['error' => 1, 'msg' => 'این کاربر دارای یک خرید تکمیل نشده است'];
</pre>


### 11- filter

<p>If you dont want any filtering in data table so set "filter_box" false, default of "filter_box" is true.</p>
<p>If you want to set filter boxes in top of header you have to fill this param like below sample.</p>

<pre>

$filter = [];
$filter[] = [
	'name' => 'Filter by Activate State',
	'action' => 'user_state',
	'data' => [1 => 'Active' , 0 => 'Deactive']
];
$filter[] = [
	'name' => 'Filter by User Fullname',
	'action' => 'user_fullname',
	'data' => [1006 => 'Ali' , 500 => 'Ahmad', ...]
];


$this->load->library('pd_list', array(
		'table' => 'ci_user t1 INNER JOIN ci_user_detail t2 USING (user_id)',
		'filter' => $filter 
		.
		.
		.
		
		
</pre>


### 12- table_empty_first

<p>If you set true this param, list data will be empty on first view of page loading.</p>


### 13- disable_form

<p>If you set true this param, list builder do not create form element for your list.</p>

<pre>

$filter = [];
$filter[] = [
	'name' => 'Filter by Activate State',
	'action' => 'user_state',
	'data' => [1 => 'Active' , 0 => 'Deactive']
];
$filter[] = [
	'name' => 'Filter by User Fullname',
	'action' => 'user_fullname',
	'data' => [1006 => 'Ali' , 500 => 'Ahmad', ...]
];


$this->load->library('pd_list', array(
		'table' => 'ci_user t1 INNER JOIN ci_user_detail t2 USING (user_id)',
		'filter' => $filter 
		.
		.
		.
		
		
</pre>

### 14- header_button, header_text, header_box // footer_button, footer_text, footer_box

<p>With "header_button" param you can create buttons on top of your list for doing some action like linking to other pages.</p>
<p>With "header_text" param you can show string content helping on top of your list for information of list or other plan.</p>
<p>If you set "false" the "header_box" param, the header box content canceled.</p>
<p>All of footer params are like header. but will be creating in footer of table.</p>

<pre>
$header_button = [];
$header_button[] = [
	'class' => 'custom_element_class',
	'id' => 'custom_element_id',
	'color' => 'custom_element_color',
	'link' => 'http://domain.com/user/create',
	'size' => 'small',
	'attr' => 'data-toggle="tooltip" data-title="title of this button"',
	'text' => '<i class="fa fa-plus"></i> افزودن کاربر جدید'
];


$this->load->library('pd_list', array(
		'table' => 'ci_user t1 INNER JOIN ci_user_detail t2 USING (user_id)',
		'header_button' => $header_button, 
		'header_text' => 'برای درج کاربر جدید از لینک زیر میتوانید استفاده کنید.', 
		.
		.
		.
</pre>

### 15- field_checkbox

<p>If you set "true" this param, list builder library will be creating checkbox element in first column of each row for selecting the rows.</p>
<p>Default value of this param is "true".</p>

<pre>
$header_button = [];
$header_button[] = [
	'class' => 'custom_element_class',
	'id' => 'custom_element_id',
	'color' => 'custom_element_color',
	'link' => 'http://domain.com/user/create',
	'size' => 'small',
	'attr' => 'data-toggle="tooltip" data-title="title of this button"',
	'text' => '<i class="fa fa-plus"></i> افزودن کاربر جدید'
];


$this->load->library('pd_list', array(
		'table' => 'ci_user t1 INNER JOIN ci_user_detail t2 USING (user_id)',
		'header_button' => $header_button, 
		'header_text' => 'برای درج کاربر جدید از لینک زیر میتوانید استفاده کنید.', 
		.
		.
		.
</pre>

## All list Options

<p>Here is All of List builder library options.</p>
<pre>
array(
		'table_ident'		=> 0, // number of table for separate multi tables in one page
		'state'	 			=> 'database',// has two mode for handling data from "database" or "handler" custom array data
		'data'	 	 		=> array(), // if you choose state => handler you need fill this param with your data
		'fields'	 	 	=> '*', // for database mode you can call your fields.
		'table' 		 	=> '',// for database mode table name
		'title' 		 	=> '',// title of table 
		'cols' 		  	 	=> array(), // column of table are define in this param
		'last_row'	 	 	=> array(), // set function for handler custom last row like get sum of data
		'ident' 		 	=> 'field_id', // identify of table
		'where' 		 	=> '', // conditions of table that you have pass array where
		'where_custom' 	 	=> '', // custom where is same as where field but you have to pass string where to this param
		'order' 		 	=> array(),// set order by in query 
		'group' 		 	=> '', // set group by in query
		'search' 		 	=> array(),// search field
		'action_link' 	 	=> array(	array('link'=>'edit/@id@','type'=>'edit'),array('link'=>'@id@','type'=>'delete')),
		'action_form' 	 	=> 'list', // list from element action
		'limit'          	=> 0, // limit for your select
		'limit_box'      	=> true, // limit & pagination box show state
		'before_delete'	 	=> 0, // before delete action
		'filter'	 	 	=> array(), // list filter array
		'filter_box'	 	=> true, // list filter box element show state
		'table_empty_first'	=> false, // showing table empty at loading the list
		'disable_form'		=> false, // disable to create form element for list
		'header_box'	 	=> true, // header box element show state
		'header_text'	 	=> ' به منظور اضافه کردن سطر جدید از طریق لینک زیر اقدام کنید', // header box text for helping string
		'header_button'	 	=> array(
			array('txt'=>'<i class="fa fa-plus"></i> اضافه کردن سطر جدید', 'color'=>'primary', 'url'=>$this->module_path),
			array('txt'=>'<i class="fa fa-trash"></i> حذف گروهی آیتم ها', 'color'=>'red', 'id'=>'delete-all', 'url'=>$this->module_path)
		),
		'field_checkbox'    => true, // showing checkbox first column of each row for selecting
		'footer_box'	 	=> false, // footer box element show state
		'footer_text'	 	=> '',// footer box text for helping string
		'footer_button'	 	=> array() 
	);
</pre>


# Helper Library

<p>The helper libraries can decrease coding time of developers in several situation like validation, timing compare and calculate and etc.</p>


## Tools Helper

<p>In this helper library we collected some method for helping to fast access to code and decrease coding time.</p>

## Validation Helper

<p>Validation helper is collection of variable validate, client data validation and etc that calculate by Regular Expression technology.</p>

## Persian Date Helper

<p>We forked a <a href="http://jdf.scr.ir" target="_blank">Persian Library</a> of date time converter for persian language in codeigniter.</p>
<p>We collected other methods like getting first or last day of the week or month and etc too.</p>



# Requirements

- <p>PHP version 5.6 or newer is recommended.</p>

- <p>Mcrypt extention for tools helper library.</p>


# Installation

<p>Install codeigniter 3.x from main source <a href="https://codeigniter.com/user_guide" target="_blank">User guide</a></p>
<p>After that copy and replace "application" and "assets" folders in main root of site.</p>
