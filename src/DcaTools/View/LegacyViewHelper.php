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

class LegacyViewHelper extends BaseView implements ViewHelper
{

    /**
	 * @param $objCommand
	 * @param $objModel
	 * @param $blnCircularReference
	 * @param $arrChildRecordIds
	 * @param $previous
	 * @param $next
	 * @return mixed
	 */
    public function renderCommand($objCommand, $objModel, $blnCircularReference, $arrChildRecordIds, $previous, $next)
    {
        $buffer = $this->buildCommand($objCommand, $objModel, $blnCircularReference, $arrChildRecordIds, $previous, $next);

        // remove serialized ids
        $buffer = preg_replace('/id=tl_[^\:]*::/', 'id=', $buffer);

        // rewrite toggle action
        $buffer = preg_replace('/act=toggle&id=[0-9]*/', '', $buffer);

        if ($buffer) {
            $buffer .= ' ';
        }

        return $buffer;
    }

}
