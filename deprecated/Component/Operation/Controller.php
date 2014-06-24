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

namespace deprecated\DcaTools\Component\Operation;

use deprecated\DcaTools\Component\AbstractController;
use deprecated\DcaTools\Definition;
use deprecated\DcaTools\Event\GenerateEvent;


/**
 * Class Controller
 * @package DcaTools\Component\Operation
 */
class Controller extends AbstractController
{

	/**
	 * @var View
	 */
	protected $view;


	/**
	 * @return array
	 */
	protected function createGenerateEvent()
	{
		$eventName = sprintf('dcatools.%s.operation.%s',
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
		if($this->getConfigAttribute('plain')) {
			$this->compilePlainView();
		}
		else {
			$this->compileDefaultView();
		}
	}


	/**
	 * Compile plain view. This can be used if not the table and id should be add by default.
	 */
	protected function compilePlainView()
	{
		if($this->getConfigAttribute('table') === true) {
			$this->setConfigAttribute('table', $this->model->getProviderName());
		}

		if($this->getConfigAttribute('id') === true) {
			$this->setConfigAttribute('id', $this->model->getId());
		}

		if($this->getConfigAttribute('rt')) {
			$this->setConfigAttribute('rt', \RequestToken::get());
		}

		if(!$this->getConfigAttribute('append')) {
			$href = $this->buildHref();

			if($href) {
				$this->view->setHref($href);
			}
		}
		else {
			$this->view->setHref(\Controller::addToUrl($this->buildHref()));
		}

	}


	/**
	 * Combine
	 */
	protected function compileDefaultView()
	{
		if(!$this->getConfigAttribute('table'))	{
			$this->setConfigAttribute('table', $this->model->getProviderName());
		}

		if(!$this->getConfigAttribute('id') && !$this->getConfigAttribute('id') !== false) {
			$this->setConfigAttribute('id', $this->model->getId());
		}

		$this->setConfigAttribute('rt', \RequestToken::get());

		$href = \Environment::get('script') . '?do=' . \Input::get('do');

		if($this->getConfigAttribute('id') === false) {
			$add  = $this->buildHref(array('table', 'rt'));
		}
		else {
			$add  = $this->buildHref();
		}


		if($this->view->getHref()) {
			$href .= '&amp;' . $this->view->getHref();
		}

		if($add) {
			$href .= '&amp;' . $add;
		}

		$this->view->setHref($href);
	}


	/**
	 * @param array $attributes
	 * @return string
	 */
	protected function buildHref($attributes = array('table', 'id', 'rt'))
	{
		$href    = '';
		$combine = '';

		foreach($attributes as $attribute)
		{
			$value = $this->getConfigAttribute($attribute);

			if($value !== null)
			{
				$href .= $combine . $attribute . '=' . $value;
				$combine = '&amp;';
			}
		}

		return $href;
	}

}
