<?php

require_once("includes/initialize.php");
/* 
echo "<pre>";
print_r($_SERVER);
print_r(get_segments());
echo "</pre>";
 */
$routes = [


	// HOME
	'/home' => [
		'POST' => 'public/home.php',
		'GET'  => 'public/home.php'
	],

	// ADD GAS METER STATE
	'/add-gasmeter-state' => [
		'POST' => 'public/add_gasmeter_state.php',
		'GET'  => 'public/add_gasmeter_state.php'
	],

	// SEASON CURRENT
	'/season-current' => [
		'POST' => 'public/season_current.php',
		'GET'  => 'public/season_current.php'
	],

	// SEASON ADD
	'/season-add' => [
		'POST' => 'public/season_add.php',
		'GET'  => 'public/season_add.php'
	],

	// SEASON ADD CHANGE
	'/season-add-change' => [
		'POST' => 'public/season_add_change.php',
		'GET'  => 'public/season_add_change.php'
	],


	/* LOGIN AND LOGOUT */

	// LOGIN
	'/login' => [
		'POST' => 'public/admin/login.php',
		'GET'  => 'public/admin/login.php'
	],

	'/' => [
		'POST' => 'public/admin/login.php',
		'GET'  => 'public/admin/login.php'
	],

	// LOGOUT
	'/logout' => [
		'POST' => 'public/admin/logout.php',
		'GET'  => 'public/admin/logout.php'
	],


	/* ROUTES FOR "APPLICTION SETTINGS" MENU SECTION */

	// APPLICTION SETTINGS
	'/settings' => [
		'GET' => 'public/settings.php'
	],

	// USERS MANAGE
	'/users-manage' => [
		'POST' => 'public/users_manage.php',
		'GET'  => 'public/users_manage.php'
	],

	// USER EDIT
	'/user-edit' => [
		'POST' => 'public/user_edit.php',
		'GET'  => 'public/user_edit.php'
	],

	// USER ADD
	'/user-add' => [
		'POST' => 'public/user_add.php',
		'GET'  => 'public/user_add.php'
	]
];


$page   = segment(1);
$method = $_SERVER['REQUEST_METHOD'];

// PAGES WITHOUT NAVIGATION BAR
$pages_without_header = array('', 'login'); // print_r($pages_without_header);


if (!in_array($page, $pages_without_header) && isset($routes["/$page"][$method])) {
	include_layout_template('header.php');
	include_layout_template('nav_bar.php');
} else {
	// The rest of pages i.e. "user_delete.php" will not be added header and nav_bar.
}


if (!isset($routes["/$page"][$method])) {
	show_404();
}

require $routes["/$page"][$method];
