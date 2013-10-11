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
	'Netzmacht',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// DcaTools
	'Netzmacht\DcaTools\Helper'                               => 'system/modules/dcatools/DcaTools/Helper.php',
	'Netzmacht\DcaTools\Event\EventDispatcher'                => 'system/modules/dcatools/DcaTools/Event/EventDispatcher.php',
	'Netzmacht\DcaTools\Event\OperationListeners'             => 'system/modules/dcatools/DcaTools/Event/OperationListeners.php',
	'Netzmacht\DcaTools\Event\OperationCallback'              => 'system/modules/dcatools/DcaTools/Event/OperationCallback.php',
	'Netzmacht\DcaTools\Event\DataContainerListeners'         => 'system/modules/dcatools/DcaTools/Event/DataContainerListeners.php',
	'Netzmacht\DcaTools\Event\Permissions'                    => 'system/modules/dcatools/DcaTools/Event/Permissions.php',
	'Netzmacht\DcaTools\Model\DcGeneralModel'                 => 'system/modules/dcatools/DcaTools/Model/DcGeneralModel.php',
	'Netzmacht\DcaTools\Definition\Operation'                 => 'system/modules/dcatools/DcaTools/Definition/Operation.php',
	'Netzmacht\DcaTools\Definition\DataContainer'             => 'system/modules/dcatools/DcaTools/Definition/DataContainer.php',
	'Netzmacht\DcaTools\Definition\Property'                  => 'system/modules/dcatools/DcaTools/Definition/Property.php',
	'Netzmacht\DcaTools\Definition\Node'                      => 'system/modules/dcatools/DcaTools/Definition/Node.php',
	'Netzmacht\DcaTools\Definition\Child'                     => 'system/modules/dcatools/DcaTools/Definition/Child.php',
	'Netzmacht\DcaTools\Definition\Legend'                    => 'system/modules/dcatools/DcaTools/Definition/Legend.php',
	'Netzmacht\DcaTools\Definition\PropertyContainer'         => 'system/modules/dcatools/DcaTools/Definition/PropertyContainer.php',
	'Netzmacht\DcaTools\Definition\SubPalette'                => 'system/modules/dcatools/DcaTools/Definition/SubPalette.php',
	'Netzmacht\DcaTools\Definition\Palette'                   => 'system/modules/dcatools/DcaTools/Definition/Palette.php',
	'Netzmacht\DcaTools\DcaTools'                             => 'system/modules/dcatools/DcaTools/DcaTools.php',
	'Netzmacht\DcaTools\Definition'                           => 'system/modules/dcatools/DcaTools/Definition.php',
	'Netzmacht\DcaTools\Component\Operation'                  => 'system/modules/dcatools/DcaTools/Component/Operation.php',
	'Netzmacht\DcaTools\Component\Component'                  => 'system/modules/dcatools/DcaTools/Component/Component.php',
	'Netzmacht\DcaTools\Structure\PropertyContainerInterface' => 'system/modules/dcatools/DcaTools/Structure/PropertyContainerInterface.php',
	'Netzmacht\DcaTools\Structure\ExportInterface'            => 'system/modules/dcatools/DcaTools/Structure/ExportInterface.php',
	'Netzmacht\DcaTools\Structure\OperationInterface'         => 'system/modules/dcatools/DcaTools/Structure/OperationInterface.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_operation' => 'system/modules/dcatools/templates',
));
