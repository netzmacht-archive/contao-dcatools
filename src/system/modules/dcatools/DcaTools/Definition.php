<?php

/**
 * DcaTools - Toolkit for data containers in Contao
 * Copyright (C) 2013 David Molineus
 *
 * @package   netzmacht-dcatools
 * @author    David Molineus <molineus@netzmacht.de>
 * @license   LGPL-3.0+
 * @copyright 2013 netzmacht creative David Molineus
 */

namespace DcaTools;

use DcaTools\Definition\DataContainer;
use DcaTools\Definition\Palette;
use DcaTools\Definition\Property;
use DcaTools\Definition\Legend;
use DcaTools\Iterator\ActivePalette;
use DcaTools\Iterator\ActivePropertyContainer;
use DcaTools\Structure\PropertyContainerInterface;
use DcGeneral\Contao\BackendBindings;
use DcGeneral\Data\ModelInterface;


/**
 * Class Definition provide access to definition of
 *
 * @package DcaTools
 */
class Definition
{
	/**
	 * @var int use for injecting element before other one
	 */
	const BEFORE = 1;

	/**
	 * @var int use for injecting element before after one
	 */
	const AFTER  = 2;

	/**
	 * @var int use for injecting element at first place
	 */
	const FIRST  = 4;

	/**
	 * @var int use for injecting element at last place
	 */
	const LAST   = 8;


	/**
	 * Get a data container
	 * @param string $strName
	 *
	 * @return DataContainer
	 *
	 * @throws \RuntimeException
	 */
	public static function getDataContainer($strName)
	{
		BackendBindings::loadDataContainer($strName);
		BackendBindings::loadLanguageFile($strName);

		if(!isset($GLOBALS['TL_DCA'][$strName])) {
			throw new \RuntimeException("DataContainer {$strName} does not exist.");
		}

		return DataContainer::getInstance($strName);
	}


	/**
	 * Get Operation of a DataContainer shortcut
	 *
	 * @param $strTable
	 * @param $strName
	 *
	 * @return Definition\Operation
	 */
	public static function getOperation($strTable, $strName)
	{
		return static::getDataContainer($strTable)->getOperation($strName);
	}


	/**
	 * Get Operation of a DataContainer shortcut
	 *
	 * @param $strTable
	 * @param $strName
	 *
	 * @return Definition\Operation
	 */
	public static function getGlobalOperation($strTable, $strName)
	{
		return static::getDataContainer($strTable)->getGlobalOperation($strName);
	}


	/**
	 * Get Property of a DataContainer shortcut
	 *
	 * @param $strTable
	 * @param $strName
	 *
	 * @return Definition\Property
	 */
	public static function getProperty($strTable, $strName)
	{
		return static::getDataContainer($strTable)->getProperty($strName);
	}


	/**
	 * Get Palette of a DataContainer shortcut
	 *
	 * @param $strTable
	 * @param $strName
	 *
	 * @return Definition\Palette
	 */
	public static function getPalette($strTable, $strName='default')
	{
		return static::getDataContainer($strTable)->getPalette($strName);
	}


	/**
	 * Get SubPalette of a DataContainer shortcut
	 *
	 * @param $strTable
	 * @param $strName
	 *
	 * @return Definition\SubPalette
	 */
	public static function getSubPalette($strTable, $strName)
	{
		return static::getDataContainer($strTable)->getSubPalette($strName);
	}


	/**
	 * Get all Active properties for a PropertyContainer
	 *
	 * @param PropertyContainerInterface $objContainer
	 * @param ModelInterface $objModel
	 * @param bool $blnRecursive
	 * @param bool $blnRecursive Also integrate fields of SubPalettes
	 *
	 * @return \RecursiveIteratorIterator
	 */
	public static function getActivePropertiesFor(PropertyContainerInterface $objContainer, ModelInterface $objModel, $blnRecursive=false)
	{
		if($objContainer instanceof Palette) {
			return new \RecursiveIteratorIterator(new ActivePalette($objContainer, $objModel, $blnRecursive));
		}

		return new \RecursiveIteratorIterator(new ActivePropertyContainer($objContainer, $objModel, $blnRecursive));
	}


	/**
	 * Get the ActivePalette iterator
	 *
	 * @param Palette $objPalette
	 * @param ModelInterface $objModel
	 *
	 * @return ActivePalette
	 */
	public static function getActivePalette(Palette $objPalette, ModelInterface $objModel)
	{
		return new ActivePalette($objPalette, $objModel);
	}


	/**
	 * Get active properties as an array list
	 *
	 * @param PropertyContainerInterface $objContainer
	 * @param ModelInterface $objModel
	 * @param bool $blnRecursive Also integrate fields of SubPalettes
	 *
	 * @return array
	 */
	public static function getActivePropertiesAsArrayFor(PropertyContainerInterface $objContainer, ModelInterface $objModel, $blnRecursive=true)
	{
		$objIterator = static::getActivePropertiesFor($objContainer, $objModel, $blnRecursive);
		$arrProperties = array();

		foreach($objIterator as $strName => $objProperty) {
			$arrProperties[] = $strName;
		}

		return $arrProperties;
	}


	/**
	 * Get Active Palette as array
	 *
	 * @param Palette $objPalette
	 * @param ModelInterface $objModel
	 * @param bool $blnRecursive Also integrate fields of SubPalettes
	 * @param bool $blnIncludeModifiers add modifier like :hide to fields as well (MetaPalettes syntax)
	 *
	 * @return array
	 */
	public static function getActivePaletteAsArray(Palette $objPalette, ModelInterface $objModel, $blnRecursive=true, $blnIncludeModifiers=false)
	{
		$objIterator = static::getActivePalette($objPalette, $objModel);
		$arrPalette = array();

		/** @var Legend $objLegend */
		foreach($objIterator as $strLegend => $objLegend) {
			if($objIterator->hasChildren()) {
				$arrPalette[$strLegend] = static::getActivePropertiesAsArrayFor($objLegend, $objModel, $blnRecursive);

				if($blnIncludeModifiers) {
					$arrModifiers = array_map(
						function($item) {
							return ':' . $item;
						},
						$objLegend->getModifiers()
					);

					$arrPalette[$strLegend] = array_merge($arrModifiers, $arrPalette);
				}
			}
		}

		return $arrPalette;
	}


	/**
	 * Get active properties as comma separated list
	 *
	 * @param PropertyContainerInterface $objContainer
	 * @param ModelInterface $objModel
	 *
	 * @return array
	 */
	public static function getActivePropertiesAsStringFor(PropertyContainerInterface $objContainer, ModelInterface $objModel)
	{
		$objIterator = static::getActivePropertiesFor($objContainer, $objModel);
		$strProperties = array();

		foreach($objIterator as $strName => $objProperty) {
			if($strProperties != '') {
				$strProperties .= ',';
			}

			$strProperties .= $strName;
		}

		return $strProperties;
	}


	/**
	 * Get Active Palette as string
	 *
	 * @param Palette $objPalette
	 * @param ModelInterface $objModel
	 * @param bool $blnIncludeModifiers add modifier like :hide to fields as well (MetaPalettes syntax)
	 *
	 * @return array
	 */
	public static function getActivePaletteAsString(Palette $objPalette, ModelInterface $objModel, $blnIncludeModifiers=true)
	{
		$objIterator = static::getActivePalette($objPalette, $objModel);
		$strPalette = '';

		/** @var Legend $objLegend */
		foreach($objIterator as $strLegend => $objLegend) {
			if($objIterator->hasChildren()) {
				$strModifier = '';

				if($blnIncludeModifiers) {
					$strModifier = implode(':', $objLegend->getModifiers());
					$strModifier = $strModifier == '' ? '' : (':'. $strModifier);
				}

				if($strPalette != '') {
					$strPalette .= ';';
				}

				$strPalette .= sprintf(
					'{%s_legend%s},%s',
					$strLegend,
					$strModifier,
					static::getActivePropertiesAsStringFor($objLegend, $objModel)
				);
			}
		}

		return $strPalette;
	}


	/**
	 * Get active sub palettes
	 *
	 * @param Palette $objPalette
	 * @param ModelInterface $objModel
	 *
	 * @return \DcaTools\Definition\SubPalette[]
	 */
	public static function getActiveSubPalettesFor(Palette $objPalette, ModelInterface $objModel)
	{
		$arrSubPalettes = array();

		foreach($objPalette->getSelectors() as $objProperty) {
			$objSubPalette = static::getActivePropertySubPalette($objProperty, $objModel);

			if($objSubPalette !== null) {
				$arrSubPalettes[$objSubPalette->getName()] = $objSubPalette;
			}
		}

		return $arrSubPalettes;
	}


	/**
	 * Get activated
	 *
	 * @param Property $objProperty
	 * @param ModelInterface $objModel
	 *
	 * @return Definition\SubPalette|null
	 */
	public static function getActivePropertySubPalette(Property $objProperty, ModelInterface $objModel)
	{
		$objProperty->getDataContainer();
		$varValue = $objModel->getProperty($objProperty->getName());

		if($objProperty->hasSubPalette($varValue)) {
			return $objProperty->getSubPalette($varValue);
		}

		return null;
	}

}
