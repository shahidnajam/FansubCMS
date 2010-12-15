<?php
class Group_TeamController extends FansubCMS_Controller_Action {
    public function memberAction() {
        $this->view->title = $this->translate('group_member_title');
        $this->view->users = User::getTeam(false);
    }

    public function detailAction() {
        $username = $this->request->getParam('username',false);
        $table = Doctrine::getTable('User');
        $this->view->user = $table->getTeamMemberByName($username);
    }
}