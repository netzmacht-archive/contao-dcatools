<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   netzmacht-dcatools
 * @author    netzmacht creative David Molineus
 * @license   MPL/2.0
 * @copyright 2013 netzmacht creative David Molineus
 */

namespace Netzmacht\DcaTools\Node;

use Netzmacht\DcaTools\Field;

interface FieldAccess extends \IteratorAggregate
{

	/**
	 * Add a field
	 *
	 * @param Field|string $field
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function addField($field);


	/**
	 * Get an Field
	 *
	 * @param string $strName
	 *
	 * @return Field
	 *
	 * @throws \RuntimeException if Field does not exists
	 */
	public function getField($strName);


	/**
	 * @return Field[]
	 */
	public function getFields();


	/**
	 * Check if field exists in container
	 *
	 * @param string|Field $field
	 *
	 * @return bool
	 */
	public function hasField($field);


	/**
	 * Create a new field
	 *
	 * @param $strName
	 *
	 * @return Field
	 *
	 * @throws \RuntimeException
	 */
	public function createField($strName);


	/**
	 * Remove a field from the container
	 *
	 * @param Field|string $field
	 * @param bool $blnFromDataContainer
	 *
	 * @return $this
	 */
	public function removeField($field, $blnFromDataContainer=false);


	/**
	 * Move field to new position
	 *
	 * @param Field|string $field
	 *
	 * @return $this
	 */
	public function moveField($field);


	/**
	 * Get all fields and also include activated fields in SubPalettes
	 *
	 * @return array
	 */
	public function getActiveFields();


	/**
	 * Check if container has selector fields
	 *
	 * @return bool
	 */
	public function hasSelectors();


	/**
	 * Get all selectors containing to the
	 *
	 * @return Field[]
	 */
	public function getSelectors();

}