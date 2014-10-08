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

use ContaoCommunityAlliance\DcGeneral\Data\CollectionInterface;
use ContaoCommunityAlliance\DcGeneral\Data\ConfigInterface;
use ContaoCommunityAlliance\DcGeneral\Data\DCGE;
use ContaoCommunityAlliance\DcGeneral\Data\DataProviderInterface;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;

/**
 * Class ConfigBuilder is a helper class for creating config objects used by the data providers or DC_General. It works
 * like a query builder
 *
 * @package DcaTools\Data
 */
class ConfigBuilder
{

    /**
	 * @var DataProviderInterface
	 */
    protected $provider;

    /**
	 * @var ConfigInterface
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
	 * @var array
	 */
    private $ids = array();

    /**
	 * @param DataProviderInterface $dataProvider
	 */
    public function __construct(DataProviderInterface $dataProvider)
    {
        $this->provider = $dataProvider;
        $this->config   = $dataProvider->getEmptyConfig();
    }

    /**
	 * Create instance
	 *
	 * @param DataProviderInterface|EnvironmentInterface $environmentOrDataProvider
	 * @param string|null $source
	 * @return static
	 */
    public static function create($environmentOrDataProvider, $source=null)
    {
        if ($environmentOrDataProvider instanceof EnvironmentInterface) {
            $environmentOrDataProvider = $environmentOrDataProvider->getDataProvider($source);
        }

        return new static($environmentOrDataProvider);
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
        $this->ids[] = $id;

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
        foreach ($ids as $id) {
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
	 * @param $fields,...
	 * @return $this
	 */
    public function fields($fields)
    {
        if (!is_array($fields)) {
            $fields = func_get_args();
        }

        foreach ($fields as $field) {
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
	 *
	 * @return $this
	 */
    public function setSorting(array $sorting)
    {
        $this->sorting = $sorting;

        return $this;
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

        if ($property !== null) {
            $filter['property']  = $property;
        }

        $this->filters[] = $filter;

        return $this;
    }

    /**
	 * Get the config object
	 *
	 * @return ConfigInterface
	 */
    public function getConfig()
    {
        if (count($this->filters)) {
            $this->config->setFilter($this->filters);
        }

        if (count($this->sorting)) {
            $this->config->setSorting($this->sorting);
        }

        if (count($this->fields)) {
            $this->config->setFields($this->fields);
        }

        if (count($this->ids) == 1 && $this->config->getId() === null) {
            $this->config->setId($this->ids[0]);
        } elseif ($this->ids) {
            $this->config->setIds($this->ids);
        }

        return $this->config;
    }

    /**
	 * @return ModelInterface
	 */
    public function fetch()
    {
        return $this->provider->fetch($this->getConfig());
    }

    /**
	 * @return CollectionInterface|ModelInterface[]
	 */
    public function fetchAll()
    {
        return $this->provider->fetchAll($this->getConfig());
    }

    /**
	 * Delete by applying the filter. If id is given a single record will deleted or all items are fetched and then
	 * deleted
	 */
    public function delete()
    {
        if ($this->config->getId() || count($this->ids) == 1) {
            $this->provider->delete($this->getConfig());
        } else {
            $this->idOnly(true);

            foreach ($this->fetchAll() as $item) {
                $this->provider->delete($item);
            }
        }
    }

    /**
	 * @return int
	 */
    public function getCount()
    {
        return $this->provider->getCount($this->getConfig());
    }

}
