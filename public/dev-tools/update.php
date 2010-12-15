<?php
require_once('common.inc.php');

initDatabase();

$migration = new Doctrine_Migration(realpath(realpath(APPLICATION_PATH.'/resource/migrations')));
$view->currentVersion = $migration->getCurrentVersion();
$view->latestVersion = $migration->getLatestVersion();


// check if the yaml has changed against the models!
$view->yamlStatus = true;

Doctrine::loadModels(realpath(APPLICATION_PATH.'/models/generated'));
$changes = new Doctrine_Migration_Diff(realpath(APPLICATION_PATH.'/models'), realpath(APPLICATION_PATH.'/resource/schema'), realpath(APPLICATION_PATH.'/resource/migrations'));
$changes = $changes->generateChanges();
foreach($changes as $changeset) {
    if(count($changeset)) $view->yamlStatus = false;
}

echo $view->render('update.phtml');
