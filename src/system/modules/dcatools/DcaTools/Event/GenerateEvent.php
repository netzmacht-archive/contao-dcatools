<?php

namespace DcaTools\Event;

use DcaTools\Component\ControllerInterface;
use Symfony\Component\EventDispatcher\Event;


/**
 * Class GenerateEvent
 * @package DcaTools\Component\Operation
 */
class GenerateEvent extends Event
{

	/**
	 * @var ControllerInterface
	 */
	protected $controller;


	/**
	 * @var string
	 */
	protected $output;


	/**
	 * @param ControllerInterface $controller
	 */
	public function __construct(ControllerInterface $controller)
	{
		$this->controller = $controller;
	}


	/**
	 * @return ControllerInterface
	 */
	public function getController()
	{
		return $this->controller;
	}


	/**
	 * Get current model
	 *
	 * @var \DcGeneral\Data\ModelInterface
	 */
	public function getModel()
	{
		return $this->controller->getModel();
	}


	/**
	 * Shortcut to get the view
	 *
	 * @var \DcaTools\Component\ViewInterface
	 */
	public function getView()
	{
		return $this->controller->getView();
	}


	/**
	 * @param $output
	 */
	public function setOutput($output)
	{
		$this->output = $output;
	}


	/**
	 * @return string|null
	 */
	public function getOutput()
	{
		return $this->output;
	}

}
