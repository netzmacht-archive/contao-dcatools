<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Command\Filter;

use DcaTools\Condition\Command\FilterFactory;
use DcaTools\Definition\Command\CommandFilter;

abstract class AbstractFilter implements CommandFilter
{
    /**
	 * @var bool
	 */
    protected $inverse=false;

    /**
	 * @param array $config
	 * @param FilterFactory $factory
	 * @return CommandFilter|static
	 */
    public static function fromConfig(array $config, FilterFactory $factory)
    {
        /** @var AbstractFilter $filter */
        $filter = new static();

        if (isset($config['inverse'])) {
            $filter->setInverse($config['inverse']);
        }

        return $filter;
    }

    /**
	 * @param boolean $inverse
	 */
    public function setInverse($inverse)
    {
        $this->inverse = (bool) $inverse;
    }

    /**
	 * @return boolean
	 */
    public function isInverse()
    {
        return $this->inverse;
    }

    /**
	 * @param $state
	 * @return bool
	 */
    protected function applyInverse($state)
    {
        if ($this->inverse) {
            return !$state;
        }

        return $state;
    }

}
