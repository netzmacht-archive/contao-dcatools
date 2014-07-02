<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Permission;


use ContaoCommunityAlliance\DcGeneral\Data\CollectionInterface;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;


interface Context
{
	const COLLECTION = 'collection';
	const MODEL      = 'model';
	const PARENT     = 'parent';

	/**
	 * @return ModelInterface
	 */
	public function getParent();

	/**
	 * @return ModelInterface
	 */
	public function getModel();

	/**
	 * @return CollectionInterface|null
	 */
	public function getCollection();

	/**
	 * Check if current request matches the given context string
	 *
	 * @param $context
	 * @return mixed
	 */
	public function match($context);

	/**
	 * @return bool
	 */
	public function isListView();

	/**
	 * @return mixed
	 */
	public function isParentMode();

} 