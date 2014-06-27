<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\View;


use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\BaseView;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\View\CommandInterface;


class DcGeneralBasedViewHelper extends BaseView implements ViewHelper
{
	/**
	 * @param CommandInterface $objCommand
	 * @param ModelInterface $objModel
	 * @param bool $blnCircularReference
	 * @param array $arrChildRecordIds
	 * @param ModelInterface $previous
	 * @param ModelInterface $next
	 * @return string
	 */
	public function renderCommand($objCommand, $objModel, $blnCircularReference, $arrChildRecordIds, $previous, $next)
	{
		$buffer = $this->buildCommand($objCommand, $objModel, $blnCircularReference, $arrChildRecordIds, $previous, $next);

		// remove serialized ids
		$buffer = preg_replace('/tl_[^\:]*::/', '', $buffer);

		// rewrite toggle action
		$buffer = preg_replace('/act=toggle&id=[0-9]*/', '', $buffer);

		if($buffer) {
			$buffer .= ' ';
		}

		return $buffer;
	}

}