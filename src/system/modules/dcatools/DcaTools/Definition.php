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
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Definition provide access to definition of
 *
 * @package DcaTools
 */
class Definition
{

	/**
	 * @var DataContainer[]
	 */
	protected static $arrDataContainers = array();


	/**
	 * Get a data container
	 * @param string $strName
	 * @param null $objModel
	 *
	 * @return DataContainer
	 *
	 * @throws \RuntimeException
	 */
	public static function getDataContainer($strName, $objModel=null)
	{
		if(!isset($GLOBALS['TL_DCA'][$strName]))
		{
			throw new \RuntimeException("DataContainer {$strName} does not exist or is not loaded.");
		}

		if(!isset(static::$arrDataContainers[$strName]))
		{
			static::$arrDataContainers[$strName] = new DataContainer($strName, $objModel);
		}

		return static::$arrDataContainers[$strName];
	}


	/**
	 * Get Operation of a DataContainer shortcut
	 *
	 * @param $strTable
	 * @param $strName
	 * @param $strScope
	 *
	 * @return Definition/Operation
	 */
	public function getOperation($strTable, $strName, $strScope='local')
	{
		return static::getDataContainer($strTable)->getOperation($strName, $strScope);
	}


	/**
	 * Get Property of a DataContainer shortcut
	 *
	 * @param $strTable
	 * @param $strName
	 *
	 * @return Definition/Operation
	 */
	public function getProperty($strTable, $strName)
	{
		return static::getDataContainer($strTable)->getProperty($strName);
	}


	/**
	 * Get Palette of a DataContainer shortcut
	 *
	 * @param $strTable
	 * @param $strName
	 *
	 * @return Definition/Operation
	 */
	public function getPalette($strTable, $strName)
	{
		return static::getDataContainer($strTable)->getPalette($strName);
	}


	/**
	 * Get SubPalette of a DataContainer shortcut
	 *
	 * @param $strTable
	 * @param $strName
	 *
	 * @return Definition/Operation
	 */
	public function getSubPalette($strTable, $strName)
	{
		return static::getDataContainer($strTable)->getSubPalette($strName);
	}

}