<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2015 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Form\Field;

use FOF30\Form\Field\Radio as FieldBase;

/**
 * Form Field class for F0F
 */
class Radio extends FieldBase
{
	protected function getInput()
	{
		$html = array();

		// Initialize some field attributes.
		$class     = !empty($option->class) ? ' class="' . $option->class . '"' : '';
		$required  = $this->required ? ' required aria-required="true"' : '';
		$autofocus = $this->autofocus ? ' autofocus' : '';
		$disabled  = $this->disabled ? ' disabled' : '';
		$readonly  = $this->readonly;

		// Start the radio field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . $required . $autofocus . $disabled . ' >';

		// Get the field options.
		$options = $this->getOptions();

		// Build the radio field output.
		foreach ($options as $i => $option)
		{
			// Initialize some option attributes.
			$checked    = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
			$class      = !empty($option->class) ? ' class="radio ' . $option->class . '"' : ' class="radio"';
			$labelclass = !empty($option->labelclass) ? ' class="' . $option->labelclass . '"' : '';
			$disabled   = !empty($option->disable) || ($readonly && !$checked);
			$disabled   = $disabled ? ' disabled' : '';

			// Initialize some JavaScript option attributes.
			$onclick  = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
			$onchange = !empty($option->onchange) ? ' onchange="' . $option->onchange . '"' : '';

			$html[] = '<div' . $class . '>';
			$html[] = '	<label for="' . $this->id . $i . '"' . $labelclass . ' >';
			$html[] = '		<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '" value="'
				. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $required . $onclick
				. $onchange . $disabled . ' />';
			$html[] = \JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . '</label>';
			$html[] = '</div>';

			$required = '';
		}

		// End the radio field output.
		$html[] = '</fieldset>';

		return implode($html);
	}
}
