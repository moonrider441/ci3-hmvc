# codeigniter v3.x hmvc pro

CodeIgniter 3.x pro with HMVC (With ORM and PHP helber and main libraries for improve code optimisations)


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

# Installation Guide Hints:
-Steps 1-3 tell you how to get a standard CI install working - if you have a clean/tested CI install, skip to step 4.
-Steps 4-5 show that normal CI still works after installing MX - it shouldn’t interfere with the normal CI setup.
-Steps 6-8 show MX working alongside CI - controller moved to the “welcome” module, the view file remains in the CI application/views directory - MX can find module resources in several places, including the application directory.
-Steps 9-11 show MX working with both controller and view in the “welcome” module - there should be no files in the application/controllers or application/views directories.
