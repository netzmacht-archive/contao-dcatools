<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Data;


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\Data\PropertyValueBagInterface;
use ContaoCommunityAlliance\DcGeneral\Exception\DcGeneralInvalidArgumentException;
use Traversable;

class ActiveRecordModelDecorator implements ModelInterface
{
	/**
	 * @var \Database\Result
	 */
	private $activeRecord;

	/**
	 * @var array
	 */
	private $meta = array();

	/**
	 * @var
	 */
	private $providerName;


	/**
	 * @param $activeRecord
	 * @param $providerName
	 */
	function __construct($activeRecord, $providerName)
	{
		$this->activeRecord = $activeRecord;
		$this->providerName = $providerName;
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
		return new \ArrayIterator($this->getPropertiesAsArray());
	}

	/**
	 * Get the id for this model.
	 *
	 * @return mixed The Id for this model.
	 */
	public function getId()
	{
		return $this->activeRecord->id;
	}

	/**
	 * Fetch the property with the given name from the model.
	 *
	 * This method returns null if an unknown property is retrieved.
	 *
	 * @param string $propertyName The property name to be retrieved.
	 *
	 * @return mixed The value of the given property.
	 */
	public function getProperty($propertyName)
	{
		return $this->activeRecord->$propertyName;
	}

	/**
	 * Fetch all properties from the model as an name => value array.
	 *
	 * @return array
	 */
	public function getPropertiesAsArray()
	{
		return $this->activeRecord->row();
	}

	/**
	 * Fetch meta information from model.
	 *
	 * @param string $metaName The meta information to retrieve.
	 *
	 * @return mixed The set meta information or null if undefined.
	 */
	public function getMeta($metaName)
	{
		if(isset($this->meta[$metaName])) {
			return $this->meta[$metaName];
		}

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
		$this->activeRecord->id = $mixId;
	}

	/**
	 * Update the property value in the model.
	 *
	 * @param string $propertyName The property name to be set.
	 *
	 * @param mixed $value The value to be set.
	 *
	 * @return void
	 */
	public function setProperty($propertyName, $value)
	{
		$this->activeRecord->$propertyName = $value;
	}

	/**
	 * Update all properties in the model.
	 *
	 * @param array $properties The property values as name => value pairs.
	 *
	 * @return void
	 */
	public function setPropertiesAsArray($properties)
	{
		foreach($properties as $name => $value) {
			$this->setProperty($name, $value);
		}
	}

	/**
	 * Update meta information in the model.
	 *
	 * @param string $metaName The meta information name.
	 *
	 * @param mixed $value The meta information value to store.
	 *
	 * @return void
	 */
	public function setMeta($metaName, $value)
	{
		$this->meta[$metaName] = $value;
	}

	/**
	 * Check if this model have any properties.
	 *
	 * @return boolean true if any property has been stored, false otherwise.
	 */
	public function hasProperties()
	{
		return (count($this->activeRecord->row()) > 0);
	}

	/**
	 * Return the data provider name.
	 *
	 * @return string the name of the corresponding data provider.
	 */
	public function getProviderName()
	{
		return $this->providerName;
	}


	/**
	 * Read all values from a value bag.
	 *
	 * If the value is not present in the value bag, it will get skipped.
	 *
	 * If the value for a property in the bag is invalid, an exception will get thrown.
	 *
	 * @param PropertyValueBagInterface $valueBag The value bag where to read from.
	 *
	 * @return ModelInterface
	 *
	 * @throws DcGeneralInvalidArgumentException When a property in the value bag has been marked as invalid.
	 */
	public function readFromPropertyValueBag(PropertyValueBagInterface $valueBag)
	{
		$this->setPropertiesAsArray($valueBag->getArrayCopy());

		return $this;
	}

	/**
	 * Write values to a value bag.
	 *
	 * @param PropertyValueBagInterface $valueBag The value bag where to write to.
	 *
	 * @return ModelInterface
	 */
	public function writeToPropertyValueBag(PropertyValueBagInterface $valueBag)
	{
		foreach($this->getPropertiesAsArray() as $name => $value) {
			$valueBag->setPropertyValue($name, $value);
		}

		return $this;
	}


	/**
	 * Copy this model, without the id.
	 *
	 * @return void
	 */
	public function __clone()
	{
		$this->activeRecord = clone $this->activeRecord;
	}


} 