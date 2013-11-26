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

namespace DcaTools\Component;

/**
 * Interface ViewInterface
 * @package DcaTools\Component
 */
interface ViewInterface
{

	/**
	 * Generate the view and return as string
	 *
	 * @return string
	 */
	public function generate();


	/**
	 * @return bool
	 */
	public function isVisible();


	/**
	 * @param $visible
	 */
	public function setVisible($visible);

}
