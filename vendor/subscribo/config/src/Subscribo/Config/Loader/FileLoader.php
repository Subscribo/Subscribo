<?php namespace Subscribo\Config\Loader;

abstract class FileLoader extends \Symfony\Component\Config\Loader\FileLoader
{
    protected static function compareExtensions($filePath, $allowedExtensions)
    {
        $allowedExtensions = is_array($allowedExtensions) ? $allowedExtensions : array($allowedExtensions);
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        foreach ($allowedExtensions as $allowedExtension) {
            if ($fileExtension === $allowedExtension) {
                return true;
            }
            if ( ( ! empty($allowedExtension))
              and (strtolower($allowedExtension) === strtolower($fileExtension))) {
                return true;
            }
        }
        return false;
    }
}
