<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Command;


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;
use DcaTools\Dca\Button;
use DcaTools\User\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Condition
 *
 * @package DcaTools\Dca\Command
 */
interface CommandCondition
{
	const DISABLE = 'disable';
	const HIDE    = 'hide';


	/**
	 * @param Button $button
	 * @param InputProviderInterface $input
	 * @param User $user
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function __invoke(Button $button, InputProviderInterface $input, User $user, ModelInterface $model = null);


	/**
	 * @param Button $button
	 * @param InputProviderInterface $input
	 * @param User $user
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function execute(Button $button, InputProviderInterface $input, User $user, ModelInterface $model = null);


	/**
	 * @param Button $button
	 * @param InputProviderInterface $input
	 * @param User $user
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function match(Button $button, InputProviderInterface $input, User $user, ModelInterface $model = null);

} 