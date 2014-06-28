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
use ContaoCommunityAlliance\DcGeneral\Data\DefaultModel;
use ContaoCommunityAlliance\DcGeneral\Factory\Event\CreateDcGeneralEvent;
use DcaTools\Definition\DcaToolsDefinition;

class PermissionHandler
{

	/**
	 * @param CreateDcGeneralEvent $event
	 */
	public static function handle(CreateDcGeneralEvent $event)
	{
		/** @var DcaToolsDefinition $definition */
		$environment = $event->getDcGeneral()->getEnvironment();
		$definition  = $environment->getDataDefinition()->getDefinition(DcaToolsDefinition::NAME);
		$conditions  = $definition->getPermissionConditions();

		foreach($conditions as $condition) {
			/** @var PermissionCondition $condition */
			if(!$condition->__invoke($environment, new DefaultModel())) {
				\Controller::log($condition->getError(), get_class($condition) . '::__INVOKE__', TL_ERROR);
				$redirect   = new RedirectEvent('contao/main.php?act=error');
				$dispatcher = $event
					->getDispatcher()
					->dispatch(ContaoEvents::CONTROLLER_REDIRECT, $redirect);

				exit;
			}
		}
	}
} 