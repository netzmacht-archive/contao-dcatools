<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Dcatools
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'DcaTools',
));


/**
 * Register the classes
 */
NamespaceClassLoader::add('DcaTools', 'system/modules/dcatools');

/*
ClassLoader::addClasses(array
(
	// DcaTools
	'DcaTools\Helper'                               => 'system/modules/dcatools/DcaTools/Helper.php',
	'DcaTools\Event\EventDispatcher'                => 'system/modules/dcatools/DcaTools/Event/EventDispatcher.php',
	'DcaTools\Event\OperationListeners'             => 'system/modules/dcatools/DcaTools/Event/OperationListeners.php',
	'DcaTools\Event\OperationCallback'              => 'system/modules/dcatools/DcaTools/Event/OperationCallback.php',
	'DcaTools\Event\DataContainerListeners'         => 'system/modules/dcatools/DcaTools/Event/DataContainerListeners.php',
	'DcaTools\Event\Permissions'                    => 'system/modules/dcatools/DcaTools/Event/Permissions.php',
	'DcaTools\Model\ContaoModel'                    => 'system/modules/dcatools/DcaTools/Model/ContaoModel.php',
	'DcaTools\Definition\Operation'                 => 'system/modules/dcatools/DcaTools/Definition/Operation.php',
	'DcaTools\Definition\DataContainer'             => 'system/modules/dcatools/DcaTools/Definition/DataContainer.php',
	'DcaTools\Definition\Property'                  => 'system/modules/dcatools/DcaTools/Definition/Property.php',
	'DcaTools\Definition\Node'                      => 'system/modules/dcatools/DcaTools/Definition/Node.php',
	'DcaTools\Definition\Legend'                    => 'system/modules/dcatools/DcaTools/Definition/Legend.php',
	'DcaTools\Definition\PropertyContainer'         => 'system/modules/dcatools/DcaTools/Definition/PropertyContainer.php',
	'DcaTools\Definition\SubPalette'                => 'system/modules/dcatools/DcaTools/Definition/SubPalette.php',
	'DcaTools\Definition\Palette'                   => 'system/modules/dcatools/DcaTools/Definition/Palette.php',
	'DcaTools\Definition'                           => 'system/modules/dcatools/DcaTools/Definition.php',
	'DcaTools\Component\Operation'                  => 'system/modules/dcatools/DcaTools/Component/Operation.php',
	'DcaTools\Component\DataContainer'              => 'system/modules/dcatools/DcaTools/Component/DataContainer.php',
	'DcaTools\Component\Visual'                     => 'system/modules/dcatools/DcaTools/Component/Visual.php',
	'DcaTools\Component\Component'                  => 'system/modules/dcatools/DcaTools/Component/Component.php',
	'DcaTools\Structure\PropertyContainerInterface' => 'system/modules/dcatools/DcaTools/Structure/PropertyContainerInterface.php',
	'DcaTools\Structure\ExportInterface'            => 'system/modules/dcatools/DcaTools/Structure/ExportInterface.php',
	'DcaTools\Structure\OperationInterface'         => 'system/modules/dcatools/DcaTools/Structure/OperationInterface.php',
));
*/

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_operation' => 'system/modules/dcatools/templates',
));
