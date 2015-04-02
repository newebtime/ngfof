<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2015 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Model\Behaviour;

use FOF30\Event\Observer;

class Autoslug extends Observer
{
	/**
	 * The event which runs after storing (saving) data to the database
	 *
	 * @param   \FOF30\Model\DataModel  $model  The model which calls this event
	 *
	 * @return  void
	 */
	public function onAfterSave(&$model)
	{
		if (!($model->hasField('title') && $model->hasField('slug')))
		{
			return true;
		}

		$slugField  = $model->getFieldAlias('slug');
		$titleField = $model->getFieldAlias('title');

		$model->$slugField = \JApplicationHelper::stringURLSafe($model->getId() . '-' . $model->$titleField);

		$updateObject = (object) array($model->getIdFieldName() => $model->getId(), $slugField => $model->$slugField);

		$model->getContainer()->db->updateObject($model->getTableName(), $updateObject, $model->getIdFieldName());
	}
}