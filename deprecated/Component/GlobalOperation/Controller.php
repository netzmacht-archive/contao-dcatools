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

namespace deprecated\DcaTools\Component\GlobalOperation;

use deprecated\DcaTools\Definition;
use deprecated\DcaTools\Event\GenerateEvent;


/**
 * Class Controller
 * @package DcaTools\Component\Operation
 */
class Controller extends \deprecated\DcaTools\Component\Operation\Controller
{

	/**
	 * @return array
	 */
	protected function createGenerateEvent()
	{
		$eventName = sprintf('dcatools.%s.global_operation.%s',
			$this->getDefinition()->getDataContainer()->getName(),
			$this->getDefinition()->getName()
		);

		$event = new GenerateEvent($this->model, $this->view, $this->config);
		return array($eventName, $event);
	}


	/**
	 * Compile the view
	 */
	protected function compile()
	{
		if(!$this->getConfigAttribute('plain'))
		{
			if($this->getConfigAttribute('table') === true) {
				$this->setConfigAttribute('table', $this->definition->getName());
			}

			if($this->getConfigAttribute('id') === true) {
				$this->setConfigAttribute('id', \Input::get('id'));
			}

			$strHref = \Environment::get('script') . '?do=' . \Input::get('do');

			if($this->view->getHref() != '')
			{
				$strHref .= '&amp;' . $this->view->getHref();
			}

			$this->setConfigAttribute('rt', \RequestToken::get());

			$attributes = array();

			if($this->getConfigAttribute('id')) {
				$attributes[] = 'id';
			}
			if($this->getConfigAttribute('table')) {
				$attributes[] = 'table';
			}

			$attributes[] = 'rt';
			$strHref .= '&amp;' . $this->buildHref($attributes);

			$this->view->setHref($strHref);
		}
	}
}
