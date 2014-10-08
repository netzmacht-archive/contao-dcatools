<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Util;


class Comparison
{
	const EQUAL        = '==';
	const GREATER_THAN = '>';
	const LESSER_THAN  = '<';
	const NOT_EQUAL    = '!=';
	const IDENTICAL    = '===';


	/**
	 * @param $operator
	 * @param $valueA
	 * @param $valueB
	 *
	 * @return bool
	 */
	public static function compare($operator, $valueA, $valueB)
	{
		switch($operator) {
			case static::EQUAL:
				return static::equal($valueA, $valueB);
				break;

			case static::GREATER_THAN:
				return static::greaterThan($valueA, $valueB);
				break;

			case static::LESSER_THAN:
				return static::lesserThan($valueA, $valueB);
				break;

            case static::NOT_EQUAL:
                return static::notEqual($valueA, $valueB);
                break;

            case static::IDENTICAL:
                return static::identical($valueA, $valueB);
                break;
		}

		return false;
	}


	/**
	 * @param $valueA
	 * @param $valueB
	 *
	 * @return bool
	 */
	public static function equal($valueA, $valueB)
	{
		return $valueA == $valueB;
	}


	/**
	 * @param $valueA
	 * @param $valueB
	 *
	 * @return bool
	 */
	public static function greaterThan($valueA, $valueB)
	{
		return ($valueA > $valueB);
	}


	/**
	 * @param $valueA
	 * @param $valueB
	 *
	 * @return bool
	 */
	public static function lesserThan($valueA, $valueB)
	{
		return ($valueA < $valueB);
	}


	/**
	 * @param $valueA
	 * @param $valueB
	 *
	 * @return bool
	 */
	public static function notEqual($valueA, $valueB)
	{
		return ($valueA != $valueB);
	}


	/**
	 * @param $valueA
	 * @param $valueB
	 *
	 * @return bool
	 */
	public static function identical($valueA, $valueB)
	{
		return ($valueA === $valueB);
	}


	/**
	 * @param $operator
	 *
	 * @return bool
	 */
	public static function supportsOperator($operator)
	{
		$reflect   = new \ReflectionClass(get_called_class());
		$constants = $reflect->getConstants();

		return in_array($operator, $constants);
	}
} 