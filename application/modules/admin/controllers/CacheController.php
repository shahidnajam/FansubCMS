<?php
class Admin_CacheController extends FansubCMS_Controller_Action {
    public function flushAction() {
        $cm = Zend_Registry::get('Zend_Cache_Manager');
        $cache = $cm->getCache('FansubCMS');
        $cache->clean(Zend_Cache::CLEANING_MODE_ALL) ?
                $this->view->message = $this->translate('admin-cache-flushed-message') :
                $this->view->message = $this->translate('admin-cache-flushed-error');
        return  $this->_helper->redirector->gotoRoute(array('action'=>'index','controller'=>'index','module'=>'admin'),'default');
    }
}