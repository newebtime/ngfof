<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2015 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Factory\MagicSwitch;

use FOF30\Dispatcher\Dispatcher;
use FOF30\Model\DataModel;
use FOF30\View\DataView\DataViewInterface;

/**
 * Creates a TransparentAuthentication object instance based on the information provided by the fof.xml configuration file
 */
class TransparentAuthenticationFactory extends BaseFactory
{
	/**
	 * Create a new object instance
	 *
	 * @param   array   $config    The config parameters which override the fof.xml information
	 *
	 * @return  Dispatcher  A new Dispatcher object
	 */
	public function make(array $config = array())
	{
		$appConfig = $this->container->appConfig;
		$defaultConfig = $appConfig->get('authentication.*');
		$config = array_merge($defaultConfig, $config);

		$className = $this->container->getNamespacePrefix() . 'TransparentAuthentication\\TransparentAuthentication';

		if (!class_exists($className, true))
		{
			$className = $this->container->getNamespacePrefix('inverse') . 'TransparentAuthentication\\TransparentAuthentication';
		}

		if (!class_exists($className, true))
		{
			$className = $this->container->getNamespacePrefix() . 'TransparentAuthentication\\DefaultTransparentAuthentication';
		}

		if (!class_exists($className, true))
		{
			$className = $this->container->getNamespacePrefix('inverse') . 'TransparentAuthentication\\DefaultTransparentAuthentication';
		}

		if (!class_exists($className, true))
		{
			$className = '\\FOF30\\TransparentAuthentication\\TransparentAuthentication';
		}

		$dispatcher = new $className($this->container, $config);

		return $dispatcher;
	}
}