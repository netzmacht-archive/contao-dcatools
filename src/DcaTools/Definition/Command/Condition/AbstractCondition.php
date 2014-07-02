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
use DcaTools\Dca\Button;
use DcaTools\Definition\AbstractGenericCondition;
use DcaTools\Definition\Command\CommandCondition;
use DcaTools\Definition\Command\Condition;
use DcaTools\User\User;


abstract class AbstractCondition extends AbstractGenericCondition implements CommandCondition
{

	/**
	 * @param array $config
	 * @param array $filter
	 */
	function __construct(array $config = array(), array $filter = array())
	{
		if(!array_key_exists('commands', $config)) {
			$config['commands'] = '*';
		}

		parent::__construct($config, $filter);
	}


	/**
	 * @param Button $button
	 * @param InputProviderInterface $input
	 * @param \DcaTools\User\User $user
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function __invoke(Button $button, InputProviderInterface $input, User $user, ModelInterface $model = null)
	{
		if($this->match($button, $input, $user, $model)) {
			return $this->execute($button, $input, $user, $model);
		}

		return true;
	}


	/**
	 * @param Button $button
	 * @param InputProviderInterface $input
	 * @param User $user
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function match(Button $button, InputProviderInterface $input, User $user, ModelInterface $model = null)
	{
		$match = true;

		// always match, no further checking
		if($this->filter['always']) {
			$match = $this->applyFilterInverse($match);

			return $match;
		}

		if($this->filter['commands'] != '*' && $this->filter['commands']) {
			$commands = (array) $this->filter['commands'];
			$match    = in_array($button->getKey(), $commands);
		}

		if($this->filter['property']) {
			$match = $this->matchPropertyFilter($model);
		}

		return $this->applyFilterInverse($match);
	}

} 