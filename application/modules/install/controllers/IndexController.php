<?php
class Install_IndexController extends FansubCMS_Controller_Action {
    public function init() {
        $envConf = Zend_Registry::get('environmentSettings');
        if(!$envConf->install && APPLICATION_ENV != 'development') die('locked!');
        $this->_helper->layout()->disableLayout(); // no layout in the installer
    }

    public function indexAction() {
        $migration = new Doctrine_Migration();
        $this->view->databaseCurrent = Install_Model_Api_Migration::getInstance()->getCurrentVersion();
        $this->view->databaseLatest = Install_Model_Api_Migration::getInstance()->getLatestVersion();

        if($this->view->databaseCurrent < $this->view->databaseLatest) {
            $this->view->databaseUpdateNeeded = true;
            $this->view->databaseInitNeeded = false;
        } else {
            $this->view->databaseUpdateNeeded = false;
            $t = Doctrine::getTable('User');
            try {
                $t->count();
                $this->view->databaseInitNeeded = false;
            } catch(Doctrine_Exception $e) {
                $this->view->databaseInitNeeded = true;
            }
        }

        $status = new stdClass;

        if(is_writable(realpath(APPLICATION_PATH.'/data/cache'))) {
            $status->cache = true;
        }
        if(is_writable(realpath(APPLICATION_PATH.'/data/sessions'))) {
            $status->sessions = true;
        }
        if(is_writable(realpath(realpath(getenv('PHP_SELF')).'/images/captcha'))) {
            $status->captcha = true;
        }
        if(is_writable(realpath(realpath(getenv('PHP_SELF')).'/upload'))) {
            $status->upload = true;
        }
        $this->view->requirements = $status;
    }
}