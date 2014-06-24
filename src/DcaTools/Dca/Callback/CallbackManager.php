<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Dca\Callback;


use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\BuildWidgetEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\DecodePropertyValueForWidgetEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\EncodePropertyValueFromWidgetEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetGlobalButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetGroupHeaderEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetParentHeaderEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPasteButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\ModelToLabelEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\ParentViewChildRecordEvent;
use ContaoCommunityAlliance\DcGeneral\Event\PostDeleteModelEvent;
use ContaoCommunityAlliance\DcGeneral\Event\PostDuplicateModelEvent;
use ContaoCommunityAlliance\DcGeneral\Event\PostPasteModelEvent;
use ContaoCommunityAlliance\DcGeneral\Event\PostPersistModelEvent;
use ContaoCommunityAlliance\DcGeneral\Factory\Event\CreateDcGeneralEvent;
use DcaTools\Assertion;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CallbackManager
{
	// container callbacks
	const CONTAINER_ON_LOAD   		  = 'onload';
	const CONTAINER_ON_SUBMIT 		  = 'onsubmit';
	const CONTAINER_ON_DELETE 		  = 'ondelete';
	const CONTAINER_ON_CUT 	  		  = 'oncut';
	const CONTAINER_ON_COPY   		  = 'oncopy';
	const CONTAINER_HEADER		      = 'header';
	const CONTAINER_GLOBAL_BUTTON	  = 'global_operation/button';
	const CONTAINER_PASTE_BUTTON	  = 'paste';
	const CONTAINER_SUBMIT_BUTTON     = 'buttons';
	const CONTAINER_PANEL			  = 'panel';

	// model callbacks
	const MODEL_OPTIONS 	 		  = 'options';
	const MODEL_LABEL 		 		  = 'label';
	const MODEL_GROUP 		 		  = 'group';
	const MODEL_CHILD_RECORD 		  = 'child_record';
	const MODEL_OPERATION_BUTTON	  = 'operation/button';
	const MODEL_VERSION_RESTORE		  = 'onrestore';
	const MODEL_ON_CREATE		      = 'oncreate';

	// property callbacks
	const PROPERTY_ON_SAVE 			  = 'save';
	const PROPERTY_ON_LOAD 			  = 'load';
	const PROPERTY_INPUT_FIELD 		  = 'input_field';
	const PROPERTY_INPUT_FIELD_WIZARD = 'wizard';
	const PROPERTY_INPUT_FIELD_XLABEL = 'xlabel';


	/**
	 * @var array $map map between callback and triggered event
	 */
	private static $map = array
	(
		CallbackManager::CONTAINER_GLOBAL_BUTTON     => GetGlobalButtonEvent::NAME,
		CallbackManager::CONTAINER_HEADER            => GetParentHeaderEvent::NAME,
		CallbackManager::CONTAINER_ON_COPY           => PostDuplicateModelEvent::NAME,
		CallbackManager::CONTAINER_ON_LOAD           => CreateDcGeneralEvent::NAME,
		CallbackManager::CONTAINER_ON_SUBMIT         => PostPersistModelEvent::NAME,
		CallbackManager::CONTAINER_ON_DELETE         => PostDeleteModelEvent::NAME,
		CallbackManager::CONTAINER_ON_CUT            => PostPasteModelEvent::NAME,
		CallbackManager::CONTAINER_PASTE_BUTTON      => GetPasteButtonEvent::NAME,
		CallbackManager::CONTAINER_SUBMIT_BUTTON     => '',
		CallbackManager::CONTAINER_PANEL             => 'panel',
		CallbackManager::MODEL_OPTIONS               => GetPropertyOptionsEvent::NAME,
		CallbackManager::MODEL_LABEL                 => ModelToLabelEvent::NAME,
		CallbackManager::MODEL_GROUP                 => GetGroupHeaderEvent::NAME,
		CallbackManager::MODEL_CHILD_RECORD          => ParentViewChildRecordEvent::NAME,
		CallbackManager::MODEL_OPERATION_BUTTON      => GetGroupHeaderEvent::NAME,
		CallbackManager::MODEL_VERSION_RESTORE       => '',
		CallbackManager::MODEL_ON_CREATE             => 'oncreate',
		CallbackManager::PROPERTY_ON_SAVE            => EncodePropertyValueFromWidgetEvent::NAME,
		CallbackManager::PROPERTY_ON_LOAD            => DecodePropertyValueForWidgetEvent::NAME,
		CallbackManager::PROPERTY_INPUT_FIELD        => BuildWidgetEvent::NAME,
		CallbackManager::PROPERTY_INPUT_FIELD_WIZARD => 'propertyInputFieldGetWizard',
		CallbackManager::PROPERTY_INPUT_FIELD_XLABEL => 'propertyInputFieldGetXLabel',
	);


	/**
	 * @var CallbackDispatcher
	 */
	private $callbackDispatcher;

	/**
	 * @var string
	 */
	private $callbackClass;


	/**
	 * @param CallbackDispatcher $callbackDispatcher
	 * @param $callbackClass
	 */
	function __construct(CallbackDispatcher $callbackDispatcher, $callbackClass)
	{
		$this->callbackDispatcher = $callbackDispatcher;
		$this->callbackClass	  = $callbackClass;
	}


	/**
	 * @param string $dataContainerName
	 * @param string $nameOrCallback
	 * @param string null $callback
	 */
	public function enableCallback($dataContainerName, $nameOrCallback, $callback=null)
	{
		// check if name wildcard is used for enabling one callback for each property
		if($callback) {
			$nameOrCallback = static::getPropertyNames($dataContainerName, $nameOrCallback);

			if(is_array($nameOrCallback)) {
				foreach($nameOrCallback as $name) {
					static::enableCallback($dataContainerName, $name, $callback);
				}

				return;
			}
		}
		elseif($callback === null) {
			$callback = $nameOrCallback;
		}

		static::assertValidCallback($callback);

		$path = self::getCallbackPath($dataContainerName, $nameOrCallback, $callback);

		if(static::isSingleCallback($callback)) {
			$callable = static::getFromDca($dataContainerName, $path);

			if($callable) {
				$this->registerSingleCallbackAsEvent($dataContainerName, $path, $callable);
			}

			$this->enableSingleCallbackEvent($dataContainerName, $path, 'METHDO');
		}
		else {
			$this->enableMultipleCallbackEvent($dataContainerName, $path, 'METHD');
		}
	}


	/**
	 * @param $dataContainerName
	 * @param $callbackName
	 * @param $callback
	 */
	private function registerSingleCallbackAsEvent($dataContainerName, $callbackName, $callback)
	{
		Assertion::keyExists(static::$map, $callbackName, 'Unknown callback');
		Assertion::true(CallbackManager::isSingleCallback($callbackName));

		$eventName = static::$map[$callbackName];
		$this->callbackDispatcher->registerCallback($dataContainerName, $eventName, $callback);
	}


	/**
	 * @param $table
	 * @param $path
	 * @param $method
	 * @internal param $eventName
	 */
	private function enableSingleCallbackEvent($table, $path, $method)
	{
		$value  = &$GLOBALS['TL_DCA'][$table];
		$chunks = explode('/', $path);
		$last   = array_pop($last);

		foreach($chunks as $chunk) {
			if(isset($value[$chunk])) {
				$current = &$value[$chunk];
				unset($value[$chunk]);
			}
			else {
				$current = array();
			}

			$value = &$current;
		}

		$value[$last] = array($this->callbackClass, $method);
	}


	/**
	 * @param $table
	 * @param $path
	 * @param $method
	 */
	private function enableMultipleCallbackEvent($table, $path, $method)
	{
		$value  = &$GLOBALS['TL_DCA'][$table];
		$chunks = explode('/', $path);
		$last   = array_pop($last);

		foreach($chunks as $chunk) {
			if(isset($value[$chunk])) {
				$current = &$value[$chunk];
				unset($value[$chunk]);
			}
			else {
				$current = array();
			}

			$value = &$current;
		}

		$value[$last][] = array($this->callbackClass, $method);
	}


	/**
	 * @param $table
	 * @param $name
	 * @param $callback
	 * @return string
	 */
	private static function getCallbackPath($table, $name, $callback)
	{
		$path = '';

		switch($callback) {
			case static::CONTAINER_ON_CUT:
			case static::CONTAINER_ON_DELETE:
			case static::CONTAINER_ON_LOAD:
			case static::CONTAINER_ON_SUBMIT:
				$path = 'config/' . $table . '/' . $callback . '_callback';
				break;

			case static::MODEL_OPTIONS:
			case static::PROPERTY_INPUT_FIELD:
				$path = 'fields/' . $name . '/' . $callback;
				break;

			case static::PROPERTY_INPUT_FIELD_WIZARD:
			case static::PROPERTY_INPUT_FIELD_XLABEL:
				$path = 'fields/' . $name . '/' . $callback;
				break;

			case static::CONTAINER_GLOBAL_BUTTON:
			case static::MODEL_OPERATION_BUTTON:
				$path = 'list/' . $callback;
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
	 * Consider whether a callback is a single callback
	 *
	 * @param $callback
	 * @return bool
	 */
	public static function isSingleCallback($callback)
	{
		return in_array($callback, array(
			static::CONTAINER_GLOBAL_BUTTON,
			static::MODEL_OPTIONS,
			static::MODEL_OPERATION_BUTTON,
			static::PROPERTY_INPUT_FIELD,
		));
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
		Assertion::inArray($callback, static::getCallbacks(), 'Given argument "' . $callback . '" is not a valid callback ');
	}


	/**
	 * @return array
	 */
	private static function getCallbacks()
	{
		$reflect = new \ReflectionClass(get_called_class());
		return $reflect->getConstants();
	}


	/**
	 * @param $table
	 * @param $nameOrCallback
	 * @return array|false
	 */
	private static function getPropertyNames($table, $nameOrCallback)
	{
		if($nameOrCallback == '*') {
			return array_keys(static::getFromDca($table, 'fields'));
		}

		return $nameOrCallback;
	}

}