<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Condition\Command;


use DcaTools\Condition\Command;


abstract class AbstractCondition implements CommandCondition
{
	/**
	 * @var array
	 */
	protected $config = array();


	/**
	 * @param array $config
	 */
	public function __construct(array $config=array())
	{
		$this->config  = array_merge($this->config, $config);
	}

} 