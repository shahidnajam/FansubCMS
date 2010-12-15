<?php
require_once('common.inc.php');

function generateModels() {
    // for devs! generates the models from yaml!
    $options = array(
            'generateBaseClasses'   =>  true,
            'generateTableClasses'  =>  true,
            'generateAccessors'     =>  false,
            'baseClassPrefix'     =>  'BaseModel',
            'baseClassesDirectory'  =>  'generated',
            'baseClassName'         =>  'Doctrine_Record',
            'phpDocName'          =>  'FansubCMS Developer',
            'phpDocPackage'         => 'FansubCMS',
            'phpDocSubpackage'         => 'Models'
    ); // options for doctrine
    Doctrine::generateModelsFromYaml(realpath(APPLICATION_PATH.'/resource/schema'),realpath(APPLICATION_PATH.'/models'),$options);
}

function initDatabase() {
    $config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/database.ini','database');
    if(empty($config->db->dsn)) {
        $dbDefaultDsn = $config->db->dbms . "://" .$config->db->user . ":" .  $config->db->password . "@" . $config->db->host . "/" . $config->db->database;
    } else {
        $dbDefaultDsn = $config->db->dsn;
    }
    $conn = Doctrine_Manager::connection($dbDefaultDsn, $config->db->database, "defaultConnection");
    $conn->execute("SET NAMES 'UTF8'");
    $conn->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);
}

?>