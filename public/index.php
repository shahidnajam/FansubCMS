<?php
// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

define('UPLOAD_PATH', realpath(dirname(__FILE__).'/upload'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
        realpath(APPLICATION_PATH . '/../library'),
        get_include_path(),
)));
/** Zend_Application */
require_once 'Zend/Application.php';  

// Create application, bootstrap, and run
$application = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini'
);

try {
    $application->bootstrap()
            ->run();
} catch(Exception $e) {
    if(!headers_sent()) {
        header('Content-Type: text/plain');
    }

    if(APPLICATON_ENV == 'production') {
        echo "A fatal system error has occured. Please contact an admistrator.";
    } else {
        echo "A fatal system error has occured. Please contact an administrator.\n\n";
        echo "Error information:\n";
        echo $e->getMessage() . "(Code " . $e->getCode() . ")\n";
        echo "File: " . $e->getFile() . ", Line: " . $e->getLine() . "\n\n";
        echo "Trace:\n";
        echo $e->getTraceAsString();
        echo "\n\n";
        echo "Extended Trace:\n";
        print_r($e->getTrace());
    }
}
// This fixes a bug with some PHP5 versions and Bytecode-Caches like APC that
// would cause a fatal error
function shutdownCMS() {
    Zend_Session::writeClose(true);
}
register_shutdown_function('shutdownCMS');
