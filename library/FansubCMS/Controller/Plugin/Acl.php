<?php
class FansubCMS_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{

    protected $_auth = null;
    protected $_acl;
    protected $_session;

    public function __construct(Zend_Auth $auth, Zend_Acl $acl)
    {
        $this->_auth = $auth;
        $this->_acl = $acl;
        $this->_session = Zend_Registry::get('applicationSessionNamespace');
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if($this->_auth->hasIdentity()) {
            $ident = $this->_auth->getIdentity();
            $date = new Zend_Date();
            $ident->last_login = $date->get(DATABASE_DATE_FORMAT);
            $ident->save();
        } 
        if($request->getControllerName() != 'admin' && $request->getModuleName() != 'admin') return; // if this is not admin skip the rest
        if(!$this->_auth->hasIdentity() && !($request->getControllerName() == 'auth' && $request->getActionName() == 'login' && $request->getModuleName() == 'admin')) {
            $redirect = new Zend_Controller_Action_Helper_Redirector();
            $redirect->gotoSimple('login','auth','admin');
        }
        if($request->getModuleName() == 'user' && $request->getControllerName() == 'admin' && $request->getActionName() == 'profile') return; // the profile is a free resource
        $resource = $request->getModuleName().'_'.$request->getControllerName();
        try {
            $this->_acl->get($resource);
            $hasResource = true;
        } catch (Zend_Acl_Exception $e) {
            $hasResource = false;
        }
        if($hasResource && !$this->_acl->isAllowed('fansubcms_user_custom_role_logged_in_user',$resource, $request->getActionName())) {
            $request->setActionName('denied');
            $request->setControllerName('error');
            $request->setModuleName('default');
        }
    }
}