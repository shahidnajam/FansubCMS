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
}