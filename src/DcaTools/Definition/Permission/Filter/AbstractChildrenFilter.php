<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Permission\Filter;

use DcaTools\Assertion;
use DcaTools\Condition\Permission\FilterFactory;
use DcaTools\Definition\Permission\PermissionFilter;
use DcaTools\Exception\InvalidArgumentException;

abstract class AbstractChildrenFilter implements PermissionFilter
{
    /**
	 * @var PermissionFilter[]
	 */
    protected $children = array();

    /**
	 * @param array $config
	 * @param FilterFactory $factory
	 * @return PermissionFilter
	 */
    public static function fromConfig(array $config, FilterFactory $factory)
    {
        /** @var AbstractChildrenFilter $filter */
        $filter   = new static();
        $children = array();

        foreach ($config as $child) {
            $children[] = $factory->createFromConfig($child);
        }

        $filter->setChildren($children);

        return $filter;
    }

    /**
	 * @param PermissionFilter[] $children
	 *
	 * @throws InvalidArgumentException
	 * @return $this;
	 */
    public function setChildren(array $children)
    {
        Assertion::allIsInstanceOf($children, 'DcaTools\Definition\Permission\PermissionFilter');

        $this->children = $children;

        return $this;
    }

    /**
	 * @return PermissionFilter[]
	 */
    public function getChildren()
    {
        return $this->children;
    }

}
