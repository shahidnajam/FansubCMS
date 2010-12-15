<?php
/**
 */
class ProjectEpisodeTable extends Doctrine_Table {
    /**
     * builds the Query for listings
     * @param string $order
     * @return Doctrine_Query
     */
    public function buildQueryForListing($order=null) {
        $q = $this->createQuery();
        $q->from('ProjectEpisode pe');
        if(!is_null($order)) {
            $q->orderBy($order);
        }
        return $q;
    }

    /**
     * returns a Zend_Paginator_Object
     * @see Zend_Paginator
     * @param $pid project id
     * @return Zend_Paginator
     */
    public function getPaginator($pid = null) {
        $q = $this->createQuery();
        $q->from('ProjectEpisode pe')
          ->leftJoin('pe.Project p')
          ->orderBy('p.name ASC, pe.number ASC, pe.container ASC');

        if(!empty($pid)) {
            $q->where('pe.project_id = ?',$pid);
        }

        $adapter = new FansubCMS_Paginator_Adapter_Doctrine($q);
        return new Zend_Paginator($adapter);
    }
}