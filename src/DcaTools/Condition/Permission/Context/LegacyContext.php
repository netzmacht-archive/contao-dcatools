<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Condition\Permission\Context;


use ContaoCommunityAlliance\DcGeneral\Data\CollectionInterface;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Data\ConfigBuilder;
use DcaTools\Condition\Permission\Context;

class LegacyContext implements Context
{
	/**
	 * @var EnvironmentInterface
	 */
	private $environment;

	/**
	 * @var CollectionInterface
	 */
	private $collection;


	/**
	 * @param EnvironmentInterface $environment
	 * @param CollectionInterface $collection
	 */
	function __construct(EnvironmentInterface $environment, CollectionInterface $collection=null)
	{
		$this->environment = $environment;
		$this->collection  = $collection;
	}


	/**
	 * @return ModelInterface
	 */
	public function getParent()
	{
		$name        = $this->environment->getParentDataDefinition()->getName();
		$input       = $this->environment->getInputProvider();

		if($input->hasParameter('act')) {
			$id = CURRENT_ID;
		}
		else {
			if($input->hasParameter('id')) {
				$id = $input->getParameter('id');
			}
			else {
				$id = CURRENT_ID;
			}
		}

		return ConfigBuilder::create($this->environment, $name)->setId($id)->fetch();
	}


	/**
	 * @throws \RuntimeException
	 * @return ModelInterface
	 */
	public function getModel()
	{
		$input       = $this->environment->getInputProvider();
		$id 		 = $input->getParameter('id');

		return ConfigBuilder::create($this->environment)->setId($id)->fetch();
	}


	/**
	 * @return bool
	 */
	public function hasCollection()
	{
		return ($this->collection !== null);
	}


	/**
	 * @return CollectionInterface|null
	 */
	public function getCollection()
	{
		return $this->collection;
	}

	/**
	 * Check if current request matches the given context string
	 *
	 * @param $context
	 * @return mixed
	 */
	public function match($context)
	{
		// TODO: Implement match() method.
	}

	/**
	 * @return bool
	 */
	public function isListView()
	{
		// TODO: Implement isListView() method.
	}

	/**
	 * @return mixed
	 */
	public function isParentMode()
	{
		// TODO: Implement isParentMode() method.
	}

} 