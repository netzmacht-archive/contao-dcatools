<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 10.10.13
 * Time: 15:38
 * To change this template use File | Settings | File Templates.
 */

namespace Netzmacht\DcaTools\Structure;

/**
 * Class OperationInterface is used for operation components
 *
 * @package Netzmacht\DcaTools\Structure
 */
interface OperationInterface extends \DcGeneral\DataDefinition\OperationInterface, ExportInterface
{
	/**
	 * Set Attributes of operation
	 *
	 * @param string $strAttributes
	 */
	public function setAttributes($strAttributes);


	/**
	 * Set href
	 *
	 * @param string $strHref
	 */
	public function setHref($strHref);


	/**
	 * Set icon
	 *
	 * @param string $strIcon
	 */
	public function setIcon($strIcon);


	/**
	 * Set label
	 *
	 * @param $arrLabel
	 */
	public function setLabel($arrLabel);


	/**
	 * Set title
	 *
	 * @param string $strTitle
	 */
	public function setTitle($strTitle);


	/**
	 * Get Title
	 *
	 * @return mixed
	 */
	public function getTitle();


	/**
	 * Get scope
	 *
	 * @return string
	 */
	public function getScope();


	/**
	 * Set Scope
	 *
	 * @param $strScope
	 */
	public function setScope($strScope);


	/**
	 * Set information
	 *
	 * @param $strKey
	 * @param $value
	 */
	public function set($strKey, $value);

}