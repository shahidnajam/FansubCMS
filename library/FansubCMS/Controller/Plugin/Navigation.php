<?php
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
                $modConf = array_merge($modConf,glob(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'addons' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'configs'. DIRECTORY_SEPARATOR . 'module.ini'));
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