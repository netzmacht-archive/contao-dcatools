<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools;


class Assertion extends \Assert\Assertion
{
	protected static $exceptionClass = 'DcaTools\Exception\InvalidArgumentException';


	/**
	 * @param mixed $value
	 * @param int|string $key
	 * @param null $message
	 * @param null $propertyPath
	 */
	public static function keyExists($value, $key, $message = null, $propertyPath = null)
	{
		if($value instanceof \ArrayObject) {
			$value = $value->getArrayCopy();
		}

		parent::keyExists($value, $key, $message, $propertyPath);
	}


	/**
	 * @param $countable
	 * @param $minCount
	 * @param $maxCount
	 * @param null $message
	 * @param null $propertyPath
	 */
	public static function countBetween($countable, $minCount, $maxCount, $message = null, $propertyPath = null)
	{
		static::range(count($countable), $minCount, $maxCount, $message, $propertyPath);
	}


	/**
	 * @param $value
	 * @param null $message
	 * @param null $propertyPath
	 */
	public static function isCallable($value, $message=null, $propertyPath=null)
	{
		static::true(is_callable($value), $message, $propertyPath);
	}

}