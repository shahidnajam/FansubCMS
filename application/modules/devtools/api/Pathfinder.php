<?php
class Devtools_Api_Pathfinder
{
    private static $_instance;
    protected $_autoloaderPaths = array();
    protected $_tempPath = null;
    /**
     * Reads all ResourceTypes from all defined Autoloaders
     * If namespace path is ambigous, the last occurence of
     * a not multiply defined path will be mapped and a warning
     * is generated.
     *
     * @return void
     */
    private function __construct()
    {
        $autoloaders = Zend_Loader_Autoloader::getInstance()->getAutoloaders();
        $paths = array();
        foreach ($autoloaders as $autoloader) {
            if(!is_object($autoloader)) {
                continue; // autoloaders via strings
            }
            $resourceTypes = $autoloader->getResourceTypes();
            foreach ($resourceTypes as $name => $data) {
                if (!isset($paths[$data['namespace']])) {
                    $paths[$data['namespace']] = array();
                }
                if (in_array($data['path'], $paths[$data['namespace']])) {
                    continue;
                }
                $paths[$data['namespace']][] = $data['path'];
            }
        }
        foreach ($paths as $namespace => $npaths) {
            if (count($npaths) > 1) {
                trigger_error("Autoloader path for namespace \"$namespace\" is ambigious.", E_USER_WARNING);
            }
            $this->_autoloaderPaths[$namespace] = array_pop($npaths);
        }
    }
    /**
     *
     * @return Devtools_Api_Pathfinder
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    protected function _normalizeModuleName($moduleName)
    {
        return ucfirst(strtolower($moduleName));
    }
    protected function _getAutoloaderPath($namespace)
    {
        if (isset($this->_autoloaderPaths[$namespace])) {
            return $this->_autoloaderPaths[$namespace];
        } else {
            throw new Exception("Autoloader Path for namespace '$namespace' not found.");
        }
    }
    public function ensurePathExists($path)
    {
        if (empty($path)) {
            throw new Exception("Path is empty string.");
        }
        if (file_exists($path) && (!is_dir($path))) {
            throw new Exception("Path '$path' already exists but is not a directory.");
        }
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        return realpath($path);
    }
    protected function _removePathRecursive($path)
    {
        if (empty($path)) {
            throw new Exception("Unable to delete '$path'.");
        }
        $path = realpath($path);
        $dh = opendir($path);
        if (!$dh) {
            throw new Exception("Unable to open directory '$path'.");
        }
        while (false !== ($file = readdir($dh))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $fullPath = $path . DIRECTORY_SEPARATOR . $file;
            if (is_dir($fullPath)) {
                $this->_removePathRecursive($fullPath);
            } elseif (is_file($fullPath)) {
                unlink($fullPath);
            } else {
                throw new Exception("Unable to determine type of '$fullPath'.");
            }
        }
        closedir($dh);
        rmdir($path);
    }
    public function __destruct()
    {
        if (!is_null($this->_tempPath)) {
            $this->_removeTempPath();
        }
    }
    protected function _removeTempPath()
    {
        if (is_null($this->_tempPath)) {
            throw new Exception("Unable to remove temp path. No temp path has been created.");
        }
        // $this->_removePathRecursive($this->_tempPath);
    }
    public function getModelPath($moduleName)
    {
        $moduleName = $this->_normalizeModuleName($moduleName);
        $namespace = $moduleName . '_Model';
        return $this->_getAutoloaderPath($namespace);
    }
   
    public function getSchemaPath($moduleName)
    {
        return $this->getModelPath($moduleName) . DIRECTORY_SEPARATOR . 'schema';
    }
    public function getBaseModelPath($moduleName)
    {
        return $this->getModelPath($moduleName) . DIRECTORY_SEPARATOR . 'Base';
    }
    public function getTableClassPath($moduleName)
    {
        return $this->getModelPath($moduleName);
    }
    public function getTempPath()
    {
        if (is_null($this->_tempPath)) {
            do {
                $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 't' . md5('gfhjdf7657er645' . microtime());
            } while (is_dir($path));
            $this->_tempPath = $this->ensurePathExists($path);
        }
        return $this->_tempPath;
    }
    public function resetTempPath()
    {
        $this->_removeTempPath();
        $this->_tempPath = null;
    }
    public function getMigrationPath()
    {
        $migrationPath = realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . 'migrations';

        if (!is_dir($migrationPath)) {
            mkdir($migrationPath, 0777, true);
        }
        if (!is_dir($migrationPath)) {
            throw new Exception("Unable to open/create migrations directory '$migrationPath'");
        }
        return $migrationPath;
    }
    public function getMigrationTempPathFrom()
    {
        return $this->ensurePathExists($this->getTempPath() . DIRECTORY_SEPARATOR . 'from');
    }
    public function getMigrationTempPathTo()
    {
        return $this->ensurePathExists($this->getTempPath() . DIRECTORY_SEPARATOR . 'to');
    }
    public function getModulesPath()
    {
        return realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules');
    }
    public function getAllModelPaths()
    {
        $ret = array();
        foreach ($this->_autoloaderPaths as $namespace => $path) {
            if (!is_dir($path)) {
                continue;
            }
            $namespaceSplit = explode('_', $namespace);
            if (isset($namespaceSplit[1]) && $namespaceSplit[1] == 'Model' && count($namespaceSplit) == 2) {
                $ret[$namespace] = $path;
            }
        }
        return $ret;
    }
    public function getAllSchemaPaths()
    {
        $ret = array();
        foreach ($this->_autoloaderPaths as $namespace => $path) {
            $namespaceSplit = explode('_', $namespace);
            if (!isset($namespaceSplit[1]) || $namespaceSplit[1] != 'Model' || count($namespaceSplit) != 2) {
                continue;
            }
            $moduleName = $namespaceSplit[0];
            $schemaPath = $path . DIRECTORY_SEPARATOR . 'schema';
            $ret[$moduleName] = $schemaPath;
        }
        return $ret;
    }
    public function getFixturePath()
    {
        $basePath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'resource' . 
            DIRECTORY_SEPARATOR . 'fixtures';
        $datePath = date('Y-m-d_H-i');
        $pathSuffix = '';
        $returnPath = $basePath . DIRECTORY_SEPARATOR . $datePath;
        $i = 1;
        while (is_dir($returnPath)) {
            $pathSuffix = '_' . $i;
            $returnPath = $basePath . DIRECTORY_SEPARATOR . $datePath . $pathSuffix;
            $i++;
        }
        if (!@mkdir($returnPath, 0777, true)) {
            throw new Exception("Unable to create fixtures directory.");
        }
        $ret = new stdClass();
        $ret->fullPath = $returnPath;
        $ret->relPath = $datePath . $pathSuffix;
        return $ret;
    }
    public function getFixturePathByBasename($baseName)
    {
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'resource' . DIRECTORY_SEPARATOR . 
        'fixtures' . DIRECTORY_SEPARATOR . $baseName;
        if (!is_dir($path)) {
            throw new Exception('Unable to determine fixture directory');
        }
        return $path;
    }
    public function getFixturePathList()
    {
        $basePath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'resource' .
            DIRECTORY_SEPARATOR . 'fixtures';
        $dh = opendir($basePath);
        if (!$dh) {
            throw new Exception('Unable to open fixtures path');
        }
        $ret = array();
        while (false !== ($file = readdir($dh))) {
            if (substr($file, 0, 1) == '.') {
                continue;
            }
            $fullPath = $basePath . DIRECTORY_SEPARATOR . $file;
            if (!is_dir($fullPath)) {
                continue;
            }
            $ret[$file] = $fullPath;
        }
        krsort($ret);
        return $ret;
    }
}
