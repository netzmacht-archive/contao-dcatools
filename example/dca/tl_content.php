<?php


$GLOBALS['TL_DCA']['tl_example'] = array
(
	'dcatools' => array
	(
		'permissions' => array
		(
			array('DcaTools\Event\DataContainerPermissions', 'hasAccess', array('module' => 'test')),
			array('DcaTools\Event\DataContainerPermissions', 'isAdmin'),
		),

		'operationListeners' => true,
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
				'events' => array
				(
					'permissions' => array
					(
						array('DcaTools\Event\OperationPermissions', 'isAdmin'),
					),

					'generate' => array
					(
						array('Netzmacht\FontAwesome\FontAwesome', 'generateOperation'),
					),
				),
			),
		)
	)

);