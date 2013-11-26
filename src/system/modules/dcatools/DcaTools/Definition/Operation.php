<?php

/**
 * DcaTools - Toolkit for data containers in Contao
 * Copyright (C) 2013 David Molineus
 *
 * @package   netzmacht-dcatools
 * @author    David Molineus <molineus@netzmacht.de>
 * @license   LGPL-3.0+
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
class Operation extends AbstractOperation
{

	/**
	 * Constructor
	 * @param string $strName
	 * @param DataContainer $objDataContainer
	 */
	public function __construct($strName, DataContainer $objDataContainer)
	{
		$definition =& $objDataContainer->getDefinition();

		parent::__construct($strName, $objDataContainer, $definition['list']['operations'][$strName]);
	}


	/**
	 * Remove child from parent
	 *
	 * @return $this
	 */
	public function remove()
	{
		$this->getDataContainer()->removeOperation($this);

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
		return static::prepareArgument($objReference, $node, $blnNull, 'Operation');
	}

}