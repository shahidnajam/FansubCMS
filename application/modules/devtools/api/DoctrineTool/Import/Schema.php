<?php
class Devtools_Api_DoctrineTool_Import_Schema extends Doctrine_Import_Schema 
{
        private $_migrationMode = false;
        private $_migrationPrefix = "ToPrfx";
        private $_writtenFiles = array();
        private $_classModuleMap = array();

        

        public function setMigrationMode($migrationMode = true) {
            $this->_migrationMode = $migrationMode;
        }

        protected function _mapModuleClass($className, $moduleName = NULL) {
            $modelSplit = explode('_', $className);
            $isShortname = (count($modelSplit) == 1);

            if($isShortname) {
                $classBaseName = $className;
            }
            else {
                $classBaseName = $modelSplit[2];
            }

            if(!is_null($moduleName)) {
                $moduleName = strtolower($moduleName);
            }

            //echo 'Model: '.$moduleName.' '.$className."\n";
            if(is_null($moduleName) && $isShortname) {
                //die();
                throw new Exception("Unable to map class '$className' to module. Only shortname given.");
            }
            elseif(is_null($moduleName)) {
                $moduleName = strtolower($modelSplit[0]);
            }

            if(!isset($this->_classModuleMap[$classBaseName])) {
                $this->_classModuleMap[$classBaseName] = $moduleName;
            }
            elseif(is_array($this->_classModuleMap[$classBaseName])) {
                if(in_array($moduleName, $this->_classModuleMap[$classBaseName]))
                    return;

                $this->_classModuleMap[$classBaseName][] = $moduleName;
            }
            else {
                if($this->_classModuleMap[$classBaseName] == $moduleName)
                    return;

                $this->_classModuleMap[$classBaseName] = array($this->_classModuleMap[$classBaseName]);
                $this->_classModuleMap[$classBaseName][] = $moduleName;
            }
        }

        protected function _getRealClassname($className, $currentModule, $prefix = '', $currentModel = '') {
            $classNameSplit = explode('_', $className);

            $currentModule = strtolower($currentModule);

            $isShortname = (count($classNameSplit) == 1);

            if($isShortname) {
                $classBaseName = $className;
            }
            else {
                return $prefix . $className;
            }

            if(!isset($this->_classModuleMap[$classBaseName])) {
                throw new Exception("Class $classBaseName is not mapped. Current Module: $currentModule, Current Model: $currentModel");
            }

            $map = $this->_classModuleMap[$classBaseName];

            if(is_array($map) && in_array($currentModule, $map)) {
                $moduleName = $currentModule;
            }
            elseif(is_array($map)) {
                throw new Exception("Cannot map class $classBaseName to module. Class name is ambigious.");
            }
            elseif(is_string($map)) {
                $moduleName = $map;
            }

            return $prefix . ucfirst(strtolower($moduleName)) . '_Model_' . $classBaseName;
        }

        protected function _buildClassModuleMap($structArray) {
            // Collect and map models to module

            $this->_classModuleMap = array();
            $existingModels = Devtools_Api_DoctrineTool::getAllDoctrineModels();

            foreach($existingModels as $model) {
                $this->_mapModuleClass($model);
            }
            //echo 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';
            //print_r($structArray);
            foreach($structArray as $definition) {
                $this->_mapModuleClass($definition['className'], $definition['zfModule']);
            }
        }


        protected function _remapClassnames($array, $prefix = '') {
            $newArray = array();
//            echo "<pre>##########\n"; debug_print_backtrace(); print_r($array); echo "</pre>";

            foreach($array as $name => $definition) {
                $moduleName = $definition['zfModule'];

                $modelName = isset($definition['className']) ? $definition['className'] : $name;

                if(!isset($moduleClasses[$moduleName])) {
                    $moduleClasses[$moduleName] = array();
                }

                if(!in_array($definition['className'], $moduleClasses[$moduleName])) {
                    $moduleClasses[$moduleName][] = $definition['className'];
                }

                if(is_null($moduleName)) die();

                $definition['className'] = $this->_getRealClassName($definition['className'], $moduleName, $prefix, $modelName); //$fullPrefix . $definition['className'] . $suffix;
                $definition['connectionClassName'] = $this->_getRealClassName($definition['connectionClassName'], $moduleName, $prefix, $modelName); //$definition['connectionClassName'] . $suffix;

                // Klassennamen der Relations:
                if(isset($definition['relations'])) {
                    $newRel = array();
                    foreach($definition['relations'] as $relName => $relDefinition) {
                        $hasClassDefinition = isset($relDefinition['class']);
                        if($hasClassDefinition) {
                            $relDefinition['class'] = $this->_getRealClassname($relDefinition['class'] , $moduleName, $prefix, $modelName); //$fullPrefix . $relDefinition['class'] . $suffix;
                        }
                        if(isset($relDefinition['refClass'])) {
                            $relDefinition['refClass'] = $this->_getRealClassname($relDefinition['refClass'] , $moduleName, $prefix, $modelName);
                        }

                        if(!$hasClassDefinition) {
                            $newRel[$this->_getRealClassname($relName, $moduleName, $prefix, $modelName)] = $relDefinition;
                        }
                        else {
                            $newRel[$relName] = $relDefinition;
                        }
                    }
                    $definition['relations'] = $newRel;

                }
                $newArray[$this->_getRealClassname($name, $moduleName, $prefix, $modelName)] = $definition;
            }

            return $newArray;
        }


        public function importSchema($schema, $format = 'yml', $tmpDir)
        {
            $this->_writtenFiles = array();

            if(empty($tmpDir)) {
                throw new Exception('Temp path is empty');
            }

            $model = array();

            $schema = (array) $schema;
            $builder = new Doctrine_Import_Builder();
            $builder->setTargetPath($tmpDir);
            $builder->setOptions($this->getOptions());

            $nonRemappedArray = $this->buildSchema($schema, $format);
            $this->_buildClassModuleMap($nonRemappedArray);
            $this->_relations = array();

            $array = $this->buildSchema($schema, $format, true);
            if($this->_migrationMode) {
                $array = $this->_remapClassnames($array, $this->_migrationPrefix);
            }

            if (count($array) == 0) {
                throw new Doctrine_Import_Exception(
                    sprintf('No ' . $format . ' schema found in ' . implode(", ", $schema))
                );
            }

            foreach ($array as $name => $definition) {
                if ( ! empty($models) && !in_array($definition['className'], $models)) {
                    continue;
                }
                $builder->buildRecord($definition);
            }

            if(!$this->_migrationMode) {
                $this->_moveClassFiles($tmpDir);
            }
            else {
                $this->_writtenFiles = $this->_getFiles($tmpDir);
            }

            return $this->_writtenFiles;
        }

        protected function _getFiles($dir) {
            $ret = array();
            $files = glob($dir . DIRECTORY_SEPARATOR . '*');

            foreach($files as $file) {
                if(is_dir($file)) {
                    $subdirFiles = $this->_getFiles($file);
                    foreach($subdirFiles as $subdirFile) {
                        $ret[] = $subdirFile;
                    }
                }
                else {
                    $ret[] = $file;
                }
            }

            return $ret;
        }

        protected function _moveClassfiles($tmpDir)  {
            $pathfinder = Devtools_Api_Pathfinder::getInstance();

            $s = DIRECTORY_SEPARATOR;

            $dh = opendir($tmpDir);
            if(!$dh) {
                throw new Exception("Unable to open temporary directory");
            }

            while(false !== ($file = readdir($dh))) {
                if($file == '.' || $file == '..' || substr($file, -4) != '.php') {
                    continue;
                }

                $className = substr($file, 0, -4);
                $classNameSplit = explode('_', $className);

                $module = strtolower($classNameSplit[0]);
                $classBaseName = $classNameSplit[2];

                $destinationFileName = $classBaseName . '.php';

                $baseClassName = ucfirst($module) . '_Model_Base_' . $classBaseName;

                $sourcePath = $tmpDir . $s . $file;
                $destinationDir = $pathfinder->getModelPath($module); // $modulesBasePath . $s . $module . $modelPath;
                $destinationPath =  $destinationDir . $s . $destinationFileName;

                if(!file_exists($destinationPath)) {
                    if(!is_dir($destinationDir)) {
                        mkdir($destinationDir, 0777, true);
                    }

                    $sourceContents = file_get_contents($sourcePath);
                    $destinationContents = str_replace(
                        "class {$className} extends Base{$className}",
                        "class {$className} extends {$baseClassName}",
                        $sourceContents
                    );

                    file_put_contents($destinationPath, $destinationContents);
                    $this->_writtenFiles[] = $destinationPath;
                }
                unlink($sourcePath);
            }

            $dh = opendir($tmpDir . $s . 'Base');
            if(!$dh) {
                throw new Exception("Unable to open temporary base classes directory");
            }

            while(false !== ($file = readdir($dh))) {
                if($file == '.' || $file == '..' || substr($file, -4) != '.php' || substr($file, 0, 4) != 'Base') {
                    continue;
                }

                $sourceClassName = substr($file, 0, -4);
                $className = substr($sourceClassName, 4);

                $classNameSplit = explode('_', $className);

                $module = strtolower($classNameSplit[0]);
                $classBaseName = $classNameSplit[2];

                $baseClassName = ucfirst($module) . '_Model_Base_' . $classBaseName;

                $destinationFileName = $classBaseName . '.php';
                $sourcePath = $tmpDir . $s . 'Base' . $s . $file;
                $destinationDir = $pathfinder->getBaseModelPath($module);
                $destinationPath = $destinationDir . $s . $destinationFileName;

                if(!is_dir($destinationDir)) {
                    mkdir($destinationDir, 0777, true);
                }
                //echo 'Von '.$sourcePath."\n";
                $sourceContents = file_get_contents($sourcePath);

                $destinationContents = str_replace(
                    "abstract class {$sourceClassName} extends FansubCMS_Doctrine_Record",
                    "abstract class {$baseClassName} extends FansubCMS_Doctrine_Record",
                    $sourceContents
                );
                //echo $destinationContents;
                //echo 'nach '.$destinationPath. "\n";
                file_put_contents($destinationPath, $destinationContents);
                $this->_writtenFiles[] = $destinationPath;
                unlink($sourcePath);
            }
            return $this->_writtenFiles;
        }


        /**
         * buildSchema
         *
         * Loop throug directories of schema files and parse them all in to one complete array of schema information
         *
         * @param  string   $schema Array of schema files or single schema file. Array of directories with schema files or single directory
         * @param  string   $format Format of the files we are parsing and building from
         * @return array    $array
         */
        public function buildSchema($schema, $format, $rewrite = false)
        {
            $array = array();

            foreach ((array) $schema AS $zfModule => $s) {
                if(is_int($zfModule)) {
                    $zfModule = 'Default';
                }

                if (is_file($s)) {
                    $e = explode('.', $s);
                    if (end($e) === $format) {
                        $array = array_merge($array, $this->zfParseSchema($s, $format, $zfModule));
                    }
                } else if (is_dir($s)) {
                    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($s),
                                                          RecursiveIteratorIterator::LEAVES_ONLY);

                    foreach ($it as $file) {
                        $e = explode('.', $file->getFileName());
                        if (end($e) === $format) {
                            $array = array_merge($array, $this->zfParseSchema($file->getPathName(), $format, $zfModule));
                        }
                    }
                } else {
                  $array = array_merge($array, $this->zfParseSchema($s, $format, $zfModule));
                }
            }

            if($rewrite) {
                $remapped = $array = $this->_remapClassnames($array);
            }

            $buildRelationships = $array = $this->_buildRelationships($array);
            $array = $this->_processInheritance($array);

            return $array;
        }


         /**
         * zfParseSchema
         *
         * A method to parse a Schema and translate it into a property array.
         * The function returns that property array.
         *
         * @param  string $schema   Path to the file containing the schema
         * @param  string $type     Format type of the schema we are parsing
         * @return array  $build    Built array of schema information
         */
        public function zfParseSchema($schema, $type, $zfModule = "Default")
        {
            $defaults = array('abstract'            =>  false,
                              'className'           =>  null,
                              'tableName'           =>  null,
                              'connection'          =>  null,
                              'relations'           =>  array(),
                              'indexes'             =>  array(),
                              'attributes'          =>  array(),
                              'templates'           =>  array(),
                              'actAs'               =>  array(),
                              'options'             =>  array(),
                              'package'             =>  null,
                              'inheritance'         =>  array(),
                              'detect_relations'    =>  false,
                              'zfModule'            =>  $zfModule);

            $array = Doctrine_Parser::load($schema, $type);

            // Go through the schema and look for global values so we can assign them to each table/class
            $globals = array();
            $globalKeys = array('connection',
                                'attributes',
                                'templates',
                                'actAs',
                                'options',
                                'package',
                                'package_custom_path',
                                'inheritance',
                                'detect_relations');

            // Loop over and build up all the global values and remove them from the array
            foreach ($array as $key => $value) {
                if (in_array($key, $globalKeys)) {
                    unset($array[$key]);
                    $globals[$key] = $value;
                }
            }

            // Apply the globals to each table if it does not have a custom value set already
            foreach ($array as $className => $table) {
                foreach ($globals as $key => $value) {
                    if (!isset($array[$className][$key])) {
                        $array[$className][$key] = $value;
                    }
                }
            }

            $build = array();

            foreach ($array as $className => $table) {
                $table = (array) $table;
                $this->_validateSchemaElement('root', array_keys($table), $className);

                $columns = array();

                $className = isset($table['className']) ? (string) $table['className']:(string) $className;

                if (isset($table['inheritance']['keyField']) || isset($table['inheritance']['keyValue'])) {
                    $table['inheritance']['type'] = 'column_aggregation';
                }

                if (isset($table['tableName']) && $table['tableName']) {
                    $tableName = $table['tableName'];
                } else {
                    if (isset($table['inheritance']['type']) && ($table['inheritance']['type'] == 'column_aggregation')) {
                        $tableName = null;
                    } else {
                        $tableName = Doctrine_Inflector::tableize($className);
                    }
                }

                $connection = isset($table['connection']) ? $table['connection']:'current';

                $columns = isset($table['columns']) ? $table['columns']:array();

                if ( ! empty($columns)) {
                    foreach ($columns as $columnName => $field) {

                        // Support short syntax: my_column: integer(4)
                        if ( ! is_array($field)) {
                            $original = $field;
                            $field = array();
                            $field['type'] = $original;
                        }

                        $colDesc = array();
                        if (isset($field['name'])) {
                            $colDesc['name'] = $field['name'];
                        } else {
                            $colDesc['name'] = $columnName;
                        }

                        $this->_validateSchemaElement('column', array_keys($field), $className . '->columns->' . $colDesc['name']);

                        // Support short type(length) syntax: my_column: { type: integer(4) }
                        $e = explode('(', $field['type']);
                        if (isset($e[0]) && isset($e[1])) {
                            $colDesc['type'] = $e[0];
                            $value = substr($e[1], 0, strlen($e[1]) - 1);
                            $e = explode(',', $value);
                            $colDesc['length'] = $e[0];
                            if (isset($e[1]) && $e[1]) {
                                $colDesc['scale'] = $e[1];
                            }
                        } else {
                            $colDesc['type'] = isset($field['type']) ? (string) $field['type']:null;
                            $colDesc['length'] = isset($field['length']) ? (int) $field['length']:null;
                            $colDesc['length'] = isset($field['size']) ? (int) $field['size']:$colDesc['length'];
                        }

                        $colDesc['fixed'] = isset($field['fixed']) ? (int) $field['fixed']:null;
                        $colDesc['primary'] = isset($field['primary']) ? (bool) (isset($field['primary']) && $field['primary']):null;
                        $colDesc['default'] = isset($field['default']) ? $field['default']:null;
                        $colDesc['autoincrement'] = isset($field['autoincrement']) ? (bool) (isset($field['autoincrement']) && $field['autoincrement']):null;
                        $colDesc['sequence'] = isset($field['sequence']) ? (string) $field['sequence']:null;
                        $colDesc['values'] = isset($field['values']) ? (array) $field['values']:null;

                        // Include all the specified and valid validators in the colDesc
                        $validators = Doctrine_Manager::getInstance()->getValidators();

                        foreach ($validators as $validator) {
                            if (isset($field[$validator])) {
                                $colDesc[$validator] = $field[$validator];
                            }
                        }

                        $columns[(string) $columnName] = $colDesc;
                    }
                }

                // Apply the default values
                foreach ($defaults as $key => $defaultValue) {
                    if (isset($table[$key]) && ! isset($build[$className][$key])) {
                        $build[$className][$key] = $table[$key];
                    } else {
                        $build[$className][$key] = isset($build[$className][$key]) ? $build[$className][$key]:$defaultValue;
                    }
                }

                $build[$className]['className'] = $className;
                $build[$className]['tableName'] = $tableName;
                $build[$className]['columns'] = $columns;

                // Make sure that anything else that is specified in the schema makes it to the final array
                $build[$className] = Doctrine_Lib::arrayDeepMerge($table, $build[$className]);

                // We need to keep track of the className for the connection
                $build[$className]['connectionClassName'] = $build[$className]['className'];
            }

            return $build;
        }
    }