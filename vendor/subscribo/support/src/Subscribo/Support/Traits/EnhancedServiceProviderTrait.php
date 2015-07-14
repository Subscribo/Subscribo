<?php namespace Subscribo\Support\Traits;

use ReflectionObject;

/**
 * Class EnhancedServiceProviderTrait
 * @package Subscribo\Support
 */
trait EnhancedServiceProviderTrait
{

    /**
     * @param $serviceProviderName
     * @return \Illuminate\Support\ServiceProvider;
     */
    protected function registerServiceProvider($serviceProviderName)
    {
        $serviceProvider = $this->app->register($serviceProviderName);
        if (true === $serviceProvider) {
            return $this->app->getProvider($serviceProviderName);
        }
        return $serviceProvider;
    }

    /**
     * Overload if child class is in non-standard directory
     * @return string
     */
    protected function getPackagePath()
    {
        $reflection = new ReflectionObject($this);
        $fileName = $reflection->getFileName();
        $packagePath = dirname(dirname(dirname(dirname(dirname(dirname($fileName))))));
        return $packagePath;
    }

    /**
     * @return string
     */
    protected function getPackageNamespace()
    {
        $classNameWithNamespace = get_class($this);
        $namespaceParts = explode('\\', $classNameWithNamespace);
        array_pop($namespaceParts); //We remove the class name itself
        $resultParts = array_slice($namespaceParts, 0, 2); //and take two parts of namespace
        $result = implode('\\', $resultParts);
        return $result;
    }

    /**
     * @param string|array $resources
     * @param string|bool $namespace
     */
    protected function registerTranslationResources($resources, $namespace = true)
    {
        $packagePath = $this->getPackagePath();
        if (true === $namespace) {
            $namespace = basename($packagePath);
        }
        $basePath = $this->app->make('path.base');
        $packageTranslationsPath = $this->getPackagePath().'/resources/lang/';
        $applicationTranslationPath = $basePath.'/subscribo/resources/lang/'.$namespace.'/';
        $this->publishes([$packageTranslationsPath => $applicationTranslationPath], 'translation');

        $manager = $this->app->make('\\Subscribo\\Localization\\Interfaces\\LocalizationResourcesManagerInterface');
        $manager->registerNamespace($namespace, [$packageTranslationsPath, $applicationTranslationPath], $resources);
    }

    /**
     * @param array $havingViewComposer
     * @param string|bool $subdirectory
     * @param string $viewNamespace
     */
    protected function registerViews($havingViewComposer = array(), $subdirectory = true, $viewNamespace = 'subscribo')
    {
        $packagePath = $this->getPackagePath();
        if (true === $subdirectory) {
            $subdirectory = basename($packagePath);
        }
        $pathSuffix = $subdirectory ? ($subdirectory.'/') : '';
        $packageTemplateBasePath = $this->getPackagePath().'/resources/views';
        $packageTemplatePath = $packageTemplateBasePath.'/'.$pathSuffix;
        $basePath = $this->app->make('path.base');
        $applicationTemplatePath = $basePath.'/resources/views/vendor/'.$viewNamespace.'/'.$pathSuffix;
        $this->publishes([$packageTemplatePath => $applicationTemplatePath], 'view');
        $this->loadViewsFrom($packageTemplateBasePath, $viewNamespace);
        $this->registerViewComposers($havingViewComposer, $subdirectory, $viewNamespace);
    }

    private function registerViewComposers($composerNames, $subdirectory, $viewNamespace)
    {
        if (empty($composerNames)) {
            return;
        }
        $view = $this->app->make('view');
        $baseNamespace = $this->getPackageNamespace() ?: 'App';
        $templateIdentifierBase = $viewNamespace.'::';
        if ($subdirectory) {
            $templateIdentifierBase .= $subdirectory.'.';
        }
        $composerNames = (array) $composerNames;
        foreach ($composerNames as $key => $composerNameBase) {
            $template = $templateIdentifierBase.(is_numeric($key) ? strtolower($composerNameBase) : $key);
            $composerName = $baseNamespace.'\\ViewComposers\\'.$composerNameBase.'Composer';
            $view->composer($template, $composerName);
        }
    }

    /**
     * @param null|\Illuminate\Routing\Router $router
     * @return \Illuminate\Routing\Router
     */
    public function getRouter($router = null)
    {
        if ($router) {
            return $router;
        }
        $router = $this->app->make('router');
        return $router;
    }
}
