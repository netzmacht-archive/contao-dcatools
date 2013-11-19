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

namespace DcaTools\Definition;

use DcGeneral\Contao\Dca\Conditions\ParentChildCondition;
use DcGeneral\Contao\Dca\Conditions\RootCondition;
use DcGeneral\DataDefinition\ContainerInterface;
use DcaTools\Definition;


/**
 * Class DataContainer
 * @package DcaTools
 */
class DataContainer extends PropertyContainer implements ContainerInterface
{

	/**
	 * @var array
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
	 * @var DataContainer[]
	 */
	protected static $arrDataContainers = array();


	/**
	 * Constructor
	 *
	 * @param $strName
	 */
	protected function __construct($strName)
	{
		$this->strName = $strName;
		$this->definition =& $GLOBALS['TL_DCA'][$strName];
	}


	/**
	 * Clone DataContainer will remove all sub elements because they can not be cloned
	 */
	public function __clone()
	{
		$this->arrSubPalettes = array();
		$this->arrOperations  = array();
		$this->arrPalettes    = array();
		$this->arrProperties  = array();
	}


	/**
	 * Get an instance of DataContainer
	 *
	 * @param $strName
	 * @return DataContainer
	 */
	public static function getInstance($strName)
	{
		if(!isset(static::$arrDataContainers[$strName]))
		{
			static::$arrDataContainers[$strName] = new self($strName);
		}

		return static::$arrDataContainers[$strName];
	}


	/**
	 * Get DataContainer
	 *
	 * @return $this
	 */
	public function getDataContainer()
	{
		return $this;
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
			$objNode = Definition::getDataContainer($objNode);
		}
		elseif(!$objNode instanceof DataContainer)
		{
			throw new \RuntimeException("Node '{$objNode->getName()}' is not a DataContainer");
		}

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

		$this->updateDefinition();
	}


	/**
	 * Copy node to a new one
	 * @param $strName
	 * @return mixed
	 */
	public function copy($strName=null)
	{
		$objCopy = parent::copy($strName);

		if($strName)
		{
			$objCopy->updateDefinition();
		}

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

				$this->arrProperties[$strName] = $objProperty;
			}

			return $this->arrProperties[$strName];
		}

		throw new \RuntimeException("Property '$strName' does not exists.");
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
	 * @return Property[]
	 */
	public function getProperties()
	{
		foreach($this->definition['fields'] as $strName => $arrDefinition)
		{
			if(!isset($this->arrProperties[$strName]))
			{
				$this->getProperty($strName);
			}
		}

		return parent::getProperties();
	}


	/**
	 * @param Property|string $strName
	 *
	 * @return bool
	 */
	public function hasProperty($strName)
	{
		$strName = is_object($strName) ? $strName->getName() : $strName;

		return isset($this->definition['fields'][$strName]);
	}


	/**
	 * @param Property|string $property
	 * @param bool $blnFromDataContainer
	 * @return $this
	 */
	public function removeProperty($property, $blnFromDataContainer=true)
	{
		list($strName, $objProperty) = Property::argument($this, $property);

		if($objProperty !== null)
		{
			unset($this->arrProperties[$strName]);
			unset($this->definition['fields'][$strName]);

			foreach($this->getPalettes() as $strName => $objPalette)
			{
				$objPalette->removeProperty($strName);
			}

			foreach($this->getSubPalettes() as $strName => $objSubPalette)
			{
				$objSubPalette->removeProperty($strName);
			}

			if(in_array($strName, $this->definition['palettes']['__selector__']))
			{
				$intKey = array_search($strName, $this->definition['palettes']['__selector__']);
				unset($this->definition['palettes']['__selector__'][$intKey]);
				$this->definition['palettes']['__selector__'] = array_values($this->definition['palettes']['__selector__']);
			}
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
		$this->definition['fields'][$strName] = array();

		$objProperty = new Property($strName, $this);
		$this->arrProperties[$strName] = $objProperty;

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
			if(!$this->hasPalette($strName))
			{
				throw new \RuntimeException("Palette '$strName' does not exist");
			}

			$this->arrPalettes[$strName] = new Palette($strName, $this);
		}

		return $this->arrPalettes[$strName];
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
		if(isset($this->definition['palettes'][$strName]))
		{
			throw new \RuntimeException("Palette {$strName} already exists in DataContainer {$this->getName()}");
		}

		$this->definition['palettes'][$strName] = '';

		$objPalette = new Palette($strName, $this);
		$this->arrPalettes[$strName] = $objPalette;

		return $objPalette;
	}


	/**
	 * @param string|Palette $palette
	 *
	 * @return bool
	 */
	public function hasPalette($palette)
	{
		// can not call Palette::argument() here could get an recursive call
		$strName = is_object($palette) ? $palette->getName() : $palette;

		return  isset($this->definition['palettes'][$strName]);
	}


	/**
	 * @param string|Palette $palette
	 *
	 * @return $this
	 */
	public function removePalette($palette)
	{
		list($strName) = Palette::argument($this, $palette);

		if($this->hasPalette($strName))
		{
			unset($this->arrPalettes[$strName]);
			unset($this->definition['palettes'][$strName]);
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
		if(isset($this->definition['subpalettes'][$strName]))
		{
			throw new \RuntimeException("SubPalette {$strName} already exists in DataContainer {$this->getName()}");
		}

		$this->definition['subpalettes'][$strName] = '';
		$this->definition['palettes']['__selector__'][] = $strName;

		$objSubPalette = new SubPalette($strName, $this);
		$this->arrSubPalettes[$strName] = $objSubPalette;

		return $objSubPalette;
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
	 * @param $subpalette
	 *
	 * @return SubPalette
	 *
	 * @throws \RuntimeException if SubPalette does not exist
	 */
	public function getSubPalette($subpalette)
	{
		/** @var SubPalette $subpalette */
		$strName = is_object($subpalette) ? $subpalette->getName() : $subpalette;

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
	 * @param $subpalette
	 *
	 * @return bool
	 */
	public function hasSubPalette($subpalette)
	{
		/** @var SubPalette $subpalette */
		$strName = is_object($subpalette) ? $subpalette->getName() : $subpalette;

		return isset($this->definition['subpalettes'][$strName]);
	}


	/**
	 * @param $strName
	 *
	 * @return $this
	 */
	public function removeSubPalette($strName)
	{
		list($strName) = SubPalette::argument($this, $strName);

		if($this->hasSubPalette($strName))
		{
			unset($this->arrSubPalettes[$strName]);
			unset($this->definition['subpalettes'][$strName]);
		}

		return $this;
	}


	/**
	 * @return Property[]
	 */
	public function getSelectors()
	{
		$arrSelectors = array();

		foreach((array) $this->definition['palettes']['__selector__'] as $strName)
		{
			$arrSelectors[$strName] = $this->getProperty($strName);
		}

		return $arrSelectors;
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
		if(!$this->hasSelector($strName))
		{
			throw new \RuntimeException("Selector '$strName' does not exist");
		}

		return $this->getProperty($strName);
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
		elseif($selector->getDataContainer() !== $this)
		{
			throw new \RuntimeException("Property {$selector->getName()} does not belong to DataContainer {$this->getName()}");
		}

		if(!in_array($selector->getName(), (array) $this->getFromDefinition('palettes/__selector__')))
		{
			$this->definition['palettes']['__selector__'][] = $selector->getName();
		}

		return $this;
	}


	/**
	 * @param Property|string $property
	 *
	 * @return bool
	 */
	public function hasSelector($property)
	{
		$strName = is_object($property) ? $property->getName() : $property;

		return ($this->hasProperty($strName) && in_array($strName, (array) $this->definition['palettes']['__selector__']));
	}


	/**
	 * @param Property|string $property
	 *
	 * @return $this
	 */
	public function removeSelector($property)
	{
		if($property instanceof Property)
		{
			$property = $this->getName();
		}

		$intKey = array_search($property, $this->definition['palettes']['__selector__']);
		unset($this->definition['palettes']['__selector__'][$intKey]);
		$this->definition['palettes']['__selector__'] = array_values($this->definition['palettes']['__selector__']);

		return $this;
	}


	/**
	 * Get all operations
	 *
	 * @param string $strScope global for global operations else local one will be loaded
	 *
	 * @return Operation[]
	 */
	public function getOperations($strScope='local')
	{
		$strConfig = ($strScope == 'global') ? 'global_operations' : 'operations';

		// add operation callback for every operation
		foreach($this->definition['list'][$strConfig] as $strOperation => $arrDefinition)
		{
			$this->getOperation($strOperation, $strScope);
		}

		return $this->arrOperations[$strScope];
	}


	/**
	 * Get an operation
	 *
	 * @param $strName
	 * @param string $strScope
	 *
	 * @return \DcaTools\Definition\Operation
	 *
	 * @throws \RuntimeException
	 */
	public function getOperation($strName, $strScope='local')
	{
		if($this->hasOperation($strName, $strScope))
		{
			if(!isset($this->arrOperations[$strScope][$strName]))
			{
				$objOperation = new Operation($strName, $strScope, $this);
				$this->arrOperations[$strScope][$strName] = $objOperation;
			}

			return $this->arrOperations[$strScope][$strName];
		}

		throw new \RuntimeException("Operation '$strName' does not exist.");
	}


	/**
	 * Retrieve the names of all defined properties.
	 *
	 * @param string $strScope
	 *
	 * @return string[]
	 */
	public function getOperationNames($strScope='local')
	{
		$strFrom = ($strScope == 'global' ?  'list/global_operations' : 'list/operations');
		return array_keys($this->getFromDefinition($strFrom));
	}


	/**
	 * Create a new operation
	 *
	 * @param $strName
	 * @param string $strScope
	 *
	 * @return Operation
	 *
	 * @throws \RuntimeException
	 */
	public function createOperation($strName, $strScope='local')
	{
		if(isset($this->arrOperations[$strScope][$strName]))
		{
			throw new \RuntimeException("Operation '$strName' already exists");
		}

		$strFrom = ($strScope == 'global' ?  'global_operations' : 'operations');
		$this->definition['list'][$strFrom][$strName] = array();

		$objOperation = new Operation($strName, $strScope, $this);
		$this->arrOperations[$strScope][$strName] = $objOperation;

		return $objOperation;
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
	public function moveOperation(Operation $objOperation, $reference=null, $intPosition=Definition::LAST)
	{
		$strScope = $objOperation->getScope();

		if($this->hasOperation($objOperation, $strScope))
		{
			unset($this->arrOperations[$strScope][$objOperation->getName()]);
		}

		$this->addAtPosition($this->arrOperations[$strScope], $objOperation, $reference, $intPosition);

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

		$strKey = $strScope == 'global' ? 'global_' : '';
		$strKey .= 'operations';

		if(isset($this->arrOperations['list'][$strScope][$operation]))
		{
			unset($this->arrOperations[$strScope][$operation]);
		}

		unset($this->definition['list'][$strKey][$operation]);

		return $this;
	}


	/**
	 * @param string $strCallback the name of the callback without _callback suffix
	 * @param array $arrCallback the Contao style callback
	 *
	 * @return $this
	 */
	public function registerCallback($strCallback, array $arrCallback)
	{
		$arrDefinition =& $this->getDefinition();

		switch($strCallback)
		{
			case 'onsubmit':
			case 'oncreate':
			case 'onload':
			case 'oncut':
			case 'oncopy':
			case 'ondelete':
				$arrDefinition['config'][$strCallback . '_callback'][] = $arrCallback;
				break;

			case 'header':
			case 'child_record':
			case 'group':
				$arrDefinition['list']['sorting'][$strCallback . '_callback'] = $arrCallback;
				break;

			case 'label':
				$arrDefinition['list']['label'][$strCallback . '_callback'] = $arrCallback;
				break;
		}

		return $this;
	}


	/**
	 * @return mixed|void
	 */
	public function remove()
	{
		unset($GLOBALS['TL_DCA'][$this->getName()]);
	}


	/**
	 * Update definition
	 *
	 * @param bool $blnPropagation
	 * @return $this
	 */
	public function updateDefinition($blnPropagation=true)
	{
		foreach($this->getProperties() as $objProperty)
		{
			$objProperty->updateDefinition(false);
		}

		foreach($this->getSubPalettes() as $objPalette)
		{
			$objPalette->updateDefinition(false);
		}

		foreach($this->getPalettes() as $objPalette)
		{
			$objPalette->updateDefinition(false);
		}

		return $this;
	}


	/**
	 * Prepare argument so that an array of name and the object is passed
	 *
	 * @param DataContainer $objReference
	 * @param DataContainer|string $node
	 *
	 * @return array(string, DataContainer)
	 */
	public static function argument(DataContainer $objReference, $node)
	{
		if(is_string($node))
		{
			return array($node, static::getInstance($node));
		}

		return array($node->getName(), $node);
	}


	/**
	 * Get definition information
	 *
	 * @param $strKey
	 * @return mixed|null
	 */
	public function get($strKey)
	{
		return $this->getFromDefinition($strKey);
	}


	/**
	 * @param $strKey
	 * @param $value
	 *
	 * @return $this
	 */
	public function set($strKey, $value)
	{
		$arrChunks = explode('/', $strKey);
		$definition =& $this->definition;

		$key = array_pop($arrChunks);

		foreach($arrChunks as $chunk)
		{
			if(!isset($definition[$chunk]))
			{
				$definition[$chunk] = array();
			}

			$definition = &$definition[$chunk];
		}

		$definition[$key ? $key : count($definition)] = $value;

		return $this;
	}

}
