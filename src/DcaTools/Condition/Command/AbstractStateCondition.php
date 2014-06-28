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
use DcaTools\Assertion;
use DcaTools\Dca\Button;
use DcaTools\Condition\Command;

/**
 * Class AbstractStateCondition
 * @package DcaTools\Dca\Button\Condition
 */
abstract class AbstractStateCondition extends AbstractCondition
{
	protected $config = array(
		'always'    => false,
		'condition' => null,
		'property'  => null,
		'value'		=> null,
		'callback'  => null,
		'invserse'  => false,
	);


	/**
	 * @param Button $button
	 * @param InputProviderInterface $input
	 * @param ModelInterface $model
	 * @return bool
	 */
	protected function getState(Button $button, InputProviderInterface $input, ModelInterface $model=null)
	{
		$visible = true;

		if($this->config['always']) {
			$visible = false;
		}
		elseif($this->config['condition']) {
			$condition = $this->config['condition'];
			$visible   = $condition($button, $input, $model);
		}
		elseif($this->config['property']) {
			Assertion::notNull($model, 'Property can part of condition for model operations');

			$visible = ($model->getProperty($this->config['property']) == $this->config['value']);
		}
		elseif($this->config['callback']) {
			$callback = $this->config['callback'];
			$visible  = call_user_func($callback, $button, $input, $model);
		}

		if($this->config['inverse']) {
			$visible = !$visible;

			return $visible;
		}

		return $visible;
	}

} 