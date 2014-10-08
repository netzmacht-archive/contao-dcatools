<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Condition\Command;

use DcaTools\Assertion;
use DcaTools\Definition\Command\CommandFilter;
use DcaTools\Exception\InvalidArgumentException;

class FilterFactory
{
    /**
	 * @var array
	 */
    private $map;

    /**
	 * @param $map
	 */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
	 * @param $name
	 * @param $config
	 *
	 * @return \DcaTools\Definition\Command\CommandFilter
	 */
    public function createByName($name, array $config)
    {
        Assertion::keyExists($this->map, $name, 'Unknown filter name');

        $filter = $this->map[$name];

        if (is_callable($filter)) {
            return call_user_func($filter, $config, $this);
        }

        /** @var \DcaTools\Definition\Command\CommandFilter $filter */

        return $filter::fromConfig($config, $this);
    }

    /**
	 * @param array $config
	 *
	 * @throws InvalidArgumentException
	 * @return CommandFilter
	 */
    public function createFromConfig(array $config)
    {
        Assertion::keyExists($config, 'filter', 'No filter name given');

        return $this->createByName($config['filter'], $config);
    }

}
