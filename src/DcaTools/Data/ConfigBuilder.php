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

namespace DcaTools\Data;

use DcGeneral\Data\DCGE;
use DcGeneral\Data\DriverInterface;


/**
 * Class ConfigBuilder is a helper class for creating config objects used by the data providers or DC_General. It works
 * like a query builder
 *
 * @package DcaTools\Data
 */
class ConfigBuilder
{

	/**
	 * @var \DcGeneral\Data\DriverInterface
	 */
	protected $driver;

	/**
	 * @var \DcGeneral\Data\ConfigInterface
	 */
	protected $config;

	/**
	 * @var array
	 */
	protected $filters = array();

	/**
	 * @var array
	 */
	protected $sorting = array();

	/**
	 * @var array
	 */
	protected $fields = array();


	/**
	 * @param DriverInterface $driver
	 */
	public function __construct(DriverInterface $driver)
	{
		$this->driver = $driver;
		$this->config = $driver->getEmptyConfig();
	}


	/**
	 * Create instance
	 *
	 * @param DriverInterface $driver
	 * @return static
	 */
	public static function create(DriverInterface $driver)
	{
		return new static($driver);
	}


	/**
	 * Set an id
	 *
	 * @param $id
	 * @return $this
	 */
	public function setId($id)
	{
		$this->config->setId($id);
		return $this;
	}


	/**
	 * An an id
	 *
	 * @param $id
	 * @return $this
	 */
	public function id($id)
	{
		$ids = $this->config->getIds();
		$ids[] = $id;

		$this->config->setIds($ids);
		return $this;
	}


	/**
	 * And multiple ids
	 *
	 * @param array $ids
	 * @return $this
	 */
	public function ids(array $ids)
	{
		foreach($ids as $id) {
			$this->id($id);
		}

		return $this;
	}


	/**
	 * @param $idOnly
	 * @return $this
	 */
	public function idOnly($idOnly)
	{
		$this->config->setIdOnly($idOnly);
		return $this;
	}


	/**
	 * @param $name
	 * @return $this
	 */
	public function field($name)
	{
		$this->fields[] = $name;
		return $this;
	}


	/**
	 * @return $this
	 */
	public function fields()
	{
		foreach(func_get_args() as $field) {
			$this->field($field);
		}

		return $this;
	}

	/**
	 * @param $column
	 * @param string $direction
	 * @return $this
	 */
	public function sorting($column, $direction = DCGE::MODEL_SORTING_ASC)
	{
		$this->sorting[$column] = $direction;
		return $this;
	}


	/**
	 * @param array $sorting
	 */
	public function setSorting(array $sorting)
	{
		$this->sorting = $sorting;
	}


	/**
	 * @param $amount
	 * @return $this
	 */
	public function amount($amount)
	{
		$this->config->setAmount($amount);
		return $this;
	}


	/**
	 * @param $start
	 * @return $this
	 */
	public function start($start)
	{
		$this->config->setStart($start);
		return $this;
	}


	/**
	 * Add an or filter
	 *
	 * @param array $children set of filter
	 *
	 * @return $this
	 */
	public function filterOr(array $children)
	{
		return $this->filter('OR', array('children' => $children));
	}


	/**
	 * Add an and filter
	 *
	 * @param array $children set of filter
	 *
	 * @return $this
	 */
	public function filterAnd(array $children)
	{
		return $this->filter('AND', array('children' => $children));
	}


	/**
	 * Add an in filter
	 *
	 * @param string $property property name
	 * @param array $values property value
	 * @return $this
	 */
	public function filterIn($property, array $values)
	{
		return $this->filter('IN', array('values' => $values), $property);
	}


	/**
	 * Add an like filter
	 *
	 * @param string $property property name
	 * @param mixed $value property value
	 * @return $this
	 */
	public function filterLike($property, $value)
	{
		return $this->filter('LIKE', array('value' => $value), $property);
	}


	/**
	 * Add an equals filter
	 *
	 * @param string $property property name
	 * @param mixed $value property value
	 * @return $this
	 */
	public function filterEquals($property, $value)
	{
		return $this->filter('=', array('value' => $value), $property);
	}

	/**
	public function addNotEquals($property, $value)
	{
	// DcGeneral does not to support it so far
	}
	 */


	/**
	 * Add a greater than filter
	 *
	 * @param string $property property name
	 * @param mixed $value property value
	 * @return $this
	 */
	public function filterGreaterThan($property, $value)
	{
		return $this->filter('>', array('value' => $value), $property);
	}


	/**
	 * Add a lesser than filter
	 *
	 * @param string $property property name
	 * @param mixed $value property value
	 * @return $this
	 */
	public function filterLesserThan($property, $value)
	{
		return $this->filter('<', array('value' => $value), $property);
	}

	/**
	 * @param $operation
	 * @param array $filter
	 * @param null $property
	 *
	 * @return $this
	 */
	public function filter($operation, array $filter=array(), $property=null)
	{
		$filter['operation'] = $operation;

		if($property !== null) {
			$filter['property']  = $property;
		}

		$this->filters[] = $filter;

		return $this;
	}


	/**
	 * Get the config object
	 *
	 * @return \DcGeneral\Data\ConfigInterface
	 */
	public function getConfig()
	{
		if(count($this->filters)) {
			$this->config->setFilter($this->filters);
		}

		if(count($this->sorting)) {
			$this->config->setSorting($this->sorting);
		}

		if(count($this->fields)) {
			$this->config->setFields($this->fields);
		}

		return $this->config;
	}


	/**
	 * @return \DcGeneral\Data\ModelInterface
	 */
	public function fetch()
	{
		return $this->driver->fetch($this->getConfig());
	}


	/**
	 * @return \DcGeneral\Data\CollectionInterface|\DcGeneral\Data\ModelInterface[]
	 */
	public function fetchAll()
	{
		return $this->driver->fetchAll($this->getConfig());
	}


	/**
	 * Delete by applying the filter. If id is given a single record will deleted or all items are fetched and then
	 * deleted
	 */
	public function delete()
	{
		if($this->config->getId()) {
			$this->driver->delete($this->getConfig());
		}
		else {
			$this->idOnly(true);

			foreach($this->fetchAll() as $item) {
				$this->driver->delete($item);
			}
		}
	}

}
