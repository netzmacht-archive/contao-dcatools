<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Dca\Callback;


use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\BuildWidgetEvent;

class EventSubscriber
{
	/**
	 * @param BuildWidgetEvent $event
	 * @return string
	 */
	public function propertyInputFieldGetWizard(BuildWidgetEvent $event)
	{
		$widget = $event->getWidget();

		return $widget->wizard;
	}


	/**
	 * @param BuildWidgetEvent $event
	 * @return string
	 */
	public function propertyInputFieldGetXLabel(BuildWidgetEvent $event)
	{
		$widget = $event->getWidget();

		return $widget->xlabel;
	}

} 