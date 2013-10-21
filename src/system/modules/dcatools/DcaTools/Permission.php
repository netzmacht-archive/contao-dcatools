<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 14.10.13
 * Time: 09:21
 * To change this template use File | Settings | File Templates.
 */

namespace DcaTools;

/**
 * Class Permission
 * @package DcaTools
 */
class Permission
{

	/**
	 * Get all allowed ptables for a user.
	 *
	 * It is nessecary to register getAllowedDynamicParents events for the data container to set the ptables
	 *
	 * @param $strTable
	 * @return mixed
	 * @throws \RuntimeException
	 */
	public static function getAllowedDynamicParents($strTable)
	{
		return Controller::getInstance($strTable)->getAllowedDynamicParents();
	}


	/**
	 * Get all ids which are allowed for the BackendUser
	 *
	 * @param $strTable
	 * @param null $strParent
	 * @return mixed
	 */
	public static function getAllowedIds($strTable, $strParent=null)
	{
		return Controller::getInstance($strTable)->getAllowedIds($strParent);
	}


	/**
	 * Get all allowed entries for a table
	 *
	 * @param $strTable
	 * @param null $strParent
	 * @param array $arrFields
	 *
	 * @return array
	 */
	public static function getAllowedEntries($strTable, $strParent=null, array $arrFields=array())
	{
		return Controller::getInstance($strTable)->getAllowedEntries($strParent, $arrFields);
	}

}