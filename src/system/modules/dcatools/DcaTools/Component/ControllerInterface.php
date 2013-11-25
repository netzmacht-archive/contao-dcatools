<?php

namespace DcaTools\Component;

use DcGeneral\Data\ModelInterface;

/**
 * Interface ControllerInterface
 * @package DcaTools\Component
 */
interface ControllerInterface
{
	/**
	 * Get definition of current component
	 *
	 * @return \DcaTools\Definition\Node
	 */
	public function getDefinition();


	/**
	 * @param ModelInterface $model
	 * @return mixed
	 */
	public function setModel(ModelInterface $model);


	/**
	 * @return \DcGeneral\Data\ModelInterface
	 */
	public function getModel();


	/**
	 * @param ViewInterface $view
	 * @return mixed
	 */
	public function setView(ViewInterface $view);


	/**
	 * @return ViewInterface
	 */
	public function getView();


	/**
	 * @param array $config
	 * @return mixed
	 */
	public function setConfig(array $config);

	/**
	 * @return array
	 */
	public function getConfig();


	/**
	 * @param $name
	 * @param $value
	 */
	public function setConfigAttribute($name, $value);


	/**
	 * @param $name
	 * @return mixed
	 */
	public function getConfigAttribute($name);


	/**
	 * @return \Symfony\COmponent\EventDispatcher\EventDispatcherInterface
	 */
	public function getEventDispatcher();


	/**
	 * @return string
	 */
	public function generate();

} 