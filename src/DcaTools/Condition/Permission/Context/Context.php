<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Condition\Permission\Context;


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;

interface Context
{
	/**
	 * @return ModelInterface
	 */
	public function getParent();

	/**
	 * @return ModelInterface
	 */
	public function getModel();
} 