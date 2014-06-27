<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Dca\Button\Condition;


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;
use DcaTools\Dca\Button;


class DisableCondition extends AbstractCondition
{
	/**
	 * @var array
	 */
	protected $config = array(
		'always'    => false,
		'property'  => null,
		'value'     => null,
		'condition' => null,
		'inverse'   => false,
	);


	/**
	 * @param Button $button
	 * @param InputProviderInterface $input
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function __invoke(Button $button, InputProviderInterface $input, ModelInterface $model=null)
	{
		$disabled = false;

		if($this->config['always']) {
			$disabled = true;
		}
		elseif($this->config['property']) {
			if($model->getProperty($this->config['property']) == $this->config['value']) {
				$disabled = true;
			}
		}
		elseif($this->config['condition']) {
			$disabled = $this->manager->handleCondition(
				(array) $this->config['condition'],
				$button,
				$model
			);
		}

		if($this->config['inverse']) {
			$disabled = !$disabled;
		}

		$button->setDisabled($disabled);

		//return $disabled;
	}

} 