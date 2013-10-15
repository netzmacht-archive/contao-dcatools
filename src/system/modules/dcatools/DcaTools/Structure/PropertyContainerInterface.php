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

namespace DcaTools\Structure;

use DcaTools\Definition\Property;

interface PropertyContainerInterface extends \IteratorAggregate, ExportInterface
{

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
	 * @return \ArrayIterator|Property[]
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


	/**
	 * @param \Traversable $objIterator
	 * @return mixed
	 */
	public static function convertToString(\Traversable $objIterator);


	/**
	 * Convert list of properties to an array
	 *
	 * @param \Traversable $objIterator
	 *
	 * @return array
	 */
	public static function convertToArray(\Traversable $objIterator);

}