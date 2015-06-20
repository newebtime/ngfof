<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2015 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Form\Field\Traits;

defined('_JEXEC') or die;

/**
 * Form Field class for F0F
 */
Trait ParseFieldTags
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
		$ret = $text;

		// Replace [ITEM:ID] in the URL with the item's key value (usually:
		// the auto-incrementing numeric ID)
		if (is_null($this->item))
		{
			$this->item = $this->form->getModel();
		}

		$keyfield = $this->item->getKeyName();
		$replace  = $this->item->$keyfield;
		$ret = str_replace('[ITEM:ID]', $replace, $ret);

		// Replace the [ITEMID] in the URL with the current Itemid parameter
		$ret = str_replace('[ITEMID]', $this->form->getContainer()->input->getInt('Itemid', 0), $ret);

		// Replace the [TOKEN] in the URL with the Joomla! form token
		$ret = str_replace('[TOKEN]', \JFactory::getSession()->getFormToken(), $ret);

		// Replace other field variables in the URL
		$fields = $this->item->getTableFields();

		foreach ($fields as $fielddata)
		{
			$fieldname = $fielddata->Field;

			if (empty($fieldname))
			{
				$fieldname = $fielddata->column_name;
			}

			$search    = '[ITEM:' . strtoupper($fieldname) . ']';
			$replace   = $this->item->$fieldname;

			if (!is_string($replace))
			{
				continue;
			}

			$ret  = str_replace($search, $replace, $ret);
		}

		return $ret;
	}
}
