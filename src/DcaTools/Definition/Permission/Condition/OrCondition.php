<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Permission\Condition;


use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Definition\Permission\Context;
use DcaTools\User\User;


/**
 * Class OrCondition
 * @package DcaTools\Definition\Permission\Condition
 */
class OrCondition extends AbstractCondition
{
	/**
	 * @return array
	 */
	protected function getDefaultConfig()
	{
		return array(
			'conditions' => array(),
		);
	}


	/**
	 * @param EnvironmentInterface $environment
	 * @param \DcaTools\User\User $user
	 * @param Context $context
	 * @return bool
	 */
	public function execute(EnvironmentInterface $environment, User $user, Context $context)
	{
		if(empty($this->conditions)) {
			return true;
		}

		foreach($this->conditions as $condition) {
			$match = $condition($environment, $context);
			$match = $this->applyConfigInverse($match);

			if($match) {
				return true;
			}
		}

		return false;
	}

} 