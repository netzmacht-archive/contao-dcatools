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

namespace DcaTools\Iterator;

use DcaTools\Definition;
use DcaTools\Structure\ExportInterface;
use DcaTools\Structure\PropertyContainerInterface;
use DcGeneral\Data\ModelInterface;


/**
 * Class ActiveProperty
 * @package DcaTools\Iterator
 */
class ActiveProperties extends \ArrayIterator implements ExportInterface
{

	/**
	 * @var \DcaTools\Structure\PropertyContainerInterface
	 */
	protected $objContainer;

	/**
	 * @var \DcGeneral\Data\ModelInterface
	 */
	protected $objModel;

	/**
	 * @var \DcaTools\Definition\Property
	 */
	protected $objCurrent;

	/**
	 * @var bool
	 */
	protected $blnRecursive;

	/**
	 * @var \ArrayIterator
	 */
	protected $objInnerIterator;


	/**
	 * @param PropertyContainerInterface $objContainer
	 * @param ModelInterface $objModel
	 * @param bool $blnRecursive
	 */
	public function __construct(PropertyContainerInterface $objContainer, ModelInterface $objModel, $blnRecursive=false)
	{
		parent::__construct($objContainer->getProperties());

		$this->objContainer = $objContainer;
		$this->objModel = $objModel;
		$this->blnRecursive = $blnRecursive;
	}


	/**
	 * Get next Property
	 */
	public function next()
	{
		if($this->objInnerIterator === null)
		{
			$objSubPalette = Definition::getActivePropertySubPalette($this->objCurrent, $this->objModel);

			if($objSubPalette !== null)
			{
				if($this->blnRecursive)
				{
					$this->objInnerIterator = new static($objSubPalette, $this->objModel, $this->blnRecursive);
				}
				else
				{
					$this->objInnerIterator = new \ArrayIterator($objSubPalette->getProperties());
				}

				$this->objInnerIterator->rewind();
			}
		}

		// no sub palette given
		if($this->objInnerIterator === null)
		{
			parent::next();
		}
		else {
			$this->objInnerIterator->next();
		}
	}


	/**
	 * Validate current
	 *
	 * @return bool
	 */
	public function valid()
	{
		if($this->objInnerIterator !== null)
		{
			if($this->objInnerIterator->valid())
			{
				return true;
			}

			// is not valid anymore so unset and go to next value
			unset($this->objInnerIterator);
			$this->next();
		}

		return parent::valid();
	}


	/**
	 * Get current property
	 *
	 * @return Definition\Property|mixed
	 */
	public function current()
	{
		if($this->objInnerIterator === null)
		{
			return parent::current();
		}

		return $this->objInnerIterator->current();
	}


	/**
	 * @return mixed
	 */
	public function asString()
	{
		/** @var PropertyContainerInterface $strClass */
		$strClass = get_class($this->objContainer);
		return $strClass::convertToString($this);
	}


	/**
	 * @return mixed
	 */
	public function asArray()
	{
		/** @var PropertyContainerInterface $strClass */
		$strClass = get_class($this->objContainer);
		return $strClass::convertToArray($this);
	}

}