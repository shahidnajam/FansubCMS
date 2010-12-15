<?php
class Admin_AuthController extends FansubCMS_Controller_Action {
    public function loginAction() {
        # we don't need the admin menu
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('frontend');
        # we won't show gadgets while logging in
        $this->view->gadgets = '';
        # actually do the login stuff
        $form = new Admin_Form_Login();
        $req = $this->getRequest();
        $this->view->form = $form;
        if($req->isPost()) {
            if($form->isValid($_POST)) {
                $values = $form->getValues();
                if(User::login($values['username'],$values['password'])->isValid()) {
                    return $this->_helper->redirector('index','index','admin');
                }
           }
        }
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('login');
        die;
    }
}