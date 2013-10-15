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

namespace DcaTools;


use DcaTools\Definition\DataContainer;
use DcaTools\Definition\Palette;
use DcaTools\Definition\Property;
use DcaTools\Iterator\ActiveProperties;
use DcaTools\Iterator\ActiveSubPalettes;
use DcaTools\Structure\PropertyContainerInterface;
use DcGeneral\Data\ModelInterface;

/**
 * Class Definition provide access to definition of
 *
 * @package DcaTools
 */
class Definition
{

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
		if(!isset($GLOBALS['TL_DCA'][$strName]))
		{
			throw new \RuntimeException("DataContainer {$strName} does not exist or is not loaded.");
		}

		return DataContainer::getInstance($strName);
	}


	/**
	 * Get Operation of a DataContainer shortcut
	 *
	 * @param $strTable
	 * @param $strName
	 * @param $strScope
	 *
	 * @return Definition\Operation
	 */
	public static function getOperation($strTable, $strName, $strScope='local')
	{
		return static::getDataContainer($strTable)->getOperation($strName, $strScope);
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
	public static function getPalette($strTable, $strName)
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
	 *
	 * @return ActiveProperties
	 */
	public static function getActivePropertiesFor(PropertyContainerInterface $objContainer, ModelInterface $objModel, $blnRecursive=false)
	{
		return new ActiveProperties($objContainer->getProperties(), $objModel, $blnRecursive);
	}



	/**
	 * Get active sub palettes
	 *
	 * @param Palette $objPalette
	 * @param ModelInterface $objModel
	 *
	 * @return ActiveSubPalettes
	 */
	public static function getActiveSubPalettesFor(Palette $objPalette, ModelInterface $objModel)
	{
		return new ActiveSubPalettes($objPalette, $objModel);
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
		$objDataContainer = $objProperty->getDataContainer();

		if($objProperty->isSelector())
		{
			if($objModel->getProperty($objProperty->getName()) == 1 && $objDataContainer->hasSubPalette($objProperty->getName()))
			{
				return $objDataContainer->getSubPalette($objProperty->getName());
			}
			else
			{
				$strSubPalette = $objProperty->getName() . '_' . $objModel->getProperty($objProperty->getName());

				if($objDataContainer->hasSubPalette($strSubPalette))
				{
					return $objDataContainer->getSubPalette($strSubPalette);
				}
			}
		}

		return null;
	}

}