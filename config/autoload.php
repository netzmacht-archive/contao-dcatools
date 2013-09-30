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
	'Symfony',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// DcaTools
	'Netzmacht\DcaTools\DataContainer'                                          => 'system/modules/dcatools/DcaTools/DataContainer.php',
	'Netzmacht\DcaTools\Field'                                                  => 'system/modules/dcatools/DcaTools/Field.php',
	'Netzmacht\DcaTools\Model\DcGeneralModel'                                   => 'system/modules/dcatools/DcaTools/Model/DcGeneralModel.php',
	'Netzmacht\DcaTools\DcaTools'                                               => 'system/modules/dcatools/DcaTools/DcaTools.php',
	'Netzmacht\DcaTools\Palette\Legend'                                         => 'system/modules/dcatools/DcaTools/Palette/Legend.php',
	'Netzmacht\DcaTools\Palette\SubPalette'                                     => 'system/modules/dcatools/DcaTools/Palette/SubPalette.php',
	'Netzmacht\DcaTools\Palette\Palette'                                        => 'system/modules/dcatools/DcaTools/Palette/Palette.php',
	'Netzmacht\DcaTools\Node\FieldContainer'                                    => 'system/modules/dcatools/DcaTools/Node/FieldContainer.php',
	'Netzmacht\DcaTools\Node\Node'                                              => 'system/modules/dcatools/DcaTools/Node/Node.php',
	'Netzmacht\DcaTools\Node\Child'                                             => 'system/modules/dcatools/DcaTools/Node/Child.php',
	'Netzmacht\DcaTools\Node\Exportable'                                        => 'system/modules/dcatools/DcaTools/Node/Exportable.php',

	// Vendor
	'Symfony\Component\EventDispatcher\Event'                                   => 'system/modules/dcatools/vendor/EventDispatcher-2.3.5/Event.php',
	'Symfony\Component\EventDispatcher\ImmutableEventDispatcher'                => 'system/modules/dcatools/vendor/EventDispatcher-2.3.5/ImmutableEventDispatcher.php',
	'Symfony\Component\EventDispatcher\EventDispatcherInterface'                => 'system/modules/dcatools/vendor/EventDispatcher-2.3.5/EventDispatcherInterface.php',
	'Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher'           => 'system/modules/dcatools/vendor/EventDispatcher-2.3.5/ContainerAwareEventDispatcher.php',
	'Symfony\Component\EventDispatcher\EventDispatcher'                         => 'system/modules/dcatools/vendor/EventDispatcher-2.3.5/EventDispatcher.php',
	'Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcherInterface' => 'system/modules/dcatools/vendor/EventDispatcher-2.3.5/Debug/TraceableEventDispatcherInterface.php',
	'Symfony\Component\EventDispatcher\Tests\ContainerAwareEventDispatcherTest' => 'system/modules/dcatools/vendor/EventDispatcher-2.3.5/Tests/ContainerAwareEventDispatcherTest.php',
	'Symfony\Component\EventDispatcher\Tests\GenericEventTest'                  => 'system/modules/dcatools/vendor/EventDispatcher-2.3.5/Tests/GenericEventTest.php',
	'Symfony\Component\EventDispatcher\Tests\ImmutableEventDispatcherTest'      => 'system/modules/dcatools/vendor/EventDispatcher-2.3.5/Tests/ImmutableEventDispatcherTest.php',
	'Symfony\Component\EventDispatcher\Tests\EventDispatcherTest'               => 'system/modules/dcatools/vendor/EventDispatcher-2.3.5/Tests/EventDispatcherTest.php',
	'Symfony\Component\EventDispatcher\Tests\EventTest'                         => 'system/modules/dcatools/vendor/EventDispatcher-2.3.5/Tests/EventTest.php',
	'Symfony\Component\EventDispatcher\EventSubscriberInterface'                => 'system/modules/dcatools/vendor/EventDispatcher-2.3.5/EventSubscriberInterface.php',
	'Symfony\Component\EventDispatcher\GenericEvent'                            => 'system/modules/dcatools/vendor/EventDispatcher-2.3.5/GenericEvent.php',
));
