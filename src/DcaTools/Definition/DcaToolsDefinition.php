<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition;

use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\DefinitionInterface;
use DcaTools\Condition\Command;
use DcaTools\Definition\CommandConditionCollection;


/**
 * Class DcaToolsDefinition
 * @package DcaTools\Definition
 */
class DcaToolsDefinition implements DefinitionInterface
{
	const NAME = 'dcatools';

	/**
	 * @var CommandConditionCollection
	 */
	private $commandConditions;

	/**
	 * @var PermissionConditionCollection
	 */
	private $permissionConditions;

	/**
	 * @var bool
	 */
	private $legacyMode = false;


	/**
	 *
	 */
	public function __construct(
		CommandConditionCollection $commandConditions=null,
		PermissionConditionCollection $permissionConditions=null
	) {
		$this->commandConditions    = $commandConditions?: new CommandConditionCollection();
		$this->permissionConditions = $permissionConditions ?: new PermissionConditionCollection();
	}

	/**
	 * @param mixed $legacyMode
	 */
	public function setLegacyMode($legacyMode)
	{
		$this->legacyMode = $legacyMode;
	}


	/**
	 * @return mixed
	 */
	public function getLegacyMode()
	{
		return $this->legacyMode;
	}


	/**
	 * @return CommandConditionCollection
	 */
	public function getCommandConditions()
	{
		return $this->commandConditions;
	}


	/**
	 * @return PermissionConditionCollection
	 */
	public function getPermissionConditions()
	{
		return $this->permissionConditions;
	}

}