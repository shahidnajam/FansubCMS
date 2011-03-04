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

class Projects_AdminController extends FansubCMS_Controller_Action 
{
    public function indexAction() 
    {
        $this->view->pageTitle = $this->translate('project_list_headline');
        $table = Doctrine_Core::getTable('Projects_Model_Project');
        $this->view->projects = $table->findAll()->toArray();
        if($this->acl->isAllowed($this->defaultUseRole, 'projects_admin', 'edit'))
            $this->session->tableActions['project_edit'] = array('module' => 'projects', 'controller' => 'admin', 'action' => 'edit');
        if($this->acl->isAllowed($this->defaultUseRole, 'projects_admin', 'episodes'))
            $this->session->tableActions['project_episodes'] = array('module' => 'projects', 'controller' => 'admin', 'action' => 'episodes');
        if($this->acl->isAllowed($this->defaultUseRole, 'projects_admin', 'screenshots'))
            $this->session->tableActions['project_screenshots'] = array('module' => 'projects', 'controller' => 'admin', 'action' => 'screenshots');
        if($this->acl->isAllowed($this->defaultUseRole, 'projects_admin', 'delete'))
            $this->session->tableActions['project_delete'] = array('module' => 'projects', 'controller' => 'admin', 'action' => 'delete');
    }

    public function addAction() 
    {
        $this->view->pageTitle = $this->translate('project_add_headline');
        $this->view->form = new Projects_Form_EditProject(array(), true);
        $req = $this->getRequest();
        if($req->isPost()) { // there are profile updates
            if($this->view->form->isValid($_POST)) {
                $values = $this->view->form->getValues();
                $p = new Projects_Model_Project;
                $p->updateProject($values);
                $this->session->message = $this->translate('project_admin_add_success');
                $this->_helper->redirector->gotoSimple('index','admin','projects');
            } else {
                $this->view->message = $this->translate('project_admin_add_failed');
            }
        }
    }

    public function deleteAction() 
    {
        $this->view->pageTitle = $this->translate('project_delete_headline');
        $id = $this->request->getParam('id');
        $table = Doctrine_Core::getTable('Projects_Model_Project');
        if($id) {
            $p = $table->find($id);
            $this->view->form = new FansubCMS_Form_Confirmation();
            $this->view->confirmation = sprintf($this->translate('project_admin_delete_confirmation'),$p->name);
            if($this->request->getParam('yes') && $p) {
                $p->delete();
                $this->session->message = $this->translate('project_admin_delete_success');
                $this->_helper->redirector->gotoSimple('index', 'admin', 'projects');
            } else if($this->request->getParam('no')) {
                $this->_helper->redirector->gotoSimple('index','admin','projects');
            }
        } else {
            $this->session->message = $this->translate('project_not_existent');
            $this->_helper->redirector->gotoSimple('index','admin','projects');
        }
    }

    public function editAction() 
    {
        $this->view->pageTitle = $this->translate('project_edit_headline');
        $id = $this->getRequest()->getParam('id');
        $table = Doctrine_Core::getTable('Projects_Model_Project');
        $p = $table->findOneBy('id', $id ? $id : 0);
        if(!$p) {
            $this->session->message = $this->translate('project_not_existent');
            $this->_helper->redirector->gotoSimple('index','admin','projects');
        }
        $this->view->form = new Projects_Form_EditProject($p->toArray());
        $req = $this->getRequest();
        if($req->isPost()) { // there are profile updates
            if($this->view->form->isValid($_POST)) {
                $values = $this->view->form->getValues();
                $p->updateProject($values);
                $this->session->message = $this->translate('project_admin_edit_success');
                $this->session->message_type = 'message';
                $this->_helper->redirector->gotoSimple('index','admin','projects');
            } else {
                $this->view->message = $this->translate('project_admin_edit_failed');
                $this->session->message_type = 'type';
            }
        }
    }

    public function episodesAction() 
    {
        $id = $this->request->getParam('id');

        if(!empty($id)) {
            $project = Doctrine_Query::create()->from('Projects_Model_Project p')->where('p.id = ?', $id)->fetchOne();
            $this->view->pageTitle = sprintf($this->translate('project_admin_episodes_headline'),$project->name);
            $this->view->all = false;
        } else {
            $this->view->pageTitle = sprintf($this->translate('project_admin_episodes_headline_2'));
            $this->view->all = true;
        }

        $table = Doctrine_Core::getTable('Projects_Model_Episode');
        $paginator = $table->getPaginator(empty($id) ? null : $id);
        if(!$paginator) {
            $this->_helper->redirector->gotoSimple('index','admin','projects');
        }
        $this->view->episodes = $paginator;
        $page = $this->getRequest()->getParam('page');
        $this->view->episodes->setItemCountPerPage(25);
        $this->view->episodes->setCurrentPageNumber($page);
        if($this->acl->isAllowed($this->defaultUseRole, 'projects_admin', 'editepisode'))
            $this->session->tableActions['project_edit_episode'] = array('module' => 'projects', 'controller' => 'admin', 'action' => 'editepisode');
        if($this->acl->isAllowed($this->defaultUseRole, 'projects_admin', 'deleteepisode'))
            $this->session->tableActions['project_delete_episode'] = array('module' => 'projects', 'controller' => 'admin', 'action' => 'deleteepisode');
    }

    public function addepisodeAction() 
    {
        $this->view->pageTitle = $this->translate('project_addepisode_headline');
        $this->view->form = new Projects_Form_EditProjectEpisode(array(), true);
        $req = $this->getRequest();
        if($req->isPost()) { // there are profile updates
            if($this->view->form->isValid($_POST)) {
                $values = $this->view->form->getValues();
                $p = new Projects_Model_Episode();
                $p->updateEpisode($values);
                $this->session->message = $this->translate('project_admin_addepisode_success');
                $this->_helper->redirector->gotoSimple('episodes','admin','projects');
            } else {
                $this->view->message = $this->translate('project_admin_addepisode_failed');
            }
        }
    }

    public function deleteepisodeAction() 
    {
        $this->view->pageTitle = $this->translate('project_deleteepisode_headline');
        $id = $this->request->getParam('id');
        $table = Doctrine_Core::getTable('Projects_Model_Episode');
        if($id) {
            $p = $table->find($id);
            $this->view->form = new FansubCMS_Form_Confirmation();
            $this->view->confirmation = sprintf($this->translate('project_admin_deleteepisode_confirmation'),$p->title);
            if($this->request->getParam('yes') && $p) {
                $p->delete();
                $this->session->message = $this->translate('project_admin_deleteepisode_success');
                $this->_helper->redirector->gotoSimple('episodes', 'admin', 'projects');
            } else if($this->request->getParam('no')) {
                $this->_helper->redirector->gotoSimple('episodes','admin','projects');
            }
        } else {
            $this->session->message = $this->translate('project_episode_not_existent');
            $this->_helper->redirector->gotoSimple('episodes','admin','projects');
        }
    }

    public function editepisodeAction() 
    {
        $this->view->pageTitle = $this->translate('project_editepisode_headline');
        $id = $this->getRequest()->getParam('id');
        $table = Doctrine_Core::getTable('Projects_Model_Episode');
        $p = $table->findOneBy('id', $id ? $id : 0);
        if(!$p) {
            $this->session->message = $this->translate('project_episode_not_existent');
            $this->_helper->redirector->gotoSimple('episodes','admin','projects');
        }
        $this->view->form = new Projects_Form_EditProjectEpisode($p->toArray());
        $req = $this->getRequest();
        if($req->isPost()) { // there are profile updates
            if($this->view->form->isValid($_POST)) {
                $values = $this->view->form->getValues();
                $p->updateEpisode($values);
                $this->session->message = $this->translate('project_admin_editepisode_success');
                $this->_helper->redirector->gotoSimple('episodes','admin','projects');
            } else {
                $this->view->message = $this->translate('project_admin_editepisode_failed');
            }
        }
    }

    public function screenshotsAction() 
    {
        $this->view->pageTitle = $this->translate('project_screenshots_headline');
        $table = Doctrine_Core::getTable('Projects_Model_Screenshot');
        $this->view->screenshots = $table->getPaginator();
        $page = $this->getRequest()->getParam('page');
        $this->view->screenshots->setItemCountPerPage(25);
        $this->view->screenshots->setCurrentPageNumber($page);
        if($this->acl->isAllowed($this->defaultUseRole, 'projects_admin', 'editscreenshot'))
            $this->session->tableActions['project_edit_screenshot'] = array('module' => 'projects', 'controller' => 'admin', 'action' => 'editscreenshot');
        if($this->acl->isAllowed($this->defaultUseRole, 'projects_admin', 'deletescreenshot'))
            $this->session->tableActions['project_delete_screenshot'] = array('module' => 'projects', 'controller' => 'admin', 'action' => 'deletescreenshot');
    }

    public function addscreenshotAction() 
    {
        $this->view->pageTitle = $this->translate('project_addscreenshot_headline');
        $this->view->form = new Projects_Form_EditProjectScreenshot(array(), true);
        $req = $this->getRequest();
        if($req->isPost()) { // there are profile updates
            if($this->view->form->isValid($req->getPost())) {
                $values = $this->view->form->getValues();
                $values['file'] = $this->view->form->getElement('screen');
                $p = new Projects_Model_Screenshot();
                $p->updateScreenshot($values);
                $this->session->message = $this->translate('project_admin_addscreenshot_success');
                $this->session->message_type = 'message';
                $this->_helper->redirector->gotoSimple('screenshots','admin','projects');
            } else {
                $this->view->message = $this->translate('project_admin_addscreenshot_failed');
                $this->view->message_type = 'error';
            }
        }
    }

    public function deletescreenshotAction() 
    {
        $this->view->pageTitle = $this->translate('project_deletescreenshot_headline');
        $id = $this->request->getParam('id');
        $table = Doctrine_Core::getTable('Projects_Model_Screenshot');
        if($id) {
            $p = $table->find($id);
            $this->view->form = new FansubCMS_Form_Confirmation();
            $this->view->confirmation = sprintf($this->translate('project_admin_deletescreenshot_confirmation'), $p->Projects_Model_Project->name, $p->screenshot);
            if($this->request->getParam('yes') && $p) {
                $p->delete();
                $this->session->message = $this->translate('project_admin_deletescreenshot_success');
                $this->_helper->redirector->gotoSimple('screenshots', 'admin', 'projects');
            } else if($this->request->getParam('no')) {
                $this->_helper->redirector->gotoSimple('screenshots','admin','projects');
            }
        } else {
            $this->session->message = $this->translate('project_screenshot_not_existent');
            $this->_helper->redirector->gotoSimple('screenshots','admin','projects');
        }
    }

    public function editscreenshotAction() 
    {
        $this->view->pageTitle = $this->translate('project_editscreenshot_headline');
        $id = $this->getRequest()->getParam('id');
        $table = Doctrine_Core::getTable('Projects_Model_Screenshot');
        $p = $table->findOneBy('id', $id ? $id : 0);
        if(!$p) {
            $this->session->message = $this->translate('project_screenshot_not_existent');
            $this->_helper->redirector->gotoSimple('screenshots','admin','projects');
        }
        $this->view->form = new Projects_Form_EditProjectScreenshot($p->toArray());
        $req = $this->getRequest();
        if($req->isPost()) { // there are profile updates
            if($this->view->form->isValid($_POST)) {
                $values = $this->view->form->getValues();
                $values['file'] = $this->view->form->getElement('screen');
                $p->updateScreenshot($values);
                $this->session->message = $this->translate('project_admin_editscreenshot_success');
                $this->_helper->redirector->gotoSimple('screenshots','admin','projects');
            } else {
                $this->view->message = $this->translate('project_admin_editscreenshot_failed');
            }
        }
    }
}