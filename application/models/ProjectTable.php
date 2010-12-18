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