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


use DcaTools\Config\Map;
use DcaTools\Dca\Button\ButtonRenderer;
use DcaTools\Dca\Button;

class ConditionHandler
{
	/**
	 * @var
	 */
	private $conditions;

	/**
	 * @var ButtonRenderer
	 */
	private $renderer;

	/**
	 * @var Map
	 */
	private $map;

	/**
	 * @param $conditions
	 * @param \DcaTools\Dca\Button\ButtonRenderer $renderer
	 * @param \DcaTools\Config\Map $map
	 */
	function __construct($conditions, ButtonRenderer $renderer, Map $map)
	{
		$this->conditions = $conditions;
		$this->renderer   = $renderer;
	}


	public function __invoke(Button $button)
	{
		$this->executeConditions();
		return $this->renderer->render($button);
	}

	/**
	 *
	 */
	private function executeConditions()
	{

	}

}