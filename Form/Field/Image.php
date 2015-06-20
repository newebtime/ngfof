<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2015 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Form\Field;

use FOF30\Form\Field\Image as FieldBase;
use FOF30\Model\DataModel;

defined('_JEXEC') or die;

/**
 * Form Field class for F0F
 */
class Image extends FieldBase
{
	use Traits\ParseFieldTags;

	/**
	 * Method to get the field input markup.
	 *
	 * @param   array   $fieldOptions  Options to be passed into the field
	 *
	 * @return  string  The field HTML
	 */
	public function getFieldContents(array $fieldOptions = array())
	{
		if (!isset($this->element['url'])
			|| (!$this->item instanceof DataModel)) {
			return parent::getFieldContents($fieldOptions);
		}

		$link_url = $this->parseFieldTags((string) $this->element['url']);
		$html = '<a href="' . $link_url . '">';
		$html .= parent::getFieldContents($fieldOptions);
		$html .= '</a>';

		return $html;
	}
}
