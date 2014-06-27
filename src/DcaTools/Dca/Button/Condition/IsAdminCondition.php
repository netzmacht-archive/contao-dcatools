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
use DcaTools\Acl;
use DcaTools\Dca\Button;


class IsAdminCondition extends AbstractCondition
{
	/**
	 * @var Acl
	 */
	private $acl;

	/**
	 * @var array
	 */
	protected $config = array(
		'always' => false,
		'action' => array(),
	);


	/**
	 * @param Acl $acl
	 * @param ConditionManager $manager
	 * @param array $arguments
	 */
	public function __construct(Acl $acl, ConditionManager $manager, $arguments = array())
	{
		if(isset($arguments['action'])) {
			$arguments['action'] = (array) $arguments['action'];
		}

		parent::__construct($manager, $arguments);

		$this->acl = $acl;
	}


	/**
	 * @param Button $button
	 * @param InputProviderInterface $input
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function __invoke(Button $button, InputProviderInterface $input, ModelInterface $model = null)
	{
		if($this->config['always']) {
			return $this->acl->isAdmin();
		}

		if($this->config['action'] && in_array($input->getParameter('action'), $this->config['action'])) {
			return $this->acl->isAdmin();
		}

		return true;
	}

}