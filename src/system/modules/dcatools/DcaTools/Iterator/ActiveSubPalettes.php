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

use DcaTools\Definition\Palette;
use DcaTools\Definition\SubPalette;
use DcaTools\Definition;
use DcGeneral\Data\ModelInterface;


/**
 * Class ActiveSubPalettes
 * @package DcaTools\Iterator
 */
class ActiveSubPalettes implements \Iterator
{

	/**
	 * @var \DcGeneral\Data\ModelInterface
	 */
	protected $objModel;


	/**
	 * @var \DcaTools\Definition\Palette
	 */
	protected $objPalette;


	/**
	 * @var \ArrayIterator
	 */
	protected $objSelectorIterator;


	/**
	 * @var SubPalette
	 */
	protected $objCurrent;


	/**
	 * @param Palette $objPalette
	 * @param ModelInterface $objModel
	 */
	public function __construct(Palette $objPalette, ModelInterface $objModel)
	{
		$this->objModel = $objModel;
		$this->objPalette = $objPalette;
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	public function current()
	{
		return $this->objCurrent;
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next()
	{
		$this->objSelectorIterator->next();
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key()
	{
		return $this->objCurrent->getName();
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 */
	public function valid()
	{
		if(!$this->objSelectorIterator->valid())
		{
			return false;
		}

		do {
			$this->objCurrent = Definition::getActivePropertySubPalette($this->objSelectorIterator->current(), $this->objModel);

			if($this->objCurrent !== null)
			{
				return true;
			}

			$this->objSelectorIterator->next();
		}
		while($this->objSelectorIterator->valid());

		return false;
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind()
	{
		$this->objSelectorIterator = $this->objPalette->getSelectors();
	}


}