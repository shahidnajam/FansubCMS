<?php
// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
        realpath(APPLICATION_PATH . '/../library'),
        realpath(APPLICATION_PATH . '/../library/Doctrine'),
        realpath(APPLICATION_PATH . '/models/generated'),
        get_include_path(),
)));

if(APPLICATION_ENV != 'development' && (file_exists(realpath(dirname(__FILE__)).'/.lock')) || file_exists(realpath(dirname(__FILE__)).'/lock')) {
    die("The installer is locked!");
}

session_start('installSession');

require_once('Zend/Loader/Autoloader.php');
$autoLoader = Zend_Loader_Autoloader::getInstance();
$autoLoader->registerNamespace('Doctrine')
           ->pushAutoloader(array('Doctrine', 'autoload'))
           ->registerNamespace('FansubCMS')
           ->registerNamespace('BaseModel');

require_once('functions.inc.php');


$translate = new Zend_Translate_Adapter_Ini(APPLICATION_PATH . '/locale/en.ini');
$translate->addTranslation(APPLICATION_PATH . '/modules/user/locale/en.ini');

Zend_Registry::set('Zend_Translate',$translate);

$view = new Zend_View;
$view->setScriptPath('views');
?>
