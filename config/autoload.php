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
	'Netzmacht\DcaTools\DataContainer'                                          => 'system/modules/dcatools/DcaTools/DataContainer.php',
	'Netzmacht\DcaTools\Event\Event'                                            => 'system/modules/dcatools/DcaTools/Event/Event.php',
	'Netzmacht\DcaTools\Event\Config'                                           => 'system/modules/dcatools/DcaTools/Event/Config.php',
	'Netzmacht\DcaTools\Field'                                                  => 'system/modules/dcatools/DcaTools/Field.php',
	'Netzmacht\DcaTools\Model\DcGeneralModel'                                   => 'system/modules/dcatools/DcaTools/Model/DcGeneralModel.php',
	'Netzmacht\DcaTools\DcaTools'                                               => 'system/modules/dcatools/DcaTools/DcaTools.php',
	'Netzmacht\DcaTools\Button\Button'                                          => 'system/modules/dcatools/DcaTools/Button/Button.php',
	'Netzmacht\DcaTools\Button\ButtonEvent'                                     => 'system/modules/dcatools/DcaTools/Button/ButtonEvent.php',
	'Netzmacht\DcaTools\Button\ContaoCallback'                                  => 'system/modules/dcatools/DcaTools/Button/ContaoCallback.php',
	'Netzmacht\DcaTools\Palette\Legend'                                         => 'system/modules/dcatools/DcaTools/Palette/Legend.php',
	'Netzmacht\DcaTools\Palette\SubPalette'                                     => 'system/modules/dcatools/DcaTools/Palette/SubPalette.php',
	'Netzmacht\DcaTools\Palette\Palette'                                        => 'system/modules/dcatools/DcaTools/Palette/Palette.php',
	'Netzmacht\DcaTools\Node\FieldContainer'                                    => 'system/modules/dcatools/DcaTools/Node/FieldContainer.php',
	'Netzmacht\DcaTools\Node\Node'                                              => 'system/modules/dcatools/DcaTools/Node/Node.php',
	'Netzmacht\DcaTools\Node\Child'                                             => 'system/modules/dcatools/DcaTools/Node/Child.php',
	'Netzmacht\DcaTools\Node\FieldAccess'                                       => 'system/modules/dcatools/DcaTools/Node/FieldAccess.php',
	'Netzmacht\DcaTools\Node\Exportable'                                        => 'system/modules/dcatools/DcaTools/Node/Exportable.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_button' => 'system/modules/dcatools/templates',
));
