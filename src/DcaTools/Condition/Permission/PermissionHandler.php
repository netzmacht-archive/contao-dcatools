<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Condition\Permission;


use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Controller\RedirectEvent;
use ContaoCommunityAlliance\DcGeneral\Factory\Event\CreateDcGeneralEvent;
use DcaTools\Assertion;
use DcaTools\Condition\Permission\Context\LegacyContext;
use DcaTools\Condition\Permission\Event\GetPermissionContextEvent;
use DcaTools\Definition\DcaToolsDefinition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PermissionHandler implements EventSubscriberInterface
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
			CreateDcGeneralEvent::NAME => 'checkPermission',
			GetPermissionContextEvent::NAME => array('getPermissionContext', -100)
		);
	}


	/**
	 * @param CreateDcGeneralEvent $event
	 */
	public function checkPermission(CreateDcGeneralEvent $event)
	{
		/** @var DcaToolsDefinition $definition */
		$environment = $event->getDcGeneral()->getEnvironment();
		$propagator  = $environment->getEventPropagator();
		$definition  = $environment->getDataDefinition()->getDefinition(DcaToolsDefinition::NAME);
		$conditions  = $definition->getPermissionConditions();

		$event = new GetPermissionContextEvent($environment);;
		$propagator->propagate($event::NAME, $event, array($environment->getDataDefinition()->getName()));

		$context = $event->getContext();
		Assertion::isInstanceOf($context, 'DcaTools\Condition\Permission\Context\Context', 'No permission context created');

		foreach($conditions as $condition) {
			/** @var PermissionCondition $condition */
			if(!$condition->__invoke($environment, $context)) {
				\Controller::log($condition->getError(), get_class($condition) . '::__INVOKE__', TL_ERROR);
				$redirect   = new RedirectEvent('contao/main.php?act=error');

				$propagator->propagate(ContaoEvents::CONTROLLER_REDIRECT, $redirect);
				exit;
			}
		}
	}


	/**
	 * @param GetPermissionContextEvent $event
	 * @return LegacyContext
	 */
	public function getPermissionContext(GetPermissionContextEvent $event)
	{
		/** @var DcaToolsDefinition $definition */
		$environment = $event->getEnvironment();
		$definition  = $event->getEnvironment()->getDataDefinition()->getDefinition(DcaToolsDefinition::NAME);

		if($event->getContext() || !$definition->getLegacyMode()) {
			return;
		}

		$context = new LegacyContext($environment);
		$event->setContext($context);
	}

} 