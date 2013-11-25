<?php

namespace DcaTools\Component\Operation;

use DcaTools\Component\ViewInterface;


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
		$this->template->href = $href;
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
		$this->template->label = $label;
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
		$this->template->title = $title;
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
		$this->template->icon = $icon;
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
		$this->template->disabled = $disabled;
	}


	/**
	 * @return bool
	 */
	public function isDisabled()
	{
		return (bool) $this->template->disabled;
	}

}
