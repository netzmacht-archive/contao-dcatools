<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\View;

use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPasteButtonEvent;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;

interface ViewHelper
{
    /**
	 * @param EnvironmentInterface $environment
	 * @return ViewHelper
	 */
    public function setEnvironment(EnvironmentInterface $environment);

    /**
	 * @param $objCommand
	 * @param $objModel
	 * @param $blnCircularReference
	 * @param $arrChildRecordIds
	 * @param $previous
	 * @param $next
	 * @return mixed
	 */
    public function renderCommand($objCommand, $objModel, $blnCircularReference, $arrChildRecordIds, $previous, $next);

    /**
	 * @param GetPasteButtonEvent $event
	 * @return mixed
	 */
    public function renderPasteAfterButton(GetPasteButtonEvent $event);

    /**
	 * @param GetPasteButtonEvent $event
	 * @return mixed
	 */
    public function renderPasteIntoButton(GetPasteButtonEvent $event);

}
