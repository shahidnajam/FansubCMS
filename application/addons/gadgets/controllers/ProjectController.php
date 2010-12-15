<?php
class Gadgets_ProjectController extends FansubCMS_Controller_Action {
    public function randomAction() {
        $num = $this->getRequest()->getParam('num',50);
        $pt = Doctrine::getTable('Project');
        $projects = $pt->createQuery()
                       ->where('private = ?','no')
                       ->orderBy('RANDOM()')
                       ->limit($num)
                       ->execute();
        $this->view->random = $projects;
    }

    public function latestAction() {
        $num = $this->getRequest()->getParam('num',5);
        $pet = Doctrine::getTable('ProjectEpisode');
        $q = $pet->buildQueryForListing('released_at DESC');
        $q->offset(0)->limit($num);
        $q->where('pe.released_at IS NOT NULL');
        $this->view->latest = $q->execute();
    }
}
