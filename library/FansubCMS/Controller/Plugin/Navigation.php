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

class FansubCMS_Controller_Plugin_Navigation extends Zend_Controller_Plugin_Abstract {
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $this->layout = Zend_Layout::getMvcInstance();
        $cm = Zend_Registry::get('Zend_Cache_Manager');
        $cache = $cm->getCache('FansubCMS');
        if(($request->getParam('module') == 'admin' || $request->getParam('controller') == 'admin') && Zend_Auth::getInstance()->hasIdentity()) {
            $navigation = $cache->load('Navigation_Backend');
            if(!$navigation) {
                // add the module and addon admin menus
                $modConf = glob(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'configs'. DIRECTORY_SEPARATOR . 'module.ini');
                $modConf = array_merge($modConf,$addConf = glob(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'addons' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'configs'. DIRECTORY_SEPARATOR . 'module.ini') ? $addConf : array());
                foreach($modConf as $nav) {
                    try {
                        $nav = new Zend_Config_Ini($nav,'adminnav',true);
                        if(isset($adminNav) && $adminNav instanceof Zend_Config_Ini)
                            $adminNav->merge($nav);
                        else
                            $adminNav = $nav;
                    } catch(Zend_Config_Exception $e) {
                        // do nothing on error, just ignore
                    }
                }
                 $navigation = new Zend_Navigation($adminNav);
                 $cache->save($navigation);
            }
        } else {
            $navigation = $cache->load('Navigation_Frontend');
            if(!$navigation) {
                $config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/navigation.ini','nav');
                $navigation = new Zend_Navigation($config);
                $cache->save($navigation);
            }
            if(Zend_Auth::getInstance()->hasIdentity()) {
                $adminPage = new Zend_Navigation_Page_Mvc();
                $adminPage->setLabel('administration');
                $adminPage->setModule('admin');
                $adminPage->setOrder(999999999);
                $adminPage->setRoute('default');
                $navigation->addPage($adminPage);
            }
        }
        $this->layout->getView()->navigation($navigation);
    }
}