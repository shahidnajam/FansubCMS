<?php
class FansubCMS_Doctrine_Record extends Doctrine_Record implements Zend_Acl_Resource_Interface
{
    public function getResourceId()
    {
        // coming soon
        return 'model'; // for now just return that it's a model...
    }
}