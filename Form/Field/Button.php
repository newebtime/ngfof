<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2015 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Form\Field;

use FOF30\Form\Field\Button as FieldBase;

/**
 * Form Field class for F0F
 */
class Button extends FieldBase
{
	/**
	 * Replace string with tags that reference fields
	 *
	 * @param   string  $text  Text to process
	 *
	 * @return  string         Text with tags replace
	 */
	protected function parseFieldTags($text)
	{
		$ret = parent::parseFieldTags($text);
		return str_replace('[TOKEN]', \JSession::getFormToken(), $ret);
	}
}
