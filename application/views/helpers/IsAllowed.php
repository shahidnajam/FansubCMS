<?php
class FansubCMS_View_Helper_IsAllowed extends Zend_View_Helper_Abstract {
    static private $_acl;

    public function isAllowed($resource, $privilege) {
        if(empty(self::$_acl)) {
            self::$_acl = Zend_Registry::get('Zend_Acl');
        }
        return self::$_acl->isAllowed('fansubcms_user_custom_role_logged_in_user', $resource, $privilege);
    }
}