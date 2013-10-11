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

use DcaTools\Definition;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class DataContainer
 * @package DcaTools\Component
 */
class DataContainer extends Component
{

	/**
	 * @param Definition\Node $strName
	 */
	public function __construct($strName)
	{
		parent::__construct(Definition::getDataContainer($strName));

		$arrConfig = $this->objDefinition->get('dcatools');

		if(isset($arrConfig['initialize']) && is_array($arrConfig['initialize']))
		{
			$this->addListeners('initialize', $arrConfig['initialize']);
		}

		if(isset($arrConfig['permissions']) && is_array($arrConfig['permissions']))
		{
			$this->addListeners('permissions', $arrConfig['permissions']);
		}
	}


	/**
	 * Initialize DataContainer
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

}