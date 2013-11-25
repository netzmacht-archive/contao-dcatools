<?php

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
