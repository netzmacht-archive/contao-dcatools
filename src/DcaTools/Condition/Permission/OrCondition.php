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


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;

class OrCondition extends AbstractChildrenCondition
{

	/**
	 * @param EnvironmentInterface $environment
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function __invoke(EnvironmentInterface $environment, ModelInterface $model)
	{
		if(empty($this->conditions)) {
			return true;
		}

		foreach($this->conditions as $condition) {
			if($condition($environment, $model)) {
				return true;
			}
		}

		return false;
	}

} 