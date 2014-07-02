<?php

namespace spec\DcaTools\Definition\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GlobalCommandConditionsSpec extends AbstractCommandConditionBehaviour
{
    function it_is_initializable()
    {
        $this->shouldHaveType('DcaTools\Definition\Command\GlobalCommandConditions');
    }
}
