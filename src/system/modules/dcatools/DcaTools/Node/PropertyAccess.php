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

use Netzmacht\DcaTools\Property;

interface PropertyAccess extends \IteratorAggregate
{

	/**
	 * Add a property
	 *
	 * @param Property|string $property
	 *
	 * @return $this
	 *
	 * @throws \RuntimeException
	 */
	public function addProperty($property);


	/**
	 * Get an Property
	 *
	 * @param string $strName
	 *
	 * @return Property
	 *
	 * @throws \RuntimeException if Property does not exists
	 */
	public function getProperty($strName);


	/**
	 * @return Property[]
	 */
	public function getProperties();


	/**
	 * Check if property exists in container
	 *
	 * @param string|Property $property
	 *
	 * @return bool
	 */
	public function hasProperty($property);


	/**
	 * Create a new property
	 *
	 * @param $strName
	 *
	 * @return Property
	 *
	 * @throws \RuntimeException
	 */
	public function createProperty($strName);


	/**
	 * Remove a property from the container
	 *
	 * @param Property|string $property
	 * @param bool $blnFromDataContainer
	 *
	 * @return $this
	 */
	public function removeProperty($property, $blnFromDataContainer=false);


	/**
	 * Move property to new position
	 *
	 * @param Property|string $property
	 *
	 * @return $this
	 */
	public function moveProperty($property);


	/**
	 * Get all propertys and also include activated propertys in SubPalettes
	 *
	 * @return array
	 */
	public function getActiveProperties();


	/**
	 * Check if container has selector propertys
	 *
	 * @return bool
	 */
	public function hasSelectors();


	/**
	 * Get all selectors containing to the
	 *
	 * @return Property[]
	 */
	public function getSelectors();

}