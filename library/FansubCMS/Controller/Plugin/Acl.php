<?php
/*
 *  This file is part of FansubCMS.
 *
 *  FansubCMS is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  FansubCMS is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with FansubCMS.  If not, see <http://www.gnu.org/licenses/>
 */

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
            $request->setModuleName('cms');
        }
    }
}