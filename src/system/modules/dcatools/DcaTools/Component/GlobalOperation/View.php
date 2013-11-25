<?php

namespace DcaTools\Component\GlobalOperation;


/**
 * Class View
 * @package DcaTools\Component\GlobalOperation
 */
class View extends \DcaTools\Component\Operation\View
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
