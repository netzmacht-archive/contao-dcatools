<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Condition\Permission;

use DcaTools\Assertion;
use DcaTools\Definition\Permission\PermissionCondition;
use DcaTools\Exception\InvalidArgumentException;

class PermissionConditionFactory
{
    /**
	 * @var array
	 */
    private $map;

    /**
	 * @var \DcaTools\Condition\Command\FilterFactory
	 */
    private $filterFactory;

    /**
	 * @param FilterFactory $filterFactory
	 * @param array $map
	 */
    public function __construct(FilterFactory $filterFactory, array $map)
    {
        $this->filterFactory = $filterFactory;
        $this->map           = $map;
    }

    /**
	 * @param $name
	 * @param array $definition
	 * @param array $filter
	 *
	 * @return PermissionCondition
	 */
    public function createByName($name, array $definition, array $filter=null)
    {
        Assertion::keyExists($this->map, $name, 'Condition is not registered');

        $condition = $this->map[$name];

        if (!empty($filter)) {
            $filter = $this->filterFactory->createByName('all', $filter, $this);
        }

        if (is_callable($condition)) {
            return call_user_func($condition, $name, $definition, $filter, $this);
        }

        /** @var PermissionCondition $condition */

        return $condition::fromConfig($definition, $filter, $this);
    }

    /**
	 * @param array $config
	 *
	 * @throws InvalidArgumentException
	 * @return PermissionCondition
	 */
    public function createFromConfig(array $config)
    {
        Assertion::keyExists($config, 'condition', 'No condition defined');

        $name   = $config['condition'];
        $filter = isset($config['filter']) ? $config['filter'] : null;
        $config = isset($config['config']) ? $config['config'] : array();

        return $this->createByName($name, $config, $filter);
    }

}
