<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   netzmacht-dcatools
 * @author    netzmacht creative David Molineus
 * @license   LGPL/3.0
 * @copyright 2013 netzmacht creative David Molineus
 */

namespace DcaTools\Component;

use DcGeneral\Data\DefaultModel;
use DcGeneral\Data\ModelInterface;
use DcaTools\Definition;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Component
 * @package DcaTools\Component
 */
abstract class Component
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
	 * @var
	 */
	protected $objDispatcher;


	/**
	 * Constructor
	 *
	 * @param $objDefinition
	 * @param EventDisPatcher $objDispatcher
	 */
	protected function __construct(Definition\Node $objDefinition, EventDispatcher $objDispatcher)
	{
		$this->objDefinition = $objDefinition;
		$this->objDispatcher = $objDispatcher;
	}


	/**
	 * @return Definition\Node
	 */
	public function getDefinition()
	{
		return $this->objDefinition;
	}

	/**
	 * @param $objModel
	 * @param string|null $table
	 *
	 * @throws \RuntimeException
	 *
	 * @return $this
	 */
	public function setModel($objModel, $table=null)
	{
		if($objModel instanceof \Model\Collection || $objModel instanceof \Model || $objModel instanceof \Database\Result)
		{
			/** @var \Model|\Model\Collection|\Database\Result $objModel */
			$this->objModel = new DefaultModel();
			$this->objModel->setPropertiesAsArray($objModel->row());

			if($objModel instanceof \Database\Result)
			{
				$this->objModel->setProviderName($table);
			}
			else {
				$this->objModel->setProviderName($objModel->getTable());
			}
		}
		elseif(is_array($objModel))
		{
			$this->objModel = new DefaultModel();
			$this->objModel->setPropertiesAsArray($objModel);
			$this->objModel->setID($objModel['id']);

			$this->objModel->setProviderName($table);
		}
		elseif($objModel instanceof ModelInterface)
		{
			$this->objModel = $objModel;
		}
		else {
			throw new \RuntimeException("Type of Model is not supported");
		}

		return $this;
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
	 * @return string
	 */
	public function getName()
	{
		return $this->objDefinition->getName();
	}

}