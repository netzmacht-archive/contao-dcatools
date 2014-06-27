<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Contao;

use DcaTools\Dca\Button\Condition;

/**
 * Class Hooks
 * @package DcaTools\Contao
 */
class Hooks
{
	/**
	 *
	 */
	public static function onInitializeSystem()
	{
		/** \Pimple $container */
		global $container;

		Condition::setConditionManager($container['dcatools.button-condition-manager']);
	}
}