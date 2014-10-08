<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Condition\Permission\Event;

use ContaoCommunityAlliance\DcGeneral\Event\AbstractEnvironmentAwareEvent;
use DcaTools\Condition\Permission\Context;

final class GetPermissionContextEvent extends AbstractEnvironmentAwareEvent
{
    const NAME = 'dcatools.get-permission-context';

    /**
	 * @var \DcaTools\Condition\Permission\Context $context
	 */
    private $context;

    /**
	 * @param mixed $context
	 */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
	 * @return mixed
	 */
    public function getContext()
    {
        return $this->context;
    }

}
