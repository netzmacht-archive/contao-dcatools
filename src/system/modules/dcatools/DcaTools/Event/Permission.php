<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 21.10.13
 * Time: 08:23
 * To change this template use File | Settings | File Templates.
 */

namespace DcaTools\Event;


class Permission extends Event
{

	/**
	 * @param null $objSubject
	 * @param array $arrArguments
	 */
	public function __construct($objSubject = null, array $arrArguments = array())
	{
		parent::__construct($objSubject, $arrArguments);

		$this['granted'] = true;
		$this['error'] = '';
	}


	/**
	 * @return mixed
	 */
	public function isAccessGranted()
	{
		return $this['granted'];
	}


	/**
	 * Deny access
	 */
	public function denyAccess()
	{
		$this['granted'] = false;
		$this->stopPropagation();
	}


	/**
	 * @return mixed
	 */
	public function getError()
	{
		return $this['error'];
	}


	/**
	 * @param $strMessage
	 */
	public function setError($strMessage)
	{
		$this['error'] = $strMessage;
	}

}