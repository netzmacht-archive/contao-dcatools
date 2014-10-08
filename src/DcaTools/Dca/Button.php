<?php

/**
 * @package    contao-dcatools
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Dca;

use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\BaseButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetGlobalButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetOperationButtonEvent;

/**
 * Class Button wraps the button event for read access and provides disable and visible feature
 *
 * @package DcaTools\Dca\Command
 */
class Button
{
    const CONTAINER = 'container-button';

    const OPERATION = 'operation-button';

    /**
	 * @var GetOperationButtonEvent|GetGlobalButtonEvent
	 */
    private $buttonEvent;

    /**
	 * @var bool
	 */
    private $visible = true;

    /**
	 * @param BaseButtonEvent $buttonEvent
	 */
    public function __construct(BaseButtonEvent $buttonEvent)
    {
        $this->buttonEvent = $buttonEvent;
    }

    /**
	 * @return \ContaoCommunityAlliance\DcGeneral\EnvironmentInterface
	 */
    public function getEnvironment()
    {
        return $this->buttonEvent->getEnvironment();
    }

    /**
	 * @return string
	 */
    public function getAttributes()
    {
        return $this->buttonEvent->getAttributes();
    }

    /**
	 * @return string
	 */
    public function getHtml()
    {
        return $this->buttonEvent->getHtml();
    }

    /**
	 * @return string
	 */
    public function getKey()
    {
        return $this->buttonEvent->getCommand()->getName();
    }

    /**
	 * @return string
	 */
    public function getLabel()
    {
        return $this->buttonEvent->getLabel();
    }

    /**
	 * @return string
	 */
    public function getTitle()
    {
        return $this->buttonEvent->getTitle();
    }

    /**
	 * @return \ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\View\CommandInterface
	 */
    public function getCommand()
    {
        return $this->buttonEvent->getCommand();
    }

    /**
	 * @return \ContaoCommunityAlliance\DcGeneral\Data\ModelInterface
	 */
    public function getPrevious()
    {
        return $this->buttonEvent->getPrevious();
    }

    /**
	 * @return string
	 */
    public function getHref()
    {
        return $this->buttonEvent->getHref();
    }

    /**
	 * @return \ContaoCommunityAlliance\DcGeneral\Data\ModelInterface
	 */
    public function getModel()
    {
        return $this->buttonEvent->getModel();
    }

    /**
	 * @param boolean $disabled
	 *
	 * @return $this
	 */
    public function setDisabled($disabled=true)
    {
        $this->buttonEvent->setDisabled($disabled);

        return $this;
    }

    /**
	 * @param boolean $visible
	 *
	 * @return $this
	 */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
	 * @return bool
	 */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
	 * @return bool
	 */
    public function isDisabled()
    {
        return $this->buttonEvent->isDisabled();
    }

}
