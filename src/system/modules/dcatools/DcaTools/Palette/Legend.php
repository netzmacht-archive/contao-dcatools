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
use Netzmacht\DcaTools\Node\FieldContainer;
use Netzmacht\DcaTools\Palette\Palette;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class Legend represents legends of a palette
 * @package Netzmacht\DcaTools\Palette
 */
class Legend extends FieldContainer
{

	/**
	 * Modifiers like hide
	 * @var array
	 */
	protected $arrModifiers = array();


	/**
	 * @var Palette
	 */
	protected $objPalette;


	/**
	 * Constructor
	 *
	 * @param $strName
	 * @param DataContainer $objDataContainer
	 * @param Palette $objPalette
	 */
	public function __construct($strName, DataContainer $objDataContainer, Palette $objPalette)
	{
		// no fields stored so far
		$definition = '';

		parent::__construct($strName, $objDataContainer, $definition);

		$this->objPalette = $objPalette;
	}


	/**
	 * @return Palette
	 */
	public function getPalette()
	{
		return $this->objPalette;
	}


	/**
	 * @param Palette $objPalette
	 */
	public function setPalette(Palette $objPalette)
	{
		$this->objPalette = $objPalette;
	}


	/**
	 * Add an modifer
	 * @param string $strName
	 *
	 * @return $this
	 */
	public function addModifier($strName)
	{
		if(!in_array($strName, $this->arrModifiers))
		{
			$this->arrModifiers[] = $strName;
		}

		return $this;
	}


	/**
	 * Check if an modifier exists
	 *
	 * @param $strName
	 *
	 * @return bool
	 */
	public function hasModifier($strName)
	{
		return in_array($strName, $this->arrModifiers);
	}


	/**
	 * Get all modifiers
	 *
	 * @return array
	 */
	public function getModifiers()
	{
		return $this->arrModifiers;
	}


	/**
	 * Append legend to a Palette
	 *
	 * @param Palette $objPalette
	 * @param null|Legend $reference
	 * @param null $intPosition
	 *
	 * @return $this
	 */
	public function appendTo(Palette $objPalette, $reference=null, $intPosition=Legend::POS_LAST)
	{
		$objPalette->addLegend($this, $reference, $intPosition);

		return $this;
	}


	/**
	 * Remove legend
	 *
	 * @return $this;
	 */
	public function remove()
	{
		$this->getPalette()->removeLegend($this);

		return $this;
	}


	/**
	 * Move Palette to new place
	 *
	 * @param Legend|string $reference
	 *
	 * @return $this
	 */
	public function appendAfter($reference)
	{
		$this->getPalette()->moveLegend($this, $reference, static::POS_AFTER);

		return $this;
	}


	/**
	 * Move Palette to new place
	 *
	 * @param Legend|string $reference
	 *
	 * @return $this
	 */
	public function appendBefore($reference)
	{
		$this->getPalette()->moveLegend($this, $reference, static::POS_BEFORE);

		return $this;
	}


	/**
	 * Export as string
	 *
	 * @param bool $blnActive
	 *
	 * @return mixed|string
	 */
	public function toString($blnActive=false)
	{
		if(empty($this->arrFields))
		{
			return '';
		}

		$strModifier = implode(':', $this->arrModifiers);
		$strModifier = $strModifier == '' ? '' : (':'. $strModifier);

		$arrFields = array_keys($blnActive ? $this->getActiveFields() : $this->getFields());

		return sprintf('{%s_legend%s},%s', $this->getName(), $strModifier, implode(',', $arrFields));
	}


	/**
	 * Export as array, following MetaPalettes syntax
	 *
	 * @param bool $blnActive
	 *
	 * @return array
	 */
	public function toArray($blnActive=false)
	{
		$arrModifiers = array_map(
			function($item) {
				return ':' . $item;
			},
			$this->arrModifiers
		);

		$arrFields = array_keys($blnActive ? $this->getActiveFields() : $this->getFields());

		return array_merge($arrModifiers, $arrFields);
	}


	/**
	 * Extend an existing node of the same type
	 *
	 * @param Legend $node
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function extend($node)
	{
		if(is_string($node))
		{
			$objNode = $this->getPalette()->getLegend($node);
		}
		elseif(get_class($node) != get_class($this))
		{
			throw new \RuntimeException("Node '{$node->getName()}' is not the same Node type");
		}

		$this->arrFields = array_merge($this->arrFields, $node->getFields());
		$this->dispatch('change');

		return $this;
	}

}
