<?php
require_once('common.inc.php');
if(!$_SESSION['update']) header('Location: index.php');
initDatabase();
Doctrine::loadModels(realpath(APPLICATION_PATH.'/models/generated'));

$form = new FansubCMS_Form_Confirmation();
$view->form = $form->render($view);

$changes = new Doctrine_Migration_Diff(realpath(APPLICATION_PATH.'/models'), realpath(APPLICATION_PATH.'/resource/schema'), realpath(APPLICATION_PATH.'/resource/migrations'));
$view->changes = $changes->generateChanges();

if(count($_POST)) {
    if(!empty($_POST['yes'])) {
        Doctrine_Core::generateMigrationsFromDiff(
                realpath(APPLICATION_PATH.'/resource/migrations'),
                realpath(APPLICATION_PATH.'/models'),
                realpath(APPLICATION_PATH.'/resource/schema')
        );
    }
    header('Location: update.php');
}



echo $view->render('update1.phtml');