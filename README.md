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

- ***Smarty** strong, popular and easy template engine* 

- ***Multi Language Support** translate to any lannguage you like* 

- ***List Builder** build your filterable list with bootstrap* 

- ***Helper Library** fast access to anything* 

---

OUTLINE
-------

- [Modular Extensions - HMVC](#Modular-Extensions---HMVC)
    - [Modular Extensions installation](#Modular-Extensions-installation)
    - [Source Modular Extensions](#Source-Modular-Extensions)
- [MINI ORM](#MINI-ORM)
    - [Definition](#Definition)
    - [Query Builder](#Query-Builder)
    - [Validate data](#Validate-data)
    - [Automatic Save (Insert OR Update)](#packed-standard-format)
    - [Delete](#Delete)
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

# Modular Extensions installation
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

## Source Modular Extensions
<a href="https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc/" target="_blank">Wiredesignz</a>


## Installation Guide Hints:
-Steps 1-3 tell you how to get a standard CI install working - if you have a clean/tested CI install, skip to step 4.

-Steps 4-5 show that normal CI still works after installing MX - it shouldn’t interfere with the normal CI setup.

-Steps 6-8 show MX working alongside CI - controller moved to the “welcome” module, the view file remains in the CI application/views directory - MX can find module resources in several places, including the application directory.

-Steps 9-11 show MX working with both controller and view in the “welcome” module - there should be no files in the application/controllers or application/views directories.


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
you can get any model data by passing array params
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
        
        $this->get_rows('ci_user t1', $params);
        
        // or get data by write your query
        $this->query('SELECT user_name FROM ci_user WHERE ...');
        
        //get last insert id
        $this->insert_id();
        
        // get count of data in table
        $this->count_all('user_table');
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

## Automatic Save (Insert OR Update)
you can insert your data posted from form or update the user row by identify ident
- First you have to call user model and run initiallize method for check insert or update mode in your controller.
-

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
</pre>


