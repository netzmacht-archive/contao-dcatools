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


/**
 * Class View
 * @package DcaTools\Component\GlobalOperation
 */
class View extends \deprecated\DcaTools\Component\Operation\View
{

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		$this->template->setName('dcatools_global_operation');
	}

}
