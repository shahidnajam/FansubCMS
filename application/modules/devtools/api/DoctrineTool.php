<?php
class Devtools_Api_DoctrineTool
{
    private static $_pathProfile = 0;
    private static $_migrationModelPath;
    const PATH_MODULE = 1;
    const PATH_MODULE_MODEL = 2;
    const PATH_MODULE_SCHEMA = 3;
    const PATH_MODULE_TABLE = 4;
    const PATH_MODULE_BASEMODEL = 5;
    const PATH_TEMP = 6;
    const PATH_MIGRATIONSET = 7;
    const PATH_MIGRATIONSCRIPTS = 8;
    const PATH_ALIASCLASSES = 9;
    const PATHPROFILE_DEFAULT = 0;
    const PATHPROFILE_MIGRATION = 1;
    public static function setPathProfile($profile)
    {
        self::$_pathProfile = $profile;
    }
    public static function getAllDoctrineModels($withPaths = false, $tableClasses = false, $baseClasses = false)
    {
        $modules = array();
        $s = DIRECTORY_SEPARATOR;
        $pathfinder = Devtools_Api_Pathfinder::getInstance();
        $paths = $pathfinder->getAllModelPaths();
        $ret = array();
        foreach ($paths as $namespace => $path) {
            $dh = opendir($path);
            $namespaceSplit = explode('_', $namespace);
            $module = $namespaceSplit[0];
            if (!$dh) {
                throw new Exception("Unable to open directory '$fullModelPath'");
            }
            while (false !== ($file = readdir($dh))) {
                if ($file == '.' || $file == '..' || substr($file, -4) != '.php')
                    continue;
                //echo 'File: '.$file."\n";
                $classBaseName = substr($file, 0, -4);
                $isTable = (substr($classBaseName, -5) == 'Table');
                if (!$tableClasses && $isTable) {
                    continue;
                }
                $className = ucfirst(strtolower($module)) . '_Model_' . $classBaseName;
                $filePath = $path . $s . $file;
                if ($withPaths) {
                    $ret[] = array($className, $filePath);
                } else {
                    $ret[] = $className;
                }
                if ($baseClasses) {
                    $baseClassPath = $path . $s . 'Base' . $s . $file;
                    if (file_exists($baseClassPath)) {
                        $classSplit = explode('_', $className);
                        $baseClassName = ucfirst(strtolower($module)) . '_Model_Base_' . $classBaseName;
                        if ($withPaths) {
                            $ret[] = array($baseClassName, $baseClassPath);
                        } else {
                            $ret[] = $baseClassName;
                        }
                    }
                }
            }
        }
        return $ret;
    }
    public static function loadAllDoctrineModels()
    {
        $models = self::getAllDoctrineModels();
        foreach ($models as $m) {
            Doctrine::loadModel($m);
        }
    }

    public static function generateModels($migrationMode = false)
    {
        //echo '<pre>';
        $options = array(
            'generateBaseClasses'  => true,
            'generateTableClasses' => true,
            'baseClassesDirectory' => 'Base',
            'baseClassName'        => 'FansubCMS_Doctrine_Record',
            'suffix'               => '.php'
        );
        $pathfinder = Devtools_Api_Pathfinder::getInstance();
        if ($migrationMode) {
            $options['generateTableClasses'] = false;
        }
        $moduleSchemas = $pathfinder->getAllSchemaPaths();
        $import = new Devtools_Api_DoctrineTool_Import_Schema();
        $import->setOptions($options);
        $import->setMigrationMode($migrationMode);
        $tmpPath = $migrationMode ? $pathfinder->getMigrationTempPathTo() : $pathfinder->getTempPath();
        $writtenFiles = $import->importSchema($moduleSchemas, 'yml', $tmpPath);
        //print_r($writtenFiles);
        //die();
        return $writtenFiles;
    }
    public static function createInitialMigration()
    {
        $migration = self::getMigration();
        if ($migration->getLatestVersion() > 0) {
            throw new Exception('Refusing to create an initial migration set. Migration classes already exist.');
        }
        $pathfinder = Devtools_Api_Pathfinder::getInstance();
        self::loadAllDoctrineModels();
        $res = Doctrine::generateMigrationsFromModels($pathfinder->getMigrationPath());
        return $res;
    }
    public static function setMigrationVersionToCurrent()
    {
        $migration = self::getMigration();
        $migration->setCurrentVersion($migration->getLatestVersion());
    }
    public static function setMigrationVersion($version)
    {
        $migration = self::getMigration();
        $migration->setCurrentVersion($version);
    }
    public static function generateMigrations($detectOnly = true)
    {
        $dm = Doctrine_Manager::getInstance();
        if (!$detectOnly && $dm->getAttribute(Doctrine_Core::ATTR_TBLNAME_FORMAT) != '%s') {
            throw new Exception(
                "You can not create migrations from diff with a custom Doctrine attribute 'ATTR_TBLNAME_FORMAT'");
        }
        if (!$detectOnly && $dm->getAttribute(Doctrine_Core::ATTR_IDXNAME_FORMAT) != '%s_idx') {
            throw new Exception(
                "You can not create migrations from diff with a custom Doctrine attribute 'ATTR_IDXNAME_FORMAT'");
        }
        if (!$detectOnly && $dm->getAttribute(Doctrine_Core::ATTR_SEQNAME_FORMAT) != '%s_seq') {
            throw new Exception(
                "You can not create migrations from diff with a custom Doctrine attribute 'ATTR_SEQNAME_FORMAT'");
        }
        $pathfinder = Devtools_Api_Pathfinder::getInstance();
        $migrationsPath = $pathfinder->getMigrationPath();
        $initialMigration = false;
        if (!is_dir($migrationsPath)) {
            mkdir($migrationsPath, 0777, true);
            $initialMigration = true;
        } elseif (count(glob($migrationsPath . DIRECTORY_SEPARATOR . '*.php')) <= 0) {
            $initialMigration = true;
        }
        if ($initialMigration && !$detectOnly) {
            self::createInitialMigration();
        }
        $writtenFiles = self::generateModels(true);
        $migrationSetPath = $pathfinder->getTempPath();
        $fromPath = $pathfinder->getMigrationTempPathFrom();
        $toPath = $pathfinder->getMigrationTempPathTo();
        $fileList = self::getAllDoctrineModels(true, false, true);
        foreach ($fileList as $file) {
            $className = $file[0];
            $path = $file[1];
            $classSplit = explode('_', $className);
            $isBaseClass = (count($classSplit) == 4);
            if ($isBaseClass) {
                $newFilename = 'Base' . $classSplit[3] . '.php';
            } else {
                $newFilename = $classSplit[2] . '.php';
            }
            $newFilename = $file[0] . '.php';
            $newPath = $fromPath . DIRECTORY_SEPARATOR . $newFilename;
            copy($path, $newPath);
            $writtenFiles[] = $newPath;
        }
        $diff = new Doctrine_Migration_Diff($fromPath, $toPath, $migrationsPath);

        if ($detectOnly) {
            $ret = $diff->generateChanges();
        } else {
            $ret = $diff->generateMigrationClasses();
        }
        // garbage collection
        foreach ($writtenFiles as $file) {
            unlink($file);
        }
        rmdir($fromPath);
        rmdir($toPath . DIRECTORY_SEPARATOR . 'Base');
        rmdir($toPath);
        rmdir($migrationSetPath);
        // return result
        return $ret;
    }
    public static function generateMigrationsFromDb($detectOnly = true)
    {
        $pathfinder = Devtools_Api_Pathfinder::getInstance();
        $migrationsPath = $pathfinder->getMigrationPath();
        $initialMigration = false;
        if (!is_dir($migrationsPath)) {
            mkdir($migrationsPath, 0777, true);
            $initialMigration = true;
        } elseif (count(glob($migrationsPath . DIRECTORY_SEPARATOR . '*.php')) <= 0) {
            $initialMigration = true;
        }
        if ($initialMigration && !$detectOnly) {
            self::createInitialMigration();
        }
        $writtenFiles = self::generateModels(true);
        $migrationSetPath = $pathfinder->getTempPath();
        //$fromPath = $pathfinder->getMigrationTempPathFrom();
        $toPath = $pathfinder->getMigrationTempPathTo();
        $diff = new Doctrine_Migration_Diff('default', $toPath, $migrationsPath);
        if ($detectOnly) {
            $ret = $diff->generateChanges();
        } else {
            $ret = $diff->generateMigrationClasses();
        }
        rmdir($toPath . DIRECTORY_SEPARATOR . 'Base');
        rmdir($toPath);
        rmdir($migrationSetPath);
        // return result
        return $ret;
    }
    public static function generateAliasClasses($prefix = '')
    {
        $models = self::getAllDoctrineModels(true, false, false);
        $aliasPath = self::getPath(self::PATH_ALIASCLASSES);
        foreach ($models as $model) {
            $modelSplit = explode('_', $model[0]);
            $className = $modelSplit[2];
            $newClassName = $prefix . $className;
            $out = "<?php\n" . "class $newClassName extends {$model[0]} {\n" . "\n" . "}\n";
            $classPath = $aliasPath . DIRECTORY_SEPARATOR . $className . '.php';
            file_put_contents($classPath, $out);
        }
    }
    public static function getMigration($conn = null)
    {
        $pathfinder = Devtools_Api_Pathfinder::getInstance();
        if (is_null($conn)) {
            $conn = Doctrine_Manager::getInstance()->getCurrentConnection();
        }
        $migration = new Doctrine_Migration($pathfinder->getMigrationPath(), $conn);
        return $migration;
    }
    public static function migrate($test = null)
    {
        $settings = Zend_Registry::get('settings');
        if (null == $test && isset($settings->doctrine->testMigrations)) {
            $test = (bool) $settings->doctrine->testMigrations;
        } elseif (null == $test) {
            $test = true;
        }
        if ($test) {
            if (!isset($settings->doctrine->testdsn)) {
                throw new Exception("No Test-DSN set.");
            }
            $testMigration = self::testMigration();
            if ($testMigration->hasErrors()) {
                return $testMigration;
            }
        }
        Doctrine_Manager::getInstance()->setCurrentConnection('default');
        $conn = Doctrine_Manager::getInstance()->getConnection('default');
        $migration = self::getMigration($conn);
        $migration->migrate();
        // self::generateModels();
        return $migration;
    }
    public static function testMigration()
    {
        fb('testMigration');
        $settings = Zend_Registry::get('settings');
        if (isset($settings->doctrine->testdsn)) {
            return self::_testMigrationUsingTestConnection($settings->doctrine->testdsn);
        } else {
            return self::_testMigrationUsingDryRun();
        }
    }
    protected static function _testMigrationUsingTestConnection($dsn)
    {
        fb('testConnection');
        $testconn = Doctrine_Manager::connection($dsn, 'test');
        //$testconn->dropDatabase();
        $dropError = false;
        try {
            $testconn->dropDatabase();
        } catch (Doctrine_Exception $e) {
            fb('Error dropping test database: ' . $e->getMessage());
            $dropError = true;
        }
        if (!$dropError) {
            fb('Test database successfully dropped.');
        }
        $testconn->createDatabase();
        $migration = self::getMigration($testconn);
        // Use dry run so no Exception is thrown;
        $migration->migrateDryRun();
        //$testconn->dropDatabase();
        return $migration;
    }
    protected static function _testMigrationUsingDryRun()
    {
        fb('dryRun');
        $migration = self::getMigration();
        $migration->migrateDryRun();
        return $migration;
    }
}
