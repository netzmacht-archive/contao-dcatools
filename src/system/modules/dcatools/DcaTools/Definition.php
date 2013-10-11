<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 27.09.13
 * Time: 18:26
 * To change this template use File | Settings | File Templates.
 */

namespace Netzmacht\DcaTools;


use Netzmacht\DcaTools\Definition\DataContainer;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Definition
{

	/**
	 * @var DataContainer[]
	 */
	protected static $arrDataContainers = array();


	/**
	 * @param $strName
	 * @param null $objRecord
	 * @return DataContainer
	 */
	public static function getDataContainer($strName, $objRecord=null)
	{
		if(!isset(static::$arrDataContainers[$strName]))
		{
			static::$arrDataContainers[$strName] = new DataContainer($strName, $objRecord);
		}

		return static::$arrDataContainers[$strName];
	}
}