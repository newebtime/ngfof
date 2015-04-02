<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2015 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Form\Header;

use FOF30\Form\Header\Field as HeaderBase;

/**
 * Form Header class for F0F
 */
class Searchable extends HeaderBase
{
	/**
	 * Get the filter field
	 *
	 * @return  string  The HTML
	 */
	protected function getFilter()
	{
		// Initialize some field attributes.
		$size        = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength   = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$filterclass = $this->element['filterclass'] ? ' class="' . (string) $this->element['filterclass'] . '"' : '';
		$placeholder = $this->element['placeholder'] ? $this->element['placeholder'] : $this->getLabel();
		$name        = $this->element['searchfieldname'] ? $this->element['searchfieldname'] : $this->name;
		$placeholder = ' placeholder="' . \JText::_($placeholder) . '"';

		if ($this->element['searchfieldname'])
		{
			$model       = $this->form->getModel();
			$searchvalue = $model->getState((string) $this->element['searchfieldname']);
		}
		else
		{
			$searchvalue = $this->value;
		}

		// Initialize JavaScript field attributes.
		if ($this->element['onchange'])
		{
			$onchange = ' onchange="' . (string) $this->element['onchange'] . '"';
		}
		else
		{
			$onchange = ' onchange="document.adminForm.submit();"';
		}

		return '<input type="text" name="' . $name . '" id="' . $this->id . '"' . ' value="'
			. htmlspecialchars($searchvalue, ENT_COMPAT, 'UTF-8') . '"' . $filterclass . $size . $placeholder . $onchange . $maxLength . '/>';
	}

	/**
	 * Get the buttons HTML code
	 *
	 * @return  string  The HTML
	 */
	protected function getButtons()
	{
		$buttonclass = $this->element['buttonclass'] ? (string) $this->element['buttonclass'] : 'btn bnt-default hasTip hasTooltip';
		$iconSearch  = $this->element['iconSearch'] ? (string) $this->element['iconSearch'] : 'icon-search';
		$iconRemove  = $this->element['iconRemove'] ? (string) $this->element['iconRemove'] : 'icon-remove';

		$buttonsState = strtolower($this->element['buttons']);
		$show_buttons = !in_array($buttonsState, array('no', 'false', '0'));

		if (!$show_buttons)
		{
			return '';
		}

		$html = '';

		$html .= '<button class="' . $buttonclass . '" onclick="this.form.submit();" title="' . \JText::_('JSEARCH_FILTER') . '" >' . "\n";
		$html .= '<i class="' . $iconSearch . '"></i>';
		$html .= '</button>' . "\n";
		$html .= '<button class="' . $buttonclass . '" onclick="document.adminForm.' . $this->id . '.value=\'\';this.form.submit();" title="' . \JText::_('JSEARCH_RESET') . '">' . "\n";
		$html .= '<i class="' . $iconRemove . '"></i>';
		$html .= '</button>' . "\n";

		return $html;
	}
}
