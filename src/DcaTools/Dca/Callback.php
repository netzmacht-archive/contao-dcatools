<?php

/**
 * @package    contao-dcatools
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Dca;


/**
 * Class Callbacks
 * @package DcaTools\Dca\Callback
 */
final class Callback
{
	// container callbacks
	const CONTAINER_GET_BREADCRUMB    = 'breadcrumb';
	const CONTAINER_GLOBAL_BUTTON	  = 'global_button';
	const CONTAINER_HEADER		      = 'header';
	const CONTAINER_ON_COPY   		  = 'oncopy';
	const CONTAINER_ON_CUT 	  		  = 'oncut';
	const CONTAINER_ON_DELETE 		  = 'ondelete';
	const CONTAINER_ON_LOAD   		  = 'onload';
	const CONTAINER_ON_SUBMIT 		  = 'onsubmit';
	const CONTAINER_PASTE_BUTTON	  = 'paste_button';
	const CONTAINER_PASTE_ROOT_BUTTON = 'paste_button';
	const CONTAINER_SUBMIT_BUTTON     = 'buttons';

	// model callbacks
	const MODEL_CHILD_RECORD 		  = 'child_record';
	const MODEL_GROUP 		 		  = 'group';
	const MODEL_LABEL 		 		  = 'label';
	const MODEL_OPERATION_BUTTON	  = 'model_button';
	const MODEL_OPTIONS 	 		  = 'options';

	// property callbacks
	const PROPERTY_INPUT_FIELD 		      = 'input_field';
	const PROPERTY_INPUT_FIELD_GET_WIZARD = 'wizard';
	const PROPERTY_ON_SAVE 			      = 'save';
	const PROPERTY_ON_LOAD 			      = 'load';


	/**
	 * @return array
	 */
	public static function getCallbacks()
	{
		$reflect = new \ReflectionClass(get_called_class());
		return $reflect->getConstants();
	}


	/**
	 * @param $callback
	 * @return mixed|string
	 */
	public static function getMethodName($callback)
	{
		$method = array_search($callback, static::getCallbacks());
		$method = strtolower($method);
		$method = preg_replace_callback('/_(.)/', function ($args) {
			return strtoupper($args[1]);
		}, $method);

		return $method;
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
			static::CONTAINER_PASTE_BUTTON,
			static::CONTAINER_PASTE_ROOT_BUTTON,
			static::MODEL_CHILD_RECORD,
			static::MODEL_GROUP,
			static::MODEL_LABEL,
			static::MODEL_OPTIONS,
			static::MODEL_OPERATION_BUTTON,
			static::PROPERTY_INPUT_FIELD,
			static::PROPERTY_INPUT_FIELD_GET_WIZARD,
		));
	}


	/**
	 * @param $name
	 * @param $callback
	 * @return string
	 */
	public static function getDcaPath($callback, $name=null)
	{
		$path = false;

		switch($callback) {
			case Callback::CONTAINER_ON_COPY:
			case Callback::CONTAINER_ON_CUT:
			case Callback::CONTAINER_ON_DELETE:
			case Callback::CONTAINER_ON_LOAD:
			case Callback::CONTAINER_ON_SUBMIT:
				$path = 'config/' . $callback . '_callback';
				break;

			case static::CONTAINER_SUBMIT_BUTTON:
				$path = 'edit/buttons_callback';
				break;

			case static::PROPERTY_ON_LOAD:
			case static::PROPERTY_ON_SAVE:
			case Callback::MODEL_OPTIONS:
			case Callback::PROPERTY_INPUT_FIELD:
				$path = 'fields/' . $name . '/' . $callback . '_callback';
				break;

			case Callback::PROPERTY_INPUT_FIELD_GET_WIZARD:
				$path = 'fields/' . $name . '/' . $callback;
				break;

			case static::CONTAINER_HEADER:
			case static::CONTAINER_PASTE_BUTTON:
			case static::CONTAINER_PASTE_ROOT_BUTTON:
			case static::MODEL_CHILD_RECORD:
				$path = 'list/sorting/' . $callback . '_callback';
				break;

			case static::MODEL_GROUP:
				$path = 'list/label/' . $callback . '_callback';
				break;

			case Callback::CONTAINER_GLOBAL_BUTTON:
				$path = 'list/global_operations/' . $name . '/button_callback';;
				break;

			case Callback::MODEL_OPERATION_BUTTON:
				$path = 'list/operations/' . $name . '/button_callback';
				break;

			case Callback::MODEL_LABEL:
				$path = 'list/label/' . $callback . '_callback';
				break;

		}

		return $path;
	}

}