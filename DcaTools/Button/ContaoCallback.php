<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 01.10.13
 * Time: 10:49
 * To change this template use File | Settings | File Templates.
 */

namespace Netzmacht\DcaTools\Button;

use Netzmacht\DcaTools\Event\Event;


/**
 * Class ContaoCallback
 * @package Netzmacht\DcaTools\Button
 */
class ContaoCallback extends ButtonEvent
{

	/**
	 * @var array
	 */
	protected $arrCallback;


	/**
	 * @param Config $objConfig
	 */
	public function __construct(\Symfony\Component\EventDispatcher\Event $objEvent, $arrCallback)
	{
		$this->objButton = $objEvent->getDispatcher();

		$this->arrCallback = $arrCallback;
	}


	/**
	 * @return mixed|void
	 */
	public function execute()
	{
		$objCallback = new $this->arrCallback[0]();

		$this->objButton->setBuffer($objCallback->{$this->arrCallback[1]}(
			$this->objButton->row,
			$this->objButton->href,
			$this->objButton->label,
			$this->objButton->title,
			$this->objButton->icon,
			$this->objButton->attributes,
			$this->objButton->table,
			null,
			null,
			false,
			null,
			null,
			null
		));
	}

}