<?php

namespace spec\DcaTools\Dca\Legacy;

use DcaTools\Dca\Legacy;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CallbackManagerSpec extends ObjectBehavior
{
	const CALLBACK_CLASS = 'DcaTools\Dca\DcaToolsIntegration';

	function let()
	{
		$this->beConstructedWith(self::CALLBACK_CLASS);
	}

    function it_is_initializable()
    {
        $this->shouldHaveType('DcaTools\Dca\Legacy\CallbackManager');
    }

	function it_enables_callback()
	{
		//$this->enableCallback(Callback::CONTAINER_HEADER, 'tl_test')->shouldReturn($this);
	}
}
