<?php
/**
 */
class ProjectScreenshotTable extends Doctrine_Table
{
    /**
     * returns a Zend_Paginator_Object
     * @see Zend_Paginator
     * @return Zend_Paginator
     */
    public function getPaginator() {
        $q = $this->createQuery();
        $q->from('ProjectScreenshot ps')
          ->leftJoin('ps.Project p')
          ->orderBy('p.name ASC');
        $adapter = new FansubCMS_Paginator_Adapter_Doctrine($q);
        return new Zend_Paginator($adapter);
    }
}