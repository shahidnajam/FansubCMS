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
class Gadgets_ProjectController extends FansubCMS_Controller_Action
{
    public function randomAction ()
    {
        $num = $this->getRequest()->getParam('num', 5);
        $pt = Doctrine::getTable('Projects_Model_Project');
        $projects = $pt->createQuery()
            ->where('private = ?', 'no')
            ->orderBy('RANDOM()')
            ->limit($num)
            ->execute();
        $this->view->random = $projects;
    }
    public function latestAction ()
    {
        $num = $this->getRequest()->getParam('num', 5);
        $pet = Doctrine::getTable('Projects_Model_Episode');
        $q = $pet->buildQueryForListing('released_at DESC');
        $q->offset(0)
            ->limit($num)
            ->where('released_at IS NOT NULL');
        $this->view->latest = $q->execute();
    }
}
