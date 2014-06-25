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
	const CONTAINER_PANEL			  = 'panel';

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
	public static function getCallbackMethodFromName($callback)
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
			Callback::CONTAINER_GLOBAL_BUTTON,
			Callback::MODEL_LABEL,
			Callback::MODEL_OPTIONS,
			Callback::MODEL_OPERATION_BUTTON,
			Callback::PROPERTY_INPUT_FIELD,
		));
	}

}