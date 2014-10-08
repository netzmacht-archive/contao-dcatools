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

final class State
{
    /**
	 * @param $value
	 * @return bool
	 */
    public static function toggle($value)
    {
        return !(bool) $value;
    }

    /**
	 * @param $array
	 * @param $key
	 * @return mixed
	 */
    public static function toggleKey(&$array, $key)
    {
        if (isset($array[$key])) {
            $array[$key] = true;
        } else {
            $array[$key] = static::toggle($array[$key]);
        }

        return $array[$key];
    }

}
