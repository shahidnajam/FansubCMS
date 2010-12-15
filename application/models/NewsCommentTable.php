<?php
/**
 */
class NewsCommentTable extends Doctrine_Table {
    public function getSpamPaginator() {
        $q = $this->createQuery();
        $q->where('spam = ?', 'yes');
        $adapter = new FansubCMS_Paginator_Adapter_Doctrine($q);
        return new Zend_Paginator($adapter);
    }
}