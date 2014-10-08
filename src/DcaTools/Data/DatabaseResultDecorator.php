<?php

/**
 * @package    contao-dcatools
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Data;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\Data\PropertyValueBagInterface;

/**
 * Class ActiveRecordModelDecorator decorates Contao database result to access it using the ModelInterface of the
 * DcGeneral
 *
 * @package DcaTools\Data
 */
class DatabaseResultDecorator implements ModelInterface
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
	 * @inheritdoc
	 */
    public function __construct($providerName, $activeRecord)
    {
        $this->activeRecord = $activeRecord;
        $this->providerName = $providerName;
    }

    /**
	 * @inheritdoc
	 */
    public function getIterator()
    {
        return new \ArrayIterator($this->getPropertiesAsArray());
    }

    /**
	 * @inheritdoc
	 */
    public function getId()
    {
        return $this->activeRecord->id;
    }

    /**
	 * @inheritdoc
	 */
    public function getProperty($propertyName)
    {
        return $this->activeRecord->$propertyName;
    }

    /**
	 * @inheritdoc
	 */
    public function getPropertiesAsArray()
    {
        return $this->activeRecord->row();
    }

    /**
	 * @inheritdoc
	 */
    public function getMeta($metaName)
    {
        if (isset($this->meta[$metaName])) {
            return $this->meta[$metaName];
        }

        return null;
    }

    /**
	 * @inheritdoc
	 */
    public function setId($mixId)
    {
        $this->activeRecord->id = $mixId;
    }

    /**
	 * @inheritdoc
	 */
    public function setProperty($propertyName, $value)
    {
        $this->activeRecord->$propertyName = $value;
    }

    /**
	 * @inheritdoc
	 */
    public function setPropertiesAsArray($properties)
    {
        foreach ($properties as $name => $value) {
            $this->setProperty($name, $value);
        }
    }

    /**
	 * @inheritdoc
	 */
    public function setMeta($metaName, $value)
    {
        $this->meta[$metaName] = $value;
    }

    /**
	 * @inheritdoc
	 */
    public function hasProperties()
    {
        return (count($this->activeRecord->row()) > 0);
    }

    /**
	 * @inheritdoc
	 */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
	 * @inheritdoc
	 */
    public function readFromPropertyValueBag(PropertyValueBagInterface $valueBag)
    {
        $this->setPropertiesAsArray($valueBag->getArrayCopy());

        return $this;
    }

    /**
	 * @inheritdoc
	 */
    public function writeToPropertyValueBag(PropertyValueBagInterface $valueBag)
    {
        foreach ($this->getPropertiesAsArray() as $name => $value) {
            $valueBag->setPropertyValue($name, $value);
        }

        return $this;
    }

    /**
	 * @inheritdoc
	 */
    public function __clone()
    {
        $this->activeRecord = clone $this->activeRecord;
    }

}
