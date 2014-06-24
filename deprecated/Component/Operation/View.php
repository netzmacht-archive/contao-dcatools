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

use deprecated\DcaTools\Component\ViewInterface;


class View implements ViewInterface
{

	/**
	 * @var \BackendTemplate
	 */
	protected $template;

	/**
	 * @var bool
	 */
	protected $visible = true;


	/**
	 * Construct
	 */
	public function __construct()
	{
		$this->template = new \BackendTemplate('dcatools_operation');
	}


	/**
	 * @param $name
	 */
	public function setTemplateName($name)
	{
		$this->template->setName($name);
	}


	/**
	 * @return string
	 */
	public function generate()
	{
		if($this->isVisible())
		{
			return $this->template->parse();
		}

		return '';
	}


	/**
	 * @param $href
	 */
	public function setHref($href)
	{
		$this->template->href = specialchars($href);
	}


	/**
	 * @return string
	 */
	public function getHref()
	{
		return $this->template->href;
	}


	/**
	 * @param $label
	 */
	public function setLabel($label)
	{
		$this->template->label = specialchars($label);
	}


	/**
	 * @return string
	 */
	public function getLabel()
	{
		return $this->template->label;
	}


	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->template->title;
	}


	/**
	 * @param $title
	 */
	public function setTitle($title)
	{
		$this->template->title = specialchars($title);
	}


	/**
	 * @param $attributes
	 */
	public function setAttributes($attributes)
	{
		$this->template->attributes = $attributes;
	}


	/**
	 * @return string
	 */
	public function getAttributes()
	{
		return $this->template->attributes;
	}


	/**
	 * @param $icon
	 */
	public function setIcon($icon)
	{
		$this->template->icon = specialchars($icon);
	}


	/**
	 * @return string
	 */
	public function getIcon()
	{
		return $this->template->icon;
	}


	/**
	 * @return bool
	 */
	public function isVisible()
	{
		return $this->visible;
	}


	/**
	 * @param $visible
	 */
	public function setVisible($visible)
	{
		$this->visible = $visible;
	}


	/**
	 * @param $disabled
	 */
	public function setDisabled($disabled)
	{
		$this->template->disabled = (bool) $disabled;
	}


	/**
	 * @return bool
	 */
	public function isDisabled()
	{
		return (bool) $this->template->disabled;
	}

}
