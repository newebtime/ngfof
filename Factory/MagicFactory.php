<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2016 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Factory;

use FOF30\Factory\MagicFactory as BaseFactory;

defined('_JEXEC') or die;

/**
 * Magic MVC object factory. This factory will "magically" create MVC objects even if the respective classes do not
 * exist, based on information in your fof.xml file.
 *
 * Note: This factory class will ONLY look for MVC objects in the same component section (front-end, back-end) you are
 * currently running in. If they are not found a new one will be created magically.
 */
class MagicFactory extends BaseFactory
{
	/**
	 * @inheritdoc
	 */
	public function model($viewName, array $config = array())
	{
		$magic = new Magic\ModelFactory($this->container);

		return $magic->make($viewName, $config);
	}
}
