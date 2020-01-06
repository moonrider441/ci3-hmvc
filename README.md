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

- ***Smart asset** category and customize your asset* 

- ***Smart asset** category and customize your asset* 

- ***List Builder** build your filterable list with bootstrap* 

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
    - [Auto Save (Insert OR Update)](#automatic-save-insert-or-update)
    - [Manually Save (Insert OR Update)](#manually-save-insert-or-update)
    - [Delete data](#delete-data)
    - [MINI ORM installation](#mini-orm-installation)
- [PDM](#pdm)  
    - [Manage Header & Footer](#manage-header-&-footer)
    - [Manage Assets](#manage-assets)
    - [Ajax Requests](#ajax-requests)
    - [Rendering pages](#rendering-pages)
- [Smarty](#smarty)
    - [Source Smarty integration](#source-smarty-integration)
- [Multi Language Support](#multi-language-support)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
    - [Routes Setting](#routes-setting)
- [Resource Controllers](#resource-controllers)
    - [Build Methods](#build-methods)
    - [Custom Routes & Methods](#custom-routes--methods)
    - [Behaviors](#behaviors)
    - [Usage](#usage)
- [HTTP Request](#http-request)
    - [Usage](#usage-1)
- [HTTP Response](#http-response)
    - [Usage](#usage-2)
- [Reference](#reference)

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

<p>You need to set needle params in application directory "config.php" file.</p>

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



