<?php
/*
 * This file is part of FansubCMS.
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
/**
 * This controller should handle errors in the application.
 *
 * @package FansubCMS
 * @subpackage Controllers
 * @author Hikaru-Shindo <hikaru@animeownage.de>
 */
class Cms_AdminController extends FansubCMS_Controller_Action {

    public function liststaticAction() {
        if($this->acl->isAllowed($this->defaultUseRole, 'user_admin', 'editatic'))
            $this->session->tableActions['cms_edit_static'] = array('module' => 'cms', 'controller' => 'admin', 'action' => 'editstatic');
        if($this->acl->isAllowed($this->defaultUseRole, 'user_admin', 'deletestatic'))
            $this->session->tableActions['cms_delete_static'] = array('module' => 'cms', 'controller' => 'admin', 'action' => 'deletestatic');

        $pages = glob(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . '*.html');
        $pages = str_replace(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR, '', $pages);
        $pages = str_replace('.html','',$pages);

        $this->view->pages = $pages;
    }

    public function editstaticAction() {
        $this->session->markitup = 'html';
        $title = $this->request->getParam('title');
        $file = realpath(UPLOAD_PATH . '/static'). DIRECTORY_SEPARATOR . $title.'.html';
        if(file_exists($file)) {
            $this->view->form = new Cms_Form_EditStatic(array('text'=>@file_get_contents($file)));
            $req = $this->getRequest();
            if($req->isPost()) { // there are profile updates
                if($this->view->form->isValid($_POST)) {
                    $values = $this->view->form->getValues();
                    @unlink($file);
                    @file_put_contents($file, $values['text']);
                    $this->session->message = $this->translate('cms_admin_editstatic_success');
                    $this->_helper->redirector->gotoRoute(array('action'=>'liststatic','controller'=>'admin','module'=>'cms'));
                } else {
                    $this->view->message = $this->translate('cms_admin_editstatic_failed');
                }
            }
        } else {
            $this->session->message = $this->translate('cms_static_not_existent');
            $this->_helper->redirector->gotoRoute(array('action'=>'liststatic','controller'=>'admin','module'=>'cms'));
        }
    }

    public function addstaticAction() {
        $this->session->markitup = 'html';
        $this->view->form = new Cms_Form_EditStatic(array(),true);
        $req = $this->getRequest();
        if($req->isPost()) { // there are profile updates
            if($this->view->form->isValid($_POST)) {
                $values = $this->view->form->getValues();
                @file_put_contents(realpath(UPLOAD_PATH . '/static') . DIRECTORY_SEPARATOR . $values['title'] . '.html', $values['text']);
                $this->session->message = $this->translate('cms_admin_addstatic_success');
                $this->_helper->redirector->gotoRoute(array('action'=>'liststatic','controller'=>'admin','module'=>'cms'));
            } else {
                $this->view->message = $this->translate('cms_admin_addstatic_failed');
            }
        }
    }

    public function deletestaticAction() {
        $title = $this->request->getParam('title');
        $file = realpath(UPLOAD_PATH . '/static'). DIRECTORY_SEPARATOR . $title.'.html';
        if(file_exists($file)) {
            $this->view->confirmation = $this->translate('cms_admin_static_delete_confirmation',$title);
            $this->view->form = new FansubCMS_Form_Confirmation();
            if($this->request->getParam('yes')) {
                if(unlink($file)) {
                    $this->session->message = $this->translate('cms_admin_deletestatic_success');
                } else {
                    $this->session->message = $this->translate('cms_admin_deletestatic_failed');
                }
                $this->_helper->redirector->gotoRoute(array('action'=>'liststatic','controller'=>'admin','module'=>'cms'));
            }
        } else {
            $this->session->message = $this->translate('cms_static_not_existent');
            $this->_helper->redirector->gotoRoute(array('action'=>'liststatic','controller'=>'admin','module'=>'cms'));
        }
    }
}