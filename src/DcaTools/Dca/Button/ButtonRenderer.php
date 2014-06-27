<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace DcaTools\Dca\Button;


use DcaTools\Dca\Button;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class ButtonRenderer
{

	/**
	 * @param $icon
	 * @return mixed
	 */
	private static function getDisabledIcon($icon)
	{
		if($icon == 'visible.gif') {
			return 'invisible.gif';
		}

		$pos = strrpos($icon, '.');

		if($pos !== false) {
			$icon = substr_replace($icon, '_.', $pos, strlen('.'));
		}

		return $icon;
	}


	/**
	 * @param Button $button
	 * @return bool|string
	 */
	public function render(Button $button)
	{
		$extra = $button->getCommand()->getExtra();

		if($button->isDisabled()) {
			$icon = $this->getDisabledIcon($extra['icon']);

			// TODO switch to element
			$html = \Image::getHtml($icon, $button->getLabel(), 'class="disabled"');

			return $html;
		}
		elseif(!$button->isVisible()) {
			return '';
		}

		return false;
	}

}