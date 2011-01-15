<?php
require_once('common.inc.php');

$status = new stdClass;
if(file_exists(APPLICATION_PATH.'/configs/database.ini')) {
    $status->database = true;
}
if(file_exists(APPLICATION_PATH.'/configs/environment.ini')) {
    $status->environment = true;
}
if(file_exists(APPLICATION_PATH.'/configs/email.ini')) {
    $status->email = true;
}
if(is_writable(realpath(APPLICATION_PATH.'/data/cache'))) {
    $status->cache = true;
}
if(is_writable(realpath(APPLICATION_PATH.'/data/sessions'))) {
    $status->sessions = true;
}
if(is_writable(realpath(realpath(APPLICATION_PATH.'/resource/').DIRECTORY_SEPARATOR.'migrations'))) {
    $status->migrations = true;
}
if(is_writable(realpath(realpath(APPLICATION_PATH.'/models/generated')))) {
    $status->models_base = true;
}
if(is_writable(realpath(APPLICATION_PATH.'/data/cache'))) {
    $status->cache = true;
}
if(is_writable(realpath(realpath(getenv('PHP_SELF')).'/../images/captcha'))) {
    $status->captcha = true;
}
if(is_writable(realpath(realpath(getenv('PHP_SELF')).'/../upload'))) {
    $status->upload = true;
}
if(!empty($status->database)) {
    try {
        initDatabase();
        $status->dbCon = true;
    } catch(Doctrine_Manager_Exception $e) {
        $status->dbCon = $e->getMessage();
    }
} else {
    $status->dbCon = 'The database configuration is missing.';
}
foreach($status as $key => $val) {
    if($key == 'migrations') continue; // we'll only need to check for updates
    if($val !== true) {
        $canDoAnything = false;
    }
}
$view->status = $status;
$view->canUpdate = true;
$_SESSION['update'] = true;
if(APPLICATION_ENV == 'development') {
    $_SESSION['update'] = true;
}

echo $view->render('index.phtml');
?>
