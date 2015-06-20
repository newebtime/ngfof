<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2015 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Form\Field;

use FOF30\Form\Field\Text as FieldBase;

defined('_JEXEC') or die;

/**
 * Form Field class for F0F
 */
class Text extends FieldBase
{
	/**
	 * Get the rendering of this field type for static display, e.g. in a single
	 * item view (typically a "read" task).
	 *
	 * @since 2.0
	 *
	 * @return  string  The field HTML
	 */
	public function getStatic()
	{
		if (isset($this->element['limit']))
		{
			$this->value = substr($this->value, 0, (int) $this->element['limit']);
		}

		return parent::getStatic();
	}

	/**
	 * Get the rendering of this field type for a repeatable (grid) display,
	 * e.g. in a view listing many item (typically a "browse" task)
	 *
	 * @since 2.0
	 *
	 * @return  string  The field HTML
	 */
	public function getRepeatable()
	{
		if (isset($this->element['limit']))
		{
			$this->value = substr($this->value, 0, (int) $this->element['limit']);
		}

		return parent::getRepeatable();
	}
}