<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 01.10.13
 * Time: 10:51
 * To change this template use File | Settings | File Templates.
 */

namespace Netzmacht\DcaTools\Event;

use Netzmacht\DcaTools\Event\Config;
use Netzmacht\DcaTools\Event\Event;


/**
 * Class ButtonEvent is base class for a button event. You do not have to use it, You can register every event as you
 * want.
 *
 * @package Netzmacht\DcaTools\Button
 */
abstract class Button extends Event
{

	/**
	 * @var Button
	 */
	protected $objButton;


	/**
	 * Constructor
	 *
	 * @param Config $objConfig
	 */
	public function __construct(Config $objConfig=null)
	{
		parent::__construct($objConfig);

		$this->objButton = $this->getDispatcher();
	}

	/**
	 * Execute the Button event
	 *
	 * @return mixed
	 */
	abstract public function execute();

}