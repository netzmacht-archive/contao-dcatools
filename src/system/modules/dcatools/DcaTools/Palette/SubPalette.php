<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   netzmacht-dcatools
 * @author    netzmacht creative David Molineus
 * @license   MPL/2.0
 * @copyright 2013 netzmacht creative David Molineus
 */

namespace Netzmacht\DcaTools\Palette;

use Netzmacht\DcaTools\DataContainer;
use Netzmacht\DcaTools\DcaTools;
use Netzmacht\DcaTools\Node\FieldContainer;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class SubPalette
 *
 * @package Netzmacht\Prototype\Palette
 */
class SubPalette extends FieldContainer
{

	/**
	 * @param string $strName
	 * @param DataContainer $objDataContainer
	 */
	public function __construct($strName, DataContainer $objDataContainer)
	{
		$arrDefinition =& $objDataContainer->getDefinition();

		parent::__construct($strName, $objDataContainer, $arrDefinition['subpalettes'][$strName]);

		$this->loadFromDefinition();
	}


	/**
	 * Remove child from parent
	 * @return mixed
	 */
	public function remove()
	{
		$this->getDataContainer()->removeSubPalette($this);
	}

	/**
	 * Extend an existing node of the same type
	 *
	 * @param SubPalette $node
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function extend($node)
	{
		if(is_string($node))
		{
			$node = $this->getDataContainer()->getSubPalette($node);
		}
		elseif(get_class($node) != get_class($this))
		{
			throw new \RuntimeException("Node '{$node->getName()}' is not the same Node type");
		}

		/** @var $objNode FieldContainer */

		$this->arrFields = array_merge($this->arrFields, $node->getFields());
		$this->dispatch('change');

		return $this;
	}


	/**
	 * Load fields from definition
	 */
	protected function loadFromDefinition()
	{
		$arrFields = explode(',', $this->getDefinition());

		foreach($arrFields as $strField)
		{
			$this->addField($strField);
		}
	}
}