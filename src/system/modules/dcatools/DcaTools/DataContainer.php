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

use Netzmacht\DcaTools\Operation;
use Netzmacht\DcaTools\Model\DcGeneralModel;
use Netzmacht\DcaTools\Node\FieldAccess;
use Netzmacht\DcaTools\Node\FieldContainer;
use Netzmacht\DcaTools\Node\Node;
use Netzmacht\DcaTools\Palette\Palette;
use Netzmacht\DcaTools\Palette\SubPalette;
use Symfony\Component\EventDispatcher\Event;


/**
 * Class DataContainer
 * @package Netzmacht\DcaTools
 */
class DataContainer extends FieldContainer implements FieldAccess
{

	/**
	 * @var Operation[]
	 */
	protected $arrOperations = array();

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
	 * Constructor
	 *
	 * @param $strName
	 * @param \Model|\Database\Result|\Model|\DcGeneral\Data\ModelInterface|null $objRecord
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
	 * @return \Database\Result|\Model|\DcGeneral\Data\ModelInterface
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
	 * return @bool
	 */
	public function hasRecord()
	{
		return ($this->objRecord !== null);
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

		$arrSelectors = $objNode->getSelectors();

		// extend Fields and make sure that and Fields are cloned
		foreach($objNode->getFields() as $strField => $objField)
		{
			if(isset($this->arrFields[$strField]))
			{
				$this->arrFields[$strField]->extend($objField);
			}
			else
			{
				$this->arrFields[$strField] = clone $objField;
				$this->arrFields[$strField]->setDataContainer($this);
			}

			if(isset($arrSelectors[$strField]))
			{
				$this->arrSelectors[$strField] = $this->arrFields[$strField];
			}
		}

		// extend Palettes and make sure that Legends and Fields are also combined
		foreach($objNode->getPalettes() as $strPalette => $objPalette)
		{
			if(isset($this->arrPalettes[$strPalette]))
			{
				$this->arrPalettes[$strPalette]->extend($objPalette);
			}
			else {
				$this->arrPalettes[$strPalette] = $objPalette;
			}
		}

		// extend SubPalettes and make sure that Legends and Fields are also combined
		foreach($objNode->getSubPalettes() as $strPalette => $objPalette)
		{
			if(isset($this->arrSubPalettes[$strPalette]))
			{
				$this->arrSubPalettes[$strPalette]->extend($objPalette);
			}
			else {
				$this->arrSubPalettes[$strPalette] = $objPalette;
			}
		}

		$this->dispatch('change');
	}


	/**
	 * Copy node to a new one
	 * @param $strName
	 * @return mixed
	 */
	public function copy($strName=null)
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
			$objField->dispatch('delete');
		}

		return $this;
	}


	/**
	 * @param string $strName
	 *
	 * @return Field
	 */
	public function createField($strName)
	{
		$objField = new Field($strName, $this);
		$objField->addListener('delete', array($this, 'fieldListener'));

		$this->arrFields[$strName] = $objField;
		$this->dispatch('change');

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
	 * @param string|Palette $palette
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function addPalette($palette)
	{
		if(!$palette instanceof Palette)
		{
			$palette = new Palette($palette, $this);
			$strEvent = 'create';
		}
		else
		{
			$palette->setDataContainer($this);
			$strEvent = 'move';
		}

		if(isset($this->arrPalettes[$palette->getName()]))
		{
			throw new \RuntimeException("Palette '{$palette->getName()}' already exists.");
		}

		$palette->addListener('create', array($this, 'paletteListener'));
		$palette->addListener('change', array($this, 'paletteListener'));
		$palette->addListener('move',   array($this, 'paletteListener'));
		$palette->addListener('remove', array($this, 'paletteListener'));

		$this->arrPalettes[$palette->getName()] = $palette;
		$this->arrPalettes[$palette->getName()]->dispatch($strEvent);

		return $this;
	}

	/**
	 * Create a new Palette
	 *
	 * @param $strName
	 *
	 * @return Palette
	 *
	 * @throws \RuntimeException
	 */
	public function createPalette($strName)
	{
		$this->createPalette($strName);

		return $this->getPalette($strName);
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
			$objPalette = $this->arrPalettes[$strName];
			unset($this->arrPalettes[$strName]);
			$objPalette->dispatch('remove');
		}

		return $this;
	}


	/**
	 * Create a new SubPalette
	 *
	 * @param $strName
	 *
	 * @return SubPalette
	 *
	 * @throws \RuntimeException
	 */
	public function createSubPalette($strName)
	{
		$this->addSubPalette($strName);

		return $this->getSubPalette($strName);
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
			$strEvent = 'create';
		}
		else
		{
			$subPalette->setDataContainer($this);
			$strEvent = 'move';
		}

		if(isset($this->arrSubPalettes[$subPalette->getName()]))
		{
			throw new \RuntimeException("SubPalette '{$subPalette->getName()}' already exists.");
		}

		$subPalette->addListener('create', array($this, 'subPaletteListener'));
		$subPalette->addListener('change', array($this, 'subPaletteListener'));
		$subPalette->addListener('move',   array($this, 'subPaletteListener'));
		$subPalette->addListener('remove', array($this, 'subPaletteListener'));

		$this->arrSubPalettes[$subPalette->getName()] = $subPalette;
		$this->arrSubPalettes[$subPalette->getName()]->dispatch($strEvent);

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
			$objSubPalette = $this->arrSubPalettes[$strName];
			unset($this->arrSubPalettes[$strName]);
			$objSubPalette->dispatch('remove');
		}

		return $this;
	}


	/**
	 * @return Field[]
	 */
	public function getSelectors()
	{
		foreach($this->definition['palettes']['__selector__'] as $strName)
		{
			if(!isset($this->arrSelectors[$strName]))
			{
				$this->arrSelectors[$strName] = $this->getField($strName);
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
		// only unset, because field can still exists
		unset($this->arrSelectors[$strName]);

		return $this;
	}


	/**
	 * Get all operations
	 *
	 * @param string $strScope global for global operations else local one will be loaded
	 */
	public function getOperations($strScope='local')
	{
		if($strScope == 'global')
		{
			$strConfig = 'global_operations';
			$strCallback = 'globalOperationCallback';
		}
		else {
			$strConfig = 'operations';
			$strCallback = 'operationCallback';
		}

		// add operation callback for every operation
		foreach($this->definition['list'][$strConfig] as $strOperation => $arrDefinition)
		{
			// operation already exists
			if(!$this->hasOperation($strOperation) || isset($this->arrOperations[$strScope][$strOperation]))
			{
				continue;
			}

			// make sure that existing callbacks will be called
			if(isset($arrDefinition['button_callback']))
			{
				$GLOBALS['TL_DCA'][$this->getName()]['list'][$strConfig][$strOperation]['events']['generate'][] = array
				(
					array('ContaoStyleCallbacks', 'execute', array(
						$arrDefinition['button_callback'],
						9
					))
				);
			}

			$GLOBALS['TL_DCA'][$this->getName()][$strConfig][$strOperation]['button_callback'] = array
			(
				'Netzmacht\DcaTools\DcaTools', $strCallback . $strOperation
			);

			$this->arrOperations[$strScope][$strOperation] = new Operation($strOperation, $strScope, $this);
		}

	}


	/**
	 * Get an operation
	 *
	 * @param $strName
	 * @param string $strScope
	 *
	 * @return mixed
	 *
	 * @throws \RuntimeException
	 */
	public function getOperation($strName, $strScope='local')
	{
		if($this->hasOperation($strName, $strScope))
		{
			if(!isset($this->arrOperations[$strScope][$strName]))
			{
				$this->arrOperations[$strScope][$strName] = new Operation($strName, $strScope, $this);
			}

			return $this->arrOperations[$strScope][$strName];
		}

		throw new \RuntimeException("Operation '$strName' does not exist.");
	}


	/**
	 * Add a operation to the data container
	 *
	 * @param $strName
	 * @param string $strScope
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function addOperation($strName, $strScope='local')
	{
		if(isset($this->arrOperations[$strScope][(string)$strName]))
		{
			throw new \RuntimeException("Operation '$strName' already exists");
		}

		if(is_string($strName))
		{
			$objOperation = new Operation($strName, $strScope, $this);
		}
		else {
			/** @var Operation $objOperation */
			$objOperation = $strName;
			$objOperation->setScope($strScope);
		}

		$this->arrOperations[$strScope][$strName] = $objOperation;
		$this->arrOperations[$strScope][$strName]->dispatch('move');

		return $this;
	}


	/**
	 * Create a new operation
	 *
	 * @param $strName
	 * @param string $strScope
	 *
	 * @return Operation
	 */
	public function createOperation($strName, $strScope='local')
	{
		$this->addOperation($strName, $strScope);

		return $this->getOperation($strName, $strScope);
	}


	/**
	 * Test if operation exists
	 *
	 * @param $strName
	 * @param string $strScope
	 *
	 * @return bool
	 */
	public function hasOperation($strName, $strScope='local')
	{
		$strConfig = $strScope == 'global' ? 'global_operations' : 'operations';

		if($strName == 'all' && $strScope == 'global')
		{
			return true;
		}

		return isset($this->definition['list'][$strConfig][$strName]);
	}


	/**
	 * @param Operation $objOperation
	 * @param null $reference
	 * @param $intPosition
	 *
	 * @return $this
	 */
	public function moveOperation(Operation $objOperation, $reference=null, $intPosition=Palette::POS_LAST)
	{
		$strScope = $objOperation->getScope();

		if($this->hasOperation($objOperation, $strScope))
		{
			unset($this->arrOperations[$strScope][$objOperation->getName()]);
		}

		$this->addAtPosition($this->arrOperations[$strScope], $objOperation, $reference, $intPosition);
		$objOperation->dispatch('move');

		return $this;
	}


	/**
	 * Remove operation from DataContainer
	 *
	 * @param string|Operation $operation
	 * @param string $strScope
	 *
	 * @return $this
	 */
	public function removeOperation($operation, $strScope='local')
	{
		if(is_object($operation))
		{
			$strScope = $operation->getScope();
			$operation = $operation->getName();
		}

		if(isset($this->arrOperations[$strScope][$operation]))
		{
			/** @var Operation $objOperation */
			$objOperation = $this->arrOperations[$strScope][$operation];
			unset($this->arrOperations[$strScope][$operation]);
			$objOperation->dispatch('remove');
		}

		return $this;
	}


	/**
	 * Listen to field events
	 *
	 * @param Event $objEvent
	 *
	 * @return mixed
	 */
	public function fieldListener(Event $objEvent)
	{
		/** @var $objField Field */
		$objField = $objEvent->getDispatcher();
		$strName = $objField->getName();

		if($objEvent->getName() == 'delete')
		{
			foreach($this->getPalettes() as $objPalette)
			{
				if($objPalette->hasField($strName))
				{
					$objPalette->removeField($strName);
				}
			}

			foreach($this->getSubPalettes() as $objSubPalette)
			{
				if($objSubPalette->hasField($strName))
				{
					$objSubPalette->removeField($strName);
				}
			}

			if($objField->isSelector())
			{
				$key = array_search($strName, $this->definition['palettes']['__selector__']);
				unset($this->definition['palettes']['__selector__'][$key]);
			}

			$this->dispatch('change');
		}
	}


	/**
	 * Listen to field events
	 *
	 * @param Event $objEvent
	 *
	 * @return mixed
	 */
	protected function paletteListener(Event $objEvent)
	{
		switch($objEvent->getName())
		{
			case 'rename':
				/** @var $objEvent \Netzmacht\DcaTools\Event\Event */
				$objConfig = $objEvent->getConfig();
				$objDispatcher = $objEvent->getDispatcher();

				/** @var $objDispatcher Palette */
				$this->arrPalettes[$objDispatcher->getName()] = $this->arrPalettes[$objConfig->get('origin')];
				unset($this->arrPalettes[$objConfig->get('origin')]);

				// no break

			case 'create':
			case 'change':
			case 'move':
			case 'remove':
				$this->dispatch('change');
				break;
		}
	}


	/**
	 * Listen to field events
	 *
	 * @param Event $objEvent
	 *
	 * @return mixed
	 */
	protected function subPaletteListener(Event $objEvent)
	{
		switch($objEvent->getName())
		{
			case 'rename':
				/** @var $objEvent \Netzmacht\DcaTools\Event\Event */
				$objConfig = $objEvent->getConfig();
				$objDispatcher = $objEvent->getDispatcher();

				/** @var $objDispatcher SubPalette */
				$this->arrSubPalettes[$objDispatcher->getName()] = $this->arrSubPalettes[$objConfig->get('origin')];
				unset($this->arrSubPalettes[$objConfig->get('origin')]);

				// no break

			case 'create':
			case 'change':
			case 'move':
			case 'remove':
				$this->dispatch('change');
				break;
		}
	}


	/**
	 * @return mixed|void
	 */
	public function remove()
	{
		unset($GLOBALS['TL_DCA'][$this->getName()]);

		$this->dispatch('remove');
	}


	/**
	 * Trigger update definition of child elements
	 *
	 */
	public function updateDefinition()
	{
		foreach($this->arrFields as $objField)
		{
			$objField->updateDefinition();
		}

		foreach($this->arrSubPalettes as $objPalette)
		{
			$objPalette->updateDefinition();
		}

		foreach($this->arrPalettes as $objPalette)
		{
			$objPalette->updateDefinition();
		}
	}

}
