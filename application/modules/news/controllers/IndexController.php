<?php
class IndexController extends FansubCMS_Controller_Action {
    public function init() {
        parent::init();
        $this->conf = Zend_Registry::get('environmentSettings');
    }

    public function indexAction() {
        $newsTable = Doctrine::getTable('News');
    	$this->view->news = $newsTable->getPaginator(true);
    	$page = $this->getRequest()->getParam('page');
        $this->view->news->setItemCountPerPage($this->conf->news->front);
        $this->view->news->setCurrentPageNumber($page);
    }

    public function showAction() {
        $id = $this->getRequest()->getParam('id');
        $this->view->news = false;
        if($id) {
            $this->view->news = News::getNewsById($id);
        }
    }
}