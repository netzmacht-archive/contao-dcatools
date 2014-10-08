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

use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;
use DcaTools\Exception\InvalidArgumentException;

class Input
{
    const METHOD_GET     = 'get';
    const METHOD_POST    = 'post';
    const METHOD_SESSION = 'session';

    /**
	 * @param InputProviderInterface $input
	 * @param $method
	 * @param $name
	 *
	 * @return mixed|null
	 */
    public static function getValue(InputProviderInterface $input, $method, $name)
    {
        switch ($method) {
            case static::METHOD_GET:
                return $input->getParameter($name);
                break;

            case static::METHOD_POST:
                return $input->getValue($name);
                break;

            case static::METHOD_SESSION:
                return $input->getPersistentValue($name);
                break;
        }

        return null;
    }

    /**
	 * @param InputProviderInterface $input
	 * @param $method
	 * @param $name
	 * @param $value
	 *
	 * @throws \DcaTools\Exception\InvalidArgumentException
	 */
    public static function setValue(InputProviderInterface $input, $method, $name, $value)
    {
        switch ($method) {
            case static::METHOD_GET:
                $input->setParameter($name, $value);
                break;

            case static::METHOD_POST:
                $input->setValue($name, $value);
                break;

            case static::METHOD_SESSION:
                $input->setPersistentValue($name, $value);
                break;

            default:
                throw new InvalidArgumentException('Unknown input method', 0, null, $value);
        }
    }

    /**
	 * @param InputProviderInterface $input
	 * @param $method
	 * @param $name
	 *
	 * @return bool
	 */
    public static function hasValue(InputProviderInterface $input, $method, $name)
    {
        switch ($method) {
            case static::METHOD_GET:
                return $input->hasParameter($name);
                break;

            case static::METHOD_POST:
                return $input->hasValue($name);
                break;

            case static::METHOD_SESSION:
                return $input->hasPersistentValue($name);
                break;
        }

        return false;
    }

}
