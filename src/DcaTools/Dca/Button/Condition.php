<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Dca\Button;


use DcaTools\Assertion;
use DcaTools\Dca\Button\Condition\ConditionChain;
use DcaTools\Dca\Button\Condition\ConditionManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Condition
 *
 * @package DcaTools\Dca\Command
 * @method static void isAdmin($dataContainerName, $commandType, $commandName, array $arguments=array())
 * @method static void hasAccess($dataContainerName, $commandType, $commandName, array $arguments=array())
 * @method static void isAllowed($dataContainerName, $commandType, $commandName, array $arguments=array())
 * @method static void disableIcon(array $arguments=array())
 * @method static void hide(array $arguments=array())
 */
class Condition
{
	const IS_ADMIN   = 'isAdmin';
	const HAS_ACCESS = 'hasAccess';
	const IS_ALLOWED = 'isAllowed';


	/**
	 * @var ConditionManager
	 */
	private static $conditionManager;


	/**
	 * @param ConditionManager $conditionManager
	 */
	public static function setConditionManager(ConditionManager $conditionManager)
	{
		self::$conditionManager = $conditionManager;
	}


	/**
	 * @param $dataContainerName
	 * @param $commandType
	 * @param $commandName
	 * @return ConditionChain
	 */
	public static function chain($dataContainerName, $commandType, $commandName)
	{
		Assertion::notNull(static::$conditionManager, 'No Condition manager registered');

		return new ConditionChain(static::$conditionManager, $dataContainerName, $commandType, $commandName);
	}


	/**
	 * @param $dataContainerName
	 * @param $commandType
	 * @param $commandName
	 * @param $conditionName
	 * @param array $arguments
	 */
	public static function add($dataContainerName, $commandType, $commandName, $conditionName, array $arguments=array())
	{
		Assertion::notNull(static::$conditionManager, 'No Condition manager registered');

		static::$conditionManager->addCondition(
			$dataContainerName,
			$commandType,
			$commandName,
			$conditionName,
			$arguments
		);
	}


	/**
	 * @param $name
	 * @param $arguments
	 */
	public static function __callStatic($name, $arguments)
	{
		Assertion::countBetween($arguments, 3, 4, 'You have to pass 4 or 5 arguments');

		$args = isset($arguments[3]) ? $arguments[3] : array();

		static::add($arguments[0], $arguments[1], $arguments[2], $name, $args);
	}

} 