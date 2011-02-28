<?php
class FansubCMS_Loader_Autoloader_Basemodel implements Zend_Loader_Autoloader_Interface
{
    public function autoload($class)
    {
        $classParts = explode('_', $class);
        if($classParts[2] != 'Model') {
            return false; // no base model
        }
        $module = strtolower($classParts[1]);
        $filename = $classParts[3] . '.php';
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module . 
        DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Base' . DIRECTORY_SEPARATOR;

        if(!file_exists($path . $filename)) {
            return false;
        }

        if(is_readable($path . $filename)) {
            
            include_once $path . $filename;
            return true;
        }
        
        return false;
    }
}