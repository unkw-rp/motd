<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ MAIN SETTINGS ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

$main_site = 'http://localhost/'; // Where to redirect anyone to after clicking "Back to site" button of the home page. (forum, home site etc.)
$main_url = 'http://localhost/web/'; // The slash at the end is important!
$main_name = 'Rank System Ultimate';
$default_order = '13'; // How to order the top players by default in the site. (0 = XP | 1 = Nick | 2 = Kills | 3 = Assists | 4 = Deaths | 5 = Skill | 6 = Headshots | 7 = Bombs Planted | 8 = Bombs Exploded | 9 = Bombs Defused | 10 = Rounds Won | 11 = MVP | 12 = Rank | 13 = Overall)

define('DB_HOST', '127.0.0.1:3307');
define('DB_NAME', 'mysql');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHAR', 'utf8'); // latin1 or utf8

// To add more servers, simply copy and paste the array line on a new line with new values.
$servers = array(
	array('ip' => 'IP', 	'port' =>	'PORT', 	'mysql_table1' => 'rank_system', 	'mysql_table2' => 'weapon_kills'),
);

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ CUSTOMIZABLES ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// The colors are ordered by descending order, aka the highest player statuses should be at the top.
// You can use hex values such as #ff0000, #bf0000, #000000 and so on...
$default_name_color = 'white';
$name_colors = array(
	array('flags' => 'abcdefghij',	'color' => 'red'),
	array('flags' => 'acd',			'color' => '#32cd32'), // lime
	array('flags' => 'b',			'color' => 'orange'),

);

// You can use hex values such as #ff0000, #bf0000, #000000 and so, so on...
$default_skill_color = '#b8a365';
$skill_colors = array(
	array('skill' => 'M+',	'color' => '	red'),
	array('skill' => 'M',	'color' => '	red'),
	array('skill' => 'M-',	'color' => '	red'),
	array('skill' => 'E+',	'color' => '	#93C572'),
	array('skill' => 'E',	'color' => '	#93C572'),
	array('skill' => 'E-',	'color' => '	#93C572'),
	array('skill' => 'V+',	'color' => '	#f0ac02'),
	array('skill' => 'V',	'color' => '	#f0ac02'),
	array('skill' => 'V-',	'color' => '	#f0ac02'),
	array('skill' => 'A+',	'color' => '	#ad9f49'),
	array('skill' => 'A',	'color' => '	#ad9f49'),
	array('skill' => 'A-',	'color' => '	#ad9f49'),
	array('skill' => 'N+',	'color' => '	#d1bd97'),
	array('skill' => 'N',	'color' => '	#d1bd97'),
	array('skill' => 'N-',	'color' => '	#d1bd97'),
);?>