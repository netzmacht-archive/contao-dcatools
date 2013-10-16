<?php


$GLOBALS['TL_DCA']['tl_example'] = array
(
	'dcatools' => array
	(
		'events' => array
		(
			'permissions' => array
			(
				array('DcaTools\Event\DataContainerPermissions', 'hasAccess', array('module' => 'test')),
				array('DcaTools\Event\DataContainerPermissions', 'isAdmin'),
			),

			'getAlowedEntries' => array
			(
				array('DcaTools\DataContainer\Content', 'getAllowedEntries'),
			),

			'getAllowedIds' => array
			(
				array('DcaTools\DataContainer\Content', 'getAllowedIds'),
			),

			'getAllowedDynamicParents' => array
			(
				array('DcaTools\DataContainer\Content', 'getAllowedDynamicParents')
			),
		),

		'operations' => array
		(
			'edit' => array
			(
				array('Netzmacht\FontAwesome\FontAwesome', 'generateOperation'),
			),
		),

		'global_operations' => array
		(

		),
	),


	'list' => array
	(
		'operations' => array
		(
			'edit' => array
			(
				'label' => $GLOBALS['TL_LANG']['tl_example']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif',
			),
		)
	)

);