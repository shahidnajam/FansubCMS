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
 */
class NewsTable extends Doctrine_Table {
    /**
     * returns a Zend_Paginator_Object
     * @see Zend_Paginator
     * @param mixed $type true public false non-public everything else all
     * @return Zend_Paginator
     */
    public function getPaginator($type) {
        $q = $this->createQuery();
        $q->from('News n')
          ->orderBy('n.created_at DESC');

        if($type === true) {
            $q->where('n.public = ?','yes');
        } else if($type === false) {
            $q->where('n.public = ?','no');
        }
        $adapter = new FansubCMS_Paginator_Adapter_Doctrine($q);
        return new Zend_Paginator($adapter);
    }
}