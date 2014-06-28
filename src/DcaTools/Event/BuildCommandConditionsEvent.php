<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Event;

use DcaTools\Definition\CommandConditionCollection;
use Symfony\Component\EventDispatcher\Event;


/**
 * Class BuildCommandConditionsEvent
 * @package DcaTools\Event
 */
class BuildCommandConditionsEvent extends Event
{
	const NAME = 'dcatools.build-command-conditions';

	/**
	 * @var string
	 */
	private $containerName;

	/**
	 * @var \DcaTools\Definition\CommandConditionCollection
	 */
	private $conditions;


	/**
	 * @param $conditions
	 * @param $containerName
	 */
	function __construct(CommandConditionCollection $conditions, $containerName)
	{
		$this->conditions    = $conditions;
		$this->containerName = $containerName;
	}


	/**
	 * @return string
	 */
	public function getContainerName()
	{
		return $this->containerName;
	}


	/**
	 * @return \DcaTools\Definition\CommandConditionCollection
	 */
	public function getConditions()
	{
		return $this->conditions;
	}

} 