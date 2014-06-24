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

namespace deprecated\DcaTools\Helper;

use deprecated\DcaTools\Definition;

/**
 * Class Formatter
 * @package DcaTools\Helper
 */
class Formatter
{

	/**
	 * @var \deprecated\DcaTools\Definition\DataContainer
	 */
	protected $definition;


	/**
	 * Construct
	 * @param $table
	 */
	public function __construct($table)
	{
		$this->definition = Definition::getDataContainer($table);
	}


	/**
	 * Create instance for data container
	 * @param $table
	 * @return static
	 */
	public static function create($table)
	{
		return new static($table);
	}


	/**
	 * Get Label
	 *
	 * @param $path
	 * @param string $default
	 *
	 * @return string
	 */
	public function getLabel($path, $default)
	{
		$label = $this->definition->get($path . '/label/0');
		return $label ? $label : $default;
	}


	/**
	 * @param $name
	 * @param null $default
	 * @return string
	 */
	public function getPropertyLabel($name, $default=null)
	{
		if($name == 'tstamp') {
			$default = $this->translate('/MSC/tstamp');
		}

		return $this->getLabel('fields/' . $name, $default ?: $name);
	}


	/**
	 * @param $name
	 * @param null $default
	 * @return string
	 */
	public function getOperationLabel($name, $default=null)
	{
		return $this->getLabel('list/operations/' . $name, $default ?: $name);
	}


	/**
	 * @param $name
	 * @param null $default
	 * @return string
	 */
	public function getGlobalOperationLabel($name, $default=null)
	{
		return $this->getLabel('list/global_operations/' . $name, $default ?: $name);
	}


	/**
	 * @param $strProperty
	 * @param $value
	 * @return string
	 */
	public function getPropertyValue($strProperty, $value)
	{
		if(!$this->definition->hasProperty($strProperty)) {
			return '';
		}

		$definition = $this->definition->getProperty($strProperty);
		$value      = deserialize($value);

		if (is_array($value))
		{
			$value = implode(', ', $value);
		}
		elseif ($definition->getWidgetType() == 'checkbox' && !$this->definition->get('eval/multiple'))
		{
			$value = $this->translate('/MSC/' . (strlen($value) ? 'yes' : 'no'));
		}
		elseif (in_array($this->definition->get('eval/rgxp'), array('date', 'datim', 'time')))
		{
			$format = $this->definition->get('eval/rgxp');
			$value  = \Date::parse($GLOBALS['TL_CONFIG'][$format], $value);
		}
		elseif ($this->definition->get(sprintf('reference/%s/0', $value)))
		{
			$value = $this->definition->get(sprintf('reference/%s/0', $value));
		}
		elseif ($this->definition->get(sprintf('reference/%s', $value)))
		{
			$value = $this->definition->get(sprintf('reference/%s', $value));
		}
		elseif ($this->definition->get('eval/isAssociative') || array_is_assoc($this->definition->get('options')))
		{
			$value = $this->definition->get('options/' . $value);
		}
		elseif ($this->definition->get('eval/foreignKey'))
		{
			$arrForeignKey = explode('.', $this->definition->get('eval/foreignKey'), 2);

			$objLabel =\Database::getInstance()->prepare("SELECT " . $arrForeignKey[1] . " AS value FROM " . $arrForeignKey[0] . " WHERE id=?")
				->limit(1)
				->execute($value);

			if ($objLabel->numRows)
			{
				$value = $objLabel->value;
			}
		}

		if ($strProperty == 'tstamp')
		{
			$value = date($GLOBALS['TL_CONFIG']['datimFormat'], $value);
		}

		return $value;
	}


	/**
	 * Translate a key
	 *
	 * If key starts with a trailing slash, it tries to find it in the global language array. Otherwise it will
	 * have a look into the data container language vars
	 *
	 * @param $key
	 * @return string
	 */
	public function translate($key)
	{
		$chunks = explode('/', $key);

		if(substr($key, 0, 1) == '/')
		{
			$value = $GLOBALS['TL_LANG'];
			array_shift($chunks);
		}
		else
		{
			$value = $GLOBALS['TL_LANG'][$this->definition->getName()];
		}

		foreach($chunks as $chunk)
		{
			if(!isset($value[$chunk]))
			{
				return '';
			}

			$value = $value[$chunk];
		}

		return $value;
	}

}
