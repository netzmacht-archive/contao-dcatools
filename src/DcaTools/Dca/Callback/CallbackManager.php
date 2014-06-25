<?php

/**
 * @package    contao-dcatools
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Dca\Callback;

use DcaTools\Assertion;
use DcaTools\Dca\Callback;
use DcaTools\Exception\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


/**
 * Class CallbackManager
 * @package DcaTools\Dca\Callback
 */
class CallbackManager
{

	/**
	 * @var string
	 */
	private $callbackClass;


	/**
	 * @param $callbackClass
	 */
	function __construct($callbackClass)
	{
		$this->callbackClass	  = $callbackClass;
	}


	/**
	 * @param string $callback
	 * @param string $dataContainerName
	 * @param null $for
	 * @return $this
	 */
	public function enableCallback($callback, $dataContainerName, $for=null)
	{
		static::assertValidCallback($callback);

		$for = $this->parseForAttribute($callback, $dataContainerName, $for);

		foreach($for as $name) {
			$this->registerCallback($callback, $dataContainerName, $name);
		}

		return $this;
	}


	/**
	 * @param $dataContainerName
	 * @param $name
	 * @param $callback
	 * @return string
	 */
	private static function getCallbackPath($callback, $dataContainerName, $name)
	{
		$path = '';

		switch($callback) {
			case Callback::CONTAINER_ON_CUT:
			case Callback::CONTAINER_ON_DELETE:
			case Callback::CONTAINER_ON_LOAD:
			case Callback::CONTAINER_ON_SUBMIT:
				$path = 'config/' . $dataContainerName . '/' . $callback . '_callback';
				break;

			case Callback::MODEL_OPTIONS:
			case Callback::PROPERTY_INPUT_FIELD:
				$path = 'fields/' . $name . '/' . $callback;
				break;

			case Callback::PROPERTY_INPUT_FIELD_GET_WIZARD:
				$path = 'fields/' . $name . '/' . $callback;
				break;

			case Callback::CONTAINER_GLOBAL_BUTTON:
				$path = 'list/' . $callback;
				break;

			case Callback::MODEL_OPERATION_BUTTON:
				$path = 'list/operations/' . $name . '/button_callback';
				break;

			case Callback::MODEL_LABEL:
				$path = 'list/label/' . $callback . '_callback';
				break;

			case 'load':
			case 'save':
			case 'options':
				$path = 'fields/' . $name . '/' . $callback . '_callback';
				break;
		}

		return $path;
	}


	/**
	 * @param $table
	 * @param $path
	 * @param null $default
	 * @return mixed
	 */
	public static function getFromDca($table, $path, $default=null)
	{
		$value  = $GLOBALS['TL_DCA'][$table];
		$chunks = explode('/', $path);

		foreach($chunks as $chunk) {
			if(!isset($value[$chunk])) {
				return $default;
			}

			$value = $value[$chunk];
		}

		return $value;
	}


	/**
	 * @param $callback
	 */
	private static function assertValidCallback($callback)
	{
		Assertion::inArray(
			$callback,
			Callback::getCallbacks(),
			'Given argument "' . $callback . '" is not a valid callback '
		);
	}


	/**
	 * @param $dataContainerName
	 * @param $callback
	 * @param $for
	 * @throws \DcaTools\Exception\InvalidArgumentException
	 * @return array|false
	 */
	private static function parseForAttribute($callback, $dataContainerName, $for)
	{
		if($for != '*') {
			return array($for);
		}

		switch($callback) {
			case Callback::CONTAINER_GLOBAL_BUTTON:
				return array_keys(static::getFromDca($dataContainerName, 'list/global_operations'));
				break;

			case Callback::MODEL_OPERATION_BUTTON:
				return array_keys(static::getFromDca($dataContainerName, 'list/operations'));
				break;

			case Callback::PROPERTY_INPUT_FIELD:
			case Callback::PROPERTY_INPUT_FIELD_GET_WIZARD:
			case Callback::PROPERTY_ON_LOAD:
			case Callback::PROPERTY_ON_SAVE:
				return array_keys(static::getFromDca($dataContainerName, 'fields'));
				break;
		}

		throw new InvalidArgumentException('Wildcard not supported for callback "' . $callback . '"', 0, null, $callback);
	}


	/**
	 * @param $dataContainerName
	 * @param $path
	 * @return array
	 */
	private function &getCallbacksDefinition($dataContainerName, $path)
	{
		$value  = &$GLOBALS['TL_DCA'][$dataContainerName];
		$chunks = explode('/', $path);
		$last   = array_pop($chunks);

		foreach($chunks as $chunk) {
			if(isset($value[$chunk])) {
				$current = &$value[$chunk];
			} else {
				$current = array();
			}

			$value = & $current;
		}

		return $value[$last];
	}


	/**
	 * @param $dataContainerName
	 * @param $for
	 * @param $callback
	 */
	private function registerCallback($callback, $dataContainerName, $for)
	{
		$path       = $this->getCallbackPath($callback, $dataContainerName, $for);
		$definition = &$this->getCallbacksDefinition($dataContainerName, $path);
		$method     = Callback::getCallbackMethodFromName($callback);

		if($callback == Callback::MODEL_OPERATION_BUTTON || $callback == Callback::CONTAINER_GLOBAL_BUTTON) {
			$method .= '_' . $for;
		}

		if(Callback::isSingleCallback($callback)) {
			$definition = array($this->callbackClass, $method);
		} else {
			$definition[] = array($this->callbackClass, $method);
		}
	}

}