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

namespace Netzmacht\DcaTools\Structure;

/**
 * Class Exportable
 * @package Netzmacht\DcaTools\Node
 */
interface ExportInterface
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