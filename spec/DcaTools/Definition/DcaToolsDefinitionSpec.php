<?php

namespace spec\DcaTools\Definition;

use DcaTools\Dca\Legacy\Callback;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DcaToolsDefinitionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('DcaTools\Definition\DcaToolsDefinition');
    }


	function it_defines_about_legacy_mode()
	{
		$this->getLegacyMode()->shouldReturn(false);
		$this->setLegacyMode(true)->shouldReturn($this);
		$this->getLegacyMode()->shouldReturn(true);
	}


	function it_defines_command_conditions()
	{
		$this->getCommandConditions()->shouldHaveType('DcaTools\Definition\Command\CommandConditions');
	}

	function it_defines_permission_conditions()
	{
		$this->getPermissionConditions()->shouldHaveType('DcaTools\Definition\Permission\PermissionConditions');
	}

	function it_defines_global_command_conditions()
	{
		$this->getGlobalCommandConditions()->shouldHaveType('DcaTools\Definition\Command\GlobalCommandConditions');
	}

	function it_defines_enabled_callbacks()
	{
		$callbacks = array(Callback::CONTAINER_HEADER => true);

		$this->setCallbacks($callbacks)->shouldReturn($this);
		$this->getCallbacks()->shouldReturn($callbacks);
	}
}
