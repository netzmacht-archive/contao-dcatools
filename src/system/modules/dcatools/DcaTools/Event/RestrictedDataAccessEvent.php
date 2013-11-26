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

namespace DcaTools\Event;


use DcaTools\DcaTools;

class RestrictedDataAccessEvent extends DcaToolsEvent
{
	protected $arrEntries = array();

	protected $strParentDataContainer;

	protected $arrFields;


	public function __construct(DcaTools $objController, $strParent=null, array $arrFields=null)
	{
		parent::__construct($objController);

		$this->strParentDataContainer = $strParent;
		$this->arrFields = $arrFields;
		$this['ptables'] = array();
	}


	public function getEntries()
	{
		return $this->arrEntries;
	}

	public function addEntry($mixed)
	{
		$this->arrEntries[] = $mixed;
	}

	public function addEntries(array $arrEntries)
	{
		$this->arrEntries = array_merge($this->arrEntries, $arrEntries);
	}


	public function affectsDataContainer($strName)
	{
		if($this->strParentDataContainer !== null && $this->strParentDataContainer != $strName)
		{
			return false;
		}

		return true;
	}

	public function isIdOnly()
	{
		return ($this->arrFields === null);
	}

	public function getFields()
	{
		if($this->isIdOnly())
		{
			return array('id');
		}

		return $this->arrFields;
	}

} 