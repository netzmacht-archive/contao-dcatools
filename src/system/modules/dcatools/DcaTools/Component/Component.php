<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 10.10.13
 * Time: 15:18
 * To change this template use File | Settings | File Templates.
 */

namespace Netzmacht\DcaTools\Component;

use DcGeneral\Data\ModelInterface;
use Netzmacht\DcaTools\Definition;
use Netzmacht\DcaTools\Event\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Component
 * @package Netzmacht\DcaTools\Component
 */
abstract class Component extends EventDispatcher
{

	/**
	 * @var ModelInterface
	 */
	protected $objModel;


	/**
	 * @var Definition\Node
	 */
	protected $objDefinition;


	/**
	 * Constructor
	 *
	 * @param $objDefinition
	 */
	public function __construct(Definition\Node $objDefinition)
	{
		$this->objDefinition = $objDefinition;
	}


	/**
	 * @return Definition\Node
	 */
	public function getDefinition()
	{
		return $this->objDefinition;
	}

	/**
	 * @param ModelInterface $objModel
	 */
	public function setModel(ModelInterface $objModel)
	{
		$this->objModel = $objModel;
	}


	/**
	 * @return ModelInterface
	 */
	public function getModel()
	{
		return $this->objModel;
	}


	/**
	 * @return bool
	 */
	public function hasModel()
	{
		return ($this->objModel !== null);
	}


	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->objDefinition->getName();
	}

}