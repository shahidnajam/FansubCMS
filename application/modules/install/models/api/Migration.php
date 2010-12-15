<?php
class Install_Model_Api_Migration {
    private static $_instance;
    private $_migration;

    /**
     * returns an instance of Install_Api_Migration
     * 
     * @return Install_Api_Migration
     */
    public static function getInstance() {
        if(empty(self::$_instance)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    private function __construct() {
        $this->_migration = new Doctrine_Migration(realpath(APPLICATION_PATH.'/resource/migrations'));
    }

    /**
     * Get the current version of the database
     *
     * @return integer $version
     */
    public function getCurrentVersion() {
        return $this->_migration->getCurrentVersion();
    }

    /**
     * Gets the latest possible version from the loaded migration classes
     *
     * @return integer $latestVersion
     */
    public function getLatestVersion() {
        return $this->_migration->getLatestVersion();
    }

    /**
     * Run the migration process but rollback at the very end. Returns true or
     * false for whether or not the migration can be ran
     *
     * @param  string  $to
     * @return boolean $success
     */
    public function migrateDryRun($to = null) {
        return $this->_migration->migrateDryRun($to);
    }

    /**
     * Perform a migration process by specifying the migration number/version to
     * migrate to. It will automatically know whether you are migrating up or down
     * based on the current version of the database.
     *
     * @param  integer $to       Version to migrate to
     * @param  boolean $dryRun   Whether or not to run the migrate process as a dry run
     * @return integer $to       Version number migrated to
     * @throws Doctrine_Exception
     */
    public function migrate($to = null, $dryRun = false) {
        return $this->_migration->migrate($to, $dryRun);
    }

    /**
     * Set the current version of the database
     *
     * @param integer $number
     * @return void
     */
    public function setCurrentVersion($number) {
        $this->_migration->setCurrentVersion($number);
    }

    /**
     * returns the migration object
     *
     * @return Doctrine_Migration
     */
    public function getMigrationObject() {
        return $this->_migration;
    }
}