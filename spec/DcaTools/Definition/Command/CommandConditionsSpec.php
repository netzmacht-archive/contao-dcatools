<?php

namespace spec\DcaTools\Definition\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

require_once 'AbstractCommandConditionBehaviour.php';

class CommandConditionsSpec extends AbstractCommandConditionBehaviour
{
    function it_is_initializable()
    {
        $this->shouldHaveType('DcaTools\Definition\Command\CommandConditions');
    }



}
