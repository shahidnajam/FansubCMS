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

class Projects_IndexController extends FansubCMS_Controller_Action {

    public function init() {
        parent::init();
        $projectsTable = Doctrine::getTable('Project');
        $this->view->quickSelect = $projectsTable->getArrayListing();
    }

    public function indexAction() {
        $this->_addDelegateType('Sorting');
        $projectsTable = Doctrine::getTable('Project');
        $projects = $projectsTable->getFrontListing();
        $this->view->projects = $this->invokeDelegate('Sorting', 'sortProjects', array($projects));
    }

    public function detailsAction() {
        $name = urldecode($this->getRequest()->getParam('project'));
        $project = Doctrine::getTable('Project');
        $this->view->projectName = $name;
        $this->view->project = $project->findOneBy('name', $name);
    }
}