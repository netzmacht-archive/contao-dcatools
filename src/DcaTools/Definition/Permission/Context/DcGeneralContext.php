<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Permission\Context;


use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\IdSerializer;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\ListView;
use ContaoCommunityAlliance\DcGeneral\Data\CollectionInterface;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use ContaoCommunityAlliance\DcGeneral\View\ViewInterface;
use DcaTools\Data\ConfigBuilder;
use DcaTools\Definition\Permission\Context;

class DcGeneralContext implements Context
{
	/**
	 * @var ViewInterface
	 */
	private $view;

	/**
	 * @var EnvironmentInterface
	 */
	private $environment;

	/**
	 * @param $environment
	 * @param $view
	 */
	function __construct(EnvironmentInterface $environment, ViewInterface $view)
	{
		$this->environment = $environment;
		$this->view        = $view;
	}


	/**
	 * @return ModelInterface
	 */
	public function getParent()
	{
		$name   = $this->environment->getParentDataDefinition()->getName();
		$input  = $this->environment->getInputProvider();

		if($input->hasParameter('id')) {
			$id = IdSerializer::fromSerialized($input->getParameter('id'));

			if($id->getDataProviderName() == $name) {
				return ConfigBuilder::create($this->environment, $name)->setId($id)->fetch();
			}
			else {
				$model  = ConfigBuilder::create($this->environment)->setId($id)->fetch();

				return ConfigBuilder::create($this->environment, $name)->setId($model->getProperty('pid'))->fetch();
			}
		}

		return null;
	}


	/**
	 * @return ModelInterface
	 */
	public function getModel()
	{
		$input  = $this->environment->getInputProvider();
		$id     = IdSerializer::fromSerialized($input->getParameter('id'));
		$name   = $id->getDataProviderName();

		return ConfigBuilder::create($this->environment, $name)
			->setId($id->getId())
			->fetch();
	}


	/**
	 * @return CollectionInterface|null
	 */
	public function getCollection()
	{
		if($this->hasCollection()) {
			/** @var ListView $view */
			$view = $this->view;

			return $view->loadCollection();
		}

		return null;
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
		return ($this->view instanceof ListView);
	}

	/**
	 * @return mixed
	 */
	public function isParentMode()
	{
		// TODO: Implement isParentMode() method.
	}


} 