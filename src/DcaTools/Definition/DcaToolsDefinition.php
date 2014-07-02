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
use DcaTools\Definition\Command\CommandConditions;
use DcaTools\Definition\Command\Condition;
use DcaTools\Definition\Command\GlobalCommandConditions;
use DcaTools\Definition\Permission\PermissionConditions;


/**
 * Class DcaToolsDefinition
 * @package DcaTools\Definition
 */
class DcaToolsDefinition implements DefinitionInterface
{
	const NAME = 'dcatools';

	/**
	 * @var CommandConditions
	 */
	private $commandConditions;

	/**
	 * @var PermissionConditions
	 */
	private $permissionConditions;

	/**
	 * @var bool
	 */
	private $legacyMode = false;

	/**
	 * @var array
	 */
	private $callbacks = array();

	/**
	 * @var CommandConditions
	 */
	private $globalCommandConditions;


	/**
	 *
	 */
	public function __construct(
		CommandConditions $commandConditions=null,
		GlobalCommandConditions $globalCommandConditions=null,
		PermissionConditions $permissionConditions=null
	) {
		$this->commandConditions       = $commandConditions ?: new CommandConditions();
		$this->globalCommandConditions = $globalCommandConditions ?: new GlobalCommandConditions();
		$this->permissionConditions    = $permissionConditions ?: new PermissionConditions();
	}

	/**
	 * @param mixed $legacyMode
	 * @return $this
	 */
	public function setLegacyMode($legacyMode)
	{
		$this->legacyMode = $legacyMode;

		return $this;
	}


	/**
	 * @return mixed
	 */
	public function getLegacyMode()
	{
		return $this->legacyMode;
	}


	/**
	 * @return CommandConditions
	 */
	public function getCommandConditions()
	{
		return $this->commandConditions;
	}


	/**
	 * @return CommandConditions
	 */
	public function getGlobalCommandConditions()
	{
		return $this->globalCommandConditions;
	}


	/**
	 * @return PermissionConditions
	 */
	public function getPermissionConditions()
	{
		return $this->permissionConditions;
	}


	/**
	 * @param array $callbacks
	 * @return $this;
	 */
	public function setCallbacks(array $callbacks)
	{
		$this->callbacks = $callbacks;

		return $this;
	}


	/**
	 * @return array
	 */
	public function getCallbacks()
	{
		return $this->callbacks;
	}

}