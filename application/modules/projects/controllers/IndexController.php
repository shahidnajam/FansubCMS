<?php
class Projects_IndexController extends FansubCMS_Controller_Action {

    public function init() {
        parent::init();
        $projectsTable = Doctrine::getTable('Project');
        $this->view->quickSelect = $projectsTable->getArrayListing();
    }

    public function indexAction() {
        $projectsTable = Doctrine::getTable('Project');
        $projects = $projectsTable->getFrontListing();
        $this->view->projects = $projects;
    }

    public function detailsAction() {
        $name = urldecode($this->getRequest()->getParam('project'));
        $project = Doctrine::getTable('Project');
        $this->view->projectName = $name;
        $this->view->project = $project = $project->findOneBy('name', $name);
    }
}