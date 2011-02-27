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
 *  
 */
/**
 * 
 * This class is the api to install the cms
 * @author Hikaru-Shindo <hikaru@fansubcode.org>
 *
 */
class Install_Api_DoctrineTool
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
    
    /**
     * 
     * Doctrine Migration
     * @var Doctrine_Migration
     */
    protected $_migration;
    
    /**
     * 
     * Instance of this api
     * @var Install_Api_DoctrineTool
     */
    protected static $_instance;
       
    /*
     * main methods
     */
    
    /**
     * 
     * Get an instance of the API
     * @return Install_Api_DoctrineTool
     */
    public static function getInstance()
    {
        if(empty(self::$_instance)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    
    /**
     * 
     * Set the path where the modules reside
     * @param string $path
     * @return void
     */
    public function setModulePath ($path)
    {
        $this->_modulePath = $path;
    }
    
    /**
     * 
     * Returns the module path
     * @return string
     */
    public function getModulePath ()
    {
        if (empty($this->_modulePath)) {
            $this->_modulePath = APPLICATION_PATH . DIRECTORY_SEPARATOR .
             'modules';
        }
        return $this->_modulePath;
    }
   
    /**
     * 
     * Initialize the database
     * @return void
     */
    public function createTablesFromModels()
    {
        $models = $this->_getModels();
        Doctrine::createTablesFromArray($models);
    }
    
    
    /*
     * Helpers
     */
    
    /**
     * 
     * Copies all models to $path
     * @return array
     */
    protected function _getModels ()
    {
        $models = glob($this->getModulePath() . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR .
        'models' . DIRECTORY_SEPARATOR . '*.php');
        
        $classNames = array();
        
        foreach($models as $model) {
            $filename = str_replace($this->getModulePath() . DIRECTORY_SEPARATOR, '', $model);
            $filenameParts = explode(DIRECTORY_SEPARATOR, $filename);
            $classNames[] = ucfirst($filenameParts[0]).'_Model_'.str_replace('.php', '', $filenameParts[2]);
        }
        
        return $classNames;
    }
}