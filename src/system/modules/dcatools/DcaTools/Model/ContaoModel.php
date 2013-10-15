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

namespace DcaTools\Model;

use DcGeneral\Data\DefaultModel;
use DcGeneral\Data\ModelInterface;
use Traversable;

/**
 * Class ContaoModel is a wrapper for Contao Models, Results or row array, so that they fit API of Dc_General
 *
 * @package DcaTools\Model
 */
class ContaoModel implements ModelInterface
{

	/**
	 * @var \Model|\Database\Result
	 */
	protected $objModel;


	/**
	 * @var string
	 */
	protected $strIdColumn;


	/**
	 * @param $objModel
	 * @param string $strIdColumn
	 */
	public function __construct($objModel, $strIdColumn='id')
	{
		if($objModel instanceof \Model\Collection)
		{
			$objModel = $objModel->current();
		}
		elseif($objModel instanceof \Database\Result)
		{
			$this->strIdColumn = $strIdColumn;
		}

		$this->objModel = $objModel;
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Retrieve an external iterator
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return Traversable An instance of an object implementing <b>Iterator</b> or
	 * <b>Traversable</b>
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->objModel->row());
	}


	/**
	 * @return \Database\Result|\Model
	 */
	public function getModel()
	{
		return $this->objModel;
	}


	/**
	 * Get the id for this model.
	 *
	 * @return mixed The Id for this model.
	 */
	public function getId()
	{
		$strId = $this->strIdColumn === null ? $this->objModel->getPk() : $this->strIdColumn;
		return $this->objModel->$strId;
	}


	/**
	 * Fetch the property with the given name from the model.
	 *
	 * This method returns null if an unknown property is retrieved.
	 *
	 * @param string $strPropertyName The property name to be retrieved.
	 *
	 * @return mixed The value of the given property.
	 */
	public function getProperty($strPropertyName)
	{
		return $this->objModel->$strPropertyName;
	}


	/**
	 * Fetch all properties from the model as an name => value array.
	 *
	 * @return array
	 */
	public function getPropertiesAsArray()
	{
		return $this->objModel->row();
	}


	/**
	 * Fetch meta information from model.
	 *
	 * @param string $strMetaName The meta information to retrieve.
	 *
	 * @return mixed The set meta information or null if undefined.
	 */
	public function getMeta($strMetaName)
	{
		return null;
	}


	/**
	 * Set the id for this object.
	 *
	 * NOTE: when the Id has been set once to a non null value, it can NOT be changed anymore.
	 *
	 * Normally this should only be called from inside of the implementing provider.
	 *
	 * @param mixed $mixId Could be a integer, string or anything else - depends on the provider implementation.
	 *
	 * @return void
	 */
	public function setId($mixId)
	{
		$strId = $this->strIdColumn === null ? $this->objModel->getPk() : $this->strIdColumn;
		$this->objModel->$strId = $mixId;
	}


	/**
	 * Update the property value in the model.
	 *
	 * @param string $strPropertyName
	 *
	 * @param mixed $varValue
	 *
	 * @return void
	 */
	public function setProperty($strPropertyName, $varValue)
	{
		$this->objModel->$strPropertyName = $varValue;
	}


	/**
	 * Update all properties in the model.
	 *
	 * @param array $arrProperties The property values as name => value pairs.
	 *
	 * @return void
	 */
	public function setPropertiesAsArray($arrProperties)
	{
		foreach($arrProperties as $strName => $varValue)
		{
			$this->setProperty($strName, $varValue);
		}
	}


	/**
	 * Update meta information in the model.
	 *
	 * @param string $strMetaName The meta information name.
	 *
	 * @param mixed $varValue    The meta information value to store.
	 *
	 * @return void
	 */
	public function setMeta($strMetaName, $varValue)
	{
		return null;
	}


	/**
	 * Check if this model have any properties.
	 *
	 * @return boolean true if any property has been stored, false otherwise.
	 */
	public function hasProperties()
	{
		$arrRow = $this->objModel->row();
		return !empty($arrRow);
	}


	/**
	 * Return the data provider name.
	 *
	 * @return string the name of the corresponding data provider.
	 *
	 * @throws \RuntimeException
	 */
	public function getProviderName()
	{
		throw new \RuntimeException('Not supported');
	}


	/**
	 * Copy this model, without the id.
	 *
	 * @return ModelInterface
	 */
	public function __clone()
	{
		$this->objModel = clone $this->objModel;
	}

}