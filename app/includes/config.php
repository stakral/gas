<?php

/* This is original DB connection */

 // It's a bad practice to be allways as root!!!
 // Database Constants
 // if(!defined('DB_SERVER')) {define("DB_SERVER", "localhost");}
 // Below - the same thing but different format (ternary operator)
/* 
 // Settings for localhost.
 defined('DB_SERVER') ? null : define("DB_SERVER", "localhost");
 defined('DB_USER') ? null : define("DB_USER", "root");
 defined('DB_PASS') ? null : define("DB_PASS", "halamaga");
 defined('DB_NAME') ? null : define("DB_NAME", "spinea");
*/
/*
 // Settings for real LIVE server.
 defined('DB_SERVER') ? null : define("DB_SERVER", "mysql57.websupport.sk");
 defined('DB_USER')   ? null : define("DB_USER"  , "spinea");
 defined('DB_PASS')   ? null : define("DB_PASS"  , "halamaga");
 defined('DB_NAME')   ? null : define("DB_NAME"  , "spinea");
 defined('PORT') 	  ? null : define("PORT"	 , "3311");
*/




	/* PDO DB Connection */
	
	define('DB_TYPE', 	'mysql');
	define('DB_NAME',	'gas'); // NOTE: This constant is also used in "db_tables_whitelist" method of database_object.php!
	define('DB_HOST',	'localhost');
	define('DB_USER',	'root');
	define('DB_PASS',	'halamaga');
	define('DB_PORT',	'3311');
	define('DB_CHARSET','utf8');


	/* Constants for required coefficients. */
/*
	// Zučtovacie obdobie.
	define('SEASON', "2016-2017");
	// Datum oficialneho odpočtu stavu plynomera za predošlé obdobie.
	define('LAST_OFFICIAL_READING_DATE', "18-07-2016");
	// Hodnota oficialneho odpočtu stavu za predošlé obdobie.
	define('LAST_OFFICIAL_READING_VALUE', 7542);
*/
/*
	// Zučtovacie obdobie.
	define('SEASON', "2017-2018");
	// Datum oficialneho odpočtu stavu plynomera za predošlé obdobie.
	define('LAST_OFFICIAL_READING_DATE', "27-07-2017");
	// Hodnota oficialneho odpočtu stavu za predošlé obdobie.
	define('LAST_OFFICIAL_READING_VALUE', 10528.86);
*/
/*
	// Zučtovacie obdobie.
	define('SEASON', "2018-2019");
	// Datum oficialneho odpočtu stavu plynomera za predošlé obdobie.
	define('LAST_OFFICIAL_READING_DATE', "27-07-2018");
	// Hodnota oficialneho odpočtu stavu za predošlé obdobie.
	define('LAST_OFFICIAL_READING_VALUE', 13486.67);
*/
/*
	// Zučtovacie obdobie.
	define('SEASON', "2019-2020");
	// Datum oficialneho odpočtu stavu plynomera za predošlé obdobie.
	define('LAST_OFFICIAL_READING_DATE', "02-07-2019");
	// Hodnota oficialneho odpočtu stavu za predošlé obdobie.
	define('LAST_OFFICIAL_READING_VALUE', 16454.77);
*/

	// Zučtovacie obdobie.
	define('SEASON', "2020-2021");
	// Datum oficialneho odpočtu stavu plynomera za predošlé obdobie.
	define('LAST_OFFICIAL_READING_DATE', "02-07-2020");
	// Hodnota oficialneho odpočtu stavu za predošlé obdobie.
	define('LAST_OFFICIAL_READING_VALUE', 19241.00);


	// Objemové prepočítacie číslo (faktor nadmorskej vysky)
	define('LOCAL_SEA_LEVEL_COEFFICIENT', 1.003);
	// Priemerné spaľovacie teplo objemové (na m3)
	define('AVERAGE_COMBUSTION_HEAT_VOLUME', 10.773);

	// Jednotkova cena za kWh - variabilná zložka.
	define('D3_UNIT_PRICE_kWh', 0.0239);
	// Distribucia - variabilna zlozka.
	define('D3_DISTRIBUTION_FEE_PER_kWh', 0.0092);
	// Preprava - variabilná zložka .
	define('D3_TRANSPORT_FEE_PER_kWh', 0.0028);

	// Stála mesačná platba za dodávku.
	define('D3_MONTHLY_DELIVERY_FEE', 12 * 1.00);
	// Stala mesačná platba za distribuciu.
	define('D3_MONTHLY_DISTRIBUTION_FEE', 12 * 7.64);


/*
$config = [

	'db' => [
		'type'     => 'mysql',
		'name'     => 'spinea',
		'server'   => 'localhost',
		'username' => 'root',
		'password' => 'halamaga',
		'charset'  => 'utf8',
		'port'	   => '3311' // This was commented in miniblog app.
	]
];
*/

	// SET @@sql_mode = CASE CURRENT_USER()
 //                             WHEN 'mini_blog@%' THEN 'NO_AUTO_VALUE_ON_ZERO'
 //                             ELSE @@sql_mode 
 //                             END;

// connect to db
/*
$db = new PDO(
	"{$config['db']['type']}:host={$config['db']['server']};
	dbname={$config['db']['name']};charset={$config['db']['charset']};port={$config['db']['port']}",
	$config['db']['username'], $config['db']['password']
);

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Use true only for older versions (<5) of MyQSL. 

*/


?>