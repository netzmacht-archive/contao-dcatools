<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Contao;

use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Image\GenerateHtmlEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetOperationButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPasteButtonEvent;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\View\ToggleCommandInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class CompatibilitySubscriber implements EventSubscriberInterface
{
	/**
	 * Returns an array of event names this subscriber wants to listen to.
	 *
	 * The array keys are event names and the value can be:
	 *
	 *  * The method name to call (priority defaults to 0)
	 *  * An array composed of the method name to call and the priority
	 *  * An array of arrays composed of the method names to call and respective
	 *    priorities, or 0 if unset
	 *
	 * For instance:
	 *
	 *  * array('eventName' => 'methodName')
	 *  * array('eventName' => array('methodName', $priority))
	 *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
	 *
	 * @return array The event names to listen to
	 *
	 * @api
	 */
	public static function getSubscribedEvents()
	{
		return array(
			GetPasteButtonEvent::NAME . '[tl_article]' => 'articlePasteButtons',
			ContaoEvents::IMAGE_GET_HTML 			   => 'generateDisabledIcon',
		//	GetOperationButtonEvent::NAME . '[tl_article][toggle]' => array('toggle', 100),
		);
	}


	/**
	 * @param GenerateHtmlEvent $event
	 */
	public function generateDisabledIcon(GenerateHtmlEvent $event)
	{
		if(strpos($event->getSrc(), '_1.') === false) {
			return;
		}

		$html = \Image::getHtml($event->getSrc(), $event->getAlt(), $event->getAttributes());

		if(!$html) {
			$src  = str_replace('_1.', '_.', $event->getSrc());
			$html = \Image::getHtml($src, $event->getAlt(), $event->getAttributes());

			if(!$html) {
				return;
			}

			$event->stopPropagation();
		}

		$event->setHtml($html);
	}


	/**
	 * @param GetPasteButtonEvent $event
	 */
	public function articlePasteButtons(GetPasteButtonEvent $event)
	{
		$model = $event->getModel();

		if($model->getProviderName() == 'tl_page') {
			if($model->getProperty('type') == 'root') {
				$event->setPasteIntoDisabled(true);
			}
			$event->setHtmlPasteAfter('');
		}
		else {
			$event->setHtmlPasteInto('');
		}
	}


	public function toggle(GetOperationButtonEvent $event)
	{
		$environment = $event->getEnvironment();
		$input       = $environment->getInputProvider();

		if ($input->hasParameter('id')) {
			$serializedId = $input->getParameter('id');
		}

		if (!(isset($serializedId) && $event->getModel()->getProviderName() == $environment->getDataDefinition()->getName()))
		{
			return;
		}
		else {
			$serializedId = $event->getModel();
		}

		/** @var ToggleCommandInterface $operation */
		$operation    = $event->getCommand();
		$dataProvider = $environment->getDataProvider();
		$newState     = $operation->isInverse()
			? $input->getParameter('state') == 1 ? '' : '1'
			: $input->getParameter('state') == 1 ? '1' : '';

		$model = $dataProvider->fetch($dataProvider->getEmptyConfig()->setId($serializedId->getId()));

		$model->setProperty($operation->getToggleProperty(), $newState);

		$dataProvider->save($model);
	}

}