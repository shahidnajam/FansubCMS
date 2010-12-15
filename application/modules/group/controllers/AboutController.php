<?php
class Group_AboutController extends FansubCMS_Controller_Action {
    public function indexAction() {
        $this->view->title = $this->translate('group_about_title');
    }
}