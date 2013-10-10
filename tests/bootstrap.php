<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('TL_MODE', 'TEST');

// load Contao
//require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/system/initialize.php';
require_once '/var/www/dev/3.1/system/initialize.php';