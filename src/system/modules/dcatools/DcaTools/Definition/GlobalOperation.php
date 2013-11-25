<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   netzmacht-dcatools
 * @author    netzmacht creative David Molineus
 * @license   LGPL/3.0
 * @copyright 2013 netzmacht creative David Molineus
 */

namespace DcaTools\Definition;

use DcaTools\Definition;


/**
 * Class Operation provides a generic class which allows to define multiple events being triggered when a button component
 * of Contao DCA is generated. The button will be loaded using Definition::buttonCallback
 *
 * Following events are supported:
 *  - initialize:   called in the constructor
 *  - validate:     Can set the button as disabled or hidden.
 *  - generate:     Called when button is generated, use this for influencing the output.
 *
 * @package DcaTools\Operation
 */
class GlobalOperation extends AbstractOperation
{

	/**
	 * Constructor
	 * @param string $strName
	 * @param DataContainer $objDataContainer
	 */
	public function __construct($strName, DataContainer $objDataContainer)
	{
		$definition =& $objDataContainer->getDefinition();

		parent::__construct($strName, $objDataContainer, $definition['list']['global_operations'][$strName]);
	}


	/**
	 * Remove child from parent
	 *
	 * @return $this
	 */
	public function remove()
	{
		$this->getDataContainer()->removeGlobalOperation($this);

		return $this;
	}


	/**
	 * Prepare argument so that an array of name and the object is passed
	 *
	 * @param DataContainer $objReference
	 * @param Legend|string $node
	 * @param bool $blnNull return null if property does not exists
	 *
	 * @return array[string|Legend|null]
	 */
	public static function argument(DataContainer $objReference, $node, $blnNull=true)
	{
		return static::prepareArgument($objReference, $node, $blnNull, 'GlobalOperation');
	}

}