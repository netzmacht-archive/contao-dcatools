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
use DcaTools\User\User;


class IsAdminCondition extends AbstractCondition
{
	/**
	 * @var User
	 */
	private $user;


	/**
	 * @var array
	 */
	protected $config = array(
		'always'  => false,
		'action'  => array(),
		'inverse' => false,
	);


	/**
	 * @param User $user
	 * @param array $config
	 */
	public function __construct(User $user, $config = array())
	{
		if(isset($config['action'])) {
			$config['action'] = (array) $config['action'];
		}
		elseif(!isset($config['always'])) {
			$config['always'] = true;
		}

		parent::__construct($config);

		$this->user = $user;
	}


	/**
	 * @param Button $button
	 * @param InputProviderInterface $input
	 * @param ModelInterface $model
	 * @return bool
	 */
	public function __invoke(Button $button, InputProviderInterface $input, ModelInterface $model = null)
	{
		$isAdmin = false;

		if($this->config['always']) {
			$isAdmin =  $this->user->hasRole(User::ROLE_ADMIN);
		}
		if($this->config['action'] && in_array($input->getParameter('action'), $this->config['action'])) {
			$isAdmin = $this->user->hasRole(User::ROLE_ADMIN);
		}

		if($this->config['inverse']) {
			return !$isAdmin;
		}

		return $isAdmin;
	}

}