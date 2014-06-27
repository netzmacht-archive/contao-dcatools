<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Event;


use Symfony\Component\EventDispatcher\Event;


class InitializeCallbackManagerEvent extends Event
{
	const NAME = 'dcatools.initialize-callback-manager';

	/**
	 * @var string
	 */
	private $dataContainerName;

	/**
	 * @var array
	 */
	private $callbacks=array();


	/**
	 * @param string $dataContainerName
	 */
	function __construct($dataContainerName)
	{
		$this->dataContainerName = $dataContainerName;
	}


	/**
	 * @return string
	 */
	public function getDataContainerName()
	{
		return $this->dataContainerName;
	}


	/**
	 * @param $callback
	 * @param string|array|null $for
	 */
	public function enableCallback($callback, $for=null)
	{
		if(!isset($this->callbacks[$callback])) {
			$this->callbacks[$callback] = array();
		}

		if($for) {
			$this->callbacks[$callback] = array_unique(
				array_merge(
					$this->callbacks[$callback],
					(array) $for
				)
			);
		}
	}

	/**
	 * @return array
	 */
	public function getCallbacks()
	{
		return $this->callbacks;
	}

} 