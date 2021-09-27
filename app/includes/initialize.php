<?php
header('Content-Type: text/html; charset=utf-8');


// Errors
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
// error_reporting(-1); // show all errors
error_reporting(E_ALL & ~E_NOTICE); // show all errors execept NOTICE Lesson 107.


// Define the core paths
// Define them as absolute paths to make sure that require_once works as expected

// DIRECTORY_SEPARATOR is a PHP pre-defined constant
// (\ for Windows, / for Unix)
defined('DS') ? null :
	define('DS', DIRECTORY_SEPARATOR);

defined('BASE_URL') ? null : 
	define( 'BASE_URL', 'http://'.$_SERVER['SERVER_NAME'].DS.'gas'.DS.'app' );

defined('SITE_ROOT') ? null : 
	define('SITE_ROOT', DS.'Users'.DS.'kingus'.DS.'Sites'.DS.'gas'.DS.'app');

defined('LIB_PATH') ? null : 
	define('LIB_PATH', SITE_ROOT.DS.'includes');

// load config file first
require_once(LIB_PATH.DS.'config.php');

// load basic functions next so that everything after can use them
require_once(LIB_PATH.DS.'functions.php');
require_once(LIB_PATH.DS.'validation_functions.php');
require_once(LIB_PATH.DS.'gas_functions.php');

// load core objects
require_once(LIB_PATH.DS.'session.php');
require_once(LIB_PATH.DS.'database.php');
require_once(LIB_PATH.DS.'database_object.php');

// Scripts like JS and jQuery
//require_once(SITE_ROOT.DS.'public'.DS.'layouts'.DS.'scripts.php');
?>