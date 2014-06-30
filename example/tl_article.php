<?php

use DcaTools\Dca\Legacy\Callback;

$GLOBALS['TL_DCA']['tl_article']['dcatools'] = array
(
	// If your Dca uses a legacy driver (DC_Table, DC_Folder, DC_Files based) you have to set legacy to true
	// This crates an instance of the DcGeneral and allows you to enable the callback to event dispatching
	'legacy' => true,

	// If DcaTools is used for legacy drivers you have to enable every callback being dispatched to events.
	// So you can avoid eventually problems with 3rd party code.
	// If the callback can be defined for different elements (button callbacks or property callbacks), you have
	// to define the name of the children in an array as well. If you want to enable it for all elements use the
	// wildcard '*'
	'callbacks' => array(
		Callback::MODEL_OPERATION_BUTTON => '*',
	),

	// Command conditions influences the rendering of a command button. DcaTools provides two conditions being
	// inteded to change the button: disable and hide. They can be configured to match your needs. Sub conditions
	// like isAdmin can be used
	'command_conditions' => array
	(
		array
		(
			'condition' => 'hide',
			'config'	=> array('always' => true),
			'filter'	=> array('show', 'cut', 'delete'),
		),
		array
		(
			'condition' => 'disable',
			'config'    => array('condition' => 'isNotAdmin'),
			'filter'    => '*',
		),
	),
	'global_command_conditions' => array
	(
	),
	// Permission conditions avoids access to the current data record.
	'permission_conditions' => array(
		array(
			'condition' => 'isAdmin',
		)
	),
);