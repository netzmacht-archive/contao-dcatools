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

namespace DcaTools\Component;

use DcaTools\Event\Event;
use DcaTools\Structure\OperationInterface;
use DcaTools\Definition;

/**
 * Class Operation
 * @package DcaTools\Component
 */
class Operation extends Visual implements OperationInterface
{

	/**
	 * Operation Template
	 * @var string
	 */
	protected $strTemplate = 'be_operation';


	/**
	 * @var array
	 */
	protected $arrData = array();


	/**
	 * @var bool
	 */
	protected $blnDisabled;


	/**
	 * @var string
	 */
	protected $strScope = 'local';


	/**
	 * Constructor
	 *
	 * @param string $strTable
	 * @param string $strName
	 * @param string $strScope
	 */
	public function __construct($strTable, $strName, $strScope='local')
	{
		$objDefinition = Definition::getDataContainer($strTable);

		parent::__construct($objDefinition);

		$arrEvents = $objDefinition->getFromDefinition(sprintf(
			'dcatools/%s/%s',
			$strScope === 'global' ? 'global_operations' : 'operations',
			$strName
		));

		if(is_array($arrEvents))
		{
			$this->addListeners('generate', $arrEvents);
		}
	}


	/**
	 * Compile
	 *
	 * @param Event $objEvent
	 *
	 * @return mixed|void
	 */
	protected function compile(Event $objEvent)
	{
		if($this->isHidden() || $this->isDisabled())
		{
			return;
		}

		$arrArguments = array();

		// handle local operations
		if($this->getScope() == 'local')
		{
			// plain links are not added via addToUrl but can also get table, id and rt added
			if($objEvent->hasArgument('plain') && $objEvent->getArgument('plain'))
			{
				if($objEvent->hasArgument('table') && $objEvent->getArgument('table'))
				{
					$arrArguments['table'] = $objEvent->getArgument('table') === true
						? $this->objDefinition->getDataContainer()->getName()
						: $objEvent->getArgument('table');
				}

				if($objEvent->hasArgument('id') && $objEvent->getArgument('id'))
				{
					$arrArguments['id'] = $objEvent->getArgument('id') === true
						? $this->objModel->getId()
						: $objEvent->getArgument('id');
				}

				if($objEvent->hasArgument('rt') && $objEvent->getArgument('rt'))
				{
					$arrArguments['rt'] = REQUEST_TOKEN;
				}

				$this->appendHrefArguments($arrArguments);
			}
			else
			{
				// table argument can be disabled
				if(!$objEvent->hasArgument('table') || $objEvent->getArgument('table') !== false)
				{
					$arrArguments['table'] = $this->objDefinition->getDataContainer()->getName();
				}

				// id argument can be disabled
				if(!$objEvent->hasArgument('id') || $objEvent->getArgument('id') !== false)
				{
					$arrArguments['id'] = $this->objModel->getId();
				}

				$this->appendHrefArguments($arrArguments);
				$this->setHref(\Controller::addToUrl($this->getHref()));
			}
		}
		// global operations
		else
		{
			if(!$objEvent->hasArgument('plain') || !$objEvent->getArgument('plain'))
			{
				if($objEvent->hasArgument('table') && $objEvent->getArgument('table'))
				{
					$arrArguments['table'] = $objEvent->getArgument('table') === true
						? $this->objDefinition->getDataContainer()->getName()
						: $objEvent->getArgument('table');
				}

				if($objEvent->hasArgument('id') && $objEvent->getArgument('id'))
				{
					$arrArguments['id'] = $objEvent->getArgument('id') === true
						? \Input::get('id')
						: $objEvent->getArgument('id');
				}

				$strHref = \Environment::get('script') . '?do=' . \Input::get('do');

				if($this->getHref() != '')
				{
					$strHref .= '&amp;' . $this->getHref();
				}

				$this->setHref($strHref);

				$arrArguments['rt'] = REQUEST_TOKEN;
				$this->appendHrefArguments($arrArguments);
			}
		}
	}


	/**
	 * Return the name of the property.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->get('name');
	}


	/**
	 * @param $strName
	 */
	public function setName($strName)
	{
		$this->set('name', $strName);
	}


	/**
	 * Return the label of the property.
	 *
	 * @return array
	 */
	public function getLabel()
	{
		if(isset($this->arrData['label']))
		{
			return $this->arrData['label'];
		}

		$arrLabel = $this->objDefinition->getLabel();
		return $arrLabel[0];
	}


	/**
	 * Return the (html) attributes to use.
	 *
	 * @return string
	 */
	public function getAttributes()
	{
		return $this->get('attributes');
	}


	/**
	 * Return the (html) href to use. This only applies to HTML views.
	 *
	 * @return string
	 */
	public function getHref()
	{
		return $this->get('href');
	}


	/**
	 * Return the icon to use.
	 *
	 * @return string
	 */
	public function getIcon()
	{
		return $this->get('icon');
	}


	/**
	 * Return the callback to use.
	 *
	 * @return array
	 */
	public function getCallback()
	{
		return $this->get('callback');
	}


	/**
	 * Fetch some arbitrary information.
	 *
	 * @param $strKey
	 *
	 * @return mixed
	 */
	public function get($strKey)
	{
		if(isset($this->arrData[$strKey]))
		{
			return $this->arrData[$strKey];
		}

		return $this->objDefinition->get($strKey);
	}


	/**
	 * This returns the whole content as Contao compatible operation array.
	 *
	 * @return array
	 *
	 * @deprecated You should rather use the interfaced methods than the operation as an array as this may not be supported.
	 */
	public function asArray()
	{
		return $this->objDefinition->asArray();
	}


	/**
	 * @return mixed
	 */
	public function asString()
	{
		return $this->generate();
	}


	/**
	 * Set Attributes of operation
	 *
	 * @param string $strAttributes
	 */
	public function setAttributes($strAttributes)
	{
		$this->set('attributes', $strAttributes);
	}


	/**
	 * Set href
	 *
	 * @param string $strHref
	 */
	public function setHref($strHref)
	{
		$this->set('href', $strHref);
	}


	/**
	 * Set icon
	 *
	 * @param string $strIcon
	 */
	public function setIcon($strIcon)
	{
		$this->set('icon', $strIcon);
	}


	/**
	 * Set label
	 *
	 * @param string $strLabel
	 */
	public function setLabel($strLabel)
	{
		$this->set('label', $strLabel);
	}


	/**
	 * Set title
	 *
	 * @param string $strTitle
	 */
	public function setTitle($strTitle)
	{
		$this->set('title', $strTitle);
	}


	/**
	 * Get Title
	 *
	 * @return mixed
	 */
	public function getTitle()
	{
		if(isset($this->arrData['title']))
		{
			return $this->arrData['title'];
		}

		$arrLabel = $this->objDefinition->getLabel();
		return $arrLabel[1];
	}


	/**
	 * Get scope
	 *
	 * @return string
	 */
	public function getScope()
	{
		return $this->strScope;
	}


	/**
	 * Set Scope
	 *
	 * @param $strScope
	 */
	public function setScope($strScope)
	{
		$this->strScope = $strScope;
	}


	/**
	 * Disable operation. Operation will be displayed as disabled
	 */
	public function disable()
	{
		$this->blnDisabled = true;
	}


	/**
	 * @return bool
	 */
	public function isDisabled()
	{
		return $this->blnDisabled;
	}


	/**
	 * Set information
	 *
	 * @param $strKey
	 * @param $value
	 */
	public function set($strKey, $value)
	{
		$this->arrData[$strKey] = $value;
	}


	/**
	 * @param array $arrArguments
	 * @return string
	 */
	protected function appendHrefArguments(array $arrArguments)
	{
		$strHref = $this->getHref();
		$strAmp = '';

		if($strHref != '' && !empty($arrArguments))
		{
			$strAmp = '&amp;';
		}

		foreach($arrArguments as $strName => $strValue)
		{
			$strHref .= $strAmp . $strName . '=' . $strValue;
			$strAmp = '&amp;';
		}

		$this->setHref($strHref);
		return $strHref;
	}

}