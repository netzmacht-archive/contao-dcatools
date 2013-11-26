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

namespace DcaTools\Iterator;

use DcaTools\Definition\Palette;
use DcaTools\Definition\Property;
use DcaTools\Structure\PropertyContainerInterface;
use DcGeneral\Data\ModelInterface;
use RecursiveIterator;

/**
 * Class ActivePalette
 * @package DcaTools\Iterator
 */
class ActivePropertyContainer implements \RecursiveIterator
{

	/**
	 * @var Palette
	 */
	protected $objPalette;

	/**
	 * @var \DcGeneral\Data\ModelInterface
	 */
	protected $objModel;

	/**
	 * @var \ArrayIterator|Property[]
	 */
	protected $objIterator;

	/**
	 * @var bool
	 */
	protected $blnRecursive;


	/**
	 * Constructor
	 *
	 * @param PropertyContainerInterface $objContainer
	 * @param ModelInterface $objModel
	 * @param bool $blnRecursive
	 */
	public function __construct(PropertyContainerInterface $objContainer, ModelInterface $objModel, $blnRecursive=true)
	{
		$this->objModel = $objModel;
		$this->objContainer = $objContainer;
		$this->objIterator = $this->objContainer->getIterator();
		$this->blnRecursive = $blnRecursive;
	}


	/**
	 * @return ModelInterface
	 */
	public function getModel()
	{
		return $this->objModel;
	}


	/**
	 * @return Palette
	 */
	public function getPropertyContainer()
	{
		return $this->getPropertyContainer();
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return Property
	 */
	public function current()
	{
		return $this->objIterator->current();
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next()
	{
		$this->objIterator->next();
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key()
	{
		return $this->objIterator->key();
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
		return $this->objIterator->valid();
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind()
	{
		$this->objIterator->rewind();
	}


	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Returns if an iterator can be created for the current entry.
	 * @link http://php.net/manual/en/recursiveiterator.haschildren.php
	 *
	 * @return bool true if the current entry can be iterated over, otherwise returns false.
	 */
	public function hasChildren()
	{
		$objProperty = $this->current();

		if($this->blnRecursive && $objProperty->isSelector())
		{
			$varValue = $this->objModel->getProperty($objProperty->getName());

			if($objProperty->hasSubPalette($varValue))
			{
				$arrProperties = $objProperty->getSubPalette($varValue)->getProperties();
				return !empty($arrProperties);
			}
		}

		return false;
	}


	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Returns an iterator for the current entry.
	 * @link http://php.net/manual/en/recursiveiterator.getchildren.php
	 * @return RecursiveIterator An iterator for the current entry.
	 */
	public function getChildren()
	{
		$objProperty = $this->current();
		$varValue = $this->objModel->getProperty($objProperty->getName());

		return new ActivePropertyContainer($objProperty->getSubPalette($varValue), $this->objModel);
	}

}