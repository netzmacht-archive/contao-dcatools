<?php

// Using dcatools for DcGeneral driven datacontainers use just need to defines your conditions
// No callback enabling or legacy mode needed
// Example for Metamodel mm_test
$GLOBALS['TL_DCA']['mm_test']['dcatools'] = array
(
	'command_conditions' => array
	(
		array
		(
			'condition' => 'disable',
			'config'    => array('condition' => 'isAdmin', 'inverse' => true),
			'filter'    => 'delete',
		),
	)
);