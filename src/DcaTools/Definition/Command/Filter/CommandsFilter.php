<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Definition\Command\Filter;

use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use DcaTools\Condition\Command\FilterFactory;
use DcaTools\Dca\Button;
use DcaTools\Definition\Command\CommandFilter;
use DcaTools\User\User;

class CommandsFilter extends AbstractFilter
{
    const NAME = 'commands';

    const ALL  = '*';

    /**
	 * @var array|string
	 */
    private $commands = CommandsFilter::ALL;

    /**
	 * @param array $config
	 * @param FilterFactory $factory
	 * @return CommandFilter
	 */
    public static function fromConfig(array $config, FilterFactory $factory)
    {
        /** @var CommandsFilter $filter */
        $filter = parent::fromConfig($config, $factory);

        if (isset($config['commands'])) {
            $filter->setCommands($config['commands']);
        }

        return $filter;
    }

    /**
	 * @param Button $button
	 * @param EnvironmentInterface $environment
	 * @param User $user
	 * @param ModelInterface $model
	 *
	 * @return bool
	 */
    public function match(Button $button, EnvironmentInterface $environment, User $user, ModelInterface $model = null)
    {
        if ($this->commands == static::ALL) {
            $match = true;
        } else {
            $match = in_array($button->getKey(), $this->commands);
        }

        return $this->applyInverse($match);
    }

    /**
	 * @param $commands
	 *
	 * @return $this
	 */
    public function setCommands($commands)
    {
        if (!is_array($commands) && $commands != static::ALL) {
            $this->commands = (array) $commands;
        } else {
            $this->commands = $commands;
        }

        return $this;
    }

    /**
	 * @return array|string
	 */
    public function getCommands()
    {
        return $this->commands;
    }

}
