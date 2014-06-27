<?php

/**
 * @package    contao-dcatools
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Dca\Button\Condition;


use DcaTools\Exception\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


/**
 * Class ConditionChain
 * @package DcaTools\Dca\Command\Condition
 * @method ConditionChain isAdmin(array $arguments=array())
 * @method ConditionChain hasAccess(array $arguments=array())
 * @method ConditionChain isAllowed(array $arguments=array())
 * @method ConditionChain disableIcon(array $arguments=array())
 * @method ConditionChain hide(array $arguments=array())
 */
class ConditionChain
{
	/**
	 * @var ConditionManager
	 */
	private $conditionManager;

	/**
	 * @var string
	 */
	private $dataContainerName;

	/**
	 * @var string
	 */
	private $buttonType;

	/**
	 * @var string
	 */
	private $buttonName;


	/**
	 * @param $conditionManager
	 * @param $dataContainerName
	 * @param $buttonType
	 * @param $buttonName
	 */
	function __construct(ConditionManager $conditionManager, $dataContainerName, $buttonType, $buttonName)
	{
		$this->conditionManager  = $conditionManager;
		$this->dataContainerName = $dataContainerName;
		$this->buttonType        = $buttonType;
		$this->buttonName        = $buttonName;
	}


	/**
	 * @param $name
	 * @param $arguments
	 * @throws InvalidArgumentException
	 * @return $this
	 */
	public function __call($name, $arguments)
	{
		$this->conditionManager
			->addCondition($this->dataContainerName, $this->buttonType, $this->buttonName,	$name, $arguments[0]);

		return $this;
	}

}