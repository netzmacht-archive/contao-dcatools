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

namespace Netzmacht\DcaTools\Node;

/**
 * Class Exportable
 * @package Netzmacht\DcaTools\Node
 */
interface Exportable
{

	/**
	 * @return mixed
	 */
	public function asString();


	/**
	 * @return mixed
	 */
	public function asArray();

}