<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Condition\Command;


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;
use DcaTools\Dca\Button;
use DcaTools\Condition\Command;

class HideCondition extends AbstractStateCondition
{

	/**
	 * @param Button $button
	 * @param InputProviderInterface $input
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function __invoke(Button $button, InputProviderInterface $input, ModelInterface $model = null)
	{
		$visible = $this->getState($button, $input, $model);

		$button->setVisible($visible);
	}

} 