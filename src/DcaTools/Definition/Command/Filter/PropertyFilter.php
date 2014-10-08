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

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Assertion;
use DcaTools\Condition\Command\FilterFactory;
use DcaTools\Dca\Button;
use DcaTools\Definition\Command\CommandFilter;
use DcaTools\Exception\InvalidArgumentException;
use DcaTools\User\User;
use DcaTools\Util\Comparison;

class PropertyFilter implements CommandFilter
{
    const NAME = 'property';

    /**
	 * @var
	 */
    private $property;

    /**
	 * @var
	 */
    private $callback;

    /**
	 * @var
	 */
    private $value;

    /**
	 * @var
	 */
    private $operator = Comparison::EQUAL;

    /**
	 * @param $property
	 * @param $operator
	 * @param $value
	 */
    public function __construct($property, $operator=Comparison::EQUAL, $value=null)
    {
        $this->property = $property;
        $this->operator = $operator;
        $this->value    = $value;
    }

    /**
	 * @param array $config
	 * @param FilterFactory $factory
	 * @return CommandFilter
	 */
    public static function fromConfig(array $config, FilterFactory $factory)
    {
        Assertion::isArray($config, 'ActionFilter config has to be an array');
        Assertion::keyExists($config, 'property');

        /** @var PropertyFilter $filter */
        $filter = new static($config['property']);

        if (isset($config['operator'])) {
            $filter->setOperator($config['operator']);
        }

        if (isset($config['value'])) {
            $filter->setValue($config['value']);
        }

        if (isset($config['callback'])) {
            $filter->setCallback($config['callback']);
        }

        return $filter;
    }

    /**
	 * @param Button $button
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param ModelInterface $model
	 *
	 * @return bool
	 */
    public function match(Button $button, EnvironmentInterface $environment, User $user, ModelInterface $model = null)
    {
        if ($this->callback) {
            $callback = $this->callback;

            return $callback($this, $button, $environment, $user, $model);
        }

        return Comparison::compare($this->operator, $model->getProperty($this->property), $this->value);
    }

    /**
	 * @param mixed $callback
	 *
	 * @throws InvalidArgumentException
	 * @return $this
	 */
    public function setCallback($callback)
    {
        Assertion::isCallable($callback, 'Callback has to be a callable');

        $this->callback = $callback;

        return $this;
    }

    /**
	 * @return mixed
	 */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
	 * @param mixed $operator
	 *
	 * @throws InvalidArgumentException
	 * @return $this
	 */
    public function setOperator($operator)
    {
        Assertion::true(Comparison::supportsOperator($operator), 'Operator is not supported');

        $this->operator = $operator;

        return $this;
    }

    /**
	 * @return mixed
	 */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
	 * @param mixed $property
	 *
	 * @return $this
	 */
    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    /**
	 * @return mixed
	 */
    public function getProperty()
    {
        return $this->property;
    }

    /**
	 * @param mixed $value
	 *
	 * @return $this;
	 */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
	 * @return mixed
	 */
    public function getValue()
    {
        return $this->value;
    }

}
