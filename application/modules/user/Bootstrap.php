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

class User_Bootstrap extends FansubCMS_Application_Module_Bootstrap
{
    protected $_acl;
    
    /**
     * init acl
     * @return void
     */
    protected function _initAcl()
    {
        Zend_Auth::getInstance()->setStorage(new FansubCMS_Auth_Storage_DoctrineSession());
        $cm = Zend_Registry::get('Zend_Cache_Manager');
        $cache = $cm->getCache('FansubCMS');
        $config = $cache->load('Acl_Settings');
        if (!$config) {
            $config = array();
            $modules = glob(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'module.ini');
            foreach ($modules as $module) {
                $cleanName = str_replace(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR, '', $module);
                $cleanName = str_replace(DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'module.ini', '', $cleanName);

                try {
                    $ini = new Zend_Config_Ini($module, 'acl');
                    $config[$cleanName] = $ini->toArray();
                } catch (Zend_Config_Exception $e) {
                    // there is just no config or no acl block
                }
            }
            $cache->save($config);
        }
        $acl = new FansubCMS_Acl();
        foreach ($config as $options) {
            $acl->setOptions($options);
        }
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $ident = Zend_Auth::getInstance()->getIdentity();

            $role = new Zend_Acl_Role('fansubcms_user_custom_role_logged_in_user');
            $inherit = $ident->getRoles();

            foreach ($inherit as $key => $value) {
                if (!$acl->hasRole($value)) {
                    unset($inherit[$key]);
                }
            }

            $acl->addRole($role, $inherit);
        }
        Zend_Registry::set('Zend_Acl', $acl);
        $this->_acl = $acl;
    }
    
    protected function _initPlugins()
    {
        $this->bootstrap('module');
        # not-logged-in-so-go-to-login plugin
        $aclPlugin = new FansubCMS_Controller_Plugin_Acl(Zend_Auth::getInstance(), $this->_acl);
        $this->frontController->registerPlugin($aclPlugin);
    }
}