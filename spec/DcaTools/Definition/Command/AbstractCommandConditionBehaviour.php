<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace spec\DcaTools\Definition\Command;

use DcaTools\Definition\Command\CommandCondition;
use PhpSpec\ObjectBehavior;

class AbstractCommandConditionBehaviour extends ObjectBehavior
{
	function it_adds_condition(CommandCondition $condition)
	{
		$this->hasCondition($condition)->shouldReturn(false);
		$this->addCondition($condition)->shouldReturn($this);
		$this->hasCondition($condition)->shouldReturn(true);
	}

	function it_returns_all_conditions(CommandCondition $condition)
	{
		$this->addCondition($condition);
		$this->getConditions()->shouldReturn(array($condition));
	}


	function it_removes_condition(CommandCondition $condition)
	{
		$this->addCondition($condition);
		$this->hasCondition($condition)->shouldReturn(true);

		$this->removeCondition($condition)->shouldReturn($this);
		$this->hasCondition($condition)->shouldReturn(false);
	}


	function it_adds_multiple_conditions(CommandCondition $condition, CommandCondition $conditionB)
	{
		$this->addConditions(array($condition, $conditionB))->shouldReturn($this);
		$this->getConditions()->shouldReturn(array($condition, $conditionB));
	}


	function it_iterates_over_conditions(CommandCondition $condition, CommandCondition $conditionB)
	{
		$conditions = array($condition, $conditionB);

		$this->shouldHaveType('Traversable');
		$this->addConditions($conditions)->shouldReturn($this);
		$this->getIterator()->shouldHaveType('Iterator');
		$this->getIterator()->getArrayCopy()->shouldReturn($conditions);
	}


} 