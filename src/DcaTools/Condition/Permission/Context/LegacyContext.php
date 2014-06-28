<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Condition\Permission\Context;


use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\DcGeneral;
use DcaTools\Data\ConfigBuilder;

class LegacyContext implements Context
{
	/**
	 * @var DcGeneral
	 */
	private $dcGeneral;


	/**
	 * @param $dcGeneral
	 */
	function __construct(DcGeneral $dcGeneral)
	{
		$this->dcGeneral = $dcGeneral;
	}


	/**
	 * @return ModelInterface
	 */
	public function getParent()
	{
		$environment = $this->dcGeneral->getEnvironment();
		$name        = $environment->getParentDataDefinition()->getName();
		$input       = $environment->getInputProvider();

		if($input->hasParameter('id')) {
			$id = $input->getParameter('id');
		}
		else {
			$id = CURRENT_ID;
		}

		return ConfigBuilder::create($environment, $name)->setId($id)->fetch();
	}


	/**
	 * @return ModelInterface
	 */
	public function getModel()
	{
		$environment = $this->dcGeneral->getEnvironment();
		$input       = $environment->getInputProvider();
		$id 		 = $input->getParameter('id');

		return ConfigBuilder::create($environment)->id($id)->fetch();
	}

} 