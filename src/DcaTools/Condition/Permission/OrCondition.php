<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Condition\Permission;


use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Condition\Permission\Context\Context;

class OrCondition extends AbstractChildrenCondition
{

	/**
	 * @param EnvironmentInterface $environment
	 * @param Context $context
	 * @return bool
	 */
	public function __invoke(EnvironmentInterface $environment, Context $context)
	{
		if(empty($this->conditions)) {
			return true;
		}

		foreach($this->conditions as $condition) {
			if($condition($environment, $context)) {
				return true;
			}
		}

		return false;
	}

} 