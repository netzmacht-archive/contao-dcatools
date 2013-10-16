<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('TL_MODE', 'TEST');

// load Contao
//require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/system/initialize.php';
require_once '/var/www/dev/3.1/system/initialize.php';

$TEST_DCA = array
(
	'config' => array
	(
	),

	'list' => array
	(
		'operations' => array
		(
			'edit' => array
			(
				'label' => array('Edit', 'label'),
				'href' => 'act=edit',
				'icon' => 'edit.png',
			)
		),
		'global_operations' => array
		(
			'sub' => array
			(
				'label' => array('Edit', 'label'),
				'href' => 'table=tl_sub',
				'icon' => 'delete.png',
			)
		),
	),

	'palettes' => array
	(
		'__selector__' => array('done'),
		'default' => '{title_legend},test',
	),

	'subpalettes' => array
	(
		'sub' => 'test',
	),

	'fields' => array
	(
		'test' => array
		(
			'label' => array('Test', 'Label'),
			'search' => false,
			'sorting' => true,
			'filter' => true,
			'inputType' => 'text',
			'eval' => array('tl_class'),
		),

		'done' => array
		(
			'label' => array('Get', 'it done'),
			'inputType' => 'checkbox',
		)
	)
);