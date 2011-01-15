<?php
require_once('common.inc.php');
if(!$_SESSION['update']) header('Location: index.php');
initDatabase();
Doctrine::loadModels(realpath(APPLICATION_PATH.'/models/generated'));
$migration = new Doctrine_Migration(realpath(APPLICATION_PATH . '/resource/migrations'));
if($migration->getCurrentVersion() > $migration->getLatestVersion()) {
    die("Something's wrong. The Database is newer than your migrations!");
} else if($migration->getCurrentVersion() == $migration->getLatestVersion()) {
    die("Nothing to do. Migrations and Database are at the same version.");
}


$form = new FansubCMS_Form_Confirmation();
$view->form = $form->render($view);

if(count($_POST)) {
    if(!empty($_POST['yes'])) {
        // actually migrate!
        $migration->migrate();
        generateModels();
    }
    header('update.php');
}

echo $view->render('update2.phtml');