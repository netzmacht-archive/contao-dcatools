<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Dca\Button\Condition;


abstract class AbstractCondition implements DispatchesByInvoke
{
	/**
	 * @var array
	 */
	protected $config = array();

	/**
	 * @var ConditionManager
	 */
	protected $manager;

	/**
	 * @param ConditionManager $manager
	 * @param array $config
	 */
	public function __construct(ConditionManager $manager, array $config=array())
	{
		$this->config  = array_merge($this->config, $config);
		$this->manager = $manager;
	}

} 