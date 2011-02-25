<?php
/*
 *  This file is part of FansubCMS.
 *
 *  FansubCMS is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  FansubCMS is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with FansubCMS.  If not, see <http://www.gnu.org/licenses/>
 */
/**
 * 
 * This class is the api to generate models and migrations
 * @author Hikaru-Shindo <hikaru@fansubcode.org>
 *
 */
class Devtools_Api_DoctrineTool
{
    /*
     * class properties
     */
    /**
     * 
     * This is the temporary path to do all actions in
     * @var string
     */
    protected $_tmpPath;
    /**
     * 
     * This is the path where the modules reside
     * @var string
     */
    protected $_modulePath;
    
    /*
     * magic methods
     */
    /**
     * 
     * The class constructor
     * @return void
     */
    public function __construct()
    {
        mkdir($this->getTempPath());
        mkdir($this->getFromPath());
        mkdir($this->getToPath());
    }
    
    /*
     * main methods
     */
    
    /**
     * 
     * Returns the temporary path
     * @return string
     */
    public function getTempPath()
    {
        if(empty($this->_tmpPath)) 
        {
            $this->_tmpPath = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . 'fcms_mig_' . md5(microtime() . 'migration');
        }
        return $this->_tmpPath;
    }
    
    /**
     * 
     * Returns the from-path
     * @return string
     */
    public function getFromPath() 
    {
        return $this->getTempPath() . DIRECTORY_SEPARATOR . 'from';
    }

    /**
     * 
     * Returns the to-path
     * @return string
     */    
    public function getToPath() 
    {
        return $this->getTempPath() . DIRECTORY_SEPARATOR . 'to';
    }
    
    /**
     * 
     * Set the path where the modules reside
     * @param string $path
     * @return void
     */
    public function setModulePath($path)
    {
        $this->_modulePath = $path;
    }
    
    /**
     * 
     * Returns the module path
     * @return string
     */
    public function getModulePath()
    {
        if(empty($this->_modulePath)) {
            $this->_modulePath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules';
        }
        return $this->_modulePath;
    }
    
    /**
     * 
     * Generate the models to the module folder
     * @return void
     */
    public function generateModels()
    {
        $this->_getSchema($this->getFromPath());
        $this->_generateModels($this->getFromPath(), $this->getToPath());
        
        $models = glob($this->getToPath() . DIRECTORY_SEPARATOR . '*.php');
        
        foreach($models as $model) {
            $filename = str_replace($this->getToPath() . DIRECTORY_SEPARATOR, '', $model);
            $filenameParts = explode('_',$filename);
            $module = strtolower($filenameParts[0]);
            $modelDir = $this->getModulePath() . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'models';
            if(!is_dir($modelDir)) {
                mkdir($modelDir);
            }
            if(!is_dir($modelDir . DIRECTORY_SEPARATOR . 'Base')) {
                mkdir($modelDir . DIRECTORY_SEPARATOR . 'Base');
            }
            if(!file_exists($modelDir . DIRECTORY_SEPARATOR . $filenameParts[2])) {
                copy($model, $modelDir . DIRECTORY_SEPARATOR . $filenameParts[2]);
            }
            unlink($model);
        }
        
        $baseModels = glob($this->getToPath() . DIRECTORY_SEPARATOR . 'Base' . DIRECTORY_SEPARATOR . '*.php');
        foreach($baseModels as $model) {
            $filename = str_replace($this->getToPath() . DIRECTORY_SEPARATOR, '', $model);
            $filenameParts = explode('_',$filename);
            $module = strtolower($filenameParts[1]);
            $modelDir = $this->getModulePath() . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Base';
            if(file_exists($modelDir . DIRECTORY_SEPARATOR . $filenameParts[3])) {
                unlink($modelDir . DIRECTORY_SEPARATOR . $filenameParts[3]);
            }
            copy($model, $modelDir . DIRECTORY_SEPARATOR . $filenameParts[3]);
            unlink($model);
        }
        
        rmdir($this->getToPath() . DIRECTORY_SEPARATOR . 'Base');
    }
    
    /*
     * Helpers
     */
    
    /**
     * 
     * Get the schema files and save them to $path
     * @param string $path
     * @return void
     */
    protected function _getSchema($path)
    {
        $schema = glob($this->getModulePath() . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'schema' . DIRECTORY_SEPARATOR . '*.yml');
        foreach($schema as $yml) {
            $filename = implode('_',explode(DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'schema' . DIRECTORY_SEPARATOR, str_replace($this->getModulePath() . DIRECTORY_SEPARATOR, '', $yml)));
            copy($yml, $path . DIRECTORY_SEPARATOR . $filename);
        }
    }
    
    /**
     * 
     * Generate models from $yaml to $path
     * @param string $yaml
     * @param string $path
     * @param array $options
     * @return void
     */
    protected function _generateModels($yaml, $path, array $options = array())
    {
        if(!count($options)) {
            $options = array(
            	'baseClassName'   =>  'FansubCMS_Doctrine_Record',
      			'suffix'          =>  '.php',
                'baseClassesDirectory' => 'Base',
                'baseClassPrefix' => 'Base_',
                'generateTableClasses' => true,
                'generateBaseClasses' => true,
                'phpDocPackage' => 'FansubCMS',
                'phpDocSubpackage' => 'Models',
                'phpDocName' => 'FansubCMS Dev Team',
                'phpDocEmail' => 'hikaru@fansubcode.org'
            );
        }
                
        Doctrine::generateModelsFromYaml($yaml, $path, $options);
    }
}