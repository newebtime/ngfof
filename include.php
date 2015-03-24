<?php
/**
 * @package     FOF
 * @copyright   2010-2015 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

if (!defined('FOF30_INCLUDED'))
{
	include_once JPATH_LIBRARIES . '/fof30/include.php';
}

FOF30\Autoloader\Autoloader::getInstance()->addMap('Ngfof\\', array(realpath(__DIR__ . '/')));