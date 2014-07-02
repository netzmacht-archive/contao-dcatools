<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Command\Condition;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;
use DcaTools\Assertion;
use DcaTools\Dca\Button;
use DcaTools\Definition\Command\Condition;
use DcaTools\User\User;

/**
 * Class AbstractStateCondition
 * @package DcaTools\Dca\Button\Condition
 */
abstract class AbstractStateCondition extends AbstractCondition
{

	/**
	 * @param Button $button
	 * @param InputProviderInterface $input
	 * @param User $user
	 * @param ModelInterface $model
	 * @return bool
	 */
	protected function getState(Button $button, InputProviderInterface $input, User $user, ModelInterface $model=null)
	{
		$state = false;

		if($this->config['condition']) {
			$condition = $this->config['condition'];
			$state     = $condition($button, $input, $user, $model);
		}
		elseif($this->config['property']) {
			Assertion::notNull($model, 'Property can part of condition for model operations');

			$state = ($model->getProperty($this->config['property']) == $this->config['value']);
		}
		elseif($this->config['callback']) {
			$callback = $this->config['callback'];
			$state  = call_user_func($callback, $button, $input, $user, $model);
		}

		return $this->applyConfigInverse($state);
	}

}