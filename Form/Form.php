<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2016 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Form;

use FOF30\Form\Form as BaseForm;

defined('_JEXEC') or die;

/**
 * @inheritdoc
 */
class Form extends BaseForm
{
	/**
	 * Added inverseNamespace
	 * From FOF 3.0.7
	 *
	 * @inheritdoc
	 */
	public function loadClass($entity, $type)
	{
		// Get the prefixes for namespaced classes (FOF3 way)
		$namespacedPrefixes = array(
			$this->container->getNamespacePrefix(),
			$this->container->getNamespacePrefix('inverse'),
			'Ngfof\\',
			'FOF30\\'
		);

		// Get the prefixes for non-namespaced classes (FOF2 and Joomla! way)
		$plainPrefixes = array('J');

		// If the type is given as prefix.type add the custom type into the two prefix arrays
		if (strpos($type, '.'))
		{
			list($prefix, $type) = explode('.', $type);

			array_unshift($plainPrefixes, $prefix);
			array_unshift($namespacedPrefixes, $prefix);
		}

		// First try to find the namespaced class
		foreach ($namespacedPrefixes as $prefix)
		{
			$class = rtrim($prefix, '\\') . '\\Form\\' . ucfirst($entity) . '\\' . ucfirst($type);

			if (class_exists($class, true))
			{
				return $class;
			}
		}

		// TODO The rest of the code is legacy and will be removed in a future version

		// Then try to find the non-namespaced class
		$classes = array();

		foreach ($plainPrefixes as $prefix)
		{
			$class = \JString::ucfirst($prefix, '_') . 'Form' . \JString::ucfirst($entity, '_') . \JString::ucfirst($type, '_');

			if (class_exists($class, true))
			{
				return $class;
			}

			$classes[] = $class;
		}

		// Get the field search path array.
		$reflector = new \ReflectionClass('\\JFormHelper');
		$addPathMethod = $reflector->getMethod('addPath');
		$addPathMethod->setAccessible(true);
		$paths = $addPathMethod->invoke(null, $entity);

		// If the type is complex, add the base type to the paths.
		if ($pos = strpos($type, '_'))
		{
			// Add the complex type prefix to the paths.
			for ($i = 0, $n = count($paths); $i < $n; $i++)
			{
				// Derive the new path.
				$path = $paths[$i] . '/' . strtolower(substr($type, 0, $pos));

				// If the path does not exist, add it.
				if (!in_array($path, $paths))
				{
					$paths[] = $path;
				}
			}

			// Break off the end of the complex type.
			$type = substr($type, $pos + 1);
		}

		// Try to find the class file.
		$type = strtolower($type) . '.php';

		foreach ($paths as $path)
		{
			if ($file = \JPath::find($path, $type))
			{
				require_once $file;

				foreach ($classes as $class)
				{
					if (class_exists($class, false))
					{
						return $class;
					}
				}
			}
		}

		return false;
	}
}
