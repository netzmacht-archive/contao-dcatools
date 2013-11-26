<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 21.10.13
 * Time: 08:23
 * To change this template use File | Settings | File Templates.
 */

namespace DcaTools\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

class PermissionEvent extends GenericEvent
{
	protected $blnGranted = true;

	protected $arrErrors;

	/**
	 * @param array $arrArguments
	 */
	public function __construct($subject, array $arrArguments = array())
	{
		parent::__construct($subject, $arrArguments);
	}


	/**
	 * @return mixed
	 */
	public function isAccessGranted()
	{
		return $this->blnGranted;
	}


	/**
	 * Deny access
	 */
	public function denyAccess()
	{
		$this->blnGranted = false;
		$this->stopPropagation();
	}


	/**
	 * @return mixed
	 */
	public function getErrors()
	{
		return $this->arrErrors;
	}


	/**
	 * @param $strMessage
	 */
	public function addError($strMessage)
	{
		$this->arrErrors[] = $strMessage;
	}

}