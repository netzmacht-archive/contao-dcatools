<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 27.09.13
 * Time: 08:42
 * To change this template use File | Settings | File Templates.
 */

namespace Netzmacht\DcaTools\Model;

class DcGeneralModel
{

	/**
	 * @var \DcGeneral\Data\ModelInterface
	 */
	protected $objModel;

	public function __construct($objModel)
	{
		$this->objModel = $objModel;
	}

	public function __get($strKey)
	{
		return $this->objModel->getProperty($strKey);
	}

	public function __set($strKey, $mixedValue)
	{
		$this->objModel->setProperty($strKey, $mixedValue);
	}

	public function getModel()
	{
		return $this->getModel();
	}

}