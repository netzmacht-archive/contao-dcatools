<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   netzmacht-dcatools
 * @author    netzmacht creative David Molineus
 * @license   LGPL/3.0
 * @copyright 2013 netzmacht creative David Molineus
 */

namespace DcaTools\DataContainer;

use DcaTools\Event\Event;


/**
 * Class Content
 * @package DcaTools\Model
 */
class Content
{

	/**
	 * @param Event $objEvent
	 * @return array
	 */
	public static function getAllowedIds(Event $objEvent)
	{
		$arrDataContainers = array('tl_article', 'tl_news', 'tl_calendar_events');

		if($objEvent->hasArgument('parentDataContainer'))
		{
			$strDataContainer = $objEvent['parentDataContainer'];

			if(in_array($strDataContainer, $arrDataContainers))
			{
				static::doGetAllowedIds($strDataContainer, $objEvent);
			}
		}
		else {
			foreach($arrDataContainers as $strDataContainer)
			{
				static::doGetAllowedIds($strDataContainer, $objEvent);
			}
		}

		return $objEvent['ids'];
	}


	/**
	 * @param Event $objEvent
	 * @return array
	 */
	public static function getAllowedDynamicParents(Event $objEvent)
	{
		$arrPtables = array();

		/** @var \BackendUser $objUser */
		$objUser = \BackendUser::getInstance();

		if($objUser->hasAccess('article', 'modules'))
		{
			$arrPtables[] = 'tl_article';
		}

		if($objUser->hasAccess('news', 'modules'))
		{
			$arrPtables[] = 'tl_news';
		}

		if($objUser->hasAccess('calendar', 'modules'))
		{
			$arrPtables[] = 'tl_calendar_events';
		}

		$objEvent['ptables'] = array_merge($objEvent['ptables'], $arrPtables);

		return $arrPtables;
	}


	/**
	 * @param Event $objEvent
	 */
	public static function getAllowedEntries(Event $objEvent)
	{
		$arrDataContainers  = array('tl_article', 'tl_news', 'tl_calendar_events');

		if(isset($objEvent['parentDataContainer']))
		{
			$strDataContainer = $objEvent['parentDataContainer'];

			if(in_array($strDataContainer, $arrDataContainers))
			{
				$objEvent['fields'] = array($strDataContainer == 'tl_news' ? 'x.headline as title' : 'x.title');
				static::getAllowedEntriesFor($strDataContainer, $objEvent);
			}
		}
		else {
			foreach($arrDataContainers as $strDataContainer)
			{
				static::getAllowedEntriesFor($strDataContainer, $objEvent);
			}
		}

		return $objEvent['entries'];
	}


	/**
	 * @param $strDataContainer
	 * @param Event $objEvent
	 *
	 * @return array
	 */
	protected static function doGetAllowedIds($strDataContainer, Event $objEvent)
	{
		$arrPids = array();
		$arrIds = array();

		$objUser = \BackendUser::getInstance();

		$strQuery = 'SELECT id FROM tl_content WHERE ';

		if($strDataContainer == 'tl_article')
		{
			$strQuery .= "(ptable='tl_article' OR ptable='')";
		}
		else {
			$strQuery .= "ptable='$strDataContainer'";
		}

		$arrField = array(
			'tl_news' => array
			(
				'module' => 'news',
				'table'  => 'tl_news_archive',
			),
			'tl_calendar_events' => array
			(
				'module' => 'calendar',
				'table' => 'tl_calendar',
			),
			'tl_article' => array
			(
				'module' => 'pagemounts',
				'table'  => 'tl_page'
			),
		);

		if(!$objUser->isAdmin)
		{
			foreach ($objUser->{$arrField[$strDataContainer]['module']} as $id)
			{
				$arrPids[] = $id;
				$arrPids = array_merge($arrPids, \Database::getInstance()->getChildRecords($id, $arrField[$strDataContainer]['table']));
			}

			if (empty($arrPids))
			{
				return $arrIds;
			}

			$strQuery = ' AND pid IN ' .implode(',', array_map('intval', array_unique($arrPids))) .')';
		}

		$objResult = \Database::getInstance()->execute($strQuery);
		$arrIds = $objResult->fetchEach('id');

		$objEvent->setArgument('ids', array_merge($objEvent->getArgument('ids'), $arrIds));
		return $arrIds;
	}


	/**
	 * @param $strParent
	 * @param $objEvent
	 *
	 * @return array
	 */
	public static function getAllowedEntriesFor($strParent, $objEvent)
	{
		$strFields = '';
		$strQuery = "SELECT
				c.id, c.pid, c.type, (CASE c.type
					WHEN 'module' THEN m.name
					WHEN 'form' THEN f.title
					WHEN 'table' THEN c.summary
					ELSE c.headline END) AS headline,
				c.text%s
				FROM tl_content c
				LEFT JOIN %s x ON x.id=c.pid
				LEFT JOIN tl_module m ON m.id=c.module
				LEFT JOIN tl_form f on f.id=c.form
				WHERE %s
				ORDER BY c.sorting";

		if($strParent == 'tl_article')
		{
			$strWhere = "(c.ptable='tl_article' OR c.ptable='')";
		}
		else {
			$strWhere = "c.ptable='{$strParent}'";
		}

		if(!\BackendUser::getInstance()->isAdmin)
		{
			$arrIds = static::getAllowedIds($objEvent);
			$strWhere .= " AND c.id IN(" . implode(',', array_map('intval', array_unique($arrIds))) . '") ';
		}

		if(isset($objEvent['id']))
		{
			$strWhere .= ' AND c.id!=?';
		}

		if(isset($objEvent['fields']) && !empty($objEvent['fields']))
		{
			$strFields = ', ' . implode(', ', $objEvent['fields']);
		}

		$arrEntries = array();
		$objResult = \Database::getInstance()
			->prepare(sprintf($strQuery, $strFields, $strParent, $strWhere))
			->execute(isset($objEvent['id']) ? $objEvent['id'] : null);

		while($objResult->next())
		{
			$arrEntries[$strParent][$objResult->pid][] = $objResult->row();
		}

		$objEvent['entries'] = array_merge($objEvent['entries'], $arrEntries);
		return $arrEntries;
	}
}