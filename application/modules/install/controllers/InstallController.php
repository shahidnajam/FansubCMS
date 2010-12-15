<?php
class Install_InstallController extends FansubCMS_Controller_Action {
    public function init() {
        $envConf = Zend_Registry::get('environmentSettings');
        if(!$envConf->install && APPLICATION_ENV != 'development') die('locked!');
        $this->_helper->layout()->disableLayout(); // no layout in the installer
    }

    public function indexAction() {
        $t = Doctrine::getTable('User');
        try {
            $t->count();
            $this->_helper->redirector->gotoSimple('createuser','install','install');
        } catch(Doctrine_Exception $e) {
            $this->view->form = new FansubCMS_Form_Confirmation;
            if($this->request->isPost()) {
                $submit = $this->request->getParam('yes');
                if(!empty($submit)) {
                    Doctrine::createTablesFromModels();
                    Install_Model_Api_Migration::getInstance()->setCurrentVersion(Install_Model_Api_Migration::getInstance()->getLatestVersion()); // we are on the top atm
                    $this->_helper->redirector->gotoSimple('createuser','install','install');
                } else {
                    $this->_helper->redirector->gotoSimple('index','index','install');
                }
            }
        }
    }

    public function createuserAction() {
        $t = Doctrine::getTable('User');
        try {
            $c = $t->count();
            if($c > 0) {
                $this->_helper->redirector->gotoSimple('success','install','install');
            } else {
                $this->view->form = new Install_Form_InstallUser;
                if($this->request->isPost()) {
                    if($this->view->form->isValid($_POST)) {
                        $values = $this->view->form->getValues();
                        $user = new User;
                        $user->name = $values['username'];
                        $user->setPassword($values['password1']);
                        $user->email = $values['email'];
                        $user->save();
                        $ur = new UserRole();
                        $ur->User = $user;
                        $ur->role_name = 'admin_admin';
                        $ur->save();
                        $this->_helper->redirector->gotoSimple('success','install','install');
                    }
                }
            }
        } catch(Doctrine_Exception $e) {
            $this->_helper->redirector->gotoSimple('index','install','install');
        }
    }

    public function successAction() {
        $t = Doctrine::getTable('User');
        try {
            $c = $t->count();
            if($c < 1) {
                $this->_helper->redirector->gotoSimple('createuser','install','install');
            }
        } catch(Doctrine_Exception $e) {
            $this->_helper->redirector->gotoSimple('index','install','install');
        }
    }
}