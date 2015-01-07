<?php namespace Subscribo\Config;

use Subscribo\Environment\EnvironmentInterface;
use Subscribo\Support\Arr;

use Subscribo\Config\Loader\PhpFileLoader;
use Subscribo\Config\Loader\YamlFileLoader;
use Subscribo\Config\Loader\JsonFileLoader;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Definition\Processor;

class Config {

    protected $supportedExtensions = array('php', 'json', 'yml', 'yaml');

    /**
     * @var null|\Subscribo\Environment\EnvironmentInterface
     */
    protected $environmentInstance = null;

    /**
     * @var array
     */
    protected $mainConfiguration = array();

    /**
     * @var array
     */
    protected $packagesConfiguration = array();

    /**
     * @var string
     */
    protected $projectBasePath;

    /**
     * @var string
     */
    protected $mainConfigDirectory;

    /**
     * @var string
     */
    protected $packageConfigDirectory;

    /**
     * @var string
     */
    protected $environmentSubdirectoryName;

    /**
     * @var \Symfony\Component\Config\Loader\LoaderResolver
     */
    protected $loaderResolver;

    /**
     * @param \Subscribo\Environment\EnvironmentInterface $environment
     * @param string $projectBasePath
     * @param string|null $mainConfigDirectory
     * @param string|null $packageConfigDirectory
     * @param string $environmentSubdirectoryName
     */
    public function __construct(EnvironmentInterface $environment, $projectBasePath, $mainConfigDirectory = null, $packageConfigDirectory = null, $environmentSubdirectoryName = 'env')
    {
        $projectBasePath = rtrim($projectBasePath, '/').'/';
        if (is_null($mainConfigDirectory)) {
            $mainConfigDirectory = $projectBasePath.'subscribo/config/';
        }
        $mainConfigDirectory = rtrim($mainConfigDirectory, '/').'/';
        if (is_null($packageConfigDirectory)) {
            $packageConfigDirectory = $mainConfigDirectory.'packages/';
        }
        $packageConfigDirectory = rtrim($packageConfigDirectory, '/').'/';
        $this->environmentInstance = $environment;
        $this->projectBasePath = $projectBasePath;
        $this->mainConfigDirectory = $mainConfigDirectory;
        $this->packageConfigDirectory = $packageConfigDirectory;
        $this->environmentSubdirectoryName = $environmentSubdirectoryName;
    }

    /**
     * Return value stored under provided key
     *
     * @param string $key Array key with dot notation
     * @param mixed $default What to return, if key is not found
     * @param string|null $package If provided, return value for given package
     * @return mixed|null
     */
    public function get($key, $default = null, $package = null)
    {
        if ($package) {
            return $this->getForPackage($package, $key, $default);
        }
        return Arr::get($this->mainConfiguration, $key, $default);

    }

    /**
     * Return value stored under provided key for given package
     *
     * @param string $package Package  name
     * @param string $key Array key with dot notation
     * @param mixed $default What to return, if key is not found
     * @return mixed|null
     */
    public function getForPackage($package, $key, $default = null)
    {
        if (empty($this->packagesConfiguration[$package])) {
            return $default;
        }
        return Arr::get($this->packagesConfiguration[$package], $key, $default);
    }

    /**
     * Store given value under given key
     *
     * @param string $key Array key with dot notation
     * @param mixed $value Value to store
     * @param string|null $package If provided, return value for given package
     * @return $this
     */
    public function set($key, $value, $package = null)
    {
        if ( ! is_null($package)) {
            $this->setForPackage($package, $key, $value);
            return $this;
        }
        $mainConfiguration = $this->mainConfiguration;
        Arr::set($mainConfiguration, $key, $value);
        return $this;
    }

    /**
     * Store given value under given key for given package
     *
     * @param string $package Package  name
     * @param string $key Array key with dot notation
     * @param mixed $value Value to store
     * @return $this
     */
    public function setForPackage($package, $key, $value)
    {
        $packageConfiguration = empty($this->packagesConfiguration[$package]) ? array() : $this->packagesConfiguration[$package];
        Arr::set($packageConfiguration, $key, $value);
        $this->packagesConfiguration[$package] = $packageConfiguration;
        return $this;
    }


    /**
     * Load configuration file(s)
     *
     * @param string|array $filePath File path or array of file paths
     * @param string|null|bool $group Whether parsed file content should be under some group.
     *        False - no group (root node)
     *        True - Group same as file base name (without extension)
     *        String - under provided string
     *        Null (default) - based on file base name - if it is 'config' then root, otherwise file name
     * @param string|bool $environment
     * @param string|null|bool $baseDirectory Directory to be prepended to filePath
     *        False - Nothing (filePath is absolute)
     *        True - Main Configs directory
     *        Null - Application root directory
     *        String - string to be prepended
     * @param null $configuration
     * @param string|null $package If provided, loads configuration for a package
     *
     * @return int How many files have been loaded
     */
    public function loadFile($filePath, $group = null, $environment = true, $baseDirectory = true, $configuration = null, $package = null)
    {
        if ( ! is_null($package)) {
            $this->loadFileForPackage($package, $filePath, $group, $environment, $baseDirectory, $configuration);
            return $this;
        }
        if (true === $baseDirectory) {
            $baseDir = $this->mainConfigDirectory;
        } else {
            $baseDir = $baseDirectory;
        }
        $result = $this->processFiles($filePath, $this->mainConfiguration, $group, $environment, $baseDir, $configuration);
        return $result;

    }

    /**
     * Load configuration file(s) for a package
     *
     * @param string $package Package name
     * @param string|array $filePath File path or array of file paths
     * @param string|null|bool $group Whether parsed file content should be under some group.
     *        False - no group (root node)
     *        True - Group same as file base name (without extension)
     *        String - under provided string
     *        Null (default) - based on file base name - if it is 'config' then root, otherwise file name
     * @param string|bool $environment
     * @param string|null|bool $baseDirectory Directory to be prepended to filePath
     *        False - Nothing (filePath is absolute)
     *        True - Main Package Config directory
     *        Null - Application root directory
     *        String - string to be prepended
     * @param null $configuration
     *
     * @return int How many files have been loaded
     */
    public function loadFileForPackage($package, $filePath, $group = null, $environment = true, $baseDirectory = true, $configuration = null)
    {
        if (true === $baseDirectory) {
            $baseDir = $this->packageConfigDirectory.$package.'/';
        } else {
            $baseDir = $baseDirectory;
        }
        if (empty($this->packagesConfiguration[$package])) {
            $this->packagesConfiguration[$package] = array();
        }
        $result = $this->processFiles($filePath, $this->packagesConfiguration[$package], $group, $environment, $baseDir, $configuration);
        return $result;
    }

    public function parseFile($filePath)
    {
        $delegatingLoader = new DelegatingLoader($this->obtainLoaderResolver());
        $result = $delegatingLoader->load($filePath);
        return $result;
    }

    /**
     * @return LoaderResolver
     */
    protected function obtainLoaderResolver()
    {
        if ($this->loaderResolver) {
            return $this->loaderResolver;
        }
        $fileLocator = new FileLocator();
        $loaders = array(
            new PhpFileLoader($fileLocator),
            new JsonFileLoader($fileLocator),
            new YamlFileLoader($fileLocator),
        );
        $this->loaderResolver = new LoaderResolver($loaders);
        return $this->loaderResolver;
    }


    public function findAndParseFile($filePath)
    {
        $realFile = $this->findFile($filePath);
        if (empty($realFile)) {
            return null;
        }
        return $this->parseFile($realFile);
    }

    /**
     * Tries to find existing file with supported extension
     *
     * @param $filePath
     * @return null|string
     */
    public function findFile($filePath)
    {
        if (file_exists($filePath)) {
            return $filePath;
        }
        foreach ($this->supportedExtensions as $extension)
        {
            $extendedFilePath = $filePath.'.'.$extension;
            if (file_exists($extendedFilePath)) {
                return $extendedFilePath;
            }
            $extendedFilePath = $filePath.'.'.strtoupper($extension);
            if (file_exists($extendedFilePath)) {
                return $extendedFilePath;
            }
            $extendedFilePath = $filePath.'.'.ucfirst($extension);
            if (file_exists($extendedFilePath)) {
                return $extendedFilePath;
            }
        }
        return null;
    }

    private function processFiles($filePath, array &$storage, $group = null, $environment = true, $baseDir = null, $configuration = null)
    {
        $filesCount = 0;
        $toProcess = empty($storage) ? array() : array($storage);
        $filePaths = is_array($filePath) ? $filePath : array($filePath);
        foreach ($filePaths as $pathToFile) {
            $processedFile = $this->processFile($pathToFile, $group, $baseDir);
            if ($processedFile) {
                $filesCount++;
                $toProcess[] = $processedFile;
            }
        }
        if ($environment) {
            foreach ($filePaths as $pathToFile) {
                $environmentFilePath = $this->assembleEnvironmentFilePath($pathToFile, $environment);
                $processedEnvironmentFile = $this->processFile($environmentFilePath, $group, $baseDir);
                if ($processedEnvironmentFile) {
                    $filesCount++;
                    $toProcess[] = $processedEnvironmentFile;
                }
            }
        }
        if ($configuration) {
            $processor = new Processor();
            $storage = $processor->process($configuration, $toProcess);
        } else {
            $storage = $this->mergeConfigurations($toProcess);
        }
        return $filesCount;
    }

    protected function mergeConfigurations(array $configurations)
    {
        $result = array();
        foreach ($configurations as $configuration)
        {
            $result = Arr::merge_natural($result, $configuration);
        }
        return $result;
    }

    /**
     * Processing the file, optionally putting result under group
     *
     * @param string $filePath
     * @param string|null|bool $group Whether parsed file content should be under some group.
     *        False - no group (root node)
     *        True - Group same as file base name (without extension)
     *        String - under provided string
     *        Null (default) - based on file base name - if it is 'config' then root, otherwise file name
     * @param string|null $baseDir If provided, then prepended to $filePath, is null, then Application path is prepended
     * @return array|null
     */
    private function processFile($filePath, $group = null, $baseDir = null)
    {
        if (is_null($baseDir)) {
            $baseDir = $this->projectBasePath;
        }
        if ( ! is_string($baseDir)) {
            $baseDir = '';
        }
        $content = $this->findAndParseFile($baseDir.$filePath);
        if (empty($content)) {
            return null;
        }
        if (is_null($group)) {
            $fileNameBase = $this->extractFileNameBase($filePath);
            if ('config' === strtolower($fileNameBase)) {
                $group = false;
            } else {
                $group = $fileNameBase;
            }
        }
        if ($group) {
            $result = array($group => $content);
        } else {
            $result = $content;
        }
        return $result;
    }


    /**
     * Return file name base, without directories and without extension
     *
     * @param string $filePath
     * @return string
     */
    private function extractFileNameBase($filePath)
    {
        $directories = explode('/', $filePath);
        $fileName = array_pop($directories);
        $fileNameParts = explode('.', $fileName);
        if (count($fileNameParts) > 1) {
            $extension = array_pop($fileNameParts);
        }
        $result = implode('.', $fileNameParts);
        return $result;
    }

    /**
     * Assemble path to configuration file with environment taken into account
     *
     * @param string $filePath Path to configuration file for all environments
     * @param string|bool $environment Environment name, true for predefined for Config object (usually the current one)
     * @return string
     */
    private function assembleEnvironmentFilePath($filePath, $environment = true)
    {
        if (true === $environment) {
            $environment = $this->environmentInstance->getEnvironment();
        }
        $directories = explode('/', $filePath);
        $fileName = array_pop($directories);
        $fileNameWithoutExtension = $this->extractFileNameBase($filePath);
        array_push($directories, $this->environmentSubdirectoryName, $environment, $fileNameWithoutExtension);
        $result = implode('/', $directories);
        return $result;
    }
}
