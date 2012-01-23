<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PharResolver
 *
 * @author alex
 */
class Ztal_Tal_PharResolver implements PHPTAL_SourceResolver
{

    private $_repositories;
    
    public function __construct($repositories)
    {
        $this->_repositories = $repositories;
    }

    public function resolve($path)
    {
        foreach ($this->_repositories as $repository) {
            $file = $repository . DIRECTORY_SEPARATOR . $path;
            if (strpos($file, 'phar://') === 0 && file_exists($file)) {
                return new PHPTAL_StringSource(file_get_contents($file), $file);
            }
        }

        return null;
    }
    /*
    function resolve($path)
    {
        if (strpos($path, 'phar://') === 0) {
            $source = file_get_contents($path);
            return new PHPTAL_StringSource($source);
        }
    }*/
}