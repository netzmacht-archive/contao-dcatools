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

namespace Netzmacht\DcaTools\Model;

/**
 * Class DcGeneralModel is a wrapper for DC_General ModelInterfaces which provides an Contao style access to properties
 *
 * @package Netzmacht\DcaTools\Model
 */
class DcGeneralModel
{

	/**
	 * @var \DcGeneral\Data\ModelInterface
	 */
	protected $objModel;


	/**
	 * @param $objModel
	 */
	public function __construct($objModel)
	{
		$this->objModel = $objModel;
	}


	/**
	 * @param $strKey
	 *
	 * @return mixed
	 */
	public function __get($strKey)
	{
		return $this->objModel->getProperty($strKey);
	}


	/**
	 * @param $strKey
	 *
	 * @param $mixedValue
	 */
	public function __set($strKey, $mixedValue)
	{
		$this->objModel->setProperty($strKey, $mixedValue);
	}


	/**
	 * @return mixed
	 */
	public function getModel()
	{
		return $this->getModel();
	}


	/**
	 * @return array
	 */
	public function getRow()
	{
		return $this->objModel->getPropertiesAsArray();
	}

}