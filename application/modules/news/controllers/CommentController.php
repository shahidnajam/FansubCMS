<?php
class CommentController extends FansubCMS_Controller_Action {
	public function init() {
		parent::init();
		$this->conf = Zend_Registry::get('environmentSettings');
	}
	
    public function indexAction() {
    	$req = $this->getRequest();
        $id = $req->getParam('id');
        $this->view->news = false;
        $this->view->paginator = false;
        $this->view->writeForm = new News_Form_Comment('#');
        if($id) {
            $this->view->news = News::getNewsById($id);
            
	        if($req->isPost() && $this->view->news) {
	            if($this->view->writeForm->isValid($_POST)) {
	                $values = $this->view->writeForm->getValues();
	                $nc = new NewsComment();
	                $nc->ip = getenv('REMOTE_ADDR');
	                $nc->email = $values['email'];
	                $nc->news_id = $this->view->news->id;
	                $nc->visible = 'yes'; // there is no moderation yet
	                $nc->author = $values['author'];
	                $nc->url = $values['url'];
	                $nc->comment = $values['comment'];
	                $nc->checkSpam();
	                $nc->save();
	                $this->view->writeForm = new News_Form_Comment('#'); // clear form because comment is submitted
	            }
	        }
            
            $paginator = $this->view->news->getCommentPaginator(false,true);
            $page = $req->getParam('page');
            $paginator->setItemCountPerPage($this->conf->news->comments->numpage);
            $paginator->setCurrentPageNumber($page);
            $this->view->paginator = $paginator;
        }
    }
}