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

namespace deprecated\DcaTools\Structure;

/**
 * Class Exportable
 * @package DcaTools\Node
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