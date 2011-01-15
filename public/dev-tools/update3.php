<?php
require_once('common.inc.php');
if(!function_exists('passthru')) {
    die('The passthru() function is disabled on your system. Cannot run backup.<br /><strong>Please use phpMyAdmin or a similar tool to backup!</strong>');
}
$config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/database.ini','database');

if($config->db->dbms != 'mysql') {
    die('Sorry, the backup function does only work with mysql so far.');
}

$command = "mysqldump --opt -h ".$config->db->host." -u ".$config->db->user." -p ".$config->db->password." ".$config->db->database ;
passthru($command, $gzip);
if($gzip == 1) {
    die('It seems you can not run the mysqldump binary.<br /><strong>Backup failed. Please use phpMyAdmin or a similar tool to backup!</strong>');
}
header('Content-Type: text/sql');
header('Content-Length: '.strlen($gzip));
header('Content-Disposition: inline; filename='.$config->db->database.'-'.time().'.sql');
echo $gzip;