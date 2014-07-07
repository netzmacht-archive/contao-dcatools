<?php

namespace spec\DcaTools\Definition\Command\Condition;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Condition\Command\CommandConditionFactory;
use DcaTools\Dca\Button;
use DcaTools\User\User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


class IsAdminConditionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('DcaTools\Definition\Command\Condition\IsAdminCondition');
    }

	function it_builds_from_config(CommandConditionFactory $factory)
	{
		$factory->createByName('isAdmin')->willReturn($this);

		$this->fromConfig(array('condition' => 'isAdmin'), null, $factory);
	}

	function it_considers_user_is_not_admin(Button $button, EnvironmentInterface $environment, User $user, ModelInterface $model)
	{
		$user->hasRole(User::ROLE_ADMIN)->willReturn(false);
		$this->match($button, $environment, $user, $model)->shouldReturn(false);
	}

	function it_considers_user_is_admin(Button $button, EnvironmentInterface $environment, User $user, ModelInterface $model)
	{
		$user->hasRole(User::ROLE_ADMIN)->willReturn(true);
		$this->match($button, $environment, $user, $model)->shouldReturn(true);
	}

	function it_can_be_inversed(Button $button, EnvironmentInterface $environment, User $user, ModelInterface $model)
	{
		$user->hasRole(User::ROLE_ADMIN)->willReturn(true);

		$this->setInverse(true)->shouldReturn($this);
		$this->isInverse()->shouldReturn(true);

		$this->match($button, $environment, $user, $model)->shouldReturn(false);
	}
}
