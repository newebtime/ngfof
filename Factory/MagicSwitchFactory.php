<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2015 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Factory;

use FOF30\Container\Container;
use FOF30\Factory\Exception\FormLoadData;
use FOF30\Factory\Exception\FormLoadFile;
use FOF30\Factory\Scaffolding\Builder as ScaffoldingBuilder;
use FOF30\Factory\BasicFactory;
use FOF30\View\View;

/**
 * MVC object factory. This implements the basic functionality, i.e. creating MVC objects only if the classes exist in
 * the same component section (front-end, back-end) you are currently running in. The Dispatcher and Toolbar will be
 * created from default objects if specialised classes are not found in your application.
 */
class MagicSwitchFactory extends BasicFactory
{
	public function __construct(Container $container)
	{
		parent::__construct($container);

		// Look for form files on the other side of the component
		$this->formLookupInOtherSide = true;
	}

	/**
	 * Create a new Controller object
	 *
	 * @param   string  $viewName  The name of the view we're getting a Controller for.
	 * @param   array   $config    Optional MVC configuration values for the Controller object.
	 *
	 * @return  Controller
	 */
	public function controller($viewName, array $config = array())
	{
		$magic = new MagicSwitch\ControllerFactory($this->container);

		return $magic->make($viewName, $config);
	}

	/**
	 * Create a new Model object
	 *
	 * @param   string  $viewName  The name of the view we're getting a Model for.
	 * @param   array   $config    Optional MVC configuration values for the Model object.
	 *
	 * @return  Model
	 */
	public function model($viewName, array $config = array())
	{
		$magic = new MagicSwitch\ModelFactory($this->container);

		return $magic->make($viewName, $config);
	}

	/**
	 * Create a new View object
	 *
	 * @param   string  $viewName  The name of the view we're getting a View object for.
	 * @param   string  $viewType  The type of the View object. By default it's "html".
	 * @param   array   $config    Optional MVC configuration values for the View object.
	 *
	 * @return  View
	 */
	public function view($viewName, $viewType = 'html', array $config = array())
	{
		$magic = new MagicSwitch\ViewFactory($this->container);

		return $magic->make($viewName, $viewType, $config);
	}

	/**
	 * Creates a new Dispatcher
	 *
	 * @param   array  $config  The configuration values for the Dispatcher object
	 *
	 * @return  Dispatcher
	 */
	public function dispatcher(array $config = array())
	{
		$magic = new MagicSwitch\DispatcherFactory($this->container);

		return $magic->make($config);
	}

	/**
	 * Creates a new Toolbar
	 *
	 * @param   array  $config  The configuration values for the Toolbar object
	 *
	 * @return  Toolbar
	 */
    public function toolbar(array $config = array())
	{
		$appConfig = $this->container->appConfig;

		$defaultConfig = array(
			'useConfigurationFile'  => true,
			'renderFrontendButtons' => in_array($appConfig->get("views.*.config.renderFrontendButtons"), array(true, 'true', 'yes', 'on', 1)),
			'renderFrontendSubmenu' => in_array($appConfig->get("views.*.config.renderFrontendSubmenu"), array(true, 'true', 'yes', 'on', 1)),
		);

		$config = array_merge($defaultConfig, $config);

		return parent::toolbar($config);
	}

	/**
	 * Creates a new TransparentAuthentication handler
	 *
	 * @param   array $config The configuration values for the TransparentAuthentication object
	 *
	 * @return  TransparentAuthentication
	 */
    public function transparentAuthentication(array $config = array())
	{
		$magic = new MagicSwitch\TransparentAuthenticationFactory($this->container);

		return $magic->make($config);
	}

	/**
	 * Creates a new Form object
	 *
	 * @param   string  $name      The name of the form.
	 * @param   string  $source    The form source filename without path and .xml extension e.g. "form.default" OR raw XML data
	 * @param   string  $viewName  The name of the view you're getting the form for.
	 * @param   array   $options   Options to the Form object
	 * @param   bool    $replace   Should form fields be replaced if a field already exists with the same group/name?
	 * @param   bool    $xpath     An optional xpath to search for the fields.
	 *
	 * @return  Form|null  The loaded form or null if the form filename doesn't exist
	 *
	 * @throws  \RuntimeException If the form exists but cannot be loaded
	 */
    public function form($name, $source, $viewName = null, array $options = array(), $replace = true, $xpath = false)
	{
		$magic = new MagicSwitch\FormFactory($this->container);

		return $magic->make($name, $source, $viewName, $options, $replace, $xpath);
	}

	/**
	 * Creates a view template finder object for a specific View.
	 *
	 * The default configuration is:
	 * Look for .php, .blade.php files; default layout "default"; no default subtemplate;
	 * look for both pluralised and singular views; fall back to the default layout without subtemplate;
	 * look for templates in both site and admin
	 *
	 * @param   View  $view   The view this view template finder will be attached to
	 * @param   array $config Configuration variables for the object
	 *
	 * @return  mixed
	 */
	function viewFinder(View $view, array $config = array())
	{
		// Initialise the configuration with the default values
		$defaultConfig = array(
			'extensions'    => array('.php', '.blade.php'),
			'defaultLayout' => 'default',
			'defaultTpl'    => '',
			'strictView'    => false,
			'strictTpl'     => false,
			'strictLayout'  => false,
			'sidePrefix'    => 'any'
		);

		$config = array_merge($defaultConfig, $config);

		return parent::viewFinder($view, $config);
	}

	/**
	 * Tries to find the absolute file path for an abstract form filename. For example, it may convert form.default to
	 * /home/myuser/mysite/components/com_foobar/View/tmpl/form.default.xml.
	 *
	 * @param   string  $source    The abstract form filename
	 * @param   string  $viewName  The name of the view we're getting the path for
	 *
	 * @return  string|bool  The fill path to the form XML file or boolean false if it's not found
	 */
	public function getFormFilename($source, $viewName = null)
	{
		return parent::getFormFilename($source, $viewName);
	}
}
