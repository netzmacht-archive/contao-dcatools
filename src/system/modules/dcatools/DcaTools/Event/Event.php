<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 21.10.13
 * Time: 08:06
 * To change this template use File | Settings | File Templates.
 */

namespace DcaTools\Event;

use DcGeneral\Data\ModelInterface;
use Symfony\Component\EventDispatcher\GenericEvent;


/**
 * Class Event
 * @package DcaTools\Event
 */
class Event extends GenericEvent
{

	/**
	 * @return bool
	 */
	public function hasOutput()
	{
		return isset($this['output']) && $this['output'] != '';
	}


	/**
	 * @return string
	 */
	public function getOutput()
	{
		if(isset($this['output']))
		{
			return $this['output'];
		}

		return '';
	}


	/**
	 * @param string $strBuffer
	 */
	public function setOutput($strBuffer)
	{
		$this['output'] = $strBuffer;
	}


	/**
	 * @param ModelInterface $objModel
	 */
	public function setModel(ModelInterface $objModel)
	{
		$this['model'] = $objModel;
	}


	/**
	 * @return ModelInterface
	 */
	public function getModel()
	{
		if(!$this->hasModel() && method_exists($this->getSubject(), 'getModel'))
		{
			return $this->getSubject()->getModel();
		}

		return $this['model'];
	}


	/**
	 * @return bool
	 */
	public function hasModel()
	{
		return isset($this['model']);
	}

}