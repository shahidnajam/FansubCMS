<?php
class Install_UpdateController extends FansubCMS_Controller_Action {
    public function init() {
        $envConf = Zend_Registry::get('environmentSettings');
        if(!$envConf->install && APPLICATION_ENV != 'development') die('locked!');
        $this->_helper->layout()->disableLayout(); // no layout in the installer
    }

    public function indexAction() {
        $this->view->form = new FansubCMS_Form_Confirmation;
        if($this->request->isPost()) {
            $submit = $this->request->getParam('yes');
            if(!empty($submit)) {
                if(Install_Model_Api_Migration::getInstance()->migrateDryRun()) {
                   $this->_helper->redirector->gotoSimple('migrate','update','install');
                } else {
                    $this->view->error = $this->translate('install_migrate_error_in_dry_run');
                }
                
            } else {
                $this->_helper->redirector->gotoSimple('index','index','install');
            }
        }
    }

    public function migrateAction() {
        $this->view->form = new FansubCMS_Form_Confirmation;
        if($this->request->isPost()) {
            $submit = $this->request->getParam('yes');
            if(!empty($submit)) {
                if(Install_Model_Api_Migration::getInstance()->migrate()) {
                   $this->_helper->redirector->gotoSimple('index','index','install');
                } else {
                    $this->view->error = $this->translate('install_migrate_error_in_migrate_run');
                }

            } else {
                $this->_helper->redirector->gotoSimple('index','index','install');
            }
        }
    }
}