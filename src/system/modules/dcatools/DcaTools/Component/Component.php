<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 10.10.13
 * Time: 15:18
 * To change this template use File | Settings | File Templates.
 */

namespace Netzmacht\DcaTools\Component;

use DcGeneral\Data\ModelInterface;
use Netzmacht\DcaTools\Definition;
use Netzmacht\DcaTools\Event\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Component
 * @package Netzmacht\DcaTools\Component
 */
abstract class Component extends EventDispatcher
{

	/**
	 * Template name
	 * @var string
	 */
	protected $strTemplate;


	/**
	 * @var ModelInterface
	 */
	protected $objModel;


	/**
	 * Template Format
	 *
	 * @var string
	 */
	protected $strFormat = 'html5';


	/**
	 * @var bool
	 */
	protected $blnHidden;


	/**
	 * @var Definition\Child
	 */
	protected $objDefinition;


	/**
	 * Constructor
	 *
	 * @param $objDefinition
	 */
	public function __construct(Node $objDefinition)
	{
		$this->objDefinition = $objDefinition;

		$arrEvents = $objDefinition->get('events');

		if(is_array($arrEvents))
		{
			foreach($arrEvents as $strEvent => $arrListeners)
			{
				$this->addListeners($strEvent, $arrListeners);
			}
		}
	}


	/**
	 * Get Template name
	 *
	 * @return string
	 */
	public function getTemplateName()
	{
		return $this->strTemplate;
	}


	/**
	 * Change used template
	 *
	 * @param string $strName
	 * @event template
	 */
	public function setTemplateName($strName)
	{
		$this->strTemplate = $strName;
	}


	/**
	 * @param ModelInterface $objModel
	 */
	public function setModel(ModelInterface $objModel)
	{
		$this->objModel = $objModel;
	}


	/**
	 * @return ModelInterface
	 */
	public function getModel()
	{
		return $this->objModel;
	}


	/**
	 * Hide Operation
	 */
	public function hide()
	{
		$this->blnHidden = true;
	}


	/**
	 * @return bool
	 */
	public function isHidden()
	{
		return $this->blnHidden;
	}


	/**
	 * @return mixed
	 */
	abstract protected function compile(GenericEvent $objEvent);


	/**
	 * @return string
	 */
	public function generate()
	{
		if($this->isHidden())
		{
			return;
		}

		$objEvent = new GenericEvent();
		$objEvent->setArgument('render', true);

		$objEvent = $this->dispatch('generate', $objEvent);

		// check if rendering is denied
		if(!$objEvent->getArgument('render'))
		{
			return;
		}

		// check for generated component
		if($objEvent->hasArgument('buffer') && $objEvent->getArgument('buffer') != '')
		{
			return $objEvent->getArgument('buffer');
		}

		$this->compile($objEvent);

		ob_start();
		include \Controller::getTemplate($this->strTemplate);
		$strBuffer = ob_get_contents();
		ob_end_clean();

		return $strBuffer;
	}

}