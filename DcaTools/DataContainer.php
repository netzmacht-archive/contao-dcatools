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

namespace Netzmacht\DcaTools;

use Netzmacht\DcaTools\Model\DcGeneralModel;
use Netzmacht\DcaTools\Node\FieldContainer;
use Netzmacht\DcaTools\Node\Node;
use Netzmacht\DcaTools\Palette\Palette;
use Netzmacht\DcaTools\Palette\SubPalette;
use Symfony\Component\EventDispatcher\Event;


/**
 * Class DataContainer
 * @package Netzmacht\DcaTools
 */
class DataContainer extends FieldContainer
{

	/**
	 * @var Palette[]
	 */
	protected $arrPalettes = array();

	/**
	 * @var SubPalette[]
	 */
	protected $arrSubPalettes = array();

	/**
	 * @var Field[]
	 */
	protected $arrSelectors = array();


	/**
	 * @var \Model|\Database\Result|DcGeneralModel
	 */
	protected $objRecord;


	/**
	 * @param $strName
	 */
	public function __construct($strName, $objRecord=null)
	{
		$this->strName = $strName;
		$this->definition =& $GLOBALS['TL_DCA'][$strName];

		if($objRecord !== null)
		{
			$this->setRecord($objRecord);
		}

		$this->addListener('updateSelectors', array($this, 'updateSelectors'));
		$this->addListener('removeFromDataContainer', array($this, 'fieldListener'));
	}


	/**
	 * Get DataContainer
	 *
	 * @return $this|DataContainer
	 */
	public function getDataContainer()
	{
		return $this;
	}


	/**
	 * @return \Database\Result|\Model|DcGeneral\Data\ModelInterface
	 */
	public function getRecord()
	{
		return $this->objRecord;
	}


	/**
	 * @param $objRecord
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function setRecord($objRecord)
	{
		if($objRecord instanceof \Model || $objRecord instanceof \Database\Result)
		{
			$this->objRecord = $objRecord;
		}
		elseif($objRecord instanceof \Model\Collection)
		{
			$this->objRecord = $objRecord->current();
		}
		elseif($objRecord instanceof \DcGeneral\Data\ModelInterface)
		{
			$this->objRecord = new DcGeneralModel($objRecord);
		}
		else {
		throw new \RuntimeException("Type of Record is not supported");
		}

		return $this;
	}


	/**
	 * Extend an existing DataContainer
	 *
	 * @param Node $objNode
	 *
	 * @return $this|void
	 *
	 * @throws \RuntimeException
	 */
	public function extend($objNode)
	{
		if(is_string($objNode))
		{
			$objNode = DcaTools::getDataContainer($objNode);
		}
		elseif(!$objNode instanceof DataContainer)
		{
			throw new \RuntimeException("Node '{$objNode->getName()}' is not a DataContainer");
		}

		$this->arrFields        = array_merge($objNode->getFields(), $this->arrFields);
		$this->arrPalettes      = array_merge($objNode->getPalettes(), $this->arrPalettes);
		$this->arrSubPalettes   = array_merge($objNode->getSubPalettes(), $this->arrSubPalettes);
		$this->arrSelectors     = array_merge($objNode->getSelectors(), $this->arrSelectors);
	}


	/**
	 * Copy node to a new one
	 * @param $strName
	 * @return mixed
	 */
	public function copy($strName)
	{
		$objCopy = parent::copy($strName);
		$objCopy->dispatch('change');

		return $objCopy;
	}


	/**
	 * Get an Field
	 *
	 * @param string $strName
	 *
	 * @return Field
	 *
	 * @throws \RuntimeException if Field does not exists
	 */
	public function getField($strName)
	{
		if($this->hasField($strName))
		{
			if(!isset($this->arrFields[$strName]))
			{
				$this->createField($strName);
			}

			return $this->arrFields[$strName];
		}

		throw new \RuntimeException("Field '$strName' does not exists.");
	}


	/**
	 * @param string $strName
	 *
	 * @return bool
	 */
	public function hasField($strName)
	{
		$strName = is_object($strName) ? $strName->getName() : $strName;

		return isset($this->definition['fields'][$strName]);
	}


	/**
	 * @param string $strName
	 * @param bool $blnFromDataContainer
	 * @return $this
	 */
	public function removeField($strName, $blnFromDataContainer=true)
	{
		$strName = is_object($strName) ? $strName->getName() : $strName;

		if($this->hasField($strName))
		{
			$objField = $this->getField($strName);
			unset($this->arrFields[$strName]);
			$objField->dispatch('removeFromDataContainer');
		}

		return $this;
	}


	/**
	 * @param $strName
	 *
	 * @return Field
	 */
	public function createField($strName)
	{
		$objField = new Field($strName, $this);
		$objField->addListener('removeFromDataContainer', array($this, 'fieldListener'));

		$this->arrFields[$strName] = $objField;
		$this->dispatch('fieldsChange');

		return $this->arrFields[$strName];
	}


	/**
	 * @return Palette[]
	 */
	public function getPalettes()
	{
		foreach(array_keys($this->definition['palettes']) as $strName)
		{
			if($strName == '__selector__')
			{
				continue;
			}

			if(!isset($this->arrPalettes[$strName]))
			{
				$this->getPalette($strName);
			}
		}

		return $this->arrPalettes;
	}


	/**
	 * Get a Palette
	 *
	 * @param $strName
	 *
	 * @return Palette
	 *
	 * @throws \RuntimeException if Palette does not exist
	 */
	public function getPalette($strName)
	{
		if(!isset($this->arrPalettes[$strName]))
		{
			if(!isset($this->definition['palettes'][$strName]))
			{
				throw new \RuntimeException("Palette '$strName' does not exist");
			}

			$this->arrPalettes[$strName] = new Palette($strName, $this);
		}

		return $this->arrPalettes[$strName];
	}


	/**
	 * Add a new Palette to the DataContainer
	 *
	 * @param string|Palette $Palette
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function addPalette($Palette)
	{
		if(!$Palette instanceof Palette)
		{
			$Palette = new Palette($Palette, $this);
		}
		else
		{
			$Palette->setDataContainer($this);
		}

		if(isset($this->arrPalettes[$Palette->getName()]))
		{
			throw new \RuntimeException("Palette '{$Palette->getName()}' already exists.");
		}

		$this->arrPalettes[$Palette->getName()] = $Palette;

		return $this;
	}


	/**
	 * @param $strName
	 *
	 * @return bool
	 */
	public function hasPalette($strName)
	{
		return isset($this->definition['Palettes'][$strName]);
	}


	/**
	 * @param $strName
	 *
	 * @return $this
	 */
	public function removePalette($strName)
	{
		if($this->hasPalette($strName))
		{
			$this->arrPalettes[$strName]->remove();
		}

		unset($this->arrPalettes[$strName]);

		return $this;
	}

	/**
	 * @return SubPalette[]
	 */
	public function getSubPalettes()
	{
		foreach(array_keys($this->definition['subpalettes']) as $strName)
		{
			if(!isset($this->arrSubPalettes[$strName]))
			{
				$this->getSubPalette($strName);
			}
		}

		return $this->arrSubPalettes;
	}


	/**
	 * Get a SubPalette
	 *
	 * @param $strName
	 *
	 * @return SubPalette
	 *
	 * @throws \RuntimeException if SubPalette does not exist
	 */
	public function getSubPalette($strName)
	{
		$strName = is_object($strName) ? $strName->getName() : $strName;

		if(!isset($this->arrSubPalettes[$strName]))
		{
			if(!$this->hasSubPalette($strName))
			{
				throw new \RuntimeException("SubPalette '$strName' does not exist");
			}

			$this->arrSubPalettes[$strName] = new SubPalette($strName, $this);
		}

		return $this->arrSubPalettes[$strName];
	}


	/**
	 * Add a new SubPalette to the DataContainer
	 *
	 * @param string|SubPalette $subPalette
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function addSubPalette($subPalette)
	{
		if(!$subPalette instanceof SubPalette)
		{
			$subPalette = new SubPalette($subPalette, $this);
		}
		else
		{
			$subPalette->setDataContainer($this);
		}

		if(isset($this->arrSubPalettes[$subPalette->getName()]))
		{
			throw new \RuntimeException("SubPalette '{$subPalette->getName()}' already exists.");
		}

		$this->arrSubPalettes[$subPalette->getName()] = $subPalette;

		return $this;
	}


	/**
	 * @param $strName
	 *
	 * @return bool
	 */
	public function hasSubPalette($strName)
	{
		$strName = is_object($strName) ? $strName->getName() : $strName;

		return isset($this->definition['subpalettes'][$strName]);
	}


	/**
	 * @param $strName
	 *
	 * @return $this
	 */
	public function removeSubPalette($strName)
	{
		if($this->hasSubPalette($strName))
		{
			$this->arrSubPalettes[$strName]->dispatch('remove');
			unset($this->arrSubPalettes[$strName]);
		}

		return $this;
	}


	/**
	 * @return Field[]
	 */
	public function getSelectors()
	{
		foreach(array_keys($this->definition['palettes']['__selector__']) as $strName)
		{
			if(!isset($this->arrSelectors[$strName]))
			{
				$this->getField($strName);
			}
		}

		return $this->arrSelectors;
	}


	/**
	 * Get a Selector
	 *
	 * @param $strName
	 *
	 * @return Field
	 *
	 * @throws \RuntimeException if Selector does not exist
	 */
	public function getSelector($strName)
	{
		if(!isset($this->arrSelectors[$strName]))
		{
			if(!$this->hasSelector($strName))
			{
				throw new \RuntimeException("Selector '$strName' does not exist");
			}

			$this->arrSelectors[$strName] = $this->getField($strName);
		}

		return $this->arrSelectors[$strName];
	}


	/**
	 * Add a new Selector to the DataContainer
	 *
	 * @param string|Field $selector
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function addSelector($selector)
	{
		if(!$selector instanceof Field)
		{
			$selector = $this->getField($selector);
		}
		else
		{
			$selector->setDataContainer($this);
		}

		if(isset($this->arrSelectors[$selector->getName()]))
		{
			throw new \RuntimeException("Selector '{$selector->getName()}' already exists.");
		}

		$this->arrSelectors[$selector->getName()] = $selector;

		return $this;
	}


	/**
	 * @param $strName
	 *
	 * @return bool
	 */
	public function hasSelector($strName)
	{
		$strName = is_object($strName) ? $strName->getName() : $strName;

		return in_array($strName, $this->definition['palettes']['__selector__']);
	}


	/**
	 * @param $strName
	 *
	 * @return $this
	 */
	public function removeSelector($strName)
	{
		if($this->hasSelector($strName))
		{
			$this->arrSelectors[$strName]->remove();
		}

		unset($this->arrSelectors[$strName]);

		return $this;
	}


	/**
	 * @param Event $objEvent
	 * @return mixed
	 */
	public function fieldListener(Event $objEvent)
	{
		/** @var $objField Field */
		$objField = $objEvent->getDispatcher();

		if($objEvent->getName() == 'removeFromDataContainer')
		{
			foreach($this->getPalettes() as $objPalette)
			{
				if($objPalette->hasField($objField->getName()))
				{
					$objPalette->removeField($objField->getName());
				}
			}

			foreach($this->getSubPalettes() as $objSubPalette)
			{
				if($objSubPalette->hasField($objField->getName()))
				{
					$objSubPalette->removeField($objField->getName());
				}
			}

			if($objField->isSelector())
			{
				$key = array_search($objField->getName(), $this->definition['palettes']['__selector__']);
				unset($this->definition['palettes']['__selector__'][$key]);
			}
		}
	}

	/**
	 * @return mixed|void
	 */
	public function remove()
	{
		unset($GLOBALS['TL_DCA'][$this->getName()]);
	}

}