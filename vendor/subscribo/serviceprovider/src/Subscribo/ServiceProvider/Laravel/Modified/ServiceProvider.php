<?php namespace Subscribo\ServiceProvider\Laravel\Modified;

/**
 * Abstract Class ServiceProvider
 *
 * This class contain modified method from ServiceProvider taken from Laravel Framework 4.2 (www.laravel.com)
 *
 * @license MIT
 * @package Subscribo\ServiceProvider
 */
abstract class ServiceProvider extends \Subscribo\ServiceProvider\Laravel\ServiceProvider {


	/**
	 * Register the package's component namespaces.
	 *
	 * @param  string  $package
	 * @param  string  $namespace
	 * @param  string  $path
	 * @return void
	 */
	public function package($package, $namespace = null, $path = null)
	{
		$namespace = $this->getPackageNamespace($package, $namespace);

		$path = $path ?: $this->guessPackagePath();

		//ChangeLog: Configuration setup removed


		// Next we will check for any "language" components. If language files exist
		// we will register them with this given package's namespace so that they
		// may be accessed using the translation facilities of the application.
		$lang = $path.'/lang';

		if ($this->app['files']->isDirectory($lang))
		{
			$this->app['translator']->addNamespace($namespace, $lang);
		}

		// Next, we will see if the application view folder contains a folder for the
		// package and namespace. If it does, we'll give that folder precedence on
		// the loader list for the views so the package views can be overridden.
		$appView = $this->getAppViewPath($package);

		if ($this->app['files']->isDirectory($appView))
		{
			$this->app['view']->addNamespace($namespace, $appView);
		}

		// Finally we will register the view namespace so that we can access each of
		// the views available in this package. We use a standard convention when
		// registering the paths to every package's views and other components.
		$view = $path.'/views';

		if ($this->app['files']->isDirectory($view))
		{
			$this->app['view']->addNamespace($namespace, $view);
		}
	}

}
