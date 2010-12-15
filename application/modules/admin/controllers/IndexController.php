<?php
class Admin_IndexController extends FansubCMS_Controller_Action {
    public function indexAction() {
        foreach($_SERVER as $k => $v) {
            $server->$k = $v;
        }
        $this->view->system = $server;
        $this->view->isAdmin = Zend_Auth::getInstance()->getIdentity()->hasRole('admin_admin');
    }
}