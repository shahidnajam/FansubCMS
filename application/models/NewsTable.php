<?php
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