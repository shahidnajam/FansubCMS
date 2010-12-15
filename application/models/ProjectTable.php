<?php
/**
 */
class ProjectTable extends Doctrine_Table {
    public function getQueryForListing($private=null) {
        $q = $this->createQuery();
        $q->from('Project p');
        if($private === true)
            $pub = 'yes';
        elseif($private === false)
            $pub = 'no';
        else
            $pub = '%';

        $q->where('p.private like ?',$pub);


        return $q;
    }

    public function getProjects() {
        $q = $this->createQuery();
        $q->from('Project p')
          ->orderBy('p.name ASC');
        $projects = $q->fetchArray();
        $proRet = array(''=>'pleasechoose');
        foreach($projects as $key => $project) {
            $proRet[$project['id']] = $project['name'];
        }
        return $proRet;
    }

    public function getFrontListing() {
        return $this->getQueryForListing(false,false)->orderBy('p.name ASC')->execute();
    }

    public function getArrayListing() {
        return $this->getQueryForListing(false,false)->orderBy('p.name ASC')->fetchArray();
    }
}