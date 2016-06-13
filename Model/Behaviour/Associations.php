<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2015 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Model\Behaviour;

use FOF30\Event\Observer;

class Associations extends Observer
{
	/**
	 * This event runs after saving a record in a model
	 *
	 * @param   \FOF30\Model\DataModel  &$model  The model which calls this event
	 *
	 * @return  bool
	 */
	public function onAfterSave(&$model)
	{
		$input = $model->input;

		if (!\JLanguageAssociations::isEnabled()
			|| !$associations = $input->get('associations', null))
		{
			return false;
		}

		$pk = $model->getKeyName();

		$view      = \JRequest::getCmd('view', 'items');
		$context   = $model->getContentType();

		foreach ($associations as $tag => $id)
		{
			if (empty($id))
			{
				unset($associations[$tag]);
			}
		}

		// Detecting all item menus
		$all_language = $model->language == '*';

		if ($all_language)
		{
			return true;
		}

		$associations[$model->language] = $model->$pk;

		// Deleting old association for these items
		$db = $model->getDbo();
		$query = $db->getQuery(true)
			->delete('#__associations')
			->where('context=' . $db->quote($context))
			->where('id IN (' . implode(',', $associations) . ')');
		$db->setQuery($query);
		$db->execute();

		if ($error = $db->getErrorMsg())
		{
			return false;
		}

		if (!$all_language && count($associations))
		{
			// Adding new association for these items
			$key = md5(json_encode($associations));
			$query->clear()
				->insert('#__associations');

			foreach ($associations as $id)
			{
				$query->values($id . ',' . $db->quote($context) . ',' . $db->quote($key));
			}

			$db->setQuery($query);
			$db->execute();

			if ($error = $db->getErrorMsg())
			{
				return false;
			}
		}
	}
}