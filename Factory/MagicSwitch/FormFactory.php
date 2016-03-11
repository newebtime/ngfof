<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2015 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Factory\MagicSwitch;

/**
 * Creates a Dispatcher object instance based on the information provided by the fof.xml configuration file
 */
class FormFactory extends BaseFactory
{
	/**
	 * Create a new object instance
	 *
	 * @param   array   $config    The config parameters which override the fof.xml information
	 *
	 * @return  Dispatcher  A new Dispatcher object
	 */
	public function make($name, $source, $viewName = null, array $options = array(), $replace = true, $xpath = false)
	{
		$className = $this->container->getNamespacePrefix() . 'Form\\Form';

		if (!class_exists($className, true))
		{
			$className = $this->container->getNamespacePrefix('inverse') . 'Form\\Form';
		}

		if (!class_exists($className, true))
		{
			$className = '\\Ngfof\\Form\\Form';
		}

		// Get a new form instance
		$form = new $className($this->container, $name, $options);

		// If $source looks like raw XML data, parse it directly
		if (strpos($source, '<form ') !== false)
		{
			if ($form->load($source, $replace, $xpath) === false)
			{
				throw new FormLoadData;
			}

			return $form;
		}

		$formFileName = $this->container->factory->getFormFilename($source, $viewName);

		if (empty($formFileName))
		{
			if ($this->scaffolding)
			{
				$scaffolding = new ScaffoldingBuilder($this->container);
				$xml = $scaffolding->make($source, $viewName);

				if (!is_null($xml))
				{
					return $this->form($name, $xml, $viewName, $options, $replace, $xpath);
				}
			}

			return null;
		}

		if ($form->loadFile($formFileName, $replace, $xpath) === false)
		{
			throw new FormLoadFile($source);
		}

		return $form;
	}
}