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

class News_AdminController extends FansubCMS_Controller_Action {
    public function indexAction() {
        $table = Doctrine_Core::getTable('News_Model_News');
        $this->view->news = $table->getPaginator('all');
        $page = $this->getRequest()->getParam('page');
        $this->view->news->setItemCountPerPage(25);
        $this->view->news->setCurrentPageNumber($page);
        if($this->acl->isAllowed($this->defaultUseRole, 'news_admin', 'comments'))
            $this->session->tableActions['news_comments'] = array('module' => 'news', 'controller' => 'admin', 'action' => 'comments');
        if($this->acl->isAllowed($this->defaultUseRole, 'news_admin', 'edit'))
            $this->session->tableActions['news_edit'] = array('module' => 'news', 'controller' => 'admin', 'action' => 'edit');
        if($this->acl->isAllowed($this->defaultUseRole, 'news_admin', 'delete'))
            $this->session->tableActions['news_delete'] = array('module' => 'news', 'controller' => 'admin', 'action' => 'delete');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $table = Doctrine_Core::getTable('News_Model_News');
        $n = $table->findOneBy('id', $id ? $id : 0);
        if(!$n) {
            $this->session->message = $this->translate('news_not_existent');
            $this->_helper->redirector->gotoRoute(array('action'=>'index','controller'=>'admin','module'=>'news'),'news');
        }
        $this->view->form = new News_Form_EditNews($n->toArray());
        $req = $this->getRequest();
        if($req->isPost()) { // there are profile updates
            if($this->view->form->isValid($_POST)) {
                $values = $this->view->form->getValues();
                $n->title = $values['title'];
                $n->text = $values['text'];
                $public = $values['public'] == 'yes' ? 'yes' : 'no';
                $n->public = $public;
                $n->save();
                $this->_helper->redirector->gotoRoute(array('action'=>'index','controller'=>'admin','module'=>'news'),'news');
            } else {
                $this->view->message = $this->translate('news_admin_edit_failed');
            }
        }
    }

    public function spamAction()
    {
        $table = Doctrine::getTable('News_Model_Comment');
        $this->view->spam = $table->getSpamPaginator();
    }

    public function deleteAction()
    {
        $id = $this->request->getParam('id');
        $table = Doctrine_Core::getTable('News_Model_News');
        if($id) {
            $news = $table->find($id);
            $this->view->form = new FansubCMS_Form_Confirmation();
            if($this->request->getParam('yes') && $news) {
                $news->delete();
                $this->session->message = $this->translate('news_admin_delete_success');
                $this->_helper->redirector->gotoRoute(array('action'=>'index','controller'=>'admin','module'=>'news'),'news');
            } else if($this->request->getParam('no')) {
                $this->_helper->redirector->gotoRoute(array('action'=>'index','controller'=>'admin','module'=>'news'),'news');
            }
        } else {
            $this->session->message = $this->translate('news_not_existent');
            $this->_helper->redirector->gotoRoute(array('action'=>'index','controller'=>'admin','module'=>'news'),'news');
        }
    }

    public function addAction()
    {
        $this->view->form = new News_Form_EditNews(null, true);
        $req = $this->getRequest();
        if($req->isPost()) { // there are profile updates
            if($this->view->form->isValid($_POST)) {
                $values = $this->view->form->getValues();
                $n = new News_Model_News;
                $n->title = $values['title'];
                $n->text = $values['text'];
                $public = $values['public'] == 'yes' ? 'yes' : 'no';
                $n->public = $public;
                $n->user_id = Zend_Auth::getInstance()->getIdentity()->id;
                $n->save();
                $this->session->message = $this->translate('news_admin_add_success');
                $this->_helper->redirector->gotoRoute(array('action'=>'index','controller'=>'admin','module'=>'news'),'news');
                $this->view->form = new News_Form_EditNews(null,true);
            } else {
                $this->view->message = $this->translate('news_admin_add_failed');
            }
        }
    }

    public function commentsAction()
    {
        $this->view->role = $this->defaultUseRole;
        $id = $this->getRequest()->getParam('id');
        $table = Doctrine_Core::getTable('News_Model_News');
        $n = $table->findOneBy('id', $id ? $id : 0);
        if(!$n) {
            $this->session->message = $this->translate('news_not_existent');
            $this->_helper->redirector->gotoRoute(array('action'=>'index','controller'=>'admin','module'=>'news'),'news');
        }
        $this->view->comments = $n->getCommentPaginator(false);
        $page = $this->getRequest()->getParam('page');
        $this->view->comments->setItemCountPerPage(25);
        $this->view->comments->setCurrentPageNumber($page); 
    }

public function markhamAction()
    {
        $id = $this->request->getParam('id');
        $table = Doctrine_Core::getTable('News_Model_Comment');
        if($id) {
            $comment = $table->find($id);
            $this->view->form = new FansubCMS_Form_Confirmation();
            if($this->request->getParam('yes') && $comment) {
                $a = new FansubCMS_Validator_Akismet();
                $a = $a->getAkismet();
                $a->setCommentAuthor($comment->author);
                $a->setCommentAuthorEmail($comment->email);
                $a->setCommentAuthorURL($comment->url);
                $a->setCommentContent($comment->comment);
                $a->setUserIP($comment->ip);
                $a->submitHam();
                $comment->spam = 'no';
                $comment->visible = 'yes';
                $comment->save();
                $this->session->message = $this->translate('news_admin_mark_ham_success');
                $this->_helper->redirector->gotoRoute(array('action'=>'spam','controller'=>'admin','module'=>'news'),'news');
            } else if($this->request->getParam('no')) {
                $this->_helper->redirector->gotoRoute(array('action'=>'spam','controller'=>'admin','module'=>'news'),'news');
            }
        } else {
            $this->session->message = $this->translate('news_comment_not_existent');
            $this->_helper->redirector->gotoRoute(array('action'=>'spam','controller'=>'admin','module'=>'news'),'news');
        }
    }
    
    public function markspamAction()
    {
        $id = $this->request->getParam('id');
        $table = Doctrine_Core::getTable('News_Model_Comment');
        if($id) {
            $comment = $table->find($id);
            $this->view->form = new FansubCMS_Form_Confirmation();
            if($this->request->getParam('yes') && $comment) {
                $a = new FansubCMS_Validator_Akismet();
                $a = $a->getAkismet();
                $a->setCommentAuthor($comment->author);
                $a->setCommentAuthorEmail($comment->email);
                $a->setCommentAuthorURL($comment->url);
                $a->setCommentContent($comment->comment);
                $a->setUserIP($comment->ip);
                $a->submitSpam();
                $comment->spam = 'yes';
                $comment->visible = 'yes';
                $comment->save();
                $this->session->message = $this->translate('news_admin_mark_spam_success');
                $this->_helper->redirector->gotoRoute(array('action'=>'comments','controller'=>'admin','module'=>'news','id'=>$comment->news_id),'news');
            } else if($this->request->getParam('no')) {
                $this->_helper->redirector->gotoRoute(array('action'=>'comments','controller'=>'admin','module'=>'news','id'=>$comment->news_id),'news');
            }
        } else {
            $this->session->message = $this->translate('news_comment_not_existent');
            $this->_helper->redirector->gotoRoute(array('action'=>'index','controller'=>'admin','module'=>'news'),'news');
        }
    }

    public function deletecommentAction()
    {
        $id = $this->request->getParam('id');
        $table = Doctrine_Core::getTable('News_Model_Comment');
        if($id) {
            $comment = $table->find($id);
            $spam = $comment->spam == 'yes' ? true : false;
            $this->view->form = new FansubCMS_Form_Confirmation();
            if($this->request->getParam('yes') && $comment) {
                $comment->delete();
                $this->session->message = $this->translate('news_admin_deletecomment_success');
                $spam ? $this->_helper->redirector->gotoRoute(array('action'=>'spam','controller'=>'admin','module'=>'news'),'news') : $this->_helper->redirector->gotoRoute(array('action'=>'comments','controller'=>'admin','module'=>'news', 'id' => $id),'news');
            } else if($this->request->getParam('no')) {
                $spam ? $this->_helper->redirector->gotoRoute(array('action'=>'spam','controller'=>'admin','module'=>'news'),'news') : $this->_helper->redirector->gotoRoute(array('action'=>'comments','controller'=>'admin','module'=>'news', 'id' => $id),'news');
            }
        } else {
            $this->session->message = $this->translate('news_comment_not_existent');
            $this->_helper->redirector->gotoRoute(array('action'=>'index','controller'=>'admin','module'=>'news'),'news');
        }
    }
}