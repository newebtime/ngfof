<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2015 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Factory\MagicSwitch;

use FOF30\Container\Container;
use FOF30\Controller\DataController;
use FOF30\Factory\Exception\ControllerNotFound;

abstract class BaseFactory
{
	/**
	 * @var   Container|null  The container where this factory belongs to
	 */
	protected $container = null;

	/**
	 * Public constructor
	 *
	 * @param   Container  $container  The container we belong to
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}
}