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

use DcGeneral\Contao\Dca\Conditions\ParentChildCondition;
use DcGeneral\Contao\Dca\Conditions\RootCondition;
use DcGeneral\DataDefinition\ContainerInterface;
use Netzmacht\DcaTools\Operation;
use Netzmacht\DcaTools\Model\DcGeneralModel;
use Netzmacht\DcaTools\Node\PropertyContainer;
use Netzmacht\DcaTools\Node\Node;
use Netzmacht\DcaTools\Palette\Palette;
use Netzmacht\DcaTools\Palette\SubPalette;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;


/**
 * Class DataContainer
 * @package Netzmacht\DcaTools
 */
class DataContainer extends PropertyContainer implements ContainerInterface
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
	 * @var Property[]
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
		$this->addListener('removeFromDataContainer', array($this, 'propertyListener'));
	}


	/**
	 * Clone DataContainer
	 */
	public function __clone()
	{
		parent::__clone();

		// clone subpalettes
		foreach($this->arrSubPalettes as $strName => $objSubPalette)
		{
			$this->arrSubPalettes[$strName] = clone $objSubPalette;
			$this->arrSubPalettes[$strName]->setDataContainer($this);
		}

		// clone palettes
		foreach($this->arrPalettes as $strName => $objSubPalette)
		{
			$this->arrSubPalettes[$strName] = clone $objSubPalette;
			$this->arrSubPalettes[$strName]->setDataContainer($this);
		}

		// selectors will be automatically created
		unset($this->arrSelectors);

		// clone operations
		foreach($this->arrOperations as $strScope => $arrOperations)
		{
			foreach($arrOperations as $strName => $objOperation)
			{
				/** @var Operation $objClone */
				$objClone = clone $objOperation;
				$objClone->setDataContainer($this);

				$this->arrOperations[$strScope][$strName] = $objClone;
			}
		}
	}


	/**
	 * Initialize the DataContainer and check permissioin
	 *
	 */
	public function initialize()
	{
		$this->dispatch('initialize');

		if(\Input::get('act') != '')
		{
			$strErrorDefault = sprintf(
				'User "%s" has not enough permission to run action "%s" for DataContainer "%s"',
				\BackendUser::getInstance()->username,
				\Input::get('act'),
				$this->getName()
			);

			if(\Input::get('id') != '')
			{
				$strErrorDefault .= ' on item with ID "' .\Input::get('id') . '"';
			}
		}
		else
		{
			$strErrorDefault = sprintf(
				'User "%s" has not enough permission to access module "%s"',
				\BackendUser::getInstance()->username,
				\Input::get('do')
			);
		}

		$objEvent = new GenericEvent($this, array('error' => $strErrorDefault, 'granted' => true));
		$objEvent = $this->dispatch('permissions', $objEvent);

		if(!$objEvent->getArgument('granted'))
		{
			\Controller::log($objEvent->getArgument('error'), 'DataContainer initialize', TL_ERROR);
			\Controller::redirect('contao/main.php?act=error');
			return;
		}
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
	 * Return the name of the callback provider class to use.
	 *
	 * @return string
	 *
	 * @throws \RuntimeException
	 */
	public function getCallbackProviderClass()
	{
		$strCallbackClass = $this->getFromDefinition('dca_config/callback');

		if (!$strCallbackClass)
		{
			$strCallbackClass = '\DcGeneral\Callbacks\ContaoStyleCallbacks';
		}

		if (!class_exists($strCallbackClass))
		{
			throw new \RuntimeException(sprintf('Invalid callback provider defined %s', var_export($strCallbackClass, true)));
		}

		return $strCallbackClass;
	}


	/**
	 * Retrieve the names of all defined properties.
	 *
	 * @return string[]
	 */
	public function getPropertyNames()
	{
		return array_keys($this->getFromDefinition('fields'));
	}


	/**
	 * Retrieve the panel layout.
	 *
	 * Returns an array of arrays of which each level 1 array is a separate group.
	 *
	 * @return array
	 */
	public function getPanelLayout()
	{
		$arrPanels = explode(';', $this->getFromDefinition('list/sorting/panelLayout'));

		foreach ($arrPanels as $key => $strValue)
		{
			$arrPanels[$key] = array_filter(explode(',', $strValue));
		}

		return array_filter($arrPanels);
	}


	/**
	 * Retrieve the names of properties to use for secondary sorting.
	 *
	 * @return string[]
	 */
	public function getAdditionalSorting()
	{
		return $this->getFromDefinition('list/sorting/fields');
	}


	/**
	 * Retrieve the sorting mode for the data container.
	 *
	 * Values are:
	 * 0 Records are not sorted
	 * 1 Records are sorted by a fixed property
	 * 2 Records are sorted by a switchable property
	 * 3 Records are sorted by the parent table
	 * 4 Displays the child records of a parent record (see style sheets module)
	 * 5 Records are displayed as tree (see site structure)
	 * 6 Displays the child records within a tree structure (see articles module)
	 *
	 * @return int
	 */
	public function getSortingMode()
	{
		return $this->getFromDefinition('list/sorting/mode');
	}


	/**
	 * Boolean flag determining if this data container is closed.
	 *
	 * True means, there may not be any records added or deleted, false means there may be any record appended or
	 * deleted..
	 *
	 * @return bool
	 */
	public function isClosed()
	{
		return (bool) $this->getFromDefinition('list/config/closed');
	}


	/**
	 * Boolean flag determining if this data container is editable.
	 *
	 * True means, the data records may be edited.
	 *
	 * @return bool
	 */
	public function isEditable()
	{
		return (bool) $this->getFromDefinition('list/config/notEditable');
	}


	/**
	 * Retrieve the root condition for the current table.
	 *
	 * @return \DcGeneral\DataDefinition\RootConditionInterface
	 */
	public function getRootCondition()
	{
		return new RootCondition($this, $this->getName());
	}


	/**
	 * Retrieve the parent child condition for the current table.
	 *
	 * @param string $strSrcTable The parenting table.
	 *
	 * @param string $strDstTable The child table.
	 *
	 * @return \DcGeneral\DataDefinition\ParentChildConditionInterface
	 */
	public function getChildCondition($strSrcTable, $strDstTable)
	{
		foreach ($this->getChildConditions($strSrcTable) as $objCondition)
		{
			if ($objCondition->getDestinationName() == $strDstTable)
			{
				return $objCondition;
			}
		}

		return null;
	}


	/**
	 * Retrieve the parent child conditions for the current table.
	 *
	 * @param string $strSrcTable The parenting table for which child conditions shall be assembled for (optional).
	 *
	 * @return \DcGeneral\DataDefinition\ParentChildConditionInterface[]
	 */
	public function getChildConditions($strSrcTable = '')
	{
		$arrConditions = $this->getFromDefinition('dca_config/childCondition');

		if (!is_array($arrConditions))
		{
			return array();
		}

		$arrReturn = array();
		foreach ($arrConditions as $intKey => $arrCondition)
		{
			if (!(empty($strSrcTable) || ($arrCondition['from'] == $strSrcTable)))
			{
				continue;
			}

			$arrReturn[] = new ParentChildCondition($this, $intKey);
		}

		return $arrReturn;
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

		// extend Properties and make sure that and Properties are cloned
		foreach($objNode->getProperties() as $strProperty => $objProperty)
		{
			if(isset($this->arrProperties[$strProperty]))
			{
				$this->arrProperties[$strProperty]->extend($objProperty);
			}
			else
			{
				$this->arrProperties[$strProperty] = clone $objProperty;
				$this->arrProperties[$strProperty]->setDataContainer($this);
			}

			if(isset($arrSelectors[$strProperty]))
			{
				$this->arrSelectors[$strProperty] = $this->arrProperties[$strProperty];
			}
		}

		// extend Palettes and make sure that Legends and Properties are also combined
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

		// extend SubPalettes and make sure that Legends and Properties are also combined
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
	 * Get an Property
	 *
	 * @param string $strName
	 *
	 * @return Property
	 *
	 * @throws \RuntimeException if Property does not exists
	 */
	public function getProperty($strName)
	{
		if($this->hasProperty($strName))
		{
			if(!isset($this->arrProperties[$strName]))
			{
				$objProperty = new Property($strName, $this);
				$objProperty->addListener('delete', array($this, 'propertyListener'));

				$this->arrProperties[$strName] = $objProperty;
			}

			return $this->arrProperties[$strName];
		}

		throw new \RuntimeException("Property '$strName' does not exists.");
	}


	/**
	 * @param string $strName
	 *
	 * @return bool
	 */
	public function hasProperty($strName)
	{
		$strName = is_object($strName) ? $strName->getName() : $strName;

		return isset($this->definition['propertys'][$strName]);
	}


	/**
	 * @param string $strName
	 * @param bool $blnFromDataContainer
	 * @return $this
	 */
	public function removeProperty($strName, $blnFromDataContainer=true)
	{
		$strName = is_object($strName) ? $strName->getName() : $strName;

		if($this->hasProperty($strName))
		{
			$objProperty = $this->getProperty($strName);
			unset($this->arrProperties[$strName]);
			$objProperty->dispatch('delete');

			// unset property not matter if auto update is on because we check against definition if property exists
			unset($this->definition['propertys'][$strName]);
		}

		return $this;
	}


	/**
	 * @param string $strName
	 *
	 * @return Property
	 */
	public function createProperty($strName)
	{
		$this->definition['propertys'][$strName] = array();

		$objProperty = new Property($strName, $this);
		$objProperty->addListener('delete', array($this, 'propertyListener'));

		$this->arrProperties[$strName] = $objProperty;
		$this->dispatch('change');

		return $this->arrProperties[$strName];
	}


	/**
	 * @return Palette[]
	 */
	public function getPalettes()
	{
		foreach(array_keys((array)$this->definition['palettes']) as $strName)
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
		foreach(array_keys((array)$this->definition['subpalettes']) as $strName)
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
	 * @return Property[]
	 */
	public function getSelectors()
	{
		foreach($this->definition['palettes']['__selector__'] as $strName)
		{
			if(!isset($this->arrSelectors[$strName]))
			{
				$this->arrSelectors[$strName] = $this->getProperty($strName);
			}
		}

		return $this->arrSelectors;
	}


	/**
	 * Get a Selector
	 *
	 * @param $strName
	 *
	 * @return Property
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

			$this->arrSelectors[$strName] = $this->getProperty($strName);
		}

		return $this->arrSelectors[$strName];
	}


	/**
	 * Add a new Selector to the DataContainer
	 *
	 * @param string|Property $selector
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function addSelector($selector)
	{
		if(!$selector instanceof Property)
		{
			$selector = $this->getProperty($selector);
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

		return in_array($strName, (array) $this->definition['palettes']['__selector__']);
	}


	/**
	 * @param $strName
	 *
	 * @return $this
	 */
	public function removeSelector($strName)
	{
		// only unset, because property can still exists
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
	 * Retrieve the names of all defined properties.
	 *
	 * @return string[]
	 */
	public function getOperationNames()
	{
		return array_keys($this->getFromDefinition('list/operations'));
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
		$objOperation->dispatch('move');

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
	 * Listen to property events
	 *
	 * @param Event $objEvent
	 *
	 * @return mixed
	 */
	public function propertyListener(Event $objEvent)
	{
		/** @var $objProperty Property */
		$objProperty = $objEvent->getDispatcher();
		$strName = $objProperty->getName();

		if($objEvent->getName() == 'delete')
		{
			foreach($this->getPalettes() as $objPalette)
			{
				if($objPalette->hasProperty($strName))
				{
					$objPalette->removeProperty($strName);
				}
			}

			foreach($this->getSubPalettes() as $objSubPalette)
			{
				if($objSubPalette->hasProperty($strName))
				{
					$objSubPalette->removeProperty($strName);
				}
			}

			if($objProperty->isSelector())
			{
				$key = array_search($strName, $this->definition['palettes']['__selector__']);
				unset($this->definition['palettes']['__selector__'][$key]);
			}

			$this->dispatch('change');
		}
	}


	/**
	 * Listen to property events
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
				/** @var GenericEvent $objEvent */
				$objPalette = $objEvent->getSubject();

				$this->arrPalettes[$objPalette->getName()] = $this->arrPalettes[$objEvent->getArgument('origin')];
				unset($this->arrPalettes[$objEvent->getArgument('origin')]);

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
	 * Listen to property events
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
				/** @var GenericEvent $objEvent */
				$objDispatcher = $objEvent->getSubject();

				/** @var $objDispatcher SubPalette */
				$this->arrSubPalettes[$objDispatcher->getName()] = $this->arrSubPalettes[$objEvent->getArgument('origin')];
				unset($this->arrSubPalettes[$objEvent->getArgument('origin')]);

				//no break

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
		foreach($this->arrProperties as $objProperty)
		{
			$objProperty->updateDefinition();
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
