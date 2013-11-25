<?php

namespace DcaTools\Component;

use DcaTools\Definition;
use DcGeneral\Data\ModelInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractController implements ControllerInterface
{

	/**
	 * @var ViewInterface
	 */
	protected $view;

	/**
	 * @var \DcGeneral\Data\ModelInterface
	 */
	protected $model;

	/**
	 * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
	 */
	protected $dispatcher;

	/**
	 * @var \DcaTools\Definition\Node
	 */
	protected $definition;

	/**
	 * @var array
	 */
	protected $config = array();


	/**
	 * Construct
	 *
	 * @param Definition\Node $definition
	 * @param EventDispatcherInterface $dispatcher
	 */
	public function __construct(Definition\Node $definition, EventDispatcherInterface $dispatcher)
	{
		$this->definition = $definition;
		$this->dispatcher = $dispatcher;

	}


	/**
	 * @param ModelInterface $model
	 * @return mixed|void
	 */
	public function setModel(ModelInterface $model)
	{
		$this->model = $model;
	}


	/**
	 * @return ModelInterface
	 */
	public function getModel()
	{
		return $this->model;
	}


	/**
	 * @return Definition\Node
	 */
	public function getDefinition()
	{
		return $this->definition;
	}


	/**
	 * @param ViewInterface $view
	 * @return mixed|void
	 */
	public function setView(ViewInterface $view)
	{
		$this->view = $view;
	}


	/**
	 * @return ViewInterface
	 */
	public function getView()
	{
		return $this->view;
	}


	/**
	 * @param $name
	 * @param $value
	 */
	public function setConfigAttribute($name, $value)
	{
		$this->config[$name] = $value;
	}


	/**
	 * @param $name
	 * @param $default=null
	 * @return mixed
	 */
	public function getConfigAttribute($name, $default=null)
	{
		if(isset($this->config[$name]))
		{
			return $this->config[$name];
		}

		return $default;
	}


	/**
	 * @param array $config
	 * @return mixed|void
	 */
	public function setConfig(array $config)
	{
		$this->config = array_merge($this->config, $config);
	}


	/**
	 * @return array
	 */
	public function getConfig()
	{
		return $this->config;
	}


	/**
	 * @return EventDispatcherInterface
	 */
	public function getEventDispatcher()
	{
		return $this->dispatcher;
	}


	/**
	 * @return string
	 */
	public function generate()
	{
		/** @var \DcaTools\Event\GenerateEvent $event */
		list($eventName, $event) = $this->createGenerateEvent();
		$this->dispatcher->dispatch($eventName, $event);

		if($this->view->isVisible())
		{
			$output = $event->getOutput();

			// Allow events to render an output. This is used for BC compatibility
			if($output !== null)
			{
				return $output;
			}

			$this->compile();
		}

		return $this->view->generate();
	}


	/**
	 * Compile the view
	 */
	abstract protected function compile();


	/**
	 * Return an array which contains the event name and the used event object
	 *
	 * @return array
	 */
	abstract protected function createGenerateEvent();

}
