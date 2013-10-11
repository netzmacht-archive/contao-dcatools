<?php


$GLOBALS['TL_DCA']['tl_example'] = array
(
	'dcatools' => array
	(
		'permissions' => array
		(
			array('\Netzmacht\DcaTools\Event\DataContainerPermissions', 'hasAccess', array('module' => 'test')),
			array('\Netzmacht\DcaTools\Event\DataContainerPermissions', 'isAdmin'),
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
						array('\Netzmacht\DcaTools\Event\OperationPermissions', 'isAdmin'),
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