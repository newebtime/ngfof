<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2015 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Model\Behaviour;

use FOF30\Event\Observer;

class Autoreset extends Observer
{
	/**
	 * This event runs after we have built the query used to fetch a record
	 * list in a model. It is used to apply automatic query filters.
	 *
	 * @param   DataModel      &$model The model which calls this event
	 * @param   JDatabaseQuery &$query The query we are manipulating
	 *
	 * @return  void
	 */
	public function onBeforeBuildQuery(&$model, &$query)
	{
		if (!$model->input->getInt('limit'))
		{
			$limit = $model->getState('limit');

			\JFactory::getApplication()->setUserState(substr($model->getHash(), 0, -1), null);

			$model->setState('limit', $limit);

			\JFactory::getApplication()->setUserState($model->getHash() . 'limit', $limit);
		}
	}
}
